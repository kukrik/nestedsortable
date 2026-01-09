<?php
    require('qcubed.inc.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\CellClick;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Event\DialogButton;
    use QCubed\Action\Ajax;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\Query\QQ;
    use QCubed\Html;
    use QCubed\QString;

    /**
     * Represents the NewsEditForm class which is responsible for editing news items.
     * It extends the Form class and provides controls and functionalities for editing,
     * saving, deleting, and managing news contents.
     *
     * This form includes multiple toastr notifications, modals, labels, textboxes,
     * editors, checkboxes, datetime pickers, and buttons to implement a robust UI for news editing.
     * It is also integrated with various plugins such as Select2 and CKEditor for enhanced functionality.
     *
     * Attributes:
     * - Contains labels, textboxes, and other controls to input and display data related to news.
     * - Utilizes plugins for advanced features.
     * - Handles user interactions to create, update, and manage news records efficiently.
     *
     * Methods:
     * - formCreate(): Initializes the form and ensures proper session handling for news-related data.
     * - createInputs(): Responsible for creating and managing form inputs such as labels, textboxes, and editors.
     * - createButtons(): Adds buttons for user actions like save, delete, and navigation.
     * - createToastr(): Sets up toastr notifications for various alerts and messages.
     * - createModals(): Configures modals used within the form.
     *
     * Note:
     * - The formCreate method also demonstrates an example of session-based user handling.
     * - Some sample-logged user handling is considered but not implemented, leaving flexibility to the developer.
     */
    class NewsEditForm extends Form
    {
        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;
        protected Q\Plugin\Toastr $dlgToastr4;
        protected Q\Plugin\Toastr $dlgToastr5;
        protected Q\Plugin\Toastr $dlgToastr6;
        protected Q\Plugin\Toastr $dlgToastr7;
        protected Q\Plugin\Toastr $dlgToastr8;
        protected Q\Plugin\Toastr $dlgToastr9;
        protected Q\Plugin\Toastr $dlgToastr10;
        protected Q\Plugin\Toastr $dlgToastr11;
        protected Q\Plugin\Toastr $dlgToastr12;
        protected Q\Plugin\Toastr $dlgToastr13;
        protected Q\Plugin\Toastr $dlgToastr14;

        protected Bs\Modal $dlgModal1;
        protected Bs\Modal $dlgModal2;
        protected Bs\Modal $dlgModal3;
        protected Bs\Modal $dlgModal4;
        protected Bs\Modal $dlgModal5;
        protected Bs\Modal $dlgModal6;

        protected Q\Plugin\Control\Label $lblTitle;
        protected Bs\TextBox $txtTitle;

        protected Q\Plugin\Control\Label $lblChanges;
        protected Q\Plugin\Select2 $lstChanges;

        protected Q\Plugin\Control\Label $lblNewsCategory;
        protected Q\Plugin\Select2 $lstNewsCategory;

        protected Q\Plugin\Control\Label $lblGroupTitle;
        protected Q\Plugin\Select2 $lstGroupTitle;

        protected Q\Plugin\Control\Label $lblTitleSlug;
        protected Q\Plugin\Control\Label$txtTitleSlug;

        protected Q\Plugin\Control\Label $lblDocumentLink;
        protected Bs\Button $btnDocumentLink;
        protected Q\Plugin\Control\Label $txtDocumentLink;
        protected Bs\TextBox $txtLinkTitle;
        protected Bs\Button $btnDownloadCancel;
        protected Bs\Button $btnDownloadSave;

        protected Q\Plugin\Control\VauuTable $dtgSelectedList;
        protected Bs\TextBox $txtSelectedTitle;
        protected Q\Plugin\Control\RadioList $lstSelectedStatus;
        protected Bs\Button $btnSelectedSave;
        protected Bs\Button $btnSelectedCheck;
        protected Bs\Button $btnSelectedDelete;
        protected Bs\Button $btnSelectedCancel;

        protected Q\Plugin\CKEditor $txtContent;

        protected Q\Plugin\Control\Label $lblPostDate;
        protected Bs\Label $calPostDate;

        protected Q\Plugin\Control\Label $lblPostUpdateDate;
        protected Bs\Label $calPostUpdateDate;

        protected Q\Plugin\Control\Label $lblNewsAuthor;
        protected Bs\Label $txtNewsAuthor;

        protected Q\Plugin\Control\Label $lblUsersAsEditors;
        protected Bs\Label $txtUsersAsEditors;

        protected  Q\Plugin\MediaFinder $objMediaFinder;

        protected Q\Plugin\Control\Label $lblPictureDescription;
        protected Bs\TextBox $txtPictureDescription;

        protected Q\Plugin\Control\Label $lblAuthorSource;
        protected Bs\TextBox $txtAuthorSource;

        protected Q\Plugin\Control\Label $lblStatus;
        protected Q\Plugin\Control\RadioList$lstStatus;

        protected Q\Plugin\Control\Label $lblUsePublicationDate;
        protected Q\Plugin\Control\Checkbox $chkUsePublicationDate;

        protected Q\Plugin\Control\Label $lblAvailableFrom;
        protected Q\Plugin\DateTimePicker $calAvailableFrom;

        protected Q\Plugin\Control\Label $lblExpiryDate;
        protected Q\Plugin\DateTimePicker  $calExpiryDate;

        protected Q\Plugin\Control\Label $lblConfirmationAsking;
        protected Q\Plugin\Control\Checkbox $chkConfirmationAsking;

        protected Bs\Button $btnSave;
        protected Bs\Button $btnSaving;
        protected Bs\Button $btnDelete;
        protected Bs\Button $btnCancel;
        protected Bs\Button $btnGoToChanges;
        protected Bs\Button $btnGoToCategories;
        protected Bs\Button $btnGoToSettings;

        protected string $strSaveButtonId;
        protected string $strSavingButtonId;

        protected int $intId;
        protected object $objNews;
        protected object $objNewsSettings;
        protected object $objFrontendLinks;
        protected ?array $objNewsFiles = null;
        protected int $intGroup;
        protected int $intDocument;

        protected int $intLoggedUserId;
        protected int $intTemporaryId;
        protected ?int $objOldPicture = null;

        protected ?object $objChangesCondition = null;
        protected ?array $objChangesClauses = null;

        protected ?object $objNewsCategoryCondition = null;
        protected ?array $objNewsCategoryClauses = null;

        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        /**
         * Initializes the form and sets necessary configurations.
         *
         * This method performs session cleanup for specific session variables related to
         * news changes, categories, and settings. It retrieves query string parameters for
         * context-based data (e.g., `id` and `group`), loads corresponding news, news settings,
         * and frontend links objects, and initializes dependencies such as inputs, buttons,
         * notifications, and modals.
         *
         * User-related configurations, such as association with a logged user's session,
         * are left to the developer to implement as needed.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        protected function formCreate(): void
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
            }

            $this->intGroup = Application::instance()->context()->queryStringItem('group');
            $this->objNewsSettings = NewsSettings::loadByIdFromNewsSettings($this->intGroup);
            $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);
            $this->objNewsFiles = NewsFiles::loadArrayByNewsGroupId($this->intId);

            $this->objOldPicture = $this->objNews->getPictureId();

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must save something here $this->objNews->setUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session
            $this->intLoggedUserId = 4;

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
            $this->createTable();
            $this->popupViewer();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes a JavaScript event listener for elements with the class 'view-js'.
         * When such an element is clicked, checks for an input element with 'data-open' set to 'true'.
         * If found, opens a link specified in the 'data-view' attribute of the input element.
         * If the link is not available, it displays an alert message.
         *
         * @return void
         * @throws Caller
         */
        public function popupViewer(): void
        {
            Application::executeJavaScript("
                $(document).on('click', '.view-js', function() {
                    var inputElement = $('input[data-open=\"true\"]');
                    if (inputElement.length > 0) {
                        var link = inputElement.attr('data-view');
                        if (link) {
                            window.open(link);
                        } else {
                            alert('Link not available!');
                        }
                    }
                });
            ");
        }

        /**
         * Creates and initializes the input controls for the News form.
         *
         * This method sets up a variety of input and display controls, including labels, text boxes,
         * drop-down selections, and content editors. The controls are configured with appropriate
         * styles, default values, placeholder texts, and actions. Specific selections are populated
         * dynamically based on the associated News object and related data entities such as
         * News Changes, Categories, and Groups.
         *
         * Controls are designed to reflect the status of the underlying data, enabling or disabling
         * them based on conditions such as the availability of items. Additionally, it includes
         * the handling of dynamic links for title slugs and leverages CKEditor for the content field.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->lblTitle = new Q\Plugin\Control\Label($this);
            $this->lblTitle->Text = t('Title');
            $this->lblTitle->addCssClass('col-md-3');
            $this->lblTitle->setCssStyle('font-weight', 400);
            $this->lblTitle->Required = true;

            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Title');
            $this->txtTitle->Text = $this->objNews->Title ?? null;
            $this->txtTitle->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtTitle->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtTitle->addAction(new EnterKey(), new Terminate());
            $this->txtTitle->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtTitle->addAction(new EscapeKey(), new Terminate());
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
            $this->lstChanges->AddAction(new Change(), new Ajax('lstChanges_Change'));

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
            $this->lstNewsCategory->AddAction(new Change(), new Ajax('lstNewsCategory_Change'));

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
            $this->lblGroupTitle->Text = t('Newsgroup');
            $this->lblGroupTitle->addCssClass('col-md-3');
            $this->lblGroupTitle->setCssStyle('font-weight', 400);

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '90%';
            $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroupTitle->setCssStyle('float', 'left');
            $this->lstGroupTitle->addItem(t('- Change newsgroup -'), null, true);
            $this->lstGroupTitle->addAction(new Change(), new Ajax('lstGroupTitle_Change'));

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
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblDocumentLink = new Q\Plugin\Control\Label($this);
            $this->lblDocumentLink->Text = t('Document(s) links');
            $this->lblDocumentLink->addCssClass('col-md-3');
            $this->lblDocumentLink->setCssStyle('font-weight', 400);

            $this->btnDocumentLink = new Bs\Button($this);
            $this->btnDocumentLink->Text = t('Search file...');
            $this->btnDocumentLink->CssClass = 'btn btn-default';
            $this->btnDocumentLink->addWrapperCssClass('center-button');
            $this->btnDocumentLink->setCssStyle('float', 'left');
            $this->btnDocumentLink->CausesValidation = false;
            $this->btnDocumentLink->setDataAttribute('popup', 'popup');
            $this->btnDocumentLink->addAction(new Click(), new Ajax('btnDocumentLink_Click'));

            $this->txtDocumentLink = new Q\Plugin\Control\Label($this);
            $this->txtDocumentLink->Text = t('No documents available...');
            $this->txtDocumentLink->setCssStyle('color', '#999');
            $this->txtDocumentLink->setCssStyle('float', 'left');
            $this->txtDocumentLink->setCssStyle('margin-left', '15px');

            if (NewsFiles::countByNewsGroupId($this->intId) === 0) {
                $this->txtDocumentLink->Display = true;
            } else {
                $this->txtDocumentLink->Display = false;
            }

            $this->txtLinkTitle = new Bs\TextBox($this);
            $this->txtLinkTitle->Placeholder = t('Link title');
            $this->txtLinkTitle->Width = '60%';
            $this->txtLinkTitle->setCssStyle('float', 'left');
            $this->txtLinkTitle->Display = false;

            $this->txtSelectedTitle = new Bs\TextBox($this);
            $this->txtSelectedTitle->Display = false;
            $this->txtSelectedTitle->setDataAttribute('view', '');
            $this->txtSelectedTitle->setDataAttribute('open', 'false');

            $this->lstSelectedStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstSelectedStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstSelectedStatus->ButtonGroupClass = 'radio radio-orange form-horizontal radio-inline';
            $this->lstSelectedStatus->setCssStyle('float', 'left');
            $this->lstSelectedStatus->setCssStyle('margin-left', '-10px');
            $this->lstSelectedStatus->Display = false;

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->txtContent = new Q\Plugin\CKEditor($this);
            $this->txtContent->Text = $this->objNews->Content;
            $this->txtContent->Configuration = 'ckConfig';

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
            $this->objMediaFinder->PopupUrl = dirname(QCUBED_FILEMANAGER_ASSETS_URL) . "/examples/finder.php";
            $this->objMediaFinder->EmptyImageAlt = t("Choose a picture");
            $this->objMediaFinder->SelectedImageAlt = t("Selected picture");

            $this->objMediaFinder->SelectedImageId = $this->objNews->getPictureId() ? $this->objNews->getPictureId() : null;

            if ($this->objMediaFinder->SelectedImageId !== null) {
                $objFiles = Files::loadById($this->objMediaFinder->SelectedImageId);
                $this->objMediaFinder->SelectedImagePath = $this->objMediaFinder->TempUrl . $objFiles->getPath();
                $this->objMediaFinder->SelectedImageName = $objFiles->getName();
            }

            $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageSave(), new Ajax( 'imageSave_Push'));
            $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageDelete(), new Ajax('imageDelete_Push'));

            $this->lblPictureDescription = new Q\Plugin\Control\Label($this);
            $this->lblPictureDescription->Text = t('Picture description');
            $this->lblPictureDescription->setCssStyle('font-weight', 'bold');

            $this->txtPictureDescription = new Bs\TextBox($this);
            $this->txtPictureDescription->Text = $this->objNews->PictureDescription;
            $this->txtPictureDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtPictureDescription->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtPictureDescription->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtPictureDescription->addAction(new EnterKey(), new Terminate());
            $this->txtPictureDescription->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtPictureDescription->addAction(new EscapeKey(), new Terminate());

            $this->lblAuthorSource = new Q\Plugin\Control\Label($this);
            $this->lblAuthorSource->Text = t('Author/source');
            $this->lblAuthorSource->setCssStyle('font-weight', 'bold');

            $this->txtAuthorSource = new Bs\TextBox($this);
            $this->txtAuthorSource->Text = $this->objNews->AuthorSource;
            $this->txtAuthorSource->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtAuthorSource->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtAuthorSource->addAction(new EnterKey(), new Terminate());
            $this->txtAuthorSource->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtAuthorSource->addAction(new EscapeKey(), new Terminate());

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
            $this->lstStatus->AddAction(new Change(), new Ajax('lstStatus_Change'));

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

        /**
         * Creates and initializes a set of buttons for various actions in the form.
         *
         * This method defines multiple buttons, such as save, save and close, delete, cancel,
         * and navigation to related managers (e.g., changes, categories, settings). It sets up
         * properties including text labels, CSS classes, tooltips, action events, and button-specific
         * configurations required for their functionality. Each button is tailored to its specific
         * purpose with the appropriate visual styling and behavior.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
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
            $this->btnSave->addAction(new Click(), new Ajax('btnSave_Click'));
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
            $this->btnSaving->addAction(new Click(), new Ajax('btnSaveClose_Click'));
            // The variable below is being prepared for fast transmission
            $this->strSavingButtonId = $this->btnSaving->ControlId;

            $this->btnDelete = new Bs\Button($this);
            $this->btnDelete->Text = t('Delete');
            $this->btnDelete->CssClass = 'btn btn-danger';
            $this->btnDelete->addWrapperCssClass('center-button');
            $this->btnDelete->CausesValidation = false;
            $this->btnDelete->addAction(new Click(), new Ajax('btnDelete_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Back');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new Ajax('btnCancel_Click'));

            $this->btnGoToChanges = new Bs\Button($this);
            $this->btnGoToChanges->Tip = true;
            $this->btnGoToChanges->ToolTip = t('Go the news change manager');
            $this->btnGoToChanges->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToChanges->CssClass = 'btn btn-default';
            $this->btnGoToChanges->setCssStyle('float', 'right');
            $this->btnGoToChanges->addWrapperCssClass('center-button');
            $this->btnGoToChanges->CausesValidation = false;
            $this->btnGoToChanges->addAction(new Click(), new Ajax('btnGoToChanges_Click'));

            $this->btnGoToCategories = new Bs\Button($this);
            $this->btnGoToCategories->Tip = true;
            $this->btnGoToCategories->ToolTip = t('Go to categorize manager');
            $this->btnGoToCategories->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToCategories->CssClass = 'btn btn-default';
            $this->btnGoToCategories->setCssStyle('float', 'right');
            $this->btnGoToCategories->addWrapperCssClass('center-button');
            $this->btnGoToCategories->CausesValidation = false;
            $this->btnGoToCategories->addAction(new Click(), new Ajax('btnGoToCategories_Click'));

            $this->btnGoToSettings = new Bs\Button($this);
            $this->btnGoToSettings->Tip = true;
            $this->btnGoToSettings->ToolTip = t('Go to news settings manager');
            $this->btnGoToSettings->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToSettings->CssClass = 'btn btn-default';
            $this->btnGoToSettings->setCssStyle('float', 'right');
            $this->btnGoToSettings->addWrapperCssClass('center-button');
            $this->btnGoToSettings->CausesValidation = false;
            $this->btnGoToSettings->addAction(new Click(), new Ajax('btnGoToSettings_Click'));

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->btnDownloadSave = new Bs\Button($this);
            $this->btnDownloadSave->Text = t('Save');
            $this->btnDownloadSave->CssClass = 'btn btn-orange';
            $this->btnDownloadSave->addWrapperCssClass('center-button');
            $this->btnDownloadSave->setCssStyle('float', 'left');
            $this->btnDownloadSave->setCssStyle('margin-left', '10px');
            $this->btnDownloadSave->CausesValidation = false;
            $this->btnDownloadSave->Display = false;
            $this->btnDownloadSave->addAction(new Click(), new Ajax('btnDownloadSave_Click'));

            $this->btnDownloadCancel = new Bs\Button($this);
            $this->btnDownloadCancel->Text = t('Cancel');
            $this->btnDownloadCancel->CssClass = 'btn btn-default';
            $this->btnDownloadCancel->addWrapperCssClass('center-button');
            $this->btnDownloadCancel->setCssStyle('float', 'left');
            $this->btnDownloadCancel->setCssStyle('margin-left', '5px');
            $this->btnDownloadCancel->CausesValidation = false;
            $this->btnDownloadCancel->Display = false;
            $this->btnDownloadCancel->addAction(new Click(), new Ajax('btnDownloadCancel_Click'));

            $this->btnSelectedSave = new Bs\Button($this);
            $this->btnSelectedSave->Text = t('Update');
            $this->btnSelectedSave->CssClass = 'btn btn-orange';
            $this->btnSelectedSave->addWrapperCssClass('center-button');
            $this->btnSelectedSave->setCssStyle('margin-left', '10px');
            $this->btnSelectedSave->Display = false;
            $this->btnSelectedSave->addAction(new Click(), new Ajax('btnSelectedSave_Click'));

            $this->btnSelectedCheck = new Bs\Button($this);
            $this->btnSelectedCheck->Text = t('Check PDF');
            $this->btnSelectedCheck->CssClass = 'btn btn-darkblue view-js';
            $this->btnSelectedCheck->addWrapperCssClass('center-button');
            $this->btnSelectedCheck->Display = false;

            $this->btnSelectedDelete = new Bs\Button($this);
            $this->btnSelectedDelete->Text = t('Delete');
            $this->btnSelectedDelete->CssClass = 'btn btn-danger';
            $this->btnSelectedDelete->addWrapperCssClass('center-button');
            $this->btnSelectedDelete->CausesValidation = false;
            $this->btnSelectedDelete->Display = false;
            $this->btnSelectedDelete->addAction(new Click(), new Ajax('btnSelectedDelete_Click'));

            $this->btnSelectedCancel = new Bs\Button($this);
            $this->btnSelectedCancel->Text = t('Cancel');
            $this->btnSelectedCancel->CssClass = 'btn btn-default';
            $this->btnSelectedCancel->addWrapperCssClass('center-button');
            $this->btnSelectedCancel->CausesValidation = false;
            $this->btnSelectedCancel->Display = false;
            $this->btnSelectedCancel->addAction(new Click(), new Ajax('btnSelectedCancel_Click'));

            if (!empty( $_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
                $this->btnDocumentLink->Display = false;
                $this->txtDocumentLink->Display = false;
                $this->txtLinkTitle->Display = true;
                $this->btnDownloadSave->Display = true;
                $this->btnDownloadCancel->Display = true;

                $filteredDataName = pathinfo($_SESSION["data_name"]);
                $this->txtLinkTitle->Text = $filteredDataName['filename'];
            }
        }

        /**
         * Initializes various Toastr notifications for user feedback.
         *
         * This method creates multiple Toastr instances to display different types of notifications
         * such as success, error, and informational messages. Each notification includes attributes
         * such as alert type, message content, position, progress bar, timeout settings, and HTML
         * escaping configurations. These Toastr notifications provide feedback for different operations
         * performed in the application (e.g., saving posts, handling errors, setting dates).
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
            $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the news title must exist!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Well done!</strong> The publication date for this post has been saved or changed.');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('<strong>Well done!</strong> The expiration date for this post has been saved or changed.');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this date \"Available from\" does not exist.</p>Please enter at least the date of publication!');
            $this->dlgToastr5->ProgressBar = true;
            $this->dlgToastr5->TimeOut = 10000;
            $this->dlgToastr5->EscapeHtml = false;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('<p style=\"margin-bottom: 2px;\">Start date must be smaller then end date!</p><strong>Try to do it right again!</strong>');
            $this->dlgToastr6->ProgressBar = true;
            $this->dlgToastr6->TimeOut = 10000;
            $this->dlgToastr6->EscapeHtml = false;

            $this->dlgToastr7 = new Q\Plugin\Toastr($this);
            $this->dlgToastr7->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr7->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr7->Message = t('Publication date has been canceled.');
            $this->dlgToastr7->ProgressBar = true;

            $this->dlgToastr8 = new Q\Plugin\Toastr($this);
            $this->dlgToastr8->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr8->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr8->Message = t('The expiration date has been canceled.');
            $this->dlgToastr8->ProgressBar = true;

            $this->dlgToastr9 = new Q\Plugin\Toastr($this);
            $this->dlgToastr9->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr9->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr9->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this date \"Available from\" does not exist.</p><strong>Re-enter publication date and expiration date!</strong>');
            $this->dlgToastr9->ProgressBar = true;
            $this->dlgToastr9->TimeOut = 10000;
            $this->dlgToastr9->EscapeHtml = false;

            $this->dlgToastr10 = new Q\Plugin\Toastr($this);
            $this->dlgToastr10->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr10->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr10->Message = t('<strong>Well done!</strong> The message has been sent to the editor-in-chief of the site for review, correction or approval!');
            $this->dlgToastr10->ProgressBar = true;
            $this->dlgToastr10->TimeOut = 10000;

            $this->dlgToastr11 = new Q\Plugin\Toastr($this);
            $this->dlgToastr11->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr11->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr11->Message = t('<strong>Well done!</strong> A message has been sent to the editor-in-chief of the site to cancel the confirmation!');
            $this->dlgToastr11->ProgressBar = true;
            $this->dlgToastr11->TimeOut = 10000;

            $this->dlgToastr12 = new Q\Plugin\Toastr($this);
            $this->dlgToastr12->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr12->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr12->Message = t('<strong>Sorry</strong>, failed to save or edit post!');
            $this->dlgToastr12->ProgressBar = true;

            $this->dlgToastr13 = new Q\Plugin\Toastr($this);
            $this->dlgToastr13->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr13->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr13->Message = t('<strong>Well done!</strong> Updates to some records for this post were discarded, and the record has been restored!');
            $this->dlgToastr13->ProgressBar = true;

            $this->dlgToastr14 = new Q\Plugin\Toastr($this);
            $this->dlgToastr14->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr14->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr14->Message = t('<strong>Sorry</strong>, this field is required!');
            $this->dlgToastr14->ProgressBar = true;
            $this->dlgToastr14->EscapeHtml = false;
        }

        /**
         * Creates and initializes a collection of modal dialogs used for various user interactions.
         *
         * This method defines a series of modals, each with customized properties including text,
         * title, header classes, associated buttons, and event actions. These modals are designed
         * for actions like confirming deletions, providing success messages, issuing warnings,
         * and other user prompts related to news management.
         *
         * @return void
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete this news?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone.</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
                ['class' => 'btn btn-default']);
            $this->dlgModal1->addAction(new DialogButton(), new Ajax('deleteItem_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move this news from this newsgroup to another newsgroup?</p>
                                ');
            $this->dlgModal2->Title = t('Warning');
            $this->dlgModal2->HeaderClasses = 'btn-danger';
            $this->dlgModal2->addButton(t("I accept"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal2->addCloseButton(t("I'll cancel"));
            $this->dlgModal2->addAction(new DialogButton(), new Ajax('moveItem_Click'));

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Success");
            $this->dlgModal5->HeaderClasses = 'btn-success';
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news is now a draft!</p>');
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this document?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone.</p>');
            $this->dlgModal6->Title = t('Warning');
            $this->dlgModal6->HeaderClasses = 'btn-danger';
            $this->dlgModal6->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal6->addButton(t("I'll cancel"), "no-pass", false, false, null,
                ['class' => 'btn btn-default']);
            $this->dlgModal6->addAction(new DialogButton(), new Ajax('deleteDocument_Click'));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and configures a new VauuTable for displaying selected list data.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createTable(): void
        {
            $this->dtgSelectedList = new Q\Plugin\Control\VauuTable($this);
            $this->dtgSelectedList->CssClass = "table vauu-table table-hover";
            $this->dtgSelectedList->UseAjax = true;
            $this->dtgSelectedList->ShowHeader = false;
            $this->dtgSelectedList->setDataBinder('dtgSelectedList_Bind');
            $this->dtgSelectedList->RowParamsCallback = [$this, 'dtgSelectedList_GetRowParams'];
            $this->dtgSelectedList->addAction(new CellClick(0, null, CellClick::rowDataValue('value')),
                new Ajax('dtgSelectedList_Click'));

            $this->dtgSelectedList->createNodeColumn(t('Document link'), QQN::NewsFiles()->Title);

            $col = $this->dtgSelectedList->createNodeColumn(t("Status"), QQN::NewsFiles()->StatusObject);
            $col->HtmlEntities = false;

            $col = $this->dtgSelectedList->createNodeColumn(t("Post date"), QQN::NewsFiles()->PostDate);
            $col->Format = 'DD.MM.YYYY';

            $col = $this->dtgSelectedList->createNodeColumn(t("Post update date"), QQN::NewsFiles()->PostUpdateDate);
            $col->Format = 'DD.MM.YYYY';

            if (!empty( $_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
                $this->dtgSelectedList->addCssClass('disabled');
            }
        }

        /**
         * Binds the data source for the selected list DataGrid.
         *
         * This method sets the data source for the dtgSelectedList DataGrid using
         * SportsAreas records filtered by the current sports calendar group ID.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function dtgSelectedList_Bind(): void
        {
            $this->dtgSelectedList->DataSource = NewsFiles::loadArrayByNewsGroupId($this->intId, QQ::Clause(QQ::orderBy(QQN::NewsFiles()->Id)));
        }

        /**
         * Retrieves row parameters for a selected item list.
         *
         * @param object $objRowObject The object representing the current row.
         * @param int $intRowIndex The index of the row.
         *
         * @return array An associative array of row parameters.
         */
        public function dtgSelectedList_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Handles the click event for the dtgSelectedList component.
         *
         * This method loads information about a selected sports area based on the
         * provided action parameter, updates the UI elements visibility and interactivity,
         * and sets the relevant data for editing.
         *
         * @param ActionParams $params Parameters containing the action details,
         *                             including the identifier for the selected sports area.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function dtgSelectedList_Click(ActionParams $params): void
        {
            $this->intDocument = intval($params->ActionParameter);
            $objNewsFile = NewsFiles::loadById($this->intDocument);
            $objFile = Files::loadById($objNewsFile->getFilesId());

            //this->dtgSelectedList->addCssClass('disabled');
            $this->btnSelectedSave->setCssStyle('margin-top', '15px');
            $this->btnSelectedSave->setCssStyle('margin-bottom', '15px');
            $this->btnDocumentLink->Enabled = false;

            $this->txtSelectedTitle->Display = true;
            $this->lstSelectedStatus->Display = true;
            $this->btnSelectedSave->Display = true;
            $this->btnSelectedCheck->Display = true;
            $this->btnSelectedDelete->Display = true;
            $this->btnSelectedCancel->Display = true;

            $this->txtSelectedTitle->Text = $objNewsFile->getTitle();
            $this->lstSelectedStatus->SelectedValue = $objNewsFile->getStatus();
            $this->txtSelectedTitle->setDataAttribute('open', 'true');
            $this->txtSelectedTitle->setDataAttribute('view', APP_UPLOADS_URL . $objFile->getPath());
            $this->txtSelectedTitle->focus();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves a list of items based on the change condition and clauses provided.
         *
         * This method queries the change data using the specified condition and clauses,
         * iterates through the results, and creates a list of items. Each item represents
         * a change and is constructed as a `ListItem` object with display text, a value,
         * and additional configurations such as whether it is selected or disabled.
         *
         * Items that are associated with the current news (`$this->objNews->Changes`) are
         * marked as selected, and items with a status of 2 are marked as disabled.
         *
         * @return array A list of `ListItem` objects representing the available changes.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstChanges_GetItems(): array
        {
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
                // use it only on a specific page. You just have to use the style.

                if ($objChanges->Status == 2) {
                    $objListItem->Disabled = true;
                }
                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Retrieves a list of category items to be used in a dropdown or similar UI element.
         *
         * This method queries the available categories based on a predefined condition and transforms
         * them into a list of selectable items, marking the corresponding category as selected or disabled
         * where applicable.
         *
         * @return array The array of ListItem objects representing the available categories,
         *               with appropriate selection and disabled states applied.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstCategory_GetItems(): array
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
                //  use it only on a specific page. You just have to use the style.

                if ($objNewsCategory->IsEnabled == 2) {
                    $objListItem->Disabled = true;
                }
                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Handles changes to the selected value in the change dropdown and updates the associated news object.
         *
         * This method performs the following actions:
         * - Updates the changes ID of the news object based on the selected value from the change dropdown.
         * - Persists the updated news object and triggers a notification.
         * - Updates the post-update date and assigns the logged-in user's name as an editor.
         * - Refreshes the UI elements reflecting the updated data.
         *
         * @param ActionParams $params The parameters associated with the action triggered by the change dropdown.
         *
         * @return void
         * @throws Caller
         */
        protected function lstChanges_Change(ActionParams $params): void
        {
            if ($this->lstChanges->SelectedValue !== null) {
                $this->objNews->setChangesId($this->lstChanges->SelectedValue);
            } else {
                $this->objNews->setChangesId(null);
            }

            $this->objNews->setPostUpdateDate(Q\QDateTime::now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->dlgToastr1->notify();

            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());

            NewsChanges::updateAllChangeLockStates();
            $this->refreshDisplay();
        }

        /**
         * Handles changes to the selected news category in a dropdown or similar UI element.
         *
         * This method updates the associated news object's category based on the selection,
         * saves any changes to the database, and performs relevant updates to the UI.
         * It also refreshes the display with the new data and logs updated information
         * such as the post-update date and assigned editors.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstNewsCategory_Change(): void
        {
            if ($this->lstNewsCategory->SelectedValue !== null) {
                $this->objNews->setNewsCategoryId($this->lstNewsCategory->SelectedValue);
                $this->objNews->setCategory(News::loadByIdFromCategory($this->lstNewsCategory->SelectedValue));
            } else {
                $this->objNews->setNewsCategoryId(null);
                $this->objNews->setCategory(null);
            }

            $this->objNews->setPostUpdateDate(Q\QDateTime::now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->dlgToastr1->notify();

            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());

            CategoryOfNews::updateAllNewsCategoryStates();
            $this->refreshDisplay();
        }

        /**
         * Handles the status change of a news item and performs appropriate actions.
         *
         * This method checks the current status of the news object and triggers different functionalities
         * based on the status value. If the status is 1, it displays a modal dialog box. Otherwise, it locks
         * specific input fields to restrict further changes.
         *
         * @return void
         */
        protected  function lstStatus_Change(): void
        {
            $this->objNews->setStatus($this->lstStatus->SelectedValue);
            $this->objNews->setPostUpdateDate(Q\QDateTime::now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            if ($this->objNews->getStatus() === 1) {
                $this->dlgModal4->showDialogBox();
            } else if ($this->objNews->getStatus() === 2) {
                $this->dlgModal3->showDialogBox();
            } else if ($this->objNews->getStatus() === 3) {
                $this->dlgModal5->showDialogBox();
            }

            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());

            $this->refreshDisplay();
        }

        /**
         * Handles the change event for the group title dropdown.
         *
         * This method checks if the selected value of the group title dropdown differs from
         * the currently assigned newsgroup title ID. If there is a mismatch, it triggers
         * the display of a modal dialog box.
         *
         * @param ActionParams $params The parameters related to the action event triggered by user interaction.
         *
         * @return void This method does not return a value.
         */
        protected function lstGroupTitle_Change(ActionParams $params): void
        {
            if ($this->lstGroupTitle->SelectedValue !== $this->objNews->getNewsGroupTitleId()) {
                $this->dlgModal2->showDialogBox();
            }
        }

        /**
         * Handles the action of moving a news item from one group to another when triggered.
         *
         * This method performs several actions to ensure a successful transfer of a news item:
         * - It validates and updates the lock status of the source and target groups based on the number of associated news items.
         * - Updates the news item's details, including its group, title, title slug, and other related properties.
         * - Updates frontend links and slug values to reflect the changes.
         * - Redirects the user to the appropriate page after the operation and triggers notifications for success or failure.
         *
         * @param ActionParams $params Action parameters passed to the method.
         *
         * @return void This method does not return a value but modifies data and redirects the user to a new page.
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function moveItem_Click(ActionParams $params): void
        {
            $this->dlgModal2->hideDialogBox();

            $objGroupTitle = NewsSettings::loadById($this->lstGroupTitle->SelectedValue);

            // Before proceeding to other activities, the initial data of the "news" and "title_of_newsgroup" tables must be fixed.
            $objLockedGroup = $this->objNews->getNewsGroupTitleId();
            $objTargetGroup = NewsSettings::loadById($this->lstGroupTitle->SelectedValue);

            $currentCount = News::countByNewsGroupTitleId($objLockedGroup);
            $nextCount = News::countByNewsGroupTitleId($objTargetGroup->getId());

            // Here you must first check the lock status of the next folder to do this check.
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

            foreach ($this->objNewsFiles as $objNewsFile) {
                $objNewsFile = NewsFiles::loadById($objNewsFile->getId());
                $objNewsFile->setMenuContentGroupId($objGroupTitle->getMenuContentId());
                $objNewsFile->save();
            }

            $this->objFrontendLinks->setGroupedId($objGroupTitle->getMenuContentId());
            $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
            $this->objFrontendLinks->setFrontendTitleSlug(str_replace($find, $replace, $this->objFrontendLinks->getFrontendTitleSlug()));
            $this->objFrontendLinks->save();

            $this->objNews->setTitle($this->txtTitle->Text);
            $this->objNews->setMenuContentId($objGroupTitle->getMenuContentId());
            $this->objNews->setNewsGroupTitleId($this->lstGroupTitle->SelectedValue);
            $this->objNews->setGroupTitle($this->lstGroupTitle->SelectedName);
            $this->objNews->setTitleSlug(str_replace($find, $replace, $this->objFrontendLinks->getFrontendTitleSlug()));
            $this->objNews->saveNews($this->txtTitle->Text, $objGroupTitle->getTitleSlug());
            $this->objNews->setPostUpdateDate(Q\QDateTime::now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            // We are updating the slug
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                '/' . $replace . '/' . QString::sanitizeForUrl($this->objNews->getTitle());
            $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);

            Application::redirect('news_edit.php?id=' . $this->objNews->getId() . '&group=' . $objGroupTitle->getMenuContentId());

            ///////////////////////////////////////////////////////////////////////////////////////////

            // Since we are using news transfer from one group to another, and we need to refresh the page with Application::redirect(),
            // we can't report on the success of the news transfer, so let these Toasts remain as they are...

            $objAfterNewsSlug = News::load($this->objNews->getId());
            $afterSlug = $objAfterNewsSlug->getTitleSlug();

            if ($beforeSlug !== $afterSlug) {
                $this->dlgToastr1->notify();
            } else {
                $this->dlgToastr12->notify();
            }
        }

        /**
         * Sets or updates the usage of publication dates for the associated news entity.
         *
         * This method determines whether publication date constraints (available from and expiry date)
         * should be applied based on the user's input, adjusts related UI elements, updates
         * the status and relevant fields in the associated news entity, and applies localization
         * scripts if necessary.
         *
         * @param ActionParams $params The parameters associated with the action triggering this method.
         *
         * @return void
         * @throws Exception If any error occurs during the processing of this method.
         */
        public function setUse_PublicationDate(ActionParams $params): void
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

                $this->calAvailableFrom->Text = '';
                $this->calExpiryDate->Text = '';

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

            $this->objNews->setPostUpdateDate(Q\QDateTime::now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        }

        /**
         * Sets the available from date for the news and adjusts the related fields and UI elements accordingly.
         *
         * This method determines the user input for the "available from" date and either applies it
         * or resets the associated values and elements when no input is provided. It also ensures
         * that the necessary updates to the news object and related settings are handled before saving the changes.
         *
         * @param ActionParams $params Object containing the parameters associated with the action triggering this
         *     method.
         *
         * @return void
         * @throws InvalidCast
         * @throws Caller
         */
        protected function setDate_AvailableFrom(ActionParams $params): void
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

                $this->calAvailableFrom->Text = '';
                $this->calExpiryDate->Text = '';

                $this->lstStatus->Enabled = true;
                $this->lstStatus->SelectedValue = 2;

                $this->objNews->setUsePublicationDate(0);
                $this->objNews->setStatus(2);
                $this->objNews->setAvailableFrom(null);
                $this->objNews->setExpiryDate(null);

                $this->dlgToastr7->notify();
            }
            $this->renderActionsWithId();

            $this->objNews->setPostUpdateDate(Q\QDateTime::now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        }

        /**
         * Sets the expiry date for a news item based on provided parameters and manages associated UI elements.
         *
         * This method evaluates the provided date values, ensuring appropriate logical checks are performed
         * between the available and expiry dates. It also handles notifications, UI updates, and saves the
         * changes back to the news item.
         *
         * @param ActionParams $params The parameters provided for the action, containing context and necessary inputs.
         *
         * @return void
         * @throws Exception If the date processing fails or encounters inconsistencies.
         */
        protected function setDate_ExpiryDate(ActionParams $params): void
        {
            if ($this->calAvailableFrom->Text && $this->calExpiryDate->Text) {
                if (new DateTime($this->calAvailableFrom->Text) > new DateTime($this->calExpiryDate->Text)) {
                    $this->calExpiryDate->Text = '';
                    $this->objNews->setExpiryDate(null);

                    $this->dlgToastr6->notify();
                } else if ($this->calExpiryDate->Text) {
                    $this->objNews->setExpiryDate($this->calExpiryDate->DateTime);

                    $this->dlgToastr4->notify();
                } else {
                    $this->calExpiryDate->Text = '';
                    $this->objNews->setExpiryDate(null);

                    $this->dlgToastr8->notify();
                }
            } else if ($this->calAvailableFrom->Text && !$this->calExpiryDate->Text) {
                $this->calExpiryDate->Text = '';
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

                $this->calAvailableFrom->Text = '';
                $this->calExpiryDate->Text = '';

                $this->objNews->setUsePublicationDate(0);
                $this->objNews->setStatus(2);
                $this->objNews->setAvailableFrom(null);
                $this->objNews->setExpiryDate(null);

                $this->dlgToastr9->notify();
            }

            $this->renderActionsWithId();

            $this->objNews->setPostUpdateDate(Q\QDateTime::now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        }

        /**
         * Handles the click event for confirmation settings and updates the news item accordingly.
         *
         * This method is responsible for adjusting the state of various fields and settings
         * based on whether confirmation asking is enabled or disabled. It ensures that the news
         * item reflects the intended state and triggers appropriate notifications.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void This method does not return a value.
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
                $this->chkUsePublicationDate->Checked = false;
                $this->lblAvailableFrom->Display = false;
                $this->calAvailableFrom->Display = false;
                $this->lblExpiryDate->Display = false;
                $this->calExpiryDate->Display = false;
                $this->lstStatus->Enabled = false;
                $this->chkUsePublicationDate->Enabled = false;
                $this->lstStatus->SelectedValue = 2;

                $this->calAvailableFrom->Text = '';
                $this->calExpiryDate->Text = '';

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

        /**
         * Saves image details and updates associated news post information.
         *
         * This method processes an action to save the image and updates various details of the related news post.
         * It ensures that the file is marked as locked if not already, assigns the picture to the news post, updates
         * metadata such as post-update date and editor information, and refreshes the UI display.
         *
         * @param ActionParams $params Parameters related to the triggered action, encapsulating input data for
         *     processing.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function imageSave_Push(ActionParams $params): void
        {
            $saveId = $this->objMediaFinder->Item;
            $objFiles = Files::loadById($saveId);

            if ($objFiles->getLockedFile() == 0) {
                $objFiles->setLockedFile($objFiles->getLockedFile() + 1);
                $objFiles->save();
            }

            $this->objNews->setPictureId($saveId);
            $this->objNews->setPostUpdateDate(Q\QDateTime::now());
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

        /**
         * Deletes the assigned image from the news item and updates related fields accordingly.
         *
         * This method handles the removal of an image associated with a news item, including unlocking
         * the file, clearing associated descriptions, and updating the metadata such as post-update date
         * and assigned editors. It also refreshes the display and user interface elements to reflect these changes.
         *
         * @param ActionParams $params The parameters required to perform the action. Likely includes contextual
         *                              details needed for processing the image deletion.
         *
         * @return void This method does not return a value but performs the necessary updates and UI refresh
         *              for the image deletion process.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function imageDelete_Push(ActionParams $params): void
        {
            $objFiles = Files::loadById($this->objNews->getPictureId());

            if ($objFiles->getLockedFile() !== 0) {
                $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                $objFiles->save();
            }

            $this->objNews->setPictureId(null);
            $this->objNews->setPictureDescription(null);
            $this->objNews->setAuthorSource(null);
            $this->objNews->setPostUpdateDate(Q\QDateTime::now());
            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

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
         * Handles the click event for the document link button.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnDocumentLink_Click(ActionParams $params): void
        {
            $_SESSION["redirect-data"] = 'news_edit.php?id=' . $this->intId . '&group=' . $this->intGroup;

            $this->btnDocumentLink->Enabled = false;

            Application::redirect('file_finder.php');
        }

        /**
         * Handles the click event for the download and save a button.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnDownloadSave_Click(ActionParams $params): void
        {
            // We check each field and add errors if necessary
            if (!$this->txtLinkTitle->Text) {
                $this->txtLinkTitle->setHtmlAttribute('required', 'required');
                $this->dlgToastr13->notify(); // If only one field is invalid
            }

            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {

                $objNewsFile = new NewsFiles();
                $objNewsFile->setNewsGroupId($this->intId);
                $objNewsFile->setMenuContentGroupId($this->objNews->getMenuContentId());
                $objNewsFile->setTitle($this->txtLinkTitle->Text);

                $objNewsFile->setFilesId($_SESSION["data_id"]);
                $objNewsFile->setStatus(2);
                $objNewsFile->setPostDate(Q\QDateTime::now());
                $objNewsFile->save();

                $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objNews->save();

                $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
                $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
                $this->refreshDisplay();

                if (NewsFiles::countByNewsGroupId($this->intId) === 0) {
                    $this->txtDocumentLink->Display = true;
                } else {
                    $this->txtDocumentLink->Display = false;
                }

                $this->btnDocumentLink->Display = true;
                $this->txtLinkTitle->Display = false;
                $this->btnDownloadSave->Display = false;
                $this->btnDownloadCancel->Display = false;

                $this->txtLinkTitle->Text = '';

                $this->dtgSelectedList->removeCssClass('disabled');
                $this->dtgSelectedList->refresh();

                $this->dlgToastr1->notify();

                unset($_SESSION["data_id"]);
                unset($_SESSION["data_name"]);
                unset($_SESSION["data_path"]);
            }
        }

        /**
         * Handles the click event for the download cancel button.
         * Resets the UI elements and session data related to file download.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnDownloadCancel_Click(ActionParams $params): void
        {
            if (NewsFiles::countByNewsGroupId($this->intId) === 0) {
                $this->txtDocumentLink->Display = true;
            } else {
                $this->txtDocumentLink->Display = false;
            }

            $this->btnDocumentLink->Display = true;
            $this->txtLinkTitle->Display = false;
            $this->btnDownloadSave->Display = false;
            $this->btnDownloadCancel->Display = false;

            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
                $objFiles = Files::loadById($_SESSION["data_id"]);

                if ($objFiles->getLockedFile() !== 0) {
                    $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                    $objFiles->save();
                }

                $this->dtgSelectedList->removeCssClass('disabled');
                $this->dtgSelectedList->refresh();

                unset($_SESSION["data_id"]);
                unset($_SESSION["data_name"]);
                unset($_SESSION["data_path"]);
            }
        }

        /**
         * Handles the 'Save' button click event for a selected item, updating the related sports areas and calendar,
         * then refreshing the display and UI elements accordingly.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnSelectedSave_Click(ActionParams $params): void
        {
            $objNewsFiles = NewsFiles::loadById($this->intDocument);
            $errors = []; // Array for tracking errors


            if (!$this->txtSelectedTitle->Text) {
                $this->txtSelectedTitle->setHtmlAttribute('required', 'required');
                $errors[] = 'txtSelectedTitle';
                $this->dlgToastr14->notify(); // If only one field is invalid
            }

            if (count($errors)) {
                return;
            }

            $objNewsFiles->setTitle($this->txtSelectedTitle->Text);
            $objNewsFiles->setStatus($this->lstSelectedStatus->SelectedValue);
            $objNewsFiles->setPostUpdateDate(Q\QDateTime::now());
            $objNewsFiles->save();

            $this->txtSelectedTitle->setDataAttribute('view', '');
            $this->txtSelectedTitle->setDataAttribute('open', 'false');

            $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objNews->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->refreshDisplay();

            $this->dtgSelectedList->removeCssClass('disabled');
            $this->btnDocumentLink->Display = true;
            $this->txtDocumentLink->Display = true;
            $this->dtgSelectedList->refresh();

            $this->txtSelectedTitle->Display = false;
            $this->lstSelectedStatus->Display = false;
            $this->btnSelectedSave->Display = false;
            $this->btnSelectedCheck->Display = false;
            $this->btnSelectedDelete->Display = false;
            $this->btnSelectedCancel->Display = false;

            $this->txtSelectedTitle->Text = '';
            $this->dtgSelectedList->refresh();

            $this->dlgToastr1->notify();
        }

        /**
         * Handle the click event for the delete button, showing the modal dialog box
         * and setting data attributes for the title element.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Caller
         */
        protected function btnSelectedDelete_Click(ActionParams $params): void
        {
            $this->dlgModal6->showDialogBox();
            $this->txtSelectedTitle->setDataAttribute('view', '');
            $this->txtSelectedTitle->setDataAttribute('open', 'false');
        }

        /**
         * Handles the click event for the cancel button in the selected section.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function btnSelectedCancel_Click(ActionParams $params): void
        {
            $this->dtgSelectedList->removeCssClass('disabled');
            $this->btnDocumentLink->Enabled = true;
            $this->dtgSelectedList->refresh();

            $this->txtSelectedTitle->Display = false;
            $this->lstSelectedStatus->Display = false;
            $this->btnSelectedSave->Display = false;
            $this->btnSelectedCheck->Display = false;
            $this->btnSelectedDelete->Display = false;
            $this->btnSelectedCancel->Display = false;

            $this->txtSelectedTitle->Text = '';
            $this->txtSelectedTitle->setDataAttribute('view', '');
            $this->txtSelectedTitle->setDataAttribute('open', 'false');
        }

        /**
         * Handles the click event to delete a document in the selected section.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function deleteDocument_Click(ActionParams $params): void
        {
            if ($params->ActionParameter == "pass") {

                $objEventFile = NewsFiles::loadById($this->intDocument);
                $objFiles = Files::loadById($objEventFile->getFilesId());

                if ($objFiles) {
                    if ($objFiles->getLockedFile() !== 0) {
                        $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                        $objFiles->save();
                    }
                }
                $objEventFile->delete();
            }
            $this->dtgSelectedList->removeCssClass('disabled');
            $this->btnDocumentLink->Enabled = true;
            $this->dtgSelectedList->refresh();

            $this->txtSelectedTitle->Display = false;
            $this->lstSelectedStatus->Display = false;
            $this->btnSelectedSave->Display = false;
            $this->btnSelectedCheck->Display = false;
            $this->btnSelectedDelete->Display = false;
            $this->btnSelectedCancel->Display = false;

            if (NewsFiles::countByNewsGroupId($this->intId) === 0) {
                $this->txtDocumentLink->Display = true;
            } else {
                $this->txtDocumentLink->Display = false;
            }

            $this->dlgModal6->hideDialogBox();
            $this->dlgToastr1->notify();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event of save a button, performing operations to update or save a news record
         * along with associated frontend links and UI updates.
         *
         * The method validates inputs, updates news and related frontend link objects, and applies a variety
         * of business rules such as conditional handling of publication dates, statuses, and titles. It also
         * ensures that changes are reflected appropriately in the UI and notifies the user of the outcome.
         *
         * @param ActionParams $params The parameters associated with the action triggering this method,
         *                              typically including event details or input data.
         *
         * @return void This method does not return any value but performs updates to backend data and UI elements.
         * @throws Caller
         * @throws InvalidCast
         * @throws Exception
         */
        public function btnSave_Click(ActionParams $params): void
        {
            $objTemplateLocking = FrontendTemplateLocking::load(4);
            $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->getId());

            $this->renderActionsWithId();

            if ($this->txtTitle->Text) {
                $this->objNews->setMenuContentId($this->intGroup);
                $this->objNews->setTitle($this->txtTitle->Text);
                $this->objNews->setContent($this->txtContent->Text);
                $this->objNews->updateNews($this->txtTitle->Text, $this->objNewsSettings->getTitleSlug());

                if (!$this->chkUsePublicationDate->Checked) {
                    $this->objNews->setStatus($this->lstStatus->SelectedValue);
                }

                if ($this->chkUsePublicationDate->Checked && $this->calAvailableFrom->Text == '') {
                    $this->chkUsePublicationDate->Checked = false;
                    $this->lblAvailableFrom->Display = false;
                    $this->calAvailableFrom->Display = false;
                    $this->lblExpiryDate->Display = false;
                    $this->calExpiryDate->Display = false;
                    $this->lstStatus->Enabled = true;

                    $this->lstStatus->SelectedValue = 2;
                    $this->calAvailableFrom->Text = '';

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

                $this->objNews->setPostUpdateDate(Q\QDateTime::now());
                $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);

                $this->objNews->save();

                $this->objFrontendLinks->setLinkedId($this->intId);
                $this->objFrontendLinks->setGroupedId($this->intGroup);
                $this->objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $this->objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
                $this->objFrontendLinks->setContentTypesManagamentId(4);
                $this->objFrontendLinks->setFrontendTitleSlug($this->objNews->getTitleSlug());
                $this->objFrontendLinks->save();

                $this->referenceValidation();

                $this->txtNewsAuthor->Text = $this->objNews->getAuthor();

                if ($this->objNews->getTitle()) {
                    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                        $this->objNews->getTitleSlug();
                    $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                    $this->txtTitleSlug->HtmlEntities = false;
                    $this->txtTitleSlug->setCssStyle('font-weight', 400);
                } else {
                    $this->txtTitleSlug->Text = t('Uncompleted link...');
                    $this->txtTitleSlug->setCssStyle('color', '#999;');
                }

                $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
                $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

                $this->refreshDisplay();

                if ($this->objNews->getContent()) {
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

                $this->dlgToastr1->notify();
            } else {
                $this->dlgToastr2->notify();
            }
        }

        /**
         * Handles the 'Save and Close' button click event to save changes made to news data
         * and associated frontend options, and performs related UI operations.
         *
         * This method processes input data from form elements, updates the news object with the
         * corresponding values, handles frontend link configurations, validates references,
         * and either redirects to a list page or displays a modal dialog based on specific conditions.
         *
         * @param ActionParams $params Parameters associated with the action event, such as
         *                              triggering element details or user inputs.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        public function btnSaveClose_Click(ActionParams $params): void
        {
            $objTemplateLocking = FrontendTemplateLocking::load(4);
            $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->getId());

            $this->renderActionsWithId();

            if ($this->txtTitle->Text) {

                $this->objNews->setMenuContentId($this->intGroup);
                $this->objNews->setTitle($this->txtTitle->Text);
                $this->objNews->setContent($this->txtContent->Text);
                $this->objNews->updateNews($this->txtTitle->Text, $this->objNewsSettings->getTitleSlug());

                if (!$this->chkUsePublicationDate->Checked) {
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

                if ($this->chkUsePublicationDate->Checked && $this->calAvailableFrom->Text == '') {
                    $this->dlgModal3->showDialogBox();
                } else {
                    $this->redirectToListPage();
                }

                $this->objNews->setPostUpdateDate(Q\QDateTime::now());
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
         *
         * @return void
         * @throws Caller
         */
        protected function itemEscape_Click(ActionParams $params): void
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

        /**
         * Handles the click event for the delete button, triggering the appropriate modal dialog based on the news
         * object's title.
         *
         * This method checks whether the news object has a title and then displays one of two modal dialogs
         * accordingly.
         *
         * @param ActionParams $params The parameters associated with the action or event triggering this method.
         *
         * @return void
         */
        public function btnDelete_Click(ActionParams $params): void
        {
            if ($this->objNews->getTitle()) {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the deletion of a news item and performs associated cleanup operations.
         *
         * This method processes a delete action triggered in the UI, validates input parameters,
         * and then executes the deletion of the relevant news item. It includes logic to manage
         * file references, unlock files, and update related settings if the newsgroup contains
         * a single item. After the deletion, users associated with this news item are disassociated,
         * and any UI dialogs are appropriately updated.
         *
         * @param ActionParams $params The action parameters containing the necessary data
         *                              for performing the delete operation, including the action identifier.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function deleteItem_Click(ActionParams $params): void
        {
            $objNewsSettings = NewsSettings::loadByIdFromNewsSettings($this->intGroup);
            $objNewsCount = count(News::loadArrayByMenuContentId($this->intGroup));

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

                foreach ($this->objNewsFiles as $objNewsFile) {
                    $objNewsFile = NewsFiles::loadById($objNewsFile->getId());
                    $objFiles = Files::loadById($objNewsFile->getFilesId());

                    if ($objFiles->getLockedFile() !== 0) {
                        $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                        $objFiles->save();
                    }

                    if ($objNewsFile->getFilesId()) {
                        $objNewsFile->delete();
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

                NewsChanges::updateAllChangeLockStates();
                CategoryOfNews::updateAllNewsCategoryStates();

                $this->redirectToListPage();
            }
            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the cancel action for an item based on the provided action parameters.
         *
         * This method performs specific operations to reset or update the state depending on the
         * action parameter value. If the parameter is "pass", it resets publication-related fields,
         * updates the status, and persists these changes. Otherwise, it closes a modal dialog and
         * focuses on a specific element.
         *
         * @param ActionParams $params The parameters containing the action details, including
         *                              the action type or trigger value.
         *
         * @return void
         * @throws Throwable
         */
        protected function cancelItem_Click(ActionParams $params): void
        {
            if ($params->ActionParameter == "pass") {

                $this->chkUsePublicationDate->Checked = false;
                $this->lblAvailableFrom->Display = false;
                $this->calAvailableFrom->Display = false;
                $this->lblExpiryDate->Display = false;
                $this->calExpiryDate->Display = false;
                $this->lstStatus->Enabled = true;

                $this->lstStatus->SelectedValue = 2;
                $this->calAvailableFrom->Text = '';

                $this->objNews->setUsePublicationDate(0);
                $this->objNews->setStatus(2);

                $this->objNews->save();

                $this->redirectToListPage();
            } else {
                $this->dlgModal3->hideDialogBox();
                $this->calAvailableFrom->focus();
            }
        }

        /**
         * Handles the click event for the cancel button, executing cleanup logic if necessary.
         *
         * This method checks whether the associated news item has a title. If the title does not exist,
         * it unassociates all users marked as editors for the news item, deletes the news item itself,
         * and also deletes any associated frontend links. Finally, it redirects the user to the list page.
         *
         * @param ActionParams $params The parameters from the button click action, including any context or data provided with the event.
         *
         * @return void
         * @throws Throwable
         */
        public function btnCancel_Click(ActionParams $params): void
        {
            if (!$this->objNews->getTitle()) {
                $this->objNews->unassociateAllUsersAsEditors();
                $this->objNews->delete();
                $this->objFrontendLinks->delete();
            }
            $this->redirectToListPage();
        }

        /**
         * Handles the event when the "Go To Changes" button is clicked.
         *
         * This method stores the current news item ID and groups into the session
         * and redirects the user to the "news changes" section of the categories manager page.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Throwable
         */
        public function btnGoToChanges_Click(ActionParams $params): void
        {
            $_SESSION['news_changes_id'] = $this->intId;
            $_SESSION['news_changes_group'] = $this->intGroup;
            Application::redirect('categories_manager.php#newsChanges_tab');
        }

        /**
         * Handles the click event for the Go To Categorize a button.
         *
         * This method stores the current news category ID and group in the session
         * and redirects the user to the categories manager page, specifically the section
         * related to news categories.
         *
         * @param ActionParams $params Parameters associated with the user action triggering this method.
         *
         * @return void This method does not return any value.
         * @throws Throwable
         */
        public function btnGoToCategories_Click(ActionParams $params): void
        {
            $_SESSION['news_categories_id'] = $this->intId;
            $_SESSION['news_categories_group'] = $this->intGroup;
            Application::redirect('categories_manager.php#newsCategories_tab');
        }

        /**
         * Handles the click event for navigating to the settings page.
         *
         * This method sets session variables related to the current news settings
         * and redirects the user to the settings manager page with the appropriate tab.
         *
         * @param ActionParams $params The parameters associated with the action triggering this event.
         *
         * @return void This method does not return a value.
         * @throws Throwable
         */
        public function btnGoToSettings_Click(ActionParams $params): void
        {
            $_SESSION['news_settings_id'] = $this->intId;
            $_SESSION['news_settings_group'] = $this->intGroup;
            Application::redirect('settings_manager.php#newsSettings_tab');
        }

        /**
         * Redirects the user to the list page for news.
         *
         * This method handles the redirection process to the predefined list page,
         * ensuring users are navigated appropriately after specific operations.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            Application::redirect('news_list.php');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Refreshes the display logic for various UI elements depending on the properties of the News object.
         *
         * This method evaluates the state of the News object, including post-date, update date, author, and
         * the number of associated editors. Based on the evaluation, it adjusts the visibility and styles
         * of corresponding UI components.
         *
         * @return void
         */
        protected function refreshDisplay(): void
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

        /**
         * Updates the associated news object with the provided data if any changes are detected and
         * records the post-update date and assigned editors.
         *
         * This method compares the current form inputs with the properties of the news object. If any
         * discrepancies are found, it updates the news object accordingly, sets the post-update timestamp,
         * assigns the current editor by their ID, and saves these changes to the database. It also updates
         * relevant fields in the UI to reflect the changes, such as the list of editors and the post-update date.
         *
         * @return void This method performs updates and UI modifications but does not return a value.
         */
        public function renderActionsWithId(): void
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
                    $this->objNews->setPostUpdateDate(Q\QDateTime::now());
                    $this->objNews->setAssignedEditorsNameById($this->intLoggedUserId);
                    $this->objNews->save();

                    $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
                    $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
                }
            }
        }

        // This function referenceValidation(), which checks and ensures that the data is up to date both when adding and
        // deleting a file. Everything is commented in the code.

        /**
         * Validates and manages the relationship between content references and associated file records.
         *
         * This method extracts ID references from content (images and links), compares them with
         * previously stored file references, and updates the state of associated files accordingly.
         * It handles locking and unlocking of files based on changes in content references.
         *
         * @return void
         * @throws Exception If processing or database updates fail.
         */
        protected function referenceValidation(): void
        {
            $objNews = News::loadById($this->objNews->getId());

            $references = $objNews->getFilesIds();
            $content = $objNews->getContent();

            // Regular expression to find the img id attribute
            $patternImgId = '/<img[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

            // Regular expression to find an id attribute
            $patternAId = '/<a[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

            $matchesImg = [];
            $matchesA = [];
            $combinedArray = [];

            if (!empty($content)) {
                // Search for a pattern
                preg_match_all($patternImgId, $content, $matchesImg);
                preg_match_all($patternAId, $content, $matchesA);

                // Check if matches were found and process only if desired
                $imgIds = $matchesImg[1] ?? [];
                $aIds = $matchesA[1] ?? [];

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
    }
    NewsEditForm::run('NewsEditForm');