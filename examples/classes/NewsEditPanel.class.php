<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\QString;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class NewsEditPanel extends Q\Control\Panel
{
    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;
    public $dlgModal6;

    protected $dlgToast1;
    protected $dlgToast2;

    public $lblExistingMenuText;
    public $txtExistingMenuText;

    public $lblMenuText;
    public $txtMenuText;

    public $lblGroupTitle;
    public $lstGroupTitle;

    public $lblContentType;
    public $lstContentTypes;

    public $lblStatus;
    public $lstStatus;

    public $lblTitleSlug;
    public $txtTitleSlug;

    public $btnGoToNewsSettings;
    public $btnGoToList;
    public $btnGoToMenu;

    protected $intId;
    protected $objMenu;
    protected $objMenuContent;
    protected $objNewsSettings;

    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'NewsEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        if (!empty($_SESSION['news_edit_group'])) {
            unset($_SESSION['news_edit_group']);
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenu = Menu::load($this->intId);
        $this->objMenuContent = MenuContent::load($this->intId);
        $this->objNewsSettings = NewsSettings::loadByIdFromNewsSettings($this->intId);

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes various input controls used for configuring menu content.
     *
     * This method sets up labels, text boxes, and select controls for existing and new menu texts,
     * content types, and statuses. It adjusts properties such as text, CSS styles, validation requirements,
     * and event actions based on the current state of menu content and settings.
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
        $this->txtMenuText->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtMenuText->addWrapperCssClass('center-button');
        $this->txtMenuText->MaxLength = MenuContent::MenuTextMaxLength;
        $this->txtMenuText->Required = true;

        if ($this->objNewsSettings->getIsReserved() == 1) {
            $this->txtMenuText->Enabled = false;
        }

        $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
        $this->lblGroupTitle->Text = t('Editing a news group title');
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
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
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
        $this->lstStatus->AddAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'lstStatus_Click'));

        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->lstStatus->Enabled = false;
        }
    }

    /**
     * Initializes and configures button components for navigation within the application.
     *
     * This method creates three buttons: 'Back to menu manager', 'Go to the news manager',
     * and 'Go to news settings manager'. Each button is customized with CSS classes
     * and attached with click events for AJAX control actions. The visibility of
     * the 'Go to the news manager' button is toggled based on the menu content's content type.
     *
     * @return void
     */
    public function CreateButtons()
    {
        $this->btnGoToMenu = new Bs\Button($this);
        $this->btnGoToMenu->Text = t('Back to menu manager');
        $this->btnGoToMenu->CssClass = 'btn btn-default';
        $this->btnGoToMenu->addWrapperCssClass('center-button');
        $this->btnGoToMenu->CausesValidation = false;
        $this->btnGoToMenu->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToMenu_Click'));

        $this->btnGoToList = new Bs\Button($this);
        $this->btnGoToList->Text = t('Go to the news manager');
        $this->btnGoToList->CssClass = 'btn btn-default';
        $this->btnGoToList->addWrapperCssClass('center-button');
        $this->btnGoToList->CausesValidation = false;
        $this->btnGoToList->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToList_Click'));

        if ($this->objMenuContent->getContentType()) {
            $this->btnGoToList->Display = true;
        } else {
            $this->btnGoToList->Display = false;
        }

        $this->btnGoToNewsSettings = new Bs\Button($this);
        $this->btnGoToNewsSettings->Text = t('Go to news settings manager');
        $this->btnGoToNewsSettings->addWrapperCssClass('center-button');
        $this->btnGoToNewsSettings->CausesValidation = false;
        $this->btnGoToNewsSettings->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnGoToNewsSettings_Click'));
    }

    /**
     * Creates two Toastr notifications with predefined configurations.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToast1 = new Q\Plugin\Toastr($this);
        $this->dlgToast1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast1->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
        $this->dlgToast1->ProgressBar = true;

        $this->dlgToast2 = new Q\Plugin\Toastr($this);
        $this->dlgToast2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast2->Message = t('The menu title exist!');
        $this->dlgToast2->ProgressBar = true;
    }

    /**
     * Initializes and configures a set of modal dialog boxes for various user notifications and confirmations.
     *
     * @return void
     */
    public function createModals()
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
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The news group status of this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this news group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this group public again later!</p>');
        $this->dlgModal3->Title = t('Question');
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addCloseButton(t("I'll cancel"));
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'statusItem_Click'));
        $this->dlgModal3->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hideCancel_Click'));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Success");
        $this->dlgModal4->HeaderClasses = 'btn-success';
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news group is now hidden!</p>');
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news group has now been made public!</p>');
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
     * Retrieves a list of group titles formatted as ListItems. This method considers various conditions,
     * such as selection state and reserved status, to build an appropriate set of list items.
     *
     * @return ListItem[] An array of ListItem objects representing the group titles. Each ListItem includes
     *                    the display text and ID of the group title, with selection and disabled status
     *                    appropriately set based on the group's properties and the current menu content.
     */
    public function lstGroupTitle_GetItems() {
        $a = array();
        $objCondition = $this->objGroupTitleCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objGroupTitleCursor = NewsSettings::queryCursor($objCondition, $this->objGroupTitleClauses);

        // Iterate through the Cursor
        while ($objGroupTitle = NewsSettings::instantiateCursor($objGroupTitleCursor)) {
            $objListItem = new ListItem($objGroupTitle->__toString(), $objGroupTitle->Id);
            if (($this->objMenuContent->GroupTitle) && ($this->objMenuContent->GroupTitle->Id == $objGroupTitle->Id))
                $objListItem->Selected = true;
            if ($objGroupTitle->IsReserved == 1) {
                $objListItem->Disabled = true;
            }
            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Retrieves an array of content type names with certain entries removed.
     *
     * This method first obtains an array of content type names from the ContentType class,
     * then removes the entry at index 1. It further processes the available content types
     * by checking the 'IsEnabled' status from a supplementary array, and removes any
     * content type whose 'IsEnabled' value is 0.
     *
     * @return array The modified array of content type names with specific entries removed.
     */
    public function lstContentTypeObject_GetItems()
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
     * Handles the click event for the status list. Based on the status and other conditions,
     * it may display different dialog boxes and update certain menu and settings objects.
     *
     * @param ActionParams $params The parameters associated with the action.
     * @return void
     */
    public function lstStatus_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

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

            $this->objNewsSettings->setStatus(1);
            $this->objNewsSettings->save();
        }
    }

    /**
     * Updates the input fields based on the current status of the menu content.
     *
     * This method sets the selected value of the status list to reflect whether
     * the menu content is enabled or not by obtaining this information from the
     * associated menu content object.
     *
     * @return void
     */
    private function updateInputFields()
    {
        $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
    }

    /**
     * Handles the click event for a status item, updating relevant settings and dialog boxes.
     *
     * @param ActionParams $params The parameters associated with the action triggering this method.
     * @return void
     */
    public function statusItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->lstStatus->SelectedValue = 2;

        $this->objMenuContent->setIsEnabled(2);
        $this->objMenuContent->save();

        $this->objnewsSettings->setStatus(2);
        $this->objNewsSettings->save();

        $this->dlgModal3->hideDialogBox();
        $this->dlgModal4->showDialogBox();
    }

    /**
     * Handles the click event for the hide cancel button.
     *
     * @param ActionParams $params The parameters associated with the action.
     * @return void
     */
    public function hideCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the "Go To News Settings" button.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnGoToNewsSettings_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $_SESSION['news_edit_group'] = $this->intId;
        Application::redirect('settings_manager.php#newsSettings_tab');
    }

    /**
     * Handles the click event for the "Go to List" button, redirecting the user to the news list page.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     * @return void
     */
    public function btnGoToList_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('news_list.php');
    }

    /**
     * Handles the click event for the "Go To Menu" button, redirecting the application to the menu management page.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnGoToMenu_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('menu_manager.php');
    }
}