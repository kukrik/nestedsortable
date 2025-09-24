<?php

    use QCubed as Q;
    use QCubed\Control\ListItem;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
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

    /**
     * Class ContentTypesManagementsEditPanel
     *
     * ContentTypesManagementsEditPanel is responsible for managing the editing interface for
     * content types within the application. It provides the user with various input controls,
     * buttons, and modal dialogs to add, modify, or delete content type information.
     * The panel is designed to be highly interactive, with support for AJAX actions
     * and integrated feedback mechanisms through modals and toast notifications.
     *
     * This class extends the base Panel class and uses several helper controls and plugins,
     * such as labels, text boxes, select2 dropdowns, buttons, modals, and toast notifications.
     */
    class ContentTypesManagementsEditPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;
        public Bs\Modal $dlgModal6;
        public Bs\Modal $dlgModal7;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;

        public Q\Plugin\Control\Label $lblContentName;
        public Bs\TextBox $txtContentName;

        public Q\Plugin\Control\Label $lblContentTypes;
        public Q\Plugin\Select2 $lstContentTypes;

        public Q\Plugin\Control\Label $lblViewTypes;
        public Q\Plugin\Select2 $lstViewTypes;

        public Bs\Button $btnSave;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected string $strSaveButtonId;

        protected ?int $intId = null;
        protected object $objContentTypesManagement;

        protected ?object $objDefaultFrontendTemplateCondition = null;
        protected ?array $objDefaultFrontendTemplateClauses = null;

        public ?string $previousContentName = null;

        protected string $strTemplate = 'ContentTypesManagementEditPanel.tpl.php';

        /**
         * Constructor for the class.
         *
         * @param mixed $objParentObject The parent object for this control.
         * @param string|null $strControlId Optional control ID for assigning a unique identifier.
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

            $this->intId = Application::instance()->context()->queryStringItem('id');

            if (!empty($this->intId)) {
                $this->objContentTypesManagement = ContentTypesManagement::load($this->intId);
            } else {
                $this->objContentTypesManagement = new ContentTypesManagement();
            }

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
            $this->createToastr();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and creates input controls for content management,
         * including labels and selection lists for content name, content type, and view type.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->lblContentName = new Q\Plugin\Control\Label($this);
            $this->lblContentName->Text = t('Content name');
            $this->lblContentName->addCssClass('col-md-3');
            $this->lblContentName->setCssStyle('font-weight', 400);
            $this->lblContentName->Required = true;

            $this->txtContentName = new Bs\TextBox($this);
            $this->txtContentName->Placeholder = t('Content new name');
            $this->txtContentName->Text = $this->objContentTypesManagement->ContentName ?
                $this->objContentTypesManagement->ContentName : null;
            $this->txtContentName->addWrapperCssClass('center-button');
            $this->txtContentName->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtContentName->addAction(new EnterKey(), new Terminate());
            $this->txtContentName->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtContentName->addAction(new EscapeKey(), new Terminate());

            $this->lblContentTypes = new Q\Plugin\Control\Label($this);
            $this->lblContentTypes->Text = t('Content type');
            $this->lblContentTypes->addCssClass('col-md-3');
            $this->lblContentTypes->setCssStyle('font-weight', 400);
            $this->lblContentTypes->Required = true;

            $this->lstContentTypes = new Q\Plugin\Select2($this);
            $this->lstContentTypes->MinimumResultsForSearch = -1;
            $this->lstContentTypes->Theme = 'web-vauu';
            $this->lstContentTypes->Width = '100%';
            $this->lstContentTypes->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstContentTypes->addItem(t('- Select a content type -'), null, true);
            $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems());
            $this->lstContentTypes->SelectedValue = $this->objContentTypesManagement->ContentType;

            $this->lblViewTypes = new Q\Plugin\Control\Label($this);
            $this->lblViewTypes->Text = t('View type');
            $this->lblViewTypes->addCssClass('col-md-3');
            $this->lblViewTypes->setCssStyle('font-weight', 400);
            $this->lblViewTypes->Required = true;

            $this->lstViewTypes = new Q\Plugin\Select2($this);
            $this->lstViewTypes->MinimumResultsForSearch = -1;
            $this->lstViewTypes->Theme = 'web-vauu';
            $this->lstViewTypes->Width = '100%';
            $this->lstViewTypes->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstViewTypes->addItem(t('- Select a view type -'), null, true);
            $this->lstViewTypes->addItems($this->lstViewTypeObject_GetItems());
            $this->lstViewTypes->SelectedValue = $this->objContentTypesManagement->ViewType;
        }

        /**
         * Initialize and configure action buttons for content management.
         *
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnSave = new Bs\Button($this);
            if (is_null($this->objContentTypesManagement->getContentName())) {
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
            $this->btnDelete->CausesValidation = false;
            $this->btnDelete->addAction(new Click(), new AjaxControl($this,'btnMenuDelete_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this,'btnMenuCancel_Click'));

            if (!empty($this->intId)) {
                $contentTypesManagement = ContentTypesManagement::loadByIdFromId($this->intId);

                if (ContentType::isStandardContentType($contentTypesManagement->getContentType())) {
                    $this->txtContentName->Enabled = false;
                    $this->lstContentTypes->Enabled = false;
                    $this->lstViewTypes->Enabled = false;
                    $this->btnSave->Display = false;
                    $this->btnDelete->Display = false;
                }
            } else {
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
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the content type?</p>
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This content type cannot be deleted because it is locked in the frontend options manager.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">This content type and all its parameters will be automatically restored.</p>');

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This content type cannot be changed because it is locked in the frontend options manager.</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">This content type and all its parameters will be automatically restored.</p>');

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->HeaderClasses = 'btn-darkblue';
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This class name together with its content types already exists in the database!</p>');

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Tip");
            $this->dlgModal5->HeaderClasses = 'btn-darkblue';
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The selected content type and view type are already linked in the database.</p>
                                        <p style="line-height: 25px; margin-bottom: -3px;">Please choose a different combination for linking!</p>');

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Title = t("Tip");
            $this->dlgModal6->HeaderClasses = 'btn-darkblue';
            $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">All fields are required, so none of them can be left empty!</p>
                                        <p style="line-height: 15px; margin-bottom: -3px;">This content type and all its parameters will be automatically restored.</p>');

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal7 = new Bs\Modal($this);
            $this->dlgModal7->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal7->Title = t("Warning");
            $this->dlgModal7->HeaderClasses = 'btn-danger';
            $this->dlgModal7->addCloseButton(t("I understand"));
        }

        /**
         * Create and configure toastr notifications for success and error alerts.
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
         * Retrieves a list of content type items with specific IDs disabled.
         *
         * @return array An array of ListItem objects representing content types.
         *               IDs between 1 and 17 are marked as non-selectable.
         * @throws Caller
         */
        public function lstContentTypeObject_GetItems(): array
        {
            $items = [];
            $strContentTypeArray = ContentType::nameArray();
            unset($strContentTypeArray[7]);
            unset($strContentTypeArray[8]);
            unset($strContentTypeArray[9]);

            foreach ($strContentTypeArray as $id => $name) {
                // ID 1-17: always non-selectable, larger ones selectable
                $disabled = ((int)$id >= 1 && (int)$id <= 17);
                $items[] = new ListItem($name, (string)$id, false, $disabled
                );
            }
            return $items;
        }

        /**
         * Retrieves an array of view type names.
         *
         * @return array An array containing the names of view types.
         */
        public function lstViewTypeObject_GetItems(): array
        {
            $strViewTypeArray = ViewType::nameArray();
            unset($strViewTypeArray[1]);

            return $strViewTypeArray;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the menu save button, saving or updating content details
         * and displaying appropriate notifications and button labels based on the form's state.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void This method does not return any value.
         * @throws Caller
         * @throws RandomException
         */
        public function btnMenuSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->previousContentName = $this->txtContentName->Text;

            if ($this->intId) {
                // Are all fields filled in?
                if (!$this->txtContentName->Text || !$this->lstContentTypes->SelectedValue || !$this->lstViewTypes->SelectedValue) {
                    $this->dlgModal6->showDialogBox();
                    $this->restoreInputs($this->objContentTypesManagement);
                    return;
                }

                // First, you need to check whether this content type is already locked or not.
                // If it is locked, it cannot be changed.
                $frontendOption = FrontendOptions::loadByIdFromId($this->intId);

                if ($frontendOption) {
                    $this->dlgModal3->showDialogBox();
                    $this->restoreInputs($this->objContentTypesManagement);
                    return;
                }

                if (($this->txtContentName->Text !== $this->objContentTypesManagement->getContentName()
                        && $this->lstContentTypes->SelectedValue == $this->objContentTypesManagement->getContentType()
                        && $this->lstViewTypes->SelectedValue == $this->objContentTypesManagement->getViewType())
                    && ContentTypesManagement::contentNameExists(trim($this->txtContentName->Text)))
                {
                    $this->dlgModal4->showDialogBox();
                    $this->restoreInputs($this->objContentTypesManagement);
                    return;
                }

                if (($this->txtContentName->Text == $this->objContentTypesManagement->getContentName()
                        && $this->lstContentTypes->SelectedValue !== $this->objContentTypesManagement->getContentType()
                        && $this->lstViewTypes->SelectedValue !== $this->objContentTypesManagement->getViewType())
                    && ContentTypesManagement::pairExists($this->lstContentTypes->SelectedValue, $this->lstViewTypes->SelectedValue))
                {
                    $this->dlgModal5->showDialogBox();
                    $this->restoreInputs($this->objContentTypesManagement);
                    return;
                }

                if ($this->txtContentName->Text && $this->lstContentTypes->SelectedValue && $this->lstViewTypes->SelectedValue) {
                    $this->objContentTypesManagement->setContentName($this->txtContentName->Text);
                    $this->objContentTypesManagement->setContentType($this->lstContentTypes->SelectedValue);
                    $this->objContentTypesManagement->setViewType($this->lstViewTypes->SelectedValue);
                    $this->objContentTypesManagement->setTableClass(ContentType::toTableClass($this->lstContentTypes->SelectedValue));
                    $this->objContentTypesManagement->setFolderName(ContentType::toFolderName($this->lstContentTypes->SelectedValue));
                    $this->objContentTypesManagement->save();
                }
            } else { // NOT $this->intId
                if ($this->txtContentName->Text && ContentTypesManagement::contentNameExists(trim($this->txtContentName->Text))) {
                    $this->dlgModal4->showDialogBox();
                    $this->txtContentName->Text = '';
                    return;
                }

                if (($this->lstContentTypes->SelectedValue && $this->lstViewTypes->SelectedValue) && ContentTypesManagement::pairExists($this->lstContentTypes->SelectedValue, $this->lstViewTypes->SelectedValue)) {
                    $this->dlgModal5->showDialogBox();
                    $this->txtContentName->Text = $this->previousContentName;
                    $this->lstContentTypes->SelectedValue = null;
                    $this->lstViewTypes->SelectedValue = null;
                    $this->lstContentTypes->refresh();
                    $this->lstViewTypes->refresh();
                    return;
                }

                if ($this->txtContentName->Text && $this->lstContentTypes->SelectedValue && $this->lstViewTypes->SelectedValue) {
                    $objContentTypesManagement = new ContentTypesManagement();
                    $objContentTypesManagement->setContentName($this->txtContentName->Text);
                    $objContentTypesManagement->setContentType($this->lstContentTypes->SelectedValue);
                    $objContentTypesManagement->setViewType($this->lstViewTypes->SelectedValue);
                    $objContentTypesManagement->setTableClass(ContentType::toTableClass($this->lstContentTypes->SelectedValue));
                    $objContentTypesManagement->setFolderName(ContentType::toFolderName($this->lstContentTypes->SelectedValue));
                    $objContentTypesManagement->save();

                    $this->dlgToastr1->notify();
                }
            }

            if (is_null($this->objContentTypesManagement->getContentName())) {
                $strSave_translate = t('Save');
                Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strSave_translate');");
            } else {
                $strUpdate_translate = t('Update');
                Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strUpdate_translate');");
            }

            if (!$this->txtContentName->Text || !$this->lstContentTypes->SelectedValue || !$this->lstViewTypes->SelectedValue) {
                $this->dlgToastr2->notify();
                $this->inputsCheck();
            }
        }

        /**
         * Handles the click event for the menu delete button.
         *
         * @param ActionParams $params Parameters passed during the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function btnMenuDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $frontendOption = FrontendOptions::loadByIdFromId($this->intId);

            if ($frontendOption) {
                $this->dlgModal2->showDialogBox();
                return;
            }

            $this->dlgModal1->showDialogBox();
        }

        /**
         * Handles the delete item click event and performs the deletion logic.
         *
         * @param ActionParams $params Parameters related to the action being triggered.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            $this->objContentTypesManagement->delete();
            $this->dlgModal1->hideDialogBox();
            $this->redirectToListPage();
        }
        
        /**
         * Handles the click event to hide an item and update related UI components.
         *
         * @param ActionParams $params The parameters related to the action triggering this method.
         *
         * @return void This method does not return a value.
         * @throws RandomException
         */
        protected function hideItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->txtContentName->Text = $this->objContentTypesManagement->ContentName ?
                $this->objContentTypesManagement->ContentName : null;
            $this->lstContentTypes->SelectedValue = $this->objContentTypesManagement->ContentType;
            $this->lstViewTypes->SelectedValue = $this->objContentTypesManagement->ViewType;
            $this->txtContentName->refresh();
            $this->lstContentTypes->refresh();
            $this->lstViewTypes->refresh();

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the click event for the menu cancel button.
         *
         * @param ActionParams $params The parameters associated with the action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws RandomException
         */
        public function btnMenuCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->redirectToListPage();
        }

        /**
         * Restores input values for content name, content type, and view type using the provided content management object.
         *
         * @param object $objContentTypesManagement The object used to retrieve and set the input values.
         *
         * @return void
         */
        protected function restoreInputs(object $objContentTypesManagement): void
        {
            $this->txtContentName->Text = $objContentTypesManagement->getContentName() ?? '';
            $this->lstContentTypes->SelectedValue = $objContentTypesManagement->getContentType() ?? null;
            $this->lstViewTypes->SelectedValue = $objContentTypesManagement->getViewType() ?? null;

            $this->txtContentName->refresh();
            $this->lstContentTypes->refresh();
            $this->lstViewTypes->refresh();
        }

        /**
         * Validates the input fields for required values and applies or removes error
         * styling accordingly. Displays a notification indicating the validation status.
         *
         * @return void
         */
        protected function inputsCheck(): void
        {
            if (!$this->txtContentName->Text) {
                $this->txtContentName->addCssClass('has-error');
            } else {
                $this->txtContentName->removeCssClass('has-error');
            }
            if (!$this->lstContentTypes->SelectedValue) {
                $this->lstContentTypes->addCssClass('has-error');
            } else {
                $this->lstContentTypes->removeCssClass('has-error');
            }
            if (!$this->lstViewTypes->SelectedValue) {
                $this->lstViewTypes->addCssClass('has-error');
            } else {
                $this->lstViewTypes->removeCssClass('has-error');
            }
        }

        /**
         * Redirects the user to the previous page in the browser history.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function redirectToListPage(): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("history.go(-1);");
        }
    }