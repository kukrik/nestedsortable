<?php
    require('qcubed.inc.php');

    require('../src/Control/GalleryUploadHandler.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Bootstrap as Bs;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Event\Click;
    use QCubed\Action\Ajax;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;

    /**
     * Class AlbumUploadForm
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
    class AlbumUploadForm extends Form
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

        protected object $objUpload;
        protected Q\Plugin\BsFileControl $btnAddFiles;
        protected Bs\Button $btnAllStart;
        protected Bs\Button $btnAllCancel;
        protected Bs\Button $btnBack;
        protected Bs\Button $btnDone;

        ////////////////////////////////

        protected Q\Plugin\Control\Label $lblPostDate;
        protected Bs\Label $calPostDate;

        protected Q\Plugin\Control\Label $lblPostUpdateDate;
        protected Bs\Label $calPostUpdateDate;

        protected Q\Plugin\Control\Label $lblInserter;
        protected Bs\Label $txtInserter;

        protected Q\Plugin\Control\Label $lblUsersAsEditors;
        protected Bs\Label $txtUsersAsEditors;

        protected Q\Plugin\Control\Label $lblPhotoAuthor;
        protected Bs\TextBox $txtPhotoAuthor;

        protected Q\Plugin\Control\Label $lblPhotoDescription;
        protected Bs\TextBox $txtPhotoDescription;

        protected Q\Plugin\Control\Label $lblStatus;
        protected Q\Plugin\Control\RadioList $lstStatus;

        ////////////////////////////////

        protected int $intId;
        protected int $intGroup;
        protected int $intFolder;

        ////////////////////////////////

         protected object $objUser;
         protected int $intLoggedUserId;
         protected object $objGalleryList;
         protected object $objSettings;


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

            $this->objSettings = GallerySettings::load($this->intGroup);

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

            $this->createInputs();
            $this->createButtons();
            $this->createObjects();
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

            $this->lblPhotoDescription = new Q\Plugin\Control\Label($this);
            $this->lblPhotoDescription->Text = t('Brief description');
            $this->lblPhotoDescription->setCssStyle('font-weight', 'bold');

            $this->txtPhotoDescription = new Bs\TextBox($this);
            $this->txtPhotoDescription->Text = $this->objGalleryList->ListDescription;
            $this->txtPhotoDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->setCssStyle('font-weight', 'bold');

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden'), 3 => t('Draft')]);
            $this->lstStatus->SelectedValue = $this->objGalleryList->Status;
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
            $this->lstStatus->Enabled = true;
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
            $this->btnAddFiles = new Q\Plugin\BsFileControl($this, 'files');
            $this->btnAddFiles->Text = t(' Add files');
            $this->btnAddFiles->Glyph = 'fa fa-upload';
            $this->btnAddFiles->Multiple = true;
            $this->btnAddFiles->CssClass = 'btn btn-orange fileinput-button';
            $this->btnAddFiles->setCssStyle('float', 'left');
            $this->btnAddFiles->setCssStyle('margin-right', '10px');
            $this->btnAddFiles->UseWrapper = false;
            $this->btnAddFiles->addAction(new Click(), new Ajax('uploadStart_Click'));

            $this->btnAllStart = new Bs\Button($this);
            $this->btnAllStart->Text = t('Start upload');
            $this->btnAllStart->CssClass = 'btn btn-darkblue all-start disabled';
            $this->btnAllStart->setCssStyle('float', 'left');
            $this->btnAllStart->setCssStyle('margin-right', '10px');
            $this->btnAllStart->PrimaryButton = true;
            $this->btnAllStart->UseWrapper = false;

            $this->btnAllCancel = new Bs\Button($this);
            $this->btnAllCancel->Text = t('Cancel all uploads');
            $this->btnAllCancel->CssClass = 'btn btn-warning all-cancel disabled';
            $this->btnAllCancel->setCssStyle('float', 'left');
            $this->btnAllCancel->setCssStyle('margin-right', '10px');
            $this->btnAllCancel->UseWrapper = false;

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back to the album');
            $this->btnBack->CssClass = 'btn btn-default back';
            $this->btnBack->setCssStyle('float', 'left');
            $this->btnBack->UseWrapper = false;
            $this->btnBack->addAction(new Click(), new Ajax('btnBack_Click'));

            $this->btnDone = new Bs\Button($this);
            $this->btnDone->Text = t('Done');
            $this->btnDone->CssClass = 'btn btn-success pull-right done';
            $this->btnDone->UseWrapper = false;
            $this->btnDone->addAction(new Click(), new Ajax('btnDone_Click'));
        }

        /**
         * Initializes and configures the gallery upload handler object.
         *
         * @return void
         * @throws Caller
         */
        public function createObjects(): void
        {
            $this->objUpload = new Q\Plugin\Control\GalleryUploadHandler($this);
            $this->objUpload->Language = 'et'; // Default en
            //$this->objUpload->ShowIcons = true; // Default false
            $this->objUpload->AcceptFileTypes = ['jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif']; // Default null
            //$this->objUpload->MaxNumberOfFiles = 5; // Default null
            //$this->objUpload->MaxFileSize = 1024 * 1024 * 2; // 2 MB // Default null
            //$this->objUpload->MinFileSize = 500000; // 500 kb // Default null
            //$this->objUpload->ChunkUpload = false; // Default true
            //$this->objUpload->MaxChunkSize = 1024 * 1024 * 2; //* 10; // 10 MB // Default 5 MB
            //$this->objUpload->LimitConcurrentUploads = 10; // Default 2
            $this->objUpload->Url = 'php/gallery_upload.php'; // Default null
            //$this->objUpload->PreviewMaxWidth = 120; // Default 80
            //$this->objUpload->PreviewMaxHeight = 120; // Default 80
            //$this->objUpload->WithCredentials = true; // Default false
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
         * Handles the click event for the "Back" button, redirecting the user to the album edit page.
         *
         * This method redirects the user to the album edit page, passing relevant query parameters such as
         * album ID, group, and folder to facilitate navigation and data persistence.
         *
         * @param ActionParams $params An object containing parameters related to the action event triggering the
         *     method.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnBack_Click(ActionParams $params): void
        {
            Application::redirect('album_edit.php' . '?id=' . $this->intId . '&group=' . $this->intGroup . '&folder=' . $this->intFolder);
        }

        /**
         * Handles the 'Done' button click event. Updates the settings and gallery list, locks the albums if not
         * already locked, updates the displayed post-update date and list of users as editors, refreshes the display,
         * and resets session variables.
         *
         * @param ActionParams $params Parameters passed from the button click action.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnDone_Click(ActionParams $params): void
        {
            $this->objSettings->setPostUpdateDate(QCubed\QDateTime::now());

            if ($this->objSettings->getAlbumsLocked() == 0) {
                $this->objSettings->setAlbumsLocked(1);
            }

            $this->objSettings->save();

            $this->objGalleryList->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objGalleryList->setPostUpdateDate(Q\QDateTime::now());
            $this->objGalleryList->save();

            $this->calPostUpdateDate->Text = $this->objGalleryList->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->txtUsersAsEditors->Text = implode(', ', $this->objGalleryList->getUserAsEditorsArray());

            unset($_SESSION['id']);
            unset($_SESSION['groupId']);
            unset($_SESSION['folderId']);
            unset($_SESSION['path']);

            $this->refreshDisplay();
        }

        /**
         * Handles the start of the upload process by enabling UI controls and initializing session variables.
         *
         * @param ActionParams $params Contains action-specific parameters passed during the event.
         *
         * @return void
         * @throws Caller
         */
        protected function uploadStart_Click(ActionParams $params): void
        {
            $_SESSION['id'] = $this->intId;
            $_SESSION['groupId'] = $this->intGroup;
            $_SESSION['folderId'] = $this->intFolder;
            $_SESSION['path'] = $this->objGalleryList->getPath();
        }


    }
    AlbumUploadForm::run('AlbumUploadForm');