<?php
    require('qcubed.inc.php');
    require('../i18n/i18n-lib.inc.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed\Project\Control\FormBase as Form;
    use QCubed as Q;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use Random\RandomException;
    use QCubed\Action\Ajax;
    use QCubed\Event\Click;
    use QCubed\Event\DialogButton;
    use QCubed\Jqui\Event\SortableStop;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\QString;
    use QCubed\Query\QQ;

    /**
     * Represents a sample form class that extends the base Form class. It defines various components
     * such as Toastr notifications, Modals, Buttons, Inputs, and other objects needed to manage
     * specific user interface functionalities. The class contains methods to initialize the form
     * and create the required UI elements.
     */
    class MenuForm extends Form
    {
        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;
        protected Q\Plugin\Toastr $dlgToastr3;

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
        protected Bs\Modal $dlgModal14;
        protected Bs\Modal $dlgModal15;
        protected Bs\Modal $dlgModal16;
        protected Bs\Modal $dlgModal17;
        protected Bs\Modal $dlgModal18;
        protected Bs\Modal $dlgModal19;
        protected Bs\Modal $dlgModal20;
        protected Bs\Modal $dlgModal21;
        protected Bs\Modal $dlgModal22;
        protected Bs\Modal $dlgModal23;
        protected Bs\Modal $dlgModal24;
        protected Bs\Modal $dlgModal25;

        protected Bs\Modal $dlgModal26;
        protected Bs\Modal $dlgModal27;
        protected Bs\Modal $dlgModal28;
        protected Bs\Modal $dlgModal29;
        protected Bs\Modal $dlgModal30;

        protected Bs\Button $btnAddMenuItem;
        protected Bs\TextBox $txtMenuText;
        protected Bs\Button $btnSave;
        protected Bs\Button $btnCancel;

        protected Bs\Button $btnCollapseAll;
        protected Bs\Button $btnExpandAll;

        protected Q\Plugin\Control\Alert $lblHomePageAlert;
        protected Q\Plugin\Control\NestedSortable $tblSorter;
        protected int $intDeleteId;

        protected array $strSelectedValues = [];

        protected object $objMenu;
        protected object $objMenuContent;
        protected ?object $objMetadata;
        protected ?object $objArticle;
        protected ?object $objNewsSettings;
        protected ?object $objNews;
        protected ?object $objGallerySettings;
        protected ?object $objGalleryList;
        protected ?object $objEventsSettings;
        protected ?object $objEventsCalendar;
        protected ?object $objSportsSettings;
        protected ?object $objSportsCalendar;
        protected ?object $objBoardsSettings;
        protected ?object $objMembersSettings;
        protected ?object $objVideosSettings;
        protected ?object $objStatisticsSettings;
        protected ?object $objLinksSettings;
        protected ?object $objFrontendLinks;
        protected ?object $objFrontendGroupedLinks;
        protected string $updatedUrl;

        /**
         * Sets up the form by calling parent initialization and delegating the
         * creation of inputs, buttons, and toastr components. This method orchestrates
         * the initialization of key interface elements required to form functionality.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
            $this->createToastr();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures input controls for the interface. This includes
         * creating and setting up a bootstrap alert and a nested sortable component
         * for managing sortable items in the UI.
         *
         * @return void
         * @throws Caller
         */
        protected function createInputs(): void
        {
            // Bootstrap Alert

            $this->lblHomePageAlert = new Q\Plugin\Control\Alert($this);
            $this->lblHomePageAlert->Display = false;
            $this->lblHomePageAlert->Dismissable = true;
            $this->lblHomePageAlert->addCssClass('alert alert-danger');
            $this->lblHomePageAlert->Text = t('<strong>Note:</strong> The first item (Homepage) is always the front page and 
                                            should not be placed under any other element. It defines the system\'s default front page.');

            // NestedSortable

            $this->tblSorter = new Q\Plugin\Control\NestedSortable($this);
            $this->tblSorter->ForcePlaceholderSize = true;
            //$this->tblSorter->DisableParentChange = true;
            $this->tblSorter->UseWrapper = false;
            //$this->tblSorter->ExcludeRoot = true;
            //$this->tblSorter->ProtectRoot = true;
            $this->tblSorter->Handle = '.reorder';
            $this->tblSorter->Helper = 'clone';
            $this->tblSorter->ListType = 'ul';
            $this->tblSorter->Items = 'li';
            $this->tblSorter->Opacity = .6;
            $this->tblSorter->Placeholder = 'placeholder';
            $this->tblSorter->Revert = 250;
            $this->tblSorter->TabSize = 25;
            $this->tblSorter->Tolerance = 'pointer';
            $this->tblSorter->ToleranceElement = '> div';
            $this->tblSorter->MaxLevels = 3;
            $this->tblSorter->IsTree = false;
            $this->tblSorter->ExpandOnHover = 700;
            $this->tblSorter->StartCollapsed = false;

            $this->tblSorter->TagName = $this->tblSorter->ListType; //Please make sure TagName and ListType tags are the same!
            $this->tblSorter->WrapperClass = 'sortable ui-sortable'; // ui-sortable
            $this->tblSorter->setDataBinder('Menu_Bind');
            $this->tblSorter->createNodeParams([$this, 'Menu_Draw']);
            $this->tblSorter->createRenderButtons([$this, 'Buttons_Draw']);
            $this->tblSorter->SectionClass = 'menu-btn-body center-button';

            $this->tblSorter->addAction(new SortableStop(), new Ajax('Sortable_Stop'));
        }

        /**
         * Initializes and configures a set of buttons and a text box for a menu item management.
         * These include buttons for adding, saving, canceling, and handling collapse/expand functionality.
         *
         * @return void
         * @throws Caller
         */
        protected function createButtons(): void
        {
            // Menu item creation group (buttons and textbox)

            $this->btnAddMenuItem = new Bs\Button($this);
            $this->btnAddMenuItem->Text = t(' Add Menu Item');
            $this->btnAddMenuItem->Glyph = 'fa fa-plus';
            $this->btnAddMenuItem->CssClass = 'btn btn-orange js-btn-add';
            $this->btnAddMenuItem->addWrapperCssClass('center-button');
            $this->btnAddMenuItem->CausesValidation = false;
            $this->btnAddMenuItem->addAction(new Click(), new Ajax('btnAddMenuItem_Click'));

            $this->txtMenuText = new Bs\TextBox($this);
            $this->txtMenuText->Placeholder = t('Menu text');
            $this->txtMenuText->setHtmlAttribute('autocomplete', 'off');
            $this->txtMenuText->addWrapperCssClass('center-button');
            $this->txtMenuText->Display = false;

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange js-btn-save';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new Ajax('btnMenuSave_Click'));
            $this->btnSave->Display = false;

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default js-btn-cancel';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new Ajax('btnMenuCancel_Click'));
            $this->btnCancel->Display = false;

            // A group of buttons for collapsing or expanding menu items

            $this->btnCollapseAll = new Bs\Button($this);
            $this->btnCollapseAll->Text = t(' Collapse All');
            $this->btnCollapseAll->Glyph = 'fa fa-minus';
            $this->btnCollapseAll->addWrapperCssClass('center-button');
            $this->btnCollapseAll->CssClass = 'btn btn-default js-btn-collapse-all';

            $this->btnExpandAll = new Bs\Button($this);
            $this->btnExpandAll->Text = t(' Expand All');
            $this->btnExpandAll->Glyph = 'fa fa-plus';
            $this->btnExpandAll->addWrapperCssClass('center-button');
            $this->btnExpandAll->CssClass = 'btn btn-default js-btn-expand-all';
        }

        /**
         * Generates and renders buttons for a given menu object, including status, edit, and delete buttons,
         * with associated properties and actions.
         *
         * @param \Menu $objMenu
         *
         * @return string Returns the concatenated HTML output for the status, edit, and delete buttons.
         * @throws \QCubed\Exception\Caller
         */
        public function Buttons_Draw(Menu $objMenu): string
        {
            $strStatusId = 'btnStatus' . $objMenu->Id;

            if (!$btnStatus = $this->getControl($strStatusId)) {
                $btnStatus = new Bs\Button($this->tblSorter, $strStatusId);

                $btnStatus->ActionParameter = $objMenu->Id;
                $btnStatus->CausesValidation = false;
                $btnStatus->setDataAttribute('status', 'change');
                $btnStatus->addAction(new Click(), new Ajax('btnStatus_Click'));
            }

            $strEditId = 'btnEdit' . $objMenu->Id;

            if (!$btnEdit = $this->getControl($strEditId)) {
                $btnEdit = new Bs\Button($this->tblSorter, $strEditId);
                $btnEdit->Glyph = 'fa fa-pencil';
                $btnEdit->Tip = true;
                $btnEdit->ToolTip = t('Edit');
                $btnEdit->CssClass = 'btn btn-darkblue btn-xs';
                $btnEdit->ActionParameter = $objMenu->Id;
                $btnEdit->setDataAttribute('status', 'change');
                $btnEdit->addAction(new Click(), new Ajax('btnEdit_Click'));
            }

            $strDeleteId = 'btnDelete' . $objMenu->Id;

            if (!$btnDelete = $this->getControl($strDeleteId)) {
                $btnDelete = new Bs\Button($this->tblSorter, $strDeleteId);
                $btnDelete->Glyph = 'fa fa-trash';
                $btnDelete->Tip = true;
                $btnDelete->ToolTip = t('Delete');
                $btnDelete->CssClass = 'btn btn-danger btn-xs';
                $btnDelete->ActionParameter = $objMenu->Id;
                $btnDelete->setDataAttribute('status', 'change');
                $btnDelete->addAction(new Click(), new Ajax('btnDelete_Click'));
            }

            if ($objMenu->MenuContent->IsEnabled == 1) {
                $btnStatus->Text = t('Disable');
                $btnStatus->CssClass = 'btn btn-white btn-xs';
            } else {
                $btnStatus->Text = t('Enable');
                $btnStatus->CssClass = 'btn btn-success btn-xs';
            }

            if ($objMenu->MenuContent->ContentType == 1 && $objMenu->MenuContent->IsEnabled == 1) {
                $btnStatus->Display = false;
                $btnDelete->Display = false;
            }

            return $btnStatus->render(false) . $btnEdit->render(false) . $btnDelete->render(false);
        }

        /**
         * Creates and initializes multiple Toastr notification dialogs with predefined messages, alert types, and
         * configurations.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_RIGHT;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> To add a new item of menu to the database is successful.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_RIGHT;
            $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the menu title is at least mandatory!');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_RIGHT;
            $this->dlgToastr3->Message = t('<strong>Sorry</strong>, the title of this menu item already exists in the database, please choose another title!');
            $this->dlgToastr3->ProgressBar = true;
        }

        /**
         * Creates and initializes multiple modal dialogs with various configurations such as title, text, header
         * styles, buttons, and actions. Each modal is customized for a specific purpose like confirmation, warnings,
         * or information tips.
         *
         * @return void
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Are you sure you want to disable this main menu item along with its sub-menu items?</p>');
            $this->dlgModal1->Title = t('Question');
            $this->dlgModal1->HeaderClasses = 'btn-darkblue';
            $this->dlgModal1->addButton(t("I accept"), 'ok', false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addCloseButton(t("I'll cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new Ajax('HideAllItem_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Ajax('DataClearing_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Are you sure you want to enable this main menu item along with its sub-menu items?</p>');
            $this->dlgModal2->Title = t("Question");
            $this->dlgModal2->HeaderClasses = 'btn-success';
            $this->dlgModal2->addButton(t("I accept"), 'ok', false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal2->addCloseButton(t("I'll cancel"));
            $this->dlgModal2->addAction(new DialogButton(), new Ajax('ShowAllItem_Click'));
            $this->dlgModal2->addAction(new Bs\Event\ModalHidden(), new Ajax('DataClearing_Click'));

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">You cannot disable the last item of this main menu’s sub-menu; you must disable the main menu item instead.</p>');
            $this->dlgModal3->Title = t("Tip");
            $this->dlgModal3->HeaderClasses = 'btn-darkblue';
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Sub-menu items cannot be made public under a hidden main menu!</p>
                                        <p style="line-height: 25px; margin-bottom: -3px;">You must enable this main menu item or move the sub-menu item elsewhere in the menu tree.</p>');
            $this->dlgModal4->Title = t("Tip");
            $this->dlgModal4->HeaderClasses = 'btn-darkblue';
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this menu item?</p>
                                        <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
            $this->dlgModal5->Title = t('Warning');
            $this->dlgModal5->HeaderClasses = 'btn-danger';
            $this->dlgModal5->addButton(t("I accept"), t('This menu item has been permanently deleted.'), false, false, null, ['class' => 'btn btn-orange']);
            $this->dlgModal5->addCloseButton(t("I'll cancel"));
            $this->dlgModal5->addAction(new DialogButton(), new Ajax('deletedItem_Click'));

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">To delete this menu item, you must move it out of the main menu or sub-menu.</p>');
            $this->dlgModal6->Title = t("Tip");
            $this->dlgModal6->HeaderClasses = 'btn-darkblue';
            $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal7 = new Bs\Modal($this);
            $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">To activate this menu item, you first need to enter edit mode and set the content type.</p>');
            $this->dlgModal7->Title = t("Tip");
            $this->dlgModal7->HeaderClasses = 'btn-darkblue';
            $this->dlgModal7->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal8 = new Bs\Modal($this);
            $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">To activate this main menu item, you need to go to the edit view of this main menu item and/or each sub-menu item and specify the content type.</p>');
            $this->dlgModal8->Title = t("Tip");
            $this->dlgModal8->HeaderClasses = 'btn-darkblue';
            $this->dlgModal8->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal9 = new Bs\Modal($this);
            $this->dlgModal9->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">To activate this main menu item, you must first activate the parent main menu item.</p>');
            $this->dlgModal9->Title = t("Tip");
            $this->dlgModal9->HeaderClasses = 'btn-darkblue';
            $this->dlgModal9->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal10 = new Bs\Modal($this);
            $this->dlgModal10->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">To disable this menu item, you must first disable the parent main menu item.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">Or move the sub-menu item to another location in the menu tree.</p>');
            $this->dlgModal10->Title = t("Tip");
            $this->dlgModal10->HeaderClasses = 'btn-darkblue';
            $this->dlgModal10->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal11 = new Bs\Modal($this);
            $this->dlgModal11->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">To enable this menu item, you need to go to edit view and enter the redirection address.</p>');
            $this->dlgModal11->Title = t("Tip");
            $this->dlgModal11->HeaderClasses = 'btn-darkblue';
            $this->dlgModal11->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal12 = new Bs\Modal($this);
            $this->dlgModal12->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This menu item cannot be deleted!</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">Please remove the redirects from other menu tree items that point to this page!</p>');
            $this->dlgModal12->Title = t("Tip");
            $this->dlgModal12->HeaderClasses = 'btn-darkblue';
            $this->dlgModal12->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal13 = new Bs\Modal($this);
            $this->dlgModal13->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news group cannot be deleted because it contains news.</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please move these news items to another news group or delete the news items in this group one by one.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide this news group along with its news!</p>');
            $this->dlgModal13->Title = t("Tip");
            $this->dlgModal13->HeaderClasses = 'btn-darkblue';
            $this->dlgModal13->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal14 = new Bs\Modal($this);
            $this->dlgModal14->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This menu item cannot be hidden!</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">Please remove the redirects from other menu tree items that point to this page!</p>');
            $this->dlgModal14->Title = t("Tip");
            $this->dlgModal14->HeaderClasses = 'btn-darkblue';
            $this->dlgModal14->addButton(t("I understand"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal15 = new Bs\Modal($this);
            $this->dlgModal15->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This gallery group cannot be deleted because it contains albums.</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please move these albums to another gallery group or delete the albums in this group one by one.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide this gallery group along with its albums!</p>');
            $this->dlgModal15->Title = t("Tip");
            $this->dlgModal15->HeaderClasses = 'btn-darkblue';
            $this->dlgModal15->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal16 = new Bs\Modal($this);
            $this->dlgModal16->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event calendar group cannot be deleted because it contains events.</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please move these events to another event calendar group or delete the events in this group one by one.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide this event calendar group along with its events!</p>');
            $this->dlgModal16->Title = t("Tip");
            $this->dlgModal16->HeaderClasses = 'btn-darkblue';
            $this->dlgModal16->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal17 = new Bs\Modal($this);
            $this->dlgModal17->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This sports calendar group cannot be deleted because it contains events.</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please move these events to another sports calendar group or delete the events in this group one by one.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide this sports calendar group along with its events!</p>');
            $this->dlgModal17->Title = t("Tip");
            $this->dlgModal17->HeaderClasses = 'btn-darkblue';
            $this->dlgModal17->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal18 = new Bs\Modal($this);
            $this->dlgModal18->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This board group cannot be deleted because it contains members.</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please delete all members of this group first!</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">The best suggestion is to hide this board group along with its members!</p>');
            $this->dlgModal18->Title = t("Tip");
            $this->dlgModal18->HeaderClasses = 'btn-darkblue';
            $this->dlgModal18->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal19 = new Bs\Modal($this);
            $this->dlgModal19->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This member group cannot be deleted because it contains members.</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please delete all members of this group first!</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">The best suggestion is to hide this member group along with its members!</p>');
            $this->dlgModal19->Title = t("Tip");
            $this->dlgModal19->HeaderClasses = 'btn-darkblue';
            $this->dlgModal19->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal20 = new Bs\Modal($this);
            $this->dlgModal20->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This video group cannot be deleted  because it contains videos.</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please move these videos to another video group or delete the videos in this group one by one.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide this video group along with its videos!</p>');
            $this->dlgModal20->Title = t("Tip");
            $this->dlgModal20->HeaderClasses = 'btn-darkblue';
            $this->dlgModal20->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal21 = new Bs\Modal($this);
            $this->dlgModal21->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This statistics group cannot be deleted because it contains documents.</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please delete the documents in this statistics group one by one.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide this statistics group along with its documents!</p>');
            $this->dlgModal21->Title = t("Tip");
            $this->dlgModal21->HeaderClasses = 'btn-darkblue';
            $this->dlgModal21->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal22 = new Bs\Modal($this);
            $this->dlgModal22->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This links group cannot be deleted because it contains links.</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please move these links to another links group or delete the links in this group one by one.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide this links group along with its links!</p>');
            $this->dlgModal22->Title = t("Tip");
            $this->dlgModal22->HeaderClasses = 'btn-darkblue';
            $this->dlgModal22->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal23 = new Bs\Modal($this);
            $this->dlgModal23->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This main menu item cannot be disabled along with its submenus because some items have redirects assigned to them.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">Please remove redirects from other menu tree items that point to this one or its related pages!</p>');
            $this->dlgModal23->Title = t("Tip");
            $this->dlgModal23->HeaderClasses = 'btn-darkblue';
            $this->dlgModal23->addButton(t("I understand"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal24 = new Bs\Modal($this);
            $this->dlgModal24->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">To delete this menu item, you must move the submenus under the main menu out of the main menu!</p>');
            $this->dlgModal24->Title = t("Tip");
            $this->dlgModal24->HeaderClasses = 'btn-darkblue';
            $this->dlgModal24->addButton(t("I understand"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal25 = new Bs\Modal($this);
            $this->dlgModal25->Text = t('<p style="margin-top: 15px;">CSRF token is invalid! The request was aborted.</p>');
            $this->dlgModal25->Title = t("Warning");
            $this->dlgModal25->HeaderClasses = 'btn-danger';
            $this->dlgModal25->addCloseButton(t("I understand"));

            ///////////////////////////////////////////////////////////////////////////////////////////
            // DRAG INFO

            $this->dlgModal26 = new Bs\Modal($this);
            $this->dlgModal26->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">To drag this menu item, you first need to enter edit mode and set the content type.</p>');
            $this->dlgModal26->Title = t("Tip");
            $this->dlgModal26->HeaderClasses = 'btn-darkblue';
            $this->dlgModal26->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal27 = new Bs\Modal($this);
            $this->dlgModal27->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This menu item cannot be dragged!</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">Please remove redirects from other menu items that point to this page!</p>');
            $this->dlgModal27->Title = t("Tip");
            $this->dlgModal27->HeaderClasses = 'btn-darkblue';
            $this->dlgModal27->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal28 = new Bs\Modal($this);
            $this->dlgModal28->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Sorry, since there are no other submenus under the main menu, the main menu status will be forcibly changed to disable!</p>');
            $this->dlgModal28->Title = t("Tip");
            $this->dlgModal28->HeaderClasses = 'btn-darkblue';
            $this->dlgModal28->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal29 = new Bs\Modal($this);
            $this->dlgModal29->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">One or more submenus under this main menu are locked! 
                                        If you want to make these submenus public, you must drag them out and change their status to enabled.</p>');
            $this->dlgModal29->Title = t("Tip");
            $this->dlgModal29->HeaderClasses = 'btn-darkblue';
            $this->dlgModal29->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal30 = new Bs\Modal($this);
            $this->dlgModal30->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Unfortunately, this selected main menu was not intended to be published!</p>
                                         <p style="line-height: 25px; margin-bottom: 2px;">Please keep the main menu status unchanged.</p>
                                         <p style="line-height: 25px; margin-bottom: -3px;">If you want to publish the submenus under this 
                                         disabled main menu, drag them out and change their status to enabled.</p>');
            $this->dlgModal30->Title = t("Tip");
            $this->dlgModal30->HeaderClasses = 'btn-darkblue';
            $this->dlgModal30->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Binds the data source for the menu table sorter with the list of menu items.
         * Queries the database for all menu records, orders them by the left attribute,
         * and includes associated menu content information in the results.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function Menu_Bind(): void
        {
            $this->tblSorter->DataSource = Menu::queryArray(QQ::All(),
                QQ::Clause(QQ::OrderBy(QQN::Menu()->Left), QQ::Expand(QQN::Menu()->MenuContent)
                ));
        }

        /**
         * Generates an array representation of a menu item's details based on the provided MenuContent object.
         * It extracts various attributes and properties of the menu and its related elements.
         *
         * @param \Menu $objMenu
         *
         * @return array An associative array containing the menu item's attributes, properties, and details:
         *               - 'id': Menu item's a unique identifier.
         *               - 'parent_id': Parent menu item's identifier.
         *               - 'depth': Depth level of the menu item in the hierarchy.
         *               - 'left': Left boundary for the menu item's tree structure.
         *               - 'right': Right boundary for the menu item's tree structure.
         *               - 'menu_text': The text displayed for the menu item.
         *               - 'redirect_url': URL to redirect to when the menu item is selected.
         *               - 'is_redirect': Boolean indicating if redirection is enabled.
         *               - 'external_url': External URL associated with the menu item.
         *               - 'selected_page_id': Identifier of the selected page linked to the menu item.
         *               - 'selected_page': The page object selected for the menu item.
         *               - 'selected_page_locked': Boolean indicating if the selected page is locked.
         *               - 'content_type_object': Object representing the content type of the menu item.
         *               - 'content_type': The type of content associated with the menu item.
         *               - 'status': The current enabled/disabled status of the menu item.
         */
        public function Menu_Draw(Menu $objMenu): array
        {
            $a['id'] = $objMenu->Id;
            $a['parent_id'] = $objMenu->ParentId;
            $a['depth'] = $objMenu->Depth;
            $a['left'] = $objMenu->Left;
            $a['right'] = $objMenu->Right;
            $a['menu_text'] = QString::htmlEntities($objMenu->MenuContent->MenuText);
            $a['redirect_url'] = $objMenu->MenuContent->RedirectUrl;
            $a['is_redirect'] = $objMenu->MenuContent->IsRedirect;
            $a['external_url'] = $objMenu->MenuContent->ExternalUrl;
            $a['selected_page_id'] = $objMenu->MenuContent->SelectedPageId;
            $a['selected_page'] = $objMenu->MenuContent->SelectedPage;
            $a['selected_page_locked'] = $objMenu->MenuContent->SelectedPageLocked;
            $a['content_type_object'] = $objMenu->MenuContent->ContentTypeObject;
            $a['content_type'] = $objMenu->MenuContent->ContentType;
            $a['status'] = $objMenu->MenuContent->IsEnabled;
            return $a;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the "Add Menu Item" button.
         * Prepares the UI and backend for adding a new menu item by modifying
         * form components and resetting relevant data.
         *
         * @param ActionParams $params Parameters associated with the action, typically populated during event handling.
         * @return void This method does not return a value.
         */
        protected function btnAddMenuItem_Click(ActionParams $params): void
        {
            $this->txtMenuText->Display = true;
            $this->btnSave->Display = true;
            $this->btnCancel->Display = true;
            $this->txtMenuText->Text = '';
            $this->txtMenuText->focus();
            $this->tblSorter->disable();
        }

        /**
         * Handles save an event for the menu. Verifies CSRF token, validates the menu text,
         * checks for existing title conflicts, and creates a new menu item with associated content if valid.
         * Updates the UI elements and provides user feedback.
         *
         * @param ActionParams $params Event parameters provided during the button click.
         *
         * @return void The method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnMenuSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal25->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objMenu = Menu::querySingle(QQ::all(), [QQ::maximum(QQN::menu()->Right, 'max')]);
            $objMaxRight = $objMenu->getVirtualAttribute('max');

            if ($this->txtMenuText->Text === '') {
                $this->txtMenuText->Text = '';
                $this->txtMenuText->focus();
                $this->tblSorter->disable();
                $this->dlgToastr2->notify();
                return;
            }

            if (!MenuContent::titleExists(trim($this->txtMenuText->Text))) {
                $objMenu = new Menu();
                $objMenu->setParentId(null);
                $objMenu->setDepth('0');
                $objMenu->setLeft($objMaxRight + 1);
                $objMenu->setRight($objMaxRight + 2);

                $objMenu->save(true);

                $objContent = new MenuContent();
                $objContent->setMenuId($objMenu->Id);
                $objContent->setMenuText(trim($this->txtMenuText->Text));
                $objContent->setIsEnabled(2);
                $objContent->save(true);

                $this->txtMenuText->Display = false;
                $this->btnSave->Display = false;
                $this->btnCancel->Display = false;
                $this->btnAddMenuItem->Enabled = true;

                $this->dlgToastr1->notify();
                $this->tblSorter->refresh();
            } else {
                $this->txtMenuText->Text = '';
                $this->txtMenuText->focus();
                $this->tblSorter->disable();
                $this->dlgToastr3->notify();
            }
        }

        /**
         * Handles the click event for the menu cancel button.
         * Resets the state of the menu form and enables the "Add Menu Item" button.
         *
         * @param ActionParams $params The parameters associated with the action event.
         * @return void
         */
        protected function btnMenuCancel_Click(ActionParams $params): void
        {
            $this->txtMenuText->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;
            $this->btnAddMenuItem->Enabled = true;
            $this->tblSorter->enable();
        }

        /**
         * Handles the status update of a menu item. Verifies CSRF token, processes the selected
         * menu item's structure and relationships, evaluates its status, and determines if it
         * should be enabled or disabled. Displays appropriate dialogs and updates related settings.
         *
         * @param ActionParams $params Event parameters provided during the button click,
         * including the status identifier.
         *
         * @return void The method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Exception
         */
        protected function btnStatus_Click(ActionParams $params): void
        {
            // CSRF check first
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal25->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intStatusId = intval($params->ActionParameter);

            // Load all menu items and set up the necessary links
            $objMenuContentArray = Menu::loadAll(QQ::clause(QQ::expand(QQN::Menu()->MenuContent)));
            $this->initializeSettingsAndLinks($intStatusId);

            // Find the IDs of all recursive descendants (including itself)
            $this->strSelectedValues = $this->tblSorter->getFullChildren($objMenuContentArray, $intStatusId);
            $this->strSelectedValues[] = $intStatusId;

            // Find all parents of this element and their active children
            $parentIds = [];
            foreach ($objMenuContentArray as $menu) {
                if ($menu->Id == $intStatusId) {
                    $parentIds[] = $menu->ParentId;
                }
            }

            $activeLeafParentIds = [];
            foreach ($objMenuContentArray as $menu) {
                foreach ($parentIds as $parentId) {
                    if ($menu->ParentId == $parentId &&
                        $menu->MenuContent->IsEnabled == 1 &&
                        $menu->Right == $menu->Left + 1) {
                        $activeLeafParentIds[] = $menu->ParentId;
                    }
                }
            }

            // Find all selected descendants that have ContentType set
            $richDescendantIds = [];
            foreach ($objMenuContentArray as $menu) {
                if (in_array($menu->Id, $this->strSelectedValues, true) && $menu->MenuContent->ContentType !== null) {
                    $richDescendantIds[] = $menu->Id;
                }
            }

            // The closest ancestor and its status object
            $intAncestorId = $this->tblSorter->getAncestorId($objMenuContentArray, $intStatusId);
            $objAncestorContent = MenuContent::load($intAncestorId);

            // Get the state of the current menu object
            $this->objMenuContent = MenuContent::load($intStatusId);
            $this->objMenu = Menu::load($intStatusId);

            $isLeaf = $this->objMenu->Right === $this->objMenu->Left + 1;
            $isRoot = $this->objMenu->Depth === 0;
            $isShallow = $this->objMenu->Depth < 2;
            $hasChildrenContent = $this->objMenuContent->ContentType !== null;
            $allRichSelected = count($this->strSelectedValues) == count(array_unique($richDescendantIds));
            $selectedPageLocked = $this->objMenuContent->SelectedPageLocked == 1;

            // Status 2 (disable)
            if ($this->objMenuContent->IsEnabled == 1) {
                if (!$isLeaf) {
                    if ($isRoot || $isShallow) {
                        if ($this->tblSorter->verifyPageLockStatus("MenuContent", $this->strSelectedValues) == 0) {
                            $this->dlgModal1->showDialogBox();
                        } else {
                            $this->dlgModal23->showDialogBox();
                        }
                    } else {
                        $this->dlgModal10->showDialogBox();
                    }
                } elseif (count($activeLeafParentIds) == 1) {
                    $this->dlgModal3->showDialogBox();
                } elseif ($selectedPageLocked) {
                    $this->dlgModal14->showDialogBox();
                } else {
                    $this->disableMenuContentAndRelated($this->objMenuContent);
                }
                // Status 1 (allowing)
            } else {
                if (!$isLeaf) {
                    if ($hasChildrenContent && $allRichSelected) {
                        if (
                            ($this->objMenu->ParentId === null) ||
                            ($this->objMenu->ParentId !== null && $this->objMenu->Depth == 1 && $objAncestorContent->IsEnabled == 1)
                        ) {
                            // If all submenus are locked, prevent changing the main menu status and show a warning.
                            if (count($richDescendantIds) > 1 && !$this->hasAnyUnlockedDescendant($richDescendantIds, $intStatusId)) {
                                $this->dlgModal29->showDialogBox();
                                return;
                            }

                            // If the main menu setting and status are disabled, it cannot be changed
                            if ($this->objMenuContent->SettingLocked == 2 && $this->objMenuContent->IsEnabled == 2) {
                                $this->dlgModal30->showDialogBox();
                                return;
                            }

                            // If the internal page redirection status is disabled, it cannot be changed
                            if (!$this->objMenuContent->InternalUrl &&
                                $this->objMenuContent->ContentType == 7 &&
                                $this->objMenuContent->IsEnabled == 2
                            ) {
                                $this->dlgModal11->showDialogBox();
                                return;
                           } else {
                                $this->dlgModal2->showDialogBox();
                            }
                        } else {
                            $this->dlgModal9->showDialogBox();
                        }
                    } else {
                        $this->dlgModal8->showDialogBox();
                    }
                } elseif ($this->objMenuContent->ContentType === null) {
                    $this->dlgModal7->showDialogBox();
                } elseif ($this->objMenu->ParentId !== null && $isLeaf && count($activeLeafParentIds) < 1) {
                    $this->dlgModal4->showDialogBox();
                } else {
                    $this->enableMenuContentAndRelated($this->objMenuContent);
                }
            }

            $this->tblSorter->refresh();
        }

        /**
         * Checks if all given submenus (except the main menu's own ID) have at least one enabled (SettingLocked==1).
         * If all are locked (i.e. SettingLocked!=1), returns FALSE.
         *
         * @param int[] $descendantIds All descendant IDs, including the main menu's ID itself
         * @param int $parentId The main menu to be checked/disabled
         *
         * @return bool TRUE - there is at least one submenu that can be enabled; FALSE - all are locked
         * @throws Caller
         * @throws InvalidCast
         */
        private function hasAnyUnlockedDescendant(array $descendantIds, int $parentId): bool
        {
            foreach ($descendantIds as $id) {
                if ($id == $parentId) continue; // We skip the main menu id
                $objContent = MenuContent::load($id);
                if ($objContent && $objContent->getSettingLocked() == 1) {
                    return true; // At least one allowed to found
                }
            }
            return false; // All subordinates locked
        }

        /**
         * Disables the specified menu content and updates related settings based on the content type.
         * Certain content types trigger the disabling of specific related settings.
         *
         * @param object $menuContent The menu content object to be disabled. It must contain a ContentType property.
         *
         * @return void The method does not return a value.
         * @throws Caller
         */
        private function disableMenuContentAndRelated(object $menuContent): void
        {
            switch ($menuContent->ContentType) {
                case 3: // News content type
                    $this->objNewsSettings->setStatus(2);
                    $this->objNewsSettings->save();
                    break;
                case 4: // Galley content type
                    $this->objGallerySettings->setStatus(2);
                    $this->objGallerySettings->save();
                    break;
                case 5: // Events content type
                    $this->objEventsSettings->setStatus(2);
                    $this->objEventsSettings->save();
                    break;
                case 6: // Sports content type
                    $this->objSportsSettings->setStatus(2);
                    $this->objSportsSettings->save();
                    break;
                case 7: // Internal page content type
                case 8: // External page content type
                    $menuContent->setIsEnabled(2);
                    $menuContent->save();
                    break;
                case 11: // Board content type
                    $this->objBoardsSettings->setStatus(2);
                    $this->objBoardsSettings->save();
                    break;
                case 12: // Member content type
                    $this->objMembersSettings->setStatus(2);
                    $this->objMembersSettings->save();
                    break;
                case 13: // Videos content type
                    $this->objVideosSettings->setStatus(2);
                    $this->objVideosSettings->save();
                    break;
                case 14: // Statistics (Records) content type
                case 15: // Statistics (Rankings) content type
                case 16: // Statistics (Achievements) content type
                    $this->objStatisticsSettings->setStatus(2);
                    $this->objStatisticsSettings->save();
                    break;
                case 17: // Links content type
                    $this->objLinksSettings->setStatus(2);
                    $this->objLinksSettings->save();
                    break;
            }

            $menuContent->setIsEnabled(2);
            $menuContent->setSettingLocked(2);
            $menuContent->save();
        }

        /**
         * Enables the specified menu content and updates related settings based on the content type.
         * Performs content-specific actions such as validating URLs, updating status settings for related objects,
         * and enabling menu content. Ensures content is always saved unless an early return condition is met.
         *
         * @param MenuContent $menuContent The menu content object to be processed and enabled.
         *
         * @return void The method does not return a value.
         * @throws Caller
         */
        private function enableMenuContentAndRelated(MenuContent $menuContent): void
        {
            switch ($menuContent->ContentType) {
                case 3: // News content type
                    $this->objNewsSettings->setStatus(1);
                    $this->objNewsSettings->save();
                    break;
                case 4: // Galley content type
                    $this->objGallerySettings->setStatus(1);
                    $this->objGallerySettings->save();
                    break;
                case 5: // Events content type
                    $this->objEventsSettings->setStatus(1);
                    $this->objEventsSettings->save();
                    break;
                case 6: // Sports content type
                    $this->objSportsSettings->setStatus(1);
                    $this->objSportsSettings->save();
                    break;
                case 7: // Internal page content type
                case 8: // External page content type
                    if ($menuContent->getExternalUrl() == null && $menuContent->getInternalUrl() == null) {
                        $this->dlgModal11->showDialogBox();
                        return;
                    }
                    $menuContent->setIsEnabled(1);
                    $menuContent->save();
                    break;
                case 11: // Board content type
                    $this->objBoardsSettings->setStatus(1);
                    $this->objBoardsSettings->save();
                    break;
                case 12: // Member content type
                    $this->objMembersSettings->setStatus(1);
                    $this->objMembersSettings->save();
                    break;
                case 13: // Videos content type
                    $this->objVideosSettings->setStatus(1);
                    $this->objVideosSettings->save();
                    break;
                case 14: // Statistics (Records) content type
                case 15: // Statistics (Rankings) content type
                case 16: // Statistics (Achievements) content type
                    $this->objStatisticsSettings->setStatus(1);
                    $this->objStatisticsSettings->save();
                    break;
                case 17: // Links content type
                    $this->objLinksSettings->setStatus(1);
                    $this->objLinksSettings->save();
                    break;
            }

            // If there was no return above, we always save
            $menuContent->setIsEnabled(1);
            $menuContent->setSettingLocked(1);
            $menuContent->save();
        }

        /**
         * Handles the edit button click event. Verifies the CSRF token, retrieves the edit ID
         * from the action parameters, and redirects to the appropriate menu edit page based on the ID.
         *
         * @param ActionParams $params Event parameters provided during the button click. Contains the action parameter
         *     used for determining the edit ID.
         *
         * @return void The method does not return a value.
         * @throws RandomException
         * @throws Throwable
         */
        public function btnEdit_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal25->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intEditId = intval($params->ActionParameter);

            if ($intEditId == 1) {
                Application::redirect('home-menu_edit.php' . '?id=' . $intEditId);
            } else {
                Application::redirect('menu_edit.php' . '?id=' . $intEditId);
            }
        }

        /**
         * Handles the delete event for a menu item. Verifies CSRF token, initializes settings,
         * performs initial checks to ensure the delete action is allowed, and handles content-specific checks
         * or warnings based on the content type before proceeding with the deletion process.
         *
         * @param ActionParams $params Event parameters provided during the button click, including the menu ID to
         *     delete.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function btnDelete_Click(ActionParams $params): void
        {
            // Check against CSRF token
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal25->showDialogBox(); // CSRF protection dialog
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intDeleteId = intval($params->ActionParameter);
            $this->initializeSettingsAndLinks($this->intDeleteId);

            $this->objMenuContent = MenuContent::load($this->intDeleteId);
            $this->objMenu = Menu::load($this->intDeleteId);

            // Initial checks
            if ($this->objMenu->ParentId === null && $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->dlgModal24->showDialogBox();
                return;
            }

            if ($this->objMenuContent->SelectedPageLocked === 1) {
                $this->dlgModal12->showDialogBox();
                return;
            }

            if ($this->objMenu->ParentId !== null) {
                $this->dlgModal6->showDialogBox();
                return;
            }

            // Depending on the content type, check the locked state
            switch ($this->objMenuContent->ContentType) {
                case null:
                case 2: // Article content type
                    $this->showPermanentDeletionWarning();
                    break;

                case 3: // News content type
                    $this->checkAndShowLockedStatus($this->objNewsSettings->getNewsLocked(), $this->dlgModal13, $this->dlgModal5);
                    break;

                case 4: // Gallery content type
                    $this->checkAndShowLockedStatus($this->objGallerySettings->getAlbumsLocked(), $this->dlgModal15, $this->dlgModal5);
                    break;

                case 5: // Event calendar type
                    $this->checkAndShowLockedStatus($this->objEventsSettings->getEventsLocked(), $this->dlgModal16, $this->dlgModal5);
                    break;

                case 6: // Sports calendar type
                    $this->checkAndShowLockedStatus($this->objSportsSettings->getEventsLocked(), $this->dlgModal17, $this->dlgModal5);
                    break;

                case 11: // Board content type
                    $this->checkAndShowLockedStatus($this->objBoardsSettings->getBoardLocked(), $this->dlgModal18, $this->dlgModal5);
                    break;

                case 12: // Member content type
                    $this->checkAndShowLockedStatus($this->objMembersSettings->getMembersLocked(), $this->dlgModal19, $this->dlgModal5);
                    break;

                case 13: // Videos content type
                    $this->checkAndShowLockedStatus($this->objVideosSettings->getVideosLocked(), $this->dlgModal20, $this->dlgModal5);
                    break;

                case 14: // Statistics (Records) content type
                case 15: // Statistics (Rankings) content type
                case 16: // Statistics (Achievements) content type
                    $this->checkAndShowLockedStatus($this->objStatisticsSettings->getStatisticsLocked(), $this->dlgModal21, $this->dlgModal5);
                    break;

                case 17: // Links content type
                    $this->checkAndShowLockedStatus($this->objLinksSettings->getLinksLocked(), $this->dlgModal22, $this->dlgModal5);
                    break;

                default:
                    $this->showPermanentDeletionWarning(); // If there is no other suitable option, a warning will be displayed.
                    break;
            }
        }

        /**
         * Checks the locked status and displays the appropriate dialog box.
         * If the locked status is active, it shows the locked dialog box.
         * Otherwise, it shows the deletion warning dialog box.
         *
         * @param int $lockedStatus Status indicating whether the resource is locked (1 for locked, 0 for unlocked).
         * @param object $lockedDialog The dialog box object to show when the resource is locked.
         * @param object $deletionWarningDialog The dialog box object to show when the resource is unlocked with a
         *     deletion warning.
         *
         * @return void This method does not return a value.
         */
        private function checkAndShowLockedStatus(int $lockedStatus, object $lockedDialog, object $deletionWarningDialog): void
        {
            if ($lockedStatus == 1) {
                $lockedDialog->showDialogBox(); // Display locked status
            } else {
                $deletionWarningDialog->showDialogBox(); // Display the deletion warning
            }
        }

        /**
         * Displays a warning dialog to confirm permanent deletion.
         * The dialog box is intended to alert users about the irreversible nature of the deletion action.
         *
         * @return void This method does not return a value.
         */
        private function showPermanentDeletionWarning(): void
        {
            $this->dlgModal5->showDialogBox(); // Dialog: Permanent deletion
        }

        /**
         * Handles the deletion of a menu item and its associated content, settings, and metadata.
         * It determines the content type and executes relevant operations, such as deleting associated files,
         * unlinking related data, and cleaning up resources. Updates the user interface elements to reflect changes.
         *
         * @param ActionParams $params Event parameters provided during the delete action.
         *
         * @return void The method does not return a value.
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function deletedItem_Click(ActionParams $params): void
        {
            $this->initializeSettingsAndLinks($this->intDeleteId);

            $this->objMenu->delete();

            if ($this->objMenuContent->getContentType() !== null && $this->objMenuContent->getContentType() !== 8) {
                $this->objFrontendLinks->delete();
            }

            if ($this->objMenuContent->getContentType() == 2) { // Article content type
                $this->objArticle->unassociateAllUsersAsArticlesEditors();

                if ($this->objArticle->getPictureId()) {
                    $objFiles = Files::loadById($this->objArticle->getPictureId());

                    if ($objFiles->getLockedFile() !== 0) {
                        $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                        $objFiles->save();
                    }
                }

                if ($this->objArticle->getFilesIds()) {
                    $references = $this->objArticle->getFilesIds();

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
            }

            if ($this->objMenuContent->getContentType() == 3) { // News content type
                $this->objNewsSettings->delete();
                $this->objMetadata->delete();
            }

            if ($this->objMenuContent->getContentType() == 4) { // Gallery content type
                $objFolders = Folders::loadById($this->objGallerySettings->getFolderId());
                $rootPath = APP_UPLOADS_DIR;
                $tempPath = APP_UPLOADS_TEMP_DIR;
                $tempFolders = array('thumbnail', 'medium', 'large');

                if (file_exists($rootPath . $objFolders->getPath())) {
                    rmdir($rootPath . $objFolders->getPath());
                }

                foreach ($tempFolders as $tempFolder) {
                    $beDeletedPath = $tempPath . '/_files/' . $tempFolder . $objFolders->getPath();
                    if (is_dir($beDeletedPath)) {
                        rmdir($beDeletedPath);
                    }
                }

                $objFolders->delete();
                $this->objGallerySettings->delete();
                $this->objMetadata->delete();
            }

            if ($this->objMenuContent->getContentType() == 5) { // Events calendar content type
                $this->objEventsSettings->delete();
                $this->objMetadata->delete();
            }

            if ($this->objMenuContent->getContentType() == 6) { // Sports calendar content type
                $this->objSportsSettings->delete();
                $this->objMetadata->delete();
            }

            if ($this->objMenuContent->ContentType == 7) { // Internal page content type
                if ($this->objMenuContent->getSelectedPageId()) {
                    $objSelectedPage = MenuContent::load($this->objMenuContent->getSelectedPageId());

                    if (MenuContent::countBySelectedPageId($this->objMenuContent->getSelectedPageId()) === 0) {
                        $objSelectedPage->setSelectedPageLocked(0);
                        $objSelectedPage->save();
                    }
                }
            }

            if ($this->objMenuContent->getContentType() == 11) { // Board content type
                $this->objBoardsSettings->unassociateAllUsersAsBoardsEditors();
                $this->objBoardsSettings->delete();
                $this->objMetadata->delete();
            }

            if ($this->objMenuContent->getContentType() == 12) { // Member content type
                $this->objMembersSettings->unassociateAllUsersAsMembersEditors();
                $this->objMembersSettings->delete();
                $this->objMetadata->delete();
            }

            if ($this->objMenuContent->getContentType() == 13) { // Videos content type
                $this->objVideosSettings->unassociateAllUsersAsVideosEditors();
                $this->objVideosSettings->delete();
                $this->objMetadata->delete();
            }

            if ($this->objMenuContent->ContentType == 14 ||
                $this->objMenuContent->ContentType == 15 ||
                $this->objMenuContent->ContentType == 16)
            { // Statistics content types (Records. Rankings, Achievements)
                $this->objStatisticsSettings->unassociateAllUsersAsStatisticsEditors();

                $this->objStatisticsSettings->setName('');
                $this->objStatisticsSettings->setIsReserved(2);
                $this->objStatisticsSettings->setStatus(2);
                $this->objStatisticsSettings->setMenuContentId(null);
                $this->objStatisticsSettings->setTitleSlug(null);
                $this->objStatisticsSettings->setPostDate(null);
                $this->objStatisticsSettings->setPostUpdateDate(null);
                $this->objStatisticsSettings->setAssignedByUser(null);
                $this->objStatisticsSettings->setAuthor(null);
                $this->objStatisticsSettings->save();

                $this->objMetadata->delete();
            }

            if ($this->objMenuContent->getContentType() == 17) { // Links content type
                $this->objLinksSettings->unassociateAllUsersAsLinksEditors();
                $this->objLinksSettings->delete();
                $this->objMetadata->delete();
            }

            $this->tblSorter->refresh();

            $this->dlgModal5->hideDialogBox();
        }

        /**
         * Handles the data clearing event by unsetting the selected values' property.
         * This method is typically used to reset or clear stored selections.
         *
         * @return void The method does not return a value.
         */
        public function DataClearing_Click(): void
        {
            unset($this->strSelectedValues);
        }

        /**
         * Handles the "Hide All Item" action. Validates the CSRF token, iterates through the selected values,
         * disables the corresponding menu items by updating their status, and modifies the UI to reflect the changes.
         *
         * @param ActionParams $params Event parameters provided during the button click.
         *
         * @return void The method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function HideAllItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal25->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            foreach ($this->strSelectedValues as $value) {
                if ($value !== null) {
                    $objContent = MenuContent::load($value);
                    $objContent->setIsEnabled(2);
                    $objContent->save();

                    $this->updateSettingsStatus($value, 2);
                }
            }

            $this->tblSorter->refresh();

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Handles the event to display all selected items. Verifies CSRF token, updates the status of selected menu
         * items, and modifies the UI dynamically to reflect the changes.
         *
         * @param ActionParams $params Event parameters provided during the button click.
         *
         * @return void The method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function ShowAllItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal25->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            foreach ($this->strSelectedValues as $value) {
                if ($value !== null) {
                    $objContent = MenuContent::load($value);
                    // If the content is not locked (i.e. setting_locked = 2), it is enabled
                    if ($objContent->getSettingLocked() == 1) {
                        $objContent->setIsEnabled(1);
                    }

                    if ($objContent->getContentType() == 7 && $objContent->getSettingLocked() == 2) {
                        $objContent->setSettingLocked(1);
                        $objContent->setIsEnabled(1);
                    }

                    $objContent->save();

                    $this->updateSettingsStatus($value, 1);
                }
            }

            $this->tblSorter->refresh();

            $this->dlgModal2->hideDialogBox();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the "stop" event for a sortable menu interface. Validates CSRF tokens, checks content type and
         * locked status, processes the rearranged menu items, updates their properties, and logs errors if
         * encountered. Provides user feedback and ensures menu consistency after changes.
         *
         * @param ActionParams $params Event parameters triggered by the sortable stop action.
         *
         * @return void This method does not return a value.
         * @throws Exception If saving the updated menu items fails or invalid menu items are encountered.
         */
        protected function Sortable_Stop(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal25->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            // Checks if menu item dragging is prohibited (content type missing or page is locked).
            // Displays an error message and stops the process.
            $objMenuContent = MenuContent::load($this->tblSorter->Item);

            if ($objMenuContent->getContentType() === null || $objMenuContent->getSelectedPageLocked() == 1) {
                $this->tblSorter->refresh();
            }

            if ($objMenuContent->getContentType() === null) {
                $this->dlgModal26->showDialogBox();
                return;
            }

            $arr = $this->tblSorter->ItemArray;
            unset($arr[0]);

            if (empty($arr)) {
                $dlgModal = new Bs\Modal($this);
                $dlgModal->Text = t('<p><strong>Unfortunately</strong>, the order could not be edited or saved.</p>
                                     <p>Please try again or refresh your browser!</p>');
                $dlgModal->Title = t('Warning');
                $dlgModal->HeaderClasses = Bs\Bootstrap::BUTTON_DANGER;
                $dlgModal->Show = true;
                return;
            }

            $errors = [];

            foreach ($arr as $value) {
                // Loading a menu item
                $objMenu = Menu::load($value["id"]);
                if (!($objMenu instanceof Menu)) {
                    $errors[] = 'Menu item with ID "' . $value["id"] . '" could not be loaded.';
                    continue;
                }

                // Updating attributes
                $objMenu->ParentId = $value["parent_id"];
                $objMenu->Depth = $value["depth"];
                $objMenu->Left = $value["left"];
                $objMenu->Right = $value["right"];

                // Recording and error logging
                try {
                    $objMenu->save();
                } catch (Exception $e) {
                    $errors[] = 'Failed to save menu item with ID "' . $value["id"] . '". Error: ' . $e->getMessage();
                }
            }

            if (!empty($errors)) {
                $dlgModal = new Bs\Modal($this);
                $dlgModal->Text = t('<p><strong>Error</strong>: The following issues occurred:</p><ul><li>' . implode('</li><li>', $errors) . '</li></ul>');
                $dlgModal->Title = t('Error');
                $dlgModal->HeaderClasses = Bs\Bootstrap::BUTTON_DANGER;
                $dlgModal->Show = true;
                return;
            }

            $this->processDraggedMenuItem($this->tblSorter->Item);
            $this->updateDraggedMenuItemStatus($this->tblSorter->Item);
            $this->updateDraggedSettingsStatus($this->tblSorter->Item);

            $this->tblSorter->refresh();
        }

        /**
         * Determines whether a setting change is allowed based on the locked status.
         * If the setting is locked with a value of 1, changes are allowed. For any other values,
         * changes are not permitted.
         *
         * @param int|null $settingLocked The locked status of the setting. A value of 1 allows changes.
         *                                while all other values restrict changes. Can be null.
         *
         * @return bool Returns true if setting change is allowed, false otherwise.
         */
        private function isSettingChangeAllowed(?int $settingLocked): bool
        {
            return ((int)$settingLocked === 1);
        }

        /**
         * Updates the status of a dragged menu item based on the status of its parent and its full hierarchy of
         * children. Synchronizes status changes across related menu items and enforces specific rules regarding
         * parent-child relationships and status locks. Displays modals for user feedback when specific conditions are
         * met.
         *
         * @param int $draggedMenuId The ID of the menu item being dragged.
         *
         * @return void The method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         */
        private function updateDraggedMenuItemStatus(int $draggedMenuId): void
        {
            // Load the entire menu tree
            $objMenuArray = Menu::loadAll(QQ::clause(QQ::expand(QQN::Menu()->MenuContent)));

            // Find the ancestor ID of the draggable
            $intAncestorId = $this->tblSorter->getAncestorId($objMenuArray, $draggedMenuId);
            if (!$intAncestorId) return;

            // Find the parent MenuContent entry
            $objParentMenuContent = MenuContent::load($intAncestorId);
            if (!$objParentMenuContent) return;
            $parentStatus = $objParentMenuContent->getIsEnabled();

            // IDs of all members of the subbranch (including their children) (recursively)
            $fullChildrenIds = array_unique($this->tblSorter->getFullChildren($objMenuArray, $intAncestorId));
            if (empty($fullChildrenIds)) return;

            // Count how many children this parent has with exactly the same status as the parent itself
            $matchingChildren = 0;
            foreach ($fullChildrenIds as $childId) {
                $objContent = MenuContent::load($childId);
                if ($objContent && $objContent->getIsEnabled() == $parentStatus) {
                    $matchingChildren++;
                }
            }

            // The dragged element also has a MenuContent entry
            $objDraggedContent = MenuContent::load($draggedMenuId);
            if (!$objDraggedContent) return;

            // If there are no child records with a matching status, and the dragged record is locked (editing is not allowed)
            if ($matchingChildren == 0 && !$this->isSettingChangeAllowed($objDraggedContent->getSettingLocked())) {
                $objParentMenuContent->setSettingLocked(1);
                $objParentMenuContent->setIsEnabled(2);
                $objParentMenuContent->save();

                $objDraggedContent->setSettingLocked(2);
                $objDraggedContent->setIsEnabled(2);
                $objDraggedContent->save();

                $this->dlgModal28->showDialogBox();
                return;
            }

            // If all child items have the same status as the parent, only change the status of the dragged item
            if ($matchingChildren != count($fullChildrenIds)) {
                foreach ($fullChildrenIds as $id) {
                    if ($id !== null) {
                        $objContent = MenuContent::load($id);

                        if ($objContent) {
                            $intParentId = $this->tblSorter->getParentId($objMenuArray, $objDraggedContent->getId());
                            $countChildren = count($this->tblSorter->getChildren($objMenuArray, $intParentId));

                            // CASE: if more than 0 child, update all allowed subentries
                            if ($countChildren !== 0) {
                                if ($objContent->getSettingLocked() == 2 && $objContent->getIsEnabled() == 2) {
                                    return;
                                }

                                if ($this->isSettingChangeAllowed($objDraggedContent->getSettingLocked())) {
                                    $objContent->setIsEnabled($parentStatus);
                                    $objContent->save();
                                } else {
                                    return;
                                }
                            }
                        }
                    }
                }
                // Update the dragged item itself as well
            }

            $objDraggedContent->setIsEnabled($parentStatus);
            $objDraggedContent->save();
        }

        /**
         * Updates the associated Settings status based on the status of the dragged menu item.
         * Iterates through the Settings entry corresponding to each changed MenuContent and synchronizes the status.
         *
         * @param int $draggedMenuId The id of the dragged menu item
         *
         * @throws Caller
         * @throws InvalidCast
         */
        private function updateDraggedSettingsStatus(int $draggedMenuId): void
        {
            $objMenuArray = Menu::loadAll(QQ::clause(QQ::expand(QQN::Menu()->MenuContent)));
            $intAncestorId = $this->tblSorter->getAncestorId($objMenuArray, $draggedMenuId);
            if (!$intAncestorId) return;

            // Dragged and all his subjects
            $fullAffectedIds = array_merge([$draggedMenuId], $this->tblSorter->getFullChildren($objMenuArray, $intAncestorId));
            $fullAffectedIds = array_unique($fullAffectedIds);

            foreach ($fullAffectedIds as $menuContentId) {
                $objContent = MenuContent::load($menuContentId);
                if ($objContent) {
                    $this->updateSettingsStatus($menuContentId, $objContent->getIsEnabled());
                }
            }
        }


        /**
         * Processes a dragged menu item by performing various hierarchy and data validations
         * and updating redirected URLs and display elements as necessary.
         *
         * @param int $draggedItemId The ID of the menu item that has been dragged.
         *
         * @return void This method does not return a value.
         * @throws Exception If the dragged menu item or its associated content/parent objects are not found.
         */
        private function processDraggedMenuItem(int $draggedItemId): void
        {
            // We load all menu objects with the associated MenuContent data
            $objMenuArray = Menu::loadAll(QQ::expand(QQN::menu()->MenuContent));

            // Loading the dragged menu item
            $objHomePage = Menu::loadById($draggedItemId);
            if (!$objHomePage) {
                throw new Exception("The dragged item (Menu) with ID $draggedItemId was not found!");
            }

            // We load the associated MenuContent object
            $objItem = MenuContent::loadById($draggedItemId);
            if (!$objItem) {
                throw new Exception("MenuContent (ID: $draggedItemId) not found!");
            }

            // Find ParentId (if it exists) and load a parent object (if applicable)
            $currentParentId = $objHomePage->ParentId;
            $objParent = $currentParentId ? MenuContent::loadById($currentParentId) : null;

            // We check if the parent entity exists if ParentId is set
            if ($currentParentId && !$objParent) {
                throw new Exception("Parent item (MenuContent) with ID $currentParentId not found!");
            }

            // We check and set the display of a warning message (Homepage related checks)
            $this->lblHomePageAlert->Display = $this->isHomePageMisplaced($objHomePage);

            // A recursive update for all hierarchy classes and nodes
            $this->updateRedirectUrls($objParent, $objItem, $objMenuArray);
        }

        /**
         * Updates the redirect URLs for menu items and their subitems based on their hierarchy.
         * This method generates a new URL for the given menu item, updates its stored hierarchy,
         * and recursively processes child menu items to update their URLs.
         *
         * @param MenuContent|null $objParent The parent menu content item. If null, the menu item is a root-level item.
         * @param MenuContent $objItem The menu content item whose redirect URL is being updated.
         * @param array $objMenuArray An array of menu content items, used to identify and process child items.
         *
         * @return void The method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         */
        private function updateRedirectUrls(?MenuContent $objParent, MenuContent $objItem, array $objMenuArray): void
        {
            // New Redirect URL logic
            $newRedirectUrl = $objParent
                ? $objParent->getMenuTreeHierarchy() . '/' . QString::sanitizeForUrl($objItem->getMenuText())
                : '/' . QString::sanitizeForUrl($objItem->getMenuText());

            $this->initializeSettingsAndLinks($objItem->getId());

            $objItem->setMenuTreeHierarchy($newRedirectUrl);
            $objItem->save();

            if ($newRedirectUrl) {
                $this->updateContentTypeUrls($objItem, $newRedirectUrl);
            }

            // Find all subnodes or children (mimics the logic of "getFullChildren")
            foreach ($objMenuArray as $childMenu) {
                if ($childMenu->ParentId == $objItem->Id) {
                    // The child node is reloaded and its URL is updated.
                    $objChild = MenuContent::loadById($childMenu->Id);
                    if ($objChild) {
                        $this->updateRedirectUrls($objItem, $objChild, $objMenuArray);
                    }
                }
            }
        }

        /**
         * Updates the status of various settings based on the provided value and status.
         * Loads settings from multiple modules, updates their status, and saves the changes.
         *
         * @param mixed $value The identifier used to load the settings for each module.
         * @param int $intStatus The new status value to set for the settings.
         *
         * @return void The method does not return a value.
         * @throws Caller
         */
        private function updateSettingsStatus(mixed $value, int $intStatus): void
        {
            $objMenuContent = MenuContent::loadById($value);

            switch ($objMenuContent->getContentType())
            {
                case 3: // News content type
                    $objNewsSetting = NewsSettings::loadByIdFromNewsSettings($value);
                    $objNewsSetting->setStatus($intStatus);
                    $objNewsSetting->save();
                    break;
                case 4: // Gallery content type
                    $objGallerySetting = GallerySettings::loadByIdFromGallerySettings($value);
                    $objGallerySetting->setStatus($intStatus);
                    $objGallerySetting->save();
                    break;
                case 5: // Events calendar content type
                    $objEventsSettings = EventsSettings::loadByIdFromEventsSettings($value);
                    $objEventsSettings->setStatus($intStatus);
                    $objEventsSettings->save();
                    break;
                case 6: // Sports calendar content type
                    $objSportsSetting = SportsSettings::loadByIdFromSportsSettings($value);
                    $objSportsSetting->setStatus($intStatus);
                    $objSportsSetting->save();
                    break;
                case 11: // Board content type
                    $objBoardsSettings = BoardsSettings::loadByIdFromBoardSettings($value);
                    $objBoardsSettings->setStatus($intStatus);
                    $objBoardsSettings->save();
                    break;
                case 12: // Member content type
                    $objMembersSetting = MembersSettings::loadByIdFromMembersSettings($value);
                    $objMembersSetting->setStatus($intStatus);
                    $objMembersSetting->save();
                    break;
                case 13: // Videos content type
                    $objVideosSetting = VideosSettings::loadByIdFromVideosSettings($value);
                    $objVideosSetting->setStatus($intStatus);
                    $objVideosSetting->save();
                    break;
                case 14: // Statistics (Records) content type
                case 15: // Statistics (Rankings) content type
                case 16: // Statistics (Achievements) content type
                    $objStatisticsSetting = StatisticsSettings::loadByIdFromStatisticsSettings($value);
                    $objStatisticsSetting->setStatus($intStatus);
                    $objStatisticsSetting->save();
                    break;
                case 17: // Links content type
                    $objLinksSetting = LinksSettings::loadByIdFromLinksSettings($value);
                    $objLinksSetting->setStatus($intStatus);
                    $objLinksSetting->save();
                    break;
            }
        }

        /**
         * Initializes settings and links by loading various objects and configurations
         * related to the provided item ID. Populates class properties with the respective
         * data for menus, metadata, content, and settings across multiple modules.
         *
         * @param mixed $objItem The identifier for the item to load settings and links for.
         *
         * @return void The method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         */
        private function initializeSettingsAndLinks(mixed $objItem): void
        {
            $this->objMenu = Menu::loadById($objItem);
            $this->objMenuContent = MenuContent::loadById($objItem);

            $this->objMetadata = Metadata::loadByIdFromMetadata($objItem);
            $this->objArticle = Article::loadByIdFromContentId($objItem);
            $this->objNewsSettings = NewsSettings::loadByIdFromNewsSettings($objItem);
            $this->objGallerySettings = GallerySettings::loadByIdFromGallerySettings($objItem);
            $this->objGalleryList = GalleryList::loadByIdFromGalleryList($objItem);
            $this->objEventsSettings = EventsSettings::loadByIdFromEventsSettings($objItem);
            $this->objEventsCalendar = EventsCalendar::loadByIdFromContentId($objItem);
            $this->objSportsSettings = SportsSettings::loadByIdFromSportsSettings($objItem);
            $this->objSportsCalendar = SportsCalendar::loadByIdFromContentId($objItem);
            $this->objBoardsSettings = BoardsSettings::loadByIdFromBoardSettings($objItem);
            $this->objMembersSettings = MembersSettings::loadByIdFromMembersSettings($objItem);
            $this->objVideosSettings = VideosSettings::loadByIdFromVideosSettings($objItem);
            $this->objStatisticsSettings = StatisticsSettings::loadByIdFromStatisticsSettings($objItem);
            $this->objLinksSettings = LinksSettings::loadByIdFromLinksSettings($objItem);
            $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($objItem);
        }

        /**
         * Updates the URL properties for a given menu content item based on its content type.
         * The method modifies the redirect URLs, hierarchy settings, and other related properties,
         * handling different content types and their specific requirements.
         *
         * @param MenuContent $objItem The menu content object whose URLs need to be updated.
         * @param string $newRedirectUrl The new base redirect URL to apply to the menu content item.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         */
        private function updateContentTypeUrls(MenuContent $objItem, string $newRedirectUrl): void
        {
            $updatedUrl = $this->objMenuContent->getTitle() ? $newRedirectUrl . '/' . QString::sanitizeForUrl($this->objMenuContent->getTitle()) : $newRedirectUrl;

            if ($objItem->getContentType() !== 1) {
                $this->objMenuContent->setRedirectUrl($updatedUrl);
                $this->objMenuContent->save();
            }

            if ($objItem->getContentType() === 8) { // External page content type
                $this->objMenuContent->setRedirectUrl(null);
                $this->objMenuContent->save();
            }

            if ($objItem->getSelectedPageLocked() === 1) {
                if (MenuContent::countBySelectedPageId($objItem->getId()) === 1) {
                    $objRedirectPage = MenuContent::loadByIdFromSelectedPage($objItem->getId());
                    $objRedirectPage->setInternalUrl($this->objMenuContent->getRedirectUrl());
                    $objRedirectPage->setRedirectUrl($this->objMenuContent->getRedirectUrl());
                    $objRedirectPage->save();

                    $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objRedirectPage->getId());
                    $objFrontendLink->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
                    $objFrontendLink->save();
                } else {
                    $objRedirectPageArray = MenuContent::loadArrayBySelectedPageId($objItem->getId());
                    foreach ($objRedirectPageArray as $objRedirectPage) {
                        $objRedirectPage->setInternalUrl($this->objMenuContent->getRedirectUrl());
                        $objRedirectPage->setRedirectUrl($this->objMenuContent->getRedirectUrl());
                        $objRedirectPage->save();

                        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objRedirectPage->getId());
                        $objFrontendLink->setFrontendTitleSlug($this->objMenuContent->getRedirectUrl());
                        $objFrontendLink->save();
                    }
                }
            }

            switch ($objItem->getContentType()) {
                case 1: // Homepage type
                    $this->objMenuContent->setMenuTreeHierarchy('');
                    $this->objMenuContent->setRedirectUrl('');
                    $this->objMenuContent->save();

                    $this->objFrontendLinks->setFrontendTitleSlug('');
                    $this->objFrontendLinks->save();
                    break;
                case 2: // Article content type
                    $this->objArticle->setTitleSlug($updatedUrl);
                    $this->objArticle->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();
                    break;
                case 3: // News content type
                    $this->objNewsSettings->setTitleSlug($newRedirectUrl);
                    $this->objNewsSettings->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();

                    if ($this->objNewsSettings->getNewsLocked() == 1) {
                        $objNewsArray = News::loadArrayByMenuContentId($this->objNewsSettings->getMenuContentId());

                        foreach ($objNewsArray as $objNews) {
                            $sanitizedTitle = '/' . QString::sanitizeForUrl(trim($objNews->getTitle()));

                            if ($objNews->getMenuContentId() == $this->objNewsSettings->getMenuContentId()) {
                                $objNews->setTitleSlug($newRedirectUrl . $sanitizedTitle);
                                $objNews->save();

                                $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objNews->getId());
                                if ($objFrontendLink) {
                                    $objFrontendLink->setFrontendTitleSlug($newRedirectUrl . $sanitizedTitle);
                                    $objFrontendLink->save();
                                }
                            }
                        }
                    }
                    break;
                case 4: // Gallery content type
                    $this->objGallerySettings->setTitleSlug($newRedirectUrl);
                    $this->objGallerySettings->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();

                    if ($this->objGallerySettings->getAlbumsLocked() == 1) {
                        $objGalleryListArray = GalleryList::loadArrayByMenuContentGroupId($this->objGallerySettings->getGalleryGroupId());

                        foreach ($objGalleryListArray as $objGalleryList) {
                            $sanitizedTitle = '/' . QString::sanitizeForUrl(trim($objGalleryList->getTitle()));

                            $objGalleryList->setMenuContentGroupId($this->objGallerySettings->getGalleryGroupId());
                            $objGalleryList->setTitleSlug($newRedirectUrl . $sanitizedTitle);
                            $objGalleryList->save();

                            $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objGalleryList->getId());
                            if ($objFrontendLink) {
                                $objFrontendLink->setFrontendTitleSlug($newRedirectUrl . $sanitizedTitle);
                                $objFrontendLink->save();
                            }
                        }
                    }
                    break;
                case 5: // Events calendar content type
                    $this->objEventsSettings->setTitleSlug($newRedirectUrl);
                    $this->objEventsSettings->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();

                    if ($this->objEventsSettings->getEventsLocked() == 1) {
                        $objEventsCalendarArray = EventsCalendar::loadArrayByMenuContentGroupId($this->objEventsSettings->getMenuContentId());

                        foreach ($objEventsCalendarArray as $objEventsCalendar) {
                            $sanitizedTitle = '/' . QString::sanitizeForUrl(trim($objEventsCalendar->getTitle()));

                            if ($objEventsCalendar->getMenuContentGroupId() == $this->objEventsSettings->getMenuContentId()) {
                                $objEventsCalendar->setTitleSlug($newRedirectUrl . $sanitizedTitle);
                                $objEventsCalendar->save();

                                $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objEventsCalendar->getId());
                                if ($objFrontendLink) {
                                    $objFrontendLink->setFrontendTitleSlug($sanitizedTitle);
                                    $objFrontendLink->save();
                                }
                            }
                        }
                    }
                    break;
                case 6: // Sports calendar content type
                    $this->objSportsSettings->setTitleSlug($newRedirectUrl);
                    $this->objSportsSettings->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();

                    if ($this->objSportsSettings->getEventsLocked() == 1) {
                        $objSportsCalendarArray = SportsCalendar::loadArrayByMenuContentGroupId($this->objSportsSettings->getMenuContentId());

                        foreach ($objSportsCalendarArray as $objSportsCalendar) {
                            $sanitizedTitle = '/' . QString::sanitizeForUrl(trim($objSportsCalendar->getTitle()));

                            if ($objSportsCalendar->getMenuContentGroupId() == $this->objSportsSettings->getMenuContentId()) {
                                $objSportsCalendar->setTitleSlug($newRedirectUrl . $sanitizedTitle);
                                $objSportsCalendar->save();

                                $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSportsCalendar->getId());
                                if ($objFrontendLink) {
                                    $objFrontendLink->setFrontendTitleSlug($sanitizedTitle);
                                    $objFrontendLink->save();
                                }
                            }
                        }
                    }
                    break;
                case 7: // Internal page content type
                    if ($this->objMenuContent->getSelectedPageId()) {
                        $this->objMenuContent->setMenuTreeHierarchy($newRedirectUrl);
                        $this->objMenuContent->setRedirectUrl($this->objMenuContent->getInternalUrl());
                        $this->objMenuContent->save();

                        $this->objFrontendLinks->setFrontendTitleSlug($this->objMenuContent->getInternalUrl());
                        $this->objFrontendLinks->save();
                    }
                    break;
                case 8: // External page content type
                    $this->objMenuContent->setMenuTreeHierarchy($newRedirectUrl);
                    $this->objMenuContent->setRedirectUrl(null);
                    $this->objMenuContent->save();
                    break;
                case 10: // Sports areas content type
                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();
                    break;
                case 11: // Board content type
                    $this->objBoardsSettings->setTitleSlug($updatedUrl);
                    $this->objBoardsSettings->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();
                    break;
                case 12: // Member content type
                    $this->objMembersSettings->setTitleSlug($updatedUrl);
                    $this->objMembersSettings->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();
                    break;
                case 13: // Videos content type
                    $this->objVideosSettings->setTitleSlug($updatedUrl);
                    $this->objVideosSettings->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();
                    break;
                case 14: // Statistics (Records) content type
                case 15: // Statistics (Rankings) content type
                case 16: // Statistics (Achievements) content type
                    $this->objStatisticsSettings->setTitleSlug($newRedirectUrl);
                    $this->objStatisticsSettings->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();
                    break;
                case 17: // Links content type
                    $this->objLinksSettings->setTitleSlug($newRedirectUrl);
                    $this->objLinksSettings->save();

                    $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                    $this->objFrontendLinks->save();
                    break;
            }
        }

        /**
         * Determines if the provided homepage object is incorrectly positioned within the system's hierarchy.
         * Validates both homepage-specific constraints (when ID is 1) and general positioning rules for non-homepage
         * elements.
         *
         * @param object $objHomePage The homepage object to validate, expected to contain properties such as Id,
         *     ParentId, Depth, Left, and Right.
         *
         * @return bool Returns true if the homepage is misplaced or if the positioning violates constraints;
         *     otherwise, false.
         */
        private function isHomePageMisplaced(object $objHomePage): bool
        {
            // Let's check if the argument is really Homepage (ID = 1)
            if ($objHomePage->Id == 1) {
                // Conditions under which the Homepage position would be incorrect
                return (
                    $objHomePage->ParentId !== null || // Homepage cannot be a child of any other node.
                    $objHomePage->Depth !== 0 || // Depth must be 0 (root level)
                    $objHomePage->Left !== 2 || // The left position must be 2
                    $objHomePage->Right !== 3 // The rightmost position must be 3
                );
            }

            // If it's not Homepage, we'll check for other violations.
            return (
                $objHomePage->Left == 2 || // Cannot be placed in the Homepage position (Left = 2)
                $objHomePage->Right == 3 || // Cannot be placed in the Homepage location (Right = 3)
                $objHomePage->ParentId == 1 // Must not be a Homepage child
            );
        }
    }
    MenuForm::run('MenuForm');