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
    protected $dlgModal10;

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

    protected $dlgInfoBox1;
    protected $dlgInfoBox2;

    protected $lblInfo;
    protected $btnAddMember;
    
    protected $txtNewMemberName;
    
    protected $btnMemberSave;
    protected $btnMemberCancel;
    protected $lblTitleSlug;
    protected $txtTitleSlug;

    protected $dlgSorter;
    protected $objMediaFinder;

    protected $txtMemberName;
    protected $lblMemberName;

    protected $txtRegistryCode;
    protected $lblRegistryCode;

    protected $txtBankAccountNumber;
    protected $lblBankAccountNumber;


    protected $txtRepresentativeFullName;
    protected $lblRepresentativeFullName;
    protected $txtRepresentativeTelephone;
    protected $lblRepresentativeTelephone;
    protected $txtRepresentativeSMS;
    protected $lblRepresentativeSMS;
    protected $txtRepresentativeFax;
    protected $lblRepresentativeFax;
    protected $txtRepresentativeEmail;
    protected $lblRepresentativeEmail;
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
    protected $txtMembersNumber;
    protected $lblMembersNumber;
    
    protected $lstMemberStatus;
    protected $lblMemberStatus;

    protected $calMemberPostUpdateDate;
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
    protected $objMember;
    protected $objMenu;
    protected $objMembersSettings;
    protected $objActiveInputs;

    protected $strRootPath = APP_UPLOADS_DIR;
    protected $strTempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
    protected $strDateTimeFormat = 'd.m.Y H:i';

    protected function formCreate()
    {
        parent::formCreate();

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->intGroup = Application::instance()->context()->queryStringItem('group');
        if (!empty($this->intId)) {
            $this->objMember = Members::loadByIdFromMemberId($this->intId);
            $this->objMembersSettings = MembersSettings::load($this->intId);
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
            $('.js-member-name').addClass('hidden');
            $('.js-registry-code').addClass('hidden');
            $('.js-bank-account-number').addClass('hidden');
            $('.js-representative-fullname').addClass('hidden');
            $('.js-representative-telephone').addClass('hidden');
            $('.js-representative-sms').addClass('hidden');
            $('.js-representative-fax').addClass('hidden');
            $('.js-representative-email').addClass('hidden');
            $('.js-description').addClass('hidden');
            $('.js-telephone').addClass('hidden');
            $('.js-sms').addClass('hidden');
            $('.js-fax').addClass('hidden');
            $('.js-address').addClass('hidden');
            $('.js-email').addClass('hidden');
            $('.js-website').addClass('hidden');
            $('.js-members-number').addClass('hidden');
        ");
    }

    /**
     * Creates and initializes the input controls for the form, including labels, text boxes, radio buttons,
     * and other components with their respective properties and styles. It also handles the visibility of certain
     * elements based on the member's settings.
     *
     * @return void
     */
    protected function createInputs()
    {
        $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
        $this->lblGroupTitle->Text = $this->objMembersSettings->getName();
        $this->lblGroupTitle->setCssStyle('font-weight', 600);

        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please create the first member!');
        $this->lblInfo->setCssStyle('margin-bottom', 0);

        if ($this->objMembersSettings->getMembersLocked() === 1) {
            $this->lblInfo->Display = false;
        } else {
            $this->lblInfo->Display = true;
        }

        $this->txtNewMemberName = new Bs\TextBox($this);
        $this->txtNewMemberName->Placeholder = t('New member\'s name');
        $this->txtNewMemberName->setHtmlAttribute('autocomplete', 'off');
        $this->txtNewMemberName->setCssStyle('float', 'left');
        $this->txtNewMemberName->Width = '45%';
        $this->txtNewMemberName->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtNewMemberName->Display = false;

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View: ');
        $this->lblTitleSlug->setCssStyle('font-weight', 'bold');

        if ($this->objMembersSettings->getTitleSlug()) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
            $this->txtTitleSlug->setCssStyle('text-align', 'left;');
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objMembersSettings->getTitleSlug();
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
        $this->calPostDate->Text = $this->objMembersSettings->PostDate ? $this->objMembersSettings->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostDate->setCssStyle('font-weight', 'normal');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objMembersSettings->PostUpdateDate ? $this->objMembersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->setCssStyle('font-weight', 'bold');

        $this->txtAuthor  = new Bs\Label($this);
        $this->txtAuthor->Text = $this->objMembersSettings->Author;
        $this->txtAuthor->setCssStyle('font-weight', 'normal');

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->setCssStyle('font-weight', 'bold');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
        $this->lstStatus->SelectedValue = $this->objMembersSettings->Status;
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
        $this->lstImageUpload->SelectedValue = $this->objMembersSettings->AllowedUploading;
        $this->lstImageUpload->setCssStyle('margin-top', '-10px');
        $this->lstImageUpload->addAction(new Q\Event\Change(), new Q\Action\Ajax('lstImageUpload_Change'));

        if ($this->objMembersSettings->AllowedUploading === 0) {
            Application::executeJavaScript("
                $('.member-image-wrapper').addClass('hidden');
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
    }

    /**
     * Binds data to the sorter dialog's data source for the specified member.
     *
     * @return void
     */
    protected function Sorter_Bind()
    {
        $this->dlgSorter->DataSource = Members::QueryArray(
            QQ::Equal(QQN::Members()->MemberId, $this->intId),
            QQ::orderBy(QQN::Members()->Order)
        );
    }

    /**
     * Returns sorting information for the given members object.
     *
     * @param Members $objMember The member object for which sorting information is being retrieved.
     * @return array An associative array containing sorting details of the board.
     */
    public function Sorter_Draw(Members $objMember)
    {
        if ($objMember->PictureId) {
            $objFile = Files::load($objMember->PictureId);
            $a['path'] = $objFile->Path;
        }

        $a['id'] = $objMember->Id;
        $a['group_id'] = $objMember->MemberId;
        $a['order'] = $objMember->Order;
        $a['title'] = $objMember->MemberName;
        $a['post_date'] = $objMember->PostDate;
        $a['post_update_date'] = $objMember->PostUpdateDate;
        $a['status'] = $objMember->Status;
        return $a;
    }

    /**
     * Draws edit and delete buttons for the given member object.
     *
     * @param Members $objMember The member object for which the buttons are being created.
     * @return string Rendered HTML for the edit and delete buttons.
     */
    public function Buttons_Draw(Members $objMember)
    {
        $strEditId = 'btnEdit' . $objMember->Id;

        if (!$btnEdit = $this->getControl($strEditId)) {
            $btnEdit = new Bs\Button($this->dlgSorter, $strEditId);
            $btnEdit->Glyph = 'glyphicon glyphicon-pencil';
            $btnEdit->Tip = true;
            $btnEdit->ToolTip = t('Edit');
            $btnEdit->CssClass = 'btn btn-icon btn-xs edit';
            $btnEdit->ActionParameter = $objMember->Id;
            $btnEdit->UseWrapper = false;
            $btnEdit->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnEdit_Click'));
        }

        $strDeleteId = 'btnDelete' . $objMember->Id;

        if (!$btnDelete = $this->getControl($strDeleteId)) {
            $btnDelete = new Bs\Button($this->dlgSorter, $strDeleteId);
            $btnDelete->Glyph = 'glyphicon glyphicon-trash';
            $btnDelete->Tip = true;
            $btnDelete->ToolTip = t('Delete');
            $btnDelete->CssClass = 'btn btn-icon btn-xs delete';
            $btnDelete->ActionParameter = $objMember->Id;
            $btnDelete->UseWrapper = false;
            $btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));
        }

        return $btnEdit->render(false) . $btnDelete->render(false);
    }

    /**
     * Initializes and configures the input manager by querying the active member options,
     * creating the necessary input fields and labels, and setting up their properties.
     * The inputs are sorted based on an 'Order' field and certain elements are displayed
     * based on their activity status.
     *
     * @return void
     */
    protected function createInputManager()
    {
        $this->objActiveInputs = MembersOptions::QueryArray(
            QQ::Equal(QQN::MembersOptions()->SettingsId, $this->intId)
        );

        // Sort the inputs according to the order specified in the 'Order' field
        usort($this->objActiveInputs, function($a, $b) {
            return $a->Order - $b->Order;
        });

        $this->txtMemberName = new Bs\TextBox($this);
        $this->txtMemberName->addCssClass('js-member-name');
        $this->txtMemberName->setHtmlAttribute('autocomplete', 'off');
        $this->txtMemberName->setHtmlAttribute('required', 'required');
        $this->txtMemberName->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtMemberName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtMemberName->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtMemberName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblMemberName  = new Q\Plugin\Control\Label($this);
        $this->lblMemberName->Text = t('Member name');
        $this->lblMemberName->addCssClass('col-md-3 js-member-name');
        $this->lblMemberName->setCssStyle('font-weight', 'normal');
        $this->lblMemberName->Required = true;

        $this->txtRegistryCode = new Bs\TextBox($this);
        $this->txtRegistryCode->addCssClass('js-registry-code ');
        $this->txtRegistryCode->setHtmlAttribute('autocomplete', 'off');
        $this->txtRegistryCode->addAction(new Q\Event\KeyUp(), new Q\Action\Ajax('txtRegistryCode_keyUp'));
        $this->txtRegistryCode->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtRegistryCode->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtRegistryCode->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtRegistryCode->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblRegistryCode  = new Q\Plugin\Control\Label($this);
        $this->lblRegistryCode->Text = t('Registry code');
        $this->lblRegistryCode->addCssClass('col-md-3 js-registry-code');
        $this->lblRegistryCode->setCssStyle('font-weight', 'normal');

        $this->dlgInfoBox1 = new Q\Plugin\Control\InfoBox($this);
        $this->dlgInfoBox1->Text = t("The entered field must contain only numbers!");
        $this->dlgInfoBox1->addCssClass('infobox-danger');
        $this->dlgInfoBox1->addCssClass('fadeOut');

        $this->txtBankAccountNumber = new Bs\TextBox($this);
        $this->txtBankAccountNumber->addCssClass('js-bank-account-number');
        $this->txtBankAccountNumber->setHtmlAttribute('autocomplete', 'off');
        $this->txtBankAccountNumber->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtBankAccountNumber->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtBankAccountNumber->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtBankAccountNumber->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblBankAccountNumber  = new Q\Plugin\Control\Label($this);
        $this->lblBankAccountNumber ->Text = t('Bank account number');
        $this->lblBankAccountNumber ->addCssClass('col-md-3 js-bank-account-number');
        $this->lblBankAccountNumber ->setCssStyle('font-weight', 'normal');

        $this->txtRepresentativeFullName = new Bs\TextBox($this);
        $this->txtRepresentativeFullName->addCssClass('js-representative-fullname');
        $this->txtRepresentativeFullName->setHtmlAttribute('autocomplete', 'off');
        $this->txtRepresentativeFullName->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtRepresentativeFullName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtRepresentativeFullName->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtRepresentativeFullName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblRepresentativeFullName  = new Q\Plugin\Control\Label($this);
        $this->lblRepresentativeFullName->Text = t('Representative\'s full name');
        $this->lblRepresentativeFullName->addCssClass('col-md-3 js-representative-fullname');
        $this->lblRepresentativeFullName->setCssStyle('font-weight', 'normal');

        $this->txtRepresentativeTelephone = new Bs\TextBox($this);
        $this->txtRepresentativeTelephone->addCssClass('js-representative-telephone');
        $this->txtRepresentativeTelephone->setHtmlAttribute('autocomplete', 'off');
        $this->txtRepresentativeTelephone->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtRepresentativeTelephone->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtRepresentativeTelephone->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtRepresentativeTelephone->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblRepresentativeTelephone = new Q\Plugin\Control\Label($this);
        $this->lblRepresentativeTelephone->Text = t('Representative\'s telephone');
        $this->lblRepresentativeTelephone->addCssClass('col-md-3 js-representative-telephone');
        $this->lblRepresentativeTelephone->setCssStyle('font-weight', 'normal');

        $this->txtRepresentativeSMS = new Bs\TextBox($this);
        $this->txtRepresentativeSMS->addCssClass('js-representative-sms');
        $this->txtRepresentativeSMS->setHtmlAttribute('autocomplete', 'off');
        $this->txtRepresentativeSMS->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtRepresentativeSMS->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtRepresentativeSMS->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtRepresentativeSMS->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblRepresentativeSMS = new Q\Plugin\Control\Label($this);
        $this->lblRepresentativeSMS->Text = t('Representative\'s SMS');
        $this->lblRepresentativeSMS->addCssClass('col-md-3 js-representative-sms');
        $this->lblRepresentativeSMS->setCssStyle('font-weight', 'normal');

        $this->txtRepresentativeFax = new Bs\TextBox($this);
        $this->txtRepresentativeFax->addCssClass('js-representative-fax');
        $this->txtRepresentativeFax->setHtmlAttribute('autocomplete', 'off');
        $this->txtRepresentativeFax->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtRepresentativeFax->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtRepresentativeFax->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtRepresentativeFax->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblRepresentativeFax = new Q\Plugin\Control\Label($this);
        $this->lblRepresentativeFax->Text = t('Representative\'s fax');
        $this->lblRepresentativeFax->addCssClass('col-md-3 js-representative-fax');
        $this->lblRepresentativeFax->setCssStyle('font-weight', 'normal');

        $this->txtRepresentativeEmail = new Bs\TextBox($this);
        $this->txtRepresentativeEmail->addCssClass('js-representative-email');
        $this->txtRepresentativeEmail->setHtmlAttribute('autocomplete', 'off');
        $this->txtRepresentativeEmail->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtRepresentativeEmail->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtRepresentativeEmail->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtRepresentativeEmail->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblRepresentativeEmail = new Q\Plugin\Control\Label($this);
        $this->lblRepresentativeEmail->Text = t('Representative\'s email');
        $this->lblRepresentativeEmail->addCssClass('col-md-3 js-representative-email');
        $this->lblRepresentativeEmail->setCssStyle('font-weight', 'normal');

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
        $this->txtSMS->addCssClass('js-sms');
        $this->txtSMS->setHtmlAttribute('autocomplete', 'off');
        $this->txtSMS->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtSMS->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtSMS->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtSMS->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblSMS = new Q\Plugin\Control\Label($this);
        $this->lblSMS->Text = t('SMS');
        $this->lblSMS->addCssClass('col-md-3 js-sms');
        $this->lblSMS->setCssStyle('font-weight', 'normal');

        $this->txtFax = new Bs\TextBox($this);
        $this->txtFax->addCssClass('js-fax');
        $this->txtFax->setHtmlAttribute('autocomplete', 'off');
        $this->txtFax->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtFax->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtFax->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtFax->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblFax = new Q\Plugin\Control\Label($this);
        $this->lblFax->Text = t('Fax');
        $this->lblFax->addCssClass('col-md-3 js-fax');
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

        $this->txtMembersNumber = new Bs\TextBox($this);
        $this->txtMembersNumber->addCssClass('js-members-number js-validate-popup');
        $this->txtMembersNumber->setHtmlAttribute('autocomplete', 'off');
        $this->txtMembersNumber->addAction(new Q\Event\KeyUp(), new Q\Action\Ajax('txtMembersNumber_keyUp'));
        $this->txtMembersNumber->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->txtMembersNumber->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtMembersNumber->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtMembersNumber->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblMembersNumber = new Q\Plugin\Control\Label($this);
        $this->lblMembersNumber->Text = t('Members number');
        $this->lblMembersNumber->addCssClass('col-md-3 js-members-number');
        $this->lblMembersNumber->setCssStyle('font-weight', 'normal');

        $this->dlgInfoBox2 = new Q\Plugin\Control\InfoBox($this);
        $this->dlgInfoBox2->Text = t("The entered field must contain only numbers!");
        $this->dlgInfoBox2->addCssClass('infobox-danger');
        $this->dlgInfoBox2->addCssClass('fadeOut');

        $this->lstMemberStatus = new Q\Plugin\RadioList($this);
        $this->lstMemberStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
        $this->lstMemberStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
        $this->lstMemberStatus->setCssStyle('margin-top', '-11px');
        $this->lstMemberStatus->addAction(new Q\Event\Change(), new Ajax('lstMemberStatus_Change'));
        $this->lstMemberStatus->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnUpdate_Click'));
        $this->lstMemberStatus->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->lstMemberStatus->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->lstMemberStatus->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblMemberStatus = new Q\Plugin\Control\Label($this);
        $this->lblMemberStatus->Text = t('Status');
        $this->lblMemberStatus->addCssClass('col-md-3');
        $this->lblMemberStatus->setCssStyle('font-weight', 'normal');

        if (!empty($this->objActiveInputs)) {
            foreach ($this->objActiveInputs as $objActiveInput) {
                if ($objActiveInput->ActivityStatus == 1) {
                    switch ($objActiveInput->InputKey) {
                        case 1:
                            Application::executeJavaScript("$('.js-member-name').removeClass('hidden');");
                            break;
                        case 2:
                            Application::executeJavaScript("$('.js-registry-code').removeClass('hidden');");
                            break;
                        case 3:
                            Application::executeJavaScript("$('.js-bank-account-number').removeClass('hidden');");
                            break;
                        case 4:
                            Application::executeJavaScript("$('.js-representative-fullname').removeClass('hidden');");
                            break;
                        case 5:
                            Application::executeJavaScript("$('.js-representative-telephone').removeClass('hidden');");
                            break;
                        case 6:
                            Application::executeJavaScript("$('.js-representative-sms').removeClass('hidden');");
                            break;
                        case 7:
                            Application::executeJavaScript("$('.js-representative-fax').removeClass('hidden');");
                            break;
                        case 8:
                            Application::executeJavaScript("$('.js-representative-email').removeClass('hidden');");
                            break;
                        case 9:
                            Application::executeJavaScript("$('.js-description').removeClass('hidden');");
                            break;
                        case 10:
                            Application::executeJavaScript("$('.js-telephone').removeClass('hidden');");
                            break;
                        case 11:
                            Application::executeJavaScript("$('.js-sms').removeClass('hidden');");
                            break;
                        case 12:
                            Application::executeJavaScript("$('.js-fax').removeClass('hidden');");
                            break;
                        case 13:
                            Application::executeJavaScript("$('.js-address').removeClass('hidden');");
                            break;
                        case 14:
                            Application::executeJavaScript("$('.js-email').removeClass('hidden');");
                            break;
                        case 15:
                            Application::executeJavaScript("$('.js-website').removeClass('hidden');");
                            break;
                        case 16:
                            Application::executeJavaScript("$('.js-members-number').removeClass('hidden');");
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
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The new member has been successfully created and saved.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, creating or saving the new member failed!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Sorry!</strong> The member\'s name is required!');
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
        $this->dlgToastr8->Message = t('<strong>Well done!</strong> The order of members was successfully updated!');
        $this->dlgToastr8->ProgressBar = true;

        $this->dlgToastr9 = new Q\Plugin\Toastr($this);
        $this->dlgToastr9->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr9->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr9->Message = t('<strong>Sorry</strong>, updating the order of members failed!');
        $this->dlgToastr9->ProgressBar = true;

        $this->dlgToastr10 = new Q\Plugin\Toastr($this);
        $this->dlgToastr10->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr10->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr10->Message = t('<strong>Well done!</strong> The member was successfully deleted!');
        $this->dlgToastr10->ProgressBar = true;

        $this->dlgToastr11 = new Q\Plugin\Toastr($this);
        $this->dlgToastr11->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr11->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr11->Message = t('<strong>Sorry</strong>, the member deletion failed!');
        $this->dlgToastr11->ProgressBar = true;

        $this->dlgToastr12 = new Q\Plugin\Toastr($this);
        $this->dlgToastr12->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr12->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr12->Message = t('<strong>Sorry</strong>, the member name is required!');
        $this->dlgToastr12->ProgressBar = true;

        $this->dlgToastr13 = new Q\Plugin\Toastr($this);
        $this->dlgToastr13->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr13->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr13->Message = t('<strong>Well done!</strong> The member data has been successfully updated!');
        $this->dlgToastr13->ProgressBar = true;

        $this->dlgToastr14 = new Q\Plugin\Toastr($this);
        $this->dlgToastr14->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr14->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr14->Message = t('<strong>Sorry</strong>, updating the member data failed!');
        $this->dlgToastr14->ProgressBar = true;

        $this->dlgToastr15 = new Q\Plugin\Toastr($this);
        $this->dlgToastr15->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr15->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr15->Message = t('<strong>Well done!</strong> Updates to some records for this member were discarded, and the record has been restored!');
        $this->dlgToastr15->ProgressBar = true;
    }

    /**
     * Initializes multiple modal dialogs with various configurations, including text, title, header classes, buttons,
     * and actions. These modals provide feedback and confirmation requests for different user actions related to member
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
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this member group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this group public again later!</p>');
        $this->dlgModal3->Title = t('Question');
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addCloseButton(t("I'll cancel"));
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('statusItem_Click'));
        $this->dlgModal3->addAction(new Bs\Event\ModalHidden(), new Q\Action\Ajax('hideItem_Click'));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Success");
        $this->dlgModal4->HeaderClasses = 'btn-success';
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This member group is now hidden!</p>');
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This member group has now been made public!</p>');
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this member?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
        $this->dlgModal6->Title = 'Warning';
        $this->dlgModal6->HeaderClasses = 'btn-danger';
        $this->dlgModal6->addButton("I accept", null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal6->addCloseButton(t("I'll cancel"));
        $this->dlgModal6->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('deleteItem_Click'));

        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to disable the image upload for this member group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can re-enable the image upload for this group later!</p>');
        $this->dlgModal7->Title = t('Question');
        $this->dlgModal7->HeaderClasses = 'btn-danger';
        $this->dlgModal7->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal7->addCloseButton(t("I'll cancel"));
        $this->dlgModal7->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('imageUploadItem_Click'));
        $this->dlgModal7->addAction(new Bs\Event\ModalHidden(), new Q\Action\Ajax('canceltem_Click'));

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

        $this->dlgModal10 = new Bs\Modal($this);
        $this->dlgModal10->Title = t("Warning");
        $this->dlgModal10->HeaderClasses = 'btn-warning';
        $this->dlgModal10->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The entered field must contain only numbers!</p>');
        $this->dlgModal10->addCloseButton(t("I understand now"));
    }

    /**
     * Handles the key-up event for the registry code text input.
     *
     * @param ActionParams $params Parameters associated with the key-up action.
     * @return void
     */
    protected function txtRegistryCode_keyUp(ActionParams $params)
    {
        if ($this->txtMembersNumber->Text === '') {
            $this->dlgInfoBox1->removeCssClass('fadeIn');
            $this->dlgInfoBox1->addCssClass('fadeOut');
        } else if (filter_var($this->txtRegistryCode->Text, FILTER_VALIDATE_INT) === false) {
            $this->dlgInfoBox1->removeCssClass('fadeOut');
            $this->dlgInfoBox1->addCssClass('fadeIn');
        } else {
            $this->dlgInfoBox1->removeCssClass('fadeIn');
            $this->dlgInfoBox1->addCssClass('fadeOut');
        }
    }

    /**
     * Handles the key up event for the members number text box.
     *
     * @param ActionParams $params Parameters associated with the action triggering the key up event.
     * @return void
     */
    protected function txtMembersNumber_keyUp(ActionParams $params)
    {
        if ($this->txtMembersNumber->Text === '') {
            $this->dlgInfoBox2->removeCssClass('fadeIn');
            $this->dlgInfoBox2->addCssClass('fadeOut');

        } else if (filter_var($this->txtMembersNumber->Text, FILTER_VALIDATE_INT) === false) {
            $this->dlgInfoBox2->removeCssClass('fadeOut');
            $this->dlgInfoBox2->addCssClass('fadeIn');
        } else {
            $this->dlgInfoBox2->removeCssClass('fadeIn');
            $this->dlgInfoBox2->addCssClass('fadeOut');
        }
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

            $objSorter = Members::load($id);
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
            $('.member-setting-wrapper').addClass('hidden');
            $('.member-image-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
       "));

        $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objMembersSettings->save();

        $this->calPostUpdateDate->Text = $this->objMembersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

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
        $this->txtNewMemberName->Display = true;
        $this->btnMemberSave->Display = true;
        $this->btnMemberCancel->Display = true;
        $this->txtNewMemberName->Text = '';
        $this->txtNewMemberName->focus();

        Application::executeJavaScript(sprintf("
            $(\"[data-value='{$this->intClick}']\").removeClass('activated');
            $('.member-setting-wrapper').addClass('hidden');
            $('.member-image-wrapper').addClass('hidden');
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
        if (trim($this->txtNewMemberName->Text) !== '') {
            $objMember = new Members();
            $objMember->setMemberName(trim($this->txtNewMemberName->Text));
            $objMember->setMemberId($this->intId);
            $objMember->setMemberIdTitle($this->objMembersSettings->getName());
            $objMember->setOrder(Members::generateOrder($this->intId));
            $objMember->setStatus(2);
            $objMember->setPostDate(Q\QDateTime::Now());
            $objMember->save();

            // A check must be made here if the first record and the following records occur in this group,
            // then set "members_locked" to 1 in the MembersSettings column, etc...

            if (Members::countByMemberId($this->intId) !== 0) {
                if ($this->objMembersSettings->getMembersLocked() == 0) {
                    $this->objMembersSettings->setmembersLocked(1);
                }
            }

            $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
            $this->objMembersSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
            $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();

            if ($objMember->getId()) {
                $this->txtNewMemberName->Text = '';
                $this->btnAddMember->Enabled = true;
                $this->txtNewMemberName->Display = false;
                $this->btnMemberSave->Display = false;
                $this->btnMemberCancel->Display = false;

                $this->dlgToastr1->notify();
            } else {
                $this->dlgToastr2->notify();
            }

            $this->dlgSorter->refresh();

        } else {
            $this->txtNewMemberName->Text = '';
            $this->txtNewMemberName->focus();
            $this->btnAddMember->Enabled = false;
            $this->txtNewMemberName->Display = true;
            $this->btnMemberSave->Display = true;
            $this->btnMemberCancel->Display = true;

            $this->dlgToastr3->notify();
        }

        if ($this->objMembersSettings->getMembersLocked() === 1) {
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
        $this->txtNewMemberName->Display = false;
        $this->btnMemberSave->Display = false;
        $this->btnMemberCancel->Display = false;
        $this->txtNewMemberName->Text = '';
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
        $objEdit = Members::load($intEditId);
        $this->intClick = intval($intEditId);

        Application::executeJavaScript("$('.js-member-wrapper').get(0).scrollIntoView({behavior: 'smooth'});");

        if (!empty($this->objActiveInputs)) {
            foreach ($this->objActiveInputs as $objActiveInput) {
                if ($objActiveInput->ActivityStatus == 1) {
                    switch ($objActiveInput->InputKey) {
                        case 1:
                            $this->txtMemberName->Text = $objEdit->MemberName;
                            break;
                        case 2:
                            $this->txtRegistryCode->Text = $objEdit->RegistryCode;
                            break;
                        case 3:
                            $this->txtBankAccountNumber->Text = $objEdit->BankAccountNumber;
                            break;
                        case 4:
                            $this->txtRepresentativeFullName->Text = $objEdit->RepresentativeFullname;
                            break;
                        case 5:
                            $this->txtRepresentativeTelephone->Text = $objEdit->RepresentativeTelephone;
                            break;
                        case 6:
                            $this->txtRepresentativeSMS->Text = $objEdit->RepresentativeSms;
                            break;
                        case 7:
                            $this->txtRepresentativeFax->Text = $objEdit->RepresentativeFax;
                            break;
                        case 8:
                            $this->txtRepresentativeEmail->Text = $objEdit->RepresentativeEmail;
                            break;
                        case 9:
                            $this->txtDescription->Text = $objEdit->Description;
                            break;
                        case 10:
                            $this->txtTelephone->Text = $objEdit->Telephone;
                            break;
                        case 11:
                            $this->txtSMS->Text = $objEdit->Sms;
                            break;
                        case 12:
                            $this->txtFax->Text = $objEdit->Fax;
                            break;
                        case 13:
                            $this->txtAddress->Text = $objEdit->Address;
                            break;
                        case 14:
                            $this->txtEmail->Text = $objEdit->Email;
                            break;
                        case 15:
                            $this->txtWebsite->Text = $objEdit->Website;
                            break;
                        case 16:
                            $this->txtMembersNumber->Text = $objEdit->MembersNumber;
                            break;
                    }
                }
            }
        }

        $this->lstMemberStatus->SelectedValue = $objEdit->Status;
        $this->objMediaFinder->SelectedImageId = $objEdit->PictureId;

        if ($this->objMediaFinder->SelectedImageId !== null) {
            $objFiles = Files::loadById($this->objMediaFinder->SelectedImageId);

            if ($objFiles) {
                $this->objMediaFinder->SelectedImagePath = $this->objMediaFinder->TempUrl . $objFiles->getPath();
                $this->objMediaFinder->SelectedImageName = $objFiles->getName();
            }
        }

        if ($this->objMembersSettings->AllowedUploading === 1) {
            Application::executeJavaScript("
               $(\"[data-value='{$intEditId}']\").addClass('activated');
               $(\"[data-value='{$intEditId}']\").removeClass('inactivated');
               $('.member-setting-wrapper').removeClass('hidden');
               $('.member-image-wrapper').addClass('open');
               $('.member-image-wrapper').removeClass('hidden');
               $('.form-actions-wrapper').removeClass('hidden');
            ");
        } else {
            Application::executeJavaScript("
               $(\"[data-value='{$intEditId}']\").addClass('activated');
               $(\"[data-value='{$intEditId}']\").removeClass('inactivated');
               $('.member-setting-wrapper').removeClass('hidden');
               $('.member-image-wrapper').addClass('open');
               $('.member-image-wrapper').addClass('hidden');
               $('.form-actions-wrapper').removeClass('hidden');
            ");
        }

        $this->dlgSorter->refresh();
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
        if ($this->objMembersSettings->getAllowedUploading() === 1) {
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
            if ($('.member-image-wrapper').hasClass('open')) {
                $('.member-image-wrapper').addClass('hidden');
            } else {
                $('.member-image-wrapper').addClass('hidden');
            }
        "));

        $this->objMembersSettings->setAllowedUploading(2);
        $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

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
            if ($('.member-image-wrapper').hasClass('open')) {
                $('.member-image-wrapper').removeClass('hidden');
            } else {
                $('.member-image-wrapper').addClass('hidden');
            }
        "));

        $this->objMembersSettings->setAllowedUploading(1);
        $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    /**
     * Method to handle the cancel button click event.
     *
     * @param ActionParams $params The parameters passed during the action event.
     * @return void
     */
    protected function canceltem_Click(ActionParams $params)
    {
        $this->lstImageUpload->SelectedValue = $this->objMembersSettings->getAllowedUploading();
    }

    /**
     * Handles the push event for saving an image to a member.
     *
     * @param ActionParams $params The parameters provided by the action event.
     * @return void
     */
    protected function imageSave_Push(ActionParams $params)
    {
        $saveId = $this->objMediaFinder->Item;
        $objFilePath = Files::loadById($saveId);
        $objMember = Members::loadById($this->intClick);

        if ($objFilePath->getLockedFile() == 0) {
            $objFilePath->setLockedFile($objFilePath->getLockedFile() + 1);
            $objFilePath->save();
        }

        $objMember->setPictureId($saveId);
        $objMember->setFileId($saveId);
        $objMember->setPostUpdateDate(Q\QDateTime::Now());
        $objMember->save();

        $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        if ($objMember->getPictureId() !== null && $objMember->getFileId() !== null) {
            $this->dlgToastr4->notify();
        } else {
            $this->dlgToastr5->notify();
        }
    }

    /**
     * Deletes the image associated with a member and updates members settings accordingly.
     *
     * @param ActionParams $params The parameters provided by the action event.
     * @return void
     */
    protected function imageDelete_Push(ActionParams $params)
    {
        $objMember = Members::loadById($this->intClick);
        $objFiles = Files::loadById($objMember->getPictureId());

        if ($objFiles->getLockedFile() !== 0) {
            $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
            $objFiles->save();
        }

        $objMember->setPictureId(null);
        $objMember->setFileId(null);
        $objMember->setPostUpdateDate(Q\QDateTime::Now());
        $objMember->save();

        $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        if ($objMember->getPictureId() == null && $objMember->getFileId() == null) {
            $this->dlgToastr6->notify();
        } else {
            $this->dlgToastr7->notify();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function lstMemberStatus_Change(ActionParams $params)
    {
        $objMember = Members::loadById($this->intClick);

        $objMember->setStatus($this->lstMemberStatus->SelectedValue);
        $objMember->save();

        $this->lstMemberStatus->SelectedValue = $objMember->getStatus();
        $this->lstMemberStatus->refresh();

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
        } else if ($this->objMembersSettings->getStatus() === 1) {
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

        $this->objMembersSettings->setStatus(2);
        $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $objMenuContent->setIsEnabled(2);
        $objMenuContent->save();

        $this->refreshDisplay();
        $this->dlgModal4->showDialogBox();
    }

    /**
     * Handles the click event for hiding an item and sets the selected value of the status list.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function hideItem_Click(ActionParams $params)
    {
        $this->lstStatus->SelectedValue = $this->objMembersSettings->getStatus();
    }

    /**
     * Updates input fields with the current status from the board settings.
     *
     * @return void
     */
    protected function updateInputFields()
    {
        $this->lstStatus->SelectedValue = $this->objMembersSettings->getStatus();
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

        $this->objMembersSettings->setStatus(1);
        $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

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
        $objUpdate = Members::load($this->intClick);

        // Check if $objUpdate is available
        if (!$objUpdate) {
            $this->dlgToastr14->notify();
            return;
        }

        // Check if MemberName is empty
        if ($this->txtMemberName->Text == '') {
            $this->dlgToastr12->notify();
            return;
        }

        if (!empty($this->objActiveInputs)) {
            foreach ($this->objActiveInputs as $objActiveInput) {
                if ($objActiveInput->ActivityStatus == 1) {
                    switch ($objActiveInput->InputKey) {
                        case 1:
                            $objUpdate->MemberName = trim($this->txtMemberName->Text);
                            break;
                        case 2:

                            if (filter_var($this->txtRegistryCode->Text, FILTER_VALIDATE_INT) === false) {
                                $objUpdate->RegistryCode = '';
                            } else {
                                $objUpdate->RegistryCode = trim($this->txtRegistryCode->Text);
                            }
                            break;
                        case 3:
                            $objUpdate->BankAccountNumber = trim($this->txtBankAccountNumber->Text);
                            break;
                        case 4:
                            $objUpdate->RepresentativeFullname = trim($this->txtRepresentativeFullName->Text);
                            break;
                        case 5:
                            $objUpdate->RepresentativeTelephone = trim($this->txtRepresentativeTelephone->Text);
                            break;
                        case 6:
                            $objUpdate->RepresentativeSms = trim($this->txtRepresentativeSMS->Text);
                            break;
                        case 7:
                            $objUpdate->RepresentativeFax = trim($this->txtRepresentativeFax->Text);
                            break;
                        case 8:
                            $objUpdate->RepresentativeEmail = trim($this->txtRepresentativeEmail->Text);
                            break;
                        case 9:
                            $objUpdate->Description = $this->txtDescription->Text;
                            break;
                        case 10:
                            $objUpdate->Telephone = trim($this->txtTelephone->Text);
                            break;
                        case 11:
                            $objUpdate->Sms = trim($this->txtSMS->Text);
                            break;
                        case 12:
                            $objUpdate->Fax = trim($this->txtFax->Text);
                            break;
                        case 13:
                            $objUpdate->Address = $this->txtAddress->Text;
                            break;
                        case 14:
                            $objUpdate->Email = trim($this->txtEmail->Text);
                            break;
                        case 15:
                            $objUpdate->Website = trim($this->txtWebsite->Text);
                            break;
                        case 16:
                            if (filter_var($this->txtMembersNumber->Text, FILTER_VALIDATE_INT) === false) {
                                $objUpdate->MembersNumber = '';
                            } else {
                                $objUpdate->MembersNumber = trim($this->txtMembersNumber->Text);
                            }
                            break;
                    }
                }
            }
        }

        $objUpdate->Status = $this->lstMemberStatus->SelectedValue;
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
        $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
        $this->dlgSorter->refresh();

        Application::executeJavaScript(sprintf("
            $(\"[data-value='%s']\").addClass('activated');
            //$('.member-setting-wrapper').addClass('hidden');  
            ", $this->intClick)
        );
    }

    /**
     * Handles the escape click event for an item.
     *
     * @param ActionParams $params Parameters associated with the action triggering the event.
     * @return void
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        $objCancel = Members::load($this->intClick);

        // Check if $objCancel is available
        if ($objCancel) {
            $this->dlgToastr15->notify();
        }

        if (!empty($this->objActiveInputs)) {
            foreach ($this->objActiveInputs as $objActiveInput) {
                if ($objActiveInput->ActivityStatus == 1) {
                    switch ($objActiveInput->InputKey) {
                        case 1:
                            $this->txtMemberName->Text = $objCancel->MemberName;
                            break;
                        case 2:
                            $this->txtRegistryCode->Text = $objCancel->RegistryCode;
                            break;
                        case 3:
                            $this->txtBankAccountNumber->Text = $objCancel->BankAccountNumber;
                            break;
                        case 4:
                            $this->txtRepresentativeFullName->Text = $objCancel->RepresentativeFullname;
                            break;
                        case 5:
                            $this->txtRepresentativeTelephone->Text = $objCancel->RepresentativeTelephone;
                            break;
                        case 6:
                            $this->txtRepresentativeSMS->Text = $objCancel->RepresentativeSms;
                            break;
                        case 7:
                            $this->txtRepresentativeFax->Text = $objCancel->RepresentativeFax;
                            break;
                        case 8:
                            $this->txtRepresentativeEmail->Text = $objCancel->RepresentativeEmail;
                            break;
                        case 9:
                            $this->txtDescription->Text = $objCancel->Description;
                            break;
                        case 10:
                            $this->txtTelephone->Text = $objCancel->Telephone;
                            break;
                        case 11:
                            $this->txtSMS->Text = $objCancel->Sms;
                            break;
                        case 12:
                            $this->txtFax->Text = $objCancel->Fax;
                            break;
                        case 13:
                            $this->txtAddress->Text = $objCancel->Address;
                            break;
                        case 14:
                            $this->txtEmail->Text = $objCancel->Email;
                            break;
                        case 15:
                            $this->txtWebsite->Text = $objCancel->Website;
                            break;
                        case 16:
                            $this->txtMembersNumber->Text = $objCancel->MembersNumber;
                            break;
                    }
                }
            }
        }

        $this->lstMemberStatus->SelectedValue = $objCancel->Status;
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
        $objMember = Members::loadById($this->intClick);

        if (Members::countByMemberId($this->intId) == 1) {
            if ($this->objMembersSettings->getMembersLocked() == 1) {
                $this->objMembersSettings->setMembersLocked(0);
            }
        }

        if ($objMember->getFileId() !== null) {
            $objFile = Files::loadById($objMember->getFileId());
            $objFile->setLockedFile($objFile->getLockedFile() - 1);
            $objFile->save();
        }

        $objMember->delete();

        if ($objMember->getId() !== $objMember) {
            $this->dlgToastr10->notify();
        } else {
            $this->dlgToastr11->notify();
        }

        $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objMembersSettings->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
        $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

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
        if ($this->objMembersSettings->AllowedUploading === 1) {
            Application::executeJavaScript(sprintf("
                $(\"[data-value='{$this->intClick}']\").removeClass('activated');
                $('.member-setting-wrapper').addClass('hidden');
                $('.member-image-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
            "));
        }
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
     * Redirects the application to the members list page.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        Application::redirect('members_list.php');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Refreshes the display based on the settings of the member.
     * Updates the visibility of the post date, post update date, author,
     * and users as editors fields, and adjusts their CSS classes accordingly.
     *
     * @return void
     */
    protected function refreshDisplay()
    {
        if ($this->objMembersSettings->getPostDate() &&
            !$this->objMembersSettings->getPostUpdateDate() &&
            $this->objMembersSettings->getAuthor() &&
            !$this->objMembersSettings->countUsersAsMembersEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = false;
            $this->txtAuthor->Display = false;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }

        if ($this->objMembersSettings->getPostDate() &&
            $this->objMembersSettings->getPostUpdateDate() &&
            $this->objMembersSettings->getAuthor() &&
            !$this->objMembersSettings->countUsersAsMembersEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }

        if ($this->objMembersSettings->getPostDate() &&
            $this->objMembersSettings->getPostUpdateDate() &&
            $this->objMembersSettings->getAuthor() &&
            $this->objMembersSettings->countUsersAsMembersEditors()) {
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