<?php

use QCubed as Q;
//use QCubed\Action\Ajax;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Action\ActionParams;
use QCubed\Query\QQ;
use QCubed\Project\Application;

class BoardOptionsPanel extends Q\Control\Panel
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
    public $lstStatusBoard;
    public $btnSave;
    public $btnClose;
    protected $intLoggedUserId;
    protected $objId;

    protected $strTemplate = 'BoardOptionsPanel.tpl.php';

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

        $this->intLoggedUserId = 1;

        $this->createSorter();
        $this->createInputs();
        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the sorter component for the application.
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
        $this->dlgSorter->watch(QQN::BoardOptions());
    }

    /**
     * Performs pre-render operations for the form.
     * Refreshes the dlgSorter component to ensure it is up-to-date before rendering.
     *
     * @return void
     */
    protected function formPreRender()
    {
        $this->dlgSorter->refresh();
    }

    /**
     * Binds the data source for dlgSorter using the selected settings ID from lstGroupTitle.
     * Queries the BoardOptions with conditions and orders the results.
     *
     * @return void
     */
    public function Sorter_Bind()
    {
        $this->dlgSorter->DataSource = BoardOptions::queryArray(
            QQ::Equal(QQN::BoardOptions()->SettingsId, $this->lstGroupTitle->SelectedValue),
            QQ::Clause(QQ::OrderBy(QQN::BoardOptions()->Order)
            )
        );
    }

    /**
     * Constructs an associative array representing board options
     * with specific properties: 'id', 'name', and 'order', derived from
     * the provided BoardOptions object.
     *
     * @param BoardOptions $objBoardOptions The board options object containing the data.
     * @return array An associative array with keys 'id', 'name', and 'order'.
     */
    public function Sorter_Draw(BoardOptions $objBoardOptions)
    {
        $a['id'] = $objBoardOptions->Id;
        $a['name'] = $objBoardOptions->Name;
        $a['order'] = $objBoardOptions->Order;
        return $a;
    }

    /**
     * Draws the inputs for the board options.
     * Depending on the board option's ID, it either renders the status board list or
     * returns the board option's activity status object.
     *
     * @param BoardOptions $objBoardOptions The board options object containing the ID and activity status.
     * @return mixed The rendered status board list if the IDs match, or the activity status object otherwise.
     */
    public function Inputs_Draw(BoardOptions $objBoardOptions)
    {
        if ($objBoardOptions->Id == $this->intChangeId) {
            return $this->lstStatusBoard->render(false);
        } else {
            return $objBoardOptions->ActivityStatusObject;
        }
    }

    /**
     * Draws buttons based on the provided board options.
     * It renders a close button or a change button depending on the board options.
     * If the board option's Id matches the change Id, it renders a close button.
     * Otherwise, it initializes and renders a change button if it doesn't already exist.
     * The change button triggers an Ajax control action on click.
     *
     * @param BoardOptions $objBoardOptions The board options containing configurations for rendering buttons.
     *
     * @return void
     */
    public function Buttons_Draw(BoardOptions $objBoardOptions)
    {
        if ($objBoardOptions->Id == $this->intChangeId) {
            return $this->btnClose->render(false);
        } else {

            $strChangeId = 'btnChange' . $objBoardOptions->Id;

            if (!$btnChange = $this->Form->getControl($strChangeId)) {
                $btnChange = new Bs\Button($this->dlgSorter, $strChangeId);
                $btnChange->Text = t('Change');
                $btnChange->CssClass = 'btn btn-orange';
                $btnChange->ActionParameter = $objBoardOptions->Id;
                $btnChange->UseWrapper = false;
                $btnChange->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnChange_Click'));
            }
        }

        if ($objBoardOptions->InputKey !== 1) {
            return $btnChange->render(false);
        }
    }

    /**
     * Initializes and sets up various input controls for managing board settings.
     * Configures labels, selection lists, and other UI components to facilitate interaction.
     * Performs conditional logic based on group title selection to display relevant information.
     *
     * @return void
     */
    public function createInputs()
    {
        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please select a board group!');

        $this->lstGroupTitle = new Q\Plugin\Select2($this);
        $this->lstGroupTitle->MinimumResultsForSearch = -1;
        $this->lstGroupTitle->Theme = 'web-vauu';
        $this->lstGroupTitle->Width = '90%';
        $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstGroupTitle->setCssStyle('float', 'left');
        $this->lstGroupTitle->addItem(t('- Choose board group -'), null, true);

        $objBoardsSettings = BoardsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::BoardsSettings()->Id)));

        $countByIsReserved = BoardsSettings::countByIsReserved(1);

        foreach ($objBoardsSettings as $objBoardsSetting) {
            if ($objBoardsSetting->IsReserved === 1 && $countByIsReserved === 1) {
                $this->lstGroupTitle->addItem($objBoardsSetting->Name, $objBoardsSetting->Id);
                $this->lstGroupTitle->SelectedValue = $objBoardsSetting->Id;
            } else if ($objBoardsSetting->IsReserved === 1 && $countByIsReserved > 1) {
                $this->lstGroupTitle->addItem($objBoardsSetting->Name, $objBoardsSetting->Id);
            }
        }

        if ($countByIsReserved === 1) {
            $this->lstGroupTitle->Enabled = false;
        } else {
            $this->lstGroupTitle->Enabled = true;
        }

        $this->lstGroupTitle->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstGroupTitle_Change'));

        if ($this->lstGroupTitle->SelectedValue) {
            $this->objId = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);
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
        $this->lblStatus->setCssStyle('font-weight', 'bold');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->Enabled = true;
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
            $this->txtUsersAsEditors->Text = implode(', ', $this->objId->getUserAsBoardsEditorsArray());
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

        $this->lstStatusBoard = new Q\Plugin\Control\RadioList($this->dlgSorter);
        $this->lstStatusBoard->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatusBoard->ButtonGroupClass = 'radio radio-orange radio-inline';
        $this->lstStatusBoard->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstStatusBoard_Change'));
    }

    /**
     * Creates and initializes the necessary buttons for user interaction.
     * Sets properties for the close button, including text, CSS class, and click action handler,
     * to facilitate user interaction without causing validation.
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
     * Creates and initializes a series of modal dialogs used in the application.
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
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this board group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this group public again later!</p>');
        $this->dlgModal3->Title = t('Question');
        $this->dlgModal3->HeaderClasses = 'btn-warning';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'statusItem_Click'));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Success");
        $this->dlgModal4->HeaderClasses = 'btn-success';
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This board group is now hidden!</p>');
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This board group has now been made public!</p>');
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This members group has now been made public!</p>');
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to disable the image upload for this board member group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can re-enable the image upload for this group later!</p>');
        $this->dlgModal6->Title = t('Question');
        $this->dlgModal6->HeaderClasses = 'btn-warning';
        $this->dlgModal6->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal6->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal6->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'imageIploadItem_Click'));

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
     * Creates and configures a series of Toastr notifications used within the application.
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
     * Handles the click event for the change button, updates the selected value in the status list for the specific board option.
     *
     * @param ActionParams $params The parameters associated with the action, including the ID of the board option to update.
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
        $obj = BoardOptions::loadById($this->intChangeId);
        $this->lstStatusBoard->SelectedValue = $obj->ActivityStatusObject->Id;
    }

    /**
     * Handles the click event for the close button, resetting the change identifier.
     *
     * @param ActionParams $params The parameters associated with the action event.
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
     * Finalizes the sorting process and updates relevant board settings and editors.
     *
     * @param ActionParams $params The parameters received from the sortable stop event.
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

            $objSorter = BoardOptions::load($id);
            $objSorter->setOrder($order);
            $objSorter->save();
        }

        $objBoardsSettings = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);

        $objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        // Let's check if the array is not empty
        if (!empty($arr)) {
            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the change event for the status of a list item, triggering different modal dialogs
     * based on certain conditions of related board settings and menu content.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function lstStatus_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objBoardsSettings = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);
        $objMenu = Menu::loadById($objBoardsSettings->getMenuContentId());
        $objMenuContent = MenuContent::loadById($objBoardsSettings->getMenuContentId());

        if ($objMenu->ParentId || $objMenu->Right !== $objMenu->Left + 1) {
            $this->dlgModal1->showDialogBox();
            $this->updateInputFields();
        } else if ($objMenuContent->SelectedPageLocked === 1) {
            $this->dlgModal2->showDialogBox();
            $this->updateInputFields();
        } else if ($objBoardsSettings->getStatus() === 1) {
            $this->dlgModal3->showDialogBox();
        } else {
            $this->lockInputFields();
        }
    }

    /**
     * Updates the input fields related to board settings based on the selected group title.
     *
     * @return void
     */
    protected function updateInputFields()
    {
        $objBoardsSettings = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);

        $this->lstStatus->SelectedValue = $objBoardsSettings->getStatus();
        $this->lstStatus->refresh();
    }

    /**
     * Handles the click event to update the status of a selected item and associated menu content.
     *
     * @param ActionParams $params The parameters passed from the action event.
     * @return void
     */
    protected function statusItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objBoardsSettings = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);
        $objMenuContent = MenuContent::loadById($objBoardsSettings->getMenuContentId());

        $objBoardsSettings->setStatus(2);
        $objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $objMenuContent->setIsEnabled(2);
        $objMenuContent->save();

        $this->refreshDisplay();
        $this->dlgModal4->showDialogBox();
    }

    /**
     * Locks input fields by updating board and menu content settings.
     * This method updates the status, post update date, and assigned editor
     * information for a board, and enables associated menu content. It finalizes
     * by refreshing the display and showing a confirmation dialog.
     *
     * @return void
     */
    protected function lockInputFields()
    {
        $objBoardsSettings = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);
        $objMenuContent = MenuContent::loadById($objBoardsSettings->getMenuContentId());

        $objBoardsSettings->setStatus(1);
        $objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $objMenuContent->setIsEnabled(1);
        $objMenuContent->save();

        $this->refreshDisplay();
        $this->dlgModal5->showDialogBox();
    }

    /**
     * Handles the change event for the image upload selection list.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function lstImageUpload_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objBoardsSettings = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);

        if ($objBoardsSettings->getAllowedUploading() === 1) {
            $this->dlgModal6->showDialogBox();
        } else {
            $this->dlgModal8->showDialogBox();
            $this->updateImageUpload();
        }
    }

    /**
     * Handles the click event for the image upload item, updating board settings and user interface accordingly.
     *
     * @param ActionParams $params The parameters associated with the action event.
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

        $objBoardsSettings = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);

        $objBoardsSettings->setAllowedUploading(2);
        $objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
        $this->dlgModal7->showDialogBox();
    }

    /**
     * Updates the image uploading configurations and refreshes display elements.
     *
     * This method sets the allowance for uploading images to true, updates the post update date
     * to the current date and time, assigns the name of the current logged-in user as an editor,
     * and saves these changes. Afterwards, it updates the text fields for displaying the users
     * who are editors and the last updated date, formatted accordingly.
     *
     * @return void
     */
    protected function updateImageUpload()
    {
        $objBoardsSettings = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);

        $objBoardsSettings->setAllowedUploading(1);
        $objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    /**
     * Handles the change event for the status board, updating board settings and options.
     *
     * This method retrieves the current board settings and options based on selected values. It updates
     * the post update date and assigns the current user as an editor, saving these changes. It refreshes
     * the display with updated editor user lists and date formats. Additionally, it toggles the activity
     * status of the board options and saves the new status, while notifying the user of the change with respective dialogs.
     *
     * @param ActionParams $params The parameters received from the action triggering the change event.
     * @return void
     */
    protected function lstStatusBoard_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal9->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objBoardsSettings = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);
        $objBoardsOptions = BoardOptions::load($this->intChangeId);

        $objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        if ($objBoardsOptions->ActivityStatusObject->Id === 1) {
            $objBoardsOptions->setActivityStatus(2);
            $objBoardsOptions->save();
            $this->lstStatusBoard->SelectedValue = 2;
            $this->dlgToastr4->notify();
        } else if ($objBoardsOptions->ActivityStatusObject->Id === 2) {
            $objBoardsOptions->setActivityStatus(1);
            $objBoardsOptions->save();
            $this->lstStatusBoard->SelectedValue = 1;
            $this->dlgToastr3->notify();
        } else {
            $this->dlgToastr5->notify();
        }
    }

    /**
     * Handles changes to the group title selection and updates relevant display elements.
     *
     * This method is triggered when the group title selection changes. If a valid selection
     * is made, it retrieves the appropriate board settings and updates the display of various
     * board-related fields including post dates, author, status, image upload permissions, and
     * a list of editor users. It also refreshes the display elements. If no valid selection is
     * made, it clears the fields and adjusts the visibility of display elements accordingly.
     *
     * @param ActionParams $params The parameters triggered by the change event.
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
            $this->objId = BoardsSettings::loadById($this->lstGroupTitle->SelectedValue);

            $this->lblInfo->Display = false;
            $this->calPostDate->Text = $this->objId->PostDate ? $this->objId->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->Text = $this->objId->PostUpdateDate ? $this->objId->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->txtAuthor->Text = $this->objId->Author;
            $this->lstStatus->SelectedValue = $this->objId->Status;
            $this->lstImageUpload->SelectedValue = $this->objId->AllowedUploading;
            $this->txtUsersAsEditors->Text = implode(', ', $this->objId->getUserAsBoardsEditorsArray());

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
     * Refreshes the display based on the current state of the post.
     *
     * This method updates the display settings of various labels and fields according to the status
     * of post data such as post date, post update date, author, and the count of users as board editors.
     * It manages the visibility of these elements to ensure consistency with the state of the post.
     *
     * @return void
     */
    protected function refreshDisplay()
    {
        if ($this->objId->getPostDate() &&
            !$this->objId->getPostUpdateDate() &&
            $this->objId->getAuthor() &&
            !$this->objId->countUsersAsBoardsEditors()) {
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
            !$this->objId->countUsersAsBoardsEditors()) {
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
            $this->objId->countUsersAsBoardsEditors()) {
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