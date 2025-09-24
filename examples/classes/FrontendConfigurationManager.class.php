<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
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
     * Class FrontendConfigurationManager handles the creation and management of frontend configuration options.
     * It provides UI components such as modals, data grids, buttons, filters, and other controls
     * necessary for managing frontend settings by an administrator or developer.
     */
    class FrontendConfigurationManager extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        public Bs\Modal $dlgModal1;
        public Q\Plugin\Control\Alert $lblInfo;
        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public FrontendConfigurationTable $dtgFrontendOptions;
        public Bs\Button $btnUpdate;
        public Bs\Button $btnNew;
        public Bs\Button $btnBack;

        protected object $objUser;
        protected int $intLoggedUserId;

        protected string $strTemplate = 'FrontendConfigurationManager.tpl.php';

        /**
         * Constructor for initializing the object and setting up the necessary UI and backend configurations.
         * The constructor primarily sets up data bindings, controls, and modals for managing front-end options.
         * It also initializes the logged-in user context.
         *
         * @param mixed $objParentObject Parent object typically responsible for managing this control.
         * @param string|null $strControlId Optional control ID to uniquely identify this control.
         *
         * @return void
         *
         * @throws DateMalformedStringException
         * @throws Caller Thrown when an error occurs in the parent constructor call.
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

            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Display = true;
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('<p>Note! This desktop is intended for webmasters and developers managing front-end template classes. 
                                        To create a new front-end template: It is strongly recommended to choose a new distinctive class name 
                                        for your template, so it is easy to identify and switch templates in the template manager. For example: 
                                        Home (custom) or any unique name you prefer. The class name and front-end template name must not duplicate any existing names.</p>
                                        <p>First, create a new class for the target folder on the front end. After that, you can add new entries (rows). 
                                        In the template manager, you can change the front-end class as needed.</p>
                                        <p>The default template classes (ID: 1–18) are standard and cannot be renamed, modified, or deleted.</p>
                                        <p>For new template classes, you are free to name, edit, or delete their associated content types through the Content Types Management interface.</p> 
                                        <p><strong>Alternatively:</strong> If the functionality of an existing class suits your requirements, 
                                        you can reuse an existing class and simply assign a new custom template file 
                                        (e.g. CustomNewsDetailController.tpl.php).</p>
                                        <p>For example: News detail (custom) → CustomNewsDetailController.tpl.php, etc.</p>');

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgFrontendOptions_Create();
            $this->dtgFrontendOptions->setDataBinder('BindData', $this);
            $this->createModals();
            $this->createButtons();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and configures the FrontendConfigurationTable for frontend options.
         * Initializes columns, pagination, editability, and other table parameters.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgFrontendOptions_Create(): void
        {
            $this->dtgFrontendOptions = new FrontendConfigurationTable($this);
            $this->dtgFrontendOptions_CreateColumns();
            $this->createPaginators();
            $this->dtgFrontendOptions_MakeEditable();
            $this->dtgFrontendOptions->RowParamsCallback = [$this, "dtgFrontendOptions_GetRowParams"];
            $this->dtgFrontendOptions->SortColumnIndex = 0;
            $this->dtgFrontendOptions->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
        }

        /**
         * Creates columns for the frontend options data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgFrontendOptions_CreateColumns(): void
        {
            $this->dtgFrontendOptions->createColumns();
        }

        /**
         * Configures the data grid for frontend options to be editable.
         *
         * This method associates a cell click event with an AJAX action, enabling interactive row selection.
         * It also applies the necessary CSS classes to style the data grid with hover effects and responsiveness.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgFrontendOptions_MakeEditable(): void
        {
            $this->dtgFrontendOptions->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgFrontendOptions_Click'));
            $this->dtgFrontendOptions->addCssClass('clickable-rows');
            $this->dtgFrontendOptions->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for frontend options and redirects to the edit page of the selected option.
         *
         * @param ActionParams $params The action parameters containing the ID of the selected option.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgFrontendOptions_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intOptionId = intval($params->ActionParameter);
            Application::redirect('frontend_option_edit.php' . '?id=' . $intOptionId);
        }

        /**
         * Retrieves the parameters for a row in the frontend options table.
         *
         * @param object $objRowObject The row object containing data for which parameters are being set.
         * @param int $intRowIndex The index of the current row.
         *
         * @return array An associative array of parameters for the row, including the data-value key set to the row's primary key.
         */
        public function dtgFrontendOptions_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures the paginators for the frontend options data grid.
         *
         * This method sets up two paginators for navigating through the data grid pages,
         * with labels for previous and next page navigation. It also configures the data grid
         * to display a specific number of items per a page, sets the default sort column index,
         * and enables AJAX for data fetching.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgFrontendOptions->Paginator = new Bs\Paginator($this);
            $this->dtgFrontendOptions->Paginator->LabelForPrevious = t('Previous');
            $this->dtgFrontendOptions->Paginator->LabelForNext = t('Next');

            $this->dtgFrontendOptions->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgFrontendOptions->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgFrontendOptions->PaginatorAlternate->LabelForNext = t('Next');

            $this->dtgFrontendOptions->ItemsPerPage = 10;
            $this->dtgFrontendOptions->SortColumnIndex = 4;
            $this->dtgFrontendOptions->UseAjax = true;
            $this->addFilterActions();
        }

        /**
         * Initializes and configures the Select2 control for managing items per page selection.
         *
         * This method sets up a Select2 control with specific configurations including
         * search options, theme, width, and selection mode. It also populates the control
         * with items and sets an action to handle changes in the selection.
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
         * Retrieves a list of items per a page that are assigned to a specific user object.
         * The method constructs a list of ListItem objects based on the query result.
         *
         * @return ListItem[] An array of ListItem objects where each item represents a page assigned to the user.
         * If a page is currently selected for the user, its corresponding ListItem is marked as selected.
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
         * Handles the change event for the ItemsPerPageByAssignedUserObject list.
         *
         * @param ActionParams $params The parameters associated with the action event.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgFrontendOptions->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgFrontendOptions->refresh();
        }

        /**
         * Initializes and configures a searchable text box filter for the application.
         * Sets up various attributes and styles specific to the search functionality.
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

            $this->dtgFrontendOptions->refresh();
        }

        /**
         * Adds filter actions to the txtFilter control. These actions include
         * an input event with a debounced period and a sequence of actions
         * triggered by the Enter key event. The input event triggers the
         * 'filterChanged' method via an Ajax call after 300 milliseconds,
         * while the Enter key event triggers the 'FilterChanged' method
         * followed by a termination action.
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
         * Handles actions or logic that should occur when a filter change event is detected.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgFrontendOptions->refresh();
        }

        /**
         * Binds data to the frontend options using the specified condition.
         *
         * Retrieves the condition for data binding and delegates the binding
         * process to the frontend options component.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgFrontendOptions->bindData($objCondition);
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
                    QQ::like(QQN::FrontendOptions()->FrontendTemplateName, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::FrontendOptions()->ClassName, "%" . $strSearchValue . "%"),
                    QQ::equal(QQN::FrontendOptions()->Status, $strSearchValue)
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the update and new template buttons.
         * This method creates two buttons with specific styles, text, and actions,
         * which are connected to their respective click event handlers.
         *
         * @return void This method does not return a value.
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
            $this->btnNew->Text = t(' Create a new template class');
            $this->btnNew->Glyph = 'fa fa-plus';
            $this->btnNew->CssClass = 'btn btn-orange';
            $this->btnNew->addWrapperCssClass('center-button');
            $this->btnNew->CausesValidation = false;
            $this->btnNew->addAction(new Click(), new AjaxControl($this, 'btnNew_Click'));
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
         * Handles the click event for the update button, triggering a refresh of the data grid.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws RandomException
         */
        public function btnUpdate_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->dtgFrontendOptions->refresh();
        }

        /**
         * Redirects the user to the 'frontend_option_edit.php' page.
         *
         * @return void This method does not return any value.
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

            Application::redirect('frontend_option_edit.php');
        }
    }