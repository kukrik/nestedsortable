<?php

    use QCubed as Q;
    use QCubed\Bootstrap as Bs;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Event\KeyUp;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Project\Application;
    use QCubed\Html;

    /**
     * Class RedirectingEditPanel
     *
     * This class is designed to provide a user interface for editing and managing menu redirects within the application.
     * It extends the Q\Control\Panel and includes functionality for a variety of input fields, labels, and controls
     * to manage menu content, external URLs, and associated settings.
     */
    class RedirectingEditPanel extends Q\Control\Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;
        public Bs\Modal $dlgModal6;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;

        public Q\Plugin\Control\Label $lblExistingMenuText;
        public Q\Plugin\Control\Label $txtExistingMenuText;

        public Q\Plugin\Control\Label $lblMenuText;
        public Bs\TextBox $txtMenuText;

        public Q\Plugin\Control\Label $lblContentType;
        public Q\Plugin\Select2 $lstContentTypes;

        public Q\Plugin\Control\Label $lblRedirect;
        public ?Bs\TextBox $txtRedirect = null;

        public Q\Plugin\Control\Label $lblTargetTypeObject;
        public Q\Plugin\Select2 $lstTargetTypeObject;

        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Q\Plugin\Control\Label $lblTitleSlug;
        public Q\Plugin\Control\Label $txtTitleSlug;

        public Bs\Button $btnSave;
        public Bs\Button $btnBack;

        protected string $strSaveButtonId;

        protected int $intId;
        protected object $objMenuContent;
        protected object $objMenu;

        protected string $strTemplate = 'RedirectingEditPanel.tpl.php';

        /**
         * Creates a ControlBase object
         * This constructor will generally not be used to create a QCubed Control object. Instead, it is used by the
         * classes which extend the class.  Only the parent object parameter is required. If the option strControlId
         * parameter is not used, QCubed will generate the ID.
         *
         * @param mixed $objParentObject
         * @param string|null $strControlId
         *   an optional ID of this Control. In HTML, this will be set as the value of the ID attribute. The ID can only
         *   contain alphanumeric characters.  If this parameter is not passed, QCubed will generate the ID.
         *
         * @throws Caller
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

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the UI components for managing menu inputs, including labels, textboxes, select controls, and other input elements.
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
            $this->txtMenuText->MaxLength = MenuContent::MENU_TEXT_MAX_LENGTH;
            $this->txtMenuText->setHtmlAttribute('required', 'required');

            $this->txtMenuText->Enabled = false;

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
            $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems(), true);
            $this->lstContentTypes->SelectedValue = $this->objMenuContent->ContentType;
            $this->lstContentTypes->addAction(new Change(), new AjaxControl($this,'lstClassNames_Change'));
            $this->lstContentTypes->setHtmlAttribute('required', 'required');

            $this->lstContentTypes->Enabled = false;

            $this->lblRedirect = new Q\Plugin\Control\Label($this);
            $this->lblRedirect->Text = t('Redirecting url');
            $this->lblRedirect->addCssClass('col-md-3');
            $this->lblRedirect->setCssStyle('font-weight', 400);
            $this->lblRedirect->Required = true;

            $this->txtRedirect = new Bs\TextBox($this);
            $this->txtRedirect->Placeholder = 'https://';
            $this->txtRedirect->Text = $this->objMenuContent->ExternalUrl;
            $this->txtRedirect->addWrapperCssClass('center-button');
            $this->txtRedirect->setHtmlAttribute('required', 'required');
            $this->txtRedirect->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtRedirect->addAction(new EnterKey(), new Q\Action\Terminate());
            $this->txtRedirect->AddAction(new EscapeKey(), new AjaxControl($this,'itemEscape_Click'));
            $this->txtRedirect->addAction(new EscapeKey(), new Q\Action\Terminate());
            $this->txtRedirect->AddAction(new KeyUp(), new AjaxControl($this, 'lstTargetTypeObject_KeyUp'));

            $this->lblTargetTypeObject = new Q\Plugin\Control\Label($this);
            $this->lblTargetTypeObject->Text = t('Target type');
            $this->lblTargetTypeObject->addCssClass('col-md-3');
            $this->lblTargetTypeObject->setCssStyle('font-weight', 400);

            $this->lstTargetTypeObject = new Q\Plugin\Select2($this);
            $this->lstTargetTypeObject->MinimumResultsForSearch = -1;
            $this->lstTargetTypeObject->Theme = 'web-vauu';
            $this->lstTargetTypeObject->Width = '100%';
            $this->lstTargetTypeObject->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstTargetTypeObject->addItem(t('- Select target type -'), null, true);
            $this->lstTargetTypeObject->addItems($this->lstTargetTypeObject_GetItems());
            $this->lstTargetTypeObject->SelectedValue = $this->objMenuContent->TargetType;

            if (!$this->txtRedirect->Text) {
                $this->lstTargetTypeObject->Enabled = false;
            } else {
                $this->lstTargetTypeObject->Enabled = true;
            }

            $this->lstTargetTypeObject->addAction(new Change(), new AjaxControl($this,'lstTargetTypeObject_Change'));

            $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
            $this->lblTitleSlug->Text = t('View');
            $this->lblTitleSlug->addCssClass('col-md-3');
            $this->lblTitleSlug->setCssStyle('font-weight', 400);

            if ($this->objMenuContent->getExternalUrl()) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $url = $this->objMenuContent->getExternalUrl();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->addCssClass('col-md-3');
            $this->lblStatus->Required = true;

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstStatus->SelectedValue = $this->objMenuContent->IsEnabled;
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstStatus->AddAction(new Change(), new AjaxControl($this,'lstStatus_Change'));
        }

        /**
         * Creates and configures two buttons: save a button and a back button.
         * Configures the save a button with text based on the presence of a redirect URL,
         * assigns CSS classes and actions to both buttons, and assigns control IDs for further use.
         *
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnSave = new Bs\Button($this);
            if ($this->objMenuContent->getExternalUrl()) {
                $this->btnSave->Text = t('Update');
            } else {
                $this->btnSave->Text = t('Save');
            }
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this,'btnMenuSave_Click'));
            // The variable below is being prepared for fast transmission
            $this->strSaveButtonId = $this->btnSave->ControlId;

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back to menu manager');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->addWrapperCssClass('center-button');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new AjaxControl($this,'btnBack_Click'));
        }

        /**
         * Initializes toastr notifications with predefined settings for success, error, and info alerts.
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
            $this->dlgToastr2->Message = t('Redirecting url must exist!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Well done!</strong> Updates to some records for this post were discarded, and the record has been restored!');
            $this->dlgToastr3->ProgressBar = true;
        }

        /**
         * Initializes and creates multiple modal dialogs with pre-defined content,
         * titles, header styles, and buttons for user interaction. The dialogs
         * are used to provide tips, confirmations, or success messages related to
         * menu item status and redirects.
         *
         * @return void
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Currently, the status of this item cannot be changed as it is associated 
                                    with submenu items or the parent menu item.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">To change the status of this item, you need to go to the menu manager 
                                    and activate or deactivate it there.</p>');
            $this->dlgModal1->Title = t("Tip");
            $this->dlgModal1->HeaderClasses = 'btn-darkblue';
            $this->dlgModal1->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the internal page link for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This internal page link is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This internal page link has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Please select a page to redirect!</p>');
            $this->dlgModal5->Title = t("Tip");
            $this->dlgModal5->HeaderClasses = 'btn-darkblue';
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

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
         * Retrieves an array of content type names with certain content types removed.
         * Specifically, the content type with index 1 is always removed. Additionally,
         * any content type with extra column values indicating it is not enabled will
         * also be removed from the array.
         *
         * @return array The filtered array of content type names.
         */
        public function lstContentTypeObject_GetItems(): array
        {
            $strContentTypeArray = ContentType::nameArray();
            unset($strContentTypeArray[1]);

            $extraColumnValuesArray = ContentType::extraColumnValuesArray();
            for ($i = 1; $i < count($extraColumnValuesArray); $i++) {
                if ($extraColumnValuesArray[$i]['IsEnabled'] == 0) {
                    unset($strContentTypeArray[$i]);
                }
            }
            return $strContentTypeArray;
        }

        /**
         * Retrieves an array of target type names.
         *
         * @return array An array containing the names of target types.
         */
        public function lstTargetTypeObject_GetItems(): array
        {
            return TargetType::nameArray();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the change event for the target type object list.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function lstTargetTypeObject_Change(ActionParams $params): void
        {
            if (!$this->verifyCsrfOrModal()) {
                return;
            }

            if ($this->lstTargetTypeObject->SelectedValue !== $this->objMenuContent->getTargetType()) {
                $this->objMenuContent->setExternalUrl($this->txtRedirect->Text);
                $this->objMenuContent->setTargetType($this->lstTargetTypeObject->SelectedValue);
                $this->objMenuContent->save();

                $this->dlgToastr1->notify();

                $url = $this->objMenuContent->getExternalUrl();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->dlgToastr2->notify();
                $this->txtRedirect->focus();
            }
        }

        /**
         * Handles the change event of the status list. Executes different actions based on the current state
         * of the object properties and the selected value of the status list.
         *
         * @param ActionParams $params The parameters passed to the action.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function lstStatus_Change(ActionParams $params): void
        {
            if (!$this->verifyCsrfOrModal()) {
                return;
            }

            if ($this->txtRedirect->Text === '') {
                $this->dlgToastr2->notify();
                $this->lstStatus->SelectedValue = 2;
                $this->txtRedirect->focus();
            } else if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->dlgModal1->showDialogBox();
                $this->updateInputFields();
            } else if ($this->objMenuContent->SelectedPageLocked === 1) {
                $this->dlgModal2->showDialogBox();
                $this->updateInputFields();
            } else {
                if ($this->objMenuContent->IsEnabled === 1) {
                    $this->objMenuContent->setIsEnabled(2);
                    $this->dlgModal3->showDialogBox();
                } else {
                    $this->objMenuContent->setIsEnabled(1);
                    $this->dlgModal4->showDialogBox();
                }

                $this->objMenuContent->save();
            }
        }

        /**
         * Updates the selected value of the status list control based on the
         * enabled status of the associated menu content object.
         *
         * @return void
         * @throws Caller
         */
        public function updateInputFields(): void
        {
            $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
            $this->lstStatus->refresh();
        }
        /**
         * Handles the KeyUp event for the lstTargetTypeObject. Enables or disables
         * the lstTargetTypeObject based on the presence of a text in txtRedirect.
         * Clears related properties if no text is present.
         *
         * @param ActionParams $params Contains parameters related to the action or event.
         *
         * @return void
         * @throws Caller
         */
        public function lstTargetTypeObject_KeyUp(ActionParams $params): void
        {
            if ($this->txtRedirect->Text) {
                $this->lstTargetTypeObject->Enabled = true;
            } else {
                $this->lstTargetTypeObject->Enabled = false;

                $this->objMenuContent->setExternalUrl(null);
                $this->objMenuContent->setTargetType(null);
                $this->objMenuContent->save();

                $this->txtRedirect->Text = '';
                $this->lstTargetTypeObject->SelectedValue = null;
            }
        }

        /**
         * Handles the click event for the item escape action.
         *
         * Updates UI elements based on the current state of the menu content.
         * If a cancellation id is available, it triggers a notification.
         * Adjusts the redirect URL text field and target type selection
         * based on the presence of a redirect URL.
         *
         * @param ActionParams $params The parameters associated with the action.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
            if (!$this->verifyCsrfOrModal()) {
                return;
            }

            $objCancel = $this->objMenuContent->getId();

            // Check if $objCancel is available
            if ($objCancel) {
                $this->dlgToastr3->notify();
            }

            $this->txtRedirect->Text = $this->objMenuContent->getExternalUrl();
            $this->lstTargetTypeObject->SelectedValue = $this->objMenuContent->getTargetType();

            if ($this->objMenuContent->getExternalUrl()) {
                $this->lstTargetTypeObject->Enabled = true;
            } else {
                $this->lstTargetTypeObject->Enabled = false;
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the save action for the menu button. This method manages the redirect URL and
         * other related properties for the menu content. It checks if there is a redirect URL
         * set; if not, it assigns default values. Otherwise, it updates the menu content
         * with the current inputs and saves these settings.
         *
         * @param ActionParams $params The parameters associated with the action triggering the method.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        function btnMenuSave_Click(ActionParams $params): void
        {
            if (!$this->verifyCsrfOrModal()) {
                return;
            }

            if ($this->txtRedirect->Text === '' && !$this->objMenuContent->getExternalUrl()) {

                $this->objMenuContent->setExternalUrl(null);
                $this->objMenuContent->setHomelyUrl(null);
                $this->objMenuContent->setIsRedirect(null);
                $this->objMenuContent->setIsEnabled(2);
                $this->objMenuContent->setTargetType(null);

                $this->objMenuContent->save();

                $this->txtRedirect->Text = $this->objMenuContent->getExternalUrl();
                $this->lstStatus->SelectedValue = 2;
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
                $this->txtRedirect->focus();
                $this->dlgToastr2->notify();
            } else {
                $this->objMenuContent->setExternalUrl($this->txtRedirect->Text);
                $this->objMenuContent->setHomelyUrl(null);
                $this->objMenuContent->setIsRedirect(1);
                $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
                $this->objMenuContent->setTargetType($this->lstTargetTypeObject->SelectedValue);

                $this->objMenuContent->save();

                $this->dlgToastr1->notify();

                $this->txtRedirect->Text = $this->objMenuContent->getExternalUrl();

                if ($this->objMenuContent->getExternalUrl()) {
                    $url = $this->objMenuContent->getExternalUrl();
                    $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                    $this->txtTitleSlug->HtmlEntities = false;
                    $this->txtTitleSlug->setCssStyle('font-weight', 400);
                } else {
                    $this->txtTitleSlug->Text = t('Uncompleted link...');
                    $this->txtTitleSlug->setCssStyle('color', '#999;');
                }

                if (!$this->objMenuContent->getExternalUrl()) {
                    $strSave_translate = t('Save');
                    Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strSave_translate');");
                } else {
                    $strUpdate_translate = t('Update');
                    Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strUpdate_translate');");
                }
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the back button.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnBack_Click(ActionParams $params): void
        {
            if (!$this->verifyCsrfOrModal()) {
                return;
            }

            $this->redirectToListPage();
        }

        /**
         * Redirects the user to the menu manager list page.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            Application::redirect('menu_manager.php');
        }

        /**
         * Verifies the CSRF token or displays a modal dialog if verification fails.
         * If the CSRF token is invalid, a new token is generated, and the modal dialog is shown.
         *
         * @return bool Returns true if the CSRF token is valid, otherwise false.
         * @throws RandomException
         */
        private function verifyCsrfOrModal(): bool
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal6->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return false;
            }
            return true;
        }
    }