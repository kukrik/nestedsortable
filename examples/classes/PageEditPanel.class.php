<?php

    use QCubed as Q;
    use QCubed\Action\ActionParams;
    use QCubed\Action\AjaxControl;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\Panel;
    use QCubed\Event\Change;
    use QCubed\Event\Click;
    use QCubed\Event\DialogButton;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Project\Application;
    use QCubed\Folder;
    use QCubed\QString;

    /**
     * Represents a panel for editing page details and menu configurations.
     * Extends the base Panel class to provide specific functionalities for managing menu text, content types,
     * and action buttons.
     */
    class PageEditPanel extends Panel
    {
        protected string $strRootPath = APP_UPLOADS_DIR;
        protected string $strRootUrl = APP_UPLOADS_URL;
        protected string $strTempPath = APP_UPLOADS_TEMP_DIR;
        protected string $strTempUrl = APP_UPLOADS_TEMP_URL;
        public array $tempFolders = ['thumbnail', 'medium', 'large'];

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;

        public Q\Plugin\Control\Label $lblExistingMenuText;
        public Q\Plugin\Control\Label $txtExistingMenuText;

        public Q\Plugin\Control\Label $lblContentType;
        public Q\Plugin\Select2 $lstContentTypes;

        public Q\Plugin\Control\Label $lblMenuText;
        public Bs\TextBox $txtMenuText;
        public Bs\TextBox $txtRename;

        public Bs\Button $btnSave;
        public Bs\Button $btnSaving;
        public Bs\Button $btnCancel;

        protected string $strSaveButtonId;
        protected string $strSavingButtonId;

        protected int $intId;
        protected object $objMenu;
        protected object $objMenuContent;
        protected ?object $objFrontendLinks = null;
        protected ?object $objMembersSetting = null;
        protected int $intLoggedUserId;

        protected string $strTemplate = 'PageEditPanel.tpl.php';

        /**
         * Constructs a new instance of the class and initializes its properties and controls.
         * This method initializes different objects and settings based on the provided parameters,
         * as well as setting up inputs, buttons, notifications, and modal dialogs.
         *
         * @param mixed $objParentObject The parent object to which this instance belongs.
         * @param null|string $strControlId Optional control ID for identifying this instance.
         *
         * @throws \QCubed\Exception\Caller
         * @throws \QCubed\Exception\InvalidCast
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
            $this->objMenu = Menu::load($this->intId);
            $this->objMenuContent = MenuContent::load($this->intId);

            $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must save something here $this->objNews->setUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session
            $this->intLoggedUserId = 1;

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
            $this->portedRenameTextBox();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and creates input controls for menu editing, including labels, text boxes, and a select box with options for selecting content types.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->lblExistingMenuText = new Q\Plugin\Control\Label($this);
            $this->lblExistingMenuText->Text = t('Existing menu text');
            $this->lblExistingMenuText->addCssClass('col-md-3');
            $this->lblExistingMenuText->setCssStyle('font-weight', 400);

            $this->txtExistingMenuText = new Q\Plugin\Control\Label($this);
            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtExistingMenuText->setCssStyle('font-weight', 400);

            $this->lblMenuText = new Q\Plugin\Control\Label($this);
            $this->lblMenuText->Text = t('Menu text');
            $this->lblMenuText->addCssClass('col-md-3');
            $this->lblMenuText->setCssStyle('font-weight', 400);
            $this->lblMenuText->Required = true;

            $this->txtMenuText = new Bs\TextBox($this);
            $this->txtMenuText->Placeholder = t('Menu text');
            $this->txtMenuText->Text = $this->objMenuContent->MenuText;
            $this->txtMenuText->addWrapperCssClass('center-button');
            $this->txtMenuText->setHtmlAttribute('required', 'required');

            if ($this->objMenuContent->getMenuText()) {
                $this->txtMenuText->Enabled = false;
            }

            $this->lblContentType = new Q\Plugin\Control\Label($this);
            $this->lblContentType->Text = t('Content type');
            $this->lblContentType->addCssClass('col-md-3');
            $this->lblContentType->setCssStyle('font-weight', 400);
            $this->lblContentType->Required = true;

            $this->lstContentTypes = new Q\Plugin\Select2($this);
            $this->lstContentTypes->MinimumResultsForSearch = -1;
            $this->lstContentTypes->Theme = 'web-vauu';
            $this->lstContentTypes->Width = '100%';
            $this->lstContentTypes->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;

            if (!$this->objMenuContent->getContentType()) {
                $this->lstContentTypes->addItem(t('- Select one type -'), null, true);
            }

            $strContentTypeArray = ContentType::nameArray();

            if ($this->objMenuContent->getContentType() == 10) {
                unset($strContentTypeArray[10]);
            }

            $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems());
            $this->lstContentTypes->SelectedValue = $this->objMenuContent->ContentType;
            $this->lstContentTypes->addAction(new Change(), new AjaxControl($this,'lstClassNames_Change'));
            $this->lstContentTypes->setHtmlAttribute('required', 'required');
        }

        /**
         * Create and configure action buttons for menu operations, including save, save and close, and cancel.
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnSave = new Bs\Button($this);
            if (is_null($this->objMenuContent->getContentType())) {
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

            $this->btnSaving = new Bs\Button($this);
            if (is_null($this->objMenuContent->getContentType())) {
                $this->btnSaving->Text = t('Save and close');
            } else {
                $this->btnSaving->Text = t('Update and close');
            }
            $this->btnSaving->CssClass = 'btn btn-darkblue';
            $this->btnSaving->addWrapperCssClass('center-button');
            $this->btnSaving->PrimaryButton = true;
            $this->btnSaving->addAction(new Click(), new AjaxControl($this,'btnMenuSaveClose_Click'));
            // The variable below is being prepared for fast transmission
            $this->strSavingButtonId = $this->btnSaving->ControlId;

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Back to menu manager');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this,'btnMenuCancel_Click'));
        }

        /**
         * Create and configure Toastr notification instances for different alert types.
         * Initializes toastr notifications with predefined settings for success and error messages.
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
            $this->dlgToastr2->Message = t('The menu title must exist!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('The title of this menu item already exists in the database, please choose another title!');
            $this->dlgToastr3->ProgressBar = true;
        }

        /**
         * Initializes a modal dialog with pre-defined settings and actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->AutoRenderChildren = true;
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">A gallery folder with the same name cannot be created!</p>
                                    <p style="line-height: 25px; margin-bottom: 2px;">Changing the appropriate title of the menu item is a must!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Opting out will delete this menu entry and redirect you back to the menu manager!</p>');
            $this->dlgModal1->Title = t("Warning");
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("Change"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addCloseButton(t("Cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'changeItem_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this,'deleteItem_Click'));

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal2->Title = t("Warning");
            $this->dlgModal2->HeaderClasses = 'btn-danger';
            $this->dlgModal2->addCloseButton(t("I understand"));
        }

        /**
         * Configures and initializes a text box for renaming purposes within a modal dialog.
         *
         * @return void
         * @throws Caller
         */
        public function portedRenameTextBox(): void
        {
            $this->txtRename = new Bs\TextBox($this->dlgModal1);
            $this->txtRename->setHtmlAttribute('autocomplete', 'off');
            $this->txtRename->setCssStyle('margin-top', '15px');
            $this->txtRename->setCssStyle('margin-bottom', '15px');
            $this->txtRename->setHtmlAttribute('required', 'required');
            $this->txtRename->UseWrapper = false;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves an array of content type names with certain conditions applied.
         *
         * @return array The content type names array with specific items removed based on predefined conditions.
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstContentTypeObject_GetItems(): array
        {
            $strContentTypeArray = ContentType::nameArray();
            unset($strContentTypeArray[1]);

            if (MenuContent::contentTypeExists(10)) {
                unset($strContentTypeArray[10]);
            }

            if (MenuContent::contentTypeExists(14)) {
                unset($strContentTypeArray[14]);
            }

            if (MenuContent::contentTypeExists(15)) {
                unset($strContentTypeArray[15]);
            }

            if (MenuContent::contentTypeExists(16)) {
                unset($strContentTypeArray[16]);
            }

            $extraColumnValuesArray = ContentType::extraColumnValuesArray();
            for ($i = 1; $i < count($extraColumnValuesArray); $i++) {
                if ($extraColumnValuesArray[$i]['IsEnabled'] == 0) {
                    unset($strContentTypeArray[$i]);
                }
            }
            return $strContentTypeArray;
        }

        /**
         * Handles changes in class names and updates related objects based on a menu content type.
         *
         * @param ActionParams $params The parameters associated with the action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function lstClassNames_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->setIsEnabled(2);
            $this->objMenuContent->setSettingLocked(2);
            $this->objMenuContent->save();

            if ($this->objMenuContent->getMenuTreeHierarchy()) {
                $updatedUrl = $this->objMenuContent->getMenuTreeHierarchy();
            } else {
                $updatedUrl = '/' . Q\QString::sanitizeForUrl($this->objMenuContent->getMenuText());
            }

            if ($this->objMenuContent->ContentType == 2) { // Article type
                $objTemplateLocking = FrontendTemplateLocking::load(2);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objArticle = new Article();
                $objArticle->setMenuContentId($this->objMenuContent->Id);

                $objArticle->setPostDate(Q\QDateTime::now());
                $objArticle->setAssignedByUser($this->intLoggedUserId);
                $objArticle->setAuthor($objArticle->getAssignedByUserObject());
                $objArticle->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->setContentTypesManagamentId(2);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType == 3) { // News type
                $objTemplateLocking = FrontendTemplateLocking::load(3);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objNewsSettings = new NewsSettings();
                $objNewsSettings->setName(trim($this->txtMenuText->Text));
                $objNewsSettings->setIsReserved(1);
                $objNewsSettings->setMenuContentId($this->objMenuContent->Id);
                $objNewsSettings->setTitleSlug($updatedUrl);
                $objNewsSettings->setPostDate(Q\QDateTime::now());
                $objNewsSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(3);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType == 4) { // Gallery type
                $path = $this->strRootPath;
                $scanned_directory = array_diff(scandir($path), array('..', '.'));

                if (in_array(QString::sanitizeForUrl(trim($this->txtMenuText->Text)), $scanned_directory)) {
                    $this->dlgModal1->showDialogBox();
                    $this->txtRename->Text = '';
                    $this->txtRename->focus();
                } else {
                    $this->lockInputFields();
                }
            }

            if ($this->objMenuContent->ContentType == 5) { // Events calendar type
                $objTemplateLocking = FrontendTemplateLocking::load(7);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objEventsSettings = new EventsSettings();
                $objEventsSettings->setName(trim($this->txtMenuText->Text));
                $objEventsSettings->setIsReserved(1);
                $objEventsSettings->setMenuContentId($this->objMenuContent->Id);
                $objEventsSettings->setTitleSlug($updatedUrl);
                $objEventsSettings->setPostDate(Q\QDateTime::now());
                $objEventsSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(7);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->setIsActivated(1);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType == 6) { // Sports calendar type
                $objTemplateLocking = FrontendTemplateLocking::load(9);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objEventsSettings = new SportsSettings();
                $objEventsSettings->setName(trim($this->txtMenuText->Text));
                $objEventsSettings->setIsReserved(1);
                $objEventsSettings->setMenuContentId($this->objMenuContent->Id);
                $objEventsSettings->setTitleSlug($updatedUrl);
                $objEventsSettings->setPostDate(Q\QDateTime::now());
                $objEventsSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(9);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType == 10) { // Sports areas type
                $objTemplateLocking = FrontendTemplateLocking::load(11);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->setContentTypesManagamentId(11);
                $objFrontendLinks->setIsActivated(1);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType == 11) { // Board type
                $objTemplateLocking = FrontendTemplateLocking::load(12);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objBoardsSettings = new BoardsSettings();
                $objBoardsSettings->setName(trim($this->txtMenuText->Text));
                $objBoardsSettings->setIsReserved(1);
                $objBoardsSettings->setMenuContentId($this->objMenuContent->Id);
                $objBoardsSettings->setTitleSlug($updatedUrl);
                $objBoardsSettings->setPostDate(Q\QDateTime::now());
                $objBoardsSettings->setAssignedByUser($this->intLoggedUserId);
                $objBoardsSettings->setAuthor($objBoardsSettings->getAssignedByUserObject());
                $objBoardsSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(12);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();

                $objInputs = [
                    ['input_key' => 1, 'name' => 'Fullname', 'order' => 0, 'activity_status' => 1],
                    ['input_key' => 2, 'name' => 'Position', 'order' => 1, 'activity_status' => 2],
                    ['input_key' => 3, 'name' => 'Area responsibility', 'order' => 2, 'activity_status' => 2],
                    ['input_key' => 4, 'name' => 'Interests and hobbies', 'order' => 3, 'activity_status' => 2],
                    ['input_key' => 5, 'name' => 'Description', 'order' => 4, 'activity_status' => 2],
                    ['input_key' => 6, 'name' => 'Telephone', 'order' => 5, 'activity_status' => 2],
                    ['input_key' => 7, 'name' => 'SMS', 'order' => 6], 'activity_status' => 2,
                    ['input_key' => 8, 'name' => 'Fax', 'order' => 7, 'activity_status' => 2],
                    ['input_key' => 9, 'name' => 'Address', 'order' => 8, 'activity_status' => 2],
                    ['input_key' => 10, 'name' => 'Email', 'order' => 9, 'activity_status' => 2],
                    ['input_key' => 11, 'name' => 'Website', 'order' => 10, 'activity_status' => 2]
                ];

                foreach ($objInputs as $value) {
                    $objBoardOptions = new BoardOptions();
                    $objBoardOptions->setSettingsId($objBoardsSettings->getId());
                    $objBoardOptions->setInputKey($value["input_key"]);
                    $objBoardOptions->setName($value["name"]);
                    $objBoardOptions->setOrder($value["order"]);
                    $objBoardOptions->setActivityStatus($value["activity_status"]);
                    $objBoardOptions->save();
                }
            }

            if ($this->objMenuContent->ContentType == 12) { // Member type
                $objTemplateLocking = FrontendTemplateLocking::load(13);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objMembersSettings = new MembersSettings();
                $objMembersSettings->setName(trim($this->txtMenuText->Text));
                $objMembersSettings->setIsReserved(1);
                $objMembersSettings->setMenuContentId($this->objMenuContent->Id);
                $objMembersSettings->setTitleSlug($updatedUrl);
                $objMembersSettings->setPostDate(Q\QDateTime::now());
                $objMembersSettings->setAssignedByUser($this->intLoggedUserId);
                $objMembersSettings->setAuthor($objMembersSettings->getAssignedByUserObject());
                $objMembersSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(13);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->setIsActivated(1);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();

                $objInputs = [
                    ['input_key' => 1, 'name' => 'Member name', 'order' => 0, 'activity_status' => 1],
                    ['input_key' => 2, 'name' => 'Registry code', 'order' => 1, 'activity_status' => 2],
                    ['input_key' => 3, 'name' => 'Bank account number', 'order' => 2, 'activity_status' => 2],
                    ['input_key' => 4, 'name' => "Representative's full name", 'order' => 3, 'activity_status' => 2],
                    ['input_key' => 5, 'name' => "Representative's telephone", 'order' => 4, 'activity_status' => 2],
                    ['input_key' => 6, 'name' => "Representative's SMS", 'order' => 5, 'activity_status' => 2],
                    ['input_key' => 7, 'name' => "Representative's fax", 'order' => 6, 'activity_status' => 2],
                    ['input_key' => 8, 'name' => "Representative's email", 'order' => 7, 'activity_status' => 2],
                    ['input_key' => 9, 'name' => 'Description', 'order' => 8, 'activity_status' => 2],
                    ['input_key' => 10, 'name' => 'Telephone', 'order' => 9, 'activity_status' => 2],
                    ['input_key' => 11, 'name' => 'SMS', 'order' => 10, 'activity_status' => 2],
                    ['input_key' => 12, 'name' => 'Fax', 'order' => 11, 'activity_status' => 2],
                    ['input_key' => 13, 'name' => 'Address', 'order' => 12, 'activity_status' => 2],
                    ['input_key' => 14, 'name' => 'Email', 'order' => 13, 'activity_status' => 2],
                    ['input_key' => 15, 'name' => 'Website', 'order' => 14, 'activity_status' => 2],
                    ['input_key' => 16, 'name' => 'Members number', 'order' => 15, 'activity_status' => 2]
                ];

                foreach ($objInputs as $value) {
                    $objMembersOptions = new MembersOptions();
                    $objMembersOptions->setSettingsId($objMembersSettings->getId());
                    $objMembersOptions->setInputKey($value["input_key"]);
                    $objMembersOptions->setName($value["name"]);
                    $objMembersOptions->setOrder($value["order"]);
                    $objMembersOptions->setActivityStatus($value["activity_status"]);
                    $objMembersOptions->save();
                }
            }

            if ($this->objMenuContent->ContentType == 13) { // Video type
                $objTemplateLocking = FrontendTemplateLocking::load(14);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objVideosSettings = new VideosSettings();
                $objVideosSettings->setName(trim($this->txtMenuText->Text));
                $objVideosSettings->setIsReserved(1);
                $objVideosSettings->setMenuContentId($this->objMenuContent->Id);
                $objVideosSettings->setTitleSlug($updatedUrl);
                $objVideosSettings->setPostDate(Q\QDateTime::now());
                $objVideosSettings->setAssignedByUser($this->intLoggedUserId);
                $objVideosSettings->setAuthor($objVideosSettings->getAssignedByUserObject());
                $objVideosSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(14);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType == 14) { // Statistics (Records) type
                $objTemplateLocking = FrontendTemplateLocking::load(15);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objStatisticsSettings = StatisticsSettings::load(14);
                $objStatisticsSettings->setName(trim($this->txtMenuText->Text));
                $objStatisticsSettings->setIsReserved(1);
                $objStatisticsSettings->setMenuContentId($this->objMenuContent->Id);
                $objStatisticsSettings->setTitleSlug($updatedUrl);
                $objStatisticsSettings->setPostDate(Q\QDateTime::now());
                $objStatisticsSettings->setAssignedByUser($this->intLoggedUserId);
                $objStatisticsSettings->setAuthor($objStatisticsSettings->getAssignedByUserObject());
                $objStatisticsSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(15);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->setIsActivated(1);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType == 15) { // Statistics (Rankings) type
                $objTemplateLocking = FrontendTemplateLocking::load(16);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objStatisticsSettings = StatisticsSettings::load(15);
                $objStatisticsSettings->setName(trim($this->txtMenuText->Text));
                $objStatisticsSettings->setIsReserved(1);
                $objStatisticsSettings->setMenuContentId($this->objMenuContent->Id);
                $objStatisticsSettings->setTitleSlug($updatedUrl);
                $objStatisticsSettings->setPostDate(Q\QDateTime::now());
                $objStatisticsSettings->setAssignedByUser($this->intLoggedUserId);
                $objStatisticsSettings->setAuthor($objStatisticsSettings->getAssignedByUserObject());
                $objStatisticsSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(16);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType == 16) { // Statistics (Achievements) type
                $objTemplateLocking = FrontendTemplateLocking::load(17);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objStatisticsSettings = StatisticsSettings::load(16);
                $objStatisticsSettings->setName(trim($this->txtMenuText->Text));
                $objStatisticsSettings->setIsReserved(1);
                $objStatisticsSettings->setMenuContentId($this->objMenuContent->Id);
                $objStatisticsSettings->setTitleSlug($updatedUrl);
                $objStatisticsSettings->setPostDate(Q\QDateTime::now());
                $objStatisticsSettings->setAssignedByUser($this->intLoggedUserId);
                $objStatisticsSettings->setAuthor($objStatisticsSettings->getAssignedByUserObject());
                $objStatisticsSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(17);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->setIsActivated(1);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType == 17) { // Links type
                $objTemplateLocking = FrontendTemplateLocking::load(18);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objLinksSettings = new LinksSettings();
                $objLinksSettings->setName(trim($this->txtMenuText->Text));
                $objLinksSettings->setIsReserved(1);
                $objLinksSettings->setMenuContentId($this->objMenuContent->Id);
                $objLinksSettings->setTitleSlug($updatedUrl);
                $objLinksSettings->setPostDate(Q\QDateTime::now());
                $objLinksSettings->setAssignedByUser($this->intLoggedUserId);
                $objLinksSettings->setAuthor($objLinksSettings->getAssignedByUserObject());
                $objLinksSettings->save();

                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(18);
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $objFrontendLinks->save();

                $objMetadata = new Metadata();
                $objMetadata->setMenuContentId($this->objMenuContent->Id);
                $objMetadata->save();
            }

            if ($this->objMenuContent->ContentType !== 4) {
                Application::redirect('menu_edit.php?id=' . $this->intId);
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for changing the item, performing operations based on the existence of the renamed item within a directory.
         *
         * @param ActionParams $params The parameters associated with the action triggering the click event.
         *
         * @return void No return value as the method performs actions related to updating and validating menu content.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        public function changeItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $path = $this->strRootPath;
            $scanned_directory = array_diff(scandir($path), array('..', '.'));

            if (in_array(QString::sanitizeForUrl(trim($this->txtRename->Text)), $scanned_directory)) {
                $this->txtRename->Text = '';
                $this->txtRename->focus();
            } else {
                $this->objMenuContent->setMenuText($this->txtRename->Text);
                $this->objMenuContent->setRedirectUrl($this->objMenuContent->getRedirectUrl());
                $this->objMenuContent->save();

                $this->txtExistingMenuText->Text = $this->txtRename->Text;
                $this->txtMenuText->Text = $this->txtRename->Text;

                $this->dlgModal1->hideDialogBox();
                $this->lockInputFields();
            }
        }

        /**
         * Handles the deletion of an item when a specific action is triggered.
         *
         * @param ActionParams $params The parameters associated with the action triggering the deletion.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws RandomException
         * @throws Throwable
         */
        public function deleteItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!$this->txtRename->Text) {
                $this->objMenu->delete();
                Application::redirect('menu_manager.php');
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Locks the input fields by creating and setting various objects and properties
         * related to menu content and template options.
         *
         * @return void This method does not return a value. It performs operations
         * that configure, save, and lock data concerning menus and templates.
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        private function lockInputFields(): void
        {
            if ($this->objMenuContent->getMenuTreeHierarchy()) {
                $updatedUrl = $this->objMenuContent->getMenuTreeHierarchy();
            } else {
                $updatedUrl = '/' . Q\QString::sanitizeForUrl($this->objMenuContent->getMenuText());
            }

            $objTemplateLocking = FrontendTemplateLocking::load(5);
            $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

            $this->makeGallery($this->objMenuContent->getMenuText());
            $fullPath = $this->strRootPath . "/" . QString::sanitizeForUrl(trim($this->objMenuContent->getMenuText()));
            $relativePath = $this->getRelativePath($fullPath);

            $objAddFolder = new Folders();
            $objAddFolder->setParentId(1);
            $objAddFolder->setPath($relativePath);
            $objAddFolder->setName(trim($this->objMenuContent->getMenuText()));
            $objAddFolder->setType('dir');
            $objAddFolder->setMtime(filemtime($fullPath));
            $objAddFolder->setLockedFile(0);
            $objAddFolder->setActivitiesLocked(1);
            $objAddFolder->save();

            $objGallerySettings = new GallerySettings();
            $objGallerySettings->setGalleryGroupId($this->objMenuContent->getId());
            $objGallerySettings->setName(trim($this->objMenuContent->getMenuText()));
            $objGallerySettings->setTitleSlug($this->objMenuContent->getRedirectUrl());
            $objGallerySettings->setIsReserved(1);
            $objGallerySettings->setGalleryGroupId($this->objMenuContent->getId());
            $objGallerySettings->setFolderId($objAddFolder->getId());
            $objGallerySettings->setPostDate(Q\QDateTime::now());
            $objGallerySettings->save();

            $this->objMenuContent->setHomelyUrl(1);
            $this->objMenuContent->save();

            $objFrontendLinks = new FrontendLinks();
            $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
            $objFrontendLinks->setContentTypesManagamentId(5);
            $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
            $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
            $objFrontendLinks->setFrontendTitleSlug($updatedUrl);
            $objFrontendLinks->setIsActivated(1);
            $objFrontendLinks->save();

            $objMetadata = new Metadata();
            $objMetadata->setMenuContentId($this->objMenuContent->Id);
            $objMetadata->save();

            Application::redirect('menu_edit.php?id=' . $this->intId);
        }

        /**
         * Creates a gallery directory structure based on the provided title.
         * Generates a root folder and multiple temporary subfolders within the defined paths.
         *
         * @param string $title The title used to create the gallery folder. It is sanitized for URL compatibility before being used as a directory name.
         *
         * @return void
         */
        protected function makeGallery(string $title): void
        {
            $fullPath = $this->strRootPath . "/" . QString::sanitizeForUrl(trim($title));
            $relativePath = $this->getRelativePath($fullPath);

            if ($this->strRootPath) {
                Folder::makeDirectory($fullPath, 0777);
            }

            foreach ($this->tempFolders as $tempFolder) {
                $tempPath = $this->strTempPath . '/_files/' . $tempFolder . $relativePath;
                Folder::makeDirectory($tempPath, 0777);
            }
        }

        /**
         * Calculates the relative path from the given absolute path by removing the predefined root path.
         *
         * @param string $path The absolute file or directory path for which the relative path will be determined.
         *
         * @return string The relative path derived by removing the root path from the provided absolute path.
         */
        protected function getRelativePath(string $path): string
        {
            return substr($path, strlen($this->strRootPath));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the Save button in the menu management interface.
         * This method performs validation on the input text and updates the menu content accordingly.
         * Depending on the content type, it also updates button labels for saving operations.
         * Notifications are displayed to indicate the result of the operation.
         *
         * @param ActionParams $params Parameters related to the action event that triggered this method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function btnMenuSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->txtMenuText->Text && !MenuContent::titleExists(trim($this->txtMenuText->Text))) {
                $this->objMenuContent->setMenuText($this->txtMenuText->Text);
                $this->objMenuContent->setSettingLocked(2);
                $this->objMenuContent->setIsEnabled(2);
                $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
                $this->objMenuContent->save();

                $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();

                if (is_null($this->objMenuContent->getContentType())) {
                    $strSave_translate = t('Save');
                    $strSaveAndClose_translate = t('Save and close');
                    Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strSave_translate');");
                    Application::executeJavaScript("jQuery($this->strSavingButtonId).text('$strSaveAndClose_translate');");
                } else {
                    $strUpdate_translate = t('Update');
                    $strUpdateAndClose_translate = t('Update and close');
                    Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strUpdate_translate');");
                    Application::executeJavaScript("jQuery($this->strSavingButtonId).text('$strUpdateAndClose_translate');");
                }

                $this->dlgToastr1->notify();
            } else if (!$this->txtMenuText->Text) {
                $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtMenuText->Text = '';
                $this->txtMenuText->focus();
                $this->dlgToastr2->notify();
            } else {
                $this->txtMenuText->Text = '';
                $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtMenuText->focus();
                $this->dlgToastr3->notify();
            }
        }

        /**
         * Handles the click event for the Save and Close button in the menu management interface.
         * This method validates the input and updates the menu content if valid. It also
         * redirects to the list page upon a successful operation or provides user feedback through
         * notifications if the operation fails.
         *
         * @param ActionParams $params Parameters related to the action event that triggered this method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        public function btnMenuSaveClose_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->txtMenuText->Text && !MenuContent::titleExists(trim($this->txtMenuText->Text))) {
                $this->objMenuContent->setMenuText($this->txtMenuText->Text);
                $this->objMenuContent->setSettingLocked(2);
                $this->objMenuContent->setIsEnabled(2);
                $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
                $this->objMenuContent->save();

                $this->redirectToListPage();
            } else if (!$this->txtMenuText->Text) {
                $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtMenuText->Text = '';
                $this->txtMenuText->focus();
                $this->dlgToastr2->notify();
            } else {
                $this->txtMenuText->Text = '';
                $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtMenuText->focus();
                $this->dlgToastr3->notify();
            }
        }

        /**
         * Handles the click event for the Cancel button in the menu management interface.
         * This method redirects the user to the list page, effectively canceling any current operations.
         *
         * @param ActionParams $params Parameters related to the action event that triggered this method.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnMenuCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->redirectToListPage();
        }

        /**
         * Redirects to the menu manager list page.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::redirect('menu_manager.php');
        }
    }