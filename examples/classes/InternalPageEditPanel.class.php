<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Control\ListItem;
use QCubed\QString;
use QCubed\Query\QQ;

class InternalPageEditPanel extends Q\Control\Panel
{
    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;
    public $dlgModal6;
    public $dlgModal7;

    protected $dlgToastr1;

    public $lblExistingMenuText;
    public $txtExistingMenuText;

    public $lblMenuText;
    public $txtMenuText;

    public $lblContentType;
    public $lstContentTypes;

    public $lblSelectedPage;
    public $lstSelectedPage;
    public $lstSelectedPageId;

    public $lblStatus;
    public $lstStatus;

    public $lblTitleSlug;
    public $txtTitleSlug;

    public $btnBack;

    protected $intId;
    protected $objMenuContent;
    protected $objMenu;
    protected $objArticle;
    protected $strRedirectUrl;
    protected $strDoubleRoutingInfo;

    protected $objSelectedPageCondition;
    protected $objSelectedPageClauses;

    protected $strTemplate = 'InternalPageEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenu = Menu::load($this->intId);
        $this->objMenuContent = MenuContent::load($this->intId);

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and configures user interface components for form inputs related to menu content.
     *
     * This method initializes and sets up various form controls, such as labels, text boxes, and select lists,
     * which are used to display and modify properties of a menu item, including menu text, content type, selected page,
     * status, and redirection information. Each control is configured with attributes and styles appropriate for the form.
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
        $this->lstContentTypes->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems(), true);
        $this->lstContentTypes->SelectedValue = $this->objMenuContent->ContentType;
        $this->lstContentTypes->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstClassNames_Change'));

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
        $this->lstSelectedPage->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstSelectedPage->addItem(t('- Select one internal page -'), null, true);
        $this->lstSelectedPage->addItems($this->lstSelectedPage_GetItems(),null, null);
        $this->lstSelectedPage->SelectedValue = $this->objMenuContent->SelectedPageId;
        $this->lstSelectedPage->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstSelectedPage_Change'));

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->addCssClass('col-md-3');
        $this->lblStatus->Required = true;

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
        $this->lstStatus->SelectedValue = $this->objMenuContent->IsEnabled;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';
        $this->lstStatus->AddAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstStatus_Click'));

        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->lstStatus->Enabled = false;
        }

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
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        } else {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }
        $this->strDoubleRoutingInfo = new Q\Control\Panel($this);
        $this->strDoubleRoutingInfo->TagName = 'span';

        if ($this->objMenuContent->getSelectedPageId()) {
            $strDoubleRouting = MenuContent::load($this->objMenuContent->getSelectedPageId());
            if ($strDoubleRouting->getId()) {
                if ($strDoubleRouting->getContentType() == 7 && $strDoubleRouting->getIsRedirect() == 2) {
                    $this->strDoubleRoutingInfo->Text = '<span style="color: #ff0000;"><strong>' . $this->objMenuContent->getSelectedPage() . '</strong>' . ' | ' . t('Warning, double redirection: ') . '<span style="color: #2593a1;">' . $strDoubleRouting->getSelectedPage() . '</span></span>';
                } else {
                    $this->strDoubleRoutingInfo->Text =  '<span style="color: #ff0000;">' . t('Redirected to this page: ') . '</span><span style="color: #2593a1;">' . $this->objMenuContent->getSelectedPage() . '</span>';
                }
            }
        }
    }

    /**
     * Initializes and configures the buttons used within the control.
     * Specifically, it creates a 'Back' button with particular text, styling,
     * and actions associated with it. The button does not trigger form validation
     * and includes an AJAX action on click.
     *
     * @return void
     */
    public function CreateButtons()
    {
        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back to menu manager');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->addWrapperCssClass('center-button');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnBack_Click'));
    }

    /**
     * Initializes multiple Toastr notifications with varying alert types and messages.
     *
     * This method configures four Toastr notifications with predefined alert types,
     * positions, messages, and progress bar visibility. The alerts include
     * success and error messages based on various conditions.
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
    }

    /**
     * Initializes and configures multiple modal dialog instances with specific texts, titles, and actions.
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
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the internal page link for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this internal page link?</p>
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
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This internal page link is now hidden!</p>');
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This internal page link has now been made public!</p>');
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Please select a page to redirect!</p>');
        $this->dlgModal6->Title = t("Tip");
        $this->dlgModal6->HeaderClasses = 'btn-darkblue';
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

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Retrieves an array of content types with specific conditions applied.
     * The array of content types is filtered to exclude a specific item
     * and those that are not enabled.
     *
     * @return array An associative array of content types with filtered conditions.
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
     * Retrieves a list of menu items and converts them into list items for selection.
     * The method processes all menu items, marks certain items as selected if they match
     * the selected page IDs, and disables items not enabled.
     *
     * @return ListItem[] An array of ListItem objects representing the menu items,
     *                    where certain items are marked as selected or disabled based
     *                    on their properties.
     */
    public function lstSelectedPage_GetItems()
    {
        $a = array();
        $selectedPages = array();

        $objMenuArray = Menu::loadAll(QQ::Clause(QQ::OrderBy(QQN::menu()->Left), QQ::expand(QQN::menu()->MenuContent)));

        foreach ($objMenuArray as $objMenu) {
            if ($objMenu->MenuContent->SelectedPageId) {
                $selectedPages[] = $objMenu->MenuContent->SelectedPageId;
            }
        }

        foreach ($objMenuArray as $objMenu) {
            $objListItem = new ListItem($this->printDepth($objMenu->MenuContent->MenuText, $objMenu->ParentId, $objMenu->Depth), $objMenu->MenuContent->Id);
            if (in_array($objMenu->MenuContent->Id, $selectedPages)) {
                $objListItem->Selected = true;
            }
            if ($objMenu->MenuContent->IsEnabled == 2) {
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
     */
    public function lstSelectedPage_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $sanitizedSlug = '/' . QString::sanitizeForUrl($this->objMenuContent->getMenuText());

        if ($this->lstSelectedPage->SelectedValue) {
            $this->objMenuContent->setHomelyUrl(1);
            $this->objMenuContent->setIsRedirect(2);
            $this->objMenuContent->setSelectedPageId($this->lstSelectedPage->SelectedValue);
            $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);

            $this->dlgToastr1->notify();
        } else {
            $this->objMenuContent->setIsEnabled(2);
            $this->objMenuContent->setRedirectUrl(null);
            $this->objMenuContent->setInternalUrl(null);

            $this->lstStatus->SelectedValue = 2;
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->strDoubleRoutingInfo->Text = '';

            $this->lstStatus->refresh();
            $this->txtTitleSlug->refresh();
            $this->strDoubleRoutingInfo->refresh();

            $this->dlgModal4->showDialogBox();
        }

        if ($this->objMenuContent->getSelectedPageId()) {
            $objRedirectUrl = MenuContent::load($this->objMenuContent->SelectedPageId);
            if ($objRedirectUrl->getId() == $this->objMenuContent->getSelectedPageId() && $objRedirectUrl->getIsRedirect() == 1) {
                $this->objMenuContent->setRedirectUrl($objRedirectUrl->getRedirectUrl());
                $this->objMenuContent->setInternalUrl($objRedirectUrl->getRedirectUrl());
            } else {
                $this->objMenuContent->setRedirectUrl($objRedirectUrl->getRedirectUrl());
                $this->objMenuContent->setInternalUrl($objRedirectUrl->getRedirectUrl());
                $this->objMenuContent->setIsRedirect(2);
            }
        }
        $this->objMenuContent->save();

        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

        if (!$objFrontendLink) {
            $objFrontendLinks = new FrontendLinks();
            $objFrontendLinks->setLinkedId($this->objMenuContent->Id);
            $objFrontendLinks->setContentTypesManagamentId(7);
            $objFrontendLinks->setTitle(trim($this->txtMenuText->Text));
            $objFrontendLinks->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
            $objFrontendLinks->setIsActivated(1);
            $objFrontendLinks->save();
        } else {
            $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
            $objFrontendLinks->setContentTypesManagamentId(7);

            if ($this->objMenuContent->getRedirectUrl() === null) {
                $objFrontendLinks->setFrontendTitleSlug(null);
            } else {
                $objFrontendLinks->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
            }

            $objFrontendLinks->save();
        }

        $objSelectedPageLockedArray = MenuContent::loadAll();
        $objPageArray = [];
        $objIdArray = [];

        foreach ($objSelectedPageLockedArray as $objSelectedPageLocked) {
            $objPageArray[] = $objSelectedPageLocked->getSelectedPageId();
        }
        foreach ($objSelectedPageLockedArray as $objSelectedPageLocked) {
            $objIdArray[] = $objSelectedPageLocked->getId();
        }

        $objTrueArray = array_intersect($objPageArray, $objIdArray);
        $objFalseArray = array_diff($objIdArray, $objPageArray);

        foreach ($objTrueArray as $objResult) {
            $objResultSave = MenuContent::load($objResult);
            $objResultSave->setSelectedPageLocked(1);
            $objResultSave->save();
        }
        foreach ($objFalseArray as $objResult) {
            $objResultSave = MenuContent::load($objResult);
            $objResultSave->setSelectedPageLocked(0);
            $objResultSave->save();
        }

        $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();

        if ($this->objMenuContent->getRedirectUrl()) {
            if ($this->objMenuContent->getIsRedirect() == null || $this->objMenuContent->getIsRedirect() == 2) {
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objMenuContent->getRedirectUrl();
            } else {
                $url = $this->objMenuContent->getRedirectUrl();
            }

            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        } else {
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        if ($this->objMenuContent->getSelectedPageId()) {
            $strDoubleRouting = MenuContent::load($this->objMenuContent->getSelectedPageId());
            if ($strDoubleRouting->getId()) {
                if ($strDoubleRouting->getContentType() == 7 && $strDoubleRouting->getIsRedirect() == 2) {
                    $this->strDoubleRoutingInfo->Text = '<span style="color: #ff0000;"><strong>' . $this->objMenuContent->getSelectedPage() . '</strong>' . ' | ' . t('Warning, double redirection: ') . '<span style="color: #2593a1;">' . $strDoubleRouting->getSelectedPage() . '</span></span>';
                } else {
                    $this->strDoubleRoutingInfo->Text = '<span style="color: #ff0000;">' . t('Redirected to this page: ') . '</span><span style="color: #2593a1;">' . $this->objMenuContent->getSelectedPage() . '</span>';
                }
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the status list control.
     *
     * @param ActionParams $params The parameters associated with the click action.
     * @return void This method does not return a value.
     */
    public function lstStatus_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!$this->lstSelectedPage->SelectedValue) {
            $this->dlgModal6->showDialogBox();
            $this->updateInputFields();
        } else if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
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
     * Updates the selected value of the status list control based on the
     * enabled status of the associated menu content object.
     *
     * @return void
     */
    public function updateInputFields()
    {
        $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
    }

    /**
     * Handles the click event for a status item. It sets the selected status value,
     * updates the menu content's enabled state, and switches the visibility of specific dialog boxes.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function statusItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->lstStatus->SelectedValue = 2;

        $this->objMenuContent->setIsEnabled(2);
        $this->objMenuContent->save();

        $this->dlgModal3->hideDialogBox();
        $this->dlgModal4->showDialogBox();
    }

    /**
     * Handles the click event for hiding the cancel action.
     * This method updates the selected value of the status list to reflect
     * the 'IsEnabled' status of the associated menu content.
     *
     * @param ActionParams $params The parameters associated with the click action.
     * @return void This method does not return a value.
     */
    public function hideCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
    }

    /**
     * Handles the click event for the menu cancel button.
     * Redirects the user to the list page.
     *
     * @param ActionParams $params Parameters associated with the button click action.
     * @return void
     */
    public function btnBack_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->redirectToListPage();
    }

    /**
     * Redirects the user to the list page for managing menus.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        Application::redirect('menu_manager.php');
    }

    /**
     * Generates a string representation of a name with a specified depth.
     *
     * @param string $name The name to be printed.
     * @param mixed $parent The parent of the name. If not null, it signifies a nested structure.
     * @param int $depth The depth level indicating how much indentation to apply to the name.
     * @return string The formatted string with appropriate indentation.
     */
    protected function printDepth($name, $parent, $depth)
    {
        $spacer = str_repeat('&nbsp;', 5); // Adjust the number as needed for your indentation.

        if ($parent !== null) {
            $strHtml = str_repeat(html_entity_decode($spacer), $depth) . ' ' . $name;
        } else {
            $strHtml = $name;
        }

        return $strHtml;
    }
}