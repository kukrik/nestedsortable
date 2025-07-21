<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;
use QCubed\QString;
use QCubed\Css;
use QCubed\Js;

class ArticleEditPanel extends Q\Control\Panel
{
    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;
    public $dlgModal6;
    public $dlgModal7;

    public $dlgToastr1;
    public $dlgToastr2;
    public $dlgToastr3;
    public $dlgToastr4;
    public $dlgToastr5;

    public $lblExistingMenuText;
    public $txtExistingMenuText;

    public $lblMenuText;
    public $txtMenuText;

    public $lblTitle;
    public $txtTitle;

    public $lblCategory;
    public $lstCategory;

    public $lblTitleSlug;
    public $txtTitleSlug;

    public $txtContent;

    public $lblPostDate;
    public $calPostDate;

    public $lblPostUpdateDate;
    public $calPostUpdateDate;

    public $lblAuthor;
    public $txtAuthor;

    public $lblUsersAsArticlesEditors;
    public $txtUsersAsArticlesEditors;

    public $objMediaFinder;

    public $lblPictureDescription;
    public $txtPictureDescription;

    public $lblAuthorSource;
    public $txtAuthorSource;

    public $lblStatus;
    public $lstStatus;

    public $lblConfirmationAsking;
    public $chkConfirmationAsking;

    public $btnSave;
    public $btnSaving;
    public $btnCancel;
    public $btnGoToArticleCategroy;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objMenu;
    protected $objMenuContent;
    protected $objFrontendLinks;
    protected $objArticle;
    protected $objMetadata;
    protected $objUser;

    protected $intLoggedUserId;
    protected $objOldPicture;
    protected $updateSlug;

    protected $objCategoryCondition;
    protected $objCategoryClauses;

    protected $strTemplate = 'ArticleEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        // Deleting sessions, if any.
        if (!empty($_SESSION['article'])) {
            unset($_SESSION['article']);
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenu = Menu::load($this->intId);
        $this->objMenuContent = MenuContent::load($this->intId);
        $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);
        $this->objArticle = Article::loadByIdFromContentId($this->intId);
        $this->objMetadata = Metadata::loadByIdFromMetadata($this->intId);

        $this->objOldPicture = $this->objArticle->getPictureId();

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
        $this->intLoggedUserId = 3;

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and configures the input controls and labels for editing menu and article details.
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
        $this->txtMenuText->setHtmlAttribute('required', 'required');
        $this->txtMenuText->Enabled = false;

        $this->lblTitle = new Q\Plugin\Control\Label($this);
        $this->lblTitle->Text = t('Title');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 400);
        $this->lblTitle->Required = true;

        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Title');
        $this->txtTitle->Text = $this->objArticle->Title;
        $this->txtTitle->addWrapperCssClass('center-button');
        $this->txtTitle->MaxLength = Article::TitleMaxLength;
        $this->txtTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'itemEscape_Click'));
        $this->txtTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtTitle->setHtmlAttribute('required', 'required');

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
        $this->lstCategory->SelectedValue = $this->objArticle->CategoryId;
        $this->lstCategory->AddAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstCategory_Change'));

        if (CategoryOfArticle::countAll() == 0 || CategoryOfArticle::countAll() == CategoryOfArticle::countByIsEnabled(2)) {
            $this->lstCategory->Enabled = false;
        } else {
            $this->lstCategory->Enabled = true;
        }

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        if ($this->txtTitle->Text) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX . $this->objArticle->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
            $this->txtTitleSlug->setCssStyle('float', 'left');
        } else {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->txtContent = new Q\Plugin\CKEditor($this);
        $this->txtContent->Text = $this->objArticle->Content;
        $this->txtContent->Configuration = 'ckConfig';
//        $this->txtContent->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
//        $this->txtContent->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
//        $this->txtContent->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'itemEscape_Click'));
//        $this->txtContent->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objArticle->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objArticle->PostUpdateDate ? $this->objArticle->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->setCssStyle('font-weight', 'bold');

        $this->txtAuthor  = new Bs\Label($this);
        $this->txtAuthor->Text = $this->objArticle->Author;

        $this->lblUsersAsArticlesEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsArticlesEditors->Text = t('Editors');
        $this->lblUsersAsArticlesEditors->setCssStyle('font-weight', 'bold');

        $this->txtUsersAsArticlesEditors = new Bs\Label($this);
        $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());
        $this->txtUsersAsArticlesEditors->setCssStyle('font-weight', 'normal');

        $this->refreshDisplay();

        $this->objMediaFinder = new Q\Plugin\MediaFinder($this);
        $this->objMediaFinder->TempUrl = APP_UPLOADS_TEMP_URL . "/_files/thumbnail";
        $this->objMediaFinder->PopupUrl = QCUBED_FILEMANAGER_URL . "/examples/finder.php";
        $this->objMediaFinder->EmptyImageAlt = t("Choose a picture");
        $this->objMediaFinder->SelectedImageAlt = t("Selected picture");

        $this->objMediaFinder->SelectedImageId = $this->objArticle->getPictureId() ? $this->objArticle->getPictureId() : null;

        if ($this->objMediaFinder->SelectedImageId !== null) {
            $objFiles = Files::loadById($this->objMediaFinder->SelectedImageId);
            $this->objMediaFinder->SelectedImagePath = $this->objMediaFinder->TempUrl . $objFiles->getPath();;
            $this->objMediaFinder->SelectedImageName = $objFiles->getName();
        }

        $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageSave(), new Q\Action\AjaxControl($this, 'imageSave_Push'));
        $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageDelete(), new Q\Action\AjaxControl($this, 'imageDelete_Push'));


        $this->lblPictureDescription = new Q\Plugin\Control\Label($this);
        $this->lblPictureDescription->Text = t('Picture description');
        $this->lblPictureDescription->setCssStyle('font-weight', 'bold');

        $this->txtPictureDescription = new Bs\TextBox($this);
        $this->txtPictureDescription->Text = $this->objArticle->PictureDescription ? $this->objArticle->PictureDescription : null;
        $this->txtPictureDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtPictureDescription->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtPictureDescription->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtPictureDescription->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'itemEscape_Click'));
        $this->txtPictureDescription->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblAuthorSource = new Q\Plugin\Control\Label($this);
        $this->lblAuthorSource->Text = t('Author/source');
        $this->lblAuthorSource->setCssStyle('font-weight', 'bold');

        $this->txtAuthorSource = new Bs\TextBox($this);
        $this->txtAuthorSource->Text = $this->objArticle->AuthorSource ? $this->objArticle->AuthorSource : null;
        $this->txtAuthorSource->MaxLength = Article::AuthorSourceMaxLength;
        $this->txtAuthorSource->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtAuthorSource->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtAuthorSource->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'itemEscape_Click'));
        $this->txtAuthorSource->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        if (!$this->objArticle->PictureId) {
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
        $this->lstStatus->SelectedValue = $this->objMenuContent->IsEnabled;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->AddAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstStatus_Change'));

        $this->lblConfirmationAsking = new Q\Plugin\Control\Label($this);
        $this->lblConfirmationAsking->Text = t('Confirmation of publication');
        $this->lblConfirmationAsking->setCssStyle('font-weight', 'bold');

        $this->chkConfirmationAsking = new Q\Plugin\Control\Checkbox($this);
        $this->chkConfirmationAsking->Checked = $this->objArticle->ConfirmationAsking;
        $this->chkConfirmationAsking->WrapperClass = 'checkbox checkbox-orange';
        $this->chkConfirmationAsking->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'gettingConfirmation_Click'));
    }

    /**
     * Creates and configures the buttons for menu operations such as save, save and close, cancel, and navigation to article categories.
     *
     * @return void
     */
    public function CreateButtons()
    {
        $this->btnSave = new Bs\Button($this);
        if ($this->objArticle->getContent()) {
            $this->btnSave->Text = t('Update');
        } else {
            $this->btnSave->Text = t('Save');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSaveButtonId = $this->btnSave->ControlId;

        $this->btnSaving = new Bs\Button($this);
        if ($this->objArticle->getContent()) {
            $this->btnSaving->Text = t('Update and close');
        } else {
            $this->btnSaving->Text = t('Save and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Back');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));

        $this->btnGoToArticleCategroy = new Bs\Button($this);
        $this->btnGoToArticleCategroy->Tip = true;
        $this->btnGoToArticleCategroy->ToolTip = t('Go to article categories manager');
        $this->btnGoToArticleCategroy->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToArticleCategroy->CssClass = 'btn btn-default';
        $this->btnGoToArticleCategroy->setCssStyle('float', 'right');
        $this->btnGoToArticleCategroy->addWrapperCssClass('center-button');
        $this->btnGoToArticleCategroy->CausesValidation = false;
        $this->btnGoToArticleCategroy->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToArticleCategroy_Click'));
    }

    /**
     * Creates and configures various Toastr notifications for different alert types and scenarios.
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
        $this->dlgToastr2->Message = t('The menu title or content title must exist!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Well done!</strong> The message has been sent to the editor-in-chief of the site for review, correction or approval!');
        $this->dlgToastr3->ProgressBar = true;
        $this->dlgToastr3->TimeOut = 10000;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('<strong>Well done!</strong> A message has been sent to the editor-in-chief of the site to cancel the confirmation!');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('<strong>Well done!</strong> Updates to some records for this post were discarded, and the record has been restored!');
        $this->dlgToastr5->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates multiple modal dialogs with predefined properties like text, title, and buttons.
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
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this article or edit it again?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this article public again later!</p>');
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
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This article is now hidden!</p>');
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This article has now been made public!</p>');
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Title = t("Success");
        $this->dlgModal6->HeaderClasses = 'btn-success';
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This article is now a draft!</p>');
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
     * Handles the 'Click' event for getting a confirmation. Depending on the state
     * of the confirmation checkbox, it adjusts the status, updates menu content
     * and article properties, notifies the user using toastr, and saves the changes.
     *
     * @param ActionParams $params Parameters for the action that triggered the click event.
     * @return void
     */
    protected function gettingConfirmation_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        // Add the code to send the message here.
        // Options to do this are left to the developer.
        //
        // Note that a proper solution must be considered here.
        // If the editor-in-chief needs to be reviewed, he should not receive messages...

        if ($this->chkConfirmationAsking->Checked) {
            $this->lstStatus->Enabled = false;
            $this->lstStatus->SelectedValue = 3;

            $this->objMenuContent->setIsEnabled(3);
            $this->objArticle->setConfirmationAsking(1);

            $this->dlgToastr3->notify();
        } else {
            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();

            $this->objMenuContent->setIsEnabled(1);
            $this->objArticle->setConfirmationAsking(0);

            $this->dlgToastr4->notify();
        }

        $this->objArticle->save();
        $this->objMenuContent->save();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Retrieves a list of category items based on the current category condition and clauses.
     *
     * @return ListItem[] An array of ListItem objects representing the categories.
     */
    public function lstCategory_GetItems()
    {
        $a = array();
        $objCondition = $this->objCategoryCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objCategoryCursor = CategoryOfArticle::queryCursor($objCondition, $this->objCategoryClauses);

        // Iterate through the Cursor
        while ($objCategory = CategoryOfArticle::instantiateCursor($objCategoryCursor)) {
            $objListItem = new ListItem($objCategory->__toString(), $objCategory->Id);
            if (($this->objArticle->Category) && ($this->objArticle->Category->Id == $objCategory->Id))
                $objListItem->Selected = true;

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objCategory->IsEnabled == 2) {
                $objListItem->Disabled = true;
            }

            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Handles the change event for the category selection.
     *
     * @param ActionParams $params The parameters for the action triggered by the category change event.
     * @return void
     */
    protected function lstCategory_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->lstCategory->SelectedValue !== $this->objArticle->getCategoryId()) {
            $this->objArticle->setCategoryId($this->lstCategory->SelectedValue);
            $this->objArticle->save();

            $this->dlgToastr1->notify();
        }

        $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
        $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objArticle->save();

        $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

        $this->refreshDisplay();
    }

    /**
     * Handles the change in status of the menu and displays the appropriate dialog box
     * or locks the input fields based on specific conditions.
     *
     * @param ActionParams $params Parameters provided by the action triggering this method.
     * @return void
     */
    protected function lstStatus_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->dlgModal1->showDialogBox();
            $this->updateInputFields();
        } else if ($this->objMenuContent->SelectedPageLocked === 1) {
            $this->dlgModal2->showDialogBox();
            $this->updateInputFields();
        } else if ($this->objMenuContent->getIsEnabled() === 1) {
            $this->dlgModal3->showDialogBox();
        } else {
            $this->lockInputFields();
        }
    }

    /**
     * Handles the click event for a status item, updates the UI and relevant data based on the selected status.
     *
     * @param ActionParams $params Parameters passed by the action triggering this method.
     *
     * @return void
     */
    protected function statusItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->lstStatus->SelectedValue === 2) {
            $this->dlgModal3->hideDialogBox();
            $this->objMenuContent->setIsEnabled(2);
        } else {
            $this->dlgModal3->hideDialogBox();
            $this->objMenuContent->setIsEnabled(3);
        }

        $this->objMenuContent->save();

        $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
        $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objArticle->save();

        $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

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
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
    }

    /**
     * Updates the input fields based on the current menu content state.
     *
     * @return void
     */
    protected function updateInputFields()
    {
        $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
        $this->lstStatus->refresh();
    }

    /**
     * Locks input fields based on the selected status value and updates the associated article and menu content.
     *
     * @return void
     */
    protected function lockInputFields()
    {
        if ($this->lstStatus->SelectedValue === 1) {
            $this->objMenuContent->setIsEnabled(1);
            $this->dlgModal5->showDialogBox();
        } else if ($this->lstStatus->SelectedValue === 2) {
            $this->objMenuContent->setIsEnabled(2);
            $this->dlgModal4->showDialogBox();
        } else if ($this->lstStatus->SelectedValue === 3) {
            $this->objMenuContent->setIsEnabled(3);
            $this->dlgModal6->showDialogBox();
        }

        $this->objMenuContent->save();

        $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
        $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objArticle->save();

        $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

        $this->refreshDisplay();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Saves the image and updates relevant article information.
     *
     * @param ActionParams $params Parameters containing the necessary data for the action.
     * @return void
     */
    protected function imageSave_Push(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $saveId = $this->objMediaFinder->Item;

        $this->objArticle->setPictureId($saveId);
        $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
        $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objArticle->save();

        $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

        $this->refreshDisplay();

        $this->lblPictureDescription->Display = true;
        $this->txtPictureDescription->Display = true;
        $this->lblAuthorSource->Display = true;
        $this->txtAuthorSource->Display = true;

        $this->dlgToastr1->notify();
    }

    /**
     * Handles the process of deleting an image associated with an article and updates related metadata.
     *
     * @param ActionParams $params Action parameters containing necessary data for this operation.
     * @return void
     */
    protected function imageDelete_Push(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFiles = Files::loadById($this->objArticle->getPictureId());

        if ($objFiles->getLockedFile() !== 0) {
            $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
            $objFiles->save();
        }

        $this->objArticle->setPictureId(null);
        $this->objArticle->setPictureDescription(null);
        $this->objArticle->setAuthorSource(null);
        $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
        $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objArticle->save();

        $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

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

    /**
     * Handles the click event for the menu save button.
     *
     * @param ActionParams $params Parameters associated with the action event.
     * @return void
     */
    public function btnMenuSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->renderActionsWithOrWithoutId();

        if ($this->txtTitle->Text) {
            $this->helperSave();
            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr2->notify();
            $this->txtTitle->Text = $this->objArticle->Title;
            $this->txtTitle->focus();
        }
    }

    /**
     * Handles the save and close button click event for the menu.
     *
     * @param ActionParams $params The parameters for the action event.
     * @return void
     */
    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->renderActionsWithOrWithoutId();

        if ($this->txtTitle->Text) {
            $this->helperSave();
            $this->redirectToListPage();
        } else {
            $this->dlgToastr2->notify();
            $this->txtTitle->Text = $this->objArticle->Title;
            $this->txtTitle->focus();
        }
    }

    /**
     * Saves the current state of the article and associated frontend links, menus,
     * and other related objects as necessary.
     *
     * @return void
     */
    protected function helperSave()
    {
        $objTemplateLocking = FrontendTemplateLocking::load(2);
        $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

        $this->renderActionsWithOrWithoutId();

        $this->objArticle->setTitle($this->txtTitle->Text);

        $this->updateSlug = $this->objMenuContent->getMenuTreeHierarchy() . '/' . Q\QString::sanitizeForUrl(trim($this->txtTitle->Text));

        $this->objArticle->setTitleSlug($this->updateSlug);
        $this->objArticle->setCategoryId($this->lstCategory->SelectedValue);
        $this->objArticle->setContent($this->txtContent->Text);

        if ($this->objArticle->PictureId) {
            $this->objArticle->setPictureDescription($this->txtPictureDescription->Text);
            $this->objArticle->setAuthorSource($this->txtAuthorSource->Text);
        } else {
            $this->objArticle->setPictureDescription(null);
            $this->objArticle->setAuthorSource(null);
        }

        $this->objArticle->save();

        $this->referenceValidation();

        if ($this->objArticle->getTitleSlug() !== $this->objMenuContent->getRedirectUrl()) {
            $this->objMenuContent->setTitle($this->txtTitle->Text);
            $this->objMenuContent->setHomelyUrl(1);
            $this->objMenuContent->setRedirectUrl($this->updateSlug);
            $this->objMenuContent->save();

            $this->objFrontendLinks->setLinkedId($this->intId);
            $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
            $this->objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
            $this->objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $this->objFrontendLinks->setContentTypesManagamentId(2);
            $this->objFrontendLinks->setFrontendTitleSlug($this->updateSlug);
            $this->objFrontendLinks->setIsActivated($this->lstStatus->SelectedValue);
            $this->objFrontendLinks->save();
        }

        $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
        $this->txtAuthor->Text = $this->objArticle->getAuthor();
        $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        if ($this->txtTitle->Text) {
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX . $this->objArticle->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
            $this->txtTitleSlug->setCssStyle('float', 'left');
        } else {
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->refreshDisplay();

        if ($this->objArticle->getContent()) {
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
    }

    /**
     * Handles the escape click action to cancel current article edits and reset form fields.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objCancel = $this->objArticle->getId();

        // Check if $objCancel is available
        if ($objCancel) {
            $this->dlgToastr5->notify();
        }

        $this->txtTitle->Text = $this->objArticle->getTitle();
        $this->txtContent->Text = $this->objArticle->getContent();
        $this->txtPictureDescription->Text = $this->objArticle->getPictureDescription();
        $this->txtAuthorSource->Text = $this->objArticle->getAuthor();
    }

    /**
     * Refreshes the display settings of article-related labels and controls
     * based on the article's post date, update date, author, and editors status.
     *
     * @return void
     */
    protected function refreshDisplay()
    {
        if ($this->objArticle->getPostDate() &&
            !$this->objArticle->getPostUpdateDate() &&
            $this->objArticle->getAuthor() &&
            !$this->objArticle->countUsersAsArticlesEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsArticlesEditors->Display = false;
            $this->txtUsersAsArticlesEditors->Display= false;
            $this->calPostDate->addCssClass('form-control-remove');
        }

        if ($this->objArticle->getPostDate() &&
            $this->objArticle->getPostUpdateDate() &&
            $this->objArticle->getAuthor() &&
            !$this->objArticle->countUsersAsArticlesEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsArticlesEditors->Display = false;
            $this->txtUsersAsArticlesEditors->Display = false;
            $this->calPostDate->removeCssClass('form-control-remove');
        }

        if ($this->objArticle->getPostDate() &&
            $this->objArticle->getPostUpdateDate() &&
            $this->objArticle->getAuthor() &&
            $this->objArticle->countUsersAsArticlesEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsArticlesEditors->Display = true;
            $this->txtUsersAsArticlesEditors->Display = true;
            $this->txtUsersAsArticlesEditors->addCssClass('form-control-add');
        }
    }

    /**
     * Renders actions based on the current state of the article and menu content, with or without the presence of an ID.
     *
     * @return void
     */
    public function renderActionsWithOrWithoutId()
    {
        if (strlen($this->intId)) {
            if ($this->txtTitle->Text !== $this->objArticle->getTitle() ||
                $this->lstCategory->SelectedValue !== $this->objArticle->getCategoryId() ||
                $this->txtContent->Text !== $this->objArticle->getContent() ||
                $this->objOldPicture !== $this->objArticle->getPictureId() ||
                $this->txtPictureDescription->Text !== $this->objArticle->getPictureDescription() ||
                $this->txtAuthorSource->Text !== $this->objArticle->getAuthorSource() ||
                $this->lstStatus->SelectedValue !== $this->objMenuContent->getIsEnabled() ||
                $this->chkConfirmationAsking->Checked !== $this->objArticle->getConfirmationAsking()
            ) {

                $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

                $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
                $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
                $this->objArticle->save();

                if (!$this->txtTitle->Text) {
                    $this->objMenuContent->setIsEnabled(2);
                    $this->lstStatus->SelectedValue = 2;
                } else {
                    $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
                    $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
                }
                $this->objMenuContent->save();
            }
        }
    }

    // This function referenceValidation(), which checks and ensures that the data is up-to-date both when adding and
    // deleting a file. Everything is commented in the code.

    /**
     * Validates and updates the reference IDs for files associated with the article's content.
     * This method scans the article's content for image and anchor tags, extracts their ID attributes,
     * and compares them with the existing file IDs referenced in the article. Depending on the comparison,
     * it updates the references and manages the lock status of the files.
     *
     * @return void
     */
    protected function referenceValidation()
    {
        $objArticle = Article::loadById($this->objArticle->getId());

        $references = $objArticle->getFilesIds();
        $content = $objArticle->getContent();

        // Regular expression to find the img id attribute
        $patternImgId = '/<img[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

        // Regular expression to find the a id attribute
        $patternAId = $patternAId = '/<a[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

        $matchesImg = [];
        $matchesA = [];

        // Search for a pattern
        preg_match_all($patternImgId, $content, $matchesImg);
        preg_match_all($patternAId, $content, $matchesA);

        // Merge arrays into one
        $combinedArray = array_merge($matchesImg[1], $matchesA[1]);

        if (!strlen($references)) {
            $saveFilesIds = implode(',', $combinedArray);
            $objArticle->setFilesIds($saveFilesIds);
            $objArticle->save();

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
                $objArticle->setFilesIds($updatedFilesIds);
                $objArticle->save();
            }

            if (count($unlockFiles)) {
                foreach ($unlockFiles as $value) {
                    $unlockFile = Files::loadById($value);
                    $unlockFile->setLockedFile($unlockFile->getLockedFile() - 1);
                    $unlockFile->save();
                }

                // Overwriting example data
                $updatedFilesIds = implode(',', $combinedArray);
                $objArticle->setFilesIds($updatedFilesIds);
                $objArticle->save();
            }
        }
    }

    /**
     * Handles the click event for the Menu Cancel button and redirects the user to the list page.
     *
     * @param ActionParams $params Parameters for the click action.
     * @return void
     */
    public function btnMenuCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->redirectToListPage();
    }

    /**
     * Handles the click event for the "Go To Article Category" button.
     * Sets the current article ID in the session and redirects to the categories manager page.
     *
     * @param ActionParams $params The action parameters associated with the click event.
     * @return void
     */
    public function btnGoToArticleCategroy_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal7->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $_SESSION['article'] = $this->intId;
        Application::redirect('categories_manager.php#articleCategories_tab');
    }

    /**
     * Redirects the user to the menu manager list page.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        Application::redirect('menu_manager.php');
    }

}