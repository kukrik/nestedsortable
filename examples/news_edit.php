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
use QCubed\Action\Server;
use QCubed\Event\Change;

class SampleForm extends Form
{
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

    protected $dlgModal1;
    protected $dlgModal2;
    protected $dlgModal3;
    protected $dlgModal4;
    protected $dlgModal5;
    protected $dlgModal6;
    protected $dlgModal7;
    protected $dlgModal8;

    protected $lblTitle;
    protected $txtTitle;

    protected $lblChanges;
    protected $lstChanges;

    protected $lblNewsCategory;
    protected $lstNewsCategory;

    protected $lblGroupTitle;
    protected $lstGroupTitle;

    protected $lblTitleSlug;
    protected $txtTitleSlug;

    protected $txtContent;

    protected $lblPostDate;
    protected $calPostDate;

    protected $lblPostUpdateDate;
    protected $calPostUpdateDate;

    protected $lblNewsAuthor;
    protected $txtNewsAuthor;

    protected $lblUsersAsEditors;
    protected $txtUsersAsEditors;

    protected $objMediaFinder;

    protected $lblPictureDescription;
    protected $txtPictureDescription;

    protected $lblAuthorSource;
    protected $txtAuthorSource;

    protected $lblStatus;
    protected $lstStatus;

    protected $lblUsePublicationDate;
    protected $chkUsePublicationDate;

    protected $lblAvailableFrom;
    protected $calAvailableFrom;

    protected $lblExpiryDate;
    protected $calExpiryDate;

    protected $lblConfirmationAsking;
    protected $chkConfirmationAsking;

    protected $btnSave;
    protected $btnSaving;
    protected $btnDelete;
    protected $btnCancel;
    protected $btnGoToChanges;
    protected $btnGoToCategories;
    protected $btnGoToSettings;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objNews;
    protected $objNewsSettings;
    protected $objFrontendLinks;
    protected $intGroup;

    protected $intLoggedUserId;
    protected $intTemporaryId;
    protected $objOldPicture;

    protected $objChangesCondition;
    protected $objChangesClauses;

    protected $objNewsCategoryCondition;
    protected $objNewsCategoryClauses;

    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected function formCreate()
    {
        parent::formCreate();

        // Deleting sessions, if any.
        if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
            unset($_SESSION['news_changes_id']);
            unset($_SESSION['news_changes_group']);
        }

        if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
            unset($_SESSION['news_categories_id']);
            unset($_SESSION['news_categories_group']);
        }

        if (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group'])) {
            unset($_SESSION['news_settings_id']);
            unset($_SESSION['news_settings_group']);
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        if (!empty($this->intId)) {
            $this->objNews = News::load($this->intId);
        } else {
            // does nothing
        }
        $this->intGroup = Application::instance()->context()->queryStringItem('group');
        $this->objNewsSettings = NewsSettings::loadByIdFromNewsSettings($this->intGroup);
        $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

        $this->objOldPicture = $this->objNews->getPictureId();

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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function createInputs()
    {
        $this->lblTitle = new Q\Plugin\Control\Label($this);
        $this->lblTitle->Text = t('Title');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 400);
        $this->lblTitle->Required = true;

        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Title');
        $this->txtTitle->Text = $this->objNews->Title ? $this->objNews->Title : null;
        $this->txtTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtTitle->setHtmlAttribute('required', 'required');

        $this->lblChanges = new Q\Plugin\Control\Label($this);
        $this->lblChanges->Text = t('Changes');
        $this->lblChanges->addCssClass('col-md-3');
        $this->lblChanges->setCssStyle('font-weight', 400);

        $this->lstChanges = new Q\Plugin\Select2($this);
        $this->lstChanges->MinimumResultsForSearch = -1;
        $this->lstChanges->Theme = 'web-vauu';
        $this->lstChanges->Width = '90%';
        $this->lstChanges->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstChanges->setCssStyle('float', 'left');
        $this->lstChanges->addItem(t('- Choose one change -'), null, true);
        $this->lstChanges->addItems($this->lstChanges_GetItems());
        $this->lstChanges->SelectedValue = $this->objNews->ChangesId;
        $this->lstChanges->AddAction(new Q\Event\Change(), new Q\Action\Ajax('lstChanges_Change'));

        $objEnabledCount = [];
        $objCanges = NewsChanges::loadAll();

        foreach ($objCanges as $objCange) {
            if ($objCange->getStatus() == 1) {
                $objEnabledCount[] = $objCange->getStatus();
            }
        }

        if (NewsChanges::countAll() == 0 || count($objEnabledCount) == 0) {
            $this->lstChanges->Enabled = false;
        } else {
            $this->lstChanges->Enabled = true;
        }

        $this->lblNewsCategory = new Q\Plugin\Control\Label($this);
        $this->lblNewsCategory->Text = t('Category');
        $this->lblNewsCategory->addCssClass('col-md-3');
        $this->lblNewsCategory->setCssStyle('font-weight', 400);

        $this->lstNewsCategory = new Q\Plugin\Select2($this);
        $this->lstNewsCategory->MinimumResultsForSearch = -1;
        $this->lstNewsCategory->Theme = 'web-vauu';
        $this->lstNewsCategory->Width = '90%';
        $this->lstNewsCategory->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstNewsCategory->setCssStyle('float', 'left');
        $this->lstNewsCategory->addItem(t('- Select one category -'), null, true);
        $this->lstNewsCategory->addItems($this->lstCategory_GetItems());
        $this->lstNewsCategory->SelectedValue = $this->objNews->NewsCategoryId;
        $this->lstNewsCategory->AddAction(new Q\Event\Change(), new Q\Action\Ajax('lstNewsCategory_Change'));

        $objEnabledCount = [];
        $objCategories = CategoryOfNews::loadAll();

        foreach ($objCategories as $objCategory) {
            if ($objCategory->getIsEnabled() == 1) {
                $objEnabledCount[] = $objCategory->getIsEnabled();
            }
        }

        if (CategoryOfNews::countAll() == 0 || count($objEnabledCount) == 0) {
            $this->lstNewsCategory->Enabled = false;
        } else {
            $this->lstNewsCategory->Enabled = true;
        }

        $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
        $this->lblGroupTitle->Text = t('News group');
        $this->lblGroupTitle->addCssClass('col-md-3');
        $this->lblGroupTitle->setCssStyle('font-weight', 400);

        $this->lstGroupTitle = new Q\Plugin\Select2($this);
        $this->lstGroupTitle->MinimumResultsForSearch = -1;
        $this->lstGroupTitle->Theme = 'web-vauu';
        $this->lstGroupTitle->Width = '90%';
        $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstGroupTitle->setCssStyle('float', 'left');
        $this->lstGroupTitle->addItem(t('- Change newsgroup -'), null, true);
        $this->lstGroupTitle->addAction(new Q\Event\Change(), new Q\Action\Ajax('lstGroupTitle_Change'));

        $countByIsReserved = NewsSettings::countByIsReserved(1);
        $objGroups = NewsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::NewsSettings()->Id)));

        foreach ($objGroups as $objTitle) {
            if ($objTitle->IsReserved === 1) {
                $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                $this->lstGroupTitle->SelectedValue = $this->objNews->NewsGroupTitleId;
            }
        }

        if ($countByIsReserved === 1) {
            $this->lstGroupTitle->Enabled = false;
        } else {
            $this->lstGroupTitle->Enabled = true;
        }

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        if ($this->objNews->getTitleSlug()) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
            $this->txtTitleSlug->setCssStyle('text-align', 'left;');
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objNews->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
        } else {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->txtContent = new Q\Plugin\CKEditor($this);
        $this->txtContent->Text = $this->objNews->Content;
        $this->txtContent->Configuration = 'ckConfig';
//        $this->txtContent->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
//        $this->txtContent->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
//        $this->txtContent->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
//        $this->txtContent->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objNews->PostDate ? $this->objNews->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostDate->setCssStyle('font-weight', 'normal');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objNews->PostUpdateDate ? $this->objNews->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

        $this->lblNewsAuthor = new Q\Plugin\Control\Label($this);
        $this->lblNewsAuthor->Text = t('News author');
        $this->lblNewsAuthor->setCssStyle('font-weight', 'bold');

        $this->txtNewsAuthor  = new Bs\Label($this);
        $this->txtNewsAuthor->Text = $this->objNews->Author;
        $this->txtNewsAuthor->setCssStyle('font-weight', 'normal');

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

        $this->refreshDisplay();

        $this->objMediaFinder = new Q\Plugin\MediaFinder($this);
        $this->objMediaFinder->TempUrl = APP_UPLOADS_TEMP_URL . "/_files/thumbnail";
        $this->objMediaFinder->PopupUrl = QCUBED_FILEMANAGER_URL . "/examples/finder.php";
        $this->objMediaFinder->EmptyImageAlt = t("Choose a picture");
        $this->objMediaFinder->SelectedImageAlt = t("Selected picture");

        $this->objMediaFinder->SelectedImageId = $this->objNews->getPictureId() ? $this->objNews->getPictureId() : null;

        if ($this->objMediaFinder->SelectedImageId !== null) {
            $objFiles = Files::loadById($this->objMediaFinder->SelectedImageId);
            $this->objMediaFinder->SelectedImagePath = $this->objMediaFinder->TempUrl . $objFiles->getPath();;
            $this->objMediaFinder->SelectedImageName = $objFiles->getName();
        }

        $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageSave(), new Q\Action\Ajax( 'imageSave_Push'));
        $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageDelete(), new Q\Action\Ajax('imageDelete_Push'));

        $this->lblPictureDescription = new Q\Plugin\Control\Label($this);
        $this->lblPictureDescription->Text = t('Picture description');
        $this->lblPictureDescription->setCssStyle('font-weight', 'bold');

        $this->txtPictureDescription = new Bs\TextBox($this);
        $this->txtPictureDescription->Text = $this->objNews->PictureDescription;
        $this->txtPictureDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtPictureDescription->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtPictureDescription->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtPictureDescription->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtPictureDescription->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtPictureDescription->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblAuthorSource = new Q\Plugin\Control\Label($this);
        $this->lblAuthorSource->Text = t('Author/source');
        $this->lblAuthorSource->setCssStyle('font-weight', 'bold');

        $this->txtAuthorSource = new Bs\TextBox($this);
        $this->txtAuthorSource->Text = $this->objNews->AuthorSource;
        $this->txtAuthorSource->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtAuthorSource->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtAuthorSource->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtAuthorSource->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtAuthorSource->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        if (!$this->objNews->getPictureId()) {
            $this->lblPictureDescription->Display = false;
            $this->txtPictureDescription->Display = false;
            $this->lblAuthorSource->Display = false;
            $this->txtAuthorSource->Display = false;
        }

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->setCssStyle('font-weight', 'bold');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden'), 3 => t('Draft')]);
        $this->lstStatus->SelectedValue = $this->objNews->Status;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->Enabled = true;
        $this->lstStatus->AddAction(new Q\Event\Change(), new Q\Action\Ajax('lstStatus_Change'));

        if ($this->objNews->getUsePublicationDate() || $this->objNews->getConfirmationAsking()) {
            $this->lstStatus->Enabled = false;
        }

        $this->lblUsePublicationDate = new Q\Plugin\Control\Label($this);
        $this->lblUsePublicationDate->Text = t('Use publication date');
        $this->lblUsePublicationDate->setCssStyle('font-weight', 'bold');

        $this->chkUsePublicationDate = new Q\Plugin\Control\Checkbox($this);
        $this->chkUsePublicationDate->Checked = $this->objNews->UsePublicationDate;
        $this->chkUsePublicationDate->WrapperClass = 'checkbox checkbox-orange';
        $this->chkUsePublicationDate->addAction(new Change(), new Ajax('setUse_PublicationDate'));

        if ($this->objNews->getConfirmationAsking()) {
            $this->chkUsePublicationDate->Enabled = false;
        }

        $this->lblAvailableFrom = new Q\Plugin\Control\Label($this);
        $this->lblAvailableFrom->Text = t('Available From');
        $this->lblAvailableFrom->setCssStyle('font-weight', 'bold');

        $this->calAvailableFrom = new Q\Plugin\DateTimePicker($this);
        $this->calAvailableFrom->Language = 'et';
        $this->calAvailableFrom->ClearBtn = true;
        $this->calAvailableFrom->TodayHighlight = true;

        $today = date('Y-m-d H:i:s');
        $this->calAvailableFrom->StartDate = $today;

        $this->calAvailableFrom->AutoClose = true;
        $this->calAvailableFrom->StartView = 2;
        $this->calAvailableFrom->ForceParse = false;
        $this->calAvailableFrom->Format = 'dd.mm.yyyy hh:ii';
        $this->calAvailableFrom->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calAvailableFrom->Text = $this->objNews->AvailableFrom ? $this->objNews->AvailableFrom->qFormat('DD.MM.YYYY hhhh:mm') : null;
        $this->calAvailableFrom->addCssClass('calendar-trigger');
        $this->calAvailableFrom->ActionParameter = $this->calAvailableFrom->ControlId;
        $this->calAvailableFrom->addAction(new Change(), new Ajax('setDate_AvailableFrom'));

        $this->lblExpiryDate = new Q\Plugin\Control\Label($this);
        $this->lblExpiryDate->Text = t('Expiry Date');
        $this->lblExpiryDate->setCssStyle('font-weight', 'bold');

        $this->calExpiryDate = new Q\Plugin\DateTimePicker($this);
        $this->calExpiryDate->Language = 'et';
        $this->calExpiryDate->ClearBtn = true;

        $tomorrow = date('Y-m-d H:i:s', strtotime('+1 day'));
        $this->calExpiryDate->StartDate = $tomorrow;

        $this->calExpiryDate->AutoClose = true;
        $this->calExpiryDate->StartView = 2;
        $this->calExpiryDate->ForceParse = false;
        $this->calExpiryDate->Format = 'dd.mm.yyyy hh:ii';
        $this->calExpiryDate->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calExpiryDate->Text = $this->objNews->ExpiryDate ? $this->objNews->ExpiryDate->qFormat('DD.MM.YYYY hhhh:mm') : null;
        $this->calExpiryDate->addCssClass('calendar-trigger');
        $this->calExpiryDate->ActionParameter = $this->calExpiryDate->ControlId;
        $this->calExpiryDate->addAction(new Change(), new Ajax('setDate_ExpiryDate'));

        if (!$this->objNews->getUsePublicationDate()) {
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
        }

        $this->lblConfirmationAsking = new Q\Plugin\Control\Label($this);
        $this->lblConfirmationAsking->Text = t('Confirmation of publication');
        $this->lblConfirmationAsking->setCssStyle('font-weight', 'bold');

        $this->chkConfirmationAsking = new Q\Plugin\Control\Checkbox($this);
        $this->chkConfirmationAsking->Checked = $this->objNews->ConfirmationAsking;
        $this->chkConfirmationAsking->WrapperClass = 'checkbox checkbox-orange';
        $this->chkConfirmationAsking->addAction(new Change(), new Ajax('gettingConfirmation_Click'));
    }

    public function createButtons()
    {
        $this->btnSave = new Bs\Button($this);
        if ($this->objNews->getContent()) {
            $this->btnSave->Text = t('Update');
        } else {
            $this->btnSave->Text = t('Save');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSave_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSaveButtonId = $this->btnSave->ControlId;

        $this->btnSaving = new Bs\Button($this);
        if ($this->objNews->getContent()) {
            $this->btnSaving->Text = t('Update and close');
        } else {
            $this->btnSaving->Text = t('Save and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;

        $this->btnDelete = new Bs\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->addWrapperCssClass('center-button');
        $this->btnDelete->CausesValidation = false;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Back');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCancel_Click'));

        $this->btnGoToChanges = new Bs\Button($this);
        $this->btnGoToChanges->Tip = true;
        $this->btnGoToChanges->ToolTip = t('Go the news change manager');
        $this->btnGoToChanges->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToChanges->CssClass = 'btn btn-default';
        $this->btnGoToChanges->setCssStyle('float', 'right');
        $this->btnGoToChanges->addWrapperCssClass('center-button');
        $this->btnGoToChanges->CausesValidation = false;
        $this->btnGoToChanges->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToChanges_Click'));

        $this->btnGoToCategories = new Bs\Button($this);
        $this->btnGoToCategories->Tip = true;
        $this->btnGoToCategories->ToolTip = t('Go to categories manager');
        $this->btnGoToCategories->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToCategories->CssClass = 'btn btn-default';
        $this->btnGoToCategories->setCssStyle('float', 'right');
        $this->btnGoToCategories->addWrapperCssClass('center-button');
        $this->btnGoToCategories->CausesValidation = false;
        $this->btnGoToCategories->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToCategories_Click'));

        $this->btnGoToSettings = new Bs\Button($this);
        $this->btnGoToSettings->Tip = true;
        $this->btnGoToSettings->ToolTip = t('Go to news settings manager');
        $this->btnGoToSettings->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToSettings->CssClass = 'btn btn-default';
        $this->btnGoToSettings->setCssStyle('float', 'right');
        $this->btnGoToSettings->addWrapperCssClass('center-button');
        $this->btnGoToSettings->CausesValidation = false;
        $this->btnGoToSettings->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToSettings_Click'));
    }

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
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the news title must exist!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Well done!</strong> The publication date for this post has been saved or changed.');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('<strong>Well done!</strong> The expiration date for this post has been saved or changed.');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this date \"Available from\" does not exist.</p>Please enter at least the date of publication!');
        $this->dlgToastr5->ProgressBar = true;
        $this->dlgToastr5->TimeOut = 10000;
        $this->dlgToastr5->EscapeHtml = false;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('<p style=\"margin-bottom: 2px;\">Start date must be smaller then end date!</p><strong>Try to do it right again!</strong>');
        $this->dlgToastr6->ProgressBar = true;
        $this->dlgToastr6->TimeOut = 10000;
        $this->dlgToastr6->EscapeHtml = false;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('Publication date have been canceled.');
        $this->dlgToastr7->ProgressBar = true;

        $this->dlgToastr8 = new Q\Plugin\Toastr($this);
        $this->dlgToastr8->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr8->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr8->Message = t('Expiration date have been canceled.');
        $this->dlgToastr8->ProgressBar = true;

        $this->dlgToastr9 = new Q\Plugin\Toastr($this);
        $this->dlgToastr9->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr9->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr9->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this date \"Available from\" does not exist.</p><strong>Re-enter publication date and expiration date!</strong>');
        $this->dlgToastr9->ProgressBar = true;
        $this->dlgToastr9->TimeOut = 10000;
        $this->dlgToastr9->EscapeHtml = false;

        $this->dlgToastr10 = new Q\Plugin\Toastr($this);
        $this->dlgToastr10->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr10->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr10->Message = t('<strong>Well done!</strong> The message has been sent to the editor-in-chief of the site for review, correction or approval!');
        $this->dlgToastr10->ProgressBar = true;
        $this->dlgToastr10->TimeOut = 10000;

        $this->dlgToastr11 = new Q\Plugin\Toastr($this);
        $this->dlgToastr11->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr11->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr11->Message = t('<strong>Well done!</strong> A message has been sent to the editor-in-chief of the site to cancel the confirmation!');
        $this->dlgToastr11->ProgressBar = true;
        $this->dlgToastr11->TimeOut = 10000;

        $this->dlgToastr12 = new Q\Plugin\Toastr($this);
        $this->dlgToastr12->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr12->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr12->Message = t('<strong>Sorry</strong>, failed to save or edit post!');
        $this->dlgToastr12->ProgressBar = true;

        $this->dlgToastr13 = new Q\Plugin\Toastr($this);
        $this->dlgToastr13->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr13->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr13->Message = t('<strong>Well done!</strong> Updates to some records for this post were discarded, and the record has been restored!');
        $this->dlgToastr13->ProgressBar = true;
    }

    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete this news?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">There is nothing to delete here!
                                Please use the "Cancel" button to access the news list!</p>');
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Are you sure you want to cancel the publication date for this post?</p>');
        $this->dlgModal3->Title = t('Warning');
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('cancelItem_Click'));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move this news from this newsgroup to another newsgroup?</p>
                                ');
        $this->dlgModal4->Title = t('Warning');
        $this->dlgModal4->HeaderClasses = 'btn-danger';
        $this->dlgModal4->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal4->addCloseButton(t("I'll cancel"));
        $this->dlgModal4->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('moveItem_Click'));

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this news or edit it again?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this news public again later!</p>');
        $this->dlgModal5->Title = t('Question');
        $this->dlgModal5->HeaderClasses = 'btn-danger';
        $this->dlgModal5->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal5->addCloseButton(t("I'll cancel"));
        $this->dlgModal5->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('statusItem_Click'));
        $this->dlgModal5->addAction(new Bs\Event\ModalHidden(), new Q\Action\Ajax('hideItem_Click'));

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Title = t("Success");
        $this->dlgModal6->HeaderClasses = 'btn-success';
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news is now hidden!</p>');
        $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Title = t("Success");
        $this->dlgModal7->HeaderClasses = 'btn-success';
        $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news has now been made public!</p>');
        $this->dlgModal7->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal8 = new Bs\Modal($this);
        $this->dlgModal8->Title = t("Success");
        $this->dlgModal8->HeaderClasses = 'btn-success';
        $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news is now a draft!</p>');
        $this->dlgModal8->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function lstChanges_GetItems() {
        $a = array();
        $objCondition = $this->objChangesCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objChangesCursor = NewsChanges::queryCursor($objCondition, $this->objChangesClauses);

        // Iterate through the Cursor
        while ($objChanges = NewsChanges::instantiateCursor($objChangesCursor)) {
            $objListItem = new ListItem($objChanges->__toString(), $objChanges->Id);
            if (($this->objNews->Changes) && ($this->objNews->Changes->Id == $objChanges->Id))
                $objListItem->Selected = true;

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objChanges->Status == 2) {
                $objListItem->Disabled = true;
            }
            $a[] = $objListItem;
        }
        return $a;
    }

    public function lstCategory_GetItems()
    {
        $a = array();
        $objCondition = $this->objNewsCategoryCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objNewsCategoryCursor = CategoryOfNews::queryCursor($objCondition, $this->objNewsCategoryClauses);

        // Iterate through the Cursor
        while ($objNewsCategory = CategoryOfNews::instantiateCursor($objNewsCategoryCursor)) {
            $objListItem = new ListItem($objNewsCategory->__toString(), $objNewsCategory->Id);
            if (($this->objNews->NewsCategory) && ($this->objNews->NewsCategory->Id == $objNewsCategory->Id))
                $objListItem->Selected = true;

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objNewsCategory->IsEnabled == 2) {
                $objListItem->Disabled = true;
            }
            $a[] = $objListItem;
        }
        return $a;
    }

    protected function lstChanges_Change(ActionParams $params)
    {
        if ($this->lstChanges->SelectedValue !== null) {
            $this->objNews->setChangesId($this->lstChanges->SelectedValue);
        } else {
            $this->objNews->setChangesId(null);
        }

        $this->objNews->save();
        $this->dlgToastr1->notify();

        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());

        $this->refreshDisplay();
    }

    protected function lstNewsCategory_Change()
    {
        if ($this->lstNewsCategory->SelectedValue !== null) {
            $this->objNews->setNewsCategoryId($this->lstNewsCategory->SelectedValue);
            $this->objNews->setCategory(News::loadByIdFromCategory($this->lstNewsCategory->SelectedValue));
        } else {
            $this->objNews->setNewsCategoryId(null);
            $this->objNews->setCategory(null);
        }

        $this->objNews->save();
        $this->dlgToastr1->notify();

        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());

        $this->refreshDisplay();
    }

    protected  function lstStatus_Change()
    {
        if ($this->objNews->getStatus() === 1) {
            $this->dlgModal5->showDialogBox();
        } else {
            $this->lockInputFields();
        }
    }

    protected function statusItem_Click(ActionParams $params)
    {
        if ($this->lstStatus->SelectedValue === 2) {
            $this->dlgModal5->hideDialogBox();
            $this->objNews->setStatus(2);
        } else if ($this->lstStatus->SelectedValue === 3){
            $this->dlgModal5->hideDialogBox();
            $this->objNews->setStatus(3);
        }

        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());

        $this->refreshDisplay();
    }

    protected function lockInputFields()
    {
        if ($this->lstStatus->SelectedValue === 1) {
            $this->objNews->setStatus(1);
            $this->dlgModal7->showDialogBox();
        } else if ($this->lstStatus->SelectedValue === 2) {
            $this->objNews->setStatus(2);
            $this->dlgModal6->showDialogBox();
        } else if ($this->lstStatus->SelectedValue === 3) {
            $this->objNews->setStatus(3);
            $this->dlgModal8->showDialogBox();
        }

        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());

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
        $this->lstStatus->SelectedValue = $this->objNews->getStatus();
    }

    protected function lstGroupTitle_Change(ActionParams $params)
    {
        if ($this->lstGroupTitle->SelectedValue !== $this->objNews->getNewsGroupTitleId()) {
            $this->dlgModal4->showDialogBox();
        }
    }

    protected function moveItem_Click(ActionParams $params)
    {
        $this->dlgModal4->hideDialogBox();

        $objGroupTitle = NewsSettings::loadById($this->lstGroupTitle->SelectedValue);

        // Before proceeding to other activities, the initial data of the "news" and "title_of_newsgroup" tables must be fixed.
        $objLockedGroup = $this->objNews->getNewsGroupTitleId();
        $objTargetGroup = NewsSettings::loadById($this->lstGroupTitle->SelectedValue);

        $currentCount = News::countByNewsGroupTitleId($objLockedGroup);
        $nextCount = News::countByNewsGroupTitleId($objTargetGroup->getId());

        // Here you must first check the lock status of the next folder, to do this check.
        $objNewsGroup = NewsSettings::loadById($objTargetGroup->getId());

        if ($nextCount == 0) {
            $objNewsGroup->setNewsLocked(1);
            $objNewsGroup->save();
        }

        // Next, we check the lock status of the previous folder, to do this, check...
        $objNewsGroup = NewsSettings::loadById($objLockedGroup);

        if ($currentCount) {
            if ($currentCount == 1) {
                $objNewsGroup->setNewsLocked(0);
            } else {
                $objNewsGroup->setNewsLocked(1);
            }
            $objNewsGroup->save();
        }

        $objBeforeNewsSlug = News::load($this->objNews->getId());
        $beforeSlug = $objBeforeNewsSlug->getTitleSlug();

        $find = $objNewsGroup->getTitleSlug();
        $replace = $objTargetGroup->getTitleSlug();

        $this->objFrontendLinks->setGroupedId($objGroupTitle->getNewsGroupId());
        $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
        $this->objFrontendLinks->setFrontendTitleSlug(str_replace($find, $replace, $this->objFrontendLinks->getFrontendTitleSlug()));
        $this->objFrontendLinks->save();

        $this->objNews->setTitle($this->txtTitle->Text);
        $this->objNews->setNewsGroupId($objGroupTitle->getNewsGroupId());
        $this->objNews->setNewsGroupTitleId($this->lstGroupTitle->SelectedValue);
        $this->objNews->setGroupTitle($this->lstGroupTitle->SelectedName);
        $this->objNews->setTitleSlug(str_replace($find, $replace, $this->objFrontendLinks->getFrontendTitleSlug()));
        $this->objNews->saveNews($this->txtTitle->Text, $objGroupTitle->getTitleSlug());
        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        // We are updating the slug
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
            '/' . $replace . '/' . QString::sanitizeForUrl($this->objNews->getTitle());
        $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
        $this->txtTitleSlug->HtmlEntities = false;
        $this->txtTitleSlug->setCssStyle('font-weight', 400);

        Application::redirect('news_edit.php?id=' . $this->objNews->getId() . '&group=' . $objGroupTitle->getNewsGroupId());

        ///////////////////////////////////////////////////////////////////////////////////////////

        // Since we are using news transfer from one group to another and we need to refresh the page with Application::redirect(),
        // we can't report on the success of the news transfer, so let these Toasts remain as they are...

        $objAfterNewsSlug = News::load($this->objNews->getId());
        $afterSlug = $objAfterNewsSlug->getTitleSlug();

        if ($beforeSlug !== $afterSlug) {
            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr12->notify();
        }
    }

    protected function deleteItem_Click(ActionParams $params)
    {
        $objNewsSettings = NewsSettings::loadByIdFromNewsSettings($this->intGroup);
        $objNewsCount = count(News::loadArrayByNewsGroupId($this->intGroup));

        if ($params->ActionParameter == "pass") {
            if ($this->objNews->getPictureId()) {
                $objFiles = Files::loadById($this->objNews->getPictureId());

                if ($objFiles->getLockedFile() !== 0) {
                    $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                    $objFiles->save();
                }
            }

            if ($this->objNews->getFilesIds()) {
                $references = $this->objNews->getFilesIds();

                // The string must be converted to an array
                $nativeFilesIds = [];
                $updatedFilesIds = explode(',', $references);

                foreach ($updatedFilesIds as $filesId) {
                    $nativeFilesIds[] = $filesId;
                }

                foreach ($nativeFilesIds as $value) {
                    $lockedFile = Files::loadById($value);
                    $lockedFile->setLockedFile($lockedFile->getLockedFile() - 1);
                    $lockedFile->save();
                }
            }

            $this->objNews->unassociateAllUsersAsEditors();
            $this->objNews->delete();
            $this->objFrontendLinks->delete();

            If ($objNewsCount === 1) {
                $objNewGroup = NewsSettings::loadById($objNewsSettings[0]->getId());
                $objNewGroup->setNewsLocked(0);
                $objNewGroup->save();
            }

            $this->redirectToListPage();
        }
        $this->dlgModal1->hideDialogBox();
    }

    protected function cancelItem_Click(ActionParams $params)
    {
        if ($params->ActionParameter == "pass") {

            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = true;

            $this->lstStatus->SelectedValue = 2;
            $this->calAvailableFrom->Text = null;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);

            $this->objNews->save();

            $this->redirectToListPage();
        } else {
            $this->dlgModal3->hideDialogBox();
            $this->calAvailableFrom->focus();
        }
    }

    public function setUse_PublicationDate(ActionParams $params)
    {
        if ($this->chkUsePublicationDate->Checked) {
            $this->lblAvailableFrom->Display = true;
            $this->calAvailableFrom->Display = true;
            $this->lblExpiryDate->Display = true;
            $this->calExpiryDate->Display = true;

            $this->lstStatus->Enabled = false;
            $this->lstStatus->SelectedValue = null;

            $this->objNews->setUsePublicationDate(1);
            $this->objNews->setStatus(4);
            $this->calAvailableFrom->focus();
        } else {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = 2;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);
            $this->objNews->setAvailableFrom(null);
            $this->objNews->setExpiryDate(null);

            $this->dlgToastr7->notify();
        }

        if ($this->calAvailableFrom->Language && $this->calExpiryDate->Language) {
            $this->calAvailableFrom->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/locales/bootstrap-datetimepicker.et.js");
            $this->calExpiryDate->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/locales/bootstrap-datetimepicker.et.js");
        }

        $this->renderActionsWithId();

        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
    }

    protected function setDate_AvailableFrom(ActionParams $params)
    {
        if ($this->calAvailableFrom->Text) {
            $this->objNews->setAvailableFrom($this->calAvailableFrom->DateTime);

            $this->dlgToastr3->notify();
        } else {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = 2;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);
            $this->objNews->setAvailableFrom(null);
            $this->objNews->setExpiryDate(null);

            $this->dlgToastr7->notify();
        }
        $this->renderActionsWithId();

        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
    }

    protected function setDate_ExpiryDate(ActionParams $params)
    {
        if ($this->calAvailableFrom->Text && $this->calExpiryDate->Text) {
            if (new DateTime($this->calAvailableFrom->Text) > new DateTime($this->calExpiryDate->Text)) {
                $this->calExpiryDate->Text = null;
                $this->objNews->setExpiryDate(null);

                $this->dlgToastr6->notify();
            } else if ($this->calExpiryDate->Text) {
                $this->objNews->setExpiryDate($this->calExpiryDate->DateTime);

                $this->dlgToastr4->notify();
            } else {
                $this->calExpiryDate->Text = null;
                $this->objNews->setExpiryDate(null);

                $this->dlgToastr8->notify();
            }
        } else if ($this->calAvailableFrom->Text && !$this->calExpiryDate->Text) {
            $this->calExpiryDate->Text = null;
            $this->objNews->setExpiryDate(null);

            $this->dlgToastr8->notify();
        } else {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = 2;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);
            $this->objNews->setAvailableFrom(null);
            $this->objNews->setExpiryDate(null);

            $this->dlgToastr9->notify();
        }

        $this->renderActionsWithId();

        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
    }

    protected function gettingConfirmation_Click(ActionParams $params)
    {
        // Add the code to send the message here.
        // Options to do this are left to the developer.
        //
        // Note that a proper solution must be considered here.
        // If the editor-in-chief needs to be reviewed, he should not receive messages...

        if ($this->chkConfirmationAsking->Checked) {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = false;
            $this->chkUsePublicationDate->Enabled = false;
            $this->lstStatus->SelectedValue = 2;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);
            $this->objNews->setAvailableFrom(null);
            $this->objNews->setExpiryDate(null);
            $this->objNews->setConfirmationAsking(1);

            $this->dlgToastr10->notify();
        } else {
            $this->objNews->setConfirmationAsking(0);
            $this->lstStatus->Enabled = true;
            $this->chkUsePublicationDate->Enabled = true;

            $this->dlgToastr11->notify();
        }

        $this->renderActionsWithId();

        $this->objNews->save();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function imageSave_Push(ActionParams $params)
    {
        $saveId = $this->objMediaFinder->Item;
        $objFiles = Files::loadById($saveId);

        if ($objFiles->getLockedFile() == 0) {
            $objFiles->setLockedFile($objFiles->getLockedFile() + 1);
            $objFiles->save();
        }

        $this->objNews->setPictureId($saveId);
        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        $this->lblPictureDescription->Display = true;
        $this->txtPictureDescription->Display = true;
        $this->lblAuthorSource->Display = true;
        $this->txtAuthorSource->Display = true;

        $this->dlgToastr1->notify();
    }

    protected function imageDelete_Push(ActionParams $params)
    {
        $objFiles = Files::loadById($this->objNews->getPictureId());

        if ($objFiles->getLockedFile() !== 0) {
            $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
            $objFiles->save();
        }

        $this->objNews->setPictureId(null);
        $this->objNews->setPictureDescription(null);
        $this->objNews->setAuthorSource(null);
        $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
        $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objNews->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        $this->lblPictureDescription->Display = false;
        $this->txtPictureDescription->Display = false;
        $this->lblAuthorSource->Display = false;
        $this->txtAuthorSource->Display = false;

        $this->txtPictureDescription->Text = null;
        $this->txtAuthorSource->Text = null;

        $this->dlgToastr1->notify();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnSave_Click(ActionParams $params)
    {
        $objTemplateLocking = FrontendTemplateLocking::load(4);
        $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->getId());

        $this->renderActionsWithId();

        if ($this->txtTitle->Text) {
            $this->objNews->setNewsGroupId($this->intGroup);
            $this->objNews->setTitle($this->txtTitle->Text);
            $this->objNews->setContent($this->txtContent->Text);
            $this->objNews->updateNews($this->txtTitle->Text, $this->objNewsSettings->getTitleSlug());

            if ($this->chkUsePublicationDate->Checked == false) {
                $this->objNews->setStatus($this->lstStatus->SelectedValue);
            }

            if ($this->chkUsePublicationDate->Checked == true && $this->calAvailableFrom->Text == null) {
                $this->chkUsePublicationDate->Checked = false;
                $this->lblAvailableFrom->Display = false;
                $this->calAvailableFrom->Display = false;
                $this->lblExpiryDate->Display = false;
                $this->calExpiryDate->Display = false;
                $this->lstStatus->Enabled = true;

                $this->lstStatus->SelectedValue = 2;
                $this->calAvailableFrom->Text = null;

                $this->objNews->setUsePublicationDate(0);
                $this->objNews->setStatus(2);

                $this->dlgToastr5->notify();
            }

            if ($this->txtTitle->Text) {
                $this->objNews->setPictureDescription($this->txtPictureDescription->Text);
                $this->objNews->setAuthorSource($this->txtAuthorSource->Text);
            } else {
                $this->objNews->setPictureDescription(null);
                $this->objNews->setAuthorSource(null);
            }

            $this->objNews->save();

            $this->objFrontendLinks->setLinkedId($this->intId);
            $this->objFrontendLinks->setGroupedId($this->intGroup);
            $this->objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
            $this->objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
            $this->objFrontendLinks->setContentTypesManagamentId(4);
            $this->objFrontendLinks->setFrontendTitleSlug($this->objNews->getTitleSlug());
            $this->objFrontendLinks->setIsActivated(1);
            $this->objFrontendLinks->save();

            $this->referenceValidation();

            $this->txtNewsAuthor->Text = $this->objNews->getAuthor();

            if ($this->objNews->getTitle()) {
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objNews->getTitleSlug();
                $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();

           if ($this->objNews->getContent()) {
                $strUpdate_translate = t('Update');
                $strUpdateAndClose_translate = t('Update and close');
                Application::executeJavaScript(sprintf("jQuery($this->strSaveButtonId).text('{$strUpdate_translate}');"));
                Application::executeJavaScript(sprintf("jQuery($this->strSavingButtonId).text('{$strUpdateAndClose_translate}');"));
            } else {
                $strSave_translate = t('Save');
                $strSaveAndClose_translate = t('Save and close');
                Application::executeJavaScript(sprintf("jQuery($this->strSaveButtonId).text('{$strSave_translate}');"));
                Application::executeJavaScript(sprintf("jQuery($this->strSavingButtonId).text('{$strSaveAndClose_translate}');"));
            }

            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    public function btnSaveClose_Click(ActionParams $params)
    {
        $objTemplateLocking = FrontendTemplateLocking::load(4);
        $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->getId());

        $this->renderActionsWithId();

        if ($this->txtTitle->Text) {

            $this->objNews->setNewsGroupId($this->intGroup);
            $this->objNews->setTitle($this->txtTitle->Text);
            $this->objNews->setContent($this->txtContent->Text);
            $this->objNews->updateNews($this->txtTitle->Text, $this->objNewsSettings->getTitleSlug());

            if ($this->chkUsePublicationDate->Checked == false) {
                $this->objNews->setStatus($this->lstStatus->SelectedValue);
            }

            if ($this->txtTitle->Text) {
                $this->objNews->setPictureDescription($this->txtPictureDescription->Text);
                $this->objNews->setAuthorSource($this->txtAuthorSource->Text);
            } else {
                $this->objNews->setPictureDescription(null);
                $this->objNews->setAuthorSource(null);
            }

            $this->objNews->save();

            $this->objFrontendLinks->setLinkedId($this->intId);
            $this->objFrontendLinks->setGroupedId($this->intGroup);
            $this->objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
            $this->objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
            $this->objFrontendLinks->setContentTypesManagamentId(4);
            $this->objFrontendLinks->setFrontendTitleSlug($this->objNews->getTitleSlug());
            $this->objFrontendLinks->setIsActivated(1);
            $this->objFrontendLinks->save();

            $this->referenceValidation();

            if ($this->chkUsePublicationDate->Checked == true && $this->calAvailableFrom->Text == null) {
                $this->dlgModal3->showDialogBox();
            } else {
                $this->redirectToListPage();
            }

            $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the escape click action to cancel current article edits and reset form fields.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        $objCancel = $this->objNews->getId();

        // Check if $objCancel is available
        if ($objCancel) {
            $this->dlgToastr13->notify();
        }

        $this->txtTitle->Text = $this->objNews->getTitle();
        $this->txtContent->Text = $this->objNews->getContent();
        $this->txtPictureDescription->Text = $this->objNews->getPictureDescription();
        $this->txtAuthorSource->Text = $this->objNews->getAuthorSource();
        $this->txtPictureDescription->Text = $this->objNews->getPictureDescription();
        $this->txtAuthorSource->Text = $this->objNews->getAuthor();
    }

    protected function refreshDisplay()
    {
        if ($this->objNews->getPostDate() &&
            !$this->objNews->getPostUpdateDate() &&
            $this->objNews->getAuthor() &&
            !$this->objNews->countUsersAsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblNewsAuthor->Display = true;
            $this->txtNewsAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
            $this->calPostDate->addCssClass('form-control-remove');
        }

        if ($this->objNews->getPostDate() &&
            $this->objNews->getPostUpdateDate() &&
            $this->objNews->getAuthor() &&
            !$this->objNews->countUsersAsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblNewsAuthor->Display = true;
            $this->txtNewsAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
            $this->calPostDate->removeCssClass('form-control-remove');
        }

        if ($this->objNews->getPostDate() &&
            $this->objNews->getPostUpdateDate() &&
            $this->objNews->getAuthor() &&
            $this->objNews->countUsersAsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblNewsAuthor->Display = true;
            $this->txtNewsAuthor->Display = true;
            $this->lblUsersAsEditors->Display = true;
            $this->txtUsersAsEditors->Display = true;
            $this->txtUsersAsEditors->addCssClass('form-control-add');
        }
    }

    public function renderActionsWithId()
    {
        if (!empty($this->intId)) {
            if ($this->txtTitle->Text !== $this->objNews->getTitle() ||
                $this->lstNewsCategory->SelectedValue !== $this->objNews->getNewsCategoryId() ||
                $this->lstGroupTitle->SelectedValue !== $this->objNews->getNewsGroupTitleId() ||
                $this->txtContent->Text !== $this->objNews->getContent() ||
                $this->objOldPicture !== $this->objNews->getPictureId() ||
                $this->txtPictureDescription->Text !== $this->objNews->getPictureDescription() ||
                $this->txtAuthorSource->Text !== $this->objNews->getAuthorSource() ||
                $this->lstStatus->SelectedValue !== $this->objNews->getStatus() ||
                $this->chkUsePublicationDate->Checked !== $this->objNews->getUsePublicationDate() ||
                $this->calAvailableFrom->Text !== $this->objNews->getAvailableFrom() ||
                $this->calExpiryDate->Text !== $this->objNews->getExpiryDate() ||
                $this->chkConfirmationAsking->Checked !== $this->objNews->getConfirmationAsking()
            ) {
                $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
                $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objNews->save();

                $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
                $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            }
        }
    }

    public function btnDelete_Click(ActionParams $params)
    {
        if ($this->objNews->getTitle()) {
            $this->dlgModal1->showDialogBox();
        } else {
            $this->dlgModal2->showDialogBox();
        }
    }

    // This function referenceValidation(), which checks and ensures that the data is up-to-date both when adding and
    // deleting a file. Everything is commented in the code.

    protected function referenceValidation()
    {
        $objNews = News::loadById($this->objNews->getId());

        $references = $objNews->getFilesIds();
        $content = $objNews->getContent();

        // Regular expression to find the img id attribute
        $patternImgId = '/<img[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

        // Regular expression to find the a id attribute
        $patternAId = $patternAId = '/<a[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

        $matchesImg = [];
        $matchesA = [];
        $combinedArray = [];

        if (!empty($content)) {
            // Search for a pattern
            preg_match_all($patternImgId, $content, $matchesImg);
            preg_match_all($patternAId, $content, $matchesA);

            // Check if matches were found and process only if desired
            $imgIds = isset($matchesImg[1]) ? $matchesImg[1] : [];
            $aIds = isset($matchesA[1]) ? $matchesA[1] : [];

            // Merge arrays into one
            $combinedArray = array_merge($imgIds, $aIds);

        }

        if (empty($references)) {
            $saveFilesIds = implode(',', $combinedArray);
            $objNews->setFilesIds($saveFilesIds);
            $objNews->save();

            foreach ($combinedArray as $value) {
                $lockedFile = Files::loadById($value);
                $lockedFile->setLockedFile($lockedFile->getLockedFile() + 1);
                $lockedFile->save();
            }
        } else {
            // The string must be converted to an array
            $nativeFilesIds = [];
            $updatedFilesIds = explode(',', $references);
            foreach ($updatedFilesIds as $filesId) {
                $nativeFilesIds[] = $filesId;
            }

            // Equal values are proven
            $result = array_intersect($combinedArray, $nativeFilesIds);

            // Content has more ids than FilesIds less references. TULEMUS: test 1 annab vastuse 1124, test 2 tühja massiivi
            // Then call back to FileHandler to lock that file (+ 1 ).
            $lockFiles = array_diff($combinedArray, $nativeFilesIds);

            // Content has fewer IDs than FilesIds, has more references. TULEMUS: test 1 annab tühja massiivi, test 2 annab vastuse
            // Then call back to FileHandler to unclog that file ( - 1 ).
            $unlockFiles = array_diff($nativeFilesIds, $combinedArray);

            if (count($lockFiles)) {
                foreach ($lockFiles as $value) {
                    $lockedFile = Files::loadById($value);
                    $lockedFile->setLockedFile($lockedFile->getLockedFile() + 1);
                    $lockedFile->save();
                }

                // Overwriting example data
                $updatedFilesIds = implode(',', $combinedArray);
                $objNews->setFilesIds($updatedFilesIds);
                $objNews->save();
            }

            if (count($unlockFiles)) {
                foreach ($unlockFiles as $value) {
                    $unlockFile = Files::loadById($value);
                    $unlockFile->setLockedFile($unlockFile->getLockedFile() - 1);
                    $unlockFile->save();
                }

                // Overwriting example data
                $updatedFilesIds = implode(',', $combinedArray);
                $objNews->setFilesIds($updatedFilesIds);
                $objNews->save();
            }
        }
    }

    public function btnCancel_Click(ActionParams $params)
    {
        if (!$this->objNews->getTitle()) {
            $this->objNews->unassociateAllUsersAsEditors();
            $this->objNews->delete();
            $this->objFrontendLinks->delete();
        }
        $this->redirectToListPage();
    }

    public function btnGoToChanges_Click(ActionParams $params)
    {
        $_SESSION['news_changes_id'] = $this->intId;
        $_SESSION['news_changes_group'] = $this->intGroup;
        Application::redirect('categories_manager.php#newsChanges_tab');
    }

    public function btnGoToCategories_Click(ActionParams $params)
    {
        $_SESSION['news_categories_id'] = $this->intId;
        $_SESSION['news_categories_group'] = $this->intGroup;
        Application::redirect('categories_manager.php#newsCategories_tab');
    }

    public function btnGoToSettings_Click(ActionParams $params)
    {
        $_SESSION['news_settings_id'] = $this->intId;
        $_SESSION['news_settings_group'] = $this->intGroup;
        Application::redirect('settings_manager.php#newsSettings_tab');
    }

    protected function redirectToListPage()
    {
        Application::redirect('news_list.php');
    }

}
SampleForm::run('SampleForm');