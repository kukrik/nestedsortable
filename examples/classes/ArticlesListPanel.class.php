<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\CellClick;
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
     * Class ArticlesListPanel
     *
     * A panel that represents a list of articles. It includes various controls and features such as
     * filtering, pagination, AJAX-enabled interactions, and edit capabilities for managing board data.
     */
    class ArticlesListPanel extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        public Bs\Modal $dlgModal1;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public ArticleTable $dtgArticles;
        public Bs\Button $btnBack;
        public Bs\Button $btnRefresh;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'ArticlesListPanel.tpl.php';

        /**
         * Constructor method for initializing the object with a parent object and an optional control ID.
         * Performs a necessary setup such as retrieving the logged-in user information, loading the user object,
         * and initializing various UI components like buttons, modals, filters, and data grids.
         *
         * @param mixed $objParentObject The parent object to which this object belongs.
         * @param string|null $strControlId An optional control ID for identifying this object.
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

            $this->createButtons();
            $this->createModals();
            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgArticles_Create();
            $this->dtgArticles->setDataBinder('BindData', $this);
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
         * Create and configure the 'Back' button with associated actions and styles
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->addWrapperCssClass('center-button');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new AjaxControl($this,'btnBack_Click'));

            $this->btnRefresh = new Bs\Button($this);
            $this->btnRefresh->Tip = true;
            $this->btnRefresh->ToolTip = t('Refresh');
            $this->btnRefresh->Glyph = 'fa fa-refresh';
            $this->btnRefresh->CssClass = 'btn btn-darkblue';
            $this->btnRefresh->CausesValidation = false;
            $this->btnRefresh->addAction(new Click(), new AjaxControl($this,'btnRefresh_Click'));
        }

        /**
         * Creates modals to display messages or alerts within the application.
         * This method configures a modal dialog for CSRF protection warnings by initializing
         * the modal, setting its content, title, and styles, and adding a close button with a label.
         *
         * @return void
         * @throws Caller
         */
        public function createModals(): void
        {
            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal1->Title = t("Warning");
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addCloseButton(t("I understand"));
        }

        /**
         * Handles the 'Back' button click event by redirecting to the menu manager page.
         *
         * @param ActionParams $params The parameters for the action event, typically including context-specific
         *     information about the event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnBack_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("history.go(-1);");
        }

        /**
         * Handles the button click event for refreshing the data grid.
         * This method refreshes the data displayed in the board data grid based on
         * the current parameters or state.
         *
         * @param ActionParams $params Contains the parameters or context associated with the button click event.
         *
         * @return void
         */
        public function btnRefresh_Click(ActionParams $params): void
        {
            $this->dtgArticles->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Create and configure the boards datagrid
         *
         * @return void
         * @throws Caller
         */
        protected function dtgArticles_Create(): void
        {
            $this->dtgArticles = new ArticleTable($this);
            $this->dtgArticles_CreateColumns();
            $this->createPaginators();
            $this->dtgArticles_MakeEditable();
            $this->dtgArticles->RowParamsCallback = [$this, "dtgArticles_GetRowParams"];
            $this->dtgArticles->SortColumnIndex = 3;
            //$this->dtgArticles->SortDirection = -1;
            $this->dtgArticles->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgArticles->UseAjax = true;
        }

        /**
         * Create columns for the datagrid
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgArticles_CreateColumns(): void
        {
            $this->dtgArticles->createColumns();
        }

        /**
         * Configures the dtgArticles datatable to be interactive and editable by adding
         * appropriate actions and CSS classes. This method enables cell click actions
         * that trigger an AJAX control event and applies specified CSS classes to the table.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgArticles_MakeEditable(): void
        {
            $this->dtgArticles->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgArticlesRow_Click'));
            $this->dtgArticles->addCssClass('clickable-rows');
            $this->dtgArticles->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles click events on rows of the dtgArticles datatable. Retrieves the board
         * settings based on the action parameter's identifier, then redirects the user
         * to the board edit page with the board's ID and group information as query parameters.
         *
         * @param ActionParams $params The parameters associated with the action, containing
         *                             the identifier of the clicked row's board.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgArticlesRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $intId = intval($params->ActionParameter);
            $objArticle = Article::loadById($intId);
            $intGroup = $objArticle->getMenuContentId();

            Application::redirect('menu_edit.php' . '?id=' . $intGroup);
        }

        /**
         * Get row parameters for the row tag
         *
         * @param mixed $objRowObject   A database object
         * @param int $intRowIndex      The row index
         *
         * @return array
         */
        public function dtgArticles_GetRowParams(mixed $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Sets up pagination for the dtgArticles datatable by initializing primary and
         * alternate paginators with labels for navigation controls and specifying
         * the number of items displayed per page. Additionally, invokes actions
         * to handle filtering of data within the table.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgArticles->Paginator = new Bs\Paginator($this);
            $this->dtgArticles->Paginator->LabelForPrevious = t('Previous');
            $this->dtgArticles->Paginator->LabelForNext = t('Next');

            $this->dtgArticles->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgArticles->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgArticles->PaginatorAlternate->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        /**
         * Initializes and configures a Select2 control for selecting the number of items
         * per a page by an assigned user. This method sets various properties such as the theme,
         * width, and selection mode. It also populates the control with item options and
         * attaches an AJAX change event to handle user interactions.
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
         * Retrieves a list of ListItems representing items per a page associated with an assigned user object.
         * This method queries the database for items per page objects based on a specified condition and
         * returns them as ListItem objects. The ListItem will be marked as selected if it matches the
         * currently assigned user object's item.
         *
         * @return ListItem[] An array of ListItems containing items per a page associated with an assigned user object.
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
         * Updates the number of items displayed per page for a data grid based on the selection
         * from a list associated with an assigned user object. This method adjusts the items per
         * page of the data grid and refreshes it to reflect the updated pagination settings.
         *
         * @param ActionParams $params The action parameters containing details of the change event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgArticles->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgArticles->refresh();
        }

        /**
         * Creates a filter control for user input. Initializes a text box with specific
         * properties and styling to serve as a search input field. This text box is designed
         * to provide a seamless user experience for entering search queries.
         *
         * @return void This method does not return any value.
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
         * @throws Caller
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->dtgArticles->refresh();
            $this->userOptions();
        }

        /**
         * Adds filter actions to the txtFilter control. This method sets up event-driven
         * interactions for the filter functionality. It registers an input event that triggers
         * an AJAX action after a delay, as well as an enter key event that initiates both an
         * AJAX action and an action termination.
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
         * Handles the event when a filter is changed, triggering the refresh of the board data grid.
         * This method updates the displayed data in the data grid to reflect the current filter criteria.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgArticles->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the data table by applying a specific condition.
         * This method retrieves a condition, typically used for filtering or querying purposes,
         * and applies it to bind data to a data table component.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgArticles->bindData($objCondition);
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
                    QQ::like(QQN::Article()->Title, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::Article()->Category, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::Article()->Author, "%" . $strSearchValue . "%")
                );
            }
        }
    }