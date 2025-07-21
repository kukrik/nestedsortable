<?php

use QCubed as Q;
//use QCubed\Action\Ajax;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Action\ActionParams;
use QCubed\Query\QQ;
use QCubed\Project\Application;

class MembersOptionsPanel extends Q\Control\Panel
{
    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;
    public $dlgModal6;
    public $dlgModal7;
    public $dlgModal8;
    public $dlgModal9;

    protected $dlgToastr1;
    protected $dlgToastr2;
    protected $dlgToastr3;
    protected $dlgToastr4;
    protected $dlgToastr5;

    public $lblInfo;
    public $lstGroupTitle;
    public $lblPostDate;
    public $calPostDate;
    public $lblPostUpdateDate;
    public $calPostUpdateDate;
    public $lblAuthor;
    public $txtAuthor;
    public $lblUsersAsEditors;
    public $txtUsersAsEditors;
    public $lblStatus;
    public $lstStatus;
    public $lblImageUpload;
    public $lstImageUpload;

    public $dlgSorter;
    public $intChangeId = null;
    public $lstStatusMember;
    public $btnSave;
    public $btnClose;
    protected $intLoggedUserId;
    protected $objId;

    protected $strTemplate = 'MembersOptionsPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        /**
         * NOTE: if the user_id is stored in session (e.g. if a User is logged in), as well, for example:
         * checking against user session etc.
         *
         * Must to save something here $this->objNews->setUserId(logged user session);
         * or something similar...
         *
         * Options to do this are left to the developer.
         **/

        // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
        // For example, John Doe is a logged user with his session

        $this->intLoggedUserId = 2;

        $this->createSorter();
        $this->createInputs();
        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and configures a SortWrapper control for managing sortable elements.
     *
     * @return void
     */
    public function createSorter()
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
        $this->dlgSorter->addAction(new Q\Jqui\Event\SortableStop(), new Q\Action\AjaxControl($this, 'Sortable_Stop'));
        $this->dlgSorter->watch(QQN::MembersOptions());
    }

    /**
     * Method to perform operations just before the form is rendered.
     *
     * @return void
     */
    protected function formPreRender()
    {
        $this->dlgSorter->refresh();
    }

    /**
     * Binds the sorter to the data source using the selected group title.
     * Fetches data from MembersOptions where the SettingsId matches the selected value
     * and orders the results by the Order field.
     *
     * @return void
     */
    public function Sorter_Bind()
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
     * @return array Associative array with keys 'id', 'name', and 'order' containing the sorted member options.
     */
    public function Sorter_Draw(MembersOptions $objMembersOptions)
    {
        $a['id'] = $objMembersOptions->Id;
        $a['name'] = $objMembersOptions->Name;
        $a['order'] = $objMembersOptions->Order;
        //$a['status'] = $objMembersOptions->ActivityStatus;
        return $a;
    }

    /**
     * Method to draw the input controls based on the given MemberOptions.
     *
     * @param MembersOptions $objMembersOptions The options related to members, including their ID and activity status.
     *
     * @return mixed The rendered status member list if the ID matches, otherwise the activity status object.
     */
    public function Inputs_Draw(MembersOptions $objMembersOptions)
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
     * @return void
     */
    public function Buttons_Draw(MembersOptions $objMembersOptions)
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
                $btnChange->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnChange_Click'));
            }
        }

        if ($objMembersOptions->InputKey !== 1) {
            return $btnChange->render(false);
        }
    }

    /**
     * Method to create and initialize various input controls for the form.
     *
     * @return void
     */
    public function createInputs()
    {
        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please select a members group!');

        $this->lstGroupTitle = new Q\Plugin\Select2($this);
        $this->lstGroupTitle->MinimumResultsForSearch = -1;
        $this->lstGroupTitle->Theme = 'web-vauu';
        $this->lstGroupTitle->Width = '90%';
        $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
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

        $this->lstGroupTitle->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstGroupTitle_Change'));

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
        $this->lstStatus->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstStatus_Change'));

        $this->lblImageUpload = new Q\Plugin\Control\Label($this);
        $this->lblImageUpload->Text = t('Image upload');
        $this->lblImageUpload->setCssStyle('margin-bottom', '-10px');
        $this->lblImageUpload->setCssStyle('font-weight', 'bold');

        $this->lstImageUpload = new Q\Plugin\Control\RadioList($this);
        $this->lstImageUpload->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstImageUpload->ButtonGroupClass = 'radio radio-orange';
        $this->lstImageUpload->Enabled = true;
        $this->lstImageUpload->setCssStyle('margin-top', '-10px');
        $this->lstImageUpload->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstImageUpload_Change'));

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
        $this->lstStatusMember->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstStatusMember_Change'));

    }

    /**
     * Method to create and configure the buttons used within the dialog sorter.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnClose = new Bs\Button($this->dlgSorter);
        $this->btnClose->Text = t('Close');
        $this->btnClose->CssClass = 'btn btn-default';
        $this->btnClose->CausesValidation = false;
        $this->btnClose->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnClose_Click'));
    }

    /**
     * Initializes and sets up the various modal dialogs used throughout the application.
     *
     * @return void
     */
    protected function createModals()
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
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this members group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this group public again later!</p>');
        $this->dlgModal3->Title = t('Question');
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addCloseButton(t("I'll cancel"));
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'statusItem_Click'));
        $this->dlgModal3->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hideItem_Click'));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Success");
        $this->dlgModal4->HeaderClasses = 'btn-success';
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This members group is now hidden!</p>');
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This members group has now been made public!</p>');
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to disable the image upload for this member group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can re-enable the image upload for this group later!</p>');
        $this->dlgModal6->Title = t('Question');
        $this->dlgModal6->HeaderClasses = 'btn-danger';
        $this->dlgModal6->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal6->addCloseButton(t("I'll cancel"));
        $this->dlgModal6->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'imageIploadItem_Click'));
        $this->dlgModal6->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'cancelItem_Click'));

        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Title = t("Success");
        $this->dlgModal7->HeaderClasses = 'btn-success';
        $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image upload is now disabled!</p>');
        $this->dlgModal7->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal8 = new Bs\Modal($this);
        $this->dlgModal8->Title = t("Success");
        $this->dlgModal8->HeaderClasses = 'btn-success';
        $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image upload is now enabled!</p>');
        $this->dlgModal8->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal9 = new Bs\Modal($this);
        $this->dlgModal9->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal9->Title = t("Warning");
        $this->dlgModal9->HeaderClasses = 'btn-danger';
        $this->dlgModal9->addCloseButton(t("I understand"));
    }

    /**
     * Method to create and configure multiple Toastr notifications.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> Inputs were successfully sorted.');

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('Failed to sort inputs!');

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Well done!</strong> This entry is now public!');

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('<strong>Well done!</strong> This entry has been hidden!');

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('Updating this entry failed!');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the change button, updating the status of a member.
     *
     * @param ActionParams $params The parameters received from the click event, containing information such as action parameters.
     * @return void
     */
    public function btnChange_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intChangeId = intval($params->ActionParameter);
        $obj = MembersOptions::loadById($this->intChangeId);
        $this->lstStatusMember->SelectedValue = $obj->ActivityStatusObject->Id;
    }

    /**
     * Event handler for the close button click event.
     * Resets the change ID to null.
     *
     * @param ActionParams $params Parameters associated with the button click event
     * @return void
     */
    protected function btnClose_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intChangeId = null;
    }

    /**
     * Method to handle stopping of sortable actions, updating order and editor settings.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    protected function Sortable_Stop(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
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

        $objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        // Let's check if the array is not empty
        if (!empty($arr)) {
            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the change in status of the list and performs corresponding actions based on the status.
     *
     * @param ActionParams $params Parameters associated with the action.
     * @return void
     */
    protected function lstStatus_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);
        $objMenu = Menu::loadById($objMembersSettings->getMenuContentId());
        $objMenuContent = MenuContent::loadById($objMembersSettings->getMenuContentId());

        if ($objMenu->ParentId || $objMenu->Right !== $objMenu->Left + 1) {
            $this->dlgModal1->showDialogBox();
            $this->updateInputFields();
        } else if ($objMenuContent->SelectedPageLocked === 1) {
            $this->dlgModal2->showDialogBox();
            $this->updateInputFields();
        } else if ($objMembersSettings->getStatus() === 1) {
            $this->dlgModal3->showDialogBox();
        } else {
            $this->lockInputFields();
        }
    }

    /**
     * Updates the input fields based on the selected value in lstGroupTitle.
     * It sets the selected value of lstStatus to the status obtained from MembersSettings.
     *
     * @return void
     */
    protected function updateInputFields()
    {
        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);

        $this->lstStatus->SelectedValue = $objMembersSettings->getStatus();
        $this->lstStatus->refresh();
    }

    /**
     * Method to handle the click event on a status item and update related entities.
     *
     * @param ActionParams $params Parameters associated with the action event.
     * @return void
     */
    protected function statusItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->lstStatus->SelectedValue === 2) {
            $this->dlgModal3->hideDialogBox();
        }

        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);
        $objMenuContent = MenuContent::loadById($objMembersSettings->getMenuContentId());

        $objMenuContent->setIsEnabled(2);
        $objMenuContent->save();

        $objMembersSettings->setStatus(2);
        $objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    /**
     * Method to lock input fields based on the selected status and update related settings and content.
     *
     * @return void
     */
    protected function lockInputFields()
    {
        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);
        $objMenuContent = MenuContent::loadById($objMembersSettings->getMenuContentId());

        if ($this->lstStatus->SelectedValue === 1) {
            $this->dlgModal5->showDialogBox();
            $objMenuContent->setIsEnabled(1);
            $objMembersSettings->setStatus(1);
        } else if ($this->lstStatus->SelectedValue === 2) {
            $this->dlgModal4->showDialogBox();
            $objMenuContent->setIsEnabled(2);
            $objMembersSettings->setStatus(2);
        }

        $objMenuContent->save();

        $objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    /**
     * Handles the click event for hiding an item and sets the selected value of the status list.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function hideItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);
        $this->lstStatus->SelectedValue = $objMembersSettings->getStatus();
    }

    /**
     * Handles the change event for the image upload selection.
     * Depending on the settings of the selected member group, it shows different dialog boxes
     * or updates the image upload accordingly.
     *
     * @param ActionParams $params The parameters provided by the event triggering the method.
     * @return void
     */
    protected function lstImageUpload_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);

        if ($objMembersSettings->getAllowedUploading() === 1) {
            $this->dlgModal6->showDialogBox();
        } else if ($objMembersSettings->getAllowedUploading() === 2) {
            $this->dlgModal8->showDialogBox();
            $this->updateImageUpload();
        } else if ($this->lstImageUpload->SelectedValue === 1) {
            $this->dlgModal7->showDialogBox();
        }
    }

    /**
     * Handles the click event for an item in the image upload section.
     *
     * @param ActionParams $params Parameters supplied by the action that triggered the event.
     * @return void
     */
    protected function imageIploadItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->dlgModal6->hideDialogBox();

        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);

        $objMembersSettings->setAllowedUploading(2);
        $objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    /**
     * Method to update image upload settings and refresh display elements.
     *
     * @return void
     */
    protected function updateImageUpload()
    {
        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);

        $objMembersSettings->setAllowedUploading(1);
        $objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    /**
     * Method to handle the cancel button click event.
     *
     * @param ActionParams $params The parameters passed during the action event.
     * @return void
     */
    protected function cancelItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);
        $this->lstImageUpload->SelectedValue = $objMembersSettings->getAllowedUploading();
    }

    /**
     * Method to handle changes in the status member list.
     *
     * @param ActionParams $params The parameters from the action event.
     * @return void
     */
    protected function lstStatusMember_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objMembersSettings = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);
        $objMembersOptions = MembersOptions::load($this->intChangeId);

        $objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

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
     * @return void
     */
    protected function lstGroupTitle_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->lstGroupTitle->SelectedValue) {
            $this->objId = MembersSettings::loadById($this->lstGroupTitle->SelectedValue);

            $this->lblInfo->Display = false;
            $this->calPostDate->Text = $this->objId->PostDate ? $this->objId->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->Text = $this->objId->PostUpdateDate ? $this->objId->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
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
    protected function refreshDisplay()
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