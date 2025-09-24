<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
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
     * Class SportsCalendarSettings
     *
     * Represents a settings management panel for sports calendar configurations.
     * This class extends the Panel class and provides various controls and features
     * for user interaction, including managing sports groups, pagination, filtering,
     * and editable features within the sports groups table.
     */
    class SportsCalendarSettings extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        public Bs\Modal $dlgModal1;

        protected Q\Plugin\Toastr $dlgToast1;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public SportsSettingsTable $dtgSportsGroups;

        public Bs\TextBox $txtSportsGroup;
        public Bs\TextBox $txtSportsTitle;
        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnGoToCalendar;

        protected object $objUser;
        protected int $intLoggedUserId;
        protected int $intId;

        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'SportsSettings.tpl.php';

        /**
         * Constructor for initializing the sports groups management interface.
         *
         * This method initializes the required properties, creates filters, prepares
         * the pagination and filtering options, and sets up buttons, modals, and notifications.
         * It binds the data table to a data provider, enabling dynamic data management.
         * Assumes a logged-in user and loads their information for context-specific operations.
         *
         * @param mixed $objParentObject The parent object, typically a form or other containing control.
         * @param string|null $strControlId Optional control ID. If null, a unique ID will be generated.
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

            $this->intLoggedUserId = 1;
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgSportsGroups_Create();
            $this->dtgSportsGroups->setDataBinder('BindData', $this);

            $this->createButtons();
            $this->createModals();
            $this->createToastr();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the sports groups data table.
         *
         * This method is responsible for setting up the sports groups
         * data table by creating columns, adding pagination, and making
         * the table editable. It also sets parameters for row configuration
         * and defines default sorting behavior.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgSportsGroups_Create(): void
        {
            $this->dtgSportsGroups = new SportsSettingsTable($this);
            $this->dtgSportsGroups_CreateColumns();
            $this->createPaginators();
            $this->dtgSportsGroups_MakeEditable();
            $this->dtgSportsGroups->RowParamsCallback = [$this, "dtgSportsGroups_GetRowParams"];
            $this->dtgSportsGroups->SortColumnIndex = 0;
            $this->dtgSportsGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
        }

        /**
         * Creates and initializes the columns for the sports groups data table.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgSportsGroups_CreateColumns(): void
        {
            $this->dtgSportsGroups->createColumns();
        }

        /**
         * Configures the SportsGroups data grid to be editable by adding appropriate actions and CSS classes.
         *
         * This method adds a cell click event action that triggers an AJAX control callback on a click.
         * It also applies CSS classes to make rows clickable and to enhance the visual style of the table.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgSportsGroups_MakeEditable(): void
        {
            $this->dtgSportsGroups->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgSportsGroups_Click'));
            $this->dtgSportsGroups->addCssClass('clickable-rows');
            $this->dtgSportsGroups->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for the sports groups data grid.
         *
         * This method processes the click event triggered in the sports groups data grid.
         * It loads and configures the necessary data based on the selected sports group for editing.
         *
         * @param ActionParams $params An object containing the parameters associated with the action event,
         *                             including the identifier of the selected sports group.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgSportsGroups_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);
            $objEventsGroups = SportsSettings::load($this->intId);

            $this->txtSportsGroup->Enabled = false;
            $this->txtSportsGroup->Text = $objEventsGroups->getName() ?? '';
            $this->txtSportsGroup->focus();
            $this->txtSportsTitle->Text = $objEventsGroups->getTitle() ?? '';
            $this->txtSportsTitle->focus();

            if (!empty($_SESSION['sports_edit_group']) || !empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
                $this->btnGoToCalendar->Display = true;
                $this->btnGoToCalendar->Enabled = false;
            }

            $this->dtgSportsGroups->addCssClass('disabled');
            $this->txtSportsGroup->Display = true;
            $this->txtSportsTitle->Display = true;
            $this->btnSave->Display = true;
            $this->btnCancel->Display = true;
        }

        /**
         * Retrieves the parameters for a specific row in the sports groups data grid.
         *
         * @param object $objRowObject The row object containing data for the specific row.
         * @param int $intRowIndex The index of the row in the data grid.
         *
         * @return array An associative array of parameters for the row, with data attributes included.
         */
        public function dtgSportsGroups_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Configures and attaches paginators to the data grid, sets pagination labels,
         * items per a page, initializes AJAX functionality, and applies filter actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgSportsGroups->Paginator = new Bs\Paginator($this);
            $this->dtgSportsGroups->Paginator->LabelForPrevious = t('Previous');
            $this->dtgSportsGroups->Paginator->LabelForNext = t('Next');

            $this->dtgSportsGroups->ItemsPerPage = 10;
            $this->dtgSportsGroups->SortColumnIndex = 0;
            $this->dtgSportsGroups->UseAjax = true;

            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the Select2 control for selecting items per a page.
         * The control is customized with specific settings such as theme, width,
         * selection mode, and pre-selected value. It also populates the control with
         * a list of items and sets up an AJAX action that triggers on change.
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
            $this->lstItemsPerPageByAssignedUserObject->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->ItemsPerPageByAssignedUser;
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstItemsPerPageByAssignedUserObject_GetItems());
            $this->lstItemsPerPageByAssignedUserObject->AddAction(new Change(), new AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
        }

        /**
         * Retrieves a list of ListItem objects representing items per page settings for the assigned user object.
         *
         * @return ListItem[] An array of ListItem objects, where each item represents a setting for items per a page.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objItemsPerPageByAssignedUserObjectCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objItemsPerPageByAssignedUserObjectCursor = ItemsPerPage::queryCursor($objCondition, $this->objItemsPerPageByAssignedUserObjectClauses);

            // Iterate through the Cursor
            while ($objItemsPerPageByAssignedUserObject = ItemsPerPage::instantiateCursor($objItemsPerPageByAssignedUserObjectCursor)) {
                $objListItem = new ListItem($objItemsPerPageByAssignedUserObject->__toString(), $objItemsPerPageByAssignedUserObject->Id);
                if (($this->objUser->ItemsPerPageByAssignedUserObject) && ($this->objUser->ItemsPerPageByAssignedUserObject->Id == $objItemsPerPageByAssignedUserObject->Id))
                    $objListItem->Selected = true;
                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Handles changes to the items per page selection by the assigned user object.
         *
         * @param ActionParams $params The parameters containing action-specific data for the event.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgSportsGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgSportsGroups->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a filter text box for search functionality.
         *
         * @return void
         * @throws Caller
         */
        public function createFilter(): void
        {
            $this->txtFilter = new Bs\TextBox($this);
            $this->txtFilter->Placeholder = t('Search...');
            $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
            $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
            $this->txtFilter->addCssClass('search-box');

            $this->btnClearFilters = new Bs\Button($this);
            $this->btnClearFilters->Text = t('Clear filter');
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
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->dtgSportsGroups->refresh();
        }

        /**
         * Adds actions to the filter input control.
         *
         * This method sets up two types of actions for the filter input control:
         * - An `Input` event with a delay of 300 milliseconds, triggering an Ajax call to 'filterChanged'.
         * - An `EnterKey` event that triggers two actions:
         *   - An Ajax call to 'filterChanged'.
         *   - A termination action to cease any further event processing.
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
         * Refreshes the data display in the sports groups table when the filter settings change.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgSportsGroups->refresh();
        }

        /**
         * Binds data to the sports groups table based on a specific condition.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgSportsGroups->bindData($objCondition);
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
                    QQ::like(QQN::SportsSettings()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::SportsSettings()->Title, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes the button and text box controls related to the sports calendar management.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnGoToCalendar = new Bs\Button($this);
            $this->btnGoToCalendar->Text = t('Go to this sports calendar');
            $this->btnGoToCalendar->addWrapperCssClass('center-button');
            $this->btnGoToCalendar->CssClass = 'btn btn-default';
            $this->btnGoToCalendar->CausesValidation = false;
            $this->btnGoToCalendar->addAction(new Click(), new AjaxControl($this, 'btnGoToCalendar_Click'));
            $this->btnGoToCalendar->setCssStyle('float', 'left');
            $this->btnGoToCalendar->setCssStyle('margin-right', '10px');

            if (!empty($_SESSION['sports_edit_group']) || !empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
                $this->btnGoToCalendar->Display = true;
            } else {
                $this->btnGoToCalendar->Display = false;
            }

            $this->txtSportsGroup = new Bs\TextBox($this);
            $this->txtSportsGroup->Placeholder = t('Sports calendar group');
            $this->txtSportsGroup->ActionParameter = $this->txtSportsGroup->ControlId;
            $this->txtSportsGroup->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtSportsGroup->setHtmlAttribute('autocomplete', 'off');
            $this->txtSportsGroup->setCssStyle('float', 'left');
            $this->txtSportsGroup->setCssStyle('margin-right', '10px');
            $this->txtSportsGroup->Width = 300;
            $this->txtSportsGroup->Display = false;

            $this->txtSportsTitle = new Bs\TextBox($this);
            $this->txtSportsTitle->Placeholder = t('Sports calendar title');
            $this->txtSportsTitle->ActionParameter = $this->txtSportsTitle->ControlId;
            $this->txtSportsTitle->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;

            $this->txtSportsTitle->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtSportsTitle->addAction(new EnterKey(), new Terminate());
            $this->txtSportsTitle->AddAction(new EscapeKey(), new AjaxControl($this, 'btnCancel_Click'));
            $this->txtSportsTitle->addAction(new EscapeKey(), new Terminate());

            $this->txtSportsTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtSportsTitle->setCssStyle('float', 'left');
            $this->txtSportsTitle->setCssStyle('margin-right', '10px');
            $this->txtSportsTitle->Width = 400;
            $this->txtSportsTitle->Display = false;

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Update');
            $this->btnSave->CssClass = 'btn btn-orange save-js';
            $this->btnSave->addWrapperCssClass('center-button');
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
         * Creates and configures the Toastr notification.
         *
         * This method initializes a Toastr notification object and configures its
         * type, position, message, and additional settings such as the progress bar.
         * The notification is intended to provide feedback about the success of
         * saving or modifying the sports calendar group.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('<strong>Well done!</strong> The sports calendar group has been saved or modified.');
            $this->dlgToast1->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the save button click event to update sports group information.
         *
         * @param ActionParams $params The action parameters associated with the button click event.
         *
         * @return void
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

            $objGroup = SportsSettings::load($this->intId);
            $objSelectedGroup = SportsSettings::selectedByIdFromSportsSettings($this->intId);
            $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
            $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

            $objMenuContent->updateMenuContent($this->txtSportsTitle->Text, $objGroup->getTitleSlug());

            $objGroup->setTitle($this->txtSportsTitle->Text);
            $objGroup->setPostUpdateDate(Q\QDateTime::now());
            $objGroup->save();

            $objFrontendLink->setTitle($this->txtSportsTitle->Text);
            $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
            $objFrontendLink->save();

            if (!empty($_SESSION['sports_edit_group']) || !empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
                $this->btnGoToCalendar->Display = true;
                $this->btnGoToCalendar->Enabled = true;
            }

            $this->txtSportsGroup->Display = false;
            $this->txtSportsTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;

            $this->dtgSportsGroups->refresh();
            $this->dtgSportsGroups->removeCssClass('disabled');
            $this->dlgToast1->notify();
        }

        /**
         * Handles the click event for the cancel button. This function checks specific session variables
         * to determine the display and enabled state of certain UI elements and resets text fields and their
         * visibility.
         *
         * @param ActionParams $params The parameters associated with the button click action.
         *
         * @return void
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['sports_edit_group']) || !empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
                $this->btnGoToCalendar->Display = true;
                $this->btnGoToCalendar->Enabled = true;
            }

            $this->txtSportsGroup->Display = false;
            $this->txtSportsTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgSportsGroups->removeCssClass('disabled');
            $this->txtSportsGroup->Text = '';
            $this->txtSportsTitle->Text = '';
        }

        /**
         * Handles the click event for the "Go To Calendar" button. Redirects the user to the appropriate page
         * based on session variables for sports groups or sports events.
         *
         * @param ActionParams $params The parameters associated with the button click action.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnGoToCalendar_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['sports_edit_group'])) {
                Application::redirect('menu_edit.php?id=' . $_SESSION['sports_edit_group']);
                unset($_SESSION['sports_edit_group']);

            }  else if (!empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
                Application::redirect('sports_calendar_edit.php?id=' . $_SESSION['sports_id'] . '&group=' . $_SESSION['sports_group']);
                unset($_SESSION['sports_id']);
                unset($_SESSION['sports_group']);
            }
        }
    }