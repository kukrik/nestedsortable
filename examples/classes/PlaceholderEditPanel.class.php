<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\DialogButton;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\Html;

    /**
     * Class PlaceholderEditPanel
     *
     * Represents a panel interface to manage placeholder menu items in the application.
     * This panel facilitates the creation and editing of placeholder menu content by offering
     * various input forms, dialogs, and controls. It organizes these options into a user-friendly,
     * responsive design suitable for user interaction.
     */
    class PlaceholderEditPanel extends Panel
    {
        public Q\Plugin\Control\Alert $lblInfo;

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

        public Bs\Button $btnBack;

        protected int $intId;
        protected object $objMenuContent;
        protected object $objMenu;

        protected string $strTemplate = 'PlaceholderEditPanel.tpl.php';

        /**
         * Constructor method for initializing the object.
         *
         * This method initializes the object by setting up its parent, handling exceptions,
         * and loading required data based on query string parameters. Additionally, it
         * triggers the creation of inputs, buttons, and modals essential for operation.
         *
         * @param mixed $objParentObject The parent object that will own this instance.
         * @param string|null $strControlId An optional control ID for uniquely identifying this instance.
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
            $this->objMenu = Menu::load($this->intId);
            $this->objMenuContent = MenuContent::load($this->intId);

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures various input controls for managing menu content.
         *
         * This method sets up multiple labels, text boxes, and selection controls to
         * facilitate the input of menu information, such as menu text, content type,
         * status, and other related properties. Several controls are associated with
         * styling and validation attributes to ensure a seamless user interaction.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Display = true;
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('This placeholder is not intended to direct or link a menu item. The purpose of this is
                                to create a link "#" in the main menu, under which can move the submenus of the menu tree
                                to a dropdown menu.');

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

            $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
            $this->lblTitleSlug->Text = t('View');
            $this->lblTitleSlug->addCssClass('col-md-3');
            $this->lblTitleSlug->setCssStyle('font-weight', 400);

            if ($this->objMenuContent->getRedirectUrl()) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $url = $this->objMenuContent->getRedirectUrl();
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
            $this->lstStatus->AddAction(new Click(), new AjaxControl($this,'lstStatus_Click'));

            if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->lstStatus->Enabled = false;
            }
        }

        /**
         * Initializes the back button for navigation in the menu manager interface.
         *
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back to menu manager');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->addWrapperCssClass('center-button');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new AjaxControl($this,'btnBack_Click'));
        }

        /**
         * Initializes and configures multiple modal dialogs with specific content, titles, and actions.
         *
         * Each modal is assigned text content, a title, header styling, and buttons with associated actions or dismiss behavior.
         * The modals are used for various prompts and confirmations within the application, providing feedback and requiring user interaction.
         *
         * @return void
         * @throws Caller
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the placeholder for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this placeholder?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this group public again later!</p>');
            $this->dlgModal3->Title = t('Question');
            $this->dlgModal3->HeaderClasses = 'btn-danger';
            $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal3->addCloseButton(t("I'll cancel"));
            $this->dlgModal3->addAction(new DialogButton(), new AjaxControl($this, 'statusItem_Click'));
            $this->dlgModal3->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'hideCancel_Click'));

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This placeholder is now hidden!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Success");
            $this->dlgModal5->HeaderClasses = 'btn-success';
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This placeholder has now been made public!</p>');
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves a filtered array of content type names.
         *
         * This method accesses an array of content type names, removes the first entry,
         * and then filters out any content types that are flagged as disabled in the
         * extra column values array. The result is a list of enabled content type names.
         *
         * @return array An array of enabled content type names, with specific entries removed.
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
         * Handles the click event for the lstStatus control.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        public function lstStatus_Click(ActionParams $params): void
        {
            if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->dlgModal1->showDialogBox();
            } else if ($this->objMenuContent->SelectedPageLocked === 1) {
                $this->dlgModal2->showDialogBox();
                $this->updateInputFields();
            } else if ($this->lstStatus->SelectedValue === 2) {
                $this->dlgModal3->showDialogBox();
            } else if ($this->lstStatus->SelectedValue === 1) {
                $this->dlgModal5->showDialogBox();

                $this->objMenuContent->setIsEnabled(1);
                $this->objMenuContent->save();
            }
        }

        /**
         * Updates the input fields for the menu content status.
         *
         * @return void
         * @throws Caller
         */
        protected function updateInputFields(): void
        {
            $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
        }

        /**
         * Handles the click event for a status item. This method updates the status of
         * a menu content item and manages the visibility of modal dialog boxes.
         *
         * @param ActionParams $params The parameters associated with the action triggering this method.
         *
         * @return void
         * @throws Caller
         */
        public function statusItem_Click(ActionParams $params): void
        {
            $this->lstStatus->SelectedValue = 2;

            $this->objMenuContent->setIsEnabled(2);
            $this->objMenuContent->save();

            $this->dlgModal3->hideDialogBox();
            $this->dlgModal4->showDialogBox();
        }

        /**
         * Handles the event when the cancel button is clicked. It sets the selected value of the status list
         * based on the enabled state of the menu content.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        public function hideCancel_Click(ActionParams $params): void
        {
            $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
        }

        /**
         * Handles the click event for the 'Back' button, redirecting the user to the list page.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Throwable
         */
        public function btnBack_Click(ActionParams $params): void
        {
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
    }