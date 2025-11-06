<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use Random\RandomException;
    use QCubed\Event\Change;
    use QCubed\Event\Click;
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
     * Class FrontendLinksOverview
     *
     * Provides an overview and management interface for frontend links, including options
     * for filtering, pagination, and customization of displayed data.
     *
     * Extends the base Panel class to include advanced functionality and user-specific configurations
     * such as selectable items per a page, filtering capabilities, and editable data tables.
     */
    class FrontendLinksOverview extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        public Bs\Modal $dlgModal1;
        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public FrontendLinksOverviewTable $dtgFrontendLinks;
        public Bs\Button $btnUpdate;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'FrontendLinksOverview.tpl.php';

        /**
         * Constructor method for initializing an instance, setting up the user session, creating UI elements, and
         * binding data.
         *
         * @param mixed $objParentObject The parent object or control that this instance is a part of.
         * @param string|null $strControlId Optional control ID to uniquely identify this instance.
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

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgFrontendLinks_Create();
            $this->dtgFrontendLinks->setDataBinder('BindData', $this);
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
         * Initializes and configures the FrontendLinksOverviewTable component.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgFrontendLinks_Create(): void
        {
            $this->dtgFrontendLinks = new FrontendLinksOverviewTable($this);
            $this->dtgFrontendLinks_CreateColumns();
            $this->createPaginators();
            $this->dtgFrontendLinks_MakeEditable();
            $this->dtgFrontendLinks->SortColumnIndex = 0;
            $this->dtgFrontendLinks->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgFrontendLinks->UseAjax = true;
        }

        /**
         * Creates columns for the FrontendLinks data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgFrontendLinks_CreateColumns(): void
        {
            $this->dtgFrontendLinks->createColumns();
        }

        /**
         * Makes the frontend links data grid editable by setting its CSS class to apply
         * specific styling, enhancing its appearance and interaction.
         *
         * @return void
         */
        protected function dtgFrontendLinks_MakeEditable(): void
        {
            $this->dtgFrontendLinks->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Initializes and configures the paginators for the frontend links data grid.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgFrontendLinks->Paginator = new Bs\Paginator($this);
            $this->dtgFrontendLinks->Paginator->LabelForPrevious = t('Previous');
            $this->dtgFrontendLinks->Paginator->LabelForNext = t('Next');

            $this->dtgFrontendLinks->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgFrontendLinks->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgFrontendLinks->PaginatorAlternate->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        /**
         * Initializes and configures a Select2 control for selecting items per a page based on the user's assigned
         * settings.
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
         * Retrieves a list of ListItem objects for the ItemsPerPageByAssignedUserObject based on a specific condition.
         *
         * @return ListItem[] An array of ListItem objects, each representing an item that matches the given condition. If the item is the same as the one assigned to the current user, it is marked as selected.
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
         * Handles the change event for the dropdown list of items per a page, updating the number of items
         * displayed in the frontend links datagrid based on the user's selection.
         *
         * @param ActionParams $params The parameters associated with the change event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgFrontendLinks->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgFrontendLinks->refresh();
        }

        /**
         * Initializes the filter text box component used for searching.
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
         * @throws Caller
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->dtgFrontendLinks->refresh();
            $this->userOptions();
        }

        /**
         * Adds input and key event actions to the filter text box.
         *
         * Configures the filter text box to trigger an AJAX call when the user provides input or presses the Enter key.
         * Specifically, when the input event is detected with a delay of 300 milliseconds, the 'filterChanged' method
         * is called via an AJAX action. Additionally, when the Enter key is pressed, an array of actions is executed:
         * an AJAX call to the 'FilterChanged' method is made, followed by a termination action.
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
         * Triggers a refresh on the frontend links data grid when the filter is changed.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgFrontendLinks->refresh();
            $this->userOptions();

        }

        /**
         * Binds data to the `dtgFrontendLinks` component using a specified condition.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgFrontendLinks->bindData($objCondition);
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

            if ($strSearchValue !== null) {
                $strSearchValue = trim($strSearchValue);
            }

            if ($strSearchValue === '') {
                return QQ::all();
            } else {
                return QQ::orCondition(
                    QQ::equal(QQN::FrontendLinks()->Id, $strSearchValue),
                    QQ::equal(QQN::FrontendLinks()->LinkedId, $strSearchValue),
                    QQ::equal(QQN::FrontendLinks()->GroupedId, $strSearchValue),
                    QQ::equal(QQN::FrontendLinks()->ContentTypesManagamentId, $strSearchValue),
                    QQ::like(QQN::FrontendLinks()->FrontendClassName, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::FrontendLinks()->FrontendTemplatePath, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::FrontendLinks()->Title, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::FrontendLinks()->FrontendTitleSlug, "%" . $strSearchValue . "%"),
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the update button for the interface. The button is styled with
         * specific CSS classes, does not trigger form validation, and has an assigned click event
         * that performs an Ajax action.
         *
         * @return void
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
         * Handles the click event for the update button.
         *
         * @param ActionParams $params The parameters associated with the click action event.
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

            $this->dtgFrontendLinks->refresh();
        }
    }