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
    protected $dlgModal8;
    protected $dlgModal9;

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
    protected $dlgToastr15;

    protected $lblInfo;
    protected $btnAddMember;
    protected $txtNewFullName;
    protected $btnMemberSave;
    protected $btnMemberCancel;
    protected $lblTitleSlug;
    protected $txtTitleSlug;

    protected $dlgSorter;
    protected $objMediaFinder;
    protected $txtFullName;
    protected $lblFullName;
    protected $txtPosition;
    protected $lblPosition;
    protected $txtAreasResponsibility;
    protected $lblAreasResponsibility;
    protected $txtInterests;
    protected $lblInterests;
    protected $txtDescription;
    protected $lblDescription;
    protected $txtTelephone;
    protected $lblTelephone;
    protected $txtSMS;
    protected $lblSMS;
    protected $txtFax;
    protected $lblFax;
    protected $txtAddress;
    protected $lblAddress;
    protected $txtEmail;
    protected $lblEmail;
    protected $txtWebsite;
    protected $lblWebsite;
    protected $lstBoardStatus;
    protected $lblBoardStatus;

    protected $calBoardPostUpdateDate;
    protected $btnUpdate;
    protected $btnCloseWindow;
    protected $btnBack;

    protected $lblGroupTitle;
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
    protected $lblImageUpload;
    protected $lstImageUpload;

    protected $intId;
    protected $intGroup;
    protected $intLoggedUserId;
    protected $intClick;
    protected $objBoard;
    protected $objMenu;
    protected $objBoardsSettings;
    protected $objActiveInputs;

    protected $strRootPath = APP_UPLOADS_DIR;
    protected $strTempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
    protected $strDateTimeFormat = 'd.m.Y H:i';
    protected $objFile;

    protected function formCreate()
    {
        parent::formCreate();

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->intGroup = Application::instance()->context()->queryStringItem('group');
        if (!empty($this->intId)) {
            $this->objBoard = Board::loadByIdFromBoardId($this->intId);
            $this->objBoardsSettings = BoardsSettings::load($this->intId);
            $this->objMenu = Menu::load($this->intGroup);
        } else {
            // does nothing
        }

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
        $this->createInputManager();
        $this->createToastr();
        $this->createModals();
        $this->refreshDisplay();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Resets the visibility of various input elements by adding a 'hidden' class using JavaScript.
     *
     * @return void
     */
    protected function resettingInputs()
    {
        Application::executeJavaScript("
           $('.js-fullname').addClass('hidden');
           $('.js-position').addClass('hidden');
           $('.js-areasresponsibility').addClass('hidden');
           $('.js-interests').addClass('hidden');
           $('.js-description').addClass('hidden');
           $('.js-telephone').addClass('hidden');
           $('.js-sms').addClass('hidden');
           $('.js-fax').addClass('hidden');
           $('.js-address').addClass('hidden');
           $('.js-email').addClass('hidden');
           $('.js-website').addClass('hidden');
       ");
    }

    /**
     * Creates and initializes the input controls for the form, including labels, text boxes, radio buttons,
     * and other components with their respective properties and styles. It also handles the visibility of certain
     * elements based on the board's settings.
     *
     * @return void
     */
    protected function createInputs()
    {
        $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
        $this->lblGroupTitle->Text = $this->objBoardsSettings->getName();
        $this->lblGroupTitle->setCssStyle('font-weight', 600);

        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please create the first board member!');
        $this->lblInfo->setCssStyle('margin-bottom', 0);

        if ($this->objBoardsSettings->getBoardLocked() === 1) {
            $this->lblInfo->Display = false;
        } else {
            $this->lblInfo->Display = true;
        }

        $this->txtNewFullName = new Bs\TextBox($this);
        $this->txtNewFullName->Placeholder = t('New member\'s full name');
        $this->txtNewFullName->setHtmlAttribute('autocomplete', 'off');
        $this->txtNewFullName->setCssStyle('float', 'left');
        $this->txtNewFullName->Width = '45%';
        $this->txtNewFullName->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtNewFullName->Display = false;

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View: ');
        $this->lblTitleSlug->setCssStyle('font-weight', 'bold');

        if ($this->objBoardsSettings->getTitleSlug()) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
            $this->txtTitleSlug->setCssStyle('text-align', 'left;');
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objBoardsSettings->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
        } else {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->objMediaFinder = new Q\Plugin\MediaFinder($this);
        $this->objMediaFinder->TempUrl = APP_UPLOADS_TEMP_URL . "/_files/thumbnail";
        $this->objMediaFinder->PopupUrl = QCUBED_FILEMANAGER_URL . "/examples/finder.php";
        $this->objMediaFinder->EmptyImageAlt = t("Choose a picture");
        $this->objMediaFinder->SelectedImageAlt = t("Selected picture");
        $this->objMediaFinder->EmptyImagePath = QCUBED_NESTEDSORTABLE_ASSETS_URL . '/images/empty-member-icon.png';
        $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageSave(), new Q\Action\Ajax( 'imageSave_Push'));
        $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageDelete(), new Q\Action\Ajax('imageDelete_Push'));

        ///////////////////////////////////////////////////////////////////////////////////////////

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objBoardsSettings->PostDate ? $this->objBoardsSettings->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostDate->setCssStyle('font-weight', 'normal');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objBoardsSettings->PostUpdateDate ? $this->objBoardsSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->setCssStyle('font-weight', 'bold');

        $this->txtAuthor  = new Bs\Label($this);
        $this->txtAuthor->Text = $this->objBoardsSettings->Author;
        $this->txtAuthor->setCssStyle('font-weight', 'normal');

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->setCssStyle('font-weight', 'bold');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
        $this->lstStatus->SelectedValue = $this->objBoardsSettings->Status;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->Enabled = true;
        $this->lstStatus->addAction(new Q\Event\Change(), new Ajax('lstStatus_Change'));

        $this->lblImageUpload = new Q\Plugin\Control\Label($this);
        $this->lblImageUpload->Text = t('Image upload');
        $this->lblImageUpload->setCssStyle('margin-bottom', '-10px');
        $this->lblImageUpload->setCssStyle('font-weight', 'bold');

        $this->lstImageUpload = new Q\Plugin\Control\RadioList($this);
        $this->lstImageUpload->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstImageUpload->ButtonGroupClass = 'radio radio-orange';
        $this->lstImageUpload->SelectedValue = $this->objBoardsSettings->AllowedUploading;
        $this->lstImageUpload->setCssStyle('margin-top', '-10px');
        $this->lstImageUpload->addAction(new Q\Event\Change(), new Q\Action\Ajax('lstImageUpload_Change'));

        if ($this->objBoardsSettings->AllowedUploading === 0) {
            Application::executeJavaScript("
                $('.board-image-wrapper').addClass('hidden');
            ");
        }
    }

    /**
     * Creates and initializes various buttons for the UI component with their respective styles, text, and actions.
     *
     * @return void
     */
    protected function createButtons()
    {
        $this->btnAddMember = new Bs\Button($this);
        $this->btnAddMember->Text = t(' Add member');
        $this->btnAddMember->CssClass = 'btn btn-orange';
        $this->btnAddMember->setCssStyle('float', 'left');
        $this->btnAddMember->setCssStyle('margin-right', '10px');
        $this->btnAddMember->CausesValidation = false;
        $this->btnAddMember->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddMember_Click'));

        $this->btnMemberSave = new Bs\Button($this);
        $this->btnMemberSave->Text = t('Save');
        $this->btnMemberSave->CssClass = 'btn btn-orange';
        $this->btnMemberSave->setCssStyle('float', 'left');
        $this->btnMemberSave->setCssStyle('margin-left', '10px');
        $this->btnMemberSave->setCssStyle('margin-right', '10px');
        $this->btnMemberSave->Display = false;
        $this->btnMemberSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMemberSave_Click'));

        $this->btnMemberCancel = new Bs\Button($this);
        $this->btnMemberCancel->Text = t('Cancel');
        $this->btnMemberCancel->CssClass = 'btn btn-default';
        $this->btnMemberCancel->setCssStyle('float', 'left');
        $this->btnMemberCancel->CausesValidation = false;
        $this->btnMemberCancel->Display = false;
        $this->btnMemberCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMemberCancel_Click'));

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

        //////////////////////////////////////////////////////////////////////////////////////////

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->setCssStyle('margin-left', '10px');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnBack_Click'));
    }

    /**
     * Initializes a sortable component with configurations for rendering, data binding, and event handling.
     *
     * @return void
     */
    protected function createSorter()
    {
        $this->dlgSorter = new Q\Plugin\Control\SlideWrapper($this);
        $this->dlgSorter->createNodeParams([$this, 'Sorter_Draw']);
        $this->dlgSorter->createRenderButtons([$this, 'Buttons_Draw']);
        $this->dlgSorter->setDataBinder('Sorter_Bind');
        $this->dlgSorter->addCssClass('sortable');
        $this->dlgSorter->DateTimeFormat = 'DD.MM.YYYY hhhh:mm:ss';
        $this->dlgSorter->TempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
        $this->dlgSorter->RootUrl = APP_UPLOADS_URL;
        $this->dlgSorter->EmptyImagePath = QCUBED_NESTEDSORTABLE_ASSETS_URL . '/images/empty-member-icon.png';
        $this->dlgSorter->Placeholder = 'placeholder';
        $this->dlgSorter->Handle = '.reorder';
        $this->dlgSorter->Items = 'div.image-blocks';
        $this->dlgSorter->addAction(new Q\Jqui\Event\SortableStop(), new Q\Action\Ajax('sortable_stop'));
        $this->dlgSorter->watch(QQN::Board());
    }

    /**
     * Binds data to the sorter dialog's data source for the specified board.
     *
     * @return void
     */
    protected function Sorter_Bind()
    {
        $this->dlgSorter->DataSource = Board::QueryArray(
            QQ::Equal(QQN::Board()->BoardId, $this->intId),
            QQ::orderBy(QQN::Board()->Order)
        );
    }

    /**
     * Returns sorting information for the given board object.
     *
     * @param Board $objBoard The board object for which sorting information is being retrieved.
     * @return array An associative array containing sorting details of the board.
     */
    public function Sorter_Draw(Board $objBoard)
    {
        if ($objBoard->PictureId) {
            $objFile = Files::load($objBoard->PictureId);
            $a['path'] = $objFile->Path;
        }

        $a['id'] = $objBoard->Id;
        $a['group_id'] = $objBoard->BoardId;
        $a['order'] = $objBoard->Order;
        $a['title'] = $objBoard->Fullname;
        $a['post_date'] = $objBoard->PostDate;
        $a['post_update_date'] = $objBoard->PostUpdateDate;
        $a['status'] = $objBoard->Status;
        return $a;
    }

    /**
     * Draws edit and delete buttons for the given board object.
     *
     * @param Board $objBoard The board object for which the buttons are being created.
     * @return string Rendered HTML for the edit and delete buttons.
     */
    public function Buttons_Draw(Board $objBoard)
    {
        $strEditId = 'btnEdit' . $objBoard->Id;

        if (!$btnEdit = $this->getControl($strEditId)) {
            $btnEdit = new Bs\Button($this->dlgSorter, $strEditId);
            $btnEdit->Glyph = 'glyphicon glyphicon-pencil';
            $btnEdit->Tip = true;
            $btnEdit->ToolTip = t('Edit');
            $btnEdit->CssClass = 'btn btn-icon btn-xs edit';
            $btnEdit->ActionParameter = $objBoard->Id;
            $btnEdit->UseWrapper = false;
            $btnEdit->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnEdit_Click'));
        }

        $strDeleteId = 'btnDelete' . $objBoard->Id;

        if (!$btnDelete = $this->getControl($strDeleteId)) {
            $btnDelete = new Bs\Button($this->dlgSorter, $strDeleteId);
            $btnDelete->Glyph = 'glyphicon glyphicon-trash';
            $btnDelete->Tip = true;
            $btnDelete->ToolTip = t('Delete');
            $btnDelete->CssClass = 'btn btn-icon btn-xs delete';
            $btnDelete->ActionParameter = $objBoard->Id;
            $btnDelete->UseWrapper = false;
            $btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));
        }

        return $btnEdit->render(false) . $btnDelete->render(false);
    }

    /**
     * Initializes and configures the input manager by querying the active board options,
     * creating the necessary input fields and labels, and setting up their properties.
     * The inputs are sorted based on an 'Order' field and certain elements are displayed
     * based on their activity status.
     *
     * @return void
     */
    protected function createInputManager()
    {
        $this->objActiveInputs = BoardOptions::QueryArray(
            QQ::Equal(QQN::BoardOptions()->SettingsId, $this->intId)
        );

        // Sort the inputs according to the order specified in the 'Order' field
        usort($this->objActiveInputs, function($a, $b) {
            return $a->Order - $b->Order;
        });

        $this->txtFullName = new Bs\TextBox($this);
        $this->txtFullName->addCssClass('js-fullname');
        $this->txtFullName->setHtmlAttribute('autocomplete', 'off');
        $this->txtFullName->setHtmlAttribute('required', 'required');
        $this->txtFullName->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtFullName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtFullName->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtFullName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblFullName  = new Q\Plugin\Control\Label($this);
        $this->lblFullName->Text = t('Full name');
        $this->lblFullName->addCssClass('col-md-3 js-fullname');
        $this->lblFullName->setCssStyle('font-weight', 'normal');
        $this->lblFullName->Required = true;

        $this->txtPosition = new Bs\TextBox($this);
        $this->txtPosition->addCssClass('js-position');
        $this->txtPosition->setHtmlAttribute('autocomplete', 'off');
        $this->txtPosition->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtPosition->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtPosition->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtPosition->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblPosition = new Q\Plugin\Control\Label($this);
        $this->lblPosition->Text = t('Position');
        $this->lblPosition->addCssClass('col-md-3 js-position');
        $this->lblPosition->setCssStyle('font-weight', 'normal');

        $this->txtAreasResponsibility = new Bs\TextBox($this);
        $this->txtAreasResponsibility->addCssClass('js-areasresponsibility');
        $this->txtAreasResponsibility->setHtmlAttribute('autocomplete', 'off');
        $this->txtAreasResponsibility->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtAreasResponsibility->Rows = 2;
        $this->txtAreasResponsibility->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtAreasResponsibility->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtAreasResponsibility->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtAreasResponsibility->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblAreasResponsibility = new Q\Plugin\Control\Label($this);
        $this->lblAreasResponsibility->Text = t('Areas responsibility');
        $this->lblAreasResponsibility->addCssClass('col-md-3 js-areasresponsibility');
        $this->lblAreasResponsibility->setCssStyle('font-weight', 'normal');

        $this->txtInterests = new Bs\TextBox($this);
        $this->txtInterests->addCssClass('js-interests');
        $this->txtInterests->setHtmlAttribute('autocomplete', 'off');
        $this->txtInterests->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtInterests->Rows = 2;
        $this->txtInterests->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtInterests->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtInterests->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtInterests->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblInterests = new Q\Plugin\Control\Label($this);
        $this->lblInterests->Text = t('Interests and hobbies');
        $this->lblInterests->addCssClass('col-md-3 js-interests');
        $this->lblInterests->setCssStyle('font-weight', 'normal');

        $this->txtDescription = new Bs\TextBox($this);
        $this->txtDescription->addCssClass('js-description');
        $this->txtDescription->setHtmlAttribute('autocomplete', 'off');
        $this->txtDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtDescription->Rows = 2;
        $this->txtDescription->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtDescription->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtDescription->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtDescription->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblDescription = new Q\Plugin\Control\Label($this);
        $this->lblDescription->Text = t('Description');
        $this->lblDescription->addCssClass('col-md-3 js-description');
        $this->lblDescription->setCssStyle('font-weight', 'normal');

        $this->txtTelephone = new Bs\TextBox($this);
        $this->txtTelephone->addCssClass('js-telephone');
        $this->txtTelephone->setHtmlAttribute('autocomplete', 'off');
        $this->txtTelephone->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtTelephone->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTelephone->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtTelephone->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblTelephone = new Q\Plugin\Control\Label($this);
        $this->lblTelephone->Text = t('Telephone');
        $this->lblTelephone->addCssClass('col-md-3 js-telephone');
        $this->lblTelephone->setCssStyle('font-weight', 'normal');

        $this->txtSMS = new Bs\TextBox($this);
        $this->txtSMS->addCssClass('js-telephone');
        $this->txtSMS->setHtmlAttribute('autocomplete', 'off');
        $this->txtSMS->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtSMS->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtSMS->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtSMS->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblSMS = new Q\Plugin\Control\Label($this);
        $this->lblSMS->Text = t('SMS');
        $this->lblSMS->addCssClass('col-md-3 js-telephone');
        $this->lblSMS->setCssStyle('font-weight', 'normal');

        $this->txtFax = new Bs\TextBox($this);
        $this->txtFax->addCssClass('js-telephone');
        $this->txtFax->setHtmlAttribute('autocomplete', 'off');
        $this->txtFax->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtFax->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtFax->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtFax->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblFax = new Q\Plugin\Control\Label($this);
        $this->lblFax->Text = t('Fax');
        $this->lblFax->addCssClass('col-md-3 js-telephone');
        $this->lblFax->setCssStyle('font-weight', 'normal');

        $this->txtAddress = new Bs\TextBox($this);
        $this->txtAddress->addCssClass('js-address');
        $this->txtAddress->setHtmlAttribute('autocomplete', 'off');
        $this->txtAddress->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtAddress->Rows = 2;
        $this->txtAddress->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtAddress->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtAddress->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtAddress->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblAddress = new Q\Plugin\Control\Label($this);
        $this->lblAddress->Text = t('Address');
        $this->lblAddress->addCssClass('col-md-3 js-address');
        $this->lblAddress->setCssStyle('font-weight', 'normal');

        $this->txtEmail = new Bs\TextBox($this);
        $this->txtEmail->addCssClass('js-email');
        $this->txtEmail->setHtmlAttribute('autocomplete', 'off');
        $this->txtEmail->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtEmail->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtEmail->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtEmail->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblEmail = new Q\Plugin\Control\Label($this);
        $this->lblEmail->Text = t('Email');
        $this->lblEmail->addCssClass('col-md-3 js-email');
        $this->lblEmail->setCssStyle('font-weight', 'normal');

        $this->txtWebsite = new Bs\TextBox($this);
        $this->txtWebsite->addCssClass('js-website');
        $this->txtWebsite->setHtmlAttribute('autocomplete', 'off');
        $this->txtWebsite->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtWebsite->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtWebsite->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtWebsite->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblWebsite = new Q\Plugin\Control\Label($this);
        $this->lblWebsite->Text = t('Website');
        $this->lblWebsite->addCssClass('col-md-3 js-website');
        $this->lblWebsite->setCssStyle('font-weight', 'normal');

        $this->lstBoardStatus = new Q\Plugin\RadioList($this);
        $this->lstBoardStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
        $this->lstBoardStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
        $this->lstBoardStatus->setCssStyle('margin-top', '-11px');
        $this->lstBoardStatus->addAction(new Q\Event\Change(), new Ajax('lstBoardStatus_Change'));
        $this->lstBoardStatus->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->lstBoardStatus->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->lstBoardStatus->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->lstBoardStatus->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblBoardStatus = new Q\Plugin\Control\Label($this);
        $this->lblBoardStatus->Text = t('Status');
        $this->lblBoardStatus->addCssClass('col-md-3');
        $this->lblBoardStatus->setCssStyle('font-weight', 'normal');

        if (!empty($this->objActiveInputs)) {
            foreach ($this->objActiveInputs as $objActiveInput) {
                if ($objActiveInput->ActivityStatus == 1) {
                    switch ($objActiveInput->InputKey) {
                        case 1:
                            Application::executeJavaScript("$('.js-fullname').removeClass('hidden');");
                            break;
                        case 2:
                            Application::executeJavaScript("$('.js-position').removeClass('hidden');");
                            break;
                        case 3:
                            Application::executeJavaScript("$('.js-areasresponsibility').removeClass('hidden');");
                            break;
                        case 4:
                            Application::executeJavaScript("$('.js-interests').removeClass('hidden');");
                            break;
                        case 5:
                            Application::executeJavaScript("$('.js-description').removeClass('hidden');");
                            break;
                        case 6:
                            Application::executeJavaScript("$('.js-telephone').removeClass('hidden');");
                            break;
                        case 7:
                            Application::executeJavaScript("$('.js-sms').removeClass('hidden');");
                            break;
                        case 8:
                            Application::executeJavaScript("$('.js-fax').removeClass('hidden');");
                            break;
                        case 9:
                            Application::executeJavaScript("$('.js-address').removeClass('hidden');");
                            break;
                        case 10:
                            Application::executeJavaScript("$('.js-email').removeClass('hidden');");
                            break;
                        case 11:
                            Application::executeJavaScript("$('.js-website').removeClass('hidden');");
                            break;
                    }
                }
            }
        }
    }

    /**
     * Initializes multiple Toastr notifications with various configurations for alert types, positions, and messages.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The new board member has been successfully created and saved.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, creating or saving the new board member failed!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Sorry!</strong> The board member\'s full name is required!');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('<strong>Well done!</strong> Successfully updated this image data!');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('<strong>Sorry!</strong> Failed to update this image data!');
        $this->dlgToastr5->ProgressBar = true;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('<strong>Sorry!</strong> Successfully deleted this image data!');
        $this->dlgToastr6->ProgressBar = true;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('<strong>Sorry!</strong> Deleting this image data failed.');
        $this->dlgToastr7->ProgressBar = true;

        $this->dlgToastr8 = new Q\Plugin\Toastr($this);
        $this->dlgToastr8->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr8->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr8->Message = t('<strong>Well done!</strong> The order of board members was successfully updated!');
        $this->dlgToastr8->ProgressBar = true;

        $this->dlgToastr9 = new Q\Plugin\Toastr($this);
        $this->dlgToastr9->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr9->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr9->Message = t('<strong>Sorry</strong>, updating the order of board members failed!');
        $this->dlgToastr9->ProgressBar = true;

        $this->dlgToastr10 = new Q\Plugin\Toastr($this);
        $this->dlgToastr10->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr10->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr10->Message = t('<strong>Well done!</strong> The board member was successfully deleted!');
        $this->dlgToastr10->ProgressBar = true;

        $this->dlgToastr11 = new Q\Plugin\Toastr($this);
        $this->dlgToastr11->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr11->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr11->Message = t('<strong>Sorry</strong>, the board member deletion failed!');
        $this->dlgToastr11->ProgressBar = true;

        $this->dlgToastr12 = new Q\Plugin\Toastr($this);
        $this->dlgToastr12->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr12->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr12->Message = t('<strong>Sorry</strong>, the full name is required!');
        $this->dlgToastr12->ProgressBar = true;

        $this->dlgToastr13 = new Q\Plugin\Toastr($this);
        $this->dlgToastr13->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr13->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr13->Message = t('<strong>Well done!</strong> The board member data has been successfully updated!');
        $this->dlgToastr13->ProgressBar = true;

        $this->dlgToastr14 = new Q\Plugin\Toastr($this);
        $this->dlgToastr14->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr14->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr14->Message = t('<strong>Sorry</strong>, updating the board member data failed!');
        $this->dlgToastr14->ProgressBar = true;

        $this->dlgToastr15 = new Q\Plugin\Toastr($this);
        $this->dlgToastr15->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr15->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr15->Message = t('<strong>Well done!</strong> Updates to some records for this member were discarded, and the record has been restored!');
        $this->dlgToastr15->ProgressBar = true;
    }

    /**
     * Initializes multiple modal dialogs with various configurations, including text, title, header classes, buttons,
     * and actions. These modals provide feedback and confirmation requests for different user actions related to board
     * groups and menu items.
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
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this board group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this group public again later!</p>');
        $this->dlgModal3->Title = t('Question');
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('statusItem_Click'));

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

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this board member?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
        $this->dlgModal6->Title = 'Warning';
        $this->dlgModal6->HeaderClasses = 'btn-danger';
        $this->dlgModal6->addButton("I accept", null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal6->addCloseButton(t("I'll cancel"));
        $this->dlgModal6->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('deleteItem_Click'));

        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to disable the image upload for this board member group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can re-enable the image upload for this group later!</p>');
        $this->dlgModal7->Title = t('Question');
        $this->dlgModal7->HeaderClasses = 'btn-danger';
        $this->dlgModal7->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal7->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal7->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('imageUploadItem_Click'));

        $this->dlgModal8 = new Bs\Modal($this);
        $this->dlgModal8->Title = t("Success");
        $this->dlgModal8->HeaderClasses = 'btn-success';
        $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image upload is now disabled!</p>');
        $this->dlgModal8->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal9 = new Bs\Modal($this);
        $this->dlgModal9->Title = t("Success");
        $this->dlgModal9->HeaderClasses = 'btn-success';
        $this->dlgModal9->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image upload is now enabled!</p>');
        $this->dlgModal9->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the stop event of a sortable action. It processes the new order of items,
     * updates the positions, and triggers notifications based on the array's content.
     *
     * @param ActionParams $params Parameters from the sortable stop action event.
     * @return void
     */
    protected function sortable_stop(ActionParams $params) {
        $arr = $this->dlgSorter->ItemArray;

        foreach ($arr as $order => $cids) {
            $cid = explode('_',  $cids);
            $id = end($cid);

            $objSorter = Board::load($id);
            $objSorter->setOrder($order);
            $objSorter->setPostUpdateDate(Q\QDateTime::Now());
            $objSorter->save();
        }

        // Let's check if the array is not empty
        if (!empty($arr)) {
            $this->dlgToastr8->notify();
        } else {
            $this->dlgToastr9->notify();
        }

        Application::executeJavaScript(sprintf("
            $('.board-setting-wrapper').addClass('hidden');
            $('.board-image-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
       "));

        $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objBoardsSettings->save();

        $this->calPostUpdateDate->Text = $this->objBoardsSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    /**
     * Handles the click event for the Add Member button.
     *
     * @param ActionParams $params The parameters provided by the action event.
     * @return void
     */
    protected function btnAddMember_Click(ActionParams $params)
    {
        $this->btnAddMember->Enabled = false;
        $this->txtNewFullName->Display = true;
        $this->btnMemberSave->Display = true;
        $this->btnMemberCancel->Display = true;
        $this->txtNewFullName->Text = '';
        $this->txtNewFullName->focus();

        Application::executeJavaScript(sprintf("
            jQuery(\"[data-value='{$this->intClick}']\").removeClass('activated');
             $('.board-setting-wrapper').addClass('hidden');
            $('.board-image-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
       "));
    }

    /**
     * Handles the click event for the Save Member button.
     *
     * @param ActionParams $params The parameters provided by the action event.
     * @return void
     */
    protected function btnMemberSave_Click(ActionParams $params)
    {
        if (trim($this->txtNewFullName->Text) !== '') {
            $objBoard = new Board();
            $objBoard->setFullname(trim($this->txtNewFullName->Text));
            $objBoard->setBoardId($this->intId);
            $objBoard->setBoardIdTitle($this->objBoardsSettings->getName());
            $objBoard->setOrder(Board::generateOrder($this->intId));
            $objBoard->setStatus(2);
            $objBoard->setPostDate(Q\QDateTime::Now());
            $objBoard->save();

            // A check must be made here if the first record and the following records occur in this group,
            // then set "board_locked" to 1 in the BoardsSettings column, etc...

            if (Board::countByBoardId($this->intId) !== 0) {
                if ($this->objBoardsSettings->getBoardLocked() == 0) {
                    $this->objBoardsSettings->setBoardLocked(1);
                }
            }

            $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
            $this->objBoardsSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();

            if ($objBoard->getId()) {
                $this->txtNewFullName->Text = '';
                $this->btnAddMember->Enabled = true;
                $this->txtNewFullName->Display = false;
                $this->btnMemberSave->Display = false;
                $this->btnMemberCancel->Display = false;

                $this->dlgToastr1->notify();
            } else {
                $this->dlgToastr2->notify();
            }
        } else {
            $this->txtNewFullName->Text = '';
            $this->txtNewFullName->focus();
            $this->btnAddMember->Enabled = false;
            $this->txtNewFullName->Display = true;
            $this->btnMemberSave->Display = true;
            $this->btnMemberCancel->Display = true;

            $this->dlgToastr3->notify();
        }

        if ($this->objBoardsSettings->getBoardLocked() === 1) {
            $this->lblInfo->Display = false;
        } else {
            $this->lblInfo->Display = true;
        }
    }

    /**
     * Handles the click event for the Member Cancel button.
     *
     * @param ActionParams $params The parameters provided by the action event.
     * @return void
     */
    protected function btnMemberCancel_Click(ActionParams $params)
    {
        $this->btnAddMember->Enabled = true;
        $this->txtNewFullName->Display = false;
        $this->btnMemberSave->Display = false;
        $this->btnMemberCancel->Display = false;
        $this->txtNewFullName->Text = '';
    }

    /**
     * Handles the click event for the Edit button.
     *
     * @param ActionParams $params The parameters provided by the action event.
     * @return void
     */
    protected function btnEdit_Click(ActionParams $params)
    {
        $intEditId = intval($params->ActionParameter);
        $objEdit = Board::load($intEditId);
        $this->intClick = intval($intEditId);

        Application::executeJavaScript("$('.js-board-wrapper').get(0).scrollIntoView({behavior: 'smooth'});");

        if (!empty($this->objActiveInputs)) {
            foreach ($this->objActiveInputs as $objActiveInput) {
                if ($objActiveInput->ActivityStatus == 1) {
                    switch ($objActiveInput->InputKey) {
                        case 1:
                            $this->txtFullName->Text = $objEdit->Fullname;
                            break;
                        case 2:
                            $this->txtPosition->Text = $objEdit->Position;
                            break;
                        case 3:
                            $this->txtAreasResponsibility->Text = $objEdit->AreasResponsibility;
                            break;
                        case 4:
                            $this->txtInterests->Text = $objEdit->Interests;
                            break;
                        case 5:
                            $this->txtDescription->Text = $objEdit->Description;
                            break;
                        case 6:
                            $this->txtTelephone->Text = $objEdit->Telephone;
                            break;
                        case 7:
                            $this->txtSMS->Text = $objEdit->Sms;
                            break;
                        case 8:
                            $this->txtFax->Text = $objEdit->Fax;
                            break;
                        case 9:
                            $this->txtAddress->Text = $objEdit->Address;
                            break;
                        case 10:
                            $this->txtEmail->Text = $objEdit->Email;
                            break;
                        case 11:
                            $this->txtWebsite->Text = $objEdit->Website;
                            break;
                    }
                }
            }
        }

        $this->lstBoardStatus->SelectedValue = $objEdit->Status;
        $this->objMediaFinder->SelectedImageId = $objEdit->PictureId;

        if ($this->objMediaFinder->SelectedImageId !== null) {
            $objFiles = Files::loadById($this->objMediaFinder->SelectedImageId);

            if ($objFiles) {
                $this->objMediaFinder->SelectedImagePath = $this->objMediaFinder->TempUrl . $objFiles->getPath();
                $this->objMediaFinder->SelectedImageName = $objFiles->getName();
            }
        }

        if ($this->objBoardsSettings->AllowedUploading === 1) {
            Application::executeJavaScript("
               $(\"[data-value='{$intEditId}']\").addClass('activated');
               $(\"[data-value='{$intEditId}']\").removeClass('inactivated');
               $('.board-setting-wrapper').removeClass('hidden');
               $('.board-image-wrapper').addClass('open');
               $('.board-image-wrapper').removeClass('hidden');
               $('.form-actions-wrapper').removeClass('hidden');
            ");
        } else {
            Application::executeJavaScript("
               $(\"[data-value='{$intEditId}']\").addClass('activated');
               $(\"[data-value='{$intEditId}']\").removeClass('inactivated');
               $('.board-setting-wrapper').removeClass('hidden');
               $('.board-image-wrapper').addClass('open');
               $('.board-image-wrapper').addClass('hidden');
               $('.form-actions-wrapper').removeClass('hidden');
            ");
        }

        //$this->dlgSorter->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the change event for the image upload list.
     *
     * @param ActionParams $params Parameters associated with the action triggering the change.
     * @return void
     */
    protected function lstImageUpload_Change(ActionParams $params)
    {
        if ($this->objBoardsSettings->getAllowedUploading() === 1) {
            $this->dlgModal7->showDialogBox();
        } else {
            $this->dlgModal9->showDialogBox();
            $this->updateImageUpload();
        }
    }

    /**
     * Handles the click event for the image upload item.
     *
     * @param ActionParams $params Parameters associated with the action triggering the click.
     * @return void
     */
    protected function imageUploadItem_Click(ActionParams $params)
    {
        $this->dlgModal7->hideDialogBox();

        Application::executeJavaScript(sprintf("
            if ($('.board-image-wrapper').hasClass('open')) {
                $('.board-image-wrapper').addClass('hidden');
            } else {
                $('.board-image-wrapper').addClass('hidden');
            }
        "));

        $this->objBoardsSettings->setAllowedUploading(2);
        $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
        $this->dlgModal8->showDialogBox();
    }

    /**
     * Updates the image upload settings and refreshes the display with the new data.
     *
     * @return void
     */
    protected function updateImageUpload()
    {
        Application::executeJavaScript(sprintf("
            if ($('.board-image-wrapper').hasClass('open')) {
                $('.board-image-wrapper').removeClass('hidden');
            } else {
                $('.board-image-wrapper').addClass('hidden');
            }
        "));

        $this->objBoardsSettings->setAllowedUploading(1);
        $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    /**
     * Handles the push event for saving an image to a board.
     *
     * @param ActionParams $params The parameters provided by the action event.
     * @return void
     */
    protected function imageSave_Push(ActionParams $params)
    {
        $saveId = $this->objMediaFinder->Item;
        $objFilePath = Files::loadById($saveId);
        $objBoard = Board::loadById($this->intClick);

        if ($objFilePath->getLockedFile() == 0) {
            $objFilePath->setLockedFile($objFilePath->getLockedFile() + 1);
            $objFilePath->save();
        }

        $objBoard->setPictureId($saveId);
        $objBoard->setFileId($saveId);
        $objBoard->setPostUpdateDate(Q\QDateTime::Now());
        $objBoard->save();

        $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        if ($objBoard->getPictureId() !== null && $objBoard->getFileId() !== null) {
            $this->dlgToastr4->notify();
        } else {
            $this->dlgToastr5->notify();
        }
    }

    /**
     * Deletes the image associated with a board and updates board settings accordingly.
     *
     * @param ActionParams $params The parameters provided by the action event.
     * @return void
     */
    protected function imageDelete_Push(ActionParams $params)
    {
        $objBoard = Board::loadById($this->intClick);
        $objFiles = Files::loadById($objBoard->getPictureId());

        if ($objFiles->getLockedFile() !== 0) {
            $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
            $objFiles->save();
        }

        $objBoard->setPictureId(null);
        $objBoard->setFileId(null);
        $objBoard->setPostUpdateDate(Q\QDateTime::Now());
        $objBoard->save();

        $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        if ($objBoard->getPictureId() == null && $objBoard->getFileId() == null) {
            $this->dlgToastr6->notify();
        } else {
            $this->dlgToastr7->notify();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function lstBoardStatus_Change(ActionParams $params)
    {
        $objBoard = Board::loadById($this->intClick);

        $objBoard->setStatus($this->lstBoardStatus->SelectedValue);
        $objBoard->save();

        $this->lstBoardStatus->SelectedValue = $objBoard->getStatus();
        $this->lstBoardStatus->refresh();

        $this->dlgToastr13->notify();
    }

    /**
     * Handles the change event for the status list.
     *
     * @param ActionParams $params Parameters associated with the action triggering the change.
     * @return void
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
        } else if ($this->objBoardsSettings->getStatus() === 1) {
            $this->dlgModal3->showDialogBox();
        } else {
            $this->lockInputFields();
        }
    }

    /**
     * Handles the click event for the status item.
     *
     * @param ActionParams $params Parameters associated with the action triggering the click.
     * @return void
     */
    protected function statusItem_Click(ActionParams $params)
    {
        $this->dlgModal3->hideDialogBox();

        $objMenuContent = MenuContent::loadById($this->intGroup);

        $this->objBoardsSettings->setStatus(2);
        $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $objMenuContent->setIsEnabled(2);
        $objMenuContent->save();

        $this->refreshDisplay();
        $this->dlgModal4->showDialogBox();
    }

    /**
     * Updates input fields with the current status from the board settings.
     *
     * @return void
     */
    protected function updateInputFields()
    {
        $this->lstStatus->SelectedValue = $this->objBoardsSettings->getStatus();
        $this->lstStatus->refresh();
    }

    /**
     * Locks the input fields, updates board settings and menu content,
     * refreshes the display, and shows a modal dialog box.
     *
     * @return void
     */
    protected function lockInputFields()
    {
        $objMenuContent = MenuContent::loadById($this->intGroup);

        $this->objBoardsSettings->setStatus(1);
        $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $objMenuContent->setIsEnabled(1);
        $objMenuContent->save();

        $this->refreshDisplay();
        $this->dlgModal5->showDialogBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the update button.
     *
     * @param ActionParams $params Parameters associated with the action triggering the click.
     * @return void
     */
    protected function btnUpdate_Click(ActionParams $params)
    {
        $objUpdate = Board::load($this->intClick);

        // Check if $objUpdate is available
        if (!$objUpdate) {
            $this->dlgToastr14->notify();
            return;
        }

        // Check if FullName is empty
        if ($this->txtFullName->Text == '') {
            $this->dlgToastr12->notify();
            return;
        }

        if (!empty($this->objActiveInputs)) {
            foreach ($this->objActiveInputs as $objActiveInput) {
                if ($objActiveInput->ActivityStatus == 1) {
                    switch ($objActiveInput->InputKey) {
                        case 1:
                            $objUpdate->Fullname = $this->txtFullName->Text;
                            break;
                        case 2:
                            $objUpdate->Position = $this->txtPosition->Text;
                            break;
                        case 3:
                            $objUpdate->AreasResponsibility = $this->txtAreasResponsibility->Text;
                            break;
                        case 4:
                            $objUpdate->Interests = $this->txtInterests->Text;
                            break;
                        case 5:
                            $objUpdate->Description = $this->txtDescription->Text;
                            break;
                        case 6:
                            $objUpdate->Telephone = $this->txtTelephone->Text;
                            break;
                        case 7:
                            $objUpdate->SMS = $this->txtSMS->Text;
                            break;
                        case 8:
                            $objUpdate->Fax = $this->txtFax->Text;
                            break;
                        case 9:
                            $objUpdate->Address = $this->txtAddress->Text;
                            break;
                        case 10:
                            $objUpdate->Email = $this->txtEmail->Text;
                            break;
                        case 11:
                            $objUpdate->Website = $this->txtWebsite->Text;
                            break;
                    }
                }
            }
        }

        $objUpdate->Status = $this->lstBoardStatus->SelectedValue;
        $objUpdate->PostUpdateDate = Q\QDateTime::Now();

        // Check if the save was successful
        try {
            $objUpdate->save();
            $this->dlgToastr13->notify();
        } catch (Exception $e) {
            $this->dlgToastr14->notify();
            error_log('Save failed: ' . $e->getMessage());
            return;
        }

        // Continue to update additional data and refresh the screen
        $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
        $this->dlgSorter->refresh();

        Application::executeJavaScript(sprintf("
            $(\"[data-value='%s']\").addClass('activated');
            //$('.board-setting-wrapper').addClass('hidden');  
            ", $this->intClick)
        );
    }

    /**
     * Handles the click event for the item escape action.
     *
     * @param ActionParams $params Parameters associated with the action triggering the click event.
     * @return void
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        $objCancel = Board::load($this->intClick);

        // Check if $objCancel is available
        if ($objCancel) {
            $this->dlgToastr15->notify();
        }

        if (!empty($this->objActiveInputs)) {
            foreach ($this->objActiveInputs as $objActiveInput) {
                if ($objActiveInput->ActivityStatus == 1) {
                    switch ($objActiveInput->InputKey) {
                        case 1:
                            $this->txtFullName->Text = $objCancel->Fullname;
                            break;
                        case 2:
                            $this->txtPosition->Text = $objCancel->Position;
                            break;
                        case 3:
                            $this->txtAreasResponsibility->Text = $objCancel->AreasResponsibility;
                            break;
                        case 4:
                            $this->txtInterests->Text = $objCancel->Interests;
                            break;
                        case 5:
                            $this->txtDescription->Text = $objCancel->Description;
                            break;
                        case 6:
                            $this->txtTelephone->Text = $objCancel->Telephone;
                            break;
                        case 7:
                            $this->txtSMS->Text = $objCancel->Sms;
                            break;
                        case 8:
                            $this->txtFax->Text = $objCancel->Fax;
                            break;
                        case 9:
                            $this->txtAddress->Text = $objCancel->Address;
                            break;
                        case 10:
                            $this->txtEmail->Text = $objCancel->Email;
                            break;
                        case 11:
                            $this->txtWebsite->Text = $objCancel->Website;
                            break;
                    }
                }
            }
        }

        $this->lstBoardStatus->SelectedValue = $objCancel->Status;
    }

    /**
     * Handles the click event for the delete button.
     *
     * @param ActionParams $params Parameters associated with the action triggering the click.
     * @return void
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        $this->intClick = intval($params->ActionParameter);
        $this->dlgModal6->showDialogBox();
    }

    /**
     * Handles the deletion of a board item when the delete button is clicked.
     *
     * @param ActionParams $params Parameters associated with the action triggering the delete event.
     * @return void
     */
    protected function deleteItem_Click(ActionParams $params)
    {
        $objBoard = Board::loadById($this->intClick);

        if (Board::countByBoardId($this->intId) == 1) {
            if ($this->objBoardsSettings->getBoardLocked() == 1) {
                $this->objBoardsSettings->setBoardLocked(0);
            }
        }

        if ($objBoard->getFileId() !== null) {
            $objFile = Files::loadById($objBoard->getFileId());
            $objFile->setLockedFile($objFile->getLockedFile() - 1);
            $objFile->save();
        }

        $objBoard->delete();

        if ($objBoard->getId() !== $objBoard) {
            $this->dlgToastr10->notify();
        } else {
            $this->dlgToastr11->notify();
        }

        $this->objBoardsSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objBoardsSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objBoardsSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objBoardsSettings->getUserAsBoardsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objBoardsSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        $this->dlgModal6->hideDialogBox();
    }

    /**
     * Handles the click event for the cancel button.
     *
     * @param ActionParams $params Parameters associated with the action triggering the click.
     * @return void
     */
    protected function btnCloseWindow_Click(ActionParams $params)
    {
        Application::executeJavaScript(sprintf("
            $(\"[data-value='{$this->intClick}']\").removeClass('activated');
            $('.board-setting-wrapper').addClass('hidden');
            $('.board-image-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
       "));
    }

    /**
     * Handles the back button click event.
     *
     * @param ActionParams $params Parameters associated with the action triggering the back button click.
     * @return void
     */
    protected function btnBack_Click(ActionParams $params)
    {

        $this->redirectToListPage();
    }

    /**
     * Redirects the application to the board list page.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        Application::redirect('board_list.php');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Refreshes the display based on the settings of the board.
     * Updates the visibility of the post date, post update date, author,
     * and users as editors fields, and adjusts their CSS classes accordingly.
     *
     * @return void
     */
    protected function refreshDisplay()
    {
        if ($this->objBoardsSettings->getPostDate() &&
            !$this->objBoardsSettings->getPostUpdateDate() &&
            $this->objBoardsSettings->getAuthor() &&
            !$this->objBoardsSettings->countUsersAsBoardsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = false;
            $this->txtAuthor->Display = false;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }

        if ($this->objBoardsSettings->getPostDate() &&
            $this->objBoardsSettings->getPostUpdateDate() &&
            $this->objBoardsSettings->getAuthor() &&
            !$this->objBoardsSettings->countUsersAsBoardsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }

        if ($this->objBoardsSettings->getPostDate() &&
            $this->objBoardsSettings->getPostUpdateDate() &&
            $this->objBoardsSettings->getAuthor() &&
            $this->objBoardsSettings->countUsersAsBoardsEditors()) {
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