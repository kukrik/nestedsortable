<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Project\Application;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Action\ActionParams;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Event\CellClick;
    use QCubed\Event\Change;
    use QCubed\Event\EnterKey;
    use QCubed\Event\Input;
    use QCubed\QDateTime;
    use QCubed\Query\QQ;

    /**
     * Class SlidersListAdmin
     *
     * Provides a user interface for managing a list of sliders. This includes functionalities
     * like displaying slider data in a tabular format, filtering, paginating, sorting, and
     * handling interactive actions on the data table and related controls.
     */
    class SlidersListAdmin extends Panel
    {
        protected Q\Plugin\VauuTable $dtgSliders;
        protected Q\Plugin\Select2 $lstItemsPerPage;
        protected Bs\TextBox $txtFilter;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'SlidersListAdmin.tpl.php';

        /**
         * Constructor for the class initializes the control and sets up the required table.
         *
         * @param mixed $objParentObject The parent object to which this control belongs.
         * @param string|null $strControlId Optional control ID for identifying the control.
         *
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

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createTable();
        }

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
         * Initializes and configures the table used to display slider data.
         *
         * This method sets up a table with columns for various slider attributes such as title, status,
         * creation date, and modification date. It also configures a paginator, adds AJAX capabilities,
         * and includes additional components for user interaction like items-per-page selection and a search filter.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createTable(): void
        {
            $this->dtgSliders = new Q\Plugin\VauuTable($this);
            $this->dtgSliders->CssClass = "table vauu-table table-hover table-responsive";

            $col = $this->dtgSliders->createNodeColumn(t('Title'), QQN::SlidersList()->Title);

            $col = $this->dtgSliders->createNodeColumn(t('Status'), QQN::SlidersList()->StatusObject);
            $col->HtmlEntities = false;

            $col = $this->dtgSliders->createNodeColumn(t('Created'), QQN::SlidersList()->PostDate);
            $col->Format = $this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time;

            $col = $this->dtgSliders->createNodeColumn(t('Modified'), QQN::SlidersList()->PostUpdateDate);
            $col->Format = $this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time;

            $col = $this->dtgSliders->createNodeColumn(t('Last modified by'), QQN::SlidersList()->PostUpdateUser);

            $this->dtgSliders->Paginator = new Bs\Paginator($this);
            $this->dtgSliders->Paginator->LabelForPrevious = t('Previous');
            $this->dtgSliders->Paginator->LabelForNext = t('Next');
            $this->dtgSliders->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();

            $this->dtgSliders->UseAjax = true;
            $this->dtgSliders->SortColumnIndex = 2;
            $this->dtgSliders->SortDirection = -1;
            $this->dtgSliders->setDataBinder('dtgSliders_Bind', $this);
            $this->dtgSliders->RowParamsCallback = [$this, 'dtgSliders_GetRowParams'];
            $this->dtgSliders->addAction(new CellClick(0, null, CellClick::rowDataValue('value')),
                new AjaxControl($this,'dtgSlidersRow_Click'));

            ////////////////////////////

            $this->lstItemsPerPage = new Q\Plugin\Select2($this);
            $this->lstItemsPerPage->addCssFile(QCUBED_FILEUPLOAD_ASSETS_URL . '/css/select2-web-vauu.css');
            $this->lstItemsPerPage->MinimumResultsForSearch = -1;
            $this->lstItemsPerPage->Theme = 'web-vauu';
            $this->lstItemsPerPage->Width = '100%';
            $this->lstItemsPerPage->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstItemsPerPage->SelectedValue = $this->dtgSliders->ItemsPerPage;
            $this->lstItemsPerPage->addItems(array(10, 25, 50, 100));
            $this->lstItemsPerPage->AddAction(new Change(), new AjaxControl($this,'lstItemsPerPage_Change'));

            $this->txtFilter = new Bs\TextBox($this);
            $this->txtFilter->Placeholder = t('Search...');
            $this->txtFilter->TextMode = TextBoxBase::SEARCH;
            $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
            $this->txtFilter->addCssClass('search-box');
            $this->addFilterActions();
        }

        /**
         * Retrieves the parameters for a specific row to be used with sliders.
         *
         * @param mixed $objRowObject The row object containing the data for the specific row.
         * @param int $intRowIndex The index of the row for which the parameters are being retrieved.
         *
         * @return array An associative array of parameters for the row, including 'data-value' with the primary key.
         */
        public function dtgSliders_GetRowParams(mixed $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Handles the click event on a row in the slider data grid.
         *
         * @param ActionParams $params The parameters of the action, which include the action parameter containing
         *     the slider ID associated with the clicked row.
         *
         * @return void
         * @throws Throwable
         */
        protected function dtgSlidersRow_Click(ActionParams $params): void
        {
            $this->userOptions();

            $intSliderId = intval($params->ActionParameter);
            Application::redirect('slider_edit.php' . '?id=' . $intSliderId);
        }

        /**
         * Handles the change event for the items per-page dropdown.
         *
         * @param ActionParams $params Parameters associated with the action triggered by changing the dropdown.
         *
         * @return void
         */
        protected function lstItemsPerPage_Change(ActionParams $params): void
        {
            $this->dtgSliders->refresh();
        }

        /**
         * Adds filter actions to the filter input component.
         * Configures actions for input events and Enter key press to trigger the filter change process.
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
         * Handles the event triggered when a filter value changes.
         *
         * @return void
         * @throws Caller
         */
        protected function filterChanged(): void
        {
            $this->dtgSliders->refresh();
            $this->userOptions();
        }

        /**
         * Binds data to the dtgSliders data grid based on the current filter and sorting criteria.
         * If a search value is provided, the data is filtered by matching titles, post-dates, or update dates.
         * If no filter is specified, all sliders are displayed.
         *
         * @return void
         * @throws Caller
         */
        public function dtgSliders_Bind(): void
        {
            $strSearchValue = $this->txtFilter->Text ?: '';
            $strSearchValue = trim($strSearchValue);

            if ($strSearchValue === '') {
                $objCondition = QQ::all();
            } else {
                $objCondition = QQ::orCondition(
                    QQ::like(QQN::SlidersList()->Title, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::SlidersList()->PostDate, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::SlidersList()->PostUpdateDate, "%" . $strSearchValue . "%")
                );
            }

            $this->dtgSliders->TotalItemCount = SlidersList::queryCount($objCondition);

            $clauses = [];
            if ($this->dtgSliders->OrderByClause) $clauses[] = $this->dtgSliders->OrderByClause;
            if ($this->dtgSliders->LimitClause) $clauses[] = $this->dtgSliders->LimitClause;

            $this->dtgSliders->DataSource = SlidersList::queryArray(
                $objCondition,
                $clauses
            );
        }
    }