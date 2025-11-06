<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\CellClick;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\Input;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\All;
    use QCubed\Query\Condition\AndCondition;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;

    /**
     * Represents a panel control for managing and displaying news items within the system.
     * Includes functionalities such as filtering, adding, moving, saving, and canceling news,
     * along with UI components for user interaction.
     */
    class NewsListPanel extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToast1;
        protected Q\Plugin\Toastr $dlgToast2;
        protected Q\Plugin\Toastr $dlgToast3;
        protected Q\Plugin\Toastr $dlgToast4;
        protected Q\Plugin\Toastr $dlgToast5;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public NewsTable $dtgNews;

        public Bs\Button $btnAddNews;
        public Bs\Button $btnMove;
        public Bs\TextBox $txtTitle;
        public ?Q\Plugin\Select2 $lstNewsLocked = null;
        public Q\Plugin\Select2 $lstTargetGroup;
        public ?Q\Plugin\Select2 $lstGroupTitle = null;
        public ?Q\Plugin\Select2 $lstGroups = null;
        public ?Q\Plugin\Select2 $lstChanges = null;
        public ?Q\Plugin\Select2 $lstCategories = null;

        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnLockedCancel;
        public Bs\Button $btnBack;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;
        protected object $objPortlet;

        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'NewsListPanel.tpl.php';

        /**
         * Constructor for initializing the object and setting up its state.
         *
         * @param mixed $objParentObject The parent object that this object will be attached to.
         * @param null|string $strControlId Optional control ID for the object.
         *
         * @throws Caller Thrown if there is an error in the caller's logic.
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

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must have to get something like here $this->objUser->getUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);
            $this->objPortlet = Portlet::load(2);

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
            $this->elementsReset();

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgNews_Create();
            $this->dtgNews->setDataBinder('BindData', $this);
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
         * Updates the portlet with the total count of news items and the latest modification date.
         * If news items are available, it sets the total count, assigns the current date and time
         * as the last updated date, and saves these changes to the portlet.
         *
         * @return void
         * @throws Caller
         */
        private function updatePortlet(): void
        {
            $objPage = News::countAll();

            if ($objPage) {
                $this->objPortlet->setTotalValue($objPage);
                $this->objPortlet->setLastDate(QDateTime::now());
                $this->objPortlet->save();
            }
        }

        /**
         * Initializes and configures input elements for the form, including a text box for the title and a select list
         * for target groups.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createInputs(): void
        {
            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Title of the new news');
            $this->txtTitle->setCssStyle('float', 'left');
            $this->txtTitle->setHtmlAttribute('autocomplete', 'off');

            $this->lstTargetGroup = new Q\Plugin\Select2($this);
            $this->lstTargetGroup->MinimumResultsForSearch = -1;
            $this->lstTargetGroup->Theme = 'web-vauu';
            $this->lstTargetGroup->Width = '100%';
            $this->lstTargetGroup->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);

            $objTargetGroups = NewsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::NewsSettings()->Id)));
            foreach ($objTargetGroups as $objTitle) {
                if ($objTitle->IsReserved !== 2) {
                    $this->lstTargetGroup->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstTargetGroup->addAction(new Change(), new AjaxControl($this,'lstTargetGroup_Change'));
            $this->lstTargetGroup->setCssStyle('float', 'left');
            $this->lstTargetGroup->Enabled = false;
        }

        /**
         * Creates and initializes a set of buttons with various functionalities like adding news, moving items, saving
         * changes, canceling actions, and going back.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnAddNews = new Bs\Button($this);
            $this->btnAddNews->Text = t(' Add news');
            $this->btnAddNews->Glyph = 'fa fa-plus';
            $this->btnAddNews->CssClass = 'btn btn-orange';
            $this->btnAddNews->addWrapperCssClass('center-button');
            $this->btnAddNews->CausesValidation = false;
            $this->btnAddNews->addAction(new Click(), new AjaxControl($this, 'btnAddNews_Click'));

            $this->btnMove = new Bs\Button($this);
            $this->btnMove->Text = t(' Move');
            $this->btnMove->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnMove->CssClass = 'btn btn-darkblue move-button-js';
            $this->btnMove->addWrapperCssClass('center-button');
            $this->btnMove->CausesValidation = false;
            $this->btnMove->addAction(new Click(), new AjaxControl($this, 'btnMove_Click'));

            if (NewsSettings::countAll() !== 1) {
                Application::executeJavaScript("
                $('.move-button-js').removeClass('hidden');
                $('.move-items-js').addClass('hidden');
                $('.new-item-js').addClass('hidden');
            ");
            } else {
                Application::executeJavaScript("
                $('.move-button-js').addClass('hidden');
                $('.move-items-js').addClass('hidden');
                $('.new-item-js').addClass('hidden');
            ");
            }

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange save-js';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->setCssStyle('float', 'left');
            $this->btnSave->setCssStyle('margin-right', '10px');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->setCssStyle('float', 'left');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));

            $this->btnLockedCancel = new Bs\Button($this);
            $this->btnLockedCancel->Text = t('Cancel');
            $this->btnLockedCancel->addWrapperCssClass('center-button');
            $this->btnLockedCancel->CssClass = 'btn btn-default';
            $this->btnLockedCancel->setCssStyle('float', 'left');
            $this->btnLockedCancel->CausesValidation = false;
            $this->btnLockedCancel->addAction(new Click(), new AjaxControl($this, 'btnLockedCancel_Click'));

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->addWrapperCssClass('center-button');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new AjaxControl($this,'btnBack_Click'));
        }

        /**
         * Initializes and configures multiple toastr notification objects with predefined settings.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('The news title is at least mandatory!');
            $this->dlgToast1->ProgressBar = true;

            $this->dlgToast2 = new Q\Plugin\Toastr($this);
            $this->dlgToast2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast2->Message = t('<p style=\"margin-bottom: 5px;\">The news group must be selected beforehand!</p>');
            $this->dlgToast2->ProgressBar = true;
            $this->dlgToast2->TimeOut = 10000;
            $this->dlgToast2->EscapeHtml = false;

            $this->dlgToast3 = new Q\Plugin\Toastr($this);
            $this->dlgToast3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast3->Message = t('<p style=\"margin-bottom: 5px;\">The news group cannot be the same as the target group!</p>');
            $this->dlgToast3->ProgressBar = true;
            $this->dlgToast3->TimeOut = 10000;
            $this->dlgToast3->EscapeHtml = false;

            $this->dlgToast4 = new Q\Plugin\Toastr($this);
            $this->dlgToast4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast4->Message = t('<strong>Well done!</strong> The transfer of news to the new group was successful.');
            $this->dlgToast4->ProgressBar = true;

            $this->dlgToast5 = new Q\Plugin\Toastr($this);
            $this->dlgToast5->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast5->Message = t('The transfer of news to the new group failed.');
            $this->dlgToast5->ProgressBar = true;
        }

        /**
         * Creates and configures modal dialog boxes with specific settings and actions.
         *
         * @return void
         * @throws Caller
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move the news from this news group to another news group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Note! If there are several news items in the selected group, they will be transferred to the new group!</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addCloseButton(t("I'll cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'moveItems_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'transferCancelling_Click'));

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal2->Title = t("Warning");
            $this->dlgModal2->HeaderClasses = 'btn-danger';
            $this->dlgModal2->addCloseButton(t("I understand"));
        }

        /**
         * Resets the visibility of certain UI elements by adding specific CSS classes to them.
         *
         * @return void
         * @throws Caller
         */
        public function elementsReset(): void
        {
            Application::executeJavaScript("
                $('.new-item-js').addClass('hidden');
                $('.move-items-js').addClass('hidden');
            ");
        }

        /**
         * Handles the click event for the 'Add News' button. It updates the UI elements to allow the user
         * to add a new news item, sets up the newsgroup selection, and manages the required interactions
         * for the 'lstGroupTitle' select control.
         *
         * @param ActionParams $params Parameters representing the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnAddNews_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.move-button-js').addClass('hidden');
                $('.new-item-js').removeClass('hidden');
                $('.move-items-js').addClass('hidden');
            ");

            $this->updateLockStatus();
            $this->disableInputs();
            $this->userOptions();

            $countByIsReserved = NewsSettings::countByIsReserved(1);

            $this->txtTitle->Text = '';
            $this->btnAddNews->Enabled = false;

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '100%';
            $this->lstGroupTitle->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroupTitle->addItem(t('- Select one newsgroup -'), null, true);

            $objGroups = NewsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::NewsSettings()->Id)));

            foreach ($objGroups as $objTitle) {
                if ($objTitle->IsReserved === 1 && $countByIsReserved === 1) {
                    $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                    $this->lstGroupTitle->SelectedValue = $objTitle->Id;
                } else if ($objTitle->IsReserved === 1 && $countByIsReserved > 1) {
                    $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstGroupTitle->addAction(new Change(), new AjaxControl($this,'lstGroupTitle_Change'));
            $this->lstGroupTitle->setCssStyle('float', 'left');
            $this->lstGroupTitle->setHtmlAttribute('required', 'required');

            if ($countByIsReserved === 1) {
                $this->lstGroupTitle->Enabled = false;
                $this->txtTitle->focus();
            } else {
                $this->lstGroupTitle->Enabled = true;
                $this->lstGroupTitle->focus();
            }

            $this->dtgNews->addCssClass('disabled');
        }

        /**
         * Handles the click event for the move button. This method updates the UI by toggling the visibility of
         * elements and initializes a dropdown list for selecting newsgroups. It manages list selection based on
         * conditions and adjusts controls' enabled state accordingly.
         *
         * @param ActionParams $params The parameters passed during the action event for handling specific logic.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnMove_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.move-items-js').removeClass('hidden');
                $('.new-item-js').addClass('hidden'); 
            ");

            $this->updateLockStatus();
            $this->disableInputs();
            $this->userOptions();

            $this->lstNewsLocked = new Q\Plugin\Select2($this);
            $this->lstNewsLocked->MinimumResultsForSearch = -1;
            $this->lstNewsLocked->Theme = 'web-vauu';
            $this->lstNewsLocked->Width = '100%';
            $this->lstNewsLocked->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstNewsLocked->addItem(t('- Select one newsgroup -'), null, true);

            $objGroups = NewsSettings::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::NewsSettings()->NewsLocked, 0), QQN::NewsSettings()->Id)
                ]
            );

            $countByNewsLocked = NewsSettings::countByNewsLocked(1);

            foreach ($objGroups as $objTitle) {
                if ($countByNewsLocked > 1 && $objTitle->NewsLocked === 1) {
                    $this->lstNewsLocked->addItem($objTitle->Name, $objTitle->Id);
                } else if ($countByNewsLocked === 1 && $objTitle->NewsLocked === 1) {
                    $this->lstNewsLocked->addItem($objTitle->Name, $objTitle->Id);
                    $this->lstNewsLocked->SelectedValue = $objTitle->Id;
                }
            }

            $this->lstNewsLocked->addAction(new Change(), new AjaxControl($this,'lstNewsLocked_Change'));

            if ($this->lstNewsLocked->SelectedValue === null) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->Enabled = false;
            } else {
                $this->lstTargetGroup->Enabled = true;
            }

            if ($countByNewsLocked === 1) {
                $this->lstNewsLocked->Enabled = false;
                $this->lstTargetGroup->Enabled = true;
                $this->lstTargetGroup->focus();
            } else if ($countByNewsLocked === 0) {
                $this->lstNewsLocked->Enabled = false;
                $this->lstTargetGroup->Enabled = false;
            } else {
                $this->lstNewsLocked->Enabled = true;
                $this->lstNewsLocked->focus();
            }

            $this->btnAddNews->Enabled = false;
            $this->dtgNews->addCssClass('disabled');
        }

        /**
         * Handles change events for the group title list. If no item is selected,
         * the focus is returned to the list to prompt the user, and a notification is shown.
         * If an item is selected, the focus is shifted to the title text input.
         *
         * @param ActionParams $params The parameters associated with the change event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstGroupTitle_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstGroupTitle->SelectedValue === null) {
                $this->lstGroupTitle->focus();
                $this->dlgToast2->notify();
            } else {
                $this->txtTitle->focus();
            }
        }

        /**
         * Handles the change event for the lstNewsLocked control.
         * Updates the UI based on the selected value in lstNewsLocked.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstNewsLocked_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstNewsLocked->SelectedValue === null) {
                $this->lstTargetGroup->Enabled = false;
                $this->lstNewsLocked->addCssClass('has-error');
                $this->dlgToast2->notify();
            } else {
                $this->lstTargetGroup->Enabled = true;
                $this->lstNewsLocked->removeCssClass('has-error');
                $this->lstTargetGroup->focus();
            }
        }

        /**
         * Handles the change event for the target group selection.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstTargetGroup_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstNewsLocked->SelectedValue === $this->lstTargetGroup->SelectedValue) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->refresh();
                $this->lstTargetGroup->addCssClass('has-error');
                $this->dlgToast3->notify();
            } else if ($this->lstNewsLocked->SelectedValue !== null && $this->lstTargetGroup->SelectedValue !== null) {
                $this->dlgModal1->showDialogBox();
            } else {
                $this->lstTargetGroup->removeCssClass('has-error');
            }
        }

        /**
         * Handles the click event for canceling a transfer operation.
         *
         * This method resets the form elements related to the news transfer process
         * and updates the UI components to reflect the cancellation state.
         *
         * @param ActionParams $params Parameters related to the triggering action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function transferCancelling_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->elementsReset();
            $this->lstNewsLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->btnAddNews->Enabled = true;
            $this->btnMove->Enabled = true;

            $this->lstNewsLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->enableInputs();
            $this->dtgNews->removeCssClass('disabled');

            $this->userOptions();
        }

        /**
         * Handles the click event for moving items. This function hides the dialog box,
         * performs the necessary transfer operations, resets the element state, and
         * re-enables specific buttons.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function moveItems_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->dlgModal1->hideDialogBox();
            $this->newsTransferOperations();

            $this->elementsReset();
            $this->btnAddNews->Enabled = true;
            $this->btnMove->Enabled = true;

            $this->userOptions();
        }

        /**
         * Handles the transfer of news operations from a locked group to a target group.
         * This involves updating the newsgroup settings and transferring associated news
         * and frontend links to the target group.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        private function newsTransferOperations(): void
        {
            $this->dlgModal1->hideDialogBox();

            $objLockedGroup = NewsSettings::loadById($this->lstNewsLocked->SelectedValue);
            $objTargetGroup = NewsSettings::loadById($this->lstTargetGroup->SelectedValue);

            $objNewsSettings = NewsSettings::loadById($objLockedGroup->getId());
            $objNewsSettings->setNewsLocked(0);
            $objNewsSettings->save();

            $objNewsSettings = NewsSettings::loadById($objTargetGroup->getId());
            $objNewsSettings->setNewsLocked(1);
            $objNewsSettings->save();

            $objNewsGroupArray = News::loadArrayByNewsGroupTitleId($this->lstNewsLocked->SelectedValue);

            foreach ($objNewsGroupArray as $objNewsGroup) {
                $objNews = News::loadById($objNewsGroup->getId());
                $objNews->setMenuContentId($objTargetGroup->getMenuContentId());
                $objNews->setNewsGroupTitleId($this->lstTargetGroup->SelectedValue);
                $objNews->setGroupTitle($objTargetGroup->getName());
                $objNews->updateNews($objNews->getTitle(), $objTargetGroup->getTitleSlug());
                $objNews->save();

                $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objNews->getId());

                $objFrontendLink->setFrontendTitleSlug($objNews->getTitleSlug());
                $objFrontendLink->setGroupedId($objTargetGroup->getMenuContentId());
                $objFrontendLink->save();
            }

            $objNewsFiles = NewsFiles::loadArrayByMenuContentGroupId($objLockedGroup->getMenuContentId());

            foreach ($objNewsFiles as $objNewsFile) {
                $objNewsFile = NewsFiles::loadById($objNewsFile->getId());
                $objNewsFile->setMenuContentGroupId($objTargetGroup->getMenuContentId());
                $objNewsFile->save();
            }

            $this->dtgNews->refresh();

            if (SportsSettings::countAll() !== 1) {
                Application::executeJavaScript("
                    $('.move-button-js').removeClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            }

            $countNewsGroupId = News::countByNewsGroupTitleId($this->lstNewsLocked->SelectedValue);

            if ($countNewsGroupId === 0) {
                $this->dlgToast4->notify();
            } else {
                $this->dlgToast5->notify();
            }

            $this->updateLockStatus();
            $this->enableInputs();
        }

        /**
         * Handles the click event to save a button. Validates input fields and then proceeds to create and save
         * new News and FrontendLinks records based on selected and entered data.
         *
         * @param ActionParams $params Contains the parameters related to the action event, such as trigger information.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objNewsSettings = NewsSettings::load($this->lstGroupTitle->SelectedValue);
            $objTemplateLocking = FrontendTemplateLocking::load(4);
            $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

            if ($this->lstGroupTitle->SelectedValue === null) {
                $this->dlgToast2->notify();
                $this->lstGroupTitle->focus();
            } else if ($this->lstGroupTitle->SelectedValue && !$this->txtTitle->Text) {
                $this->dlgToast1->notify();
                $this->txtTitle->focus();
            } else if ($this->lstGroupTitle->SelectedValue && $this->txtTitle->Text) {

                $objNews = new News();
                $objNews->setPostDate(QDateTime::now());
                $objNews->setTitle($this->txtTitle->Text);
                $objNews->setMenuContentId($objNewsSettings->getMenuContentId());
                $objNews->setNewsGroupTitleId($objNewsSettings->getId());
                $objNews->setGroupTitle($objNewsSettings->getName());
                $objNews->setStatus(2);
                $objNews->saveNews($this->txtTitle->Text, $objNewsSettings->getTitleSlug());
                $objNews->setAssignedByUser($this->objUser->Id);
                $objNews->setAuthor($objNews->getAssignedByUserObject());
                $objNews->save();

                $this->userOptions();
                $this->updatePortlet();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($objNews->getId());
                $objFrontendLinks->setGroupedId($objNewsSettings->getMenuContentId());
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtTitle->Text));
                $objFrontendLinks->setContentTypesManagamentId(4);
                $objFrontendLinks->setFrontendTitleSlug($objNews->getTitleSlug());
                $objFrontendLinks->save();

                if ($objNewsSettings->getNewsLocked() == 0) {
                    $objGroup = NewsSettings::loadById($objNewsSettings->getId());
                    $objGroup->setNewsLocked(1);
                    $objGroup->save();
                }

                $this->btnAddNews->Enabled = true;
                $this->btnMove->Enabled = true;
                $this->lstGroupTitle->SelectedValue = null;
                $this->txtTitle->Text = '';
                $this->elementsReset();

                Application::redirect('news_edit.php' . '?id=' . $objNews->getId() . '&group=' . $objNewsSettings->getMenuContentId());
            }
        }

        /**
         * Handles the click event for the cancel button, performing UI updates and resetting form fields.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (SportsSettings::countAll() !== 1) {
                Application::executeJavaScript("
                    $('.move-button-js').removeClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').removeClass('hidden');
                ");
            }

            $this->btnAddNews->Enabled = true;
            $this->txtTitle->Text = '';
            $this->lstGroupTitle->SelectedValue = null;
            $this->lstGroupTitle->refresh();

            $this->enableInputs();
            $this->dtgNews->removeCssClass('disabled');

            $this->userOptions();
        }

        /**
         * Event handler for the Locked Cancel button click event.
         * Toggles the visibility of specified UI elements based on the count of SportsSettings.
         * Resets and refreshes list selections and enables specific buttons.
         *
         * @param ActionParams $params The parameters from the button click action.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnLockedCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (SportsSettings::countAll() !== 1) {
                Application::executeJavaScript("
                    $('.move-button-js').removeClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            }

            $this->btnMove->Enabled = true;
            $this->btnAddNews->Enabled = true;

            $this->lstNewsLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->lstNewsLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->enableInputs();
            $this->dtgNews->removeCssClass('disabled');

            $this->userOptions();
        }

        /**
         * Handles the click event for the "Back" button.
         *
         * @param ActionParams $params The parameters associated with the button click event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnBack_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            Application::executeJavaScript("history.go(-1);");
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes the `dtgNews` data grid by creating a new instance of `NewsTable`, setting up columns,
         * generating paginators, and enabling edit functionality. Configures row parameters callback,
         * default sort behavior, and page loading options through AJAX.
         * Customization of items per a page is done based on user-assigned preferences.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgNews_Create(): void
        {
            $this->dtgNews = new NewsTable($this);
            $this->dtgNews_CreateColumns();
            $this->createPaginators();
            $this->dtgNews_MakeEditable();
            $this->dtgNews->RowParamsCallback = [$this, "dtgNews_GetRowParams"];
            //$this->dtgNews->SortColumnIndex = 5;
            //$this->dtgNews->SortDirection = -1;
            $this->dtgNews->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgNews->UseAjax = true;
        }

        /**
         * Creates columns for the dtgNews data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgNews_CreateColumns(): void
        {
            $this->dtgNews->createColumns();
        }

        /**
         * Configures the datagrid to be editable by making rows clickable and
         * adding CSS classes for styling. Attaches an Ajax action to handle
         * row click events by invoking the dtgNewsRow_Click method.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgNews_MakeEditable(): void
        {
            $this->dtgNews->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgNewsRow_Click'));
            $this->dtgNews->addCssClass('clickable-rows');
            $this->dtgNews->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for a news row in a data grid.
         *
         * @param ActionParams $params The parameters passed from the action, including the ID of the news item.
         *
         * @return void This method does not return any value but redirects the user to the news edit page.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgNewsRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intNewsId = intval($params->ActionParameter);
            $objNews = News::loadById($intNewsId);
            $intGroup = $objNews->getMenuContentId();

            $this->userOptions();

            Application::redirect('news_edit.php' . '?id=' . $intNewsId . '&group=' . $intGroup);
        }

        /**
         * Retrieves parameters for a specific row in the news data grid.
         *
         * @param object $objRowObject The row object from which to extract the parameters.
         * @param int $intRowIndex The index of the row within the data grid.
         *
         * @return array An associative array of parameters for the row, including a 'data-value' key with the row's
         *     primary key.
         */
        public function dtgNews_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures primary and alternate paginators for a news datagrid,
         * setting labels for navigation controls and specifying items per a page.
         * Also invokes filter action methods associated with the datagrid.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgNews->Paginator = new Bs\Paginator($this);
            $this->dtgNews->Paginator->LabelForPrevious = t('Previous');
            $this->dtgNews->Paginator->LabelForNext = t('Next');

            $this->dtgNews->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgNews->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgNews->PaginatorAlternate->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        /**
         * Initializes and configures a Select2 dropdown component to manage items per a page for a user.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createItemsPerPage(): void
        {
            $this->lstItemsPerPageByAssignedUserObject = new Q\Plugin\Select2($this);
            $this->lstItemsPerPageByAssignedUserObject->MinimumResultsForSearch = -1;
            $this->lstItemsPerPageByAssignedUserObject->Theme = 'web-vauu';
            $this->lstItemsPerPageByAssignedUserObject->Width = '100%';
            $this->lstItemsPerPageByAssignedUserObject->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstPreferredItemsPerPageObject_GetItems());
            $this->lstItemsPerPageByAssignedUserObject->AddAction(new Change(), new AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
        }

        /**
         * Retrieves a list of items per a page filtered by an assigned user object.
         *
         * @return ListItem[] An array of ListItem objects representing the items per page
         *         associated with the assigned user object, with the relevant item marked as selected.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstPreferredItemsPerPageObject_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objPreferredItemsPerPageObjectCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objPreferredItemsPerPageObjectCursor = ItemsPerPage::queryCursor($objCondition, $this->objPreferredItemsPerPageObjectClauses);

            // Iterate through the Cursor
            while ($objPreferredItemsPerPageObject = ItemsPerPage::instantiateCursor($objPreferredItemsPerPageObjectCursor)) {
                $objListItem = new ListItem($objPreferredItemsPerPageObject->__toString(), $objPreferredItemsPerPageObject->Id);
                if (($this->objUser->PreferredItemsPerPageObject) && ($this->objUser->PreferredItemsPerPageObject->Id == $objPreferredItemsPerPageObject->Id))
                    $objListItem->Selected = true;
                $a[] = $objListItem;
            }

            return $a;
        }

        /**
         * Updates the number of items displayed per page in the news data grid based on the selected value from the
         * dropdown list.
         *
         * @param ActionParams $params The parameters associated with the change action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgNews->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgNews->refresh();
        }

        /**
         * Configures and initializes multiple input, dropdown, and button components
         * including search boxes, filter selectors, and a filter clear button
         * for managing filtering functionalities within the application.
         *
         * @return void
         * @throws Caller
         */
        protected function createFilter(): void
        {
            $this->txtFilter = new Bs\TextBox($this);
            $this->txtFilter->Placeholder = t('Search...');
            $this->txtFilter->TextMode = TextBoxBase::SEARCH;
            $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
            $this->txtFilter->addCssClass('search-box');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstGroups = new Q\Plugin\Select2($this);
            $this->lstGroups->MinimumResultsForSearch = -1;
            $this->lstGroups->Theme = 'web-vauu';
            $this->lstGroups->Width = '100%';
            $this->lstGroups->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroups->addItem(t('- Search newsgroup -'), null, true);

            $objGroups = NewsSettings::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::NewsSettings()->NewsLocked, 0), QQN::NewsSettings()->Id)
                ]
            );

            $countByNewsLocked = NewsSettings::countByNewsLocked(1);

            foreach ($objGroups as $objTitle) {
                if ($countByNewsLocked > 1 && $objTitle->NewsLocked === 1) {
                    $this->lstGroups->addItem($objTitle->Name, $objTitle->Id);
                } else if ($countByNewsLocked === 1 && $objTitle->NewsLocked === 1) {
                    $this->lstGroups->addItem($objTitle->Name, $objTitle->Id);
                    $this->lstGroups->SelectedValue = $objTitle->Id;
                }
            }

            $this->lstGroups->addAction(new Change(), new AjaxControl($this,'lstGroups_Change'));
            $this->lstGroups->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstChanges = new Q\Plugin\Select2($this);
            $this->lstChanges->MinimumResultsForSearch = -1;
            $this->lstChanges->Theme = 'web-vauu';
            $this->lstChanges->Width = '100%';
            $this->lstChanges->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstChanges->addItem(t('- Search change -'), null, true);

            $objChanges = NewsChanges::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::NewsChanges()->NewsChangeLocked, 0), QQN::NewsChanges()->Id)
                ]
            );

            foreach ($objChanges as $objTitle) {
                if ($objTitle->NewsChangeLocked === 1) {
                    $this->lstChanges->addItem($objTitle->Title, $objTitle->Id);
                }
            }

            $this->lstChanges->addAction(new Change(), new AjaxControl($this,'lstChanges_Change'));
            $this->lstChanges->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstCategories = new Q\Plugin\Select2($this);
            $this->lstCategories->MinimumResultsForSearch = -1;
            $this->lstCategories->Theme = 'web-vauu';
            $this->lstCategories->Width = '100%';
            $this->lstCategories->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstCategories->addItem(t('- Search category -'), null, true);

            $objCategories = CategoryOfNews::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::CategoryOfNews()->Name, 0), QQN::CategoryOfNews()->Id)
                ]
            );

            foreach ($objCategories as $objName) {
                if ($objName->NewsCategoryLocked === 1) {
                    $this->lstCategories->addItem($objName->Name, $objName->Id);
                }
            }

            $this->lstCategories->addAction(new Change(), new AjaxControl($this,'lstCategories_Change'));
            $this->lstCategories->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->btnClearFilters = new Bs\Button($this);
            $this->btnClearFilters->Text = t('Clear filters');
            $this->btnClearFilters->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnClearFilters->setCssStyle('float', 'left');
            $this->btnClearFilters->CausesValidation = false;
            $this->btnClearFilters->addAction(new Click(), new AjaxControl($this, 'clearFilters_Click'));

            $this->updateLockStatus();
            $this->addFilterActions();
        }

        /**
         * Updates the lock status of various elements and refreshes the associated lists.
         *
         * The method performs checks on the count of locked items in different categories
         * (groups, changes, and categories) and applies visibility changes to the respective
         * UI elements using JavaScript. Each associated list is refreshed after the visibility update.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function updateLockStatus(): void
        {
            $countByNewsLocked = NewsSettings::countByNewsLocked(1);

            if ($countByNewsLocked > 1) {
                Application::executeJavaScript("$('.js-groups').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-groups').addClass('hidden');");
            }

            $this->lstGroups->refresh();

            $countByNewsChangeLocked = NewsChanges::countByNewsChangeLocked(1);

            if ($countByNewsChangeLocked > 0) {
                Application::executeJavaScript("$('.js-changes').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-changes').addClass('hidden');");
            }

            $this->lstChanges->refresh();

            $countByNewsCategoryLocked = CategoryOfNews::countByNewsCategoryLocked(1);

            if ($countByNewsCategoryLocked > 0) {
                Application::executeJavaScript("$('.js-categories').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-categories').addClass('hidden');");
            }

            $this->lstCategories->refresh();
        }

        /**
         * Disables various input controls and resets their values.
         *
         * @return void
         */
        protected function disableInputs(): void
        {
            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->lstItemsPerPageByAssignedUserObject->refresh();

            $this->txtFilter->Text = '';
            $this->txtFilter->Enabled = false;
            $this->txtFilter->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->Enabled = false;
            $this->lstGroups->refresh();

            $this->lstChanges->SelectedValue = null;
            $this->lstChanges->Enabled = false;
            $this->lstChanges->refresh();

            $this->lstCategories->SelectedValue = null;
            $this->lstCategories->Enabled = false;
            $this->lstCategories->refresh();

            $this->dtgNews->refresh();
        }

        /**
         * Enables a series of input controls and clears their current values as well as refreshes their state.
         *
         * @return void
         */
        protected function enableInputs(): void
        {
            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->lstItemsPerPageByAssignedUserObject->refresh();

            $this->txtFilter->Text = '';
            $this->txtFilter->Enabled = true;
            $this->txtFilter->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->Enabled = true;
            $this->lstGroups->refresh();

            $this->lstChanges->SelectedValue = null;
            $this->lstChanges->Enabled = true;
            $this->lstChanges->refresh();

            $this->lstCategories->SelectedValue = null;
            $this->lstCategories->Enabled = true;
            $this->lstCategories->refresh();

            $this->dtgNews->refresh();
        }

        /**
         * Handles the change event for the search list and refreshes the news data grid.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         */
        protected function lstGroups_Change(ActionParams $params): void
        {
            $this->dtgNews->refresh();
        }

        /**
         * Handles changes made to the list of changes.
         * Refreshes the news data grid in response to the action triggered.
         *
         * @param ActionParams $params The parameters associated with the action triggering this change.
         *
         * @return void
         */
        protected function lstChanges_Change(ActionParams $params): void
        {
            $this->dtgNews->refresh();
        }

        /**
         * Handles the change event for the category list.
         * This method refreshes the data grid for news items.
         *
         * @param ActionParams $params Parameters from the triggered action event.
         *
         * @return void
         */
        protected function lstCategories_Change(ActionParams $params): void
        {
            $this->dtgNews->refresh();
        }

        /**
         * Clears all applied filters in the current view, resetting text and dropdown fields
         * and refreshing associated controls.
         *
         * @param ActionParams $params The parameters passed to the action, containing any additional
         *         information about the event triggered.
         *
         * @return void This method does not return a value.
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->refresh();

            $this->lstChanges->SelectedValue = null;
            $this->lstChanges->refresh();

            $this->lstCategories->SelectedValue = null;
            $this->lstCategories->refresh();

            $this->dtgNews->refresh();
        }

        /**
         * Adds actions to the filter input component. Configures the filter input to
         * trigger an Ajax control action upon user input and specifically on the Enter key event.
         *
         * @return void
         * @throws Caller
         */
        protected function addFilterActions(): void
        {
            $this->txtFilter->addAction(new Input(300), new AjaxControl($this, 'filterChanged'));
            $this->txtFilter->addActionArray(new EnterKey(),
                [
                    new AjaxControl($this, 'FilterChanged'),
                    new Terminate()
                ]
            );
        }

        /**
         * Handles the event triggered when a filter condition changes.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgNews->refresh();
        }

        /**
         * Binds data to the data grid using the specified conditions.
         *
         * This method retrieves the condition object and applies it to bind
         * data to the data grid. It is responsible for ensuring that the
         * grid is populated with data that meets the defined criteria.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgNews->bindData($objCondition);
        }

        /**
         * Constructs a condition to filter news items based on optional group selection and text search.
         *
         * @return All|AndCondition|OrCondition A condition object that represents the filtering
         *         criteria for news items. If no filter is applied, it returns a condition that selects all items.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function getCondition(): All|AndCondition|OrCondition
        {
            $strText = trim($this->txtFilter->Text ?? '');
            $intGroupId = $this->lstGroups->SelectedValue; // ID value
            $intChangeId = $this->lstChanges->SelectedValue; // ID value
            $intCategoryId = $this->lstCategories->SelectedValue; // ID value

            $condList = [];

            // If a group selected
            if (!empty($intGroupId)) {
                $condList[] = QQ::equal(QQN::News()->NewsGroupTitleId, $intGroupId); // or the correct field that you have binding
            }

            // If a change selected
            if (!empty($intChangeId)) {
                $condList[] = QQ::equal(QQN::News()->ChangesId, $intChangeId); // or the correct field that you have binding
            }

            // If a category selected
            if (!empty($intCategoryId)) {
                $condList[] = QQ::equal(QQN::News()->NewsCategoryId, $intCategoryId); // or the correct field that you have binding
            }

            // If a text is entered
            if ($strText !== '') {
                // Do one big 'or' for multiple fields in the text
                $orText = QQ::orCondition(
                    QQ::like(QQN::News()->GroupTitle, "%" . $strText . "%"),
                    QQ::like(QQN::News()->Title, "%" . $strText . "%"),
                    QQ::like(QQN::News()->Category, "%" . $strText . "%"),
                    QQ::like(QQN::News()->Author, "%" . $strText . "%")
                );
                $condList[] = $orText;
            }

            // If neither filter is present, return all
            if (count($condList) === 0) {
                return QQ::all();
            }

            // If both conditions are met, combine with AND
            return QQ::andCondition(...$condList);
        }
    }