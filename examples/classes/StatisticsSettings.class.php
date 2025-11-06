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
     * Defines the `StatisticsSetting` class, which extends the `Q\Control\Panel`
     * and provides functionality for managing and configuring statistical settings
     * with an interactive and dynamic UI.
     *
     * This class includes data grid management for statistical groups, item-per-page controls,
     * modal dialogs, user-specific configurations, and AJAX-driven interactions.
     */
    class StatisticsSetting extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        public Bs\Modal $dlgModal1;

        protected Q\Plugin\Toastr $dlgToast1;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public StatisticsSettingsTable $dtgStatisticsGroups;

        public Bs\TextBox $txtStatisticsGroup;
        public Bs\TextBox $txtStatisticsTitle;
        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnGoToStatistics;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected object $objMenuContent;
        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'StatisticsSettings.tpl.php';

        /**
         * Constructor method for initializing the class.
         *
         * This method sets up the necessary properties, instantiates a User object based on the logged-in user ID,
         * and initializes various UI components such as items per a page, filters, data grids, buttons, modals, and
         * notifications. Developers are encouraged to implement the logic for retrieving the user ID (e.g., from a
         * session) if required.
         *
         * Note: Default behavior assigns a placeholder user ID (e.g., 1), but this should be replaced with actual
         * session-based user management logic in a real application.
         *
         * @param mixed $objParentObject The parent object, typically a QForm or QControl, to associate the new
         *     instance with.
         * @param string|null $strControlId Optional unique control ID. If not provided, a default ID will be
         *     generated.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller Thrown if the parent constructor encounters an issue.
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
            $this->dtgStatisticsGroups_Create();
            $this->dtgStatisticsGroups->setDataBinder('BindData', $this);

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
         * Creates and initializes the StatisticsGroups data grid.
         *
         * This method sets up the data grid by instantiating it, configuring its columns,
         * applying pagination settings, enabling editing capabilities, and defining row parameters.
         * It also sets the default sort column and the number of items displayed per page
         * based on the user's preferences.
         *
         * @return void
         * @throws Caller
         */
        public function dtgStatisticsGroups_Create(): void
        {
            $this->dtgStatisticsGroups = new StatisticsSettingsTable($this);
            $this->dtgStatisticsGroups_CreateColumns();
            $this->createPaginators();
            $this->dtgStatisticsGroups_MakeEditable();
            $this->dtgStatisticsGroups->RowParamsCallback = [$this, "dtgStatisticsGroups_GetRowParams"];
            $this->dtgStatisticsGroups->SortColumnIndex = 0;
            $this->dtgStatisticsGroups->SortDirection = -1;
            $this->dtgStatisticsGroups->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgStatisticsGroups->UseAjax = true;
        }

        /**
         * Creates the columns for the statistics groups data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgStatisticsGroups_CreateColumns(): void
        {
            $this->dtgStatisticsGroups->createColumns();
        }

        /**
         * Configures the `dtgStatisticsGroups` DataTable to be editable by adding interactivity, including cell click
         * actions and CSS styling.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgStatisticsGroups_MakeEditable(): void
        {
            $this->dtgStatisticsGroups->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgStatisticsGroups_CellClick'));
            $this->dtgStatisticsGroups->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for the statistics groups data grid. Sets up the UI for editing
         * or viewing a specific statistics group and its associated settings based on the action parameters.
         *
         * @param ActionParams $params The action parameters that specify the context of the event
         *                             including the action parameter representing the ID of the statistics group.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgStatisticsGroups_CellClick(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->intId = intval($params->ActionParameter);
            $objStatisticsGroups = StatisticsSettings::load($this->intId);

            $this->txtStatisticsGroup->Enabled = false;
            $this->txtStatisticsGroup->Text = $objStatisticsGroups->getName() ?? '';
            $this->txtStatisticsTitle->Text = $objStatisticsGroups->getTitle() ?? '';
            $this->txtStatisticsTitle->focus();

            if (!empty($_SESSION['statistics_edit_group']) || (!empty($_SESSION['statistics']) || !empty($_SESSION['group']))) {
                $this->btnGoToStatistics->Display = true;
                $this->btnGoToStatistics->Enabled = false;
            }

            $this->disableInputs();
        }

        /**
         * Generates and returns an array of parameters for a specific row in the data table.
         *
         * @param object $objRowObject The object representing the current row in the data table.
         * @param int $intRowIndex The index position of the current row in the data table.
         *
         * @return array An associative array of parameters, including a 'data-value' key containing the primary key of the current row.
         */
        public function dtgStatisticsGroups_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $intIsReserved = $objRowObject->getIsReserved();

            if ($intIsReserved == 2) {
                $params['class'] = 'hidden';
            }

            $params['data-value'] = $strKey;

            return $params;
        }

        /**
         * Creates and configures paginators for the statistics group data grid.
         * Sets up paginator labels for navigation, defines the number of items per a page,
         * establishes the initial sorting column, and enables AJAX functionality.
         * Also triggers the addition of filter actions for the data grid.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgStatisticsGroups->Paginator = new Bs\Paginator($this);
            $this->dtgStatisticsGroups->Paginator->LabelForPrevious = t('Previous');
            $this->dtgStatisticsGroups->Paginator->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and initializes a Select2 dropdown for managing the items per page selection.
         *
         * This method configures the dropdown with specific properties such as theme, width,
         * and selection mode. It sets the initially selected value based on the current user's
         * items per page setting, populates the dropdown with options, and assigns an AJAX action
         * for handling change events.
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
         * Retrieves a list of ListItem objects representing items per a page assigned to the user.
         *
         * The method queries and iterates through the ItemsPerPage objects based on the set condition,
         * creating ListItem objects for each ItemsPerPage object. If the user has an assigned
         * ItemsPerPage object, the corresponding ListItem is marked as selected.
         *
         * @return ListItem[] An array of ListItem objects representing the items per page assigned to the user.
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
         * Updates the items per page for the statistics groups data table
         * based on the selected value from the assigned user object list and refreshes the table.
         *
         * @param ActionParams $params The action parameters passed during the call, typically related to the event triggering the method.
         *
         * @return void This method does not return any value.
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgStatisticsGroups->ItemsPerPage =ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgStatisticsGroups->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a search filter text box component for user input.
         *
         * This method creates a search text box with a placeholder text indicating a search action.
         * The text box is set to search mode with autocomplete disabled, and a specific CSS class is applied for
         * styling. Additional actions are added to enhance the filter functionality.
         *
         * @return void No return value as the method sets up the filter component within the class context.
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

            $this->dtgStatisticsGroups->refresh();
            $this->userOptions();
        }

        /**
         * Adds filter actions to the filter input control.
         *
         * This method assigns actions to the filter input control to handle user interactions. It adds an
         * Input event action to trigger an AJAX call after a specified delay when the input changes. It also
         * adds a series of actions that execute when the Enter key is pressed, including an AJAX call and a
         * termination of further events.
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
         * Refreshes the data grid when a filter change is detected.
         *
         * This method handles the logic for updating the display of the data grid
         * to reflect new filter criteria, ensuring accurate and up-to-date data.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgStatisticsGroups->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the statistics groups data grid based on a specified condition.
         *
         * This method retrieves the filtering condition using the getCondition method
         * and applies it to the data grid for statistics groups. It ensures that the
         * data grid displays relevant data based on the current criteria.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgStatisticsGroups->bindData($objCondition);
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
                    QQ::like(QQN::StatisticsSettings()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::StatisticsSettings()->Title, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and initializes a set of buttons and text boxes used for links, navigation and actions.
         *
         * This method sets up various UI elements, including buttons and text boxes for links-related
         * operations. It configures their display properties, styles, and behavior based on session variables
         * and user interactions. The visibility and actions of each button are carefully defined to handle
         * specific user requests related to links management.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnGoToStatistics = new Bs\Button($this);
            $this->btnGoToStatistics->Text = t('Go to these statistics');
            $this->btnGoToStatistics->addWrapperCssClass('center-button');
            $this->btnGoToStatistics->CssClass = 'btn btn-default';
            $this->btnGoToStatistics->CausesValidation = false;
            $this->btnGoToStatistics->addAction(new Click(), new AjaxControl($this, 'btnGoToStatistics_Click'));
            $this->btnGoToStatistics->setCssStyle('float', 'left');
            $this->btnGoToStatistics->setCssStyle('margin-right', '10px');

            if (!empty($_SESSION['statistics_edit_group']) || (!empty($_SESSION['statistics']) || !empty($_SESSION['group']))) {
                $this->btnGoToStatistics->Display = true;
            } else {
                $this->btnGoToStatistics->Display = false;
            }

            $this->txtStatisticsGroup = new Bs\TextBox($this);
            $this->txtStatisticsGroup->Placeholder = t('Statistics group');
            $this->txtStatisticsGroup->ActionParameter = $this->txtStatisticsGroup->ControlId;
            $this->txtStatisticsGroup->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtStatisticsGroup->setHtmlAttribute('autocomplete', 'off');
            $this->txtStatisticsGroup->setCssStyle('float', 'left');
            $this->txtStatisticsGroup->setCssStyle('margin-right', '10px');
            $this->txtStatisticsGroup->Width = 300;
            $this->txtStatisticsGroup->Display = false;

            $this->txtStatisticsTitle = new Bs\TextBox($this);
            $this->txtStatisticsTitle->Placeholder = t('Statistics group title');
            $this->txtStatisticsTitle->ActionParameter = $this->txtStatisticsTitle->ControlId;
            $this->txtStatisticsTitle->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;

            $this->txtStatisticsTitle->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtStatisticsTitle->addAction(new EnterKey(), new Terminate());
            $this->txtStatisticsTitle->AddAction(new EscapeKey(), new AjaxControl($this, 'btnCancel_Click'));
            $this->txtStatisticsTitle->addAction(new EscapeKey(), new Terminate());

            $this->txtStatisticsTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtStatisticsTitle->setCssStyle('float', 'left');
            $this->txtStatisticsTitle->setCssStyle('margin-right', '10px');
            $this->txtStatisticsTitle->Width = 400;
            $this->txtStatisticsTitle->Display = false;

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
         * Creates and configures a Toastr notification instance.
         *
         * This method initializes and sets up a Toastr instance for displaying notification messages.
         * It defines the alert type, position class, message content, and additional features such as
         * a progress bar, ensuring the notification is styled and positioned appropriately.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('<strong>Well done!</strong> The statistics group title has been saved or modified.');
            $this->dlgToast1->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the save button click event to update statistics settings and frontend links.
         *
         * This method updates the title and metadata of a statistics group, modifies related menu content,
         * and adjusts the associated frontend links. It also manages the visibility and interactivity of
         * interface elements such as buttons and text boxes, ensuring consistency in the user interface.
         * Finally, it refreshes the data grid displaying the statistics groups and triggers a notification.
         *
         * @param ActionParams $params Event action parameters used during the button click event.
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

            $objGroup = StatisticsSettings::load($this->intId);
            $objSelectedGroup = StatisticsSettings::selectedByIdFromStatisticsSettings($this->intId);
            $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
            $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

            $objMenuContent->updateMenuContent($this->txtStatisticsTitle->Text, $objGroup->getTitleSlug());

            $objGroup->setTitle($this->txtStatisticsTitle->Text);
            $objGroup->setPostUpdateDate(QDateTime::now());
            $objGroup->setAssignedEditorsNameById($this->intLoggedUserId);
            $objGroup->save();

            $objFrontendLink->setTitle($this->txtStatisticsTitle->Text);
            $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
            $objFrontendLink->save();

            if (!empty($_SESSION['links_edit_group']) || (!empty($_SESSION['links']) || !empty($_SESSION['group']))) {
                $this->btnGoToStatistics->Display = true;
                $this->btnGoToStatistics->Enabled = true;
            }

            $this->dtgStatisticsGroups->refresh();
            $this->enableInputs();
            $this->dlgToast1->notify();
        }

        /**
         * Handles the cancel button click event to reset the UI elements and data for statistics management.
         *
         * This method is triggered when the cancel button is clicked. It updates the display properties and
         * state of various UI components related to statistics groups and titles. Additionally, it ensures
         * the proper reset of text inputs and re-enables relevant controls to restore default functionality.
         *
         * @param ActionParams $params The parameters associated with the cancel button click action.
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

            if (!empty($_SESSION['statistics_edit_group']) || (!empty($_SESSION['statistics']) || !empty($_SESSION['group']))) {
                $this->btnGoToStatistics->Display = true;
                $this->btnGoToStatistics->Enabled = true;
            }

            $this->enableInputs();
            $this->txtStatisticsGroup->Text = '';
            $this->txtStatisticsTitle->Text = '';
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
            $this->txtStatisticsGroup->Display = false;
            $this->txtStatisticsTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->btnClearFilters->Enabled = true;
            $this->dtgStatisticsGroups->Paginator->Enabled = true;

            $this->dtgStatisticsGroups->removeCssClass('disabled');
        }

        /**
         * Disables specific input elements and applies a disabled style to the statistics group data grid.
         *
         * This method sets the `Enabled` property of specific input controls to `false`,
         * indicating that those inputs are no longer interactable. Additionally, the data grid
         * for gallery groups is styled with a disabled CSS class for visual feedback.
         *
         * @return void This method does not return any value.
         */
        public function disableInputs(): void
        {
            $this->txtStatisticsGroup->Display = true;
            $this->txtStatisticsTitle->Display = true;
            $this->btnSave->Display = true;
            $this->btnCancel->Display = true;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->txtFilter->Enabled = false;
            $this->btnClearFilters->Enabled = false;
            $this->dtgStatisticsGroups->Paginator->Enabled = false;

            $this->dtgStatisticsGroups->addCssClass('disabled');
        }

        /**
         * Handles the click event for the "Go to Statistics" button.
         *
         * Directs the user to the appropriate page depending on the session variables related
         * to editing statistics or groups. If a specific statistics edit group is set in the
         * session, the user is redirected to the corresponding menu edit page. Otherwise,
         * redirects to the statistics edit page based on session variables for statistics or group.
         * Clears the relevant session data after redirection.
         *
         * @param ActionParams $params The parameters associated with the button click action.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnGoToStatistics_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['statistics_edit_group'])) {
                Application::redirect('menu_edit.php?id=' . $_SESSION['statistics_edit_group']);
                unset($_SESSION['statistics_edit_group']);

            } else if (!empty($_SESSION['statistics']) || !empty($_SESSION['group'])) {
                Application::redirect('statistics_edit.php?id=' . $_SESSION['statistics'] . '&group=' . $_SESSION['group']);
                unset($_SESSION['statistics']);
                unset($_SESSION['group']);
            }
        }
    }