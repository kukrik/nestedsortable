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
     * BoardEditPanel is responsible for managing and editing the board content
     * within the administration interface. This panel provides various input fields,
     * dropdowns, buttons, and modals to interact with board settings and content.
     */
    class BoardEditPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;
        public Bs\Modal $dlgModal6;

        public Q\Plugin\Control\Label $lblExistingMenuText;
        public Q\Plugin\Control\Label $txtExistingMenuText;

        public Q\Plugin\Control\Label $lblMenuText;
        public Bs\TextBox $txtMenuText;

        public Q\Plugin\Control\Label $lblGroupTitle;

        public Q\Plugin\Control\Label $lblContentType;
        public Q\Plugin\Select2 $lstContentTypes;

        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Q\Plugin\Control\Label $lblTitleSlug;
        public Q\Plugin\Control\Label $txtTitleSlug;

        public Bs\Button $btnGoToBoards;
        public Bs\Button $btnGoToList;
        public Bs\Button $btnGoToMenu;

        protected int $intId;
        protected object $objMenu;
        protected object $objMenuContent;
        protected object $objBoardsSettings;
        protected int $intLoggedUserId;

        protected object $objGroupTitleCondition;
        protected ?array $objGroupTitleClauses;

        protected string $strTemplate = 'BoardEditPanel.tpl.php';

        /**
         * Initializes the instance of the class and sets up the initial state, including loading menu and board
         * settings, managing session data, and creating necessary UI components (inputs, buttons, modals, etc.).
         *
         * @param mixed $objParentObject The parent object that this control is rendered within.
         * @param string|null $strControlId [optional] The control ID used to uniquely identify this instance. Null to
         *     allow the framework to auto-generate one.
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

            if (!empty($_SESSION['board_edit_group'])) {
                unset($_SESSION['board_edit_group']);
            }

            $this->intId = Application::instance()->context()->queryStringItem('id');
            $this->objMenu = Menu::load($this->intId);
            $this->objMenuContent = MenuContent::load($this->intId);
            $this->objBoardsSettings = BoardsSettings::loadByIdFromBoardSettings($this->intId);


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
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the input controls for a menu content management.
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
            $this->txtMenuText->MaxLength = MenuContent::MENU_TEXT_MAX_LENGTH;
            $this->txtMenuText->Required = true;

            if ($this->objBoardsSettings->getIsReserved() == 1) {
                $this->txtMenuText->Enabled = false;
            }

            $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
            $this->lblGroupTitle->Text = t('Editing a board group title');
            $this->lblGroupTitle->addCssClass('col-md-3');
            $this->lblGroupTitle->setCssStyle('font-weight', 400);

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
            $this->lstStatus->AddAction(new Change(), new AjaxControl($this,'lstStatus_Change'));
        }

        /**
         * Creates and configures buttons for navigating between different managers.
         *
         * This method initializes buttons for menu manager, boards manager, and board settings manager.
         * It sets text, CSS classes, and clicks actions for each button.
         * The visibility of the boards manager button is determined based on the content type.
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

            $this->btnGoToList = new Bs\Button($this);
            $this->btnGoToList->Text = t('Go to the boards manager');
            $this->btnGoToList->CssClass = 'btn btn-default';
            $this->btnGoToList->addWrapperCssClass('center-button');
            $this->btnGoToList->CausesValidation = false;
            $this->btnGoToList->addAction(new Click(), new AjaxControl($this, 'btnGoToList_Click'));

            if ($this->objMenuContent->getContentType()) {
                $this->btnGoToList->Display = true;
            } else {
                $this->btnGoToList->Display = false;
            }

            $this->btnGoToBoards = new Bs\Button($this);
            $this->btnGoToBoards->Text = t('Go to the board settings manager');
            $this->btnGoToBoards->addWrapperCssClass('center-button');
            $this->btnGoToBoards->CausesValidation = false;
            $this->btnGoToBoards->addAction(new Click(), new AjaxControl($this,'btnGoToBoards_Click'));
        }

        /**
         * Creates and configures multiple modal dialogs used for displaying various informational and confirmation
         * messages to the user.
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the board group for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This board group is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This board group has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal5->Title = t("Warning");
            $this->dlgModal5->HeaderClasses = 'btn-danger';
            $this->dlgModal5->addCloseButton(t("I understand"));

            ///////////////////////////////////////////////////////////////////////////////////////////
            // ADDED FOR BOARD WARNING

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Title = t("Tip");
            $this->dlgModal6->HeaderClasses = 'btn-darkblue';
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The board members are either selected and hidden, or not added yet, so it is not appropriate to make the board group public.</p>');
            $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves an array of content type names, excluding certain items based on specified conditions.
         *
         * @return array An array of content type names with exclusions applied where 'IsEnabled' is 0.
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
         * Handles the click event for the status list, triggering different dialog boxes and updating content
         * based on the status and conditions of the menu and menu content.
         *
         * @param ActionParams $params The parameters associated with the action event, providing context for the click event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function lstStatus_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objBoards = Board::loadArrayByMenuContentGroupId($this->intId);
            $countBoards = Board::countByMenuContentGroupId($this->intId);
            $enabledBoardStatus = 0;

            foreach ($objBoards as $objStatus) {
                if ($objStatus->Status == 1) {
                    $enabledBoardStatus++;
                }
            }

            // Prevent enabling (publishing) the board group if there are either no board members added
            // or all existing board members are currently hidden (status != 1). Show a warning dialog instead.
//            if ($enabledBoardStatus === 0 || $countBoards === 0) {
//                $this->dlgModal6->showDialogBox();
//                $this->updateInputFields();
//                return;
//            }

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

            $this->objBoardsSettings->setStatus($this->lstStatus->SelectedValue);
            $this->objBoardsSettings->save();

            if ($this->objMenuContent->getIsEnabled() === 2) {
                $this->dlgModal3->showDialogBox();
            } else if ($this->objMenuContent->getIsEnabled() === 1) {
                $this->dlgModal4->showDialogBox();
            }
        }

        /**
         * Updates the selected value of the status input field based on the enabled status
         * of the current menu content.
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
         * Handles the click event for the "Go to Boards" button, setting session parameters and redirecting the user.
         *
         * @param ActionParams $params The parameters associated with the action, typically provided by the event system.
         *
         * @return void No return value as the method performs a session variable assignment and a page redirect.
         * @throws RandomException
         * @throws Throwable
         */
        public function btnGoToBoards_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $_SESSION['board_edit_group'] = $this->intId;
            Application::redirect('settings_manager.php#boardsSettings_tab');
        }

        /**
         * Handles the click event for the 'Go To List' button and redirects the user to the board list page.
         *
         * @param ActionParams $params The parameters provided by the action triggering this method.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnGoToList_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::redirect('board_list.php');
        }

        /**
         * Handles the action for navigating to the menu management page.
         *
         * @param ActionParams $params Parameters associated with the action event.
         *
         * @return void
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
    }