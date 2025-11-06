<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
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
     * Class NewsChangesManager
     *
     * Manages the News Changes component of the application, providing CRUD functionality,
     * filtering, pagination, and user-specific configurations for managing news change items.
     * This class extends a base Panel class to initialize user interaction, display elements,
     * and data-binding capabilities for News Changes.
     */
    class NewsChangesManager extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
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
        public NewsChangesTable $dtgNewsChanges;

        public Bs\Button $btnAddChange;
        public Bs\Button $btnGoToNews;
        public Bs\TextBox $txtChange;
        public Q\Plugin\Control\RadioList $lstStatus;
        public Bs\Button $btnSaveChange;
        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'NewsChangesManager.tpl.php';

        /**
         * Constructor for initializing the object with a parent object and an optional control ID.
         *
         * Sets up the logged-in user ID, retrieves the corresponding user object, and initializes various controls
         * and functionalities, such as items per a page, filter, data binder, buttons, and modals.
         *
         * @param mixed $objParentObject The parent object that owns this control.
         * @param string|null $strControlId An optional control ID for identifying the control.
         *
         * @throws DateMalformedStringException
         * @throws Caller If an invalid operation is attempted during parent construction or initialization.
         * @throws InvalidCast
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
            $this->dtgNewsChanges_Create();
            $this->dtgNewsChanges->setDataBinder('BindData', $this);
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
         * Initializes the news changes data table, defining its columns, pagination,
         * and editability features, as well as configuring its row parameters and
         * sorting behavior.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgNewsChanges_Create(): void
        {
            $this->dtgNewsChanges = new NewsChangesTable($this);
            $this->dtgNewsChanges_CreateColumns();
            $this->createPaginators();
            $this->dtgNewsChanges_MakeEditable();
            $this->dtgNewsChanges->RowParamsCallback = [$this, "dtgNewsChanges_GetRowParams"];
            $this->dtgNewsChanges->SortColumnIndex = 0;
            $this->dtgNewsChanges->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgNewsChanges->UseAjax = true;
        }

        /**
         * Initializes and creates columns for the dtgNewsChanges component.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgNewsChanges_CreateColumns(): void
        {
            $this->dtgNewsChanges->createColumns();
        }

        /**
         * Configures the datagrid for news changes to be editable by adding an action
         * that triggers a click event and adds relevant CSS classes for styling.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgNewsChanges_MakeEditable(): void
        {
            $this->dtgNewsChanges->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgNewsChangesRow_Click'));
            $this->dtgNewsChanges->addCssClass('clickable-rows');
            $this->dtgNewsChanges->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for a row in the news changes data grid.
         * Loads the news change item based on the provided action parameters,
         * initializing form controls with the item's data and updating UI elements
         * accordingly.
         *
         * @param ActionParams $params Contains the action parameter indicating
         *                             which row was clicked, specifically the ID of
         *                             the news change item.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgNewsChangesRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->intId = intval($params->ActionParameter);
            $objNewsChanges = NewsChanges::load($this->intId);

            $this->txtChange->Text = $objNewsChanges->getTitle();
            $this->txtChange->focus();
            $this->lstStatus->SelectedValue = $objNewsChanges->Status ?? null;

            $this->btnAddChange->Enabled = false;
            $this->btnGoToNews->Display = false;

            $this->disableInputs();

            if (News::countByNewsChangesId($this->intId) > 0) {
                $this->dlgModal3->showDialogBox();
                $this->btnDelete->Enabled = false;
            } else {
                $this->btnDelete->Enabled = true;
            }
        }

        /**
         * Retrieves parameters for a row in the news changes data grid.
         *
         * @param object $objRowObject The object representing the row in the data grid.
         * @param int $intRowIndex The index of the row in the data grid.
         *
         * @return array An associative array containing parameters for the row, including a 'data-value' key set to the row's primary key.
         */
        public function dtgNewsChanges_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures paginators for the news changes data grid component.
         * The paginators are set up with labels for navigation and specific pagination settings.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgNewsChanges->Paginator = new Bs\Paginator($this);
            $this->dtgNewsChanges->Paginator->LabelForPrevious = t('Previous');
            $this->dtgNewsChanges->Paginator->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        /**
         * Initializes and configures the items-per-page selection component for the assigned user object.
         * It sets display properties like theme, width, and selection mode and populates it with items.
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
            $this->lstItemsPerPageByAssignedUserObject->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstPreferredItemsPerPageObject_GetItems());
            $this->lstItemsPerPageByAssignedUserObject->AddAction(new Change(), new AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
        }

        /**
         * Retrieves a list of list items representing each `ItemsPerPage` object associated with the assigned user object.
         *
         * Iterates through the `ItemsPerPage` objects retrieved by the query and creates a `ListItem` for each.
         * If the current `ItemsPerPage` object matches the one associated with the user, it is marked as selected.
         *
         * @return ListItem[] An array of `ListItem` objects based on the `ItemsPerPage` associated objects.
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
         * Updates the items per page for a data grid based on the user's selection and refreshes the grid.
         *
         * @param ActionParams $params The parameters passed from the action triggering the change in items per a page.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgNewsChanges->ItemsPerPage =  ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgNewsChanges->refresh();
        }

        /**
         * Initializes and configures the search filter text box component.
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

            $this->dtgNewsChanges->refresh();
            $this->userOptions();
        }

        /**
         * Adds filter actions to the text filter input control.
         *
         * This method assigns AJAX-based actions to respond to user interactions with the filter input.
         * An input event with a delay is added to trigger the 'filterChanged' method asynchronously.
         * An additional action array is added to handle when the Enter key is pressed, which triggers
         * the 'FilterChanged' method and then terminates further event propagation.
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
         * Refreshes the data grid for news changes when the filter criteria is modified.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgNewsChanges->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the data grid using the specified condition.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgNewsChanges->bindData($objCondition);
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
                    QQ::equal(QQN::NewsChanges()->Id, $strSearchValue),
                    QQ::like(QQN::NewsChanges()->Title, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and configures various buttons and controls for managing changes,
         * including add, save, delete, and cancel operations, as well as input and status controls.
         *
         * @return void
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

            $this->btnGoToNews = new Bs\Button($this);
            $this->btnGoToNews->Text = t('Go to this news');
            $this->btnGoToNews->addWrapperCssClass('center-button');
            $this->btnGoToNews->CssClass = 'btn btn-default';
            $this->btnGoToNews->CausesValidation = false;
            $this->btnGoToNews->addAction(new Click(), new AjaxControl($this, 'btnGoToNews_Click'));
            $this->btnGoToNews->setCssStyle('float', 'left');

            if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                $this->btnGoToNews->Display = true;
            } else {
                $this->btnGoToNews->Display = false;
            }

            $this->txtChange = new Bs\TextBox($this);
            $this->txtChange->Placeholder = t('New change');
            $this->txtChange->ActionParameter = $this->txtChange->ControlId;
            $this->txtChange->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
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
         * Initializes and configures two Toastr notification instances for success and error alerts.
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
         * Creates and initializes several modal dialogs with specific titles, texts, headers, and buttons.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the news change?</p>
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The news change cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this news change, 
                                    simply release any news changes previously associated with created news.</p>');
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The news change cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this change, 
                                    simply release any changes previously associated with created news.</p>');
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This change already exists! Please choose another new name!</p>');
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
         * Handles the event when the Add/Change button is clicked. This method updates the UI components
         * to enable editing a change by displaying and enabling relevant input fields and buttons while
         * disabling the Add/Change button. It also sets the selected status to a default value.
         *
         * @param ActionParams $params Parameters provided by the action event.
         *
         * @return void
         * @throws RandomException
         * @throws Caller
         */
        protected function btnAddChange_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->btnAddChange->Enabled = false;
            $this->btnGoToNews->Display = false;
            $this->lstStatus->SelectedValue = 2;
            $this->txtChange->Text = '';
            $this->txtChange->focus();

            $this->disableInputs();

            if (!$this->txtChange->Text) {
                $this->btnDelete->Display = false;
            }

            $this->btnSave->Display = false;
            $this->btnSaveChange->Display = true;
        }

        /**
         * Handles the logic for saving changes when save a button is clicked.
         *
         * @param ActionParams $params The parameters associated with the action triggered.
         *
         * @return void
         * @throws Caller
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
                if (!NewsChanges::titleExists(trim($this->txtChange->Text))) {
                    $objCategoryNews = new NewsChanges();
                    $objCategoryNews->setTitle(trim($this->txtChange->Text));
                    $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                    $objCategoryNews->setPostDate(QDateTime::now());
                    $objCategoryNews->save();

                    if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                        $this->btnGoToNews->Display = true;
                    }

                    $this->btnAddChange->Enabled = true;
                    $this->enableInputs();

                    $this->dtgNewsChanges->refresh();

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

            $this->userOptions();
        }

        /**
         * Handles the logic for saving changes to news items. This method checks the status of the change, validates the input,
         * and updates the news item if necessary. It also manages the display and state of UI components based on the operation outcome.
         *
         * @param ActionParams $params Parameters passed from the action triggering this method. Contains information about the action context.
         *
         * @return void This method does not return any value.
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

            $objNewsChanges = NewsChanges::loadById($this->intId);

            if ($this->txtChange->Text) {
                if (NewsChanges::titleExists(trim($this->txtChange->Text)) && $this->lstStatus->SelectedValue ==  $objNewsChanges->getStatus()) {
                    $this->txtChange->Text = $objNewsChanges->getTitle();
                    $this->btnGoToNews->Display = false;
                    $this->dlgModal4->showDialogBox();
                    return;
                }

                if (News::countByNewsChangesId($this->intId) > 0 && $this->lstStatus->SelectedValue == 2) {

                    if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                        $this->btnGoToNews->Display = true;
                    }

                    $this->lstStatus->SelectedValue = 1;
                    $this->dlgModal2->showDialogBox();

                } else if (($this->txtChange->Text == $objNewsChanges->getTitle() && $this->lstStatus->SelectedValue !== $objNewsChanges->getStatus()) ||
                    ($this->txtChange->Text !== $objNewsChanges->getTitle() && $this->lstStatus->SelectedValue == $objNewsChanges->getStatus())) {
                    $objNewsChanges->setTitle(trim($this->txtChange->Text));
                    $objNewsChanges->setStatus($this->lstStatus->SelectedValue);
                    $objNewsChanges->setPostUpdateDate(QDateTime::now());
                    $objNewsChanges->save();

                    if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                        $this->btnGoToNews->Display = true;
                    }

                    $this->btnAddChange->Enabled = true;
                    $this->enableInputs();

                    $this->dtgNewsChanges->refresh();

                    $this->dlgToastr1->notify();
                }
            } else {
                $this->txtChange->Text = $objNewsChanges->getTitle();
                $this->txtChange->focus();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        /**
         * Handles the delete button click event. Checks if the current ID is in the list of change IDs
         * and shows a modal dialog accordingly. It adjusts the visibility and enabled state of various
         * UI components based on the session state and other conditions.
         *
         * @param ActionParams $params The parameters associated with the delete button click event.
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

            if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                $this->btnGoToNews->Display = true;
            }

            if (News::countByNewsChangesId($this->intId) > 0) {
                $this->dlgModal3->showDialogBox();
                $this->disableInputs();
            } else {
                $this->dlgModal1->showDialogBox();
            }

            $this->userOptions();
        }

        /**
         * Handles the click event for deleting a news item change.
         *
         * @param ActionParams $params The parameters passed with the action, including the action parameter to confirm deletion.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            $objNewsChanges = NewsChanges::loadById($this->intId);

            if ($params->ActionParameter == "pass") {
                $objNewsChanges->delete();
            }

            $this->dtgNewsChanges->refresh();

            $this->btnAddChange->Enabled = true;
            $this->enableInputs();

            $this->dlgModal1->hideDialogBox();

            $this->userOptions();
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
         * @throws Caller
         */
        protected function hideItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->btnAddChange->Enabled = true;
            $this->enableInputs();
        }

        /**
         * Handles the cancel button click event to reset the UI to its initial state by hiding certain elements,
         * enabling others, and clearing any entered text changes.
         *
         * @param ActionParams $params The parameters associated with the action that triggered this event.
         *
         * @return void This method does not return a value.
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

            $this->userOptions();

            if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                $this->btnGoToNews->Display = true;
            }

            $this->btnAddChange->Enabled = true;
            $this->enableInputs();
            $this->txtChange->Text = '';
            $this->btnSaveChange->Display = false;
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
            $this->txtChange->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->btnClearFilters->Enabled = true;
            $this->dtgNewsChanges->Paginator->Enabled = true;

            $this->dtgNewsChanges->removeCssClass('disabled');
        }

        /**
         * Disables specific input elements and applies a disabled style to the news changes data grid.
         *
         * This method sets the `Enabled` property of specific input controls to `false`,
         * indicating that those inputs are no longer interactable. Additionally, the data grid
         * for gallery groups is styled with a disabled CSS class for visual feedback.
         *
         * @return void This method does not return any value.
         */
        public function disableInputs(): void
        {
            $this->txtChange->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->txtFilter->Enabled = false;
            $this->btnClearFilters->Enabled = false;
            $this->dtgNewsChanges->Paginator->Enabled = false;

            $this->dtgNewsChanges->addCssClass('disabled');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the "Go To News" button. Redirects the user to the news edit page if session variables
         * for news changes are set and clears these session variables afterwards.
         *
         * @param ActionParams $params The event parameters associated with the button click action.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnGoToNews_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                $news = $_SESSION['news_changes_id'];
                $group = $_SESSION['news_changes_group'];

                Application::redirect('news_edit.php?id=' . $news . '&group=' . $group);
                unset($_SESSION['news_changes_id']);
                unset($_SESSION['news_changes_group']);
            }
        }
    }