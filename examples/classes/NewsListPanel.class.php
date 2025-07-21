<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;
use QCubed\QString;

class NewsListPanel extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    protected $dlgToast1;
    protected $dlgToast2;
    protected $dlgToast3;
    protected $dlgToast4;
    protected $dlgToast5;

    public $dlgModal1;
    public $dlgModal2;

    public $txtFilter;
    public $dtgNews;

    public $btnAddNews;
    public $btnMove;
    public $txtTitle;
    public $lblGroupTitle;
    public $lstNewsLocked;
    public $lstTargetGroup;
    public $lstGroupTitle;
    public $btnSave;
    public $btnCancel;
    public $btnLockedCancel;
    public $btnBack;

    protected $objUser;
    protected $intLoggedUserId;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'NewsListPanel.tpl.php';

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
         * Must have to get something like here $this->objUser->getUserId(logged user session);
         * or something similar...
         *
         * Options to do this are left to the developer.
         **/

        // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
        // For example, John Doe is a logged user with his session

        $this->intLoggedUserId = 3;
        $this->objUser = User::load($this->intLoggedUserId);

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
     * Initializes and configures input elements for the form, including a text box for the title and a select list for target groups.
     *
     * @return void
     */
    protected function createInputs()
    {
        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Title of the new news');
        $this->txtTitle->setCssStyle('float', 'left');
        $this->txtTitle->setHtmlAttribute('autocomplete', 'off');

        $this->lstTargetGroup = new Q\Plugin\Select2($this);
        $this->lstTargetGroup->MinimumResultsForSearch = -1;
        $this->lstTargetGroup->Theme = 'web-vauu';
        $this->lstTargetGroup->Width = '100%';
        $this->lstTargetGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);

        $objTargetGroups = NewsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::NewsSettings()->Id)));
        foreach ($objTargetGroups as $objTitle) {
            if ($objTitle->IsReserved !== 2) {
                $this->lstTargetGroup->addItem($objTitle->Name, $objTitle->Id);
            }
        }

        $this->lstTargetGroup->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstTargetGroup_Change'));
        $this->lstTargetGroup->setCssStyle('float', 'left');
        $this->lstTargetGroup->Enabled = false;
    }

    /**
     * Creates and initializes a set of buttons with various functionalities like adding news, moving items, saving changes, canceling actions, and going back.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnAddNews = new Bs\Button($this);
        $this->btnAddNews->Text = t(' Add news');
        $this->btnAddNews->Glyph = 'fa fa-plus';
        $this->btnAddNews->CssClass = 'btn btn-orange';
        $this->btnAddNews->addWrapperCssClass('center-button');
        $this->btnAddNews->CausesValidation = false;
        $this->btnAddNews->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddNews_Click'));

        $this->btnMove = new Bs\Button($this);
        $this->btnMove->Text = t(' Move');
        $this->btnMove->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnMove->CssClass = 'btn btn-darkblue move-button-js';
        $this->btnMove->addWrapperCssClass('center-button');
        $this->btnMove->CausesValidation = false;
        $this->btnMove->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnMove_Click'));

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
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->setCssStyle('float', 'left');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));

        $this->btnLockedCancel = new Bs\Button($this);
        $this->btnLockedCancel->Text = t('Cancel');
        $this->btnLockedCancel->addWrapperCssClass('center-button');
        $this->btnLockedCancel->CssClass = 'btn btn-default';
        $this->btnLockedCancel->setCssStyle('float', 'left');
        $this->btnLockedCancel->CausesValidation = false;
        $this->btnLockedCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnLockedCancel_Click'));

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->addWrapperCssClass('center-button');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnBack_Click'));
    }

    /**
     * Initializes and configures multiple toastr notification objects with predefined settings.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToast1 = new Q\Plugin\Toastr($this);
        $this->dlgToast1->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast1->Message = t('The news title is at least mandatory!');
        $this->dlgToast1->ProgressBar = true;

        $this->dlgToast2 = new Q\Plugin\Toastr($this);
        $this->dlgToast2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast2->Message = t('<p style=\"margin-bottom: 5px;\">The news group must be selected beforehand!</p>');
        $this->dlgToast2->ProgressBar = true;
        $this->dlgToast2->TimeOut = 10000;
        $this->dlgToast2->EscapeHtml = false;

        $this->dlgToast3 = new Q\Plugin\Toastr($this);
        $this->dlgToast3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast3->Message = t('<p style=\"margin-bottom: 5px;\">The news group cannot be the same as the target group!</p>');
        $this->dlgToast3->ProgressBar = true;
        $this->dlgToast3->TimeOut = 10000;
        $this->dlgToast3->EscapeHtml = false;

        $this->dlgToast4 = new Q\Plugin\Toastr($this);
        $this->dlgToast4->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast4->Message = t('<strong>Well done!</strong> The transfer of news to the new group was successful.');
        $this->dlgToast4->ProgressBar = true;

        $this->dlgToast5 = new Q\Plugin\Toastr($this);
        $this->dlgToast5->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast5->Message = t('The transfer of news to the new group failed.');
        $this->dlgToast5->ProgressBar = true;
    }

    /**
     * Creates and configures modal dialog boxes with specific settings and actions.
     *
     * @return void
     */
    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move the news from this news group to another news group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Note! If there are several news items in the selected group, they will be transferred to the new group!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'moveItems_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'transferCancelling_Click'));

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
     */
    public function elementsReset()
    {
        Application::executeJavaScript("
            $('.new-item-js').addClass('hidden');
            $('.move-items-js').addClass('hidden');
        ");
    }

    /**
     * Handles the click event for the 'Add News' button. It updates the UI elements to allow the user
     * to add a new news item, sets up the news groups selection, and manages the required interactions
     * for the 'lstGroupTitle' select control.
     *
     * @param ActionParams $params Parameters representing the action event.
     * @return void
     */
    protected function btnAddNews_Click(ActionParams $params)
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

        $countByIsReserved = NewsSettings::countByIsReserved(1);

        $this->txtTitle->Text = null;
        $this->btnAddNews->Enabled = false;

        $this->lstGroupTitle = new Q\Plugin\Select2($this);
        $this->lstGroupTitle->MinimumResultsForSearch = -1;
        $this->lstGroupTitle->Theme = 'web-vauu';
        $this->lstGroupTitle->Width = '100%';
        $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
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

        $this->lstGroupTitle->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstGroupTitle_Change'));
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
     * Handles the click event for the move button. This method updates the UI by toggling visibility of elements
     * and initializes a dropdown list for selecting news groups. It manages list selection based on conditions
     * and adjusts controls' enabled state accordingly.
     *
     * @param ActionParams $params The parameters passed during the action event for handling specific logic.
     * @return void
     */
    protected function btnMove_Click(ActionParams $params)
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

        $this->lstNewsLocked = new Q\Plugin\Select2($this);
        $this->lstNewsLocked->MinimumResultsForSearch = -1;
        $this->lstNewsLocked->Theme = 'web-vauu';
        $this->lstNewsLocked->Width = '100%';
        $this->lstNewsLocked->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
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

        $this->lstNewsLocked->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstNewsLocked_Change'));

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
     * If an item is selected, focus is shifted to the title text input.
     *
     * @param ActionParams $params The parameters associated with the change event.
     * @return void
     */
    protected function lstGroupTitle_Change(ActionParams $params)
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
     * @return void
     */
    protected function lstNewsLocked_Change(ActionParams $params)
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
     * @return void
     */
    protected function lstTargetGroup_Change(ActionParams $params)
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
     * @return void
     */
    public function transferCancelling_Click(ActionParams $params)
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

        $this->dtgNews->removeCssClass('disabled');
    }

    /**
     * Handles the click event for moving items. This function hides the dialog box,
     * performs the necessary transfer operations, resets the elements state, and
     * re-enables specific buttons.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function moveItems_Click(ActionParams $params)
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
    }

    /**
     * Handles the transfer of news operations from a locked group to a target group.
     * This involves updating the news group settings, and transferring associated news
     * and frontend links to the target group.
     *
     * @return void
     */
    private function newsTransferOperations()
    {
        $objLockedGroup = NewsSettings::loadById($this->lstNewsLocked->SelectedValue);
        $objTargetGroup = NewsSettings::loadById($this->lstTargetGroup->SelectedValue);

        $objNewsGroupArray = News::loadArrayByNewsGroupTitleId($this->lstNewsLocked->SelectedValue);

        $beforeCount = count(FrontendLinks::loadArrayByGroupedId($objLockedGroup->getNewsGroupId()));
        $afterCount = 0;

        $objNewsSettings = NewsSettings::loadById($objLockedGroup->getId());
        $objNewsSettings->setNewsLocked(0);
        $objNewsSettings->save();

        $objNewsSettings = NewsSettings::loadById($objTargetGroup->getId());
        $objNewsSettings->setNewsLocked(1);
        $objNewsSettings->save();

        foreach ($objNewsGroupArray as $objNewsGroup) {
            $objNews = News::loadById($objNewsGroup->getId());
            $objNews->setNewsGroupId($objTargetGroup->getNewsGroupId());
            $objNews->setNewsGroupTitleId($this->lstTargetGroup->SelectedValue);
            $objNews->setGroupTitle($objTargetGroup->getName());
            $objNews->updateNews($objNews->getTitle(), $objTargetGroup->getTitleSlug());
            $objNews->save();

            $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objNews->getId());

            $objFrontendLink->setFrontendTitleSlug($objTargetGroup->getTitleSlug() . '/' . Q\QString::sanitizeForUrl(trim($objNews->getTitle())));
            $objFrontendLink->setGroupedId($objTargetGroup->getNewsGroupId());
            $objFrontendLink->save();
        }

        $this->dtgNews->refresh(true);

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
    }

    /**
     * Handles the click event for the save button. Validates input fields and then proceeds to create and save
     * new News and FrontendLinks records based on selected and entered data.
     *
     * @param ActionParams $params Contains the parameters related to the action event, such as trigger information.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
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
            $objNews->setPostDate(Q\QDateTime::Now());
            $objNews->setTitle($this->txtTitle->Text);
            $objNews->setNewsGroupId($objNewsSettings->getNewsGroupId());
            $objNews->setNewsGroupTitleId($objNewsSettings->getId());
            $objNews->setGroupTitle($objNewsSettings->getName());
            $objNews->setStatus(2);
            $objNews->saveNews($this->txtTitle->Text, $objNewsSettings->getTitleSlug());
            $objNews->setAssignedByUser($this->objUser->Id);
            $objNews->setAuthor($objNews->getAssignedByUserObject());
            $objNews->save();

            $objFrontendLinks = new FrontendLinks();
            $objFrontendLinks->setLinkedId($objNews->getId());
            $objFrontendLinks->setGroupedId($objNewsSettings->getNewsGroupId());
            $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
            $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $objFrontendLinks->setTitle(trim($this->txtTitle->Text));
            $objFrontendLinks->setContentTypesManagamentId(4);
            $objFrontendLinks->setFrontendTitleSlug($objNews->getTitleSlug());
            $objFrontendLinks->setIsActivated(1);
            $objFrontendLinks->save();

            if ($objNewsSettings->getNewsLocked() == 0) {
                $objGroup = NewsSettings::loadById($objNewsSettings->getId());
                $objGroup->setNewsLocked(1);
                $objGroup->save();
            }

            $this->btnAddNews->Enabled = true;
            $this->btnMove->Enabled = true;
            $this->lstGroupTitle->SelectedValue = null;
            $this->txtTitle->Text = null;
            $this->elementsReset();

            Application::redirect('news_edit.php' . '?id=' . $objNews->getId() . '&group=' . $objNewsSettings->getNewsGroupId());
        }
    }

    /**
     * Handles the click event for the cancel button, performing UI updates and resetting form fields.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function btnCancel_Click(ActionParams $params)
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
        $this->txtTitle->Text = null;
        $this->lstGroupTitle->SelectedValue = null;
        $this->lstGroupTitle->refresh();

        $this->dtgNews->removeCssClass('disabled');
    }

    /**
     * Event handler for the Locked Cancel button click event.
     * Toggles the visibility of specified UI elements based on the count of SportsSettings.
     * Resets and refreshes list selections and enables specific buttons.
     *
     * @param ActionParams $params The parameters from the button click action.
     * @return void
     */
    protected function btnLockedCancel_Click(ActionParams $params)
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

        $this->dtgNews->removeCssClass('disabled');
    }

    /**
     * Handles the click event for the "Back" button.
     *
     * @param ActionParams $params The parameters associated with the button click event.
     * @return void
     */
    public function btnBack_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('menu_manager.php');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the `dtgNews` data grid by creating a new instance of `NewsTable`, setting up columns,
     * generating paginators, and enabling edit functionality. Configures row parameters callback,
     * default sort behavior, and page loading options through AJAX.
     * Customization of items per page is done based on user-assigned preferences.
     *
     * @return void
     */
    protected function dtgNews_Create()
    {
        $this->dtgNews = new NewsTable($this);
        $this->dtgNews_CreateColumns();
        $this->createPaginators();
        $this->dtgNews_MakeEditable();
        $this->dtgNews->RowParamsCallback = [$this, "dtgNews_GetRowParams"];
        $this->dtgNews->SortColumnIndex = 5;
        //$this->dtgNews->SortDirection = -1;
        $this->dtgNews->UseAjax = true;
        $this->dtgNews->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Creates columns for the dtgNews data grid.
     *
     * @return void
     */
    protected function dtgNews_CreateColumns()
    {
        $this->dtgNews->createColumns();
    }

    /**
     * Configures the datagrid to be editable by making rows clickable and
     * adding CSS classes for styling. Attaches an Ajax action to handle
     * row click events by invoking the dtgNewsRow_Click method.
     *
     * @return void
     */
    protected function dtgNews_MakeEditable()
    {
        $this->dtgNews->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgNewsRow_Click'));
        $this->dtgNews->addCssClass('clickable-rows');
        $this->dtgNews->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for a news row in a data grid.
     *
     * @param ActionParams $params The parameters passed from the action, including the ID of the news item.
     * @return void This method does not return any value but redirects the user to the news edit page.
     */
    protected function dtgNewsRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $intNewsId = intval($params->ActionParameter);
        $objNews = News::loadById($intNewsId);
        $intGroup = $objNews->getNewsGroupId();

        Application::redirect('news_edit.php' . '?id=' . $intNewsId . '&group=' . $intGroup);
    }

    /**
     * Retrieves parameters for a specific row in the news data grid.
     *
     * @param object $objRowObject The row object from which to extract the parameters.
     * @param int $intRowIndex The index of the row within the data grid.
     * @return array An associative array of parameters for the row, including a 'data-value' key with the row's primary key.
     */
    public function dtgNews_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures primary and alternate paginators for a news datagrid,
     * setting labels for navigation controls and specifying items per page.
     * Also invokes filter action methods associated with the datagrid.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgNews->Paginator = new Bs\Paginator($this);
        $this->dtgNews->Paginator->LabelForPrevious = t('Previous');
        $this->dtgNews->Paginator->LabelForNext = t('Next');

        $this->dtgNews->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgNews->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgNews->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgNews->ItemsPerPage = 10;

        $this->addFilterActions();
    }

    /**
     * Initializes and configures a Select2 dropdown component to manage items per page for a user.
     *
     * @return void
     */
    protected function createItemsPerPage()
    {
        $this->lstItemsPerPageByAssignedUserObject = new Q\Plugin\Select2($this);
        $this->lstItemsPerPageByAssignedUserObject->MinimumResultsForSearch = -1;
        $this->lstItemsPerPageByAssignedUserObject->Theme = 'web-vauu';
        $this->lstItemsPerPageByAssignedUserObject->Width = '100%';
        $this->lstItemsPerPageByAssignedUserObject->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->ItemsPerPageByAssignedUser;
        $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstItemsPerPageByAssignedUserObject_GetItems());
        $this->lstItemsPerPageByAssignedUserObject->AddAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
    }

    /**
     * Retrieves a list of items per page filtered by assigned user object.
     *
     * @return ListItem[] An array of ListItem objects representing the items per page
     *         associated with the assigned user object, with the relevant item marked as selected.
     */
    public function lstItemsPerPageByAssignedUserObject_GetItems() {
        $a = array();
        $objCondition = $this->objItemsPerPageByAssignedUserObjectCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objItemsPerPageByAssignedUserObjectCursor = ItemsPerPage::queryCursor($objCondition, $this->objItemsPerPageByAssignedUserObjectClauses);

        // Iterate through the Cursor
        while ($objItemsPerPageByAssignedUserObject = ItemsPerPage::instantiateCursor($objItemsPerPageByAssignedUserObjectCursor)) {
            $objListItem = new ListItem($objItemsPerPageByAssignedUserObject->__toString(), $objItemsPerPageByAssignedUserObject->Id);
            if (($this->objUser->ItemsPerPageByAssignedUserObject) && ($this->objUser->ItemsPerPageByAssignedUserObject->Id == $objItemsPerPageByAssignedUserObject->Id))
                $objListItem->Selected = true;
            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Updates the number of items displayed per page in the news data grid based on the selected value from the dropdown list.
     *
     * @param ActionParams $params The parameters associated with the change action.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgNews->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgNews->refresh();
    }

    /**
     * Creates a search filter text box with specific attributes and styles, and initializes filter actions.
     *
     * @return void
     */
    protected function createFilter()
    {
        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();
    }

    /**
     * Adds actions to the filter input component. Configures the filter input to
     * trigger an Ajax control action upon user input and specifically on the Enter key event.
     *
     * @return void
     */
    protected function addFilterActions()
    {
        $this->txtFilter->addAction(new Q\Event\Input(300), new Q\Action\AjaxControl($this, 'filterChanged'));
        $this->txtFilter->addActionArray(new Q\Event\EnterKey(),
            [
                new Q\Action\AjaxControl($this, 'FilterChanged'),
                new Q\Action\Terminate()
            ]
        );
    }

    /**
     * Handles the event triggered when a filter condition changes.
     *
     * @return void
     */
    protected function filterChanged()
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
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgNews->bindData($objCondition);
    }

    /**
     * Generates a query condition based on the current text filter input.
     *
     * If the filter input is empty or null, a condition that matches all entries is returned.
     * Otherwise, returns a compound condition that performs a case-insensitive search for the filter input
     * within the Picture, GroupTitle, Title, Category, and Author fields of the News entity.
     *
     * @return mixed The generated query condition, either matching all entries or matching specified fields against the filter input.
     */
    protected function getCondition()
    {
        $strSearchValue = $this->txtFilter->Text;

        if ($strSearchValue === null) {
            $strSearchValue = '';
        }

        $strSearchValue = trim($strSearchValue);

        if (is_null($strSearchValue) || $strSearchValue === '') {
            return Q\Query\QQ::all();
        } else {
            return Q\Query\QQ::orCondition(
                Q\Query\QQ::like(QQN::News()->GroupTitle, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::News()->Title, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::News()->Category, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::News()->Author, "%" . $strSearchValue . "%")
            );
        }
    }
}