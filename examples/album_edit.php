<?php
    require('qcubed.inc.php');

    require('../src/Control/GalleryUploadHandler.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Bootstrap as Bs;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Plugin\Control\VauuTable;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Action\Ajax;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\Folder;
    use QCubed\Html;
    use QCubed\QString;
    use QCubed\Query\QQ;

    /**
     * Class AlbumEditForm
     *
     * This class is an extension of the base Form class. It provides functionality for creating,
     * editing, and managing albums within the application. It sets up various controls, modals,
     * tables, and other user interface components for an album editing form.
     *
     * Properties such as file paths, upload directories, and allowed file types are defined
     * within this class. It also manages the relationships and conditions associated with user
     * permissions, gallery groups, and albums.
     *
     * Specific UI components include dialog modals, input controls, buttons, and data grids
     * that enable users to view, modify, and save album data. Additionally, this class includes
     * functionality for handling user-specific data and session-based operations, while providing
     * options for item selection and filtering.
     *
     * Protected Methods:
     * - formCreate(): Sets up the form with all necessary components like user data, albums, settings, and tables.
     * - createTable(): Initializes a table to display album data such as name, author, description, status, and more.
     * - createInputs(): Configures input elements for album fields like title, group, authors, and dates.
     * - createButtons(): Sets up various buttons such as "Save," "Cancel," and "Delete" for album-related actions.
     * - createObjects(): Handles the creation of backend objects like user data, album details, and gallery settings.
     * - createToastr(): Prepares notifications for user interaction.
     * - createModals(): Configures and initializes the modal dialogs used in the form.
     * - checkGalleryAvailability(): Verifies and sets the gallery availability status based on input conditions.
     *
     * Key Properties:
     * - Directories and paths for file handling, including root directories and temporary paths.
     * - User-defined conditions and clauses for managing relationships between data objects.
     * - UI components such as labels, input fields, buttons, alerts, modals, and data grids.
     * - Flags for tracking internal states and IDs for managing items, users, and groups.
     * - Configurable settings for UI components such as pagination and table columns.
     *
     * This class combines backend logic for handling album data with frontend UI generation, allowing for
     * a comprehensive and interactive user experience in album management.
     */
    class AlbumEditForm extends Form
    {
        /** @var string */
        protected string $strRootPath = APP_UPLOADS_DIR;
        /** @var string */
        protected string $strRootUrl = APP_UPLOADS_URL;
        /** @var string */
        protected string $strTempPath = APP_UPLOADS_TEMP_DIR;
        /** @var string */
        protected string $strTempUrl = APP_UPLOADS_TEMP_URL;
        /** @var array */
        protected array $tempFolders = ['thumbnail', 'medium', 'large'];
        /** @var array */
        protected array $arrAllowed = array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');

        protected ?object $objGalleryGroupTitleCondition = null;
        protected ?array $objGalleryGroupTitleClauses = null;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr4;
        protected Q\Plugin\Toastr $dlgToastr5;
        protected Q\Plugin\Toastr $dlgToastr6;
        protected Q\Plugin\Toastr $dlgToastr7;
        protected Q\Plugin\Toastr $dlgToastr8;
        protected Q\Plugin\Toastr $dlgToastr9;
        protected Q\Plugin\Toastr $dlgToastr10;

        protected Bs\Modal $dlgModal1;
        protected Bs\Modal $dlgModal2;
        protected Bs\Modal $dlgModal3;
        protected Bs\Modal $dlgModal4;
        protected Bs\Modal $dlgModal5;
        protected Bs\Modal $dlgModal6;
        protected Bs\Modal $dlgModal7;
        protected Bs\Modal $dlgModal8;
        protected Bs\Modal $dlgModal9;
        protected Bs\Modal $dlgModal10;
        protected Bs\Modal $dlgModal11;
        protected Bs\Modal $dlgModal12;
        protected Bs\Modal $dlgModal13;

        protected Bs\Button $btnGoUpload;

        protected VauuTable $dtgAlbumList;
        protected Q\Plugin\Control\Alert $lblInfo;

        protected Q\Plugin\Control\Label $lblTitle;
        protected Bs\TextBox $txtTitle;

        protected Q\Plugin\Control\Label $lblGroupTitle;
        protected Q\Plugin\Select2 $lstGroupTitle;
        protected Bs\Button $btnGoToSettings;

        protected Q\Plugin\Control\Label $lblTitleSlug;
        protected Q\Plugin\Control\Label $txtTitleSlug;

        protected Q\Plugin\Control\Label $lblPostDate;
        protected Bs\Label $calPostDate;

        protected Q\Plugin\Control\Label $lblPostUpdateDate;
        protected Bs\Label $calPostUpdateDate;

        protected Q\Plugin\Control\Label $lblInserter;
        protected Bs\Label $txtInserter;

        protected Q\Plugin\Control\Label $lblUsersAsEditors;
        protected Bs\Label $txtUsersAsEditors;

        ////////////////////////////////

        protected Q\Plugin\Control\Label $lblPhotoAuthor;
        protected Bs\TextBox $txtPhotoAuthor;

        protected Q\Plugin\Control\Label $lblPhotoDescription;
        protected Bs\TextBox $txtPhotoDescription;

        ////////////////////////////////

        protected Bs\TextBox $txtFileName;
        protected Bs\TextBox $txtFileAuthor;
        protected Bs\TextBox $txtFileDescription;
        protected Q\Plugin\Control\RadioList $lstIsEnabled;
        protected Bs\Button $btnPhotoSave;
        protected Bs\Button $btnPhotoCancel;

        ////////////////////////////////

        protected Q\Plugin\Control\Label $lblStatus;
        protected Q\Plugin\Control\RadioList $lstStatus;

        protected Bs\Button $btnAlbumSave;
        protected Bs\Button $btnAlbumDelete;
        protected Bs\Button $btnAlbumCancel;

        protected int $intId;
        protected int $intGroup;
        protected int $intFolder;
        protected object $objGalleryList;
        protected object $objGroup;
        protected object $objFolder;
        protected object $objSettings;
        protected object $objFrontendLinks;
        protected ?string $oldTitle = null;
        protected ?string $oldTitleSlug = null;

        protected bool $blnCheckGallery;
        protected int $intDataNumbers;
        protected object $objUser;
        protected int $intLoggedUserId;
        protected ?int $intChangeFilesId = null;
        protected ?int $intDeleteId = null;
        protected ?object $intAlbum = null;
        protected ?object $objAlbum = null;

        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        ////////////////////////////////

        protected int $oldGroupTitleId;
        protected array $tempSelectedItems = [];

        /**
         * Initializes the form by setting up various properties, loading necessary data,
         * creating UI components and verifying gallery availability.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->intId = Application::instance()->context()->queryStringItem('id');
            $this->intGroup = Application::instance()->context()->queryStringItem('group');
            $this->intFolder = Application::instance()->context()->queryStringItem('folder');

            if (!empty($this->intId)) {
                $this->objGalleryList = GalleryList::load($this->intId);
            }

            $this->objAlbum = Album::load($this->intId);
            $this->objSettings = GallerySettings::load($this->intGroup);
            $this->objFolder = Folders::load($this->intFolder);
            $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must save something here $this->objGalleryList->setUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session

            $this->intLoggedUserId = 1;
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createTable();
            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
            $this->checkGalleryAvailability();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and creates the album table with various columns and settings.
         * This method configures a table to display album-related information, including
         * view, name, author, description, status, and actions. It also sets up pagination
         * and a dropdown for selecting the number of items displayed per page.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createTable(): void
        {
            $this->blnCheckGallery = Album::countByFolderId($this->intFolder);

            $this->dtgAlbumList = new VauuTable($this);
            $this->dtgAlbumList->CssClass = "table vauu-table table-hover";

            $col = $this->dtgAlbumList->createCallableColumn(t('View'), [$this, 'View_render']);
            $col->HtmlEntities = false;
            $col->CellStyler->Width = '8%';

            $col = $this->dtgAlbumList->createCallableColumn(t('Name'), [$this, 'Name_render']);
            $col->HtmlEntities = false;
            $col->CellStyler->Width = '15%';

            $col = $this->dtgAlbumList->createCallableColumn(t('Author'), [$this, 'PhotoAuthor_render']);
            $col->HtmlEntities = false;
            $col->CellStyler->Width = '24%';

            $col = $this->dtgAlbumList->createCallableColumn(t('Brief description'), [$this, 'PhotoDescription_render']);
            $col->HtmlEntities = false;
            $col->CellStyler->Width = '25%';

            $col = $this->dtgAlbumList->createCallableColumn(t('Status'), [$this, 'IsEnabled_render']);
            $col->HtmlEntities = false;
            $col->CellStyler->Width = '16%';

            $col = $this->dtgAlbumList->createCallableColumn(t('Actions'), [$this, 'Change_render']);
            $col->HtmlEntities = false;
            $col->CellStyler->Width = '12%';

            $this->dtgAlbumList->UseAjax = true;
            $this->dtgAlbumList->setDataBinder('dtgAlbumList_Bind');
        }

        /**
         * Binds data to the album list by setting the total item count and loading the appropriate data source.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function dtgAlbumList_Bind(): void
        {
            $this->dtgAlbumList->DataSource = Album::loadArrayByFolderId($this->intFolder, QQ::Clause(QQ::orderBy(QQN::Album()->Name)));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and creates input controls necessary for managing and editing a gallery.
         * This includes setting up labels, text boxes, list selectors, and actions for various
         * fields such as title, group, description, author, and more. The method configures
         * input styles, required attributes, placeholders, and binds event actions with
         * appropriate handlers.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createInputs(): void
        {
            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('There are no images in the album. Click the "Go to upload area" button to upload suitable images.');

            $this->lblTitle = new Q\Plugin\Control\Label($this);
            $this->lblTitle->Text = t('Title');
            $this->lblTitle->addCssClass('col-md-3');
            $this->lblTitle->setCssStyle('font-weight', 400);
            $this->lblTitle->Required = true;

            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Album title');
            $this->txtTitle->Text = $this->objGalleryList->getTitle() ?? null;
            $this->txtTitle->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtTitle->AddAction(new EnterKey(), new Ajax('btnAlbumSave_Click'));
            $this->txtTitle->addAction(new EnterKey(), new Terminate());
            $this->txtTitle->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtTitle->addAction(new EscapeKey(), new Terminate());
            $this->txtTitle->setHtmlAttribute('required', 'required');

            $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
            $this->lblGroupTitle->Text = t('Gallery group');
            $this->lblGroupTitle->addCssClass('col-md-3');
            $this->lblGroupTitle->setCssStyle('font-weight', 400);

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '90%';
            $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;

            $this->lstGroupTitle->setCssStyle('float', 'left');
            $this->lstGroupTitle->addItem(t('- Change gallery group -'), null, true);
            $this->lstGroupTitle->addItems($this->lstGalleryGroupTitle_GetItems());
            $this->lstGroupTitle->SelectedValue = $this->objGalleryList->getGalleryGroupTitleId();
            $this->lstGroupTitle->addAction(new Change(), new Ajax('lstGroupTitle_Change'));

            $this->oldGroupTitleId = $this->lstGroupTitle->SelectedValue;

            $countByIsReserved = GallerySettings::countByIsReserved(1);

            if ($countByIsReserved === 1) {
                $this->lstGroupTitle->Enabled = false;
            } else {
                $this->lstGroupTitle->Enabled = true;
            }

            $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
            $this->lblTitleSlug->Text = t('View');
            $this->lblTitleSlug->addCssClass('col-md-3');
            $this->lblTitleSlug->setCssStyle('font-weight', 400);

            if ($this->objGalleryList->getTitleSlug()) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
                $this->txtTitleSlug->setCssStyle('text-align', 'left;');
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objGalleryList->getTitleSlug();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            $this->lblPostDate = new Q\Plugin\Control\Label($this);
            $this->lblPostDate->Text = t('Created');
            $this->lblPostDate->setCssStyle('font-weight', 'bold');

            $this->calPostDate = new Bs\Label($this);
            $this->calPostDate->Text = $this->objGalleryList->PostDate ? $this->objGalleryList->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostDate->setCssStyle('font-weight', 'normal');

            $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
            $this->lblPostUpdateDate->Text = t('Updated');
            $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

            $this->calPostUpdateDate = new Bs\Label($this);
            $this->calPostUpdateDate->Text = $this->objGalleryList->PostUpdateDate ? $this->objGalleryList->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

            $this->lblInserter = new Q\Plugin\Control\Label($this);
            $this->lblInserter->Text = t('Album author');
            $this->lblInserter->setCssStyle('font-weight', 'bold');

            $this->txtInserter = new Bs\Label($this);
            $this->txtInserter->Text = $this->objGalleryList->Author;
            $this->txtInserter->setCssStyle('font-weight', 'normal');

            $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
            $this->lblUsersAsEditors->Text = t('Editors');
            $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

            $this->txtUsersAsEditors = new Bs\Label($this);
            $this->txtUsersAsEditors->Text = implode(', ', $this->objGalleryList->getUserAsEditorsArray());
            $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

            $this->refreshDisplay();

            $this->lblPhotoAuthor = new Q\Plugin\Control\Label($this);
            $this->lblPhotoAuthor->Text = t('Photo author/source');
            $this->lblPhotoAuthor->setCssStyle('font-weight', 'bold');

            $this->txtPhotoAuthor = new Bs\TextBox($this);
            $this->txtPhotoAuthor->Text = $this->objGalleryList->ListAuthor;
            $this->txtPhotoAuthor->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtPhotoAuthor->AddAction(new EnterKey(), new Ajax('btnAlbumSave_Click'));
            $this->txtPhotoAuthor->addAction(new EnterKey(), new Terminate());
            $this->txtPhotoAuthor->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtPhotoAuthor->addAction(new EscapeKey(), new Terminate());

            $this->lblPhotoDescription = new Q\Plugin\Control\Label($this);
            $this->lblPhotoDescription->Text = t('Brief description');
            $this->lblPhotoDescription->setCssStyle('font-weight', 'bold');

            $this->txtPhotoDescription = new Bs\TextBox($this);
            $this->txtPhotoDescription->Text = $this->objGalleryList->ListDescription;
            $this->txtPhotoDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtPhotoDescription->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtPhotoDescription->AddAction(new EnterKey(), new Ajax('btnAlbumSave_Click'));
            $this->txtPhotoDescription->addAction(new EnterKey(), new Terminate());
            $this->txtPhotoDescription->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtPhotoDescription->addAction(new EscapeKey(), new Terminate());

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->setCssStyle('font-weight', 'bold');

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden'), 3 => t('Draft')]);
            $this->lstStatus->SelectedValue = $this->objGalleryList->Status;
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
            $this->lstStatus->Enabled = true;
            $this->lstStatus->AddAction(new Change(), new Ajax('lstStatus_Change'));

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->txtFileName = new Bs\TextBox($this->dtgAlbumList);
            $this->txtFileName->setHtmlAttribute('required', 'required');
            $this->txtFileName->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;

            $this->txtFileName->AddAction(new EnterKey(), new Ajax('btnPhotoSave_Click'));
            $this->txtFileName->addAction(new EnterKey(), new Terminate());
            $this->txtFileName->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtFileName->addAction(new EscapeKey(), new Terminate());


            $this->txtFileDescription = new Bs\TextBox($this->dtgAlbumList);
            $this->txtFileDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtFileDescription->Rows = 2;
            $this->txtFileDescription->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;

            $this->txtFileDescription->AddAction(new EnterKey(), new Ajax('btnPhotoSave_Click'));
            $this->txtFileDescription->addAction(new EnterKey(), new Terminate());
            $this->txtFileDescription->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtFileDescription->addAction(new EscapeKey(), new Terminate());

            $this->txtFileAuthor = new Bs\TextBox($this->dtgAlbumList);
            $this->txtFileAuthor->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;

            $this->txtFileAuthor->AddAction(new EnterKey(), new Ajax('btnPhotoSave_Click'));
            $this->txtFileAuthor->addAction(new EnterKey(), new Terminate());
            $this->txtFileAuthor->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtFileAuthor->addAction(new EscapeKey(), new Terminate());

            $this->lstIsEnabled = new Q\Plugin\Control\RadioList($this->dtgAlbumList);
            $this->lstIsEnabled->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstIsEnabled->ButtonGroupClass = 'radio radio-orange'; //  radio-inline
            $this->lstIsEnabled->AddAction(new Change(), new Ajax('lstIsEnabled_Change'));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Renders a preview for a given album, displaying either a thumbnail or a file icon based on the file extension.
         *
         * @param Album $objAlbum The album object containing details such as file path.
         * @return string The rendered HTML content for the album preview.
         */
        public function View_render(Album $objAlbum): string
        {
            $ext = strtolower(pathinfo($this->strRootUrl . $objAlbum->Path, PATHINFO_EXTENSION));

            $strHtm = '<span class="preview">';
            if (in_array($ext, $this->arrAllowed)) {
                $strHtm .= '<img src="' . $this->strTempUrl . '/_files/thumbnail' . $objAlbum->Path . '">';
            } else {
                $strHtm .= self::getFileIconExtension($ext);
            }
            $strHtm .= '</span>';
            return $strHtm;
        }

        /**
         * Renders the name of the album based on specific conditions.
         *
         * @param Album $objAlbum The album object whose name is to be rendered.
         *
         * @return string Rendered output for the album name.
         * @throws Caller
         */
        public function Name_render(Album $objAlbum): string
        {
            if ($objAlbum->Id == $this->intChangeFilesId) {
                return $this->txtFileName->render(false);
            } else {
                // return QCubed::truncate($objAlbum->Name, 25);
                return wordwrap($objAlbum->Name, 25, "\n", true);
            }
        }

        /**
         * Renders the "Is Enabled" state of a given album based on its ID.
         *
         * @param Album $objAlbum The album object for which the "Is Enabled" state is to be rendered.
         *
         * @return string Returns the rendered output of the "Is Enabled" list if the album ID matches the given
         *     condition, or the status object of the album otherwise.
         * @throws Caller
         */
        public function IsEnabled_render(Album $objAlbum): string
        {
            if ($objAlbum->Id == $this->intChangeFilesId) {
                return $this->lstIsEnabled->render(false);
            } else {
                return $objAlbum->StatusObject;
            }
        }

        /**
         * Renders the photo description for a given album or provides an input field for editing.
         *
         * @param Album $objAlbum The album object whose photo description is to be rendered.
         *
         * @return null|string The rendered description or the input field for editing if in edit mode.
         * @throws Caller
         */
        public function PhotoDescription_render(Album $objAlbum): ?string
        {
            if ($objAlbum->Id == $this->intChangeFilesId) {
                return $this->txtFileDescription->render(false);
            } else {
                return $objAlbum->Description;
            }
        }

        /**
         * Renders the author of a photo for a given album.
         *
         * @param Album $objAlbum The album object for which the photo author is being rendered.
         *
         * @return null|string The rendered photo author if the album ID matches, otherwise the album's photo author.
         * @throws Caller
         */
        public function PhotoAuthor_render(Album $objAlbum): ?string
        {
            if ($objAlbum->Id == $this->intChangeFilesId) {
                return $this->txtFileAuthor->render(false);
            } else {
                return $objAlbum->Author;
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and initializes various buttons with specific properties, styles, and behaviors.
         *
         * This method defines multiple buttons used for file uploads, album management, and navigation within the
         * gallery settings. It assigns distinct styles, text, actions, and behaviors to each button to ensure proper
         * functionality and user interactivity.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnGoUpload = new Bs\Button($this);
            $this->btnGoUpload->Text = t('Go to the upload area');
            $this->btnGoUpload->CssClass = 'btn btn-orange';
            $this->btnGoUpload->setCssStyle('float', 'left');
            $this->btnGoUpload->CausesValidation = false;
            $this->btnGoUpload->UseWrapper = false;
            $this->btnGoUpload->addAction(new Click(), new Ajax('btnGoUpload_Click'));

            $this->btnPhotoSave = new Bs\Button($this->dtgAlbumList);
            $this->btnPhotoSave->Text = t('Save');
            $this->btnPhotoSave->CssClass = 'btn btn-orange';
            $this->btnPhotoSave->addAction(new Click(), new Ajax('btnPhotoSave_Click'));
            $this->btnPhotoSave->PrimaryButton = true;

            $this->btnPhotoCancel = new Bs\Button($this->dtgAlbumList);
            $this->btnPhotoCancel->Text = t('Cancel');
            $this->btnPhotoCancel->setCssStyle('margin-top', '7px');
            $this->btnPhotoCancel->addAction(new Click(), new Ajax('btnPhotoCancel_Click'));
            $this->btnPhotoCancel->CausesValidation = false;

            $this->btnAlbumSave = new Bs\Button($this);
            $this->btnAlbumSave->Text = t('Update');
            $this->btnAlbumSave->CssClass = 'btn btn-orange js-album-save';
            $this->btnAlbumSave->addWrapperCssClass('center-button');
            $this->btnAlbumSave->PrimaryButton = true;
            $this->btnAlbumSave->addAction(new Click(), new Ajax('btnAlbumSave_Click'));

            $this->btnAlbumDelete = new Bs\Button($this);
            $this->btnAlbumDelete->Text = t('Delete');
            $this->btnAlbumDelete->CssClass = 'btn btn-danger js-album-delete';
            $this->btnAlbumDelete->addWrapperCssClass('center-button');
            $this->btnAlbumDelete->CausesValidation = false;
            $this->btnAlbumDelete->addAction(new Click(), new Ajax('btnAlbumDelete_Click'));

            $this->btnAlbumCancel = new Bs\Button($this);
            $this->btnAlbumCancel->Text = t('Back');
            $this->btnAlbumCancel->CssClass = 'btn btn-default js-album-cancel';
            $this->btnAlbumCancel->addWrapperCssClass('center-button');
            $this->btnAlbumCancel->CausesValidation = false;
            $this->btnAlbumCancel->addAction(new Click(), new Ajax('btnAlbumCancel_Click'));

            $this->btnGoToSettings = new Bs\Button($this);
            $this->btnGoToSettings->Tip = true;
            $this->btnGoToSettings->ToolTip = t('Go to the gallery settings manager');
            $this->btnGoToSettings->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToSettings->CssClass = 'btn btn-default';
            $this->btnGoToSettings->setCssStyle('float', 'right');
            $this->btnGoToSettings->addWrapperCssClass('center-button');
            $this->btnGoToSettings->CausesValidation = false;
            $this->btnGoToSettings->addAction(new Click(), new Ajax('btnGoToSettings_Click'));
        }


        /**
         * Creates and configures multiple Toastr objects for displaying various alert messages.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> Album update was successful');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('<strong>Sorry!</strong> You cannot update an album with the same album title');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('<strong>Well done!</strong> File changed successfully.');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('<strong>Sorry!</strong> Failed to change file.');
            $this->dlgToastr5->ProgressBar = true;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('<strong>Well done!</strong> File deleted successfully.');
            $this->dlgToastr6->ProgressBar = true;

            $this->dlgToastr7 = new Q\Plugin\Toastr($this);
            $this->dlgToastr7->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr7->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr7->Message = t('<strong>Sorry!</strong> Failed to delete file.');
            $this->dlgToastr7->ProgressBar = true;

            $this->dlgToastr8 = new Q\Plugin\Toastr($this);
            $this->dlgToastr8->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr8->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr8->Message = t('<strong>Well done!</strong> This image has now been made public!');
            $this->dlgToastr8->ProgressBar = true;

            $this->dlgToastr9 = new Q\Plugin\Toastr($this);
            $this->dlgToastr9->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr9->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr9->Message = t('<strong>Well done!</strong> This image is now hidden!');
            $this->dlgToastr9->ProgressBar = true;

            $this->dlgToastr10 = new Q\Plugin\Toastr($this);
            $this->dlgToastr10->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr10->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr10->Message = t('<strong>Well done!</strong> Updates to some records for this post were discarded, and the record has been restored!');
            $this->dlgToastr10->ProgressBar = true;
        }

        /**
         * Initializes and configures multiple modal dialog instances with specific properties and behaviors.
         *
         * @return void
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Title = t('Tip');
            $this->dlgModal1->Text = t('<p style="margin-top: 15px;">The album cannot be updated without a name!</p>');
            $this->dlgModal1->HeaderClasses = 'btn-darkblue';
            $this->dlgModal1->addCloseButton(t("I close the window"));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Title = t('Tip');
            $this->dlgModal2->Text = t('<p style="margin-top: 15px;">If the brief description or author name/source is not filled out,
                                    neither field will be displayed under the images in the gallery!</p>
                                    <p style="margin-top: 25px; margin-bottom: 15px;">Please fill in both fields or omit them!</p>');
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addCloseButton(t("I close the window"));

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this file?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
            $this->dlgModal3->Title = 'Warning';
            $this->dlgModal3->HeaderClasses = 'btn-danger';
            $this->dlgModal3->addButton("I accept", 'This file has been permanently deleted', false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal3->addCloseButton(t("I'll cancel"));
            $this->dlgModal3->addAction(new DialogButton(), new Ajax('photoDeleteItem_Click'));

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Text = t('<p style="line-height: 15px; margin-bottom: 2px;">File cannot be updated without name!</p>');
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->HeaderClasses = 'btn-darkblue';
            $this->dlgModal4->addCloseButton(t("I close the window"));

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Cannot create a file with the same name!</p>');
            $this->dlgModal5->Title = t("Warning");
            $this->dlgModal5->HeaderClasses = 'btn-danger';
            $this->dlgModal5->addCloseButton(t("I understand"));

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this album?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
            $this->dlgModal6->Title = 'Warning';
            $this->dlgModal6->HeaderClasses = 'btn-danger';
            $this->dlgModal6->addButton("I accept", 'This file has been permanently deleted', false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal6->addCloseButton(t("I'll cancel"));
            $this->dlgModal6->addAction(new DialogButton(), new Ajax('deleteAlbum_Click'));

            $this->dlgModal7 = new Bs\Modal($this);
            $this->dlgModal7->Title = t('Tip');
            $this->dlgModal7->Text = t('<p style="margin-top: 15px;">The album status cannot be changed to public without images!</p>
                                <p style="margin-top: 25px; margin-bottom: 15px;">At the moment, you can modify the album title or delete the album.');
            $this->dlgModal7->HeaderClasses = 'btn-darkblue';
            $this->dlgModal7->addCloseButton(t("I understand"));

            $this->dlgModal8 = new Bs\Modal($this);
            $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move this album from this gallery group to another gallery group?</p>
                                ');
            $this->dlgModal8->Title = t('Warning');
            $this->dlgModal8->HeaderClasses = 'btn-danger';
            $this->dlgModal8->addButton(t("I accept"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal8->addCloseButton(t("I'll cancel"));
            $this->dlgModal8->addAction(new DialogButton(), new Ajax('moveItem_Click'));

            $this->dlgModal9 = new Bs\Modal($this);
            $this->dlgModal9->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Please select a gallery group!</p>');
            $this->dlgModal9->Title = t("Tip");
            $this->dlgModal9->HeaderClasses = 'btn-darkblue';
            $this->dlgModal9->addCloseButton(t("I understand"));

            $this->dlgModal10 = new Bs\Modal($this);
            $this->dlgModal10->Title = t('Tip');
            $this->dlgModal10->Text = t('<p style="margin-top: 15px;">If the brief  description is already filled in and 
                                    the author\'s name/source is not provided, the image description will not be displayed 
                                    under the image in the gallery!</p>
                                    <p style="margin-top: 25px; margin-bottom: 15px;">Please write only the author\'s 
                                    name/source text or fill in both fields!</p>');
            $this->dlgModal10->HeaderClasses = 'btn-darkblue';
            $this->dlgModal10->addCloseButton(t("I close the window"));

            $this->dlgModal11 = new Bs\Modal($this);
            $this->dlgModal11->Title = t("Success");
            $this->dlgModal11->HeaderClasses = 'btn-success';
            $this->dlgModal11->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This album is now hidden!</p>');
            $this->dlgModal11->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal12 = new Bs\Modal($this);
            $this->dlgModal12->Title = t("Success");
            $this->dlgModal12->HeaderClasses = 'btn-success';
            $this->dlgModal12->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This album has now been made public!</p>');
            $this->dlgModal12->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal13 = new Bs\Modal($this);
            $this->dlgModal13->Title = t("Success");
            $this->dlgModal13->HeaderClasses = 'btn-success';
            $this->dlgModal13->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This album is now a draft!</p>');
            $this->dlgModal13->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        }

        /**
         * Checks the availability of the gallery and updates the UI visibility
         * for the gallery table body accordingly.
         *
         * @return void
         * @throws Caller
         */
        public function checkGalleryAvailability(): void
        {
            if ($this->blnCheckGallery) {
                Application::executeJavaScript("
                    $('.table-body-alert').addClass('hidden');
                    $('.table-body').removeClass('hidden');
                    $('.album-tools-wrapper').css('border-top', '');
                ");
            } else {
                Application::executeJavaScript("
                    $('.table-body-alert').removeClass('hidden');
                    $('.table-body').addClass('hidden');
                    $('.album-tools-wrapper').css('border-top', '1px solid #dedede');                     
                ");
            }
        }

        /**
         * Updates the visibility of display elements based on the state of the gallery list.
         * The method checks the post-date, post-update date, author, and editor counts
         * to show or hide various UI components accordingly.
         *
         * @return void
         */
        protected function refreshDisplay(): void
        {
            if ($this->objGalleryList->getPostDate() &&
                !$this->objGalleryList->getPostUpdateDate() &&
                $this->objGalleryList->getAuthor() &&
                !$this->objGalleryList->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblInserter->Display = true;
                $this->txtInserter->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($this->objGalleryList->getPostDate() &&
                $this->objGalleryList->getPostUpdateDate() &&
                $this->objGalleryList->getAuthor() &&
                !$this->objGalleryList->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblInserter->Display = true;
                $this->txtInserter->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($this->objGalleryList->getPostDate() &&
                $this->objGalleryList->getPostUpdateDate() &&
                $this->objGalleryList->getAuthor() &&
                $this->objGalleryList->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblInserter->Display = true;
                $this->txtInserter->Display = true;
                $this->lblUsersAsEditors->Display = true;
                $this->txtUsersAsEditors->Display = true;
            }
        }


        /**
         * Redirects the user to the album upload page with specified query parameters.
         *
         * @param ActionParams $params Event parameters passed to the method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function btnGoUpload_Click(ActionParams $params): void
        {
            Application::redirect('album_upload.php' . '?id=' . $this->intId . '&group=' . $this->intGroup . '&folder=' . $this->intFolder);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves a list of items for the gallery group title selector.
         *
         * This method queries the data source based on the specified conditions and clauses
         * and generates a list of `ListItem` objects representing the gallery group titles.
         * The list items will be marked as selected or disabled based on the current state of the gallery list.
         *
         * @return ListItem[] An array of `ListItem` objects representing the gallery group titles.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstGalleryGroupTitle_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objGalleryGroupTitleCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objGalleryGroupTitleCursor = GallerySettings::queryCursor($objCondition, $this->objGalleryGroupTitleClauses);

            // Iterate through the Cursor
            while ($objGalleryGroupTitle = GallerySettings::instantiateCursor($objGalleryGroupTitleCursor)) {
                $objListItem = new ListItem($objGalleryGroupTitle->__toString(), $objGalleryGroupTitle->Id);
                if (($this->objGalleryList->GalleryGroupTitle) && ($this->objGalleryList->GalleryGroupTitle->Id == $objGalleryGroupTitle->Id))
                    $objListItem->Selected = true;

                if ($this->objGalleryList->GalleryGroupTitle->Id == $objGalleryGroupTitle->Id) {
                    $objListItem->Disabled = true;
                }

                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Handles the change event for the group title list.
         * Displays a dialog box based on the selected value of the list.
         *
         * @param ActionParams $params Parameters related to the action triggered.
         * @return void
         */
        protected function lstGroupTitle_Change(ActionParams $params): void
        {
            if ($this->lstGroupTitle->SelectedValue == null) {
                $this->dlgModal9->showDialogBox();
            } else if ($this->lstGroupTitle->SelectedValue !== $this->objGalleryList->getGalleryGroupTitleId()) {
                $this->dlgModal8->showDialogBox();
            }
        }

        /**
         * Handles the click event for moving an item (album or folder) to a target folder, updating associated data,
         * and maintaining the consistency of lock statuses, slugs, and database references.
         *
         * This method performs the following:
         * - Hides the modal dialog box.
         * - Sanitizes and validates folder paths.
         * - Verifies unique folder names.
         * - Updates folder and album information including paths, associations, and lock statuses.
         * - Moves the physical folder and files to the new location.
         * - Updates database entries for related objects such as frontend links and settings.
         * - Refreshes UI components and redirects to the appropriate page.
         *
         * @param ActionParams $params The parameters of the triggered action, typically containing user interaction data.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function moveItem_Click(ActionParams $params): void
        {
            $this->dlgModal8->hideDialogBox();

            $fullPath = $this->strRootPath . '/' . QString::sanitizeForUrl($this->lstGroupTitle->SelectedName);
            $verifiedFolder = $this->generateUniqueFolderName(QString::sanitizeForUrl($this->txtTitle->Text), $fullPath);

            $src = $this->strRootPath . $this->objGalleryList->getPath();
            $dst = $fullPath . '/' . $verifiedFolder;

            $beforeCount = count(Folder::listFilesInFolder(dirname($dst), false));

            $objCurrentFolderId = $this->getIdFromParent($src); // Current folder id
            $objNextFolderId = $this->getIdFromParent($dst); // Next folder id

            // Here must first check the lock status of the following folder to do this check...
            if (GalleryList::countByParentFolderId($objNextFolderId) == 0) {
                $objFolder = Folders::loadById($objNextFolderId);
                $objFolder->setLockedFile(1);
                $objFolder->save();
            }

            // Next, we check the lock status of the previous folder, to do this, check...
            if ($objCurrentFolderId) {
                $objFolder = Folders::loadById($objCurrentFolderId);

                if (GalleryList::countByParentFolderId($objCurrentFolderId) == 1) {
                    $objFolder->setLockedFile(0);
                } else {
                    $objFolder->setLockedFile(1);
                }
                $objFolder->save();
            }

            // Now the "parent_id" and "path" of the folder you are moving must be changed accordingly
            $objFolder = Folders::loadById($this->objGalleryList->getFolderId());
            $objFolder->setParentId($objNextFolderId);
            $objFolder->setPath($this->getRelativePath($dst));
            $objFolder->save();

            // next we check the association of the images in the "files" and "album" tables with the previous folder
            // and change some data to match the next folder
            $objFileArray = Files::loadArrayByFolderId($this->objGalleryList->getFolderId());

            foreach ($objFileArray as $objFile) {
                if ($objFile->getFolderId() == $this->objGalleryList->getFolderId()) {
                    $objFile->setPath($this->getRelativePath($dst) . '/' . $objFile->getName());
                    $objFile->save();
                }
            }

            $objAlbumArray = Album::loadArrayByFolderId($this->objGalleryList->getFolderId());

            foreach ($objAlbumArray as $objAlbum) {
                if ($objAlbum->getFolderId() == $this->objGalleryList->getFolderId()) {
                    $objAlbum->setGalleryListId($this->objGalleryList->getId());
                    $objAlbum->setGalleryGroupTitleId($this->lstGroupTitle->SelectedValue);
                    $objAlbum->setGroupTitle($this->lstGroupTitle->SelectedName);
                    $objAlbum->setPath($this->getRelativePath($dst) . '/' . $objAlbum->getName());
                    $objAlbum->save();
                }
            }

            // We keep the path and slug separate, we only update the slug
            $objSettings = GallerySettings::loadById($this->lstGroupTitle->SelectedValue);
            $updatedSlug = $objSettings->getTitleSlug() . '/' . $verifiedFolder;

            // Let's change some values of this link in FrontendLinks
            $this->objFrontendLinks->setGroupedId($objSettings->getGalleryGroupId());
            $this->objFrontendLinks->setFrontendTitleSlug($updatedSlug);
            $this->objFrontendLinks->save();

            // Let's lock some data
            $this->objGalleryList->setMenuContentGroupId($objSettings->getGalleryGroupId());
            $this->objGalleryList->setGalleryGroupTitleId($this->lstGroupTitle->SelectedValue);
            $this->objGalleryList->setGroupTitle($this->lstGroupTitle->SelectedName);
            $this->objGalleryList->setParentFolderId($this->getIdFromParent($dst));
            $this->objGalleryList->setPath($this->getRelativePath($dst));
            $this->objGalleryList->setTitleSlug($updatedSlug);
            $this->objGalleryList->setPostUpdateDate(Q\QDateTime::Now());
            $this->objGalleryList->save();

            // We match the latest data and tell GallerySettings whether there are albums under each main folder,
            // if so, whether to lock that main folder "album_locked" or not
            $objGallerySettingArray = GallerySettings::loadAll();

            foreach ($objGallerySettingArray as $objGallerySetting) {
                if (GalleryList::countByGalleryGroupTitleId($objGallerySetting->getId())) {
                    $objGallerySetting->setAlbumsLocked(1);
                } else {
                    $objGallerySetting->setAlbumsLocked(0);
                }
                $objGallerySetting->save();
            }

            // Now we will move the album or album with images to another folder
            $this->fullMove($src, $dst);

            // We are updating the slug
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                '/' . $this->getRelativePath($dst);
            $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->objGalleryList->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objGalleryList->setPostUpdateDate(Q\QDateTime::now());
            $this->objGalleryList->save();

            $this->calPostUpdateDate->Text = $this->objGalleryList->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->txtUsersAsEditors->Text = implode(', ', $this->objGalleryList->getUserAsEditorsArray());

            $afterCount = count(Folder::listFilesInFolder(dirname($dst), false));

            $this->refreshDisplay();
            $this->dtgAlbumList->refresh();

            Application::redirect('album_edit.php?id=' . $this->objGalleryList->getId() . '&group=' . $this->lstGroupTitle->SelectedValue . '&folder=' . $this->objGalleryList->getFolderId());

            clearstatcache();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the change event for the status in the gallery list.
         * If the status is 1, it triggers a dialog box; otherwise, it locks the input fields.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected  function lstStatus_Change(): void
        {
            if (Album::countByFolderId($this->intFolder) == 0) {
                $this->dlgModal7->showDialogBox();
                $this->lstStatus->SelectedValue = $this->objGalleryList->getStatus();
                return;
            }

            $this->objGalleryList->setStatus($this->lstStatus->SelectedValue);
            $this->objGalleryList->setPostUpdateDate(Q\QDateTime::now());
            $this->objGalleryList->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objGalleryList->save();

            if ($this->objGalleryList->getStatus() === 1) {
                $this->dlgModal12->showDialogBox();
            } else if ($this->objGalleryList->getStatus() === 2) {
                $this->dlgModal11->showDialogBox();
            } else if ($this->objGalleryList->getStatus() === 3) {
                $this->dlgModal13->showDialogBox();
            }

            $this->calPostUpdateDate->Text = $this->objGalleryList->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->txtUsersAsEditors->Text = implode(', ', $this->objGalleryList->getUserAsEditorsArray());

            $this->refreshDisplay();
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the album save button.
         * Validates input data and triggers appropriate actions or displays dialog boxes
         * based on specific conditions.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function btnAlbumSave_Click(ActionParams $params): void
        {
            $this->oldTitle = $this->objGalleryList->getTitle();
            $this->oldTitleSlug = $this->objGalleryList->getPath();

            if (!$this->txtTitle->Text) {
                $this->dlgModal1->showDialogBox();

            } else if ((!$this->txtPhotoAuthor->Text && $this->txtPhotoDescription->Text) || ((!$this->txtPhotoDescription->Text) && $this->txtPhotoAuthor->Text)) {
                $this->dlgModal2->showDialogBox();

            } else if (Album::countByFolderId($this->intFolder) == 0) {
                $this->dlgModal7->showDialogBox();

                $this->txtTitle->Text = $this->objGalleryList->getTitle();
                $this->lstStatus->SelectedValue = 2;
            } else {
                $this->updateAlbum();
            }
        }

        /**
         * Updates the album based on the provided gallery settings, folder configuration, and user input.
         *
         * This method performs several operations, including
         * - Updating file paths and album paths to reflect the new folder or title.
         * - Modifying gallery list properties such as title, title slug, paths, and update timestamps.
         * - Updating folder configurations and frontend links metadata.
         * - Synchronizing gallery folder structure and handling status or description changes.
         * - Rendering a viewable URL for the album if applicable.
         *
         * Notifications are triggered to inform the user of the update's success or any pending changes.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function updateAlbum(): void
        {
            $objFileArray = Files::loadArrayByFolderId($this->intFolder);
            $objAlbumArray = Album::loadArrayByFolderId($this->intFolder);

            $objTemplateLocking = FrontendTemplateLocking::load(6);
            $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

            $fullPath = $this->strRootPath . '/' . QString::sanitizeForUrl($this->lstGroupTitle->SelectedName);
            $relativePath = $this->getRelativePath($fullPath);

            $verifiedFolder = $this->generateUniqueFolderName(QString::sanitizeForUrl($this->txtTitle->Text), $fullPath);
            $verifiedTitleSlug = $relativePath . '/' . $verifiedFolder;

            if (trim($this->txtTitle->Text) !== $this->objGalleryList->getTitle()) {

                foreach ($objFileArray as $objFile) {
                    $objFile = Files::loadById($objFile->getId());
                    $objFile->setPath($verifiedTitleSlug . '/' . $objFile->getName());
                    $objFile->save();
                }

                foreach ($objAlbumArray as $objAlbum) {
                    $objAlbum = Album::loadById($objAlbum->getId());
                    $objAlbum->setPath($verifiedTitleSlug . '/' . $objAlbum->getName());
                    $objAlbum->save();
                }

                $this->objGalleryList->setTitle($this->txtTitle->Text);
                $this->objGalleryList->setPath($verifiedTitleSlug);
                $this->objGalleryList->setTitleSlug($this->objSettings->getTitleSlug() . '/' . $verifiedFolder);
                $this->objGalleryList->setPostUpdateDate(Q\QDateTime::Now());
                $this->objGalleryList->save();

                $this->objFolder->setName($this->txtTitle->Text);
                $this->objFolder->setPath($verifiedTitleSlug);
                $this->objFolder->save();

                $this->objFrontendLinks->setLinkedId($this->intId);
                $this->objFrontendLinks->setGroupedId($this->intGroup);
                $this->objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $this->objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
                $this->objFrontendLinks->setContentTypesManagamentId(6);
                $this->objFrontendLinks->setFrontendTitleSlug($this->objSettings->getTitleSlug() . '/' . $verifiedFolder);
                $this->objFrontendLinks->save();

                $this->updateGalleryFolder($this->lstGroupTitle->SelectedName, $this->oldTitleSlug, $this->txtTitle->Text);

            } else if ($this->txtPhotoDescription->Text !== $this->objGalleryList->getListDescription() ||
                $this->txtPhotoAuthor->Text !== $this->objGalleryList->getListAuthor()) {

                $this->objGalleryList->setListDescription($this->txtPhotoDescription->Text);
                $this->objGalleryList->setListAuthor($this->txtPhotoAuthor->Text);
                $this->objGalleryList->save();

                $this->dlgToastr1->notify();
            } else if ($this->lstStatus->SelectedValue !== $this->objGalleryList->getStatus()) {
                $this->objGalleryList->setStatus($this->lstStatus->SelectedValue);
                $this->objGalleryList->save();

                $this->dlgToastr1->notify();
            } else {
                $this->dlgToastr2->notify();
            }

            if ($this->txtTitle->Text) {
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objGalleryList->getTitleSlug();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["class" => "view-link", "target" => "_blank"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            $this->renderActionsWithId();
        }

        /**
         * Handles the click event for the item escape action. Updates text fields with the title,
         * description, and author of the selected gallery item and triggers a notification if applicable.
         *
         * @param ActionParams $params Parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
            $objCancel = $this->objGalleryList->getId();

            // Check if $objCancel is available
            if ($objCancel) {
                $this->dlgToastr10->notify();
            }

            $this->txtTitle->Text = $this->objGalleryList->getTitle();
            $this->txtPhotoDescription->Text = $this->objGalleryList->getListDescription();
            $this->txtPhotoAuthor->Text = $this->objGalleryList->getListAuthor();

            $objAlbum = Album::loadById($this->intChangeFilesId);

            $this->txtFileName->Text = $this->getFileName($objAlbum->getName());
            $this->txtFileAuthor->Text = $objAlbum->getAuthor();
            $this->txtFileDescription->Text = $objAlbum->getDescription();

        }

        /**
         * Handles the click event for the album delete button and displays a modal dialog box.
         *
         * @param ActionParams $params The parameters associated with the button click action.
         * @return void
         */
        public function btnAlbumDelete_Click(ActionParams $params): void
        {
            $this->dlgModal6->showDialogBox();
        }

        /**
         * Handles the deletion of an album, its associated files, and related folders.
         * This method removes all related files from the filesystem, deletes temporary files,
         * and removes database entities for the folder, files, and albums associated with the album.
         * Additionally, it updates the frontend links and redirects to the gallery list page.
         *
         * @param ActionParams $params The parameters for the action triggering the album deletion.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function deleteAlbum_Click(ActionParams $params): void
        {
            $this->dlgModal6->hideDialogBox();

            $objFolder = Folders::loadById($this->intFolder);
            $objFileArray = Files::loadArrayByFolderId($this->intFolder);
            $objAlbumArray = Album::loadArrayByFolderId($this->intFolder);

            foreach ($objFileArray as $objFile) {
                $objFile = Files::loadById($objFile->getId());

                if (is_file($this->strRootPath . $objFile->getPath())) {
                    unlink($this->strRootPath . $objFile->getPath());
                }

                foreach ($this->tempFolders as $tempFolder) {
                    if (is_file($this->strTempPath . '/_files/' . $tempFolder . $objFile->getPath())) {
                        unlink($this->strTempPath . '/_files/' . $tempFolder . $objFile->getPath());
                    }
                }

                $objFile->delete();
            }

            if (Album::countByFolderId($this->intFolder) !== 0) {
                foreach ($objAlbumArray as $objAlbum) {
                    $objAlbum = Album::loadById($objAlbum->getId());
                    $objAlbum->delete();
                }
            }

            $objSetting = GallerySettings::loadById($this->intGroup);
            if (Album::countByGalleryGroupTitleId($this->intGroup) === 0) {
                $objSetting->setAlbumsLocked(0);
                $objSetting->save();
            }

            if (is_dir($this->strRootPath . $objFolder->getPath())) {
                rmdir($this->strRootPath . $objFolder->getPath());

                foreach ($this->tempFolders as $tempFolder) {
                    if (is_dir($this->strTempPath . '/_files/' . $tempFolder . $objFolder->getPath())) {
                        rmdir($this->strTempPath . '/_files/' . $tempFolder . $objFolder->getPath());
                    }
                }
            }

            $objFolder->delete();

            $this->objFrontendLinks->delete();
            $this->objGalleryList->delete();

            Application::redirect('gallery_list.php');
        }

        /**
         * Handles the click event for the "Go to Settings" button.
         * Sets session variables related to the gallery state and redirects the user to the settings manager page.
         *
         * @param ActionParams $params The parameters associated with the button click action.
         *
         * @return void
         * @throws Throwable
         */
        public function btnGoToSettings_Click(ActionParams $params): void
        {
            $_SESSION['gallery'] = $this->intId;
            $_SESSION['gallery_group'] = $this->intGroup;
            $_SESSION['gallery_folder'] = $this->intFolder;
            Application::redirect('settings_manager.php#galleriesSettings_tab');
        }

        /**
         * Handles the cancel action for the album form by redirecting to the album list page.
         *
         * @param ActionParams $params The parameters associated with the cancel action event.
         *
         * @return void
         * @throws Throwable
         */
        public function btnAlbumCancel_Click(ActionParams $params): void
        {
            $this->redirectToListPage();
        }

        /**
         * Redirects the application to the gallery list page.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            Application::redirect('gallery_list.php');
        }

        ///////////////////////////////////////////

        /**
         * Renders buttons for altering or deleting a specific album. If the album matches the selected album for
         * changes, renders the save and cancel buttons instead. Otherwise, dynamically generates and renders change
         * and delete buttons.
         *
         * @param Album $objAlbum The album object to render buttons for.
         *
         * @return string The HTML rendered for the applicable buttons.
         * @throws Caller
         */
        public function Change_render(Album $objAlbum): string
        {
            if ($objAlbum->Id == $this->intChangeFilesId) {
                return $this->btnPhotoSave->render(false) . ' ' . $this->btnPhotoCancel->render(false);
            } else {
                $btnChangeId = 'btnChange' . $objAlbum->Id;
                $btnChange = $this->getControl($btnChangeId);
                if (!$btnChange) {
                    $btnChange = new Bs\Button($this->dtgAlbumList, $btnChangeId);
                    $btnChange->Text = t('Change');
                    $btnChange->ActionParameter = $objAlbum->Id;
                    $btnChange->CssClass = 'btn btn-orange';
                    $btnChange->CausesValidation = false;
                    $btnChange->addAction(new Click(), new Ajax('btnChange_Click'));
                }
                $btnDeleteId = 'btnDelete' . $objAlbum->Id;
                $btnPhotoDelete = $this->getControl($btnDeleteId);

                if (!$btnPhotoDelete) {
                    $btnPhotoDelete = new Bs\Button($this->dtgAlbumList, $btnDeleteId);
                    $btnPhotoDelete->Text = 'Delete';
                    $btnPhotoDelete->ActionParameter = $objAlbum->Id;
                    $btnPhotoDelete->setCssStyle('margin-top', '7px');
                    $btnPhotoDelete->CausesValidation = false;
                    $btnPhotoDelete->addAction(new Click(), new Ajax('btnPhotoDelete_Click'));
                }

                return $btnChange->render(false) . ' ' . $btnPhotoDelete->render(false);
            }
        }

        /**
         * Handles the click event for the change button, initializing file properties and refreshing the album list.
         *
         * @param ActionParams $params The parameters passed during the action, containing the file ID to change.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnChange_Click(ActionParams $params): void
        {
            $this->intChangeFilesId = intval($params->ActionParameter);
            $objFile = Album::load($this->intChangeFilesId);

            $this->txtFileName->Text = pathinfo(APP_UPLOADS_DIR . $objFile->getPath(), PATHINFO_FILENAME);
            $this->lstIsEnabled->SelectedValue = $objFile->getStatus();
            $this->txtFileDescription->Text = $objFile->getDescription() ?? '';
            $this->txtFileAuthor->Text = $objFile->getAuthor() ?? '';
            Application::executeControlCommand($this->txtFileName->ControlId, 'focus');

            $this->dtgAlbumList->refresh();
        }

        /**
         * Handles the event triggered when the "Delete Photo" button is clicked.
         * It retrieves the photo ID to be deleted from the action parameters
         * and displays a confirmation dialog box.
         *
         * @param ActionParams $params The action parameters containing the ID of the photo to be deleted.
         * @return void
         */
        protected function btnPhotoDelete_Click(ActionParams $params): void
        {
            $this->intDeleteId = intval($params->ActionParameter);
            $this->dlgModal3->showDialogBox();

            $this->dtgAlbumList->refresh();
        }

        /**
         * Handles the deletion of a photo item from the album and related file references.
         *
         * @param ActionParams $params Parameters for the action, typically including request and control data.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function photoDeleteItem_Click(ActionParams $params): void
        {
            $this->dlgModal3->hideDialogBox();

            $objAlbum = Album::load($this->intDeleteId);

            if (is_file($this->strRootPath . $objAlbum->getPath())) {
                unlink($this->strRootPath . $objAlbum->getPath());

                foreach ($this->tempFolders as $tempFolder) {
                    if (is_file($this->strTempPath . '/_files/' . $tempFolder . $objAlbum->getPath())) {
                        unlink($this->strTempPath . '/_files/' . $tempFolder . $objAlbum->getPath());
                    }
                }

                $objFile = Files::loadById($objAlbum->getFileId());
                $objFile->delete();

                $objAlbum->delete();
            }

            if (is_file($this->strRootPath . $objAlbum->getPath())) {
                $this->dlgToastr7->notify();
            } else {
                $this->dlgToastr6->notify();
            }

            if (Album::countByFolderId($this->intFolder) === 0) {

                Application::executeJavaScript("
                    $('.table-body-alert').removeClass('hidden');
                    $('.table-body').addClass('hidden');
                    $('.album-tools-wrapper').css('border-top', '1px solid #dedede');   
                ");

                $objList = GalleryList::loadById($this->intId);
                $objList->setListAuthor('');
                $objList->setListDescription('');
                $objList->setStatus(2);
                $objList->save();

                $objSetting = GallerySettings::loadById($this->intGroup);
                if (Album::countByGalleryGroupTitleId($this->intGroup) === 0) {
                    $objSetting->setAlbumsLocked(0);
                    $objSetting->save();
                }

                $this->txtPhotoAuthor->Text = '';
                $this->txtPhotoDescription->Text = '';
                $this->lstStatus->SelectedValue = 2;
            }

            $this->renderActionsWithId();
            $this->dtgAlbumList->refresh();
        }

        /**
         * Handles the change event for the "Is Enabled" dropdown list, updating the album status
         * and triggering related updates for the gallery list and UI components.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected  function lstIsEnabled_Change(): void
        {
            $objAlbum = Album::load($this->intChangeFilesId);

            $objAlbum->setStatus($this->lstIsEnabled->SelectedValue);
            $objAlbum->setPostUpdateDate(Q\QDateTime::now());
            $objAlbum->save();

            $this->objGalleryList->setPostUpdateDate(Q\QDateTime::now());
            $this->objGalleryList->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objGalleryList->save();

            if ($objAlbum->getStatus() === 1) {
                $this->dlgToastr8->notify();
            } else if ($objAlbum->getStatus() === 2) {
                $this->dlgToastr9->notify();
            }

            $this->calPostUpdateDate->Text = $this->objGalleryList->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->txtUsersAsEditors->Text = implode(', ', $this->objGalleryList->getUserAsEditorsArray());

            $this->refreshDisplay();
        }

        /**
         * Handles the save operation for a photo, validating input fields and triggering updates or displaying dialogs as necessary.
         *
         * @param ActionParams $params Event parameters passed to the method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnPhotoSave_Click(ActionParams $params): void
        {
            $this->intAlbum = Album::load($this->intChangeFilesId);
            $parts = pathinfo($this->strRootPath . $this->intAlbum->getPath());
            $files = glob($parts['dirname'] . '/*', GLOB_NOSORT);

            if (!$this->txtFileName->Text) {
                $this->dlgModal4->showDialogBox();
                $this->txtFileName->Text = $this->getFileName($this->intAlbum->getName());
            } else if (!$this->txtFileAuthor->Text && $this->txtFileDescription->Text) {
                $this->dlgModal10->showDialogBox();
                $this->txtFileDescription->Text = '';
                $this->txtFileAuthor->focus();

            } else if ($this->txtFileName->Text == $this->getFileName($this->intAlbum->getName()) &&
                ($this->txtFileAuthor->Text !== $this->intAlbum->getAuthor() ||
                    $this->txtFileDescription->Text !== $this->intAlbum->getDescription() ||
                    $this->lstIsEnabled->SelectedValue !== $this->intAlbum->getStatus())) {
                $this->updateFile($this->intAlbum);

            } else if (in_array($parts['dirname'] . '/' . trim($this->txtFileName->Text) . '.' . strtolower($parts['extension']), $files)) {
                $this->dlgModal5->showDialogBox();
                $this->txtFileName->Text = $this->getFileName($this->intAlbum->getName());
            } else {
                $this->updateFile($this->intAlbum);
            }
        }

        /**
         * Updates the file associated with the given album by renaming it, updating its metadata,
         * and ensuring changes reflect across related storage locations.
         *
         * @param Album $intAlbum The album object containing information about the file to be updated.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function updateFile(Album $intAlbum): void
        {
            $parts = pathinfo($this->strRootPath . $this->intAlbum->getPath());
            $files = glob($parts['dirname'] . '/*', GLOB_NOSORT);
            $newPath = $parts['dirname'] . '/' . trim($this->txtFileName->Text) . '.' . strtolower($parts['extension']);

            if (is_file($this->strRootPath . $this->intAlbum->getPath())) {
                if (!in_array($parts['dirname'] . '/' . trim($this->txtFileName->Text) . '.' . strtolower($parts['extension']), $files)) {
                    $this->rename($this->strRootPath . $this->intAlbum->getPath(), $newPath);

                    foreach ($this->tempFolders as $tempFolder) {
                        if (is_file($this->strTempPath . '/_files/' . $tempFolder . $this->intAlbum->getPath())) {
                            $this->rename($this->strTempPath . '/_files/' . $tempFolder . $this->intAlbum->getPath(), $this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($newPath));
                        }
                    }
                }

                $objAlbum = Album::loadById($this->intAlbum->getId());
                $objAlbum->setName(basename($newPath));
                $objAlbum->setPath($this->getRelativePath($newPath));
                $objAlbum->setDescription($this->txtFileDescription->Text);
                $objAlbum->setAuthor($this->txtFileAuthor->Text);
                $objAlbum->setStatus($this->lstIsEnabled->SelectedValue);
                $objAlbum->setPostUpdateDate(Q\QDateTime::Now());
                $objAlbum->save();

                $objFile = Files::loadById($this->intAlbum->getFileId());
                $objFile->setName(basename($newPath));
                $objFile->setPath($this->getRelativePath($newPath));
                $objFile->setMtime(time());
                $objFile->save();
            }

            $this->renderActionsWithId();

            if (is_file($newPath)) {
                $this->dlgToastr4->notify();
            } else {
                $this->dlgToastr5->notify();
            }

            $this->intChangeFilesId = null;
            $this->dtgAlbumList->refresh();
        }

        /**
         * Handles the cancellation of the photo editing process.
         * Resets the current file change identifier and refreshes the album list.
         *
         * @param ActionParams $params The parameters associated with the action triggering this method.
         * @return void
         */
        protected function btnPhotoCancel_Click(ActionParams $params): void
        {
            $this->intChangeFilesId = null;
            $this->dtgAlbumList->refresh();
        }

        /**
         * Validates and updates properties of the current gallery list and album based on input values.
         * If a mismatch is detected between the current values and those from the inputs, the gallery list
         * meta-information such as assigned editors and update date is updated accordingly.
         *
         * @return void No return value. Handles updates and UI changes internally.
         */
        public function renderActionsWithId(): void
        {
            if ($this->intId) {
                if ($this->txtTitle !== $this->objGalleryList->getTitle() ||
                    $this->txtPhotoDescription->Text !== $this->objGalleryList->getListDescription() ||
                    $this->txtPhotoAuthor->Text !== $this->objGalleryList->getListAuthor() ||
                    $this->lstStatus->SelectedValue !== $this->objGalleryList->getStatus() ||

                    $this->txtFileName->Text !== $this->objAlbum->getName() ||
                    $this->txtFileAuthor->Text !== $this->objAlbum->getAuthor() ||
                    $this->txtFileDescription->Text !== $this->objAlbum->getDescription() ||
                    $this->lstIsEnabled->SelectedValue !== $this->objAlbum->getStatus()
                ) {
                    $this->objGalleryList->setAssignedEditorsNameById($this->intLoggedUserId);
                    $this->objGalleryList->setPostUpdateDate(Q\QDateTime::now());
                    $this->objGalleryList->save();

                    $this->calPostUpdateDate->Text = $this->objGalleryList->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
                    $this->txtUsersAsEditors->Text = implode(', ', $this->objGalleryList->getUserAsEditorsArray());
                }
            }
        }

        /**
         * Updates the name and location of a gallery folder, ensuring the new folder name is unique and properly updates related temporary folders.
         *
         * @param string $destination The target folder or directory where the updated folder should reside.
         * @param string $currentTitleSlug The current title or slug of the folder to be updated.
         * @param string $newFolderName The desired new name for the folder.
         *
         * @return void
         */
        public function updateGalleryFolder(string $destination, string $currentTitleSlug, string $newFolderName): void
        {
            $basePath = $this->strRootPath . '/' . QString::sanitizeForUrl($destination);

            $currentFolderPath = $this->strRootPath . $currentTitleSlug;

            // Check if the new folder name is unique
            $uniqueNewFolderName = $this->generateUniqueFolderName(QString::sanitizeForUrl($newFolderName), $basePath);
            $uniqueNewFolderPath = $basePath . '/' . $uniqueNewFolderName;

            // Update folder name
            rename($currentFolderPath, $uniqueNewFolderPath);

            // Update temp folders name
            foreach ($this->tempFolders as $tempFolder) {
                $currentTempPath = QString::sanitizeForUrl($destination) . '/' . basename($currentFolderPath);
                $newTempPath = QString::sanitizeForUrl($destination) . '/' . basename($uniqueNewFolderPath);

                $currentTempFolderPath = $this->strTempPath . '/_files/' . $tempFolder . '/' . $currentTempPath;
                $newTempFolderPath = $this->strTempPath . '/_files/' . $tempFolder . '/' . $newTempPath;

                rename($currentTempFolderPath, $newTempFolderPath);
            }
        }

        /**
         * Moves all files and directories from the source to the destination by copying and then removing the source.
         *
         * @param string $src The source directory or file path to be moved.
         * @param string $dst The destination directory or file path where the content should be moved.
         *
         * @return void
         * @throws Caller
         */
        protected function fullMove(string $src, string $dst): void
        {
            $this->fullCopy($src, $dst);
            $this->fullRemove($src);
        }

        /**
         * Performs a full copy of a source file or directory to a destination, handling unique naming for conflicts
         * and additional processing for temporary files and directories.
         *
         * @param string $src The source path, which can be a file or a directory.
         * @param string $dst The destination path where the source should be copied.
         *
         * @return void
         */
        protected function fullCopy(string $src, string $dst): void
        {
            $dirname = $this->removeFileName($dst);
            $name = pathinfo($dst, PATHINFO_FILENAME);
            $ext = pathinfo($dst, PATHINFO_EXTENSION);

            if (is_dir($src)) {
                // Let's check if the folder already exists
                if (file_exists($dirname . '/' . basename($name))) {
                    $inc = 1;
                    while (file_exists($dirname . '/' . $name . '-' . $inc)) {
                        $inc++;
                    }
                    $dst = $dirname . '/' . $name . '-' . $inc; // We use a unique name
                }

                Folder::makeDirectory($dst, 0777);

                foreach ($this->tempFolders as $tempFolder) {
                    Folder::makeDirectory($this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($dst), 0777);
                }

                $files = array_diff(scandir($src), array('..', '.'));
                foreach ($files as $file) {
                    // Recursive copying
                    $this->fullCopy("$src/$file", "$dst/$file");
                }

            } else if (file_exists($src)) {
                // If the file already exists, we add a unique name
                if (file_exists($dirname . '/' . basename($name) . '.' . $ext)) {
                    $inc = 1;
                    while (file_exists($dirname . '/' . $name . '-' . $inc . '.' . $ext)) {
                        $inc++;
                    }
                    $dst = $dirname . '/' . $name . '-' . $inc . '.' . $ext;
                }

                copy($src, $dst);

                // Strategy for copying temp files
                if (in_array(strtolower($ext), $this->arrAllowed)) {
                    foreach ($this->tempFolders as $tempFolder) {
                        copy(
                            $this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($src),
                            $this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($dst)
                        );
                    }
                }
            }
        }

        /**
         * Recursively removes a directory and its contents, including files and subdirectories,
         * and cleans up associated temporary files and folders.
         *
         * @param string $dir The path of the directory or file to be removed.
         *
         * @return void
         * @throws Caller
         */
        protected function fullRemove(string $dir): void
        {
            $objFolders = Folders::loadAll();
            $objFiles = Files::loadAll();

            if (is_dir($dir)) {
                $files = array_diff(scandir($dir), array('..', '.'));

                foreach ($files as $file) {
                    $this->fullRemove($dir . "/" . $file);
                }

                if (file_exists($dir)) {
                    rmdir($dir);

                    foreach ($this->tempFolders as $tempFolder) {
                        $tempPath = $this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($dir);
                        if (is_dir($tempPath)) {
                            rmdir($tempPath);
                        }
                    }
                }
            } elseif (file_exists($dir)) {
                unlink($dir);

                foreach ($this->tempFolders as $tempFolder) {
                    $tempPath = $this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($dir);
                    if (is_file($tempPath)) {
                        unlink($tempPath);
                    }
                }
            }

            $dirname = dirname($dir);
            if (is_dir($dirname)) {
                $folders = glob($dirname . '/*', GLOB_ONLYDIR);
                $files = array_filter(glob($dirname . '/*'), 'is_file');
            }
        }

        /**
         * Generates a unique folder name by appending an incremental index if the base folder name already exists in the specified path.
         *
         * @param string $baseFolderName The desired base folder name.
         * @param string $path The directory path where the folder will be created.
         *
         * @return string A unique folder name that does not conflict with existing folders in the specified path.
         */
        protected function generateUniqueFolderName(string $baseFolderName, string $path): string
        {
            // Download only folder names from the given directory
            $existingFolders = array_map('basename', glob($path . '/*', GLOB_ONLYDIR));

            // If the original name does not exist, return it directly
            if (!in_array($baseFolderName, $existingFolders)) {
                return $baseFolderName;
            }

            $uniqueFolderName = $baseFolderName;
            $inc = 1;

            // Add an index until a unique name is found
            while (in_array($uniqueFolderName, $existingFolders)) {
                $uniqueFolderName = $baseFolderName . '-' . $inc;
                $inc++;
            }

            return $uniqueFolderName;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves the ID of a folder that matches the parent directory of the given path.
         *
         * @param string $path The absolute path used to find the parent folder's ID.
         *
         * @return int|null The ID of the matching folder if found, 1 if the path is empty, or null if no match is
         *     found.
         * @throws Caller
         */
        private function getIdFromParent(string $path): ?int
        {
            $objFolders = Folders::loadAll();
            $objPath = $this->getRelativePath(realpath(dirname($path)));

            foreach ($objFolders as $objFolder) {
                if ($objPath == $objFolder->getPath()) {
                    return $objFolder->getId();
                }
            }

            // Handle the case where no matching folder is found.
            return ($objPath == "") ? 1 : null;
        }

        /**
         * Removes the file name from a given file path, returning the directory path.
         *
         * @param string $path The full file path from which the file name should be removed.
         *
         * @return string The directory path without the file name.
         */
        public function removeFileName(string $path): string
        {
            return substr($path, 0, (int)strrpos($path, '/'));
        }

        /**
         * Returns the relative path by removing the root path prefix from the given path.
         *
         * @param string $path The absolute path to be converted to a relative path.
         *
         * @return string The calculated relative path.
         */
        protected function getRelativePath(string $path): string
        {
            return substr($path, strlen($this->strRootPath));
        }

        /**
         * Extracts the base name of a file by removing its extension.
         *
         * @param string $filename The full name of the file, including its extension.
         *
         * @return string The file name without the extension.
         */
        protected function getFileName(string $filename): string
        {
            return substr($filename, 0, strrpos($filename, "."));
        }

        /**
         * Renames a file or directory from the old name to the new name, ensuring the new name does not already exist and the old name exists.
         *
         * @param string $old The current name of the file or directory to be renamed.
         * @param string $new The desired new name for the file or directory.
         *
         * @return bool|null Returns true on success, false on failure, or null if the new name exists or the old name does not exist.
         */
        public function rename(string $old, string $new): ?bool
        {
            return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
        }

        /**
         * Retrieves the file extension from a given file path if the provided path points to a valid file.
         *
         * @param string $path The file path from which to extract the extension.
         *
         * @return string|null The extracted file extension in lowercase, or null if the path is not a valid file.
         */
        public static function getExtension(string $path): ?string
        {
            if (!is_dir($path) && is_file($path)) {
                return strtolower(substr(strrchr($path, '.'), 1));
            }

            return null;
        }

        /**
         * Determines the MIME type of the given file path using available PHP functions.
         *
         * @param string $path The file path for which the MIME type needs to be identified.
         *
         * @return string|false Returns the MIME type as a string if successfully determined, or false on failure.
         */
        public static function getMimeType(string $path): false|string
        {
            if (function_exists('mime_content_type')) {
                return mime_content_type($path);
            } else {
                return function_exists('finfo_file') ? finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path) : false;
            }
        }

        /**
         * Retrieves the dimensions of an image file based on its path.
         *
         * @param string $path The file path of the image to retrieve dimensions for.
         *
         * @return string The dimensions of the image in the format "width x height", or an empty value if dimensions are not retrievable.
         */
        public static function getDimensions(string $path): string
        {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $ImageSize = getimagesize($path);

            if (in_array($ext, self::getImageExtensions())) {
                $width = ($ImageSize[0] ?? '0');
                $height = ($ImageSize[1] ?? '0');
                return $width . ' x ' . $height;
            }

            return '';
        }

        /**
         * Retrieves the width of an image file.
         *
         * @param string $path The file path to the image.
         *
         * @return null|int|string The width of the image in pixels, or null if the file is not a valid image.
         */
        public static function getImageWidth(string $path): int|string|null
        {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $ImageSize = getimagesize($path);

            if (in_array($ext, self::getImageExtensions())) {
                return ($ImageSize[0] ?? '0');
            }

            return null;
        }

        /**
         * Retrieves the height of an image file from the specified path.
         *
         * @param string $path The file system path to the image.
         *
         * @return int|string The height of the image in pixels, or '0' if the height cannot be determined or the file is not a valid image.
         */
        public static function getImageHeight(string $path): int|string
        {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $ImageSize = getimagesize($path);

            if (in_array($ext, self::getImageExtensions())) {
                return ($ImageSize[1] ?? '0');
            }

            return 0;
        }

        /**
         * Retrieves the list of valid image file extensions.
         *
         * @return array An array of supported image file extensions.
         */
        public static function getImageExtensions(): array
        {
            return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
        }

        /**
         * Returns an SVG file icon based on the given file extension.
         *
         * @param string $ext The file extension used to determine the appropriate SVG icon to return. Common extensions include 'GIF', 'JPG', 'PDF', 'DOCX', etc.
         *
         * @return string An SVG representation of an icon corresponding to the provided file extension.
         */
        protected static function getFileIconExtension(string $ext): string
        {
            return match ($ext) {
                'gif', 'jpg', 'jpeg', 'jpc', 'png', 'bmp' => '<svg class="svg-file svg-image files-svg" viewBox="0 0 56 56"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"> <circle cx="18.931" cy="14.431" r="4.569" style="fill:#f3d55b"></circle> <polygon points="6.5,39 17.5,39 49.5,39 49.5,28 39.5,18.5 29,30 23.517,24.517" style="fill:#88c057"></polygon> </g> <path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path> <text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text> </svg>',
                'pdf' => '<svg class="svg-file svg-pdf files-svg" viewBox="0 0 56 56"> <path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M19.514,33.324L19.514,33.324c-0.348,0-0.682-0.113-0.967-0.326 c-1.041-0.781-1.181-1.65-1.115-2.242c0.182-1.628,2.195-3.332,5.985-5.068c1.504-3.296,2.935-7.357,3.788-10.75 c-0.998-2.172-1.968-4.99-1.261-6.643c0.248-0.579,0.557-1.023,1.134-1.215c0.228-0.076,0.804-0.172,1.016-0.172 c0.504,0,0.947,0.649,1.261,1.049c0.295,0.376,0.964,1.173-0.373,6.802c1.348,2.784,3.258,5.62,5.088,7.562 c1.311-0.237,2.439-0.358,3.358-0.358c1.566,0,2.515,0.365,2.902,1.117c0.32,0.622,0.189,1.349-0.39,2.16 c-0.557,0.779-1.325,1.191-2.22,1.191c-1.216,0-2.632-0.768-4.211-2.285c-2.837,0.593-6.15,1.651-8.828,2.822 c-0.836,1.774-1.637,3.203-2.383,4.251C21.273,32.654,20.389,33.324,19.514,33.324z M22.176,28.198 c-2.137,1.201-3.008,2.188-3.071,2.744c-0.01,0.092-0.037,0.334,0.431,0.692C19.685,31.587,20.555,31.19,22.176,28.198z M35.813,23.756c0.815,0.627,1.014,0.944,1.547,0.944c0.234,0,0.901-0.01,1.21-0.441c0.149-0.209,0.207-0.343,0.23-0.415 c-0.123-0.065-0.286-0.197-1.175-0.197C37.12,23.648,36.485,23.67,35.813,23.756z M28.343,17.174 c-0.715,2.474-1.659,5.145-2.674,7.564c2.09-0.811,4.362-1.519,6.496-2.02C30.815,21.15,29.466,19.192,28.343,17.174z M27.736,8.712c-0.098,0.033-1.33,1.757,0.096,3.216C28.781,9.813,27.779,8.698,27.736,8.712z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>',
                'docx', 'doc' => '<svg class="svg-file svg-word files-svg" viewBox="0 0 56 56"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M12.5,13h6c0.553,0,1-0.448,1-1s-0.447-1-1-1h-6c-0.553,0-1,0.448-1,1S11.947,13,12.5,13z"></path><path d="M12.5,18h9c0.553,0,1-0.448,1-1s-0.447-1-1-1h-9c-0.553,0-1,0.448-1,1S11.947,18,12.5,18z"></path><path d="M25.5,18c0.26,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 c-0.38-0.37-1.04-0.37-1.42,0c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71C24.979,17.89,25.24,18,25.5,18z"></path><path d="M29.5,18h8c0.553,0,1-0.448,1-1s-0.447-1-1-1h-8c-0.553,0-1,0.448-1,1S28.947,18,29.5,18z"></path><path d="M11.79,31.29c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71 C11.979,32.89,12.229,33,12.5,33c0.27,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 C12.84,30.92,12.16,30.92,11.79,31.29z"></path><path d="M24.5,31h-8c-0.553,0-1,0.448-1,1s0.447,1,1,1h8c0.553,0,1-0.448,1-1S25.053,31,24.5,31z"></path><path d="M41.5,18h2c0.553,0,1-0.448,1-1s-0.447-1-1-1h-2c-0.553,0-1,0.448-1,1S40.947,18,41.5,18z"></path><path d="M12.5,23h22c0.553,0,1-0.448,1-1s-0.447-1-1-1h-22c-0.553,0-1,0.448-1,1S11.947,23,12.5,23z"></path><path d="M43.5,21h-6c-0.553,0-1,0.448-1,1s0.447,1,1,1h6c0.553,0,1-0.448,1-1S44.053,21,43.5,21z"></path><path d="M12.5,28h4c0.553,0,1-0.448,1-1s-0.447-1-1-1h-4c-0.553,0-1,0.448-1,1S11.947,28,12.5,28z"></path><path d="M30.5,26h-10c-0.553,0-1,0.448-1,1s0.447,1,1,1h10c0.553,0,1-0.448,1-1S31.053,26,30.5,26z"></path><path d="M43.5,26h-9c-0.553,0-1,0.448-1,1s0.447,1,1,1h9c0.553,0,1-0.448,1-1S44.053,26,43.5,26z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>',
                'xlsx', 'xls' => '<svg viewBox="0 0 56 56" class="svg-file svg-excel files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path style="fill:#c8bdb8" d="M23.5,16v-4h-12v4v2v2v2v2v2v2v2v4h10h2h21v-4v-2v-2v-2v-2v-2v-4H23.5z M13.5,14h8v2h-8V14z M13.5,18h8v2h-8V18z M13.5,22h8v2h-8V22z M13.5,26h8v2h-8V26z M21.5,32h-8v-2h8V32z M42.5,32h-19v-2h19V32z M42.5,28h-19v-2h19V28 z M42.5,24h-19v-2h19V24z M23.5,20v-2h19v2H23.5z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>',
                'pptx', 'ppt' => '<svg viewBox="0 0 56 56" class="svg-file svg-powerpoint files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path style="fill:#c8bdb8" d="M39.5,30h-24V14h24V30z M17.5,28h20V16h-20V28z"></path><path style="fill:#c8bdb8" d="M20.499,35c-0.175,0-0.353-0.046-0.514-0.143c-0.474-0.284-0.627-0.898-0.343-1.372l3-5 c0.284-0.474,0.898-0.627,1.372-0.343c0.474,0.284,0.627,0.898,0.343,1.372l-3,5C21.17,34.827,20.839,35,20.499,35z"></path><path style="fill:#c8bdb8" d="M34.501,35c-0.34,0-0.671-0.173-0.858-0.485l-3-5c-0.284-0.474-0.131-1.088,0.343-1.372 c0.474-0.283,1.088-0.131,1.372,0.343l3,5c0.284,0.474,0.131,1.088-0.343,1.372C34.854,34.954,34.676,35,34.501,35z"></path><path style="fill:#c8bdb8" d="M27.5,16c-0.552,0-1-0.447-1-1v-3c0-0.553,0.448-1,1-1s1,0.447,1,1v3C28.5,15.553,28.052,16,27.5,16 z"></path><rect x="17.5" y="16" style="fill:#d3ccc9" width="20" height="12"></rect></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>',
                'mov', 'mpeg', 'mpg', 'mp4', 'm4v' => '<svg viewBox="0 0 56 56" class="svg-file svg-video files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path>\<polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M24.5,28c-0.166,0-0.331-0.041-0.481-0.123C23.699,27.701,23.5,27.365,23.5,27V13 c0-0.365,0.199-0.701,0.519-0.877c0.321-0.175,0.71-0.162,1.019,0.033l11,7C36.325,19.34,36.5,19.658,36.5,20 s-0.175,0.66-0.463,0.844l-11,7C24.874,27.947,24.687,28,24.5,28z M25.5,14.821v10.357L33.637,20L25.5,14.821z"></path><path d="M28.5,35c-8.271,0-15-6.729-15-15s6.729-15,15-15s15,6.729,15,15S36.771,35,28.5,35z M28.5,7 c-7.168,0-13,5.832-13,13s5.832,13,13,13s13-5.832,13-13S35.668,7,28.5,7z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>',
                'wav', 'mp3', 'mp2', 'm4a', 'aac' => '<svg viewBox="0 0 56 56" class="svg-file svg-audio files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M35.67,14.986c-0.567-0.796-1.3-1.543-2.308-2.351c-3.914-3.131-4.757-6.277-4.862-6.738V5 c0-0.553-0.447-1-1-1s-1,0.447-1,1v1v8.359v9.053h-3.706c-3.882,0-6.294,1.961-6.294,5.117c0,3.466,2.24,5.706,5.706,5.706 c3.471,0,6.294-2.823,6.294-6.294V16.468l0.298,0.243c0.34,0.336,0.861,0.72,1.521,1.205c2.318,1.709,6.2,4.567,5.224,7.793 C35.514,25.807,35.5,25.904,35.5,26c0,0.43,0.278,0.826,0.71,0.957C36.307,26.986,36.404,27,36.5,27c0.43,0,0.826-0.278,0.957-0.71 C39.084,20.915,37.035,16.9,35.67,14.986z M26.5,27.941c0,2.368-1.926,4.294-4.294,4.294c-2.355,0-3.706-1.351-3.706-3.706 c0-2.576,2.335-3.117,4.294-3.117H26.5V27.941z M31.505,16.308c-0.571-0.422-1.065-0.785-1.371-1.081l-1.634-1.34v-3.473 c0.827,1.174,1.987,2.483,3.612,3.783c0.858,0.688,1.472,1.308,1.929,1.95c0.716,1.003,1.431,2.339,1.788,3.978 C34.502,18.515,32.745,17.221,31.505,16.308z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>',
                'rtf', 'txt' => '<svg viewBox="0 0 56 56" class="svg-file svg-text files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M12.5,13h6c0.553,0,1-0.448,1-1s-0.447-1-1-1h-6c-0.553,0-1,0.448-1,1S11.947,13,12.5,13z"></path><path d="M12.5,18h9c0.553,0,1-0.448,1-1s-0.447-1-1-1h-9c-0.553,0-1,0.448-1,1S11.947,18,12.5,18z"></path><path d="M25.5,18c0.26,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 c-0.38-0.37-1.04-0.37-1.42,0c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71C24.979,17.89,25.24,18,25.5,18z"></path><path d="M29.5,18h8c0.553,0,1-0.448,1-1s-0.447-1-1-1h-8c-0.553,0-1,0.448-1,1S28.947,18,29.5,18z"></path><path d="M11.79,31.29c-0.181,0.19-0.29,0.44-0.29,0.71s0.109,0.52,0.29,0.71 C11.979,32.89,12.229,33,12.5,33c0.27,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71c0-0.26-0.11-0.52-0.29-0.71 C12.84,30.92,12.16,30.92,11.79,31.29z"></path><path d="M24.5,31h-8c-0.553,0-1,0.448-1,1s0.447,1,1,1h8c0.553,0,1-0.448,1-1S25.053,31,24.5,31z"></path><path d="M41.5,18h2c0.553,0,1-0.448,1-1s-0.447-1-1-1h-2c-0.553,0-1,0.448-1,1S40.947,18,41.5,18z"></path><path d="M12.5,23h22c0.553,0,1-0.448,1-1s-0.447-1-1-1h-22c-0.553,0-1,0.448-1,1S11.947,23,12.5,23z"></path><path d="M43.5,21h-6c-0.553,0-1,0.448-1,1s0.447,1,1,1h6c0.553,0,1-0.448,1-1S44.053,21,43.5,21z"></path><path d="M12.5,28h4c0.553,0,1-0.448,1-1s-0.447-1-1-1h-4c-0.553,0-1,0.448-1,1S11.947,28,12.5,28z"></path><path d="M30.5,26h-10c-0.553,0-1,0.448-1,1s0.447,1,1,1h10c0.553,0,1-0.448,1-1S31.053,26,30.5,26z"></path><path d="M43.5,26h-9c-0.553,0-1,0.448-1,1s0.447,1,1,1h9c0.553,0,1-0.448,1-1S44.053,26,43.5,26z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>',
                'zip', 'rar', 'asice', 'cdoc' => '<svg viewBox="0 0 56 56" class="svg-file svg-archive files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><g class="svg-file-icon"><path d="M28.5,24v-2h2v-2h-2v-2h2v-2h-2v-2h2v-2h-2v-2h2V8h-2V6h-2v2h-2v2h2v2h-2v2h2v2h-2v2h2v2h-2v2h2v2 h-4v5c0,2.757,2.243,5,5,5s5-2.243,5-5v-5H28.5z M30.5,29c0,1.654-1.346,3-3,3s-3-1.346-3-3v-3h6V29z"></path><path d="M26.5,30h2c0.552,0,1-0.447,1-1s-0.448-1-1-1h-2c-0.552,0-1,0.447-1,1S25.948,30,26.5,30z"></path></g><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext" x="28" y="51.5">' . $ext . '</text></svg>',
                default => '<svg viewBox="0 0 56 56" class="svg-file svg-none files-svg"><path class="svg-file-bg" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"></path><polygon class="svg-file-flip" points="37.5,0.151 37.5,12 49.349,12"></polygon><path class="svg-file-text-bg" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"></path><text class="svg-file-ext f_10" x="28" y="51.5">' . $ext . '</text></svg>',
            };
        }
    }
    AlbumEditForm::run('AlbumEditForm');