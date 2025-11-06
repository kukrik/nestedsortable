<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use Random\RandomException;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Jqui\Event\SortableStop;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Query\QQ;
    use QCubed\Project\Application;

    /**
     * Class MembersOptionsPanel
     *
     * Represents a UI panel that provides options for configuring board settings.
     * This panel manages various modals, notifications, inputs, buttons, and sorting behavior.
     * It facilitates customizing board properties dynamically and tracks session-related user data.
     */
    class MembersOptionsPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;
        public Bs\Modal $dlgModal6;
        public Bs\Modal $dlgModal7;


        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;
        protected Q\Plugin\Toastr $dlgToastr4;
        protected Q\Plugin\Toastr $dlgToastr5;

        public Q\Plugin\Control\Alert $lblInfo;
        public Q\Plugin\Select2 $lstGroupTitle;
        public Q\Plugin\Control\Label $lblPostDate;
        public Bs\Label $calPostDate;
        public Q\Plugin\Control\Label $lblPostUpdateDate;
        public Bs\Label $calPostUpdateDate;
        public Q\Plugin\Control\Label $lblAuthor;
        public Bs\Label $txtAuthor;
        public Q\Plugin\Control\Label $lblUsersAsEditors;
        public Bs\Label $txtUsersAsEditors;
        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;
        public Q\Plugin\Control\Label $lblImageUpload;
        public Q\Plugin\Control\RadioList $lstImageUpload;

        public Q\Plugin\Control\SortWrapper $dlgSorter;
        public mixed $intChangeId = null;
        public Q\Plugin\Control\RadioList $lstStatusMember;
        public Bs\Button $btnSave;
        public Bs\Button $btnClose;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;
        protected object $objPortlet;

        protected mixed $objId = null;

        protected string $strTemplate = 'MembersOptionsPanel.tpl.php';

        /**
         * Constructor method for initializing the object and setting up required configurations.
         *
         * @param mixed $objParentObject The parent object to be referenced during the construction.
         * @param string|null $strControlId Optional control identifier for the object.
         *
         * @return void
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

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);
            $this->objPortlet = Portlet::load(7);


            $this->createSorter();
            $this->createInputs();
            $this->createButtons();
            $this->createModals();
            $this->createToastr();
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

            $this->objPortlet->setLastDate(QDateTime::now());
            $this->objPortlet->save();
        }

        /**
         * Creates and configures a SortWrapper control for managing sortable elements.
         *
         * @return void
         * @throws Caller
         */
        public function createSorter(): void
        {
            $this->dlgSorter = new Q\Plugin\Control\SortWrapper($this);
            $this->dlgSorter->setDataBinder('Sorter_Bind', $this);
            $this->dlgSorter->createNodeParams([$this, 'Sorter_Draw']);
            $this->dlgSorter->createRenderInputs([$this, 'Inputs_Draw']);
            $this->dlgSorter->createRenderButtons([$this, 'Buttons_Draw']);

            $this->dlgSorter->addCssClass('sortable');
            $this->dlgSorter->Placeholder = 'placeholder';
            $this->dlgSorter->Handle = '.reorder';
            $this->dlgSorter->Items = 'div.div-block';
            $this->dlgSorter->addAction(new SortableStop(), new AjaxControl($this, 'Sortable_Stop'));
            $this->dlgSorter->watch(QQN::MembersOptions());
        }

        /**
         * Binds the sorter to the data source using the selected group title.
         * Fetches data from MembersOptions where the SettingsId matches the selected value
         * and orders the results by the Order field.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function Sorter_Bind(): void
        {
            $this->dlgSorter->DataSource = MembersOptions::queryArray(
                QQ::Equal(QQN::MembersOptions()->SettingsId, $this->lstGroupTitle->SelectedValue),
                QQ::Clause(QQ::OrderBy(QQN::MembersOptions()->Order)
                )
            );
        }

        /**
         * Method to draw the sorter details utilizing the given member options.
         *
         * @param MembersOptions $objMembersOptions Object containing member options.
         * @return array An associative array with keys 'id', 'name', and 'order' containing the sorted member options.
         */
        public function Sorter_Draw(MembersOptions $objMembersOptions): array
        {
            $a['id'] = $objMembersOptions->Id;
            $a['name'] = $objMembersOptions->Name;
            $a['order'] = $objMembersOptions->Order;
            return $a;
        }

        /**
         * Method to draw the input controls based on the given MemberOptions.
         *
         * @param MembersOptions $objMembersOptions The options related to members, including their ID and activity
         *     status.
         *
         * @return string The rendered status member list if the ID matches, otherwise the activity status object.
         * @throws Caller
         */
        public function Inputs_Draw(MembersOptions $objMembersOptions): string
        {
            if ($objMembersOptions->Id == $this->intChangeId) {
                return $this->lstStatusMember->render(false);
            } else {
                return $objMembersOptions->ActivityStatusObject;
            }
        }

        /**
         * Draws buttons based on certain conditions defined by MembersOptions object.
         *
         * @param MembersOptions $objMembersOptions The options for the members
         *                                          that affect button rendering.
         *
         * @return string
         * @throws Caller
         */
        public function Buttons_Draw(MembersOptions $objMembersOptions): string
        {
            if ($objMembersOptions->Id == $this->intChangeId) {
                return $this->btnClose->render(false);
            } else {

                $strChangeId = 'btnChange' . $objMembersOptions->Id;

                if (!$btnChange = $this->Form->getControl($strChangeId)) {
                    $btnChange = new Bs\Button($this->dlgSorter, $strChangeId);
                    $btnChange->Text = t('Change');
                    $btnChange->CssClass = 'btn btn-orange';
                    $btnChange->ActionParameter = $objMembersOptions->Id;
                    $btnChange->UseWrapper = false;
                    $btnChange->addAction(new Click(), new AjaxControl($this, 'btnChange_Click'));
                }
            }

            if ($objMembersOptions->InputKey !== 1) {
                return $btnChange->render(false);
            }

            return '';
        }

        /**
         * Method to create and initialize various input controls for the form.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('Please select a member group!');

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '90%';
            $this->lstGroupTitle->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroupTitle->setCssStyle('float', 'left');
            $this->lstGroupTitle->addItem(t('- Choose members group -'), null, true);

            $objMembersSettings = MembersSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::MembersSettings()->Id)));

            $countByIsReserved = MembersSettings::countByIsReserved(1);

            foreach ($objMembersSettings as $objMembersSetting) {
                if ($objMembersSetting->IsReserved === 1 && $countByIsReserved === 1) {
                    $this->lstGroupTitle->addItem($objMembersSetting->Name, $objMembersSetting->Id);
                    $this->lstGroupTitle->SelectedValue = $objMembersSetting->Id;
                } else if ($objMembersSetting->IsReserved === 1 && $countByIsReserved > 1) {
                    $this->lstGroupTitle->addItem($objMembersSetting->Name, $objMembersSetting->Id);
                }
            }

            if ($countByIsReserved === 1) {
                $this->lstGroupTitle->Enabled = false;
            } else {
                $this->lstGroupTitle->Enabled = true;
            }

            $this->lstGroupTitle->addAction(new Change(), new AjaxControl($this,'lstGroupTitle_Change'));

            if ($this->lstGroupTitle->SelectedValue) {
                $this->objId = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);
            }

            $this->lblPostDate = new Q\Plugin\Control\Label($this);
            $this->lblPostDate->Text = t('Created');
            $this->lblPostDate->setCssStyle('font-weight', 'bold');

            $this->calPostDate = new Bs\Label($this);
            $this->calPostDate->setCssStyle('font-weight', 'normal');

            $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
            $this->lblPostUpdateDate->Text = t('Updated');
            $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

            $this->calPostUpdateDate = new Bs\Label($this);
            $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

            $this->lblAuthor = new Q\Plugin\Control\Label($this);
            $this->lblAuthor->Text = t('Author');
            $this->lblAuthor->setCssStyle('font-weight', 'bold');

            $this->txtAuthor  = new Bs\Label($this);
            $this->txtAuthor->setCssStyle('font-weight', 'normal');

            $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
            $this->lblUsersAsEditors->Text = t('Editors');
            $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

            $this->txtUsersAsEditors  = new Bs\Label($this);
            $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->setCssStyle('margin-bottom', '-10px');
            $this->lblStatus->setCssStyle('font-weight', 'bold');

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
            $this->lstStatus->Enabled = true;
            $this->lstStatus->setCssStyle('margin-top', '-10px');
            $this->lstStatus->addAction(new Change(), new AjaxControl($this, 'lstStatus_Change'));

            $this->lblImageUpload = new Q\Plugin\Control\Label($this);
            $this->lblImageUpload->Text = t('Image upload');
            $this->lblImageUpload->setCssStyle('margin-bottom', '-10px');
            $this->lblImageUpload->setCssStyle('font-weight', 'bold');

            $this->lstImageUpload = new Q\Plugin\Control\RadioList($this);
            $this->lstImageUpload->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstImageUpload->ButtonGroupClass = 'radio radio-orange';
            $this->lstImageUpload->Enabled = true;
            $this->lstImageUpload->setCssStyle('margin-top', '-10px');
            $this->lstImageUpload->addAction(new Change(), new AjaxControl($this, 'lstImageUpload_Change'));

            if ($this->lstGroupTitle->SelectedValue) {
                $this->lblInfo->Display = false;
                $this->calPostDate->Text = $this->objId->PostDate ? $this->objId->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $this->calPostUpdateDate->Text = $this->objId->PostUpdateDate ? $this->objId->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $this->txtAuthor->Text = $this->objId->Author;
                $this->lstStatus->SelectedValue = $this->objId->Status;
                $this->lstImageUpload->SelectedValue = $this->objId->AllowedUploading;
                $this->txtUsersAsEditors->Text = implode(', ', $this->objId->getUserAsMembersEditorsArray());
                $this->refreshDisplay();
            } else {
                $this->calPostDate->Text = null;
                $this->calPostUpdateDate->Text = null;
                $this->txtAuthor->Text = null;
                $this->lstStatus->SelectedValue = null;
                $this->lstImageUpload->SelectedValue = null;
                $this->txtUsersAsEditors->Text = null;

                $this->lblInfo->Display = true;
                $this->lblPostDate->Display = false;
                $this->lblPostUpdateDate->Display = false;
                $this->lblAuthor->Display = false;
                $this->lblUsersAsEditors->Display = false;
                $this->lblStatus->Display = false;
                $this->lstStatus->Display = false;
                $this->lblImageUpload->Display = false;
                $this->lstImageUpload->Display = false;
            }

            $this->lstStatusMember = new Q\Plugin\Control\RadioList($this->dlgSorter);
            $this->lstStatusMember->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstStatusMember->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstStatusMember->addAction(new Change(), new AjaxControl($this, 'lstStatusMember_Change'));
        }

        /**
         * Method to create and configure the buttons used within the dialog sorter.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnClose = new Bs\Button($this->dlgSorter);
            $this->btnClose->Text = t('Close');
            $this->btnClose->CssClass = 'btn btn-default';
            $this->btnClose->CausesValidation = false;
            $this->btnClose->addAction(new Click(), new AjaxControl($this, 'btnClose_Click'));
        }

        /**
         * Initializes and sets up the various modal dialogs used throughout the application.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
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
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This members group is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This members group has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Success");
            $this->dlgModal5->HeaderClasses = 'btn-success';
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image upload is now disabled!</p>');
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Title = t("Success");
            $this->dlgModal6->HeaderClasses = 'btn-success';
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image upload is now enabled!</p>');
            $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal7 = new Bs\Modal($this);
            $this->dlgModal7->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal7->Title = t("Warning");
            $this->dlgModal7->HeaderClasses = 'btn-danger';
            $this->dlgModal7->addCloseButton(t("I understand"));
        }

        /**
         * Method to create and configure multiple Toastr notifications.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> Inputs were successfully sorted.');

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('Failed to sort inputs!');

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Well done!</strong> This entry is now public!');

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('<strong>Well done!</strong> This entry has been hidden!');

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('Updating this entry failed!');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the change button, updating the status of a member.
         *
         * @param ActionParams $params The parameters received from the click event, containing information such as action parameters.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function btnChange_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intChangeId = intval($params->ActionParameter);
            $obj = MembersOptions::loadById($this->intChangeId);
            $this->lstStatusMember->SelectedValue = $obj->ActivityStatusObject->Id;

            $this->dlgSorter->refresh();
            $this->userOptions();
        }

        /**
         * Event handler for the close button click event.
         * Resets the change ID to null.
         *
         * @param ActionParams $params Parameters associated with the button click event
         *
         * @return void
         * @throws RandomException
         * @throws Caller
         */
        protected function btnClose_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intChangeId = null;

            $this->dlgSorter->refresh();
            $this->userOptions();
        }

        /**
         * Method to handle stopping of sortable actions, updating order and editor settings.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function Sortable_Stop(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $arr = $this->dlgSorter->ItemArray;

            foreach ($arr as $order => $cids) {
                $cid = explode('_',  $cids);
                $id = end($cid);

                $objSorter = MembersOptions::load($id);
                $objSorter->setOrder($order);
                $objSorter->save();
            }

            $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);

            $objMembersSettings->setPostUpdateDate(QDateTime::now());
            $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objMembersSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
            $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);

            $this->refreshDisplay();

            // Let's check if the array is not empty
            if (!empty($arr)) {
                $this->dlgToastr1->notify();
            } else {
                $this->dlgToastr2->notify();
            }

            $this->intChangeId = null;

            $this->dlgSorter->refresh();
            $this->userOptions();
        }

        /**
         * Handles the change in status of the list and performs corresponding actions based on the status.
         *
         * @param ActionParams $params Parameters associated with the action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);
            $objMenu = Menu::loadById($objMembersSettings->getMenuContentId());
            $objMenuContent = MenuContent::loadById($objMembersSettings->getMenuContentId());

            if ($objMenu->ParentId || $objMenu->Right !== $objMenu->Left + 1) {
                $this->dlgModal1->showDialogBox();
                $this->updateInputFields();
                return;
            }

            if ($objMenuContent->SelectedPageLocked === 1) {
                $this->dlgModal2->showDialogBox();
                $this->updateInputFields();
                return;
            }

            $objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $objMenuContent->save();

            $objMembersSettings->setStatus($this->lstStatus->SelectedValue);
            $objMembersSettings->setPostUpdateDate(QDateTime::now());
            $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objMembersSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
            $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);

            if ($objMembersSettings->getStatus() === 2) {
                $this->dlgModal3->showDialogBox();
            } else {
                $this->dlgModal4->showDialogBox();
            }

            $this->refreshDisplay();
            $this->userOptions();
        }

        /**
         * Updates the input fields based on the selected value in lstGroupTitle.
         * It sets the selected value of lstStatus to the status obtained from MembersSettings.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function updateInputFields(): void
        {
            $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);

            $this->lstStatus->SelectedValue = $objMembersSettings->getStatus();
            $this->lstStatus->refresh();
        }

        /**
         * Handles the change event for the image upload selection.
         * Depending on the settings of the selected member group, it shows different dialog boxes
         * or updates the image upload accordingly.
         *
         * @param ActionParams $params The parameters provided by the event triggering the method.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstImageUpload_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);

            $objMembersSettings->setAllowedUploading($this->lstImageUpload->SelectedValue);
            $objMembersSettings->setPostUpdateDate(QDateTime::now());
            $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objMembersSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
            $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);

            if ($objMembersSettings->getAllowedUploading() === 2) {
                $this->dlgModal5->showDialogBox();
            } else {
                $this->dlgModal6->showDialogBox();
            }

            $this->refreshDisplay();
            $this->userOptions();
        }

        /**
         * Method to handle changes in the status member list.
         *
         * @param ActionParams $params The parameters from the action event.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstStatusMember_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);
            $objMembersOptions = MembersOptions::load($this->intChangeId);

            $objMembersSettings->setPostUpdateDate(QDateTime::Now());
            $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objMembersSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
            $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);

            $this->refreshDisplay();
            $this->userOptions();

            if ($objMembersOptions->ActivityStatusObject->Id === 1) {
                $objMembersOptions->setActivityStatus(2);
                $objMembersOptions->save();
                $this->lstStatusMember->SelectedValue = 2;
                $this->dlgToastr4->notify();
            } else if ($objMembersOptions->ActivityStatusObject->Id === 2) {
                $objMembersOptions->setActivityStatus(1);
                $objMembersOptions->save();
                $this->lstStatusMember->SelectedValue = 1;
                $this->dlgToastr3->notify();
            } else {
                $this->dlgToastr5->notify();
            }
        }

        /**
         * Handles changes in the group title list and updates the form fields accordingly.
         *
         * @param ActionParams $params Parameters passed from the action triggering the change.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstGroupTitle_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal7->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstGroupTitle->SelectedValue) {
                $this->objId = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);

                $this->lblInfo->Display = false;
                $this->calPostDate->Text = $this->objId->PostDate ? $this->objId->PostDate->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time) : null;
                $this->calPostUpdateDate->Text = $this->objId->PostUpdateDate ? $this->objId->PostUpdateDate->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time) : null;
                $this->txtAuthor->Text = $this->objId->Author;
                $this->lstStatus->SelectedValue = $this->objId->Status;
                $this->lstImageUpload->SelectedValue = $this->objId->AllowedUploading;
                $this->txtUsersAsEditors->Text = implode(', ', $this->objId->getUserAsMembersEditorsArray());

                $this->dlgSorter->refresh();
                $this->refreshDisplay();
            } else {
                $this->calPostDate->Text = null;
                $this->calPostUpdateDate->Text = null;
                $this->txtAuthor->Text = null;
                $this->lstStatus->SelectedValue = null;
                $this->lstImageUpload->SelectedValue = null;
                $this->txtUsersAsEditors->Text = null;

                $this->lblInfo->Display = true;
                $this->lblPostDate->Display = false;
                $this->lblPostUpdateDate->Display = false;
                $this->lblAuthor->Display = false;
                $this->lblUsersAsEditors->Display = false;
                $this->lblStatus->Display = false;
                $this->lstStatus->Display = false;
                $this->lblImageUpload->Display = false;
                $this->lstImageUpload->Display = false;
            }

            $this->intChangeId = null;

            $this->dlgSorter->refresh();
            $this->userOptions();
        }

        /**
         * Refresh the display based on the state of the objId properties.
         *
         * The display settings for various labels and controls are toggled
         * depending on whether the post and author information exist and
         * whether users are counted as members or editors.
         *
         * @return void
         */
        protected function refreshDisplay(): void
        {
            if ($this->objId->getPostDate() &&
                !$this->objId->getPostUpdateDate() &&
                $this->objId->getAuthor() &&
                !$this->objId->countUsersAsMembersEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
                $this->lblStatus->Display = true;
                $this->lstStatus->Display = true;
                $this->lblImageUpload->Display = true;
                $this->lstImageUpload->Display = true;
            }

            if ($this->objId->getPostDate() &&
                $this->objId->getPostUpdateDate() &&
                $this->objId->getAuthor() &&
                !$this->objId->countUsersAsMembersEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
                $this->lblStatus->Display = true;
                $this->lstStatus->Display = true;
                $this->lblImageUpload->Display = true;
                $this->lstImageUpload->Display = true;
            }

            if ($this->objId->getPostDate() &&
                $this->objId->getPostUpdateDate() &&
                $this->objId->getAuthor() &&
                $this->objId->countUsersAsMembersEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = true;
                $this->txtUsersAsEditors->Display = true;
                $this->lblStatus->Display = true;
                $this->lstStatus->Display = true;
                $this->lblImageUpload->Display = true;
                $this->lstImageUpload->Display = true;
            }
        }
    }