<?php
require('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;
use QCubed\QString;
use QCubed\Action\Ajax;
use QCubed\Event\Change;

class SampleForm extends Form
{
    protected $dlgModal1;
    protected $dlgModal2;
    protected $dlgModal3;
    protected $dlgModal4;
    protected $dlgModal5;
    protected $dlgModal6;
    protected $dlgModal7;

    protected $dlgToastr1;
    protected $dlgToastr2;
    protected $dlgToastr3;
    protected $dlgToastr4;
    protected $dlgToastr5;
    protected $dlgToastr6;
    protected $dlgToastr7;
    protected $dlgToastr8;
    protected $dlgToastr9;
    protected $dlgToastr10;
    protected $dlgToastr11;
    protected $dlgToastr12;
    protected $dlgToastr13;
    protected $dlgToastr14;

    protected $lblGroupTitle;
    protected $lblInfo;
    protected $btnAddLink;
    protected $txtNewTitle;
    protected $btnLinkSave;
    protected $btnLinkCancel;
    protected $lblTitleSlug;
    protected $txtTitleSlug;

    protected $lblTitle;
    protected $txtTitle;
    
    protected $lblUrl;
    protected $txtUrl;
    
    protected $lblCategory;
    protected $lstCategory;

    protected $lblLinksGroupTitle;
    protected $lstGroupTitle;

    protected $lblLinkStatus;
    protected $lstLinkStatus;
    
    protected $dlgSorter;

    protected $btnUpdate;
    protected $btnCloseWindow;
    protected $btnGoToCategory;
    protected $btnGoToSettings;
    protected $btnBack;

    protected $lblPostDate;
    protected $calPostDate;
    protected $lblPostUpdateDate;
    protected $calPostUpdateDate;
    protected $lblAuthor;
    protected $txtAuthor;
    protected $lblUsersAsEditors;
    protected $txtUsersAsEditors;
    protected $lblStatus;
    protected $lstStatus;

    protected $intId;
    protected $intGroup;
    protected $intLoggedUserId;
    protected $intClick;

    protected $objMenu;
    protected $objLink;
    protected $objLinksSettings;

    protected $objCategoryCondition;
    protected $objCategoryClauses;

    protected $errors = []; // Array for tracking errors

    protected function formCreate()
    {
        parent::formCreate();

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->intGroup = Application::instance()->context()->queryStringItem('group');
        if (!empty($this->intId)) {
            $this->objMenu = Menu::load($this->intGroup);
            $this->objLink = Links::loadByIdFromLinksId($this->intId);
            $this->objLinksSettings = LinksSettings::load($this->intId);
        } else {
            // does nothing
        }

        //Application::displayAlert(json_encode($this->objLinksSettings));

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
        $this->intLoggedUserId = 4;

        $this->resettingInputs();
        $this->createInputs();
        $this->createButtons();
        $this->createSorter();
        $this->createToastr();
        $this->createModals();
        $this->refreshDisplay();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Resets the input elements by hiding specific UI components.
     *
     * @return void
     */
    protected function resettingInputs()
    {
        Application::executeJavaScript("
           $('.link-setting-wrapper').addClass('hidden');
           $('.form-actions-wrapper').addClass('hidden');
       ");
    }

    /**
     * Initializes and configures input fields, labels, and controls for the link creation and editing interface.
     *
     * @return void
     */
    protected function createInputs()
    {
        $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
        $this->lblGroupTitle->Text = $this->objLinksSettings->getName();
        $this->lblGroupTitle->setCssStyle('font-weight', 600);

        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please create the first link!');
        $this->lblInfo->setCssStyle('margin-bottom', 0);

        if ($this->objLinksSettings->getLinksLocked() === 1) {
            $this->lblInfo->Display = false;
        } else {
            $this->lblInfo->Display = true;
        }

        $this->txtNewTitle = new Bs\TextBox($this);
        $this->txtNewTitle->Placeholder = t('Link title');
        $this->txtNewTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtNewTitle->setCssStyle('float', 'left');
        $this->txtNewTitle->Width = '45%';
        $this->txtNewTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtNewTitle->Display = false;

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View: ');
        $this->lblTitleSlug->setCssStyle('font-weight', 'bold');

        if ($this->objLinksSettings->getTitleSlug()) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
            $this->txtTitleSlug->setCssStyle('text-align', 'left;');
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objLinksSettings->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
        } else {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->lblTitle  = new Q\Plugin\Control\Label($this);
        $this->lblTitle->Text = t('Link title');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 'normal');
        $this->lblTitle->Required = true;

        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Link title');
        $this->txtTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtTitle->setHtmlAttribute('required', 'required');
        $this->txtTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblUrl  = new Q\Plugin\Control\Label($this);
        $this->lblUrl->Text = t('Url');
        $this->lblUrl->addCssClass('col-md-3');
        $this->lblUrl->setCssStyle('font-weight', 'normal');
        $this->lblUrl->Required = true;

        $this->txtUrl = new Bs\TextBox($this);
        $this->txtUrl->Placeholder = t('Url');
        $this->txtUrl->setHtmlAttribute('autocomplete', 'off');
        $this->txtUrl->setHtmlAttribute('required', 'required');
        $this->txtUrl->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtUrl->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtUrl->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtUrl->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblCategory = new Q\Plugin\Control\Label($this);
        $this->lblCategory->Text = t('Category');
        $this->lblCategory->addCssClass('col-md-3');
        $this->lblCategory->setCssStyle('font-weight', 400);

        $this->lstCategory = new Q\Plugin\Select2($this);
        $this->lstCategory->MinimumResultsForSearch = -1;
        $this->lstCategory->Theme = 'web-vauu';
        $this->lstCategory->Width = '90%';
        $this->lstCategory->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstCategory->addItem(t('- Select one category -'), null, true);
        $this->lstCategory->addItems($this->lstCategory_GetItems());

        if (!empty($this->objLink->CategoryId)) {
            $this->lstCategory->SelectedValue = $this->objLink->CategoryId ? $this->objLink->CategoryId : null;
        }

        $this->lstCategory->AddAction(new Q\Event\Change(), new Q\Action\Ajax('lstCategory_Change'));

        if (LinksCategory::countAll() == 0 || LinksCategory::countAll() == LinksCategory::countByStatus(2)) {
            $this->lstCategory->Enabled = false;
        } else {
            $this->lstCategory->Enabled = true;
        }

        $this->lblLinksGroupTitle = new Q\Plugin\Control\Label($this);
        $this->lblLinksGroupTitle->Text = t('Links group');
        $this->lblLinksGroupTitle->addCssClass('col-md-3');
        $this->lblLinksGroupTitle->setCssStyle('font-weight', 400);

        $this->lstGroupTitle = new Q\Plugin\Control\Select2($this);
        $this->lstGroupTitle->MinimumResultsForSearch = -1;
        $this->lstGroupTitle->ContainerWidth = 'resolve';
        $this->lstGroupTitle->Theme = 'web-vauu';
        $this->lstGroupTitle->Width = '90%';
        $this->lstGroupTitle->setCssStyle('float', 'left');
        $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstGroupTitle->addAction(new Q\Event\Change(), new Q\Action\Ajax('lstGroupTitle_Change'));

        $countByIsReserved = LinksSettings::countByIsReserved(1);
        $objGroups = LinksSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::LinksSettings()->Id)));

        foreach ($objGroups as $objTitle) {
            if ($objTitle->IsReserved === 1) {
                $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                if (!empty($this->objLink->SettingsId)) {
                    $this->lstGroupTitle->SelectedValue = $this->objLink->SettingsId ? $this->objLink->SettingsId : null;
                }
            }
        }

        if ($countByIsReserved === 1) {
            $this->lstGroupTitle->Enabled = false;
        } else {
            $this->lstGroupTitle->Enabled = true;
        }

        $this->lblLinkStatus = new Q\Plugin\Control\Label($this);
        $this->lblLinkStatus->Text = t('Status');
        $this->lblLinkStatus->addCssClass('col-md-3');
        $this->lblLinkStatus->setCssStyle('font-weight', 'normal');

        $this->lstLinkStatus = new Q\Plugin\RadioList($this);
        $this->lstLinkStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
        $this->lstLinkStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
        $this->lstLinkStatus->setCssStyle('margin-top', '-11px');
        $this->lstLinkStatus->addAction(new Q\Event\Change(), new Ajax('lstLinkStatus_Change'));

        ///////////////////////////////////////////////////////////////////////////////////////////

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objLinksSettings->PostDate ? $this->objLinksSettings->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostDate->setCssStyle('font-weight', 'normal');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objLinksSettings->PostUpdateDate ? $this->objLinksSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->setCssStyle('font-weight', 'bold');

        $this->txtAuthor  = new Bs\Label($this);
        $this->txtAuthor->Text = $this->objLinksSettings->Author;
        $this->txtAuthor->setCssStyle('font-weight', 'normal');

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->setCssStyle('font-weight', 'bold');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
        $this->lstStatus->SelectedValue = $this->objLinksSettings->Status;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->Enabled = true;
        $this->lstStatus->addAction(new Q\Event\Change(), new Ajax('lstStatus_Change'));
    }

    /**
     * Creates and initializes various buttons used in the interface,
     * including defining their styles, properties, and event handlers for interactions.
     *
     * @return void
     */
    protected function createButtons()
    {
        $this->btnAddLink = new Bs\Button($this);
        $this->btnAddLink->Text = t(' Add link');
        $this->btnAddLink->CssClass = 'btn btn-orange';
        $this->btnAddLink->setCssStyle('float', 'left');
        $this->btnAddLink->setCssStyle('margin-right', '10px');
        $this->btnAddLink->CausesValidation = false;
        $this->btnAddLink->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddLink_Click'));

        $this->btnLinkSave = new Bs\Button($this);
        $this->btnLinkSave->Text = t('Save');
        $this->btnLinkSave->CssClass = 'btn btn-orange';
        $this->btnLinkSave->setCssStyle('float', 'left');
        $this->btnLinkSave->setCssStyle('margin-left', '10px');
        $this->btnLinkSave->setCssStyle('margin-right', '10px');
        $this->btnLinkSave->Display = false;
        $this->btnLinkSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnLinkSave_Click'));

        $this->btnLinkCancel = new Bs\Button($this);
        $this->btnLinkCancel->Text = t('Cancel');
        $this->btnLinkCancel->CssClass = 'btn btn-default';
        $this->btnLinkCancel->setCssStyle('float', 'left');
        $this->btnLinkCancel->CausesValidation = false;
        $this->btnLinkCancel->Display = false;
        $this->btnLinkCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnLinkCancel_Click'));

        //////////////////////////////////////////////////////////////////////////////////////////

        $this->btnUpdate = new Bs\Button($this);
        $this->btnUpdate->Text = t('Update');
        $this->btnUpdate->CssClass = 'btn btn-orange';
        $this->btnUpdate->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnUpdate_Click'));

        $this->btnCloseWindow = new Bs\Button($this);
        $this->btnCloseWindow->Text = t('Close the window');
        $this->btnCloseWindow->CssClass = 'btn btn-default';
        $this->btnCloseWindow->CausesValidation = false;
        $this->btnCloseWindow->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCloseWindow_Click'));

        $this->btnGoToCategory = new Bs\Button($this);
        $this->btnGoToCategory->Tip = true;
        $this->btnGoToCategory->ToolTip = t('Go to categories manager');
        $this->btnGoToCategory->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToCategory->CssClass = 'btn btn-default';
        $this->btnGoToCategory->setCssStyle('float', 'right');
        $this->btnGoToCategory->addWrapperCssClass('center-button');
        $this->btnGoToCategory->CausesValidation = false;
        $this->btnGoToCategory->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToCategory_Click'));

        $this->btnGoToSettings = new Bs\Button($this);
        $this->btnGoToSettings->Tip = true;
        $this->btnGoToSettings->ToolTip = t('Go to links settings manager');
        $this->btnGoToSettings->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToSettings->CssClass = 'btn btn-default';
        $this->btnGoToSettings->setCssStyle('float', 'right');
        $this->btnGoToSettings->addWrapperCssClass('center-button');
        $this->btnGoToSettings->CausesValidation = false;
        $this->btnGoToSettings->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToSettings_Click'));

        //////////////////////////////////////////////////////////////////////////////////////////

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->setCssStyle('margin-left', '10px');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnBack_Click'));
    }

    /**
     * Creates and configures a SortWrapper instance used for managing sortable items.
     *
     * @return void
     */
    protected function createSorter()
    {
        $this->dlgSorter = new Q\Plugin\Control\SortWrapper($this);
        $this->dlgSorter->createNodeParams([$this, 'Sorter_Draw']);
        $this->dlgSorter->createControlButtons([$this, 'Buttons_Draw']);
        $this->dlgSorter->createRenderInputs([$this, 'Dates_Draw']);
        $this->dlgSorter->setDataBinder('Sorter_Bind');
        $this->dlgSorter->ActivatedLink = true;
        $this->dlgSorter->addCssClass('sortable');
        $this->dlgSorter->Placeholder = 'placeholder';
        $this->dlgSorter->Handle = '.reorder';
        $this->dlgSorter->Items = 'div.div-block';

        $this->dlgSorter->addAction(new Q\Jqui\Event\SortableStop(), new Q\Action\Ajax('sortable_stop'));
        $this->dlgSorter->watch(QQN::Links());
    }

    /**
     * Binds the sorter with the data source based on the current settings.
     *
     * @return void
     */
    protected function Sorter_Bind()
    {
        $this->dlgSorter->DataSource = Links::QueryArray(
            QQ::Equal(QQN::Links()->SettingsId, $this->intId),
            QQ::orderBy(QQN::Links()->Order)
        );
    }

    /**
     * Prepares and returns an associative array representation of a given link.
     *
     * @param Links $objLink The link object to be processed.
     * @return array An associative array containing the id, category, name, url, order, and status of the link.
     */
    public function Sorter_Draw(Links $objLink)
    {
        $a['id'] = $objLink->Id;
        $a['category'] = $objLink->LinkCategory;
        $a['name'] = $objLink->Name;
        $a['url'] = $objLink->Url;
        $a['order'] = $objLink->Order;
        $a['status'] = $objLink->Status;
        return $a;
    }

    /**
     * Generates and returns the HTML for "Edit" and "Delete" buttons associated with a specific link.
     * If the buttons do not already exist, they are created, configured, and stored.
     *
     * @param Links $objLink The link object for which the buttons are being created or retrieved.
     * @return string The rendered HTML for the "Edit" and "Delete" buttons.
     */
    public function Buttons_Draw(Links $objLink)
    {
        $strEditId = 'btnEdit' . $objLink->Id;

        if (!$btnEdit = $this->getControl($strEditId)) {
            $btnEdit = new Bs\Button($this->dlgSorter, $strEditId);
            $btnEdit->Glyph = 'glyphicon glyphicon-pencil';
            $btnEdit->Tip = true;
            $btnEdit->ToolTip = t('Edit');
            $btnEdit->CssClass = 'btn btn-icon btn-xs edit';
            $btnEdit->ActionParameter = $objLink->Id;
            $btnEdit->UseWrapper = false;
            $btnEdit->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnEdit_Click'));
        }

        $strDeleteId = 'btnDelete' . $objLink->Id;

        if (!$btnDelete = $this->getControl($strDeleteId)) {
            $btnDelete = new Bs\Button($this->dlgSorter, $strDeleteId);
            $btnDelete->Glyph = 'glyphicon glyphicon-trash';
            $btnDelete->Tip = true;
            $btnDelete->ToolTip = t('Delete');
            $btnDelete->CssClass = 'btn btn-icon btn-xs delete';
            $btnDelete->ActionParameter = $objLink->Id;
            $btnDelete->UseWrapper = false;
            $btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));
        }

        return $btnEdit->render(false) . $btnDelete->render(false);
    }

    /**
     * Renders and returns formatted date labels for the given link object.
     *
     * @param Links $objLink The link object for which date labels are to be created and rendered.
     * @return string A concatenated string of the rendered post and post update date labels.
     */
    public function Dates_Draw(Links $objLink)
    {
        $strPostDate = 'calPostDate' . $objLink->Id;

        if (!$calPostDate = $this->getControl($strPostDate)) {
            $calPostDate =  new Bs\Label($this->dlgSorter, $strPostDate);
            $calPostDate->Text = $objLink->PostDate ? $objLink->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $calPostDate->setCssStyle('float', 'left');
            //$calPostDate->setCssStyle('padding-right', '30px');
            $calPostDate->setCssStyle('font-weight', 'normal');
        }

        $strPostUpdateDate = 'calPostUpdateDate' . $objLink->Id;

        if (!$calPostUpdateDate = $this->getControl($strPostUpdateDate)) {
            $calPostUpdateDate =  new Bs\Label($this->dlgSorter, $strPostUpdateDate);
            $calPostUpdateDate->Text = $objLink->PostUpdateDate ? $objLink->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $calPostUpdateDate->setCssStyle('float', 'right');
            $calPostUpdateDate->setCssStyle('font-weight', 'normal');
        }

        return $calPostDate->render(false) . ' ' . $calPostUpdateDate->render(false);
    }

    /**
     * Initializes multiple Toastr notification instances with specific configurations
     * for various success, error, or informational messages and settings.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The new title of link has been successfully created and saved.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, creating or saving the new title of link failed!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Sorry</strong>, the title is required!');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('<strong>Well done!</strong> The order of links was successfully updated!');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('<strong>Sorry</strong>, updating the order of links failed!');
        $this->dlgToastr5->ProgressBar = true;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('<strong>Well done!</strong> The link data has been successfully updated!');
        $this->dlgToastr6->ProgressBar = true;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('<strong>Sorry</strong>, updating the link data failed!');
        $this->dlgToastr7->ProgressBar = true;

        $this->dlgToastr8 = new Q\Plugin\Toastr($this);
        $this->dlgToastr8->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr8->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr8->Message = t('<strong>Well done!</strong> Updates to some records for this link were discarded, and the record has been restored!');
        $this->dlgToastr8->ProgressBar = true;

        ///////////////////////////////////////////////////////////////////////////////////////////

        $this->dlgToastr9 = new Q\Plugin\Toastr($this);
        $this->dlgToastr9->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr9->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr9->Message = t('<strong>Well done!</strong> This link with data has now been made public!');
        $this->dlgToastr9->ProgressBar = true;

        $this->dlgToastr10 = new Q\Plugin\Toastr($this);
        $this->dlgToastr10->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr10->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr10->Message = t('<strong>Well done!</strong> This link with data is now hidden!');
        $this->dlgToastr10->ProgressBar = true;

        $this->dlgToastr11 = new Q\Plugin\Toastr($this);
        $this->dlgToastr11->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr11->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr11->Message = t('<strong>Sorry</strong>, this field is required!');
        $this->dlgToastr11->ProgressBar = true;
        $this->dlgToastr11->EscapeHtml = false;

        $this->dlgToastr12 = new Q\Plugin\Toastr($this);
        $this->dlgToastr12->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr12->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr12->Message = t('<strong>Sorry</strong>, these fields must be filled!');
        $this->dlgToastr12->ProgressBar = true;

        ///////////////////////////////////////////////////////////////////////////////////////////

        $this->dlgToastr13 = new Q\Plugin\Toastr($this);
        $this->dlgToastr13->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr13->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr13->Message = t('<strong>Well done!</strong> The link with the data was successfully deleted!');
        $this->dlgToastr13->ProgressBar = true;

        $this->dlgToastr14 = new Q\Plugin\Toastr($this);
        $this->dlgToastr14->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr14->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr14->Message = t('<strong>Sorry</strong>, the link deletion failed!');
        $this->dlgToastr14->ProgressBar = true;
    }

    /**
     * Creates and initializes modal dialogs with specific configurations and content.
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
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the Links group for this menu item cannot be changed!</p>
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
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('statusItem_Click'));

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

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this link and its associated data?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
        $this->dlgModal6->Title = 'Warning';
        $this->dlgModal6->HeaderClasses = 'btn-danger';
        $this->dlgModal6->addButton("I accept", null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal6->addCloseButton(t("I'll cancel"));
        $this->dlgModal6->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('deleteItem_Click'));
        
        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to transfer this link along with its data from this links group to another links group?</p>');
        $this->dlgModal7->Title = t('Warning');
        $this->dlgModal7->HeaderClasses = 'btn-danger';
        $this->dlgModal7->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal7->addCloseButton(t("I'll cancel"));
        $this->dlgModal7->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('moveItem_Click'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the stop action for the sortable functionality, updating the order of items
     * and applying necessary changes to the linked data. Also notifies the user and updates
     * associated settings after the reordering process is completed.
     *
     * @param ActionParams $params The parameters passed during the sortable stop action event.
     * @return void
     */
    protected function sortable_stop(ActionParams $params) {
        $arr = $this->dlgSorter->ItemArray;

        foreach ($arr as $order => $cids) {
            $cid = explode('_',  $cids);
            $id = end($cid);

            $objSorter = Links::load($id);
            $objSorter->setOrder($order);
            $objSorter->setPostUpdateDate(Q\QDateTime::Now());
            $objSorter->save();
        }

        // Let's check if the array is not empty
        if (!empty($arr)) {
            $this->dlgToastr4->notify();
        } else {
            $this->dlgToastr5->notify();
        }

        Application::executeJavaScript(sprintf("
            $('.link-setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
       "));

        $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
        $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objLinksSettings->save();

        $this->calPostUpdateDate->Text = $this->objLinksSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    /**
     * Handles the click event for the "Add Link" button. Updates UI elements by enabling/disabling components,
     * initializing input fields, and executing client-side JavaScript for advanced UI manipulations.
     *
     * @param ActionParams $params Parameters associated with the triggered action.
     * @return void
     */
    protected function btnAddLink_Click(ActionParams $params)
    {
        $this->btnAddLink->Enabled = false;
        $this->txtNewTitle->Display = true;
        $this->btnLinkSave->Display = true;
        $this->btnLinkCancel->Display = true;
        $this->txtNewTitle->Text = '';
        $this->txtNewTitle->focus();

        Application::executeJavaScript(sprintf("
            jQuery(\"[data-value='{$this->intClick}']\").removeClass('activated');
             $('.link-setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
       "));
    }

    /**
     * Handles the save action for a new link.
     *
     * This method is triggered when the save button for creating a new link is clicked.
     * It validates the input, creates and saves a new link, updates related settings,
     * and manages the display state of UI components.
     *
     * @param ActionParams $params Parameters passed from the action event.
     * @return void
     */
    protected function btnLinkSave_Click(ActionParams $params)
    {
        if (trim($this->txtNewTitle->Text) !== '') {
            $objLink = new Links();
            $objLink->setSettingsId($this->intId);
            $objLink->setSettingsIdTitle($this->objLinksSettings->getName());
            $objLink->setName(trim($this->txtNewTitle->Text));
            $objLink->setOrder(Links::generateOrder($this->intId));
            $objLink->setStatus(2);
            $objLink->setPostDate(Q\QDateTime::Now());
            $objLink->save();

            // A check must be made here if the first record and the following records occur in this group,
            // then set "Links_locked" to 1 in the LinksSettings column, etc...

            if (Links::countBySettingsId($this->intId) !== 0) {
                if ($this->objLinksSettings->getLinksLocked() === 0) {
                    $this->objLinksSettings->setLinksLocked(1);
                }
            }

            $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
            $this->objLinksSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();

            if ($objLink->getId()) {
                $this->txtNewTitle->Text = '';
                $this->btnAddLink->Enabled = true;
                $this->txtNewTitle->Display = false;
                $this->btnLinkSave->Display = false;
                $this->btnLinkCancel->Display = false;

                $this->dlgToastr1->notify();
            } else {
                $this->dlgToastr12->notify();
            }
        } else {
            $this->txtNewTitle->Text = '';
            $this->txtNewTitle->focus();
            $this->btnAddLink->Enabled = false;
            $this->txtNewTitle->Display = true;
            $this->btnLinkSave->Display = true;
            $this->btnLinkCancel->Display = true;

            $this->dlgToastr3->notify();
        }

        if ($this->objLinksSettings->getLinksLocked() === 1) {
            $this->lblInfo->Display = false;
        } else {
            $this->lblInfo->Display = true;
        }
    }

    /**
     * Handles the cancel action for the link creation process.
     * Resets the form and re-enables the 'Add Link' button.
     *
     * @param ActionParams $params The parameters related to the action triggered by the user.
     * @return void
     */
    protected function btnLinkCancel_Click(ActionParams $params)
    {
        $this->btnAddLink->Enabled = true;
        $this->txtNewTitle->Display = false;
        $this->btnLinkSave->Display = false;
        $this->btnLinkCancel->Display = false;
        $this->txtNewTitle->Text = '';
    }

    protected function btnEdit_Click(ActionParams $params)
    {
        $intEditId = intval($params->ActionParameter);
        $objEdit = Links::load($intEditId);
        $this->intClick = intval($intEditId);

        $this->txtTitle->Text = $objEdit->Name;
        $this->txtUrl->Text = $objEdit->Url;
        $this->lstCategory->SelectedValue = $objEdit->CategoryId;
        $this->lstGroupTitle->SelectedValue = $objEdit->SettingsId;
        $this->lstLinkStatus->SelectedValue = $objEdit->Status;

        Application::executeJavaScript(sprintf("
            $(\"[data-value='{$intEditId}']\").addClass('activated');
            $(\"[data-value='{$intEditId}']\").removeClass('inactivated');
            $('.link-setting-wrapper').removeClass('hidden');
            $('.form-actions-wrapper').removeClass('hidden');
       "));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Retrieves a list of category items based on the provided conditions and clauses.
     *
     * @return ListItem[] An array of ListItem objects representing categories, with specific selection
     * and disabling logic applied.
     */
    public function lstCategory_GetItems()
    {
        $a = array();
        $objCondition = $this->objCategoryCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objCategoryCursor = LinksCategory::queryCursor($objCondition, $this->objCategoryClauses);

        // Iterate through the Cursor
        while ($objCategory = LinksCategory::instantiateCursor($objCategoryCursor)) {
            $objListItem = new ListItem($objCategory->__toString(), $objCategory->Id);

            if (!empty($this->objLink->Category)) {
                if (($this->objLink->Category) && ($this->objLink->Category->Id == $objCategory->Id))
                    $objListItem->Selected = true;
            }

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objCategory->Status == 2) {
                $objListItem->Disabled = true;
            }

            $a[] = $objListItem;
        }

        return $a;
    }

    /**
     * Handles the change event for the category dropdown list and updates relevant data accordingly.
     *
     * @param ActionParams $params The parameters associated with the action that triggered this method.
     * @return void
     */
    protected function lstCategory_Change(ActionParams $params)
    {
        $objCategory = Links::load($this->intClick);

        if ($this->lstCategory->SelectedValue !== $objCategory->getCategoryId()) {
            $objCategory->setCategoryId($this->lstCategory->SelectedValue);

            if ($this->lstCategory->SelectedValue === null) {
                $objCategory->setLinkCategory(null);
            } else {
                $objCategory->setLinkCategory($this->lstCategory->SelectedName);
            }

            $objCategory->setPostUpdateDate(Q\QDateTime::Now());
            $objCategory->save();

            $this->dlgToastr6->notify();
        }

        $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objLinksSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
        $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
        $this->dlgSorter->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the change event for the group title dropdown list.
     *
     * This method compares the selected value in the `lstGroupTitle` dropdown with the settings ID
     * stored in the `objLink` object. If they do not match, it triggers the display of a modal dialog box.
     *
     * @param ActionParams $params The parameters associated with the action, usually including context for the event triggering this method.
     * @return void
     */
    protected function lstGroupTitle_Change(ActionParams $params)
    {
        if ($this->lstGroupTitle->SelectedValue !== $this->objLink->getSettingsId()) {
            $this->dlgModal7->showDialogBox();
        }
    }

    /**
     * Handles the action triggered by clicking the Move Item button.
     * This method moves a specific link item to a different group and updates related group and link settings accordingly.
     *
     * @param ActionParams $params The parameters associated with the action, containing details about the user's interaction.
     * @return void
     */
    protected function moveItem_Click(ActionParams $params)
    {
        $this->dlgModal7->hideDialogBox();

        $objMove = Links::load($this->intClick);

        $objGroupId = LinksSettings::loadById($this->lstGroupTitle->SelectedValue);
        $objGroupTitle = LinksSettings::loadById($this->lstGroupTitle->SelectedName);

        $currentCount = Links::countBySettingsId($objMove->getSettingsId());
        $nextCount = Links::countBySettingsId($objGroupId->getId());

        $objTargetGroup = LinksSettings::loadById($objGroupId->getId());
        if ($nextCount == 0) {
            $objTargetGroup->setLinksLocked(1);
            $objTargetGroup->save();
        }

        $objGroup = LinksSettings::loadById($objMove->getSettingsId());
        if ($currentCount) {
            if ($currentCount == 1) {
                $objGroup->setLinksLocked(0);
            } else {
                $objGroup->setLinksLocked(1);
            }
            $objGroup->save();
        }

        $objLink = Links::load($objMove->getId());
        $objLink->setSettingsId($this->lstGroupTitle->SelectedValue);
        $objLink->setSettingsIdTitle($this->lstGroupTitle->SelectedName);
        $objLink->setOrder(Links::generateOrder($this->lstGroupTitle->SelectedValue));
        $objLink->setPostUpdateDate(Q\QDateTime::Now());
        $objLink->save();

        Application::redirect('Links_edit.php?id=' . $objGroupId->getId() . '&group=' . $objGroupId->getMenuContentId());
    }

    /**
     * Handles the change event for the link status dropdown selection.
     * This method performs input validation, saves data conditionally based on errors or status changes,
     * updates related data, and refreshes the UI.
     *
     * @param ActionParams $params Parameters associated with the action event triggering this method.
     * @return void
     */
    protected function lstLinkStatus_Change(ActionParams $params)
    {
        $intClick = Links::load($this->intClick);
        $this->InputsCheck();

        if (count($this->errors)) {
            $this->lstLinkStatus->SelectedValue =  $intClick->getStatus();
        }

        // Condition for which notification to show
        if (count($this->errors) === 1) {
            $this->dlgToastr11->notify(); // If only one field is invalid
            $this->saveHelper(); // Partial saving allowed
        } elseif (count($this->errors) > 1) {
            $this->dlgToastr12->notify(); // If there is more than one invalid field
            $this->saveHelper(); // Partial saving allowed
        } else {
            if ($this->lstLinkStatus->SelectedValue === 1) {
                if ($this->txtTitle->Text || $this->txtUrl->Text) {
                    $intClick->setStatus(1);
                    $this->dlgToastr9->notify();
                } else {
                    $intClick->setStatus(2);
                    $this->dlgToastr10->notify();
                }
            } else {
                $intClick->setStatus(2);
                $this->dlgToastr10->notify();
            }

            $intClick->save();
        }

        unset($this->errors);

        // Continue to update additional data and refresh the screen
        $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objLinksSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
        $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        Application::executeJavaScript(sprintf("
            $(\"[data-value='{$intClick}']\").addClass('activated');
        "));

        $this->dlgSorter->refresh();
        $this->refreshDisplay();
    }

    /**
     * Handles the change in status for the related menu and page configuration.
     *
     * @param ActionParams $params Parameters associated with the action triggered.
     * @return void This method does not return any value, it performs necessary actions based on the status change.
     */
    protected function lstStatus_Change(ActionParams $params)
    {
        $objMenuContent = MenuContent::loadById($this->objMenu->getId());

        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->dlgModal1->showDialogBox();
            $this->updateInputFields();
        } else if ($objMenuContent->SelectedPageLocked === 1) {
            $this->dlgModal2->showDialogBox();
            $this->updateInputFields();
        } else if ($this->objLinksSettings->getStatus() === 1) {
            $this->dlgModal3->showDialogBox();
        } else {
            $this->lockInputFields();
        }
    }

    /**
     * Handles the click event for the status item. It performs multiple operations including updating link settings,
     * refreshing the display, and updating related UI components.
     *
     * @param ActionParams $params Contains the parameters passed to this method when the status item is clicked.
     * @return void This method does not return any value.
     */
    protected function statusItem_Click(ActionParams $params)
    {
        $this->dlgModal3->hideDialogBox();

        $objMenuContent = MenuContent::loadById($this->intGroup);

        $this->objLinksSettings->setStatus(2);
        $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objLinksSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
        $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $objMenuContent->setIsEnabled(2);
        $objMenuContent->save();

        Application::executeJavaScript(sprintf("
            $('.link-setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
        "));

        $this->dlgSorter->refresh();
        $this->refreshDisplay();
    }

    /**
     * Updates the input fields by setting the selected value of the status dropdown
     * to match the status from the associated LinksSettings object and refreshes the dropdown.
     *
     * @return void
     */
    protected function updateInputFields()
    {
        $this->lstStatus->SelectedValue = $this->objLinksSettings->getStatus();
        $this->lstStatus->refresh();
    }

    /**
     * Locks the input fields by updating the status, post-update date, assigned editors,
     * and other related properties based on the current user and group.
     * Also sets the visibility and other settings for the associated menu content.
     *
     * @return void
     */
    protected function lockInputFields()
    {
        $objMenuContent = MenuContent::loadById($this->intGroup);

        $this->objLinksSettings->setStatus(1);
        $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objLinksSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
        $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $objMenuContent->setIsEnabled(1);
        $objMenuContent->save();

        $this->refreshDisplay();
        $this->dlgModal5->showDialogBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Saves or updates the helper object with the provided attributes from the form inputs.
     *
     * @return void Performs the saving of the helper object to the database.
     */
    protected function saveHelper()
    {
        $objHelper = Links::load($this->intClick);

        $objHelper->setName($this->txtTitle->Text);
        $objHelper->setUrl($this->txtUrl->Text);
        $objHelper->setStatus($this->lstLinkStatus->SelectedValue);
        $objHelper->setPostDate(Q\QDateTime::Now());
        $objHelper->save();
    }

    /**
     * Handles the update process for a specific link, applying validation, updating fields, displaying notifications, and saving changes.
     *
     * @param ActionParams $params The parameters associated with the click action.
     *
     * @return void
     */
    protected function btnUpdate_Click(ActionParams $params)
    {
        $objUpdate = Links::load($this->intClick);
        $this->InputsCheck();

        // Check if $objUpdate is available
        if (!$objUpdate) {
            $this->dlgToastr7->notify();
            return;
        }

        if (count($this->errors)) {
            $this->lstLinkStatus->SelectedValue = 2;
        }

        // Condition for which notification to show
        if (count($this->errors) === 1) {
            $this->dlgToastr11->notify(); // If only one field is invalid
            $this->saveHelper(); // Partial saving allowed
            $this->lstLinkStatus->SelectedValue = 2;

        } elseif (count($this->errors) > 1) {
            $this->dlgToastr12->notify(); // If there is more than one invalid field
            $this->saveHelper(); // Partial saving allowed
            $this->lstLinkStatus->SelectedValue = 2;
        } else {
            $objUpdate->Name = $this->txtTitle->Text;
            $objUpdate->Url = $this->txtUrl->Text;
            $objUpdate->CategoryId = $this->lstCategory->SelectedValue;
            $objUpdate->Status = $this->lstLinkStatus->SelectedValue;
            $objUpdate->PostUpdateDate = Q\QDateTime::Now();
        }

        // Check if the save was successful
        try {
            $objUpdate->save();
            if (!count($this->errors)) {
                $this->dlgToastr6->notify();
            }
        } catch (Exception $e) {
            //$this->dlgToastr7->notify();
            error_log('Save failed: ' . $e->getMessage());
            return;
        }

        unset($this->errors);

        // Continue to update additional data and refresh the screen
        $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objLinksSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
        $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
        $this->dlgSorter->refresh();

        Application::executeJavaScript(sprintf("
            $(\"[data-value='{$this->intClick}']\").addClass('activated');
            $(\"[data-value='{$this->intClick}']\").removeClass('inactivated');
            $('.link-setting-wrapper').removeClass('hidden');
            $('.form-actions-wrapper').removeClass('hidden');
       "));
    }

    /**
     * Validates input fields and adds errors to the errors array if fields are empty.
     *
     * @return void This method does not return any value. It sets HTML attributes for required fields
     * and updates the errors array with field identifiers.
     */
    protected  function InputsCheck()
    {
        // We check each field and add errors if necessary
        if (!$this->txtTitle->Text) {
            $this->txtTitle->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtTitle';
        }

        if (!$this->txtUrl->Text) {
            $this->txtUrl->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtUrl';
        }
    }

    /**
     * Handles the click event for the close window button. Executes JavaScript to remove specific
     * activation classes and hide specific UI elements.
     *
     * @param ActionParams $params The parameters for the button click action.
     * @return void
     */
    protected function btnCloseWindow_Click(ActionParams $params)
    {
        Application::executeJavaScript(sprintf("
            $(\"[data-value='{$this->intClick}']\").removeClass('activated');
            $('.link-setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
       "));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event to load and set item details for editing.
     *
     * @param ActionParams $params The parameters passed from the triggered action.
     * @return void
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        $objCancel = Links::load($this->intClick);

        // Check if $objCancel is available
        if ($objCancel) {
            $this->dlgToastr8->notify();
        }

        $this->txtTitle->Text = $objCancel->Name;
        $this->txtUrl->Text = $objCancel->Url;
        $this->lstCategory->SelectedValue = $objCancel->CategoryId;
        $this->lstLinkStatus->SelectedValue = $objCancel->Status;
    }

    /**
     * Handles the click event for the delete button. It sets the action parameter
     * as an integer and triggers the display of a modal dialog box.
     *
     * @param ActionParams $params The parameters associated with the button click action,
     * including the action parameter to be processed.
     *
     * @return void This method does not return any value.
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        $this->intClick = intval($params->ActionParameter);
        $this->dlgModal6->showDialogBox();
    }

    /**
     * Handles the deletion of a specific link item associated with a menu content
     * and performs various updates related to link settings, user interfaces, and notifications.
     *
     * @param ActionParams $params An object containing action parameters and metadata related to the
     *                              click action triggering this method.
     *
     * @return void This method does not return a value but modifies the application state, including
     *              database entries, UI elements, and associated settings.
     */
    protected function deleteItem_Click(ActionParams $params)
    {
        $objMenuContent = MenuContent::loadById($this->objMenu->getId());
        $objLink = Links::loadById($this->intClick);

        if (Links::countBySettingsId($objLink->getSettingsId()) === 1) {
            if ($this->objLinksSettings->getLinksLocked() === 1) {
                $this->objLinksSettings->setLinksLocked(0);
            }
        }

//        if (Links::countBySettingsId($objLink->getSettingsId()) === 1) {
//            $this->objLinksSettings->setStatus(2);
//            $this->lstStatus->SelectedValue = 2;
//
//            $objMenuContent->setIsEnabled(2);
//            $objMenuContent->save();
//        }

        $objLink->delete();

        Application::executeJavaScript(sprintf("
            $('.link-setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
            "));

        if ($objLink->getId() !== $objLink) {
            $this->dlgToastr13->notify();
        } else {
            $this->dlgToastr14->notify();
        }

        $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objLinksSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
        $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->dlgSorter->refresh();
        $this->refreshDisplay();
        $this->dlgModal6->hideDialogBox();
    }

    /**
     * Handles the click event for the "Go To Category" button. Sets session variables for the current link ID and group,
     * and redirects the user to the categories manager page at the specified tab.
     *
     * @param ActionParams $params The parameters containing context for the action triggered by the button click.
     * @return void
     */
    public function btnGoToCategory_Click(ActionParams $params)
    {
        $_SESSION['links'] = $this->intId;
        $_SESSION['group'] = $this->intGroup;

        Application::redirect('categories_manager.php#c105_tab');
    }

    /**
     * Handles the click event for the "Go to Settings" button.
     * Sets session variables and redirects the user to the settings manager page.
     *
     * @param ActionParams $params Action parameters associated with the click event.
     * @return void
     */
    public function btnGoToSettings_Click(ActionParams $params)
    {
        $_SESSION['Links'] = $this->intId;
        $_SESSION['group'] = $this->intGroup;

        Application::redirect('settings_manager.php#c100_tab');
    }

    /**
     * Handles the click event for the "Back" button, navigating the user to the list page.
     *
     * @param ActionParams $params Parameters associated with the button click event.
     * @return void
     */
    protected function btnBack_Click(ActionParams $params)
    {
        $this->redirectToListPage();
    }

    /**
     * Redirects the user to the links list page.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        Application::redirect('Links_list.php');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Updates the visibility of various UI elements based on the state of the
     * associated links settings. The method evaluates conditions such as the
     * presence of a post date, post update date, author, and linked editors,
     * and adjusts the display properties accordingly.
     *
     * @return void
     */
    protected function refreshDisplay()
    {
        if ($this->objLinksSettings->getPostDate() &&
            !$this->objLinksSettings->getPostUpdateDate() &&
            $this->objLinksSettings->getAuthor() &&
            !$this->objLinksSettings->countUsersAsLinksEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }

        if ($this->objLinksSettings->getPostDate() &&
            $this->objLinksSettings->getPostUpdateDate() &&
            $this->objLinksSettings->getAuthor() &&
            !$this->objLinksSettings->countUsersAsLinksEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }

        if ($this->objLinksSettings->getPostDate() &&
            $this->objLinksSettings->getPostUpdateDate() &&
            $this->objLinksSettings->getAuthor() &&
            $this->objLinksSettings->countUsersAsLinksEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = true;
            $this->txtUsersAsEditors->Display = true;
        }
    }
}
SampleForm::run('SampleForm');