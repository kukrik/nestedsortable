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

class StatisticsEditPanel extends Q\Control\Panel
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

    public $btnGoToStatistics;
    public $btnGoToList;
    public $btnGoToMenu;

    protected $lblInfo;

    protected $intId;
    protected $objMenu;
    protected $objMenuContent;
    protected $objStatisticsSettings;
    protected $intLoggedUserId;

    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $objStatisticsTypeClauses;
    protected $objStatisticsTypeCondition;

    const MAX_ALLOWED_TYPES = 3;

    protected $strTemplate = 'StatisticsEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
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

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();

        $this->checkStatisticsTypes();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the input controls for menu content management.
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
        $this->btnGoToList->Text = t('Go to the statistics manager');
        $this->btnGoToList->CssClass = 'btn btn-default';
        $this->btnGoToList->addWrapperCssClass('center-button');
        $this->btnGoToList->CausesValidation = false;
        $this->btnGoToList->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToList_Click'));

        $this->btnGoToStatistics = new Bs\Button($this);
        $this->btnGoToStatistics->Text = t('Go to statistics settings manager');
        $this->btnGoToStatistics->addWrapperCssClass('center-button');
        $this->btnGoToStatistics->CausesValidation = false;
        $this->btnGoToStatistics->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnGoToStatistics_Click'));
    }

    /**
     * Creates and configures Toastr notifications for user feedback.
     *
     * This method initializes success and error Toastr notifications with specified
     * alert types, positions, messages, and progress bars.
     * The success notification indicates the completion of a post save or modification,
     * whereas the error notification alerts about a duplicate menu title.
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
     * Creates and configures multiple modal dialogs used for displaying various informational and confirmation messages to the user.
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
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the links group for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this links group?</p>
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
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This links group is now hidden!</p>');
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This links group has now been made public!</p>');
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
     * Retrieves an array of content type names, excluding certain items based on specified conditions.
     *
     * @return array An array of content type names with exclusions applied where 'IsEnabled' is 0.
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
     * Validates and processes available statistics types for a given content type, ensuring adherence to set constraints.
     *
     * @return void This method updates the text property of an internal label component to communicate available or unavailable statistics types based on defined logic.
     */
    protected function checkStatisticsTypes()
    {
        // Define all possible statistics types
        $arrStatisticsTypes = [14 => 'Records', 15 => 'Rankings', 16 => 'Achievements'];
        $intMaxAllowedTypes = self::MAX_ALLOWED_TYPES; // Allows for easy customization in the future

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

            $this->objStatisticsSettings->setStatus(1);
            $this->objStatisticsSettings->save();
        }
    }

    /**
     * Updates the selected value of the status input field based on the enabled status
     * of the current menu content.
     *
     * @return void
     */
    private function updateInputFields()
    {
        $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
    }

    /**
     * Handles the click event for a status item, updating relevant UI components and settings.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void This method does not return any value.
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

        $this->objStatisticsSettings->setStatus(2);
        $this->objStatisticsSettings->save();

        $this->dlgModal3->hideDialogBox();
        $this->dlgModal4->showDialogBox();
    }

    /**
     * Handles the click event for hiding the cancel button, setting the selected value of the status list
     * based on the enabled state of the menu content.
     *
     * @param ActionParams $params The parameters received from the click action event.
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
     * Handles the click event for the "Go to statistics" button, setting session parameters and redirecting the user.
     *
     * @param ActionParams $params The parameters associated with the action, typically provided by the event system.
     * @return void No return value as the method performs a session variable assignment and a page redirect.
     */
    public function btnGoToStatistics_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $_SESSION['statistics_edit_group'] = $this->intId;
        Application::redirect('settings_manager.php#statisticsSettings_tab');
    }

    /**
     * Handles the click event for the 'Go To List' button and redirects the user to the links list page.
     *
     * @param ActionParams $params The parameters provided by the action triggering this method.
     * @return void
     */
    public function btnGoToList_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('statistics_list.php');
    }

    /**
     * Handles the action for navigating to the menu management page.
     *
     * @param ActionParams $params Parameters associated with the action event.
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