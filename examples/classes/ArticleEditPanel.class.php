<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Action\AjaxControl;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Html;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\QDateTime;
    use QCubed\Query\QQ;
    use QCubed\QString;

    /**
     * Class representing a panel for editing article details and associated metadata.
     * Extends the Q\Control\Panel class to provide UI controls and functionality specific to article management.
     */
    class ArticleEditPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;

        public Q\Plugin\Toastr $dlgToastr1;
        public Q\Plugin\Toastr $dlgToastr2;
        public Q\Plugin\Toastr $dlgToastr3;
        public Q\Plugin\Toastr $dlgToastr4;
        public Q\Plugin\Toastr $dlgToastr5;

        public Q\Plugin\Control\Label $lblExistingMenuText;
        public Q\Plugin\Control\Label $txtExistingMenuText;

        public Q\Plugin\Control\Label $lblMenuText;
        public Bs\TextBox $txtMenuText;

        public Q\Plugin\Control\Label $lblTitle;
        public Bs\TextBox $txtTitle;

        public Q\Plugin\Control\Label $lblCategory;
        public Q\Plugin\Select2 $lstCategory;

        public Q\Plugin\Control\Label $lblTitleSlug;
        public Q\Plugin\Control\Label $txtTitleSlug;

        public Q\Plugin\CKEditor $txtContent;

        public Q\Plugin\Control\Label $lblPostDate;
        public Bs\Label $calPostDate;

        public Q\Plugin\Control\Label $lblPostUpdateDate;
        public Bs\Label $calPostUpdateDate;

        public Q\Plugin\Control\Label $lblAuthor;
        public Bs\Label $txtAuthor;

        public Q\Plugin\Control\Label $lblUsersAsArticlesEditors;
        public Bs\Label $txtUsersAsArticlesEditors;

        public Q\Plugin\MediaFinder $objMediaFinder;

        public Q\Plugin\Control\Label $lblPictureDescription;
        public Bs\TextBox $txtPictureDescription;

        public Q\Plugin\Control\Label $lblAuthorSource;
        public Bs\TextBox $txtAuthorSource;

        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Q\Plugin\Control\Label $lblConfirmationAsking;
        public Q\Plugin\Control\Checkbox $chkConfirmationAsking;

        public Bs\Button $btnSave;
        public Bs\Button $btnSaving;
        public Bs\Button $btnCancel;
        public Bs\Button $btnGoToArticleCategroy;

        protected string$strSaveButtonId;
        protected string$strSavingButtonId;

        protected int $intId;
        protected object $objMenu;
        protected object $objMenuContent;
        protected object $objFrontendLinks;
        protected object $objArticle;
        protected object $objMetadata;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;
        protected object $objPortlet;

        protected ?int $objOldPicture = null;
        protected string $updateSlug;

        protected ?object $objCategoryCondition = null;
        protected ?array $objCategoryClauses = null;

        protected string $strTemplate = 'ArticleEditPanel.tpl.php';

        /**
         * Constructor for initializing the object and setting up its state.
         *
         * @param mixed $objParentObject The parent object that this object will be attached to.
         * @param null|string $strControlId Optional control ID for the object.
         *
         * @throws DateMalformedStringException
         * @throws Caller Thrown if there is an error in the caller's logic.
         * @throws InvalidCast
         * @throws Exception
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
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
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must save something here $this->objArticle->setUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session
            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);
            $this->objPortlet = Portlet::load(1);

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Updates the user's last active timestamp to the current time and saves the changes to the user object.
         *
         * @return void The method does not return a value.
         * @throws Caller
         */
        private function userOptions(): void
        {
            $this->objUser->setLastActive(QDateTime::now());
            $this->objUser->save();

            $this->objPortlet->setLastDate(QDateTime::now());
            $this->objPortlet->save();
        }

        /**
         * Creates and configures the input controls and labels for editing menu and article details.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
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
            $this->txtTitle->MaxLength = Article::TITLE_MAX_LENGTH;
            $this->txtTitle->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtTitle->addAction(new EnterKey(), new Terminate());
            $this->txtTitle->AddAction(new EscapeKey(), new AjaxControl($this,'itemEscape_Click'));
            $this->txtTitle->addAction(new EscapeKey(), new Terminate());
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
            $this->lstCategory->addItem(t('- Select one category'), null, true);
            $this->lstCategory->addItems($this->lstCategory_GetItems());
            $this->lstCategory->SelectedValue = $this->objArticle->ArticleCategoryId;
            $this->lstCategory->AddAction(new Change(), new AjaxControl($this,'lstCategory_Change'));

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
                $this->txtTitleSlug->setCssStyle('text-align', 'left');
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX . $this->objArticle->getTitleSlug();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
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

            $this->lblPostDate = new Q\Plugin\Control\Label($this);
            $this->lblPostDate->Text = t('Created');
            $this->lblPostDate->setCssStyle('font-weight', 'bold');

            $this->calPostDate = new Bs\Label($this);
            $this->calPostDate->Text = $this->objArticle->PostDate->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);

            $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
            $this->lblPostUpdateDate->Text = t('Updated');
            $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

            $this->calPostUpdateDate = new Bs\Label($this);
            $this->calPostUpdateDate->Text = $this->objArticle->PostUpdateDate ? $this->objArticle->PostUpdateDate->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time) : null;

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
            $this->objMediaFinder->PopupUrl = dirname(QCUBED_FILEMANAGER_ASSETS_URL) . "/examples/finder.php";
            $this->objMediaFinder->EmptyImageAlt = t("Choose a picture");
            $this->objMediaFinder->SelectedImageAlt = t("Selected picture");

            $this->objMediaFinder->SelectedImageId = $this->objArticle->getPictureId() ? $this->objArticle->getPictureId() : null;

            if ($this->objMediaFinder->SelectedImageId !== null) {
                $objFiles = Files::loadById($this->objMediaFinder->SelectedImageId);
                $this->objMediaFinder->SelectedImagePath = $this->objMediaFinder->TempUrl . $objFiles->getPath();
                $this->objMediaFinder->SelectedImageName = $objFiles->getName();
            }

            $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageSave(), new AjaxControl($this, 'imageSave_Push'));
            $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageDelete(), new AjaxControl($this, 'imageDelete_Push'));

            $this->lblPictureDescription = new Q\Plugin\Control\Label($this);
            $this->lblPictureDescription->Text = t('Picture description');
            $this->lblPictureDescription->setCssStyle('font-weight', 'bold');

            $this->txtPictureDescription = new Bs\TextBox($this);
            $this->txtPictureDescription->Text = $this->objArticle->PictureDescription ? $this->objArticle->PictureDescription : null;
            $this->txtPictureDescription->TextMode = TextBoxBase::MULTI_LINE;
            $this->txtPictureDescription->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtPictureDescription->addAction(new EnterKey(), new Terminate());
            $this->txtPictureDescription->AddAction(new EscapeKey(), new AjaxControl($this,'itemEscape_Click'));
            $this->txtPictureDescription->addAction(new EscapeKey(), new Terminate());

            $this->lblAuthorSource = new Q\Plugin\Control\Label($this);
            $this->lblAuthorSource->Text = t('Author/source');
            $this->lblAuthorSource->setCssStyle('font-weight', 'bold');

            $this->txtAuthorSource = new Bs\TextBox($this);
            $this->txtAuthorSource->Text = $this->objArticle->AuthorSource ? $this->objArticle->AuthorSource : null;
            $this->txtAuthorSource->MaxLength = Article::AUTHOR_SOURCE_MAX_LENGTH;
            $this->txtAuthorSource->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtAuthorSource->addAction(new EnterKey(), new Terminate());
            $this->txtAuthorSource->AddAction(new EscapeKey(), new AjaxControl($this,'itemEscape_Click'));
            $this->txtAuthorSource->addAction(new EscapeKey(), new Terminate());

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
            $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')/*, 3 => t('Draft')*/]);
            $this->lstStatus->SelectedValue = $this->objMenuContent->IsEnabled;
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
            $this->lstStatus->AddAction(new Change(), new AjaxControl($this,'lstStatus_Change'));

            $this->lblConfirmationAsking = new Q\Plugin\Control\Label($this);
            $this->lblConfirmationAsking->Text = t('Confirmation of publication');
            $this->lblConfirmationAsking->setCssStyle('font-weight', 'bold');

            $this->chkConfirmationAsking = new Q\Plugin\Control\Checkbox($this);
            $this->chkConfirmationAsking->Checked = $this->objArticle->ConfirmationAsking;
            $this->chkConfirmationAsking->WrapperClass = 'checkbox checkbox-orange';
            $this->chkConfirmationAsking->addAction(new Change(), new AjaxControl($this, 'gettingConfirmation_Click'));
        }

        /**
         * Creates and configures the buttons for menu operations such as save, save and close, cancel, and navigation
         * to article categories.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnSave = new Bs\Button($this);
            if ($this->objArticle->getContent()) {
                $this->btnSave->Text = t('Update');
            } else {
                $this->btnSave->Text = t('Save');
            }
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this,'btnMenuSave_Click'));
            // The variable below is being prepared for fast transmission
            $this->strSaveButtonId = $this->btnSave->ControlId;

            $this->btnSaving = new Bs\Button($this);
            if ($this->objArticle->getContent()) {
                $this->btnSaving->Text = t('Update and close');
            } else {
                $this->btnSaving->Text = t('Save and close');
            }
            $this->btnSaving->CssClass = 'btn btn-darkblue';
            $this->btnSaving->PrimaryButton = true;
            $this->btnSaving->addAction(new Click(), new AjaxControl($this,'btnMenuSaveClose_Click'));
            // The variable below is being prepared for fast transmission
            $this->strSavingButtonId = $this->btnSaving->ControlId;

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Back');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this,'btnMenuCancel_Click'));

            $this->btnGoToArticleCategroy = new Bs\Button($this);
            $this->btnGoToArticleCategroy->Tip = true;
            $this->btnGoToArticleCategroy->ToolTip = t('Go to the article categories manager');
            $this->btnGoToArticleCategroy->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToArticleCategroy->CssClass = 'btn btn-default';
            $this->btnGoToArticleCategroy->setCssStyle('float', 'right');
            $this->btnGoToArticleCategroy->CausesValidation = false;
            $this->btnGoToArticleCategroy->addAction(new Click(), new AjaxControl($this, 'btnGoToArticleCategory_Click'));
        }

        /**
         * Creates and configures various Toastr notifications for different alert types and scenarios.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('The menu title or content title must exist!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Well done!</strong> The message has been sent to the editor-in-chief of the site for review, correction or approval!');
            $this->dlgToastr3->ProgressBar = true;
            $this->dlgToastr3->TimeOut = 10000;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('<strong>Well done!</strong> A message has been sent to the editor-in-chief of the site to cancel the confirmation!');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('<strong>Well done!</strong> Updates to some records for this post were discarded, and the record has been restored!');
            $this->dlgToastr5->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates multiple modal dialogs with predefined properties like a text, title, and buttons.
         *
         * @return void
         */
        protected function createModals(): void
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the article for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This article is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This article has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Success");
            $this->dlgModal5->HeaderClasses = 'btn-success';
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This article is now a draft!</p>');
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the 'Click' event for getting a confirmation. Depending on the state
         * of the confirmation checkbox, it adjusts the status, updates menu content
         * and article properties, notifies the user using toastr, and saves the changes.
         *
         * @param ActionParams $params Parameters for the action that triggered the click event.
         *
         * @return void
         * @throws Caller
         */
        protected function gettingConfirmation_Click(ActionParams $params): void
        {
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

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves a list of category items based on the current category condition and clauses.
         *
         * @return ListItem[] An array of ListItem objects representing the categories.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstCategory_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objCategoryCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objCategoryCursor = CategoryOfArticle::queryCursor($objCondition, $this->objCategoryClauses);

            // Iterate through the Cursor
            while ($objCategory = CategoryOfArticle::instantiateCursor($objCategoryCursor)) {
                $objListItem = new ListItem($objCategory->__toString(), $objCategory->Id);
                if (($this->objArticle->ArticleCategory) && ($this->objArticle->ArticleCategory->Id == $objCategory->Id))
                    $objListItem->Selected = true;

                // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
                // {display: none;} </style>
                // A little trick on how to hide some options. Just set the option to "disabled" and
                //  use it only on a specific page. You just have to use the style.

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
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstCategory_Change(ActionParams $params): void
        {
            if ($this->lstCategory->SelectedValue !== $this->objArticle->getArticleCategoryId()) {
                $this->objArticle->setArticleCategoryId($this->lstCategory->SelectedValue);
                $this->objArticle->setCategory($this->lstCategory->SelectedName);
                $this->objArticle->save();

                $this->userOptions();

                $this->dlgToastr1->notify();
            }

            $this->objArticle->setPostUpdateDate(QDateTime::now());
            $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objArticle->save();

            $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);
            $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

            $this->refreshDisplay();
        }

        /**
         * Handles changes to the status of a menu and associated content, validating CSRF token
         * and managing conditional state-related actions.
         *
         * @param ActionParams $params Parameters associated with the action.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->dlgModal1->showDialogBox();
                $this->updateInputFields();
            } else if ($this->objMenuContent->SelectedPageLocked === 1) {
                $this->dlgModal2->showDialogBox();
                $this->updateInputFields();
            } else {
                $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
                $this->objMenuContent->setSettingLocked($this->lstStatus->SelectedValue);
                $this->objArticle->setStatus($this->lstStatus->SelectedValue);
                $this->objMenuContent->save();

                if ($this->objMenuContent->getIsEnabled() === 1) {
                    $this->dlgModal4->showDialogBox();
                } else if ($this->objMenuContent->getIsEnabled() === 2) {
                    $this->dlgModal3->showDialogBox();
                } else if ($this->objMenuContent->getIsEnabled() === 3) {
                    $this->dlgModal5->showDialogBox();
                }

                $this->objArticle->setPostUpdateDate(QDateTime::now());
                $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objArticle->save();

                $this->userOptions();

                $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);
                $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

                $this->refreshDisplay();
            }
        }

        /**
         * Updates the input fields based on the current menu content state.
         *
         * @return void
         * @throws Caller
         */
        protected function updateInputFields(): void
        {
            $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
            $this->lstStatus->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Saves the image and updates relevant article information.
         *
         * @param ActionParams $params Parameters containing the necessary data for the action.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function imageSave_Push(ActionParams $params): void
        {
            $saveId = $this->objMediaFinder->Item;

            $this->objArticle->setPictureId($saveId);
            $this->objArticle->setPostUpdateDate(QDateTime::now());
            $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objArticle->save();

            $this->userOptions();

            $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);
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
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function imageDelete_Push(ActionParams $params): void
        {
            $objFiles = Files::loadById($this->objArticle->getPictureId());

            if ($objFiles->getLockedFile() !== 0) {
                $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                $objFiles->save();
            }

            $this->userOptions();

            $this->objArticle->setPictureId(null);
            $this->objArticle->setPictureDescription(null);
            $this->objArticle->setAuthorSource(null);
            $this->objArticle->setPostUpdateDate(QDateTime::now());
            $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objArticle->save();

            $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);
            $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

            $this->refreshDisplay();

            $this->lblPictureDescription->Display = false;
            $this->txtPictureDescription->Display = false;
            $this->lblAuthorSource->Display = false;
            $this->txtAuthorSource->Display = false;

            $this->txtPictureDescription->Text = '';
            $this->txtAuthorSource->Text = '';

            $this->dlgToastr1->notify();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the menu save button.
         *
         * @param ActionParams $params Parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        public function btnMenuSave_Click(ActionParams $params): void
        {
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
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        public function btnMenuSaveClose_Click(ActionParams $params): void
        {
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
         * @throws Caller
         * @throws InvalidCast
         */
        protected function helperSave(): void
        {
            $objTemplateLocking = FrontendTemplateLocking::load(2);
            $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

            $this->renderActionsWithOrWithoutId();

            $this->objArticle->setTitle($this->txtTitle->Text);

            $this->updateSlug = $this->objMenuContent->getMenuTreeHierarchy() . '/' . QString::sanitizeForUrl(trim($this->txtTitle->Text));

            $this->objArticle->setTitleSlug($this->updateSlug);
            $this->objArticle->setArticleCategoryId($this->lstCategory->SelectedValue);
            $this->objArticle->setCategory($this->lstCategory->SelectedName);
            $this->objArticle->setContent($this->txtContent->Text);

            if ($this->objArticle->PictureId) {
                $this->objArticle->setPictureDescription($this->txtPictureDescription->Text);
                $this->objArticle->setAuthorSource($this->txtAuthorSource->Text);
            } else {
                $this->objArticle->setPictureDescription(null);
                $this->objArticle->setAuthorSource(null);
            }

            $this->objArticle->save();

            $this->userOptions();

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
                $this->objFrontendLinks->save();
            }

            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtAuthor->Text = $this->objArticle->getAuthor();
            $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);

            if ($this->txtTitle->Text) {
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX . $this->objArticle->getTitleSlug();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
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
                Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strUpdate_translate');");
                Application::executeJavaScript("jQuery($this->strSavingButtonId).text('$strUpdateAndClose_translate');");
            } else {
                $strSave_translate = t('Save');
                $strSaveAndClose_translate = t('Save and close');
                Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strSave_translate');");
                Application::executeJavaScript("jQuery($this->strSavingButtonId).text('$strSaveAndClose_translate');");
            }
        }

        /**
         * Handles the escape click action to cancel current article edits and reset form fields.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
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
         * based on the article's post-date, update date, author, and editor status.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function refreshDisplay(): void
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
         * Renders actions based on the current state of the article and menu content, with or without the presence of
         * an ID.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function renderActionsWithOrWithoutId(): void
        {
            if ($this->intId) {
                if ($this->txtTitle->Text !== $this->objArticle->getTitle() ||
                    $this->lstCategory->SelectedValue !== $this->objArticle->getArticleCategoryId() ||
                    $this->txtContent->Text !== $this->objArticle->getContent() ||
                    $this->objOldPicture !== $this->objArticle->getPictureId() ||
                    $this->txtPictureDescription->Text !== $this->objArticle->getPictureDescription() ||
                    $this->txtAuthorSource->Text !== $this->objArticle->getAuthorSource() ||
                    $this->lstStatus->SelectedValue !== $this->objMenuContent->getIsEnabled() ||
                    $this->chkConfirmationAsking->Checked !== $this->objArticle->getConfirmationAsking()
                ) {

                    $this->objArticle->setAssignedEditorsNameById($this->intLoggedUserId);
                    $this->txtUsersAsArticlesEditors->Text = implode(', ', $this->objArticle->getUserAsArticlesEditorsArray());

                    $this->objArticle->setPostUpdateDate(QDateTime::now());
                    $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat($this->objUser->PreferredDateTimeObject->Date . ' ' . $this->objUser->PreferredDateTimeObject->Time);
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

        // This function referenceValidation(), which checks and ensures that the data is up to date both when adding and
        // deleting a file. Everything is commented in the code.

        /**
         * Validates and updates the reference IDs for files associated with the article's content.
         * This method scans the article's content for an image and anchor tags, extracts their ID attributes,
         * and compares them with the existing file IDs referenced in the article. Depending on the comparison,
         * it updates the references and manages the lock status of the files.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function referenceValidation(): void
        {
            $objArticle = Article::loadById($this->objArticle->getId());

            if (!$objArticle->getContent()) {
                return;
            }

            $references = $objArticle->getFilesIds();
            $content = $objArticle->getContent();

            // Regular expression to find the img id attribute
            $patternImgId = '/<img[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

            // Regular expression to find an id attribute
            $patternAId = '/<a[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

            $matchesImg = [];
            $matchesA = [];

            // Search for a pattern
            preg_match_all($patternImgId, $content, $matchesImg);
            preg_match_all($patternAId, $content, $matchesA);

            // Merge arrays into one
            $combinedArray = array_merge($matchesImg[1], $matchesA[1]);

            if (!$references) {
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

                // Content has more IDs than FilesIds fewer references.
                // Then call back to FileHandler to lock that file (+ 1).
                $lockFiles = array_diff($combinedArray, $nativeFilesIds);

                // Content has fewer IDs than FilesIds, has more references.
                // Then call back to FileHandler to unclog that file (- 1).
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
         *
         * @return void
         * @throws Throwable
         */
        public function btnMenuCancel_Click(ActionParams $params): void
        {
            $this->redirectToListPage();
        }

        /**
         * Handles the click event for the "Go To Article Category" button.
         * Sets the current article ID in the session and redirects to the categories manager page.
         *
         * @param ActionParams $params The action parameters associated with the click event.
         *
         * @return void
         * @throws Throwable
         */
        public function btnGoToArticleCategory_Click(ActionParams $params): void
        {
            $_SESSION['article'] = $this->intId;
            Application::redirect('categories_manager.php#articleCategories_tab');
        }

        /**
         * Redirects the user to the menu manager list page.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            //Application::redirect('menu_manager.php');
            Application::executeJavaScript("history.go(-1);");
        }
    }