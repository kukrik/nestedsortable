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
    use QCubed\Control\ListItem;
    use QCubed\Query\QQ;

    /**
     * The GalleryEditPanel class provides a control panel for editing gallery-related content in a web application.
     * It includes features such as modal dialogs, toast notifications, input fields, radio buttons,
     * select dropdowns, and buttons to help manage and edit gallery settings and menu configuration.
     *
     * Properties:
     * - Includes various modal dialogs for displaying additional information or confirmation messages.
     * - Provides toast message control for user notifications.
     * - Displays and handles input labels, text boxes, and radio list components for managing menu and gallery content.
     * - Defines buttons for various actions such as saving, navigating to gallery settings, or returning to a menu list.
     *
     * Methods:
     * - __construct: Initializes the panel, its associated controls, and prepares it for rendering.
     * - createInputs: Prepares input fields, labels, and dropdowns for initializing and modifying menu and gallery settings.
     * - createButtons: Initializes action buttons and configures their properties and event handlers.
     * - createToastr: Sets up instances of Toastr for user notification handling.
     * - createModals: Initializes modal dialogs for use in popup confirmation and additional interactions.
     */
    class GalleryEditPanel extends Panel
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

        public Q\Plugin\Control\Label $lblGroupTitle;

        public Q\Plugin\Control\Label $lblContentType;
        public Q\Plugin\Select2 $lstContentTypes;

        public Q\Plugin\Control\Label $lblGallerySettings;

        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Q\Plugin\Control\Label $lblTitleSlug;
        public Q\Plugin\Control\Label $txtTitleSlug;

        public Bs\Button $btnGoToGallerySettings;
        public Bs\Button $btnGoToList;
        public Bs\Button $btnSave;
        public Bs\Button $btnGoToMenu;

        protected int $intId;
        protected object $objMenu;
        protected object $objMenuContent;
        protected object $objGallerySettings;

        protected object $objGroupTitleCondition;
        protected ?array $objGroupTitleClauses;

        protected object $objGallerySettingsCondition;
        protected ?array $objGallerySettingsClauses;

        protected string $strTemplate = 'GalleryEditPanel.tpl.php';

        /**
         * Constructor method for initializing the object with a parent object and an optional control ID.
         * This method sets up session cleanup, creates input elements, buttons, toastr notifications, and modals.
         *
         * @param mixed $objParentObject The parent object that this control is a child of.
         * @param string|null $strControlId Optional unique identifier for the control; can be null.
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

            if (!empty($_SESSION['gallery_group-edit'])) {
                unset($_SESSION['gallery_group-edit']);
            }

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initialize and configure various QControl elements for menu management,
         * setting up text labels, input fields, dropdowns, and radio buttons.
         * This method uses information from the current application context and
         * database objects to configure controls, apply styles, and set their
         * respective properties and data bindings.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->intId = Application::instance()->context()->queryStringItem('id');
            $this->objMenu = Menu::load($this->intId);
            $this->objMenuContent = MenuContent::load($this->intId);
            $this->objGallerySettings = GallerySettings::loadByIdFromGallerySettings($this->intId);

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

            if ($this->objMenuContent->getMenuText()) {
                $this->txtMenuText->Enabled = false;
            }

            $this->lblGallerySettings = new Q\Plugin\Control\Label($this);
            $this->lblGallerySettings->Text = t('Editing a gallery group title');
            $this->lblGallerySettings->addCssClass('col-md-3');
            $this->lblGallerySettings->setCssStyle('font-weight', 400);

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
            $this->lstStatus->AddAction(new Change(), new AjaxControl($this,'lstStatus_Click'));
        }

        /**
         * Creates and configures a set of buttons for navigating between different managers.
         *
         * This method initializes three buttons: one for returning to the menu manager,
         * another for navigating to the albums manager, and the last one for accessing
         * the gallery settings manager. Each button is styled and assigned specific actions
         * to handle user interactions.
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
            $this->btnGoToList->Text = t('Go to the album manager');
            $this->btnGoToList->CssClass = 'btn btn-default';
            $this->btnGoToList->addWrapperCssClass('center-button');
            $this->btnGoToList->CausesValidation = false;
            $this->btnGoToList->addAction(new Click(), new AjaxControl($this, 'btnGoToList_Click'));

            if ($this->objMenuContent->getContentType()) {
                $this->btnGoToList->Display = true;
            } else {
                $this->btnGoToList->Display = false;
            }

            $this->btnGoToGallerySettings = new Bs\Button($this);
            $this->btnGoToGallerySettings->Text = t('Go to the gallery settings manager');
            $this->btnGoToGallerySettings->addWrapperCssClass('center-button');
            $this->btnGoToGallerySettings->CausesValidation = false;
            $this->btnGoToGallerySettings->addAction(new Click(), new AjaxControl($this,'btnGoToGallerySettings_Click'));
        }

        /**
         * Initializes and creates multiple modal dialogs for various status notifications.
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The gallery group status of this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This gallery group is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This gallery group has now been made public!</p>');
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
         * Retrieves a list of gallery settings as ListItem objects, applying specified conditions and clauses.
         *
         * @return ListItem[] An array of ListItem objects representing the gallery settings,
         *                    with appropriate selection and disability states based on the gallery settings properties.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstGallerySettings_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objGallerySettingsCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objGallerySettingsCursor = GallerySettings::queryCursor($objCondition, $this->objGallerySettingsClauses);

            // Iterate through the Cursor
            while ($objGallerySettings = GallerySettings::instantiateCursor($objGallerySettingsCursor)) {
                $objListItem = new ListItem($objGallerySettings->__toString(), $objGallerySettings->Id);
                if (($this->objMenuContent->GroupTitle) && ($this->objMenuContent->GroupTitle->Id == $objGallerySettings->Id))
                    $objListItem->Selected = true;
                if ($objGallerySettings->IsReserved == 1) {
                    $objListItem->Disabled = true;
                }
                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Retrieves an array of content type names, excluding items based on specific conditions.
         * This function filters out the second item (index 1) from the content type names array.
         * Additionally, it removes items where the 'IsEnabled' property is set to 0.
         *
         * @return array An array of filtered content type names where the second item and any disabled items have been removed.
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
         * Handles click events on the status list and performs actions based on the menu state and selected status.
         * This method checks several conditions related to menu hierarchy, page lock status, and the selected status value,
         * and triggers appropriate dialog boxes and updates data accordingly.
         *
         * @param ActionParams $params Parameters associated with the click action event.
         *
         * @return void This method does not return any value.
         * @throws RandomException
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

            $this->objMenuContent->setSettingLocked($this->lstStatus->SelectedValue);
            $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $this->objMenuContent->save();

            $this->objGallerySettings->setStatus($this->lstStatus->SelectedValue);
            $this->objGallerySettings->save();

            if ($this->objMenuContent->getIsEnabled() === 2) {
                $this->dlgModal3->showDialogBox();
            } else if ($this->objMenuContent->getIsEnabled() === 1) {
                $this->dlgModal4->showDialogBox();
            }
        }

        /**
         * Updates input fields based on the current state of the menu content object.
         * Specifically, sets the selected value of the status list to match the
         * enabled status of the menu content.
         *
         * @return void
         */
        private function updateInputFields(): void
        {
            $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the "Go To Gallery Settings" button.
         * This method sets a session variable to the current gallery group ID and redirects the user
         * to the settings manager page with a specific tab highlighted.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnGoToGallerySettings_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $_SESSION['gallery_group_edit'] = $this->intId;
            Application::redirect('settings_manager.php#galleriesSettings_tab');
        }

        /**
         * Handles the click event for the 'Go To List' button. This function will clear the 'gallery_group_edit'
         * session variable if it is set and then redirect the user to the gallery list page.
         *
         * @param ActionParams $params The parameters associated with the button click action.
         *
         * @return void This method does not return a value.
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

            if (!empty($_SESSION['gallery_group_edit'])) {
                unset($_SESSION['gallery_group_edit']);
            }

            Application::redirect('gallery_list.php');
        }

        /**
         * Handles the click event for the 'Go To Menu' button. This method clears a specific session variable
         * associated with gallery group editing and redirects the user to the menu manager page.
         *
         * @param ActionParams $params The parameters associated with the action event triggered by the button click.
         *
         * @return void This method does not return any value.
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

            if (!empty($_SESSION['gallery_group_edit'])) {
                unset($_SESSION['gallery_group_edit']);
            }

            Application::redirect('menu_manager.php');
        }
    }