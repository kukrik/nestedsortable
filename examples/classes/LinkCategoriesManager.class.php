<?php

    use QCubed as Q;
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
     * Class LinkChangesManager
     *
     * Manages the Link Changes component of the application, providing CRUD functionality,
     * filtering, pagination, and user-specific configurations for managing news change items.
     * This class extends a base Panel class to initialize user interaction, display elements,
     * and data-binding capabilities for News Changes.
     */
    class LinkCategoriesManager extends Panel
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
        public LinksCategoryTable $dtgLinkCategories;

        public Bs\Button $btnAddCategory;
        public Bs\Button $btnGoToLinks;
        public Bs\TextBox $txtCategory;
        public Q\Plugin\Control\RadioList $lstStatus;
        public Bs\Button $btnSaveCategory;
        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected array $objCategoryIds = [];
        protected ?string $oldName = '';

        protected string $strTemplate = 'LinkCategoriesManager.tpl.php';

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

            // $objUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgLinkCategories_Create();
            $this->dtgLinkCategories->setDataBinder('BindData', $this);
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
            $this->CheckCategories();
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
         * Initializes and configures the data grid for displaying link categories.
         *
         * This method sets up the data grid instance and configures its columns,
         * pagination, editability, row parameters, sorting, and items per a page.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgLinkCategories_Create(): void
        {
            $this->dtgLinkCategories = new LinksCategoryTable($this);
            $this->dtgLinkCategories_CreateColumns();
            $this->createPaginators();
            $this->dtgLinkCategories_MakeEditable();
            $this->dtgLinkCategories->RowParamsCallback = [$this, "dtgLinkCategories_GetRowParams"];
            $this->dtgLinkCategories->SortColumnIndex = 0;
            $this->dtgLinkCategories->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgLinkCategories->UseAjax = true;
        }

        /**
         * Creates the columns for the data grid of link categories.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgLinkCategories_CreateColumns(): void
        {
            $this->dtgLinkCategories->createColumns();
        }

        /**
         * Configures the data grid of link categories to be editable by adding actions and CSS classes.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgLinkCategories_MakeEditable(): void
        {
            $this->dtgLinkCategories->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgLinkCategoriesRow_Click'));
            $this->dtgLinkCategories->addCssClass('clickable-rows');
            $this->dtgLinkCategories->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event on a row in the link categories data grid.
         *
         * This method retrieves the selected link category details, sets up the form for editing,
         * and updates the UI components' states accordingly. It also verifies the CSRF token for security.
         *
         * @param ActionParams $params Contains the parameters of the action, including the selected row's ID.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgLinkCategoriesRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->intId = intval($params->ActionParameter);
            $objCategories = LinksCategory::load($this->intId);

            $this->oldName = $objCategories->getTitle();

            $this->txtCategory->Text = $objCategories->getTitle();
            $this->txtCategory->focus();
            $this->lstStatus->SelectedValue = $objCategories->Status ?? null;

            $this->btnAddCategory->Enabled = false;
            $this->btnGoToLinks->Display = false;

            $this->disableInputs();

            if (Links::countByCategoryId($this->intId) > 0) {
                $this->dlgModal3->showDialogBox();
                $this->btnDelete->Enabled = false;
            } else {
                $this->btnDelete->Enabled = true;
            }
        }

        /**
         * Retrieves the row parameters for a given row object in the link categories data grid.
         *
         * @param mixed $objRowObject The data object representing the row.
         * @param int $intRowIndex The index of the row in the data grid.
         *
         * @return array The parameters to be applied to the row, including a data-value attribute with the primary key.
         */
        public function dtgLinkCategories_GetRowParams(object $objRowObject, int $intRowIndex): array
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
            $this->dtgLinkCategories->Paginator = new Bs\Paginator($this);
            $this->dtgLinkCategories->Paginator->LabelForPrevious = t('Previous');
            $this->dtgLinkCategories->Paginator->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        /**
         * Initializes and configures the items-per-page selection component for the assigned user object.
         * It sets display properties like theme, width, and selection mode and populates it with items.
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
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstPreferredItemsPerPageObject_GetItems());
            $this->lstItemsPerPageByAssignedUserObject->AddAction(new Change(), new AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
        }

        /**
         * Retrieves a list of list items representing each `ItemsPerPage` object associated with the assigned user
         * object.
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
            $this->dtgLinkCategories->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgLinkCategories->refresh();
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

            $this->dtgLinkCategories->refresh();
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
            $this->dtgLinkCategories->refresh();
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
            $this->dtgLinkCategories->bindData($objCondition);
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
                    QQ::equal(QQN::LinksCategory()->Id, $strSearchValue),
                    QQ::like(QQN::LinksCategory()->Title, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Checks the categories associated with links and stores the category IDs in a class property.
         *
         * Iterates through all available links, and for each link with an associated category,
         * retrieves and saves the category ID for further use.
         *
         * @return void
         * @throws Caller
         */
        private function CheckCategories(): void
        {
            $objLinkArray = Links::loadAll();

            foreach ($objLinkArray as $objLink) {
                if ($objLink->getCategoryId()) {
                    $this->objCategoryIds[] = $objLink->getCategoryId();
                }
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures various buttons and input components, including their
         * properties, styles, actions, and display states used in the form or UI.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createButtons(): void
        {
            $this->btnAddCategory = new Bs\Button($this);
            $this->btnAddCategory->Text = t(' Create a new category');
            $this->btnAddCategory->Glyph = 'fa fa-plus';
            $this->btnAddCategory->CssClass = 'btn btn-orange';
            $this->btnAddCategory->addWrapperCssClass('center-button');
            $this->btnAddCategory->CausesValidation = false;
            $this->btnAddCategory->addAction(new Click(), new AjaxControl($this, 'btnAddCategory_Click'));
            $this->btnAddCategory->setCssStyle('float', 'left');
            $this->btnAddCategory->setCssStyle('margin-right', '10px');

            $this->btnGoToLinks = new Bs\Button($this);
            $this->btnGoToLinks->Text = t('Go to this link');
            $this->btnGoToLinks->addWrapperCssClass('center-button');
            $this->btnGoToLinks->CssClass = 'btn btn-default';
            $this->btnGoToLinks->CausesValidation = false;
            $this->btnGoToLinks->addAction(new Click(), new AjaxControl($this, 'btnGoToLinks_Click'));
            $this->btnGoToLinks->setCssStyle('float', 'left');

            if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
                $this->btnGoToLinks->Display = true;
            } else {
                $this->btnGoToLinks->Display = false;
            }

            $this->txtCategory = new Bs\TextBox($this);
            $this->txtCategory->Placeholder = t('New category');
            $this->txtCategory->ActionParameter = $this->txtCategory->ControlId;
            $this->txtCategory->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtCategory->setHtmlAttribute('autocomplete', 'off');
            $this->txtCategory->setCssStyle('float', 'left');
            $this->txtCategory->setCssStyle('margin-right', '10px');
            $this->txtCategory->Width = 300;
            $this->txtCategory->Display = false;

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange form-horizontal radio-inline';
            $this->lstStatus->setCssStyle('float', 'left');
            $this->lstStatus->setCssStyle('margin-left', '15px');
            $this->lstStatus->setCssStyle('margin-right', '15px');
            $this->lstStatus->Display = false;

            $this->btnSaveCategory = new Bs\Button($this);
            $this->btnSaveCategory->Text = t('Save');
            $this->btnSaveCategory->CssClass = 'btn btn-orange';
            $this->btnSaveCategory->addWrapperCssClass('center-button');
            $this->btnSaveCategory->PrimaryButton = true;
            $this->btnSaveCategory->CausesValidation = true;
            $this->btnSaveCategory->addAction(new Click(), new AjaxControl($this, 'btnSaveCategory_Click'));
            $this->btnSaveCategory->setCssStyle('float', 'left');
            $this->btnSaveCategory->setCssStyle('margin-right', '10px');
            $this->btnSaveCategory->Display = false;

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
         * Creates and configures Toastr notifications for success and error alerts.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The category has been saved or modified.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the category name must exist!');
            $this->dlgToastr2->ProgressBar = true;
        }

        /**
         * Creates various modal dialogs for user interactions within the application.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the category?</p>
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The category cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this category, 
                                    simply release any category previously associated with created link.</p>');
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The category cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this category, 
                                    simply release any links previously associated with created link.</p>');
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The title of this category already exists in the database, please choose another title!</p>');
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
         * Handles the click event for the "Add Category" button. This method sets up the UI elements
         * to allow the user to add a new category, ensuring proper handling of CSRF protection.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Exception If CSRF token generation fails or an invalid action occurs.
         */
        protected function btnAddCategory_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->btnGoToLinks->Display = false;
            $this->txtCategory->Display = true;
            $this->lstStatus->SelectedValue = 2;
            $this->txtCategory->Text = '';
            $this->txtCategory->focus();

            $this->disableInputs();

            if (!$this->txtCategory->Text) {
                $this->btnDelete->Display = false;
            }

            $this->btnSave->Display = false;
            $this->btnSaveCategory->Display = true;
        }

        /**
         * Handles the save category button click event. Validates CSRF token, processes the category
         * input, checks for its uniqueness, and either saves it or notifies the user of issues.
         *
         * @param ActionParams $params Action parameters passed during the button click event.
         *
         * @return void
         * @throws Exception If errors occur during token generation or category save.
         */
        protected function btnSaveCategory_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
                $this->btnGoToLinks->Display = true;
            }

            if ($this->txtCategory->Text) {
                if (!LinksCategory::titleExists(trim($this->txtCategory->Text))) {
                    $objCategoryNews = new LinksCategory();
                    $objCategoryNews->setTitle(trim($this->txtCategory->Text));
                    $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                    $objCategoryNews->setPostDate(QDateTime::now());
                    $objCategoryNews->save();

                    $this->btnAddCategory->Enabled = true;
                    $this->enableInputs();

                    $this->dtgLinkCategories->refresh();

                    $this->txtCategory->Text = '';
                    $this->dlgToastr1->notify();
                } else {
                    $this->txtCategory->Text = '';
                    $this->txtCategory->focus();
                    $this->dlgModal4->showDialogBox();
                }
            } else {
                $this->txtCategory->Text = '';
                $this->txtCategory->focus();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        /**
         * Handles the click event for the save a button, performing actions such as validating the CSRF token,
         * updating category data, and managing UI updates based on the operation's success or constraints.
         *
         * @param ActionParams $params The parameters associated with the save button click action.
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

            if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
                $this->btnGoToLinks->Display = true;
            }

            $objCategories = LinksCategory::loadById($this->intId);

            if (trim($this->txtCategory->Text)) {
                if (LinksCategory::titleExists(trim($this->txtCategory->Text))) {
                    $this->txtCategory->Text = $objCategories->getTitle();
                    $this->btnGoToLinks->Display = false;
                    $this->dlgModal4->showDialogBox();
                    return;
                }

                if (Links::countByCategoryId($this->intId) > 0 && $this->lstStatus->SelectedValue == 2) {
                    $this->lstStatus->SelectedValue = 1;
                    $this->dlgModal2->showDialogBox();

                } else if (Links::countByCategoryId($this->intId) === 0 && $this->txtCategory->Text == $objCategories->getTitle() && $this->lstStatus->SelectedValue !== $objCategories->getStatus()) {
                    $objCategories->setTitle(trim($this->txtCategory->Text));
                    $objCategories->setStatus($this->lstStatus->SelectedValue);
                    $objCategories->setPostUpdateDate(QDateTime::now());
                    $objCategories->save();

                    $this->btnAddCategory->Enabled = true;
                    $this->enableInputs();

                    $this->dtgLinkCategories->refresh();

                    $this->dlgToastr1->notify();
                }
            } else {
                $this->txtCategory->Text = $objCategories->getTitle();
                $this->txtCategory->focus();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        /**
         * Handles the click event for the delete button. Verifies CSRF token, processes
         * category deletion logic, and updates UI components or shows appropriate dialog boxes
         * based on the current state.
         *
         * @param ActionParams $params The parameters associated with the action triggered.
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

            if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
                $this->btnGoToLinks->Display = true;
            }

            if (Links::countByCategoryId($this->intId) > 0) {
                $this->dlgModal3->showDialogBox();
                $this->disableInputs();
            } else {
                $this->dlgModal1->showDialogBox();
            }

            $this->userOptions();
        }

        /**
         * Handles the click event for deleting an item based on its ID and updates the user interface accordingly.
         *
         * @param ActionParams $params Parameters associated with the delete action, including the action parameter.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            $objCategories = LinksCategory::loadById($this->intId);

            if ($params->ActionParameter == "pass") {
                $objCategories->delete();
            }

            $this->dtgLinkCategories->refresh();

            $this->btnAddCategory->Enabled = true;
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

            $this->btnAddCategory->Enabled = true;
            $this->enableInputs();
        }

        /**
         * Handles the cancel button click event. This method performs various UI updates,
         * including hiding and showing appropriate buttons and fields, resetting the category
         * input, and managing CSRF token verification.
         *
         * @param ActionParams $params Parameters associated with the triggered action.
         *
         * @return void
         * @throws Exception If an error occurs during CSRF token generation.
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
                $this->btnGoToLinks->Display = true;
            }

            $this->btnAddCategory->Enabled = true;
            $this->enableInputs();
            $this->txtCategory->Text = '';
            $this->btnSaveCategory->Display = false;
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
            $this->txtCategory->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->btnClearFilters->Enabled = true;
            $this->dtgLinkCategories->Paginator->Enabled = true;

            $this->dtgLinkCategories->removeCssClass('disabled');
        }

        /**
         * Disables specific input elements and applies a disabled style to the links categories data grid.
         *
         * This method sets the `Enabled` property of specific input controls to `false`,
         * indicating that those inputs are no longer interactable. Additionally, the data grid
         * for gallery groups is styled with a disabled CSS class for visual feedback.
         *
         * @return void This method does not return any value.
         */
        public function disableInputs(): void
        {
            $this->txtCategory->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;

            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->txtFilter->Enabled = false;
            $this->btnClearFilters->Enabled = false;
            $this->dtgLinkCategories->Paginator->Enabled = false;

            $this->dtgLinkCategories->addCssClass('disabled');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the 'Go To Links' button. It verifies the CSRF token to ensure security
         * and, if valid, redirects the user to the link edit page with the provided session parameters.
         * Resets session variables associated with links after redirection.
         *
         * @param ActionParams $params Contains the parameters for the action associated with the button click.
         *
         * @return void
         * @throws Exception If generating a CSRF token fails.
         * @throws Throwable
         */
        protected function btnGoToLinks_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {

                Application::redirect('links_edit.php?id=' . $_SESSION['links'] . '&group=' . $_SESSION['group']);
                unset($_SESSION['links']);
                unset($_SESSION['group']);
            }
        }
    }