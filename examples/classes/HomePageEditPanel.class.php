<?php

    use QCubed as Q;

    use QCubed\Bootstrap as Bs;
    use QCubed\Control\Panel;
    use QCubed\Event\Click;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;

    /**
     * Represents a panel for editing content on the home page.
     *
     * This class provides functionalities for managing inputs, buttons, modals,
     * and notifications used within the home page edit panel.
     * It forms the backbone for creating and handling UI components necessary for
     * the user to edit menu content effectively.
     */
    class HomeEditPanel extends Panel
    {
        public Bs\Modal $dlgModal1;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;

        public Q\Plugin\Control\Label $lblExistingMenuText;
        public Q\Plugin\Control\Label $txtExistingMenuText;

        public Q\Plugin\Control\Label $lblMenuText;
        public Bs\TextBox $txtMenuText;

        public Q\Plugin\Control\Label $lblTitleSlug;
        public Q\Plugin\Control\Label $txtTitleSlug;

        public Bs\Button $btnSave;
        public Bs\Button $btnSaving;
        public Bs\Button $btnCancel;

        protected int $intId;
        protected object $objMenuContent;
        protected object $objFrontendLinks;

        protected string $strTemplate = 'HomePageEditPanel.tpl.php';

        /**
         * Class constructor that initializes the object, loads menu content and frontend links,
         * and sets up the required inputs, buttons, modals, and toast notifications.
         *
         * @param mixed $objParentObject The parent object that this control belongs to.
         * @param string|null $strControlId An optional control ID for the object.
         *
         * @return void
         * @throws Caller Thrown if there is an issue during the parent constructor call.
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
            $this->objMenuContent = MenuContent::load($this->intId);
            $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
            $this->createToastr();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures input fields and labels for the menu text and its associated data.
         * This method sets up labels and text boxes with required properties, styles, and events
         * necessary for managing the menu text input form.
         *
         * @return void
         * @throws Caller
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
            $this->txtMenuText->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtMenuText->addAction(new EnterKey(), new Terminate());
            $this->txtMenuText->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtMenuText->addAction(new EscapeKey(), new Terminate());
            $this->txtMenuText->setHtmlAttribute('required', 'required');

            $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
            $this->lblTitleSlug->Text = t('View');
            $this->lblTitleSlug->addCssClass('col-md-3');
            $this->lblTitleSlug->setCssStyle('font-weight', 400);

            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX;
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        }

        /**
         * Creates and configures the buttons used for menu actions, including save, save and close, and cancel
         * operations. Each button is styled and associated with its respective action events.
         *
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnSave = new Bs\Button($this);
            if (mb_strlen($this->objMenuContent->MenuText) > 0) {
                $this->btnSave->Text = t('Update');
            } else {
                $this->btnSave->Text = t('Save');
            }
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this,'btnMenuSave_Click'));

            $this->btnSaving = new Bs\Button($this);
            if (mb_strlen($this->objMenuContent->MenuText) > 0) {
                $this->btnSaving->Text = t('Update and close');
            } else {
                $this->btnSaving->Text = t('Save and close');
            }
            $this->btnSaving->CssClass = 'btn btn-darkblue';
            $this->btnSaving->addWrapperCssClass('center-button');
            $this->btnSaving->PrimaryButton = true;
            $this->btnSaving->addAction(new Click(), new AjaxControl($this,'btnMenuSaveClose_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this,'btnMenuCancel_Click'));
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
         * Initializes and configures multiple Toastr notification dialogs with
         * predefined alert types, positions, messages, and settings. These notifications
         * are used to provide feedback to the user based on specific conditions.
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
            $this->dlgToastr2->Message = t('The menu title must exist!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('The title of this menu item already exists in the database, please choose another title!');
            $this->dlgToastr3->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the save action for the menu button click event. This method
         * will check if the menu text is set, and if it doesn't already exist,
         * save the new menu content and update frontend links. It also provides
         * feedback notifications based on various conditions.
         *
         * @param ActionParams $params The parameters related to the action triggered by the button click.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function btnMenuSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->txtMenuText->Text && !MenuContent::titleExists(trim($this->txtMenuText->Text))) {
                $this->objMenuContent->setMenuText($this->txtMenuText->Text);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $this->objFrontendLinks->setLinkedId($this->intId);
                $this->objFrontendLinks->setContentTypesManagamentId(1);
                $this->objFrontendLinks->setTitle($this->txtMenuText->Text);
                $this->objFrontendLinks->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
                $this->objFrontendLinks->save();

                $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();

                $this->dlgToastr1->notify();
            } else if (!$this->txtMenuText->Text) {
                $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtMenuText->Text = '';
                $this->txtMenuText->focus();
                $this->dlgToastr2->notify();
            } else {
                $this->txtMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtMenuText->Text = $this->objMenuContent->getMenuText();
                $this->txtMenuText->focus();
                $this->dlgToastr3->notify();
            }
        }

        /**
         * Handles the save and close action for menu items. It checks if the menu text is valid and unique,
         * saves the menu content, updates frontend link associations, and redirects to the list page.
         * Notifies the user if the menu text is missing or already exists.
         *
         * @param ActionParams $params The parameters associated with the action event.
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
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->txtMenuText->Text && !MenuContent::titleExists(trim($this->txtMenuText->Text))) {
                $this->objMenuContent->setMenuText($this->txtMenuText->Text);
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->save();

                $this->objFrontendLinks->setLinkedId($this->intId);
                $this->objFrontendLinks->setContentTypesManagamentId(1);
                $this->objFrontendLinks->setTitle($this->txtMenuText->Text);
                $this->objFrontendLinks->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
                $this->objFrontendLinks->save();

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
         * Handles the click event for the menu cancel button.
         *
         * This method redirects the user to the list page when the cancel button is clicked.
         *
         * @param ActionParams $params The parameters associated with the cancel button click event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnMenuCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->redirectToListPage();
        }

        /**
         * Redirects the application to the list page for managing menus.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::redirect('menu_manager.php');
        }
    }