<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
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
     * Class NewsSetting
     *
     * Represents a panel for managing news settings within the application. Includes functionality
     * for configuring data grids, managing user-specific settings, handling events, and displaying
     * modals and notifications.
     */
    class NewsSetting extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        public Bs\Modal $dlgModal1;

        protected Q\Plugin\Toastr $dlgToast1;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public NewsSettingsTable $dtgNewsGroups;

        public Bs\TextBox $txtNewsGroup;
        public Bs\TextBox $txtNewsTitle;
        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnGoToNews;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected object $objMenuContent;
        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'NewsSettings.tpl.php';

        /**
         * Constructor method to initialize and configure the object.
         *
         * This method sets up the component by initializing necessary member variables, loading the logged user,
         * and creating UI components such as items per a page, filters, data grids, buttons, modals, and toastr
         * notifications. The logged user ID is hardcoded for example purposes but can be dynamically retrieved from a
         * session or other storage.
         *
         * @param mixed $objParentObject The parent object controlling this component.
         * @param null|string $strControlId Optional control ID, defaults to null if not provided.
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
            $this->dtgNewsGroups_Create();
            $this->dtgNewsGroups->setDataBinder('BindData', $this);

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
         * Initializes the NewsGroups data grid, sets up columns, pagination, editability,
         * and row parameters, and configures default sorting and items per a page.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgNewsGroups_Create(): void
        {
            $this->dtgNewsGroups = new NewsSettingsTable($this);
            $this->dtgNewsGroups_CreateColumns();
            $this->createPaginators();
            $this->dtgNewsGroups_MakeEditable();
            $this->dtgNewsGroups->RowParamsCallback = [$this, "dtgNewsGroups_GetRowParams"];
            $this->dtgNewsGroups->SortColumnIndex = 0;
            $this->dtgNewsGroups->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgNewsGroups->UseAjax = true;
        }

        /**
         * Initializes and creates columns for the data grid of newsgroups.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgNewsGroups_CreateColumns(): void
        {
            $this->dtgNewsGroups->createColumns();
        }

        /**
         * Configures the NewsGroups data grid to be editable by adding interactivity and styling.
         *
         * This method attaches an action to the data grid that triggers an AJAX call when a cell is clicked.
         * It also applies CSS classes to make rows clickable and to style the grid with hover and responsive features.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgNewsGroups_MakeEditable(): void
        {
            $this->dtgNewsGroups->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgNewsGroups_Click'));
            $this->dtgNewsGroups->addCssClass('clickable-rows');
            $this->dtgNewsGroups->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for the data grid displaying newsgroups.
         *
         * @param ActionParams $params The parameters associated with the action, including action-specific parameters.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgNewsGroups_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->intId = intval($params->ActionParameter);
            $objNewsGroups = NewsSettings::load($this->intId);

            $this->txtNewsGroup->Enabled = false;
            $this->txtNewsGroup->Text = $objNewsGroups->getName() ?? '';
            $this->txtNewsTitle->Text = $objNewsGroups->getTitle() ?? '';
            $this->txtNewsTitle->focus();

            if (!empty($_SESSION['news_edit_group']) || (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group']))) {
                $this->btnGoToNews->Display = true;
                $this->btnGoToNews->Enabled = false;
            }

            $this->disableInputs();
        }

        /**
         * Generates an array of parameters for a specific row in a data grid.
         *
         * @param object $objRowObject The object representing the data for the current row.
         * @param int $intRowIndex The index of the current row in the data grid.
         *
         * @return array An associative array of parameters for the current row, including data attributes.
         */
        public function dtgNewsGroups_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Creates and configures paginators for the dtgNewsGroups data table.
         * This includes setting up the Paginator object, setting the previous and next labels,
         * defining the number of items per a page, setting the default sort column, and enabling AJAX for loading data.
         * It also initiates additional filter actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgNewsGroups->Paginator = new Bs\Paginator($this);
            $this->dtgNewsGroups->Paginator->LabelForPrevious = t('Previous');
            $this->dtgNewsGroups->Paginator->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the `lstItemsPerPageByAssignedUserObject` control, which is a Select2 dropdown.
         * The dropdown is configured with specific settings including a theme, width, and selection mode.
         * Populates the dropdown with items and sets the selected value based on the user's assigned items per a page.
         * Attaches an AJAX action to handle changes in the selection.
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
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstPreferredItemsPerPageObject_GetItems());
            $this->lstItemsPerPageByAssignedUserObject->AddAction(new Change(), new AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
        }

        /**
         * Retrieves a list of ListItem objects representing items per page settings for an assigned user.
         *
         * This method queries the ItemsPerPage objects based on a condition and creates a list of ListItem objects,
         * marking the one associated with the current user as selected if applicable.
         *
         * @return ListItem[] An array of ListItem objects, with one marked as selected if it matches the assigned
         *     user's settings.
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
         * Updates the number of items displayed per page in the data grid based on the selected option.
         *
         * @param ActionParams $params The action parameters containing the context for the change event.
         *
         * @return void
         * @throws Caller
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgNewsGroups->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgNewsGroups->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes the filter text box with specific properties and settings.
         * Sets placeholder, text mode, disables autocomplete, and applies CSS class.
         * Invokes a method to add necessary actions to the filter.
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
         * @throws Caller
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->dtgNewsGroups->refresh();
            $this->userOptions();
        }

        /**
         * Adds filter actions to the filter input component, enabling the execution of specified actions
         * upon certain events such as input changes or when the Enter key is pressed. The actions trigger
         * an Ajax call to handle the event and optionally terminate further event handling.
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
         * Refreshes the data grid of newsgroups whenever the filter criteria are modified.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgNewsGroups->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the data grid based on the current condition.
         *
         * This method retrieves the current condition using the getCondition method
         * and applies it to bind data to the dtgNewsGroups data grid.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgNewsGroups->bindData($objCondition);
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
                    QQ::like(QQN::NewsSettings()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::NewsSettings()->Title, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a set of buttons and text boxes for interacting with news items.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnGoToNews = new Bs\Button($this);
            $this->btnGoToNews->Text = t('Go to this news');
            $this->btnGoToNews->addWrapperCssClass('center-button');
            $this->btnGoToNews->CssClass = 'btn btn-default';
            $this->btnGoToNews->CausesValidation = false;
            $this->btnGoToNews->addAction(new Click(), new AjaxControl($this, 'btnGoToNews_Click'));
            $this->btnGoToNews->setCssStyle('float', 'left');
            $this->btnGoToNews->setCssStyle('margin-right', '10px');

            if (!empty($_SESSION['news_edit_group']) || (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group']))) {
                $this->btnGoToNews->Display = true;
            } else {
                $this->btnGoToNews->Display = false;
            }

            $this->txtNewsGroup = new Bs\TextBox($this);
            $this->txtNewsGroup->Placeholder = t('Newsgroup');
            $this->txtNewsGroup->ActionParameter = $this->txtNewsGroup->ControlId;
            $this->txtNewsGroup->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtNewsGroup->setHtmlAttribute('autocomplete', 'off');
            $this->txtNewsGroup->setCssStyle('float', 'left');
            $this->txtNewsGroup->setCssStyle('margin-right', '10px');
            $this->txtNewsGroup->Width = 300;
            $this->txtNewsGroup->Display = false;

            $this->txtNewsTitle = new Bs\TextBox($this);
            $this->txtNewsTitle->Placeholder = t('News title');
            $this->txtNewsTitle->ActionParameter = $this->txtNewsTitle->ControlId;
            $this->txtNewsTitle->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;

            $this->txtNewsTitle->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtNewsTitle->addAction(new EnterKey(), new Terminate());
            $this->txtNewsTitle->AddAction(new EscapeKey(), new AjaxControl($this, 'btnCancel_Click'));
            $this->txtNewsTitle->addAction(new EscapeKey(), new Terminate());

            $this->txtNewsTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtNewsTitle->setCssStyle('float', 'left');
            $this->txtNewsTitle->setCssStyle('margin-right', '10px');
            $this->txtNewsTitle->Width = 400;
            $this->txtNewsTitle->Display = false;

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
         * @throws Caller
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
         * Initializes and configures multiple Toastr notification instances with different alert types,
         * positions, messages, and progress bar settings for displaying various success or error messages.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('<strong>Well done!</strong> The news group title has been saved or modified.');
            $this->dlgToast1->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the save button click event to update newsgroup settings, menu content, and frontend links.
         *
         * This method updates the newsgroup information based on user input, including the title and slug. It also
         * updates the associated menu content and frontend link records. After saving the updates, it modifies the
         * visibility and state of specific UI components and refreshes the data grid of newsgroups.
         *
         * @param ActionParams $params Parameters related to the triggering of the save action.
         *
         * @return void No return value.
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

            $objNewGroup = NewsSettings::load($this->intId);
            $objSelectedGroup = NewsSettings::selectedByIdFromNewsSettings($this->intId);

            $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
            $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

            $objMenuContent->updateMenuContent($this->txtNewsTitle->Text, $objNewGroup->getTitleSlug());

            $objNewGroup->setTitle($this->txtNewsTitle->Text);
            $objNewGroup->setTitleSlug($objMenuContent->getRedirectUrl());
            $objNewGroup->setPostUpdateDate(QDateTime::now());
            $objNewGroup->save();

            $objFrontendLink->setTitle($this->txtNewsTitle->Text);
            $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
            $objFrontendLink->save();

            if (!empty($_SESSION['news_edit_group']) || (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group']))) {
                $this->btnGoToNews->Display = true;
                $this->btnGoToNews->Enabled = true;
            }

            $this->dtgNewsGroups->refresh();
            $this->enableInputs();
            $this->dlgToast1->notify();
        }

        /**
         * Handles the click event for the cancel button. This method is responsible for adjusting the UI elements
         * based on session variables related to news editing and settings. It modifies the visibility and enabled
         * state of certain UI components and resets text fields.
         *
         * @param ActionParams $params The parameters associated with the action event triggered by the cancel button
         *     click.
         *
         * @return void This method does not return any value.
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

            if (!empty($_SESSION['news_edit_group']) || (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group']))) {
                $this->btnGoToNews->Display = true;
                $this->btnGoToNews->Enabled = true;
            }

            $this->enableInputs();
            $this->txtNewsGroup->Text = '';
            $this->txtNewsTitle->Text = '';
        }

        /**
         * Enables input elements and controls associated with managing newsgroups.
         *
         * This method activates the interactivity for input fields, buttons, and controls,
         * ensuring they are ready for user interaction.
         *
         * @return void
         */
        public function enableInputs(): void
        {
            $this->txtNewsGroup->Display = false;
            $this->txtNewsTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->btnClearFilters->Enabled = true;
            $this->dtgNewsGroups->Paginator->Enabled = true;

            $this->dtgNewsGroups->removeCssClass('disabled');
        }

        /**
         * Disables input controls and pagination for the associated UI components.
         *
         * This method sets the `Enabled` property to false for several UI elements, including
         * list controls, text inputs, buttons, data grids, and paginators, making them non-interactive.
         *
         * @return void
         */
        public function disableInputs(): void
        {
            $this->txtNewsGroup->Display = true;
            $this->txtNewsTitle->Display = true;
            $this->btnSave->Display = true;
            $this->btnCancel->Display = true;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->txtFilter->Enabled = false;
            $this->btnClearFilters->Enabled = false;
            $this->dtgNewsGroups->Enabled = false;
            $this->dtgNewsGroups->Paginator->Enabled = false;

            $this->dtgNewsGroups->addCssClass('disabled');

        }

        /**
         * Handles the redirection logic when the "Go To News" button is clicked,
         * directing the user to the appropriate edit page based on session variables.
         *
         * @param ActionParams $params The parameters passed with the action event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnGoToNews_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['news_edit_group'])) {
                Application::redirect('menu_edit.php?id=' . $_SESSION['news_edit_group']);
                unset($_SESSION['news_edit_group']);

            } else if (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group'])) {
                Application::redirect('news_edit.php?id=' . $_SESSION['news_settings_id'] . '&group=' . $_SESSION['news_settings_group']);
                unset($_SESSION['news_settings_id']);
                unset($_SESSION['news_settings_group']);
            }
        }
    }