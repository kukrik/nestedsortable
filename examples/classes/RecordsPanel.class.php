<?php

use QCubed as Q;
use QCubed\Action\Ajax;
use QCubed\Bootstrap as Bs;
use QCubed\Event\Change;
    use QCubed\Exception\Caller;
    use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\QDateTime;
use QCubed\QString;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class RecordsPanel extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;

    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

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

    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;
    public $dlgModal6;

    public $lstSportsAreas;
    public $btnAddNewRecord;
    public $lblInfo;
    public $lblWarning;

    public $lblAthletesNames;
    public $lstAthletesNames;
    public $lblSportsAreas;
    public $lstNewSportsAreas;
    public $lblCompetitionAreas;
    public $lstCompetitionAreas;
    public $lblUnits;
    public $lstUnits;
    public $lblResult;
    public $txtResult;
    public $lblDetailedResult;
    public $txtDetailedResult;
    public $lblDifference;
    public $txtDifference;
    public $lblCompetitionVenue;
    public $txtCompetitionVenue;
    public $lblCompetitionDate;
    public $dtxCompetitionDate;
    public $btnCompetitionDate;

    public $tblExistingRecords;
    public $tblNewRecord;
    public $chkRecordStatus;
    public $btnConfirm;
    public $btnReplace;
    public $btnRecordCancel;
    public $lblRecordInfo;
    public $pnlRecordsActionsWrapper;

    public $lblStatus;
    public $lstStatus;
    public $btnCheckConfirm;
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

    public $txtFilter;
    //public $dtgRecords;
    public $btnBack;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $intClick;
    protected $blnEditMode = true;
    protected $errors = []; // Array for tracking errors


    protected $arrExistingRecords;
    protected $intExistingRecord;
    protected $intBestRecord;
    protected $blnPeakRecord;
    protected $blnExistingRecord;

    protected string $strTemplate = 'RecordsPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
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

        $this->intLoggedUserId = 3;
        $this->objUser = User::load($this->intLoggedUserId);

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Sets up various input controls, labels, and UI elements for managing athlete records.
     * This method creates form elements for input fields like first name, last name, birth date,
     * gender, athlete status, along with labels and additional metadata such as creation and update information.
     * It dynamically adjusts the display of certain elements based on the number of Records available.
     *
     * @return void No value is returned as this method generates and configures form elements.
     */
    protected function createInputs()
    {
        $this->lblWarning = new Q\Plugin\Control\Alert($this);
        $this->lblWarning->Dismissable = true;
        $this->lblWarning->addCssClass('alert alert-warning alert-dismissible');
        $this->lblWarning->Text = t('<p>There are no records in the record table.</p>
                                    <p>Please create the first record!</p>');
        $this->lblWarning->setCssStyle('margin-bottom', 0);
        $this->lblWarning->Display = false;

        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please select a sport from the dropdown menu!');
        $this->lblInfo->setCssStyle('margin-bottom', 0);
        $this->lblInfo->Display = false;

        ///////////////////////////////////////////////////////////////////////////////////////////

        $this->lstSportsAreas = new Q\Plugin\Control\Select2($this);
        $this->lstSportsAreas->MinimumResultsForSearch = -1;
        $this->lstSportsAreas->ContainerWidth = 'resolve';
        $this->lstSportsAreas->Theme = 'web-vauu';
        $this->lstSportsAreas->Width = '100%';
        $this->lstSportsAreas->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstSportsAreas->addItem(t('- Select sport area -'), null, true);
        //$this->lstSportsAreas->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstSportsAreas_Change'));

        $uniqueSportsAreas = $this->getUniqueSportsAreas();

        foreach ($uniqueSportsAreas as $sportsAreaId) {
            $sportsArea = SportsAreas::load($sportsAreaId);
            $this->lstSportsAreas->AddItem(t($sportsArea->Name), $sportsAreaId);
        }

        $this->lblAthletesNames = new Q\Plugin\Control\Label($this);
        $this->lblAthletesNames->Text = t('Athlete\'s name');
        $this->lblAthletesNames->addCssClass('col-md-4');
        $this->lblAthletesNames->setCssStyle('font-weight', 'normal');
        $this->lblAthletesNames->Required = true;

        $this->lblSportsAreas = new Q\Plugin\Control\Label($this);
        $this->lblSportsAreas->Text = t('Sports area');
        $this->lblSportsAreas->addCssClass('col-md-4');
        $this->lblSportsAreas->setCssStyle('font-weight', 'normal');
        $this->lblSportsAreas->Required = true;

        $this->lstNewSportsAreas = new Q\Plugin\Control\Select2($this);
        $this->lstNewSportsAreas->MinimumResultsForSearch = -1;
        $this->lstNewSportsAreas->ContainerWidth = 'resolve';
        $this->lstNewSportsAreas->Theme = 'web-vauu';
        $this->lstNewSportsAreas->Width = '100%';
        $this->lstNewSportsAreas->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstNewSportsAreas->addItem(t('- Select sport area -'), null, true);
        $this->lstNewSportsAreas->Enabled = false;
        $this->lstNewSportsAreas->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstNewSportsAreas_Change'));

        $this->lblCompetitionAreas = new Q\Plugin\Control\Label($this);
        $this->lblCompetitionAreas->Text = t('Competition area');
        $this->lblCompetitionAreas->addCssClass('col-md-4');
        $this->lblCompetitionAreas->setCssStyle('font-weight', 'normal');
        $this->lblCompetitionAreas->Required = true;

        $this->lstCompetitionAreas = new Q\Plugin\Control\Select2($this);
        $this->lstCompetitionAreas->MinimumResultsForSearch = -1;
        $this->lstCompetitionAreas->ContainerWidth = 'resolve';
        $this->lstCompetitionAreas->Theme = 'web-vauu';
        $this->lstCompetitionAreas->Width = '100%';
        $this->lstCompetitionAreas->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstCompetitionAreas->addItem(t('- Select competition area -'), null, true);
        $this->lstCompetitionAreas->Enabled = false;
        $this->lstCompetitionAreas->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstCompetitionAreas_Change'));

        $this->lblUnits = new Q\Plugin\Control\Label($this);
        $this->lblUnits->Text = t('Units of measurement');
        $this->lblUnits->addCssClass('col-md-4');
        $this->lblUnits->setCssStyle('font-weight', 'normal');
        $this->lblUnits->Required = true;

        $this->lstUnits = new Q\Plugin\Control\Select2($this);
        $this->lstUnits->MinimumResultsForSearch = -1;
        $this->lstUnits->ContainerWidth = 'resolve';
        $this->lstUnits->Theme = 'web-vauu';
        $this->lstUnits->Width = '100%';
        $this->lstUnits->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstUnits->addItem(t('- Select Unit of measurement -'));
        $this->lstUnits->Enabled = false;

        $this->lblResult = new Q\Plugin\Control\Label($this);
        $this->lblResult->Text = t('Result');
        $this->lblResult->addCssClass('col-md-4');
        $this->lblResult->setCssStyle('font-weight', 'normal');
        $this->lblResult->Required = true;

        $this->txtResult = new Bs\TextBox($this);
        $this->txtResult->Placeholder = t('Result');
        $this->txtResult->setHtmlAttribute('autocomplete', 'off');
        $this->txtResult->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
//        $this->txtResult->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
//        $this->txtResult->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
//        $this->txtResult->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
//        $this->txtResult->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtResult->Enabled = false;

        $this->lblDifference = new Q\Plugin\Control\Label($this);
        $this->lblDifference->Text = t('Difference');
        $this->lblDifference->addCssClass('col-md-4');
        $this->lblDifference->setCssStyle('font-weight', 'normal');

        $this->txtDifference = new Bs\TextBox($this);
        $this->txtDifference->Placeholder = t('Difference');
        $this->txtDifference->setHtmlAttribute('autocomplete', 'off');
        $this->txtDifference->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
//        $this->txtDifference->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
//        $this->txtDifference->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
//        $this->txtDifference->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
//        $this->txtDifference->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtDifference->Enabled = false;

        $this->lblDetailedResult = new Q\Plugin\Control\Label($this);
        $this->lblDetailedResult->Text = t('Detailed result');
        $this->lblDetailedResult->addCssClass('col-md-4');
        $this->lblDetailedResult->setCssStyle('font-weight', 'normal');
        $this->lblDetailedResult->Required = true;

        $this->txtDetailedResult = new Bs\TextBox($this);
        $this->txtDetailedResult->Placeholder = t('Detailed result');
        $this->txtDetailedResult->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtDetailedResult->setHtmlAttribute('autocomplete', 'off');
        $this->txtDetailedResult->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
//        $this->txtDetailedResult->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
//        $this->txtDetailedResult>addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
//        $this->txtDetailedResult>AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
//        $this->txtDetailedResult->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtDetailedResult->Enabled = false;

        $this->lblCompetitionVenue = new Q\Plugin\Control\Label($this);
        $this->lblCompetitionVenue->Text = t('Competition venue');
        $this->lblCompetitionVenue->addCssClass('col-md-4');
        $this->lblCompetitionVenue->setCssStyle('font-weight', 'normal');
        $this->lblCompetitionVenue->Required = true;

        $this->txtCompetitionVenue = new Bs\TextBox($this);
        $this->txtCompetitionVenue->Placeholder = t('Competition venue');
        $this->txtCompetitionVenue->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtCompetitionVenue->setHtmlAttribute('autocomplete', 'off');
        $this->txtCompetitionVenue->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
//        $this->txtCompetitionVenue->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
//        $this->txtCompetitionVenue->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
//        $this->txtCompetitionVenue->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
//        $this->txtCompetitionVenue->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtCompetitionVenue->Enabled = false;

        $this->lblCompetitionDate = new Q\Plugin\Control\Label($this);
        $this->lblCompetitionDate->Text = t('Competition date');
        $this->lblCompetitionDate->addCssClass('col-md-4');
        $this->lblCompetitionDate->setCssStyle('font-weight', 'normal');
        $this->lblCompetitionDate->Required = true;

        $this->dtxCompetitionDate = new Q\Plugin\Control\DateTimeTextBox($this);
        $this->dtxCompetitionDate->Mode = 'date';
        $this->dtxCompetitionDate->DateTimeFormat = 'DD.MM.YYYY';
        $this->dtxCompetitionDate->Placeholder = t('dd.mm.yyyy');
        $this->dtxCompetitionDate->LabelForInvalid = t('dd.mm.yyyy');
        $this->dtxCompetitionDate->setCssStyle('width', '78%');
        $this->dtxCompetitionDate->setHtmlAttribute('autocomplete', 'off');
        $this->dtxCompetitionDate->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'dtxCompetitionDate_EnterKey'));
        $this->dtxCompetitionDate->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->dtxCompetitionDate->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
        $this->dtxCompetitionDate->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->dtxCompetitionDate->UseWrapper = false;
        $this->dtxCompetitionDate->Enabled = false;

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->addCssClass('col-md-4');
        $this->lblStatus->setCssStyle('font-weight', 'normal');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
        $this->lstStatus->SelectedValue = 2;
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

        ///////////////////////////////////////////////////////////////////////////////////////////
        
        $countRecords = Records::countAll();

        if ($countRecords === 0) {
            $this->lblWarning->Display = true;
            $this->lstSportsAreas->Enabled = false;
        } else {
            $this->lblInfo->Display = true;
            $this->lstSportsAreas->Enabled = true;
        }
    }

    /**
     * Creates and initializes a set of buttons for user interaction, including Add New Record Holder,
     * Save, Delete, and Cancel buttons. Each button is styled, configured for validation behavior,
     * and linked to an Ajax click event handler for its corresponding action.
     *
     * @return void This method does not return a value, as it sets up and configures the buttons
     *              for the associated control.
     */
    public function createButtons()
    {
        $this->btnAddNewRecord = new Bs\Button($this);
        $this->btnAddNewRecord->Text = t('Add new record');
        $this->btnAddNewRecord->CssClass = 'btn btn-orange';
        $this->btnAddNewRecord->CausesValidation = false;
        $this->btnAddNewRecord->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddNewRecord_Click'));

        $this->btnCompetitionDate = new Bs\Button($this);
        $this->btnCompetitionDate->Tip = true;
        $this->btnCompetitionDate->Glyph = 'fa fa-chevron-down';
        $this->btnCompetitionDate->CssClass = 'btn btn-default';
        $this->btnCompetitionDate->addCssClass('input-group-addon');
        $this->btnCompetitionDate->setCssStyle('width', 'auto');
        $this->btnCompetitionDate->CausesValidation = false;
        $this->btnCompetitionDate->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->btnCompetitionDate->UseWrapper = false;
        $this->btnCompetitionDate->Enabled = false;

        $this->btnCheckConfirm = new Bs\Button($this);
        $this->btnCheckConfirm->Text = t('Check and confirm');
        $this->btnCheckConfirm->CssClass = 'btn btn-orange';
        $this->btnCheckConfirm->setCssStyle('margin-right', '10px');
        $this->btnCheckConfirm->CausesValidation = true;
        $this->btnCheckConfirm->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCheckConfirm_Click'));
        $this->btnCheckConfirm->Enabled = false;

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

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->addWrapperCssClass('center-button');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnBack_Click'));
    }

    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('Result is required!');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('Detailed result is required!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('Competition venue is required!');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('Competition date is required!');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('The result is in an incorrect format!<p>Please enter numbers only!</p>');
        $this->dlgToastr5->ProgressBar = true;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('The athlete cannot compete outside the predefined age range!');
        $this->dlgToastr6->ProgressBar = true;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('New record saved successfully!');
        $this->dlgToastr7->ProgressBar = true;

        $this->dlgToastr8 = new Q\Plugin\Toastr($this);
        $this->dlgToastr8->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr8->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr8->Message = t('An error occurred while saving the record!');
        $this->dlgToastr8->ProgressBar = true;

        ////////////////////////////////////////////////////

        $this->dlgToastr9 = new Q\Plugin\Toastr($this);
        $this->dlgToastr9->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr9->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr9->Message = t('Updates to some records for this record holder were discarded, and the record has been restored!');
        $this->dlgToastr9->ProgressBar = true;

        $this->dlgToastr10 = new Q\Plugin\Toastr($this);
        $this->dlgToastr10->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr10->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr10->Message = t('<strong>Well done!</strong> This record holder with data is now active!');
        $this->dlgToastr10->ProgressBar = true;

        $this->dlgToastr11 = new Q\Plugin\Toastr($this);
        $this->dlgToastr11->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr11->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr11->Message = t('<strong>Well done!</strong> This record holder with data is now inactive!');
        $this->dlgToastr11->ProgressBar = true;
    }

    /**
     * Creates and configures modal dialogs for displaying warnings, tips, and performing actions.
     *
     * @return void This method does not return any value. It initializes and sets up modal dialogs
     *              with necessary text, titles, styles, and actions to handle user interactions.
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t("Tip");
        $this->dlgModal1->HeaderClasses = 'btn-darkblue';
        $this->dlgModal1->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">Invalid date format!</p>
                                    <p style="line-height: 25px; margin-bottom: 5px;">Please use the date format "' . $this->dtxCompetitionDate->LabelForInvalid . '"! </p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">The previously saved record holder\'s date will be automatically restored!</p>');

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->AutoRenderChildren = true;
        $this->dlgModal2->Title = t('Check and confirm');
        $this->dlgModal2->HeaderClasses = 'btn-default';
        $this->dlgModal2->Size = 'modal-xl';
        $this->dlgModal2->Backdrop = 'static';
        $this->dlgModal2->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\AjaxControl($this, 'btnRecordCancel_Click'));

//        $this->dlgModal1 = new Bs\Modal($this);
//        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
//                                    delete the record holder?</p>
//                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
//        $this->dlgModal1->Title = t('Warning');
//        $this->dlgModal1->HeaderClasses = 'btn-danger';
//        $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
//            ['class' => 'btn btn-orange']);
//        $this->dlgModal1->addCloseButton(t("I'll cancel"));
//        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));
//        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hideItem_Click'));

//        $this->dlgModal2 = new Bs\Modal($this);
//        $this->dlgModal2->Title = t("Tip");
//        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
//        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
//            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
//        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This record holder cannot be deleted
//                                    because they are locked in the records table or leaderboard!</p>
//                                    <p style="line-height: 15px; margin-bottom: -3px;">If you still wish to delete,
//                                    please unlink the record holder from both tables.</p>');

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This record holder cannot be hidden 
                                    because it is locked in the record or leaderboard!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                    and both tables will be updated automatically.</p>');

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The data of the record holder locked 
                                    in the records table or leaderboard can be modified, but required fields must not be left empty.</p>
                                    <p style="line-height: 25px; margin-bottom: 5px;">If this happens, the previous data will be restored!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">After modification, both tables will be updated automatically!</p>');


    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnAddNewRecord_Click(ActionParams $params)
    {
        Application::executeJavaScript("
            $('.setting-wrapper').removeClass('hidden');
            $('.form-actions-wrapper').removeClass('hidden');
        ");

        $this->lstAthletesNames = new Q\Plugin\Control\Select2($this);
        $this->lstAthletesNames->MinimumResultsForSearch = -1;
        $this->lstAthletesNames->ContainerWidth = 'resolve';
        $this->lstAthletesNames->Theme = 'web-vauu';
        $this->lstAthletesNames->Width = '100%';
        $this->lstAthletesNames->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstAthletesNames->addItem('- Select an athlete -', null, true);

        $objAthletesNames = Athletes::loadAll();

        foreach ($objAthletesNames as $objAthletesName) {
            if ($objAthletesName->Status === 1) {
                $this->lstAthletesNames->AddItem($objAthletesName->FirstName . ' ' . $objAthletesName->LastName, $objAthletesName->Id);
            }
        }

        $this->lstAthletesNames->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstAthletesNames_Change'));

        $this->hideUserWindow();

        $this->lstSportsAreas->RemoveAllItems();
        $this->lstSportsAreas->addItem(t('- Select sport area -'), null, true);
        $this->lstSportsAreas->refresh();

        $uniqueSportsAreas = $this->getUniqueSportsAreas();

        foreach ($uniqueSportsAreas as $sportsAreaId) {
            $sportsArea = SportsAreas::load($sportsAreaId);
            $this->lstSportsAreas->AddItem(t($sportsArea->Name), $sportsAreaId);
        }

        $this->lstSportsAreas->Enabled = false;
        $this->lstAthletesNames->focus();
        $this->btnAddNewRecord->Enabled = false;
        $this->btnCheckConfirm->Display = true;
        $this->btnCheckConfirm->Enabled = false;
        $this->btnDelete->Display = false;

        $this->blnEditMode = false;
        $this->lblWarning->Display = false;
        $this->lblInfo->Display = false;
    }

    protected function lstAthletesNames_Change(ActionParams $params)
    {
        if ($this->lstAthletesNames->SelectedValue !== null) {
            $this->lstNewSportsAreas->Enabled = true;

            $this->lstNewSportsAreas->RemoveAllItems();
            $this->lstNewSportsAreas->addItem(t('- Select sport area -'), null, true);

            $this->txtResult->Text = '';
            $this->txtResult->Enabled = false;

            $this->txtDetailedResult->Text = '';
            $this->txtDetailedResult->Enabled = false;

            $this->txtDifference->Text = '';
            $this->txtDifference->Enabled = false;

            $this->txtCompetitionVenue->Text = '';
            $this->txtCompetitionVenue->Enabled = false;

            $this->dtxCompetitionDate->Text = '';
            $this->dtxCompetitionDate->Enabled = false;

            $this->dtxCompetitionDate->Enabled = false;
            $this->btnCompetitionDate->Enabled = false;

            $uniqueSportsAreas = $this->getUniqueSportsAreas();

            foreach ($uniqueSportsAreas as $sportsAreaId) {
                $sportsArea = SportsAreas::load($sportsAreaId);
                $this->lstNewSportsAreas->AddItem($sportsArea->Name, $sportsAreaId);
            }
        } else {
            $this->lstNewSportsAreas->Enabled = false;
            $this->lstNewSportsAreas->RemoveAllItems();
            $this->lstNewSportsAreas->addItem(t('- Select sport area -'), null, true);
            $this->lstNewSportsAreas->refresh();

            $this->lstCompetitionAreas->Enabled = false;
            $this->lstCompetitionAreas->RemoveAllItems();
            $this->lstCompetitionAreas->addItem(t('- Select competition area -'), null, true);
            $this->lstCompetitionAreas->refresh();

            $this->lstUnits->RemoveAllItems();
            $this->lstUnits->addItem(t('- Select Unit of measurement -'));
            $this->lstUnits->refresh();

            $this->txtResult->Text = '';
            $this->txtResult->Enabled = false;

            $this->txtDifference->Text = '';
            $this->txtDifference->Enabled = false;

            $this->txtCompetitionVenue->Text = '';
            $this->txtCompetitionVenue->Enabled = false;

            $this->dtxCompetitionDate->Text = '';
            $this->dtxCompetitionDate->Enabled = false;

            $this->dtxCompetitionDate->Enabled = false;
            $this->btnCompetitionDate->Enabled = false;
        }

        $this->lstNewSportsAreas->focus();
        $this->lstNewSportsAreas->refresh();
    }

    protected function lstNewSportsAreas_Change(ActionParams $params)
    {
        if ($this->lstNewSportsAreas->SelectedValue !== null) {
            $this->lstCompetitionAreas->Enabled = true;

            $this->lstCompetitionAreas->RemoveAllItems();
            $this->lstCompetitionAreas->addItem(t('- Select competition area -'), null, true);
            $this->lstCompetitionAreas->refresh();

            $this->lstUnits->RemoveAllItems();
            $this->lstUnits->addItem(t('- Select Unit of measurement -'));
            $this->lstUnits->refresh();

            $this->txtResult->Text = '';
            $this->txtResult->Enabled = false;

            $this->txtDetailedResult->Text = '';
            $this->txtDetailedResult->Enabled = false;

            $this->txtDifference->Text = '';
            $this->txtDifference->Enabled = false;

            $this->txtCompetitionVenue->Text = '';
            $this->txtCompetitionVenue->Enabled = false;

            $this->dtxCompetitionDate->Text = '';
            $this->dtxCompetitionDate->Enabled = false;

            $this->dtxCompetitionDate->Enabled = false;
            $this->btnCompetitionDate->Enabled = false;

            $objSportsCompetitionAreas = SportsAreasCompetitionAreas::loadArrayBySportsAreasId($this->lstNewSportsAreas->SelectedValue);

            foreach ($objSportsCompetitionAreas as $objSportsAreas) {
                if ($objSportsAreas->SportsAreasId == $this->lstNewSportsAreas->SelectedValue) {
                    $this->lstCompetitionAreas->AddItem($objSportsAreas->SportsCompetitionAreasName, $objSportsAreas->SportsCompetitionAreasId);
                }
            }
        } else {
            $this->lstCompetitionAreas->Enabled = false;

            $this->lstCompetitionAreas->RemoveAllItems();
            $this->lstCompetitionAreas->addItem(t('- Select competition area -'), null, true);
            $this->lstCompetitionAreas->refresh();

            $this->lstUnits->RemoveAllItems();
            $this->lstUnits->addItem(t('- Select Unit of measurement -'));
            $this->lstUnits->refresh();

            $this->txtResult->Text = '';
            $this->txtResult->Enabled = false;

            $this->txtDifference->Text = '';
            $this->txtDifference->Enabled = false;

            $this->txtCompetitionVenue->Text = '';
            $this->txtCompetitionVenue->Enabled = false;

            $this->dtxCompetitionDate->Text = '';
            $this->dtxCompetitionDate->Enabled = false;

            $this->dtxCompetitionDate->Enabled = false;
            $this->btnCompetitionDate->Enabled = false;
        }

        $this->lstCompetitionAreas->focus();
        $this->lstCompetitionAreas->refresh();
    }

    protected function lstCompetitionAreas_Change(ActionParams $params)
    {
        $objCompetitionAreas = SportsCompetitionAreas::loadById($this->lstCompetitionAreas->SelectedValue);

        if ($objCompetitionAreas->IsDetailedResult === 1) {
            $this->txtDetailedResult->Text = '';
            $this->txtDetailedResult->Enabled = true;
        } else {
            $this->txtDetailedResult->Text = '';
            $this->txtDetailedResult->Enabled = false;
        }

        if ($this->lstCompetitionAreas->SelectedValue !== null) {

            $this->lstUnits->RemoveAllItems();
            $this->lstUnits->addItem(t('- Select Unit of measurement -'));

            $objUnits = SportsUnits::loadAll();

            foreach ($objUnits as $objUnits) {

                if ($objCompetitionAreas->UnitId == $objUnits->Id) {
                    $this->lstUnits->addItem(t('- Select Unit of measurement -'));
                    $this->lstUnits->AddItem($objUnits->Name, $objUnits->Id, true);
                    $this->lstUnits->SelectedValue = $objUnits->Id;
                }
            }

            $this->btnCheckConfirm->Enabled = true;

            $this->txtResult->Text = '';
            $this->txtResult->focus();
            $this->txtResult->Enabled = true;

            $this->txtDifference->Text = '';
            //$this->txtDifference->Enabled = true;

            $this->txtCompetitionVenue->Text = '';
            $this->txtCompetitionVenue->Enabled = true;

            $this->dtxCompetitionDate->Text = '';
            $this->dtxCompetitionDate->Enabled = true;

            $this->dtxCompetitionDate->Enabled = true;
            $this->btnCompetitionDate->Enabled = true;
        } else {
            $this->lstUnits->RemoveAllItems();
            $this->lstUnits->addItem(t('- Select Unit of measurement -'));

            $this->btnCheckConfirm->Enabled = false;

            $this->txtResult->Text = '';
            $this->txtResult->Enabled = false;

            $this->txtDifference->Text = '';
            //$this->txtDifference->Enabled = false;

            $this->txtCompetitionVenue->Text = '';
            $this->txtCompetitionVenue->Enabled = false;

            $this->dtxCompetitionDate->Text = '';
            $this->dtxCompetitionDate->Enabled = false;

            $this->dtxCompetitionDate->Enabled = false;
            $this->btnCompetitionDate->Enabled = false;
        }

        $this->lstUnits->refresh();
    }

    protected function dtxCompetitionDate_EnterKey(ActionParams $params)
    {
        if ($this->blnEditMode === true) {

            if ($this->intClick) {
                $objRecord = Records::load($this->intClick);
            }

            $this->checkInputs();

            if ($objRecord->getIsLocked() === 2) {
                if ($this->dtxBirthDate->Text) {
                    if ($this->dtxBirthDate->validateFormat()) {
                        $objRecord->setBirthDate($this->dtxBirthDate->DateTime);
                        $objRecord->save();

                        $this->dlgToastr4->notify();
                        $this->updateAndValidateRecord($objRecord);
                    } else {
                        $this->dtxBirthDate->Text = $objRecord->getBirthDate() ? $objRecord->getBirthDate()->qFormat('DD.MM.YYYY') : null;

                        $this->dlgModal5->showDialogBox();
                        return;
                    }
                } else {
                    $this->dtxBirthDate->Text = $objRecord->getBirthDate() ? $objRecord->getBirthDate()->qFormat('DD.MM.YYYY') : null;

                    $this->dlgModal4->showDialogBox();
                }
            }

            if ($objRecord->getIsLocked() === 1) {
                if ($this->dtxBirthDate->Text) {
                    if ($this->dtxBirthDate->validateFormat()) {
                        $objRecord->setBirthDate($this->dtxBirthDate->DateTime);
                        $objRecord->save();

                        $this->dlgToastr2->notify();
                    } else {
                        $this->dtxBirthDate->Text = $objRecord->getBirthDate() ? $objRecord->getBirthDate()->qFormat('DD.MM.YYYY') : null;

                        $this->dlgModal5->showDialogBox();
                        return;
                    }
                } else {
                    $this->dtxBirthDate->Text = null;
                    $objRecord->setBirthDate(null);

                    $this->lstStatus->SelectedValue = 2;
                    $objRecord->setStatus(2);

                    $this->dlgToastr11->notify();
                }

                $objRecord->save();
                $this->updateAndValidateRecord($objRecord);
            }

            unset($this->errors);
        }
    }

    protected function lstStatus_Change(ActionParams $params)
    {
        if ($this->blnEditMode === true) {

            if ($this->intClick) {
                $objRecord = Records::load($this->intClick);
            }

            $this->checkInputs();

            if (count($this->errors)) {
                $this->lstStatus->SelectedValue = 2;
                $objRecord->setStatus(2);
            }

            if ($objRecord->getIsLocked() === 2) {
                if ($this->lstStatus->SelectedValue == 2) {
                    $this->dlgModal3->showDialogBox();
                    $this->lstStatus->SelectedValue = 1;
                }
            }

            if ($objRecord->getIsLocked() === 1) {
                if (!count($this->errors)) {
                    if ($this->lstStatus->SelectedValue == 1) {
                        $this->lstStatus->SelectedValue = 1;
                        $objRecord->setStatus(1);

                        $this->dlgToastr10->notify();
                    } else {
                        $this->lstStatus->SelectedValue = 2;
                        $objRecord->setStatus(2);

                        $this->dlgToastr11->notify();
                    }
                }

                $objRecord->save();
                $this->updateAndValidateRecord($objRecord);
            }

            unset($this->errors);
        }
    }

    public function btnCancel_Click(ActionParams $params)
    {
        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->hideUserWindow();
        unset($this->errors);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function btnCheckConfirm_Click(ActionParams $params)
    {
        $this->checkInputs();

        $this->txtResult->Text = $this->replaceCommaWithDot($this->txtResult->Text);
        $this->txtDifference->Text = $this->replaceCommaWithDot($this->txtDifference->Text);
        $this->dtxCompetitionDate->Text = trim($this->replaceCommaWithDot($this->dtxCompetitionDate->Text));

        if ($this->lblRecordInfo instanceof Q\Plugin\Control\Alert) {
            $this->lblRecordInfo->Text = t('<p>No existing record was found in the record table for the selected sport based on the athlete\'s gender.</p>
                                          <p>Please create the first record now!</p>');
        } else {
            $this->lblRecordInfo = new Q\Plugin\Control\Alert($this->dlgModal2);
            $this->lblRecordInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblRecordInfo->Text = t('<p>No existing record was found in the record table for the selected sport based on the athlete\'s gender.</p>
                                          <p>Please create the first record now!</p>');
            $this->lblRecordInfo->setCssStyle('margin-bottom', 0);
        }

        if ($this->dtxCompetitionDate->Text && !$this->dtxCompetitionDate->validateFormat()) {
            $this->dtxCompetitionDate->Text = '';
            $this->dlgModal1->showDialogBox();
            return;

        } else if (!count($this->errors)) {

            if (Records::countAll() !== 0) {

                if ($this->tblExistingRecords instanceof Q\Plugin\Control\VauuTable) {
                    $this->tblExistingRecords->setDataBinder('tblExistingRecords_Bind', $this);
                } else {
                    $this->tblExistingRecords = new Q\Plugin\Control\VauuTable($this->dlgModal2);
                    $this->tblExistingRecords->CssClass = "table vauu-table table-responsive";
                    $this->tblExistingRecords->Caption = t("<strong>Existing record(s):</strong>");

                    $col = $this->tblExistingRecords->createNodeColumn(t('Athlete'), QQN::Records()->Athlete);
                    $col->CellStyler->Width = '10%';
                    $col = $this->tblExistingRecords->createCallableColumn(t('Gender'), [$this, 'getAthleteGender']);
                    $col->CellStyler->Width = '6%';
                    $col = $this->tblExistingRecords->createNodeColumn(t('Age group'), QQN::Records()->AgeCategory);
                    $col->CellStyler->Width = '8%';
                    $col = $this->tblExistingRecords->createNodeColumn(t('Competition area'), QQN::Records()->CompetitionArea);
                    $col->CellStyler->Width = '13%';
                    $col = $this->tblExistingRecords->createNodeColumn(t('Result'), QQN::Records()->Result);
                    $col->CellStyler->Width = '6%';
                    $col = $this->tblExistingRecords->createNodeColumn(t('Difference'), QQN::Records()->Difference);
                    $col->CellStyler->Width = '8%';
                    $col = $this->tblExistingRecords->createNodeColumn(t('Detailed result'), QQN::Records()->DetailedResult);
                    $col->CellStyler->Width = '11%';
                    $col = $this->tblExistingRecords->createNodeColumn(t('Competition venue'), QQN::Records()->CompetitionVenue);
                    $col->CellStyler->Width = '13%';
                    $col = $this->tblExistingRecords->createNodeColumn(t('Competition date'), QQN::Records()->CompetitionDate);
                    $col->CellStyler->Width = '13%';
                    $col->Format = 'DD.MM.YYYY';
                    $col = $this->tblExistingRecords->createCallableColumn(t('Is youth record'), [$this, 'getRecordsFlags']);
                    $col->CellStyler->Width = '12%';
                    $col->HtmlEntities = false;
                    $col->CellStyler->TextAlign = 'center';

                    $this->tblExistingRecords->SortableAsHeader = false;
                    $this->tblExistingRecords->UseWrapper = false;
                    $this->tblExistingRecords->setDataBinder('tblExistingRecords_Bind', $this);
                }
            }

            if ($this->tblNewRecord instanceof Q\Plugin\Control\VauuTable) {
                $this->tblNewRecord->setDataBinder('tblNewRecord_Bind', $this);
            } else {
                $this->tblNewRecord = new Q\Plugin\Control\VauuTable($this->dlgModal2);
                $this->tblNewRecord->CssClass = "table vauu-table table-responsive";
                $this->tblNewRecord->Caption = t("<strong>New record:</strong>");

                $col = $this->tblNewRecord->createIndexedColumn(t('Athlete'), 0);
                $col->CellStyler->Width = '10%';
                $col = $this->tblNewRecord->createIndexedColumn(t('Gender'), 1);
                $col->CellStyler->Width = '6%';
                $col = $this->tblNewRecord->createIndexedColumn(t('Age group'), 2);
                $col->CellStyler->Width = '8%';
                $col = $this->tblNewRecord->createIndexedColumn(t('Competition area'), 3);
                $col->CellStyler->Width = '13%';
                $col = $this->tblNewRecord->createIndexedColumn(t('Result'), 4);
                $col->CellStyler->Width = '6%';
                $col = $this->tblNewRecord->createIndexedColumn(t('Difference'), 5);
                $col->CellStyler->Width = '8%';
                $col = $this->tblNewRecord->createIndexedColumn(t('Detailed result'), 6);
                $col->CellStyler->Width = '11%';
                $col = $this->tblNewRecord->createIndexedColumn(t('Competition venue'), 7);
                $col->CellStyler->Width = '13%';
                $col = $this->tblNewRecord->createIndexedColumn(t('Competition date'), 8);
                $col->CellStyler->Width = '13%';
                $col = $this->tblNewRecord->createCallableColumn(t('Is peak record'), [$this, 'IsYouthRecords']);
                $col->CellStyler->Width = '12%';
                $col->HtmlEntities = false;
                $col->CellStyler->TextAlign = 'center';

                $this->tblNewRecord->UseAjax = false;
                $this->tblNewRecord->UseWrapper = false;
                $this->tblNewRecord->setDataBinder('tblNewRecord_Bind', $this);
            }

            if ($this->pnlRecordsActionsWrapper instanceof Q\Control\Panel) {
                $this->pnlRecordsActionsWrapper->AutoRenderChildren = true;
            } else {
                $this->pnlRecordsActionsWrapper = new Q\Control\Panel($this->dlgModal2);
                $this->pnlRecordsActionsWrapper->AutoRenderChildren = true;
                $this->pnlRecordsActionsWrapper->CssClass = "record-actions-wrapper";
                $this->pnlRecordsActionsWrapper->UseWrapper = false;
            }

            if ($this->btnConfirm instanceof Bs\Button &&
                $this->btnRecordCancel instanceof Bs\Button)
            {
                $this->btnConfirm->Text = t('Confirm');
                $this->btnRecordCancel->Text = t('Cancel');
            } else {
                $this->btnConfirm = new Bs\Button($this->pnlRecordsActionsWrapper);
                $this->btnConfirm->Text = t('Confirm');
                $this->btnConfirm->CssClass = 'btn btn-orange';
                $this->btnConfirm->setCssStyle('margin-right', '10px');
                $this->btnConfirm->CausesValidation = true;
                $this->btnConfirm->UseWrapper = false;
                $this->btnConfirm->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnConfirm_Click'));

                $this->btnRecordCancel = new Bs\Button($this->pnlRecordsActionsWrapper);
                $this->btnRecordCancel->Text = t('Cancel');
                $this->btnRecordCancel->CssClass = 'btn btn-default';
                $this->btnRecordCancel->CausesValidation = true;
                $this->btnRecordCancel->UseWrapper = false;
                $this->btnRecordCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnRecordCancel_Click'));
            }

            $this->tblExistingRecords->refresh();
            $this->tblNewRecord->refresh();

            $this->dlgModal2->showDialogBox();
        }

        unset($this->errors);
    }

    public function getAthleteGender(Records $objRecord)
    {
        if (empty($objRecord)) {
            return null;
        }

        $objAthlete = Athletes::load($objRecord->AthleteId);
        $objGender = AthleteGender::load($objAthlete->AthleteGenderId);
        return $objGender->Gender;
    }

    public function getRecordsFlags(Records $objRecord)
    {
        if (empty($objRecord)) {
            return null;
        }

        if ($objRecord->getIsYouthRecords() === 1) {
            return '<i class="fa fa-check" style="color:#449d44;line-height:0.1;"></i>';
        } else {
            return null;
        }
    }

    public function IsYouthRecords()
    {
        if ($this->chkRecordStatus instanceof Q\Plugin\Control\Checkbox) {

            return $this->chkRecordStatus->render(false);
        } else {
            $this->chkRecordStatus = new Q\Plugin\Control\Checkbox($this->tblNewRecord);
            $this->chkRecordStatus->WrapperClass = 'checkbox checkbox-orange';
            $this->chkRecordStatus->addCssClass('js-is-youth');
            $this->chkRecordStatus->UseWrapper = false;

            return $this->chkRecordStatus->render(false);
        }
    }

    public function tblExistingRecords_Bind()
    {
        $objAthlete = Athletes::load($this->lstAthletesNames->SelectedValue);
        $calculatedAgeCroup = $this->calculateAgeCategory($objAthlete->BirthDate, $this->dtxCompetitionDate->DateTime);

        //Application::displayAlert('Vanuse ID: ' . $calculatedAgeCroup);

        // Andmebaasist valime kõik relevantsed rekordid, mis põhinevad sportlase sool ja võistlusalal
        $this->arrExistingRecords = Records::queryArray(
            QQ::AndCondition(
                QQ::equal(QQN::Records()->AthleteGenderId, $objAthlete->AthleteGenderId),
                QQ::equal(QQN::Records()->CompetitionAreaId, $this->lstCompetitionAreas->SelectedValue)
            )
        );

        // Kui tulemusi pole, määrame datasource-i tühjaks
        $this->tblExistingRecords->DataSource = $this->arrExistingRecords ?: [];

        // Määrame, kas rekorditabel kuvatakse või mitte
        if ($this->arrExistingRecords) {
            $this->tblExistingRecords->Display = true;
            $this->lblRecordInfo->Display = false; // Rekordite puudumise info peidetakse
        } else {
            $this->tblExistingRecords->Display = false; // Rekorditabel peidetakse
            $this->lblRecordInfo->Display = true;
        }

        /////////////////////////////////////////////////////////////////////////////////

        // NÕUDED REKORDITABELILE
        // Vanuseklassi "Täiskasvanud (Adults)" tulemusi käsitletakse kokkuleppeliselt kui tipprekordite tabelit.
        // Igal vanuseklassi, sportlase soo ja võistlusala kombinatsioonil võib olla ainult üks rekord.
        // Noorte tulemusi kuvatakse eraldi vastavalt nende vanusegruppidele.
        //
        // Käsitlused:
        // 1. Kui uus noorte rekord EI OLE parem kui täiskasvanu rekord:
        //    - Rekord lisatakse ainult noorte vanusegruppide tabelisse, kuid täiskasvanu rekord jääb muutmatuks.
        //
        // 2. Kui uus noorte rekord ON parem kui täiskasvanu rekord:
        //    - See asendab täiskasvanu rekordi. Isiku rekordile märgitakse lipuke "noorte rekord".
        //    - Rekord muutub nähtavaks mõlemas tabelivaates.
        //
        // Sellise loogikaga tagatakse rekordite järjepidevus ja grupipõhine ülevaade.

        ///////////////////////////////////////////////////////////////////////////////////////////

        // Kui rekorditabelis pole valitud sportlase soole, võistlusalale ja vanuseklassile vastavaid tulemusi, siis
        // uue rekordi salvestamine on väga lihtne.

        $this->chkRecordStatus = new Q\Plugin\Control\Checkbox($this->tblNewRecord);
        $this->chkRecordStatus->WrapperClass = 'checkbox checkbox-orange';

        if (!$this->arrExistingRecords) {
            // Teeme kinnitamise nupu aktiivseks
            $this->btnConfirm->Enabled = true;

            // Kui uus rekord kuulub noorteklassidesse, teeme märkeruudu aktiivseks.
            // Kasutajale/andmesisestajale jääb täielik vastutus, et kas noorte rekord
            // kuulub täiskasvanute parimate tulemuste hulka.
            if ($this->eligibleForYouthCategory($objAthlete->BirthDate, $this->dtxCompetitionDate->DateTime)) {
                $this->chkRecordStatus->Enabled = true;
            } else {
                $this->chkRecordStatus->Enabled = false;
            }

            $this->chkRecordStatus->refresh();

        } else { // Rekordid on olemas

            // Kui tabelis on olemas rekordeid, märkeruut inaktiveeritakse
            $this->chkRecordStatus->Enabled = false;

            // Määrame kõige parema tulemusega rekordi unikaalse ID
            $this->intBestRecord = $this->findBestRecordId($this->arrExistingRecords);
            $objBestRecord = Records::load($this->intBestRecord);

            // Määrame olemasoleva vanuseklassi jaoks sobivaima rekordi
            $this->intExistingRecord = $this->findExistingRecordId($this->arrExistingRecords, $calculatedAgeCroup);
            $objRecord = Records::load($this->intExistingRecord);

            //Application::displayAlert('IS PEAK RECORD: ' . $this->isBetterResult($this->txtResult->Text, $objBestRecord->Result));

            // Kui uus rekord ületab parimat olemasolevat tulemust, asetatakse uus rekord kõrgeimale tasemele
            if ($this->isBetterResult($this->txtResult->Text, $objBestRecord->Result)) {
                $this->blnPeakRecord = false;
            } else {
                $this->blnPeakRecord = true;
            }

            ///////////////////////////////////////////////////////////////////////////////////////////

            // Tähelepanu! Mõlemate kontrollide järjekorda EI TOHI muuta!
            // Kui kontrollide järjekord on vale, ei ole uue ja olemasoleva rekordi tulemuste VAHE arvutatud korrektselt.
            // Veendu, et seda piirangut järgitakse nii jooksva koodi kui ka muudatuste korral.

            if ($this->intExistingRecord) {
                // Kontrollime, kas sisestatud tulemus on parem kui olemasolev tulemus.
                if ($this->isBetterResult($this->txtResult->Text, $objRecord->Result)) {
                    // Kui on parem tulemus, arvutame nende tulemuste erinevuse.
                    $this->txtDifference->Text = $this->calculateDifference($this->txtResult->Text, $objRecord->Result);
                }
            }

            if ($this->intBestRecord) {
                // Kontrollime, kas sisestatud tulemus on parem kui seni parim tulemus.
                if ($this->isBetterResult($this->txtResult->Text, $objBestRecord->Result)) {
                    // Siin arvutatakse erinevus uue ja parima tulemuse vahel.
                    $this->txtDifference->Text = $this->calculateDifference($this->txtResult->Text, $objBestRecord->Result);

                    // Kui rekord kuulub 'Youth' kategooriasse, märgime rekordi vastavalt.
                    if ($this->eligibleForYouthCategory($objAthlete->BirthDate, $this->dtxCompetitionDate->DateTime)) {
                        $this->chkRecordStatus->Checked = true;
                    } else {
                        $this->chkRecordStatus->Checked = false;
                    }

                    // Rekordi staatuse muutumine kantakse liidesesse.
                    $this->chkRecordStatus->refresh();
                }
            }

            ///////////////////////////////////////////////////////////////////////////////////////////

            // Kui sisestatud uus tulemus on olemasolevast rekordist kehvem,
            // muudetakse kinnitamise nupp mitteaktiivseks, vältimaks vale tulemuse salvestamist.
            if ($this->intExistingRecord) {
                if ($this->isBetterResult($this->txtResult->Text, $objRecord->Result) === true) {

                    if ($calculatedAgeCroup === $objRecord->AgeCategoryId) {
                        $this->btnConfirm->Enabled = true;
                        // Märgime, et olemasolev rekord ei ole enam asjakohane.
                        $this->blnExistingRecord = false;

                    } else if ($calculatedAgeCroup !== $objRecord->AgeCategoryId) {
                        $this->btnConfirm->Enabled = true;


                    } else {
                        $this->btnConfirm->Enabled = false;
                    }


                    /////////////////////////////////////


                } else {
                    $this->btnConfirm->Enabled = true;
                }



            } else {
                // Kui puudub 'olemasolev rekord', lubatakse kinnitamise nupp vaikimisi.
                $this->btnConfirm->Enabled = true;
            }
        }

        // Märkuse sättimine tabelireale, mis tähistab praegust või parimat.
        $this->tblExistingRecords->RowParamsCallback = [$this, "tblExistingRecords_getRowParams"];
    }

    public function tblNewRecord_Bind()
    {
        $objAthlete = Athletes::load($this->lstAthletesNames->SelectedValue);

        // Arvutame sportlase sünnikuupäeva ja võistluse kuupäeva põhjal tema vanuseklassi sobivuse.
        $calculatedAgeCroup = $this->calculateAgeCategory($objAthlete->BirthDate, $this->dtxCompetitionDate->DateTime);

        // Laadime vanuseklassi objekti, kus määratletud klassi nimi (Adults, U23, jne).
        $objCalculatedAgeGroup = AgeCategories::load($calculatedAgeCroup);

        // Määrame sportlase soo andmebaasist, kasutades tema soo ID-d.
        $objGender = AthleteGender::load($objAthlete->AthleteGenderId);

        // Võtame seotud võistluspaiga andmebaasi põhjal.
        $objCompetitionArea = SportsCompetitionAreas::loadById($this->lstCompetitionAreas->SelectedValue);

        // Uue rekordi andmed täidetakse ja seotakse tabelina uue rekordi tabelivaatega.
        $data[] = [
            $objAthlete->FirstName . ' ' . $objAthlete->LastName,           // Sportlase täisnimi
            $objGender,                                                     // Sportlase sugu
            $objCalculatedAgeGroup->ClassName,                              // Sportlase vanuseklass (näiteks "Adults")
            $objCompetitionArea->Name,                                      // Võistlusala nimi
            $this->replaceCommaWithDot($this->txtResult->Text),             // Tulemus (koma asendatakse punktiga)
            $this->txtDifference->Text,                                     // Erinevus võrreldes eelneva rekordiga
            $this->txtDetailedResult->Text,                                 // Lisainfo tulemuse kohta
            $this->txtCompetitionVenue->Text,                               // Võistluse asukoht
            $this->replaceCommaWithDot($this->dtxCompetitionDate->Text),    // Võistluse kuupäev (koma asendatakse punktiga)
            $this->lstStatus->SelectedValue                                 // Rekordi olek
        ];

        // Tabeli andmeallikaks määratakse eespool loodud massiiv.
        $this->tblNewRecord->DataSource = $data;
    }

    public function tblExistingRecords_getRowParams($objRowObject, $intRowIndex)
    {
        $params = [];

        // Märgime olemasoleva rekordi oleku visuaalseks
        if ($objRowObject->getId() == $this->intExistingRecord) {
            if ($this->blnExistingRecord === false) {
                $params['class'] = 'interchangeable-record';
            }
        }

        // Märgime tipprekordi oleku visuaalseks
        if ($objRowObject->getId() == $this->intBestRecord) {
            if ($this->blnPeakRecord === true) {
                $params['class'] = 'is-best-record';
            } else {
                $params['class'] = 'interchangeable-record';
            }
        }

        return $params;
    }

    protected function btnConfirm_Click(ActionParams $params)
    {
        // Peida dialoogiboks (võistluse või rekordi lisamisel).
        $this->dlgModal2->hideDialogBox();

        try {

            // Laadime seotud sportlast ja spordiala uue rekordi sisestuse jaoks.
            $objAthlete = Athletes::load($this->lstAthletesNames->SelectedValue);
            $objSportArea = SportsAreas::load($this->lstNewSportsAreas->SelectedValue);

            // Laadime võistluspaiga ja spordiala paari, kontrollimaks nende sidumist/ühendust.
            $objSportAreaCompetitionAreaPair = SportsAreasCompetitionAreas::getIdByPair(
                $this->lstNewSportsAreas->SelectedValue,
                $this->lstCompetitionAreas->SelectedValue
            );

            $objSportsAreasCompetitionAreas = SportsAreasCompetitionAreas::load($objSportAreaCompetitionAreaPair);

            // Arvutame vanuseklassi, millele sportlane sobib ja laadime selle koos tema soo infoga.
            $calculatedAgeCroup = $this->calculateAgeCategory($objAthlete->BirthDate, $this->dtxCompetitionDate->DateTime);
            $objAgeCategoryGenderPair = AgeCategoryGender::getIdByPair($calculatedAgeCroup, $objAthlete->AthleteGenderId);
            $objAgeCategoryGender = AgeCategoryGender::load($objAgeCategoryGenderPair);

            // Loome uue rekordi ja täidame selle seotud sisendväärtustega.
            $objRecord = new Records();
            $this->saveInputs($objRecord); // Kõik sisendid salvestatakse rekordobjekti.
            $objRecord->setAssignedByUser($this->objUser->Id); // Kontrolli, kes andis rekordi.
            $objRecord->setAuthor($objRecord->getAssignedByUserObject());
            $objRecord->setPostDate(Q\QDateTime::Now()); // Määratakse postitamisel hetkekuupäev.
            $objRecord->save();

            //////////////////////////////////////////////////////

            // Samad andmed ka saata EDETABELI hulka

            //////////////////////////////////////////////////////

            $this->dlgToastr7->notify();

        } catch (Exception $e) {
            $this->dlgToastr8->notify();
        }

        // Viimase tulemustabeli muutmise kuupäeva seadmine spordiala andmetele.
        $objSportArea->setRecordTableUpdateDate(Q\QDateTime::Now());
        $objSportArea->save();

        // Lukkude kontrollimine: sportlane, võistlustabel ja vanuseklassi sugu ei tohi olla vabastatud.
        if ($objAthlete->getIsLocked() !== 2) {
            $objAthlete->setIsLocked(2);
            $objAthlete->save();
        }

        if ($objSportsAreasCompetitionAreas->getIsLocked() !== 2) {
            $objSportsAreasCompetitionAreas->setIsLocked(2);
            $objSportsAreasCompetitionAreas->save();
        }

        if ($objAgeCategoryGender->getIsLocked() !== 2) {
            $objAgeCategoryGender->setIsLocked(2);
            $objAgeCategoryGender->save();
        }

        // Kui rekorditabelis on olemasolevad rekordid, töödeldakse neid vastavalt uue tulemuse lisamise reeglitele.
        if ($this->arrExistingRecords) {

            // Kontrollime parima rekordi (Best Record) kustutamise tingimusi.
            if ($this->intBestRecord) {
                $objRecord = Records::load($this->intBestRecord);

                // Kui parim rekord kuulub ka noorte rekordite nimekirja (IsYouthRecords = 1),
                // eemaldatakse sellest ainult noorte rekordi staatus, jättes rekordi alles
                // oma vanusekategooriasse (täiskasvanute rekordid).
                if ($objRecord->IsYouthRecords === 1) {
                    $objRecord->setIsYouthRecords(0); // Muudame noorte rekordi staatuse väärtuseks 0.
                    $objRecord->save(); // Salvesta muudatused andmebaasi.

                    // Kui parim rekord ei ole seotud noorte rekordiga ja ületamishetkel ei ole seotud
                    // 'blnPeakRecord' väärtusega (nt see ei ole silmapaistva tulemuse staatuses),
                    // kustutatakse rekord täielikult, kuna see on ületatud.
                } else if ($this->blnPeakRecord === false) {
                    $objRecord->delete(); // Kustutame parima rekordi andmebaasist.
                }
            }

            // Käsitseme olemasoleva rekordi (Existing Record) kustutamise tingimusi.
            // Kui olemasolev rekord ei ole enam parim ja uus tulemus selle asendab:
            if ($this->intExistingRecord) {
                // Kui olemasoleva rekordi ID erineb parima rekordi ID-st:
                if ($this->intExistingRecord !== $this->intBestRecord) {
                    $objRecord = Records::load($this->intExistingRecord);

                    // Kui olemasolev rekord ei kvalifitseeru enam kehtivaks rekordiks,
                    // kustutame selle täielikult.
                    if ($this->blnExistingRecord === false) {
                        $objRecord->delete(); // Kustutame olemasoleva rekordi andmebaasist.
                    }
                }
            }
        }

        // Sulgeme kasutajaaknad (nt dialoogid) ja taastame nuppude oleku.
        $this->hideUserWindow();

        // Peidab seaded ja vormi tegevuste elemendid kasutajaliidesest JavaScript'i abil.
        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
        ");

        // Nuppude lubamine vastavalt tingimustele.
        $this->btnAddNewRecord->Enabled = true;

        // Spordiala kuvamiseks aktiveerime drop-down'i vaid siis, kui andmeid on saadaval.
        if (Records::countAll() !== 0) {
            $this->lstSportsAreas->Enabled = true;
        } else {
            $this->lstSportsAreas->Enabled = false;
        }

        // Uuendame nimekirja (ka langetatud spordialade tabelit).
        $this->lstSportsAreas->refresh();
    }

    protected function btnRecordCancel_Click(ActionParams $params)
    {
        // Peidab seaded ja vormi tegevuste elemendid kasutajaliidesest JavaScript'i abil.
        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden');
        ");

        // Sulgeme kasutajaaknad (nt dialoogid) ja taastame nuppude oleku.
        $this->hideUserWindow();

        // Peida dialoogiboks (võistluse või rekordi lisamisel).
        $this->dlgModal2->hideDialogBox();
    }




    public function btnBack_Click(ActionParams $params)
    {
        Application::redirect('statistics_list.php');
    }

    protected function hideItem_Click(ActionParams $params)
    {
        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

//        $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
//        $this->txtFilter->Enabled = true;
//        $this->dtgRecords->Paginator->Enabled = true;
//        $this->dtgRecords->removeCssClass('disabled');

        $this->dlgModal1->hideDialogBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function checkInputs()
    {
        // We check each field and add errors if necessary
        if (!$this->dtxCompetitionDate->Text) {
            $this->dlgToastr4->notify();
            $this->dtxCompetitionDate->setHtmlAttribute('required', 'required');
            $this->errors[] = 'dtxCompetitionDate';
        } else {
            $this->dtxCompetitionDate->removeHtmlAttribute('required');
        }

        if (!$this->txtCompetitionVenue->Text) {
            $this->dlgToastr3->notify();
            $this->txtCompetitionVenue->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtCompetitionVenue';
        } else {
            $this->txtCompetitionVenue->removeHtmlAttribute('required');
        }

        $objCompetitionAreas = SportsCompetitionAreas::loadById($this->lstCompetitionAreas->SelectedValue);

        if ($objCompetitionAreas->IsDetailedResult === 1) {
            if (!$this->txtDetailedResult->Text) {
                $this->dlgToastr2->notify();
                $this->txtDetailedResult->setHtmlAttribute('required', 'required');
                $this->errors[] = 'txtDetailedResult';
            } else {
                $this->txtDetailedResult->removeHtmlAttribute('required');
            }
        }

        if (!$this->txtResult->Text) {
            $this->dlgToastr1->notify();
            $this->txtResult->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtResult';
        } else {
            $this->txtResult->removeHtmlAttribute('required');
        }

        if (!$this->cleanAndValidateInput($this->txtResult->Text)) {
            $this->dlgToastr5->notify();
            $this->txtResult->Text = '';
            $this->txtResult->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtNumbers';
        } else {
            $this->txtResult->removeHtmlAttribute('required');
        }

        $objAthlete = Athletes::load($this->lstAthletesNames->SelectedValue);
        $birthDate = new DateTime($objAthlete->BirthDate);

        if ($this->dtxCompetitionDate->Text) {
            $competitionDate = $this->dtxCompetitionDate->DateTime;

            //Check: Check the youngest age limit according to pre-defined categories.
            if ($this->youngestAgeLimitExceeded($birthDate, $competitionDate)) {
                $this->dtxCompetitionDate->Text = '';
                $this->dlgToastr6->notify();
                $this->dtxCompetitionDate->setHtmlAttribute('required', 'required');
                $this->errors[] = 'youngestAgeLimitExceeded';
                return;
            } else {
                $this->dtxCompetitionDate->removeHtmlAttribute('required');
            }
        }
    }

    public function resetInputs()
    {
        $this->lstAthletesNames->refresh();
        $this->lstNewSportsAreas->refresh();
        $this->lstCompetitionAreas->refresh();
        $this->lstUnits->refresh();

        $this->txtResult->removeHtmlAttribute('required');
        $this->txtDetailedResult->removeHtmlAttribute('required');
        $this->txtCompetitionVenue->removeHtmlAttribute('required');
        $this->dtxCompetitionDate->removeHtmlAttribute('required');
    }

    public function activeInputs($objEdit)
    {
        $this->lstAthletesNames->SelectedValue = $objEdit->getAthleteId();

        $objSportsCompetitionAreas = SportsAreasCompetitionAreas::loadById($objEdit->getSportAreaCompetitionAreaId());

        $this->lstNewSportsAreas->SelectedValue = $objSportsCompetitionAreas->getSportsAreasId();
        $this->lstCompetitionAreas->SelectedValue = $objSportsCompetitionAreas->getSportsCompetitionAreasId();

        $this->lstUnits->SelectedValue = $objEdit->getUnitId();
        $this->txtResult->Text = $objEdit->getResult();
        $this->txtDifference->Text = $objEdit->getDifference();
        $this->txtDetailedResult->Text = $objEdit->getDetailedResult();
        $this->txtCompetitionVenue->Text = $objEdit->getCompetitionVenue();
        //$this->dtxCompetitionDate->Text = $objEdit->getCompetitionDate() ? $objRecord->getCompetitionDate()->qFormat('DD.MM.YYYY') : null;
        $this->lstStatus->SelectedValue = $objEdit->getStatus();

        $this->lstStatus->refresh();
    }

    public function saveInputs($objEdit)
    {
        $objAthlete = Athletes::load($this->lstAthletesNames->SelectedValue);
        $objUnitType = SportsUnits::load($this->lstUnits->SelectedValue);

        $objEdit->setAthleteId($this->lstAthletesNames->SelectedValue);
        $objEdit->setAthleteGenderId($objAthlete->AthleteGenderId);
        $objEdit->setAgeCategoryId($this->calculateAgeCategory($objAthlete->getBirthDate(), $this->dtxCompetitionDate->DateTime));
        $objEdit->setSportAreaId($this->lstNewSportsAreas->SelectedValue);
        $objEdit->setCompetitionAreaId($this->lstCompetitionAreas->SelectedValue);
        $objEdit->setResult($this->txtResult->Text);
        $objEdit->setUnit($objUnitType->getUnit());
        $objEdit->setDifference($this->txtDifference->Text);
        $objEdit->setDetailedResult($this->txtDetailedResult->Text);

        if ($this->chkRecordStatus->Checked == true) {
            $objEdit->setIsYouthRecords(1);
        } else {
            $objEdit->setIsYouthRecords(0);
        }

        $objEdit->setCompetitionVenue($this->txtCompetitionVenue->Text);
        $objEdit->setCompetitionDate($this->dtxCompetitionDate->DateTime);
        $objEdit->setStatus($this->lstStatus->SelectedValue);
    }

    protected function updateAndValidateRecord($objRecord)
    {
        $objRecord->setPostUpdateDate(Q\QDateTime::Now());
        $objRecord->setAssignedEditorsNameById($this->intLoggedUserId);
        $objRecord->save();

        $this->calPostDate->Text = $objRecord->PostDate ? $objRecord->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->Text = $objRecord->PostUpdateDate ? $objRecord->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->txtAuthor->Text = $objRecord->Author;
        $this->txtUsersAsEditors->Text = implode(', ', $objRecord->getUserAsEditorsArray());

        $this->refreshDisplay($objRecord->getId());
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Determines the age category of an individual based on their birth date and a competition date.
     * Compares the individual's age against predefined age categories and assigns the appropriate category.
     *
     * @param \DateTime $birthDate The birth date of the individual.
     * @param \DateTime $competitionDate The date of the competition to calculate the age against.
     *
     * @return int|null The ID of the matching age category, or null if no category matches.
     */
    protected function calculateAgeCategory($birthDate, $competitionDate)
    {
        if ($birthDate && $competitionDate) {
            $age = $competitionDate->diff($birthDate)->y;

            $categories = AgeCategories::loadAll();

            foreach ($categories as $category) {
                // Checks if age is in range and considers NULL value
                if ($age >= $category->MinAge &&
                    ($category->MaxAge === null || $age <= $category->MaxAge)) {
                    return $category->Id;
                }
            }

            return null; // If no category is found, returns null.
        }
    }

    protected function eligibleForYouthCategory($birthDate, $competitionDate)
    {
        // Arvutame sportlase vanuse võistluspäeva seisuga
        $age = $competitionDate->diff($birthDate)->y;

        // Laadime kõik vanusekategooriad ja leiame suurima sobiva maksimaalse vanuse
        $categories = AgeCategories::loadAll();
        $maxYouthAge = null; // Algne maksimaalne vanus noortekategooriale

        foreach ($categories as $category) {
            // Kui kategooria maksimum vanus on määratud ja see on piisavalt noor, uuri lähemalt
            if ($category->MaxAge !== null && ($category->MinAge === null || $category->MinAge <= $age)) {
                // Leiame sobiva noorteklassi suurima võimalik vanuse
                if ($maxYouthAge === null || $category->MaxAge > $maxYouthAge) {
                    $maxYouthAge = $category->MaxAge;
                }
            }
        }

        // Tagastame tulemuse: kas sportlase vanus jääb noortekategooriasse
        return $maxYouthAge !== null && $age <= $maxYouthAge;
    }

    protected function youngestAgeLimitExceeded($birthDate, $competitionDate)
    {
        // We calculate the athlete's age at the time of the competition.
        $age = $competitionDate->diff($birthDate)->y;

        // I load all the age categories and find the youngest minimum age.
        $categories = AgeCategories::loadAll();
        $minAge = null; // Algne miinimum on määramata.

        foreach ($categories as $category) {
            if ($minAge === null || $category->MinAge < $minAge) {
                $minAge = $category->MinAge;
            }
        }

        // We check whether the athlete's age is below the minimum age limit.
        return $minAge !== null && $age < $minAge;
    }

    /**
     * Retrieves a list of unique sports area IDs based on all available competition areas.
     * This method ensures that each sports area ID is included only once in the returned list.
     *
     * @return array an array of unique sports area IDs
     */
    public function getUniqueSportsAreas() {
        $allItems = SportsAreasCompetitionAreas::loadAll();
        $uniqueSportsAreas = [];

        foreach ($allItems as $item) {
            if (!in_array($item->SportsAreasId, $uniqueSportsAreas)) {
                $uniqueSportsAreas[] = $item->SportsAreasId;
            }
        }

        return $uniqueSportsAreas;
    }

    /**
     * Cleans and validates a given input string to ensure it contains only allowed characters
     * and normalizes specific characters for further processing.
     *
     * @param string $input The input string to be validated and cleaned.
     * @return string|bool Returns the normalized input string if valid, or false if invalid.
     */
    protected function cleanAndValidateInput(string $input)
    {
        // Kontrollime, kas sisend sisaldab üksnes lubatud märke
        if (!preg_match('/^[\d,.:* ]*$/', $input)) {
            return false;
        }

        // Asendame kõik komad punktidega
        $normalizedInput = preg_replace('/,/', '.', $input);

        // Tagastame muudetud sisendi
        return $normalizedInput;
    }

    /**
     * Calculates the difference between two numeric string inputs after sanitizing them.
     * Converts the sanitized strings to floats, computes the difference, and rounds it to two decimal places.
     * Returns null if either input is null or empty.
     *
     * @param string|null $input1 The first numeric string input, which can include asterisks.
     * @param string|null $input2 The second numeric string input, which can include asterisks.
     *
     * @return float|null The calculated difference rounded to two decimal places, or null if inputs are invalid.
     */
    protected function calculateDifference(?string $input1, ?string $input2): ? float
    {
        // Kui ükskõik milline sisend on tühi või null
        if (empty($input1) || empty($input2)) {
            return null;
        }

        // Eemaldame ajutiselt ülemised tärnid mõlemast sisendist
        $cleanInput1 = str_replace('*', '', $input1);
        $cleanInput2 = str_replace('*', '', $input2);

        // Teisendame väärtused ujukomaarvudeks arvutamiseks
        $num1 = floatval($cleanInput1);
        $num2 = floatval($cleanInput2);

        // Arvutame erinevuse ja ümardame 2 komakohani
        return round(($num1 - $num2), 2);
    }


    protected function findBestRecordId($objRecords)
    {
        // Kui massiiv on tühi, tagastame nulli
        if (empty($objRecords)) {
            return null;
        }

        // Alustame esimese tulemusega kui algse parimaga
        $objBestRecord = $objRecords[0]; // Võtame esimese kirje algseks parimaks

        foreach ($objRecords as $objCurrentRecord) {
            // Võrdleme iga uut kirjet hetkel parimaga, kasutades isBetterResult funktsiooni
            if ($this->isBetterResult($objCurrentRecord->result, $objBestRecord->result)) {
                $objBestRecord = $objCurrentRecord; // Kui uus on parem, uuendame hetke parimat
            }
        }

        // Tagastame parima tulemuse kirje ID
        return $objBestRecord->Id;
    }

    protected function findExistingRecordId($objRecords, $objAgeGroup)
    {
        // Kui massiiv on tühi (ei ole ühtegi tulemust), tagastame nulli
        if (empty($objRecords)) {
            return null;
        }

        // Kui vanuseklassi ID ei ole määratud (null või tühi), tagastame nulli
        if (empty($objAgeGroup)) {
            return null;
        }

        // Itereerime iga tulemuse kirje (objCurrentRecord) üle massiivis $objRecords
        foreach ($objRecords as $objCurrentRecord) {

            // Kontrollime kõigepealt, kas antud rekord kuulub noorte rekordisse
            // Kui IsYouthRecords === 1, eelistame antud kirjet ja väljume tsüklist siin
            if ( $objCurrentRecord->IsYouthRecords === 1) {
                $objExistingRecord = $objCurrentRecord;
                break;
            }

            // Juhul kui noorte rekordit ei ole või see ei kehti, kontrollime vanusekategooriat
            // Kui rekordi "AgeCategoryId" vastab antud $objAgeGroup väärtusele, arvestame sellega järgmisena
            if ($objCurrentRecord->AgeCategoryId === $objAgeGroup) {
                $objExistingRecord = $objCurrentRecord;
                break; // Kuna oleme sobiva kirje leidnud, katkestame ülejäänud iteratsiooni
            }
        }

        // Tagastame leitud rekordi ID või nulli, kui ükski tingimus ei kehti
        return isset($objExistingRecord) ? $objExistingRecord->Id : null;
    }

    protected function isBetterResult($objCurrentRecord, $objBestRecord)
    {
        $objUnitType = SportsUnits::load($this->lstUnits->SelectedValue);

        // Eemaldame ajutiselt ülemised tärnid mõlemast sisendist
        $cleanCurrentRecord = str_replace('*', '', $objCurrentRecord);
        $cleanBestRecord = str_replace('*', '', $objBestRecord);

        // Kontrollime, mis tüüpi andmeid võistlusel mõõdetakse
        if ($objUnitType->Unit === 'seconds') {
            // Aja puhul väiksem väärtus on parem
            return $cleanCurrentRecord < $cleanBestRecord;
        } elseif ($objUnitType->Unit === 'meters') {
            // Kauguste või kõrguse või jms puhul suurem väärtus on parem
            return $cleanCurrentRecord > $cleanBestRecord;
        } elseif ($objUnitType->Unit === 'points') {
            // Punktide puhul suurem väärtus on parem
            return $cleanCurrentRecord > $cleanBestRecord;
        }

        // Defaults: midagi läks valesti. Tagastame false.
        return false;
    }

    /**
     * Replace commas with dots in numeric strings.
     *
     * @param string $input The input string containing numeric values.
     * @return string The formatted string with commas replaced by dots.
     */
    protected function replaceCommaWithDot(string $input): string
    {
        // Use regex to find numbers with commas as decimals and replace them with dots
        return preg_replace('/(\d+),(\d+)/', '$1.$2', $input);
    }

    /**
     * Updates the UI components related to an Athlete object based on the provided
     * object's properties. Adjusts the visibility of labels and input fields and
     * populates their values accordingly.
     *
     * @param int $objEdit The ID of the Athlete object to be loaded and processed.
     *
     * @return void
     */
    protected function refreshDisplay($objEdit)
    {
        $objRecord = Records::load($objEdit);

        if (!$objRecord) {
            $this->lblPostDate->Display = false;
            $this->calPostDate->Display = false;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = false;
            $this->txtAuthor->Display = false;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        } else {
            if ($objRecord->getPostDate() &&
                !$objRecord->getPostUpdateDate() &&
                $objRecord->getAuthor() &&
                !$objRecord->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($objRecord->getPostDate() &&
                $objRecord->getPostUpdateDate() &&
                $objRecord->getAuthor() &&
                !$objRecord->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($objRecord->getPostDate() &&
                $objRecord->getPostUpdateDate() &&
                $objRecord->getAuthor() &&
                $objRecord->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = true;
                $this->txtUsersAsEditors->Display = true;
            }

            $this->calPostDate->Text = $objRecord->PostDate ? $objRecord->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->Text = $objRecord->PostUpdateDate ? $objRecord->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->txtAuthor->Text = $objRecord->Author;
            $this->txtUsersAsEditors->Text = implode(', ', $objRecord->getUserAsEditorsArray());
        }
    }

    /**
     * Resets the text values of specific UI elements related to user attributes
     * and hides their associated labels and input fields.
     *
     * @return void
     */
    protected function hideUserWindow()
    {
        $this->lstNewSportsAreas->Enabled = false;
        $this->lstNewSportsAreas->RemoveAllItems();
        $this->lstNewSportsAreas->addItem(t('- Select sport area -'), null, true);
        $this->lstNewSportsAreas->refresh();

        $this->lstCompetitionAreas->Enabled = false;
        $this->lstCompetitionAreas->RemoveAllItems();
        $this->lstCompetitionAreas->addItem(t('- Select competition area -'), null, true);
        $this->lstCompetitionAreas->refresh();

        $this->lstUnits->RemoveAllItems();
        $this->lstUnits->addItem(t('- Select Unit of measurement -'), null, true);
        $this->lstUnits->refresh();

        $this->txtResult->Text = '';
        $this->txtResult->Enabled = false;

        $this->txtDetailedResult->Text = '';
        $this->txtDetailedResult->Enabled = false;

        $this->txtDifference->Text = '';
        $this->txtDifference->Enabled = false;

        $this->txtCompetitionVenue->Text = '';
        $this->txtCompetitionVenue->Enabled = false;

        $this->dtxCompetitionDate->Text = '';
        $this->dtxCompetitionDate->Enabled = false;

        $this->dtxCompetitionDate->Enabled = false;
        $this->btnCompetitionDate->Enabled = false;

        $this->lstStatus->SelectedValue = 2;
        $this->lstStatus->refresh();

        $countRecords = Records::countAll();

        if ($countRecords === 0) {
            $this->lblWarning->Display = true;
            $this->lstSportsAreas->Enabled = false;
        } else {
            $this->lblInfo->Display = true;
            $this->lstSportsAreas->Enabled = true;
        }

        $this->lstSportsAreas->refresh();
        $this->btnAddNewRecord->Enabled = true;

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



    protected function chkRecordStatus_Change(ActionParams $params)
    {
//        //$this->eligibleForYouthCategory($birthDate, $competitionDate);
//
//        $objRecordArray = Records::loadAll();
//
//        if ($this->chkRecordStatus->Checked === true) {
//
//            foreach ($objRecordArray as $objRecord) {
//                if ($objRecord->getIsPeakRecord() === 1) {
//
//                    //Application::displayAlert($objRecord->Result);
//
//                    $this->txtDifference->Text = $this->calculateDifference($this->replaceCommaWithDot($this->txtResult->Text), $objRecord->Result);
//
//                    if ($this->isBetterResult($this->txtResult->Text, $objRecord->Result) === true) {
//                        $this->btnConfirm->Enabled = true;
//                    } else {
//                        $this->btnConfirm->Enabled = false;
//                    }
//                }
//            }
//
//
//
//
//        } else {
//
//        }
//
//
//        $this->txtDifference->refresh();
//        $this->tblNewRecord->refresh();
//        //$this->tblExistingRecords->refresh();
//        //Application::displayAlert(json_encode($objIsPeakRecord));
//
////        $this->intExistingRecord = $objRecords[0]->Id;
////        $objRecord = Records::load($this->intExistingRecord);
////
////        $this->txtDifference->Text = $this->calculateDifference($this->replaceCommaWithDot($this->txtResult->Text), $objRecord->Result);
////
////        if ($this->isBetterResult($this->txtResult->Text, $objRecord->Result) === true) {
////            $this->btnConfirm->Enabled = true;
////        } else {
////            $this->btnConfirm->Enabled = false;
////        }
//
//        $objAthlete = Athletes::load($this->lstAthletesNames->SelectedValue);
////        $calculatedAgeCroup = $this->calculateAgeCategory($objAthlete->BirthDate, $this->dtxCompetitionDate->DateTime);
////        $objRecords = Records::loadArrayByAgeCategoryId($calculatedAgeCroup);
////
////        Application::displayAlert(json_encode($objRecords), JSON_PRETTY_PRINT);
//
//

//        $objRecord = Records::load($this->intExistingRecord);
//
//        $this->txtDifference->Text = $this->calculateDifference($this->txtResult->Text, $objRecord->Result);
//
//        if ($this->isBetterResult($this->txtResult->Text, $objRecord->Result) === true) {
//            $this->btnConfirm->Enabled = true;
//        } else {
//            $this->btnConfirm->Enabled = false;
//        }

    }


    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function itemEscape_Click(ActionParams $params)
    {
        if ($this->intClick) {
            $objRecord = Records::load($this->intClick);
        }

        $this->txtResult->Text = $objRecord->getResult();
        $this->txtDifference->Text = $objRecord->getDifference();
        $this->txtDetailedResult->Text = $objRecord->getDetailedResult();
        $this->txtCompetitionVenue->Text = $objRecord->getCompetitionVenue();
        $this->dtxCompetitionDate->Text = $objRecord->getCompetitionDate() ? $objRecord->getCompetitionDate()->qFormat('DD.MM.YYYY') : null;

        $this->dlgToastr7->notify();
    }


    protected function btnSave_Click(ActionParams $params)
    {
//        if ($this->intClick) {
//            $objRecord = Records::load($this->intClick);
//        }
//
//        if (($this->blnEditMode === false) || ($objRecord->getIsLocked() === 1)) {
//            $this->checkInputs();
//        }
//
//        If ($this->errors) {
//            $this->lstStatus->SelectedValue = 2;
//            $objRecord->setStatus(2);
//        }
//
//        if ($this->blnEditMode === false) {
//            if (!count($this->errors)) {
//                if (!Records::namesExists(trim($this->txtFirstName->Text), trim($this->txtLastName->Text))) {
//                    $objRecord = new Records();
//                    $this->saveInputs($objRecord);
//                    $objRecord->setPostDate(Q\QDateTime::Now());
//                    $objRecord->setAssignedByUser($this->intLoggedUserId);
//                    $objRecord->setAuthor($objRecord->getAssignedByUserObject());
//                    $objRecord->save();
//
//                    Application::executeJavaScript("
//                            $('.setting-wrapper').addClass('hidden');
//                            $('.form-actions-wrapper').addClass('hidden');
//                        ");
//
//                    $this->hideUserWindow();
//                    $this->dtgRecords->removeCssClass('disabled');
//
//                    $this->dlgToastr1->notify();
//                } else if (!$this->dtxBirthDate->validateFormat()) {
//                    $this->dtxBirthDate->Text = '';
//                    $this->dlgModal5->focus();
//
//                    $this->dlgModal5->showDialogBox();
//                    return;
//                } else {
//                    $this->txtFirstName->Text = '';
//                    $this->txtLastName->Text = '';
//                    $this->txtFirstName->focus();
//
//                    $this->dlgToastr3->notify();
//                }
//            }
//        }
//
//        if ($this->blnEditMode === true) {
//            if ($objRecord->getIsLocked() === 2) {
//                if (!count($this->errors)) {
//                    if (!$this->dtxBirthDate->validateFormat()) {
//                        $this->dtxBirthDate->Text = $objRecord->getBirthDate() ? $objRecord->getBirthDate()->qFormat('DD.MM.YYYY') : null;
//
//                        $this->dlgModal5->showDialogBox();
//                        return;
//                    } else if (!Records::namesExists(trim($this->txtFirstName->Text), trim($this->txtLastName->Text))) {
//                        $this->saveInputs($objRecord);
//                        $objRecord->save();
//
//                        $this->updateAndValidateRecord($objRecord);
//                        $this->dlgToastr4->notify();
//                    } else {
//                        $this->activeInputs($objRecord);
//                        $this->lstStatus->SelectedValue = 1;
//
//                        $this->dlgModal4->showDialogBox();
//                    }
//                } else {
//                    $this->activeInputs($objRecord);
//
//                    $this->dlgModal4->showDialogBox();
//                }
//            }
//
//            if ($objRecord->getIsLocked() === 1) {
//                if (!Records::namesExists(trim($this->txtFirstName->Text), trim($this->txtLastName->Text))) {
//                    $this->saveInputs($objRecord);
//
//                    $this->dlgToastr2->notify();
//                } else if (!$this->dtxBirthDate->Text) {
//                    $objRecord->setBirthDate(null);
//                    $this->dtxBirthDate->Text = '';
//                } else if (!$this->dtxBirthDate->validateFormat()) {
//                    $this->dtxBirthDate->Text = $objRecord->getBirthDate() ? $objRecord->getBirthDate()->qFormat('DD.MM.YYYY') : null;
//
//                    $this->dlgModal5->showDialogBox();
//                    return;
//                } else {
//                    $objRecord->setBirthDate($this->dtxBirthDate->DateTime);
//                    $this->dlgToastr2->notify();
//                }
//
//                $this->saveInputs($objRecord);
//                $objRecord->save();
//                $this->updateAndValidateRecord($objRecord);
//            }
//        }
//
//        unset($this->errors);
    }

    protected function btnDelete_Click(ActionParams $params)
    {
        if ($this->intClick) {
            $objRecord = Records::load($this->intClick);
        }

        if ($objRecord->getIsLocked() === 2) {
            $this->dlgModal2->showDialogBox();
        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    public function deleteItem_Click(ActionParams $params)
    {
        if ($this->intClick) {
            $objRecord = Records::load($this->intClick);
        }

        $objRecord->delete();
        $this->dlgModal1->hideDialogBox();

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

//        $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
//        $this->txtFilter->Enabled = true;
//        $this->dtgRecords->Paginator->Enabled = true;
//        $this->dtgRecords->removeCssClass('disabled');
    }
}