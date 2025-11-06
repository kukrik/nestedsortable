<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use Random\RandomException;
    use QCubed\Project\Application;
    use QCubed\Html;
    use QCubed\Control\ListItem;
    use QCubed\Query\QQ;

    /**
     * Represents a panel designed to facilitate editing of internal page attributes and configurations.
     *
     * The InternalPageEditPanel class extends the base Panel class to provide a specialized user interface
     * for editing properties of an internal page, such as menu text, content type, status, and redirection
     * options. It includes support for modal dialogs, form controls, and feedback via notifications.
     *
     * Properties:
     * - Includes several modal components for dialog operations.
     * - Uses label controls for displaying associated text within the form.
     * - Includes specific input fields such as textboxes, radio lists, and dropdowns to configure menu content.
     * - Features a back button for navigation.
     * - Contains configuration-specific properties for managing internal logic, such as menu-related objects and clauses.
     * - Utilizes a dedicated template for rendering the panel layout.
     *
     * Constructor:
     * The constructor initializes the panel, sets up the environment and properties, and loads associated menu data and content.
     * It also organizes the creation of input controls, buttons, toast notifications, and modals.
     */
    class InternalPageEditPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;
        public Bs\Modal $dlgModal6;

        protected Q\Plugin\Toastr $dlgToastr1;

        public Q\Plugin\Control\Label $lblExistingMenuText;
        public Q\Plugin\Control\Label $txtExistingMenuText;

        public Q\Plugin\Control\Label $lblMenuText;
        public Bs\TextBox $txtMenuText;

        public Q\Plugin\Control\Label $lblContentType;
        public Q\Plugin\Select2 $lstContentTypes;

        public Q\Plugin\Control\Label $lblSelectedPage;
        public Q\Plugin\Select2 $lstSelectedPage;

        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Q\Plugin\Control\Label $lblTitleSlug;
        public Q\Plugin\Control\Label $txtTitleSlug;

        public Bs\Button $btnBack;

        protected int $intId;
        protected object $objMenuContent;
        protected object $objMenu;
        protected object $objArticle;
        protected array $objMenuArray;

        protected Panel $strDoubleRoutingInfo;

        protected object $objSelectedPageCondition;
        protected ?array $objSelectedPageClauses = null;

        protected string $strTemplate = 'InternalPageEditPanel.tpl.php';

        /**
         * Constructor for initializing the component with its parent object and optional control ID.
         * Handles setup of necessary inputs, buttons, toast notifications, and modals.
         *
         * @param mixed $objParentObject The parent object that will contain this control.
         * @param string|null $strControlId Optional control ID for identifying the control.
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

            $this->objMenuArray = Menu::loadAll(QQ::Clause(QQ::OrderBy(QQN::menu()->Left), QQ::expand(QQN::menu()->MenuContent)));

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must save something here $this->objArticle->setUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session
            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);


            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Updates the user's last active timestamp to the current time and saves the changes to the user object.
         *
         * @return void The method does not return a value.
         * @throws Caller
         */
        private function userOptions(): void
        {
            $this->objUser->setLastActive(QDateTime::now());
            $this->objUser->save();
        }

        /**
         * Creates and configures user interface components for form inputs related to menu content.
         *
         * This method initializes and sets up various form controls, such as labels, text boxes, and select lists,
         * which are used to display and modify properties of a menu item, including menu text, content type, selected page,
         * status, and redirection information. Each control is configured with attributes and styles appropriate for the form.
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
            $this->lstContentTypes->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems(), true);
            $this->lstContentTypes->SelectedValue = $this->objMenuContent->ContentType;
            $this->lstContentTypes->addAction(new Change(), new AjaxControl($this,'lstClassNames_Change'));

            $this->lstContentTypes->Enabled = false;

            $this->lblSelectedPage = new Q\Plugin\Control\Label($this);
            $this->lblSelectedPage->Text = t('Internal page redirect');
            $this->lblSelectedPage->addCssClass('col-md-3');
            $this->lblSelectedPage->setCssStyle('font-weight', 400);
            $this->lblSelectedPage->Required = true;

            $this->lstSelectedPage = new Q\Plugin\Select2($this);
            $this->lstSelectedPage->MinimumResultsForSearch = -1;
            $this->lstSelectedPage->Theme = 'web-vauu';
            $this->lstSelectedPage->Width = '100%';
            $this->lstSelectedPage->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstSelectedPage->addItem(t('- Select one internal page -'), null, true);
            $this->lstSelectedPage->addItems($this->lstSelectedPage_GetItems(),null, null);
            $this->lstSelectedPage->SelectedValue = $this->objMenuContent->SelectedPageId;
            $this->lstSelectedPage->addAction(new Change(), new AjaxControl($this,'lstSelectedPage_Change'));

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->addCssClass('col-md-3');
            $this->lblStatus->Required = true;

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstStatus->SelectedValue = $this->objMenuContent->IsEnabled;
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstStatus->AddAction(new Change(), new AjaxControl($this,'lstStatus_Click'));

            $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
            $this->lblTitleSlug->Text = t('View');
            $this->lblTitleSlug->addCssClass('col-md-3');
            $this->lblTitleSlug->setCssStyle('font-weight', 400);

                if ($this->objMenuContent->getRedirectUrl()) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);

                if ($this->objMenuContent->getIsRedirect() == null  || $this->objMenuContent->getIsRedirect() == 2) {
                    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX . $this->objMenuContent->getRedirectUrl();
                } else {
                    $url = $this->objMenuContent->getRedirectUrl();
                }
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            $this->strDoubleRoutingInfo = new Panel($this);
            $this->strDoubleRoutingInfo->TagName = 'span';
            $this->strDoubleRoutingInfo->Text = $this->buildDoubleRoutingInfo($this->objMenuContent->getSelectedPageId());
        }

        /**
         * Initializes and configures the buttons used within the control.
         * Specifically, it creates a 'Back' button with particular text, styling,
         * and actions associated with it. The button does not trigger form validation
         * and includes an AJAX action on a click.
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
         * Initializes multiple Toastr notifications with varying alert types and messages.
         *
         * This method configures four Toastr notifications with predefined alert types,
         * positions, messages, and progress bar visibility. The alerts include
         * success and error messages based on various conditions.
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
        }

        /**
         * Initializes and configures multiple modal dialog instances with specific texts, titles, and actions.
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
         * Retrieves an array of content types with specific conditions applied.
         * The array of content types is filtered to exclude a specific item
         * and those that are not enabled.
         *
         * @return array An associative array of content types with filtered conditions.
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
         * Retrieves a list of menu items and converts them into list items for selection.
         * The method processes all menu items, marks certain items as selected if they match
         * the selected page IDs, and disables items not enabled.
         *
         * @return ListItem[] An array of ListItem objects representing the menu items,
         *                    where certain items are marked as selected or disabled based
         *                    on their properties.
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstSelectedPage_GetItems(): array
        {
            $a = array();
            $selectedPages = array();

            foreach ($this->objMenuArray as $objMenu) {
                if ($objMenu->MenuContent->SelectedPageId) {
                    $selectedPages[] = $objMenu->MenuContent->SelectedPageId;
                }
            }

            foreach ($this->objMenuArray as $objMenu) {
                $objListItem = new ListItem($this->printDepth($objMenu->MenuContent->MenuText, $objMenu->ParentId, $objMenu->Depth), $objMenu->MenuContent->Id);
                if (in_array($objMenu->MenuContent->Id, $selectedPages)) {
                    $objListItem->Selected = true;
                }
                if ($objMenu->MenuContent->SettingLocked == 2 || $objMenu->MenuContent->Id == $this->intId) {
                    $objListItem->Disabled = true;
                }
                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Handles changes to the selected page and updates various properties
         * of the menu content accordingly. This method performs various operations
         * including setting URLs, handling redirects, updating status, and managing
         * frontend link records.
         *
         * @param ActionParams $params The parameters passed during action triggering.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstSelectedPage_Change(ActionParams $params): void
        {
            if (!$this->verifyCsrfOrModal()) {
                return;
            }

            if ($this->lstSelectedPage->SelectedValue) {
                $this->objMenuContent->setHomelyUrl(1);
                $this->objMenuContent->setIsRedirect(2);
                $this->objMenuContent->setSelectedPageId($this->lstSelectedPage->SelectedValue);
                $this->dlgToastr1->notify();
            } else {
                $this->objMenuContent->setIsRedirect(null);
                $this->objMenuContent->setRedirectUrl(null);
                $this->objMenuContent->setInternalUrl(null);
                $this->objMenuContent->setSelectedPageId(null);
                $this->objMenuContent->setIsEnabled(2);

                $strSelectedValues = $this->getFullChildren($this->objMenuArray, $this->intId);

                if (count($strSelectedValues) > 0) {
                    foreach ($strSelectedValues as $strSelectedValue) {
                        $objMenuContent = MenuContent::load($strSelectedValue);
                        $objMenuContent->setIsEnabled(2);
                        $objMenuContent->save();
                    }
                }

                $this->lstStatus->SelectedValue = 2;
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->strDoubleRoutingInfo->Text = '';
                $this->dlgModal3->showDialogBox();
            }

            if ($this->objMenuContent->getSelectedPageId()) {
                $objRedirectUrl = MenuContent::load($this->objMenuContent->SelectedPageId);
                if ($objRedirectUrl->getId() == $this->objMenuContent->getSelectedPageId() && $objRedirectUrl->getIsRedirect() == 2) {
                    $this->objMenuContent->setRedirectUrl($objRedirectUrl->getRedirectUrl());
                    $this->objMenuContent->setInternalUrl($objRedirectUrl->getRedirectUrl());
                } else {
                    $this->objMenuContent->setRedirectUrl($objRedirectUrl->getRedirectUrl());
                    $this->objMenuContent->setInternalUrl($objRedirectUrl->getRedirectUrl());
                    $this->objMenuContent->setIsRedirect(2);
                }
            }

            $this->lstStatus->refresh();
            $this->txtTitleSlug->refresh();
            $this->strDoubleRoutingInfo->refresh();
            $this->objMenuContent->save();

            $objSelectedPageLockedArray = MenuContent::loadAll();
            $objIdToObject = [];
            $objIdArray = [];
            $objPageIdArray = [];

            // We prepare the necessary data in one cycle
            foreach ($objSelectedPageLockedArray as $objSelectedPageLocked) {
                $id = $objSelectedPageLocked->getId();
                $objIdToObject[$id] = $objSelectedPageLocked;
                $objIdArray[] = $id;

                $selectedPageId = $objSelectedPageLocked->getSelectedPageId();
                if ($selectedPageId !== null) {
                    $objPageIdArray[] = $selectedPageId;
                }
            }

            // Find which IDs match and which don't
            $objTrueArray = array_intersect($objPageIdArray, $objIdArray);
            $objFalseArray = array_diff($objIdArray, $objPageIdArray);

            // Change lock tag on prepared objects, avoid double-loading
            foreach ($objTrueArray as $id) {
                $objIdToObject[$id]->setSelectedPageLocked(1);
                $objIdToObject[$id]->save();
            }
            foreach ($objFalseArray as $id) {
                $objIdToObject[$id]->setSelectedPageLocked(0);
                $objIdToObject[$id]->save();
            }

            if ($this->objMenuContent->getRedirectUrl()) {
                if ($this->objMenuContent->getIsRedirect() == null || $this->objMenuContent->getIsRedirect() == 2) {
                    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                        $this->objMenuContent->getRedirectUrl();
                } else {
                    $url = $this->objMenuContent->getRedirectUrl();
                }

                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            $this->strDoubleRoutingInfo = new Panel($this);
            $this->strDoubleRoutingInfo->TagName = 'span';
            $this->strDoubleRoutingInfo->Text = $this->buildDoubleRoutingInfo($this->objMenuContent->getSelectedPageId());

            $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

            if (!$objFrontendLink) {
                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
                $objFrontendLinks->setContentTypesManagamentId(7);
                $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
                $objFrontendLinks->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
            } else {
                $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                $objFrontendLinks->setContentTypesManagamentId(7);

                if ($this->objMenuContent->getRedirectUrl() === null) {
                    $objFrontendLinks->setFrontendTitleSlug(null);
                } else {
                    $objFrontendLinks->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
                }
            }
            $objFrontendLinks->save();

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the status list control.
         *
         * @param ActionParams $params The parameters associated with the click action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws RandomException
         */
        public function lstStatus_Click(ActionParams $params): void
        {
            if (!$this->verifyCsrfOrModal()) {
                return;
            }

            if ($this->objMenuContent->SelectedPageId === null) {
                $this->dlgModal5->showDialogBox();
                $this->updateInputFields();
            } else if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->dlgModal1->showDialogBox();
                $this->updateInputFields();
            } else if ($this->objMenuContent->SelectedPageLocked === 1) {
                $this->dlgModal2->showDialogBox();
                $this->updateInputFields();
            } else {
                if ($this->objMenuContent->IsEnabled === 1) {
                    $this->objMenuContent->setSettingLocked(1);
                    $this->objMenuContent->setIsEnabled(2);
                        $this->dlgModal3->showDialogBox();
                    } else {
                    $this->objMenuContent->setSettingLocked(1);
                    $this->objMenuContent->setIsEnabled(1);
                        $this->dlgModal4->showDialogBox();
                    }

                    $this->objMenuContent->save();
            }

            $this->userOptions();
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
         * Handles the click event for the menu cancel button.
         * Redirects the user to the list page.
         *
         * @param ActionParams $params Parameters associated with the button click action.
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

            $this->userOptions();

            $this->redirectToListPage();
        }

        /**
         * Redirects the user to the list page for managing menus.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            Application::redirect('menu_manager.php');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

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

        /**
         * Builds a string representation of double routing information based on the selected page.
         * The method checks if multiple redirects exist for the given page and generates an
         * appropriate message, including a warning for double redirections.
         *
         * @param null|int|string $selectedPageId The ID of the selected page to evaluate routing for.
         *
         * @return string A message indicating redirection information or an empty string if no redirection applies.
         * @throws Caller
         * @throws InvalidCast
         */
        private function buildDoubleRoutingInfo(int|string|null $selectedPageId): string
        {
            $count = 0;
            $pages = MenuContent::loadAll();
            foreach ($pages as $row) {
                if ($row->getSelectedPageId() === $selectedPageId) $count++;
            }
            $selectedPage = $this->objMenuContent->getSelectedPage();

            if ($this->objMenuContent->getIsRedirect()) {
                if ($count > 1) {
                    return '<span style="color: #ff0000;">' . t('Redirected to this page ') . ' | ' . t('Warning, double redirection: ') . '</span><span style="color: #2593a1;">' . $selectedPage . '</span>';
                } else {
                    return '<span style="color: #ff0000;">' . t('Redirected to this page: ') . '</span><span style="color: #2593a1;">' . $selectedPage . '</span>';
                }
            }
            return '';
        }

        /**
         * Generates a string representation of a name with a specified depth.
         *
         * @param string $name The name to be printed.
         * @param mixed $parent The parent of the name. If not null, it signifies a nested structure.
         * @param int $depth The depth level indicating how much indentation to apply to the name.
         *
         * @return string The formatted string with appropriate indentation.
         */
        protected function printDepth(string $name, mixed $parent, int $depth): string
        {
            $spacer = str_repeat('&nbsp;', 5); // Adjust the number as needed for your indentation.

            if ($parent !== null) {
                $strHtml = str_repeat(html_entity_decode($spacer), $depth) . ' ' . $name;
            } else {
                $strHtml = $name;
            }

            return $strHtml;
        }

        /**
         * Retrieves an array of IDs representing all child elements of a specific parent ID
         * within a given menu hierarchy. The method performs a recursive traversal to include
         * all descendants of the specified element.
         *
         * @param array $objMenuArray An array of menu objects, where each object includes properties such as Id and ParentId.
         * @param mixed $clickedId The ID of the parent element for which all child IDs should be retrieved. Defaults to null.
         *
         * @return array An array of IDs representing all children and their descendants of the specified parent element.
         */
        private function getFullChildren(array $objMenuArray, mixed $clickedId = null): array
        {
            $objTempArray = [];
            foreach ($objMenuArray as $objMenu) {
                if ($objMenu->ParentId == $clickedId) {
                    $objTempArray[] = $objMenu->Id;
                    array_push($objTempArray, ...$this->getFullChildren($objMenuArray, $objMenu->Id));
                }
            }
            return $objTempArray;
        }
    }