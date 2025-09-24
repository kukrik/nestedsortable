<?php

    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\CellClick;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;

    /**
     * Represents a panel for displaying and interacting with a list of statistics.
     *
     * This class extends the core Panel class and provides functionalities for managing
     * and displaying statistical data in a tabular format. It includes options for
     * creating buttons, modals, and dynamic data binding with customized behavior.
     */
    class StatisticsListPanel extends Panel
    {
        public Bs\Modal $dlgModal1;

        public  StatisticsTable $dtgStatistics;
        public Bs\Button $btnBack;

        protected object $objUser;
        protected int $intLoggedUserId;

        protected string $strTemplate = 'StatisticsListPanel.tpl.php';

        /**
         * Class constructor for initializing the object and its dependencies.
         *
         * This constructor sets up the necessary components such as loading the logged-in user,
         * creating buttons, initializing modal dialogs, and preparing the statistics grid.
         * It also ensures that parent class initialization is correctly handled and any
         * exceptions during instantiation are appropriately managed.
         *
         * @param mixed $objParentObject The parent object for this control, typically the form or a control container.
         * @param string|null $strControlId Optional control ID. If null, a unique ID will be auto-generated.
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

            $this->intLoggedUserId = 3;
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createButtons();
            $this->createModals();
            $this->dtgStatistics_Create();
            $this->dtgStatistics->setDataBinder('BindData', $this);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and configures buttons for the user interface.
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
         * Handles the back button click event and redirects the user to the menu manager page.
         *
         * @param ActionParams $params Parameters associated with the button click event.
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

            Application::redirect('menu_manager.php');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes the StatisticsTable instance and configures its properties and behavior.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgStatistics_Create(): void
        {
            $this->dtgStatistics = new StatisticsTable($this);
            $this->dtgStatistics_CreateColumns();
            $this->dtgStatistics_MakeEditable();
            $this->dtgStatistics->RowParamsCallback = [$this, "dtgStatistics_GetRowParams"];
            $this->dtgStatistics->SortColumnIndex = 0;
            //$this->dtgStatistics->SortDirection = -1;
            $this->dtgStatistics->UseAjax = true;
        }

        /**
         * Initializes and creates columns for the dtgStatistics object.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgStatistics_CreateColumns(): void
        {
            $this->dtgStatistics->createColumns();
        }

        /**
         * Configures the dtgStatistics object to be editable by adding actions and CSS classes.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgStatistics_MakeEditable(): void
        {
            $this->dtgStatistics->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgStatisticsRow_Click'));
            $this->dtgStatistics->addCssClass('clickable-rows');
            $this->dtgStatistics->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click action for a statistics row in the DataGrid.
         *
         * @param ActionParams $params Parameters received from the action, including the identifier of the clicked row.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgStatisticsRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intId = intval($params->ActionParameter);
            $objStatistics = StatisticsSettings::loadById($intId);

            Application::redirect($objStatistics->getUrlDestination() .'?id=' . $intId);
        }

        /**
         * Retrieves the parameters for a specific row in the statistics table.
         *
         * @param object $objRowObject The row object containing the data for the current row.
         * @param int $intRowIndex The index of the row being processed.
         *
         * @return array An associative array of parameters for the row, including a data-value attribute.
         */
        public function dtgStatistics_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $intIsReserved = $objRowObject->getIsReserved();

            if ($intIsReserved == 2) {
                $params['class'] = 'hidden';
            }

            $params['data-value'] = $strKey;

            return $params;
        }

        /**
         * Binds data to the dtgStatistics object based on the specified condition.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgStatistics->bindData($objCondition);
        }

        /**
         * Retrieves the condition for the current operation or context.
         *
         * @return null The condition determined by the implementation.
         */
        protected function getCondition(): null
        {
            return null;
        }
    }