<?php

    use QCubed as Q;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\Panel;
    use QCubed\Event\EscapeKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\Change;
    use QCubed\Event\Click;
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
     * SportsAreasPanel is a custom panel component that provides a user interface for managing
     * sports areas. It includes various UI elements such as filters, data grids, buttons,
     * modals, and notification controls. The panel allows users to view, edit, and update
     * the list of sports areas interactively.
     */
    class SportsAreasPanel extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;
        protected Q\Plugin\Toastr $dlgToastr4;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public SportAreasTable $dtgSportsAreas;

        public Q\Plugin\Control\Label $lblSportsArea;
        public Bs\TextBox $txtSportsArea;
        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;
        public Bs\Button $btnAddSportsArea;
        public Bs\Button $btnGoToEvents;
        public Bs\Button $btnSaveNew;
        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected ?int $intId = null;
        protected object $objUser;
        protected int $intLoggedUserId;

        protected bool $blnEditMode = true;

        protected string $strTemplate = 'SportsAreasPanel.tpl.php';

        /**
         * Class constructor for initializing the object with a parent object and optional control ID.
         *
         * The constructor sets up the initial state of the object, including loading the logged-in user's data,
         * creating necessary UI components (items per a page, filter, data grid, buttons, etc.), and setting up data
         * bindings. It also includes examples and notes for developers regarding session management for logged-in
         * users, though implementation specifics are left to the developer.
         *
         * @param mixed $objParentObject The parent object that owns this instance, typically a form or other container.
         * @param string|null $strControlId An optional control ID for the instance. Defaults to null.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller Thrown if an error occurs during the parent class instantiation.
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

            $this->intLoggedUserId= 1;
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createItemsPerPage();

            $this->createFilter();
            $this->dtgSportsAreas_Create();
            $this->dtgSportsAreas->setDataBinder('bindData', $this);

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the SportAreasTable component.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgSportsAreas_Create(): void
        {
            $this->dtgSportsAreas = new SportAreasTable($this);
            $this->dtgSportsAreas_CreateColumns();
            $this->createPaginators();
            $this->dtgSportsAreas_MakeEditable();
            $this->dtgSportsAreas->RowParamsCallback = [$this, "dtgSportsAreas_GetRowParams"];
            $this->dtgSportsAreas->SortColumnIndex = 0;
            $this->dtgSportsAreas->SortDirection = -1;
            $this->dtgSportsAreas->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
        }

        /**
         * Creates columns for the sports areas data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgSportsAreas_CreateColumns(): void
        {
            $this->dtgSportsAreas->createColumns();
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
        protected function dtgSportsAreas_MakeEditable(): void
        {
            $this->dtgSportsAreas->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgSportsAreas_Click'));
            $this->dtgSportsAreas->addCssClass('clickable-rows');
            $this->dtgSportsAreas->CssClass = 'table vauu-table js-sports-area table-hover table-responsive';
        }

        /**
         * Handles the click event for the sports areas data grid. It sets up the UI with the selected sports area details.
         *
         * @param ActionParams $params An object containing parameters related to the triggered event, such as the action parameter used to identify which area of sports was clicked.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgSportsAreas_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);
            $objSportsAreas = SportsAreas::load($this->intId);

            if ($objSportsAreas->getIsLocked() === 1) {
                $this->btnDelete->Display = true;
            } else {
                $this->btnDelete->Display = false;
            }

            $this->blnEditMode = true;
            $this->btnSaveNew->Display = false;
            $this->btnSave->Display = true;
            $this->btnAddSportsArea->Enabled = false;
            $this->btnGoToEvents->Enabled = false;
            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->txtFilter->Enabled = false;
            $this->dtgSportsAreas->Paginator->Enabled = false;
            $this->dtgSportsAreas->addCssClass('disabled');

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

            $this->txtSportsArea->Text = $objSportsAreas->getName();
            $this->lstStatus->SelectedValue = $objSportsAreas->getIsEnabled();
        }

        /**
         * Retrieves the parameters for a row in the sports areas data table.
         *
         * @param object $objRowObject The object representing the row for which parameters are being set.
         * @param int $intRowIndex The index of the row in the data table.
         *
         * @return array The array of parameters with keys as parameter names and values as parameter values for the row.
         */
        public function dtgSportsAreas_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            //$intLocked = $objRowObject->getIsLocked();

//            if ($intLocked == 1) {
//                $params['class'] = 'locked';
//            }

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
            $this->dtgSportsAreas->Paginator = new Bs\Paginator($this);
            $this->dtgSportsAreas->Paginator->LabelForPrevious = t('Previous');
            $this->dtgSportsAreas->Paginator->LabelForNext = t('Next');

            //$this->dtgSportsAreas->PaginatorAlternate = new Bs\Paginator($this);
            //$this->dtgSportsAreas->PaginatorAlternate->LabelForPrevious = t('Previous');
            //$this->dtgSportsAreas->PaginatorAlternate->LabelForNext = t('Next');

            $this->dtgSportsAreas->ItemsPerPage = 10;
            $this->dtgSportsAreas->SortColumnIndex = 0;
            $this->dtgSportsAreas->UseAjax = true;
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
            $this->lstItemsPerPageByAssignedUserObject->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->ItemsPerPageByAssignedUser;
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstItemsPerPageByAssignedUserObject_GetItems());
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
         * Updates the number of items displayed per page for the data grid and refreshes it based on the selected user object.
         *
         * @param ActionParams $params The parameters related to the action, which may include details about the specific user object selection change.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgSportsAreas->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgSportsAreas->refresh();
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

            $this->dtgSportsAreas->refresh();
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
         */
        protected function filterChanged(): void
        {
            $this->dtgSportsAreas->refresh();
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
            $this->dtgSportsAreas->bindData($objCondition);
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
                    QQ::like(QQN::SportsAreas()->Name, "%" . $strSearchValue . "%"),
                    QQ::equal(QQN::SportsAreas()->IsEnabled, $strSearchValue)
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and creates the input controls for the UI.
         *
         * This method defines and configures various input controls, including
         * labels, text boxes, and radio button lists, used in the application UI.
         * The settings include text, CSS classes, styles, placeholders, and other
         * attributes required for proper configuration and presentation of the controls.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->lblSportsArea = new Q\Plugin\Control\Label($this);
            $this->lblSportsArea->Text = t('Sport area');
            $this->lblSportsArea->addCssClass('col-md-4');
            $this->lblSportsArea->setCssStyle('font-weight', 'normal');
            $this->lblSportsArea->Required = true;

            $this->txtSportsArea = new Bs\TextBox($this);
            $this->txtSportsArea->Placeholder = t('Sport area');
            $this->txtSportsArea->ActionParameter = $this->txtSportsArea->ControlId;
            $this->txtSportsArea->setHtmlAttribute('autocomplete', 'off');
            $this->txtSportsArea->setCssStyle('float', 'left');
            $this->txtSportsArea->setCssStyle('margin-right', '10px');
            $this->txtSportsArea->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtSportsArea->addAction(new EnterKey(), new Terminate());
            $this->txtSportsArea->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtSportsArea->addAction(new EscapeKey(), new Terminate());

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Is enabled');
            $this->lblStatus->addCssClass('col-md-4');
            $this->lblStatus->setCssStyle('font-weight', 'normal');

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstStatus->setCssStyle('float', 'left');
            $this->lstStatus->setCssStyle('margin-left', '10px');
            $this->lstStatus->setCssStyle('margin-right', '10px');
            $this->lstStatus->addAction(new Change(), new AjaxControl($this, 'lstStatus_Change'));
        }

        /**
         * Initializes and configures a set of buttons and input controls for managing sports area data.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createButtons(): void
        {
            $this->btnAddSportsArea = new Bs\Button($this);
            $this->btnAddSportsArea->Text = t(' Add sport area');
            $this->btnAddSportsArea->Glyph = 'fa fa-plus';
            $this->btnAddSportsArea->CssClass = 'btn btn-orange';
            $this->btnAddSportsArea->addWrapperCssClass('center-button');
            $this->btnAddSportsArea->CausesValidation = false;
            $this->btnAddSportsArea->addAction(new Click(), new AjaxControl($this, 'btnAddSportsArea_Click'));
            $this->btnAddSportsArea->setCssStyle('float', 'left');
            $this->btnAddSportsArea->setCssStyle('margin-right', '10px');

            $this->btnGoToEvents = new Bs\Button($this);
            $this->btnGoToEvents->Text = t('Go to the sports calendar events');
            $this->btnGoToEvents->addWrapperCssClass('center-button');
            $this->btnGoToEvents->CssClass = 'btn btn-default';
            $this->btnGoToEvents->CausesValidation = false;
            $this->btnGoToEvents->addAction(new Click(), new AjaxControl($this, 'btnGoToEvents_Click'));
            $this->btnGoToEvents->setCssStyle('float', 'left');

            if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
                $this->btnGoToEvents->Display = true;
            } else {
                $this->btnGoToEvents->Display = false;
            }

            $this->btnSaveNew = new Bs\Button($this);
            $this->btnSaveNew->Text = t('Save');
            $this->btnSaveNew->CssClass = 'btn btn-orange';
            $this->btnSaveNew->addWrapperCssClass('center-button');
            $this->btnSaveNew->CausesValidation = true;
            $this->btnSaveNew->addAction(new Click(), new AjaxControl($this, 'btnNewSave_Click'));
            $this->btnSaveNew->setCssStyle('float', 'left');
            $this->btnSaveNew->setCssStyle('margin-right', '10px');

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));
            $this->btnSave->setCssStyle('float', 'left');
            $this->btnSave->setCssStyle('margin-right', '10px');

            $this->btnDelete = new Bs\Button($this);
            $this->btnDelete->Text = t('Delete');
            $this->btnDelete->CssClass = 'btn btn-danger';
            $this->btnDelete->addWrapperCssClass('center-button');
            $this->btnDelete->CausesValidation = true;
            $this->btnDelete->addAction(new Click(), new AjaxControl($this, 'btnDelete_Click'));
            $this->btnDelete->setCssStyle('float', 'left');
            $this->btnDelete->setCssStyle('margin-right', '10px');

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
            $this->btnCancel->setCssStyle('float', 'left');
        }

        /**
         * Creates and configures Toastr notifications for different alert scenarios.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> To add a new sport area to the database is successful.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('The sport area is at least mandatory!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Well done!</strong> The sport area has been saved or modified.');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('The sport entry update was canceled and the sport has been restored!');
            $this->dlgToastr4->ProgressBar = true;

        }

        /**
         * Creates and initializes modal dialog instances with predefined text,
         * titles, header styles, and buttons for typical user interactions such as warnings,
         * confirmations, and informational tips. These modals are intended to guide users
         * through various actions and to confirm irreversible operations.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the sport area?</p>
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The sports area cannot be deleted at the moment!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">To delete this sports area, you must first unlink it 
                                        from any previously created calendar events or competition areas associated with it.</p>');

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The sports area cannot be disabled at the moment!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">To disable this sports area, you must first unlink it from all previously 
                                        created calendar events or competition areas associated with it.</p>');

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The name of this sport already exists in the database, please choose another name!</p>');
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
         * Handles the click event for the "Add Sports Area" button. This method sets various UI elements to be
         * displayed, clears the text field for the sports area, focuses the text field, and handles session-related
         * logic.
         *
         * @param ActionParams $params The parameters associated with the action event triggering this method.
         *
         * @return void This method does not return any value.
         * @throws RandomException
         * @throws Caller
         */
        protected function btnAddSportsArea_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
                $this->btnGoToEvents->Enabled = false;
            }

            Application::executeJavaScript("
                $('.setting-wrapper').removeClass('hidden');
                $('.form-actions').removeClass('hidden');
            ");

            $this->blnEditMode = false;
            $this->btnSaveNew->Display = true;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->txtSportsArea->Text = '';
            $this->txtSportsArea->focus();
            $this->lstStatus->SelectedValue = 2;
            $this->btnAddSportsArea->Enabled = false;
            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->txtFilter->Enabled = false;
            $this->dtgSportsAreas->Paginator->Enabled = false;
            $this->dtgSportsAreas->addCssClass('disabled');
        }

        /**
         * Handles the status change action for the sports area record.
         *
         * This method verifies the CSRF token to prevent unauthorized actions,
         * and proceeds to load the sports area based on its ID. If the edit mode
         * is active, it checks the locked status of the sports area and handles
         * the status and name updates accordingly. Appropriate modal dialogs are
         * displayed based on the state of the record or validation results. If valid,
         * it updates the name, status, and the post-update date of the record.
         *
         * @param ActionParams $params Contains the parameters passed during the
         *                             execution of this action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objSportsAreas = SportsAreas::load($this->intId);

            if ($this->blnEditMode === true) {
                if ($objSportsAreas->getIsLocked() == 2) {
                    $this->dlgModal3->showDialogBox();
                    $this->lstStatus->SelectedValue = $objSportsAreas->getIsEnabled();

                    if (!$this->txtSportsArea->Text) {
                        $this->txtSportsArea->Text = $objSportsAreas->getName();
                    }

                    return;
                } else { // LOCKED 1
                    if (!$this->txtSportsArea->Text) {
                        $this->txtSportsArea->Text = $objSportsAreas->getName();
                        $this->lstStatus->SelectedValue = $objSportsAreas->getIsEnabled();
                        $this->dlgModal2->showDialogBox();
                    } else {
                        $objSportsAreas->setName($this->txtSportsArea->Text);
                        $objSportsAreas->setIsEnabled($this->lstStatus->SelectedValue);
                        $this->dlgToastr3->notify();
                    }
                }

                $objSportsAreas->setPostUpdateDate(Q\QDateTime::now());
                $objSportsAreas->save();
            }
        }

        /**
         * Handles the click event for the "Save New" button in the sports area form.
         *
         * This method is responsible for validating the input from the sports area form, checking
         * if the sports area title already exists, creating a new sports area if it doesn't exist,
         * and updating UI elements based on the success or failure of the operation.
         *
         * @param ActionParams $params The action parameters passed from the click event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnNewSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === false) {
                if ($this->txtSportsArea->Text) {
                    if (!SportsAreas::titleExists(trim($this->txtSportsArea->Text))) {
                        $objSportsAreas = new SportsAreas();
                        $objSportsAreas->setName($this->txtSportsArea->Text);
                        $objSportsAreas->setIsEnabled($this->lstStatus->SelectedValue);
                        $objSportsAreas->setPostDate(Q\QDateTime::now());
                        $objSportsAreas->save();

                        $this->dtgSportsAreas->refresh();

                        if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
                            $this->btnGoToEvents->Display = true;
                        }

                        $this->displayHelper();
                        $this->txtSportsArea->Text = '';
                        $this->dlgToastr1->notify();
                    } else {
                        $this->txtSportsArea->Text = '';
                        $this->txtSportsArea->focus();
                        $this->dlgModal4->showDialogBox();
                    }
                } else {
                    $this->txtSportsArea->Text = '';
                    $this->txtSportsArea->focus();
                    $this->dlgToastr2->notify();
                }
            }
        }

        /**
         * Handles the click event to save a button. This function updates the display and internal state
         * based on whether the selected sports area is already existing or new and manages various UI elements
         * based on the operation performed.
         *
         * @param ActionParams $params Parameters related to the action event triggered by the save button click.
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

            $objSportsAreas = SportsAreas::load($this->intId);
            $countSportsAreas = SportsCalendar::countBySportsAreasId($this->intId);
            $countSelectedSportsAreas = SportsAreasCompetitionAreas::countBySportsAreasId($this->intId);

            if ($this->blnEditMode === true) {

                if ($this->txtSportsArea->Text) {
                    if (SportsAreas::titleExists(trim($this->txtSportsArea->Text)) && $this->lstStatus->SelectedValue == $objSportsAreas->getIsEnabled()) {
                        $this->txtSportsArea->Text = $objSportsAreas->getName();
                        $this->dlgModal4->showDialogBox();
                        return;
                    }

                    if (($countSportsAreas > 0 || $countSelectedSportsAreas > 0) && $this->lstStatus->SelectedValue == 2) {

                        if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
                            $this->btnGoToEvents->Display = true;
                        }

                        $this->lstStatus->SelectedValue = 1;
                        $this->displayHelper();
                        $this->dlgModal3->showDialogBox();

                    } else if (($this->txtSportsArea->Text == $objSportsAreas->getName() && $this->lstStatus->SelectedValue !== $objSportsAreas->getIsEnabled()) ||
                        ($this->txtSportsArea->Text != $objSportsAreas->getName() && $this->lstStatus->SelectedValue == $objSportsAreas->getIsEnabled())) {
                        $objSportsAreas->setName(trim($this->txtSportsArea->Text));
                        $objSportsAreas->setIsEnabled($this->lstStatus->SelectedValue);
                        $objSportsAreas->setPostUpdateDate(Q\QDateTime::now());
                        $objSportsAreas->save();

                        if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
                            $this->btnGoToEvents->Display = true;
                        }

                        $this->dtgSportsAreas->refresh();
                        $this->displayHelper();
                        $this->dlgToastr1->notify();
                    }


                } else { // NOT TEXT
                    $this->txtSportsArea->Text = $objSportsAreas->getName();;
                    $this->txtSportsArea->focus();
                    $this->dlgToastr2->notify();
                }

            } // TRUE
        }

        /**
         * Handles the "item escape" action triggered by a user interaction.
         *
         * This method first verifies the CSRF token for the session to ensure secure handling of the request.
         * If the verification fails, a modal dialog box is displayed, a new CSRF token is generated,
         * and the method exits. Upon successful CSRF verification, the method fetches a SportsAreas object,
         * updates the text field with the name of the sports area, and displays a notification.
         *
         * @param ActionParams $params Parameters passed with the action, such as event details or additional data.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objSportsAreas = SportsAreas::load($this->intId);

            $this->txtSportsArea->Text = $objSportsAreas->getName();

            $this->dlgToastr4->notify();
        }

        /**
         * Handles the click event for the delete button. This method checks if the current sports area ID is within the list of
         * sports areas IDs. If it is, it displays a secondary dialog box and modifies the interface by enabling or disabling
         * specific elements based on the session information. If not, it shows a primary dialog box.
         *
         * @param ActionParams $params The parameters for the action that includes context or information about the click event.
         *
         * @return void This method does not return any value.
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

            if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            if (SportsCalendar::countBySportsAreasId($this->intId) > 0 || SportsAreasCompetitionAreas::countBySportsAreasId($this->intId) > 0) {
                $this->dlgModal2->showDialogBox();
                $this->displayHelper();
            } else {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the click event for deleting an item, specifically for sports areas.
         * Based on the parameters provided, it either deletes the item or updates the
         * user interface appropriately without deletion.
         *
         * @param ActionParams $params The parameters associated with the action, including
         *                             the action parameter that determines whether deletion
         *                             should occur.
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

            $objSportsAreas = SportsAreas::load($this->intId);

            if ($params->ActionParameter == "pass") {
                $objSportsAreas->delete();
            }

            if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            $this->displayHelper();

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the click event to hide specific UI elements and enable others.
         *
         * This method updates the display and enabled statuses of various UI components
         * and modifies the presentation of the competition areas datagrid by removing a
         * CSS class and refreshing its content.
         *
         * @param ActionParams $params The parameters passed from the click action, containing
         *                              details about the event triggering this method.
         *
         * @return void This method does not return any value.
         * @throws RandomException
         * @throws Caller
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
         * Handles the click event for the Cancel button in the UI. This method hides specific UI elements
         * and enables/adjusts others as part of the cancel operation.
         *
         * @param ActionParams $params Parameters representing the context and data associated with the action event.
         *
         * @return void This method does not return any value.
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

            if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            $this->displayHelper();
            $this->txtSportsArea->Text = '';
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Configures the display settings for various UI components
         * and enables interactions for the link categories data grid.
         *
         * @return void
         * @throws Caller
         */
        protected function displayHelper(): void
        {
            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions').addClass('hidden')
            ");

            $this->btnAddSportsArea->Enabled = true;
            $this->btnGoToEvents->Enabled = true;
            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->dtgSportsAreas->Paginator->Enabled = true;
            $this->dtgSportsAreas->removeCssClass('disabled');
            $this->dtgSportsAreas->refresh();

        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the "Go To Events" button. Redirects the user to the sports calendar edit page
         * using session parameters for sports area identification and group assignment if they are available.
         *
         * @param ActionParams $params The parameters associated with the action event.
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

            if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
                Application::redirect('sports_calendar_edit.php?id=' . $_SESSION['sports_areas_id'] . '&group=' . $_SESSION['sports_areas_group']);
                unset($_SESSION['sports_areas_id']);
                unset($_SESSION['sports_areas_group']);
            }
        }
    }