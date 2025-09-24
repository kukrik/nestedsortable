<?php

    use QCubed as Q;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\Panel;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\CellClick;
    use QCubed\Event\Change;
    use QCubed\Event\Click;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\Input;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\Action\ActionParams;
    use QCubed\Query\Condition\All;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;
    use Random\RandomException;

    /**
     * Class TargetCroupPanel
     *
     * Represents a user interface panel for managing target groups within a system.
     * It includes functionalities for filtering, paginated data grid handling,
     * and user interaction with target group objects.
     *
     * Extends from the Panel class and contains custom methods
     * for data grid configuration, event handling for rows,
     * AJAX interactions, pagination, and UI element initialization.
     */
    class TargetCroupPanel extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public CalendarTargetGroupTable $dtgTargetGroup;

        public Bs\TextBox $txtTargetGroup;
        public  Q\Plugin\Control\RadioList $lstStatus;
        public Bs\Button $btnAddTargetGroup;
        public Bs\Button $btnGoToEvents;
        public Bs\Button $btnSaveNew;
        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected object $objUser;
        protected int $intLoggedUserId;

        protected string $strTemplate = 'TargetCroupPanel.tpl.php';

        /**
         * Constructor method for initializing the object.
         *
         * @param mixed $objParentObject This represents the parent object (e.g., a form or panel) to which this
         *     control belongs.
         * @param string|null $strControlId An optional control ID that can be used to uniquely identify this object
         *     within its parent.
         *
         * @return void
         *
         * @throws DateMalformedStringException
         * @throws Caller If there is an issue during the parent constructor call.
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
            $this->dtgTargetGroup_Create();
            $this->dtgTargetGroup->setDataBinder('bindData', $this);

            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and sets up the data grid for the target group.
         *
         * This method creates a new instance of CalendarTargetGroupTable and configures its columns, pagination,
         * and editability. It also specifies a callback for row parameters and sets default sorting and pagination
         * settings.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgTargetGroup_Create(): void
        {
            $this->dtgTargetGroup = new CalendarTargetGroupTable($this);
            $this->dtgTargetGroup_CreateColumns();
            $this->createPaginators();
            $this->dtgTargetGroup_MakeEditable();
            $this->dtgTargetGroup->RowParamsCallback = [$this, "dtgTargetGroup_GetRowParams"];
            $this->dtgTargetGroup->SortColumnIndex = 0;
            $this->dtgTargetGroup->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
        }

        /**
         * Creates columns for the dtgTargetGroup.
         *
         * @return void No return value.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgTargetGroup_CreateColumns(): void
        {
            $this->dtgTargetGroup->createColumns();
        }

        /**
         * Configures the target group data grid to be editable by adding click events and CSS classes.
         *
         * This method enables row-level click events on the data grid for editing purposes.
         * It attaches an Ajax control action to handle the clicks and applies CSS styling
         * to make the rows visually distinct as interactive elements.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgTargetGroup_MakeEditable(): void
        {
            $this->dtgTargetGroup->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgTargetGroup_Click'));
            $this->dtgTargetGroup->addCssClass('clickable-rows');
            $this->dtgTargetGroup->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event on the target group data grid. Loads the target group data,
         * updates UI components, and manages session-state-related actions.
         *
         * @param ActionParams $params Parameters from the action event, typically containing context or action-specific data.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgTargetGroup_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);

            $objTargetGroup = TargetGroupOfCalendar::load($this->intId);
            $this->txtTargetGroup->Text = $objTargetGroup->Name;
            $this->lstStatus->SelectedValue = $objTargetGroup->IsEnabled;
            $this->txtTargetGroup->focus();

            $this->btnAddTargetGroup->Enabled = false;
            $this->dtgTargetGroup->addCssClass('disabled');

            $this->btnGoToEvents->Display = false;
            $this->txtTargetGroup->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;
        }

        /**
         * Retrieves the parameters for a specific row in the target group data grid.
         *
         * @param object $objRowObject The object representing the row for which parameters are being retrieved.
         * @param int $intRowIndex The index of the row in the data grid.
         *
         * @return array An associative array containing parameters for the specified row, including a data-value attribute with the primary key.
         */
        public function dtgTargetGroup_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures the paginator for the target data grid.
         *
         * The paginator navigation labels are set to 'Previous' and 'Next'.
         * The number of items displayed per page is set to 10.
         * The AJAX behavior is enabled to handle sorting and pagination.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgTargetGroup->Paginator = new Bs\Paginator($this);
            $this->dtgTargetGroup->Paginator->LabelForPrevious = t('Previous');
            $this->dtgTargetGroup->Paginator->LabelForNext = t('Next');

            //$this->dtgTargetGroup->PaginatorAlternate = new Bs\Paginator($this);
            //$this->dtgTargetGroup->PaginatorAlternate->LabelForPrevious = t('Previous');
            //$this->dtgTargetGroup->PaginatorAlternate->LabelForNext = t('Next');

            $this->dtgTargetGroup->ItemsPerPage = 10;
            $this->dtgTargetGroup->SortColumnIndex = 0;
            $this->dtgTargetGroup->UseAjax = true;
            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a Select2 control for selecting items per a page by an assigned user.
         * The control is configured with specific appearance and behavior settings, including the theme, width,
         * and selection mode. It populates the list with items retrieved from a separate method and sets an
         * action to handle changes.
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
         * Retrieves a list of items per a page associated with the assigned user object.
         *
         * @return ListItem[] An array of ListItem objects, each representing an item per page.
         *                     The currently assigned user object item will be marked as selected.
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
         * Handles the change event for the ItemsPerPageByAssignedUserObject list.
         *
         * @param ActionParams $params The parameters associated with the action triggering the change event.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgTargetGroup->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgTargetGroup->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a search filter text box. The text box is set with
         * a placeholder for search, configured for search mode, disables autocomplete,
         * and applies a CSS class for styling. Additional filter actions are also registered.
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

            $this->dtgTargetGroup->refresh();
        }

        /**
         * Adds filter actions to the text filter control. Two types of actions are added:
         * an input event and an enter key event. The input event triggers an Ajax
         * control action when typing occurs in the filter field after a delay of 300 milliseconds.
         * The enter key event triggers an Ajax control action immediately when the enter key is pressed,
         * followed by a terminate action to stop further event processing.
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
         * Refreshes the data grid for the target group when a filter is changed.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgTargetGroup->refresh();
        }

        /**
         * Binds data to the target group data grid using the specified condition.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgTargetGroup->bindData($objCondition);
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
                    QQ::like(QQN::TargetGroupOfCalendar()->Name, "%" . $strSearchValue . "%"),
                    QQ::equal(QQN::TargetGroupOfCalendar()->IsEnabled, $strSearchValue)
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures several button and input controls used in a user interface.
         * Sets up styles, actions, and properties to ensure a proper layout and functionality.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createButtons(): void
        {
            $this->btnAddTargetGroup = new Bs\Button($this);
            $this->btnAddTargetGroup->Text = t(' Add target group');
            $this->btnAddTargetGroup->Glyph = 'fa fa-plus';
            $this->btnAddTargetGroup->CssClass = 'btn btn-orange';
            $this->btnAddTargetGroup->addWrapperCssClass('center-button');
            $this->btnAddTargetGroup->CausesValidation = false;
            $this->btnAddTargetGroup->addAction(new Click(), new AjaxControl($this, 'btnAddTargetGroup_Click'));
            $this->btnAddTargetGroup->setCssStyle('float', 'left');
            $this->btnAddTargetGroup->setCssStyle('margin-right', '10px');

            $this->btnGoToEvents = new Bs\Button($this);
            $this->btnGoToEvents->Text = t('Go to the calendar events');
            $this->btnGoToEvents->addWrapperCssClass('center-button');
            $this->btnGoToEvents->CssClass = 'btn btn-default';
            $this->btnGoToEvents->CausesValidation = false;
            $this->btnGoToEvents->addAction(new Click(), new AjaxControl($this, 'btnGoToEvents_Click'));
            $this->btnGoToEvents->setCssStyle('float', 'left');
            $this->btnGoToEvents->setCssStyle('margin-right', '10px');

            if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
                $this->btnGoToEvents->Display = true;
            } else {
                $this->btnGoToEvents->Display = false;
            }

            $this->txtTargetGroup = new Bs\TextBox($this);
            $this->txtTargetGroup->Placeholder = t('Target group');
            $this->txtTargetGroup->MaxLength = TargetGroupOfCalendar::NAME_MAX_LENGTH;
            $this->txtTargetGroup->ActionParameter = $this->txtTargetGroup->ControlId;
            $this->txtTargetGroup->setHtmlAttribute('autocomplete', 'off');
            $this->txtTargetGroup->setCssStyle('float', 'left');
            $this->txtTargetGroup->setCssStyle('margin-right', '10px');
            $this->txtTargetGroup->Width = 300;
            $this->txtTargetGroup->Display = false;

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstStatus->setCssStyle('float', 'left');
            $this->lstStatus->setCssStyle('margin-left', '10px');
            $this->lstStatus->setCssStyle('margin-right', '10px');
            $this->lstStatus->Display = false;

            $this->btnSaveNew = new Bs\Button($this);
            $this->btnSaveNew->Text = t('Save');
            $this->btnSaveNew->CssClass = 'btn btn-orange';
            $this->btnSaveNew->addWrapperCssClass('center-button');
            $this->btnSaveNew->PrimaryButton = true;
            $this->btnSaveNew->CausesValidation = true;
            $this->btnSaveNew->addAction(new Click(), new AjaxControl($this, 'btnSaveNew_Click'));
            $this->btnSaveNew->setCssStyle('float', 'left');
            $this->btnSaveNew->setCssStyle('margin-right', '10px');
            $this->btnSaveNew->Display = false;

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));
            $this->btnSave->setCssStyle('float', 'left');
            $this->btnSave->setCssStyle('margin-right', '10px');
            $this->btnSave->Display = false;

            $this->btnDelete = new Bs\Button($this);
            $this->btnDelete->Text = t('Delete');
            $this->btnDelete->CssClass = 'btn btn-danger';
            $this->btnDelete->addWrapperCssClass('center-button');
            $this->btnDelete->CausesValidation = true;
            $this->btnDelete->addAction(new Click(), new AjaxControl($this, 'btnTargetGroupDelete_Click'));
            $this->btnDelete->setCssStyle('float', 'left');
            $this->btnDelete->setCssStyle('margin-right', '10px');
            $this->btnDelete->Display = false;

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnTargetGroupCancel_Click'));
            $this->btnCancel->setCssStyle('float', 'left');
            $this->btnCancel->Display = false;
        }

        /**
         * Initializes and configures multiple Toastr notification dialogs with predefined alert types, positions,
         * messages, and progress bars.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> To add a new target group to the database is successful.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('The target group is at least mandatory!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Well done!</strong> The target group has been saved or modified.');
            $this->dlgToastr3->ProgressBar = true;
        }

        /**
         * Creates and initializes multiple modal dialogs with predefined properties and actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the target group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
                ['class' => 'btn btn-default']);
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'deleteTargetGroupItem_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'hideItem_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The target group cannot be deleted
                                    at this time!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this target group,
                                    just must release target groups related to previously created calendar event.</p>');

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The target group cannot
                                    be deactivated at this time!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this target group,
                                    just must release target groups related to previously created calendar event.</p>');

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The name of this change already exists in the database, please choose another name!</p>');
            $this->dlgModal4->HeaderClasses = 'btn-darkblue';
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal5->Title = t("Warning");
            $this->dlgModal5->HeaderClasses = 'btn-danger';
            $this->dlgModal5->addCloseButton(t("I understand"));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the "Add Target Group" button, displaying and enabling various input controls,
         * and configuring their initial states for adding a new target group. Also manages button states based on
         * session data.
         *
         * @param ActionParams $params The parameters for the action event.
         *
         * @return void
         * @throws RandomException
         */
        protected function btnAddTargetGroup_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->txtTargetGroup->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSaveNew->Display = true;
            $this->btnCancel->Display = true;
            $this->txtTargetGroup->Text = '';
            $this->txtTargetGroup->focus();
            $this->lstStatus->SelectedValue = 2;
            $this->btnAddTargetGroup->Enabled = false;
            $this->dtgTargetGroup->addCssClass('disabled');

            if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
                $this->btnGoToEvents->Display = false;
            }
        }

        /**
         * Handles the click event for saving a new target group.
         * Validates the input for the target group name and saves the new target group if it doesn't exist.
         * Updates the UI components based on the operation's outcome.
         *
         * @param ActionParams $params The parameters associated with the action triggering the click event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnSaveNew_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->txtTargetGroup->Text) {
                if (!TargetGroupOfCalendar::titleExists(trim($this->txtTargetGroup->Text))) {
                    $objTargetGroup = new TargetGroupOfCalendar();
                    $objTargetGroup->setName($this->txtTargetGroup->Text);
                    $objTargetGroup->setIsEnabled($this->lstStatus->SelectedValue);
                    $objTargetGroup->setPostDate(Q\QDateTime::now());
                    $objTargetGroup->save();

                    if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
                        $this->btnGoToEvents->Display = true;
                    }

                    $this->dtgTargetGroup->refresh();

                    $this->displayHelper();
                    $this->txtTargetGroup->Text = '';
                    $this->dlgToastr1->notify();
                } else {
                    $this->txtTargetGroup->Text = '';
                    $this->txtTargetGroup->focus();
                    $this->dlgModal4->showDialogBox();
                }
            } else {
                $this->txtTargetGroup->Text = '';
                $this->txtTargetGroup->focus();
                $this->dlgToastr2->notify();
            }
        }

        /**
         * Handles the click event for save a button, updating the state of the target group based on the current selection and input.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (EventsCalendar::countByTargetGroupId($this->intId) > 0) {
                $this->dlgModal3->showDialogBox();
                $this->lstStatus->SelectedValue = 1;

                if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
                    $this->btnGoToEvents->Display = true;
                }

                $this->displayHelper();
                $this->dtgTargetGroup->removeCssClass('disabled');
                return;
            }

            $objTargetGroup = TargetGroupOfCalendar::load($this->intId);

            if ($this->txtTargetGroup->Text) {
                if (TargetGroupOfCalendar::titleExists(trim($this->txtTargetGroup->Text)) && $this->lstStatus->SelectedValue == $objTargetGroup->getIsEnabled()) {
                    $this->txtTargetGroup->Text = $objTargetGroup->getName();
                    $this->btnGoToEvents->Display = false;
                    $this->dlgModal4->showDialogBox();
                    return;
                }

                if (News::countByNewsChangesId($this->intId) > 0 && $this->lstStatus->SelectedValue == 2) {

                    if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
                        $this->btnGoToEvents->Display = true;
                    }

                    $this->lstStatus->SelectedValue = 1;
                    $this->displayHelper();
                    $this->dlgModal2->showDialogBox();

                } else if (($this->txtTargetGroup->Text == $objTargetGroup->getName() && $this->lstStatus->SelectedValue !== $objTargetGroup->getIsEnabled()) ||
                    ($this->txtTargetGroup->Text !== $objTargetGroup->getName() && $this->lstStatus->SelectedValue == $objTargetGroup->getIsEnabled())) {
                    $objTargetGroup->setName(trim($this->txtTargetGroup->Text));
                    $objTargetGroup->setIsEnabled($this->lstStatus->SelectedValue);
                    $objTargetGroup->setPostUpdateDate(Q\QDateTime::now());
                    $objTargetGroup->save();

                    if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
                        $this->btnGoToEvents->Display = true;
                    }

                    $this->dtgTargetGroup->refresh();
                    $this->displayHelper();
                    $this->dlgToastr1->notify();
                }
            } else {
                $this->txtTargetGroup->Text = $objTargetGroup->getName();
                $this->txtTargetGroup->focus();
                $this->dlgToastr2->notify();
            }
        }

        /**
         * Handles the click event for deleting a target group. It checks if the group ID is present in the list
         * of target group IDs, showing a modal dialog and updating the display of various controls based on session
         * data.
         *
         * @param ActionParams $params Contains parameters related to the action triggering this method.
         *
         * @return void This method doesn't return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnTargetGroupDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            if (EventsCalendar::countByTargetGroupId($this->intId) > 0) {
                $this->dlgModal2->showDialogBox();
                $this->displayHelper();
            } else {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the click event to delete a target group item from the calendar.
         *
         * This method performs the deletion of a target group associated with a calendar if the action parameter
         * is "pass". It then updates various user interface components based on the deletion outcome.
         *
         * @param ActionParams $params Parameters that include the action to be taken.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function deleteTargetGroupItem_Click(ActionParams $params): void
        {
            $objTargetGroup = TargetGroupOfCalendar::load($this->intId);

            if ($params->ActionParameter == "pass") {
                $objTargetGroup->delete();
            }

            $this->dtgTargetGroup->refresh();

            $this->displayHelper();

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the click event for hiding an item. This method verifies the CSRF token,
         * updates the display properties of UI elements, and refreshes the institution data grid.
         * If the CSRF token validation fails, a modal dialog is displayed, and a new token is generated.
         *
         * @param ActionParams $params The parameters passed from the action triggering this event.
         *
         * @return void
         * @throws RandomException
         */
        protected function hideItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->displayHelper();
        }

        /**
         * Handles the click event for the cancel button associated with target group operations.
         * This method hides various UI components related to target group actions and resets control states.
         *
         * @param ActionParams $params The parameters passed with the action event, typically includes any relevant
         *     context for the event.
         *
         * @return void This method does not return a value.
         * @throws RandomException
         */
        protected function btnTargetGroupCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            $this->displayHelper();
            $this->txtTargetGroup->Text = '';
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Configures the display settings for various UI components
         * and enables interactions for the link categories data grid.
         *
         * @return void
         */
        protected function displayHelper(): void
        {
            $this->txtTargetGroup->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSaveNew->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->btnAddTargetGroup->Enabled = true;

            $this->dtgTargetGroup->removeCssClass('disabled');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the 'Go To Events' button. Checks if there is a target ID or target group set in the session.
         * If set, redirects to the event calendar edit page with the target ID and group. Unsets the session variables after the redirection.
         *
         * @param ActionParams $params The parameters associated with the action event triggering this method.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnGoToEvents_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {

                Application::redirect('event_calendar_edit.php?id=' . $_SESSION['target_id'] . '&group=' . $_SESSION['target_group']);
                unset($_SESSION['target_id']);
                unset($_SESSION['target_group']);
            }
        }
    }