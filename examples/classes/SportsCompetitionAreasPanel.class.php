<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Event\Input;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use QCubed\Query\Condition\All;
    use QCubed\Query\Condition\OrCondition;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\CellClick;
    use QCubed\Event\Change;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\Query\QQ;

    /**
     * The CompetitionAreasPanel class represents a UI panel for managing competition areas.
     * It provides functionality to filter, add, edit, delete, and manage competition areas
     * through interactive components such as data grids, buttons, modal dialogs, and input fields.
     * This class extends the base Panel class and integrates various plugins for enhanced UI behavior.
     */
    class CompetitionAreasPanel extends Panel
    {
        protected ?object $objUnitCondition = null;
        protected ?array $objUnitClauses = null;

        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;
        protected Q\Plugin\Toastr $dlgToastr4;
        protected Q\Plugin\Toastr $dlgToastr5;
        protected Q\Plugin\Toastr $dlgToastr6;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;
        public Bs\Modal $dlgModal6;
        public Bs\Modal $dlgModal7;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public CompetitionAreasTable $dtgCompetitionAreas;
        public Bs\Button $btnRefresh;

        public Bs\Button $btnAddCompetitionArea;

        public Q\Plugin\Control\Label $lblCompetitionArea;
        public Bs\TextBox $txtCompetitionArea;
        public Q\Plugin\Control\Label $lblUnits;
        public Q\Plugin\Select2 $lstUnits;
        public Q\Plugin\Control\Label $lblIsDetailedResult;
        public Q\Plugin\Control\RadioList$lstIsDetailedResult;
        public Q\Plugin\Control\Label $lblIsEnabled;
        public Q\Plugin\Control\RadioList $lstIsEnabled;

        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected ?object $objCompetitionAreas = null;
        protected bool $blnEditMode = true;
        protected array $errors = []; // Array for tracking errors

        protected string $strTemplate = 'SportsCompetitionAreasPanel.tpl.php';

        /**
         * Constructs a new instance of the class.
         *
         * Initializes the object with the specified parent object and control ID.
         * This method also sets up the environment by loading the user based on
         * a logged user session or default value and creating the necessary components
         * such as items per a page, filters, data tables, inputs, buttons, modals, and notifications.
         *
         * Note: The implementation expects the developer to obtain the currently
         * logged-in user ID from the session or similar mechanism.
         *
         * @param mixed $objParentObject The parent object to associate with this instance.
         * @param string|null $strControlId Optional control ID for this instance.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller If an error occurs while initializing the parent object.
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

            $this->intLoggedUserId= $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgCompetitionAreas_Create();
            $this->dtgCompetitionAreas->setDataBinder('bindData', $this);

            $this->createInputs();
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
         * Initializes and configures the SportAreasTable component.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgCompetitionAreas_Create(): void
        {
            $this->dtgCompetitionAreas = new CompetitionAreasTable($this);
            $this->dtgCompetitionAreas_CreateColumns();
            $this->createPaginators();
            $this->dtgCompetitionAreas_MakeEditable();
            $this->dtgCompetitionAreas->RowParamsCallback = [$this, "dtgCompetitionAreas_GetRowParams"];
            $this->dtgCompetitionAreas->SortColumnIndex = 0;
            $this->dtgCompetitionAreas->SortDirection = -1;
            $this->dtgCompetitionAreas->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgCompetitionAreas->UseAjax = true;
        }

        /**
         * Creates columns for the sports areas data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgCompetitionAreas_CreateColumns(): void
        {
            $this->dtgCompetitionAreas->createColumns();
        }

        /**
         * Configures the Sports Areas datagrid to be editable by adding actions to it.
         * It sets up a cell click event on the datagrid that triggers an AJAX control
         * action. It also adds CSS classes to make the rows clickable and gives the
         * datagrid a specific styling with additional CSS classes.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgCompetitionAreas_MakeEditable(): void
        {
            $this->dtgCompetitionAreas->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgCompetitionAreas_CellClick'));
            $this->dtgCompetitionAreas->CssClass = 'table vauu-table js-competition-area table-hover table-responsive';
        }

        /**
         * Handles the cell click event in the competition areas data grid.
         *
         * This method is triggered when a cell in the competition areas data grid is clicked.
         * It loads the associated competition area based on the clicked item's ID, updates the state
         * of various UI elements (e.g., enabling/disabling controls, toggling visibility of buttons),
         * and executes JavaScript for smooth scrolling and UI adjustments. The method also performs
         * additional checks on inputs related to the selected competition area.
         *
         * @param ActionParams $params The parameters associated with the cell click action,
         *                              including the ID of the clicked item.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgCompetitionAreas_CellClick(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->intId = intval($params->ActionParameter);
            $this->objCompetitionAreas = SportsCompetitionAreas::load($this->intId);

            if ($this->objCompetitionAreas->getIsLocked() === 2) {
                $this->btnDelete->Display = false;
            } else {
                $this->btnDelete->Display = true;
            }

            $this->blnEditMode = true;

            $this->btnAddCompetitionArea->Enabled = false;
            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->txtFilter->Enabled = false;
            $this->btnClearFilters->Enabled = false;
            $this->dtgCompetitionAreas->Paginator->Enabled = false;
            $this->dtgCompetitionAreas->addCssClass('disabled');

            Application::executeJavaScript("
                $('.setting-wrapper').removeClass('hidden');
                $('.form-actions').removeClass('hidden');
      
                var wrapperTop = document.querySelector('.tabbable-custom'); // tabbable-custom
                
                if (wrapperTop) {
                    var offsetTop = wrapperTop.getBoundingClientRect().top + window.scrollY; // Absolute position
                    var windowHeight = window.innerHeight; // Browser window height
                    var scrollPosition = offsetTop - (windowHeight / 2); // Determining the center of an element
            
                    window.scrollTo({
                        top: scrollPosition,
                        behavior: 'smooth' // Smooth scrolling
                    });
                }
            ");

            $this->activeInputs($this->objCompetitionAreas);
            $this->checkInputs();
        }

        /**
         * Retrieves the parameters for a row in the sports areas data table.
         *
         * @param object $objRowObject The object representing the row for which parameters are being set.
         * @param int $intRowIndex The index of the row in the data table.
         *
         * @return array The array of parameters with keys as parameter names and values as parameter values for the row.
         */
        public function dtgCompetitionAreas_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            //$intLocked = $objRowObject->getIsLocked();

            //if ($intLocked == 2) {
                //$params['class'] = 'locked';
            //}

            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures the pagination for the data grid, setting the paginator labels
         * and configuring data grid properties such as items per a page, sorting, and AJAX usage.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgCompetitionAreas->Paginator = new Bs\Paginator($this);
            $this->dtgCompetitionAreas->Paginator->LabelForPrevious = t('Previous');
            $this->dtgCompetitionAreas->Paginator->LabelForNext = t('Next');

            //$this->dtgCompetitionAreas->PaginatorAlternate = new Bs\Paginator($this);
            //$this->dtgCompetitionAreas->PaginatorAlternate->LabelForPrevious = t('Previous');
            //$this->dtgCompetitionAreas->PaginatorAlternate->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a Select2 dropdown control for selecting the number of items per a page.
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
         * Retrieves a list of items per a page assigned to a user object.
         *
         * Queries the ItemsPerPage using the given condition and clauses, and creates
         * a list of ListItem objects based on the result. The list includes an
         * indication if the item is selected based on the user's an assigned object.
         *
         * @return ListItem[] An array of ListItem objects representing the items per page assigned
         *                    to the user object, with the selected item identified if applicable.
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
         * Updates the number of items displayed per page for the data grid and refreshes it based on the selected user object.
         *
         * @param ActionParams $params The parameters related to the action, which may include details about the specific user object selection change.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgCompetitionAreas->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgCompetitionAreas->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes the search filter by creating a text box with specific attributes and styles.
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

            $this->dtgCompetitionAreas->refresh();
            $this->userOptions();
        }

        /**
         * Adds filter actions to the txtFilter control.
         *
         * This method assigns two types of actions to the txtFilter control:
         * 1. An input event that triggers an Ajax control action named 'filterChanged' with a delay of 300
         * milliseconds.
         * 2. An enter key event that also triggers the 'filterChanged' Ajax control action in addition to a terminate
         * action.
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
         * Refreshes the sports areas data grid when the filter is changed.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgCompetitionAreas->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the data grid based on a specific condition.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgCompetitionAreas->bindData($objCondition);
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
                    QQ::like(QQN::SportsCompetitionAreas()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::SportsCompetitionAreas()->Unit->Name, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and configures various input controls for the form.
         *
         * Initializes labels, text boxes, selection boxes, and radio buttons
         * to be used in the form. Each control is styled, configured with appropriate
         * behaviors (such as adding CSS classes or setting event actions),
         * and linked to specific actions for user interaction.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function createInputs(): void
        {
            $this->lblCompetitionArea = new Q\Plugin\Control\Label($this);
            $this->lblCompetitionArea->Text = t('Competition area');
            $this->lblCompetitionArea->addCssClass('col-md-4');
            $this->lblCompetitionArea->setCssStyle('font-weight', 'normal');
            $this->lblCompetitionArea->Required = true;

            $this->txtCompetitionArea = new Bs\TextBox($this);
            $this->txtCompetitionArea->Placeholder = t('Competition area');
            $this->txtCompetitionArea->ActionParameter = $this->txtCompetitionArea->ControlId;
            $this->txtCompetitionArea->setHtmlAttribute('autocomplete', 'off');
            $this->txtCompetitionArea->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtCompetitionArea->addAction(new EnterKey(), new Terminate());
            $this->txtCompetitionArea->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtCompetitionArea->addAction(new EscapeKey(), new Terminate());

            $this->lblUnits = new Q\Plugin\Control\Label($this);
            $this->lblUnits->Text = t('Units of measurement');
            $this->lblUnits->addCssClass('col-md-4');
            $this->lblUnits->setCssStyle('font-weight', 'normal');
            $this->lblUnits->Required = true;

            $this->lstUnits = new Q\Plugin\Select2($this);
            $this->lstUnits->MinimumResultsForSearch = -1;
            $this->lstUnits->ContainerWidth = 'resolve';
            $this->lstUnits->Theme = 'web-vauu';
            $this->lstUnits->Width = '100%';
            $this->lstUnits->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstUnits->addItem(t('- Select Unit of measurement -'));
            $this->lstUnits->addItems($this->lstUnit_GetItems());
            $this->lstUnits->addAction(new Change(), new AjaxControl($this, 'lstUnits_Change'));

            $this->lblIsDetailedResult = new Q\Plugin\Control\Label($this);
            $this->lblIsDetailedResult->Text = t('Is detailed result');
            $this->lblIsDetailedResult->addCssClass('col-md-4');
            $this->lblIsDetailedResult->setCssStyle('font-weight', 'normal');

            $this->lstIsDetailedResult = new Q\Plugin\Control\RadioList($this);
            $this->lstIsDetailedResult->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstIsDetailedResult->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstIsDetailedResult->setCssStyle('float', 'left');
            $this->lstIsDetailedResult->setCssStyle('margin-left', '10px');
            $this->lstIsDetailedResult->setCssStyle('margin-right', '10px');
            $this->lstIsDetailedResult->addAction(new Change(), new AjaxControl($this, 'lstIsDetailedResult_Change'));

            $this->lblIsEnabled = new Q\Plugin\Control\Label($this);
            $this->lblIsEnabled->Text = t('Is enabled');
            $this->lblIsEnabled->addCssClass('col-md-4');
            $this->lblIsEnabled->setCssStyle('font-weight', 'normal');

            $this->lstIsEnabled = new Q\Plugin\Control\RadioList($this);
            $this->lstIsEnabled->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstIsEnabled->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstIsEnabled->setCssStyle('float', 'left');
            $this->lstIsEnabled->setCssStyle('margin-left', '10px');
            $this->lstIsEnabled->setCssStyle('margin-right', '10px');
            $this->lstIsEnabled->addAction(new Change(), new AjaxControl($this, 'lstIsEnabled_Change'));
        }

        /**
         * Retrieves a list of sports units for selection.
         *
         * Queries the SportsUnits using the specified condition and clauses and constructs
         * a list of ListItem objects based on the query results. If a competition area is
         * clicked and its unit matches one of the results, that item is marked as selected.
         *
         * @return ListItem[] An array of ListItem objects representing the sports units,
         *                    with the selected item identified if applicable.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstUnit_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objUnitCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objUnitCursor = SportsUnits::queryCursor($objCondition, $this->objUnitClauses);

            // Iterate through the Cursor
            while ($objUnit = SportsUnits::instantiateCursor($objUnitCursor)) {
                $objListItem = new ListItem($objUnit->__toString(), $objUnit->Id);

                if (!empty($this->objCompetitionAreas->UnitId)) {
                    if (($this->objCompetitionAreas->UnitId) && ($this->objCompetitionAreas->UnitId == $objUnit->Id))
                        $objListItem->Selected = true;
                }

                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Creates and configures a set of buttons for the user interface.
         *
         * This method initializes buttons such as 'Add Competition Area', 'Save',
         * 'Delete', and 'Cancel', setting their properties including text, CSS classes,
         * icons (if applicable), and validation behavior. It also assigns click actions
         * to each button to handle user interaction.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnRefresh = new Bs\Button($this);
            $this->btnRefresh->Tip = true;
            $this->btnRefresh->ToolTip = t('Refresh tables');
            $this->btnRefresh->Glyph = 'fa fa-refresh';
            $this->btnRefresh->CssClass = 'btn btn-darkblue';
            $this->btnRefresh->CausesValidation = false;
            $this->btnRefresh->setCssStyle('margin-left', '15px');
            $this->btnRefresh->addAction(new Click(), new AjaxControl($this, 'btnRefresh_Click'));

            $this->btnAddCompetitionArea = new Bs\Button($this);
            $this->btnAddCompetitionArea->Text = t(' Add a competition area');
            $this->btnAddCompetitionArea->Glyph = 'fa fa-plus';
            $this->btnAddCompetitionArea->CssClass = 'btn btn-orange';
            $this->btnAddCompetitionArea->CausesValidation = false;
            $this->btnAddCompetitionArea->addAction(new Click(), new AjaxControl($this, 'btnAddCompetitionArea_Click'));

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->setCssStyle('margin-right', '10px');
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));

            $this->btnDelete = new Bs\Button($this);
            $this->btnDelete->Text = t('Delete');
            $this->btnDelete->CssClass = 'btn btn-danger';
            $this->btnDelete->setCssStyle('margin-right', '10px');
            $this->btnDelete->CausesValidation = true;
            $this->btnDelete->addAction(new Click(), new AjaxControl($this, 'btnDelete_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
        }

        /**
         * Creates and initializes multiple Toastr notification dialogs with predefined settings.
         *
         * Configures a set of Toastr dialogs, each with a specific alert type, position,
         * message, and progress bar enabled. These dialogs are used to display notifications
         * for various operations related to competition areas, such as success, error, or informational messages.
         *
         * @return void This method does not return a value.
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> To add a new competition area to the database is successful.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('The competition area is at least mandatory!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('The unit of measurement for the competition area is at least mandatory!');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('<strong>Well done!</strong> The competition area has been saved or modified.');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('The update to the competition area entry was discarded, and the competition area has been restored!');
            $this->dlgToastr5->ProgressBar = true;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('The table has been updated!');
            $this->dlgToastr6->ProgressBar = true;
        }

        /**
         * Creates and initializes modal dialog components for user interactions.
         *
         * Defines multiple modal dialogs with specific texts, titles, styles, and buttons
         * for various user scenarios. Each modal is configured with actions and events
         * tailored to its respective purpose, allowing for user feedback and interaction.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                        delete the competition area?</p>
                                        <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
                ['class' => 'btn btn-default']);
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'deleteItem_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'hideItem_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The competition area cannot be deleted at this time!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">To delete this competition area,
                                        just must release sport areas related to previously created calendar event.</p>');

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The competition area cannot be deactivated at this time!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this competition area, you must first unlink it from the previously created sports areas.</p>');

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->HeaderClasses = 'btn-darkblue';
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The competition area cannot be deleted at this time!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">To delete this competition area, you must first unlink it from the previously created sports areas.</p>');

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Tip");
            $this->dlgModal5->HeaderClasses = 'btn-darkblue';
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The unit of measurement drop-down cannot be left empty at this time!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">To clear the unit of measurement drop-down, you must first unlink it from the sports areas created previously.</p>');

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Title = t("Tip");
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The name of this competition area already exists in the database, please choose another name!</p>');
            $this->dlgModal6->HeaderClasses = 'btn-darkblue';
            $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal7 = new Bs\Modal($this);
            $this->dlgModal7->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal7->Title = t("Warning");
            $this->dlgModal7->HeaderClasses = 'btn-danger';
            $this->dlgModal7->addCloseButton(t("I understand"));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the Refresh button.
         *
         * Verifies the CSRF token for security purposes and refreshes the competition areas data
         * grid. If the verification fails, displays a modal dialog and regenerates the CSRF token.
         * Upon a successful refresh, triggers a notification to the user.
         *
         * @param ActionParams $params The parameters passed from the button click action, potentially
         *                              containing context-related information about the event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnRefresh_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->dtgCompetitionAreas->refresh();

            $this->dlgToastr6->notify();
        }

        /**
         * Handles the action triggered when the 'Add Competition Area' button is clicked.
         *
         * Executes JavaScript to update the UI by displaying certain elements and disabling others.
         * Prepares the interface for adding a new competition area by resetting inputs,
         * clearing relevant fields, and disabling functionality for existing competition area elements.
         *
         * @param ActionParams $params The parameters associated with the action event triggered by the button click.
         *
         * @return void This method does not return any value.
         * @throws Caller
         * @throws RandomException
         */
        protected function btnAddCompetitionArea_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            Application::executeJavaScript("
                $('.setting-wrapper').removeClass('hidden');
                $('.form-actions').removeClass('hidden');
            ");

            $this->blnEditMode = false;

            $this->btnDelete->Display = false;
            $this->btnAddCompetitionArea->Enabled = false;
            $this->dtgCompetitionAreas->addCssClass('disabled');
            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->txtFilter->Enabled = false;
            $this->btnClearFilters->Enabled = false;
            $this->dtgCompetitionAreas->Paginator->Enabled = false;

            $this->txtCompetitionArea->Text = '';
            $this->txtCompetitionArea->focus();

            $this->resetInputs();
        }

        /**
         * Handles changes to the unit list control in the form.
         *
         * This method processes user input received from a change in the unit list,
         * checks the edit mode, validates the associated competition area state, and
         * performs appropriate actions depending on whether the competition area is
         * locked or unlocked. It also verifies the provided inputs and updates the
         * competition area accordingly, including notifying the user of any missing
         * input or successful updates.
         *
         * @param ActionParams $params Parameters related to the change action triggered by the user.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstUnits_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === true) {
                if ($this->objCompetitionAreas->getIsLocked() == 1) {
                    if (!$this->lstUnits->SelectedValue) {
                        $this->dlgModal5->showDialogBox();
                        $this->activeInputs($this->objCompetitionAreas);
                        return;
                    }
                } else { // LOCKED 2
                    $this->checkInputs();

                    if (!$this->txtCompetitionArea->Text) {
                        $this->dlgToastr2->notify();
                    }

                    if (!$this->lstUnits->SelectedValue) {
                        $this->dlgToastr3->notify();
                    }

                    if ($this->objCompetitionAreas->getIsLocked() == 1 && (!$this->txtCompetitionArea->Text || !$this->lstUnits->SelectedValue)) {
                        if ($this->lstUnits->SelectedValue == null) {
                            $this->lstUnits->SelectedValue = null;
                            $this->lstIsEnabled->SelectedValue = 2;
                            $this->saveInputs($this->objCompetitionAreas);
                            $this->objCompetitionAreas->setPostUpdateDate(QDateTime::now());
                        }
                    }

                    if ($this->txtCompetitionArea->Text && $this->lstUnits->SelectedValue) {
                        $this->saveInputs($this->objCompetitionAreas);
                        $this->objCompetitionAreas->setPostUpdateDate(QDateTime::now());
                        $this->dlgToastr4->notify();
                    }

                    $this->objCompetitionAreas->save();
                }
            }

            unset($this->errors);

            $this->userOptions();
        }

        /**
         * Handles the change event for the detailed result selection list.
         *
         * Updates the "IsDetailedResult" property of the selected competition area
         * object based on the new selection from the list. If in edit mode, the method
         * modifies the competition area's detailed result status, updates the post-update
         * date to the current timestamp, and saves the changes. A notification is then
         * triggered to confirm the action.
         *
         * @param ActionParams $params Parameters from the action triggering the event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstIsDetailedResult_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === true) {

                $this->objCompetitionAreas->setIsDetailedResult($this->lstIsDetailedResult->SelectedValue);
                $this->objCompetitionAreas->setPostUpdateDate(QDateTime::now());
                $this->objCompetitionAreas->save();
                $this->dlgToastr4->notify();
            }

            $this->userOptions();
        }

        /**
         * Handles the change event for the "IsEnabled" list.
         *
         * This method processes changes to the "IsEnabled" list based on the current edit mode,
         * input validations, and the state of the associated competition area. It performs various
         * actions such as notifying the user of errors, updating competition area properties, and saving
         * changes to the database.
         *
         * @param ActionParams $params The parameters associated with the change action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstIsEnabled_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === true) {

                $this->checkInputs();

                if ($this->objCompetitionAreas->getIsLocked() == 2) {
                    $this->dlgModal3->showDialogBox();
                    $this->activeInputs($this->objCompetitionAreas);
                } else {
                    if (!$this->txtCompetitionArea->Text) {
                        $this->dlgToastr2->notify();
                    }

                    if (!$this->lstUnits->SelectedValue) {
                        $this->dlgToastr3->notify();
                    }

                    if (!$this->txtCompetitionArea->Text || !$this->lstUnits->SelectedValue) {
                        if ($this->lstIsEnabled->SelectedValue == 1) {
                            $this->lstIsEnabled->SelectedValue = 2;
                        }
                    } else {
                        $this->objCompetitionAreas->setIsEnabled($this->lstIsEnabled->SelectedValue);
                        $this->objCompetitionAreas->setPostUpdateDate(QDateTime::now());
                        $this->objCompetitionAreas->save();
                        $this->dlgToastr4->notify();
                    }
                }

                unset($this->errors);
            }

            $this->userOptions();
        }

        /**
         * Handles the save action for competition areas.
         *
         * This method performs various operations depending on the edit mode and the state
         * of the competition area data. It checks inputs, validates conditions, and either creates
         * a new competition area or updates an existing one. The method also handles UI changes,
         * error notifications, and ensures the application state is updated after the action.
         *
         * @param ActionParams $params Parameters passed to the method during the action invocation.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->checkInputs();

            if ($this->blnEditMode === false) {
                if (!count($this->errors)) {
                    $objCompetitionAreas = new SportsCompetitionAreas();
                    $this->saveInputs($this->objCompetitionAreas);
                    $objCompetitionAreas->setPostDate(QDateTime::now());
                    $this->dlgToastr1->notify();

                    Application::executeJavaScript("
                        $('.setting-wrapper').addClass('hidden');
                        $('.form-actions').addClass('hidden')
                    ");

                    $this->btnAddCompetitionArea->Enabled = true;
                    $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
                    $this->txtFilter->Enabled = true;
                    $this->btnClearFilters->Enabled = true;
                    $this->dtgCompetitionAreas->Paginator->Enabled = true;
                    $this->dtgCompetitionAreas->removeCssClass('disabled');
                    $this->dtgCompetitionAreas->refresh();

                    $this->resetInputs();
                }
            } else { // $this->blnEditMode === true

                if ($this->objCompetitionAreas->getIsLocked() == 2) {
                    if (!$this->txtCompetitionArea->Text) {
                        $this->dlgModal4->showDialogBox();
                        $this->activeInputs($this->objCompetitionAreas);
                        return;
                    } else {
                        $this->saveInputs($this->objCompetitionAreas);
                        $this->objCompetitionAreas->setPostUpdateDate(QDateTime::now());
                        $this->dlgToastr4->notify();
                    }
                } else { // LOCKED 1
                    if (!$this->txtCompetitionArea->Text || !$this->lstUnits->SelectedValue) {
                        if (!$this->txtCompetitionArea->Text) {
                            $this->txtCompetitionArea->Text = $this->objCompetitionAreas->getName();
                            $this->dlgToastr2->notify();
                        }

                        if (!$this->lstUnits->SelectedValue) {
                            $this->lstUnits->SelectedValue = null;
                            $this->lstIsEnabled->SelectedValue = 2;
                            $this->saveInputs($this->objCompetitionAreas);
                            $this->objCompetitionAreas->setPostUpdateDate(QDateTime::now());
                            $this->dlgToastr3->notify();
                        }
                    } else {
                        $this->saveInputs($this->objCompetitionAreas);
                        $this->objCompetitionAreas->setPostUpdateDate(QDateTime::now());
                        $this->dlgToastr4->notify();
                    }
                }

                unset($this->errors);
            }

            $this->objCompetitionAreas->save();

            $this->userOptions();
        }

        /**
         * Validates input fields and updates error tracking.
         *
         * Checks required fields for missing values and manages HTML attributes
         * or CSS classes to visually indicate errors. Errors are added to the internal
         * tracking array for further processing.
         *
         * @return void
         */
        public function checkInputs(): void
        {
            // We check each field and add errors if necessary
            if (!$this->txtCompetitionArea->Text) {
                $this->txtCompetitionArea->setHtmlAttribute('required', 'required');
                $this->errors[] = 'txtCompetitionArea';
            } else {
                $this->txtCompetitionArea->removeHtmlAttribute('required');
            }

            if (!$this->lstUnits->SelectedValue) {
                $this->lstUnits->addCssClass('has-error');
                $this->errors[] = 'lstUnits';
            } else {
                $this->lstUnits->removeCssClass('has-error');
            }
        }

        /**
         * Resets the input fields to their default states.
         *
         * Clears the text in the competition area field, resets the selected values
         * of the units, detailed result, and enabled status to predefined defaults,
         * and refreshes their respective components to ensure the UI reflects these changes.
         *
         * @return void
         */
        public function resetInputs(): void
        {
            $this->txtCompetitionArea->Text = '';
            $this->lstUnits->SelectedValue = null;
            $this->lstIsDetailedResult->SelectedValue = 2;
            $this->lstIsEnabled->SelectedValue = 2;

            $this->lstUnits->refresh();
            $this->lstIsDetailedResult->refresh();
            $this->lstIsEnabled->refresh();
        }

        /**
         * Updates the active input fields based on the provided edit object.
         *
         * Sets the text and selected values of input fields to match the properties of
         * the given edit object and refreshes these fields to update their state.
         *
         * @param object $objEdit The edit object containing the data to populate the input fields.
         *
         * @return void
         */
        public function activeInputs(object $objEdit): void
        {
            $this->txtCompetitionArea->Text = $objEdit->getName() ?? '';
            $this->lstUnits->SelectedValue = $objEdit->getUnitId();
            $this->lstIsDetailedResult->SelectedValue = $objEdit->getIsDetailedResult();
            $this->lstIsEnabled->SelectedValue = $objEdit->getIsEnabled();

            $this->txtCompetitionArea->refresh();
            $this->lstUnits->refresh();
            $this->lstIsDetailedResult->refresh();
            $this->lstIsEnabled->refresh();
        }

        /**
         * Saves the input values into the provided edit object.
         *
         * Transfers user input values (e.g., competition area name, unit ID, detailed result flag,
         * and enabled status) from the form fields to the specified edit object.
         *
         * @param object $objEdit An object where the input values will be saved. The object must
         *                        have appropriate setter methods for a name, unit ID, detailed result,
         *                        and enabled status.
         *
         * @return void
         */
        public function saveInputs(object $objEdit): void
        {
            $objEdit->setName($this->txtCompetitionArea->Text);
            $objEdit->setUnitId($this->lstUnits->SelectedValue);
            $objEdit->setIsDetailedResult($this->lstIsDetailedResult->SelectedValue);
            $objEdit->setIsEnabled($this->lstIsEnabled->SelectedValue);
        }

        /**
         * Handles the click event for escaping an item.
         *
         * Loads competition area data based on the given click parameter, activates
         * inputs related to the loaded data, and triggers a notification.
         *
         * @param ActionParams $params The parameters associated with the action triggering the event.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->activeInputs($this->objCompetitionAreas);

            $this->dlgToastr5->notify();
        }

        /**
         * Handles the click event of the delete button.
         *
         * The method performs deletion-related actions for a competition area. It first determines
         * if the targeted competition area is locked. If locked, it shows a specific dialog, modifies
         * UI elements' states, resets some inputs, and refreshes the table containing the list of competition areas.
         * If not locked, it shows a different dialog.
         *
         * @param ActionParams $params The parameters associated with the delete action event.
         *
         * @return void The method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            if ($this->objCompetitionAreas->getIsLocked() == 2) {

                $this->dlgModal2->showDialogBox();

                Application::executeJavaScript("
                    $('.setting-wrapper').addClass('hidden');
                    $('.form-actions').addClass('hidden')
                ");

                $this->resetInputs();

                $this->btnAddCompetitionArea->Enabled = true;
                $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
                $this->txtFilter->Enabled = true;
                $this->btnClearFilters->Enabled = true;
                $this->dtgCompetitionAreas->Paginator->Enabled = true;
                $this->dtgCompetitionAreas->removeCssClass('disabled');
                $this->dtgCompetitionAreas->refresh();
            } else {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the click event for deleting a competition area item.
         *
         * Loads the competition area specified by the class property and, if the action parameter
         * matches a specific condition, deletes the competition area. Updates the user interface elements
         * by enabling related controls, hiding specific sections, and refreshing the data grid.
         * Additionally, hides the modal dialog box after the operation.
         *
         * @param ActionParams $params The parameters of the action, including the action parameter
         *                              that determines the behavior of the deletion.
         *
         * @return void This method does not return any value.
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            if ($params->ActionParameter == "pass") {
                $this->objCompetitionAreas->delete();

                Application::executeJavaScript("
                    $('.setting-wrapper').addClass('hidden');
                    $('.form-actions').addClass('hidden')
                ");

                $this->btnAddCompetitionArea->Enabled = true;
                $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
                $this->txtFilter->Enabled = true;
                $this->btnClearFilters->Enabled = true;
                $this->dtgCompetitionAreas->Paginator->Enabled = true;
                $this->dtgCompetitionAreas->removeCssClass('disabled');
                $this->dtgCompetitionAreas->refresh();
            }

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the click event to hide a specific item and reset the interface.
         *
         * Executes JavaScript commands to hide certain UI elements and resets input fields. Re-enables
         * buttons, dropdowns, and other interface elements and refreshes a data grid after removing
         * the disabled CSS class. Closes a modal dialog box as part of the UI reset process.
         *
         * @param ActionParams $params Parameters associated with the action triggering the method.
         *
         * @return void This method does not return any value.
         * @throws Caller
         * @throws RandomException
         */
        protected function hideItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions').addClass('hidden')
            ");

            $this->resetInputs();

            $this->btnAddCompetitionArea->Enabled = true;
            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->btnClearFilters->Enabled = true;
            $this->dtgCompetitionAreas->Paginator->Enabled = true;
            $this->dtgCompetitionAreas->removeCssClass('disabled');
            $this->dtgCompetitionAreas->refresh();

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the click event for the cancel button, performing UI reset and enabling controls.
         *
         * This method executes JavaScript to hide specific form sections, resets form inputs,
         * and re-enables various UI components including form fields and the paginator.
         * Additionally, it clears any existing error messages associated with the form.
         *
         * @param ActionParams $params Parameters related to the action triggering the event.
         *
         * @return void This method does not return any value.
         * @throws Caller
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions').addClass('hidden')
            ");

            $this->resetInputs();

            $this->btnAddCompetitionArea->Enabled = true;
            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->btnClearFilters->Enabled = true;
            $this->dtgCompetitionAreas->Paginator->Enabled = true;
            $this->dtgCompetitionAreas->removeCssClass('disabled');
            $this->dtgCompetitionAreas->refresh();

            unset($this->errors);
        }

    }