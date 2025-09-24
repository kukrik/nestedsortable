<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
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
     * Represents the GendersPanel class that extends the base Panel class.
     *
     * Provides functionalities for managing gender records through data tables, input controls, and modals.
     * Includes components such as data grids for viewing and editing genders, input forms for adding or updating information,
     * and actionable buttons for saving, deleting, or canceling changes.
     *
     * This class is designed with various features and components that operate in concert to achieve a CRUD (Create, Read, Update, Delete) workflow.
     */
    class GendersPanel extends Panel
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

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;

        public Q\Plugin\Control\Alert $lblInfo;
        public Bs\Button $btnAddNewGender;

        public Q\Plugin\Control\Label $lblName;
        public Bs\TextBox $txtName;
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

        public GendersTable $dtgGenders;

        protected int $intId;
        protected bool $blnEditMode = true;
        protected object $objUser;
        protected int $intLoggedUserId;
        protected ?object $objGender = null;

        protected string $strTemplate = 'GendersPanel.tpl.php';

        /**
         * Constructor for the class.
         *
         * Initializes the control with a parent object and an optional control ID.
         * Performs setup of user-related data by retrieving the logged user's information
         * based on a session or predefined setup. Additionally, it initializes required components
         * such as input fields, buttons, notifications, and modals.
         *
         * @param mixed $objParentObject The parent object that will own this control.
         * @param string|null $strControlId Optional control ID for this object.
         *
         * @return void
         * @throws Caller Throws an exception if there's an issue with the parent constructor call.
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

            $this->dtgGenders_Create();

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and initializes the Genders data table.
         * Sets up columns, enables editing capabilities, configures row parameters callback,
         * and defines default sorting behavior.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgGenders_Create(): void
        {
            $this->dtgGenders = new GendersTable($this);
            $this->dtgGenders_CreateColumns();
            $this->dtgGenders_MakeEditable();
            $this->dtgGenders->RowParamsCallback = [$this, "dtgGenders_GetRowParams"];
            $this->dtgGenders->SortColumnIndex = 0;
            $this->dtgGenders->SortDirection = -1;
        }

        /**
         * Initializes and creates the columns for the dtgGenders data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgGenders_CreateColumns(): void
        {
            $this->dtgGenders->createColumns();
        }

        /**
         * Configures the "dtgGenders" data grid to be editable by enabling row click functionality.
         * Adds a click event to each row, making it interactive and creating a visual indication of rows being
         * clickable. Applies appropriate CSS classes for styling and interactivity.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgGenders_MakeEditable(): void
        {
            $this->dtgGenders->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgGenders_Click'));
            $this->dtgGenders->addCssClass('clickable-rows');
            $this->dtgGenders->CssClass = 'table vauu-table js-sports-area table-hover table-responsive';
        }

        /**
         * Handles the click event for the datagrid of genders.
         *
         * Updates internal state variables, toggles UI elements based on the selected gender's properties,
         * and refreshes the display to reflect the selected gender's details.
         *
         * @param ActionParams $params Parameters containing the action's information, including the clicked item's ID.
         *
         * @return void This method modifies the UI and state without returning any value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgGenders_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);
            $this->objGender = Genders::load($this->intId);

            if ($this->objGender->getIsLocked() == 1) {
                $this->btnDelete->Display = true;
            } else {
                $this->btnDelete->Display = false;
            }

            $this->blnEditMode = true;

            $this->dtgGenders->addCssClass('disabled');
            $this->refreshDisplay($this->intId);

            Application::executeJavaScript("
                $('.setting-wrapper').removeClass('hidden');
                $('.form-actions-wrapper').removeClass('hidden');
                $('.js-wrapper-top').get(0).scrollIntoView({behavior: 'smooth'});
            ");

            $this->txtName->Text = $this->objGender->getName();
            $this->lstStatus->SelectedValue = $this->objGender->getStatus();
        }

        /**
         * Generates row parameters for a data grid row based on the provided object and row index.
         *
         * @param object $objRowObject The row object representing the data for the current row. It must have a `primaryKey()` method to retrieve the primary key value.
         * @param int $intRowIndex The zero-based index of the row in the data grid.
         *
         * @return array An associative array of parameters, including the primary key of the row object as 'data-value'.
         */
        public function dtgGenders_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();

            $params['data-value'] = $strKey;
            return $params;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes various input controls and labels for the application.
         *
         * Creates and configures alert messages, labels, textboxes, radiobutton lists,
         * and other controls used for data input and display. Adjusts the display
         * settings of components based on specific conditions, such as the presence of data.
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
            $this->lblInfo->Text = t('Please create the first gender!');
            $this->lblInfo->setCssStyle('margin-bottom', 0);

            $countGenders = Genders::countAll();

            if ($countGenders === 0) {
                $this->lblInfo->Display = true;
                $this->dtgGenders->Display = false;
            } else {
                $this->lblInfo->Display = false;
                $this->dtgGenders->Display = true;
            }

            $this->lblName = new Q\Plugin\Control\Label($this);
            $this->lblName->Text = t('Name');
            $this->lblName->addCssClass('col-md-4');
            $this->lblName->setCssStyle('font-weight', 'normal');
            $this->lblName->Required = true;

            $this->txtName = new Bs\TextBox($this);
            $this->txtName->Placeholder = t('Gender name');
            $this->txtName->setHtmlAttribute('autocomplete', 'off');
            $this->txtName->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtName->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtName->addAction(new EnterKey(), new Terminate());
            $this->txtName->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtName->addAction(new EscapeKey(), new Terminate());

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
         * Creates buttons for adding a new gender, saving, deleting, and canceling.
         * The buttons are configured with appropriate text, CSS classes, validation settings,
         * and actions to handle click events.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnAddNewGender = new Bs\Button($this);
            $this->btnAddNewGender->Text = t('Add a new gender');
            $this->btnAddNewGender->CssClass = 'btn btn-orange';
            $this->btnAddNewGender->CausesValidation = false;
            $this->btnAddNewGender->addAction(new Click(), new AjaxControl($this, 'btnAddNewGender_Click'));

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
         * Creates and configures toastr notifications for various scenarios such as success, error, and info.
         * Each toastr is configured with an alert type, position, message, and progress bar visibility.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The new gender was successfully added to the database.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('Failed to add the new gender');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('This new gender already exists in the database!');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('The gender data was saved or modified!');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('This field is required!');
            $this->dlgToastr5->ProgressBar = true;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('These fields must be filled!');
            $this->dlgToastr6->ProgressBar = true;

            $this->dlgToastr7 = new Q\Plugin\Toastr($this);
            $this->dlgToastr7->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr7->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr7->Message = t('The gender name update was discarded, and the gender name has been restored!');
            $this->dlgToastr7->ProgressBar = true;

            $this->dlgToastr8 = new Q\Plugin\Toastr($this);
            $this->dlgToastr8->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr8->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr8->Message = t('<strong>Well done!</strong> This gender with data is now active!');
            $this->dlgToastr8->ProgressBar = true;

            $this->dlgToastr9 = new Q\Plugin\Toastr($this);
            $this->dlgToastr9->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr9->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr9->Message = t('<strong>Well done!</strong> This gender with data is now inactive!');
            $this->dlgToastr9->ProgressBar = true;
        }

        /**
         * Initializes and configures multiple modal dialogs.
         *
         * This method creates and sets up several modal instances with specific titles,
         * properties, and actions. Each modal serves a distinct purpose, such as displaying
         * warnings, providing tips, or notifying users of specific conditions.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the gender?</p>
                                        <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This gender cannot be deleted 
                                        as it is locked in the records table or leaderboard!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                        and both tables will be updated automatically.</p>');

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The status of this gender cannot 
                                        be deactivated as it is locked in the records table or leaderboard!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                        and both tables will be updated automatically.</p>');

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->HeaderClasses = 'btn-darkblue';
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">When completing a new gender, 
                                        please make sure this field are filled!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">Activating the status is optional!</p>');

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal5->Title = t("Warning");
            $this->dlgModal5->HeaderClasses = 'btn-danger';
            $this->dlgModal5->addCloseButton(t("I understand"));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the event when the "Add New Gender" button is clicked.
         *
         * This method modifies the UI to allow the user to add a new gender entry.
         * It updates the appearance of certain elements, prepares the form for
         * new input, and ensures the application is in creation mode.
         *
         * @param ActionParams $params The parameters associated with the action triggering this event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function btnAddNewGender_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.setting-wrapper').removeClass('hidden');
                $('.form-actions-wrapper').removeClass('hidden');
            ");

            $this->dtgGenders->addCssClass('disabled');
            $this->btnSave->Display = true;
            $this->btnDelete->Display = false;
            $this->blnEditMode = false;

            $this->txtName->Text = '';
            $this->txtName->focus();
            $this->lstStatus->SelectedValue = 2;
        }

        /**
         * Handles the change event for the status dropdown list.
         *
         * This method processes the status change for a selected gender record. Depending on the
         * current edit mode and the lock status of the gender, it validates, updates, and saves
         * the status of the gender while triggering corresponding notifications or dialog boxes.
         * Ensures proper validation and updates related attributes or displays modals if conditions
         * are not met.
         *
         * @param ActionParams $params The parameters related to the action triggering this method.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === true) {
                if ($this->objGender->getIsLocked() == 1) {
                    if ($this->lstStatus->SelectedValue == 1) {
                        $this->objGender->setStatus(1);
                        $this->dlgToastr8->notify();
                    } else {
                        $this->objGender->setStatus(2);
                        $this->dlgToastr9->notify();
                    }

                    $this->updateAndValidateAgeGroups($this->objGender);
                } else {
                    $this->dlgModal3->showDialogBox();
                    $this->lstStatus->SelectedValue = 1;
                }

                $this->objGender->save();
            } else {
                if (!$this->txtName->Text) {
                    $this->txtName->setHtmlAttribute('required', 'required');

                    $this->txtName->Text = '';
                    $this->lstStatus->SelectedValue = 2;
                    $this->dlgModal4->showDialogBox();
                }
            }
        }

        /**
         * Handles the save button click event to create or update a gender record.
         *
         * This method processes the user input and executes appropriate logic for creating
         * or updating a gender entity. It validates input, prevents duplicate entries,
         * and updates the UI or displays notifications based on the outcome.
         *
         * @param ActionParams $params Includes dynamic parameters related to the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->blnEditMode === true) {
                if ($this->objGender->getIsLocked() == 1) {
                    if ($this->txtName->Text) {
                        $this->objGender->setName($this->txtName->Text);
                        $this->objGender->setStatus($this->lstStatus->SelectedValue);
                        $this->objGender->save();

                        $this->updateAndValidateAgeGroups($this->objGender);
                        $this->dlgToastr4->notify();
                    } else {
                        $this->txtName->setHtmlAttribute('required', 'required');
                        $this->txtName->Text = '';
                        $this->lstStatus->SelectedValue = 2;
                        $this->dlgModal4->showDialogBox();
                    }
                } else { // LOCKED 2
                    if ($this->txtName->Text) {
                        $this->objGender->setName($this->txtName->Text);
                        $this->objGender->save();

                        Application::executeJavaScript("
                            $('.setting-wrapper').addClass('hidden');
                            $('.form-actions-wrapper').addClass('hidden');
                        ");

                        $this->lstStatus->SelectedValue  = $this->objGender->getStatus();
                        $this->updateAndValidateAgeGroups($this->objGender);
                        $this->hideUserWindow();
                        $this->dtgGenders->removeCssClass('disabled');

                        $this->dlgToastr4->notify();
                    } else {
                        $this->txtName->setHtmlAttribute('required', 'required');
                        $this->txtName->Text = '';
                        $this->lstStatus->SelectedValue = 2;
                        $this->dlgModal4->showDialogBox();
                    }
                }
            } else { // $this->blnEditMode === false
                if ($this->txtName->Text) {
                    if (!Genders::nameExists(trim($this->txtName->Text))) {
                        $objGender = new Genders();
                        $objGender->setName($this->txtName->Text);
                        $objGender->setStatus($this->lstStatus->SelectedValue);
                        $objGender->setPostDate(Q\QDateTime::now());
                        $objGender->setAssignedByUser($this->intLoggedUserId);
                        $objGender->setAuthor($objGender->getAssignedByUserObject());
                        $objGender->save();

                        Application::executeJavaScript("
                            $('.setting-wrapper').addClass('hidden');
                            $('.form-actions-wrapper').addClass('hidden');
                        ");

                        $this->updateAndValidateAgeGroups($this->objGender);
                        $this->hideUserWindow();
                        $this->dtgGenders->removeCssClass('disabled');

                        $this->dlgToastr1->notify();
                    } else {
                        $this->txtName->Text = '';
                        $this->txtName->focus();
                        $this->lstStatus->SelectedValue = 2;
                        $this->dlgToastr5->notify();
                    }
                } else {
                    $this->txtName->setHtmlAttribute('required', 'required');
                    $this->txtName->Text = '';
                    $this->lstStatus->SelectedValue = 2;
                    $this->dlgToastr5->notify();
                }
            }
        }

        /**
         * Handles the cancel button click event.
         *
         * This method executes JavaScript to hide certain UI elements, resets various form fields,
         * and modifies the state of controls within the user interface. It is typically used
         * to reset the input form and UI state when the user cancels an operation.
         *
         * @param ActionParams $params Parameters associated with the action that triggered the event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden')
            ");

            $this->hideUserWindow();

            $this->dtgGenders->removeCssClass('disabled');
            $this->txtName->removeHtmlAttribute('required');

            $this->txtName->Text = '';
            $this->lstStatus->SelectedValue = 2;
        }

        /**
         * Handles the item escape click event.
         *
         * This method checks the CSRF token validity and resets it if invalid.
         * If the token is valid, it proceeds to update a text field with the
         * name of the `Gender` object and displays a notification.
         *
         * @param ActionParams $params Holds parameters related to the triggered action.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->txtName->Text = $this->objGender->getName();
            $this->dlgToastr7->notify();
        }

        /**
         * Updates and validates age group data for a given gender object and refreshes the display.
         *
         * This method updates the post-update date, assigns the editor's name,
         * and saves changes to the provided gender object. Afterward, it refreshes
         * several display fields, including post-dates, author information, and editors'
         * list for the updated gender.
         *
         * @param object $objGender The gender object being updated, validated, and saved.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function updateAndValidateAgeGroups(object $objGender): void
        {
            $objGender->setPostUpdateDate(Q\QDateTime::now());
            $objGender->setAssignedEditorsNameById($this->intLoggedUserId);
            $objGender->save();

            $this->calPostDate->Text = $objGender->PostDate ? $objGender->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->Text = $objGender->PostUpdateDate ? $objGender->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->txtAuthor->Text = $objGender->Author;
            $this->txtUsersAsEditors->Text = implode(', ', $objGender->getUserAsEditorsArray());

            $this->refreshDisplay($objGender->getId());
        }

        /**
         * Handles the click event for the delete button.
         *
         * This method is triggered when the delete button is clicked. It checks the state of the
         * selected item and displays the appropriate modal dialog box based on whether the
         * item is locked or not.
         *
         * @param ActionParams $params Parameters containing information about the action event.
         *
         * @return void
         * @throws RandomException
         */
        protected function btnDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->objGender->getIsLocked() == 1) {
                $this->dlgModal1->showDialogBox();
            } else {
                $this->dlgModal2->showDialogBox();
            }
        }

        /**
         * Handles the deletion of an item and updates the UI accordingly.
         *
         * This method performs the deletion of a selected item, hides the relevant modal dialog,
         * executes JavaScript to modify the interface dynamically, and refreshes specific display elements.
         *
         * @param ActionParams $params The parameters associated with the current action, typically containing data
         *     about the clicked item.
         *
         * @return void
         * @throws Caller
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            $this->objGender->delete();
            $this->dlgModal1->hideDialogBox();

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden')
            ");

            $this->dtgGenders->removeCssClass('disabled');

            $this->refreshDisplay($this->objGender->getId());
        }

        /**
         * Handles the click event for hiding UI elements and resetting specific states.
         *
         * This method is triggered when a user interacts with a related action. It hides
         * specific UI elements by adding CSS classes, manages table state by removing
         * a CSS class, and hides a modal dialog box.
         *
         * @param ActionParams $params Parameters related to the triggered action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function hideItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden')
            ");

            $this->hideUserWindow();

            $this->dtgGenders->removeCssClass('disabled');

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Refreshes the display based on the provided gender object.
         *
         * This method updates the visibility and values of several UI elements
         * depending on the state of the provided gender object. It considers
         * conditions such as post-date, update date, author, and the count of users
         * as editors to appropriately display or hide specific fields.
         *
         * @param int $objEdit The identifier or object used to load the gender data.
         *
         * @return void
         * @throws \QCubed\Exception\Caller
         * @throws \QCubed\Exception\InvalidCast
         */
        protected function refreshDisplay(int $objEdit): void
        {
            $objGender = Genders::load($objEdit);

            if (!$objGender) {
                $this->lblPostDate->Display = false;
                $this->calPostDate->Display = false;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = false;
                $this->txtAuthor->Display = false;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            } else {
                if ($objGender->getPostDate() &&
                    !$objGender->getPostUpdateDate() &&
                    $objGender->getAuthor() &&
                    !$objGender->countUsersAsEditors()) {
                    $this->lblPostDate->Display = true;
                    $this->calPostDate->Display = true;
                    $this->lblPostUpdateDate->Display = false;
                    $this->calPostUpdateDate->Display = false;
                    $this->lblAuthor->Display = true;
                    $this->txtAuthor->Display = true;
                    $this->lblUsersAsEditors->Display = false;
                    $this->txtUsersAsEditors->Display = false;
                }

                if ($objGender->getPostDate() &&
                    $objGender->getPostUpdateDate() &&
                    $objGender->getAuthor() &&
                    !$objGender->countUsersAsEditors()) {
                    $this->lblPostDate->Display = true;
                    $this->calPostDate->Display = true;
                    $this->lblPostUpdateDate->Display = true;
                    $this->calPostUpdateDate->Display = true;
                    $this->lblAuthor->Display = true;
                    $this->txtAuthor->Display = true;
                    $this->lblUsersAsEditors->Display = false;
                    $this->txtUsersAsEditors->Display = false;
                }

                if ($objGender->getPostDate() &&
                    $objGender->getPostUpdateDate() &&
                    $objGender->getAuthor() &&
                    $objGender->countUsersAsEditors()) {
                    $this->lblPostDate->Display = true;
                    $this->calPostDate->Display = true;
                    $this->lblPostUpdateDate->Display = true;
                    $this->calPostUpdateDate->Display = true;
                    $this->lblAuthor->Display = true;
                    $this->txtAuthor->Display = true;
                    $this->lblUsersAsEditors->Display = true;
                    $this->txtUsersAsEditors->Display = true;
                }

                $this->calPostDate->Text = $objGender->PostDate ? $objGender->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $this->calPostUpdateDate->Text = $objGender->PostUpdateDate ? $objGender->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $this->txtAuthor->Text = $objGender->Author;
                $this->txtUsersAsEditors->Text = implode(', ', $objGender->getUserAsEditorsArray());
            }
        }

        /**
         * Hides the user-related input window and resets its fields.
         *
         * This method clears the text fields for post-date, update date, author, and editors
         * while setting the visibility of related labels and input fields to false, effectively
         * hiding them from the user interface.
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