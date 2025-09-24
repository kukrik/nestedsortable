<?php
    require('qcubed.inc.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Event\KeyUp;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Event\DialogButton;
    use QCubed\Jqui\Event\SortableStop;
    use QCubed\Action\Ajax;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Query\QQ;

    /**
     * Handles the editing functionality for members in the application.
     *
     * This form provides a user interface for managing member fields, managing inputs, creating buttons,
     * handling modal dialogs, toastr notifications, and implementing sorting features. It accommodates
     * various member-related attributes such as names, contact information, and settings while providing
     * methods to handle their dynamics, visibility, and presentation.
     */
    class MembersEditForm extends Form
    {
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
        protected Q\Plugin\Toastr $dlgToastr15;


        protected Q\Plugin\Control\InfoBox $dlgInfoBox1;
        protected Q\Plugin\Control\InfoBox $dlgInfoBox2;

        protected Q\Plugin\Control\Alert $lblInfo;
        protected Bs\Button $btnAddMember;
        protected Bs\TextBox $txtNewMemberName;
        protected Bs\Button $btnMemberSave;
        protected Bs\Button $btnMemberCancel;
        protected Q\Plugin\Control\Label $lblTitleSlug;
        protected Q\Plugin\Control\Label $txtTitleSlug;

        protected Q\Plugin\Control\SlideWrapper $dlgSorter;
        protected Q\Plugin\MediaFinder $objMediaFinder;

        protected Bs\TextBox $txtMemberName;
        protected Q\Plugin\Control\Label $lblMemberName;

        protected Bs\TextBox $txtRegistryCode;
        protected Q\Plugin\Control\Label $lblRegistryCode;

        protected Bs\TextBox $txtBankAccountNumber;
        protected Q\Plugin\Control\Label $lblBankAccountNumber;


        protected Bs\TextBox $txtRepresentativeFullName;
        protected Q\Plugin\Control\Label $lblRepresentativeFullName;
        protected Bs\TextBox $txtRepresentativeTelephone;
        protected Q\Plugin\Control\Label $lblRepresentativeTelephone;
        protected Bs\TextBox $txtRepresentativeSMS;
        protected Q\Plugin\Control\Label $lblRepresentativeSMS;
        protected Bs\TextBox $txtRepresentativeFax;
        protected Q\Plugin\Control\Label $lblRepresentativeFax;
        protected Bs\TextBox $txtRepresentativeEmail;
        protected Q\Plugin\Control\Label $lblRepresentativeEmail;
        protected Bs\TextBox $txtDescription;
        protected Q\Plugin\Control\Label $lblDescription;
        protected Bs\TextBox $txtTelephone;
        protected Q\Plugin\Control\Label $lblTelephone;
        protected Bs\TextBox $txtSMS;
        protected Q\Plugin\Control\Label $lblSMS;
        protected Bs\TextBox $txtFax;
        protected Q\Plugin\Control\Label $lblFax;
        protected Bs\TextBox $txtAddress;
        protected Q\Plugin\Control\Label $lblAddress;
        protected Bs\TextBox $txtEmail;
        protected Q\Plugin\Control\Label $lblEmail;
        protected Bs\TextBox $txtWebsite;
        protected Q\Plugin\Control\Label $lblWebsite;
        
        protected Bs\TextBox $txtMembersNumber;
        protected Q\Plugin\Control\Label $lblMembersNumber;

        protected Q\Plugin\RadioList $lstMemberStatus;
        protected Q\Plugin\Control\Label $lblMemberStatus;

        protected Bs\Button $btnUpdate;
        protected Bs\Button $btnCloseWindow;
        protected Bs\Button $btnBack;

        protected Q\Plugin\Control\Label $lblGroupTitle;
        protected Q\Plugin\Control\Label $lblPostDate;
        protected Bs\Label $calPostDate;
        protected Q\Plugin\Control\Label $lblPostUpdateDate;
        protected Bs\Label $calPostUpdateDate;
        protected Q\Plugin\Control\Label $lblAuthor;
        protected Bs\Label $txtAuthor;
        protected Q\Plugin\Control\Label $lblUsersAsEditors;
        protected Bs\Label $txtUsersAsEditors;
        protected Q\Plugin\Control\Label $lblStatus;
        protected Q\Plugin\Control\RadioList $lstStatus;
        protected Q\Plugin\Control\Label $lblImageUpload;
        protected Q\Plugin\Control\RadioList $lstImageUpload;

        protected int $intId;
        protected int $intGroup;
        protected int $intLoggedUserId;
        protected ?int $intClick = null;
        protected ?object $objMember = null;
        protected object $objMenu;
        protected object $objMembersSettings;
        protected ?array $objActiveInputs = null;

        protected string $strRootPath = APP_UPLOADS_DIR;
        protected string $strTempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
        protected string $strDateTimeFormat = 'd.m.Y H:i';

        /**
         * Sets up the form and initializes all necessary components.
         *
         * This method is responsible for creating and initializing form elements,
         * retrieving context-specific data such as query string parameters,
         * and setting up the application's state for the form. It also manages
         * initialization of user session data and other dependencies.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->intId = Application::instance()->context()->queryStringItem('id');
            $this->intGroup = Application::instance()->context()->queryStringItem('group');
            if (!empty($this->intId)) {
                $this->objMember = Members::loadByIdFromMemberId($this->intId);
                $this->objMembersSettings = MembersSettings::load($this->intId);
                $this->objMenu = Menu::load($this->intGroup);
            }
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
         * @throws Caller
         */
        protected function resettingInputs(): void
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
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createInputs(): void
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
            $this->txtNewMemberName->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
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
            $this->objMediaFinder->PopupUrl = dirname(QCUBED_FILEMANAGER_ASSETS_URL) . "/examples/finder.php";
            $this->objMediaFinder->EmptyImageAlt = t("Choose a picture");
            $this->objMediaFinder->SelectedImageAlt = t("Selected picture");
            $this->objMediaFinder->EmptyImagePath = QCUBED_NESTEDSORTABLE_ASSETS_URL . '/images/empty-member-icon.png';
            $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageSave(), new Ajax( 'imageSave_Push'));
            $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageDelete(), new Ajax('imageDelete_Push'));

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
            $this->lstStatus->addAction(new Change(), new Ajax('lstStatus_Change'));

            $this->lblImageUpload = new Q\Plugin\Control\Label($this);
            $this->lblImageUpload->Text = t('Image upload');
            $this->lblImageUpload->setCssStyle('margin-bottom', '-10px');
            $this->lblImageUpload->setCssStyle('font-weight', 'bold');

            $this->lstImageUpload = new Q\Plugin\Control\RadioList($this);
            $this->lstImageUpload->addItems([1 => t('Active'), 2 => t('Inactive')]);
            $this->lstImageUpload->ButtonGroupClass = 'radio radio-orange';
            $this->lstImageUpload->SelectedValue = $this->objMembersSettings->AllowedUploading;
            $this->lstImageUpload->setCssStyle('margin-top', '-10px');
            $this->lstImageUpload->addAction(new Change(), new Ajax('lstImageUpload_Change'));
        }

        /**
         * Creates and initializes various buttons for the UI component with their respective styles, text, and actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createButtons(): void
        {
            $this->btnAddMember = new Bs\Button($this);
            $this->btnAddMember->Text = t(' Add member');
            $this->btnAddMember->CssClass = 'btn btn-orange';
            $this->btnAddMember->setCssStyle('float', 'left');
            $this->btnAddMember->setCssStyle('margin-right', '10px');
            $this->btnAddMember->CausesValidation = false;
            $this->btnAddMember->addAction(new Click(), new Ajax('btnAddMember_Click'));

            $this->btnMemberSave = new Bs\Button($this);
            $this->btnMemberSave->Text = t('Save');
            $this->btnMemberSave->CssClass = 'btn btn-orange';
            $this->btnMemberSave->setCssStyle('float', 'left');
            $this->btnMemberSave->setCssStyle('margin-left', '10px');
            $this->btnMemberSave->setCssStyle('margin-right', '10px');
            $this->btnMemberSave->Display = false;
            $this->btnMemberSave->addAction(new Click(), new Ajax('btnMemberSave_Click'));

            $this->btnMemberCancel = new Bs\Button($this);
            $this->btnMemberCancel->Text = t('Cancel');
            $this->btnMemberCancel->CssClass = 'btn btn-default';
            $this->btnMemberCancel->setCssStyle('float', 'left');
            $this->btnMemberCancel->CausesValidation = false;
            $this->btnMemberCancel->Display = false;
            $this->btnMemberCancel->addAction(new Click(), new Ajax('btnMemberCancel_Click'));

            //////////////////////////////////////////////////////////////////////////////////////////

            $this->btnUpdate = new Bs\Button($this);
            $this->btnUpdate->Text = t('Update');
            $this->btnUpdate->CssClass = 'btn btn-orange';
            $this->btnUpdate->addAction(new Click(), new Ajax('btnUpdate_Click'));

            $this->btnCloseWindow = new Bs\Button($this);
            $this->btnCloseWindow->Text = t('Close the window');
            $this->btnCloseWindow->CssClass = 'btn btn-default';
            $this->btnCloseWindow->CausesValidation = false;
            $this->btnCloseWindow->addAction(new Click(), new Ajax('btnCloseWindow_Click'));

            //////////////////////////////////////////////////////////////////////////////////////////

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->setCssStyle('margin-left', '10px');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new Ajax('btnBack_Click'));
        }

        /**
         * Initializes a sortable component with configurations for rendering, data binding, and event handling.
         *
         * @return void
         * @throws Caller
         */
        protected function createSorter(): void
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
            $this->dlgSorter->addAction(new SortableStop(), new Ajax('sortable_stop'));
        }

        /**
         * Binds data to the sorter dialog's data source for the specified member.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function Sorter_Bind(): void
        {
            $this->dlgSorter->DataSource = Members::QueryArray(
                QQ::Equal(QQN::Members()->MemberId, $this->intId),
                QQ::Clause(
                    QQ::orderBy(QQN::Members()->Order)
                )
            );
        }

        /**
         * Returns sorting information for the given members object.
         *
         * @param Members $objMember The member object for which sorting information is being retrieved.
         *
         * @return array An associative array containing sorting details of the board.
         * @throws Caller
         * @throws InvalidCast
         */
        public function Sorter_Draw(Members $objMember): array
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
         *
         * @return string Rendered HTML for the edit and delete buttons.
         * @throws Caller
         */
        public function Buttons_Draw(Members $objMember): string
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
                $btnEdit->addAction(new Click(), new Ajax('btnEdit_Click'));
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
                $btnDelete->addAction(new Click(), new Ajax('btnDelete_Click'));
            }

            return $btnEdit->render(false) . $btnDelete->render(false);
        }

        /**
         * Initializes and configures the input manager by querying the active member options,
         * creating the necessary input fields and labels, and setting up their properties.
         * The inputs are sorted based on an 'Order' field, and certain elements are displayed
         * based on their activity status.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createInputManager(): void
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
            $this->txtMemberName->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtMemberName->addAction(new EnterKey(), new Terminate());
            $this->txtMemberName->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtMemberName->addAction(new EscapeKey(), new Terminate());

            $this->lblMemberName  = new Q\Plugin\Control\Label($this);
            $this->lblMemberName->Text = t('Member name');
            $this->lblMemberName->addCssClass('col-md-3 js-member-name');
            $this->lblMemberName->setCssStyle('font-weight', 'normal');
            $this->lblMemberName->Required = true;

            $this->txtRegistryCode = new Bs\TextBox($this);
            $this->txtRegistryCode->addCssClass('js-registry-code ');
            $this->txtRegistryCode->setHtmlAttribute('autocomplete', 'off');
            $this->txtRegistryCode->addAction(new KeyUp(), new Ajax('txtRegistryCode_keyUp'));
            $this->txtRegistryCode->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtRegistryCode->addAction(new EnterKey(), new Terminate());
            $this->txtRegistryCode->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtRegistryCode->addAction(new EscapeKey(), new Terminate());

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
            $this->txtBankAccountNumber->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtBankAccountNumber->addAction(new EnterKey(), new Terminate());
            $this->txtBankAccountNumber->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtBankAccountNumber->addAction(new EscapeKey(), new Terminate());

            $this->lblBankAccountNumber  = new Q\Plugin\Control\Label($this);
            $this->lblBankAccountNumber ->Text = t('Bank account number');
            $this->lblBankAccountNumber ->addCssClass('col-md-3 js-bank-account-number');
            $this->lblBankAccountNumber ->setCssStyle('font-weight', 'normal');

            $this->txtRepresentativeFullName = new Bs\TextBox($this);
            $this->txtRepresentativeFullName->addCssClass('js-representative-fullname');
            $this->txtRepresentativeFullName->setHtmlAttribute('autocomplete', 'off');
            $this->txtRepresentativeFullName->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtRepresentativeFullName->addAction(new EnterKey(), new Terminate());
            $this->txtRepresentativeFullName->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtRepresentativeFullName->addAction(new EscapeKey(), new Terminate());

            $this->lblRepresentativeFullName  = new Q\Plugin\Control\Label($this);
            $this->lblRepresentativeFullName->Text = t('Representative\'s full name');
            $this->lblRepresentativeFullName->addCssClass('col-md-3 js-representative-fullname');
            $this->lblRepresentativeFullName->setCssStyle('font-weight', 'normal');

            $this->txtRepresentativeTelephone = new Bs\TextBox($this);
            $this->txtRepresentativeTelephone->addCssClass('js-representative-telephone');
            $this->txtRepresentativeTelephone->setHtmlAttribute('autocomplete', 'off');
            $this->txtRepresentativeTelephone->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtRepresentativeTelephone->addAction(new EnterKey(), new Terminate());
            $this->txtRepresentativeTelephone->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtRepresentativeTelephone->addAction(new EscapeKey(), new Terminate());

            $this->lblRepresentativeTelephone = new Q\Plugin\Control\Label($this);
            $this->lblRepresentativeTelephone->Text = t('Representative\'s telephone');
            $this->lblRepresentativeTelephone->addCssClass('col-md-3 js-representative-telephone');
            $this->lblRepresentativeTelephone->setCssStyle('font-weight', 'normal');

            $this->txtRepresentativeSMS = new Bs\TextBox($this);
            $this->txtRepresentativeSMS->addCssClass('js-representative-sms');
            $this->txtRepresentativeSMS->setHtmlAttribute('autocomplete', 'off');
            $this->txtRepresentativeSMS->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtRepresentativeSMS->addAction(new EnterKey(), new Terminate());
            $this->txtRepresentativeSMS->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtRepresentativeSMS->addAction(new EscapeKey(), new Terminate());

            $this->lblRepresentativeSMS = new Q\Plugin\Control\Label($this);
            $this->lblRepresentativeSMS->Text = t('Representative\'s SMS');
            $this->lblRepresentativeSMS->addCssClass('col-md-3 js-representative-sms');
            $this->lblRepresentativeSMS->setCssStyle('font-weight', 'normal');

            $this->txtRepresentativeFax = new Bs\TextBox($this);
            $this->txtRepresentativeFax->addCssClass('js-representative-fax');
            $this->txtRepresentativeFax->setHtmlAttribute('autocomplete', 'off');
            $this->txtRepresentativeFax->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtRepresentativeFax->addAction(new EnterKey(), new Terminate());
            $this->txtRepresentativeFax->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtRepresentativeFax->addAction(new EscapeKey(), new Terminate());

            $this->lblRepresentativeFax = new Q\Plugin\Control\Label($this);
            $this->lblRepresentativeFax->Text = t('Representative\'s fax');
            $this->lblRepresentativeFax->addCssClass('col-md-3 js-representative-fax');
            $this->lblRepresentativeFax->setCssStyle('font-weight', 'normal');

            $this->txtRepresentativeEmail = new Bs\TextBox($this);
            $this->txtRepresentativeEmail->addCssClass('js-representative-email');
            $this->txtRepresentativeEmail->setHtmlAttribute('autocomplete', 'off');
            $this->txtRepresentativeEmail->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtRepresentativeEmail->addAction(new EnterKey(), new Terminate());
            $this->txtRepresentativeEmail->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtRepresentativeEmail->addAction(new EscapeKey(), new Terminate());

            $this->lblRepresentativeEmail = new Q\Plugin\Control\Label($this);
            $this->lblRepresentativeEmail->Text = t('Representative\'s email');
            $this->lblRepresentativeEmail->addCssClass('col-md-3 js-representative-email');
            $this->lblRepresentativeEmail->setCssStyle('font-weight', 'normal');

            $this->txtDescription = new Bs\TextBox($this);
            $this->txtDescription->addCssClass('js-description');
            $this->txtDescription->setHtmlAttribute('autocomplete', 'off');
            $this->txtDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtDescription->Rows = 2;
            $this->txtDescription->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtDescription->addAction(new EnterKey(), new Terminate());
            $this->txtDescription->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtDescription->addAction(new EscapeKey(), new Terminate());

            $this->lblDescription = new Q\Plugin\Control\Label($this);
            $this->lblDescription->Text = t('Description');
            $this->lblDescription->addCssClass('col-md-3 js-description');
            $this->lblDescription->setCssStyle('font-weight', 'normal');

            $this->txtTelephone = new Bs\TextBox($this);
            $this->txtTelephone->addCssClass('js-telephone');
            $this->txtTelephone->setHtmlAttribute('autocomplete', 'off');
            $this->txtTelephone->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtTelephone->addAction(new EnterKey(), new Terminate());
            $this->txtTelephone->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtTelephone->addAction(new EscapeKey(), new Terminate());

            $this->lblTelephone = new Q\Plugin\Control\Label($this);
            $this->lblTelephone->Text = t('Telephone');
            $this->lblTelephone->addCssClass('col-md-3 js-telephone');
            $this->lblTelephone->setCssStyle('font-weight', 'normal');

            $this->txtSMS = new Bs\TextBox($this);
            $this->txtSMS->addCssClass('js-sms');
            $this->txtSMS->setHtmlAttribute('autocomplete', 'off');
            $this->txtSMS->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtSMS->addAction(new EnterKey(), new Terminate());
            $this->txtSMS->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtSMS->addAction(new EscapeKey(), new Terminate());

            $this->lblSMS = new Q\Plugin\Control\Label($this);
            $this->lblSMS->Text = t('SMS');
            $this->lblSMS->addCssClass('col-md-3 js-sms');
            $this->lblSMS->setCssStyle('font-weight', 'normal');

            $this->txtFax = new Bs\TextBox($this);
            $this->txtFax->addCssClass('js-fax');
            $this->txtFax->setHtmlAttribute('autocomplete', 'off');
            $this->txtFax->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtFax->addAction(new EnterKey(), new Terminate());
            $this->txtFax->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtFax->addAction(new EscapeKey(), new Terminate());

            $this->lblFax = new Q\Plugin\Control\Label($this);
            $this->lblFax->Text = t('Fax');
            $this->lblFax->addCssClass('col-md-3 js-fax');
            $this->lblFax->setCssStyle('font-weight', 'normal');

            $this->txtAddress = new Bs\TextBox($this);
            $this->txtAddress->addCssClass('js-address');
            $this->txtAddress->setHtmlAttribute('autocomplete', 'off');
            $this->txtAddress->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtAddress->Rows = 2;
            $this->txtAddress->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtAddress->addAction(new EnterKey(), new Terminate());
            $this->txtAddress->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtAddress->addAction(new EscapeKey(), new Terminate());

            $this->lblAddress = new Q\Plugin\Control\Label($this);
            $this->lblAddress->Text = t('Address');
            $this->lblAddress->addCssClass('col-md-3 js-address');
            $this->lblAddress->setCssStyle('font-weight', 'normal');

            $this->txtEmail = new Bs\TextBox($this);
            $this->txtEmail->addCssClass('js-email');
            $this->txtEmail->setHtmlAttribute('autocomplete', 'off');
            $this->txtEmail->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtEmail->addAction(new EnterKey(), new Terminate());
            $this->txtEmail->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtEmail->addAction(new EscapeKey(), new Terminate());

            $this->lblEmail = new Q\Plugin\Control\Label($this);
            $this->lblEmail->Text = t('Email');
            $this->lblEmail->addCssClass('col-md-3 js-email');
            $this->lblEmail->setCssStyle('font-weight', 'normal');

            $this->txtWebsite = new Bs\TextBox($this);
            $this->txtWebsite->addCssClass('js-website');
            $this->txtWebsite->setHtmlAttribute('autocomplete', 'off');
            $this->txtWebsite->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtWebsite->addAction(new EnterKey(), new Terminate());
            $this->txtWebsite->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtWebsite->addAction(new EscapeKey(), new Terminate());

            $this->lblWebsite = new Q\Plugin\Control\Label($this);
            $this->lblWebsite->Text = t('Website');
            $this->lblWebsite->addCssClass('col-md-3 js-website');
            $this->lblWebsite->setCssStyle('font-weight', 'normal');

            $this->txtMembersNumber = new Bs\TextBox($this);
            $this->txtMembersNumber->addCssClass('js-members-number js-validate-popup');
            $this->txtMembersNumber->setHtmlAttribute('autocomplete', 'off');
            $this->txtMembersNumber->addAction(new KeyUp(), new Ajax('txtMembersNumber_keyUp'));
            $this->txtMembersNumber->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtMembersNumber->addAction(new EnterKey(), new Terminate());
            $this->txtMembersNumber->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtMembersNumber->addAction(new EscapeKey(), new Terminate());

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
            $this->lstMemberStatus->addAction(new Change(), new Ajax('lstMemberStatus_Change'));
            $this->lstMemberStatus->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->lstMemberStatus->addAction(new EnterKey(), new Terminate());
            $this->lstMemberStatus->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->lstMemberStatus->addAction(new EscapeKey(), new Terminate());

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
         * Initializes multiple Toastr notifications with various configurations for alert types, positions, and
         * messages.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The new member has been successfully created and saved.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('<strong>Sorry</strong>, creating or saving the new member failed!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Sorry!</strong> The member\'s name is required!');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('<strong>Well done!</strong> Successfully updated this image data!');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('<strong>Sorry!</strong> Failed to update this image data!');
            $this->dlgToastr5->ProgressBar = true;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('<strong>Sorry!</strong> Successfully deleted this image data!');
            $this->dlgToastr6->ProgressBar = true;

            $this->dlgToastr7 = new Q\Plugin\Toastr($this);
            $this->dlgToastr7->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr7->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr7->Message = t('<strong>Sorry!</strong> Deleting this image data failed.');
            $this->dlgToastr7->ProgressBar = true;

            $this->dlgToastr8 = new Q\Plugin\Toastr($this);
            $this->dlgToastr8->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr8->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr8->Message = t('<strong>Well done!</strong> The order of members was successfully updated!');
            $this->dlgToastr8->ProgressBar = true;

            $this->dlgToastr9 = new Q\Plugin\Toastr($this);
            $this->dlgToastr9->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr9->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr9->Message = t('<strong>Sorry</strong>, updating the order of members failed!');
            $this->dlgToastr9->ProgressBar = true;

            $this->dlgToastr10 = new Q\Plugin\Toastr($this);
            $this->dlgToastr10->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr10->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr10->Message = t('<strong>Well done!</strong> The member was successfully deleted!');
            $this->dlgToastr10->ProgressBar = true;

            $this->dlgToastr11 = new Q\Plugin\Toastr($this);
            $this->dlgToastr11->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr11->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr11->Message = t('<strong>Sorry</strong>, the member deletion failed!');
            $this->dlgToastr11->ProgressBar = true;

            $this->dlgToastr12 = new Q\Plugin\Toastr($this);
            $this->dlgToastr12->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr12->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr12->Message = t('<strong>Sorry</strong>, the member name is required!');
            $this->dlgToastr12->ProgressBar = true;

            $this->dlgToastr13 = new Q\Plugin\Toastr($this);
            $this->dlgToastr13->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr13->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr13->Message = t('<strong>Well done!</strong> The member data has been successfully updated!');
            $this->dlgToastr13->ProgressBar = true;

            $this->dlgToastr14 = new Q\Plugin\Toastr($this);
            $this->dlgToastr14->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr14->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr14->Message = t('<strong>Sorry</strong>, updating the member data failed!');
            $this->dlgToastr14->ProgressBar = true;

            $this->dlgToastr15 = new Q\Plugin\Toastr($this);
            $this->dlgToastr15->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr15->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the board group for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This member group is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This member group has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this member?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
            $this->dlgModal5->Title = 'Warning';
            $this->dlgModal5->HeaderClasses = 'btn-danger';
            $this->dlgModal5->addButton("I accept", null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal5->addCloseButton(t("I'll cancel"));
            $this->dlgModal5->addAction(new DialogButton(), new Ajax('deleteItem_Click'));

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Title = t("Success");
            $this->dlgModal6->HeaderClasses = 'btn-success';
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image upload is now disabled!</p>');
            $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal7 = new Bs\Modal($this);
            $this->dlgModal7->Title = t("Success");
            $this->dlgModal7->HeaderClasses = 'btn-success';
            $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image upload is now enabled!</p>');
            $this->dlgModal7->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);


            $this->dlgModal8 = new Bs\Modal($this);
            $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: 2px;"><strong>Note:</strong> Please add at least one member to the member group before making it public.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Alternatively, you can choose to delete the group.</p>');
            $this->dlgModal8->Title = t("Tip");
            $this->dlgModal8->HeaderClasses = 'btn-darkblue';
            $this->dlgModal8->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal9 = new Bs\Modal($this);
            $this->dlgModal9->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The organization\'s members have been selected, but all members are hidden, so there is no reason to make the organization\'s group public.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">The member group is now hidden.</p>');
            $this->dlgModal9->Title = t("Tip");
            $this->dlgModal9->HeaderClasses = 'btn-darkblue';
            $this->dlgModal9->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal10 = new Bs\Modal($this);
            $this->dlgModal10->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The organization\'s members have been selected, but all members are hidden, so there is no reason to make the organization\'s group public.</p>');
            $this->dlgModal10->Title = t("Tip");
            $this->dlgModal10->HeaderClasses = 'btn-darkblue';
            $this->dlgModal10->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        }

        /**
         * Handles the key-up event for the registry code text input.
         *
         * @param ActionParams $params Parameters associated with the key-up action.
         * @return void
         */
        protected function txtRegistryCode_keyUp(ActionParams $params): void
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
         * Handles the key up event for the number of members text box.
         *
         * @param ActionParams $params Parameters associated with the action triggering the key up event.
         * @return void
         */
        protected function txtMembersNumber_keyUp(ActionParams $params): void
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
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function sortable_stop(ActionParams $params): void
        {
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

            Application::executeJavaScript("
                $('.member-setting-wrapper').addClass('hidden');
                $('.member-image-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
           ");

            $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
            $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objMembersSettings->save();

            $this->calPostUpdateDate->Text = $this->objMembersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
        }

        /**
         * Handles the click event for the Add Member button.
         *
         * @param ActionParams $params The parameters provided by the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function btnAddMember_Click(ActionParams $params): void
        {
            $this->btnAddMember->Enabled = false;
            $this->txtNewMemberName->Display = true;
            $this->btnMemberSave->Display = true;
            $this->btnMemberCancel->Display = true;
            $this->txtNewMemberName->Text = '';
            $this->txtNewMemberName->focus();

            Application::executeJavaScript("
                $('.member-setting-wrapper').addClass('hidden');
                $('.member-image-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
           ");
        }

        /**
         * Handles the click event for the Save Member button.
         *
         * @param ActionParams $params The parameters provided by the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnMemberSave_Click(ActionParams $params): void
        {
            if (trim($this->txtNewMemberName->Text) !== '') {
                $objMember = new Members();
                $objMember->setMemberName(trim($this->txtNewMemberName->Text));
                $objMember->setMemberId($this->intId);
                $objMember->setMemberIdTitle($this->objMembersSettings->getName());
                $objMember->setOrder(Members::generateOrder($this->intId));
                $objMember->setStatus(2);
                $objMember->setPostDate(Q\QDateTime::now());
                $objMember->save();

                // A check must be made here if the first record and the following records occur in this group,
                // then set "members_locked" to 1 in the MembersSettings column, etc...

                if (Members::countByMemberId($this->intId) !== 0) {
                    if ($this->objMembersSettings->getMembersLocked() == 0) {
                        $this->objMembersSettings->setmembersLocked(1);
                    }
                }

                $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::now());
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

            $this->dlgSorter->refresh();
        }

        /**
         * Handles the click event for the Member Cancel button.
         *
         * @param ActionParams $params The parameters provided by the action event.
         * @return void
         */
        protected function btnMemberCancel_Click(ActionParams $params): void
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
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnEdit_Click(ActionParams $params): void
        {
            $intEditId = intval($params->ActionParameter);
            $objEdit = Members::load($intEditId);
            $this->intClick = $intEditId;

            Application::executeJavaScript("$('.js-member-wrapper').get(0).scrollIntoView({behavior: 'smooth'});");

            if (!empty($this->objActiveInputs)) {
                foreach ($this->objActiveInputs as $objActiveInput) {
                    if ($objActiveInput->ActivityStatus == 1) {
                        switch ($objActiveInput->InputKey) {
                            case 1:
                                $this->txtMemberName->Text = $objEdit->MemberName ?? '';
                                break;
                            case 2:
                                $this->txtRegistryCode->Text = $objEdit->RegistryCode ?? '';
                                break;
                            case 3:
                                $this->txtBankAccountNumber->Text = $objEdit->BankAccountNumber ?? '';
                                break;
                            case 4:
                                $this->txtRepresentativeFullName->Text = $objEdit->RepresentativeFullname ?? '';
                                break;
                            case 5:
                                $this->txtRepresentativeTelephone->Text = $objEdit->RepresentativeTelephone ?? '';
                                break;
                            case 6:
                                $this->txtRepresentativeSMS->Text = $objEdit->RepresentativeSms ?? '';
                                break;
                            case 7:
                                $this->txtRepresentativeFax->Text = $objEdit->RepresentativeFax ?? '';
                                break;
                            case 8:
                                $this->txtRepresentativeEmail->Text = $objEdit->RepresentativeEmail ?? '';
                                break;
                            case 9:
                                $this->txtDescription->Text = $objEdit->Description ?? '';
                                break;
                            case 10:
                                $this->txtTelephone->Text = $objEdit->Telephone ?? '';
                                break;
                            case 11:
                                $this->txtSMS->Text = $objEdit->Sms ?? '';
                                break;
                            case 12:
                                $this->txtFax->Text = $objEdit->Fax ?? '';
                                break;
                            case 13:
                                $this->txtAddress->Text = $objEdit->Address ?? '';
                                break;
                            case 14:
                                $this->txtEmail->Text = $objEdit->Email ?? '';
                                break;
                            case 15:
                                $this->txtWebsite->Text = $objEdit->Website ?? '';
                                break;
                            case 16:
                                $this->txtMembersNumber->Text = $objEdit->MembersNumber ?? '';
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

            if ($this->lstImageUpload->SelectedValue === '1') {
                Application::executeJavaScript("
                   $(\"[data-value='$intEditId']\").addClass('activated');
                   $(\"[data-value='$intEditId']\").removeClass('inactivated');
                   $('.member-setting-wrapper').removeClass('hidden');
                   $('.member-image-wrapper').removeClass('hidden');
                   $('.form-actions-wrapper').removeClass('hidden');
                ");
            }

            if ($this->lstImageUpload->SelectedValue === '2') {
                Application::executeJavaScript("
                   $(\"[data-value='$intEditId']\").addClass('activated');
                   $(\"[data-value='$intEditId']\").removeClass('inactivated');
                   $('.member-setting-wrapper').removeClass('hidden');
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
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstImageUpload_Change(ActionParams $params): void
        {
            $objMembersSettings = MembersSettings::loadById($this->intId);

            $objMembersSettings->setAllowedUploading($this->lstImageUpload->SelectedValue);
            $objMembersSettings->setPostUpdateDate(Q\QDateTime::now());
            $objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objMembersSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $objMembersSettings->getUserAsMembersEditorsArray());
            $this->calPostUpdateDate->Text = $objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            if ($objMembersSettings->getAllowedUploading() === 2) {
                $this->dlgModal6->showDialogBox();

            } else {
                $this->dlgModal7->showDialogBox();
            }

            if ($objMembersSettings->getAllowedUploading()) {
                Application::executeJavaScript("
                   $('.member-setting-wrapper').addClass('hidden');
                   $('.member-image-wrapper').addClass('hidden');
                   $('.form-actions-wrapper').addClass('hidden');
                ");
            }

            $this->refreshDisplay();

            $this->dlgSorter->refresh();
        }

        /**
         * Handles the push event for saving an image to a member.
         *
         * @param ActionParams $params The parameters provided by the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function imageSave_Push(ActionParams $params): void
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
            $objMember->setPostUpdateDate(Q\QDateTime::now());
            $objMember->save();

            $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::now());
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
         * Deletes the image associated with a member and updates member settings accordingly.
         *
         * @param ActionParams $params The parameters provided by the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function imageDelete_Push(ActionParams $params): void
        {
            $objMember = Members::loadById($this->intClick);
            $objFiles = Files::loadById($objMember->getPictureId());

            if ($objFiles->getLockedFile() !== 0) {
                $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                $objFiles->save();
            }

            $objMember->setPictureId(null);
            $objMember->setFileId(null);
            $objMember->setPostUpdateDate(Q\QDateTime::now());
            $objMember->save();

            $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::now());
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

        /**
         * Handles the change event for the member status list.
         *
         * @param ActionParams $params Parameters associated with the action triggering the change.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstMemberStatus_Change(ActionParams $params): void
        {
            $objMember = Members::loadById($this->intClick);
            $objMenuContent = MenuContent::loadById($this->objMenu->getId());

            $objMembers = Members:: loadArrayByMemberId($this->intId);
            $enabledStatus = 0;

            if ($this->lstMemberStatus->SelectedValue == 2) {
                foreach ($objMembers as $objStatus) {
                    if ($objStatus->Status == 1) {
                        $enabledStatus++;
                    }
                }

                if (($this->objMenu->ParentId == null) /*&& ($enabledStatus - 1) == 0*/) {
                    $objMember->setStatus($this->lstMemberStatus->SelectedValue);
                    $objMember->save();

                    $this->lstStatus->SelectedValue = 2;

                    $objMenuContent->setIsEnabled(2);
                    $objMenuContent->save();

                    $this->objMembersSettings->setStatus(2);
                    $this->objMembersSettings->save();

                    $this->dlgModal9->showDialogBox();
                    $this->dlgSorter->refresh();
                    return;
                }
            }

//            if ($this->objMenu->ParentId && ($enabledStatus) == 1) {
//                $this->dlgModal1->showDialogBox();
//                $this->lstMemberStatus->SelectedValue = $objMember->getStatus();
//                $this->dlgSorter->refresh();
//                return;
//            }

            $objMember->setStatus($this->lstMemberStatus->SelectedValue);
            $objMember->save();

            $this->dlgToastr13->notify();

            $this->updateStatus();
            $this->dlgSorter->refresh();
        }

        /**
         * Handles the change event triggered by the status dropdown selection.
         * Updates the menu and board settings based on the selected status.
         * Displays dialog boxes in various conditions to guide user actions.
         *
         * @param ActionParams $params The parameters associated with the action invoked.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            $objMenuContent = MenuContent::loadById($this->objMenu->getId());
            $objMembers = Members::loadArrayByMemberId($this->intId);
            $enabledStatus = 0;

            $this->updateStatus();

            foreach ($objMembers as $objStatus) {
                if ($objStatus->Status == 1) {
                    $enabledStatus++;
                }
            }

//            if (Members::countByMemberId($this->intId) === 0) {
//                $this->dlgModal8->showDialogBox();
//                $this->updateInputFields();
//                return;
//            }
//
//            if ($this->lstStatus->SelectedValue == 1 && $enabledStatus == 0) {
//                $this->dlgModal10->showDialogBox();
//                $this->updateInputFields();
//                return;
//            }

            if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->dlgModal1->showDialogBox();
                $this->updateInputFields();
                return;
            }

            $objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $objMenuContent->save();

            $this->objMembersSettings->setStatus($this->lstStatus->SelectedValue);
            $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objMembersSettings->save();

            if ($this->objMembersSettings->getStatus() === 2) {
                $this->dlgModal3->showDialogBox();
            } else {
                $this->dlgModal4->showDialogBox();
            }
        }

        /**
         * Updates the status of member settings by recording the current date, assigning the editor's name,
         * and saving the changes. Additionally, updates the display fields with the latest information and
         * refreshes the UI.
         *
         * @return void Does not return any value.
         */
        protected function updateStatus(): void
        {
            $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objMembersSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
            $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
        }

        /**
         * Updates input fields with the current status from the board settings.
         *
         * @return void
         */
        protected function updateInputFields(): void
        {
            $this->lstStatus->SelectedValue = $this->objMembersSettings->getStatus();
            $this->lstStatus->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the update button.
         *
         * @param ActionParams $params Parameters associated with the action triggering the click.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnUpdate_Click(ActionParams $params): void
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
            $objUpdate->PostUpdateDate = Q\QDateTime::now();

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
            $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objMembersSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
            $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
            $this->dlgSorter->refresh();

            Application::executeJavaScript("$(\"[data-value='%s']\").addClass('activated');", $this->intClick);
        }

        /**
         * Handles the escape click event for an item.
         *
         * @param ActionParams $params Parameters associated with the action triggering the event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function itemEscape_Click(ActionParams $params): void
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
        protected function btnDelete_Click(ActionParams $params): void
        {
            $this->intClick = intval($params->ActionParameter);
            $this->dlgModal5->showDialogBox();
        }

        /**
         * Handles the deletion of a board item when the delete button is clicked.
         *
         * @param ActionParams $params Parameters associated with the action triggering the delete event.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function deleteItem_Click(ActionParams $params): void
        {
            $this->dlgModal5->hideDialogBox();

            $objMember = Members::loadById($this->intClick);
            $objMenuContent = MenuContent::loadById($this->objMenu->getId());

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

            if ($this->objMembersSettings->getAllowedUploading()) {
                Application::executeJavaScript("
                   $('.member-setting-wrapper').addClass('hidden');
                   $('.member-image-wrapper').addClass('hidden');
                   $('.form-actions-wrapper').addClass('hidden');
                ");
            }

            if (Members::countByMemberId($this->intId) > 0) {
                $this->lblInfo->Display = false;
            } else {
                $this->lblInfo->Display = true;

                $this->lstStatus->SelectedValue = 2;

                $objMenuContent->setIsEnabled(2);
                $objMenuContent->save();

                $this->objMembersSettings->setStatus(2);
                $this->objMembersSettings->save();
            }

            if ($objMember->getId() !== $objMember) {
                $this->dlgToastr10->notify();
            } else {
                $this->dlgToastr11->notify();
            }

            $this->objMembersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objMembersSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objMembersSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objMembersSettings->getUserAsMembersEditorsArray());
            $this->calPostUpdateDate->Text = $this->objMembersSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();

            $this->dlgSorter->refresh();
        }

        /**
         * Handles the click event for the cancel button.
         *
         * @param ActionParams $params Parameters associated with the action triggering the click.
         *
         * @return void
         * @throws Caller
         */
        protected function btnCloseWindow_Click(ActionParams $params): void
        {
            if ($this->objMembersSettings->AllowedUploading === 1) {
                Application::executeJavaScript("
                    $(\"[data-value='$this->intClick']\").removeClass('activated');
                    $('.member-setting-wrapper').addClass('hidden');
                    $('.member-image-wrapper').addClass('hidden');
                    $('.form-actions-wrapper').addClass('hidden');
                ");
            }
        }

        /**
         * Handles the back button click event.
         *
         * @param ActionParams $params Parameters associated with the action triggering the back button click.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnBack_Click(ActionParams $params): void
        {

            $this->redirectToListPage();
        }

        /**
         * Redirects the application to the member list page.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            Application::redirect('members_list.php');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Refreshes the display based on the settings of the member.
         * Updates the visibility of the post-date, post-update date, author,
         * and users as editor fields, and adjusts their CSS classes accordingly.
         *
         * @return void
         */
        protected function refreshDisplay(): void
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
    MembersEditForm::run('MembersEditForm');