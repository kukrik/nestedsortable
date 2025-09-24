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
     * This class, EventSettings, serves as a configurable UI panel
     * for managing event-related settings and filtering options.
     */
    class EventSettings extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        public Bs\Modal $dlgModal1;

        protected Q\Plugin\Toastr $dlgToast1;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public EventsSettingsTable $dtgEventsGroups;

        public Bs\TextBox $txtEventGroup;
        public Bs\TextBox $txtEventTitle;
        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnGoToEvents;

        protected object $objUser;
        protected int $intLoggedUserId;
        protected int $intId;

        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'EventsSettings.tpl.php';

        /**
         * Constructor for the class, which initializes the required properties and components.
         *
         * This method sets up the control by calling its parent constructor, retrieves the logged-in user's ID,
         * loads the corresponding user object, and initializes several components such as items per a page,
         * filters, data grids, buttons, modals, and notifications. The implementation assumes a predefined method
         * is used to retrieve the logged-in user's ID (e.g., from a session).
         *
         * @param object $objParentObject The parent object (usually a parent form or control) to which this control
         *     belongs.
         * @param null|string $strControlId Optional control ID for this instance. Defaults to null if not provided.
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

            // $this->intLoggedUserId= $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session

            $this->intLoggedUserId = 1;
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgEventsGroups_Create();
            $this->dtgEventsGroups->setDataBinder('BindData', $this);

            $this->createButtons();
            $this->createModals();
            $this->createToastr();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the data grid for event groups.
         *
         * The method sets up the event groups table, defines its columns, enables pagination,
         * and marks the table as editable. It also assigns a callback for row parameters
         * and sets the default sort column and items per a page based on user preferences.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgEventsGroups_Create(): void
        {
            $this->dtgEventsGroups = new EventsSettingsTable($this);
            $this->dtgEventsGroups_CreateColumns();
            $this->createPaginators();
            $this->dtgEventsGroups_MakeEditable();
            $this->dtgEventsGroups->RowParamsCallback = [$this, "dtgEventsGroups_GetRowParams"];
            $this->dtgEventsGroups->SortColumnIndex = 0;
            $this->dtgEventsGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
        }

        /**
         * Initializes the creation of columns for the events groups data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgEventsGroups_CreateColumns(): void
        {
            $this->dtgEventsGroups->createColumns();
        }

        /**
         * Configures the event groups data grid to be editable by adding click actions
         * and necessary CSS classes for user interaction.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgEventsGroups_MakeEditable(): void
        {
            $this->dtgEventsGroups->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgEventsGroups_Click'));
            $this->dtgEventsGroups->addCssClass('clickable-rows');
            $this->dtgEventsGroups->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for event groups by loading the associated event group and menu content details.
         * Updates UI controls based on loaded data.
         *
         * @param ActionParams $params Contains parameters for the action, including the event group identifier to be processed.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgEventsGroups_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);
            $objEventsGroups = EventsSettings::load($this->intId);

            $this->txtEventGroup->Enabled = false;
            $this->txtEventGroup->Text = $objEventsGroups->getName() ?? '';
            $this->txtEventGroup->focus();
            $this->txtEventTitle->Text = $objEventsGroups->getTitle() ?? '';
            $this->txtEventTitle->focus();

            if (!empty($_SESSION['events_edit_group']) || !empty($_SESSION['events_id']) && !empty($_SESSION['events_group'])) {
                $this->btnGoToEvents->Display = true;
                $this->btnGoToEvents->Enabled = false;
            }

            $this->dtgEventsGroups->addCssClass('disabled');
            $this->txtEventGroup->Display = true;
            $this->txtEventTitle->Display = true;
            $this->btnSave->Display = true;
            $this->btnCancel->Display = true;
        }

        /**
         * Retrieves the parameters for a specific row in the events groups data grid.
         *
         * @param object $objRowObject The object representing the row for which parameters are to be retrieved.
         * @param int $intRowIndex The index of the row within the data grid.
         *
         * @return array An associative array containing parameters for the specified row, keyed by parameter names.
         */
        public function dtgEventsGroups_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Configures pagination for the events groups data grid, setting up the paginator labels,
         * items per a page, sorting column, and AJAX usage. Also adds filter actions to the data grid.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgEventsGroups->Paginator = new Bs\Paginator($this);
            $this->dtgEventsGroups->Paginator->LabelForPrevious = t('Previous');
            $this->dtgEventsGroups->Paginator->LabelForNext = t('Next');

            $this->dtgEventsGroups->ItemsPerPage = 10;
            $this->dtgEventsGroups->SortColumnIndex = 0;
            $this->dtgEventsGroups->UseAjax = true;

            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the items per page selection control using Select2 plugin.
         * This method sets various properties of the control like theme, width, selection mode,
         * and adds selectable items to it. An Ajax control action is attached to handle change events.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
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
         * Retrieves a list of ListItem objects based on the ItemsPerPage objects assigned to a user.
         *
         * The method queries the database for ItemsPerPage objects that match a specified condition and
         * converts each of these objects into a ListItem object. The ListItem objects are then gathered
         * into an array, which is returned. If a particular ItemsPerPage object is assigned to the user,
         * it is marked as selected within the returned array of ListItem objects.
         *
         * @return ListItem[] An array of ListItem objects representing the items per page for a specific user.
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
         * Handles the change event when the items per page selection is modified by the assigned user.
         *
         * @param ActionParams $params Parameters associated with the change action.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgEventsGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgEventsGroups->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes a search filter TextBox component for user input.
         *
         * This method creates a Bs\TextBox object configured for search purposes, sets its placeholder text,
         * and establishes search-specific attributes. It disables autocomplete, assigns a CSS class for styling,
         * and invokes additional filter-related actions.
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

            $this->dtgEventsGroups->refresh();
        }

        /**
         * Adds filter actions to the txtFilter control for handling user input events.
         *
         * This method attaches actions to the txtFilter control, ensuring that the specified events
         * trigger the filter change process. An Input event with a delay of 300 milliseconds is set up
         * to call the filterChanged method via AJAX. Additionally, an EnterKey event is configured to
         * execute the filterChanged method and terminate further action propagation.
         *
         * @return void This method does not return any value.
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
         * Triggers a refresh of the dtgEventsGroups data display.
         *
         * This method is intended to be called whenever there is a change in the filter criteria
         * affecting the dtgEventsGroups. It ensures that the displayed data is up to date by refreshing
         * the data grid with the potentially new data set defined by the current filter.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgEventsGroups->refresh();
        }

        /**
         * Binds data to the dtgEventsGroups control based on a specified condition.
         *
         * This method retrieves a condition using the getCondition method and applies this condition
         * to bind data to the dtgEventsGroups control. The condition defines which data should be
         * retrieved and displayed by the data grid.
         *
         * @return void This method does not return any value.
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgEventsGroups->bindData($objCondition);
        }

        /**
         * Constructs and returns a query condition based on the user's filter input.
         *
         * This method checks the value of a text filter to determine the search criteria. If the filter
         * text is empty or null, it returns a condition matching all records. Otherwise, it returns a
         * condition that matches records whose 'Name' field contains the filter text as a substring.
         *
         * @return All|OrCondition A query condition object that represents either all records or a filtered condition
         *                    based on the user's search input.
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
                    QQ::like(QQN::EventsSettings()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::EventsSettings()->Title, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a button and text box controls used for event management.
         *
         * This method creates several buttons and text boxes for handling event-related actions. It sets
         * various properties such as text, CSS classes, styles, and behaviors. The visibility of the
         * controls depends on the session variables related to events. Each button and text box is
         * customized for specific purposes, including navigation, updating, and cancelling changes.
         *
         * @return void This method does not return any value.
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnGoToEvents = new Bs\Button($this);
            $this->btnGoToEvents->Text = t('Go to the calendar events');
            $this->btnGoToEvents->addWrapperCssClass('center-button');
            $this->btnGoToEvents->CssClass = 'btn btn-default';
            $this->btnGoToEvents->CausesValidation = false;
            $this->btnGoToEvents->addAction(new Click(), new AjaxControl($this, 'btnGoToEvents_Click'));
            $this->btnGoToEvents->setCssStyle('float', 'left');
            $this->btnGoToEvents->setCssStyle('margin-right', '10px');

            if (!empty($_SESSION['events_edit_group']) || !empty($_SESSION['events_id']) && !empty($_SESSION['events_group'])) {
                $this->btnGoToEvents->Display = true;
            } else {
                $this->btnGoToEvents->Display = false;
            }

            $this->txtEventGroup = new Bs\TextBox($this);
            $this->txtEventGroup->Placeholder = t('Event group');
            $this->txtEventGroup->ActionParameter = $this->txtEventGroup->ControlId;
            $this->txtEventGroup->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtEventGroup->setHtmlAttribute('autocomplete', 'off');
            $this->txtEventGroup->setCssStyle('float', 'left');
            $this->txtEventGroup->setCssStyle('margin-right', '10px');
            $this->txtEventGroup->Width = 300;
            $this->txtEventGroup->Display = false;

            $this->txtEventTitle = new Bs\TextBox($this);
            $this->txtEventTitle->Placeholder = t('Event title');
            $this->txtEventTitle->ActionParameter = $this->txtEventTitle->ControlId;
            $this->txtEventTitle->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;

            $this->txtEventTitle->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtEventTitle->addAction(new EnterKey(), new Terminate());
            $this->txtEventTitle->AddAction(new EscapeKey(), new AjaxControl($this, 'btnCancel_Click'));
            $this->txtEventTitle->addAction(new EscapeKey(), new Terminate());

            $this->txtEventTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtEventTitle->setCssStyle('float', 'left');
            $this->txtEventTitle->setCssStyle('margin-right', '10px');
            $this->txtEventTitle->Width = 400;
            $this->txtEventTitle->Display = false;

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
         * Initializes and configures multiple Toastr notification dialogs with predefined settings.
         *
         * This method creates several Toastr dialogs, each initialized with specific alert types,
         * position classes, messages, and configurations such as a progress bar. Each Toastr instance
         * is set to display different messages and alert types based on certain conditions.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('<strong>Well done!</strong> The event group title has been saved or modified.');
            $this->dlgToast1->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the Save button and updates a relevant event and menu settings.
         *
         * This method updates the title and other related properties for an event group and its associated
         * menu content and frontend link. It also manages the display and state of UI components and refreshes
         * the data grid displaying event groups. If certain session variables are set, additional UI buttons
         * become enabled and visible.
         *
         * @param ActionParams $params Parameters from the triggered action.
         *
         * @return void This method does not return a value.
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

            $objGroup = EventsSettings::load($this->intId);
            $objSelectedGroup = EventsSettings::selectedByIdFromEventsSettings($this->intId);
            $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
            $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

            $objMenuContent->updateMenuContent($this->txtEventTitle->Text, $objGroup->getTitleSlug());

            $objGroup->setTitle($this->txtEventTitle->Text);
            $objGroup->setPostUpdateDate(Q\QDateTime::Now());
            $objGroup->save();

            $objFrontendLink->setTitle($this->txtEventTitle->Text);
            $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
            $objFrontendLink->save();

            if (!empty($_SESSION['events_edit_group']) || !empty($_SESSION['events_id']) && !empty($_SESSION['events_group'])) {
                $this->btnGoToEvents->Display = true;
                $this->btnGoToEvents->Enabled = true;
            }

            $this->txtEventGroup->Display = false;
            $this->txtEventTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;

            $this->dtgEventsGroups->refresh();
            $this->dtgEventsGroups->removeCssClass('disabled');
            $this->dlgToast1->notify();
        }

        /**
         * Handles the click event for the cancel button.
         *
         * This method checks specific session variables related to events and adjusts the display
         * and state of certain UI elements accordingly. It primarily hides input fields and buttons
         * and resets their values, while enabling navigation to the events section if conditions are met.
         *
         * @param ActionParams $params The parameters associated with the action that triggered the click event.
         *
         * @return void Does not return any value.
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['events_edit_group']) || !empty($_SESSION['events_id']) && !empty($_SESSION['events_group'])) {
                $this->btnGoToEvents->Display = true;
                $this->btnGoToEvents->Enabled = true;
            }

            $this->txtEventGroup->Display = false;
            $this->txtEventTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgEventsGroups->removeCssClass('disabled');
            $this->txtEventGroup->Text = '';
            $this->txtEventTitle->Text = '';
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the 'Go To Events' button click event, redirecting the user to different pages
         * based on available session data.
         *
         * This method checks session variables to determine which page the user should be redirected to.
         * If 'events_edit_group' is set in the session, it redirects to a menu edit page. Otherwise,
         * it checks for 'events_id' and 'events_group' to redirect the user to an event calendar edit page.
         * After redirection, the relevant session variables are cleared.
         *
         * @param ActionParams $params The parameters passed during the button click action.
         *
         * @return void This method does not return a value.
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnGoToEvents_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['events_edit_group'])) {
                Application::redirect('menu_edit.php?id=' . $_SESSION['events_edit_group']);
                unset($_SESSION['events_edit_group']);
            }

            if (!empty($_SESSION['events_id']) || !empty($_SESSION['events_group'])) {
                Application::redirect('event_calendar_edit.php?id=' . $_SESSION['events_id'] . '&group=' . $_SESSION['events_group']);
                unset($_SESSION['events_id']);
                unset($_SESSION['events_group']);
            }
        }
    }