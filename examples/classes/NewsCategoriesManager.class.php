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
    use QCubed\Event\EnterKey;
    use QCubed\Event\Input;
    use QCubed\Event\DialogButton;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\All;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;

    /**
     * Manages the listing and interaction of news categories within a data grid interface.
     * Provides functionality for editing, filtering, and paginating categories, while incorporating
     * user-specific preferences such as items per a page and AJAX-based interactivity.
     */
    class NewsCategoriesManager extends Panel
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
        public NewsCategoriesTable $dtgCategoryOfNewses;

        public Bs\Button $btnAddCategory;
        public Bs\Button $btnGoToNews;
        public Bs\TextBox $txtCategory;
        public Q\Plugin\Control\RadioList $lstStatus;
        public Bs\Button $btnSaveCategory;
        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected object $objUser;
        protected int $intLoggedUserId;

        protected string $strTemplate = 'NewsCategoriesManager.tpl.php';

        /**
         * Constructor for initializing the object and setting up its state.
         *
         * @param mixed $objParentObject The parent object that this object will be attached to.
         * @param null|string $strControlId Optional control ID for the object.
         *
         * @throws Caller Thrown if there is an error in the caller's logic.
         * @throws InvalidCast
         * @throws Exception
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
            $this->dtgNews_Create();
            $this->dtgCategoryOfNewses->setDataBinder('BindData', $this);
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the NewsCategoriesTable for displaying news categories.
         * Sets up columns, pagination, editability, and row parameters callback.
         * Sorts by the first column and sets the items per page based on user preferences.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgNews_Create(): void
        {
            $this->dtgCategoryOfNewses = new NewsCategoriesTable($this);
            $this->dtgCategoryOfNewses_CreateColumns();
            $this->createPaginators();
            $this->dtgCategoryOfNewses_MakeEditable();
            $this->dtgCategoryOfNewses->RowParamsCallback = [$this, "dtgCategoryOfNewses_GetRowParams"];
            $this->dtgCategoryOfNewses->SortColumnIndex = 0;
            $this->dtgCategoryOfNewses->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();

        }

        /**
         * Creates columns for the category of news data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgCategoryOfNewses_CreateColumns(): void
        {
            $this->dtgCategoryOfNewses->createColumns();
        }

        /**
         * Configures the category of news data grid to be editable by making its rows clickable.
         * It sets up an action for cell clicks to trigger an Ajax control event and applies
         * the necessary CSS classes to the table for styling and responsiveness.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgCategoryOfNewses_MakeEditable(): void
        {
            $this->dtgCategoryOfNewses->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgCategoryOfNewsesRow_Click'));
            $this->dtgCategoryOfNewses->addCssClass('clickable-rows');
            $this->dtgCategoryOfNewses->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event on a category of news row, updating the UI for editing the selected category.
         *
         * @param ActionParams $params An object containing parameters related to the action event, such as the selected row's identifier.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgCategoryOfNewsesRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);
            $objCategoryNews = CategoryOfNews::load($this->intId);

            $this->txtCategory->Text = $objCategoryNews->getName() ?? '';
            $this->txtCategory->focus();
            $this->lstStatus->SelectedValue = $objCategoryNews->IsEnabled ?? null;

            $this->dtgCategoryOfNewses->addCssClass('disabled');
            $this->btnAddCategory->Enabled = false;
            $this->btnGoToNews->Display = false;
            $this->txtCategory->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;
        }

        /**
         * Retrieves the parameters for a row in the CategoryOfNewses data table.
         *
         * @param object $objRowObject The object representing the row, which contains the data attributes.
         * @param int $intRowIndex The index of the current row within the data table.
         *
         * @return array An associative array containing parameters for the row, with 'data-value' as a key.
         */
        public function dtgCategoryOfNewses_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures the paginators for the data grid.
         *
         * The method sets up a new paginator with specific labels for navigation.
         * It also configures the items per page, the default sort column, and enables Ajax for dynamic updates.
         * Additionally, it adds filter actions to the data grid.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgCategoryOfNewses->Paginator = new Bs\Paginator($this);
            $this->dtgCategoryOfNewses->Paginator->LabelForPrevious = t('Previous');
            $this->dtgCategoryOfNewses->Paginator->LabelForNext = t('Next');

            $this->dtgCategoryOfNewses->ItemsPerPage = 10;
            $this->dtgCategoryOfNewses->SortColumnIndex = 4;
            $this->dtgCategoryOfNewses->UseAjax = true;
            $this->addFilterActions();
        }

        /**
         * Initializes and configures a Select2 control for selecting the number of items per a page.
         * Sets the required properties and event handling for changes in selection.
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
         * Retrieves a list of items per a page associated with an assigned user object.
         *
         * @return ListItem[] An array of ListItem objects representing the items per page
         *                    for the assigned user. Each ListItem contains the display
         *                    text and associated ID, with the appropriate item marked
         *                    as selected based on the current user's assignment.
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
         * Handles the change event for the items per page list controlled by an assigned user object.
         *
         * @param ActionParams $params The parameters for the action event that triggered this change.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgCategoryOfNewses->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgCategoryOfNewses->refresh();
        }

        /**
         * Initializes a text filter for search functionality.
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

            $this->dtgCategoryOfNewses->refresh();
        }

        /**
         * Adds filter actions to the txtFilter control. Specifically, it registers AJAX actions
         * to be triggered on specific events, such as input and pressing the enter key. When
         * triggered, these actions invoke the filterChanged method, allowing dynamic filtering
         * functionality in the interface.
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
         * Handles the event when a filter is changed in the CategoryOfNewses data table.
         *
         * @return void This method refreshes the data table to reflect any changes in the filter settings.
         */
        protected function filterChanged(): void
        {
            $this->dtgCategoryOfNewses->refresh();
        }

        /**
         * Binds data to the data grid using specific conditions.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgCategoryOfNewses->bindData($objCondition);
        }

        /**
         * Generates a query condition based on the current text filter input.
         *
         * If the filter input is empty or null, a condition that matches all entries is returned.
         * Otherwise, returns a compound condition that performs a case-insensitive search for the filter input
         * within the Picture, GroupTitle, Title, Category, and Author fields of the News entity.
         *
         * @return All|OrCondition The generated query condition, either matching all entries or matching specified
         *     fields against the filter input.
         * @throws Caller
         */
        protected function getCondition(): All|OrCondition
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
                    QQ::equal(QQN::CategoryOfNews()->Id, $strSearchValue),
                    QQ::like(QQN::CategoryOfNews()->Name, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and creates various buttons and input controls for category management in the user interface.
         * Configures buttons with specific styles, actions, and visibility based on session conditions.
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

            $this->btnGoToNews = new Bs\Button($this);
            $this->btnGoToNews->Text = t('Go to this news');
            $this->btnGoToNews->addWrapperCssClass('center-button');
            $this->btnGoToNews->CssClass = 'btn btn-default';
            $this->btnGoToNews->CausesValidation = false;
            $this->btnGoToNews->addAction(new Click(), new AjaxControl($this, 'btnGoToNews_Click'));
            $this->btnGoToNews->setCssStyle('float', 'left');

            if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                $this->btnGoToNews->Display = true;
            } else {
                $this->btnGoToNews->Display = false;
            }

            $this->txtCategory = new Bs\TextBox($this);
            $this->txtCategory->Placeholder = t('New category');
            $this->txtCategory->ActionParameter = $this->txtCategory->ControlId;
            $this->txtCategory->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
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
         * Initializes two toastr notifications with predefined settings. The first
         * toastr is configured to display a success message, while the second toastr
         * is configured to display an error message. Both toasters are displayed at
         * the top center of the screen and include a progress bar.
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
            $this->dlgToastr2->Message = t('The category name must exist!');
            $this->dlgToastr2->ProgressBar = true;
        }

        /**
         * Creates and configures a series of modals for various user interactions
         * such as warnings, tips, and confirmations regarding news category actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the news category?</p>
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The news category cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this news category, 
                                    simply release any news categories previously associated with created news.</p>');
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The news category cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this category, 
                                    simply release any categories previously associated with created news.</p>');
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This category already exists! Please choose another new name!</p>');
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
         * Handles the click event for the "Add Category" button, updating the display and state of various UI
         * components.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws RandomException
         */
        protected function btnAddCategory_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->btnGoToNews->Display = false;
            $this->txtCategory->Display = true;
            $this->lstStatus->Display = true;
            $this->lstStatus->SelectedValue = 2;
            $this->btnSaveCategory->Display = true;
            $this->btnCancel->Display = true;
            $this->txtCategory->Text = '';
            $this->txtCategory->focus();
            $this->btnAddCategory->Enabled = false;
            $this->dtgCategoryOfNewses->addCssClass('disabled');
        }

        /**
         * Handles saving a new category when the "Save" button is clicked.
         * Validates the category name and ensures it is not a duplicate before saving.
         * Toggles UI element visibility and states based on the operation's success or failure.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnSaveCategory_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->txtCategory->Text) {
                if (!CategoryOfNews::titleExists($this->txtCategory->Text)) {
                    $objCategoryNews = new CategoryOfNews();
                    $objCategoryNews->setName(trim($this->txtCategory->Text));
                    $objCategoryNews->setIsEnabled($this->lstStatus->SelectedValue);
                    $objCategoryNews->setPostDate(Q\QDateTime::Now());
                    $objCategoryNews->save();

                    if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                        $this->btnGoToNews->Display = true;
                    }

                    $this->dtgCategoryOfNewses->refresh();

                    $this->displayHelper();
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
        }

        /**
         * Handles the click event for save a button, performing various operations
         * based on the current state of the category of news being edited or added.
         *
         * @param ActionParams $params The parameters associated with save a button's action event.
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

            $objCategoryOfNews = CategoryOfNews::loadById($this->intId);

            if ($this->txtCategory->Text) {
                if (CategoryOfNews::titleExists(trim($this->txtCategory->Text)) && $this->lstStatus->SelectedValue == $objCategoryOfNews->getIsEnabled()) {
                    $this->txtCategory->Text = $objCategoryOfNews->getName();
                    $this->btnGoToNews->Display = false;
                    $this->dlgModal4->showDialogBox();
                    return;
                }

                if (News::countByNewsCategoryId($this->intId) > 0 && $this->lstStatus->SelectedValue == 2) {

                    if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                        $this->btnGoToNews->Display = true;
                    }

                    $this->lstStatus->SelectedValue = 1;
                    $this->dlgModal2->showDialogBox();
                    $this->displayHelper();

                } else if (($this->txtCategory->Text == $objCategoryOfNews->getName() && $this->lstStatus->SelectedValue !== $objCategoryOfNews->getIsEnabled()) ||
                    ($this->txtCategory->Text !== $objCategoryOfNews->getName() && $this->lstStatus->SelectedValue == $objCategoryOfNews->getIsEnabled())) {

                    $objCategoryOfNews->setName(trim($this->txtCategory->Text));
                    $objCategoryOfNews->setIsEnabled($this->lstStatus->SelectedValue);
                    $objCategoryOfNews->setPostUpdateDate(Q\QDateTime::now());
                    $objCategoryOfNews->save();

                    if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                        $this->btnGoToNews->Display = true;
                    }

                    $this->dtgCategoryOfNewses->refresh();
                    $this->displayHelper();
                    $this->dlgToastr1->notify();
                }
            } else {
                $this->txtCategory->Text = $objCategoryOfNews->getName();
                $this->txtCategory->focus();
                $this->dlgToastr2->notify();
            }
        }

        /**
         * Handles the click event for the delete button. Determines the state of various UI components and displays
         * the appropriate dialog based on whether the category ID is present in the category IDs list.
         *
         * @param ActionParams $params The parameters associated with the button click event.
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

            if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                $this->btnGoToNews->Display = true;
            }

            if (News::countByNewsCategoryId($this->intId)) {
                $this->dlgModal3->showDialogBox();
                $this->displayHelper();
            } else {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the click event for deleting a category of news item.
         *
         * @param ActionParams $params The parameters associated with the action, including the action parameter.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            $objCategoryOfNews = CategoryOfNews::loadById($this->intId);

            if ($params->ActionParameter == "pass") {
                $objCategoryOfNews->delete();
            }

            $this->dtgCategoryOfNewses->refresh();

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
         * Handles the click event of the cancel button. It resets the display states
         * of various UI components and clears the category text input. Additionally,
         * it manages the enabled/disabled state of the added category button and the
         * CSS class of the news categories datagrid.
         *
         * @param ActionParams $params The parameters associated with the button click action.
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

            if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                $this->btnGoToNews->Display = true;
            }

            $this->displayHelper();
            $this->txtCategory->Text = '';
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Configures the display properties and state of various UI components.
         *
         * @return void
         */
        protected function displayHelper(): void
        {
            $this->btnAddCategory->Enabled = true;
            $this->txtCategory->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSaveCategory->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;

            $this->dtgCategoryOfNewses->removeCssClass('disabled');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the "Go To News" button. Redirects the user to the news edit page
         * with the appropriate news category ID and group retrieved from the session variables. Clears the
         * session variables after redirection.
         *
         * @param ActionParams $params The parameters associated with the button click action.
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

            if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                $news = $_SESSION['news_categories_id'];
                $group = $_SESSION['news_categories_group'];

                Application::redirect('news_edit.php?id=' . $news . '&group=' . $group);
                unset($_SESSION['news_categories_id']);
                unset($_SESSION['news_categories_group']);
            }
        }
    }