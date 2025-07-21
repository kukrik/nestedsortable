<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Query\QQ;
use QCubed\Control\ListItem;

class HomeEditPanel extends Q\Control\Panel
{
    public $dlgModal1;

    protected $dlgToastr1;
    protected $dlgToastr2;
    protected $dlgToastr3;
    protected $dlgToastr4;

    public $lblExistingMenuText;
    public $txtExistingMenuText;

    public $lblMenuText;
    public $txtMenuText;

    public $lblTitleSlug;
    public $txtTitleSlug;

    public $btnSave;
    public $btnSaving;
    public $btnCancel;

    protected $intId;
    protected $objMenuContent;
    protected $objFrontendLinks;

    protected $strTemplate = 'HomePageEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }


        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenuContent = MenuContent::load($this->intId);
        $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

        $this->createInputs();
        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures input controls for menu management.
     *
     * @return void
     */
    public function createInputs()
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
        $this->txtMenuText->MaxLength = MenuContent::MenuTextMaxLength;
        $this->txtMenuText->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtMenuText->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtMenuText->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtMenuText->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtMenuText->setHtmlAttribute('required', 'required');

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX;
        $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
        $this->txtTitleSlug->HtmlEntities = false;
        $this->txtTitleSlug->setCssStyle('font-weight', 400);
    }

    /**
     * Creates and configures the buttons used for saving, updating, and canceling actions.
     *
     * @return void
     */
    public function CreateButtons()
    {
        $this->btnSave = new Bs\Button($this);
        if (mb_strlen($this->objMenuContent->MenuText) > 0) {
            $this->btnSave->Text = t('Update');
        } else {
            $this->btnSave->Text = t('Save');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));

        $this->btnSaving = new Bs\Button($this);
        if (mb_strlen($this->objMenuContent->MenuText) > 0) {
            $this->btnSaving->Text = t('Update and close');
        } else {
            $this->btnSaving->Text = t('Save and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSaveClose_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
    }

    /**
     * Creates modal dialogs to handle specific user-related actions or warnings.
     *
     * This method initializes and configures modal dialogs used for displaying critical
     * messages or warnings. In this case, it creates a modal to notify the user about
     * an invalid CSRF token, including a warning title, styled header, explanatory text,
     * and a close button.
     *
     * @return void This method does not return any value.
     */
    public function createModals()
    {
        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal1->Title = t("Warning");
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addCloseButton(t("I understand"));
    }

    /**
     * Initializes multiple Toastr notifications with predefined configurations for different scenarios.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('The menu title must exist!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('The title of this menu item already exists in the database, please choose another title!');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('A template must be selected');
        $this->dlgToastr4->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the save action for the menu button click event. This method
     * will check if the menu text is set and if it doesn't already exist,
     * save the new menu content and update frontend links. It also provides
     * feedback notifications based on various conditions.
     *
     * @param ActionParams $params The parameters related to the action triggered by the button click.
     * @return void
     */
    public function btnMenuSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtMenuText->Text && !MenuContent::titleExists(trim($this->txtMenuText->Text))) {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setHomelyUrl(1);
            $this->objMenuContent->save();

            $this->objFrontendLinks->setLinkedId($this->intId);
            $this->objFrontendLinks->setContentTypesManagamentId(1);
            $this->objFrontendLinks->setTitle($this->txtMenuText->Text);
            $this->objFrontendLinks->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
            $this->objFrontendLinks->save();

            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();

            $this->dlgToastr1->notify();
        } else if (!$this->txtMenuText->Text) {
            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtMenuText->Text = null;
            $this->txtMenuText->focus();
            $this->dlgToastr2->notify();
        } else {
            $this->txtMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtMenuText->focus();
            $this->dlgToastr3->notify();
        }
    }

    /**
     * Handles the save and close action for menu items. It checks if the menu text is valid and unique,
     * saves the menu content, updates frontend link associations, and redirects to the list page.
     * Notifies the user if the menu text is missing or already exists.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtMenuText->Text && !MenuContent::titleExists(trim($this->txtMenuText->Text))) {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setHomelyUrl(1);
            $this->objMenuContent->save();

            $this->objFrontendLinks->setLinkedId($this->intId);
            $this->objFrontendLinks->setContentTypesManagamentId(1);
            $this->objFrontendLinks->setTitle($this->txtMenuText->Text);
            $this->objFrontendLinks->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
            $this->objFrontendLinks->save();

            $this->redirectToListPage();
        } else if (!$this->txtMenuText->Text) {
            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtMenuText->Text = null;
            $this->txtMenuText->focus();
            $this->dlgToastr2->notify();
        } else {
            $this->txtMenuText->Text = null;
            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtMenuText->focus();
            $this->dlgToastr3->notify();
        }
    }

    /**
     * Handles the click event for the menu cancel button.
     *
     * This method redirects the user to the list page when the cancel button is clicked.
     *
     * @param ActionParams $params The parameters associated with the cancel button click event.
     * @return void
     */
    public function btnMenuCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->redirectToListPage();
    }

    /**
     * Redirects the application to the list page for managing menus.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('menu_manager.php');
    }
}