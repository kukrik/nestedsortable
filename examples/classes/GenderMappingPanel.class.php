<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use Random\RandomException;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\Click;
    use QCubed\Event\CellClick;
    use QCubed\Event\Change;
    use QCubed\Event\DialogButton;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;

    /**
     * Class GenderMappingPanel
     *
     * This class is responsible for managing the Gender Mapping Panel, which includes user interface components
     * for adding and managing mappings between age categories, genders, and their respective statuses.
     * It features data grids, modals, buttons, input controls, and other functionalities required for seamless management.
     */
    class GenderMappingPanel extends Panel
    {
        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;
        protected Q\Plugin\Toastr $dlgToastr4;
        protected Q\Plugin\Toastr $dlgToastr5;
        protected Q\Plugin\Toastr $dlgToastr6;
        protected Q\Plugin\Toastr $dlgToastr7;
        protected Q\Plugin\Toastr $dlgToastr8;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;
        public Bs\Modal $dlgModal6;

        public Q\Plugin\Control\Alert $lblInfo;
        public GenderMappingTable $dtgAgeCategoryGender;
        public Bs\Button $btnRefresh;
        public Bs\Button $btnAddNewMapping;

        public Q\Plugin\Control\Label $lblAgeGroup;
        public Q\Plugin\Select2 $lstAgeGroup;
        public Q\Plugin\Control\Label $lblAthleteGender;
        public Q\Plugin\Select2 $lstAthleteGender;
        public Q\Plugin\Control\Label $lblGender;
        public Q\Plugin\Select2 $lstGender;
        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        public Q\Plugin\Control\Label $lblPostDate;
        public Bs\Label $calPostDate;
        public Q\Plugin\Control\Label $lblPostUpdateDate;
        public Bs\Label $calPostUpdateDate;
        public Q\Plugin\Control\Label $lblAuthor;
        public Bs\Label $txtAuthor;
        public Q\Plugin\Control\Label $lblUsersAsEditors;
        public Bs\Label $txtUsersAsEditors;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected bool $blnEditMode = true;
        protected ?object $objAgeCategoryGender = null;
        protected array $errors = []; // Array for tracking errors

        protected string $strTemplate = 'GenderMappingPanel.tpl.php';

        /**
         * Constructor for initializing the component and setting up necessary properties, controls, buttons, and modals.
         *
         * @param mixed $objParentObject The parent object or controller instance that contains this component.
         * @param string|null $strControlId Optional control ID for differentiating this instance in case of multiple instances.
         *
         * @return void
         * @throws Caller If the parent constructor encounters an error.
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

            $this->dtgAgeCategoryGender_Create();

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
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
         * Initializes the GenderMappingTable component and sets up its configuration.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgAgeCategoryGender_Create(): void
        {
            $this->dtgAgeCategoryGender = new GenderMappingTable($this);
            $this->dtgAgeCategoryGender_CreateColumns();
            $this->dtgAgeCategoryGender_MakeEditable();
            $this->dtgAgeCategoryGender->RowParamsCallback = [$this, "dtgAgeCategoryGender_GetRowParams"];
            $this->dtgAgeCategoryGender->SortColumnIndex = 0;
            $this->dtgAgeCategoryGender->SortDirection = -1;
        }

        /**
         * Initializes and creates the columns for the AgeCategoryGender datagrid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgAgeCategoryGender_CreateColumns(): void
        {
            $this->dtgAgeCategoryGender->createColumns();
        }

        /**
         * Configures the DataGrid (dtgAgeCategoryGender) to be editable by enabling cell click actions
         * and applying appropriate CSS classes for styling and functionality.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgAgeCategoryGender_MakeEditable(): void
        {
            $this->dtgAgeCategoryGender->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgAgeCategoryGender_Click'));
            $this->dtgAgeCategoryGender->addCssClass('clickable-rows');
            $this->dtgAgeCategoryGender->CssClass = 'table vauu-table js-sports-area table-hover table-responsive';
        }

        /**
         * Handles the Click event for the age category gender data grid row.
         *
         * @param ActionParams $params The parameters containing action details, including the ID of the selected row.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgAgeCategoryGender_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->intId = intval($params->ActionParameter);
            $this->objAgeCategoryGender = AgeCategoryGender::load($this->intId);
            $this->btnSave->Display = false;

            if ($this->objAgeCategoryGender->getIsLocked() == 1) {
                $this->btnDelete->Display = true;
            } else {
                $this->btnDelete->Display = false;
            }

            $this->blnEditMode = true;

            $this->dtgAgeCategoryGender->addCssClass('disabled');
            $this->refreshDisplay($this->intId);

            Application::executeJavaScript("
                $('.setting-wrapper').removeClass('hidden');
                $('.form-actions-wrapper').removeClass('hidden');
                $('.js-wrapper-top').get(0).scrollIntoView({behavior: 'smooth'});
            ");

            $this->activeInputs($this->objAgeCategoryGender);
            $this->checkInputs();
        }

        /**
         * Retrieves row parameters for AgeCategoryGender data grid.
         *
         * @param object $objRowObject The object representing the current row in the data grid.
         * @param int $intRowIndex The index of the current row in the data grid.
         *
         * @return array An associative array containing the row parameters, such as 'data-value'.
         */
        public function dtgAgeCategoryGender_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();

            $params['data-value'] = $strKey;
            return $params;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures input controls, labels, and selectors for managing age categories, genders, and statuses.
         *
         * This method creates and applies styles, actions, and data population logic
         * for inputs including dropdowns, labels, and other user interface components.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createInputs(): void
        {
            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('Please create the first age class!');
            $this->lblInfo->setCssStyle('margin-bottom', 0);

            $countAgeCategoryGender = AgeCategoryGender::countAll();

            if ($countAgeCategoryGender === 0) {
                $this->lblInfo->Display = true;
                $this->dtgAgeCategoryGender->Display = false;
            } else {
                $this->lblInfo->Display = false;
                $this->dtgAgeCategoryGender->Display = true;
            }

            $this->lblAgeGroup = new Q\Plugin\Control\Label($this);
            $this->lblAgeGroup->Text = t('Age group');
            $this->lblAgeGroup->addCssClass('col-md-4');
            $this->lblAgeGroup->setCssStyle('font-weight', 'normal');

            $this->lstAgeGroup = new Q\Plugin\Select2($this);
            $this->lstAgeGroup->MinimumResultsForSearch = -1;
            $this->lstAgeGroup->ContainerWidth = 'resolve';
            $this->lstAgeGroup->Theme = 'web-vauu';
            $this->lstAgeGroup->Width = '100%';
            $this->lstAgeGroup->setCssStyle('float', 'left');
            $this->lstAgeGroup->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstAgeGroup->addItem(t('- Select age group -'), null, true);
            $this->lstAgeGroup->addAction(new Change(), new AjaxControl($this, 'lstAgeGroup_Change'));

            $objAgeGroups = AgeCategories::loadAll();

            foreach ($objAgeGroups as $objAgeGroup) {
                $this->lstAgeGroup->addItem($objAgeGroup->ClassName, $objAgeGroup->Id);
            }

            $this->lblAthleteGender = new Q\Plugin\Control\Label($this);
            $this->lblAthleteGender->Text = t('Athlete gender');
            $this->lblAthleteGender->addCssClass('col-md-4');
            $this->lblAthleteGender->setCssStyle('font-weight', 'normal');

            $this->lstAthleteGender = new Q\Plugin\Select2($this);
            $this->lstAthleteGender->MinimumResultsForSearch = -1;
            $this->lstAthleteGender->ContainerWidth = 'resolve';
            $this->lstAthleteGender->Theme = 'web-vauu';
            $this->lstAthleteGender->Width = '100%';
            $this->lstAthleteGender->setCssStyle('float', 'left');
            $this->lstAthleteGender->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstAthleteGender->addItem(t('- Select age group -'), null, true);
            $this->lstAthleteGender->addAction(new Change(), new AjaxControl($this, 'lstAthleteGender_Change'));

            $objAthleteGenders = AthleteGender::loadAll();

            foreach ($objAthleteGenders as $objAthleteGender) {
                $this->lstAthleteGender->addItem($objAthleteGender->Gender, $objAthleteGender->Id);
            }

            $this->lblGender = new Q\Plugin\Control\Label($this);
            $this->lblGender->Text = t('Gender group');
            $this->lblGender->addCssClass('col-md-4');
            $this->lblGender->setCssStyle('font-weight', 'normal');

            $this->lstGender = new Q\Plugin\Select2($this);
            $this->lstGender->MinimumResultsForSearch = -1;
            $this->lstGender->ContainerWidth = 'resolve';
            $this->lstGender->Theme = 'web-vauu';
            $this->lstGender->Width = '100%';
            $this->lstGender->setCssStyle('float', 'left');
            $this->lstGender->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGender->addItem(t('- Select gender group -'), null, true);
            $this->lstGender->addAction(new Change(), new AjaxControl($this, 'lstGender_Change'));

            $objGenders = Genders::loadAll();

            foreach ($objGenders as $objGender) {
                $this->lstGender->addItem($objGender->Name, $objGender->Id);
            }

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->addCssClass('col-md-4');
            $this->lblStatus->setCssStyle('font-weight', 'normal');

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
            $this->lstStatus->addAction(new Change(), new AjaxControl($this, 'lstStatus_Change'));

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblPostDate = new Q\Plugin\Control\Label($this);
            $this->lblPostDate->Text = t('Created');
            $this->lblPostDate->setCssStyle('font-weight', 'bold');
            $this->lblPostDate->Display = false;

            $this->calPostDate = new Bs\Label($this);
            $this->calPostDate->setCssStyle('font-weight', 'normal');
            $this->calPostDate->Display = false;

            $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
            $this->lblPostUpdateDate->Text = t('Updated');
            $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');
            $this->lblPostUpdateDate->Display = false;

            $this->calPostUpdateDate = new Bs\Label($this);
            $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');
            $this->calPostUpdateDate->Display = false;

            $this->lblAuthor = new Q\Plugin\Control\Label($this);
            $this->lblAuthor->Text = t('Author');
            $this->lblAuthor->setCssStyle('font-weight', 'bold');
            $this->lblAuthor->Display = false;

            $this->txtAuthor  = new Bs\Label($this);
            $this->txtAuthor->setCssStyle('font-weight', 'normal');
            $this->txtAuthor->Display = false;

            $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
            $this->lblUsersAsEditors->Text = t('Editors');
            $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');
            $this->lblUsersAsEditors->Display = false;

            $this->txtUsersAsEditors  = new Bs\Label($this);
            $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');
            $this->txtUsersAsEditors->Display = false;
        }

        /**
         * Initializes and configures buttons for the interface, including actions, styles, and validation settings.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnRefresh = new Bs\Button($this);
            $this->btnRefresh->Tip = true;
            $this->btnRefresh->ToolTip = t('Refresh tables');
            $this->btnRefresh->Glyph = 'fa fa-refresh';
            $this->btnRefresh->CssClass = 'btn btn-darkblue';
            $this->btnRefresh->CausesValidation = false;
            $this->btnRefresh->setCssStyle('margin-left', '15px');
            $this->btnRefresh->addAction(new Click(), new AjaxControl($this, 'btnRefresh_Click'));

            $this->btnAddNewMapping = new Bs\Button($this);
            $this->btnAddNewMapping->Text = t('Add new mapping');
            $this->btnAddNewMapping->CssClass = 'btn btn-orange';
            $this->btnAddNewMapping->CausesValidation = false;
            //$this->btnAddNewMapping->setCssStyle('float', 'left');
            $this->btnAddNewMapping->addAction(new Click(), new AjaxControl($this, 'btnAddNewMapping_Click'));

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->setCssStyle('margin-right', '10px');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));

            $this->btnDelete = new Bs\Button($this);
            $this->btnDelete->Text = t('Delete');
            $this->btnDelete->CssClass = 'btn btn-danger';
            $this->btnDelete->setCssStyle('margin-right', '10px');
            $this->btnDelete->CausesValidation = true;
            $this->btnDelete->addAction(new Click(), new AjaxControl($this, 'btnDelete_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
        }

        /**
         * Creates and initializes multiple Toastr notification instances with predefined configurations
         * for displaying success or error messages in various scenarios.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('The completed mapping data was saved or modified!');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('Failed to add the completed mapping');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('This field is required!');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('These fields must be filled!');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('<strong>Well done!</strong> This completed mapping with data is now active!');
            $this->dlgToastr5->ProgressBar = true;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('<strong>Well done!</strong> This completed mapping with data is now inactive!');
            $this->dlgToastr6->ProgressBar = true;

            $this->dlgToastr7 = new Q\Plugin\Toastr($this);
            $this->dlgToastr7->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr7->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr7->Message = t('<strong>Well done!</strong> The new completed mapping was successfully added to the database.');
            $this->dlgToastr7->ProgressBar = true;

            $this->dlgToastr8 = new Q\Plugin\Toastr($this);
            $this->dlgToastr8->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr8->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr8->Message = t('The compiled mapping data table has been refreshed!');
            $this->dlgToastr8->ProgressBar = true;
        }

        /**
         * Initializes and configures multiple modal dialogs used within the system.
         * Each modal has specific text, titles, header styling, and associated actions corresponding to predefined use
         * cases.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the completed mapping?</p>
                                        <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone.</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addCloseButton(t("I'll cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'deleteItem_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'hideItem_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This completed mapping cannot be 
                                        deleted because it is locked in the records table or leaderboard!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                        and both tables will be updated automatically.</p>');

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This completed mapping status cannot 
                                        be deactivated because it is locked in records or leaderboards.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                        and both tables will be updated automatically.</p>');

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->HeaderClasses = 'btn-darkblue';
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">One or the other input of this bundled 
                                        mapping must not be left blank, as it is already locked in the records or leaderboards.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                        and both tables will be updated automatically.</p>');

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Tip");
            $this->dlgModal5->HeaderClasses = 'btn-darkblue';
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">When completing a new mapping, 
                                        please make sure all fields are filled!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">Activating the status is optional!</p>');

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
         * Handles the click event for the refresh button, verifying the CSRF token and refreshing
         * the data grid displaying AgeCategoryGender objects.
         *
         * @param ActionParams $params The parameters received from the user interaction that triggered this method.
         *
         * @return void
         * @throws Exception If the CSRF token verification fails or any other unexpected scenario occurs during execution.
         */
        protected function btnRefresh_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->dtgAgeCategoryGender->refresh();
            $this->dlgToastr8->notify();
        }

        /**
         * Handles the click event for the "Add New Mapping" button. This method prepares the UI for adding a new mapping by
         * displaying relevant sections, updating the CSS styles, and resetting the inputs.
         *
         * @param ActionParams $params Event parameters passed from the triggering action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws RandomException
         */
        public function btnAddNewMapping_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            Application::executeJavaScript("
                $('.setting-wrapper').removeClass('hidden');
                $('.form-actions-wrapper').removeClass('hidden');
            ");

            $this->dtgAgeCategoryGender->addCssClass('disabled');
            $this->btnSave->Display = true;
            $this->btnDelete->Display = false;
            $this->blnEditMode = false;

            $this->resetInputs();
        }

        /**
         * Handles the change event for the age group selector.
         *
         * This method updates the age category associated with the given object,
         * validates inputs, and notifies the user about the status of the operation.
         * It is primarily used in edit mode to modify the age category.
         *
         * @param ActionParams $params Parameters associated with the change event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstAgeGroup_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === true) {
                if ($this->objAgeCategoryGender->getIsLocked() == 2) {
                    if ($this->lstAgeGroup->SelectedValue == null) {
                        $this->activeInputs($this->objAgeCategoryGender);
                        $this->lstStatus->SelectedValue = 1;
                        $this->dlgModal4->showDialogBox();
                        return;
                    } else {
                        $this->objAgeCategoryGender->setAgeCategoryId($this->lstAgeGroup->SelectedValue);
                        $this->dlgToastr1->notify(); // Everything OK
                    }
                } else {
                    $this->checkInputs();

                    if ($this->lstAgeGroup->SelectedValue) {
                        $this->objAgeCategoryGender->setAgeCategoryId($this->lstAgeGroup->SelectedValue);
                        $this->dlgToastr1->notify(); // Everything OK
                    } else {
                        $this->objAgeCategoryGender->setAgeCategoryId(null);
                        $this->objAgeCategoryGender->setStatus(2);
                        $this->lstStatus->SelectedValue = 2;
                    }
                }

                $this->objAgeCategoryGender->save();

                $this->updateAndValidateAgeGroups($this->objAgeCategoryGender);
            }

            $this->userOptions();
        }

        /**
         * Handles the change event for the athlete gender selection and updates the corresponding
         * AgeCategoryGender object based on the current selection and conditions.
         *
         * @param ActionParams $params The parameters received from the user interaction that triggered this method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstAthleteGender_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === true) {
                if ($this->objAgeCategoryGender->getIsLocked() == 2) {
                    if ($this->lstAthleteGender->SelectedValue == null) {
                        $this->activeInputs($this->objAgeCategoryGender);
                        $this->lstStatus->SelectedValue = 1;
                        $this->dlgModal4->showDialogBox();
                        return;
                    } else {
                        $this->objAgeCategoryGender->setAthleteGenderId($this->lstAthleteGender->SelectedValue);
                        $this->dlgToastr1->notify(); // Everything OK
                    }
                } else {
                    $this->checkInputs();

                    if ($this->lstAthleteGender->SelectedValue) {
                        $this->objAgeCategoryGender->setAthleteGenderId($this->lstAthleteGender->SelectedValue);
                        $this->dlgToastr1->notify(); // Everything OK
                    } else {
                        $this->objAgeCategoryGender->setAthleteGenderId(null);
                        $this->objAgeCategoryGender->setStatus(2);
                        $this->lstStatus->SelectedValue = 2;
                    }
                }

                $this->objAgeCategoryGender->save();

                $this->updateAndValidateAgeGroups($this->objAgeCategoryGender);
            }

            $this->userOptions();
        }

        /**
         * Handles the change event for the gender dropdown list.
         *
         * This method updates the gender information for a specific age category and applies the necessary validations.
         * It also triggers notifications or dialogs based on the state of the data and user inputs.
         *
         * @param ActionParams $params The parameters passed in the action event, containing details about the triggered action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws \Random\RandomException
         */
        protected function lstGender_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === true) {
                if ($this->objAgeCategoryGender->getIsLocked() == 2) {
                    if ($this->lstGender->SelectedValue == null) {
                        $this->activeInputs($this->objAgeCategoryGender);
                        $this->lstStatus->SelectedValue = 1;
                        $this->dlgModal4->showDialogBox();
                        return;
                    } else {
                        $this->objAgeCategoryGender->setGenderId($this->lstGender->SelectedValue);
                        $this->dlgToastr1->notify(); // Everything OK
                    }
                } else {
                    $this->checkInputs();

                    if ($this->lstGender->SelectedValue) {
                        $this->objAgeCategoryGender->setGenderId($this->lstGender->SelectedValue);
                        $this->dlgToastr1->notify(); // Everything OK
                    } else {
                        $this->objAgeCategoryGender->setGenderId(null);
                        $this->objAgeCategoryGender->setStatus(2);
                        $this->lstStatus->SelectedValue = 2;
                    }
                }

                $this->objAgeCategoryGender->save();

                $this->updateAndValidateAgeGroups($this->objAgeCategoryGender);
            }

            $this->userOptions();
        }

        /**
         * Handles the change event for the status dropdown list.
         *
         * This method processes the selected status of an age category gender entity
         * based on the user input and the current state of the application. It performs
         * various validations and updates the status of the entity accordingly.
         *
         * @param ActionParams $params Action parameters that include context information for the event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->checkInputs();

            if ($this->blnEditMode === true) {
                if (count($this->errors)) {
                    $this->lstStatus->SelectedValue = 2;
                    $this->objAgeCategoryGender->setStatus(2);
                } else {
                    if ($this->objAgeCategoryGender->getIsLocked() == 1) {
                        if ($this->lstStatus->SelectedValue == 1) {
                            $this->objAgeCategoryGender->setStatus(1);
                            $this->dlgToastr5->notify();
                        } else {
                            $this->objAgeCategoryGender->setStatus(2);
                            $this->dlgToastr6->notify();
                        }
                    } else {
                        $this->dlgModal3->showDialogBox();
                        $this->lstStatus->SelectedValue = 1;
                    }
                }

                $this->objAgeCategoryGender->save();

                $this->updateAndValidateAgeGroups($this->objAgeCategoryGender);
            }

            $this->userOptions();
        }

        /**
         * Handles the save button click event to create and update records related to AgeCategoryGender,
         * AgeCategories, and Genders based on user input and conditions.
         *
         * @param ActionParams $params The parameters received from the user interaction that triggered this method.
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

            $this->checkInputs();

            if ($this->blnEditMode === false) {
                if (!count($this->errors)) {
                    $objAgeCategoryGender = new AgeCategoryGender();
                    $objAgeCategoryGender->setAgeCategoryId($this->lstAgeGroup->SelectedValue);
                    $objAgeCategoryGender->setAthleteGenderId($this->lstAthleteGender->SelectedValue);
                    $objAgeCategoryGender->setGenderId($this->lstGender->SelectedValue);
                    $objAgeCategoryGender->setStatus($this->lstStatus->SelectedValue);
                    $objAgeCategoryGender->setAssignedByUser($this->intLoggedUserId);
                    $objAgeCategoryGender->setAuthor($objAgeCategoryGender->getAssignedByUserObject());
                    $objAgeCategoryGender->setPostDate(QDateTime::now());
                    $objAgeCategoryGender->save();

                    $objAgeGroup = AgeCategories::loadById($this->lstAgeGroup->SelectedValue);
                    $objAgeGroup->setIsLocked(2);
                    $objAgeGroup->save();

                    $objGender = Genders::loadById($this->lstGender->SelectedValue);
                    $objGender->setIsLocked(2);
                    $objGender->save();

                    Application::executeJavaScript("
                        $('.setting-wrapper').addClass('hidden');
                        $('.form-actions-wrapper').addClass('hidden');
                    ");

                    $this->hideUserWindow();
                    $this->dtgAgeCategoryGender->removeCssClass('disabled');

                    $this->dlgToastr7->notify();
                } else {
                    $this->resetInputs();
                    unset($this->errors);

                    $this->dlgModal5->showDialogBox();
                }
            }

            $this->userOptions();
        }

        /**
         * Handles the cancel button click event. Hides the settings and actions UI components,
         * resets errors, and enables the AgeCategoryGender datagrid interactions.
         *
         * @param ActionParams $params The parameters received from the cancel button click event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden')
            ");

            $this->hideUserWindow();
            $this->dtgAgeCategoryGender->removeCssClass('disabled');
            unset($this->errors);
        }

        /**
         * Validates the selected values of the input fields and applies error classes to
         * the fields that are invalid. Any fields that do not have a valid selection are
         * recorded in the error array.
         *
         * @return void
         */
        public function checkInputs(): void
        {
            // We check each field and add errors if necessary

            if (!$this->lstAgeGroup->SelectedValue) {
                $this->lstAgeGroup->addCssClass('has-error');
                $this->errors[] = 'lstAgeGroup';
            } else {
                $this->lstAgeGroup->removeCssClass('has-error');
            }

            if (!$this->lstAthleteGender->SelectedValue) {
                $this->lstAthleteGender->addCssClass('has-error');
                $this->errors[] = 'lstAthleteGender';
            } else {
                $this->lstAthleteGender->removeCssClass('has-error');
            }

            if (!$this->lstGender->SelectedValue) {
                $this->lstGender->addCssClass('has-error');
                $this->errors[] = 'lstGender';
            } else {
                $this->lstGender->removeCssClass('has-error');
            }
        }

        /**
         * Updates the selected values in dropdown lists based on the provided object's properties
         * and refreshes the controls to reflect the changes.
         *
         * @param object $objEdit An object containing the properties used to update the dropdown selections.
         *
         * @return void
         */
        public function activeInputs(object $objEdit): void
        {
            $this->lstAgeGroup->SelectedValue = $objEdit->getAgeCategoryId();
            $this->lstAthleteGender->SelectedValue = $objEdit->getAthleteGenderId();
            $this->lstGender->SelectedValue = $objEdit->getGenderId();
            $this->lstStatus->SelectedValue = $objEdit->getStatus();

            $this->lstAgeGroup->refresh();
            $this->lstAthleteGender->refresh();
            $this->lstGender->refresh();
            $this->lstStatus->refresh();
        }

        /**
         * Resets the inputs for age group, athlete gender, gender, and status selections to their default values.
         * Also removes any error indicators and refreshes the respective controls.
         *
         * @return void
         */
        public function resetInputs(): void
        {
            $this->lstAgeGroup->SelectedValue = null;
            $this->lstAgeGroup->removeCssClass('has-error');
            $this->lstAthleteGender->SelectedValue = null;
            $this->lstAthleteGender->removeCssClass('has-error');
            $this->lstGender->SelectedValue = null;
            $this->lstGender->removeCssClass('has-error');
            $this->lstStatus->SelectedValue = 2;

            $this->lstAgeGroup->refresh();
            $this->lstAthleteGender->refresh();
            $this->lstGender->refresh();
            $this->lstStatus->refresh();
        }

        /**
         * Updates the AgeCategoryGender object with post-update details, validates input fields,
         * and refreshes the display with updated data. Displays notifications based on validation results.
         *
         * @param object $objAgeCategoryGender The AgeCategoryGender object being updated and validated.
         *
         * @return void
         * @throws \QCubed\Database\Exception\UndefinedPrimaryKey
         * @throws \QCubed\Exception\Caller
         * @throws \QCubed\Exception\InvalidCast
         */
        protected function updateAndValidateAgeGroups(object $objAgeCategoryGender): void
        {
            // Condition for which notification to show
            if (count($this->errors) === 1) {
                $this->dlgToastr3->notify(); // If only one field is invalid
            } elseif (count($this->errors) > 1) {
                $this->dlgToastr4->notify(); // If there is more than one invalid field
            }

            if ($this->blnEditMode === true) {
                $objAgeCategoryGender->setPostUpdateDate(QDateTime::Now());
                $objAgeCategoryGender->setAssignedEditorsNameById($this->intLoggedUserId);
                $objAgeCategoryGender->save();
            }

            $this->calPostDate->Text = $objAgeCategoryGender->PostDate ? $objAgeCategoryGender->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->Text = $objAgeCategoryGender->PostUpdateDate ? $objAgeCategoryGender->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->txtAuthor->Text = $objAgeCategoryGender->Author;
            $this->txtUsersAsEditors->Text = implode(', ', $objAgeCategoryGender->getUserAsEditorsArray());

            $this->refreshDisplay($objAgeCategoryGender->getId());
            unset($this->errors);
        }

        /**
         * Handles the click event for the delete button, determining whether to show
         * a confirmation or restriction dialog based on the locked state of the
         * AgeCategoryGender object.
         *
         * @param ActionParams $params The parameters received from the user interaction that triggered this method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            if ($this->objAgeCategoryGender->getIsLocked() == 1) {
                $this->dlgModal1->showDialogBox();
            } else {
                $this->dlgModal2->showDialogBox();
            }
        }

        /**
         * Handles the delete action for an AgeCategoryGender item, updates related AgeCategories and Genders,
         * and refreshes the display or hides components as necessary.
         *
         * @param ActionParams $params The parameters received from the user interaction that triggered this method.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            $objAgeCategories = AgeCategories::loadByIdFromAgeCategories($this->objAgeCategoryGender->getAgeCategoryId());
            $objGenders = Genders::loadByIdFromGenders($this->objAgeCategoryGender->getGenderId());

            if (AgeCategoryGender::countByAgeCategoryId($this->objAgeCategoryGender->getAgeCategoryId()) == 1) {
                $objAgeCategories->setIsLocked(1);
                $objAgeCategories->save();
            }

            if (AgeCategoryGender::countByAgeCategoryId($this->objAgeCategoryGender->getGenderId()) == 1) {
                $objGenders->setIsLocked(1);
                $objGenders->save();
            }

            $this->objAgeCategoryGender->delete();
            $this->dlgModal1->hideDialogBox();

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden')
            ");

            $this->dtgAgeCategoryGender->removeCssClass('disabled');

            if (AgeCategoryGender::countAll() === 0) {
                $this->dtgAgeCategoryGender->Display = false;
            }

            $this->refreshDisplay($this->objAgeCategoryGender->getId());

            $this->userOptions();
        }

        /**
         * Handles the click event to hide settings and form action wrappers, re-enables the data grid,
         * and hides the dialog box.
         *
         * @param ActionParams $params The parameters received from the user interaction that triggered this method.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function hideItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden')
            ");

            $this->hideUserWindow();

            $this->dtgAgeCategoryGender->removeCssClass('disabled');

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Updates the display of various UI controls based on the state and properties
         * of the given AgeCategoryGender object.
         *
         * @param mixed $objEdit The identifier used to load the corresponding AgeCategoryGender object.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function refreshDisplay(mixed $objEdit): void
        {
            $objAgeCategoryGender = AgeCategoryGender::load($objEdit);

            if (!$objAgeCategoryGender) {
                $this->lblPostDate->Display = false;
                $this->calPostDate->Display = false;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = false;
                $this->txtAuthor->Display = false;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            } else {
                if ($objAgeCategoryGender->getPostDate() &&
                    !$objAgeCategoryGender->getPostUpdateDate() &&
                    $objAgeCategoryGender->getAuthor() &&
                    !$objAgeCategoryGender->countUsersAsEditors()) {
                    $this->lblPostDate->Display = true;
                    $this->calPostDate->Display = true;
                    $this->lblPostUpdateDate->Display = false;
                    $this->calPostUpdateDate->Display = false;
                    $this->lblAuthor->Display = true;
                    $this->txtAuthor->Display = true;
                    $this->lblUsersAsEditors->Display = false;
                    $this->txtUsersAsEditors->Display = false;
                }

                if ($objAgeCategoryGender->getPostDate() &&
                    $objAgeCategoryGender->getPostUpdateDate() &&
                    $objAgeCategoryGender->getAuthor() &&
                    !$objAgeCategoryGender->countUsersAsEditors()) {
                    $this->lblPostDate->Display = true;
                    $this->calPostDate->Display = true;
                    $this->lblPostUpdateDate->Display = true;
                    $this->calPostUpdateDate->Display = true;
                    $this->lblAuthor->Display = true;
                    $this->txtAuthor->Display = true;
                    $this->lblUsersAsEditors->Display = false;
                    $this->txtUsersAsEditors->Display = false;
                }

                if ($objAgeCategoryGender->getPostDate() &&
                    $objAgeCategoryGender->getPostUpdateDate() &&
                    $objAgeCategoryGender->getAuthor() &&
                    $objAgeCategoryGender->countUsersAsEditors()) {
                    $this->lblPostDate->Display = true;
                    $this->calPostDate->Display = true;
                    $this->lblPostUpdateDate->Display = true;
                    $this->calPostUpdateDate->Display = true;
                    $this->lblAuthor->Display = true;
                    $this->txtAuthor->Display = true;
                    $this->lblUsersAsEditors->Display = true;
                    $this->txtUsersAsEditors->Display = true;
                }

                $this->calPostDate->Text = $objAgeCategoryGender->PostDate ? $objAgeCategoryGender->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $this->calPostUpdateDate->Text = $objAgeCategoryGender->PostUpdateDate ? $objAgeCategoryGender->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $this->txtAuthor->Text = $objAgeCategoryGender->Author;
                $this->txtUsersAsEditors->Text = implode(', ', $objAgeCategoryGender->getUserAsEditorsArray());
            }
        }

        /**
         * Resets and hides the user window elements, including clearing text inputs
         * and disabling the display of associated labels and fields.
         *
         * @return void
         */
        protected function hideUserWindow(): void
        {
            $this->calPostDate->Text = '';
            $this->calPostUpdateDate->Text = '';
            $this->txtAuthor->Text = '';
            $this->txtUsersAsEditors->Text = '';

            $this->lblPostDate->Display = false;
            $this->calPostDate->Display = false;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = false;
            $this->txtAuthor->Display = false;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }
    }