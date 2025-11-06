<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
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
     * Class OrganizingInstitutionManager
     *
     * This class manages the user interface and functionality related to organizing institutions.
     * It extends Q\Control\Panel, providing various components for user interaction,
     * data management, and editing functionalities for institutions.
     */
    class OrganizingInstitutionManager extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public OrganizingInstitutionTable $dtgInstitution;

        public Bs\Button $btnAddInstitution;
        public Bs\Button $btnGoToEvents;
        public Bs\TextBox $txtName;
        public Q\Plugin\Control\RadioList $lstStatus;
        public Bs\Button $btnSaveChange;
        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'OrganizingInstitutionManager.tpl.php';

        /**
         * Constructor for initializing the control with necessary setup for user and UI components.
         * Attempts to establish user context, configuring various elements such as items per a page, filters,
         * data, buttons, modals, and change tracking functionality.
         *
         * @param mixed $objParentObject Parent object to which this control belongs.
         * @param string|null $strControlId Optional control ID for identifying the control.
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
            $this->dtgInstitution_Create();
            $this->dtgInstitution->setDataBinder('BindData', $this);
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
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
         * Initializes and configures the data grid for organizing institutions.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgInstitution_Create(): void
        {
            $this->dtgInstitution = new OrganizingInstitutionTable($this);
            $this->dtgInstitution_CreateColumns();
            $this->createPaginators();
            $this->dtgInstitution_MakeEditable();
            $this->dtgInstitution->RowParamsCallback = [$this, "dtgInstitution_GetRowParams"];
            $this->dtgInstitution->SortColumnIndex = 0;
            $this->dtgInstitution->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgInstitution->UseAjax = true;
        }

        /**
         * Initializes and creates the necessary columns for the data grid component associated with the institution.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgInstitution_CreateColumns(): void
        {
            $this->dtgInstitution->createColumns();
        }

        /**
         * Configures the institution DataGrid to be editable by adding necessary actions and CSS classes.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgInstitution_MakeEditable(): void
        {
            $this->dtgInstitution->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgInstitutionRow_Click'));
            $this->dtgInstitution->addCssClass('clickable-rows');
            $this->dtgInstitution->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event on the institution row in the data grid. It retrieves the institution's details
         * based on the selected row and updates the interface to allow for editing.
         *
         * @param ActionParams $params The parameters from the action, including the ActionParameter used to identify the institution.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgInstitutionRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->intId = intval($params->ActionParameter);
            $objChanges = OrganizingInstitution::load($this->intId);

            $this->txtName->Text = $objChanges->getName();
            $this->lstStatus->SelectedValue = $objChanges->Status ?? null;

            $this->dtgInstitution->addCssClass('disabled');
            $this->btnAddInstitution->Enabled = false;
            $this->btnGoToEvents->Display = false;
            $this->txtName->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;
        }

        /**
         * Constructs an array of parameters for a row in the institution data grid.
         *
         * @param object $objRowObject The row object representing a single row in the data grid.
         * @param int $intRowIndex The index of the row within the data grid.
         *
         * @return array An associative array containing parameters for the row, including 'data-value' which is the primary key of the row object.
         */
        public function dtgInstitution_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures the pagination mechanism for the data grid.
         * Sets the paginator labels, items per a page, and sort column index.
         * Enables AJAX functionality for the data grid to improve user experience.
         * Also triggers the addition of filter actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgInstitution->Paginator = new Bs\Paginator($this);
            $this->dtgInstitution->Paginator->LabelForPrevious = t('Previous');
            $this->dtgInstitution->Paginator->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        /**
         * Initializes the items per page selection control with specific configurations and adds available items to
         * it.
         * The control is set up with a specific theme, width, and selection mode and listens for change events to
         * trigger a specified action.
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
         * Retrieves a list of ListItem objects representing the items per page by the assigned user.
         * It instantiates each item in the cursor and checks if it matches the user's current
         * ItemsPerPageByAssignedUserObject, marking it as selected if it does.
         *
         * @return ListItem[] An array of ListItem objects, with one item potentially marked as selected.
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
         * Updates the items per page setting for the institution data grid
         * based on the selected value from the items per page list.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgInstitution->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgInstitution->refresh();
        }

        /**
         * Initializes and configures a text filter input control for search functionality.
         *
         * @return void
         * @throws Caller
         */
        protected function createFilter(): void
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

            $this->dtgInstitution->refresh();
            $this->userOptions();
        }

        /**
         * Adds filter actions to the text input element to handle user input events.
         * These actions listen for input events with a delay and key press events.
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
         * Refreshes the data grid displaying institution information.
         * This method is typically called when a filter change event is detected,
         * ensuring that the displayed data reflects the updated filter criteria.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgInstitution->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the data grid using a specified condition.
         *
         * This method retrieves the condition to be applied and binds
         * data to the dtgInstitution data grid object based on that condition.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgInstitution->bindData($objCondition);
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
                    QQ::equal(QQN::OrganizingInstitution()->Id, $strSearchValue),
                    QQ::like(QQN::OrganizingInstitution()->Name, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and initializes various buttons and controls for managing institutions,
         * including buttons for adding, saving, and deleting entries, as well as a text box
         * for entering new institution names and a radio list for status selection.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createButtons(): void
        {
            $this->btnAddInstitution = new Bs\Button($this);
            $this->btnAddInstitution->Text = t(' Create a new name');
            $this->btnAddInstitution->Glyph = 'fa fa-plus';
            $this->btnAddInstitution->CssClass = 'btn btn-orange';
            $this->btnAddInstitution->addWrapperCssClass('center-button');
            $this->btnAddInstitution->CausesValidation = false;
            $this->btnAddInstitution->addAction(new Click(), new AjaxControl($this, 'btnAddInstitution_Click'));
            $this->btnAddInstitution->setCssStyle('float', 'left');
            $this->btnAddInstitution->setCssStyle('margin-right', '10px');

            $this->btnGoToEvents = new Bs\Button($this);
            $this->btnGoToEvents->Text = t('Go to the sports calendar events');
            $this->btnGoToEvents->addWrapperCssClass('center-button');
            $this->btnGoToEvents->CssClass = 'btn btn-default';
            $this->btnGoToEvents->CausesValidation = false;
            $this->btnGoToEvents->addAction(new Click(), new AjaxControl($this, 'btnGoToEvents_Click'));
            $this->btnGoToEvents->setCssStyle('float', 'left');

            if (!empty($_SESSION['dtgInstitution_changes']) && !empty($_SESSION['dtgInstitution_group'])) {
                $this->btnGoToEvents->Display = true;
            } else {
                $this->btnGoToEvents->Display = false;
            }

            $this->txtName = new Bs\TextBox($this);
            $this->txtName->Placeholder = t('Organizing institution new name');
            $this->txtName->ActionParameter = $this->txtName->ControlId;
            $this->txtName->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
            $this->txtName->setHtmlAttribute('autocomplete', 'off');
            $this->txtName->setCssStyle('float', 'left');
            $this->txtName->setCssStyle('margin-right', '10px');
            $this->txtName->Width = 300;
            $this->txtName->Display = false;

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
         * Creates two Toastr notification instances with predefined settings.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The institution name has been saved or modified.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('The institution name must exist!');
            $this->dlgToastr2->ProgressBar = true;
        }

        /**
         * Creates and configures modal dialogs for warning and informational messages.
         *
         * The method initializes four different modal dialogs with specified titles,
         * text content, button configurations, and header classes. Each modal is set up
         * with actions and styles suitable for particular scenarios such as warnings or tips.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this institution?</p>
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This institution cannot be deactivated at the moment!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this institution, simply release all 
                                        previously created institutions related to sporting events.</p>');
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This institution cannot be deleted at the moment!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">To delete this institutions, simply release all 
                                        previously created changes related to sports events.</p>');
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The name of this institution already 
                                        exists in the database, please choose another name!</p>');
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
         * Handles the click event for the 'Add Institution' button. This method sets up the UI for adding a new
         * institution by adjusting the visibility and state of several controls and components. The institutions' grid
         * is disabled to prevent interaction while new institution data is being entered.
         *
         * @return void
         * @throws RandomException
         * @throws Caller
         */
        protected function btnAddInstitution_Click(): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->btnGoToEvents->Display = false;
            $this->txtName->Display = true;
            $this->lstStatus->Display = true;
            $this->lstStatus->SelectedValue = 2;
            $this->btnSaveChange->Display = true;
            $this->btnCancel->Display = true;
            $this->txtName->Text = '';
            $this->txtName->focus();
            $this->btnAddInstitution->Enabled = false;
            $this->dtgInstitution->addCssClass('disabled');

            $this->userOptions();
        }

        /**
         * Handles the click event for the save changes button.
         *
         * This method processes the input data from the user interface when save a button is clicked,
         * checks the validity of the entered information, and appropriately updates or alerts the user.
         * It saves a new organizing institution if the entered name is valid and not already existing.
         * It also updates the visual elements and notifies the user upon successful completion or error.
         *
         * @param ActionParams $params The parameters associated with the click action event, including any relevant user interface elements.
         *
         * @return void This method does not return a value.
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

            if ($this->txtName->Text) {
                if (!OrganizingInstitution::titleExists(trim($this->txtName->Text))) {
                    $objCategoryNews = new OrganizingInstitution();
                    $objCategoryNews->setName(trim($this->txtName->Text));
                    $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                    $objCategoryNews->setPostDate(QDateTime::now());
                    $objCategoryNews->save();

                    $this->dtgInstitution->refresh();

                    if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                        $this->btnGoToEvents->Display = true;
                    }

                    $this->displayHelper();
                    $this->txtName->Text = '';
                    $this->dlgToastr1->notify();
                } else {
                    $this->txtName->Text = '';
                    $this->txtName->focus();
                    $this->dlgModal4->showDialogBox();
                }
            } else {
                $this->txtName->Text = '';
                $this->txtName->focus();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        /**
         * Handles the click event for the Save button. It processes and validates the data entered
         * in the institution form, updating the relevant records or showing appropriate notifications
         * depending on the state of the data.
         *
         * @param ActionParams $params Parameters passed during the button click action.
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

            $objChanges = OrganizingInstitution::loadById($this->intId);

            if ($this->txtName->Text) {
                if (OrganizingInstitution::titleExists(trim($this->txtName->Text)) && $this->lstStatus->SelectedValue ==  $objChanges->getStatus()) {
                    $this->txtName->Text = $objChanges->getName();
                    $this->dlgModal4->showDialogBox();
                    return;
                }

                if (SportsCalendar::countByOrganizingInstitutionId($this->intId) > 0 && $this->lstStatus->SelectedValue == 2) {
                    $this->lstStatus->SelectedValue = 1;
                    $this->dlgModal2->showDialogBox();

                    if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                        $this->btnGoToEvents->Display = true;
                    }

                    $this->lstStatus->SelectedValue = 1;
                    $this->displayHelper();
                    $this->dlgModal2->showDialogBox();

                } else if (($this->txtName->Text == $objChanges->getName() && $this->lstStatus->SelectedValue !== $objChanges->getStatus()) ||
                    ($this->txtName->Text != $objChanges->getName() && $this->lstStatus->SelectedValue == $objChanges->getStatus())) {
                    $objChanges->setName(trim($this->txtName->Text));
                    $objChanges->setStatus($this->lstStatus->SelectedValue);
                    $objChanges->setPostUpdateDate(QDateTime::now());
                    $objChanges->save();

                    if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                        $this->btnGoToEvents->Display = true;
                    }

                    $this->dtgInstitution->refresh();
                    $this->displayHelper();
                    $this->dlgToastr1->notify();
                }
            } else {
                $this->txtName->Text = $objChanges->getName();
                $this->txtName->focus();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        /**
         * Handles the delete button click event. Determines the behavior based on the
         * presence of the current ID in the change list. Shows a confirmation dialog and
         * conditionally alters the display and enabled state of various UI elements.
         *
         * @param ActionParams $params The parameters associated with the action event.
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

            $this->userOptions();

            if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            if (SportsCalendar::countByOrganizingInstitutionId($this->intId) > 0) {
                $this->dlgModal3->showDialogBox();
                $this->displayHelper();
            } else {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the click event for deleting an item. Depending on the action parameter, it either performs a delete
         * operation or updates the UI components to reflect the current changes without deletion.
         *
         * @param ActionParams $params The parameter object that carries action-specific data, including an action
         *     parameter that determines the course of action.
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

            $this->userOptions();

            $objChanges = OrganizingInstitution::loadById($this->intId);

            if ($params->ActionParameter == "pass") {
                $objChanges->delete();
            }

            if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            $this->dtgInstitution->refresh();

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
         * Handles the cancel button click event by adjusting the display and enabling/disabling
         * various UI elements based on the session state and resets the institution name input field.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws RandomException
         * @throws Caller
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            $this->displayHelper();
            $this->txtName->Text = '';

            $this->userOptions();
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
            $this->btnAddInstitution->Enabled = true;
            $this->txtName->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;

            $this->dtgInstitution->removeCssClass('disabled');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the "Go To Events" button. Checks if there are changes
         * or group sessions related to institutions and redirects to the sports calendar edit page
         * with appropriate query parameters if such sessions are found. Cleans up the session variables
         * after redirection.
         *
         * @param ActionParams $params Parameters associated with the button click event.
         *
         * @return void No return value.
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

            $this->userOptions();

            if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {

                Application::redirect('sports_calendar_edit.php?id=' . $_SESSION['dtgInstitution_changes'] . '&group=' . $_SESSION['dtgInstitution_group']);
                unset($_SESSION['dtgInstitution_changes']);
                unset($_SESSION['dtgInstitution_group']);
            }
        }
    }