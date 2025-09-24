<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Event\Change;
    use QCubed\Event\Click;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Project\Application;
    use QCubed\Html;

    /**
     * A panel class responsible for rendering and managing the editing interface
     * for sports area menu items within the application.
     */
    class SportsAreasEditPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;

        public Q\Plugin\Control\Label $lblExistingMenuText;
        public Q\Plugin\Control\Label $txtExistingMenuText;

        public Q\Plugin\Control\Label $lblMenuText;
        public Bs\TextBox $txtMenuText;

        public Q\Plugin\Control\Label $lblContentType;
        public Q\Plugin\Select2 $lstContentTypes;

        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Q\Plugin\Control\Label $lblTitleSlug;
        public Q\Plugin\Control\Label $txtTitleSlug;

        public Bs\Button $btnSave;
        public Bs\Button $btnGoToMenu;
        public Bs\Button $btnGoToView;

        protected Q\Plugin\Control\Alert $lblInfo;

        protected int $intId;
        protected object $objMenu;
        protected object $objMenuContent;
        protected object $objFrontendLinks;

        protected string $strTemplate = 'SportsAreasEditPanel.tpl.php';

        /**
         * Constructor method for initializing the class. Sets up necessary properties
         * and creates input elements, buttons, modals, and notifications.
         *
         * @param mixed $objParentObject The parent object that owns this instance.
         * @param string|null $strControlId Optional unique identifier for the control.
         *
         * @throws Caller
         * @throws InvalidCast
         * @throws Exception
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            // Deleting sessions, if any.
            if (!empty($_SESSION['sports_view'])) {
                unset($_SESSION['sports_view']);
            }

            $this->intId = Application::instance()->context()->queryStringItem('id');
            $this->objMenu = Menu::load($this->intId);
            $this->objMenuContent = MenuContent::load($this->intId);
            $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Method to create and initialize input and label controls for the menu management interface.
         *
         * This method sets up various UI elements such as labels, textboxes, dropdown menus, and radio buttons.
         * It also applies necessary styles, attributes, and conditional configurations based on the current
         * menu content and properties.
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
            $this->txtMenuText->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtMenuText->addWrapperCssClass('center-button');
            $this->txtMenuText->Required = true;

            if ($this->objMenuContent->getContentType()) {
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
            $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems(), true);
            $this->lstContentTypes->SelectedValue = $this->objMenuContent->ContentType;
            $this->lstContentTypes->setHtmlAttribute('required', 'required');

            if ($this->objMenuContent->getContentType()) {
                $this->lstContentTypes->Enabled = false;
            }

            $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
            $this->lblTitleSlug->Text = t('View');
            $this->lblTitleSlug->addCssClass('col-md-3');
            $this->lblTitleSlug->setCssStyle('font-weight', 400);

            if ($this->objMenuContent->getRedirectUrl()) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objMenuContent->getRedirectUrl();
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
            $this->lstStatus->AddAction(new Change(), new AjaxControl($this, 'lstStatus_Click'));

//            if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
//                $this->lstStatus->Enabled = false;
//            }

            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Display = false;
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('Important information! A menu item and folder for this content type have been created at once. 
                                  It is not practical or possible to create more entries with this content type!');

            if ($this->objMenuContent->getContentType() === 10) {
                $this->lblInfo->Display = true;
            }
        }

        /**
         * Creates and initializes buttons for the interface, setting their properties and actions.
         *
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnGoToMenu = new Bs\Button($this);
            $this->btnGoToMenu->Text = t('Back to menu manager');
            $this->btnGoToMenu->CssClass = 'btn btn-default';
            $this->btnGoToMenu->addWrapperCssClass('center-button');
            $this->btnGoToMenu->CausesValidation = false;
            $this->btnGoToMenu->addAction(new Click(), new AjaxControl($this, 'btnGoToMenu_Click'));

            $this->btnGoToView = new Bs\Button($this);
            $this->btnGoToView->Text = t('Go to the linked documents overview');
            $this->btnGoToView->CssClass = 'btn btn-default';
            $this->btnGoToView->addWrapperCssClass('center-button');
            $this->btnGoToView->CausesValidation = false;
            $this->btnGoToView->addAction(new Click(), new AjaxControl($this, 'btnGoToView_Click'));
        }

        /**
         * Initializes and configures a set of modal dialog boxes for various user notifications and confirmations.
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The news group status of this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">These sports areas is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">These sports areas has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

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
         * Retrieves a filtered list of content type items from the ContentType class.
         * Items with disabled states, as determined by extra column values, will be excluded.
         *
         * @return array The filtered array of content type items.
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
         * Handles the click event for the status list. This method performs CSRF validation,
         * checks the state of the menu and content, and displays appropriate dialog boxes
         * depending on the evaluated conditions.
         *
         * @param ActionParams $params Parameters related to the action triggering this click event.
         *
         * @return void This method does not return a value but performs actions such as
         *              displaying dialog boxes or updating input fields based on certain conditions.
         * @throws RandomException
         * @throws Caller
         */
        public function lstStatus_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->dlgModal1->showDialogBox();
                $this->updateInputFields();
                return;
            }

            if ($this->objMenuContent->SelectedPageLocked === 1) {
                $this->dlgModal2->showDialogBox();
                $this->updateInputFields();
                return;
            }

            $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $this->objMenuContent->setSettingLocked($this->lstStatus->SelectedValue);
            $this->objMenuContent->save();

            if ($this->objMenuContent->getIsEnabled() === 2) {
                $this->dlgModal3->showDialogBox();
            } else if ($this->objMenuContent->getIsEnabled() === 1) {
                $this->dlgModal4->showDialogBox();
            }
        }

        /**
         * Updates the input fields based on the current status of the menu content.
         *
         * This method sets the selected value of the status list to reflect whether
         * the menu content is enabled or not by obtaining this information from the
         * associated menu content object.
         *
         * @return void
         * @throws Caller
         */
        private function updateInputFields(): void
        {
            $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the redirect operation triggered by clicking the 'Go to Menu' button.
         * Verifies the CSRF token before performing the redirection to the menu manager page.
         * If the token verification fails, it displays a modal dialog box and regenerates the CSRF token.
         *
         * @param ActionParams $params The parameters passed for the current action.
         *
         * @return void This method does not return a value.
         * @throws RandomException
         * @throws Throwable
         */
        public function btnGoToMenu_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::redirect('menu_manager.php');
        }

        /**
         * Handles the operation triggered by clicking the "Go To View" button.
         * Verifies the CSRF token, sets the session variable for the target view,
         * and redirects the user to the appropriate page.
         *
         * @param ActionParams $params The parameters passed for the current action.
         *
         * @return void This method does not return a value.
         * @throws RandomException
         * @throws Throwable
         */
        public function btnGoToView_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $_SESSION['sports_view'] = $this->intId;
            Application::redirect('sports_view.php');
        }
    }