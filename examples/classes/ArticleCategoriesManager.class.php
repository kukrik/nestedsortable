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
    use QCubed\Query\Condition\All;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;


    /**
     * Class ArticleCategoriesManager
     *
     * Manages the operations related to article categories, including creating, viewing, updating,
     * and removing categories. The class handles the data grid creation, pagination, filtering,
     * and modal integration for a user-friendly interface.
     */
    class ArticleCategoriesManager extends Panel
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
        public Bs\Modal $dlgModal6;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public ArticleCategoriesTable $dtgCategoryOfArticle;

        public Bs\Button $btnAddCategory;
        public Bs\Button $btnGoToArticle;
        public Bs\TextBox $txtCategory;
        public Q\Plugin\Control\RadioList $lstStatus;
        public Bs\Button $btnSaveCategory;
        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected object $objUser;
        protected int $intLoggedUserId;
        protected ?string $oldName = '';

        protected object $objCategoryOfArticle;
        protected array $objCategoryIds = [];
        protected array $objCategoryNames = [];
        protected array $objCompressTexts = [];
        protected string $objMenuTexts;

        protected string $strTemplate = 'ArticleCategoriesManager.tpl.php';

        /**
         * Constructor method for initializing the object, setting up user session details, and preparing necessary UI components.
         *
         * @param mixed $objParentObject The parent object to the current instance. This could be a form or control that contains this object.
         * @param string|null $strControlId An optional control ID. If not specified, a default one will be automatically generated.
         *
         * @throws DateMalformedStringException
         * @throws Caller
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

            $this->intLoggedUserId = 1;
            $this->objUser = User::load($this->intLoggedUserId);

            //$this->intId = Application::instance()->context()->queryStringItem('id');
            //$this->objCategoryOfArticle = CategoryOfArticle::load($this->intId);

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must save something here $this->objNews->setUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgCategoryOfArticle_Create();
            $this->dtgCategoryOfArticle->setDataBinder('BindData', $this);
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
            $this->CheckCategories();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes the Article Categories Table with columns, paginators,
         * and editability settings, and configures row parameters and sorting.
         *
         * @return void
         *
         * @throws Caller
         */
        protected function dtgCategoryOfArticle_Create(): void
        {
            $this->dtgCategoryOfArticle = new ArticleCategoriesTable($this);
            $this->dtgCategoryOfArticle_CreateColumns();
            $this->createPaginators();
            $this->dtgCategoryOfArticle_MakeEditable();
            $this->dtgCategoryOfArticle->RowParamsCallback = [$this, "dtgCategoryOfArticle_GetRowParams"];
            $this->dtgCategoryOfArticle->SortColumnIndex = 0;
            $this->dtgCategoryOfArticle->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
        }

        /**
         * Initializes and creates columns for the CategoryOfArticle data grid.
         *
         * This method is responsible for setting up the necessary columns
         * in the data grid that displays categories of articles. It delegates
         * the actual creation of columns to the `createColumns` method
         * of the data grid object.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgCategoryOfArticle_CreateColumns(): void
        {
            $this->dtgCategoryOfArticle->createColumns();
        }

        /**
         * Configures the CategoryOfArticle data grid to be editable through user interactions.
         *
         * This method sets up the data grid to respond to cell click events by linking the event
         * to the specified AJAX control action. It also modifies the CSS classes to ensure
         * that the data grid rows are visually distinct and responsive, enhancing the user
         * interface for interactivity.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgCategoryOfArticle_MakeEditable(): void
        {
            $this->dtgCategoryOfArticle->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgCategoryOfArticleRow_Click'));
            $this->dtgCategoryOfArticle->addCssClass('clickable-rows');
            $this->dtgCategoryOfArticle->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click action on the CategoryOfArticle data grid row.
         *
         * This method is triggered when a user clicks on a row in the CategoryOfArticle
         * data grid. It loads the corresponding CategoryOfArticle record based on the
         * provided action parameter, updates the UI controls with the category's details,
         * and adjusts the visibility and state of several buttons and inputs.
         *
         * @param ActionParams $params The parameters associated with the click action,
         *                             including the identifier for the selected category.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgCategoryOfArticleRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);
            $objCategoryOfArticle = CategoryOfArticle::load($this->intId);

            $this->oldName = $objCategoryOfArticle->getName();

            $this->txtCategory->Text = $objCategoryOfArticle->getName();
            $this->txtCategory->focus();
            $this->lstStatus->SelectedValue = $objCategoryOfArticle->IsEnabled ?? null;

            $this->dtgCategoryOfArticle->addCssClass('disabled');
            $this->btnAddCategory->Enabled = false;
            $this->btnGoToArticle->Display = false;
            $this->txtCategory->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;

            $this->oldName = $objCategoryOfArticle->getName();

            $this->txtCategory->Text = $objCategoryOfArticle->getName();
            $this->txtCategory->focus();
            $this->lstStatus->SelectedValue = $objCategoryOfArticle->IsEnabled ?? null;

            $this->dtgCategoryOfArticle->addCssClass('disabled');
            $this->btnAddCategory->Enabled = false;
            $this->btnGoToArticle->Display = false;
            $this->txtCategory->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;

            $this->oldName = $objCategoryOfArticle->getName();

            $this->txtCategory->Text = $objCategoryOfArticle->getName();
            $this->txtCategory->focus();
            $this->lstStatus->SelectedValue = $objCategoryOfArticle->IsEnabled ?? null;

            $this->dtgCategoryOfArticle->addCssClass('disabled');
            $this->btnAddCategory->Enabled = false;
            $this->btnGoToArticle->Display = false;
            $this->txtCategory->Display = true;
            $this->lstStatus->Display = true;
            $this->btnSave->Display = true;
            $this->btnDelete->Display = true;
            $this->btnCancel->Display = true;
        }

        /**
         * Retrieves parameters for a specific row in the CategoryOfArticle data grid.
         *
         * This method extracts the primary key from the given row object and uses it
         * to set a data attribute in the returned parameters array. This is typically
         * used to provide additional metadata for the row, which can be utilized in
         * front-end interactions or data tracking.
         *
         * @param object $objRowObject The row object from which the primary key is extracted.
         * @param int $intRowIndex The index of the row in the data grid.
         *
         * @return array An associative array of parameters where 'data-value' is set
         *               to the primary key of the row object.
         */
        public function dtgCategoryOfArticle_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Configures and initializes paginators for the CategoryOfArticle data grid.
         *
         * This method sets up the paginators for navigating the data grid,
         * assigning labels for the navigation controls. It also sets the number of items per a page,
         * specifies the default sort column, and enables AJAX for seamless data grid interactions.
         * Additional filter actions are added to enhance the grid functionality.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgCategoryOfArticle->Paginator = new Bs\Paginator($this);
            $this->dtgCategoryOfArticle->Paginator->LabelForPrevious = t('Previous');
            $this->dtgCategoryOfArticle->Paginator->LabelForNext = t('Next');

            $this->dtgCategoryOfArticle->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgCategoryOfArticle->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgCategoryOfArticle->PaginatorAlternate->LabelForNext = t('Next');

            $this->dtgCategoryOfArticle->ItemsPerPage = 10;
            $this->dtgCategoryOfArticle->SortColumnIndex = 4;
            $this->dtgCategoryOfArticle->UseAjax = true;
            $this->addFilterActions();
        }

        /**
         * Configures and initializes the items per page selection control.
         *
         * This method sets up a Select2 widget for choosing the number of items
         * displayed per page, tailored for the assigned user. It configures visual
         * and functional properties of the widget, populates it with available
         * options, and binds a change event handler.
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
         * Retrieves a list of items per a page, filtered by the assigned user's object condition.
         *
         * @return ListItem[] An array of ListItem objects representing the items per page associated with the assigned user. Each ListItem will have its 'Selected' property set to true if it matches the user's current assignment.
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
         * Updates the items per page for the category of article data grid based on the selected value from the list of items per a page by the assigned user object and refreshes the data grid.
         *
         * @param ActionParams $params The parameters containing action-related data that may be used to determine how the change should be handled.
         * @return void This method does not return any value.
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgCategoryOfArticle->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgCategoryOfArticle->refresh();
        }

        /**
         * Initializes a search filter as a text input field with specific attributes for user interaction.
         *
         * @return void This method does not return a value. It configures a text box for search functionality, setting
         *     attributes such as placeholder text and autocomplete, and apply CSS classes for styling. Additionally,
         *     it invokes actions to handle filter events.
         * Caller
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

            $this->dtgCategoryOfArticle->refresh();
        }

        /**
         * Adds filter actions to the text filter input control. These actions facilitate dynamic filtering
         * based on user input and interaction with the filter's UI.
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
         * Refreshes the data grid displaying the category of articles when the filter is changed.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgCategoryOfArticle->refresh();
        }

        /**
         * Binds data to the dtgCategoryOfArticle data grid based on a specified condition.
         *
         * @return void This method does not return a value but updates the data grid with the relevant data according
         *     to the provided condition.
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgCategoryOfArticle->bindData($objCondition);
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
                    QQ::equal(QQN::CategoryOfArticle()->Id, $strSearchValue),
                    QQ::like(QQN::CategoryOfArticle()->Name, "%" . $strSearchValue . "%")

                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a set of buttons and other UI controls for managing categories and articles.
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

            $this->btnGoToArticle = new Bs\Button($this);
            $this->btnGoToArticle->Text = t('Go to this article');
            $this->btnGoToArticle->addWrapperCssClass('center-button');
            $this->btnGoToArticle->CssClass = 'btn btn-default';
            $this->btnGoToArticle->CausesValidation = false;
            $this->btnGoToArticle->addAction(new Click(), new AjaxControl($this, 'btnGoToArticle_Click'));
            $this->btnGoToArticle->setCssStyle('float', 'left');

            if (!empty($_SESSION['article'])) {
                $this->btnGoToArticle->Display = true;
            } else {
                $this->btnGoToArticle->Display = false;
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

        /**
         * Initializes and configures two Toastr notifications for different alert types.
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
         * Initializes and configures a set of modal dialogs used for different user interactions.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the article category?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addCloseButton(t("I'll cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'deleteItem_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'hide_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss' => 'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                [ 'class' => 'btn btn-orange']);
            $this->dlgModal3->addAction(new DialogButton(), new AjaxControl($this, 'hide_Click'));
            $this->dlgModal3->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'hide_Click'));

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t('Warning');
            $this->dlgModal4->HeaderClasses = 'btn-danger';
            $this->dlgModal4->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal4->addCloseButton(t("I'll cancel"));
            $this->dlgModal4->addAction(new DialogButton(), new AjaxControl($this, 'categoryItem_Click'));
            $this->dlgModal4->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'hide_Click'));

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Tip");
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This category already exists! Please choose another new name!</p>');
            $this->dlgModal5->HeaderClasses = 'btn-darkblue';
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal6->Title = t("Warning");
            $this->dlgModal6->HeaderClasses = 'btn-danger';
            $this->dlgModal6->addCloseButton(t("I understand"));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the "Add Category" button. This method prepares the UI for adding a new category
         * by updating display properties and resetting relevant fields.
         *
         * @return void
         * @throws RandomException
         */
        protected function btnAddCategory_Click(): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->btnGoToArticle->Display = false;
            $this->txtCategory->Display = true;
            $this->lstStatus->Display = true;
            $this->lstStatus->SelectedValue = 2;
            $this->btnSaveCategory->Display = true;
            $this->btnCancel->Display = true;
            $this->txtCategory->Text = '';
            $this->txtCategory->focus();
            $this->btnAddCategory->Enabled = false;
            $this->dtgCategoryOfArticle->addCssClass('disabled');
        }

        /**
         * Handles the save operation when the "Save Category" button is clicked.
         * Validates the input category name to ensure it is not already taken,
         * then creates a new category and updates the display accordingly.
         *
         * @param ActionParams $params The parameters associated with the button click event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnSaveCategory_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->txtCategory->Text) {
                if (!in_array(trim(strtolower($this->txtCategory->Text)), $this->objCategoryNames)) {

                    $objCategoryNews = new CategoryOfArticle();
                    $objCategoryNews->setName(trim($this->txtCategory->Text));
                    $objCategoryNews->setIsEnabled($this->lstStatus->SelectedValue);
                    $objCategoryNews->setPostDate(Q\QDateTime::now());
                    $objCategoryNews->save();

                    $this->dtgCategoryOfArticle->refresh();

                    if (!empty($_SESSION['article'])) {
                        $this->btnGoToArticle->Display = true;
                    }

                    unset($this->objCategoryNames);

                    $this->txtCategory->Display = false;
                    $this->lstStatus->Display = false;
                    $this->btnSaveCategory->Display = false;
                    $this->btnCancel->Display = false;
                    $this->btnAddCategory->Enabled = true;
                    $this->dtgCategoryOfArticle->removeCssClass('disabled');
                    $this->txtCategory->Text = '';
                    $this->dlgToastr1->notify();
                } else {
                    $this->txtCategory->Text = '';
                    $this->txtCategory->focus();
                    $this->dlgModal5->showDialogBox();
                }
            } else {
                $this->txtCategory->Text = '';
                $this->txtCategory->focus();
                $this->dlgToastr2->notify();
            }
        }

        /**
         * Handles the save button click event, processing the current article category and its related articles.
         *
         * @param ActionParams $params Parameters related to the button click action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objArticleArray = Article::loadAll();
            $objCategoryOfArticle = CategoryOfArticle::loadById($this->intId);

            foreach ($objArticleArray as $objArticle) {
                if ($objArticle->CategoryId == $this->intId) {
                    $this->objCompressTexts[] = $objArticle->Title;
                }
            }

            $this->objMenuTexts = implode(', ', $this->objCompressTexts);

            if ($this->txtCategory->Text) {
                if (in_array($this->intId, $this->objCategoryIds) && $this->lstStatus->SelectedValue == 2) {
                    $this->lstStatus->SelectedValue = 1;

                    $this->dlgModal3->showDialogBox();
                    $this->dlgModal3->Text = t('<p style="line-height: 25px;">The article category cannot be deactivated at this time!</p>
                                            <p style="line-height: 25px;">Articles related to the category: <span style="color: #ff0000;">
                                    ' . $this->objMenuTexts . '</span>.</p>
                                            <p style="line-height: 25px; margin-bottom: -3px;">To deactivate this article category,
                                            just must release article categories related to previously created article.</p>');

                    if (!empty($_SESSION['article'])) {
                        $this->btnGoToArticle->Display = false;
                    }

                } else if ($this->lstStatus->SelectedValue == $objCategoryOfArticle->getIsEnabled() && $this->txtCategory->Text !== $objCategoryOfArticle->getName() && in_array($this->intId, $this->objCategoryIds)) {
                    $this->dlgModal4->showDialogBox();
                    $this->dlgModal4->Text = t('<p style="line-height: 25px">Are you sure you want to rename the article category?</p>
                                    <p style="line-height: 25px;">Articles related to the category: <span style="color: #ff0000;">
                                    ' . $this->objMenuTexts . '</span>.</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Renaming this category will update the category for all articles associated with it! </p>');

                    if (!empty($_SESSION['article'])) {
                        $this->btnGoToArticle->Display = false;
                    }

                } else if ($this->txtCategory->Text == $objCategoryOfArticle->getName() && $this->lstStatus->SelectedValue !== $objCategoryOfArticle->getIsEnabled() ||
                    $this->txtCategory->Text !== $objCategoryOfArticle->getName() && !in_array(trim(strtolower($this->txtCategory->Text)), $this->objCategoryNames)) {

                    $objCategoryOfArticle->setName(trim($this->txtCategory->Text));
                    $objCategoryOfArticle->setIsEnabled($this->lstStatus->SelectedValue);
                    $objCategoryOfArticle->setPostUpdateDate(Q\QDateTime::now());
                    $objCategoryOfArticle->save();

                    $this->dtgCategoryOfArticle->refresh();
                    $this->btnAddCategory->Enabled = true;

                    if (!empty($_SESSION['article'])) {
                        $this->btnGoToArticle->Display = true;
                    }

                    $this->txtCategory->Display = false;
                    $this->lstStatus->Display = false;
                    $this->btnSave->Display = false;
                    $this->btnDelete->Display = false;
                    $this->btnCancel->Display = false;

                    $this->dtgCategoryOfArticle->removeCssClass('disabled');
                    $this->txtCategory->Text = $objCategoryOfArticle->getName();
                    $this->dlgToastr1->notify();

                } else if (CategoryOfArticle::titleExists(trim($this->txtCategory->Text))) {
                    $this->txtCategory->Text = $objCategoryOfArticle->getName();
                    $this->dlgModal5->showDialogBox();
                }

            } else {
                $this->txtCategory->Text = $objCategoryOfArticle->getName();
                $this->dlgToastr2->notify();
            }

            unset($this->objCompressTexts);
        }

        /**
         * Handles the click event for a category item, updating the category details and managing UI components.
         *
         * @param ActionParams $params Parameters associated with the action, including any event-specific data needed for processing.
         *
         * @return void No direct return value, but updates the UI state and persists changes to the category.
         * @throws Caller
         * @throws InvalidCast
         */
        public function categoryItem_Click(ActionParams $params): void
        {
            $objCategoryOfArticle = CategoryOfArticle::loadById($this->intId);
            $objCategoryOfArticle->setName(trim($this->txtCategory->Text));
            $objCategoryOfArticle->setIsEnabled($this->lstStatus->SelectedValue);
            $objCategoryOfArticle->setPostUpdateDate(Q\QDateTime::Now());
            $objCategoryOfArticle->save();

            if (!empty($_SESSION['article'])) {
                $this->btnGoToArticle->Display = true;
            }

            $this->btnAddCategory->Enabled = true;
            $this->txtCategory->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgCategoryOfArticle->removeCssClass('disabled');

            $this->dlgModal4->hideDialogBox();
            $this->dlgToastr1->notify();

            unset($this->objCompressTexts);
        }

        /**
         * Handles the click event for hiding certain UI elements and resetting specific controls within the
         * application interface.
         *
         * @param ActionParams $params The parameters associated with the action event triggering the click.
         *
         * @return void
         * @throws RandomException
         */
        public function hide_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['article'])) {
                $this->btnGoToArticle->Display = true;
            }

            $this->btnAddCategory->Enabled = true;
            $this->txtCategory->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgCategoryOfArticle->removeCssClass('disabled');

            $this->dlgModal1->hideDialogBox();
            $this->dlgModal3->hideDialogBox();
            $this->dlgModal4->hideDialogBox();
            unset($this->objCompressTexts);
        }

        /**
         * Handles the click event for the delete button. Checks if the selected article category can be deleted
         * by verifying its association with existing articles. If associated, displays a modal dialog informing
         * the user of the related articles and restricts deletion. Otherwise, shows a confirmation dialog.
         *
         * @param ActionParams $params The parameters associated with the action triggering this method.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objArticleArray = Article::loadAll();

            foreach ($objArticleArray as $objArticle) {
                if ($objArticle->CategoryId == $this->intId) {
                    $this->objCompressTexts[] = $objArticle->Title;
                }
            }

            $this->objMenuTexts = implode(', ', $this->objCompressTexts);

            if (Article::countByCategoryId($this->intId) > 0) {
                $this->dlgModal2->showDialogBox();
                $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The article category cannot be
                                    deleted at this time!</p>
                                    <p style="line-height: 15px;">Articles related to the category: <span style="color: #ff0000;">
                                    ' . $this->objMenuTexts . '</span>.</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this category, just
                                    must release categories related to previously created articles.</p>');

                if (!empty($_SESSION['article'])) {
                    $this->btnGoToArticle->Display = true;
                }

                unset($this->objCompressTexts);

                $this->btnAddCategory->Enabled = true;
                $this->txtCategory->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;
                $this->dtgCategoryOfArticle->removeCssClass('disabled');

            } else {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the click event for deleting an item from the category of articles.
         *
         * @param ActionParams $params The parameters associated with the action, including the action parameter that determines if the item should be deleted.
         *
         * @return void This method does not return a value but performs actions such as deleting a category if specified and updating the UI components accordingly.
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            $objCategoryOfArticle = CategoryOfArticle::loadById($this->intId);

            if ($params->ActionParameter == "pass") {
                $objCategoryOfArticle->delete();
            }

            $this->dtgCategoryOfArticle->refresh();
            $this->btnAddCategory->Enabled = true;
            $this->txtCategory->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;

            $this->dtgCategoryOfArticle->removeCssClass('disabled');
            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the cancel button click event, performing cleanup and UI updates.
         *
         * @param ActionParams $params The parameters provided by the action triggering the button click.
         *
         * @return void
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->txtCategory->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSaveCategory->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->btnAddCategory->Enabled = true;
            $this->dtgCategoryOfArticle->removeCssClass('disabled');
            $this->txtCategory->Text = '';

            if (!empty($_SESSION['article'])) {
                $this->btnGoToArticle->Display = true;
            }
        }

        /**
         * Handles the button click event to redirect the user to the appropriate edit menu page
         * based on the session data available.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void This method does not return a value. It performs a redirect operation.
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnGoToArticle_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['news_group'])) {
                Application::redirect('menu_edit.php?id=' . $_SESSION['news_group']);
                unset($_SESSION['news_group']);
            } else if (!empty($_SESSION['gallery_group'])) {
                Application::redirect('menu_edit.php?id=' . $_SESSION['gallery_group']);
                unset($_SESSION['gallery_group']);
            } else if (!empty($_SESSION['article'])) {
                Application::redirect('menu_edit.php?id=' . $_SESSION['article']);
                unset($_SESSION['article']);
            } else if (!empty($_SESSION['news']) || !empty($_SESSION['group'])) {
                Application::redirect('news_edit.php?id=' . $_SESSION['news']) . '&group=' . $_SESSION['group'];
                unset($_SESSION['news']);
                unset($_SESSION['group']);
            }
        }

        /**
         * Checks all articles and categories, storing associated category IDs and category names.
         *
         * @return void This method populates the objCategoryIds property with category IDs associated with articles
         * and the objCategoryNames property with the lowercase names of all categories.
         * @throws Caller
         */
        public function CheckCategories(): void
        {
            $objArticleArray = Article::loadAll();
            $objCategoryArray = CategoryOfArticle::loadAll();

            foreach ($objArticleArray as $objArticle) {
                if ($objArticle->CategoryId) {
                    $this->objCategoryIds[] = $objArticle->CategoryId;
                }
            }

            foreach ($objCategoryArray as $objCategory) {
                $this->objCategoryNames[] = strtolower($objCategory->Name);
            }
        }
    }