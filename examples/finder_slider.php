<?php
require_once('qcubed.inc.php');
require_once ('../src/Control/FileManager.php');
require_once ('../src/Control/FilePopupCroppie.php');
require_once ('../src/Control/FileUploadHandler.php');
require_once ('../src/Control/FileInfo.php');
require_once ('../src/Control/DestinationInfo.class.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Plugin\UploadHandler;
use QCubed\Plugin\FileManager;
use QCubed\Plugin\FileInfo;
use QCubed\QDateTime;
use QCubed\Folder;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Js;
use QCubed\Html;
use QCubed\QString;
use QCubed\Query\QQ;
use QCubed\Action\Ajax;
use QCubed\Jqui\Event\SelectableStop;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{

    protected $dlgModal1;
    protected $dlgModal2;
    protected $dlgModal3;
    protected $dlgModal4;
    protected $dlgModal5;
    protected $dlgModal6;
    protected $dlgModal7;
    protected $dlgModal8;
    protected $dlgModal9;
    protected $dlgModal10;
    protected $dlgModal11;
    protected $dlgModal12;
    protected $dlgModal13;
    protected $dlgModal14;
    protected $dlgModal15;
    protected $dlgModal16;
    protected $dlgModal17;
    protected $dlgModal18;
    protected $dlgModal19;
    protected $dlgModal20;
    protected $dlgModal21;
    protected $dlgModal22;
    protected $dlgModal23;
    protected $dlgModal24;
    protected $dlgModal25;
    protected $dlgModal26;
    protected $dlgModal27;
    protected $dlgModal28;
    protected $dlgModal29;
    protected $dlgModal30;
    protected $dlgModal31;
    protected $dlgModal32;
    protected $dlgModal33;
    protected $dlgModal34;
    protected $dlgModal35;

    protected $dlgModal40;
    protected $dlgModal41;
    protected $dlgModal42;
    protected $dlgModal43;
    protected $dlgModal44;
    protected $dlgModal45;
    protected $dlgModal46;

    protected $objUpload;
    protected $objManager;
    protected $dlgPopup;
    protected $objInfo;
    protected $lblSearch;
    protected $objHomeLink;

    protected $btnAddFiles;
    protected $btnAllStart;
    protected $btnAllCancel;
    protected $btnBack;
    protected $btnDone;

    protected $btnUploadStart;
    protected $btnAddFolder;
    protected $btnRefresh;
    protected $btnRename;
    protected $btnCrop;
    protected $btnCopy;
    protected $btnDelete;
    protected $btnMove;
    protected $btnImageListView;
    protected $btnListView;
    protected $btnBoxView;
    protected $txtFilter;

    protected $btnInsert;
    protected $btnCancel;

    protected $txtAddFolder;
    protected $lblError;
    protected $lblSameName;
    protected $lblRenameName;
    protected $lblDirectoryError;
    protected $txtRename;

    protected $lblDestinationError;
    protected $lblCourceTitle;
    protected $lblCourcePath;
    protected $lblCopyingTitle;
    protected $dlgCopyingDestination;

    protected $lblMovingError;
    protected $lblMoveInfo;
    protected $lblMovingDestinationError;
    protected $lblMovingCourceTitle;
    protected $lblMovingCourcePath;
    protected $lblMovingTitle;
    protected $dlgMovingDestination;

    protected $lblDeletionWarning;
    protected $lblDeletionInfo;
    protected $lblDeleteError;
    protected $lblDeleteInfo;
    protected $lblDeleteTitle;
    protected $lblDeletePath;

    protected $arrSomeArray = [];
    protected $tempItems = [];
    protected $tempSelectedItems = [];
    protected $objLockedFiles = 0;
    protected $objLockedDirs = [];

    protected $intDataId = "";
    protected $strDataName = "";
    protected $strDataPath = "";
    protected $strDataExtension = "";
    protected $strDataType = "";
    protected $intDataLocked = "";
    protected $strNewPath;
    protected $intStoredChecks = 0;
    protected $arrAllowed = array('jpg', 'jpeg', 'png', 'gif', 'svg');
    protected $tempFolders = ['thumbnail', 'medium', 'large'];
    protected $arrCroppieTypes = array('jpg', 'jpeg', 'png');

    protected $blnMove = false;

    protected $intLoggedUserId;

    protected function formCreate()
    {
        parent::formCreate();

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

        $this->objUpload = new Q\Plugin\FileUploadHandler($this);
        $this->objUpload->Language = "et"; // Default en
        //$this->objUpload->ShowIcons = true; // Default false
        //$this->objUpload->AcceptFileTypes = ['gif', 'jpg', 'jpeg', 'png', 'pdf', 'ppt', 'docx', 'mp4']; // Default null
        //$this->objUpload->MaxNumberOfFiles = 5; // Default null
        //$this->objUpload->MaxFileSize = 1024 * 1024 * 2; // 2 MB // Default null
        //$this->objUpload->MinFileSize = 500000; // 500 kb // Default null
        //$this->objUpload->ChunkUpload = false; // Default true
        $this->objUpload->MaxChunkSize = 1024 * 1024; // 10 MB // Default 5 MB
        //$this->objUpload->LimitConcurrentUploads = 5; // Default 2
        $this->objUpload->Url = 'php/'; // Default null
        //$this->objUpload->PreviewMaxWidth = 120; // Default 80
        //$this->objUpload->PreviewMaxHeight = 120; // Default 80
        $this->objUpload->UseWrapper = false;

        $this->objManager = new Q\Plugin\FileManager($this);
        $this->objManager->Language = 'et'; // Default en
        $this->objManager->RootPath = APP_UPLOADS_DIR;
        $this->objManager->RootUrl = APP_UPLOADS_URL;
        $this->objManager->TempPath = APP_UPLOADS_TEMP_DIR;
        $this->objManager->TempUrl = APP_UPLOADS_TEMP_URL;
        $this->objManager->DateTimeFormat = 'DD.MM.YYYY HH:mm:ss';
        $this->objManager->LockedDocuments = true;
        //$this->objManager->LockedImages = true;
        $this->objManager->UseWrapper = false;
        $this->objManager->addAction(new SelectableStop(), new Ajax ('selectable_stop'));


        $this->dlgPopup = new Q\Plugin\FilePopupCroppie($this);
        $this->dlgPopup->Url = "php/crop_upload.php";
        $this->dlgPopup->Language = "et";
        $this->dlgPopup->TranslatePlaceholder = t("- Select a destination -");
        $this->dlgPopup->Theme = "web-vauu";
        $this->dlgPopup->HeaderTitle = t("Crop image");
        $this->dlgPopup->SaveText = t("Crop and save");
        $this->dlgPopup->CancelText = t("Cancel");

        $this->dlgPopup->addAction(new Q\Plugin\Event\ChangeObject(), new \QCubed\Action\Ajax('objManagerRefresh_Click'));

        if ($this->dlgPopup->Language) {
            $this->dlgPopup->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/i18n/". $this->dlgPopup->Language . ".js");
        }

        $this->objInfo = new Q\Plugin\Control\FileInfo($this);
        $this->objInfo->RootUrl = APP_UPLOADS_URL;
        $this->objInfo->TempUrl = APP_UPLOADS_TEMP_URL;
        $this->objInfo->UseWrapper = false;

        $this->lblSearch = new Q\Plugin\Label($this);
        $this->lblSearch->addCssClass('search-results hidden');
        $this->lblSearch->setHtmlAttribute("data-lang", "search_results");
        $this->lblSearch->setCssStyle('font-weight', 600);
        $this->lblSearch->setCssStyle('font-size', '14px;');
        $this->lblSearch->Text = t('Search results:');

        $this->objHomeLink = new Q\Plugin\Label($this);
        $this->objHomeLink->addCssClass('homelink');
        $this->objHomeLink->setCssStyle('font-weight', 600);
        $this->objHomeLink->setCssStyle('font-size', '14px;');
        $this->objHomeLink->Text = Q\Html::renderLink("finder.php#/", "Repository", ["data-lang" => "repository"]);
        $this->objHomeLink->HtmlEntities = false;
        $this->objHomeLink->addAction(new Q\Event\Click(), new Q\Action\Ajax('appendData_Click'));

        $this->CreateButtons();
        $this->createModals();
        $this->portedAddFolderTextBox();
        $this->portedRenameTextBox();
        $this->portedCheckDestination();
        $this->portedCopyingListBox();
        $this->portedDeleteBox();
        $this->portedMovingListBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function selectable_stop(ActionParams $params)
    {
        $arr = $this->objManager->SelectedItems;
        $this->arrSomeArray = json_decode($arr, true);

        // Here comes a small check that when you select a file, the "Insert" button becomes active or not.
        Application::executeJavaScript("
            const insert = document.querySelector('.insert');
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

            if ('{$this->arrSomeArray[0]["data-item-type"]}' === 'dir') {
                insert.setAttribute('disabled', 'disabled');
            } else {
                insert.removeAttribute('disabled', 'disabled');
            }
            
            if ('{$this->objManager->LockedImages}') {
                if ('{$this->arrSomeArray[0]["data-item-type"]}' === 'file') {
                    if (allowedExtensions.includes('{$this->arrSomeArray[0]["data-extension"]}')) {
                        insert.setAttribute('disabled', 'disabled');
                    } else {
                        insert.removeAttribute('disabled', 'disabled');
                    } 
                }
           } 
            
           if ('{$this->objManager->LockedDocuments}') {
                if ('{$this->arrSomeArray[0]["data-item-type"]}' === 'file') {
                    if (!allowedExtensions.includes('{$this->arrSomeArray[0]["data-extension"]}')) {
                        insert.setAttribute('disabled', 'disabled');
                    } else {
                        insert.removeAttribute('disabled', 'disabled');
                    }
                }
           } 
        ");

        return $this->arrSomeArray;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnAddFiles = new Q\Plugin\BsFileControl($this, 'files');
        $this->btnAddFiles->Text = t(' Add files');
        $this->btnAddFiles->Glyph = 'fa fa-upload';
        $this->btnAddFiles->Multiple = true;
        $this->btnAddFiles->CssClass = 'btn btn-orange fileinput-button';
        $this->btnAddFiles->UseWrapper = false;

        $this->btnAllStart = new Bs\Button($this);
        $this->btnAllStart->Text = t('Start upload');
        $this->btnAllStart->CssClass = 'btn btn-darkblue all-start disabled';
        $this->btnAllStart->UseWrapper = false;
        $this->btnAllStart->addAction(new Q\Event\Click(), new Q\Action\Ajax('confirmParent_Click'));

        $this->btnAllCancel = new Bs\Button($this);
        $this->btnAllCancel->Text = t('Cancel all uploads');
        $this->btnAllCancel->CssClass = 'btn btn-warning all-cancel disabled';
        $this->btnAllCancel->UseWrapper = false;

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back to file manager');
        $this->btnBack->CssClass = 'btn btn-default back';
        $this->btnBack->UseWrapper = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnBack_Click'));
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\Ajax('dataClearing_Click'));

        $this->btnDone = new Bs\Button($this);
        $this->btnDone->Text = t('Done');
        $this->btnDone->CssClass = 'btn btn-success pull-right done';
        $this->btnDone->UseWrapper = false;
        $this->btnDone->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDone_Click'));

        $this->btnUploadStart = new Q\Plugin\Button($this);
        $this->btnUploadStart->Text = t(' Upload');
        $this->btnUploadStart->Glyph = 'fa fa-upload';
        $this->btnUploadStart->CssClass = 'btn btn-orange launch-start';
        $this->btnUploadStart->CausesValidation = false;
        $this->btnUploadStart->UseWrapper = false;
        $this->btnUploadStart->addAction(new Q\Event\Click(), new Q\Action\Ajax('uploadStart_Click'));

        $this->btnAddFolder = new Q\Plugin\Button($this);
        $this->btnAddFolder->Text = t(' Add folder');
        $this->btnAddFolder->Glyph = 'fa fa-folder';
        $this->btnAddFolder->CssClass = 'btn btn-orange';
        $this->btnAddFolder->CausesValidation = false;
        $this->btnAddFolder->UseWrapper = false;
        $this->btnAddFolder->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddFolder_Click'));

        $this->btnRefresh = new Q\Plugin\Button($this);
        $this->btnRefresh->Glyph = 'fa fa-refresh';
        $this->btnRefresh->CssClass = 'btn btn-darkblue';
        $this->btnRefresh->CausesValidation = false;
        $this->btnRefresh->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnRefresh_Click'));

        $this->btnRename = new Q\Plugin\Button($this);
        $this->btnRename->Text = t(' Rename');
        $this->btnRename->Glyph = 'fa fa-pencil';
        $this->btnRename->CssClass = 'btn btn-darkblue';
        $this->btnRename->CausesValidation = false;
        $this->btnRename->UseWrapper = false;
        $this->btnRename->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnRename_Click'));

        $this->btnCrop = new Q\Plugin\Button($this);
        $this->btnCrop->Text = t(' Crop');
        $this->btnCrop->Glyph = 'fa fa-crop';
        $this->btnCrop->CssClass = 'btn btn-darkblue';
        $this->btnCrop->CausesValidation = false;
        $this->btnCrop->UseWrapper = false;
        $this->btnCrop->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCrop_Click'));

        $this->btnCopy = new Q\Plugin\Button($this);
        $this->btnCopy->Text = t(' Copy');
        $this->btnCopy->Glyph = 'fa fa-files-o';
        $this->btnCopy->CssClass = 'btn btn-darkblue';
        $this->btnCopy->CausesValidation = false;
        $this->btnCopy->UseWrapper = false;
        $this->btnCopy->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCopy_Click'));

        $this->btnDelete = new Q\Plugin\Button($this);
        $this->btnDelete->Text = t(' Delete');
        $this->btnDelete->Glyph = 'fa fa-trash-o';
        $this->btnDelete->CssClass = 'btn btn-darkblue';
        $this->btnDelete->CausesValidation = false;
        $this->btnDelete->UseWrapper = false;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));

        $this->btnMove = new Q\Plugin\Button($this);
        $this->btnMove->Text = t(' Move');
        $this->btnMove->Glyph = 'fa fa-reply-all';
        $this->btnMove->CssClass = 'btn btn-darkblue';
        $this->btnMove->CausesValidation = false;
        $this->btnMove->UseWrapper = false;
        $this->btnMove->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMove_Click'));

        $this->btnImageListView = new Q\Plugin\Button($this);
        $this->btnImageListView->Glyph = 'fa fa-list'; //  fa-align-justify
        $this->btnImageListView->CssClass = 'btn btn-darkblue';
        $this->btnImageListView->addCssClass('btn-imageList active');
        $this->btnImageListView->UseWrapper = false;
        $this->btnImageListView->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnImageListView_Click'));

        $this->btnListView = new Q\Plugin\Button($this);
        $this->btnListView->Glyph = 'fa fa-align-justify';
        $this->btnListView->CssClass = 'btn btn-darkblue';
        $this->btnListView->addCssClass('btn-list');
        $this->btnListView->UseWrapper = false;
        $this->btnListView->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnListView_Click'));

        $this->btnBoxView = new Q\Plugin\Button($this);
        $this->btnBoxView->Glyph = 'fa fa-th-large';
        $this->btnBoxView->CssClass = 'btn btn-darkblue';
        $this->btnBoxView->addCssClass('btn-box');
        $this->btnBoxView->UseWrapper = false;
        $this->btnBoxView->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnBoxView_Click'));

        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-trigger');
        //$this->addFilterActions();

        $this->btnInsert = new Q\Plugin\Button($this);
        $this->btnInsert->Text = t('Insert');
        $this->btnInsert->CssClass = 'btn btn-orange insert';
        $this->btnInsert->setHtmlAttribute("disabled", "disabled");
        $this->btnInsert->CausesValidation = false;
        $this->btnInsert->UseWrapper = false;
        $this->btnInsert->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnInsert_Click'));

        $this->btnCancel = new Q\Plugin\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->UseWrapper = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCancel_Click'));
    }

    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->Text = t('<p style="margin-top: 15px;">Corrupted table "folders" in the database or folder "upload" in the file system!</p>
                                    <p style="margin-top: 15px;">The table and the file system must be in sync.</p>
                                    <p style="margin-top: 15px;">Please contact the developer or webmaster!</p>');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addCloseButton(t("I take note and ask for help"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // UPLOAD

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t('Tip');
        $this->dlgModal2->Text = t('<p style="margin-top: 15px;">Sorry, files cannot be added to this reserved folder!</p>
                                    <p style="margin-top: 15px;">Choose another folder!</p>');
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addCloseButton(t("I close the window"));

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t('Tip');
        $this->dlgModal3->Text = t('<p style="margin-top: 15px;">Please choose only specific folder to upload files!</p>');
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addCloseButton(t("I close the window"));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t('Tip');
        $this->dlgModal4->Text = t('<p style="margin-top: 15px;">Cannot select multiple folders to upload files!</p>');
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
        $this->dlgModal4->addCloseButton(t("I close the window"));

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->AutoRenderChildren = true;
        $this->dlgModal5->Title = t('Info');
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 10px;">Please check if the destination is correct!</p>');
        $this->dlgModal5->HeaderClasses = 'btn-default';
        $this->dlgModal5->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal5->addCloseButton(t("I'll cancel"));
        $this->dlgModal5->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startUploadProcess_Click'));
        $this->dlgModal5->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // NEW FOLDER

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Title = t('Tip');
        $this->dlgModal6->Text = t('<p style="margin-top: 15px;">Sorry, a new folder cannot be added to this reserved folder!</p>
                                    <p style="margin-top: 15px;">Choose another folder!</p>');
        $this->dlgModal6->HeaderClasses = 'btn-darkblue';
        $this->dlgModal6->addCloseButton(t("I close the window"));

        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Title = t('Tip');
        $this->dlgModal7->Text = t('<p style="margin-top: 15px;">Please select only one folder to create a new folder in!</p>');
        $this->dlgModal7->HeaderClasses = 'btn-darkblue';
        $this->dlgModal7->addCloseButton(t("I close the window"));

        $this->dlgModal8 = new Bs\Modal($this);
        $this->dlgModal8->AutoRenderChildren = true;
        $this->dlgModal8->Title = t('Info');
        $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: 10px;">Please check if the destination is correct!</p>');
        $this->dlgModal8->HeaderClasses = 'btn-default';
        $this->dlgModal8->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal8->addCloseButton(t("I'll cancel"));
        $this->dlgModal8->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startAddFolderProcess_Click'));
        $this->dlgModal8->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal9 = new Bs\Modal($this);
        $this->dlgModal9->AutoRenderChildren = true;
        $this->dlgModal9->Title = t('Name of new folder');
        $this->dlgModal9->HeaderClasses = 'btn-default';
        $this->dlgModal9->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal9->addCloseButton(t("I'll cancel"));
        $this->dlgModal9->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('addFolderName_Click'));
        $this->dlgModal9->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal10 = new Bs\Modal($this);
        $this->dlgModal10->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">New folder created successfully!</p>');
        $this->dlgModal10->Title = t("Success");
        $this->dlgModal10->HeaderClasses = 'btn-success';
        $this->dlgModal10->addCloseButton(t("I close the window"));

        $this->dlgModal11 = new Bs\Modal($this);
        $this->dlgModal11->Title = t('Warning');
        $this->dlgModal11->Text = t('<p style="margin-top: 15px;">Failed to create new folder!</p>');
        $this->dlgModal11->HeaderClasses = 'btn-danger';
        $this->dlgModal11->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // RENAME

        $this->dlgModal12 = new Bs\Modal($this);
        $this->dlgModal12->Title = t('Tip');
        $this->dlgModal12->Text = t('<p style="margin-top: 15px;">Sorry, this reserved folder or file cannot be renamed!</p>
                                    <p style="margin-top: 15px;">Choose another folder or file!</p>');
        $this->dlgModal12->HeaderClasses = 'btn-darkblue';
        $this->dlgModal12->addCloseButton(t("I close the window"));

        $this->dlgModal13 = new Bs\Modal($this);
        $this->dlgModal13->Title = t('Tip');
        $this->dlgModal13->Text = t('<p style="margin-top: 15px;">Please select a folder or file!</p>');
        $this->dlgModal13->HeaderClasses = 'btn-darkblue';
        $this->dlgModal13->addCloseButton(t("I close the window"));

        $this->dlgModal14 = new Bs\Modal($this);
        $this->dlgModal14->Title = t('Tip');
        $this->dlgModal14->Text = t('<p style="margin-top: 15px;">Please select only one folder or file to rename!</p>');
        $this->dlgModal14->HeaderClasses = 'btn-darkblue';
        $this->dlgModal14->addCloseButton(t("I close the window"));

        $this->dlgModal15 = new Bs\Modal($this);
        $this->dlgModal15->AutoRenderChildren = true;
        $this->dlgModal15->Title = t('Rename the folder or file name');
        $this->dlgModal15->HeaderClasses = 'btn-default';
        $this->dlgModal15->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal15->addCloseButton(t("I'll cancel"));
        $this->dlgModal15->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('renameName_Click'));
        $this->dlgModal15->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal16 = new Bs\Modal($this);
        $this->dlgModal16->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Folder name changed successfully!</p>');
        $this->dlgModal16->Title = t("Success");
        $this->dlgModal16->HeaderClasses = 'btn-success';
        $this->dlgModal16->addCloseButton(t("I close the window"));

        $this->dlgModal17 = new Bs\Modal($this);
        $this->dlgModal17->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Failed to rename folder!</p>');
        $this->dlgModal17->Title = t("Warning");
        $this->dlgModal17->HeaderClasses = 'btn-danger';
        $this->dlgModal17->addCloseButton(t("I understand"));

        $this->dlgModal18 = new Bs\Modal($this);
        $this->dlgModal18->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">File name changed successfully!</p>');
        $this->dlgModal18->Title = t("Success");
        $this->dlgModal18->HeaderClasses = 'btn-success';
        $this->dlgModal18->addCloseButton(t("I close the window"));

        $this->dlgModal19 = new Bs\Modal($this);
        $this->dlgModal19->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Failed to rename file!</p>');
        $this->dlgModal19->Title = t("Warning");
        $this->dlgModal19->HeaderClasses = 'btn-danger';
        $this->dlgModal19->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // COPY

        $this->dlgModal20 = new Bs\Modal($this);
        $this->dlgModal20->Title = t('Tip');
        $this->dlgModal20->Text = t('<p style="margin-top: 15px;">Please select a specific folder(s) or file(s)!</p>');
        $this->dlgModal20->HeaderClasses = 'btn-darkblue';
        $this->dlgModal20->addCloseButton(t("I close the window"));

        $this->dlgModal21 = new Bs\Modal($this);
        $this->dlgModal21->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">It is not possible to copy the main directory!</p>');
        $this->dlgModal21->Title = t("Warning");
        $this->dlgModal21->HeaderClasses = 'btn-danger';
        $this->dlgModal21->addCloseButton(t("I understand"));

        $this->dlgModal22 = new Bs\Modal($this);
        $this->dlgModal22->AutoRenderChildren = true;
        $this->dlgModal22->Title = t('Copy files or folders');
        $this->dlgModal22->HeaderClasses = 'btn-default';
        $this->dlgModal22->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal22->addCloseButton(t("I'll cancel"));
        $this->dlgModal22->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startCopyingProcess_Click'));
        $this->dlgModal22->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal23 = new Bs\Modal($this);
        $this->dlgModal23->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Selected files and folders have been copied successfully!</p>');
        $this->dlgModal23->Title = t("Success");
        $this->dlgModal23->HeaderClasses = 'btn-success';
        $this->dlgModal23->addCloseButton(t("Ok"));

        $this->dlgModal24 = new Bs\Modal($this);
        $this->dlgModal24->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Error while copying items!</p>');
        $this->dlgModal24->Title = t("Warning");
        $this->dlgModal24->HeaderClasses = 'btn-danger';
        $this->dlgModal24->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // DELETE

        $this->dlgModal25 = new Bs\Modal($this);
        $this->dlgModal25->Title = t('Tip');
        $this->dlgModal25->Text = t('<p style="margin-top: 15px;">Sorry, this reserved folder or file cannot be deleted!</p>
                                    <p style="margin-top: 15px;">Choose another folder or file!</p>');
        $this->dlgModal25->HeaderClasses = 'btn-darkblue';
        $this->dlgModal25->addCloseButton(t("I close the window"));

        $this->dlgModal26 = new Bs\Modal($this);
        $this->dlgModal26->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">It is not possible to delete the main directory!</p>');
        $this->dlgModal26->Title = t("Warning");
        $this->dlgModal26->HeaderClasses = 'btn-danger';
        $this->dlgModal26->addCloseButton(t("I understand"));

        $this->dlgModal27 = new Bs\Modal($this);
        $this->dlgModal27->AutoRenderChildren = true;
        $this->dlgModal27->Title = t('Delete files or folders');
        $this->dlgModal27->HeaderClasses = 'btn-danger';
        $this->dlgModal27->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal27->addCloseButton(t("I'll cancel"));
        $this->dlgModal27->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startDeletionProcess_Click'));
        $this->dlgModal27->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal28 = new Bs\Modal($this);
        $this->dlgModal28->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The selected files and folders have been successfully deleted!</p>');
        $this->dlgModal28->Title = t("Success");
        $this->dlgModal28->HeaderClasses = 'btn-success';
        $this->dlgModal28->addCloseButton(t("Ok"));

        $this->dlgModal29 = new Bs\Modal($this);
        $this->dlgModal29->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Error while deleting items!</p>');
        $this->dlgModal29->Title = t("Warning");
        $this->dlgModal29->HeaderClasses = 'btn-danger';
        $this->dlgModal29->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // MOVE

        $this->dlgModal30 = new Bs\Modal($this);
        $this->dlgModal30->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">It is not possible to move the main directory!</p>');
        $this->dlgModal30->Title = t("Warning");
        $this->dlgModal30->HeaderClasses = 'btn-danger';
        $this->dlgModal30->addCloseButton(t("I understand"));

        $this->dlgModal31 = new Bs\Modal($this);
        $this->dlgModal31->Title = t('Tip');
        $this->dlgModal31->Text = t('<p style="margin-top: 15px;">Sorry, this reserved folder or file cannot be moved!</p>
                                    <p style="margin-top: 15px;">Choose another folder or file!</p>');
        $this->dlgModal31->HeaderClasses = 'btn-darkblue';
        $this->dlgModal31->addCloseButton(t("I close the window"));

        $this->dlgModal32 = new Bs\Modal($this);
        $this->dlgModal32->AutoRenderChildren = true;
        $this->dlgModal32->Title = t('Move files or folders');
        $this->dlgModal32->HeaderClasses = 'btn-default move-class';

        $this->dlgModal32->addCssClass("move-class");

        $this->dlgModal32->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal32->addCloseButton(t("I'll cancel"));
        $this->dlgModal32->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startMovingProcess_Click'));
        $this->dlgModal32->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal33 = new Bs\Modal($this);
        $this->dlgModal33->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The selected files and folders have been successfully moved!</p>');
        $this->dlgModal33->Title = t("Success");
        $this->dlgModal33->HeaderClasses = 'btn-success';
        $this->dlgModal33->addCloseButton(t("Ok"));

        $this->dlgModal34 = new Bs\Modal($this);
        $this->dlgModal34->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Error while moving items!</p>');
        $this->dlgModal34->Title = t("Warning");
        $this->dlgModal34->HeaderClasses = 'btn-danger';
        $this->dlgModal34->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // INSERT

        $this->dlgModal35 = new Bs\Modal($this);
        $this->dlgModal35->Title = t('Tip');
        $this->dlgModal35->Text = t('<p style="margin-top: 15px;">Sorry, be cannot insert into a reserved file!</p>
                                    <p style="margin-top: 15px;">Select and copy this file to another location, then insert!</p>');
        $this->dlgModal35->HeaderClasses = 'btn-darkblue';
        $this->dlgModal35->addCloseButton(t("I close the window"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CROP

        $this->dlgModal40 = new Bs\Modal($this);
        $this->dlgModal40->Title = t('Tip');
        $this->dlgModal40->Text = t('<p style="margin-top: 15px;">Please select a image!</p>');
        $this->dlgModal40->HeaderClasses = 'btn-darkblue';
        $this->dlgModal40->addCloseButton(t("I close the window"));

        $this->dlgModal41 = new Bs\Modal($this);
        $this->dlgModal41->Title = t('Tip');
        $this->dlgModal41->Text = t('<p style="margin-top: 15px;">Please select only one image to crop!</p>
                                    <p style="margin-top: 15px;">Allowed file types: jpg, jpeg, png.</p>');
        $this->dlgModal41->HeaderClasses = 'btn-darkblue';
        $this->dlgModal41->addCloseButton(t("I close the window"));

        $this->dlgModal42 = new Bs\Modal($this);
        $this->dlgModal42->Title = t('Tip');
        $this->dlgModal42->Text = t('<p style="margin-top: 15px;">Please select only one image to crop!</p>');
        $this->dlgModal42->HeaderClasses = 'btn-darkblue';
        $this->dlgModal42->addCloseButton(t("I close the window"));

        $this->dlgModal43 = new Bs\Modal($this);
        $this->dlgModal43->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image cropping succeeded!</p>');
        $this->dlgModal43->Title = t("Success");
        $this->dlgModal43->HeaderClasses = 'btn-success';
        $this->dlgModal43->addCloseButton(t("I close the window"));

        $this->dlgModal44 = new Bs\Modal($this);
        $this->dlgModal44->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image cropping failed!</p>');
        $this->dlgModal44->Title = t("Warning");
        $this->dlgModal44->HeaderClasses = 'btn-danger';
        $this->dlgModal44->addCloseButton(t("I understand"));

        $this->dlgModal45 = new Bs\Modal($this);
        $this->dlgModal45->Text = t('<p style="margin-top: 15px;">The image is invalid for cropping!</p>
                                    <p style="margin-top: 15px;">It is recommended to delete this image and upload it again!</p>');
        $this->dlgModal45->Title = t("Warning");
        $this->dlgModal45->HeaderClasses = 'btn-danger';
        $this->dlgModal45->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal46 = new Bs\Modal($this);
        $this->dlgModal46->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal46->Title = t("Warning");
        $this->dlgModal46->HeaderClasses = 'btn-danger';
        $this->dlgModal46->addCloseButton(t("I understand"));
    }

    public function portedCheckDestination()
    {
        $pnl1 = new Q\Plugin\Control\DestinationInfo($this->dlgModal5);
        $pnl2 = new Q\Plugin\Control\DestinationInfo($this->dlgModal8);
    }

    public function portedAddFolderTextBox()
    {
        $this->lblError = new Q\Plugin\Label($this->dlgModal9);
        $this->lblError->Text = t('Folder cannot be created without name!');
        $this->lblError->addCssClass("modal-error-text hidden");
        $this->lblError->setCssStyle('color', '#ff0000');
        $this->lblError->setCssStyle('font-weight', 600);
        $this->lblError->setCssStyle('padding-top', '5px');
        $this->lblError->UseWrapper = false;

        $this->lblSameName = new Q\Plugin\Label($this->dlgModal9);
        $this->lblSameName->Text = t('Cannot create a folder with the same name!');
        $this->lblSameName->addCssClass("modal-error-same-text hidden");
        $this->lblSameName->setCssStyle('color', '#ff0000');
        $this->lblSameName->setCssStyle('font-weight', 600);
        $this->lblSameName->setCssStyle('padding-top', '5px');
        $this->lblSameName->UseWrapper = false;

        $this->txtAddFolder = new Bs\TextBox($this->dlgModal9);
        $this->txtAddFolder->setHtmlAttribute('autocomplete', 'off');
        $this->txtAddFolder->addCssClass("modal-check-textbox");
        $this->txtAddFolder->setCssStyle('margin-top', '15px');
        $this->txtAddFolder->setCssStyle('margin-bottom', '15px');
        $this->txtAddFolder->setHtmlAttribute('required', 'required');
        $this->txtAddFolder->UseWrapper = false;
    }

    public function portedRenameTextBox()
    {
        $this->lblDirectoryError = new Q\Plugin\Label($this->dlgModal15);
        $this->lblDirectoryError->Text = t('The name of the main directory cannot be changed!');
        $this->lblDirectoryError->addCssClass("modal-error-directory hidden");
        $this->lblDirectoryError->setCssStyle('font-weight', 400);
        $this->lblDirectoryError->setCssStyle('padding-top', '5px');
        $this->lblDirectoryError->UseWrapper = false;

        $this->lblError = new Q\Plugin\Label($this->dlgModal15);
        $this->lblError->Text = t('Cannot rename a folder or file without a name!');
        $this->lblError->addCssClass("modal-error-text hidden");
        $this->lblError->setCssStyle('color', '#ff0000');
        $this->lblError->setCssStyle('font-weight', 600);
        $this->lblError->setCssStyle('padding-top', '5px');
        $this->lblError->UseWrapper = false;

        $this->lblRenameName = new Q\Plugin\Label($this->dlgModal15);
        $this->lblRenameName->Text = t('This name cannot be used because it is already in use!');
        $this->lblRenameName->addCssClass("modal-error-rename-text hidden");
        $this->lblRenameName->setCssStyle('color', '#ff0000');
        $this->lblRenameName->setCssStyle('font-weight', 600);
        $this->lblRenameName->setCssStyle('padding-top', '5px');
        $this->lblRenameName->UseWrapper = false;

        $this->txtRename = new Bs\TextBox($this->dlgModal15);
        $this->txtRename->setHtmlAttribute('autocomplete', 'off');
        $this->txtRename->addCssClass("modal-check-rename-textbox");
        $this->txtRename->setCssStyle('margin-top', '15px');
        $this->txtRename->setCssStyle('margin-bottom', '15px');
        $this->txtRename->setHtmlAttribute('required', 'required');
        $this->txtRename->UseWrapper = false;
    }

    public function portedCopyingListBox()
    {
        $this->lblDestinationError = new Q\Plugin\Label($this->dlgModal22);
        $this->lblDestinationError->Text = t('Please select a destination folder!');
        $this->lblDestinationError->addCssClass('destination-error hidden');
        $this->lblDestinationError->setCssStyle('width', '100%');
        $this->lblDestinationError->setCssStyle('color', '#ff0000');
        $this->lblDestinationError->setCssStyle('font-weight', 600);
        $this->lblDestinationError->setCssStyle('padding-top', '5px');
        $this->lblDestinationError->UseWrapper = false;

        $this->lblCourceTitle = new Q\Plugin\Label($this->dlgModal22);
        $this->lblCourceTitle->Text = t('Source folder: ');
        $this->lblCourceTitle->addCssClass('source-title');
        $this->lblCourceTitle->setCssStyle('width', '100%');
        $this->lblCourceTitle->setCssStyle('font-weight', 600);
        $this->lblCourceTitle->setCssStyle('padding-right', '5px');
        $this->lblCourceTitle->setCssStyle('padding-bottom', '5px');
        $this->lblCourceTitle->UseWrapper = false;

        $this->lblCourcePath = new Q\Plugin\Label($this->dlgModal22);
        $this->lblCourcePath->addCssClass('source-path');
        $this->lblCourcePath->setCssStyle('width', '100%');
        $this->lblCourcePath->setCssStyle('font-weight', 400);
        $this->lblCourcePath->setCssStyle('padding-right', '5px');
        $this->lblCourcePath->setCssStyle('padding-bottom', '5px');
        $this->lblCourcePath->UseWrapper = false;

        $this->lblCopyingTitle = new Q\Plugin\Label($this->dlgModal22);
        $this->lblCopyingTitle->Text = t('Destination folder: ');
        $this->lblCopyingTitle->setCssStyle('width', '100%');
        $this->lblCopyingTitle->setCssStyle('font-weight', 600);
        $this->lblCopyingTitle->setCssStyle('padding-right', '5px');
        $this->lblCopyingTitle->setCssStyle('padding-bottom', '5px');
        $this->lblCopyingTitle->UseWrapper = false;

        $this->dlgCopyingDestination = new Q\Plugin\Select2($this->dlgModal22);
        $this->dlgCopyingDestination->Width = '100%';
        $this->dlgCopyingDestination->MinimumResultsForSearch = -1; // If you want to remove the search box, set it to "-1"
        $this->dlgCopyingDestination->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->dlgCopyingDestination->AddItem(t('- Select One -'), null);
        $this->dlgCopyingDestination->Theme = 'web-vauu';
        $this->dlgCopyingDestination->AllowClear = true;
        $this->dlgCopyingDestination->AddAction(new Q\Event\Change(), new Q\Action\Ajax('dlgDestination_Change'));
    }

    public function portedDeleteBox()
    {
        $this->lblDeletionWarning = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeletionWarning->Text = t('Are you sure you want to permanently delete these files and folders?');
        $this->lblDeletionWarning->addCssClass("deletion-warning-text");
        $this->lblDeletionWarning->setCssStyle('width', '100%');
        $this->lblDeletionWarning->setCssStyle('color', '#ff0000');
        $this->lblDeletionWarning->setCssStyle('font-weight', 600);
        $this->lblDeletionWarning->setCssStyle('padding-top', '5px');
        $this->lblDeletionWarning->UseWrapper = false;

        $this->lblDeletionInfo = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeletionInfo->Text = t("Can\'t undo it afterwards!");
        $this->lblDeletionInfo->addCssClass("deletion-info-text");
        $this->lblDeletionInfo->setCssStyle('width', '100%');
        $this->lblDeletionInfo->setCssStyle('color', '#ff0000');
        $this->lblDeletionInfo->setCssStyle('font-weight', 600);
        $this->lblDeletionInfo->setCssStyle('padding-top', '5px');
        $this->lblDeletionInfo->UseWrapper = false;

        $this->lblDeleteError = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeleteError->Text = t('Files are locked or cannot be deleted together with folders!');
        $this->lblDeleteError->addCssClass("delete-error-text hidden");
        $this->lblDeleteError->setCssStyle('width', '100%');
        $this->lblDeleteError->setCssStyle('color', '#ff0000');
        $this->lblDeleteError->setCssStyle('font-weight', 600);
        $this->lblDeleteError->setCssStyle('padding-top', '5px');
        $this->lblDeleteError->UseWrapper = false;

        $this->lblDeleteInfo = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeleteInfo->Text = t('Unlocked files can be deleted!');
        $this->lblDeleteInfo->addCssClass("delete-info-text hidden");
        $this->lblDeleteInfo->setCssStyle('width', '100%');
        $this->lblDeleteInfo->setCssStyle('font-weight', 600);
        $this->lblDeleteInfo->setCssStyle('padding-top', '5px');
        $this->lblDeleteInfo->setCssStyle('padding-bottom', '15px');
        $this->lblDeleteInfo->UseWrapper = false;

        $this->lblDeleteTitle = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeleteTitle->Text = t('Files and folders to be deleted: ');
        $this->lblDeleteTitle->setCssStyle('font-weight', 600);
        $this->lblDeleteTitle->setCssStyle('padding-right', '5px');
        $this->lblDeleteTitle->setCssStyle('padding-bottom', '5px');
        $this->lblDeleteTitle->UseWrapper = false;

        $this->lblDeletePath = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeletePath->addCssClass('delete-path');
        $this->lblDeletePath->setCssStyle('font-weight', 400);
        $this->lblDeletePath->setCssStyle('padding-right', '5px');
        $this->lblDeletePath->setCssStyle('padding-bottom', '5px');
        $this->lblDeletePath->UseWrapper = false;
    }

    public function portedMovingListBox()
    {
        $this->lblMovingError = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingError->Text = t('Files are locked or cannot be moved together  with folders!');
        $this->lblMovingError->addCssClass("move-error-text hidden");
        $this->lblMovingError->setCssStyle('width', '100%');
        $this->lblMovingError->setCssStyle('color', '#ff0000');
        $this->lblMovingError->setCssStyle('font-weight', 600);
        $this->lblMovingError->setCssStyle('padding-top', '5px');
        $this->lblMovingError->UseWrapper = false;

        $this->lblMoveInfo = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMoveInfo->Text = t('Unlocked files can be moved!');
        $this->lblMoveInfo->addCssClass("move-info-text hidden");
        $this->lblMoveInfo->setCssStyle('width', '100%');
        $this->lblMoveInfo->setCssStyle('font-weight', 600);
        $this->lblMoveInfo->setCssStyle('padding-top', '5px');
        $this->lblMoveInfo->setCssStyle('padding-bottom', '15px');
        $this->lblMoveInfo->UseWrapper = false;

        $this->lblMovingDestinationError = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingDestinationError->Text = t('Please select a destination folder!');
        $this->lblMovingDestinationError->addCssClass('destination-moving-error hidden');
        $this->lblMovingDestinationError->setCssStyle('color', '#ff0000');
        $this->lblMovingDestinationError->setCssStyle('font-weight', 600);
        $this->lblMovingDestinationError->setCssStyle('padding-top', '5px');
        $this->lblMovingDestinationError->UseWrapper = false;

        $this->lblMovingCourceTitle = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingCourceTitle->Text = t('Source folder: ');
        $this->lblMovingCourceTitle->addCssClass('moving-source-title');
        $this->lblMovingCourceTitle->setCssStyle('font-weight', 600);
        $this->lblMovingCourceTitle->setCssStyle('padding-right', '5px');
        $this->lblMovingCourceTitle->setCssStyle('padding-bottom', '5px');
        $this->lblMovingCourceTitle->UseWrapper = false;

        $this->lblMovingCourcePath = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingCourcePath->addCssClass('moving-source-path');
        $this->lblMovingCourcePath->setCssStyle('font-weight', 400);
        $this->lblMovingCourcePath->setCssStyle('padding-right', '5px');
        $this->lblMovingCourcePath->setCssStyle('padding-bottom', '5px');
        $this->lblMovingCourcePath->UseWrapper = false;

        $this->lblMovingTitle = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingTitle->Text = t('Destination folder: ');
        $this->lblMovingTitle->Width = '100%';
        $this->lblMovingTitle->setCssStyle('font-weight', 600);
        $this->lblMovingTitle->setCssStyle('padding-right', '5px');
        $this->lblMovingTitle->setCssStyle('padding-bottom', '5px');
        $this->lblMovingTitle->UseWrapper = false;

        $this->dlgMovingDestination = new Q\Plugin\Select2($this->dlgModal32);
        $this->dlgMovingDestination->Width = '100%';
        $this->dlgMovingDestination->MinimumResultsForSearch = -1; // If you want to remove the search box, set it to "-1"
        $this->dlgMovingDestination->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->dlgMovingDestination->AddItem(t('- Select One -'), null);
        $this->dlgMovingDestination->Theme = 'web-vauu';
        $this->dlgMovingDestination->AllowClear = true;
        $this->dlgMovingDestination->AddAction(new Q\Event\Change(), new Q\Action\Ajax('dlgDestination_Change'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // REPOSITORY LINK

    public function appendData_Click(ActionParams $params)
    {
        $this->arrSomeArray = [["data-id" => 1, "data-path" => "", "data-item-type" => "dir", "data-locked" => 0, "data-activities-locked" => 0]];
        Application::executeJavaScript(sprintf("$('.breadcrumbs').empty()"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // UPLOAD

    public function uploadStart_Click(ActionParams $params)
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Rikutud tabel "folders" andmebaasis  või kausta "upload" failisüsteem! ...
            return;
        }

        if (!$this->arrSomeArray) {
            $this->showDialog(3); // Vali failide üleslaadimiseks ainult konkreetne kaust!
            return;
        }

        $locked = $this->arrSomeArray[0]["data-activities-locked"];

        if ($locked == 1) {
            $this->showDialog(2); // Vabandust, sellesse reserveeritud kausta ei saa faile lisada! ...
            return;
        }

        if ($this->arrSomeArray[0]["data-item-type"] !== "dir") {
            $this->showDialog(3); // Vali failide üleslaadimiseks ainult konkreetne kaust!
            return;
        }

        if (count($this->arrSomeArray) !== 1) {
            $this->showDialog(7); // Palun vali uue kausta loomiseks ainult üks kaust!
            return;
        }

        $this->showDialog(5); // Palun kontrolli, kas sihtkoht on õige!

        $this->intDataId = $this->arrSomeArray[0]["data-id"];
        $this->strDataPath = $this->arrSomeArray[0]["data-path"];
        $_SESSION['folderId'] = $this->intDataId;
        $_SESSION['filePath'] = $this->strDataPath;

        if ($this->strDataPath == "") {
            $_SESSION['folderId'] = 1;
            $_SESSION['filePath'] = "";
            Application::executeJavaScript(sprintf("$('.modalPath').append('/')"));
        } else {
            Application::executeJavaScript(sprintf("$('.modalPath').append('{$this->strDataPath}')"));
        }
    }

    private function showDialog($modalNumber)
    {
        $dialog = $this->getDialogByNumber($modalNumber);
        $dialog->showDialogBox();
    }

    private function getDialogByNumber($modalNumber)
    {
        switch ($modalNumber) {
            case 2:
                return $this->dlgModal2;
            case 3:
                return $this->dlgModal3;
            case 5:
                return $this->dlgModal5;
            case 7:
                return $this->dlgModal7;
            default:
                // Default to dlgModal3 if an unknown modal number is provided.
                return $this->dlgModal3;
        }
    }

    public function startUploadProcess_Click(ActionParams $params)
    {
        $script = "
            $('.fileupload-buttonbar').removeClass('hidden');
            $('.upload-wrapper').removeClass('hidden');
            $('.fileupload-donebar').addClass('hidden');
            $('body').removeClass('no-scroll');
            $('.head').addClass('hidden');
            $('.files-heading').addClass('hidden');
            $('.scroll-wrapper').addClass('hidden');
            $('.alert').remove();
        ";

        Application::executeJavaScript($script);

        $this->dlgModal5->hideDialogBox(); // Please check if the destination is correct!
    }

    public function confirmParent_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $path = $this->objManager->RootPath . $this->strDataPath;

        $folderId = isset($_SESSION['folderId']) ? $_SESSION['folderId'] : null;

        if ($folderId) {
            $objFolder = Folders::loadById($folderId);

            // Check if the folder exists before updating properties
            if ($objFolder) {
                $objFolder->setLockedFile(1);
                $objFolder->setMtime(filemtime($path));
                $objFolder->save();
            }
        }
    }

    public function btnBack_Click(ActionParams $params)
    {
        $script = "
            $('.fileupload-buttonbar').addClass('hidden');
            $('.upload-wrapper').addClass('hidden');
            $('body').addClass('no-scroll');
            $('.head').removeClass('hidden');
            $('.files-heading').removeClass('hidden');
            $('.scroll-wrapper').removeClass('hidden');
            $('.alert').remove();
        ";

        Application::executeJavaScript($script);

        $this->objManager->refresh();
    }

    protected function btnDone_Click(ActionParams $params)
    {
        unset($_SESSION['folderId']);
        unset($_SESSION['filePath']);

        Application::executeJavaScript("
            $('.fileupload-buttonbar').addClass('hidden');
            $('.upload-wrapper').addClass('hidden');
            $('body').addClass('no-scroll');
            $('.head').removeClass('hidden');
            $('.files-heading').removeClass('hidden');
            $('.scroll-wrapper').removeClass('hidden');
            $('.alert').remove();
        ");

        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // NEW FOLDER

    public function btnAddFolder_Click(ActionParams $params)
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Rikutud tabel "folders" andmebaasis  või kausta "upload" failisüsteem! ...
            return;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal7->showDialogBox();
            return;
        }

        $locked = $this->arrSomeArray[0]["data-activities-locked"];

        if ($locked == 1) {
            $this->dlgModal6->showDialogBox();
            return;
        }

        if (count($this->arrSomeArray) !== 1 || $this->arrSomeArray[0]["data-item-type"] !== "dir") {
            $this->dlgModal7->showDialogBox();
            return;
        }

        $this->dlgModal8->showDialogBox();
        $this->intDataId = $this->arrSomeArray[0]["data-id"];
        $this->strDataPath = $this->arrSomeArray[0]["data-path"];

        if ($this->strDataPath == "") {
            $this->intDataId = 1;
            $this->strDataPath = "";
            Application::executeJavaScript(sprintf("$('.modalPath').append('/')"));
        } else {
            Application::executeJavaScript(sprintf("$('.modalPath').append('{$this->strDataPath}')"));
        }
    }

    public function startAddFolderProcess_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $_SESSION['fileId'] = $this->intDataId;
        $_SESSION['filePath'] = $this->strDataPath;

        $this->dlgModal8->hideDialogBox();
        $this->dlgModal9->showDialogBox(); // Uue kausta nimi
        $this->txtAddFolder->Text = '';

        $javascript = "
        $('.modal-check-textbox').on('keyup keydown', function() {
            var length = $(this).val().length;
            var modalHeader = $('.modal-header');
            var modalFooterBtn = $('.modal-footer .btn-orange');

            if (length === 0) {
                modalHeader.removeClass('btn-default').addClass('btn-danger');
                $('.modal-error-same-text').addClass('hidden');
                $('.modal-error-text').removeClass('hidden');
                modalFooterBtn.attr('disabled', 'disabled');
            } else {
                modalHeader.removeClass('btn-danger').addClass('btn-default');
                $('.modal-error-same-text').addClass('hidden');
                $('.modal-error-text').addClass('hidden');
                modalFooterBtn.removeAttr('disabled', 'disabled');
            }
        });
    ";

        Application::executeJavaScript(sprintf($javascript));
    }

    public function addFolderName_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $path = $this->objManager->RootPath . $_SESSION['filePath'];
        $scanned_directory = array_diff(scandir($path), array('..', '.'));

        if (trim($this->txtAddFolder->Text) == "") {
            Application::executeJavaScript($this->getJavaScriptForEmptyFolder());
            return;
        }

        if (in_array(trim($this->txtAddFolder->Text), $scanned_directory)) {
            Application::executeJavaScript($this->getJavaScriptForDuplicateFolder());
            return;
        }

        $this->makeFolders($this->txtAddFolder->Text, $_SESSION['fileId'], $path);
        $this->dlgModal9->hideDialogBox();
    }

    private function getJavaScriptForEmptyFolder()
    {
        return sprintf("
            $('.modal-header').removeClass('btn-default').addClass('btn-danger');
            $('.modal-error-same-text').addClass('hidden');
            $('.modal-error-text').removeClass('hidden');
            $('.modal-footer .btn-orange').attr('disabled', 'disabled');
        ");
    }

    private function getJavaScriptForDuplicateFolder()
    {
        return sprintf("
            $('.modal-header').removeClass('btn-default').addClass('btn-danger');
            $('.modal-error-same-text').removeClass('hidden');
            $('.modal-error-text').addClass('hidden');
            $('.modal-footer .btn-orange').attr('disabled', 'disabled');
        ");
    }

    protected function makeFolders($text, $id, $path)
    {
        clearstatcache();

        $fullPath = $path . "/" . trim(QString::sanitizeForUrl($text));
        $relativePath = $this->objManager->getRelativePath($fullPath);

        Folder::makeDirectory($fullPath, 0777);

        if ($id) {
            $objFolder = Folders::loadById($id);
            if ($objFolder->getLockedFile() !== 1) {
                $objFolder->setMtime(filemtime($path));
                $objFolder->setLockedFile(1);
                $objFolder->save();
            }
        }

        $objAddFolder = new Folders();
        $objAddFolder->setParentId($id);
        $objAddFolder->setPath($relativePath);
        $objAddFolder->setName(trim($text));
        $objAddFolder->setType('dir');
        $objAddFolder->setMtime(filemtime($path));
        $objAddFolder->setLockedFile(0);
        $objAddFolder->save();

        foreach ($this->tempFolders as $tempFolder) {
            $tempPath = $this->objManager->TempPath . '/_files/' . $tempFolder . $relativePath;
            Folder::makeDirectory($tempPath, 0777);
        }

        $dialogBox = file_exists($fullPath) ? $this->dlgModal10 : $this->dlgModal11;
        $dialogBox->showDialogBox();

        unset($_SESSION['fileId']);
        unset($_SESSION['filePath']);
        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // REFRESH

    public function btnRefresh_Click(ActionParams $params)
    {
        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // RENAME

    public function btnRename_Click(ActionParams $params)
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal13->showDialogBox();
            return;
        }

        $locked = $this->arrSomeArray[0]["data-activities-locked"];

        if ($locked == 1) {
            $this->dlgModal12->showDialogBox();
            return;
        }

        if (count($this->arrSomeArray) !== 1) {
            $this->dlgModal14->showDialogBox();
            return;
        }

        $this->intDataId = $this->arrSomeArray[0]["data-id"];
        $this->strDataName = $this->arrSomeArray[0]["data-name"];
        $this->strDataPath = $this->arrSomeArray[0]["data-path"];
        $this->strDataType = $this->arrSomeArray[0]["data-item-type"];
        $this->intDataLocked = $this->arrSomeArray[0]["data-locked"];

        $this->txtRename->Text = $this->strDataName;

        $this->dlgModal15->showDialogBox();

        if ($this->txtRename->Text == "upload") {
            $this->showUploadError();
        } else {
            $this->showRenameJavaScript();
        }
    }

    private function showUploadError()
    {
        $script = "
            $('.modal-header').removeClass('btn-default').addClass('btn-danger');
            $('.modal-error-directory').removeClass('hidden');
            $('.modal-check-rename-textbox').addClass('hidden');
            $('.modal-error-rename-text').addClass('hidden');
            $('.modal-error-text').addClass('hidden');
            $('.modal-footer .btn-orange').attr('disabled', 'disabled');
        ";
        Application::executeJavaScript($script);
    }

    private function showRenameJavaScript()
    {
        $script = "
            $('.modal-check-rename-textbox').on('keyup keydown', function() {
                var length = $('.modal-check-rename-textbox').val().length;
                if(length == 0) {
                    $('.modal-header').removeClass('btn-default').addClass('btn-danger');
                    $('.modal-error-rename-text').addClass('hidden');
                    $('.modal-error-text').removeClass('hidden');
                    $('.modal-footer .btn-orange').attr('disabled', 'disabled');
                } else {
                    $('.modal-header').removeClass('btn-danger').addClass('btn-default');
                    $('.modal-error-rename-text').addClass('hidden');
                    $('.modal-error-text').addClass('hidden');
                    $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
                }
            });
        ";
        Application::executeJavaScript($script);
    }

    public function renameName_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $path = $this->objManager->RootPath . $this->strDataPath;

        // Check conditions preventing renaming
        if ($this->isRenameNotAllowed($path)) {
            $this->showRenameError();
            return;
        }

        // Perform the renaming based on the data type
        if ($this->strDataType == "dir") {
            $this->renameDirectory();
        } else {
            $this->renameFile();
        }

        // Additional operations after renaming
        $this->postRenameOperations();

        $this->objManager->refresh();
    }

    // Helper functions

    private function isRenameNotAllowed($path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $files = array_diff(scandir(dirname($path)), array('..', '.'));

        $matchedString = ($this->strDataType == "file") ? $this->txtRename->Text . "." . $ext : $this->txtRename->Text;

        return in_array($matchedString, $files);
    }

    private function showRenameError()
    {
        Application::executeJavaScript(sprintf("
        $('.modal-header').removeClass('btn-default').addClass('btn-danger');
        $('.modal-error-rename-text').removeClass('hidden');
        $('.modal-error-text').addClass('hidden');
        $('.modal-footer .btn-orange').attr('disabled', 'disabled');
    "));
    }

    private function renameDirectory()
    {
        // Perform directory renaming logic

        $path = $this->objManager->RootPath . $this->strDataPath;
        $parts = pathinfo($path);
        $sanitizedName = QString::sanitizeForUrl(trim($this->txtRename->Text));
        $this->strNewPath = $parts['dirname'] . '/' . $sanitizedName;

        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        // If the folder does not contain subfolders and files, renaming the folder is easy. If this folder contains
        // subfolders and files, all names and paths in descending order must be renamed according to the same logic
        if ($this->intDataLocked == 0) {
            // If there are no subfolders or files in a folder, renaming is easy.
            if (is_dir($path)) {
                // We will immediately update the database accordingly.
                $objFolder = Folders::loadById($this->intDataId);
                $objFolder->Name = trim($this->txtRename->Text);
                $objFolder->Path = $this->objManager->getRelativePath($this->strNewPath);
                $objFolder->Mtime = time();
                $objFolder->save();

                $this->objManager->rename($path, $this->strNewPath);
            }

            // Here the files must be renamed according to the same logic in temp directories
            foreach ($this->tempFolders as $tempFolder) {
                if (is_dir($this->objManager->TempPath . '/_files/' . $tempFolder . $this->strDataPath)) {
                    $this->objManager->rename($this->objManager->TempPath . '/_files/' . $tempFolder . $this->strDataPath, $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($this->strNewPath));
                }
            }

            $this->handleResult();
        } else {
            // If there are subfolders and files in the folder, they must also be renamed.
            $this->tempItems = $this->fullScanIds($this->intDataId);
            $arrUpdatehash = [];

            if ($this->intDataId) {
                $obj = Folders::loadById($this->intDataId);
                $obj->Name = trim($this->txtRename->Text);
                $obj->Mtime = time();
                $obj->save();
            }

            foreach ($objFolders as $objFolder) {
                foreach ($this->tempItems as $temp) {
                    if ($temp == $objFolder->getId()) {
                        $newPath = str_replace(basename($this->strDataPath), $sanitizedName, $objFolder->Path);
                        $this->strNewPath = $this->objManager->RootPath . $newPath;

                        $arrUpdatehash[] = $newPath;
                        $this->objManager->UpdatedHash = rawurlencode(dirname($arrUpdatehash[0]));

                        if (is_dir($this->objManager->RootPath . $objFolder->getPath())) {
                            $this->objManager->rename($this->objManager->RootPath . $objFolder->getPath(), $this->strNewPath);
                        }

                        foreach ($this->tempFolders as $tempFolder) {
                            if (is_dir($this->objManager->TempPath . '/_files/' . $tempFolder . $objFolder->getPath())) {
                                $this->objManager->rename($this->objManager->TempPath . '/_files/' . $tempFolder . $objFolder->getPath(), $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($this->strNewPath));
                            }
                        }

                        if ($this->intDataLocked !== 0) {
                            $obj = Folders::loadById($objFolder->getId());
                            $obj->Path = $this->objManager->getRelativePath($this->strNewPath);
                            $obj->Mtime = time();
                            $obj->save();
                        }

                    }
                }
            }

            foreach ($objFiles as $objFile) {
                foreach ($this->tempItems as $temp) {
                    if ($temp == $objFile->getFolderId()) {
                        $newPath = str_replace(basename($this->strDataPath), $sanitizedName, $objFile->Path);
                        $this->strNewPath = $this->objManager->RootPath . $newPath;

                        if (is_file($this->objManager->RootPath . $objFile->getPath())) {
                            $this->objManager->rename($this->objManager->RootPath . $objFile->getPath(), $this->objManager->RootPath . $this->strNewPath);
                        }

                        $obj = Files::loadById($objFile->getId());
                        $obj->Path = $this->objManager->getRelativePath($this->strNewPath);
                        $obj->Mtime = time();
                        $obj->save();
                    }
                }
            }

            $this->handleResult();
        }
    }

    private function renameFile()
    {
        // Perform file renaming logic

        $path = $this->objManager->RootPath . $this->strDataPath;
        $parts = pathinfo($path);

        // The file name is changed in the main directory
        if (is_file($path)) {
            $this->strNewPath = $parts['dirname'] . '/' . trim($this->txtRename->Text) . '.' . strtolower($parts['extension']);
            $this->objManager->rename($this->objManager->RootPath . $this->strDataPath, $this->strNewPath);
        }

        // Here the files must be renamed according to the same logic in temp directories
        if (in_array(strtolower($parts['extension']), $this->arrAllowed)) {
            foreach ($this->tempFolders as $tempFolder) {
                if (is_file($this->objManager->TempPath . '/_files/' . $tempFolder . $this->strDataPath)) {
                    $this->objManager->rename($this->objManager->TempPath . '/_files/' . $tempFolder . $this->strDataPath, $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($this->strNewPath));
                }
            }
        }

        $objFile = Files::loadById($this->intDataId);
        $objFile->Name = basename($this->strNewPath);
        $objFile->Path = $this->objManager->getRelativePath($this->strNewPath);
        $objFile->Size = filesize($this->strNewPath);
        $objFile->Mtime = time();
        $objFile->save();

        $this->handleResult();
    }

    private function handleResult()
    {
        // Handle success or failure scenarios after renaming

        if (file_exists($this->strNewPath)) {

            $this->dlgModal15->hideDialogBox(); // Nimeta kausta või faili nimi ümber

            if ($this->strDataType == "dir") {
                $this->dlgModal16->showDialogBox(); // Kausta nime muutmine õnnestus!
            } else {
                $this->dlgModal18->showDialogBox(); // Failinime muutmine õnnestus!
            }
        } else {
            if ($this->strDataType == "dir") {
                $this->dlgModal17->showDialogBox(); // Kausta nime muutmine ebaõnnestus!
            } else {
                $this->dlgModal19->showDialogBox(); // Failinime muutmine ebaõnnestus!
            }
        }
    }

    private function postRenameOperations()
    {
        if (count($this->arrSomeArray) === 1) {
            Application::executeJavaScript(sprintf("$('.breadcrumbs').empty()"));
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // CROP

    public function btnCrop_Click(ActionParams $params)
    {

        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal40->showDialogBox(); // Please select a image!
            return;
        }

        if ($this->arrSomeArray[0]['data-item-type'] == 'file' &&
            !in_array(strtolower($this->arrSomeArray[0]['data-extension']), $this->arrCroppieTypes)) {
            $this->dlgModal41->showDialogBox(); // Please select only one image to crop! Allowed file types: jpg, jpeg, png.
            return;
        }

        if (count($this->arrSomeArray) !== 1 || $this->arrSomeArray[0]['data-item-type'] !== 'file') {
            $this->dlgModal42->showDialogBox(); // Please select only one image to crop!
            return;
        }

        $this->strDataPath = $this->arrSomeArray[0]["data-path"];
        $fullFilePath = $this->objManager->RootUrl . $this->strDataPath;
        
        // Check if the file exists and its size is 0 bytes
        if (file_exists($fullFilePath) && filesize($fullFilePath) === 0) {
            $this->dlgModal45->showDialogBox(); // The image is invalid for cropping! It is recommended to delete this image and upload it again!
            return;
        }

        $scanFolders = $this->scanForSelect();
        $folderData = [];

        foreach ($scanFolders as $folder) {
            if ($folder['activities_locked'] !== 1) {
                $level = $folder['depth'];
                if ($this->checkString($folder['path'])) {
                    $level = 0;
                }
                $folderData[] = [
                    'id' => $folder['path'],
                    'text' => $folder['name'],
                    'level' => $level,
                    'folderId' => $folder['id']
                ];
            }
        }

        $this->dlgPopup->showDialogBox();

        $this->dlgPopup->SelectedImage = $fullFilePath;
        $this->dlgPopup->Data = $folderData;
    }

    public function objManagerRefresh_Click(ActionParams $params)
    {
        if (file_exists($this->objManager->RootPath . $this->dlgPopup->FinalPath)) {
            $this->dlgModal43->showDialogBox(); // Image cropping succeeded!
        } else {
            $this->dlgModal44->showDialogBox(); // Image cropping failed!
        }

        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // COPY

    public function btnCopy_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        // Check for conditions preventing copying
        if (!$this->validateCopyConditions()) {
            return;
        }

        // Prepare and send data to function fullCopy($src, $dst)
        $this->prepareCopyData();

        // Data validation and processing
        $this->processCopyData($objFolders, $objFiles);

        // UI-related operations
        $this->updateCopyDestinationDialog();

        // Show the copy dialog
        $this->showCopyDialog();
    }

    // Helper functions

    private function validateCopyConditions()
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Rikutud tabel "folders" andmebaasis  või kausta "upload" failisüsteem! ...
            return false;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal20->showDialogBox(); // Palun vali konkreetne/sed kaust/ad või fail/failid!
            return false;
        }

        if ($this->arrSomeArray[0]["data-id"] == 1 && $this->arrSomeArray[0]["data-path"] == "") {
            $this->dlgModal21->showDialogBox(); // Peakataloogi pole võimalik kopeerida!
            return false;
        }

        return true;
    }

    private function prepareCopyData()
    {
        // Preparing and sending data to the function fullCopy($src, $dst)

        $tempArr = [];

        foreach ($this->arrSomeArray as $arrSome) {
            $tempArr[] = $arrSome;
        }
        foreach ($tempArr as $temp) {
            if ($temp['data-path']) {
                $this->tempSelectedItems[] = $temp['data-path'];
            }
        }
    }

    private function processCopyData($objFolders, $objFiles)
    {
        // Processing logic for copying data

        $this->copyDirectory($objFolders);
        $this->copyFile($objFiles);
    }

    private function copyDirectory($objFolders)
    {
        // Perform directory copying logic

        $dataFolders = [];
        $tempIds = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "dir") {
                $dataFolders[] = $arrSome["data-id"];
            }
        }
        foreach ($dataFolders as $dataFolder) {
            $tempIds = array_merge($tempIds, $this->fullScanIds($dataFolder));

        }
        foreach ($objFolders as $objFolder) {
            foreach ($tempIds as $tempId) {
                if ($objFolder->getId() == $tempId) {
                    $this->tempItems[] = $objFolder->getPath();
                }
            }
            sort($this->tempItems);
        }
    }

    private function copyFile($objFiles)
    {
        // Perform file copying logic

        $tempIds = [];
        $dataFiles = [];

        foreach ($objFiles as $objFile) {
            foreach ($tempIds as $tempId) {
                if ($objFile->getFolderId() == $tempId) {
                    $this->tempItems[] = $objFile->getPath();
                }
            }
            sort($this->tempItems);
        }

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "file") {
                $dataFiles[] = $arrSome["data-id"];
            }
        }
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    $this->tempItems[] = $objFile->getPath();
                }
            }
            sort($this->tempItems);
        }
    }

    private function updateCopyDestinationDialog()
    {
        // Update destination dialog UI

        $objPaths = $this->scanForSelect();

        foreach ($this->tempItems as $tempItem) {
            if (is_dir($this->objManager->RootPath . $tempItem)) {
                $this->objLockedDirs[] = $tempItem;
            }
        }

        if ($objPaths) foreach ($objPaths as $objPath) {
            if ($objPath['activities_locked'] == 1) {
                array_push($this->objLockedDirs, $objPath["path"]);
            }
        }

        if ($objPaths) foreach ($objPaths as $objPath) {
            if (in_array($objPath["path"], $this->objLockedDirs)) {
                $mark = true;
            } else {
                $mark = false;
            }
            $this->dlgCopyingDestination->AddItem($this->printDepth($objPath['name'], $objPath['parent_id'], $objPath['depth']), $objPath, null, $mark);
        }
    }

    private function showCopyDialog()
    {
        // Show the copy dialog

        if (count($this->tempItems) !== 0) {
            $source = join(', ', $this->tempItems);
            $this->lblCourcePath->Text = $source;
            $this->lblCourcePath->setCssStyle('color', '#000000');
            $this->dlgCopyingDestination->Enabled = true;
        } else {
            $this->lblCourcePath->Text = t("It is not possible to copy the main directory!");
            $this->lblCourcePath->setCssStyle('color', '#ff0000');
            $this->dlgCopyingDestination->Enabled = false;
        }

        if (count($this->tempItems) == 0 || $this->dlgCopyingDestination->SelectedValue == null) {
            Application::executeJavaScript(sprintf("
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        }

        $this->dlgModal22->showDialogBox();  // Kopeerige failid või kaustad
    }

    public function startCopyingProcess_Click(ActionParams $params)
    {
        $objPath = $this->dlgCopyingDestination->SelectedValue;

        if (!$objPath) {
            $this->handleCopyError();
            return;
        }

        $this->dlgModal22->hideDialogBox(); // Kopeerige failid või kaustad

        if ($this->dlgCopyingDestination->SelectedValue !== null) {
            foreach ($this->tempSelectedItems as $selectedItem) {
                $this->fullCopyItem($selectedItem, $objPath);
            }
        }

        $this->handleCopyResult();
    }

    // Helper functions

    private function handleCopyError()
    {
        $this->resetDestinationAndDisplayError();
        $this->cleanupAfterCopy();
    }

    private function resetDestinationAndDisplayError()
    {
        if ($this->dlgCopyingDestination->SelectedValue == null) {
            $this->dlgCopyingDestination->removeAllItems();
            $this->dlgCopyingDestination->AddItem(t('- Select One -'), null);

            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-default').addClass('btn-danger');
                $('.destination-error').removeClass('hidden');
                $('.source-title').addClass('hidden');
                $('.source-path').addClass('hidden');
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        }
    }

    private function fullCopyItem($selectedItem, $objPath)
    {
        $sourcePath = $this->objManager->RootPath . $selectedItem;
        $destinationPath = $this->objManager->RootPath . $objPath['path'] . "/" . basename($selectedItem);

        // Perform the copying logic
        $this->fullCopy($sourcePath, $destinationPath);
    }

    private function handleCopyResult()
    {
        if ($this->intStoredChecks >= count($this->tempItems)) {
            $this->dlgModal23->showDialogBox(); // Valitud failid ja kaustad on edukalt kopeeritud!
        } else {
            $this->dlgModal24->showDialogBox(); // Viga üksuste kopeerimisel!
        }

        // Clean up after the copying process
        $this->cleanupAfterCopy();
    }

    private function cleanupAfterCopy()
    {
        unset($this->tempSelectedItems);
        unset($this->tempItems);
        unset($this->objLockedDirs);
        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // DELETE

    public function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Rikutud tabel "folders" andmebaasis  või kausta "upload" failisüsteem!
            return;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal20->showDialogBox(); // Palun vali konkreetne/sed kaust/ad või fail/failid!
            return;
        }

        if ($this->arrSomeArray[0]["data-id"] == 1 && $this->arrSomeArray[0]["data-path"] == "") {
            $this->dlgModal26->showDialogBox(); // Peakataloogi pole võimalik kustutada!
            return;
        }

        if ($this->arrSomeArray[0]["data-activities-locked"] == 1) {
            $this->dlgModal25->showDialogBox(); // Vabandust, selle reserveeritud kausta või faili ei saa kustutada!
            return;
        }

        $this->initializeDeleteOperation();
    }

    // Helper functions

    private function initializeDeleteOperation()
    {
        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        // Prepare and send data to function fullRemove($dir)
        $this->prepareDeleteData();

        // Data validation and processing
        $this->processDeleteData($objFolders, $objFiles);

        // UI-related operations
        $this->deleteListDialog();
    }

    private function prepareDeleteData()
    {
        // Preparing and sending data to the function fullRemove($dir)

        $tempArr = [];

        foreach ($this->arrSomeArray as $arrSome) {
            $tempArr[] = $arrSome;
        }
        foreach ($tempArr as $temp) {
            if ($temp['data-path']) {
                $this->tempSelectedItems[] = $temp['data-path'];
            }
        }
    }

    private function processDeleteData($objFolders, $objFiles)
    {
        // Processing logic for deleting data

        $this->deleteDirectory($objFolders, $objFiles);
        $this->deleteFile($objFiles);

    }

    private function deleteDirectory($objFolders, $objFiles)
    {
        $dataFolders = [];
        $dataFiles = [];
        $tempIds = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "dir") {
                $dataFolders[] = $arrSome["data-id"];
            }
        }

        foreach ($dataFolders as $dataFolder) {
            $tempIds = array_merge($tempIds, $this->fullScanIds($dataFolder));
        }

        foreach ($objFiles as $objFile) {
            foreach ($tempIds as $tempId) {
                if ($objFile->getFolderId() == $tempId) {
                    $dataFiles[] = $objFile->getId();
                }
            }
        }

        // Here have to check whether the files are locked
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    if ($objFile->getLockedFile() == 1) {
                        $this->objLockedFiles++;
                    }
                }
            }
        }

        foreach ($objFolders as $objFolder) {
            foreach ($tempIds as $tempId) {
                if ($objFolder->getId() == $tempId) {
                    $this->tempItems[] = $objFolder->getPath();
                }
            }
            sort($this->tempItems);
        }
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    $this->tempItems[] = $objFile->getPath();
                }
            }
            sort($this->tempItems);
        }
    }

    private function deleteFile($objFiles)
    {
        $dataFiles = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "file") {
                $dataFiles[] = $arrSome["data-id"];
            }
        }

        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    if ($objFile->getId() == $dataFile) {
                        $this->tempItems[] = $objFile->getPath();
                    }

                    // Here have to check whether the files are locked
                    if ($objFile->getLockedFile() > 0) {
                        $this->objLockedFiles++;
                    }
                }
            }
        }
    }

    private function deleteListDialog()
    {
        // Update list dialog UI

        // Show folder and file names before deletion
        if (count($this->tempItems) !== 0) {
            $source = implode(', ', $this->tempItems);
            $this->lblDeletePath->Text = $source;
        }

        // Here have to check if some files have already been locked before.
        //If so, cancel and select unlocked files again...
        if ($this->objLockedFiles !== 0) {
            Application::executeJavaScript(sprintf("
                $('.deletion-warning-text').addClass('hidden');
                $('.deletion-info-text').addClass('hidden');
                $('.delete-error-text').removeClass('hidden');
                $('.delete-info-text').removeClass('hidden');
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.deletion-warning-text').removeClass('hidden');
                $('.deletion-info-text').removeClass('hidden');
                $('.delete-error-text').addClass('hidden');
                $('.delete-info-text').addClass('hidden');
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        }

        $this->dlgModal27->showDialogBox(); // Kustuta failid või kaustad
    }

    public function startDeletionProcess_Click(ActionParams $params)
    {
        $this->dlgModal27->hideDialogBox(); // Kustuta failid või kaustad

        foreach ($this->tempSelectedItems as $tempSelectedItem) {
            $this->fullRemoveItem($tempSelectedItem);
        }

        $this->handleDeletionResult();
    }

    // Helper functions

    private function fullRemoveItem($tempSelectedItem)
    {
        $itemPath = $this->objManager->RootPath . $tempSelectedItem;

        // Perform the removal logic
        $this->fullRemove($itemPath);
    }

    private function handleDeletionResult()
    {
        if ($this->intStoredChecks >= count($this->tempItems)) {
            $this->dlgModal28->showDialogBox(); // Valitud failid ja kaustad on edukalt kustutatud!
        } else {
            $this->dlgModal29->showDialogBox(); // Viga üksuste kustutamisel!
        }

        // Clean up after the deletion process
        $this->cleanupAfterDeletion();
    }

    private function cleanupAfterDeletion()
    {
        unset($this->tempSelectedItems);
        unset($this->objLockedFiles);
        unset($this->tempItems);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // MOVE

    public function btnMove_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        // Check for conditions preventing relocation
        if (!$this->validateMoveConditions()) {
            return;
        }

        // Prepare and send data to function fullMove($src, $dst)
        $this->prepareMoveData();

        // Data validation and processing
        $this->processMoveData($objFolders, $objFiles);

        // UI-related operations
        $this->updateMoveDestinationDialog();

        // Show the move dialog
        $this->showMoveDialog();
    }

    // Helper functions

    private function validateMoveConditions()
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Rikutud tabel "folders" andmebaasis  või kausta "upload" failisüsteem! ...
            return false;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal20->showDialogBox(); // Palun vali konkreetne/sed kaust/ad või fail/failid!
            return false;
        }

        if ($this->arrSomeArray[0]["data-id"] == 1 && $this->arrSomeArray[0]["data-path"] == "") {
            $this->dlgModal30->showDialogBox(); // Peakataloogi pole võimalik ümber tõsta!
            return false;
        }

        if ($this->arrSomeArray[0]["data-activities-locked"] == 1) {
            $this->dlgModal31->showDialogBox(); // Vabandust, selle reserveeritud kausta või faili ei saa ümber tõsta!
            return false;
        }

        return true;
    }

    private function prepareMoveData()
    {
        // Preparing and sending data to the function fullMove($src, $dst)

        $tempArr = [];

        foreach ($this->arrSomeArray as $arrSome) {
            $tempArr[] = $arrSome;
        }
        foreach ($tempArr as $temp) {
            if ($temp['data-path']) {
                $this->tempSelectedItems[] = $temp['data-path'];
            }
        }
    }

    private function processMoveData($objFolders, $objFiles)
    {
        // Processing logic for moving data

        $this->moveDirectory($objFolders, $objFiles);
        $this->moveFile($objFiles);
    }

    private function moveDirectory($objFolders, $objFiles)
    {
        // Perform directory moving logic

        $dataFolders = [];
        $dataFiles = [];
        $tempIds = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "dir") {
                $dataFolders[] = $arrSome["data-id"];
            }
        }

        foreach ($dataFolders as $dataFolder) {
            $tempIds = array_merge($tempIds, $this->fullScanIds($dataFolder));
        }

        foreach ($objFiles as $objFile) {
            foreach ($tempIds as $tempId) {
                if ($objFile->getFolderId() == $tempId) {
                    $dataFiles[] = $objFile->getId();
                }
            }
        }

        // Here have to check whether the files are locked
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    if ($objFile->getLockedFile() == 1) {
                        $this->objLockedFiles++;
                    }
                }
            }
        }

        foreach ($objFolders as $objFolder) {
            foreach ($tempIds as $tempId) {
                if ($objFolder->getId() == $tempId) {
                    $this->tempItems[] = $objFolder->getPath();
                }
            }
            sort($this->tempItems);
        }
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    $this->tempItems[] = $objFile->getPath();
                }
            }
            sort($this->tempItems);
        }
    }

    private function moveFile($objFiles)
    {
        // Perform file moving logic

        $dataFiles = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "file") {
                $dataFiles[] = $arrSome["data-id"];
            }
        }

        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    if ($objFile->getId() == $dataFile) {
                        $this->tempItems[] = $objFile->getPath();
                    }

                    // Here have to check whether the files are locked
                    if ($objFile->getLockedFile() == 1) {
                        $this->objLockedFiles++;
                    }
                }
            }
        }
    }

    private function updateMoveDestinationDialog()
    {
        // Update destination dialog UI

        $objPaths = $this->scanForSelect();

        foreach ($this->tempItems as $tempItem) {
            if (is_dir($this->objManager->RootPath . $tempItem)) {
                $this->objLockedDirs[] = $tempItem;
            }
        }

        if ($objPaths) foreach ($objPaths as $objPath) {
            if ($objPath['activities_locked'] == 1) {
                array_push($this->objLockedDirs, $objPath["path"]);
            }
        }

        if ($objPaths) foreach ($objPaths as $objPath) {
            if (in_array($objPath["path"], $this->objLockedDirs)) {
                $mark = true;
            } else {
                $mark = false;
            }
            $this->dlgMovingDestination->AddItem($this->printDepth($objPath['name'], $objPath['parent_id'], $objPath['depth']), $objPath, null, $mark);
        }
    }

    private function showMoveDialog()
    {
        // Show the move dialog

        // Show folder and file names before moving
        if (count($this->tempItems) !== 0) {
            $source = implode(', ', $this->tempItems);
            $this->lblMovingCourcePath->Text = $source;
        }

        // Here have to check if some files have already been locked before.
        //If so, cancel and select unlocked files again...
        if ($this->objLockedFiles !== 0) {
            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-default').addClass('btn-danger');
                $('.move-error-text').removeClass('hidden');
                $('.move-info-text').removeClass('hidden');
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-danger').addClass('btn-default');
                $('.move-error-text').addClass('hidden');
                $('.move-info-text').addClass('hidden');
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        }

        if ($this->dlgMovingDestination->SelectedValue == null) {
            Application::executeJavaScript(sprintf("
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        }

        $this->dlgModal32->showDialogBox(); // Tõsta failid või kaustad ümber
    }

    public function startMovingProcess_Click(ActionParams $params)
    {
        $objPath = $this->dlgMovingDestination->SelectedValue;

        if (!$objPath) {
            $this->handleMovingError();
            return;
        }

        $this->dlgModal32->hideDialogBox(); // Tõsta failid või kaustad ümber

        if ($this->dlgMovingDestination->SelectedValue !== null) {
            foreach ($this->tempSelectedItems as $selectedItem) {
                $this->fullMoveItem($selectedItem, $objPath);
            }
        }

        $this->handleMovingResult();
    }

    // Helper functions

    private function fullMoveItem($selectedItem, $objPath)
    {
        $sourcePath = $this->objManager->RootPath . $selectedItem;
        $destinationPath = $this->objManager->RootPath . $objPath['path'] . "/" . basename($selectedItem);



        // Perform the move logic
        $this->fullMove($sourcePath, $destinationPath);
    }

    private function handleMovingError()
    {
        if ($this->dlgMovingDestination->SelectedValue == null) {
            $this->dlgMovingDestination->removeAllItems();
            $this->dlgMovingDestination->AddItem(t('- Select One -'), null);

            Application::executeJavaScript(sprintf("
               $('.modal-header').removeClass('btn-default').addClass('btn-danger');
               $('.destination-moving-error').removeClass('hidden');
               $('.moving-source-title').addClass('hidden');
               $('.moving-source-path').addClass('hidden');
               $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        }
    }

    private function handleMovingResult()
    {
        if ($this->intStoredChecks >= count($this->tempItems)) {
            $this->dlgModal33->showDialogBox(); // Valitud failid ja kaustad on edukalt ümber tõstetud!
        } else {
            $this->dlgModal34->showDialogBox(); // Viga üksuste ümber tõstmisel!
        }

        // Clean up after the moving process
        $this->cleanupAfterMoving();
    }

    private function cleanupAfterMoving()
    {
        unset($this->tempSelectedItems);
        unset($this->objLockedFiles);
        unset($this->tempItems);
        unset($this->objLockedDirs);
        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function dataClearing_Click()
    {
        // Clearing form elements
        $this->txtAddFolder->Text = '';
        $this->dlgCopyingDestination->SelectedValue = '';
        $this->dlgMovingDestination->SelectedValue = '';
        $this->clearDropdownOptions($this->dlgCopyingDestination);
        $this->clearDropdownOptions($this->dlgMovingDestination);

        // Unset variables
        $this->clearVariables();

        // Clearing session storage
        Application::executeJavaScript(sprintf("sessionStorage.clear();"));
    }

    // Helper functions

    private function clearDropdownOptions($dropdown)
    {
        $dropdown->removeAllItems();
        $dropdown->AddItem(t('- Select One -'), null);
    }

    private function clearVariables()
    {
        unset($this->tempSelectedItems);
        unset($this->objLockedFiles);
        unset($this->tempItems);
        unset($this->intDataId);
        unset($this->strDataName);
        unset($this->strDataPath);
        unset($this->intDataLocked);
        unset($this->objLockedDirs);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Check the synchronicity of folders and database.
     * If they don't match, Filemanager is broken.
     * The reason for this can be either the "folders" table is corrupted or the file system of the "upload" folder is corrupted or an empty folder.
     * In this case, help should be asked from the developer or webmaster.
     *
     * Here is one way to immediately kontrol with the code below (with example):
     *
     * $path = $this->objManager->RootPath;
     * print "<pre>";
     * print "<br>DATASCAN:<br>";
     * print_r($this->dataScan());
     * print "<br>SCAN:<br>";
     * print_r($this->scan($path));
     * print "</pre>";
     *
     */

    protected function dataScan()
    {
        $folders = Folders::loadAll();

        // Use array_map to extract paths.
        $arr = array_map(function ($folder) {
            return $folder->getPath();
        }, $folders);

        // Remove the first element from the array
        array_shift($arr);
        // Sort the paths.
        sort($arr);

        return $arr;
    }

    protected function scan($path)
    {
        $folders = [];

        if (file_exists($path)) {
            foreach (scandir($path) as $f) {
                if ($f[0] == '.') {
                    continue;
                }

                $fullPath = $path . DIRECTORY_SEPARATOR . $f;

                if (is_dir($fullPath)) {
                    $folders[] = $this->objManager->getRelativePath($fullPath);
                    array_push($folders, ...$this->scan($fullPath));
                }
            }
        }

        sort($folders);

        return $folders;
    }

    /**
     * Recursively scan for all descendant folder IDs given a parent folder ID.
     *
     * @param int $parentId
     * @return array
     */
    protected function fullScanIds($parentId)
    {
        $descendantIds = [];

        $objFolders = Folders::loadAll();

        foreach ($objFolders as $objFolder) {
            if ($objFolder->ParentId == $parentId) {
                array_push($descendantIds, ...$this->fullScanIds($objFolder->Id));
            }
        }

        array_push($descendantIds, $parentId);

        return $descendantIds;
    }

    /**
     * Scan folders for select options.
     *
     * @return array
     */
    protected function scanForSelect()
    {
        $folders = Folders::loadAll();
        $folderData = [];
        $sortedNames = [];

        foreach ($folders as $folder) {
            $folderData[] = [
                'id' => $folder->getId(),
                'parent_id' => $folder->getParentId(),
                'name' => $folder->getName(),
                'path' => $folder->getPath(),
                'depth' => substr_count($folder->getPath(), '/'),
                'activities_locked' => $folder->getActivitiesLocked(),
            ];
        }

        foreach ($folderData as $key => $val) {
            $sortedNames[$key] = strtolower($val['path']);
        }

        array_multisort($sortedNames, SORT_ASC, $folderData);

        return $folderData;
    }

    protected function checkString($str) {
        // Remove leading and trailing spaces
        $str = trim($str);

        // Split the string based on the slashes
        $parts = explode('/', $str);

        // We check if there are more parts after the first element
        return count($parts) <= 2 && empty($parts[1]);
    }

    /**
     * Print a string with indentation based on depth.
     *
     * @param string $name   The name to print.
     * @param mixed  $parent The parent identifier.
     * @param int    $depth  The depth level.
     *
     * @return string The formatted string.
     */
    protected function printDepth($name, $parent, $depth)
    {
        $spacer = str_repeat('&nbsp;', 5); // Adjust the number as needed for your indentation.

        if ($parent !== null) {
            $strHtml = str_repeat(html_entity_decode($spacer), $depth) . ' ' . t($name);
        } else {
            $strHtml = t($name);
        }

        return $strHtml;
    }

    /**
     * Get width of an image
     * @param string $path
     * @return mixed|string
     */
    public static function getImageWidth($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions())) {
            $width = (isset($ImageSize[0]) ? $ImageSize[0] : '0');
            return $width;
        }
    }

    /**
     * Get height of an image
     * @param string $path
     * @return mixed|string
     */
    public static function getImageHeight($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions())) {
            $height = (isset($ImageSize[1]) ? $ImageSize[1] : '0');
            return $height;
        }
    }

    /**
     * Get image files extensions
     * @return array
     */
    public static function getImageExtensions()
    {
        return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function fullMove($src, $dst)
    {
        $this->fullCopy($src, $dst);
        $this->fullRemove($src);
    }

    protected function fullCopy($src, $dst)
    {
        $objId = $this->getIdFromParent($dst);

        if ($objId) {
            $objFolder = Folders::loadById($objId);
            if ($objFolder->getLockedFile() !== 1) {
                $objFolder->setMtime(filemtime(dirname($dst)));
                $objFolder->setLockedFile(1);
                $objFolder->save();
            }
        }

        $dirname = $this->objManager->removeFileName($dst);
        $name = pathinfo($dst, PATHINFO_FILENAME);
        $ext = pathinfo($dst, PATHINFO_EXTENSION);

        if (is_dir($src)) {
            if (file_exists($dirname . '/' . basename($name))) {
                $inc = 1;
                while (file_exists($dirname . '/' . $name . '-' . $inc)) $inc++;
                $dst = $dirname . '/' . $name . '-' . $inc;
            }

            Folder::makeDirectory($dst, 0777);

            $objFolder = new Folders();
            $objFolder->setParentId($objId);
            $objFolder->setPath($this->objManager->getRelativePath(realpath($dst)));
            $objFolder->setName(basename($dst));
            $objFolder->setType("dir");
            $objFolder->setMtime(filemtime($dst));
            $objFolder->save();

            foreach ($this->tempFolders as $tempFolder) {
                Folder::makeDirectory($this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($dst),0777);
            }

            $files = array_diff(scandir($src), array('..', '.'));
            foreach($files as $file) {
                $this->fullCopy("$src" . "/" . "$file", "$dst" . "/". "$file");
            }

        } else if (file_exists($src)) {
            if (file_exists($dirname . '/' . basename($name) . '.' . $ext)) {
                $inc = 1;
                while (file_exists($dirname . '/' . $name . '-' . $inc . '.' . $ext)) $inc++;
                $dst = $dirname . '/' . $name . '-' . $inc . '.' . $ext;
            }

            copy($src,$dst);

            if (in_array(strtolower($ext), $this->arrAllowed)) {
                foreach ($this->tempFolders as $tempFolder) {
                    copy($this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($src),$this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($dst));
                }
            }

            $objFiles = new Files();
            $objFiles->setFolderId($objId);
            $objFiles->setName(basename($dst));
            $objFiles->setType("file");
            $objFiles->setPath($this->objManager->getRelativePath(realpath($dst)));
            $objFiles->setExtension($this->objManager->getExtension($dst));
            $objFiles->setMimeType($this->objManager->getMimeType($dst));
            $objFiles->setSize(filesize($dst));
            $objFiles->setMtime(filemtime($dst));
            $objFiles->setDimensions($this->objManager->getDimensions($dst));
            $objFiles->save();
        }

        if (file_exists($dst)) {
            $this->intStoredChecks++;
        }

        $this->objManager->refresh();
        clearstatcache();
    }

    /**
     * Get the ID of the folder based on its parent path.
     *
     * @param string $path The path of the folder.
     * @return int|null The ID of the folder or null if not found.
     */
    protected function getIdFromParent($path)
    {
        $objFolders = Folders::loadAll();
        $objPath = $this->objManager->getRelativePath(realpath(dirname($path)));

        foreach ($objFolders as $objFolder) {
            if ($objPath == $objFolder->getPath()) {
                return $objFolder->getId();
            }
        }

        // Handle the case where no matching folder is found.
        return ($objPath == "") ? 1 : null;
    }

    protected function fullRemove($dir)
    {
        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('..', '.'));

            foreach ($files as $file) {
                $this->fullRemove($dir . "/" . $file);
            }

            foreach ($objFolders as $objFolder) {
                if ($objFolder->getPath() == $this->objManager->getRelativePath($dir)) {
                    if ($objFolder->getId()) {
                        $obj = Folders::loadById($objFolder->getId());
                        $obj->delete();
                        $this->intStoredChecks++;
                    }
                }
            }

            if (file_exists($dir)) {
                rmdir($dir);

                foreach ($this->tempFolders as $tempFolder) {
                    $tempPath = $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($dir);
                    if (is_dir($tempPath)) {
                        rmdir($tempPath);
                    }
                }
            }
        } elseif (file_exists($dir)) {
            foreach ($objFiles as $objFile) {
                if ($objFile->getPath() == $this->objManager->getRelativePath($dir)) {
                    if ($objFile->getId()) {
                        $obj = Files::loadById($objFile->getId());
                        $obj->delete();
                        $this->intStoredChecks++;
                    }
                }
            }

            unlink($dir);

            foreach ($this->tempFolders as $tempFolder) {
                $tempPath = $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($dir);
                if (is_file($tempPath)) {
                    unlink($tempPath);
                }
            }
        }

        $dirname = dirname($dir);
        if (is_dir($dirname)) {
            $folders = glob($dirname . '/*', GLOB_ONLYDIR);
            $files = array_filter(glob($dirname . '/*'), 'is_file');

            foreach ($objFolders as $objFolder) {
                if ($objFolder->getPath() == $this->objManager->getRelativePath($dirname)) {
                    if (count($folders) == 0 && count($files) == 0) {
                        $obj = Folders::loadById($objFolder->getId());
                        if ($obj->getLockedFile() == 1) {
                            $obj->setMtime(filemtime($dirname));
                            $obj->setLockedFile(0);
                            $obj->save();
                        }
                    }
                }
            }
        }

        $this->objManager->refresh();
    }

    public function dlgDestination_Change(ActionParams $params)
    {
        if (is_array($this->dlgCopyingDestination->SelectedValue) || is_array($this->dlgMovingDestination->SelectedValue)) {
            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-danger').addClass('btn-default');
                $('.destination-error').addClass('hidden');
                $('.destination-moving-error').addClass('hidden');
                $('.source-title').removeClass('hidden');
                $('.moving-source-title').removeClass('hidden');
                $('.source-path').removeClass('hidden');
                $('.moving-source-path').removeClass('hidden');
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-default').addClass('btn-danger');
                $('.destination-error').removeClass('hidden');
                $('.destination-moving-error').removeClass('hidden');
                $('.source-title').addClass('hidden');
                $('.moving-source-title').addClass('hidden');
                $('.source-path').addClass('hidden');
                $('.moving-source-path').addClass('hidden');
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        }

        if ($this->objLockedFiles !== 0) {

            Application::executeJavaScript(sprintf("
               $('.modal-header').removeClass('btn-default').addClass('btn-danger');
               //$('.destination-moving-error').removeClass('hidden');
               $('.moving-source-title').addClass('hidden');
               $('.moving-source-path').addClass('hidden');
               $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnImageListView_Click(ActionParams $params)
    {
        $this->btnImageListView->addCssClass("active");
        $this->btnListView->removeCssClassesByPrefix("active");
        $this->btnBoxView->removeCssClassesByPrefix("active");

        $this->objManager->IsImageListView = true;
        $this->objManager->IsListView = false;
        $this->objManager->IsBoxView = false;
        $this->objManager->refresh();
    }

    public function btnListView_Click(ActionParams $params)
    {
        $this->btnListView->addCssClass("active");
        $this->btnImageListView->removeCssClassesByPrefix("active");
        $this->btnBoxView->removeCssClassesByPrefix("active");

        $this->objManager->IsListView = true;
        $this->objManager->IsImageListView = false;
        $this->objManager->IsBoxView = false;
        $this->objManager->refresh();
    }

    public function btnBoxView_Click(ActionParams $params)
    {
        $this->btnBoxView->addCssClass("active");
        $this->btnImageListView->removeCssClassesByPrefix("active");
        $this->btnListView->removeCssClassesByPrefix("active");

        $this->objManager->IsBoxView = true;
        $this->objManager->IsImageListView = false;
        $this->objManager->IsListView = false;
        $this->objManager->refresh();
    }


    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnInsert_Click(ActionParams $params)
    {
        if ($this->arrSomeArray[0]["data-activities-locked"] == 1) {
            $this->dlgModal35->showDialogBox(); // Sorry, be cannot insert into a reserved file! ...
        } else {
            foreach ($this->arrSomeArray as $arrSome) {

                // Here, the "files" table needs to be updated to indicate that the selected image(s) are now locked.
                // If this is not done, the file manager will not provide accurate information about whether
                // the files are free or not. This is to prevent accidentally deleting files that are in use by others.
                $objFiles = Files::loadById($arrSome["data-id"]);
                $objFiles->setLockedFile($objFiles->getLockedFile() + 1);
                $objFiles->save();


                // First, you need to create your own table with your chosen name and define the necessary columns.
                // However, here you must definitely add a column named "file_id", where you will pull the "id" value
                // from the "files" table.
                //
                //This is necessary so that when you need to delete from your table, you can release the lock in
                // the "files" table based on the "file_id". This ensures that FileHandler reflects the correct
                // information to other users.

                $objSliders = Sliders::querySingle(QQ::all(),
                    [
                        QQ::maximum(QQN::sliders()->Order, 'max')
                    ]
                );
                $objMaxOrder = $objSliders->getVirtualAttribute('max');

                $objSlider = new Sliders();
                $objSlider->setGroupId($_SESSION['finder_id']);
                $objSlider->setFileId($arrSome["data-id"]);
                $objSlider->setOrder($objMaxOrder + 1);
                $objSlider->setPath($arrSome["data-path"]);
                $objSlider->setExtension($arrSome["data-extension"]);
                $objSlider->setStatus(2);
                $objSlider->setPostDate(Q\QDateTime::Now());

                if ($arrSome["data-extension"] !== "svg") {
                    $objSlider->setDimensions($arrSome["data-dimensions"]);
                    $objSlider->setWidth($this->getImageWidth($this->objManager->TempPath . "/_files/large" . $arrSome["data-path"]));
                    $objSlider->setHeight($this->getImageHeight($this->objManager->TempPath . "/_files/large" . $arrSome["data-path"]));
                }

                $objSlider->save();
            }

            $objSlidersSettings = SlidersSettings::loadById($_SESSION['finder_id']);
            $objSlidersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
            $objSlidersSettings->save();

            Application::redirect('slider_edit.php' . '?id=' . $_SESSION['finder_id']);
            unset($_SESSION['finder_id']);
        }
    }

    public function btnCancel_Click(ActionParams $params)
    {
        Application::redirect('slider_edit.php' . '?id=' . $_SESSION['finder_id']);
        unset($_SESSION['finder_id']);
    }
}

SampleForm::run('SampleForm');
