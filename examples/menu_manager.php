<?php
require('qcubed.inc.php');
require('../i18n/i18n-lib.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\QString;
use QCubed\Query\QQ;
use QCubed\Js;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $dlgToastr1;
    protected $dlgToastr2;
    protected $dlgToastr3;

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
    
    protected $intEditMenuId = null;

    protected $btnAddMenuItem;
    protected $txtMenuText;
    protected $btnSave;
    protected $btnCancel;

    protected $btnCollapseAll;
    protected $btnExpandAll;

    protected $lblHomePageAlert;
    protected $tblSorter;
    protected $intDeleteId;
    protected $btnStatus;

    protected $strSelectedValues = [];

    protected $objMenu;
    protected $objMenuContent;
    protected $objMetadata;
    protected $objArticle;
    protected $objNewsSettings;
    protected $objNews;
    protected $objGallerySettings;
    protected $objGalleryList;
    protected $objEventsSettings;
    protected $objEventsCalendar;
    protected $objSportsSettings;
    protected $objSportsCalendar;
    protected $objBoardsSettings;
    protected $objMembersSettings;
    protected $objVideosSettings;
    protected $objStatisticsSettings;
    protected $objLinksSettings;
    protected $objFrontendLinks;
    protected $objFrontendGroupedLinks;
    protected $updatedUrl;

    protected function formCreate()
    {
        parent::formCreate();

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures input controls for the interface. This includes
     * creating and setting up a bootstrap alert and a nested sortable component
     * for managing sortable items in the UI.
     *
     * @return void
     */
    protected function createInputs()
    {
        // Bootstrap Alert

        $this->lblHomePageAlert = new Q\Plugin\Control\Alert($this);
        $this->lblHomePageAlert->Display = false;
        $this->lblHomePageAlert->Dismissable = true;
        $this->lblHomePageAlert->addCssClass('alert alert-danger');
        $this->lblHomePageAlert->Text = t('<strong>Note:</strong> The first item (Homepage) is always the front page and 
                                            should not be placed under any other element. It defines the system\'s default front page.');

        // NestedSortable

        $this->tblSorter = new Q\Plugin\NestedSortable($this);
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
        //$this->tblSorter->MaxLevels = 3;
        $this->tblSorter->IsTree = true;
        $this->tblSorter->ExpandOnHover = 700;
        $this->tblSorter->StartCollapsed = false;

        $this->tblSorter->TagName = $this->tblSorter->ListType; //Please make sure TagName and ListType tags are the same!
        $this->tblSorter->WrapperClass = 'sortable ui-sortable'; // ui-sortable
        $this->tblSorter->setDataBinder('Menu_Bind');
        $this->tblSorter->createNodeParams([$this, 'Menu_Draw']);
        $this->tblSorter->createRenderButtons([$this, 'Buttons_Draw']);
        $this->tblSorter->SectionClass = 'menu-btn-body center-button';

        $this->tblSorter->addAction(new Q\Jqui\Event\SortableStop(), new Q\Action\Ajax('Sortable_Stop'));
    }

    /**
     * Initializes and configures a set of buttons and a text box for menu item management.
     * These include buttons for adding, saving, canceling, and handling collapse/expand functionality.
     *
     * @return void
     */
    protected function createButtons()
    {
        // Menu item creation group (buttons and textbox)

        $this->btnAddMenuItem = new Bs\Button($this);
        $this->btnAddMenuItem->Text = t(' Add Menu Item');
        $this->btnAddMenuItem->Glyph = 'fa fa-plus';
        $this->btnAddMenuItem->CssClass = 'btn btn-orange';
        $this->btnAddMenuItem->addWrapperCssClass('center-button');
        $this->btnAddMenuItem->CausesValidation = false;
        $this->btnAddMenuItem->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddMenuItem_Click'));
        $this->btnAddMenuItem->setDataAttribute('buttons', 'true');

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
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuSave_Click'));

        if ($this->txtMenuText->Text == '') {
            $this->btnSave->setDataAttribute('buttons', 'true');
        } else {
            $this->btnSave->setDataAttribute('buttons', 'false');
        }
        $this->btnSave->Display = false;

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuCancel_Click'));
        $this->btnCancel->setDataAttribute('buttons', 'false');
        $this->btnCancel->Display = false;

        // A group of buttons for collapsing or expanding menu items

        $this->btnCollapseAll = new Bs\Button($this);
        $this->btnCollapseAll->Text = t(' Collapse All');
        $this->btnCollapseAll->Glyph = 'fa fa-minus';
        $this->btnCollapseAll->addWrapperCssClass('center-button');
        $this->btnCollapseAll->CssClass = 'btn btn-default';
        $this->btnCollapseAll->setDataAttribute('collapse', 'true');

        $this->btnExpandAll = new Bs\Button($this);
        $this->btnExpandAll->Text = t(' Expand All');
        $this->btnExpandAll->Glyph = 'fa fa-plus';
        $this->btnExpandAll->addWrapperCssClass('center-button');
        $this->btnExpandAll->CssClass = 'btn btn-default';
        $this->btnExpandAll->setDataAttribute('collapse', 'false');
    }

    /**
     * Generates and renders buttons for a given menu object, including status, edit, and delete buttons,
     * with associated properties and actions.
     *
     * @param Menu $objMenu The menu object for which the buttons are created. It includes content information
     * related to the menu, such as status and content type.
     * @return string Returns the concatenated HTML output for the status, edit, and delete buttons.
     */
    public function Buttons_Draw(Menu $objMenu)
    {
        $strStatusId = 'btnStatus' . $objMenu->Id;

        if (!$this->btnStatus = $this->getControl($strStatusId)) {
            $this->btnStatus = new Bs\Button($this->tblSorter, $strStatusId);

            $this->btnStatus->ActionParameter = $objMenu->MenuContent->Id;
            $this->btnStatus->CausesValidation = false;
            $this->btnStatus->setDataAttribute('status', 'change');
            $this->btnStatus->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnStatus_Click'));
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
            $btnEdit->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnEdit_Click'));
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
            $btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));
        }

        if ($objMenu->MenuContent->IsEnabled == 1) {
            $this->btnStatus->Text = t('Disable');
            $this->btnStatus->CssClass = 'btn btn-white btn-xs';
        } else {
            $this->btnStatus->Text = t('Enable');
            $this->btnStatus->CssClass = 'btn btn-success btn-xs';
        }

        if ($objMenu->MenuContent->ContentType == 1 && $objMenu->MenuContent->IsEnabled == 1) {
            $this->btnStatus->Display = false;
            $btnDelete->Display = false;
        }

        $this->createModals();

        return $this->btnStatus->render(false) . $btnEdit->render(false) . $btnDelete->render(false);
    }

    /**
     * Creates and initializes multiple Toastr notification dialogs with predefined messages, alert types, and configurations.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_RIGHT;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> To add a new item of menu to the database is successful.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_RIGHT;
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the menu title is at least mandatory!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_RIGHT;
        $this->dlgToastr3->Message = t('<strong>Sorry</strong>, the title of this menu item already exists in the database, please choose another title!');
        $this->dlgToastr3->ProgressBar = true;
    }

    /**
     * Creates and initializes multiple modal dialogs with various configurations such as title, text, header styles, buttons,
     * and actions. Each modal is customized for a specific purpose like confirmation, warnings, or information tips.
     *
     * @return void
     */
    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Are you sure you want to disable 
                                    this main menu item along with its sub-menu items?</p>');
        $this->dlgModal1->Title = t('Question');
        $this->dlgModal1->HeaderClasses = 'btn-darkblue';
        $this->dlgModal1->addButton(t("I accept"), $this->btnStatus->ActionParameter, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('HideAllItem_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('DataClearing_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Are you sure you want to enable 
                                    this main menu item along with its sub-menu items?</p>');
        $this->dlgModal2->Title = t("Question");
        $this->dlgModal2->HeaderClasses = 'btn-success';
        $this->dlgModal2->addButton(t("I accept"), $this->btnStatus->ActionParameter, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal2->addCloseButton(t("I'll cancel"));
        $this->dlgModal2->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('ShowAllItem_Click'));
        $this->dlgModal2->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('DataClearing_Click'));

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">You cannot disable the last item of 
                                    this main menu’s sub-menu; you must disable the main menu item instead.</p>');
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Sub-menu items cannot be made public 
                                    under a hidden main menu!</p><p style="line-height: 25px; margin-bottom: -3px;">
                                    You must enable this main menu item or move the sub-menu item elsewhere in the menu tree.</p>');
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this menu item?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal5->Title = t('Warning');
        $this->dlgModal5->HeaderClasses = 'btn-danger';
        $this->dlgModal5->addButton(t("I accept"), t('This menu item has been permanently deleted.'), false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal5->addCloseButton(t("I'll cancel"));
        $this->dlgModal5->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('deletedItem_Click'));

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">To delete this menu item, 
                                    you must move it out of the main menu or sub-menu.</p>');
        $this->dlgModal6->Title = t("Tip");
        $this->dlgModal6->HeaderClasses = 'btn-darkblue';
        $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">To activate this menu item, you need 
                                    to go to the edit view and specify the content type.</p>');
        $this->dlgModal7->Title = t("Tip");
        $this->dlgModal7->HeaderClasses = 'btn-darkblue';
        $this->dlgModal7->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal8 = new Bs\Modal($this);
        $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">To activate this main menu item, 
                                    you need to go to the edit view of this main menu item and/or each sub-menu item and 
                                    specify the content type.</p>');
        $this->dlgModal8->Title = t("Tip");
        $this->dlgModal8->HeaderClasses = 'btn-darkblue';
        $this->dlgModal8->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal9 = new Bs\Modal($this);
        $this->dlgModal9->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">To activate this main menu item, 
                                    you must first activate the parent main menu item.</p>');
        $this->dlgModal9->Title = t("Tip");
        $this->dlgModal9->HeaderClasses = 'btn-darkblue';
        $this->dlgModal9->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal10 = new Bs\Modal($this);
        $this->dlgModal10->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">To disable this menu item, you must 
                                    first disable the parent main menu item.</p><p style="line-height: 25px; margin-bottom: -3px;">
                                    Or move the sub-menu item to another location in the menu tree.</p>');
        $this->dlgModal10->Title = t("Tip");
        $this->dlgModal10->HeaderClasses = 'btn-darkblue';
        $this->dlgModal10->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal11 = new Bs\Modal($this);
        $this->dlgModal11->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">To enable this menu item, 
                                    you need to go to edit view and enter the redirection address.</p>');
        $this->dlgModal11->Title = t("Tip");
        $this->dlgModal11->HeaderClasses = 'btn-darkblue';
        $this->dlgModal11->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal12 = new Bs\Modal($this);
        $this->dlgModal12->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This menu item cannot be deleted!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove the redirects from 
                                    other menu tree items that point to this page!</p>');
        $this->dlgModal12->Title = t("Tip");
        $this->dlgModal12->HeaderClasses = 'btn-darkblue';
        $this->dlgModal12->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal13 = new Bs\Modal($this);
        $this->dlgModal13->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This news group cannot be deleted 
                                    because it contains news.</p><p style="line-height: 25px; margin-bottom: 2px;">
                                    Please move these news items to another news group or delete the news items in this group one by one.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide this news group along with its news!</p>');
        $this->dlgModal13->Title = t("Tip");
        $this->dlgModal13->HeaderClasses = 'btn-darkblue';
        $this->dlgModal13->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal14 = new Bs\Modal($this);
        $this->dlgModal14->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This menu item cannot be hidden!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove the redirects from 
                                    other menu tree items that point to this page!</p>');
        $this->dlgModal14->Title = t("Tip");
        $this->dlgModal14->HeaderClasses = 'btn-darkblue';
        $this->dlgModal14->addButton(t("I understand"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal15 = new Bs\Modal($this);
        $this->dlgModal15->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This gallery group cannot be 
                                    deleted because it contains albums.</p><p style="line-height: 25px; margin-bottom: 2px;">
                                    Please move these albums to another gallery group or delete the albums in this group one by one.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide 
                                    this gallery group along with its albums!</p>');
        $this->dlgModal15->Title = t("Tip");
        $this->dlgModal15->HeaderClasses = 'btn-darkblue';
        $this->dlgModal15->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal16 = new Bs\Modal($this);
        $this->dlgModal16->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event calendar group cannot 
                                    be deleted because it contains events.</p><p style="line-height: 25px; margin-bottom: 2px;">
                                    Please move these events to another event calendar group or delete the events in this group one by one.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide 
                                    this event calendar group along with its events!</p>');
        $this->dlgModal16->Title = t("Tip");
        $this->dlgModal16->HeaderClasses = 'btn-darkblue';
        $this->dlgModal16->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal17 = new Bs\Modal($this);
        $this->dlgModal17->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This sports calendar group cannot 
                                    be deleted because it contains events.</p><p style="line-height: 25px; margin-bottom: 2px;">
                                    Please move these events to another sports calendar group or delete the events in this group one by one.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide 
                                    this sports calendar group along with its events!</p>');
        $this->dlgModal17->Title = t("Tip");
        $this->dlgModal17->HeaderClasses = 'btn-darkblue';
        $this->dlgModal17->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

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
        $this->dlgModal19->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal20 = new Bs\Modal($this);
        $this->dlgModal20->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This video group cannot be deleted 
                                    because it contains videos.</p><p style="line-height: 25px; margin-bottom: 2px;">
                                    Please move these videos to another video group or delete the videos in this group one by one.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide 
                                    this video group along with its videos!</p>');
        $this->dlgModal20->Title = t("Tip");
        $this->dlgModal20->HeaderClasses = 'btn-darkblue';
        $this->dlgModal20->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal21 = new Bs\Modal($this);
        $this->dlgModal21->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This statistics group cannot be 
                                    deleted because it contains documents.</p><p style="line-height: 25px; margin-bottom: 2px;">
                                    Please delete the documents in this statistics group one by one.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide 
                                    this statistics group along with its documents!</p>');
        $this->dlgModal21->Title = t("Tip");
        $this->dlgModal21->HeaderClasses = 'btn-darkblue';
        $this->dlgModal21->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal22 = new Bs\Modal($this);
        $this->dlgModal22->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This links group cannot be 
                                    deleted because it contains links.</p><p style="line-height: 25px; margin-bottom: 2px;">
                                    Please move these links to another links group or delete the links in this group one by one.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">The best recommendation is to hide 
                                    this links group along with its links!</p>');
        $this->dlgModal22->Title = t("Tip");
        $this->dlgModal22->HeaderClasses = 'btn-darkblue';
        $this->dlgModal22->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal23 = new Bs\Modal($this);
        $this->dlgModal23->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This main menu item cannot be 
                                    disabled along with its submenus because some items have redirects assigned to them.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove redirects from other 
                                    menu tree items that point to this one or its related pages!</p>');
        $this->dlgModal23->Title = t("Tip");
        $this->dlgModal23->HeaderClasses = 'btn-darkblue';
        $this->dlgModal23->addButton(t("I understand"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal24 = new Bs\Modal($this);
        $this->dlgModal24->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">To delete this menu item, you must 
                                    move the submenus under the main menu out of the main menu!</p>');
        $this->dlgModal24->Title = t("Tip");
        $this->dlgModal24->HeaderClasses = 'btn-darkblue';
        $this->dlgModal24->addButton(t("I understand"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal25 = new Bs\Modal($this);
        $this->dlgModal25->Text = t('<p style="margin-top: 15px;">CSRF token is invalid! The request was aborted.</p>');
        $this->dlgModal25->Title = t("Warning");
        $this->dlgModal25->HeaderClasses = 'btn-danger';
        $this->dlgModal25->addCloseButton(t("I understand"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Binds the data source for the menu table sorter with the list of menu items.
     * Queries the database for all menu records, orders them by the left attribute,
     * and includes associated menu content information in the results.
     *
     * @return void
     */
    protected function Menu_Bind()
    {
        $this->tblSorter->DataSource = Menu::QueryArray(QQ::All(),
            QQ::Clause(QQ::OrderBy(QQN::menu()->Left), QQ::Expand(QQN::menu()->MenuContent)
            ));
    }

    /**
     * Generates an associative array containing menu details based on the provided Menu object.
     *
     * @param Menu $objMenu The menu object containing the details to be used for constructing the array.
     *
     * @return array An associative array with keys and values representing the menu properties such as id, parent_id,
     *               depth, left, right, menu text, redirect URL, external URL, selected page details, content type,
     *               and status.
     */
    public function Menu_Draw(Menu $objMenu)
    {
        $a['id'] = $objMenu->Id;
        $a['parent_id'] = $objMenu->ParentId;
        $a['depth'] = $objMenu->Depth;
        $a['left'] = $objMenu->Left;
        $a['right'] = $objMenu->Right;
        $a['menu_text'] = Q\QString::htmlEntities($objMenu->MenuContent->MenuText);
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
     * Handles actions to be performed before rendering the form.
     * Adjusts the state of UI components such as enabling or disabling
     * the table sorter and add menu item button based on whether an
     * edit menu action is in progress.
     *
     * @return void
     */
    protected function formPreRender()
    {
        if ($this->intEditMenuId) {
            $this->tblSorter->disable();

        } else {
            $this->btnAddMenuItem->Enabled = true;
            $this->tblSorter->enable();
        }
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
    protected function btnAddMenuItem_Click(ActionParams $params)
    {
        $this->txtMenuText->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;
        $this->txtMenuText->Text = null;
        $this->txtMenuText->focus();

        $this->tblSorter->disable();
        $this->intEditMenuId = -1;
    }

    /**
     * Handles the save event for the menu. Verifies CSRF token, validates the menu text,
     * checks for existing title conflicts, and creates a new menu item with associated content if valid.
     * Updates the UI elements and provides user feedback.
     *
     * @param ActionParams $params Event parameters provided during the button click.
     * @return void The method does not return a value.
     */
    protected function btnMenuSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal25->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objMenu = Menu::querySingle(QQ::all(),
            [
                QQ::maximum(QQN::menu()->Right, 'max')
            ]
        );
        $objMaxRight = $objMenu->getVirtualAttribute('max');

        if (!MenuContent::titleExists(trim($this->txtMenuText->Text)) && ($this->txtMenuText->Text !== null)) {

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

            $this->intEditMenuId = null;

            $this->txtMenuText->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;
            $this->btnAddMenuItem->Enabled = true;

            $this->dlgToastr1->notify();
            $this->tblSorter->refresh();

        } else if ($this->txtMenuText->Text === null) {
            $this->txtMenuText->Text = null;
            $this->txtMenuText->focus();
            $this->tblSorter->disable();
            $this->dlgToastr2->notify();
        } else {
            $this->txtMenuText->Text = null;
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
    protected function btnMenuCancel_Click(ActionParams $params)
    {
        $this->txtMenuText->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddMenuItem->Enabled = true;

        $this->tblSorter->enable();
        $this->intEditMenuId = null;
    }

    /**
     * Handles the click event for the status button, enabling or disabling menu items
     * and their sub-items based on specific conditions while managing related settings
     * and dependencies.
     *
     * @param ActionParams $params The parameters passed with the click event,
     *                              including the action parameter which indicates
     *                              the specific status ID being processed.
     *
     * @return void
     */
    protected function btnStatus_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal25->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $intStatusId = intval($params->ActionParameter);
        $objMenuArray = Menu::loadAll(QCubed\Query\QQ::expand(QQN::menu()->MenuContent));
        $this->initializeSettingsAndLinks($intStatusId);

        ///////////////////////////////////////////////////////

        // ParentId entries equivalent to Id value are picked by the clicked ID.
        // Purpose to enable or disable the main menu item with submenu items.
        // See the getFullChildren() function from NestedSortableBase class, what it does...

        $this->strSelectedValues = $this->tblSorter->getFullChildren($objMenuArray, $intStatusId);
        array_push($this->strSelectedValues, $intStatusId);

        ///////////////////////////////////////////////////////

        // ParentId entries equivalent to the Id value are picked by the clicked ID.
        // ParentId entries with the same value are filtered according to the IsEnabled condition for those entries.
        // The purpose is to check how many active entries are still left.

        $strInTempArray = [];
        $strValidArray = [];
        foreach ($objMenuArray as $objTempMenu) {
            if ($intStatusId == $objTempMenu->Id) {
                $strInTempArray[] = $objTempMenu->ParentId;
            }
        }
        foreach ($objMenuArray as $objTempMenu) {
            foreach ($strInTempArray as $strInTemp) {
                if ($strInTemp == $objTempMenu->ParentId) {
                    if ($objTempMenu->MenuContent->IsEnabled == 1 && $objTempMenu->Right == $objTempMenu->Left + 1) {
                        $strValidArray[] = $objTempMenu->ParentId;
                    }
                }
            }
        }

        ///////////////////////////////////////////////////////

        // The clicked ID checks the existence of the Id entry.
        // ParentId entries equivalent to the Id value are picked by the clicked ID.
        // Summarize the first and second loop entries into an array.
        // Object to compare the count() of two arrays ($strSelectedInValues and $strCalculatedArray) by the ContentType condition.

        $strCalculatedArray = [];
        foreach ($objMenuArray as $objInMenu) {
            foreach ($this->strSelectedValues as &$strValidTemp) {
                if ($strValidTemp == $objInMenu->Id) {
                    if($objInMenu->MenuContent->ContentType !== null)
                        $strCalculatedArray[] = $objInMenu->Id;
                }
            }
        }

        ///////////////////////////////////////////////////////

        // The goal is to identify the ancestor ID by clicking on the child ID of that ancestor.
        // There are many ways to use the getAncestorId() function.
        // Here it is detected through this function the IsEnabled status of the ancestor.
        // See the getAncestorId() function from NestedSortableBase class, what it does...

        $intAncestorId = $this->tblSorter->getAncestorId($objMenuArray, $intStatusId);
        $intIdentifiedStatus = MenuContent::load($intAncestorId);

        ///////////////////////////////////////////////////////

        if ($this->objMenuContent->IsEnabled == 1) {
            if ($this->objMenu->Right !== $this->objMenu->Left + 1) {
                if ($this->objMenu->Depth == 0 || $this->objMenu->Depth < 2) {
                    if ($this->tblSorter->verifyPageLockStatus("MenuContent", $this->strSelectedValues) == 0) {
                        $this->dlgModal1->showDialogBox();
                    } else {
                        $this->dlgModal23->showDialogBox();
                    }
                } elseif ($this->objMenu->Depth > 1) {
                    $this->dlgModal10->showDialogBox();
                }
            } elseif (count($strValidArray) == 1) {
                $this->dlgModal3->showDialogBox();
            } elseif ($this->objMenuContent->SelectedPageLocked == 1) {
                $this->dlgModal14->showDialogBox();
            } else {
                if ($this->objMenuContent->ContentType == 8) {
                    $this->objMenuContent->setIsEnabled(2);
                    $this->objMenuContent->save();
                    }

                if ($this->objMenuContent->ContentType == 11) {
                    $this->objBoardsSettings->setStatus(2);
                    $this->objBoardsSettings->save();
                }

                if ($this->objMenuContent->ContentType == 12) {
                    $this->objMembersSettings->setStatus(2);
                    $this->objMembersSettings->save();
                }

                if ($this->objMenuContent->ContentType == 13) {
                    $this->objVideosSettings->setStatus(2);
                    $this->objVideosSettings->save();
                }

                if ($this->objMenuContent->ContentType == 14 ||
                    $this->objMenuContent->ContentType == 15 ||
                    $this->objMenuContent->ContentType == 16)
                {
                    $this->objStatisticsSettings->setStatus(2);
                    $this->objStatisticsSettings->save();
                }

                if ($this->objMenuContent->ContentType == 17) {
                    $this->objLinksSettings->setStatus(2);
                    $this->objLinksSettings->save();
                }

                $this->objMenuContent->setIsEnabled(2);
                $this->objMenuContent->save();

                $enable_translate = t('Enable');
                Application::executeJavaScript(sprintf("jQuery('#btnStatus{$intStatusId}')
                    .removeClass('btn btn-white btn-xs')
                    .addClass('btn btn-success btn-xs')
                    .text('{$enable_translate}');
                    jQuery('#btnStatus{$intStatusId}').closest('div').removeClass('enable').addClass('disable');"));
            }
        } else {  // $objContent->IsEnabled == 2
            if ($this->objMenu->Right !== $this->objMenu->Left + 1) {
                if ($this->objMenuContent->ContentType && count($this->strSelectedValues) == count(array_unique($strCalculatedArray))) {
                    if (($this->objMenu->ParentId == null) ||
                        ($this->objMenu->ParentId !== null &&
                            $this->objMenu->Depth == 1 &&
                            $intIdentifiedStatus->IsEnabled == 1)) {
                        $this->dlgModal2->showDialogBox();
                    } else {
                        $this->dlgModal9->showDialogBox();
                    }
                } else {
                    $this->dlgModal8->showDialogBox();
                }
            }  elseif ($this->objMenuContent->ContentType == null) {
                $this->dlgModal7->showDialogBox();
            } elseif ($this->objMenu->ParentId !== null && $this->objMenu->Right == $this->objMenu->Left + 1 && count($strValidArray) < 1) {
                $this->dlgModal4->showDialogBox();
            } else {
                if ($this->objMenuContent->ContentType == 8) {
                    if ($this->objMenuContent->getExternalUrl() == null) {
                        $this->dlgModal11->showDialogBox();
                    } else {
                        $this->objMenuContent->setIsEnabled(1);
                        $this->objMenuContent->save();
                    }
                }

                if ($this->objMenuContent->ContentType == 11) {
                    $this->objBoardsSettings->setStatus(1);
                    $this->objBoardsSettings->save();
                }

                if ($this->objMenuContent->ContentType == 12) {
                    $this->objMembersSettings->setStatus(1);
                    $this->objMembersSettings->save();
                }

                if ($this->objMenuContent->ContentType == 13) {
                    $this->objVideosSettings->setStatus(1);
                    $this->objVideosSettings->save();
                }

                if ($this->objMenuContent->ContentType == 14 ||
                    $this->objMenuContent->ContentType == 15 ||
                    $this->objMenuContent->ContentType == 16)
                {
                    $this->objStatisticsSettings->setStatus(1);
                    $this->objStatisticsSettings->save();
                }

                if ($this->objMenuContent->ContentType == 17) {
                    $this->objLinksSettings->setStatus(1);
                    $this->objLinksSettings->save();
                }

                $this->objMenuContent->setIsEnabled(1);
                $this->objMenuContent->save();

                $disable_translate = t('Disable');
                Application::executeJavaScript(sprintf("jQuery('#btnStatus{$intStatusId}')
                    .removeClass('btn btn-success btn-xs')
                    .addClass('btn btn-white btn-xs')
                    .text('{$disable_translate}');
                    jQuery('#btnStatus{$intStatusId}').closest('div').removeClass('disable').addClass('enable');
                "));
            }
        }
        $this->intEditMenuId = null;
    }

    /**
     * Handles the edit button click event. Verifies the CSRF token, retrieves the edit ID
     * from the action parameters, and redirects to the appropriate menu edit page based on the ID.
     *
     * @param ActionParams $params Event parameters provided during the button click. Contains the action parameter used for determining the edit ID.
     * @return void The method does not return a value.
     */
    public function btnEdit_Click(ActionParams $params)
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
     * @param ActionParams $params Event parameters provided during the button click, including the menu ID to delete.
     * @return void This method does not return a value.
     */
    public function btnDelete_Click(ActionParams $params)
    {
        // Check against CSRF token
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal25->showDialogBox(); // CSRF protection dialog
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intDeleteId = intval($params->ActionParameter);
        $this->initializeSettingsAndLinks($this->intDeleteId);

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

            case 13: // Video content type
                $this->checkAndShowLockedStatus($this->objVideosSettings->getVideosLocked(), $this->dlgModal20, $this->dlgModal5);
                break;

            case 14: // Statistics (Records) content type
            case 15: // Statistics (Rankings) content type
            case 16: // Statistics (Achievements) content type
                $this->checkAndShowLockedStatus($this->objStatisticsSettings->getStatisticsLocked(), $this->dlgModal21, $this->dlgModal5);
                break;

            case 17: // Link content type
                $this->checkAndShowLockedStatus($this->objLinksSettings->getLinksLocked(), $this->dlgModal22, $this->dlgModal5);
                break;

            default:
                $this->showPermanentDeletionWarning(); // If there is no other suitable option, a warning will be displayed.
                break;
        }

        // Preparing to complete the deletion
        $this->intEditMenuId = null;
    }

    /**
     * Checks the locked status and displays the appropriate dialog box.
     * If the locked status is active, it shows the locked dialog box.
     * Otherwise, it shows the deletion warning dialog box.
     *
     * @param int $lockedStatus Status indicating whether the resource is locked (1 for locked, 0 for unlocked).
     * @param object $lockedDialog The dialog box object to show when the resource is locked.
     * @param object $deletionWarningDialog The dialog box object to show when the resource is unlocked with a deletion warning.
     * @return void This method does not return a value.
     */
    private function checkAndShowLockedStatus($lockedStatus, $lockedDialog, $deletionWarningDialog)
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
    private function showPermanentDeletionWarning()
    {
        $this->dlgModal5->showDialogBox(); // Dialog: Permanent deletion
    }

    /**
     * Handles the deletion of a menu item and its associated content, settings, and metadata.
     * It determines the content type and executes relevant operations, such as deleting associated files,
     * unlinking related data, and cleaning up resources. Updates the user interface elements to reflect changes.
     *
     * @param ActionParams $params Event parameters provided during the delete action.
     * @return void The method does not return a value.
     */
    public function deletedItem_Click(ActionParams $params)
    {
        $this->initializeSettingsAndLinks($this->intDeleteId);

        $this->objMenu->delete();
        $this->tblSorter->refresh();

        if ($this->objMenuContent->getContentType() !== 8) {
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

        if ($this->objMenuContent->getContentType() == 4) { // gallery content type
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

        if ($this->objMenuContent->getContentType() == 12) { // Members content type
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

        $this->dlgModal5->hideDialogBox();
    }

    /**
     * Handles the data clearing event by unsetting the selected values property.
     * This method is typically used to reset or clear stored selections.
     *
     * @return void The method does not return a value.
     */
    public function DataClearing_Click()
    {
        unset($this->strSelectedValues);
    }

    /**
     * Handles the "Hide All Item" action. Validates the CSRF token, iterates through the selected values,
     * disables the corresponding menu items by updating their status, and modifies the UI to reflect the changes.
     *
     * @param ActionParams $params Event parameters provided during the button click.
     * @return void The method does not return a value.
     */
    public function HideAllItem_Click(ActionParams $params)
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

                $enable_translate = t('Enable');
                Application::executeJavaScript(sprintf("jQuery('#btnStatus{$value}')
                    .removeClass('btn btn-white btn-xs')
                    .addClass('btn btn-success btn-xs')
                    .text('{$enable_translate}');
                    jQuery('#btnStatus{$value}').closest('div').removeClass('enabled').addClass('disable');"
                ));
            }
        }

        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the event to display all selected items. Verifies CSRF token, updates the status of selected menu items,
     * and modifies the UI dynamically to reflect the changes.
     *
     * @param ActionParams $params Event parameters provided during the button click.
     * @return void The method does not return a value.
     */
    public function ShowAllItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal25->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        foreach ($this->strSelectedValues as $value) {
            if ($value !== null) {
                $objContent = MenuContent::load($value);
                $objContent->setIsEnabled(1);
                $objContent->save();

                $disable_translate = t('Disable');
                Application::executeJavaScript(sprintf("jQuery('#btnStatus{$value}')
                    .removeClass('btn btn-success btn-xs')
                    .addClass('btn btn-white btn-xs')
                    .text('{$disable_translate}');
                    jQuery('#btnStatus{$value}').closest('div').removeClass('disable').addClass('enable');
                "));
            }
        }

        $this->dlgModal2->hideDialogBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the stop event for a sortable table, verifies the CSRF token, processes the dragged items,
     * and updates menu attributes in the database. Displays error or warning modals if the operation fails.
     *
     * @param ActionParams $params Event parameters provided during the sortable stop event.
     * @return void The method does not return a value.
     */
    protected function Sortable_Stop(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal25->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $arr = $this->tblSorter->ItemArray;

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
            if (!$objMenu || !($objMenu instanceof Menu)) {
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
    }

    /**
     * Processes a dragged menu item by performing various hierarchy and data validations
     * and updating redirected URLs and display elements as necessary.
     *
     * @param int $draggedItemId The ID of the menu item that has been dragged.
     * @return void This method does not return a value.
     * @throws Exception If the dragged menu item or its associated content/parent objects are not found.
     */
    private function processDraggedMenuItem($draggedItemId)
    {
        // We load all menu objects with the associated MenuContent data
        $objMenuArray = Menu::loadAll(QCubed\Query\QQ::expand(QQN::menu()->MenuContent));

        // Loading the dragged menu item
        $objHomePage = Menu::loadById($draggedItemId);
        if (!$objHomePage) {
            throw new Exception("The dragged item (Menu) with ID {$draggedItemId} was not found!");
        }

        // We load the associated MenuContent object
        $objItem = MenuContent::loadById($draggedItemId);
        if (!$objItem) {
            throw new Exception("MenuContent (ID: {$draggedItemId}) not found!");
        }

        // Find ParentId (if it exists) and load parent object (if applicable)
        $currentParentId = $objHomePage->ParentId;
        $objParent = $currentParentId ? MenuContent::loadById($currentParentId) : null;

        // We check if the parent entity exists if ParentId is set
        if ($currentParentId && !$objParent) {
            throw new Exception("Parent item (MenuContent) with ID {$currentParentId} not found!");
        }

        // We check and set the display of a warning message (Homepage related checks)
        $this->lblHomePageAlert->Display = $this->isHomePageMisplaced($objHomePage);

        // Recursive update for all hierarchy classes and nodes
        $this->updateRedirectUrls($objParent, $objItem, $objMenuArray);
    }

    /**
     * Updates the redirect URLs for menu items and their sub-items based on their hierarchy.
     * This method generates a new URL for the given menu item, updates its stored hierarchy,
     * and recursively processes child menu items to update their URLs.
     *
     * @param MenuContent|null $objParent The parent menu content item. If null, the menu item is a root-level item.
     * @param MenuContent $objItem The menu content item whose redirect URL is being updated.
     * @param array $objMenuArray An array of menu content items, used to identify and process child items.
     * @return void The method does not return a value.
     */
    private function updateRedirectUrls(?MenuContent $objParent, MenuContent $objItem, array $objMenuArray)
    {
        // New Redirect URL logic
        $newRedirectUrl = $objParent
            ? $objParent->getMenuTreeHierarchy() . '/' . Q\QString::sanitizeForUrl($objItem->getMenuText())
            : '/' . Q\QString::sanitizeForUrl($objItem->getMenuText());

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
     * Initializes settings and links by loading various objects and configurations
     * related to the provided item ID. Populates class properties with the respective
     * data for menus, metadata, content, and settings across multiple modules.
     *
     * @param mixed $objItem The identifier for the item to load settings and links for.
     * @return void The method does not return a value.
     */
    private function initializeSettingsAndLinks($objItem)
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
     * @return void This method does not return a value.
     */
    private function updateContentTypeUrls(MenuContent $objItem, string $newRedirectUrl)
    {
        $updatedUrl = $this->objMenuContent->getTitle() ? $newRedirectUrl . Q\QString::sanitizeForUrl($this->objMenuContent->getTitle())/* $sanitizedTitle */ : $newRedirectUrl;

        if ($objItem->getContentType() !== 1) {
            $this->objMenuContent->setRedirectUrl($updatedUrl);
            $this->objMenuContent->save();
        }

        if ($objItem->getContentType() === 8) {
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
                    $objNewsArray = News::loadArrayByNewsGroupId($this->objNewsSettings->getNewsGroupId());

                    foreach ($objNewsArray as $objNews) {
                        $sanitizedTitle = '/' . Q\QString::sanitizeForUrl(trim($objNews->getTitle()));

                        if ($objNews->getNewsGroupId() == $this->objNewsSettings->getNewsGroupId()) {
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
            case 4: // Gallery content type:
                $this->objGallerySettings->setTitleSlug($newRedirectUrl);
                $this->objGallerySettings->save();

                $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $this->objFrontendLinks->save();

                if ($this->objGallerySettings->getAlbumsLocked() == 1) {
                    $objGalleryListArray = GalleryList::loadArrayByMenuContentGroupId($this->objGallerySettings->getGalleryGroupId());

                    foreach ($objGalleryListArray as $objGalleryList) {
                        $sanitizedTitle = '/' . Q\QString::sanitizeForUrl(trim($objGalleryList->getTitle()));

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
                        $urlWithLastTwoParts = implode('/', array_slice(explode('/', trim($objEventsCalendar->getTitleSlug(), '/')), -2, 2));

                        $modifiedSlug = $newRedirectUrl . '/' . $urlWithLastTwoParts;

                        $objEventsCalendar->setMenuContentGroupId($this->objEventsSettings->getMenuContentId());
                        $objEventsCalendar->setTitleSlug($modifiedSlug);
                        $objEventsCalendar->save();

                        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objEventsCalendar->getId());
                        if ($objFrontendLink) {
                            $objFrontendLink->setFrontendTitleSlug($modifiedSlug);
                            $objFrontendLink->save();
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
                        $urlWithLastTwoParts = implode('/', array_slice(explode('/', trim($objSportsCalendar->getTitleSlug(), '/')), -2, 2));
                        $modifiedSlug = $newRedirectUrl . '/' . $urlWithLastTwoParts;

                        $objSportsCalendar->setMenuContentGroupId($this->objSportsSettings->getMenuContentId());
                        $objSportsCalendar->setTitleSlug($modifiedSlug);
                        $objSportsCalendar->save();

                        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSportsCalendar->getId());
                        if ($objFrontendLink) {
                            $objFrontendLink->setFrontendTitleSlug($modifiedSlug);
                            $objFrontendLink->save();
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
            case 10: // Sports areas
                $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $this->objFrontendLinks->save();
                break;
            case 11: // Board content type
                $this->objBoardsSettings->setTitleSlug($updatedUrl);
                $this->objBoardsSettings->save();

                $this->objFrontendLinks->setFrontendTitleSlug($updatedUrl);
                $this->objFrontendLinks->save();
                break;
            case 12: // Members content type
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
     * Validates both homepage-specific constraints (when ID is 1) and general positioning rules for non-homepage elements.
     *
     * @param object $objHomePage The homepage object to validate, expected to contain properties such as Id, ParentId, Depth, Left, and Right.
     * @return bool Returns true if the homepage is misplaced or if the positioning violates constraints; otherwise, false.
     */
    private function isHomePageMisplaced($objHomePage): bool
    {
        // Let's check if the argument is really Homepage (ID=1)
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
            $objHomePage->Left == 2 || // Cannot be placed in Homepage position (Left = 2)
            $objHomePage->Right == 3 || // Cannot be placed in Homepage location (Right = 3)
            $objHomePage->ParentId == 1 // Must not be a Homepage child
        );
    }
}
SampleForm::run('SampleForm');