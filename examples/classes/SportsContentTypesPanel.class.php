<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
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
    use QCubed\Query\Condition\All;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;

    /**
     * The SportsContentTypesPanel class extends the base Panel class and is responsible for managing
     * and displaying sports content types in a grid format. It provides functionalities for filtering,
     * pagination, adding, updating, and deleting sports content types through an interactive UI.
     */
    class SportsContentTypesPanel extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
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
        public SportsContentTypesTable $dtgSportsContentTypes;

        public Bs\Button $btnAddType;
        public Bs\TextBox $txtType;
        public Q\Plugin\Control\RadioList $lstStatus;
        public Bs\Button $btnSaveType;
        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected object $objUser;
        protected int $intLoggedUserId;
        protected array $objContentTypes = [];
        protected string $oldName;

        protected string $strTemplate = 'SportsContentTypesPanel.tpl.php';

        /**
         * Constructor for initializing the object instance. It sets up the logged-in user, creates the necessary
         * components, and initializes data binding and other required features.
         *
         * @param mixed $objParentObject The parent object for this instance, typically a form or panel.
         * @param string|null $strControlId Optional control ID to uniquely identify this object.
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

            $this->intLoggedUserId = 2;
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgSportsContentTypes_Create();
            $this->dtgSportsContentTypes->setDataBinder('BindData', $this);
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the data grid for displaying sports content types.
         * This method creates the necessary columns, paginators, and sets the grid to be editable.
         * Additionally, it assigns callbacks and default settings for sorting and pagination.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgSportsContentTypes_Create(): void
        {
            $this->dtgSportsContentTypes = new SportsContentTypesTable($this);
            $this->dtgSportsContentTypes_CreateColumns();
            $this->createPaginators();
            $this->dtgSportsContentTypes_MakeEditable();
            $this->dtgSportsContentTypes->RowParamsCallback = [$this, "dtgSportsContentTypes_GetRowParams"];
            $this->dtgSportsContentTypes->SortColumnIndex = 0;
            $this->dtgSportsContentTypes->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
        }

        /**
         * Initializes and creates columns for the data grid associated with sports content types.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgSportsContentTypes_CreateColumns(): void
        {
            $this->dtgSportsContentTypes->createColumns();
        }

        /**
         * Configures the sports content types data grid to be editable by adding
         * interactivity and styling. Registers an action to handle cell click events
         * and assigns necessary CSS classes for visual feedback.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgSportsContentTypes_MakeEditable(): void
        {
            $this->dtgSportsContentTypes->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgSportsContentTypesRow_Click'));
            $this->dtgSportsContentTypes->addCssClass('clickable-rows');
            $this->dtgSportsContentTypes->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for a row in the sports content types data grid.
         *
         * @param ActionParams $params The parameters associated with the action,
         * containing the identifier of the sports content type.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgSportsContentTypesRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);
            $objTypes = SportsContentTypes::load($this->intId);

            $this->oldName = $objTypes->getName();

            $this->txtType->Text = $objTypes->getName();
            $this->txtType->focus();
            $this->lstStatus->SelectedValue = $objTypes->Status ?? null;

            $this->dtgSportsContentTypes->addCssClass('disabled');
            $this->btnAddType->Enabled = false;
            $this->txtType->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;
        }

        /**
         * Retrieves the parameters for a row in the sports content types data grid.
         *
         * @param object $objRowObject The object representing the current row.
         * @param int $intRowIndex The index of the current row.
         *
         * @return array An associative array of parameters for the row, including a 'data-value' key.
         */
        public function dtgSportsContentTypes_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes paginators for the sports content types data grid.
         * Configures the paginator with labels for navigation, the number of items per a page,
         * the default sort column index, and enables AJAX for data retrieval.
         * Invokes an additional filter actions configuration.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgSportsContentTypes->Paginator = new Bs\Paginator($this);
            $this->dtgSportsContentTypes->Paginator->LabelForPrevious = t('Previous');
            $this->dtgSportsContentTypes->Paginator->LabelForNext = t('Next');

            $this->dtgSportsContentTypes->ItemsPerPage = 10;
            $this->dtgSportsContentTypes->SortColumnIndex = 4;
            $this->dtgSportsContentTypes->UseAjax = true;
            $this->addFilterActions();
        }

        /**
         * Initializes the items per page selection component with specific attributes and settings.
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
         * Retrieves a list of items associated with the assigned user object, applying specific conditions and clauses.
         * Iterates through the result set, creating a list item for each entry and marking it as selected if it matches the user's current assignment.
         *
         * @return ListItem[] Returns an array of ListItem objects, representing the items per page associated with the assigned user object.
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
         * Updates the items per page setting of the data grid based on the selected name
         * from the list control and refreshes the data grid.
         *
         * @param ActionParams $params The parameters associated with the action event.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgSportsContentTypes->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgSportsContentTypes->refresh();
        }

        /**
         * Initializes and configures the filter text box used for search functionality.
         * Sets placeholder text, text mode, disables autocomplete, and adds a CSS class.
         * Also invokes the method to add related filter actions.
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

            $this->dtgSportsContentTypes->refresh();
        }

        /**
         * Adds filter actions to the user interface component. It sets up event
         * listeners on the filter input to trigger AJAX calls when the input value
         * changes or when the enter key is pressed. This allows for dynamic filtering
         * of data without requiring a page reload.
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
         * Refreshes the data grid containing sports content types when a filter change is detected.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgSportsContentTypes->refresh();
        }

        /**
         * Binds data to the sports content types data grid using a specified condition.
         *
         * The method retrieves the condition for data binding through `getCondition()`
         * and applies it to the `dtgSportsContentTypes` for data binding.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgSportsContentTypes->bindData($objCondition);
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
                    QQ::equal(QQN::SportsContentTypes()->Id, $strSearchValue),
                    QQ::like(QQN::SportsContentTypes()->Name, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a set of buttons and controls for managing types.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createButtons(): void
        {
            $this->btnAddType = new Bs\Button($this);
            $this->btnAddType->Text = t(' Create a new type');
            $this->btnAddType->Glyph = 'fa fa-plus';
            $this->btnAddType->CssClass = 'btn btn-orange';
            $this->btnAddType->addWrapperCssClass('center-button');
            $this->btnAddType->CausesValidation = false;
            $this->btnAddType->addAction(new Click(), new AjaxControl($this, 'btnAddType_Click'));
            $this->btnAddType->setCssStyle('float', 'left');
            $this->btnAddType->setCssStyle('margin-right', '10px');

            $this->txtType = new Bs\TextBox($this);
            $this->txtType->Placeholder = t('New type');
            $this->txtType->ActionParameter = $this->txtType->ControlId;
            $this->txtType->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
            $this->txtType->setHtmlAttribute('autocomplete', 'off');
            $this->txtType->setCssStyle('float', 'left');
            $this->txtType->setCssStyle('margin-right', '10px');
            $this->txtType->Width = 300;
            $this->txtType->Display = false;

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange form-horizontal radio-inline';
            $this->lstStatus->setCssStyle('float', 'left');
            $this->lstStatus->setCssStyle('margin-left', '15px');
            $this->lstStatus->setCssStyle('margin-right', '15px');
            $this->lstStatus->Display = false;

            $this->btnSaveType = new Bs\Button($this);
            $this->btnSaveType->Text = t('Save');
            $this->btnSaveType->CssClass = 'btn btn-orange';
            $this->btnSaveType->addWrapperCssClass('center-button');
            $this->btnSaveType->PrimaryButton = true;
            $this->btnSaveType->CausesValidation = true;
            $this->btnSaveType->addAction(new Click(), new AjaxControl($this, 'btnSaveType_Click'));
            $this->btnSaveType->setCssStyle('float', 'left');
            $this->btnSaveType->setCssStyle('margin-right', '10px');
            $this->btnSaveType->Display = false;

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
         * Initializes and configures two toastr notifications, one for success and one for error.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The type has been saved or modified.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('The type name must exist!');
            $this->dlgToastr2->ProgressBar = true;
        }

        /**
         * Initializes and configures multiple modal dialogs for user interactions and confirmations.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the sports content type?</p>
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The content type cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this content type, 
                                    simply release any content type previously associated with created sports calendar.</p>');
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The content type cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this content type, 
                                    simply release any content type previously associated with created sports calendar.</p>');
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The name of this content type already exists in the database, please choose another name!</p>');
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
         * Handles the click event for the Add Type button.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws RandomException
         */
        protected function btnAddType_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->txtType->Display = true;
            $this->lstStatus->Display = true;
            $this->lstStatus->SelectedValue = 2;
            $this->btnSaveType->Display = true;
            $this->btnCancel->Display = true;
            $this->txtType->Text = '';
            $this->txtType->focus();
            $this->btnAddType->Enabled = false;
            $this->dtgSportsContentTypes->addCssClass('disabled');
        }

        /**
         * Handles the click event for save a button to create a new sports content type.
         *
         * @param ActionParams $params Parameters passed from the click action triggering the method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnSaveType_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->txtType->Text) {
                if (!SportsContentTypes::titleExists(trim($this->txtType->Text))) {
                    $objCategoryNews = new SportsContentTypes();
                    $objCategoryNews->setName(trim($this->txtType->Text));
                    $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                    $objCategoryNews->setPostDate(Q\QDateTime::now());
                    $objCategoryNews->save();

                    $this->dtgSportsContentTypes->refresh();

                    $this->txtType->Display = false;
                    $this->lstStatus->Display = false;
                    $this->btnSaveType->Display = false;
                    $this->btnCancel->Display = false;
                    $this->btnAddType->Enabled = true;
                    $this->dtgSportsContentTypes->removeCssClass('disabled');
                    $this->txtType->Text = '';
                    $this->dlgToastr1->notify();
                } else {
                    $this->txtType->Text = '';
                    $this->txtType->focus();
                    $this->dlgModal4->showDialogBox();
                }
            } else {
                $this->txtType->Text = '';
                $this->txtType->focus();
                $this->dlgToastr2->notify();
            }
        }

        /**
         * Handles the click event to save a button. This function checks and updates
         * the sports content types according to the user input and displays appropriate dialogs.
         *
         * @param ActionParams $params The parameters associated with the click action.
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

            $objContentType = SportsContentTypes::load($this->intId);

            if ($this->txtType->Text) {
                if (SportsTables::countBySportsContentTypesId($this->intId) > 0 && $this->lstStatus->SelectedValue == 2) {
                    $this->lstStatus->SelectedValue = 1;
                    $this->dlgModal2->showDialogBox();

                    $this->btnAddType->Enabled = true;
                    $this->txtType->Display = false;
                    $this->lstStatus->Display = false;
                    $this->btnSave->Display = false;
                    $this->btnDelete->Display = false;
                    $this->btnCancel->Display = false;
                    $this->dtgSportsContentTypes->removeCssClass('disabled');

                } else if ($this->txtType->Text == $objContentType->getName() && $this->lstStatus->SelectedValue !== $objContentType->getStatus()) {
                    $objContentType->setName(trim($this->txtType->Text));
                    $objContentType->setStatus($this->lstStatus->SelectedValue);
                    $objContentType->setPostUpdateDate(Q\QDateTime::now());
                    $objContentType->save();

                    $this->dtgSportsContentTypes->refresh();
                    $this->btnAddType->Enabled = true;

                    $this->txtType->Display = false;
                    $this->lstStatus->Display = false;
                    $this->btnSave->Display = false;
                    $this->btnDelete->Display = false;
                    $this->btnCancel->Display = false;

                    $this->dtgSportsContentTypes->removeCssClass('disabled');
                    $this->txtType->Text = $objContentType->getName();
                    $this->dlgToastr1->notify();

                } else if (SportsContentTypes::titleExists(trim($this->txtType->Text))) {
                    $this->txtType->Text = $objContentType->getName();
                    $this->dlgModal4->showDialogBox();
                }
            } else {
                $this->txtType->Text = $objContentType->getName();
                $this->txtType->focus();
                $this->dlgToastr2->notify();
            }
        }

        /**
         * Handles the deletion process when the delete button is clicked.
         * This method checks if the current ID is in the list of content types.
         * If it is, it shows a modal dialog box and updates the display and enabled state
         * of various controls. Otherwise, it shows a different modal dialog box.
         *
         * @param ActionParams $params Parameters associated with the delete button click action.
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

            if (SportsTables::countBySportsContentTypesId($this->intId) > 0) {
                $this->dlgModal3->showDialogBox();

                $this->btnAddType->Enabled = true;
                $this->txtType->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;
                $this->dtgSportsContentTypes->removeCssClass('disabled');
            } else {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the click event for deleting a sports content type item.
         * Depending on the specified action parameter, it deletes the content type
         * and updates the UI components to reflect the changes.
         *
         * @param ActionParams $params Contains the parameters for the action, including the condition for deletion.
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

            $objContentTypes = SportsContentTypes::loadById($this->intId);

            if ($params->ActionParameter == "pass") {
                $objContentTypes->delete();
            }

            $this->dtgSportsContentTypes->refresh();
            $this->btnAddType->Enabled = true;
            $this->txtType->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;

            $this->dtgSportsContentTypes->removeCssClass('disabled');
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

            $this->btnAddType->Enabled = true;
            $this->txtType->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;

            $this->dtgSportsContentTypes->removeCssClass('disabled');
            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the event when the cancel button is clicked. It hides various form elements,
         * resets text fields, and enables certain buttons within the user interface.
         *
         * @param ActionParams $params The parameters associated with the action event triggered by clicking the button.
         *
         * @return void This method does not return a value.
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->txtType->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSaveType->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->btnAddType->Enabled = true;
            $this->dtgSportsContentTypes->removeCssClass('disabled');
            $this->txtType->Text = '';
        }
    }