<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\Change;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EscapeKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\EnterKey;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\Query\QQ;

    /**
     * Class FrontendOptionEditPanel
     *
     * Represents a panel for editing frontend options such as template name, class name,
     * content types management, template path, and status. This class provides controls
     * for user input, including various labels, text boxes, dropdowns, and action buttons.
     *
     * Properties:
     * - Provides modal functionality through $dlgModal9.
     * - Allows toast notifications with $dlgToastr1 and $dlgToastr2.
     * - Includes fields and labels for editing frontend template options, class names,
     *   content types, and statuses.
     * - Provides action buttons for saving and canceling operations.
     *
     * Methods:
     * - __construct: Initializes all required objects and configurations for the panel,
     *   including the loading or creation of frontend option and content type management objects.
     * - lstContentTypesManagement_GetItems: Returns a list of selectable items for content type
     *   management options.
     * - createInputs: Sets up the various input fields and labels used in the panel, linking
     *   them to backend data as required.
     * - createButtons: Initializes action buttons (save, saving, cancel).
     * - createModals: Prepares and configures modals within the interface.
     * - createToastr: Initializes instances of toast notifications.
     */
    class FrontendOptionEditPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;
        public Bs\Modal $dlgModal6;
        public Bs\Modal $dlgModal7;
        public Bs\Modal $dlgModal8;
        public Bs\Modal $dlgModal9;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;

        public Q\Plugin\Control\Label $lblFrontendTemplateName;
        public Bs\TextBox $txtFrontendTemplateName;

        public Q\Plugin\Control\Label $lblContentTypesManagement;
        public Q\Plugin\Select2 $lstContentTypesManagement;

        public Q\Plugin\Control\Label $lblClassName;
        public Bs\TextBox $txtClassName;

        public Q\Plugin\Control\Label $lblFrontendTemplatePath;
        public Bs\TextBox $txtFrontendTemplatePath;

        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected string $strSaveButtonId;
        protected string $strSavingButtonId;

        protected ?int $intId = null;
        protected ?object $objFrontendOptions = null;
        protected ?object $objContentTypesManagement = null;

        protected ?object $objContentTypesManagementCondition = null;
        protected ?array $objContentTypesManagementClauses = null;

        public ?string $previousFrontendTemplateName = null;
        public ?string $previousFrontendTemplatePath = null;

        protected string $strTemplate = 'FrontendOptionEditPanel.tpl.php';

        /**
         * Constructor for initializing and setting up the class. It attempts to load existing
         * data based on the query string's `id` parameter or initializes new entities if no
         * `id` is provided. It also creates the necessary inputs, buttons, modals, and notifications.
         *
         * @param mixed $objParentObject The parent object for this control.
         * @param string|null $strControlId Optional control ID for uniquely identifying the control.
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

            $this->intId = Application::instance()->context()->queryStringItem('id');
            if (!empty($this->intId)) {
                $this->objFrontendOptions = FrontendOptions::load($this->intId);
                $this->objContentTypesManagement = ContentTypesManagement::load($this->intId);
            } else {
                $this->objFrontendOptions = new FrontendOptions();
                $this->objContentTypesManagement = new ContentTypesManagement();
            }

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
            $this->createToastr();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves a list of ListItem objects representing content types management entries.
         *
         * @return ListItem[] An array of ListItem objects, each created from a ContentTypesManagement object.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstContentTypesManagement_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objContentTypesManagementCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objContentTypesManagementCursor = ContentTypesManagement::queryCursor($objCondition, $this->objContentTypesManagementClauses);

            // Iterate through the Cursor
            while ($objContentTypesManagement = ContentTypesManagement::instantiateCursor($objContentTypesManagementCursor)) {
                $objListItem = new ListItem($objContentTypesManagement->__toString(), $objContentTypesManagement->Id);
                if (($this->objFrontendOptions->ContentTypesManagement) && ($this->objFrontendOptions->ContentTypesManagement->Id == $objContentTypesManagement->Id))
                    $objListItem->Selected = true;
                $a[] = $objListItem;
            }

            return $a;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the input controls for frontend template options.
         *
         * This method sets up labels and input fields for frontend template name,
         * content type management, class name, template path, and status. It assigns
         * necessary properties like text, placeholder, CSS classes, and actions to each
         * control to prepare them for user interaction.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->lblFrontendTemplateName = new Q\Plugin\Control\Label($this);
            $this->lblFrontendTemplateName->Text = t('Frontend template name');
            $this->lblFrontendTemplateName->addCssClass('col-md-3');
            $this->lblFrontendTemplateName->setCssStyle('font-weight', 400);
            $this->lblFrontendTemplateName->Required = true;

            $this->txtFrontendTemplateName = new Bs\TextBox($this);
            $this->txtFrontendTemplateName->Placeholder = t('Frontend template new name');
            $this->txtFrontendTemplateName->Text = $this->objFrontendOptions->FrontendTemplateName ?? null;
            $this->txtFrontendTemplateName->addWrapperCssClass('center-button');
            $this->txtFrontendTemplateName->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtFrontendTemplateName->addAction(new EnterKey(), new Terminate());
            $this->txtFrontendTemplateName->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtFrontendTemplateName->addAction(new EscapeKey(), new Terminate());

            $this->lblContentTypesManagement = new Q\Plugin\Control\Label($this);
            $this->lblContentTypesManagement->Text = t('Custom content type');
            $this->lblContentTypesManagement->addCssClass('col-md-3');
            $this->lblContentTypesManagement->setCssStyle('font-weight', 400);
            $this->lblContentTypesManagement->Required = true;

            $this->lstContentTypesManagement = new Q\Plugin\Select2($this);
            $this->lstContentTypesManagement->MinimumResultsForSearch = -1;
            $this->lstContentTypesManagement->Theme = 'web-vauu';
            $this->lstContentTypesManagement->Width = '100%';
            $this->lstContentTypesManagement->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstContentTypesManagement->addItem(t('- Select custom content type -'), null, true);
            $this->lstContentTypesManagement->addItems($this->lstContentTypesManagement_GetItems());
            $this->lstContentTypesManagement->SelectedValue = $this->objFrontendOptions->ContentTypesManagementId;

            $this->lblClassName = new Q\Plugin\Control\Label($this);
            $this->lblClassName->Text = t('Frontend class name');
            $this->lblClassName->addCssClass('col-md-3');
            $this->lblClassName->setCssStyle('font-weight', 400);
            $this->lblClassName->Required = true;

            $this->txtClassName = new Bs\TextBox($this);
            $this->txtClassName->Placeholder = t('New class name');
            $this->txtClassName->Text = $this->objFrontendOptions->ClassName ?? null;
            $this->txtClassName->addWrapperCssClass('center-button');
            $this->txtClassName->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
            $this->txtClassName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
            $this->txtClassName->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtClassName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

            $this->lblFrontendTemplatePath = new Q\Plugin\Control\Label($this);
            $this->lblFrontendTemplatePath->Text = t('Frontend template path');
            $this->lblFrontendTemplatePath->addCssClass('col-md-3');
            $this->lblFrontendTemplatePath->setCssStyle('font-weight', 400);
            $this->lblFrontendTemplatePath->Required = true;

            $this->txtFrontendTemplatePath = new Bs\TextBox($this);
            $this->txtFrontendTemplatePath->Placeholder = t('Frontend template new path');
            $this->txtFrontendTemplatePath->Text = $this->objFrontendOptions->FrontendTemplatePath ?? null;
            $this->txtFrontendTemplatePath->addWrapperCssClass('center-button');
            $this->txtFrontendTemplatePath->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtFrontendTemplatePath->addAction(new EnterKey(), new Terminate());
            $this->txtFrontendTemplatePath->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtFrontendTemplatePath->addAction(new EscapeKey(), new Terminate());

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->addCssClass('col-md-3');
            $this->lblStatus->Required = true;

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstStatus->SelectedValue = $this->objFrontendOptions->Status;
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstStatus->addAction(new Change(), new AjaxControl($this, 'lstStatus_Change'));
        }

        /**
         * Creates and configures three buttons: Save, Save and Close, and Cancel.
         * The Save and Save and Close buttons are initialized with text and styled
         * based on the presence of a class name in `objFrontendOptions`.
         * The buttons are also linked to specific event handlers to perform actions
         * when clicked. The button IDs are stored for quick access.
         *
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnSave = new Bs\Button($this);
            if (is_null($this->objFrontendOptions->getClassName())) {
                $this->btnSave->Text = t('Save');
            } else {
                $this->btnSave->Text = t('Update');
            }
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this,'btnMenuSave_Click'));
            // The variable below is being prepared for fast transmission
            $this->strSaveButtonId = $this->btnSave->ControlId;

            $this->btnDelete = new Bs\Button($this);
            $this->btnDelete->Text = t('Delete');
            $this->btnDelete->CssClass = 'btn btn-danger';
            $this->btnDelete->addWrapperCssClass('center-button');
            $this->btnDelete->PrimaryButton = true;
            $this->btnDelete->addAction(new Click(), new AjaxControl($this,'btnMenuDelete_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this,'btnMenuCancel_Click'));

            if (!$this->intId) {
                $this->btnDelete->Display = false;
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
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this frontend option?<br>
                                        This action cannot be undone!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">It is highly recommended to hide this frontend option, 
                                        so you can reuse or edit it later.</p>');
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This frontend option cannot be deleted because it is locked in the template manager.</p>');

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The specified template name already exists in the database, 
                                        or the specified template path already exists in the template directory.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">Please use different names that 
                                        do not already exist in the database or the template directory.</p>');

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->HeaderClasses = 'btn-darkblue';
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This template name already exists in the database.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">Please use different names that do not already exist in the database.</p>');

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Tip");
            $this->dlgModal5->HeaderClasses = 'btn-darkblue';
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This template name and custom content type are currently available, 
                                        but the template path and file have not yet been created.
                                        <p>The data will be saved to the database as inactive.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;"> Please complete the missing field and activate this option once the template file is created!</p>');

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Title = t("Tip");
            $this->dlgModal6->HeaderClasses = 'btn-darkblue';
            $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The status of this frontend option cannot be hidden because it is locked in the template manager.</p>');

            $this->dlgModal7 = new Bs\Modal($this);
            $this->dlgModal7->Title = t("Tip");
            $this->dlgModal7->HeaderClasses = 'btn-darkblue';
            $this->dlgModal7->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">These frontend options cannot be modified or edited because they are locked in the template manager.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">These frontend options will be automatically restored.</p>');

            $this->dlgModal8 = new Bs\Modal($this);
            $this->dlgModal8->Title = t("Tip");
            $this->dlgModal8->HeaderClasses = 'btn-darkblue';
            $this->dlgModal8->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">All fields are required, so none of them can be left empty!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">These frontend options will be automatically restored.</p>');

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal9 = new Bs\Modal($this);
            $this->dlgModal9->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal9->Title = t("Warning");
            $this->dlgModal9->HeaderClasses = 'btn-danger';
            $this->dlgModal9->addCloseButton(t("I understand"));
        }

        /**
         * Initializes two Toastr notification instances with predefined settings for
         * success and error alerts. The first Toastr is configured to display a
         * success message when a post has been successfully saved or modified. The
         * second Toastr is set up to show an error message when there are missing
         * required fields.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('All fields are required!');
            $this->dlgToastr2->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles changes in the status dropdown. Validates CSRF token and updates the status of
         * the frontend template if no conflict is detected. Otherwise, it reverts the status change
         * and displays a conflict dialog.
         *
         * @param ActionParams $params An object containing parameters for the action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal9->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->intId) {
                if (!$this->txtFrontendTemplatePath->Text && !$this->txtClassName->Text && !$this->objFrontendOptions->getFrontendTemplatePath()) {
                    $this->lstStatus->SelectedValue = $this->objFrontendOptions->getStatus();
                    $this->inputsCheck();
                    $this->lstStatus->refresh();
                    $this->dlgModal5->showDialogBox();
                    return;
                }

                // A safe getter, if there should be more places like this
                // $getLock = FrontendTemplateLocking::loadByFrontendTemplateLockedIdFromId($id);
                // if ($getLock?->getFrontendTemplateLockedId()) { ... } // (PHP 8+ nullsafe operator)

                $frontendTemplateLocked = FrontendTemplateLocking::loadByFrontendTemplateLockedIdFromId($this->intId);

                if ($frontendTemplateLocked && $frontendTemplateLocked->getFrontendTemplateLockedId()) {
                    $this->lstStatus->SelectedValue = $this->objFrontendOptions->getStatus();
                    $this->lstStatus->refresh();
                    $this->dlgModal6->showDialogBox();
                    return;
                }

                $this->objFrontendOptions->setStatus($this->lstStatus->SelectedValue);
                $this->objFrontendOptions->save();
                $this->dlgToastr1->notify();
            }
        }

        /**
         * Handles the click event for the menu save button. Validates all input fields
         * and updates the frontend options. If any input is missing, it applies error
         * styling to the respective field.
         *
         * @param ActionParams $params An object containing parameters for the action.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function btnMenuSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal9->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->previousFrontendTemplateName = $this->txtFrontendTemplateName->Text;
            $this->previousFrontendTemplatePath = $this->txtFrontendTemplatePath->Text;

            $objContentTypesManagement = ContentTypesManagement::loadById($this->lstContentTypesManagement->SelectedValue);
            $getLock = FrontendTemplateLocking::loadByFrontendTemplateLockedIdFromId($this->intId);


            if ($this->intId) {
                // Are the front-end options locked in the template manager?
                if ($getLock?->getFrontendTemplateLockedId()) {
                    $this->restoreInputs($this->objFrontendOptions);
                    $this->lstStatus->SelectedValue = $this->objFrontendOptions->getStatus();
                    $this->lstStatus->refresh();
                    $this->dlgModal7->showDialogBox();
                    return;
                }

                if ($this->txtFrontendTemplateName->Text && $this->lstContentTypesManagement->SelectedValue && $this->txtClassName->Text !== $this->objFrontendOptions->getClassName()
                    || !FrontendOptions::templateFileExists(FRONTEND_DIR, $this->objFrontendOptions->getFolderName(), $this->txtFrontendTemplatePath->Text)) {
                    $this->dlgModal5->showDialogBox();

                    if (!$this->txtClassName->Text) {
                        $this->txtClassName->Text = '';
                        $this->txtClassName->refresh();
                    }

                    $this->txtFrontendTemplatePath->Text = '';
                    $this->txtFrontendTemplatePath->refresh();
                    $this->lstStatus->SelectedValue = 2;
                    $this->lstStatus->refresh();

                    $this->saveInputs($this->objFrontendOptions);
                    $this->lstStatus->refresh();
                    $this->inputsCheck();
                    return;
                }

                // Are all fields filled in?
                if (!$this->txtFrontendTemplateName->Text || !$this->lstContentTypesManagement->SelectedValue || !$this->txtClassName->Text || !$this->txtFrontendTemplatePath->Text) {
                    $this->dlgModal8->showDialogBox();

                    $this->restoreInputs($this->objFrontendOptions);
                    $this->inputsCheck();
                    return;
                }

                if (($this->txtFrontendTemplateName->Text == $this->objFrontendOptions->getFrontendTemplateName() && FrontendOptions::frontendTemplateNameExists(trim($this->txtFrontendTemplateName->Text)))
                && ($this->txtFrontendTemplatePath->Text == $this->objFrontendOptions->getFrontendTemplatePath() && FrontendOptions::templateFileExists(FRONTEND_DIR, $this->objFrontendOptions->getFolderName(), $this->txtFrontendTemplatePath->Text))
                ) {
                    $this->dlgModal3->showDialogBox();
                    $this->restoreInputs($this->objFrontendOptions);
                    return;
                }

                $this->saveInputs($this->objFrontendOptions);
                $this->resetInputs();

            } else { // NOT $this->>intId

                if ($this->txtFrontendTemplateName->Text && FrontendOptions::frontendTemplateNameExists(trim($this->txtFrontendTemplateName->Text))) {
                    $this->dlgModal4->showDialogBox();
                    $this->txtFrontendTemplateName->Text = '';
                    $this->lstStatus->SelectedValue = 2;
                    $this->lstStatus->refresh();
                    return;
                }

                if (($this->txtFrontendTemplateName->Text || !$this->txtClassName->Text || !$this->txtFrontendTemplateName->Text) && !$this->lstContentTypesManagement->SelectedValue) {
                    $this->dlgModal8->showDialogBox();
                    $this->txtClassName->Text = '';
                    $this->txtFrontendTemplateName->Text = '';
                    $this->lstStatus->SelectedValue = 2;
                    $this->lstStatus->refresh();
                    return;
                }

                if ($this->txtFrontendTemplateName->Text && $this->lstContentTypesManagement->SelectedValue
                    && !FrontendOptions::templateFileExists(FRONTEND_DIR, $objContentTypesManagement->getFolderName(),$this->txtFrontendTemplatePath->Text)) {
                    $this->dlgModal5->showDialogBox();

                    $objFrontendOption = new FrontendOptions();
                    $objFrontendOption->setFrontendTemplateName($this->txtFrontendTemplateName->Text);
                    $objFrontendOption->setContentTypesManagementId($this->lstContentTypesManagement->SelectedValue);
                    $objFrontendOption->setClassName('');
                    $objFrontendOption->setFolderName($objContentTypesManagement->getFolderName());
                    $objFrontendOption->setFrontendTemplatePath('');
                    $objFrontendOption->setStatus(2);
                    $objFrontendOption->save();

                    $this->txtFrontendTemplateName->Enabled = false;
                    $this->lstContentTypesManagement->Enabled = false;
                    $this->txtClassName->Enabled = false;
                    $this->txtFrontendTemplatePath->Enabled = false;
                    $this->btnSave->Enabled = false;
                    
                    $this->txtClassName->Text = '';
                    $this->txtFrontendTemplatePath->Text = '';
                    $this->lstStatus->SelectedValue = 2;
                    $this->lstStatus->refresh();
                    return;
                }

                $objFrontendOption = new FrontendOptions();
                $objFrontendOption->setFrontendTemplateName($this->txtFrontendTemplateName->Text);
                $objFrontendOption->setContentTypesManagementId($this->lstContentTypesManagement->SelectedValue);
                $objFrontendOption->setClassName($this->txtClassName->Text);
                $objFrontendOption->setFolderName($objContentTypesManagement->getFolderName());
                $objFrontendOption->setFrontendTemplatePath($this->txtFrontendTemplatePath->Text);
                $objFrontendOption->setStatus($this->lstStatus->SelectedValue);
                $objFrontendOption->save();

                $this->resetInputs();
            }
            $this->dlgToastr1->notify();

            if (is_null($this->objFrontendOptions->getClassName())) {
                $strSave_translate = t('Save');
                Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strSave_translate');");
            } else {
                $strUpdate_translate = t('Update');
                Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strUpdate_translate');");
            }

            if (!$this->txtFrontendTemplateName->Text || !$this->lstContentTypesManagement->SelectedValue || !$this->txtFrontendTemplatePath->Text) {
                $this->dlgToastr2->notify();
                $this->inputsCheck();
                $this->lstStatus->SelectedValue = 2;
                $this->lstStatus->refresh();
            }
        }

        /**
         * Handles the click event for the delete button. Validates the CSRF token and shows
         * a confirmation dialog box. If the token is invalid, it generates a new one and
         * displays an error dialog.
         *
         * @param ActionParams $params An object containing parameters for the action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnMenuDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal9->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $getLock = FrontendTemplateLocking::loadByFrontendTemplateLockedIdFromId($this->intId);

            if ($getLock?->getFrontendTemplateLockedId()) {
                $this->dlgModal2->showDialogBox();
                return;
            }

            $this->dlgModal1->showDialogBox();
        }

        /**
         * Handles the click event for deleting an item. Deletes the specified item from the
         * frontend options and redirects the user to the list page.
         *
         * @param ActionParams $params An object containing parameters for the action.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws RandomException
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            $this->dlgModal1->hideDialogBox();
            $this->objFrontendOptions->delete();
            $this->redirectToListPage();
        }

        /**
         * Handles the click event for hiding an item. Validates the CSRF token,
         * retrieves data from the frontend options, updates the respective fields,
         * and refreshes their display. Finally, it hides the first modal dialog box.
         *
         * @param ActionParams $params An object containing parameters for the action.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function hideItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal9->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->restoreInputs($this->objFrontendOptions);
            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the click event for the menu cancel button.
         *
         * Redirects the user to the list page when the button is clicked.
         *
         * @param ActionParams $params The parameters related to the action triggered by the button click.
         *
         * @return void
         * @throws RandomException
         * @throws Caller
         */
        public function btnMenuCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal9->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->redirectToListPage();
        }

        /**
         * Validates the input fields for required values and applies or removes error
         * styling accordingly. Displays a notification indicating the validation status.
         *
         * @return void
         */
        protected function inputsCheck(): void
        {
            if (!$this->txtFrontendTemplateName->Text) {
                $this->txtFrontendTemplateName->addCssClass('has-error');
            } else {
                $this->txtFrontendTemplateName->removeCssClass('has-error');
            }

            if (!$this->lstContentTypesManagement->SelectedValue) {
                $this->lstContentTypesManagement->addCssClass('has-error');
            } else {
                $this->lstContentTypesManagement->removeCssClass('has-error');
            }

            if (!$this->txtClassName->Text) {
                $this->txtClassName->addCssClass('has-error');
            } else {
                $this->txtClassName->removeCssClass('has-error');
            }

            if (!$this->txtFrontendTemplatePath->Text) {
                $this->txtFrontendTemplatePath->addCssClass('has-error');
            } else {
                $this->txtFrontendTemplatePath->removeCssClass('has-error');
            }
        }

        /**
         * Resets the input fields by removing any error-related CSS classes for specific input elements.
         * This method ensures that the input fields appear in their default visual state.
         *
         * @return void
         */
        public function resetInputs(): void
        {
            if ($this->txtFrontendTemplateName->Text) {
                $this->txtFrontendTemplateName->removeCssClass('has-error');
            }

            if ($this->lstContentTypesManagement->SelectedValue) {
                $this->lstContentTypesManagement->removeCssClass('has-error');
            }

            if ($this->txtClassName->Text) {
                $this->txtClassName->removeCssClass('has-error');
            }

            if ($this->txtFrontendTemplatePath->Text) {
                $this->txtFrontendTemplatePath->removeCssClass('has-error');
            }
        }

        /**
         * Saves the input fields into the given frontend options object and persists the changes.
         *
         * @param object $objFrontendOptions The frontend options object where the inputs will be saved.
         *
         * @return void
         * @throws Caller
         */
        protected function saveInputs(object $objFrontendOptions): void
        {
            $objFrontendOptions->setFrontendTemplateName(trim($this->txtFrontendTemplateName->Text));
            $objFrontendOptions->setContentTypesManagementId($this->lstContentTypesManagement->SelectedValue);
            $objFrontendOptions->setClassName(trim($this->txtClassName->Text));
            $objFrontendOptions->setFrontendTemplatePath(trim($this->txtFrontendTemplatePath->Text));
            $objFrontendOptions->setStatus($this->lstStatus->SelectedValue);
            $objFrontendOptions->save();
        }

        /**
         * Restores the input fields with values from the given frontend options object.
         * If the associated values in the object are null, it uses default or previous values.
         * Each field is refreshed to reflect the updated inputs.
         *
         * @param mixed $objFrontendOptions An object containing the values for the frontend template fields.
         *
         * @return void
         * @throws Caller
         */
        protected function restoreInputs(mixed $objFrontendOptions): void
        {
            $this->txtFrontendTemplateName->Text = $objFrontendOptions->getFrontendTemplateName();
            $this->lstContentTypesManagement->SelectedValue = $objFrontendOptions->getContentTypesManagementId();
            $this->txtClassName->Text = $objFrontendOptions->getClassName();
            $this->txtFrontendTemplatePath->Text = $objFrontendOptions->getFrontendTemplatePath();
            $this->lstStatus->SelectedValue = $objFrontendOptions->getStatus();

            $this->txtFrontendTemplateName->refresh();
            $this->lstContentTypesManagement->refresh();
            $this->txtClassName->refresh();
            $this->txtFrontendTemplatePath->refresh();
            $this->lstStatus->refresh();
        }

        /**
         * Redirects the browser to the previous page by executing a JavaScript command.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function redirectToListPage(): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal9->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("history.go(-1);");
        }
    }