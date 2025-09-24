<?php
    require('qcubed.inc.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Control\ListItem;
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
    use QCubed\Html;

    /**
     * Class LinksEditForm
     *
     * This class provides methods and interface elements for creating and editing links, organizing them into categories, and applying other link-related settings.
     * The class extends a base form class and implements functionality such as modal dialogs, notifications, form inputs, link status management, and user access settings.
     */
    class LinksEditForm extends Form
    {
        protected ?object $objLinksSettingsCondition = null;
        protected ?array $objLinksSettingsClauses = null;

        protected Bs\Modal $dlgModal1;
        protected Bs\Modal $dlgModal2;
        protected Bs\Modal $dlgModal3;
        protected Bs\Modal $dlgModal4;
        protected Bs\Modal $dlgModal5;
        protected Bs\Modal $dlgModal6;

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

        protected Q\Plugin\Control\Label $lblGroupTitle;
        protected Q\Plugin\Control\Alert $lblInfo;
        protected Bs\Button $btnAddLink;
        protected Bs\TextBox $txtNewTitle;
        protected Bs\Button $btnLinkSave;
        protected Bs\Button $btnLinkCancel;
        protected Q\Plugin\Control\Label $lblTitleSlug;
        protected Q\Plugin\Control\Label $txtTitleSlug;

        protected Q\Plugin\Control\Label $lblTitle;
        protected Bs\TextBox $txtTitle;

        protected Q\Plugin\Control\Label $lblUrl;
        protected Bs\TextBox $txtUrl;

        protected Q\Plugin\Control\Label $lblDocumentLink;
        protected Bs\Button $btnDocumentLink;
        protected Q\Plugin\Control\Label $lblFileName;
        protected Bs\TextBox $txtHiddenDocument;
        protected Bs\Button $btnSaveFile;
        protected Bs\Button $btnCancelFile;

        protected Q\Plugin\Control\Label $lblCategory;
        protected Q\Plugin\Select2 $lstCategory;

        protected Q\Plugin\Control\Label $lblLinksGroupTitle;
        protected Q\Plugin\Select2 $lstGroupTitle;

        protected Q\Plugin\Control\Label $lblLinkStatus;
        protected Q\Plugin\Control\RadioList $lstLinkStatus;

        protected Q\Plugin\Control\SortWrapper $dlgSorter;

        protected Bs\Button $btnUpdate;
        protected Bs\Button $btnSelectedCheck;
        protected Bs\Button $btnSelectedDelete;
        protected Bs\Button $btnCloseWindow;
        protected Bs\Button $btnGoToCategory;
        protected Bs\Button $btnGoToSettings;
        protected Bs\Button $btnBack;

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

        protected int $intId;
        protected int $intGroup;
        protected int $intLoggedUserId;
        protected ?int $intClick = null;

        protected object $objMenu;
        protected ?object $objLink = null;
        protected object $objLinksSettings;
        protected int $countByIsReserved;
        protected int $countByLinkType;

        protected ?object $objCategoryCondition = null;
        protected ?array $objCategoryClauses = null;

        protected array $errors = []; // Array for tracking errors

        /**
         * Initializes the form by setting up necessary properties and creating form components.
         *
         * This method performs the following actions:
         * - Retrieves query string parameters to initialize object properties.
         * - Loads menu, link, and link settings based on the provided ID and group.
         * - Sets a hardcoded logged-in user ID for demonstration purposes.
         * - Calls various methods to initialize and render form components such as inputs, buttons, modals, and more.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->intId = Application::instance()->context()->queryStringItem('id');
            $this->intGroup = Application::instance()->context()->queryStringItem('group');
            if (!empty($this->intId)) {
                $this->objMenu = Menu::load($this->intGroup);
                $this->objLink = Links::loadByIdFromLinksId($this->intId);
                $this->objLinksSettings = LinksSettings::load($this->intId);
            }

            $this->countByIsReserved = LinksSettings::countByIsReserved(1);
            $this->countByLinkType = Links::countBySettingsId($this->intId);


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

            $this->popupViewer();
            $this->createInputs();
            $this->createButtons();
            $this->createSorter();
            $this->createToastr();
            $this->createModals();
            $this->refreshDisplay();
            $this->resettingInputs();
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
         * Resets the input elements by hiding specific UI components.
         *
         * @return void
         * @throws Caller
         */
        protected function resettingInputs(): void
        {
            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
                Application::executeJavaScript("
                    $('.link-setting-wrapper').removeClass('hidden');
                    $('.form-actions-wrapper').removeClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.link-setting-wrapper').addClass('hidden');
                    $('.form-actions-wrapper').addClass('hidden');
                ");
            }

            if ($this->objLinksSettings->getLinkType() === 1) {
                Application::executeJavaScript("
                    $('.js-url').removeClass('hidden');
                    $('.js-path').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.js-url').addClass('hidden');
                    $('.js-path').removeClass('hidden');
                ");

                if (!empty($_SESSION["links-title"])) {
                    $this->txtTitle->Text = $_SESSION["links-title"];
                }
            }
        }

        /**
         * Initializes and configures input fields, labels, and controls for the link creation and editing interface.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createInputs(): void
        {
            $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
            $this->lblGroupTitle->Text = $this->objLinksSettings->getName();
            $this->lblGroupTitle->setCssStyle('font-weight', 600);

            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('Please create the first link!');
            $this->lblInfo->setCssStyle('margin-bottom', 0);

            if ($this->objLinksSettings->getLinksLocked() === 1) {
                $this->lblInfo->Display = false;
            } else {
                $this->lblInfo->Display = true;
            }

            $this->txtNewTitle = new Bs\TextBox($this);
            $this->txtNewTitle->Placeholder = t('Link title');
            $this->txtNewTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtNewTitle->setCssStyle('float', 'left');
            $this->txtNewTitle->Width = '45%';
            $this->txtNewTitle->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtNewTitle->Display = false;

            $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
            $this->lblTitleSlug->Text = t('View: ');
            $this->lblTitleSlug->setCssStyle('font-weight', 'bold');

            if ($this->objLinksSettings->getTitleSlug()) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
                $this->txtTitleSlug->setCssStyle('text-align', 'left;');
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objLinksSettings->getTitleSlug();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            $this->lblTitle  = new Q\Plugin\Control\Label($this);
            $this->lblTitle->Text = t('Link title');
            $this->lblTitle->addCssClass('col-md-3');
            $this->lblTitle->setCssStyle('font-weight', 'normal');
            $this->lblTitle->Required = true;

            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Link title');
            $this->txtTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtTitle->setHtmlAttribute('required', 'required');
            $this->txtTitle->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtTitle->addAction(new EnterKey(), new Terminate());
            $this->txtTitle->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtTitle->addAction(new EscapeKey(), new Terminate());

            $this->lblUrl  = new Q\Plugin\Control\Label($this);
            $this->lblUrl->Text = t('Url');
            $this->lblUrl->addCssClass('col-md-3');
            $this->lblUrl->setCssStyle('font-weight', 'normal');
            $this->lblUrl->Required = true;

            $this->txtUrl = new Bs\TextBox($this);
            $this->txtUrl->Placeholder = t('Url');
            $this->txtUrl->setHtmlAttribute('autocomplete', 'off');
            $this->txtUrl->setHtmlAttribute('required', 'required');
            $this->txtUrl->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtUrl->addAction(new EnterKey(), new Terminate());
            $this->txtUrl->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtUrl->addAction(new EscapeKey(), new Terminate());

            $this->lblDocumentLink = new Q\Plugin\Control\Label($this);
            $this->lblDocumentLink->Text = t('Document link');
            $this->lblDocumentLink->addCssClass('col-md-3');
            $this->lblDocumentLink->setCssStyle('font-weight', 400);
            $this->lblDocumentLink->Required = true;

            $this->lblFileName = new Q\Plugin\Control\Label($this);
            $this->lblFileName->setCssStyle('float', 'left');
            $this->lblFileName->setCssStyle('font-weight', 400);

            $this->txtHiddenDocument = new Bs\TextBox($this);
            $this->txtHiddenDocument->Display = false;
            $this->txtHiddenDocument->setDataAttribute('view', '');
            $this->txtHiddenDocument->setDataAttribute('open', 'false');

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

            if (!empty($this->objLink->CategoryId)) {
                $this->lstCategory->SelectedValue = $this->objLink->CategoryId ?? null;
            }

            $this->lstCategory->AddAction(new Change(), new Ajax('lstCategory_Change'));

            if (LinksCategory::countAll() == 0 || LinksCategory::countAll() == LinksCategory::countByStatus(2)) {
                $this->lstCategory->Enabled = false;
            } else {
                $this->lstCategory->Enabled = true;
            }

            $this->lblLinksGroupTitle = new Q\Plugin\Control\Label($this);
            $this->lblLinksGroupTitle->Text = t('Links group');
            $this->lblLinksGroupTitle->addCssClass('col-md-3');
            $this->lblLinksGroupTitle->setCssStyle('font-weight', 400);

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->ContainerWidth = 'resolve';
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '90%';
            $this->lstGroupTitle->setCssStyle('float', 'left');
            $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroupTitle->addItem(t('- Change links group -'), null, true);
            $this->lstGroupTitle->SelectedValue = $this->intId;
            $this->lstGroupTitle->addItems($this->lstLinksSettings_GetItems());
            $this->lstGroupTitle->addAction(new Change(), new Ajax('lstGroupTitle_Change'));

            if ($this->countByIsReserved === 1) {
                $this->lstGroupTitle->Enabled = false;
            } else {
                $this->lstGroupTitle->Enabled = true;
            }

            $this->lblLinkStatus = new Q\Plugin\Control\Label($this);
            $this->lblLinkStatus->Text = t('Status');
            $this->lblLinkStatus->addCssClass('col-md-3');
            $this->lblLinkStatus->setCssStyle('font-weight', 'normal');

            $this->lstLinkStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstLinkStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstLinkStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
            $this->lstLinkStatus->setCssStyle('margin-top', '-11px');
            $this->lstLinkStatus->addAction(new Change(), new Ajax('lstLinkStatus_Change'));

            if (!empty($_SESSION["links-data"] )) {
                $objLinks = $_SESSION["links-data"];

                $objLink = Links::load($objLinks->getId());
                $this->lstLinkStatus->SelectedValue = $objLink->getStatus();
                $this->lstLinkStatus->refresh();
            }

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblPostDate = new Q\Plugin\Control\Label($this);
            $this->lblPostDate->Text = t('Created');
            $this->lblPostDate->setCssStyle('font-weight', 'bold');

            $this->calPostDate = new Bs\Label($this);
            $this->calPostDate->Text = $this->objLinksSettings->PostDate ? $this->objLinksSettings->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostDate->setCssStyle('font-weight', 'normal');

            $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
            $this->lblPostUpdateDate->Text = t('Updated');
            $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

            $this->calPostUpdateDate = new Bs\Label($this);
            $this->calPostUpdateDate->Text = $this->objLinksSettings->PostUpdateDate ? $this->objLinksSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

            $this->lblAuthor = new Q\Plugin\Control\Label($this);
            $this->lblAuthor->Text = t('Author');
            $this->lblAuthor->setCssStyle('font-weight', 'bold');

            $this->txtAuthor  = new Bs\Label($this);
            $this->txtAuthor->Text = $this->objLinksSettings->Author;
            $this->txtAuthor->setCssStyle('font-weight', 'normal');

            $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
            $this->lblUsersAsEditors->Text = t('Editors');
            $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

            $this->txtUsersAsEditors  = new Bs\Label($this);
            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->setCssStyle('font-weight', 'bold');

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstStatus->SelectedValue = $this->objLinksSettings->Status;
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
            $this->lstStatus->Enabled = true;
            $this->lstStatus->addAction(new Change(), new Ajax('lstStatus_Change'));
        }

        /**
         * Creates and initializes various buttons used in the interface,
         * including defining their styles, properties, and event handlers for interactions.
         *
         * @return void
         * @throws Caller
         */
        protected function createButtons(): void
        {
            $this->btnAddLink = new Bs\Button($this);
            $this->btnAddLink->Text = t('Add a link');
            $this->btnAddLink->CssClass = 'btn btn-orange';
            $this->btnAddLink->setCssStyle('float', 'left');
            $this->btnAddLink->setCssStyle('margin-right', '10px');
            $this->btnAddLink->CausesValidation = false;
            $this->btnAddLink->addAction(new Click(), new Ajax('btnAddLink_Click'));

            $this->btnLinkSave = new Bs\Button($this);
            $this->btnLinkSave->Text = t('Save');
            $this->btnLinkSave->CssClass = 'btn btn-orange';
            $this->btnLinkSave->setCssStyle('float', 'left');
            $this->btnLinkSave->setCssStyle('margin-left', '10px');
            $this->btnLinkSave->setCssStyle('margin-right', '10px');
            $this->btnLinkSave->Display = false;
            $this->btnLinkSave->addAction(new Click(), new Ajax('btnLinkSave_Click'));

            $this->btnLinkCancel = new Bs\Button($this);
            $this->btnLinkCancel->Text = t('Cancel');
            $this->btnLinkCancel->CssClass = 'btn btn-default';
            $this->btnLinkCancel->setCssStyle('float', 'left');
            $this->btnLinkCancel->CausesValidation = false;
            $this->btnLinkCancel->Display = false;
            $this->btnLinkCancel->addAction(new Click(), new Ajax('btnLinkCancel_Click'));

            //////////////////////////////////////////////////////////////////////////////////////////

            $this->btnDocumentLink = new Bs\Button($this);
            $this->btnDocumentLink->Text = t('Search file...');
            $this->btnDocumentLink->CssClass = 'btn btn-default';
            $this->btnDocumentLink->addWrapperCssClass('center-button');
            $this->btnDocumentLink->setCssStyle('float', 'left');
            $this->btnDocumentLink->CausesValidation = false;
            $this->btnDocumentLink->setDataAttribute('popup', 'popup');
            $this->btnDocumentLink->addAction(new Click(), new Ajax('btnDocumentLink_Click'));

            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
                $this->btnDocumentLink->Display = false;
                $this->lblFileName->Display = true;
                $this->lblFileName->Text = $_SESSION["data_name"];
            } else {
                $this->btnDocumentLink->Display = true;
                $this->lblFileName->Display = false;
            }

            $this->btnSaveFile = new Bs\Button($this);
            $this->btnSaveFile->Text = t('Save file');
            $this->btnSaveFile->CssClass = 'btn btn-orange';
            $this->btnSaveFile->addWrapperCssClass('center-button');
            $this->btnSaveFile->setCssStyle('float', 'right');
            $this->btnSaveFile->setCssStyle('margin-left', '5px');
            $this->btnSaveFile->CausesValidation = false;
            $this->btnSaveFile->addAction(new Click(), new Ajax('btnSaveFile_Click'));

            $this->btnCancelFile = new Bs\Button($this);
            $this->btnCancelFile->Text = t('Cancel');
            $this->btnCancelFile->CssClass = 'btn btn-default';
            $this->btnCancelFile->addWrapperCssClass('center-button');
            $this->btnCancelFile->setCssStyle('float', 'right');
            $this->btnCancelFile->setCssStyle('margin-left', '5px');
            $this->btnCancelFile->CausesValidation = false;
            $this->btnCancelFile->addAction(new Click(), new Ajax('btnCancelFile_Click'));

            $this->btnUpdate = new Bs\Button($this);
            $this->btnUpdate->Text = t('Update');
            $this->btnUpdate->CssClass = 'btn btn-orange';
            $this->btnUpdate->addAction(new Click(), new Ajax('btnUpdate_Click'));

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

            $this->btnCloseWindow = new Bs\Button($this);
            $this->btnCloseWindow->Text = t('Close the window');
            $this->btnCloseWindow->CssClass = 'btn btn-default';
            $this->btnCloseWindow->CausesValidation = false;
            $this->btnCloseWindow->addAction(new Click(), new Ajax('btnCloseWindow_Click'));

            $this->btnGoToCategory = new Bs\Button($this);
            $this->btnGoToCategory->Tip = true;
            $this->btnGoToCategory->ToolTip = t('Go to categorize manager');
            $this->btnGoToCategory->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToCategory->CssClass = 'btn btn-default';
            $this->btnGoToCategory->setCssStyle('float', 'right');
            $this->btnGoToCategory->addWrapperCssClass('center-button');
            $this->btnGoToCategory->CausesValidation = false;
            $this->btnGoToCategory->addAction(new Click(), new Ajax('btnGoToCategory_Click'));

            $this->btnGoToSettings = new Bs\Button($this);
            $this->btnGoToSettings->Tip = true;
            $this->btnGoToSettings->ToolTip = t('Go to the links settings manager');
            $this->btnGoToSettings->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToSettings->CssClass = 'btn btn-default';
            $this->btnGoToSettings->setCssStyle('float', 'right');
            $this->btnGoToSettings->addWrapperCssClass('center-button');
            $this->btnGoToSettings->CausesValidation = false;
            $this->btnGoToSettings->addAction(new Click(), new Ajax('btnGoToSettings_Click'));

            //////////////////////////////////////////////////////////////////////////////////////////

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->setCssStyle('margin-left', '10px');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new Ajax('btnBack_Click'));
        }

        /**
         * Creates and configures a SortWrapper instance used for managing sortable items.
         *
         * @return void
         * @throws Caller
         */
        protected function createSorter(): void
        {
            $this->dlgSorter = new Q\Plugin\Control\SortWrapper($this);
            $this->dlgSorter->createNodeParams([$this, 'Sorter_Draw']);
            $this->dlgSorter->createControlButtons([$this, 'Buttons_Draw']);
            $this->dlgSorter->createRenderInputs([$this, 'Dates_Draw']);
            $this->dlgSorter->setDataBinder('Sorter_Bind');
            $this->dlgSorter->ActivatedLink = true;
            $this->dlgSorter->addCssClass('sortable');
            $this->dlgSorter->Placeholder = 'placeholder';
            $this->dlgSorter->Handle = '.reorder';
            $this->dlgSorter->Items = 'div.div-block';

            $this->dlgSorter->addAction(new SortableStop(), new Ajax('sortable_stop'));
            $this->dlgSorter->watch(QQN::Links());
        }

        /**
         * Binds the sorter with the data source based on the current settings.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function Sorter_Bind(): void
        {
            $this->dlgSorter->DataSource = Links::QueryArray(
                QQ::Equal(QQN::Links()->SettingsId, $this->intId),
                QQ:: Clause(
                    QQ::orderBy(QQN::Links()->Order)
                )
            );
        }

        /**
         * Prepares and returns an associative array representation of a given link.
         *
         * @param Links $objLink The link object to be processed.
         *
         * @return array An associative array containing the id, category, name, url, order, and status of the link.
         * @throws Caller
         * @throws InvalidCast
         */
        public function Sorter_Draw(Links $objLink): array
        {
            $strRootPath = APP_UPLOADS_URL;

            $a['id'] = $objLink->Id;
            $a['category'] = $objLink->LinkCategory;
            $a['name'] = $objLink->Name;

            if ($objLink->FilesId == null) {
                $a['url'] = $objLink->Url;
            } else {
                $objFile = Files::load($objLink->FilesId);
                $a['url'] = $strRootPath . $objFile->Path;
            }

            $a['order'] = $objLink->Order;
            $a['status'] = $objLink->Status;
            return $a;
        }

        /**
         * Generates and returns the HTML for "Edit" and "Delete" buttons associated with a specific link.
         * If the buttons do not already exist, they are created, configured, and stored.
         *
         * @param Links $objLink The link object for which the buttons are being created or retrieved.
         *
         * @return string The rendered HTML for the "Edit" and "Delete" buttons.
         * @throws Caller
         */
        public function Buttons_Draw(Links $objLink): string
        {
            $strEditId = 'btnEdit' . $objLink->Id;

            if (!$btnEdit = $this->getControl($strEditId)) {
                $btnEdit = new Bs\Button($this->dlgSorter, $strEditId);
                $btnEdit->Glyph = 'glyphicon glyphicon-pencil';
                $btnEdit->Tip = true;
                $btnEdit->ToolTip = t('Edit');
                $btnEdit->CssClass = 'btn btn-icon btn-xs edit';
                $btnEdit->ActionParameter = $objLink->Id;
                $btnEdit->UseWrapper = false;
                $btnEdit->addAction(new Click(), new Ajax('btnEdit_Click'));
            }

            $strDeleteId = 'btnDelete' . $objLink->Id;

            if (!$btnDelete = $this->getControl($strDeleteId)) {
                $btnDelete = new Bs\Button($this->dlgSorter, $strDeleteId);
                $btnDelete->Glyph = 'glyphicon glyphicon-trash';
                $btnDelete->Tip = true;
                $btnDelete->ToolTip = t('Delete');
                $btnDelete->CssClass = 'btn btn-icon btn-xs delete';
                $btnDelete->ActionParameter = $objLink->Id;
                $btnDelete->UseWrapper = false;
                $btnDelete->addAction(new Click(), new Ajax('btnDelete_Click'));
            }

            return $btnEdit->render(false) . $btnDelete->render(false);
        }

        /**
         * Renders and returns formatted date labels for the given link object.
         *
         * @param Links $objLink The link object for which date labels are to be created and rendered.
         *
         * @return string A concatenated string of the rendered post- and post-update date labels.
         * @throws Caller
         */
        public function Dates_Draw(Links $objLink): string
        {
            $strPostDate = 'calPostDate' . $objLink->Id;

            if (!$calPostDate = $this->getControl($strPostDate)) {
                $calPostDate =  new Bs\Label($this->dlgSorter, $strPostDate);
                $calPostDate->Text = $objLink->PostDate ? $objLink->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $calPostDate->setCssStyle('float', 'left');
                //$calPostDate->setCssStyle('padding-right', '30px');
                $calPostDate->setCssStyle('font-weight', 'normal');
            }

            $strPostUpdateDate = 'calPostUpdateDate' . $objLink->Id;

            if (!$calPostUpdateDate = $this->getControl($strPostUpdateDate)) {
                $calPostUpdateDate =  new Bs\Label($this->dlgSorter, $strPostUpdateDate);
                $calPostUpdateDate->Text = $objLink->PostUpdateDate ? $objLink->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $calPostUpdateDate->setCssStyle('float', 'right');
                $calPostUpdateDate->setCssStyle('font-weight', 'normal');
            }

            return $calPostDate->render(false) . ' ' . $calPostUpdateDate->render(false);
        }

        /**
         * Initializes multiple Toastr notification instances with specific configurations
         * for various success, error, or informational messages and settings.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The new title of link has been successfully created and saved.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('<strong>Sorry</strong>, creating or saving the new title of link failed!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Sorry</strong>, the title is required!');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('<strong>Well done!</strong> The order of links was successfully updated!');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('<strong>Sorry</strong>, updating the order of links failed!');
            $this->dlgToastr5->ProgressBar = true;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('<strong>Well done!</strong> The link data has been successfully updated!');
            $this->dlgToastr6->ProgressBar = true;

            $this->dlgToastr7 = new Q\Plugin\Toastr($this);
            $this->dlgToastr7->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr7->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr7->Message = t('<strong>Sorry</strong>, updating the link data failed!');
            $this->dlgToastr7->ProgressBar = true;

            $this->dlgToastr8 = new Q\Plugin\Toastr($this);
            $this->dlgToastr8->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr8->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr8->Message = t('Updates to some records for this link were discarded, and the record has been restored!');
            $this->dlgToastr8->ProgressBar = true;

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->dlgToastr9 = new Q\Plugin\Toastr($this);
            $this->dlgToastr9->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr9->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr9->Message = t('<strong>Well done!</strong> This link with data has now been made public!');
            $this->dlgToastr9->ProgressBar = true;

            $this->dlgToastr10 = new Q\Plugin\Toastr($this);
            $this->dlgToastr10->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr10->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr10->Message = t('<strong>Well done!</strong> This link with data is now hidden!');
            $this->dlgToastr10->ProgressBar = true;

            $this->dlgToastr11 = new Q\Plugin\Toastr($this);
            $this->dlgToastr11->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr11->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr11->Message = t('<strong>Sorry</strong>, this field is required!');
            $this->dlgToastr11->ProgressBar = true;
            $this->dlgToastr11->EscapeHtml = false;

            $this->dlgToastr12 = new Q\Plugin\Toastr($this);
            $this->dlgToastr12->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr12->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr12->Message = t('<strong>Sorry</strong>, these fields must be filled!');
            $this->dlgToastr12->ProgressBar = true;

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->dlgToastr13 = new Q\Plugin\Toastr($this);
            $this->dlgToastr13->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr13->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr13->Message = t('<strong>Well done!</strong> The link with the data was successfully deleted!');
            $this->dlgToastr13->ProgressBar = true;

            $this->dlgToastr14 = new Q\Plugin\Toastr($this);
            $this->dlgToastr14->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr14->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr14->Message = t('<strong>Sorry</strong>, the link deletion failed!');
            $this->dlgToastr14->ProgressBar = true;
        }

        /**
         * Creates and initializes modal dialogs with specific configurations and content.
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the Links group for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This links group is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This links group has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this link and its associated data?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
            $this->dlgModal5->Title = 'Warning';
            $this->dlgModal5->HeaderClasses = 'btn-danger';
            $this->dlgModal5->addButton("I accept", null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal5->addCloseButton(t("I'll cancel"));
            $this->dlgModal5->addAction(new DialogButton(), new Ajax('deleteItem_Click'));

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to transfer this link along with its data from this links group to another links group?</p>');
            $this->dlgModal6->Title = t('Warning');
            $this->dlgModal6->HeaderClasses = 'btn-danger';
            $this->dlgModal6->addButton(t("I accept"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal6->addCloseButton(t("I'll cancel"));
            $this->dlgModal6->addAction(new DialogButton(), new Ajax('moveItem_Click'));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the stop action for the sortable functionality, updating the order of items
         * and applying necessary changes to the linked data. Also notifies the user and updates
         * associated settings after the reordering process is completed.
         *
         * @param ActionParams $params The parameters passed during the sortable stop action event.
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

                $objSorter = Links::load($id);
                $objSorter->setOrder($order);
                $objSorter->setPostUpdateDate(Q\QDateTime::now());
                $objSorter->save();
            }

            // Let's check if the array is not empty
            if (!empty($arr)) {
                $this->dlgToastr4->notify();
            } else {
                $this->dlgToastr5->notify();
            }

            Application::executeJavaScript("
                $('.link-setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
           ");

            $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objLinksSettings->save();

            $this->calPostUpdateDate->Text = $this->objLinksSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        /**
         * Handles the click event for the "Add Link" button. Updates UI elements by enabling/disabling components,
         * initializing input fields, and executing client-side JavaScript for advanced UI manipulations.
         *
         * @param ActionParams $params Parameters associated with the triggered action.
         *
         * @return void
         * @throws Caller
         */
        protected function btnAddLink_Click(ActionParams $params): void
        {
            $this->btnAddLink->Enabled = false;
            $this->txtNewTitle->Display = true;
            $this->btnLinkSave->Display = true;
            $this->btnLinkCancel->Display = true;
            $this->txtNewTitle->Text = '';
            $this->txtNewTitle->focus();

            Application::executeJavaScript("
                jQuery(\"[data-value='$this->intClick']\").removeClass('activated');
                $('.link-setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
           ");

            //$this->dlgSorter->refresh();
        }

        /**
         * Handles the save action for a new link.
         *
         * This method is triggered when save a button for creating a new link is clicked.
         * It validates the input, creates and saves a new link, updates related settings,
         * and manages the display state of UI components.
         *
         * @param ActionParams $params Parameters passed from the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnLinkSave_Click(ActionParams $params): void
        {
            if (trim($this->txtNewTitle->Text) !== '') {
                $objLink = new Links();
                $objLink->setMenuContentGroupId($this->intGroup);
                $objLink->setSettingsId($this->intId);
                $objLink->setSettingsIdTitle($this->objLinksSettings->getName());
                $objLink->setName(trim($this->txtNewTitle->Text));
                $objLink->setOrder(Links::generateOrder($this->intId));
                $objLink->setStatus(2);
                $objLink->setPostDate(Q\QDateTime::now());
                $objLink->save();

                // A check must be made here if the first record and the following records occur in this group,
                // then set "Links_locked" to 1 in the LinksSettings column, etc...

                if (Links::countBySettingsId($this->intId) !== 0) {
                    if ($this->objLinksSettings->getLinksLocked() === 0) {
                        $this->objLinksSettings->setLinksLocked(1);
                    }
                }

                $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
                $this->objLinksSettings->save();

                $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
                $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

                if ($objLink->getId()) {
                    $this->txtNewTitle->Text = '';
                    $this->btnAddLink->Enabled = true;
                    $this->txtNewTitle->Display = false;
                    $this->btnLinkSave->Display = false;
                    $this->btnLinkCancel->Display = false;

                    $this->dlgToastr1->notify();
                } else {
                    $this->dlgToastr12->notify();
                }
            } else {
                $this->txtNewTitle->Text = '';
                $this->txtNewTitle->focus();
                $this->btnAddLink->Enabled = false;
                $this->txtNewTitle->Display = true;
                $this->btnLinkSave->Display = true;
                $this->btnLinkCancel->Display = true;

                $this->dlgToastr3->notify();
            }

            if ($this->objLinksSettings->getLinksLocked() === 1) {
                $this->lblInfo->Display = false;
            } else {
                $this->lblInfo->Display = true;
            }

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        /**
         * Handles the cancel action for the link creation process.
         * Resets the form and re-enables the 'Add Link' button.
         *
         * @param ActionParams $params The parameters related to the action triggered by the user.
         * @return void
         */
        protected function btnLinkCancel_Click(ActionParams $params): void
        {
            $this->btnAddLink->Enabled = true;
            $this->txtNewTitle->Display = false;
            $this->btnLinkSave->Display = false;
            $this->btnLinkCancel->Display = false;
            $this->txtNewTitle->Text = '';

            if ($this->objLinksSettings->getLinksLocked() === 1) {
                $this->lblInfo->Display = false;
            } else {
                $this->lblInfo->Display = true;
            }
        }

        /**
         * Handles the click event for the "Edit" button, sets the form with the selected item's data,
         * updates UI states, and triggers the necessary JavaScript for smooth scrolling and visual feedback.
         *
         * @param ActionParams $params The parameters containing the action information, including the ActionParameter
         *                             which holds the ID of the item to be edited.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnEdit_Click(ActionParams $params): void
        {
            $intEditId = intval($params->ActionParameter);
            $objEdit = Links::load($intEditId);
            $this->intClick = $intEditId;

            Application::executeJavaScript("$('.js-links-wrapper').get(0).scrollIntoView({behavior: 'smooth'});");

            $this->txtTitle->Text = $objEdit->Name ?? '';
            $this->txtUrl->Text = $objEdit->Url ?? '';
            $this->lstCategory->SelectedValue = $objEdit->CategoryId;
            $this->lstGroupTitle->SelectedValue = $objEdit->SettingsId;
            $this->lstLinkStatus->SelectedValue = $objEdit->Status;

            $this->lstCategory->refresh();
            $this->lstGroupTitle->refresh();
            $this->lstLinkStatus->refresh();

            if ($objEdit->FilesId) {
                $objFile = Files::load($objEdit->FilesId);
                $this->lblFileName->Display = true;
                $this->lblFileName->Text = $objFile->getName();

                $this->txtHiddenDocument->setDataAttribute('open', 'true');
                $this->txtHiddenDocument->setDataAttribute('view', APP_UPLOADS_URL . $objFile->getPath());

                $this->btnDocumentLink->Display = false;
                $this->btnSaveFile->Display = false;
                $this->btnCancelFile->Display = false;

                $this->btnSelectedCheck->Display = true;
                $this->btnSelectedDelete->Display = true;
            } else {
                $this->lblFileName->Display = false;

                $this->txtHiddenDocument->setDataAttribute('open', 'false');
                $this->txtHiddenDocument->setDataAttribute('view', '');

                $this->btnDocumentLink->Display = true;
                $this->btnDocumentLink->setCssStyle('border', '1px solid red');

                $this->btnSaveFile->Display = false;
                $this->btnCancelFile->Display = false;

                $this->btnSelectedCheck->Display = false;
                $this->btnSelectedDelete->Display = false;
            }

            Application::executeJavaScript("
                $(\"[data-value='$intEditId']\").addClass('activated');
                $(\"[data-value='$intEditId']\").removeClass('inactivated');
                $('.link-setting-wrapper').removeClass('hidden');
                $('.form-actions-wrapper').removeClass('hidden');
           ");

            $this->InputsCheck();
            $this->dlgSorter->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves a list of link settings items based on their link type and additional conditions.
         *
         * @return ListItem[] An array of ListItem objects representing link settings, with selection
         * and disabling logic applied based on the current link settings.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstLinksSettings_GetItems(): array
        {
            $a = array();

            // What LinkType should this menu/settings be? (1 or 2)
            $linkType = $this->objLinksSettings->getLinkType();

            // If LinkType is missing, there is no point in showing anything at all
            if (empty($linkType)) {
                return $a;
            }

            // Main condition: always link_type = $linkType
            $objCondition = QQ::Equal(QQN::LinksSettings()->LinkType, $linkType);

            // If you have any special conditions set (for example, $this->objLinksSettingsCondition), combine them
            if (!is_null($this->objLinksSettingsCondition)) {
                $objCondition = QQ::AndCondition(
                    $this->objLinksSettingsCondition,
                    $objCondition // LinkType filter always REMAINS active!
                );
            }

            $objSettingsCursor = LinksSettings::queryCursor($objCondition, $this->objLinksSettingsClauses);

            while ($objSettings = LinksSettings::instantiateCursor($objSettingsCursor)) {
                $objListItem = new ListItem($objSettings->__toString(), $objSettings->Id);
                if (($this->intId) && ($this->intId == $objSettings->Id))
                    $objListItem->Selected = true;

                if ($this->intId == $objSettings->Id) {
                    $objListItem->Disabled = true;
                }

                $a[] = $objListItem;
            }

            return $a;
        }

        /**
         * Retrieves a list of category items based on the provided conditions and clauses.
         *
         * @return ListItem[] An array of ListItem objects representing categories, with specific selection
         * and disabling logic applied.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstCategory_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objCategoryCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objCategoryCursor = LinksCategory::queryCursor($objCondition, $this->objCategoryClauses);

            // Iterate through the Cursor
            while ($objCategory = LinksCategory::instantiateCursor($objCategoryCursor)) {
                $objListItem = new ListItem($objCategory->__toString(), $objCategory->Id);

                if (!empty($this->objLink->Category)) {
                    if (($this->objLink->Category) && ($this->objLink->Category->Id == $objCategory->Id))
                        $objListItem->Selected = true;
                }

                // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
                // {display: none;} </style>
                // A little trick on how to hide some options. Just set the option to "disabled" and
                //  use it only on a specific page. You just have to use the style.

                if ($objCategory->Status == 2) {
                    $objListItem->Disabled = true;
                }

                $a[] = $objListItem;
            }

            return $a;
        }

        /**
         * Handles the change event for the category dropdown list and updates relevant data accordingly.
         *
         * @param ActionParams $params The parameters associated with the action that triggered this method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstCategory_Change(ActionParams $params): void
        {
            $objCategory = Links::load($this->intClick);

            if ($this->lstCategory->SelectedValue !== $objCategory->getCategoryId()) {
                $objCategory->setCategoryId($this->lstCategory->SelectedValue);

                if ($this->lstCategory->SelectedValue === null) {
                    $objCategory->setLinkCategory(null);
                } else {
                    $objCategory->setLinkCategory($this->lstCategory->SelectedName);
                }

                $objCategory->setPostUpdateDate(Q\QDateTime::now());
                $objCategory->save();

                $this->dlgToastr6->notify();
            }

            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objLinksSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the change event for the group title dropdown list.
         *
         * This method compares the selected value in the `lstGroupTitle` dropdown with the settings ID
         * stored in the `objLink` object. If they do not match, it triggers the display of a modal dialog box.
         *
         * @param ActionParams $params The parameters associated with the action, usually including context for the event triggering this method.
         * @return void
         */
        protected function lstGroupTitle_Change(ActionParams $params): void
        {
            if ($this->lstGroupTitle->SelectedValue !== $this->objLink->getSettingsId()) {
                $this->dlgModal6->showDialogBox();
            }
        }

        /**
         * Handles the action triggered by clicking the Move Item button.
         * This method moves a specific link item to a different group and updates related group and link settings accordingly.
         *
         * @param ActionParams $params The parameters associated with the action, containing details about the user's interaction.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function moveItem_Click(ActionParams $params): void
        {
            $this->dlgModal6->hideDialogBox();

            $objMove = Links::load($this->intClick);

            $objGroupId = LinksSettings::loadById($this->lstGroupTitle->SelectedValue);

            $currentCount = Links::countBySettingsId($objMove->getSettingsId());
            $nextCount = Links::countBySettingsId($objGroupId->getId());

            $objTargetGroup = LinksSettings::loadById($objGroupId->getId());
            if ($nextCount == 0) {
                $objTargetGroup->setLinksLocked(1);
                $objTargetGroup->save();
            }

            $objGroup = LinksSettings::loadById($objMove->getSettingsId());
            if ($currentCount) {
                if ($currentCount == 1) {
                    $objGroup->setLinksLocked(0);
                } else {
                    $objGroup->setLinksLocked(1);
                }
                $objGroup->save();
            }

            $objLink = Links::load($objMove->getId());
            $objLink->setSettingsId($this->lstGroupTitle->SelectedValue);
            $objLink->setSettingsIdTitle($this->lstGroupTitle->SelectedName);
            $objLink->setOrder(Links::generateOrder($this->lstGroupTitle->SelectedValue));
            $objLink->setPostUpdateDate(Q\QDateTime::now());
            $objLink->save();

            Application::redirect('Links_edit.php?id=' . $objGroupId->getId() . '&group=' . $objGroupId->getMenuContentId());
        }

        /**
         * Handles the change event for the link status dropdown selection.
         * This method performs input validation, saves data conditionally based on errors or status changes,
         * updates related data, and refreshes the UI.
         *
         * @param ActionParams $params Parameters associated with the action event triggering this method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstLinkStatus_Change(ActionParams $params): void
        {
            $objLink = Links::load($this->intClick);
            $linkType = $this->objLinksSettings->getLinkType();
            $this->InputsCheck();

            // Condition for which notification to show
            if (count($this->errors) === 1 || ($linkType == 2 && $objLink->getFilesId() == null)) {
                $this->dlgToastr11->notify(); // If only one field is invalid
                $this->saveHelper(); // Partial saving allowed
            } elseif (count($this->errors) > 1 || ($linkType == 2 && $objLink->getFilesId() == null)) {
                $this->dlgToastr12->notify(); // If there is more than one invalid field
                $this->saveHelper(); // Partial saving allowed
            } else {
                $objLink->setStatus($this->lstLinkStatus->SelectedValue);
                if ($objLink->getStatus() == 1) {
                    $this->dlgToastr9->notify();
                } else {
                    $this->dlgToastr10->notify();
                }
            }

            if (count($this->errors) || ($linkType == 2 && $objLink->getFilesId() == null)) {
                $this->btnDocumentLink->setCssStyle('border', '1px solid red');
                $this->lstLinkStatus->SelectedValue = 2;
                $objLink->setStatus(2);
            }

            $objLink->save();

            unset($this->errors);

            // Continue to update additional data and refresh the screen
            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objLinksSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            Application::executeJavaScript(" $(\"[data-value='$objLink']\").addClass('activated');");

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        /**
         * Handles the change in status for the related menu and page configuration.
         *
         * @param ActionParams $params Parameters associated with the action triggered.
         *
         * @return void This method does not return any value, it performs necessary actions based on the status change.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            $objMenuContent = MenuContent::loadById($this->objMenu->getId());

            if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->dlgModal1->showDialogBox();
                $this->updateInputFields();
                return;
            }
            
            if ($objMenuContent->SelectedPageLocked === 1) {
                $this->dlgModal2->showDialogBox();
                $this->updateInputFields();
                return;
            }

            $this->objLinksSettings->setStatus($this->lstStatus->SelectedValue);
            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objLinksSettings->save();
            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
            $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objLinksSettings->save();

            $objMenuContent->setIsEnabled(2);
            $objMenuContent->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            if ($this->objLinksSettings->getStatus() === 2) {
                $this->dlgModal3->showDialogBox();
            } else {
                $this->dlgModal4->showDialogBox();
            }

            Application::executeJavaScript("
                $('.link-setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
            ");

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        /**
         * Updates the input fields by setting the selected value of the status dropdown
         * to match the status from the associated LinksSettings object and refreshes the dropdown.
         *
         * @return void
         */
        protected function updateInputFields(): void
        {
            $this->lstStatus->SelectedValue = $this->objLinksSettings->getStatus();
            $this->lstStatus->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Saves or updates the helper object with the provided attributes from the form inputs.
         *
         * @return void Performs the saving of the helper object to the database.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function saveHelper(): void
        {
            $objHelper = Links::load($this->intClick);

            $objHelper->setName($this->txtTitle->Text);
            $objHelper->setUrl($this->txtUrl->Text);

            $objHelper->setStatus($this->lstLinkStatus->SelectedValue);
            $objHelper->save();

            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objLinksSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        /**
         * Handles the update process for a specific link, applying validation, updating fields, displaying notifications, and saving changes.
         *
         * @param ActionParams $params The parameters associated with the click action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnUpdate_Click(ActionParams $params): void
        {
            $objUpdate = Links::load($this->intClick);
            $linkType = $this->objLinksSettings->getLinkType();
            $this->InputsCheck();

            if ($objUpdate->FilesId) {
                $objFile = Files::load($objUpdate->FilesId);
                $objFile->Name = $this->lblFileName->Text;
            }

            // Check if $objUpdate is available
            if (!$objUpdate) {
                $this->dlgToastr7->notify();
                return;
            }

            // Condition for which notification to show
            if (count($this->errors) === 1|| ($linkType == 2 && $objUpdate->getFilesId() == null)) {
                $this->dlgToastr11->notify(); // If only one field is invalid
                $this->saveHelper(); // Partial saving allowed
                $this->lstLinkStatus->SelectedValue = 2;

            } elseif (count($this->errors) > 1|| ($linkType == 2 && $objUpdate->getFilesId() == null)) {
                $this->dlgToastr12->notify(); // If there is more than one invalid field
                $this->saveHelper(); // Partial saving allowed
                $this->lstLinkStatus->SelectedValue = 2;
            } else {
                $objUpdate->Name = $this->txtTitle->Text ?? '';
                $objUpdate->Url = $this->txtUrl->Text ?? '';
                $objUpdate->CategoryId = $this->lstCategory->SelectedValue;
                $objUpdate->Status = $this->lstLinkStatus->SelectedValue;
                $objUpdate->PostUpdateDate = Q\QDateTime::now();

                if ($objUpdate->FilesId) {
                    $this->btnDocumentLink->Display = false;
                    $this->btnSaveFile->Display = false;

                    $this->btnSelectedCheck->Display = true;
                    $this->btnSelectedDelete->Display = true;
                } else {
                    $this->btnDocumentLink->Display = true;
                    $this->btnSaveFile->Display = false;

                    $this->btnSelectedCheck->Display = false;
                    $this->btnSelectedDelete->Display = false;
                }
            }

            if (count($this->errors) || ($linkType == 2 && $objUpdate->getFilesId() == null)) {
                $this->btnDocumentLink->setCssStyle('border', '1px solid red');
                $this->lstLinkStatus->SelectedValue = 2;
                $objUpdate->setStatus(2);
            }

            // Check if the save was successful
            try {
                $objUpdate->save();
                if (!count($this->errors)) {
                    $this->dlgToastr6->notify();
                }
            } catch (Exception $e) {
                error_log('Save failed: ' . $e->getMessage());
                return;
            }

            unset($this->errors);

            // Continue to update additional data and refresh the screen
            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objLinksSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->txtTitle->refresh();
            $this->txtUrl->refresh();

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        /**
         * Handles the click event for the document link button, performing actions such as updating session data,
         * modifying link status, and redirecting the user to another page.
         *
         * @param ActionParams $params The parameters associated with the action triggered by clicking the button.
         *
         * @return void This method does not return a value but performs operations such as updating the link status,
         * modifying UI elements and redirecting the user.
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function btnDocumentLink_Click(ActionParams $params): void
        {
            $objLink = Links::load($this->intClick);

            $_SESSION["redirect-data"] = 'links_edit.php?id=' . $this->intId . '&group=' . $this->intGroup;
            $_SESSION["links-title"] = $objLink->getName();
            $_SESSION["links-data"] = $objLink;

            $this->btnDocumentLink->Enabled = false;
            $this->btnSaveFile->Display = true;

            $objLink->setStatus(2);
            $objLink->save();

            $this->lstLinkStatus->SelectedValue = 2;
            $this->lstLinkStatus->refresh();

            Application::redirect('file_finder.php');
        }

        /**
         * Handles the save file button, click event, updating link and file data, and refreshing UI elements accordingly.
         *
         * @param ActionParams $params The parameters associated with the button click event.
         *
         * @return void This method does not return anything.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnSaveFile_Click(ActionParams $params): void
        {
            $objLink = $_SESSION["links-data"] ?? Links::load($this->intClick);

            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {

                $objLink->setFilesId($_SESSION["data_id"]);
                $objLink->setPostUpdateDate(Q\QDateTime::now());
                $objLink->setStatus(2);
                $objLink->save();

                $this->lstLinkStatus->SelectedValue = 2;
                $this->lstLinkStatus->refresh();

                $this->intClick = $objLink->getId();

                $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
                $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objLinksSettings->save();

                $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
                $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

                $objFile = Files::load($objLink->getFilesId());
                $this->txtTitle->Text = $objLink->getName();

                if ($objLink->FilesId) {
                    $this->lblFileName->Display = true;
                    $this->lblFileName->Text = $objFile->getName();
                    $this->lblFileName->refresh();

                    $this->txtHiddenDocument->setDataAttribute('open', 'true');
                    $this->txtHiddenDocument->setDataAttribute('view', APP_UPLOADS_URL . $objFile->getPath());

                    $this->btnDocumentLink->Display = false;
                    $this->btnSaveFile->Display = false;
                    $this->btnCancelFile->Display = false;

                    $this->btnSelectedCheck->Display = true;
                    $this->btnSelectedDelete->Display = true;

                    $this->dlgToastr6->notify();
                } else {
                    $this->lblFileName->Display = false;

                    $this->txtHiddenDocument->setDataAttribute('open', 'false');
                    $this->txtHiddenDocument->setDataAttribute('view', '');

                    $this->btnDocumentLink->Display = true;
                    $this->btnSaveFile->Display = false;
                    $this->btnCancelFile->Display = false;
                }

                unset($_SESSION["data_id"]);
                unset($_SESSION["data_name"]);
                unset($_SESSION["data_path"]);
                unset($_SESSION["links-title"]);
                unset($_SESSION["links-data"]);

                $this->InputsCheck();
                $this->refreshDisplay();
                $this->dlgSorter->refresh();
            }
        }

        /**
         * Handles the event when the Cancel File button is clicked. This method resets file-related session data, updates
         * file lock status if necessary, manages UI element visibility and styling, and releases resources.
         *
         * @param ActionParams $params The parameters associated with the action triggering the event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnCancelFile_Click(ActionParams $params): void
        {
            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
                $objFile = Files::load($_SESSION["data_id"]);

                if ($_SESSION["data_id"]) {
                    if ($objFile->getLockedFile() !== 0) {
                        $objFile->setLockedFile($objFile->getLockedFile() - 1);
                        $objFile->save();
                    }
                }

                $this->btnDocumentLink->Display = true;
                $this->btnDocumentLink->setCssStyle('border', '1px solid red');
                $this->lblFileName->Display = false;
                $this->lblFileName->Text = null;
                $this->btnSaveFile->Display = false;
                $this->btnCancelFile->Display = false;

                unset($_SESSION["data_id"]);
                unset($_SESSION["data_name"]);
                unset($_SESSION["data_path"]);
                unset($_SESSION["links-title"]);
                unset($_SESSION["links-data"]);
            }
        }

        /**
         * Validates input fields and adds errors to the error array if fields are empty.
         *
         * @return void This method does not return any value. It sets HTML attributes for required fields
         * and updates the error array with field identifiers.
         */
        protected  function InputsCheck(): void
        {
            // We check each field and add errors if necessary
            if (!$this->txtTitle->Text) {
                $this->txtTitle->setHtmlAttribute('required', 'required');
                $this->errors[] = 'txtTitle';
            } else {
                $this->txtTitle->removeHtmlAttribute('required');
            }

            if ($this->objLinksSettings->getLinkType() === 1) {
                if (!$this->txtUrl->Text) {
                    $this->txtUrl->setHtmlAttribute('required', 'required');
                    $this->errors[] = 'txtUrl';
                } else {
                    $this->txtUrl->removeHtmlAttribute('required');
                }
            } else {
                if(!$this->lblDocumentLink->Text) {
                    $this->btnDocumentLink->setCssStyle('border', '1px solid red');
                    $this->errors[] = 'txtPath';
                } else {
                    $this->btnDocumentLink->setCssStyle('border', null);
                }
            }
        }

        /**
         * Handles the deletion of a selected link and updates various related properties, UI components,
         * and settings. This method manages the logic for unlinking files, adjusting file locking status,
         * updating link statuses, and refreshing the user interface.
         *
         * @param ActionParams $params The parameters containing the context in which the action is
         *                              triggered, such as the selected link or affected objects.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnSelectedDelete_Click(ActionParams $params): void
        {
            $objLinks = Links::load($this->intClick);

            if ($objLinks->getFilesId()) {
                $objFile = Files::load($objLinks->getFilesId());

                if ($objFile->getLockedFile() !== 0) {
                    $objFile->setLockedFile($objFile->getLockedFile() - 1);
                    $objFile->save();
                }
            }

            $objLink = Links::load($this->intClick);
            $objLink->setFilesId(null);
            $objLink->setPostUpdateDate(Q\QDateTime::now());
            $objLink->setStatus(2);
            $objLink->save();
            $this->lstLinkStatus->SelectedValue = 2;
            $this->lstLinkStatus->refresh();

            $this->lblFileName->Display = false;
            $this->btnDocumentLink->Display = true;
            $this->btnDocumentLink->setCssStyle('border', '1px solid red');
            $this->btnSaveFile->Display = false;
            $this->btnCancelFile->Display = false;

            $this->btnSelectedCheck->Display = false;
            $this->btnSelectedDelete->Display = false;

            $this->txtHiddenDocument->setDataAttribute('open', 'false');
            $this->txtHiddenDocument->setDataAttribute('view', '');

            if ($objLink->getFilesId() == null) {
                $this->dlgToastr6->notify();
            } else {
                $this->dlgToastr7->notify();
            }

            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objLinksSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->dlgSorter->refresh();
            $this->refreshDisplay();
        }

        /**
         * Handles the click event for the close window button. Executes JavaScript to remove specific
         * activation classes and hide specific UI elements.
         *
         * @param ActionParams $params The parameters for the button click action.
         *
         * @return void
         * @throws Caller
         */
        protected function btnCloseWindow_Click(ActionParams $params): void
        {
            Application::executeJavaScript("
                $(\"[data-value='$this->intClick']\").removeClass('activated');
                $('.link-setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
           ");

            $this->dlgSorter->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event to load and set item details for editing.
         *
         * @param ActionParams $params The parameters passed from the triggered action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
            $objCancel = Links::load($this->intClick);
            $objFile = Files::load($objCancel->getFilesId());

            // Check if $objCancel is available
            if ($objCancel) {
                $this->dlgToastr8->notify();
            }

            $this->txtTitle->Text = $objCancel->Name ?? '';
            $this->txtUrl->Text = $objCancel->Url ?? '';
            $this->lblFileName->Text = $objFile->Name ?? '';
            $this->lstCategory->SelectedValue = $objCancel->CategoryId;
            $this->lstLinkStatus->SelectedValue = $objCancel->Status;

            $this->dlgSorter->refresh();
        }

        /**
         * Handles the click event for the delete button. It sets the action parameter
         * as an integer and triggers the display of a modal dialog box.
         *
         * @param ActionParams $params The parameters associated with the button click action,
         * including the action parameter to be processed.
         *
         * @return void This method does not return any value.
         */
        protected function btnDelete_Click(ActionParams $params): void
        {
            $this->intClick = intval($params->ActionParameter);
            $this->dlgModal5->showDialogBox();
        }

        /**
         * Handles the deletion of a specific link item associated with a menu content
         * and performs various updates related to link settings, user interfaces, and notifications.
         *
         * @param ActionParams $params An object containing action parameters and metadata related to the
         *                              click the action triggering this method.
         *
         * @return void This method does not return a value but modifies the application state, including
         *              database entries, UI elements, and associated settings.
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function deleteItem_Click(ActionParams $params): void
        {
            $objLink = Links::loadById($this->intClick);
            $objFile = Files::load($objLink->getFilesId());

            if (Links::countBySettingsId($objLink->getSettingsId()) === 1) {
                if ($this->objLinksSettings->getLinksLocked() === 1) {
                    $this->objLinksSettings->setLinksLocked(0);
                }
            }

            if ($objLink->getFilesId()) {
                if ($objFile->getLockedFile() !== 0) {
                    $objFile->setLockedFile($objFile->getLockedFile() - 1);
                    $objFile->save();
                }
            }

            $objLink->delete();

            $this->lblFileName->Display = false;
            $this->btnDocumentLink->Display = true;
            $this->btnSaveFile->Display = false;
            $this->btnDocumentLink->Enabled = true;

            Application::executeJavaScript("
                $('.link-setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
            ");

            if ($objLink->getId() !== $objLink) {
                $this->dlgToastr13->notify();
            } else {
                $this->dlgToastr14->notify();
            }

            if ($this->objLinksSettings->getLinksLocked() === 1) {
                $this->lblInfo->Display = false;
            } else {
                $this->lblInfo->Display = true;
            }

            $this->objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objLinksSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objLinksSettings->getUserAsLinksEditorsArray());
            $this->calPostUpdateDate->Text = $this->objLinksSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->dlgModal5->hideDialogBox();
            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        /**
         * Handles the click event for the "Go To Category" button. Sets session variables for the current link ID and
         * group, and redirects the user to the categories manager page at the specified tab.
         *
         * @param ActionParams $params The parameters containing context for the action triggered by the button click.
         *
         * @return void
         * @throws Throwable
         */
        public function btnGoToCategory_Click(ActionParams $params): void
        {
            $_SESSION['links'] = $this->intId;
            $_SESSION['group'] = $this->intGroup;

            Application::redirect('categories_manager.php#linksCategories_tab');
        }

        /**
         * Handles the click event for the "Go to Settings" button.
         * Sets session variables and redirects the user to the settings manager page.
         *
         * @param ActionParams $params Action parameters associated with the click event.
         *
         * @return void
         * @throws Throwable
         */
        public function btnGoToSettings_Click(ActionParams $params): void
        {
            $_SESSION['Links'] = $this->intId;
            $_SESSION['group'] = $this->intGroup;

            Application::redirect('settings_manager.php#linksSettings_tab');
        }

        /**
         * Handles the click event for the "Back" button, navigating the user to the list page.
         *
         * @param ActionParams $params Parameters associated with the button click event.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnBack_Click(ActionParams $params): void
        {
            $this->redirectToListPage();
        }

        /**
         * Redirects the user to the link list page.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            Application::redirect('Links_list.php');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Updates the visibility of various UI elements based on the state of the
         * associated links settings. The method evaluates conditions such as the
         * presence of a post-date, post-update date, author, and linked editors,
         * and adjusts the display properties accordingly.
         *
         * @return void
         */
        protected function refreshDisplay(): void
        {
            if ($this->objLinksSettings->getPostDate() &&
                !$this->objLinksSettings->getPostUpdateDate() &&
                $this->objLinksSettings->getAuthor() &&
                !$this->objLinksSettings->countUsersAsLinksEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($this->objLinksSettings->getPostDate() &&
                $this->objLinksSettings->getPostUpdateDate() &&
                $this->objLinksSettings->getAuthor() &&
                !$this->objLinksSettings->countUsersAsLinksEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($this->objLinksSettings->getPostDate() &&
                $this->objLinksSettings->getPostUpdateDate() &&
                $this->objLinksSettings->getAuthor() &&
                $this->objLinksSettings->countUsersAsLinksEditors()) {
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
    LinksEditForm::run('LinksEditForm');