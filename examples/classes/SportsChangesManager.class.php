<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\CellClick;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\Input;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\All;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;


    /**
     * Class SportsChangesManager
     *
     * Manages the Sports Changes component of the application, providing CRUD functionality,
     * filtering, pagination, and user-specific configurations for managing news change items.
     * This class extends a base Panel class to initialize user interaction, display elements,
     * and data-binding capabilities for News Changes.
     */
    class SportsChangesManager extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public SportsChangesTable $dtgEventsChanges;

        public Bs\Button $btnAddChange;
        public Bs\Button $btnGoToEvents;
        public Bs\TextBox $txtChange;
        public Q\Plugin\Control\RadioList $lstStatus;
        public Bs\Button $btnSaveChange;
        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected object $objUser;
        protected int $intLoggedUserId;

        protected string $strTemplate = 'EventsChangesManager.tpl.php';

        /**
         * Constructor for initializing the object with a parent object and an optional control ID.
         *
         * Sets up the logged-in user ID, retrieves the corresponding user object, and initializes various controls
         * and functionalities, such as items per a page, filter, data binder, buttons, and modals.
         *
         * @param mixed $objParentObject The parent object that owns this control.
         * @param string|null $strControlId An optional control ID for identifying the control.
         *
         * @throws Caller If an invalid operation is attempted during parent construction or initialization.
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

            $this->intLoggedUserId = 2;
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgEventsChanges_Create();
            $this->dtgEventsChanges->setDataBinder('BindData', $this);
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the `dtgEventsChanges` data grid for displaying sports event changes.
         *
         * This method sets up the data grid by creating the columns, configuring pagination,
         * making rows editable, and setting the callback for row parameters. It also sets
         * the default sort column and the number of items displayed per page based on the
         * user's settings.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgEventsChanges_Create(): void
        {
            $this->dtgEventsChanges = new SportsChangesTable($this);
            $this->dtgEventsChanges_CreateColumns();
            $this->createPaginators();
            $this->dtgEventsChanges_MakeEditable();
            $this->dtgEventsChanges->RowParamsCallback = [$this, "dtgEventsChanges_GetRowParams"];
            $this->dtgEventsChanges->SortColumnIndex = 0;
            $this->dtgEventsChanges->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
        }

        /**
         * Initializes and creates columns for the dtgEventsChanges datagrid component.
         * This function delegates the creation of columns to the datagrid object itself,
         * potentially setting up headers, data bindings, and other necessary configuration
         * for each column in the display.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgEventsChanges_CreateColumns(): void
        {
            $this->dtgEventsChanges->createColumns();
        }

        /**
         * Makes the dtgEventsChanges DataGrid editable by adding interactive features.
         *
         * This method adds a cell click action to the DataGrid, enabling Ajax control
         * for handling row clicks. It also assigns CSS classes to enhance the visual
         * presentation and interactivity of the rows.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgEventsChanges_MakeEditable(): void
        {
            $this->dtgEventsChanges->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgEventsChangesRow_Click'));
            $this->dtgEventsChanges->addCssClass('clickable-rows');
            $this->dtgEventsChanges->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for a row in the events changes data grid. This method
         * updates the UI to reflect the selected change, allowing the user to edit or
         * delete the change. It also disables certain UI elements to guide the user
         * through the change process.
         *
         * @param ActionParams $params An encapsulation of the parameters associated with
         *                             the action, including the action parameter which
         *                             identifies the specific row that was clicked.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgEventsChangesRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);
            $objChanges = SportsChanges::load($this->intId);

            $this->txtChange->Text = $objChanges->getTitle();
            $this->txtChange->focus();
            $this->lstStatus->SelectedValue = $objChanges->Status ?? null;

            $this->dtgEventsChanges->addCssClass('disabled');
            $this->btnAddChange->Enabled = false;
            $this->btnGoToEvents->Display = false;
            $this->txtChange->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;
        }

        /**
         * Retrieves the parameters for a row in the data grid based on the provided object.
         *
         * @param mixed $objRowObject The object representing the row for which parameters are needed. It should have a method or property to get the primary key.
         * @param int $intRowIndex The index of the row in the data grid.
         *
         * @return array An associative array containing the parameters for the row, with 'data-value' set to the primary key of the row object.
         */
        public function dtgEventsChanges_GetRowParams(mixed $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures the paginators for the sportsChanges data grid.
         *
         * This method sets up the paginator with labels for the previous and next buttons,
         * defines the number of items to be displayed per page, the sort column index,
         * and enables AJAX functionality for the data grid. It also applies filter actions
         * to further refine the displayed data.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgEventsChanges->Paginator = new Bs\Paginator($this);
            $this->dtgEventsChanges->Paginator->LabelForPrevious = t('Previous');
            $this->dtgEventsChanges->Paginator->LabelForNext = t('Next');

            $this->dtgEventsChanges->ItemsPerPage = 10;
            $this->dtgEventsChanges->SortColumnIndex = 4;
            $this->dtgEventsChanges->UseAjax = true;
            $this->addFilterActions();
        }

        /**
         * Creates and configures a UI component for selecting items per a page for the assigned user.
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
         * @return ListItem[] An array of ListItem objects representing items per page
         *                    associated with the assigned user object, with the
         *                    current user's item marked as selected if applicable.
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
         * Handles changes to the items per page selection for events by the assigned user.
         *
         * @param ActionParams $params The parameters associated with the action triggering this change.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgEventsChanges->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgEventsChanges->refresh();
        }

        /**
         * Initializes the filter component used for searching.
         *
         * @return void
         * @throws Caller
         */
        protected function createFilter(): void
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

            $this->dtgEventsChanges->refresh();
        }

        /**
         * Adds filter actions to the text filter input.
         *
         * This method associates specific actions with events on the txtFilter input control.
         * It sets up an action to trigger on user input after a delay of 300 milliseconds,
         * and another set of actions to trigger when the Enter key is pressed.
         *
         * @return void
         * @throws Caller
         */
        protected function addFilterActions(): void
        {
            $this->txtFilter->addAction(new Input(300), new AjaxControl($this, 'filterChanged'));
            $this->txtFilter->addActionArray(new EnterKey(),
                [
                    new AjaxControl($this, 'FilterChanged'),
                    new Terminate()
                ]
            );
        }

        /**
         * Refreshes the data grid for events when the filter is changed.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgEventsChanges->refresh();
        }

        /**
         * Binds data to the events changes data grid based on a specified condition.
         *
         * This method retrieves a condition using the getCondition method and utilizes it
         * to bind data to the dtgEventsChanges data grid object.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgEventsChanges->bindData($objCondition);
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
                    QQ::equal(QQN::SportsChanges()->Id, $strSearchValue),
                    QQ::like(QQN::SportsChanges()->Title, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures buttons and related UI elements for user interactions.
         *
         * @return void This method does not return any value.
         * @throws Caller
         * @throws InvalidCast
         */
        public function createButtons(): void
        {
            $this->btnAddChange = new Bs\Button($this);
            $this->btnAddChange->Text = t(' Create a new change');
            $this->btnAddChange->Glyph = 'fa fa-plus';
            $this->btnAddChange->CssClass = 'btn btn-orange';
            $this->btnAddChange->addWrapperCssClass('center-button');
            $this->btnAddChange->CausesValidation = false;
            $this->btnAddChange->addAction(new Click(), new AjaxControl($this, 'btnAddChange_Click'));
            $this->btnAddChange->setCssStyle('float', 'left');
            $this->btnAddChange->setCssStyle('margin-right', '10px');

            $this->btnGoToEvents = new Bs\Button($this);
            $this->btnGoToEvents->Text = t('Go to this sport\'s calendar');
            $this->btnGoToEvents->addWrapperCssClass('center-button');
            $this->btnGoToEvents->CssClass = 'btn btn-default';
            $this->btnGoToEvents->CausesValidation = false;
            $this->btnGoToEvents->addAction(new Click(), new AjaxControl($this, 'btnGoToEvents_Click'));
            $this->btnGoToEvents->setCssStyle('float', 'left');

            if (!empty($_SESSION['sports_changes']) && !empty($_SESSION['sports_group'])) {
                $this->btnGoToEvents->Display = true;
            } else {
                $this->btnGoToEvents->Display = false;
            }

            $this->txtChange = new Bs\TextBox($this);
            $this->txtChange->Placeholder = t('New change');
            $this->txtChange->ActionParameter = $this->txtChange->ControlId;
            $this->txtChange->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtChange->setHtmlAttribute('autocomplete', 'off');
            $this->txtChange->setCssStyle('float', 'left');
            $this->txtChange->setCssStyle('margin-right', '10px');
            $this->txtChange->Width = 300;
            $this->txtChange->Display = false;

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange form-horizontal radio-inline';
            $this->lstStatus->setCssStyle('float', 'left');
            $this->lstStatus->setCssStyle('margin-left', '15px');
            $this->lstStatus->setCssStyle('margin-right', '15px');
            $this->lstStatus->Display = false;

            $this->btnSaveChange = new Bs\Button($this);
            $this->btnSaveChange->Text = t('Save');
            $this->btnSaveChange->CssClass = 'btn btn-orange';
            $this->btnSaveChange->addWrapperCssClass('center-button');
            $this->btnSaveChange->PrimaryButton = true;
            $this->btnSaveChange->CausesValidation = true;
            $this->btnSaveChange->addAction(new Click(), new AjaxControl($this, 'btnSaveChange_Click'));
            $this->btnSaveChange->setCssStyle('float', 'left');
            $this->btnSaveChange->setCssStyle('margin-right', '10px');
            $this->btnSaveChange->Display = false;

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
            $this->btnDelete->addAction(new Click(), new AjaxControl($this, 'btnDelete_Click'));
            $this->btnDelete->setCssStyle('float', 'left');
            $this->btnDelete->setCssStyle('margin-right', '10px');
            $this->btnDelete->Display = false;

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
            $this->btnCancel->setCssStyle('float', 'left');
            $this->btnCancel->Display = false;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes toastr notifications with predefined settings for success and error alerts.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The change has been saved or modified.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('The change name must exist!');
            $this->dlgToastr2->ProgressBar = true;
        }

        /**
         * Creates and initializes a set of modal dialog windows with specific configurations for warnings, tips,
         * and notifications related to changes and their respective actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this change?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
                ['class' => 'btn btn-default']);
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'deleteItem_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'hideItem_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This change cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this change, simply release all 
                                    previously created changes related to sporting events.</p>');
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This change cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this change, simply release all 
                                    previously created changes related to sports events.</p>');
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The title of this change already 
                                 exists in the database, please choose another title!</p>');
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
         * Handles the button click event for adding changes. This method performs several UI updates,
         * such as hiding the "Go To Events" button, showing the text input and status list for changes,
         * selecting a default status, and enabling or disabling relevant buttons. Additionally, it resets
         * the change input text and focuses on it while disabling further change additions.
         *
         * @param ActionParams $params
         *
         * @return void
         * @throws RandomException
         */
        protected function btnAddChange_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->btnGoToEvents->Display = false;
            $this->txtChange->Display = true;
            $this->lstStatus->Display = true;
            $this->lstStatus->SelectedValue = 2;
            $this->btnSaveChange->Display = true;
            $this->btnCancel->Display = true;
            $this->txtChange->Text = '';
            $this->txtChange->focus();
            $this->btnAddChange->Enabled = false;
            $this->dtgEventsChanges->addCssClass('disabled');
        }

        /**
         * Handles the click event for the Save Change button.
         *
         * @param ActionParams $params Action parameters containing event information.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnSaveChange_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->txtChange->Text) {
                if (!SportsChanges::titleExists(trim($this->txtChange->Text))) {
                    $objCategoryNews = new SportsChanges();
                    $objCategoryNews->setTitle(trim($this->txtChange->Text));
                    $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                    $objCategoryNews->setPostDate(Q\QDateTime::now());
                    $objCategoryNews->save();

                    $this->dtgEventsChanges->refresh();

                    if (!empty($_SESSION['sports_changes']) || !empty($_SESSION['sports_group'])) {
                        $this->btnGoToEvents->Display = true;
                    }

                    $this->displayHelper();
                    $this->txtChange->Text = '';
                    $this->dlgToastr1->notify();
                } else {
                    $this->txtChange->Text = '';
                    $this->txtChange->focus();
                    $this->dlgModal4->showDialogBox();
                }
            } else {
                $this->txtChange->Text = '';
                $this->txtChange->focus();
                $this->dlgToastr2->notify();
            }
        }

        /**
         * Handles the click event to save a button within the context of sports changes.
         * This method updates the status and title of a sports change, displays dialogs based on conditions,
         * and manages the UI state of various controls.
         *
         * @param ActionParams $params The parameters received from the button click event, which may include
         *                             context about the action performed.
         *
         * @return void This method does not return a value. It performs operations related to saving and updating sports changes.
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

            $objChanges = SportsChanges::loadById($this->intId);

            if ($this->txtChange->Text) {
                if (SportsChanges::titleExists(trim($this->txtChange->Text)) && $this->lstStatus->SelectedValue ==  $objChanges->getStatus()) {
                    $this->txtChange->Text = $objChanges->getTitle();
                    $this->dlgModal4->showDialogBox();
                    return;
                }

                if (SportsCalendar::countByEventsChangesId($this->intId) > 0 && $this->lstStatus->SelectedValue == 2) {

                    if (!empty($_SESSION['sports_changes']) || !empty($_SESSION['sports_group'])) {
                        $this->btnGoToEvents->Display = true;
                    }

                    $this->lstStatus->SelectedValue = 1;
                    $this->displayHelper();
                    $this->dlgModal2->showDialogBox();

                } else if (($this->txtChange->Text == $objChanges->getTitle() && $this->lstStatus->SelectedValue !== $objChanges->getStatus()) ||
                    ($this->txtChange->Text != $objChanges->getTitle() && $this->lstStatus->SelectedValue == $objChanges->getStatus())) {
                    $objChanges->setTitle(trim($this->txtChange->Text));
                    $objChanges->setStatus($this->lstStatus->SelectedValue);
                    $objChanges->setPostUpdateDate(Q\QDateTime::now());
                    $objChanges->save();

                    if (!empty($_SESSION['sports_changes']) || !empty($_SESSION['sports_group'])) {
                        $this->btnGoToEvents->Display = true;
                    }

                    $this->dtgEventsChanges->refresh();
                    $this->displayHelper();
                    $this->dlgToastr1->notify();
                }
            } else {
                $this->txtChange->Text = $objChanges->getTitle();
                $this->txtChange->focus();
                $this->dlgToastr2->notify();
            }
        }

        /**
         * Handles the click event for the delete button. Determines if the current item can be deleted
         * based on its ID and updates the UI accordingly. If the item ID is found in the list of change IDs,
         * a confirmation dialog is displayed, and UI elements are enabled or disabled. If not, a warning dialog is
         * shown.
         *
         * @param ActionParams $params Parameters associated with the action of clicking the delete button.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

                if (!empty($_SESSION['sports_changes']) || !empty($_SESSION['sports_group'])) {
                    $this->btnGoToEvents->Display = true;
                }

            if (SportsCalendar::countByEventsChangesId($this->intId) > 0) {
                $this->dlgModal3->showDialogBox();
                $this->displayHelper();
            } else {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the delete item click event. Checks the action parameter and deletes the item if the parameter is "pass".
         * Otherwise, it adjusts the display properties of various controls based on session variables.
         *
         * @param ActionParams $params The parameters associated with the action, including the action parameter that determines deletion.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objChanges = SportsChanges::loadById($this->intId);

            if ($params->ActionParameter == "pass") {
                $objChanges->delete();
            }

            if (!empty($_SESSION['sports_changes']) || !empty($_SESSION['sports_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            $this->dtgEventsChanges->refresh();

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
         * Handles the cancel action for the change interface. Adjusts the display and enabled status of various
         * controls depending on session variables.
         *
         * @param ActionParams $params The parameters associated with the action triggering this method.
         *
         * @return void
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['sports_changes']) || !empty($_SESSION['sports_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            $this->displayHelper();
            $this->txtChange->Text = '';
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
            $this->txtChange->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSaveChange->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->btnAddChange->Enabled = true;
            $this->dtgEventsChanges->removeCssClass('disabled');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for navigation to the sports calendar editing page.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void This method does not return any values but redirects to another page if conditions are met.
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

            if (!empty($_SESSION['sports_changes']) || !empty($_SESSION['sports_group'])) {
                Application::redirect('sports_calendar_edit.php?id=' . $_SESSION['sports_changes'] . '&group=' . $_SESSION['sports_group']);
                unset($_SESSION['sports_changes']);
                unset($_SESSION['sports_group']);
            }
        }
    }