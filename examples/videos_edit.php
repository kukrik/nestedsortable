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
     * This class defines the VideoEditForm, which extends a base Form class.
     * It initializes various controls, including modals, toasts, labels, buttons,
     * inputs, and calendar controls, to facilitate managing videos and their settings.
     * The form also handles data fetching and user interactions.
     */
    class VideoEditForm extends Form
    {
        protected ?object $objVideosSettingsCondition = null;
        protected ?array $objVideosSettingsClauses = null;

        protected Bs\Modal $dlgModal1;
        protected Bs\Modal $dlgModal2;
        protected Bs\Modal $dlgModal3;
        protected Bs\Modal $dlgModal4;
        protected Bs\Modal $dlgModal5;
        protected Bs\Modal $dlgModal6;
        protected Bs\Modal $dlgModal7;
        protected Bs\Modal $dlgModal8;
        protected Bs\Modal $dlgModal9;

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

        protected Q\Plugin\Toastr $dlgToastr16;
        protected Q\Plugin\Toastr $dlgToastr17;
        protected Q\Plugin\Toastr $dlgToastr18;
        protected Q\Plugin\Toastr $dlgToastr19;
        protected Q\Plugin\Toastr $dlgToastr20;

        protected Q\Plugin\Control\Label $lblGroupTitle;
        protected Q\Plugin\Control\Alert $lblInfo;
        protected Bs\Button $btnAddVideo;
        protected Bs\TextBox $txtNewTitle;
        protected Bs\Button $btnVideoSave;
        protected Bs\Button $btnVideoCancel;
        protected Q\Plugin\Control\Label $lblTitleSlug;
        protected Q\Plugin\Control\Label $txtTitleSlug;

        protected Q\Plugin\Control\Label $lblTitle;
        protected Bs\TextBox $txtTitle;
        protected Q\Plugin\Control\Label $lblIntroduction;
        protected Q\Plugin\CKEditor $txtIntroduction;
        protected Q\Plugin\Control\Label $lblEmbedCode;
        protected Bs\TextBox $txtEmbedCode;
        protected Bs\Button $btnEmbed;
        protected Q\Plugin\Control\Label $lblVideo;
        protected Q\Plugin\Control\Label $strVideo;
        protected Q\Plugin\Control\Label $lblContent;
        protected Q\Plugin\CKEditor $txtContent;
        protected Q\Plugin\Control\Label $lblVideosGroupTitle;
        protected Q\Plugin\Select2 $lstGroupTitle;
        protected Q\Plugin\Control\RadioList $lstVideoStatus;
        protected Q\Plugin\Control\Label $lblVideoStatus;

        protected Q\Plugin\Control\SortWrapper $dlgSorter;

        protected Bs\Button $btnUpdate;
        protected Bs\Button $btnDeleteVideo;
        protected Bs\Button $btnCloseWindow;
        protected Bs\Button $btnGoToSettings;
        protected Bs\Button $btnSort;
        protected Bs\Button $btnBack;

        protected Q\Plugin\Control\Label $lblDirection;
        protected Q\Plugin\Select2 $dlgDirection;

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
        protected ?object $objVideo = null;
        protected object $objVideosSettings;
        protected int $countByIsReserved;
        protected int $countByVideos;
        protected ?array $objActiveInputs = null;

        protected string $strDateTimeFormat = 'd.m.Y H:i';

        /**
         * Initializes and prepares the form for user interaction.
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
            if (!empty($this->intId)) {
                $this->objVideo = Videos::loadByIdFromVideosId($this->intId);
                $this->objVideosSettings = VideosSettings::load($this->intId);
                $this->objMenu = Menu::load($this->intGroup);
            }

            $this->countByIsReserved = VideosSettings::countByIsReserved(1);
            $this->countByVideos = Videos::countBySettingsId($this->intId);

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
            $this->createToastr();
            $this->createModals();
            $this->portedSortListBox();
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
               $('.video-setting-wrapper').addClass('hidden');
               $('.form-actions-wrapper').addClass('hidden');
           ");
        }

        /**
         * Creates and initializes the input controls for the form, including labels, text boxes, radio buttons,
         * and other components with their respective properties and styles. It also handles the visibility of certain
         * elements based on the board's settings.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createInputs(): void
        {
            $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
            $this->lblGroupTitle->Text = $this->objVideosSettings->getName();
            $this->lblGroupTitle->setCssStyle('font-weight', 600);

            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Dismissable = true;
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('Please create the first video!');
            $this->lblInfo->setCssStyle('margin-bottom', 0);

            if ($this->objVideosSettings->getVideosLocked() === 1) {
                $this->lblInfo->Display = false;
            } else {
                $this->lblInfo->Display = true;
            }

            $this->txtNewTitle = new Bs\TextBox($this);
            $this->txtNewTitle->Placeholder = t('Video title');
            $this->txtNewTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtNewTitle->setCssStyle('float', 'left');
            $this->txtNewTitle->Width = '45%';
            $this->txtNewTitle->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtNewTitle->Display = false;

            $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
            $this->lblTitleSlug->Text = t('View: ');
            $this->lblTitleSlug->setCssStyle('font-weight', 'bold');

            if ($this->objVideosSettings->getTitleSlug()) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
                $this->txtTitleSlug->setCssStyle('text-align', 'left;');
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objVideosSettings->getTitleSlug();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            $this->lblTitle  = new Q\Plugin\Control\Label($this);
            $this->lblTitle->Text = t('Video title');
            $this->lblTitle->addCssClass('col-md-2');
            $this->lblTitle->setCssStyle('font-weight', 'normal');
            $this->lblTitle->Required = true;

            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Video title');
            $this->txtTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtTitle->setHtmlAttribute('required', 'required');
            $this->txtTitle->AddAction(new EnterKey(), new Ajax('btnUpdate_Click'));
            $this->txtTitle->addAction(new EnterKey(), new Terminate());
            $this->txtTitle->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtTitle->addAction(new EscapeKey(), new Terminate());

            $this->lblIntroduction = new Q\Plugin\Control\Label($this);
            $this->lblIntroduction->Text = t('Introduction');
            $this->lblIntroduction->addCssClass('col-md-2');
            $this->lblIntroduction->setCssStyle('font-weight', 'normal');

            $this->txtIntroduction = new Q\Plugin\CKEditor($this);
            $this->txtIntroduction->Configuration = 'customConfig';
            $this->txtIntroduction->Placeholder = t('Introduction');

            $this->lblEmbedCode  = new Q\Plugin\Control\Label($this);
            $this->lblEmbedCode->Text = t('Embed code (</>)');
            $this->lblEmbedCode->addCssClass('col-md-2');
            $this->lblEmbedCode->setCssStyle('font-weight', 'normal');

            $this->txtEmbedCode = new Bs\TextBox($this);
            $this->txtEmbedCode->Placeholder = t('Embed code (</>)');
            $this->txtEmbedCode->setHtmlAttribute('autocomplete', 'off');
            $this->txtEmbedCode->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtEmbedCode->CrossScripting =  Q\Control\TextBoxBase::XSS_ALLOW;
            $this->txtEmbedCode->Rows = 2;
            $this->txtEmbedCode->Width = '80%';
            $this->txtEmbedCode->setCssStyle('float', 'left');

            $this->lblVideo  = new Q\Plugin\Control\Label($this);
            $this->lblVideo ->Text = t('Video');
            $this->lblVideo ->addCssClass('col-md-2');
            $this->lblVideo ->setCssStyle('font-weight', 'normal');

            $this->strVideo  = new Q\Plugin\Control\Label($this);
            $this->strVideo->HtmlEntities = false;

            $this->lblContent = new Q\Plugin\Control\Label($this);
            $this->lblContent->Text = t('Content');
            $this->lblContent->addCssClass('col-md-2');
            $this->lblContent->setCssStyle('font-weight', 'normal');

            $this->txtContent = new Q\Plugin\CKEditor($this);
            $this->txtContent ->Configuration = 'customConfig';

            $this->lblVideosGroupTitle = new Q\Plugin\Control\Label($this);
            $this->lblVideosGroupTitle->Text = t('Videos group');
            $this->lblVideosGroupTitle->addCssClass('col-md-2');
            $this->lblVideosGroupTitle->setCssStyle('font-weight', 400);

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->ContainerWidth = 'resolve';
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '90%';
            $this->lstGroupTitle->setCssStyle('float', 'left');
            $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroupTitle->addItem(t('- Change videos group -'), null, true);
            $this->lstGroupTitle->SelectedValue = $this->objVideo->SettingsId ?? null;
            $this->lstGroupTitle->addAction(new Change(), new Ajax('lstGroupTitle_Change'));

            if ($this->countByIsReserved === 1) {
                $this->lstGroupTitle->Enabled = false;
            } else {
                $this->lstGroupTitle->Enabled = true;
            }

            if ($this->countByVideos > 0) {
                $this->lstGroupTitle->addItems($this->lstVideosSettings_GetItems());
            }

            $this->lblVideoStatus = new Q\Plugin\Control\Label($this);
            $this->lblVideoStatus->Text = t('Status');
            $this->lblVideoStatus->addCssClass('col-md-2');
            $this->lblVideoStatus->setCssStyle('font-weight', 'normal');

            $this->lstVideoStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstVideoStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstVideoStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
            $this->lstVideoStatus->setCssStyle('margin-top', '-11px');
            $this->lstVideoStatus->addAction(new Change(), new Ajax('lstVideoStatus_Change'));

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblPostDate = new Q\Plugin\Control\Label($this);
            $this->lblPostDate->Text = t('Created');
            $this->lblPostDate->setCssStyle('font-weight', 'bold');

            $this->calPostDate = new Bs\Label($this);
            $this->calPostDate->Text = $this->objVideosSettings->PostDate ? $this->objVideosSettings->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostDate->setCssStyle('font-weight', 'normal');

            $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
            $this->lblPostUpdateDate->Text = t('Updated');
            $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

            $this->calPostUpdateDate = new Bs\Label($this);
            $this->calPostUpdateDate->Text = $this->objVideosSettings->PostUpdateDate ? $this->objVideosSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

            $this->lblAuthor = new Q\Plugin\Control\Label($this);
            $this->lblAuthor->Text = t('Author');
            $this->lblAuthor->setCssStyle('font-weight', 'bold');

            $this->txtAuthor  = new Bs\Label($this);
            $this->txtAuthor->Text = $this->objVideosSettings->Author;
            $this->txtAuthor->setCssStyle('font-weight', 'normal');

            $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
            $this->lblUsersAsEditors->Text = t('Editors');
            $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

            $this->txtUsersAsEditors  = new Bs\Label($this);
            $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
            $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->setCssStyle('font-weight', 'bold');

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstStatus->SelectedValue = $this->objVideosSettings->Status;
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
            $this->lstStatus->Enabled = true;
            $this->lstStatus->addAction(new Change(), new Ajax('lstStatus_Change'));
        }

        /**
         * Creates and initializes various buttons for the UI component with their respective styles, text, and actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createButtons(): void
        {
            $this->btnAddVideo = new Bs\Button($this);
            $this->btnAddVideo->Text = t(' Add video');
            $this->btnAddVideo->CssClass = 'btn btn-orange';
            $this->btnAddVideo->setCssStyle('float', 'left');
            $this->btnAddVideo->setCssStyle('margin-right', '10px');
            $this->btnAddVideo->CausesValidation = false;
            $this->btnAddVideo->addAction(new Click(), new Ajax('btnAddVideo_Click'));

            $this->btnVideoSave = new Bs\Button($this);
            $this->btnVideoSave->Text = t('Save');
            $this->btnVideoSave->CssClass = 'btn btn-orange';
            $this->btnVideoSave->setCssStyle('float', 'left');
            $this->btnVideoSave->setCssStyle('margin-left', '10px');
            $this->btnVideoSave->setCssStyle('margin-right', '10px');
            $this->btnVideoSave->Display = false;
            $this->btnVideoSave->addAction(new Click(), new Ajax('btnVideoSave_Click'));

            $this->btnVideoCancel = new Bs\Button($this);
            $this->btnVideoCancel->Text = t('Cancel');
            $this->btnVideoCancel->CssClass = 'btn btn-default';
            $this->btnVideoCancel->setCssStyle('float', 'left');
            $this->btnVideoCancel->CausesValidation = false;
            $this->btnVideoCancel->Display = false;
            $this->btnVideoCancel->addAction(new Click(), new Ajax('btnVideoCancel_Click'));

            //////////////////////////////////////////////////////////////////////////////////////////

            $this->btnEmbed = new Bs\Button($this);
            $this->btnEmbed->Text = t('Embed');
            $this->btnEmbed->CssClass = 'btn btn-orange';
            $this->btnEmbed->setCssStyle('float', 'right');
            $this->btnEmbed->addAction(new Click(), new Ajax('btnEmbed_Click'));

            $this->btnUpdate = new Bs\Button($this);
            $this->btnUpdate->Text = t('Update');
            $this->btnUpdate->CssClass = 'btn btn-orange';
            $this->btnUpdate->addAction(new Click(), new Ajax('btnUpdate_Click'));

            $this->btnDeleteVideo = new Bs\Button($this);
            $this->btnDeleteVideo->Text = t('Delete video');
            $this->btnDeleteVideo->CssClass = 'btn btn-danger';
            $this->btnDeleteVideo->addAction(new Click(), new Ajax('btnDeleteVideo_Click'));

            $this->btnCloseWindow = new Bs\Button($this);
            $this->btnCloseWindow->Text = t('Close the window');
            $this->btnCloseWindow->CssClass = 'btn btn-default';
            $this->btnCloseWindow->CausesValidation = false;
            $this->btnCloseWindow->addAction(new Click(), new Ajax('btnCloseWindow_Click'));

            $this->btnGoToSettings = new Bs\Button($this);
            $this->btnGoToSettings->Tip = true;
            $this->btnGoToSettings->ToolTip = t('Go to the videos settings manager');
            $this->btnGoToSettings->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToSettings->CssClass = 'btn btn-default';
            $this->btnGoToSettings->setCssStyle('float', 'right');
            $this->btnGoToSettings->addWrapperCssClass('center-button');
            $this->btnGoToSettings->CausesValidation = false;
            $this->btnGoToSettings->addAction(new Click(), new Ajax('btnGoToSettings_Click'));

            //////////////////////////////////////////////////////////////////////////////////////////

            $this->btnSort = new Bs\Button($this);
            $this->btnSort->Text = t('Sort');
            $this->btnSort->CssClass = 'btn btn-orange';
            $this->btnSort->setCssStyle('margin-left', '10px');
            $this->btnSort->CausesValidation = false;
            $this->btnSort->addAction(new Click(), new Ajax('btnSort_Click'));

            if ($this->countByVideos > 1) {
                $this->btnSort->Enabled = true;
            } else {
                $this->btnSort->Enabled = false;
            }

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
            $this->dlgSorter = new Q\Plugin\Control\SortWrapper($this);
            $this->dlgSorter->createNodeParams([$this, 'Sorter_Draw']);
            $this->dlgSorter->createControlButtons([$this, 'Buttons_Draw']);
            $this->dlgSorter->createRenderInputs([$this, 'Dates_Draw']);
            $this->dlgSorter->setDataBinder('Sorter_Bind');
            $this->dlgSorter->addCssClass('sortable');
            $this->dlgSorter->Placeholder = 'placeholder';
            $this->dlgSorter->Handle = '.reorder';
            $this->dlgSorter->Items = 'div.div-block';

            $this->dlgSorter->addAction(new SortableStop(), new Ajax('sortable_stop'));
            $this->dlgSorter->watch(QQN::Videos());
        }

        /**
         * Binds data to the sorter dialog by querying videos based on the associated settings ID.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function Sorter_Bind(): void
        {
            $this->dlgSorter->DataSource = Videos::QueryArray(
                QQ::Equal(QQN::Videos()->SettingsId, $this->intId),
                QQ::Clause(
                    QQ::orderBy(QQN::Videos()->Order)
                )
            );
        }

        /**
         * Prepares and returns an array representing the video details for sorting purposes.
         *
         * @param Videos $objVideo An instance of the Videos class representing the video to be processed.
         * @return array An associative array containing the video's id, name, order, and status.
         */
        public function Sorter_Draw(Videos $objVideo): array
        {
            $a['id'] = $objVideo->Id;
            $a['name'] = $objVideo->Title;
            $a['order'] = $objVideo->Order;
            $a['status'] = $objVideo->Status;
            return $a;
        }

        /**
         * Generates and renders "Edit" and "Delete" buttons for the given video object.
         *
         * @param Videos $objVideo The video object for which the buttons are created.
         *
         * @return string The HTML output of the rendered "Edit" and "Delete" buttons.
         * @throws Caller
         */
        public function Buttons_Draw(Videos $objVideo): string
        {
            $strEditId = 'btnEdit' . $objVideo->Id;

            if (!$btnEdit = $this->getControl($strEditId)) {
                $btnEdit = new Bs\Button($this->dlgSorter, $strEditId);
                $btnEdit->Glyph = 'glyphicon glyphicon-pencil';
                $btnEdit->Tip = true;
                $btnEdit->ToolTip = t('Edit');
                $btnEdit->CssClass = 'btn btn-icon btn-xs edit';
                $btnEdit->ActionParameter = $objVideo->Id;
                $btnEdit->UseWrapper = false;
                $btnEdit->addAction(new Click(), new Ajax('btnEdit_Click'));
            }

            $strDeleteId = 'btnDelete' . $objVideo->Id;

            if (!$btnDelete = $this->getControl($strDeleteId)) {
                $btnDelete = new Bs\Button($this->dlgSorter, $strDeleteId);
                $btnDelete->Glyph = 'glyphicon glyphicon-trash';
                $btnDelete->Tip = true;
                $btnDelete->ToolTip = t('Delete');
                $btnDelete->CssClass = 'btn btn-icon btn-xs delete';
                $btnDelete->ActionParameter = $objVideo->Id;
                $btnDelete->UseWrapper = false;
                $btnDelete->addAction(new Click(), new Ajax('btnDelete_Click'));
            }

            return $btnEdit->render(false) . $btnDelete->render(false);
        }

        /**
         * Renders and returns the formatted post-date and post-update date for a given video.
         *
         * @param Videos $objVideo The video object containing the post-date and post-update date information.
         *
         * @return string The rendered output of post-date and post-update date labels.
         * @throws Caller
         */
        public function Dates_Draw(Videos $objVideo): string
        {
            $strPostDate = 'calPostDate' . $objVideo->Id;

            if (!$calPostDate = $this->getControl($strPostDate)) {
                $calPostDate =  new Bs\Label($this->dlgSorter, $strPostDate);
                $calPostDate->Text = $objVideo->PostDate ? $objVideo->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $calPostDate->setCssStyle('float', 'left');
                //$calPostDate->setCssStyle('padding-right', '30px');
                $calPostDate->setCssStyle('font-weight', 'normal');
            }

            $strPostUpdateDate = 'calPostUpdateDate' . $objVideo->Id;

            if (!$calPostUpdateDate = $this->getControl($strPostUpdateDate)) {
                $calPostUpdateDate =  new Bs\Label($this->dlgSorter, $strPostUpdateDate);
                $calPostUpdateDate->Text = $objVideo->PostUpdateDate ? $objVideo->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
                $calPostUpdateDate->setCssStyle('float', 'right');
                $calPostUpdateDate->setCssStyle('font-weight', 'normal');
            }

            return $calPostDate->render(false) . ' ' . $calPostUpdateDate->render(false);
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
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The new title of video has been successfully created and saved.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('<strong>Sorry</strong>, creating or saving the new title of video failed!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Sorry!</strong> The title is required!');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr8 = new Q\Plugin\Toastr($this);
            $this->dlgToastr8->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr8->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr8->Message = t('<strong>Well done!</strong> The order of videos was successfully updated!');
            $this->dlgToastr8->ProgressBar = true;

            $this->dlgToastr9 = new Q\Plugin\Toastr($this);
            $this->dlgToastr9->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr9->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr9->Message = t('<strong>Sorry</strong>, updating the order of videos failed!');
            $this->dlgToastr9->ProgressBar = true;

            $this->dlgToastr10 = new Q\Plugin\Toastr($this);
            $this->dlgToastr10->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr10->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr10->Message = t('<strong>Well done!</strong> The video was successfully deleted!');
            $this->dlgToastr10->ProgressBar = true;

            $this->dlgToastr11 = new Q\Plugin\Toastr($this);
            $this->dlgToastr11->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr11->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr11->Message = t('<strong>Sorry</strong>, the video deletion failed!');
            $this->dlgToastr11->ProgressBar = true;

            $this->dlgToastr12 = new Q\Plugin\Toastr($this);
            $this->dlgToastr12->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr12->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr12->Message = t('<strong>Sorry</strong>, the title is required!');
            $this->dlgToastr12->ProgressBar = true;

            $this->dlgToastr13 = new Q\Plugin\Toastr($this);
            $this->dlgToastr13->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr13->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr13->Message = t('<strong>Well done!</strong> The video data has been successfully updated!');
            $this->dlgToastr13->ProgressBar = true;

            $this->dlgToastr14 = new Q\Plugin\Toastr($this);
            $this->dlgToastr14->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr14->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr14->Message = t('<strong>Sorry</strong>, updating the video data failed!');
            $this->dlgToastr14->ProgressBar = true;

            $this->dlgToastr15 = new Q\Plugin\Toastr($this);
            $this->dlgToastr15->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr15->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr15->Message = t('<strong>Well done!</strong> Updates to some records for this video were discarded, and the record has been restored!');
            $this->dlgToastr15->ProgressBar = true;

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->dlgToastr16 = new Q\Plugin\Toastr($this);
            $this->dlgToastr16->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr16->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr16->Message = t('<strong>Well done!</strong> This video with data has now been made public!');
            $this->dlgToastr16->ProgressBar = true;

            $this->dlgToastr17 = new Q\Plugin\Toastr($this);
            $this->dlgToastr17->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr17->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr17->Message = t('<strong>Well done!</strong> This video with data is now hidden!');
            $this->dlgToastr17->ProgressBar = true;

            $this->dlgToastr18 = new Q\Plugin\Toastr($this);
            $this->dlgToastr18->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr18->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr18->Message = t('<strong>Sorry</strong>, the video details cannot be updated or published without the video!');
            $this->dlgToastr18->ProgressBar = true;

            $this->dlgToastr19 = new Q\Plugin\Toastr($this);
            $this->dlgToastr19->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr19->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr19->Message = t('<p><strong>Well done!</strong> This video has been successfully deleted!</p><p>At the same time, its associated data will be hidden!</p>');
            $this->dlgToastr19->ProgressBar = true;

            $this->dlgToastr20 = new Q\Plugin\Toastr($this);
            $this->dlgToastr20->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr20->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr20->Message = t('<p><strong>Well done!</strong> The video order has been successfully sorted!</p>');
            $this->dlgToastr20->ProgressBar = true;
        }

        /**
         * Initializes multiple modal dialogs with various configurations, including text, title, header classes, buttons,
         * and actions. These modals provide feedback and confirmation requests for different user actions related to board
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
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the videos group for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This videos group is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This videos group has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this video and its associated data?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
            $this->dlgModal5->Title = 'Warning';
            $this->dlgModal5->HeaderClasses = 'btn-danger';
            $this->dlgModal5->addButton("I accept", null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal5->addCloseButton(t("I'll cancel"));
            $this->dlgModal5->addAction(new DialogButton(), new Ajax('deleteItem_Click'));

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this video?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
            $this->dlgModal6->Title = 'Warning';
            $this->dlgModal6->HeaderClasses = 'btn-danger';
            $this->dlgModal6->addButton("I accept", null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal6->addCloseButton(t("I'll cancel"));
            $this->dlgModal6->addAction(new DialogButton(), new Ajax('deleteVideo_Click'));

            $this->dlgModal7 = new Bs\Modal($this);
            $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to transfer this video along with its data from this video group to another video group?</p>');
            $this->dlgModal7->Title = t('Warning');
            $this->dlgModal7->HeaderClasses = 'btn-danger';
            $this->dlgModal7->addButton(t("I accept"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal7->addCloseButton(t("I'll cancel"));
            $this->dlgModal7->addAction(new DialogButton(), new Ajax('moveItem_Click'));

            $this->dlgModal8 = new Bs\Modal($this);
            $this->dlgModal8->AutoRenderChildren = true;
            $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to sort the video order?</p>
                                        <p style="line-height: 25px; margin-bottom: 2px;"><strong>Note!</strong> All videos in the selected video group
                                         will be sorted by their <u>post date</u> in ascending or descending order.</p>
                                        <p style="line-height: 25px; margin-bottom: 2px;">Please choose the direction from the dropdown menu and confirm or cancel!</p>');
            $this->dlgModal8->Title = t('Warning');
            $this->dlgModal8->HeaderClasses = 'btn-danger';
            $this->dlgModal8->addButton(t("I confirm"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal8->addCloseButton(t("I'll cancel"));
            $this->dlgModal8->addAction(new DialogButton(), new Ajax('sortItems_Click'));

            $this->dlgModal9 = new Bs\Modal($this);
            $this->dlgModal9->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Please select a target group!</p>');
            $this->dlgModal9->Title = t("Tip");
            $this->dlgModal9->HeaderClasses = 'btn-darkblue';
            $this->dlgModal9->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        }

        /**
         * Initializes and configures the UI components for sorting a direction.
         *
         * This method creates and configures a label and a select input to allow the
         * user to specify the sorting direction (ascending or descending) within a modal dialog.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function portedSortListBox(): void
        {
            $this->lblDirection = new Q\Plugin\Control\Label($this->dlgModal8);
            $this->lblDirection->Text = t('Direction:');
            $this->lblDirection->setCssStyle('width', '100%');
            $this->lblDirection->setCssStyle('font-weight', 600);
            $this->lblDirection->setCssStyle('padding-top', '5px');
            $this->lblDirection->setCssStyle('padding-bottom', '5px');
            $this->lblDirection->UseWrapper = false;

            $this->dlgDirection = new Q\Plugin\Select2($this->dlgModal8);
            $this->dlgDirection->Width = '100%';
            $this->dlgDirection->MinimumResultsForSearch = -1; // If you want to remove the search box, set it to "-1"
            $this->dlgDirection->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->dlgDirection->Theme = 'web-vauu';
            $this->dlgDirection->addItems([t('Ascending'), t('Descending')], [['false', true], ['true', false]]);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Retrieves a list of VideosSettings items based on specified conditions and clauses.
         * This method generates an array of ListItem objects representing VideosSettings entries,
         * with an appropriate selection state determined by the associated Videos object.
         *
         * @return ListItem[] An array of ListItem objects, each representing a VideosSettings entity.
         * @throws Caller
         * @throws DateMalformedStringException
         * @throws InvalidCast
         */
        public function lstVideosSettings_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objVideosSettingsCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objSettingsCursor = VideosSettings::queryCursor($objCondition, $this->objVideosSettingsClauses);

            // Iterate through the Cursor
            while ($objSettings = VideosSettings::instantiateCursor($objSettingsCursor)) {
                $objListItem = new ListItem($objSettings->__toString(), $objSettings->Id);
                if (($this->objVideo->Settings) && ($this->objVideo->Settings->Id == $objSettings->Id))
                    $objListItem->Selected = true;

                    if ($this->objVideo->Settings->Id == $objSettings->Id) {
                        $objListItem->Disabled = true;
                    }

                $a[] = $objListItem;
            }

            return $a;
        }

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

                $objSorter = Videos::load($id);
                $objSorter->setOrder($order);
                $objSorter->setPostUpdateDate(Q\QDateTime::now());
                $objSorter->save();
            }

            // Let's check if the array is not empty
            if (!empty($arr)) {
                $this->dlgToastr8->notify();
            } else {
                $this->dlgToastr9->notify();
            }

            Application::executeJavaScript("
                $('.video-setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
           ");

            $this->objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
            $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objVideosSettings->save();

            $this->calPostUpdateDate->Text = $this->objVideosSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
        }

        /**
         * Handles the click event for the "Add Video" button. Initializes the UI for adding a new video and updates the
         * display of related components.
         *
         * @param ActionParams $params Parameters associated with the action triggering the button click.
         *
         * @return void
         * @throws Caller
         */
        protected function btnAddVideo_Click(ActionParams $params): void
        {
            $this->btnAddVideo->Enabled = false;
            $this->txtNewTitle->Display = true;
            $this->btnVideoSave->Display = true;
            $this->btnVideoCancel->Display = true;
            $this->txtNewTitle->Text = '';
            $this->txtNewTitle->focus();

            Application::executeJavaScript("
                jQuery(\"[data-value='$this->intClick']\").removeClass('activated');
                $('.video-setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
           ");
        }

        /**
         * Handles the click event for saving a video.
         *
         * @param ActionParams $params Parameters associated with the action triggering the button click.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnVideoSave_Click(ActionParams $params): void
        {
            if (trim($this->txtNewTitle->Text) !== '') {
                $objVideo = new Videos();
                $objVideo->setSettingsId($this->intId);
                $objVideo->setSettingsIdTitle($this->objVideosSettings->getName());
                $objVideo->setTitle(trim($this->txtNewTitle->Text));
                $objVideo->setOrder(Videos::generateOrder($this->intId));
                $objVideo->setStatus(2);
                $objVideo->setPostDate(Q\QDateTime::now());
                $objVideo->save();

                // A check must be made here if the first record and the following records occur in this group,
                // then set "videos_locked" to 1 in the VideosSettings column, etc...

                if (Videos::countBySettingsId($this->intId) !== 0) {
                    if ($this->objVideosSettings->getVideosLocked() === 0) {
                        $this->objVideosSettings->setVideosLocked(1);
                    }
                }

                $this->objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::Now());
                $this->objVideosSettings->save();

                $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
                $this->calPostUpdateDate->Text = $this->objVideosSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

                $this->refreshDisplay();

                if ($objVideo->getId()) {
                    $this->txtNewTitle->Text = '';
                    $this->btnAddVideo->Enabled = true;
                    $this->txtNewTitle->Display = false;
                    $this->btnVideoSave->Display = false;
                    $this->btnVideoCancel->Display = false;

                    $this->dlgToastr1->notify();
                } else {
                    $this->dlgToastr2->notify();
                }
            } else {
                $this->txtNewTitle->Text = '';
                $this->txtNewTitle->focus();
                $this->btnAddVideo->Enabled = false;
                $this->txtNewTitle->Display = true;
                $this->btnVideoSave->Display = true;
                $this->btnVideoCancel->Display = true;

                $this->dlgToastr3->notify();
            }

            if ($this->objVideosSettings->getVideosLocked() === 1) {
                $this->lblInfo->Display = false;
            } else {
                $this->lblInfo->Display = true;
            }

            if ($this->txtEmbedCode->Text) {
                $this->btnDeleteVideo->Enabled = true;
            } else {
                $this->btnDeleteVideo->Enabled = false;
            }

            $this->dlgSorter->refresh();
        }

        /**
         * Handles the click event for the "Cancel" video button.
         *
         * @param ActionParams $params Parameters associated with the action triggering the click event.
         * @return void
         */
        protected function btnVideoCancel_Click(ActionParams $params): void
        {
            $this->btnAddVideo->Enabled = true;
            $this->txtNewTitle->Display = false;
            $this->btnVideoSave->Display = false;
            $this->btnVideoCancel->Display = false;
            $this->txtNewTitle->Text = '';
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
            $objEdit = Videos::load($intEditId);
            $this->intClick = $intEditId;

            Application::executeJavaScript("$('.js-video-wrapper').get(0).scrollIntoView({behavior: 'smooth'});");

            $this->txtTitle->Text = $objEdit->Title ?? '';
            $this->txtIntroduction->Text = $objEdit->Introduction ?? '';
            $this->txtEmbedCode->Text = $objEdit->EmbedCode ?? '';
            $this->strVideo->Text = $objEdit->EmbedCode ?? '';
            $this->txtContent->Text = $objEdit->Content ?? '';
            $this->lstVideoStatus->SelectedValue = $objEdit->Status;

            if ($objEdit->EmbedCode) {
                Application::executeJavaScript("
                    $('.js-video').removeClass('hidden');
                    $('.js-embed-code').addClass('hidden');
                ");
            } else {
                $this->txtEmbedCode->Text = '';
                Application::executeJavaScript("
                    $('.js-video').addClass('hidden');
                    $('.js-embed-code').removeClass('hidden');
                ");
            }

            Application::executeJavaScript("
                $(\"[data-value='$intEditId']\").addClass('activated');
                $(\"[data-value='$intEditId']\").removeClass('inactivated');
                
                $('.video-setting-wrapper').fadeIn(500, function() {
                    $(this).removeClass('hidden');
                });
                
                $('.form-actions-wrapper').fadeIn(500, function() {
                    $(this).removeClass('hidden');
                });
           ");

            if ($this->txtEmbedCode->Text) {
                $this->btnDeleteVideo->Enabled = true;
            } else {
                $this->btnDeleteVideo->Enabled = false;
            }

            $this->dlgSorter->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the embed button click event, updating embed information and refreshing associated UI elements.
         *
         * @param ActionParams $params Parameters associated with the action triggering the button click.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnEmbed_Click(ActionParams $params): void
        {
            if (!$this->txtEmbedCode->Text) {
                $this->btnDeleteVideo->Enabled = false;
                $this->dlgToastr14->notify();
                $this->dlgSorter->refresh();
                return;
            } else {
                $objEmbed = Videos::load($this->intClick);
                $cleanedEmbedCode = $this->cleanEmbedCode($this->txtEmbedCode->Text);

                $objEmbed ->setEmbedCode($cleanedEmbedCode);
                $objEmbed ->setPostUpdateDate(Q\QDateTime::now());
                $this->strVideo->Text = $objEmbed->EmbedCode;
                $objEmbed->save();

                if ($objEmbed->getEmbedCode()) {
                    $this->btnDeleteVideo->Enabled = true;
                    $this->dlgToastr13->notify();
                }
            }

            Application::executeJavaScript("
                    $('.js-video').removeClass('hidden');
                    $('.js-embed-code').addClass('hidden');
                ");

            $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objVideosSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
            $this->calPostUpdateDate->Text = $this->objVideosSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the change event for the group title list.
         *
         * @param ActionParams $params Parameters associated with the action triggering the change.
         * @return void
         */
        protected function lstGroupTitle_Change(ActionParams $params): void
        {
            if (!$this->lstGroupTitle->SelectedValue) {
                $this->dlgModal9->showDialogBox();
                return;
            }

            if ($this->lstGroupTitle->SelectedValue !== $this->objVideo->getSettingsId()) {
                $this->dlgModal7->showDialogBox();
            }
        }

        /**
         * Handles the click event to move a video item to a different group.
         *
         * @param ActionParams $params Parameters associated with the action triggering the move.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function moveItem_Click(ActionParams $params): void
        {
            $this->dlgModal7->hideDialogBox();

            $objMove = Videos::load($this->intClick);

            $objGroupId = VideosSettings::loadById($this->lstGroupTitle->SelectedValue);

            $currentCount = Videos::countBySettingsId($objMove->getSettingsId());
            $nextCount = Videos::countBySettingsId($objGroupId->getId());

            $objTargetGroup = VideosSettings::loadById($objGroupId->getId());

            if ($nextCount == 0) {
                $objTargetGroup->setVideosLocked(1);
                $objTargetGroup->save();
            }

            $objGroup = VideosSettings::loadById($objMove->getSettingsId());

            if ($currentCount) {
                if ($currentCount == 1) {
                    $objGroup->setVideosLocked(0);
                } else {
                    $objGroup->setVideosLocked(1);
                }
                $objGroup->save();
            }

            $objVideo = Videos::load($objMove->getId());
            $objVideo->setSettingsId($this->lstGroupTitle->SelectedValue);
            $objVideo->setSettingsIdTitle($this->lstGroupTitle->SelectedName);
            $objVideo->setOrder(Videos::generateOrder($this->lstGroupTitle->SelectedValue));
            $objVideo->setPostUpdateDate(Q\QDateTime::now());
            $objVideo->save();

            Application::redirect('videos_edit.php?id=' . $objGroupId->getId() . '&group=' . $objGroupId->getMenuContentId());
        }

        /**
         * Handles the change event for the video status list.
         *
         * @param ActionParams $params Parameters associated with the action triggering the change.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstVideoStatus_Change(ActionParams $params): void
        {
            $objVideo = Videos::load($this->intClick);

            if ($objVideo->getEmbedCode() && !$this->txtTitle->Text) {
                $this->lstVideoStatus->SelectedValue = 2;
                $this->txtTitle->Text = $objVideo->getTitle();
                $this->txtTitle->focus();
                $this->dlgToastr12->notify();
                $this->dlgSorter->refresh();
                return;
            }

            if ($objVideo->getEmbedCode() && $objVideo->getTitle()) {
                $objVideo->setStatus($this->lstVideoStatus->SelectedValue);
                $objVideo->setPostUpdateDate(Q\QDateTime::now());
                $objVideo->save();

                if ($objVideo->getStatus() === 2) {
                    $this->dlgToastr17->notify();
                } else {
                    $this->dlgToastr16->notify();
                }
            } else if (!$objVideo->getEmbedCode() && $objVideo->getTitle()) {
                $this->lstVideoStatus->SelectedValue = 2;
                $objVideo->setStatus(2);
                $objVideo->setPostUpdateDate(Q\QDateTime::now());
                $objVideo->save();
                $this->dlgToastr18->notify();
            }

            // Continue to update additional data and refresh the screen
            $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objVideosSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
            $this->calPostUpdateDate->Text = $this->objVideosSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            Application::executeJavaScript("$(\"[data-value='$objVideo']\").addClass('activated');");

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        /**
         * Handles the change event for the status dropdown, updating various components and invoking modals based on specific conditions.
         *
         * @param ActionParams $params The parameters passed to the method, usually containing information related to the triggered event.
         *
         * @return void This method does not return a value, as its purpose is to update the UI and internal state.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstStatus_Change(ActionParams $params): void
        {
            $objMenuContent = MenuContent::loadById($this->objMenu->getId());

            Application::executeJavaScript("
                $('.video-setting-wrapper').fadeOut('slow', function() {
                    $(this).addClass('hidden');
                });
                
                $('.form-actions-wrapper').fadeOut('slow', function() {
                    $(this).addClass('hidden');
                });"
            );

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

            $this->objVideosSettings->setStatus($this->lstStatus->SelectedValue);
            $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objVideosSettings->save();
            $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::Now());
            $this->objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objVideosSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
            $this->calPostUpdateDate->Text = $this->objVideosSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            if ($this->objVideosSettings->getStatus() === 2) {
                $this->dlgModal3->showDialogBox();
            } else {
                $this->dlgModal4->showDialogBox();
            }

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }


        /**
         * Updates input fields with the current status from the video settings.
         *
         * @return void
         */
        protected function updateInputFields(): void
        {
            $this->lstStatus->SelectedValue = $this->objVideosSettings->getStatus();
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
            $objUpdate = Videos::load($this->intClick);

            // Check if $objUpdate is available
            if (!$objUpdate) {
                $this->dlgToastr14->notify();
                return;
            }

            // Check if Title is empty
            if ($this->txtTitle->Text == '') {
                $this->dlgToastr12->notify();
                return;
            }

            $objUpdate->Title = $this->txtTitle->Text;
            $objUpdate->Introduction = $this->txtIntroduction->Text;
            $objUpdate->EmbedCode = $this->strVideo->Text;
            $objUpdate->Content = $this->txtContent->Text;
            $objUpdate->Status = $this->lstVideoStatus->SelectedValue;
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
            $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objVideosSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
            $this->calPostUpdateDate->Text = $this->objVideosSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            if ($objUpdate->EmbedCode) {
                Application::executeJavaScript("
                    $('.js-video').removeClass('hidden');
                    $('.js-embed-code').addClass('hidden');
                ");
            } else {
                $this->txtEmbedCode->Text = '';
                Application::executeJavaScript("
                    $('.js-video').addClass('hidden');
                    $('.js-embed-code').removeClass('hidden');
                ");
            }

            Application::executeJavaScript("
                $(\"[data-value='$this->intClick']\").addClass('activated');
                $(\"[data-value='$this->intClick']\").removeClass('inactivated');
                $('.video-setting-wrapper').removeClass('hidden');
                $('.form-actions-wrapper').removeClass('hidden');
           ");

            $this->refreshDisplay();
            $this->dlgSorter->refresh();
        }

        /**
         * Handles the click event for delete a video button.
         *
         * @param ActionParams $params Parameters associated with the action triggering the click.
         * @return void
         */
        protected function btnDeleteVideo_Click(ActionParams $params): void
        {
            $this->dlgModal6->showDialogBox();
        }

        /**
         * Handles the click event to delete a video, updating its status and related UI elements.
         *
         * @param ActionParams $params Parameters associated with the action triggering the deletion.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function deleteVideo_Click(ActionParams $params): void
        {
            $objVideo = Videos::loadById($this->intClick);

            $objVideo->setEmbedCode(null);
            $objVideo->setStatus(2);
            $objVideo->save();

            Application::executeJavaScript("
                $('.js-video').addClass('hidden');
                $('.js-embed-code').removeClass('hidden');
            ");

            $this->strVideo->Text = '';
            $this->txtEmbedCode->Text = '';
            $this->txtEmbedCode->focus();
            $this->btnDeleteVideo->Enabled = false;

            $this->lstVideoStatus->SelectedValue = 2;

            $this->objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objVideosSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
            $this->calPostUpdateDate->Text = $this->objVideosSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();
            $this->dlgModal6->hideDialogBox();

            if (!$objVideo->getEmbedCode()) {
                $this->dlgToastr19->notify();
            }
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
            Application::executeJavaScript("
                $(\"[data-value='$this->intClick']\").removeClass('activated');
                $('.video-setting-wrapper').addClass('hidden');
                $('.form-actions-wrapper').addClass('hidden');
           ");

            $this->dlgSorter->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the item escape action.
         *
         * @param ActionParams $params Parameters associated with the action triggering the click event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
            $objCancel = Videos::load($this->intClick);

            // Check if $objCancel is available
            if ($objCancel) {
                $this->dlgToastr15->notify();
            }

            $this->txtTitle->Text = $objCancel->Title;
            $this->txtIntroduction->Text = $objCancel->Introduction;
            $this->strVideo->Text = $objCancel->EmbedCode;
            $this->txtContent->Text = $objCancel->Content;
            $this->lstVideoStatus->SelectedValue = $objCancel->Status;
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
         * Handles the click event to delete an item, including associated updates and UI changes.
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
            $objVideo = Videos::loadById($this->intClick);

            if (Videos::countBySettingsId($objVideo->getSettingsId()) === 1) {
                if ($this->objVideosSettings->getVideosLocked() === 1) {
                    $this->objVideosSettings->setVideosLocked(0);
                }
            }

            $objVideo->delete();

            Application::executeJavaScript("
                $('.video-setting-wrapper').fadeOut(500, function() {
                    $(this).addClass('hidden');
                });
                
                $('.form-actions-wrapper').fadeOut(500, function() {
                    $(this).addClass('hidden');
                });
            ");

            if ($objVideo->getId() !== $objVideo) {
                $this->dlgToastr10->notify();
            } else {
                $this->dlgToastr11->notify();
            }

            if ($this->objVideosSettings->getVideosLocked() === 1) {
                $this->lblInfo->Display = false;
            } else {
                $this->lblInfo->Display = true;
            }

            $this->objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objVideosSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
            $this->calPostUpdateDate->Text = $this->objVideosSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->dlgModal5->hideDialogBox();
            $this->dlgSorter->refresh();
            $this->refreshDisplay();

        }

        /**
         * Handles the click event for the "Go to Settings" button.
         *
         * @param ActionParams $params Parameters associated with the action triggering the button click.
         *
         * @return void
         * @throws Throwable
         */
        public function btnGoToSettings_Click(ActionParams $params): void
        {
            $_SESSION['videos'] = $this->intId;
            $_SESSION['group'] = $this->intGroup;

            Application::redirect('settings_manager.php#videosSettings_tab');
        }

        /**
         * Handles the click event for the sort button.
         *
         * @param ActionParams $params Parameters associated with the action triggering the click event.
         * @return void
         */
        protected function btnSort_Click(ActionParams $params): void
        {
            $this->dlgModal8->showDialogBox();
        }

        /**
         * Handles the click event for sorting items.
         *
         * @param ActionParams $params Parameters associated with the action triggering the click.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function sortItems_Click(ActionParams $params): void
        {
            $this->dlgModal8->hideDialogBox();

            $currentValues = [];
            $objCurrentArray = Videos::QueryArray(QQ::Equal(QQN::Videos()->SettingsId, $this->intId));

            foreach ($objCurrentArray as $objCurrent) {
                $currentValues[] = $objCurrent->getOrder();
            }

            $objVideoArray = Videos::QueryArray(
                QQ::Equal(QQN::Videos()->SettingsId, $this->intId),
                QQ::Clause(
                    QQ::OrderBy(QQN::Videos()->PostDate, $this->dlgDirection->SelectedValue)
                )
            );

            foreach ($objVideoArray as $order => $objVideo) {
                $objMove = Videos::loadById($objVideo->getId());
                $objMove->setOrder($order);
                $objMove->setPostUpdateDate(Q\QDateTime::now());
                $objMove->save();
            }

            $this->objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $this->objVideosSettings->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objVideosSettings->getUserAsVideosEditorsArray());
            $this->calPostUpdateDate->Text = $this->objVideosSettings->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');


            $nextValues = [];
            $objNextArray = Videos::QueryArray(QQ::Equal(QQN::Videos()->SettingsId, $this->intId));

            foreach ($objNextArray as $objNext) {
                $nextValues[] = $objNext->getOrder();
            }

            if ($currentValues !== $nextValues) {
                $this->dlgToastr20->notify();
            }

            $this->dlgSorter->refresh();
            $this->refreshDisplay();
        }

        /**
         * Handles the click event for the back button.
         *
         * @param ActionParams $params Parameters associated with the action triggering the click event.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnBack_Click(ActionParams $params): void
        {
            $this->redirectToListPage();
        }

        /**
         * Redirects the user to the list page for videos.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            Application::redirect('videos_list.php');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Updates the visibility state of various display components based on the video settings' attributes.
         *
         * This method adjusts the display of post-date, update date, author, and users-as-editors elements
         * based on the current state of the videos settings, such as post-date, update date, and authorship
         * data availability, as well as the count of users as video editors.
         *
         * @return void
         */
        protected function refreshDisplay(): void
        {
            if ($this->objVideosSettings->getPostDate() &&
                !$this->objVideosSettings->getPostUpdateDate() &&
                $this->objVideosSettings->getAuthor() &&
                !$this->objVideosSettings->countUsersAsVideosEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = false;
                $this->txtAuthor->Display = false;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($this->objVideosSettings->getPostDate() &&
                $this->objVideosSettings->getPostUpdateDate() &&
                $this->objVideosSettings->getAuthor() &&
                !$this->objVideosSettings->countUsersAsVideosEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($this->objVideosSettings->getPostDate() &&
                $this->objVideosSettings->getPostUpdateDate() &&
                $this->objVideosSettings->getAuthor() &&
                $this->objVideosSettings->countUsersAsVideosEditors()) {
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

        /**
         * Cleans the input embed code by removing unnecessary tags and attributes, ensuring a properly formatted iframe.
         *
         * @param string $code The embed code to be cleaned.
         *
         * @return string The cleaned and formatted embed code.
         */
        private function cleanEmbedCode(string $code): string
        {
            // Removes div tags and other wrappers
            $code = preg_replace('/<div[^>]*>|<\/div>/', '', $code);

            // Removes the width and height attributes from the iframe
            $code = preg_replace('/\s*(width|height)=["\'].*?["\']/', '', $code);

            // Ensures the iframe starts correctly
            $code = preg_replace('/^.*?(<iframe\b.*?>).*?(<\/iframe>).*$/s', '$1$2', $code);

            // Returns the cleaned embed code
            return trim($code);
        }
    }
    VideoEditForm::run('VideoEditForm');