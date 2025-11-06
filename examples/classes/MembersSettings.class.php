<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\QDateTime;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\CellClick;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Event\Input;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\All;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;

    /**
     * Class MembersSetting
     *
     * Represents a panel for managing member-related settings. This class is responsible for the
     * creation and configuration of controls, data grids, and other UI elements related to
     * member settings. It provides functionality for filtering, pagination, and editing
     * member groups within an application.
     */
    class MembersSetting extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        public Bs\Modal $dlgModal1;

        protected Q\Plugin\Toastr $dlgToast1;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public MembersSettingsTable $dtgMembersGroups;

        public Bs\TextBox $txtMembersGroup;
        public Bs\TextBox $txtMembersTitle;
        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnGoToMembers;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected object $objMenuContent;
        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'MembersSettings.tpl.php';

        /**
         * Constructor function for initializing the object.
         *
         * This method sets up the environment for working with the control, including
         * initializing the logged-in user's session, loading the related user object,
         * and creating the required components such as items per a page, filters, data grids,
         * buttons, modals, and toastr notifications.
         *
         * @param mixed $objParentObject The parent object that owns this control.
         * @param string|null $strControlId An optional control ID, which can be provided for uniquely identifying the
         *     control.
         *
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must have to get something like here $this->objUser->getUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgMembersGroups_Create();
            $this->dtgMembersGroups->setDataBinder('BindData', $this);

            $this->createButtons();
            $this->createModals();
            $this->createToastr();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Updates the user's last active timestamp to the current time and saves the changes to the user object.
         *
         * @return void The method does not return a value.
         * @throws Caller
         */
        private function userOptions(): void
        {
            $this->objUser->setLastActive(QDateTime::now());
            $this->objUser->save();
        }

        /**
         * Initializes the MembersGroups data table by setting up columns, paginators, and editability.
         * This method configures the data table to display and interact with records of member settings.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgMembersGroups_Create(): void
        {
            $this->dtgMembersGroups = new MembersSettingsTable($this);
            $this->dtgMembersGroups_CreateColumns();
            $this->createPaginators();
            $this->dtgMembersGroups_MakeEditable();
            $this->dtgMembersGroups->RowParamsCallback = [$this, "dtgMembersGroups_GetRowParams"];
            $this->dtgMembersGroups->SortColumnIndex = 0;
            $this->dtgMembersGroups->ItemsPerPage = $this->objUser->PreferredItemsPerPage;
        }

        /**
         * This method is responsible for creating columns for the dtgMembersGroups object.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgMembersGroups_CreateColumns(): void
        {
            $this->dtgMembersGroups->createColumns();
        }

        /**
         * Configures the MembersGroups data grid to be editable by adding actions and CSS classes.
         * The method sets up a cell click event to handle row data interactions and applies
         * styling to make the rows appear clickable and responsive.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgMembersGroups_MakeEditable(): void
        {
            $this->dtgMembersGroups->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgMembersGroups_Click'));
            $this->dtgMembersGroups->addCssClass('clickable-rows');
            $this->dtgMembersGroups->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for the dtgMembersGroups control. It loads the selected member group settings
         * and updates the UI components based on the loaded data.
         *
         * @param ActionParams $params Parameters for the action, including the ActionParameter which contains the ID
         *                             of the member group to be loaded.
         *
         * @return void This method does not return any value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgMembersGroups_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->intId = intval($params->ActionParameter);
            $objMembersGroups = MembersSettings::load($this->intId);

            $this->txtMembersGroup->Enabled = false;
            $this->txtMembersTitle->Text = $objMembersGroups->getTitle() ?? '';
            $this->txtMembersGroup->Text = $objMembersGroups->getName() ?? '';
            $this->txtMembersGroup->focus();

            if (!empty($_SESSION['members_edit_group'])) {
                $this->btnGoToMembers->Display = true;
                $this->btnGoToMembers->Enabled = false;
            }

            $this->disableInputs();
        }

        /**
         * Generates parameters for a row in a data table, based on the provided row object and row index.
         *
         * @param object $objRowObject The object representing the current row for which parameters are being generated.
         * @param int $intRowIndex The index of the current row in the data table.
         *
         * @return array An associative array containing parameters, with 'data-value' set to the primary key of the row object.
         */
        public function dtgMembersGroups_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures pagination for the members groups data grid.
         *
         * This method sets up a paginator with labels for navigation and configures
         * the number of items per a page, default sort column, and enables AJAX updates.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgMembersGroups->Paginator = new Bs\Paginator($this);
            $this->dtgMembersGroups->Paginator->LabelForPrevious = t('Previous');
            $this->dtgMembersGroups->Paginator->LabelForNext = t('Next');

            $this->dtgMembersGroups->ItemsPerPage = 10;
            $this->dtgMembersGroups->SortColumnIndex = 0;
            $this->dtgMembersGroups->UseAjax = true;

            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes the items-per-page selector for the current user and configures its properties and events.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createItemsPerPage(): void
        {
            $this->lstItemsPerPageByAssignedUserObject = new Q\Plugin\Select2($this);
            $this->lstItemsPerPageByAssignedUserObject->MinimumResultsForSearch = -1;
            $this->lstItemsPerPageByAssignedUserObject->Theme = 'web-vauu';
            $this->lstItemsPerPageByAssignedUserObject->Width = '100%';
            $this->lstItemsPerPageByAssignedUserObject->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->PreferredItemsPerPage;
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstPreferredItemsPerPageObject_GetItems());
            $this->lstItemsPerPageByAssignedUserObject->AddAction(new Change(), new AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
        }

        /**
         * Retrieves a list of items per a page assigned to a user, encapsulated in ListItem objects.
         *
         * This method generates a condition-based query to fetch items per a page associated
         * with a specific user. Each item is represented as a ListItem, and if the item
         * matches the one assigned to the current user, it is marked as selected.
         *
         * @return ListItem[] An array containing ListItem objects each representing an item per page.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstPreferredItemsPerPageObject_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objPreferredItemsPerPageObjectCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objPreferredItemsPerPageObjectCursor = ItemsPerPage::queryCursor($objCondition, $this->objPreferredItemsPerPageObjectClauses);

            // Iterate through the Cursor
            while ($objPreferredItemsPerPageObject = ItemsPerPage::instantiateCursor($objPreferredItemsPerPageObjectCursor)) {
                $objListItem = new ListItem($objPreferredItemsPerPageObject->__toString(), $objPreferredItemsPerPageObject->Id);
                if (($this->objUser->PreferredItemsPerPageObject) && ($this->objUser->PreferredItemsPerPageObject->Id == $objPreferredItemsPerPageObject->Id))
                    $objListItem->Selected = true;
                $a[] = $objListItem;
            }

            return $a;
        }

        /**
         * Updates the number of items per a page displayed for members groups based on the selected user object
         * and refreshes the data grid to reflect this change.
         *
         * @param ActionParams $params The parameters passed from the action triggering this method.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgMembersGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgMembersGroups->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes a search filter textbox with specific attributes and CSS classes, preparing it for user input
         * and interaction.
         *
         * @return void
         * @throws Caller
         */
        public function createFilter(): void
        {
            $this->txtFilter = new Bs\TextBox($this);
            $this->txtFilter->Placeholder = t('Search...');
            $this->txtFilter->TextMode = TextBoxBase::SEARCH;
            $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
            $this->txtFilter->addCssClass('search-box');

            $this->btnClearFilters = new Bs\Button($this);
            $this->btnClearFilters->Text = t('Clear filters');
            $this->btnClearFilters->addWrapperCssClass('center-button');
            $this->btnClearFilters->CssClass = 'btn btn-default';
            $this->btnClearFilters->setCssStyle('float', 'left');
            $this->btnClearFilters->CausesValidation = false;
            $this->btnClearFilters->addAction(new Click(), new AjaxControl($this, 'clearFilters_Click'));

            $this->addFilterActions();
        }

        /**
         * Clears all filters from the interface and refreshes the relevant components.
         * This method resets the filter text field and refreshes both the filter input and the datagrid
         * to display all data without any filtering applied.
         *
         * @param ActionParams $params The parameters passed to the click action, typically containing event details.
         *
         * @return void
         * @throws Caller
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->dtgMembersGroups->refresh();
            $this->userOptions();
        }

        /**
         * Adds actions to the filter input field to handle user interactions.
         *
         * The method sets up an input event to trigger an Ajax control action after
         * a specified delay, allowing for dynamic filtering. Additionally, it configures
         * an enter key event which triggers the same Ajax action and immediately
         * terminates the event after execution.
         *
         * @return void
         * @throws Caller
         */
        protected function addFilterActions(): void
        {
            $this->txtFilter->addAction(new Input(300), new AjaxControl($this, 'filterChanged'));
            $this->txtFilter->addActionArray(new EnterKey(),
                [
                    new AjaxControl($this, 'filterChanged'),
                    new Terminate()
                ]
            );
        }

        /**
         * Refreshes the members groups data grid when a filter change is detected.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgMembersGroups->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the data grid using a specified condition.
         *
         * This method retrieves the current condition to filter or modify
         * the dataset and then binds this data to a grid for display or processing.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgMembersGroups->bindData($objCondition);
        }

        /**
         * Retrieves the query condition based on the current filter input.
         * If the filter input is empty or null, it returns a condition that matches all records.
         * Otherwise, it creates a condition to match records where the 'Name' field of
         * 'NewsSettings' contains the filter input as a substring.
         *
         * @return All|OrCondition The query condition based on the filter input.
         * @throws Caller
         */
        public function getCondition(): All|OrCondition
        {
            $strSearchValue = $this->txtFilter->Text;

            if ($strSearchValue === null) {
                $strSearchValue = '';
            }
            $strSearchValue = trim($strSearchValue);

            if ($strSearchValue === '') {
                return QQ::all();
            } else {
                return QQ::orCondition(
                    QQ::like(QQN::MembersSettings()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::MembersSettings()->Title, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures several buttons and text boxes for the user interface.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnGoToMembers = new Bs\Button($this);
            $this->btnGoToMembers->Text = t('Go to this member');
            $this->btnGoToMembers->addWrapperCssClass('center-button');
            $this->btnGoToMembers->CssClass = 'btn btn-default';
            $this->btnGoToMembers->CausesValidation = false;
            $this->btnGoToMembers->addAction(new Click(), new AjaxControl($this, 'btnGoToMembers_Click'));
            $this->btnGoToMembers->setCssStyle('float', 'left');
            $this->btnGoToMembers->setCssStyle('margin-right', '10px');

            if (!empty($_SESSION['members_edit_group'])) {
                $this->btnGoToMembers->Display = true;
            } else {
                $this->btnGoToMembers->Display = false;
            }

            $this->txtMembersGroup = new Bs\TextBox($this);
            $this->txtMembersGroup->Placeholder = t('Members group');
            $this->txtMembersGroup->ActionParameter = $this->txtMembersGroup->ControlId;
            $this->txtMembersGroup->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtMembersGroup->setHtmlAttribute('autocomplete', 'off');
            $this->txtMembersGroup->setCssStyle('float', 'left');
            $this->txtMembersGroup->setCssStyle('margin-right', '10px');
            $this->txtMembersGroup->Width = 300;
            $this->txtMembersGroup->Display = false;

            $this->txtMembersTitle = new Bs\TextBox($this);
            $this->txtMembersTitle->Placeholder = t('Member title');
            $this->txtMembersTitle->ActionParameter = $this->txtMembersGroup->ControlId;
            $this->txtMembersTitle->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;

            $this->txtMembersTitle->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtMembersTitle->addAction(new EnterKey(), new Terminate());
            $this->txtMembersTitle->AddAction(new EscapeKey(), new AjaxControl($this, 'btnCancel_Click'));
            $this->txtMembersTitle->addAction(new EscapeKey(), new Terminate());

            $this->txtMembersTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtMembersTitle->setCssStyle('float', 'left');
            $this->txtMembersTitle->setCssStyle('margin-right', '10px');
            $this->txtMembersTitle->Width = 400;
            $this->txtMembersTitle->Display = false;

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Update');
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));
            $this->btnSave->setCssStyle('float', 'left');
            $this->btnSave->setCssStyle('margin-right', '10px');
            $this->btnSave->Display = false;

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
            $this->btnCancel->setCssStyle('float', 'left');
            $this->btnCancel->Display = false;
        }

        /**
         * Creates modal dialogs to handle specific user-related actions or warnings.
         *
         * This method initializes and configures modal dialogs used for displaying critical
         * messages or warnings. In this case, it creates a modal to notify the user about
         * an invalid CSRF token, including a warning title, styled header, explanatory text,
         * and a close button.
         *
         * @return void This method does not return any value.
         */
        public function createModals(): void
        {
            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal1->Title = t("Warning");
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addCloseButton(t("I understand"));
        }

        /**
         * Initializes multiple Toastr dialogs with predefined configurations for success and error alerts.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('<strong>Well done!</strong> The members group title has been saved or modified.');
            $this->dlgToast1->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the save button click event to update or create a Members Group.
         * Depending on the text values provided, it processes the MembersSettings
         * and updates UI components accordingly.
         *
         * @param ActionParams $params The parameters provided by the action triggering the click event.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $objMembersGroup = MembersSettings::load($this->intId);
            $objSelectedGroup = MembersSettings::selectedByIdFromMembersSettings($this->intId);
            $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
            $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

            $objMenuContent->updateMenuContent($this->txtMembersTitle->Text, $objMembersGroup->getTitleSlug());

            $objMembersGroup->setTitle($this->txtMembersTitle->Text);
            $objMembersGroup->setTitleSlug($objMenuContent->getRedirectUrl());
            $objMembersGroup->setPostUpdateDate(QDateTime::now());
            $objMembersGroup->setAssignedEditorsNameById($this->intLoggedUserId);
            $objMembersGroup->save();

            $objFrontendLink->setTitle($this->txtMembersTitle->Text);
            $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
            $objFrontendLink->save();

            if (!empty($_SESSION['members_edit_group'])) {
                $this->btnGoToMembers->Display = true;
                // $this->btnGoToMembers->Enabled = true;
            }

            $this->btnGoToMembers->Enabled = true;

            $this->dtgMembersGroups->refresh();
            $this->enableInputs();
            $this->dlgToast1->notify();
        }

        /**
         * Handles the click event for the cancel button.
         * Resets the display and state of various UI elements associated with member groups.
         *
         * @param ActionParams $params Parameters related to the action event triggered.
         *
         * @return void
         * @throws RandomException
         * @throws Caller
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            if (!empty($_SESSION['members_edit_group'])) {
                $this->btnGoToMembers->Display = true;
                $this->btnGoToMembers->Enabled = true;
            }

            $this->enableInputs();
            $this->txtMembersGroup->Text = '';
            $this->txtMembersTitle->Text = '';
        }

        /**
         * Enables input fields and interactive elements within the form.
         *
         * This method activates specific UI components, including text fields, buttons,
         * filters, and the paginator, making them available for user interaction. Some
         * elements, such as gallery-related fields and save/cancel buttons, are hidden
         * or disabled.
         *
         * @return void
         */
        public function enableInputs(): void
        {
            $this->txtMembersGroup->Display = false;
            $this->txtMembersTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->btnClearFilters->Enabled = true;
            $this->dtgMembersGroups->Paginator->Enabled = true;

            $this->dtgMembersGroups->removeCssClass('disabled');
        }

        /**
         * Disables specific input elements and applies a disabled style to the members group data grid.
         *
         * This method sets the `Enabled` property of specific input controls to `false`,
         * indicating that those inputs are no longer interactable. Additionally, the data grid
         * for gallery groups is styled with a disabled CSS class for visual feedback.
         *
         * @return void This method does not return any value.
         */
        public function disableInputs(): void
        {
            $this->txtMembersGroup->Display = true;
            $this->txtMembersTitle->Display = true;
            $this->btnSave->Display = true;
            $this->btnCancel->Display = true;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->txtFilter->Enabled = false;
            $this->btnClearFilters->Enabled = false;
            $this->dtgMembersGroups->Paginator->Enabled = false;

            $this->dtgMembersGroups->addCssClass('disabled');
        }

        /**
         * Handles the click event for the "Go To Members" button. Redirects the user to the appropriate edit page based
         * on the session variables available. Clears the related session variables after redirection.
         *
         * @param ActionParams $params The parameters associated with the action triggering this event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnGoToMembers_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['members_edit_group'])) {
                Application::redirect('menu_edit.php?id=' . $_SESSION['members_edit_group']);
                unset($_SESSION['members_edit_group']);
            }
        }
    }