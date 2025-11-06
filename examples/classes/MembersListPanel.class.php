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
     *  Class MembersListPanel
     *
     * MembersListPanel class handles the display and management of the member list, including
     * filtering, pagination, and interaction with individual members through various actions.
     */
    class MembersListPanel extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        public Bs\Modal $dlgModal1;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public MembersTable $dtgMembers;
        public Bs\Button $btnBack;
        public Bs\Button $btnRefresh;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'MembersListPanel.tpl.php';

        /**
         * Constructs a new instance of the class and initializes the required components and data.
         *
         * @param mixed $objParentObject The parent object that this control belongs to.
         * @param string|null $strControlId An optional control ID to uniquely identify this control.
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
            $this->dtgMembers_Create();
            $this->dtgMembers->setDataBinder('BindData', $this);
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
         * Creates and configures the "Back" button with specified text, CSS classes, and actions.
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
         * Creates modal dialogs to handle specific user-related actions or warnings.
         *
         * This method initializes and configures modal dialogs used for displaying critical
         * messages or warnings. In this case, it creates a modal to notify the user about
         * an invalid CSRF token, including a warning title, styled header, explanatory text,
         * and a close button.
         *
         * @return void This method does not return any value.
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
         * Handles the click event for the "Back" button, redirecting the user to the menu manager page.
         *
         * @param ActionParams $params The parameters associated with the action event.
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
         * This method refreshes the data displayed in the member data grid based on
         * the current parameters or state.
         *
         * @param ActionParams $params Contains the parameters or context associated with the button click event.
         *
         * @return void
         */
        public function btnRefresh_Click(ActionParams $params): void
        {
            $this->dtgMembers->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes the member data grid by creating an instance of MembersTable
         * and configuring its properties and behaviors. This includes setting up
         * columns, pagination, editability, row parameters callback, sorting, and
         * AJAX usage.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgMembers_Create(): void
        {
            $this->dtgMembers = new MembersTable($this);
            $this->dtgMembers_CreateColumns();
            $this->createPaginators();
            $this->dtgMembers_MakeEditable();
            $this->dtgMembers->RowParamsCallback = [$this, "dtgMembers_GetRowParams"];
            $this->dtgMembers->SortColumnIndex = 5;
            //$this->dtgMembers->SortDirection = -1;
            $this->dtgMembers->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgMembers->UseAjax = true;
        }

        /**
         * Creates columns for the member data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgMembers_CreateColumns(): void
        {
            $this->dtgMembers->createColumns();
        }

        /**
         * Configures the dtgMembers data grid to be editable by adding a cell click event and styling.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgMembers_MakeEditable(): void
        {
            $this->dtgMembers->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgMembersRow_Click'));
            $this->dtgMembers->addCssClass('clickable-rows');
            $this->dtgMembers->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for a row in the member data grid.
         *
         * @param ActionParams $params The parameters associated with the click action,
         *                             including the action parameter used to identify the specific member.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgMembersRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $intId = intval($params->ActionParameter);
            $objMember = MembersSettings::loadById($intId);
            $intGroup = $objMember->getMenuContentId();

            Application::redirect('members_edit.php' . '?id=' . $intId . '&group=' . $intGroup);
        }

        /**
         * Generates row parameters for a table row representing a member.
         *
         * @param object $objRowObject The object representing the row data, which should have a primary key.
         * @param int $intRowIndex The index of the row in the table.
         *
         * @return array The array of parameters including the 'data-value' attribute assigned to the primary key of the row object.
         */
        public function dtgMembers_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures the paginators for the member data grid.
         *
         * This method sets up two paginators (primary and alternate) for handling
         * pagination of the data grid displaying members. Each paginator is customized
         * with labels for the previous and next controls. The number of items displayed
         * per page is set to a default value. Additionally, this method invokes the
         * addition of filter actions to the data grid.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgMembers->Paginator = new Bs\Paginator($this);
            $this->dtgMembers->Paginator->LabelForPrevious = t('Previous');
            $this->dtgMembers->Paginator->LabelForNext = t('Next');

            $this->dtgMembers->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgMembers->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgMembers->PaginatorAlternate->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        /**
         * Initializes and configures the list of items per a page for a user interface element.
         * The method sets up a Select2 list control with specified properties and events.
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
         * @return ListItem[] An array of ListItem objects, with a selected state based on user assignment.
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
         * Updates the datagrid's items per page setting based on the selected option
         * and refreshes the datagrid to reflect this change.
         *
         * @param ActionParams $params The parameters associated with the change action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgMembers->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgMembers->refresh();
        }

        /**
         * Initializes the filter text box used for search functionality.
         * The text box is configured with a placeholder, search mode, and additional attributes
         * and CSS classes to enhance the user experience. Also, sets up filter actions necessary for interaction.
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

            $this->dtgMembers->refresh();
            $this->userOptions();
        }

        /**
         * Adds filter actions to the filter text box. This includes setting up an input event
         * handler that triggers an Ajax control response, and another event handler for the
         * Enter key which triggers the same response and then terminates further event propagation.
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
         * Refreshes the member data grid when a filter change is detected.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgMembers->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the data grid based on the current condition.
         *
         * This method retrieves the current condition using the getCondition method
         * and then binds the data to the data grid, dtgMembers, using the retrieved condition.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgMembers->bindData($objCondition);
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
                    QQ::like(QQN::MembersSettings()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::MembersSettings()->Title, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::MembersSettings()->Author, "%" . $strSearchValue . "%")
                );
            }
        }
    }