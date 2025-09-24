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
    use QCubed\Query\QQ;
    use QCubed\Html;

    /**
     * A panel class for editing and managing statistics-related settings and configurations.
     * Extends the Q\Control\Panel class to provide a user interface for modifying menu content,
     * group titles, content types, and status configurations.
     *
     * Members include UI elements such as modal dialogs, toast notifications, labels, text boxes, radio buttons,
     * dropdown lists, and buttons to navigate to related sections or save configurations.
     *
     * It also utilizes protected members for internal functionality like database access using defined objects,
     * condition and clause management, and configuration for the panel's template.
     */
    class StatisticsEditPanel extends Panel
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

        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Q\Plugin\Control\Label $lblTitleSlug;
        public Q\Plugin\Control\Label $txtTitleSlug;


        public Bs\Button $btnGoToStatistics;
        public Bs\Button $btnGoToList;
        public Bs\Button $btnGoToMenu;

        protected Q\Plugin\Control\Alert $lblInfo;

        protected int $intId;
        protected object $objMenu;
        protected object $objMenuContent;
        protected object $objStatisticsSettings;
        protected int $intLoggedUserId;

        protected object $objGroupTitleCondition;
        protected ?array $objGroupTitleClauses;

        protected ?array $objStatisticsTypeClauses;
        protected object $objStatisticsTypeCondition;

        const int MAX_ALLOWED_TYPES = 3;

        protected string $strTemplate = 'StatisticsEditPanel.tpl.php';

        /**
         * Constructor for initializing the object with required data and creating UI elements.
         *
         * @param mixed $objParentObject The parent object that initializes this control.
         * @param string|null $strControlId An optional string to specify the control ID.
         *
         * @return void
         *
         * @throws Exception
         * @throws Caller If the parent constructor fails due to an invalid call.
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            if (!empty($_SESSION['statistics_edit_group'])) {
                unset($_SESSION['statistics_edit_group']);
            }

            $this->intId = Application::instance()->context()->queryStringItem('id');
            $this->objMenu = Menu::load($this->intId);
            $this->objMenuContent = MenuContent::load($this->intId);
            $this->objStatisticsSettings = StatisticsSettings::loadByIdFromStatisticsSettings($this->intId);

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must save something here $this->objStatisticsSettings->setUserId(logged user session);
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
            $this->checkStatisticsTypes();
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

            if ($this->objStatisticsSettings->getIsReserved() == 1) {
                $this->txtMenuText->Enabled = false;
            }

            $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
            $this->lblGroupTitle->Text = t('Editing a statistics group title');
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

            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Display = false;
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');

            if ($this->objMenuContent->getContentType() === 14) {
                $this->lblInfo->Display = true;
            }
        }

        /**
         * Creates and configures a set of buttons for navigating between different sections or managers,
         * and adjusts button states and controls based on the statistics settings and their availability.
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
            $this->btnGoToList->Text = t('Go to the statistics manager');
            $this->btnGoToList->CssClass = 'btn btn-default';
            $this->btnGoToList->addWrapperCssClass('center-button');
            $this->btnGoToList->CausesValidation = false;
            $this->btnGoToList->addAction(new Click(), new AjaxControl($this, 'btnGoToList_Click'));

            $this->btnGoToStatistics = new Bs\Button($this);
            $this->btnGoToStatistics->Text = t('Go to the statistics settings manager');
            $this->btnGoToStatistics->addWrapperCssClass('center-button');
            $this->btnGoToStatistics->CausesValidation = false;
            $this->btnGoToStatistics->addAction(new Click(), new AjaxControl($this,'btnGoToStatistics_Click'));
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the links group for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This links group is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This links group has now been made public!</p>');
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
         * Validates and processes available statistics types for a given content type, ensuring adherence to set constraints.
         *
         * @return void This method updates the text property of an internal label component to communicate available or unavailable statistics types based on defined logic.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function checkStatisticsTypes(): void
        {
            // Define all possible statistics types
            $arrStatisticsTypes = [14 => t('Records'), 15 => t('Rankings'), 16 => t('Achievements')];

            // Get all `content_type` values from `MenuContent` table
            $arrSelectedTypeIds = MenuContent::queryArray(
                QQ::isNotNull(QQN::MenuContent()->ContentType),
                QQ::select(QQN::MenuContent()->ContentType)
            );

            // Convert `content_type` to a simple associative array
            $arrSelectedTypeIds = array_map(function($objContent) {
                return $objContent->ContentType;
            }, $arrSelectedTypeIds);

            // Free types: remove selected types from possible types
            $arrAvailableTypeNames = array_diff_key($arrStatisticsTypes, array_flip($arrSelectedTypeIds));

            // Display HTML title
            $htmlHeader = t('<p>Important Information! For this content type, you can create up to three menu items for different types of statistics (Records, Rankings, Achievements).</p>
                     <p>It is neither practical nor possible to create additional entries with this content type!</p>');

            if (empty($arrAvailableTypeNames)) {
                // All types are in use
                $this->lblInfo->Text = $htmlHeader . t('<p>Currently, no available types are left!</p>');
            } else {
                // Show types available to the user
                $this->lblInfo->Text = $htmlHeader . t('<p>Currently available types: ' . implode(', ', $arrAvailableTypeNames) . '</p>');
            }
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

            $this->objStatisticsSettings->setStatus($this->lstStatus->SelectedValue);
            $this->objStatisticsSettings->save();

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
         * Handles the click event for the "Go to statistics" button, setting session parameters and redirecting the user.
         *
         * @param ActionParams $params The parameters associated with the action, typically provided by the event system.
         *
         * @return void No return value as the method performs a session variable assignment and a page redirect.
         * @throws RandomException
         * @throws Throwable
         */
        public function btnGoToStatistics_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $_SESSION['statistics_edit_group'] = $this->intId;
            Application::redirect('settings_manager.php#statisticsSettings_tab');
        }

        /**
         * Handles the click event for the 'Go To List' button and redirects the user to the link list page.
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

            Application::redirect('statistics_list.php');
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