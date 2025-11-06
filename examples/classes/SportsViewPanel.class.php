<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use QCubed\Query\Condition\AndCondition;
    use Random\RandomException;
    use QCubed\Event\CellClick;
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
     * This class represents a panel in the UI for managing and viewing sports areas. It provides
     * functionalities for displaying a data grid of sports areas, including filtering, pagination,
     * and interactive controls. It also includes modals, buttons, and input elements for user interactions.
     *
     * SportsViewPanel offers the ability to dynamically bind data to the grid, enable row interactivity,
     * and redirect users based on various actions, such as selecting rows or using paginators.
     */
    class SportsViewPanel extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        public Bs\Modal $dlgModal1;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public SportsViewTable $dtgSportsAreas;

        public Q\Plugin\Select2 $lstYears;
        public Q\Plugin\Select2 $lstGroups;
        public Q\Plugin\Select2 $lstContentTypes;

        public Q\Plugin\Control\Alert$lblInfo;
        public Bs\Button $btnBack;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'SportsViewPanel.tpl.php';

        /**
         * Constructor method for initializing the class with a parent object and optional control ID.
         * Performs setup for user-specific data and initializes relevant components like filters, buttons, modals, and
         * data grids. Assumes a session or similar mechanism to retrieve the logged-in user's ID, assigning a default
         * for demonstration purposes.
         *
         * @param mixed $objParentObject The parent object to which this control belongs.
         * @param string|null $strControlId An optional control ID identifying this control uniquely.
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
            $this->dtgSportsAreas_Create();
            $this->createButtons();
            $this->createModals();
            $this->dtgSportsAreas->setDataBinder('bindData', $this);
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
         * Initializes and configures the SportsViewTable for displaying sports areas.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgSportsAreas_Create(): void
        {
            $this->dtgSportsAreas = new SportsViewTable($this);
            $this->dtgSportsAreas_CreateColumns();
            $this->createPaginators();
            $this->dtgSportsAreas_MakeEditable();
            $this->dtgSportsAreas->RowParamsCallback = [$this, "dtgSportsAreas_GetRowParams"];
            $this->dtgSportsAreas->SortColumnIndex = 5;
            $this->dtgSportsAreas->SortDirection = -1;
            $this->dtgSportsAreas->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgSportsAreas->UseAjax = true;
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
         * Makes the sports areas data grid editable by adding interactive features.
         *
         * This method enables a clickable cell action on the data grid, which triggers
         * an Ajax control event when a cell is clicked. It also applies specific CSS
         * classes to enhance the visual style and interactivity of the rows within the
         * sports areas data grid.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgSportsAreas_MakeEditable(): void
        {
            $this->dtgSportsAreas->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgSportsAreas_Click'));
            $this->dtgSportsAreas->addCssClass('clickable-rows');
            $this->dtgSportsAreas->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for the sports areas data grid.
         * This method redirects the user to the sports calendar edit page,
         * passing the sports calendar group ID and menu content group ID as parameters.
         *
         * @param ActionParams $params The parameters indicating which sports area were clicked.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgSportsAreas_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $intId = intval($params->ActionParameter);
            $objSportsAreas = SportsTables::load($intId);

            Application::redirect('sports_calendar_edit.php?id=' . $objSportsAreas->getSportsCalendarGroupId() . '&group=' . $objSportsAreas->getMenuContentGroupId());
        }

        /**
         * Retrieves the parameters for a specific row in the sports areas data grid.
         *
         * @param object $objRowObject The object representing the row for which parameters are being retrieved.
         * @param int $intRowIndex The index of the current row.
         *
         * @return array An associative array containing the parameters for the row, including the data-value attribute
         *     with the primary key of the object.
         */
        public function dtgSportsAreas_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures paginators for the sports areas data grid. Sets up the primary paginator
         * with labels for navigation and specifies the number of items per a page. It enables AJAX usage for the
         * data grid and applies additional filter actions through a separate method call.
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

            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the Select2 control for items per page selection.
         * Sets the theme, width, selection mode, and default-selected value based on
         * the user's assigned items per a page. It also populates the control with
         * available items and assigns a change event action.
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
         * Retrieves a list of list items representing items per a page assigned to users.
         * This function fetches and iterates over items per a page based on the specified conditions,
         * creating a ListItem for each and marking it as selected if it matches the assigned user's item.
         *
         * @return ListItem[] An array of ListItems, each representing an item per page assigned to a user,
         *                    with the appropriate ListItem marked as selected if it matches the user's current item.
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
         * Handles the change event for the item list associated with a user object for pagination.
         *
         * @param ActionParams $params The parameters provided by the action triggering the change.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgSportsAreas->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgSportsAreas->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a search filter text box with specific attributes and styles.
         *
         * @return void
         * @throws Caller
         */
        public function createFilter(): void
        {
            $this->txtFilter = new Bs\TextBox($this);
            $this->txtFilter->Placeholder = t('Search...');
            $this->txtFilter->TextMode = TextBoxBase::SEARCH;
            $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
            $this->txtFilter->addCssClass('search-box');
            $this->addFilterActions();

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstYears = new Q\Plugin\Select2($this);
            $this->lstYears->MinimumResultsForSearch = -1;
            $this->lstYears->Theme = 'web-vauu';
            $this->lstYears->Width = '100%';
            $this->lstYears->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstYears->addItem(t('- Select year -'), null, true);
            $this->lstYears->addItems($this->clearDuplicateYears());

            $this->lstYears->addAction(new Change(), new AjaxControl($this,'lstYears_Change'));
            $this->lstYears->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstGroups = new Q\Plugin\Select2($this);
            $this->lstGroups->MinimumResultsForSearch = -1;
            $this->lstGroups->Theme = 'web-vauu';
            $this->lstGroups->Width = '100%';
            $this->lstGroups->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroups->addItem(t('- Select sports area -'), null, true);

            $uniqueSportsAreas = $this->getUniqueSportsAreas();

            foreach ($uniqueSportsAreas as $sportsAreaId) {
                $sportsArea = SportsAreas::load($sportsAreaId);
                $this->lstGroups->AddItem(t($sportsArea->Name), $sportsAreaId);
            }

            $this->lstGroups->addAction(new Change(), new AjaxControl($this,'lstGroups_Change'));
            $this->lstGroups->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstContentTypes = new Q\Plugin\Select2($this);
            $this->lstContentTypes->MinimumResultsForSearch = -1;
            $this->lstContentTypes->Theme = 'web-vauu';
            $this->lstContentTypes->Width = '100%';
            $this->lstContentTypes->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstContentTypes->addItem(t('- Select content type -'), null, true);

            $objContentTypes = SportsContentTypes::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::SportsContentTypes()->TypeLocked, 1), QQN::SportsContentTypes()->Id)
                ]
            );

            foreach ($objContentTypes as $objName) {
                if ($objName->TypeLocked === 2) {
                    $this->lstContentTypes->addItem($objName->Name, $objName->Id);
                }
            }

            $this->lstContentTypes->addAction(new Change(), new AjaxControl($this,'lstContentTypes_Change'));
            $this->lstContentTypes->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->btnClearFilters = new Bs\Button($this);
            $this->btnClearFilters->Text = t('Clear filters');
            $this->btnClearFilters->addWrapperCssClass('center-button');
            $this->btnClearFilters->CssClass = 'btn btn-default';
            $this->btnClearFilters->setCssStyle('float', 'left');
            $this->btnClearFilters->CausesValidation = false;
            $this->btnClearFilters->addAction(new Click(), new AjaxControl($this, 'clearFilters_Click'));

            $this->updateLockStatus();
            $this->addFilterActions();
        }

        /**
         * Retrieves a list of unique sports area IDs.
         * This method iterates through all sports tables and adds the unique sports area IDs to the result.
         *
         * @return int[] An array of unique sports area IDs.
         * @throws Caller
         */
        public function getUniqueSportsAreas(): array
        {
            $allItems = SportsTables::loadAll();
            $uniqueSportsAreas = [];

            foreach ($allItems as $item) {
                if (!in_array($item->SportsAreasId, $uniqueSportsAreas)) {
                    $uniqueSportsAreas[] = $item->SportsAreasId;
                }
            }

            return $uniqueSportsAreas;
        }

        /**
         * Removes duplicate years from the collection of sports table items.
         * This method loads all items from the sports tables, extracts the years,
         * and ensures only unique year values are returned.
         *
         * @return int[] An array of unique years extracted from the sports table items.
         * @throws Caller
         */
        public function clearDuplicateYears(): array
        {
            $allItems = SportsTables::loadAll();
            $uniqueYears = [];

            foreach ($allItems as $item) {
                if ($item->SportsCalendarGroup->Status == 1) {
                    $uniqueYears[] = $item->Year;
                }
            }

            return array_unique($uniqueYears);
        }

        /**
         * Updates the lock status of various elements and refreshes the associated lists.
         *
         * The method performs checks on the count of locked items in different categories
         * (groups, changes, and categories) and applies visibility changes to the respective
         * UI elements using JavaScript. Each associated list is refreshed after the visibility update.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function updateLockStatus(): void
        {
            $countByYears = count($this->clearDuplicateYears());

            if ($countByYears > 1) {
                Application::executeJavaScript("$('.js-years').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-years').addClass('hidden');");
            }

            $countBySportsAreas = count($this->getUniqueSportsAreas());

            if ($countBySportsAreas > 1) {
                Application::executeJavaScript("$('.js-groups').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-groups').addClass('hidden');");
            }

            $this->lstGroups->refresh();

            $countByTypeLocked = SportsContentTypes::countByTypeLocked(2);

            if ($countByTypeLocked > 0) {
                Application::executeJavaScript("$('.js-types').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-types').addClass('hidden');");
            }

            $this->lstContentTypes->refresh();
        }

        /**
         * Disables various input controls and resets their values.
         *
         * @return void
         */
        protected function disableInputs(): void
        {
            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->lstItemsPerPageByAssignedUserObject->refresh();

            $this->txtFilter->Text = '';
            $this->txtFilter->Enabled = false;
            $this->txtFilter->refresh();

            $this->lstYears->SelectedValue = null;
            $this->lstYears->Enabled = false;
            $this->lstYears->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->Enabled = false;
            $this->lstGroups->refresh();

            $this->lstContentTypes->SelectedValue = null;
            $this->lstContentTypes->Enabled = false;
            $this->lstContentTypes->refresh();

            $this->dtgSportsAreas->refresh();
        }

        /**
         * Enables a series of input controls and clears their current values as well as refreshes their state.
         *
         * @return void
         */
        protected function enableInputs(): void
        {
            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->lstItemsPerPageByAssignedUserObject->refresh();

            $this->txtFilter->Text = '';
            $this->txtFilter->Enabled = true;
            $this->txtFilter->refresh();

            $this->lstYears->SelectedValue = null;
            $this->lstYears->Enabled = true;
            $this->lstYears->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->Enabled = true;
            $this->lstGroups->refresh();

            $this->lstContentTypes->SelectedValue = null;
            $this->lstContentTypes->Enabled = true;
            $this->lstContentTypes->refresh();

            $this->dtgSportsAreas->refresh();
        }

        /**
         * Handles the change event for the list of years.
         * This function triggers the refresh of the sports areas datagrid when the year selection changes.
         *
         * @param ActionParams $params Parameters associated with the action, providing context for the event.
         *
         * @return void
         * @throws Caller
         */
        protected function lstYears_Change(ActionParams $params): void
        {
            $this->dtgSportsAreas->refresh();
            $this->userOptions();
        }

        /**
         * Handles the change event for the search list and refreshes the news data grid.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function lstGroups_Change(ActionParams $params): void
        {
            $this->dtgSportsAreas->refresh();
            $this->userOptions();
        }

        /**
         * Handles changes to the content types list.
         * This function is triggered upon modifications to the content types,
         * refreshing the associated sports areas data grid to reflect the updates.
         *
         * @param ActionParams $params Parameters representing the action details, such as event context or data.
         *
         * @return void
         * @throws Caller
         */
        protected function lstContentTypes_Change(ActionParams $params): void
        {
            $this->dtgSportsAreas->refresh();
            $this->userOptions();
        }

        /**
         * Clears all applied filters in the current view, resetting text and dropdown fields
         * and refreshing associated controls.
         *
         * @param ActionParams $params The parameters passed to the action, containing any additional
         *         information about the event triggered.
         *
         * @return void This method does not return a value.
         * @throws Caller
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->lstYears->SelectedValue = null;
            $this->lstYears->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->refresh();

            $this->lstContentTypes->SelectedValue = null;
            $this->lstContentTypes->refresh();

            $this->dtgSportsAreas->refresh();
            $this->userOptions();
        }

        /**
         * Adds filter actions to a text filter control.
         * This method binds actions to be triggered by events such as input and enter key press.
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
         * Triggers a refresh of the sports areas data grid when the filter is changed.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgSportsAreas->refresh();
        }

        /**
         * Binds data to the sports areas data grid using a specified condition.
         *
         * Retrieves the condition for filtering data and applies it to the data grid
         * of sports areas for binding the relevant data sets.
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
         * @return All|AndCondition|OrCondition The query condition based on the filter input.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function getCondition(): All|AndCondition|OrCondition
        {
            $strText = trim($this->txtFilter->Text ?? '');
            $intYearId = (int)$this->lstYears->SelectedName; // NAME value
            $intGroupId = $this->lstGroups->SelectedValue; // ID value
            $intContentTypeId = $this->lstContentTypes->SelectedValue; // ID value

            $condList = [];

            // If a year selected
            if (!empty($intYearId)) {
                $condList[] = QQ::equal(QQN::SportsTables()->Year, $intYearId); // or the correct field that you have binding
            }

            // If a group selected
            if (!empty($intGroupId)) {
                $condList[] = QQ::equal(QQN::SportsTables()->SportsAreas->Id, $intGroupId); // or the correct field that you have binding
            }

            // If a content type selected
            if (!empty($intContentTypeId)) {
                $condList[] = QQ::equal(QQN::SportsTables()->SportsContentTypes->Id, $intContentTypeId); // or the correct field that you have binding
            }

            // If a text is entered
            if ($strText !== '') {
                // Do one big 'or' for multiple fields in the text
                $orText = QQ::orCondition(
                    QQ::like(QQN::SportsTables()->Year, "%" . $strText . "%"),
                    QQ::like(QQN::SportsTables()->SportsAreas->Name, "%" . $strText . "%"),
                    QQ::like(QQN::SportsTables()->SportsContentTypes->Name, "%" . $strText . "%"),
                    QQ::like(QQN::SportsTables()->Title, "%" . $strText . "%")
                );
                $condList[] = $orText;
            }

            // If neither filter is present, return all
            if (count($condList) === 0) {
                return QQ::all();
            }

            // If both conditions are met, combine with AND
            return QQ::andCondition(...$condList);
        }

        /**
         * Initializes and configures informational and navigation buttons for the user interface.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('<p>Important information! Clicking on a table row will redirect you to the sports calendar 
                                to edit documents if needed. There will be no return to this page!</p>
                                <p>Alternatively, you can return to this page by using the browser\'s "Back" button.</p>');

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->addWrapperCssClass('center-button');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new AjaxControl($this, 'btnBack_Click'));
            $this->btnBack->setCssStyle('float', 'left');
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
         * Handles the click event for the back button, performing a redirection and session cleanup.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void This method does not return a value. It redirects the application and modifies the session.
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnBack_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            Application::redirect('menu_edit.php?id=' . $_SESSION['sports_view']);
            unset($_SESSION['sports_view']);

        }
    }