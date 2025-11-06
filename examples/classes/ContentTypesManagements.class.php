<?php

    use QCubed as Q;
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
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\All;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;

    /**
     * Class ContentTypesManagements
     *
     * This class represents a panel for managing content types. It provides functionality
     * to display, filter, and edit content type information through a data grid and related controls.
     *
     * It includes features such as pagination, data binding, interactivity through Ajax controls,
     * user-specific preferences, and modal dialog boxes for enhanced functionality. The template
     * for rendering the panel is specified with "ContentTypesManagements.tpl.php".
     */
    class ContentTypesManagements extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        public Bs\Modal $dlgModal1;

        public Q\Plugin\Control\Alert $lblInfo;
        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public ContentTypesManagementTable $dtgContentTypesManagements;
        public Bs\Button $btnUpdate;
        public Bs\Button $btnNew;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'ContentTypesManagements.tpl.php';

        /**
         * Constructor for initializing the control with necessary properties and components.
         *
         * @param mixed $objParentObject The parent object or control that will contain this control.
         * @param string|null $strControlId Optional control ID for uniquely identifying this control instance.
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

            // $this->intLoggedUserId= $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Display = true;
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('<p>Note! This desktop is created for a webmaster or for a developer. This desktop is 
                                        necessary for adding a new class, such as the Blog class. To do this, you first need 
                                        to create a "blog" table in the database. Then, add a new row in the "content_type" 
                                        table and let the code generator generate the ORM model objects. After that, write 
                                        the custom Blog class and connect it to the template manager.</p>
                                        <p>The classes with content types (ID: 1–18) provided by default are standard and 
                                        cannot be renamed, changed, or deleted. When you add a new class, you are free to name, 
                                        edit, or delete its content type.</p>');

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgContentTypesManagements_Create();
            $this->dtgContentTypesManagements->setDataBinder('BindData', $this);
            $this->createModals();
            $this->createButtons();
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
         * Initializes and configures the ContentTypesManagementTable object.
         *
         * This method sets up the data grid for managing content types by creating
         * its columns, enabling pagination, and making it editable. It also sets
         * up row parameters and sorting configuration. The number of items per
         * a page is determined by the user's preference.
         *
         * @return void
         *
         * @throws Caller
         */
        protected function dtgContentTypesManagements_Create(): void
        {
            $this->dtgContentTypesManagements = new ContentTypesManagementTable($this);
            $this->dtgContentTypesManagements_CreateColumns();
            $this->createPaginators();
            $this->dtgContentTypesManagements_MakeEditable();
            $this->dtgContentTypesManagements->RowParamsCallback = [$this, "dtgContentTypesManagements_GetRowParams"];
            $this->dtgContentTypesManagements->SortColumnIndex = 0;
            $this->dtgContentTypesManagements->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgContentTypesManagements->UseAjax = true;
        }

        /**
         * Creates columns for dtgContentTypesManagements.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgContentTypesManagements_CreateColumns(): void
        {
            $this->dtgContentTypesManagements->createColumns();
        }

        /**
         * Configures the ContentTypesManagement table to be editable by adding interactivity features.
         *
         * @return void
         *
         * @throws Caller
         */
        protected function dtgContentTypesManagements_MakeEditable(): void
        {
            $this->dtgContentTypesManagements->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgContentTypesManagements_Click'));
            $this->dtgContentTypesManagements->addCssClass('clickable-rows');
            $this->dtgContentTypesManagements->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for content type managements.
         *
         * @param ActionParams $params The parameters associated with the click action, including the action parameter which indicates the ID.
         *
         * @return void Redirects to the content types management edit page for the specified ID.
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgContentTypesManagements_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $intId = intval($params->ActionParameter);
            Application::redirect('content_types_management_edit.php' . '?id=' . $intId);
        }

        /**
         * Retrieves the parameters for a specific row in the Content Types Management data grid.
         *
         * @param object $objRowObject The object representing the current row from which parameters are extracted.
         * @param int $intRowIndex The index of the current row within the data grid.
         *
         * @return array An associative array of parameters where 'data-value' is set to the primary key of the object.
         */
        public function dtgContentTypesManagements_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures paginators for content type management.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgContentTypesManagements->Paginator = new Bs\Paginator($this);
            $this->dtgContentTypesManagements->Paginator->LabelForPrevious = t('Previous');
            $this->dtgContentTypesManagements->Paginator->LabelForNext = t('Next');

            $this->dtgContentTypesManagements->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgContentTypesManagements->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgContentTypesManagements->PaginatorAlternate->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        /**
         * Initializes and configures the items-per-page selector for filtering by the assigned user.
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
         * Retrieves a list of ListItem objects representing items per a page for the assigned user.
         *
         * @return ListItem[] An array of ListItem objects containing the items per-page options.
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
         * Updates the items per page setting for the content types management based on user selection
         * and refreshes the data grid to reflect the changes.
         *
         * @param ActionParams $params Parameters related to the user action triggering this change.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgContentTypesManagements->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgContentTypesManagements->refresh();
        }

        /**
         * Initializes and configures a search filter textbox component within the UI.
         *
         * @return void This method does not return any value.
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
         * @throws Caller
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->dtgContentTypesManagements->refresh();
            $this->userOptions();
        }

        /**
         * Adds input and enter key actions to the filter control.
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
         * Triggers the refresh of the content types management data grid when a filter change occurs.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgContentTypesManagements->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the content types management data grid based on a specified condition.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgContentTypesManagements->bindData($objCondition);
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
                    QQ::equal(QQN::ContentTypesManagement()->Id, $strSearchValue),
                    QQ::like(QQN::ContentTypesManagement()->ContentName, "%" . $strSearchValue . "%"),
                );
            }
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
         * Creates and initializes buttons for an update and new actions.
         *
         * @return void No return value, the buttons are created as part of the class state.
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnUpdate = new Bs\Button($this);
            $this->btnUpdate->Text = t('Update table');
            $this->btnUpdate->CssClass = 'btn btn-orange';
            $this->btnUpdate->addWrapperCssClass('center-button');
            $this->btnUpdate->CausesValidation = false;
            $this->btnUpdate->addAction(new Click(), new AjaxControl($this,'btnUpdate_Click'));

            $this->btnNew = new Bs\Button($this);
            $this->btnNew->Text = t(' New');
            $this->btnNew->Glyph = 'fa fa-plus';
            $this->btnNew->CssClass = 'btn btn-orange';
            $this->btnNew->addWrapperCssClass('center-button');
            $this->btnNew->CausesValidation = false;
            $this->btnNew->addAction(new Click(), new AjaxControl($this, 'btnNew_Click'));
        }

        /**
         * Handles the click event for the Update button, triggering a refresh of the content types management data
         * grid.
         *
         * @param ActionParams $params The action parameters associated with the button click event.
         *
         * @return void
         * @throws RandomException
         * @throws Caller
         */
        public function btnUpdate_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->dtgContentTypesManagements->refresh();
        }

        /**
         * Handles the click event of the 'New' button, redirecting the user to the content types management edit page.
         *
         * @param ActionParams $params
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnNew_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            Application::redirect('content_types_management_edit.php');
        }
    }