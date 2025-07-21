<?php

use QCubed as Q;
use QCubed\Action\Ajax;
use QCubed\Bootstrap as Bs;
use QCubed\Event\Change;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class GenderMappingPanel extends Q\Control\Panel
{
    protected $dlgToastr1;
    protected $dlgToastr2;
    protected $dlgToastr3;
    protected $dlgToastr4;
    protected $dlgToastr5;
    protected $dlgToastr6;
    protected $dlgToastr7;
    
    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;
    public $dlgModal6;

    public $lblInfo;
    public $dtgAgeCategoryGender;
    public $btnAddNewMapping;

    public $lblAgeGroup;
    public $lstAgeGroup;
    public $lblAthleteGender;
    public $lstAthleteGender;
    public $lblGender;
    public $lstGender;
    public $lblStatus;
    public $lstStatus;

    public $btnSave;
    public $btnDelete;
    public $btnCancel;

    public $lblPostDate;
    public $calPostDate;
    public $lblPostUpdateDate;
    public $calPostUpdateDate;
    public $lblAuthor;
    public $txtAuthor;
    public $lblUsersAsEditors;
    public $txtUsersAsEditors;
    
    protected $intId;
    protected $intClick;
    protected $blnEditMode = true;
    protected $objUser;
    protected $intLoggedUserId;
    protected $errors = []; // Array for tracking errors

    protected $strTemplate = 'GenderMappingPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        /**
         * NOTE: if the user_id is stored in session (e.g. if a User is logged in), as well, for example:
         * checking against user session etc.
         *
         * Must have to get something like here $this->objUser->getUserId(logged user session);
         * or something similar...
         *
         * Options to do this are left to the developer.
         **/

        // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
        // For example, John Doe is a logged user with his session

        $this->intLoggedUserId = 2;
        $this->objUser = User::load($this->intLoggedUserId);
        
        $this->dtgAgeCategoryGender_Create();

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the GenderMappingTable component and sets up its configuration.
     *
     * @return void
     */
    protected function dtgAgeCategoryGender_Create()
    {
        $this->dtgAgeCategoryGender = new GenderMappingTable($this);
        $this->dtgAgeCategoryGender_CreateColumns();
        $this->dtgAgeCategoryGender_MakeEditable();
        $this->dtgAgeCategoryGender->RowParamsCallback = [$this, "dtgAgeCategoryGender_GetRowParams"];
        $this->dtgAgeCategoryGender->SortColumnIndex = 0;
        $this->dtgAgeCategoryGender->SortDirection = -1;
    }

    /**
     * Initializes and creates the columns for the AgeCategoryGender datagrid.
     *
     * @return void
     */
    protected function dtgAgeCategoryGender_CreateColumns()
    {
        $this->dtgAgeCategoryGender->createColumns();
    }

    /**
     * Configures the DataGrid (dtgAgeCategoryGender) to be editable by enabling cell click actions
     * and applying appropriate CSS classes for styling and functionality.
     *
     * @return void
     */
    protected function dtgAgeCategoryGender_MakeEditable()
    {
        $this->dtgAgeCategoryGender->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgAgeCategoryGender_Click'));
        $this->dtgAgeCategoryGender->addCssClass('clickable-rows');
        $this->dtgAgeCategoryGender->CssClass = 'table vauu-table js-sports-area table-hover table-responsive';
    }

    /**
     * Handles the Click event for the age category gender data grid row.
     *
     * @param ActionParams $params The parameters containing action details, including the ID of the selected row.
     * @return void
     */
    protected function dtgAgeCategoryGender_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objAgeCategoryGender = AgeCategoryGender::load($this->intId);
        $this->intClick = $this->intId;
        $this->btnSave->Display = false;

        if ($objAgeCategoryGender->getIsLocked() == 1) {
            $this->btnDelete->Display = true;
        } else {
            $this->btnDelete->Display = false;
        }

        $this->blnEditMode = true;

        $this->dtgAgeCategoryGender->addCssClass('disabled');
        $this->refreshDisplay($this->intId);

        Application::executeJavaScript("
            $('.setting-wrapper').removeClass('hidden');
            $('.form-actions-wrapper').removeClass('hidden');
            $('.js-wrapper-top').get(0).scrollIntoView({behavior: 'smooth'});
        ");

        $this->activeInputs($objAgeCategoryGender);
        $this->checkInputs();
    }

    /**
     * Retrieves row parameters for AgeCategoryGender data grid.
     *
     * @param object $objRowObject The object representing the current row in the data grid.
     * @param int $intRowIndex The index of the current row in the data grid.
     * @return array An associative array containing the row parameters, such as 'data-value'.
     */
    public function dtgAgeCategoryGender_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();

        $params['data-value'] = $strKey;
        return $params;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures input controls, labels, and selectors for managing age categories, genders, and statuses.
     *
     * This method creates and applies styles, actions, and data population logic
     * for inputs including dropdowns, labels, and other user interface components.
     *
     * @return void
     */
    protected function createInputs()
    {
        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please create the first age class!');
        $this->lblInfo->setCssStyle('margin-bottom', 0);

        $countAgeCategoryGender = AgeCategoryGender::countAll();

        if ($countAgeCategoryGender === 0) {
            $this->lblInfo->Display = true;
            $this->dtgAgeCategoryGender->Display = false;
        } else {
            $this->lblInfo->Display = false;
            $this->dtgAgeCategoryGender->Display = true;
        }

        $this->lblAgeGroup = new Q\Plugin\Control\Label($this);
        $this->lblAgeGroup->Text = t('Age group');
        $this->lblAgeGroup->addCssClass('col-md-4');
        $this->lblAgeGroup->setCssStyle('font-weight', 'normal');

        $this->lstAgeGroup = new Q\Plugin\Control\Select2($this);
        $this->lstAgeGroup->MinimumResultsForSearch = -1;
        $this->lstAgeGroup->ContainerWidth = 'resolve';
        $this->lstAgeGroup->Theme = 'web-vauu';
        $this->lstAgeGroup->Width = '100%';
        $this->lstAgeGroup->setCssStyle('float', 'left');
        $this->lstAgeGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstAgeGroup->addItem(t('- Select age group -'), null, true);
        $this->lstAgeGroup->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstAgeGroup_Change'));

        $objAgeGroups = AgeCategories::loadAll();

        foreach ($objAgeGroups as $objAgeGroup) {
            $this->lstAgeGroup->addItem($objAgeGroup->ClassName, $objAgeGroup->Id);
        }

        $this->lblAthleteGender = new Q\Plugin\Control\Label($this);
        $this->lblAthleteGender->Text = t('Athlete gender');
        $this->lblAthleteGender->addCssClass('col-md-4');
        $this->lblAthleteGender->setCssStyle('font-weight', 'normal');

        $this->lstAthleteGender = new Q\Plugin\Control\Select2($this);
        $this->lstAthleteGender->MinimumResultsForSearch = -1;
        $this->lstAthleteGender->ContainerWidth = 'resolve';
        $this->lstAthleteGender->Theme = 'web-vauu';
        $this->lstAthleteGender->Width = '100%';
        $this->lstAthleteGender->setCssStyle('float', 'left');
        $this->lstAthleteGender->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstAthleteGender->addItem(t('- Select age group -'), null, true);
        $this->lstAthleteGender->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstAthleteGender_Change'));

        $objAthleteGenders = AthleteGender::loadAll();

        foreach ($objAthleteGenders as $objAthleteGender) {
            $this->lstAthleteGender->addItem($objAthleteGender->Gender, $objAthleteGender->Id);
        }

        $this->lblGender = new Q\Plugin\Control\Label($this);
        $this->lblGender->Text = t('Gender group');
        $this->lblGender->addCssClass('col-md-4');
        $this->lblGender->setCssStyle('font-weight', 'normal');

        $this->lstGender = new Q\Plugin\Control\Select2($this);
        $this->lstGender->MinimumResultsForSearch = -1;
        $this->lstGender->ContainerWidth = 'resolve';
        $this->lstGender->Theme = 'web-vauu';
        $this->lstGender->Width = '100%';
        $this->lstGender->setCssStyle('float', 'left');
        $this->lstGender->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstGender->addItem(t('- Select gender group -'), null, true);
        $this->lstGender->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstGender_Change'));

        $objGenders = Genders::loadAll();

        foreach ($objGenders as $objGender) {
            $this->lstGender->addItem($objGender->Name, $objGender->Id);
        }

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->addCssClass('col-md-4');
        $this->lblStatus->setCssStyle('font-weight', 'normal');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
        $this->lstStatus->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstStatus_Change'));

        ///////////////////////////////////////////////////////////////////////////////////////////

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');
        $this->lblPostDate->Display = false;

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->setCssStyle('font-weight', 'normal');
        $this->calPostDate->Display = false;

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');
        $this->lblPostUpdateDate->Display = false;

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');
        $this->calPostUpdateDate->Display = false;

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->setCssStyle('font-weight', 'bold');
        $this->lblAuthor->Display = false;

        $this->txtAuthor  = new Bs\Label($this);
        $this->txtAuthor->setCssStyle('font-weight', 'normal');
        $this->txtAuthor->Display = false;

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');
        $this->lblUsersAsEditors->Display = false;

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');
        $this->txtUsersAsEditors->Display = false;
    }

    /**
     * Initializes and configures buttons for the interface, including actions, styles, and validation settings.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnAddNewMapping = new Bs\Button($this);
        $this->btnAddNewMapping->Text = t('Add new mapping');
        $this->btnAddNewMapping->CssClass = 'btn btn-orange';
        $this->btnAddNewMapping->CausesValidation = false;
        $this->btnAddNewMapping->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddNewMapping_Click'));

        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->setCssStyle('margin-right', '10px');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));

        $this->btnDelete = new Bs\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->setCssStyle('margin-right', '10px');
        $this->btnDelete->CausesValidation = true;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnDelete_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
    }

    /**
     * Creates and initializes multiple Toastr notification instances with predefined configurations
     * for displaying success or error messages in various scenarios.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('The completed mapping data was saved or modified!');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('Failed to add the completed mapping');
        $this->dlgToastr2->ProgressBar = true;
        
        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('This field is required!');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('These fields must be filled!');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('<strong>Well done!</strong> This completed mapping with data is now active!');
        $this->dlgToastr5->ProgressBar = true;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('<strong>Well done!</strong> This completed mapping with data is now inactive!');
        $this->dlgToastr6->ProgressBar = true;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('<strong>Well done!</strong> The new completed mapping was successfully added to the database.');
        $this->dlgToastr7->ProgressBar = true;
    }

    /**
     * Initializes and configures multiple modal dialogs used within the system.
     * Each modal has specific text, titles, header styling, and associated actions corresponding to predefined use cases.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the completed mapping?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hideItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This completed mapping cannot be 
                                    deleted because it is locked in the records table or leaderboard!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                    and both tables will be updated automatically.</p>');

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This completed mapping status cannot 
                                    be deactivated because it is locked in records or leaderboards.</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                    and both tables will be updated automatically.</p>');

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">One or the other input of this bundled 
                                    mapping must not be left blank, as it is already locked in the records or leaderboards.</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                    and both tables will be updated automatically.</p>');

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Tip");
        $this->dlgModal5->HeaderClasses = 'btn-darkblue';
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">When completing a new mapping, 
                                    please make sure all fields are filled!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">Activating the status is optional!</p>');

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal6->Title = t("Warning");
        $this->dlgModal6->HeaderClasses = 'btn-danger';
        $this->dlgModal6->addCloseButton(t("I understand"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the "Add New Mapping" button. This method prepares the UI for adding a new mapping by
     * displaying relevant sections, updating the CSS styles, and resetting the inputs.
     *
     * @param ActionParams $params Event parameters passed from the triggering action.
     * @return void This method does not return a value.
     */
    public function btnAddNewMapping_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').removeClass('hidden');
            $('.form-actions-wrapper').removeClass('hidden');
        ");

        $this->dtgAgeCategoryGender->addCssClass('disabled');
        $this->btnSave->Display = true;
        $this->btnDelete->Display = false;
        $this->blnEditMode = false;

        $this->resetInputs();
    }

    /**
     * Handles the change event for the age group selector.
     *
     * This method updates the age category associated with the given object,
     * validates inputs, and notifies the user about the status of the operation.
     * It is primarily used in edit mode to modify the age category.
     *
     * @param ActionParams $params Parameters associated with the change event.
     * @return void
     */
    protected function lstAgeGroup_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objAgeCategoryGender = AgeCategoryGender::load($this->intClick);
        }

        if ($this->blnEditMode === true) {
            if ($objAgeCategoryGender->getIsLocked() == 2) {
                if ($this->lstAgeGroup->SelectedValue == null) {
                    $this->activeInputs($objAgeCategoryGender);
                    $this->lstStatus->SelectedValue = 1;
                    $this->dlgModal4->showDialogBox();
                    return;
                } else {
                    $objAgeCategoryGender->setAgeCategoryId($this->lstAgeGroup->SelectedValue);
                    $this->dlgToastr1->notify(); // Everything OK
                }
            } else {
                $this->checkInputs();

                if ($this->lstAgeGroup->SelectedValue) {
                    $objAgeCategoryGender->setAgeCategoryId($this->lstAgeGroup->SelectedValue);
                    $this->dlgToastr1->notify(); // Everything OK
                } else {
                    $objAgeCategoryGender->setAgeCategoryId(null);
                    $objAgeCategoryGender->setStatus(2);
                    $this->lstStatus->SelectedValue = 2;
                }
            }

            $objAgeCategoryGender->save();

            $this->updateAndValidateAgeGroups($objAgeCategoryGender);
        }
    }

    /**
     * Handles the change event for the athlete gender selection and updates the corresponding
     * AgeCategoryGender object based on the current selection and conditions.
     *
     * @param ActionParams $params The parameters received from the user interaction that triggered this method.
     * @return void
     */
    protected function lstAthleteGender_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objAgeCategoryGender = AgeCategoryGender::load($this->intClick);
        }

        if ($this->blnEditMode === true) {
            if ($objAgeCategoryGender->getIsLocked() == 2) {
                if ($this->lstAthleteGender->SelectedValue == null) {
                    $this->activeInputs($objAgeCategoryGender);
                    $this->lstStatus->SelectedValue = 1;
                    $this->dlgModal4->showDialogBox();
                    return;
                } else {
                    $objAgeCategoryGender->setAthleteGenderId($this->lstAthleteGender->SelectedValue);
                    $this->dlgToastr1->notify(); // Everything OK
                }
            } else {
                $this->checkInputs();

                if ($this->lstAthleteGender->SelectedValue) {
                    $objAgeCategoryGender->setAthleteGenderId($this->lstAthleteGender->SelectedValue);
                    $this->dlgToastr1->notify(); // Everything OK
                } else {
                    $objAgeCategoryGender->setAthleteGenderId(null);
                    $objAgeCategoryGender->setStatus(2);
                    $this->lstStatus->SelectedValue = 2;
                }
            }

            $objAgeCategoryGender->save();

            $this->updateAndValidateAgeGroups($objAgeCategoryGender);
        }
    }

    /**
     * Handles the change event for the gender dropdown list.
     *
     * This method updates the gender information for a specific age category and applies necessary validations.
     * It also triggers notifications or dialogs based on the state of the data and user inputs.
     *
     * @param ActionParams $params The parameters passed in the action event, containing details about the triggered action.
     * @return void
     */
    protected function lstGender_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objAgeCategoryGender = AgeCategoryGender::load($this->intClick);
        }

        if ($this->blnEditMode === true) {
            if ($objAgeCategoryGender->getIsLocked() == 2) {
                if ($this->lstGender->SelectedValue == null) {
                    $this->activeInputs($objAgeCategoryGender);
                    $this->lstStatus->SelectedValue = 1;
                    $this->dlgModal4->showDialogBox();
                    return;
                } else {
                    $objAgeCategoryGender->setGenderId($this->lstGender->SelectedValue);
                    $this->dlgToastr1->notify(); // Everything OK
                }
            } else {
                $this->checkInputs();

                if ($this->lstGender->SelectedValue) {
                    $objAgeCategoryGender->setGenderId($this->lstGender->SelectedValue);
                    $this->dlgToastr1->notify(); // Everything OK
                } else {
                    $objAgeCategoryGender->setGenderId(null);
                    $objAgeCategoryGender->setStatus(2);
                    $this->lstStatus->SelectedValue = 2;
                }
            }

            $objAgeCategoryGender->save();

            $this->updateAndValidateAgeGroups($objAgeCategoryGender);
        }
    }

    /**
     * Handles the change event for the status dropdown list.
     *
     * This method processes the selected status of an age category gender entity
     * based on the user input and the current state of the application. It performs
     * various validations and updates the status of the entity accordingly.
     *
     * @param ActionParams $params Action parameters that include context information for the event.
     * @return void
     */
    protected function lstStatus_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objAgeCategoryGender = AgeCategoryGender::load($this->intClick);
        }

        $this->checkInputs();

        if ($this->blnEditMode === true) {
            if (count($this->errors)) {
                $this->lstStatus->SelectedValue = 2;
                $objAgeCategoryGender->setStatus(2);
            } else {
                if ($objAgeCategoryGender->getIsLocked() == 1) {
                    if ($this->lstStatus->SelectedValue == 1) {
                        $objAgeCategoryGender->setStatus(1);
                        $this->dlgToastr5->notify();
                    } else {
                        $objAgeCategoryGender->setStatus(2);
                        $this->dlgToastr6->notify();
                    }
                } else {
                    $this->dlgModal3->showDialogBox();
                    $this->lstStatus->SelectedValue = 1;
                }
            }

            $objAgeCategoryGender->save();

            $this->updateAndValidateAgeGroups($objAgeCategoryGender);
        }
    }

    /**
     * Handles the save button click event to create and update records related to AgeCategoryGender,
     * AgeCategories, and Genders based on user input and conditions.
     *
     * @param ActionParams $params The parameters received from the user interaction that triggered this method.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->checkInputs();

        if ($this->blnEditMode === false) {
            if (!count($this->errors)) {
                $objAgeCategoryGender = new AgeCategoryGender();
                $objAgeCategoryGender->setAgeCategoryId($this->lstAgeGroup->SelectedValue);
                $objAgeCategoryGender->setAthleteGenderId($this->lstAthleteGender->SelectedValue);
                $objAgeCategoryGender->setGenderId($this->lstGender->SelectedValue);
                $objAgeCategoryGender->setStatus($this->lstStatus->SelectedValue);
                $objAgeCategoryGender->setAssignedByUser($this->intLoggedUserId);
                $objAgeCategoryGender->setAuthor($objAgeCategoryGender->getAssignedByUserObject());
                $objAgeCategoryGender->setPostDate(Q\QDateTime::Now());
                $objAgeCategoryGender->save();

                $objAgeGroup = AgeCategories::loadById($this->lstAgeGroup->SelectedValue);
                $objAgeGroup->setIsLocked(2);
                $objAgeGroup->save();

                $objGender = Genders::loadById($this->lstGender->SelectedValue);
                $objGender->setIsLocked(2);
                $objGender->save();

                Application::executeJavaScript("
                    $('.setting-wrapper').addClass('hidden');
                    $('.form-actions-wrapper').addClass('hidden');
                ");

                $this->hideUserWindow();
                $this->dtgAgeCategoryGender->removeCssClass('disabled');

                $this->dlgToastr7->notify();
            } else {
                $this->resetInputs();
                unset($this->errors);

                $this->dlgModal5->showDialogBox();
            }
        }
    }

    /**
     * Handles the cancel button click event. Hides the settings and actions UI components,
     * resets errors, and enables the AgeCategoryGender datagrid interactions.
     *
     * @param ActionParams $params The parameters received from the cancel button click event.
     * @return void
     */
    public function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->hideUserWindow();
        $this->dtgAgeCategoryGender->removeCssClass('disabled');
        unset($this->errors);
    }

    /**
     * Validates the selected values of the input fields and applies error classes to
     * the fields that are invalid. Any fields that do not have a valid selection are
     * recorded in the errors array.
     *
     * @return void
     */
    public function checkInputs()
    {
        // We check each field and add errors if necessary

        if (!$this->lstAgeGroup->SelectedValue) {
            $this->lstAgeGroup->addCssClass('has-error');
            $this->errors[] = 'lstAgeGroup';
        } else {
            $this->lstAgeGroup->removeCssClass('has-error');
        }

        if (!$this->lstAthleteGender->SelectedValue) {
            $this->lstAthleteGender->addCssClass('has-error');
            $this->errors[] = 'lstAthleteGender';
        } else {
            $this->lstAthleteGender->removeCssClass('has-error');
        }

        if (!$this->lstGender->SelectedValue) {
            $this->lstGender->addCssClass('has-error');
            $this->errors[] = 'lstGender';
        } else {
            $this->lstGender->removeCssClass('has-error');
        }
    }

    /**
     * Updates the selected values in dropdown lists based on the provided object's properties
     * and refreshes the controls to reflect the changes.
     *
     * @param object $objEdit An object containing the properties used to update the dropdown selections.
     * @return void
     */
    public function activeInputs($objEdit)
    {
        $this->lstAgeGroup->SelectedValue = $objEdit->getAgeCategoryId();
        $this->lstAthleteGender->SelectedValue = $objEdit->getAthleteGenderId();
        $this->lstGender->SelectedValue = $objEdit->getGenderId();
        $this->lstStatus->SelectedValue = $objEdit->getStatus();

        $this->lstAgeGroup->refresh();
        $this->lstAthleteGender->refresh();
        $this->lstGender->refresh();
        $this->lstStatus->refresh();
    }

    /**
     * Resets the inputs for age group, athlete gender, gender, and status selections to their default values.
     * Also removes any error indicators and refreshes the respective controls.
     *
     * @return void
     */
    public function resetInputs()
    {
        $this->lstAgeGroup->SelectedValue = null;
        $this->lstAgeGroup->removeCssClass('has-error');
        $this->lstAthleteGender->SelectedValue = null;
        $this->lstAthleteGender->removeCssClass('has-error');
        $this->lstGender->SelectedValue = null;
        $this->lstGender->removeCssClass('has-error');
        $this->lstStatus->SelectedValue = 2;

        $this->lstAgeGroup->refresh();
        $this->lstAthleteGender->refresh();
        $this->lstGender->refresh();
        $this->lstStatus->refresh();
    }

    /**
     * Updates the AgeCategoryGender object with post-update details, validates input fields,
     * and refreshes the display with updated data. Displays notifications based on validation results.
     *
     * @param AgeCategoryGender $objAgeCategoryGender The AgeCategoryGender object being updated and validated.
     * @return void
     */
    protected function updateAndValidateAgeGroups($objAgeCategoryGender)
    {
        // Condition for which notification to show
        if (count($this->errors) === 1) {
            $this->dlgToastr3->notify(); // If only one field is invalid
        } elseif (count($this->errors) > 1) {
            $this->dlgToastr4->notify(); // If there is more than one invalid field
        }

        if ($this->blnEditMode === true) {
            $objAgeCategoryGender->setPostUpdateDate(Q\QDateTime::Now());
            $objAgeCategoryGender->setAssignedEditorsNameById($this->intLoggedUserId);
            $objAgeCategoryGender->save();
        }

        $this->calPostDate->Text = $objAgeCategoryGender->PostDate ? $objAgeCategoryGender->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->Text = $objAgeCategoryGender->PostUpdateDate ? $objAgeCategoryGender->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->txtAuthor->Text = $objAgeCategoryGender->Author;
        $this->txtUsersAsEditors->Text = implode(', ', $objAgeCategoryGender->getUserAsEditorsArray());

        $this->refreshDisplay($objAgeCategoryGender->getId());
        unset($this->errors);
    }

    /**
     * Handles the click event for the delete button, determining whether to show
     * a confirmation or restriction dialog based on the locked state of the
     * AgeCategoryGender object.
     *
     * @param ActionParams $params The parameters received from the user interaction that triggered this method.
     * @return void
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objAgeCategoryGender = AgeCategoryGender::load($this->intClick);
        }

        if ($objAgeCategoryGender->getIsLocked() == 1) {
            $this->dlgModal1->showDialogBox();
        } else {
            $this->dlgModal2->showDialogBox();
        }
    }

    /**
     * Handles the delete action for an AgeCategoryGender item, updates related AgeCategories and Genders,
     * and refreshes the display or hides components as necessary.
     *
     * @param ActionParams $params The parameters received from the user interaction that triggered this method.
     * @return void
     */
    public function deleteItem_Click(ActionParams $params)
    {
        if ($this->intClick) {
            $objAgeCategoryGender = AgeCategoryGender::load($this->intClick);
        }

        $objAgeCategories = AgeCategories::loadByIdFromAgeCategories($objAgeCategoryGender->getAgeCategoryId());
        $objGenders = Genders::loadByIdFromGenders($objAgeCategoryGender->getGenderId());

        if (AgeCategoryGender::countByAgeCategoryId($objAgeCategoryGender->getAgeCategoryId()) == 1) {
            $objAgeCategories->setIsLocked(1);
            $objAgeCategories->save();
        }

        if (AgeCategoryGender::countByAgeCategoryId($objAgeCategoryGender->getGenderId()) == 1) {
            $objGenders->setIsLocked(1);
            $objGenders->save();
        }

        $objAgeCategoryGender->delete();
        $this->dlgModal1->hideDialogBox();

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->dtgAgeCategoryGender->removeCssClass('disabled');

        if (AgeCategoryGender::countAll() === 0) {
            $this->dtgAgeCategoryGender->Display = false;
        }

        $this->refreshDisplay($objAgeCategoryGender->getId());
    }

    /**
     * Handles the click event to hide settings and form action wrappers, re-enables the data grid,
     * and hides the dialog box.
     *
     * @param ActionParams $params The parameters received from the user interaction that triggered this method.
     * @return void
     */
    protected function hideItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->dtgAgeCategoryGender->removeCssClass('disabled');

        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Updates the display of various UI controls based on the state and properties
     * of the given AgeCategoryGender object.
     *
     * @param mixed $objEdit The identifier used to load the corresponding AgeCategoryGender object.
     * @return void
     */
    protected function refreshDisplay($objEdit)
    {
        $objAgeCategoryGender = AgeCategoryGender::load($objEdit);

        if (!$objAgeCategoryGender) {
            $this->lblPostDate->Display = false;
            $this->calPostDate->Display = false;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = false;
            $this->txtAuthor->Display = false;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        } else {
            if ($objAgeCategoryGender->getPostDate() &&
                !$objAgeCategoryGender->getPostUpdateDate() &&
                $objAgeCategoryGender->getAuthor() &&
                !$objAgeCategoryGender->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($objAgeCategoryGender->getPostDate() &&
                $objAgeCategoryGender->getPostUpdateDate() &&
                $objAgeCategoryGender->getAuthor() &&
                !$objAgeCategoryGender->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($objAgeCategoryGender->getPostDate() &&
                $objAgeCategoryGender->getPostUpdateDate() &&
                $objAgeCategoryGender->getAuthor() &&
                $objAgeCategoryGender->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = true;
                $this->txtUsersAsEditors->Display = true;
            }

            $this->calPostDate->Text = $objAgeCategoryGender->PostDate ? $objAgeCategoryGender->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->Text = $objAgeCategoryGender->PostUpdateDate ? $objAgeCategoryGender->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->txtAuthor->Text = $objAgeCategoryGender->Author;
            $this->txtUsersAsEditors->Text = implode(', ', $objAgeCategoryGender->getUserAsEditorsArray());
        }
    }

    /**
     * Resets and hides the user window elements, including clearing text inputs
     * and disabling the display of associated labels and fields.
     *
     * @return void
     */
    protected function hideUserWindow()
    {
        $this->calPostDate->Text = '';
        $this->calPostUpdateDate->Text = '';
        $this->txtAuthor->Text = '';
        $this->txtUsersAsEditors->Text = '';

        $this->lblPostDate->Display = false;
        $this->calPostDate->Display = false;
        $this->lblPostUpdateDate->Display = false;
        $this->calPostUpdateDate->Display = false;
        $this->lblAuthor->Display = false;
        $this->txtAuthor->Display = false;
        $this->lblUsersAsEditors->Display = false;
        $this->txtUsersAsEditors->Display = false;
    }
}