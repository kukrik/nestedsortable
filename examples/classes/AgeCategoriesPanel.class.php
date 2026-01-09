<?php

    use QCubed as Q;
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
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;

    /**
     * Class AgeCategoriesPanel
     *
     * This class represents a panel for managing age categories in an application. It provides UI elements, data grid configurations,
     * event handlers, and business logic to handle age-category-related operations such as creating, updating, deleting, and managing inputs.
     * It includes modal popups, alerts, and Toastr notifications for better user interaction.
     */
    class AgeCategoriesPanel extends Panel
    {
        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;
        protected Q\Plugin\Toastr $dlgToastr4;
        protected Q\Plugin\Toastr $dlgToastr5;
        protected Q\Plugin\Toastr $dlgToastr6;
        protected Q\Plugin\Toastr $dlgToastr7;
        protected Q\Plugin\Toastr $dlgToastr8;
        protected Q\Plugin\Toastr $dlgToastr9;
        protected Q\Plugin\Toastr $dlgToastr10;
        protected Q\Plugin\Toastr $dlgToastr11;
        protected Q\Plugin\Toastr $dlgToastr12;
        protected Q\Plugin\Toastr $dlgToastr13;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;
        public Bs\Modal $dlgModal6;

        public Q\Plugin\Control\Alert $lblInfo;
        public Bs\Button $btnAddNewAgeCategory;

        public Q\Plugin\Control\Label $lblClassName;
        public Bs\TextBox $txtClassName;

        public Q\Plugin\Control\Label $lblAgeRange;
        public Bs\TextBox $txtMinAge;
        public Bs\TextBox $txtMaxAge;

        public Q\Plugin\Control\Label $lblDescription;
        public Bs\TextBox $txtDescription;
        public Q\Plugin\Control\Label $lblTitle;
        public Bs\TextBox $txtTitle;
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

        public AgeCategoriesTable $dtgAgeCategories;
        public Bs\Button $btnRefresh;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected bool $blnEditMode = true;
        protected ?object $objAgeCategory = null;
        protected array $errors = []; // Array for tracking errors

        protected string $strTemplate = 'AgeCategoriesPanel.tpl.php';

        /**
         * Constructor for initializing the object and setting up required properties and controls.
         *
         * @param mixed $objParentObject The parent object to which this control belongs.
         *                               Typically, it is a parent form or panel.
         * @param string|null $strControlId An optional control ID, if specified; otherwise, generates a unique ID.
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

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->dtgAgeCategories_Create();

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
         * Initializes and configures the AgeCategories data grid.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgAgeCategories_Create(): void
        {
            $this->dtgAgeCategories = new AgeCategoriesTable($this);
            $this->dtgAgeCategories_CreateColumns();
            $this->dtgAgeCategories_MakeEditable();
            $this->dtgAgeCategories->RowParamsCallback = [$this, "dtgAgeCategories_GetRowParams"];
            $this->dtgAgeCategories->SortColumnIndex = 0;
            $this->dtgAgeCategories->SortDirection = -1;
        }

        /**
         * Creates columns for the Age Categories data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgAgeCategories_CreateColumns(): void
        {
            $this->dtgAgeCategories->createColumns();
        }

        /**
         * Configures the Age Categories data grid to be editable by adding a click action to rows and applying CSS
         * classes for styling and interactivity.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgAgeCategories_MakeEditable(): void
        {
            $this->dtgAgeCategories->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgAgeCategories_Click'));
            $this->dtgAgeCategories->addCssClass('clickable-rows');
            $this->dtgAgeCategories->CssClass = 'table vauu-table js-sports-area table-hover table-responsive';
        }

        /**
         * Handles the click event for the Age Categories data grid. Sets the edit mode
         * and populates form fields with data for the selected Age Category.
         *
         * @param ActionParams $params The action parameters passed during the event, containing details such as the selected item's ID.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgAgeCategories_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->intId = intval($params->ActionParameter);
            $this->objAgeCategory = AgeCategories::load($this->intId);

            if ($this->objAgeCategory->getIsLocked() == 1) {
                $this->btnDelete->Display = true;
            } else {
                $this->btnDelete->Display = false;
            }

            $this->blnEditMode = true;

            $this->dtgAgeCategories->addCssClass('disabled');
            $this->refreshDisplay($this->intId);

            Application::executeJavaScript("
                $('.setting-wrapper').removeClass('hidden');
                $('.form-actions-wrapper').removeClass('hidden');
                $('.js-wrapper-top').get(0).scrollIntoView({behavior: 'smooth'});
            ");

            $this->activeInputs($this->objAgeCategory);
            $this->checkInputs();
        }

        /**
         * Generates an array of row parameters for the Age Categories data table grid.
         *
         * @param object $objRowObject The object representing a row in the data table grid.
         * @param int $intRowIndex The index of the current row.
         *
         * @return array An associative array of parameters for the specified row.
         */
        public function dtgAgeCategories_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();

            $params['data-value'] = $strKey;
            return $params;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures form controls and UI elements for managing inputs related to age categories.
         *
         * This method dynamically creates elements such as labels, text boxes, and radio buttons with pre-defined
         * behaviors and appearance. Depending on the existence of age categories, it toggles the visibility of
         * certain elements. It also sets up event handlers for keyboard actions (e.g., Enter key or Escape key)
         * to streamline user interactions.
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

            $countAgeCategories = AgeCategories::countAll();

            if ($countAgeCategories === 0) {
                $this->lblInfo->Display = true;
                $this->dtgAgeCategories->Display = false;
            } else {
                $this->lblInfo->Display = false;
                $this->dtgAgeCategories->Display = true;
            }

            $this->lblClassName = new Q\Plugin\Control\Label($this);
            $this->lblClassName->Text = t('Age group');
            $this->lblClassName->addCssClass('col-md-4');
            $this->lblClassName->setCssStyle('font-weight', 'normal');
            $this->lblClassName->Required = true;

            $this->txtClassName = new Bs\TextBox($this);
            $this->txtClassName->Placeholder = t('Age group');
            $this->txtClassName->setHtmlAttribute('autocomplete', 'off');
            $this->txtClassName->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtClassName->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtClassName->addAction(new EnterKey(), new Terminate());
            $this->txtClassName->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtClassName->addAction(new EscapeKey(), new Terminate());

            $this->lblAgeRange = new Q\Plugin\Control\Label($this);
            $this->lblAgeRange->Text = t('Age range');
            $this->lblAgeRange->addCssClass('col-md-4');
            $this->lblAgeRange->setCssStyle('font-weight', 'normal');
            $this->lblAgeRange->Required = true;

            $this->txtMinAge = new Bs\TextBox($this);
            $this->txtMinAge->Placeholder = t('Min age');
            $this->txtMinAge->setHtmlAttribute('autocomplete', 'off');
            $this->txtMinAge->setCssStyle('float', 'left');
            $this->txtMinAge->setCssStyle('margin-right', '10px');
            $this->txtMinAge->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtMinAge->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtMinAge->addAction(new EnterKey(), new Terminate());
            $this->txtMinAge->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtMinAge->addAction(new EscapeKey(), new Terminate());
            $this->txtMinAge->Width = 100;

            $this->txtMaxAge = new Bs\TextBox($this);
            $this->txtMaxAge->Placeholder = t('Max age');
            $this->txtMaxAge->setHtmlAttribute('autocomplete', 'off');
            $this->txtMaxAge->setCssStyle('float', 'left');
            $this->txtMaxAge->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtMaxAge->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtMaxAge->addAction(new EnterKey(), new Terminate());
            $this->txtMaxAge->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtMaxAge->addAction(new EscapeKey(), new Terminate());
            $this->txtMaxAge->Width = 100;

            $this->lblDescription = new Q\Plugin\Control\Label($this);
            $this->lblDescription->Text = t('Description');
            $this->lblDescription->addCssClass('col-md-4');
            $this->lblDescription->setCssStyle('font-weight', 'normal');

            $this->txtDescription = new Bs\TextBox($this);
            $this->txtDescription->Placeholder = t('Description');
            $this->txtDescription->setHtmlAttribute('autocomplete', 'off');
            $this->txtDescription->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtDescription->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtDescription->addAction(new EnterKey(), new Terminate());
            $this->txtDescription->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtDescription->addAction(new EscapeKey(), new Terminate());

            $this->lblTitle = new Q\Plugin\Control\Label($this);
            $this->lblTitle->Text = t('Title');
            $this->lblTitle->addCssClass('col-md-4');
            $this->lblTitle->setCssStyle('font-weight', 'normal');

            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Title');
            $this->txtTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
            $this->txtTitle->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtTitle->addAction(new EnterKey(), new Terminate());
            $this->txtTitle->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtTitle->addAction(new EscapeKey(), new Terminate());

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
         * Creates and initializes a set of buttons with specific properties and actions.
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

            $this->btnAddNewAgeCategory = new Bs\Button($this);
            $this->btnAddNewAgeCategory->Text = t('Add a new age group');
            $this->btnAddNewAgeCategory->CssClass = 'btn btn-orange';
            $this->btnAddNewAgeCategory->CausesValidation = false;
            $this->btnAddNewAgeCategory->addAction(new Click(), new AjaxControl($this, 'btnAddNewAgeCategory_Click'));

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
         * Creates and configures multiple Toastr alert instances for various messages and notifications.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('Class name is required!');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('Minimum age is required!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('Maximum age is required!');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('This new age group already exists in the database!');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('Minimum age cannot be greater than maximum age!');
            $this->dlgToastr5->ProgressBar = true;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('The minimum age cannot be equal to the maximum age!');
            $this->dlgToastr6->ProgressBar = true;

            $this->dlgToastr7 = new Q\Plugin\Toastr($this);
            $this->dlgToastr7->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr7->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr7->Message = t('The minimum and maximum age for the age group must be provided together!');
            $this->dlgToastr7->ProgressBar = true;

            //////////////////////////////////////

            $this->dlgToastr8 = new Q\Plugin\Toastr($this);
            $this->dlgToastr8->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr8->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr8->Message = t('<strong>Well done!</strong> The new age group was successfully added to the database!');
            $this->dlgToastr8->ProgressBar = true;

            $this->dlgToastr9 = new Q\Plugin\Toastr($this);
            $this->dlgToastr9->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr9->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr9->Message = t('The age group data was saved or modified!');
            $this->dlgToastr9->ProgressBar = true;

            $this->dlgToastr10 = new Q\Plugin\Toastr($this);
            $this->dlgToastr10->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr10->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr10->Message = t('<strong>Well done!</strong> This age group with data is now active!');
            $this->dlgToastr10->ProgressBar = true;

            $this->dlgToastr11 = new Q\Plugin\Toastr($this);
            $this->dlgToastr11->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr11->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr11->Message = t('<strong>Well done!</strong> This age group with data is now inactive!');
            $this->dlgToastr11->ProgressBar = true;

            //////////////////////////////////////

            $this->dlgToastr12 = new Q\Plugin\Toastr($this);
            $this->dlgToastr12->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr12->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr12->Message = t('Updates to some records for this age group were discarded, and the age group has been restored!');
            $this->dlgToastr12->ProgressBar = true;

            $this->dlgToastr13 = new Q\Plugin\Toastr($this);
            $this->dlgToastr13->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr13->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr13->Message = t('The table has been updated!');
            $this->dlgToastr13->ProgressBar = true;
        }

        /**
         * Initializes and configures multiple modal dialogs used within the application.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the age group?</p>
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This age group cannot be deleted 
                                        as it is locked in the records table or leaderboard!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                        and both tables will be updated automatically.</p>');

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The status of this age group cannot 
                                        be deactivated as it is locked in the records table or leaderboard!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                        and both tables will be updated automatically.</p>');

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->HeaderClasses = 'btn-darkblue';
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">One or the other input of this age 
                                        group must not be left blank, as it is already locked in the records or leaderboards.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                        and both tables will be updated automatically.</p>');

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Tip");
            $this->dlgModal5->HeaderClasses = 'btn-darkblue';
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">Only one age group is allowed to have no maximum age defined.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">If you wish to add a new age group without a maximum age, 
                                        you must first assign a numeric value to the "Max age" field of the existing entry.</p>');
            
            // Lubatud on ainult üks vanusegrupp, millel pole määratud maksimum vanust.
            // Kui soovid lisada uue sellise vanusegrupi, siis muuda esmalt olemasoleva kirje "Max vanus" lahtrisse konkreetne arvuline väärtus.
            
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
         * Handles the click event for the refresh button and performs the necessary actions.
         *
         * @param ActionParams $params The parameters associated with the triggered action.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnRefresh_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->dtgAgeCategories->refresh();

            $this->dlgToastr13->notify();
        }

        /**
         * Handles the click event for the "Add New Age Category" button, initializing the form for adding a
         * new age category and updating the user interface appropriately.
         *
         * @param ActionParams $params The parameters related to the action event triggered.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function btnAddNewAgeCategory_Click(ActionParams $params): void
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

            $this->txtClassName->Text = '';
            $this->txtClassName->focus();
            $this->dtgAgeCategories->addCssClass('disabled');

            $this->btnSave->Display = true;
            $this->btnDelete->Display = false;
            $this->blnEditMode = false;

            $this->resetInputs();
        }

        /**
         * Handles the change event for the status dropdown list and performs relevant updates and validations.
         *
         * @param ActionParams $params The parameters associated with the triggered action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === true) {
                $this->checkInputs();

                if (count($this->errors)) {
                    $this->lstStatus->SelectedValue = 2;
                    $this->objAgeCategory->setStatus(2);
                } else {
                    if ($this->objAgeCategory->getIsLocked() === 1) {
                        if ($this->lstStatus->SelectedValue == 1) {
                            $this->objAgeCategory->setStatus(1);
                            $this->dlgToastr10->notify();
                        } else {
                            $this->objAgeCategory->setStatus(2);
                            $this->dlgToastr11->notify();
                        }
                    } else {
                        $this->dlgModal3->showDialogBox();
                        $this->lstStatus->SelectedValue = 1;
                    }
                }

                $this->objAgeCategory->save();

                $this->updateAndValidateAgeGroups($this->objAgeCategory);
            }

            $this->userOptions();
        }

        /**
         * Handles the save button click event for managing age categories.
         *
         * This method performs validation, input processing, object initialization,
         * and saving/updating of AgeCategories data. It also handles notifications
         * and UI updates based on the current edit mode and input conditions.
         *
         * @param ActionParams $params The parameters passed by the action triggering the save button click.
         *
         * @return void This method does not return any value.
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

            if ($this->txtMinAge->Text && ($this->txtMaxAge->Text === '' || $this->txtMaxAge->Text === null)) {
                if (AgeCategories::hasExistingNullMaxAge()) {
                    if ($this->blnEditMode === true) {
                        $this->txtMaxAge->Text = $this->objAgeCategory->getMaxAge();
                    } else {
                        $this->txtMaxAge->setHtmlAttribute('required', 'required');
                        $this->txtMaxAge->Text = '';
                        $this->lstStatus->SelectedValue = 2;
                    }
                    $this->dlgModal5->showDialogBox();
                    return;
                }
            }

            if (($this->blnEditMode === false) || ($this->objAgeCategory->getIsLocked() === 1)) {
                if ($this->txtClassName->Text) {
                    if ((!$this->txtMinAge->Text && !$this->txtMaxAge->Text)) {
                        $this->dlgToastr7->notify();
                        return;
                    }

                    if (!$this->txtMinAge->Text) {
                        $this->txtMinAge->Text = '';
                        $this->dlgToastr2->notify();
                        return;
                    }

                    if ((int)$this->txtMinAge->Text === (int)$this->txtMaxAge->Text) {
                        $this->txtMinAge->Text = '';
                        $this->txtMaxAge->Text = '';
                        $this->dlgToastr6->notify();
                        return;
                    }

                    if ($this->txtMinAge->Text && $this->txtMaxAge->Text) {
                        if ((int)$this->txtMinAge->Text > (int)$this->txtMaxAge->Text) {
                            $this->txtMinAge->Text = '';
                            $this->txtMaxAge->Text = '';
                            $this->dlgToastr5->notify();
                            return;
                        }
                    }
                } else {
                    foreach ($this->errors as $error) {
                        if ($error === 'txtClassName') {
                            $this->dlgToastr1->notify();
                        }

                        if ($error === 'txtMinAge') {
                            $this->dlgToastr2->notify();
                        }

                        if ($error === 'txtMaxAge') {
                            $this->dlgToastr3->notify();
                        }
                    }
                }

                if (!$this->txtClassName->Text) {
                    $this->txtClassName->focus();
                } else if (!$this->txtMinAge->Text) {
                    $this->txtMinAge->focus();
                } else if (!$this->txtMaxAge->Text) {
                    $this->txtMaxAge->focus();
                }
            }

            if ($this->blnEditMode === false) {
                if (!AgeCategories::classExists(trim($this->txtClassName->Text))) {
                    if ($this->txtClassName->Text && $this->txtMinAge->Text && !AgeCategories::hasExistingNullMaxAge()) {
                        $objAgeCategory = new AgeCategories();
                        $this->saveInputs($objAgeCategory);
                        $objAgeCategory->setPostDate(QDateTime::now());
                        $objAgeCategory->setAssignedByUser($this->intLoggedUserId);
                        $objAgeCategory->setAuthor($objAgeCategory->getAssignedByUserObject());
                        $objAgeCategory->save();

                        Application::executeJavaScript("
                            $('.setting-wrapper').addClass('hidden');
                            $('.form-actions-wrapper').addClass('hidden');
                        ");

                        $this->hideUserWindow();
                        $this->dtgAgeCategories->removeCssClass('disabled');

                        $this->dlgToastr8->notify();
                    }
                } else {
                    $this->txtClassName->Text = '';
                    $this->dlgToastr5->notify();
                }
            } else {
                if ($this->objAgeCategory->getIsLocked() === 2) { // LOCKED
                    if (!$this->txtClassName->Text || !$this->txtMinAge->Text || !$this->txtMaxAge->Text) {
                        $this->activeInputs($this->objAgeCategory);
                        $this->dlgModal4->showDialogBox();
                    } else if ($this->txtClassName->Text !== $this->objAgeCategory->getClassName()) {
                        if (AgeCategories::classExists(trim($this->txtClassName->Text))) {
                            $this->activeInputs($this->objAgeCategory);
                            $this->dlgToastr4->notify();
                        } else {
                            $this->objAgeCategory->setClassName(trim($this->txtClassName->Text));
                            $this->dlgToastr9->notify();
                        }
                    } else if ($this->txtClassName->Text &&
                        (int)$this->txtMinAge->Text === (int)$this->txtMaxAge->Text) {
                        $this->activeInputs($this->objAgeCategory);
                        $this->dlgToastr6->notify();
                    } else if ($this->txtClassName->Text &&
                        (int)$this->txtMinAge->Text > (int)$this->txtMaxAge->Text) {
                        $this->activeInputs($this->objAgeCategory);
                        $this->dlgToastr5->notify();
                    } else {
                        $this->dlgToastr9->notify();
                    }

                    $this->lstStatus->SelectedValue = 1;

                } else { // NOT LOCKED
                    if (count($this->errors)) {
                        $this->lstStatus->SelectedValue = 2;
                    } else {
                        $this->dlgToastr9->notify();
                    }

                    $this->saveInputs($this->objAgeCategory);
                    $this->objAgeCategory->save();
                    $this->updateAndValidateAgeGroups($this->objAgeCategory);
                }
            }

            unset($this->errors);

            $this->userOptions();
        }

        /**
         * Handles the click event for the Cancel button, performing UI updates and state resets.
         *
         * @param ActionParams $params The parameters associated with the action event of the button click.
         *
         * @return void This method does not return any value.
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

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden')
            ");

            $this->userOptions();

            $this->hideUserWindow();

            $this->dtgAgeCategories->removeCssClass('disabled');
            unset($this->errors);
        }

        /**
         * Handles the click event for an item and populates relevant form fields
         * with data loaded from the selected Age Category.
         *
         * @param ActionParams $params The parameters passed for the click action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->txtClassName->Text = $this->objAgeCategory->getClassName();
            $this->txtMinAge->Text = $this->objAgeCategory->getMinAge();
            $this->txtMaxAge->Text = $this->objAgeCategory->getMaxAge();
            $this->txtDescription->Text = $this->objAgeCategory->getDescription();
            $this->txtTitle->Text = $this->objAgeCategory->getTitle();

            $this->dlgToastr12->notify();
        }

        /**
         * Validates input fields and sets error messages for any missing required fields.
         *
         * @return void
         * @throws Caller
         */
        public function checkInputs(): void
        {
            // We check each field and add errors if necessary
            if (!$this->txtClassName->Text) {
                $this->txtClassName->setHtmlAttribute('required', 'required');
                $this->errors[] = 'txtClassName';
            } else {
                $this->txtClassName->removeHtmlAttribute('required');
            }

            if (!$this->txtMinAge->Text) {
                $this->txtMinAge->setHtmlAttribute('required', 'required');
                $this->errors[] = 'txtMinAge';
            } else {
                $this->txtMinAge->removeHtmlAttribute('required');
            }

            if (!$this->txtMaxAge->Text) {
                if (AgeCategories::isMaxMinAge((int)$this->txtMaxAge->Text)) {
                    $this->txtMaxAge->setHtmlAttribute('required', 'required');
                    $this->errors[] = 'txtMaxAge';
                } else {
                    $this->txtMaxAge->removeHtmlAttribute('required');
                }
            }
        }

        /**
         * Populates input fields with data from the given edit object.
         *
         * @param object $objEdit The object containing data to populate the input fields.
         *
         * @return void
         */
        public function activeInputs(object $objEdit): void
        {
            $this->txtClassName->Text = $objEdit->getClassName() ?? '';
            $this->txtMinAge->Text = $objEdit->getMinAge() ?? '';
            $this->txtMaxAge->Text = $objEdit->getMaxAge() ?: '';
            $this->txtDescription->Text = $objEdit->getDescription() ?? '';
            $this->txtTitle->Text = $objEdit->getTitle() ?? '';
            $this->lstStatus->SelectedValue = $objEdit->getStatus();
            $this->lstStatus->refresh();
        }

        /**
         * Saves input values to the provided Age Category object.
         *
         * @param object $objAgeCategory The Age Category objects to be updated with input data.
         *
         * @return void
         */
        public function saveInputs(object $objAgeCategory): void
        {
            $objAgeCategory->setClassName($this->txtClassName->Text);
            $objAgeCategory->setMinAge((int)$this->txtMinAge->Text);
            $objAgeCategory->setMaxAge($this->txtMaxAge->Text ?: null);
            $objAgeCategory->setDescription($this->txtDescription->Text);
            $objAgeCategory->setTitle($this->txtTitle->Text);
            $objAgeCategory->setStatus($this->lstStatus->SelectedValue);
        }

        /**
         * Resets the input fields to their default values and removes specific HTML attributes.
         *
         * @return void
         */
        public function resetInputs(): void
        {
            $this->txtClassName->Text = '';
            $this->txtClassName->removeHtmlAttribute('required');
            $this->txtMinAge->Text = '';
            $this->txtMinAge->removeHtmlAttribute('required');
            $this->txtMaxAge->Text = '';
            $this->txtMaxAge->removeHtmlAttribute('required');
            $this->txtDescription->Text = '';
            $this->txtTitle->Text = '';

            $this->lstStatus->SelectedValue = 2;
            $this->lstStatus->refresh();
        }

        /**
         * Updates and validates the specified age category and refreshes the display with updated data.
         *
         * @param object $objAgeCategory The age category object to update and validate.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function updateAndValidateAgeGroups(object $objAgeCategory): void
        {
            $objAgeCategory->setPostUpdateDate(QDateTime::now());
            $objAgeCategory->setAssignedEditorsNameById($this->intLoggedUserId);
            $objAgeCategory->save();

            $this->calPostDate->Text = $objAgeCategory->PostDate ? $objAgeCategory->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->Text = $objAgeCategory->PostUpdateDate ? $objAgeCategory->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->txtAuthor->Text = $objAgeCategory->Author;
            $this->txtUsersAsEditors->Text = implode(', ', $objAgeCategory->getUserAsEditorsArray());

            $this->refreshDisplay($objAgeCategory->getId());
        }

        /**
         * Handles the delete button, click event and performs actions based on the lock status of an age category.
         *
         * @param ActionParams $params The parameters associated with the button click event.
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

            $this->userOptions();

            if ($this->objAgeCategory->getIsLocked() == 1) {
                $this->dlgModal1->showDialogBox();
            } else {
                $this->dlgModal2->showDialogBox();
            }
        }

        /**
         * Handles the click event to delete an item from the Age Categories and updates the UI accordingly.
         *
         * @param ActionParams $params Parameters associated with the action triggering this method.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            $this->objAgeCategory->delete();
            $this->dlgModal1->hideDialogBox();

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden')
            ");

            $this->dtgAgeCategories->removeCssClass('disabled');

            $this->refreshDisplay($this->objAgeCategory->getId());

            $this->userOptions();
        }

        /**
         * Handles the click action for hiding specific UI elements and performing related updates.
         *
         * @param ActionParams $params Parameters associated with the click action, typically including context or event-related data.
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

            $this->dtgAgeCategories->removeCssClass('disabled');

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Updates the display controls on the interface based on the input ID of the editable entity.
         *
         * @param int $objEdit The identifier for the AgeCategories object to be loaded and evaluated.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function refreshDisplay(int $objEdit): void
        {
            $objAgeCategory = AgeCategories::load($objEdit);

            if (!$objAgeCategory) {
                $this->lblPostDate->Display = false;
                $this->calPostDate->Display = false;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = false;
                $this->txtAuthor->Display = false;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            } else {
                if ($objAgeCategory->getPostDate() &&
                    !$objAgeCategory->getPostUpdateDate() &&
                    $objAgeCategory->getAuthor() &&
                    !$objAgeCategory->countUsersAsEditors()) {
                    $this->lblPostDate->Display = true;
                    $this->calPostDate->Display = true;
                    $this->lblPostUpdateDate->Display = false;
                    $this->calPostUpdateDate->Display = false;
                    $this->lblAuthor->Display = true;
                    $this->txtAuthor->Display = true;
                    $this->lblUsersAsEditors->Display = false;
                    $this->txtUsersAsEditors->Display = false;
                }

                if ($objAgeCategory->getPostDate() &&
                    $objAgeCategory->getPostUpdateDate() &&
                    $objAgeCategory->getAuthor() &&
                    !$objAgeCategory->countUsersAsEditors()) {
                    $this->lblPostDate->Display = true;
                    $this->calPostDate->Display = true;
                    $this->lblPostUpdateDate->Display = true;
                    $this->calPostUpdateDate->Display = true;
                    $this->lblAuthor->Display = true;
                    $this->txtAuthor->Display = true;
                    $this->lblUsersAsEditors->Display = false;
                    $this->txtUsersAsEditors->Display = false;
                }

                if ($objAgeCategory->getPostDate() &&
                    $objAgeCategory->getPostUpdateDate() &&
                    $objAgeCategory->getAuthor() &&
                    $objAgeCategory->countUsersAsEditors()) {
                    $this->lblPostDate->Display = true;
                    $this->calPostDate->Display = true;
                    $this->lblPostUpdateDate->Display = true;
                    $this->calPostUpdateDate->Display = true;
                    $this->lblAuthor->Display = true;
                    $this->txtAuthor->Display = true;
                    $this->lblUsersAsEditors->Display = true;
                    $this->txtUsersAsEditors->Display = true;
                }

                $this->calPostDate->Text = $objAgeCategory->PostDate ? $objAgeCategory->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $this->calPostUpdateDate->Text = $objAgeCategory->PostUpdateDate ? $objAgeCategory->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $this->txtAuthor->Text = $objAgeCategory->Author;
                $this->txtUsersAsEditors->Text = implode(', ', $objAgeCategory->getUserAsEditorsArray());
            }
        }

        /**
         * Hides the user window by resetting form elements and toggling the visibility of related labels and inputs.
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