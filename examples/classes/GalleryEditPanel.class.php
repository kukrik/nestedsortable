<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Folder;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\QString;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class GalleryEditPanel extends Q\Control\Panel
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
    public $txtRename;

    public $lblGroupTitle;
    public $lstGroupTitle;

    public $lblGallerySettings;

    public $lblContentType;
    public $lstContentTypes;

    public $lblStatus;
    public $lstStatus;

    public $lblTitleSlug;
    public $txtTitleSlug;

    public $btnGoToGallerySettings;
    public $btnGoToList;
    public $btnSave;
    public $btnGoToMenu;

    protected $intId;
    protected $objMenu;
    protected $objMenuContent;
    protected $objGallerySettings;

    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $objGallerySettingsCondition;
    protected $objGallerySettingsClauses;

    protected $strTemplate = 'GalleryEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        if (!empty($_SESSION['gallery_group-edit'])) {
            unset($_SESSION['gallery_group-edit']);
        }

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initialize and configure various QControl elements for menu management,
     * setting up text labels, input fields, dropdowns, and radio buttons.
     * This method uses information from the current application context and
     * database objects to configure controls, apply styles, and set their
     * respective properties and data bindings.
     *
     * @return void
     */
    public function createInputs()
    {
        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenu = Menu::load($this->intId);
        $this->objMenuContent = MenuContent::load($this->intId);
        $this->objGallerySettings = GallerySettings::loadByIdFromGallerySettings($this->intId);

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

        if ($this->objMenuContent->getMenuText()) {
            $this->txtMenuText->Enabled = false;
        }

        $this->lblGallerySettings = new Q\Plugin\Control\Label($this);
        $this->lblGallerySettings->Text = t('Editing a gallery group title');
        $this->lblGallerySettings->addCssClass('col-md-3');
        $this->lblGallerySettings->setCssStyle('font-weight', 400);

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
     * Creates and configures a set of buttons for navigating between different managers.
     *
     * This method initializes three buttons: one for returning to the menu manager,
     * another for navigating to the albums manager, and the last one for accessing
     * the gallery settings manager. Each button is styled and assigned specific actions
     * to handle user interactions.
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
        $this->btnGoToList->Text = t('Go to albums manager');
        $this->btnGoToList->CssClass = 'btn btn-default';
        $this->btnGoToList->addWrapperCssClass('center-button');
        $this->btnGoToList->CausesValidation = false;
        $this->btnGoToList->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToList_Click'));

        if ($this->objMenuContent->getContentType()) {
            $this->btnGoToList->Display = true;
        } else {
            $this->btnGoToList->Display = false;
        }

        $this->btnGoToGallerySettings = new Bs\Button($this);
        $this->btnGoToGallerySettings->Text = t('Go to gallery settings manager');
        $this->btnGoToGallerySettings->addWrapperCssClass('center-button');
        $this->btnGoToGallerySettings->CausesValidation = false;
        $this->btnGoToGallerySettings->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnGoToGallerySettings_Click'));
    }

    /**
     * Creates and configures toast notifications for user feedback.
     *
     * This method initializes two toast notifications using the Toastr plugin.
     * The first toast indicates a successful operation, such as saving or modifying a post,
     * while the second toast alerts the user to an error, such as a duplicate menu title.
     * Both notifications are configured with specific alert types, positions, messages,
     * and progress bars to enhance user experience.
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
     * Initializes and creates multiple modal dialogs for various status notifications.
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
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The gallery group status of this menu item cannot be changed!</p>
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
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addCloseButton(t("I'll cancel"));
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'statusItem_Click'));
        $this->dlgModal3->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hideCancel_Click'));

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
     * Retrieves a list of gallery settings as ListItem objects, applying specified conditions and clauses.
     *
     * @return ListItem[] An array of ListItem objects representing the gallery settings,
     *                    with appropriate selection and disability states based on the gallery settings properties.
     */
    public function lstGallerySettings_GetItems() {
        $a = array();
        $objCondition = $this->objGallerySettingsCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objGallerySettingsCursor = GallerySettings::queryCursor($objCondition, $this->objGallerySettingsClauses);

        // Iterate through the Cursor
        while ($objGallerySettings = GallerySettings::instantiateCursor($objGallerySettingsCursor)) {
            $objListItem = new ListItem($objGallerySettings->__toString(), $objGallerySettings->Id);
            if (($this->objMenuContent->GroupTitle) && ($this->objMenuContent->GroupTitle->Id == $objGallerySettings->Id))
                $objListItem->Selected = true;
            if ($objGallerySettings->IsReserved == 1) {
                $objListItem->Disabled = true;
            }
            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Retrieves an array of content type names, excluding items based on specific conditions.
     * This function filters out the second item (index 1) from the content type names array.
     * Additionally, it removes items where the 'IsEnabled' property is set to 0.
     *
     * @return array An array of filtered content type names where the second item and any disabled items have been removed.
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
     * Handles click events on the status list and performs actions based on the menu state and selected status.
     * This method checks several conditions related to menu hierarchy, page lock status, and the selected status value,
     * and triggers appropriate dialog boxes and updates data accordingly.
     *
     * @param ActionParams $params Parameters associated with the click action event.
     * @return void This method does not return any value.
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

            $this->objGallerySettings->setStatus(1);
            $this->objGallerySettings->save();
        }
    }

    /**
     * Updates input fields based on the current state of the menu content object.
     * Specifically, sets the selected value of the status list to match the
     * enabled status of the menu content.
     *
     * @return void
     */
    private function updateInputFields()
    {
        $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
    }

    /**
     * Handles the click event for a status item, updating specific components and managing modal dialogs.
     * This function sets the selected value of the status list and updates the status of menu content
     * and gallery settings to a specific value. It then hides one dialog box and shows another.
     *
     * @param ActionParams $params Parameters passed to the method during the action event.
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

        $this->objGallerySettings->setStatus(2);
        $this->objGallerySettings->save();

        $this->dlgModal3->hideDialogBox();
        $this->dlgModal4->showDialogBox();
    }

    /**
     * Handles the click event for hiding the cancel action. This method sets the selected value
     * of the status list to the enabled status of the menu content.
     *
     * @param ActionParams $params The parameters associated with the action event.
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
     * Handles the click event for the "Go To Gallery Settings" button.
     * This method sets a session variable to the current gallery group ID and redirects the user
     * to the settings manager page with a specific tab highlighted.
     *
     * @param ActionParams $params The parameters associated with the action event.
     *
     * @return void
     */
    public function btnGoToGallerySettings_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $_SESSION['gallery_group_edit'] = $this->intId;
        Application::redirect('settings_manager.php#galleriesSettings_tab');
    }

    /**
     * Handles the click event for the 'Go To List' button. This function will clear the 'gallery_group_edit'
     * session variable if it is set, and then redirect the user to the gallery list page.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     * @return void This method does not return a value.
     */
    public function btnGoToList_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['gallery_group_edit'])) {
            unset($_SESSION['gallery_group_edit']);
        }

        Application::redirect('gallery_list.php');
    }

    /**
     * Handles the click event for the 'Go To Menu' button. This method clears a specific session variable
     * associated with gallery group editing and redirects the user to the menu manager page.
     *
     * @param ActionParams $params The parameters associated with the action event triggered by the button click.
     * @return void This method does not return any value.
     */
    public function btnGoToMenu_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['gallery_group_edit'])) {
            unset($_SESSION['gallery_group_edit']);
        }

        Application::redirect('menu_manager.php');
    }
}