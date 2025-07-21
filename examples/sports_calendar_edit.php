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

    protected $dlgModal1;
    protected $dlgModal2;
    protected $dlgModal3;

    protected $dlgModal4;
    protected $dlgModal5;
    protected $dlgModal6;
    protected $dlgModal7;

    protected $lblYear;
    protected $txtYear;

    protected $lblInstitutions;
    protected $lstInstitutions;

    protected $lblSportsAreas;
    protected $lstSportsAreas;

    protected $lblTitle;
    protected $txtTitle;

    protected $lblChanges;
    protected $lstChanges;

    protected $lblEventPlace;
    protected $txtEventPlace;

    protected $lblEventDate;
    protected $calBeginningEvent;
    protected $calEndEvent;
    protected $calStartTime;
    protected $calEndTime;

    protected $lblWebsiteUrl;
    protected $txtWebsiteUrl;
    protected $lstWebsiteTargetType;

    protected $lblFacebookUrl;
    protected $txtFacebookUrl;
    protected $lstFacebookTargetType;

    protected $lblInstagramUrl;
    protected $txtInstagramUrl;
    protected $lstInstagramTargetType;

    protected $lblContact;
    protected $txtOrganizers;
    protected $txtPhone;
    protected $txtEmail;

    protected $LockingFlag = false;

    protected $lblGroupTitle;
    protected $lstGroupTitle;

    protected $lblTitleSlug;
    protected $txtTitleSlug;

    protected $lblInstructionLink;
    protected $btnInstructionLink;
    protected $txtInstructionLink;
    protected $lstSportsContentTypes;
    protected $calShowDate;
    protected $txtLinkTitle;
    protected $btnDownloadCancel;
    protected $btnDownloadSave;

    protected $dtgSelectedList;
    protected $calSelectedDate;
    protected $txtSelectedTitle;
    protected $lstSelectedStatus;
    protected $btnSelectedSave;
    protected $btnSelectedCheck;
    protected $btnSelectedDelete;
    protected $btnSelectedCancel;

    protected $objFileFinder;

    protected $lblInformation;
    protected $txtInformation;

    protected $lblSchedule;
    protected $txtSchedule;

    protected $lblPostDate;
    protected $calPostDate;

    protected $lblPostUpdateDate;
    protected $calPostUpdateDate;

    protected $lblAuthor;
    protected $txtAuthor;

    protected $lblUsersAsEditors;
    protected $txtUsersAsEditors;

    protected $objMediaFinder;

    protected $lblPictureDescription;
    protected $txtPictureDescription;

    protected $lblAuthorSource;
    protected $txtAuthorSource;

    protected $lblStatus;
    protected $lstStatus;

    protected $btnSave;
    protected $btnSaving;
    protected $btnDelete;
    protected $btnCancel;

    protected $btnGoToSportsAreas;
    protected $btnGoToInstitutions;
    protected $btnGoToChanges;
    protected $btnGoToSettings;

    protected $txtYearId;

    protected $intId;
    protected $objMenu;
    protected $objSportsCalendar;
    protected $objSportsSettings;
    protected $objFrontendLinks;
    protected $objSportsAreas;
    protected $intGroup;
    protected $intDocument;

    protected $intLoggedUserId;

    protected $objChangesCondition;
    protected $objChangesClauses;

    protected $objSportsAreasCondition;
    protected $objSportsAreasClauses;

    protected $strWebsiteTargetTypeNullLabel;
    protected $strFacebookTargetTypeNullLabel;
    protected $strInstagramTargetTypeNullLabel;

    protected $errors = []; // Array for tracking errors

    protected function formCreate()
    {
        parent::formCreate();

        $this->intId = Application::instance()->context()->queryStringItem('id');
        if (!empty($this->intId)) {
            $this->objSportsCalendar = SportsCalendar::load($this->intId);
        } else {
            // does nothing
        }

        $this->intGroup = Application::instance()->context()->queryStringItem('group');
        $this->objSportsSettings = SportsSettings::loadByIdFromSportsSettings($this->intGroup);
        $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);
        $this->objSportsAreas = SportsTables::loadByIdFromSportsTables($this->intId);

        // Deleting sessions, if any.
        if (!empty($_SESSION['sports_changes']) || !empty($_SESSION['sports_group'])) {
            unset($_SESSION['sports_changes']);
            unset($_SESSION['sports_group']);
        } else if (!empty($_SESSION['sports_areas_id']) || !empty($_SESSION['sports_areas_group'])) {
            unset($_SESSION['sports_areas_id']);
            unset($_SESSION['sports_areas_group']);
        } else if (!empty($_SESSION['dtgInstitution_changes'])) {
            unset($_SESSION['dtgInstitution_changes']);
        } else if (!empty($_SESSION['dtgInstitution_group'])) {
            unset($_SESSION['dtgInstitution_group']);
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
        $this->intLoggedUserId = 1;

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->createTable();
        $this->popupViewer();
    }

    /**
     * Initializes a JavaScript event listener for elements with the class 'view-js'.
     * When such an element is clicked, checks for an input element with 'data-open' set to 'true'.
     * If found, opens a link specified in the 'data-view' attribute of the input element.
     * If the link is not available, displays an alert message.
     *
     * @return void
     */
    public function popupViewer()
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
     * Create input controls for various fields such as year, institutions, sports areas, event title, changes, event place,
     * and event date. Sets up the necessary properties, styles, and actions for each control.
     *
     * @return void
     */
    public function createInputs()
    {
        $this->lblYear = new Q\Plugin\Control\Label($this);
        $this->lblYear->Text = t('Year');
        $this->lblYear->addCssClass('col-md-3');
        $this->lblYear->setCssStyle('font-weight', 400);
        $this->lblYear->Required = true;

        $this->txtYear = new Q\Plugin\YearPicker($this);
        $this->txtYear->Text = $this->objSportsCalendar->Year ? $this->objSportsCalendar->Year : null;
        $this->txtYear->Language = 'et';
        $this->txtYear->TodayBtn = true;
        $this->txtYear->ClearBtn = true;
        $this->txtYear->AutoClose = true;
        $this->txtYear->setHtmlAttribute('autocomplete', 'off');
        $this->txtYear->addCssClass('calendar-trigger');
        $this->txtYear->addAction(new Q\Event\Change(), new Q\Action\Ajax('txtYear_Change'));
        $this->txtYear->addAction(new Q\Plugin\Event\Clear(), new Q\Action\Ajax('txtYear_Clear'));

        $this->lblInstitutions = new Q\Plugin\Control\Label($this);
        $this->lblInstitutions->Text = t('Organizing institution ');
        $this->lblInstitutions->addCssClass('col-md-3');
        $this->lblInstitutions->setCssStyle('font-weight', 400);

        $this->lstInstitutions = new Q\Plugin\Control\Select2($this);
        $this->lstInstitutions->MinimumResultsForSearch = -1;
        $this->lstInstitutions->ContainerWidth = 'resolve';
        $this->lstInstitutions->Theme = 'web-vauu';
        $this->lstInstitutions->Width = '90%';
        $this->lstInstitutions->setCssStyle('float', 'left');
        $this->lstInstitutions->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstInstitutions->addItem(t('- Choose organizing institution -'), null);
        $this->lstInstitutions->SelectedValue = $this->objSportsCalendar->OrganizingInstitutionId ? $this->objSportsCalendar->OrganizingInstitutionId : null;
        $this->lstInstitutions->addItems($this->lstInstitutions_GetItems());
        $this->lstInstitutions->addAction(new Q\Event\Change(), new Q\Action\Ajax('lstInstitutions_Change'));

        $this->lblSportsAreas = new Q\Plugin\Control\Label($this);
        $this->lblSportsAreas->Text = t('Sports areas');
        $this->lblSportsAreas->addCssClass('col-md-3');
        $this->lblSportsAreas->setCssStyle('font-weight', 400);
        $this->lblSportsAreas->Required = true;

        $this->lstSportsAreas = new Q\Plugin\Control\Select2($this);
        $this->lstSportsAreas->MinimumResultsForSearch = -1;
        $this->lstSportsAreas->ContainerWidth = 'resolve';
        $this->lstSportsAreas->Theme = 'web-vauu';
        $this->lstSportsAreas->addCssClass('js-sports-areas');
        $this->lstSportsAreas->Width = '90%';
        $this->lstSportsAreas->setCssStyle('float', 'left');
        $this->lstSportsAreas->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstSportsAreas->addItem(t('- Choose one sport -'), null);
        $this->lstSportsAreas->SelectedValue = $this->objSportsCalendar->SportsAreasId ? $this->objSportsCalendar->SportsAreasId : null;
        $this->lstSportsAreas->addItems($this->lstSportsAreas_GetItems());
        $this->lstSportsAreas->addAction(new Q\Event\Change(), new Q\Action\Ajax('lstSportsAreas_Change'));

        $this->lblTitle = new Q\Plugin\Control\Label($this);
        $this->lblTitle->Text = t('Event title');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 400);
        $this->lblTitle->Required = true;

        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Event title');
        $this->txtTitle->Text = $this->objSportsCalendar->Title ? $this->objSportsCalendar->Title : null;
        $this->txtTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblChanges = new Q\Plugin\Control\Label($this);
        $this->lblChanges->Text = t('Changes');
        $this->lblChanges->addCssClass('col-md-3');
        $this->lblChanges->setCssStyle('font-weight', 400);

        $this->lstChanges = new Q\Plugin\Select2($this);
        $this->lstChanges->MinimumResultsForSearch = -1;
        $this->lstChanges->Theme = 'web-vauu';
        $this->lstChanges->Width = '90%';
        $this->lstChanges->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;

        $this->lstChanges->setCssStyle('float', 'left');
        $this->lstChanges->addItem(t('- Choose one change -'), null, true);
        $this->lstChanges->addItems($this->lstChanges_GetItems());
        $this->lstChanges->SelectedValue = $this->objSportsCalendar->EventsChangesId;

        if (SportsChanges::countAll() == 0 || SportsChanges::countByStatus(1) == 0) {
            $this->lstChanges->Enabled = false;
        } else {
            $this->lstChanges->Enabled = true;
        }

        $this->lstChanges->AddAction(new Q\Event\Change(), new Q\Action\Ajax('lstChanges_Change'));

        $this->lblEventPlace = new Q\Plugin\Control\Label($this);
        $this->lblEventPlace->Text = t('Event place');
        $this->lblEventPlace->addCssClass('col-md-3');
        $this->lblEventPlace->setCssStyle('font-weight', 400);
        $this->lblEventPlace->Required = true;

        $this->txtEventPlace = new Bs\TextBox($this);
        $this->txtEventPlace->Placeholder = t('Event place');
        $this->txtEventPlace->Text = $this->objSportsCalendar->EventPlace ? $this->objSportsCalendar->EventPlace : null;
        $this->txtEventPlace->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtEventPlace->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtEventPlace->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtEventPlace->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblEventDate = new Q\Plugin\Control\Label($this);
        $this->lblEventDate->Text = t('Event date');
        $this->lblEventDate->addCssClass('col-md-3');
        $this->lblEventDate->setCssStyle('font-weight', 400);
        $this->lblEventDate->Required = true;

        $this->calBeginningEvent = new Q\Plugin\DateTimePicker($this);
        $this->calBeginningEvent->Language = 'et';
        $this->calBeginningEvent->TodayHighlight = true;
        $this->calBeginningEvent->ClearBtn = true;
        $this->calBeginningEvent->AutoClose = true;
        $this->calBeginningEvent->StartView = 2;
        $this->calBeginningEvent->MinView = 2;
        $this->calBeginningEvent->ForceParse = false;
        $this->calBeginningEvent->Format = 'dd.mm.yyyy';
        $this->calBeginningEvent->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calBeginningEvent->Text = $this->objSportsCalendar->BeginningEvent ?
            $this->objSportsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') : null;
        $this->calBeginningEvent->addCssClass('calendar-trigger');
        $this->calBeginningEvent->Placeholder = t('Start');
        $this->calBeginningEvent->addAction(new Change(), new Ajax('setDate_BeginningEvent'));
        $this->calBeginningEvent->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/locales/bootstrap-datetimepicker.et.js");

        $this->calEndEvent = new Q\Plugin\DateTimePicker($this);
        $this->calEndEvent->Language = 'et';
        $this->calEndEvent->TodayHighlight = true;
        $this->calEndEvent->ClearBtn = true;
        $this->calEndEvent->AutoClose = true;
        $this->calEndEvent->StartView = 2;
        $this->calEndEvent->MinView = 2;
        $this->calEndEvent->ForceParse = false;
        $this->calEndEvent->Format = 'dd.mm.yyyy';
        $this->calEndEvent->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calEndEvent->Text = $this->objSportsCalendar->EndEvent ?
            $this->objSportsCalendar->EndEvent->qFormat('DD.MM.YYYY') : null;
        $this->calEndEvent->addCssClass('calendar-trigger');
        $this->calEndEvent->Placeholder = t('End');
        $this->calEndEvent->addAction(new Change(), new Ajax('setDate_EndEvent'));
        $this->calEndEvent->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/locales/bootstrap-datetimepicker.et.js");

        $this->calStartTime = new Q\Plugin\ClockPicker($this);
        $this->calStartTime->AutoClose = true;
        $this->calStartTime->Text = $this->objSportsCalendar->StartTime ?
            $this->objSportsCalendar->StartTime->qFormat('hhhh:mm') : null;
        $this->calStartTime->addCssClass('clock-trigger');
        $this->calStartTime->Placeholder = t('Start');
        $this->calStartTime->addAction(new Change(), new Ajax('setTime_StartTime'));

        $this->calEndTime = new Q\Plugin\ClockPicker($this);
        $this->calEndTime->AutoClose = true;
        $this->calEndTime->Text = $this->objSportsCalendar->EndTime ?
            $this->objSportsCalendar->EndTime->qFormat('hhhh:mm') : null;
        $this->calEndTime->addCssClass('clock-trigger');
        $this->calEndTime->Placeholder = t('End');
        $this->calEndTime->addAction(new Change(), new Ajax('setTime_EndTime'));

        $this->lblWebsiteUrl = new Q\Plugin\Control\Label($this);
        $this->lblWebsiteUrl->Text = t('Website');
        $this->lblWebsiteUrl->addCssClass('col-md-3');
        $this->lblWebsiteUrl->setCssStyle('font-weight', 400);

        $this->txtWebsiteUrl = new Bs\TextBox($this);
        $this->txtWebsiteUrl->Placeholder = t('Website address');
        $this->txtWebsiteUrl->Text = $this->objSportsCalendar->WebsiteUrl ? $this->objSportsCalendar->WebsiteUrl : null;
        $this->txtWebsiteUrl->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtWebsiteUrl->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtWebsiteUrl->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtWebsiteUrl->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtWebsiteUrl->AddAction(new Q\Event\KeyUp(), new Q\Action\Ajax('lstWebsiteTargetType_KeyUp'));

        $this->lstWebsiteTargetType = new Q\Plugin\Select2($this);
        $this->lstWebsiteTargetType->MinimumResultsForSearch = -1;
        $this->lstWebsiteTargetType->Theme = 'web-vauu';
        $this->lstWebsiteTargetType->Width = '100%';
        $this->lstWebsiteTargetType->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;

        if (!$this->strWebsiteTargetTypeNullLabel) {
            if (!$this->lstWebsiteTargetType->Required) {
                $this->strWebsiteTargetTypeNullLabel = t('- Select target type -');
            }
        }

        $this->lstWebsiteTargetType->addItem($this->strWebsiteTargetTypeNullLabel, null);
        $this->lstWebsiteTargetType->addItems($this->lstWebsiteTargetType_GetItems());
        $this->lstWebsiteTargetType->SelectedValue = $this->objSportsCalendar->WebsiteTargetTypeId;

        if (!$this->objSportsCalendar->WebsiteUrl) {
            $this->lstWebsiteTargetType->Enabled = false;
        } else {
            $this->lstWebsiteTargetType->Enabled = true;
        }

        $this->lstWebsiteTargetType->addAction(new Change(), new Ajax('lstWebsiteTarget_Change'));

        $this->lblFacebookUrl = new Q\Plugin\Control\Label($this);
        $this->lblFacebookUrl->Text = t('Facebook');
        $this->lblFacebookUrl->addCssClass('col-md-3');
        $this->lblFacebookUrl->setCssStyle('font-weight', 400);

        $this->txtFacebookUrl = new Bs\TextBox($this);
        $this->txtFacebookUrl->Placeholder = t('Facebook address');
        $this->txtFacebookUrl->Text = $this->objSportsCalendar->FacebookUrl ? $this->objSportsCalendar->FacebookUrl : null;
        $this->txtFacebookUrl->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtFacebookUrl->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtFacebookUrl->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtFacebookUrl->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtFacebookUrl->AddAction(new Q\Event\KeyUp(), new Q\Action\Ajax('lstFacebookTargetType_KeyUp'));

        $this->lstFacebookTargetType = new Q\Plugin\Select2($this);
        $this->lstFacebookTargetType->MinimumResultsForSearch = -1;
        $this->lstFacebookTargetType->Theme = 'web-vauu';
        $this->lstFacebookTargetType->Width = '100%';
        $this->lstFacebookTargetType->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;

        if (!$this->strFacebookTargetTypeNullLabel) {
            if (!$this->lstFacebookTargetType->Required) {
                $this->strFacebookTargetTypeNullLabel = t('- Select target type -');
            }
        }

        $this->lstFacebookTargetType->addItem($this->strFacebookTargetTypeNullLabel, null);
        $this->lstFacebookTargetType->addItems($this->lstFacebookTargetType_GetItems());
        $this->lstFacebookTargetType->SelectedValue = $this->objSportsCalendar->FacebookTargetTypeId;

        if (!$this->objSportsCalendar->FacebookUrl) {
            $this->lstFacebookTargetType->Enabled = false;
        } else {
            $this->lstFacebookTargetType->Enabled = true;
        }

        $this->lstFacebookTargetType->addAction(new Change(), new Ajax('lstFacebookTarget_Change'));

        $this->lblInstagramUrl = new Q\Plugin\Control\Label($this);
        $this->lblInstagramUrl->Text = t('Instagram');
        $this->lblInstagramUrl->addCssClass('col-md-3');
        $this->lblInstagramUrl->setCssStyle('font-weight', 400);

        $this->txtInstagramUrl = new Bs\TextBox($this);
        $this->txtInstagramUrl->Placeholder = t('Instagram address');
        $this->txtInstagramUrl->Text = $this->objSportsCalendar->InstagramUrl ? $this->objSportsCalendar->InstagramUrl : null;
        $this->txtInstagramUrl->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtInstagramUrl->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtInstagramUrl->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtInstagramUrl->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtInstagramUrl->AddAction(new Q\Event\KeyUp(), new Q\Action\Ajax('lstInstagramTargetType_KeyUp'));

        $this->lstInstagramTargetType = new Q\Plugin\Select2($this);
        $this->lstInstagramTargetType->MinimumResultsForSearch = -1;
        $this->lstInstagramTargetType->Theme = 'web-vauu';
        $this->lstInstagramTargetType->Width = '100%';
        $this->lstInstagramTargetType->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;

        if (!$this->strInstagramTargetTypeNullLabel) {
            if (!$this->lstInstagramTargetType->Required) {
                $this->strInstagramTargetTypeNullLabel = t('- Select target type -');
            }
        }

        $this->lstInstagramTargetType->addItem($this->strWebsiteTargetTypeNullLabel, null);
        $this->lstInstagramTargetType->addItems($this->lstInstagramTargetType_GetItems());
        $this->lstInstagramTargetType->SelectedValue = $this->objSportsCalendar->InstagramTargetTypeId;

        if (!$this->objSportsCalendar->InstagramUrl) {
            $this->lstInstagramTargetType->Enabled = false;
        } else {
            $this->lstInstagramTargetType->Enabled = true;
        }

        $this->lstInstagramTargetType->addAction(new Change(), new Ajax('lstInstagramTarget_Change'));
        $this->lblContact = new Q\Plugin\Control\Label($this);
        $this->lblContact->Text = t('Contact person information');
        $this->lblContact->addCssClass('col-md-3');
        $this->lblContact->setCssStyle('font-weight', 400);
        $this->lblContact->Required = true;

        $this->txtOrganizers = new Bs\TextBox($this);
        $this->txtOrganizers->Placeholder = t('Organizers');
        $this->txtOrganizers->Text = $this->objSportsCalendar->Organizers ? $this->objSportsCalendar->Organizers : null;
        $this->txtOrganizers->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtOrganizers->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtOrganizers->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtOrganizers->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtPhone = new Bs\TextBox($this);
        $this->txtPhone->Placeholder = t('+372 1234 5678');
        $this->txtPhone->Text = $this->objSportsCalendar->Phone ? $this->objSportsCalendar->Phone : null;
        $this->txtPhone->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtPhone->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtPhone->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtPhone->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtEmail = new Bs\TextBox($this);
        $this->txtEmail->Placeholder = t('Email');
        $this->txtEmail->Text = $this->objSportsCalendar->Email ? $this->objSportsCalendar->Email : null;
        $this->txtEmail->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtEmail->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtEmail->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtEmail->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
        $this->lblGroupTitle->Text = t('Sports calendar group');
        $this->lblGroupTitle->addCssClass('col-md-3');
        $this->lblGroupTitle->setCssStyle('font-weight', 400);

        $this->lstGroupTitle = new Q\Plugin\Control\Select2($this);
        $this->lstGroupTitle->MinimumResultsForSearch = -1;
        $this->lstGroupTitle->ContainerWidth = 'resolve';
        $this->lstGroupTitle->Theme = 'web-vauu';
        $this->lstGroupTitle->Width = '90%';
        $this->lstGroupTitle->setCssStyle('float', 'left');
        $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstGroupTitle->addAction(new Q\Event\Change(), new Q\Action\Ajax('lstGroupTitle_Change'));

        $countByIsReserved = SportsSettings::countByIsReserved(1);
        $objGroups = SportsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::SportsSettings()->Id)));

        foreach ($objGroups as $objTitle) {
            if ($objTitle->IsReserved === 1) {
                $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                $this->lstGroupTitle->SelectedValue = $this->objSportsCalendar->MenuContentGroupTitleId;
            }
        }

        if ($countByIsReserved === 1) {
            $this->lstGroupTitle->Enabled = false;
        } else {
            $this->lstGroupTitle->Enabled = true;
        }

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        if ($this->objSportsCalendar->getTitleSlug()) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
            $this->txtTitleSlug->setCssStyle('text-align', 'left;');
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objSportsCalendar->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
        } else {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        ////////////////////////////////////////////////////////////

        $this->lblInstructionLink = new Q\Plugin\Control\Label($this);
        $this->lblInstructionLink->Text = t('Document(s) links');
        $this->lblInstructionLink->addCssClass('col-md-3');
        $this->lblInstructionLink->setCssStyle('font-weight', 400);

        $this->btnInstructionLink = new Bs\Button($this);
        $this->btnInstructionLink->Text = t('Search file...');
        $this->btnInstructionLink->CssClass = 'btn btn-default';
        $this->btnInstructionLink->addWrapperCssClass('center-button');
        $this->btnInstructionLink->setCssStyle('float', 'left');
        $this->btnInstructionLink->CausesValidation = false;
        $this->btnInstructionLink->setDataAttribute('popup', 'popup');
        $this->btnInstructionLink->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnInstructionLink_Click'));

        $this->lstSportsContentTypes = new Q\Plugin\Select2($this);
        $this->lstSportsContentTypes->MinimumResultsForSearch = -1;
        $this->lstSportsContentTypes->Theme = 'web-vauu';
        $this->lstSportsContentTypes->Width = '100%';
        $this->lstSportsContentTypes->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstSportsContentTypes->addItem(t('- Choose one content type -'), null, true);
        $this->lstSportsContentTypes->Display = false;

        $objTypes = SportsContentTypes::loadAll(QQ::Clause(QQ::orderBy(QQN::SportsContentTypes()->Id)));

        foreach ($objTypes as $objType) {
            if ($objType->Status === 1) {
                $this->lstSportsContentTypes->addItem($objType->Name, $objType->Id);
            }
        }

        if (SportsContentTypes::countAll() == 0 || SportsContentTypes::countByStatus(1) == 0) {
            $this->lstSportsContentTypes->Enabled = false;
        } else {
            $this->lstSportsContentTypes->Enabled = true;
        }

        $this->calShowDate = new Q\Plugin\DateTimePicker($this);
        $this->calShowDate->Language = 'et';
        $this->calShowDate->ClearBtn = true;
        $this->calShowDate->AutoClose = true;
        $this->calShowDate->StartView = 2;
        $this->calShowDate->MinView = 2;
        $this->calShowDate->ForceParse = false;
        $this->calShowDate->Format = 'dd.mm.yyyy';
        $this->calShowDate->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calShowDate->addCssClass('calendar-trigger');
        $this->calShowDate->Placeholder = t('- Select date -');
        $this->calShowDate->Width = '100%';
        $this->calShowDate->Display = false;
        $this->calShowDate->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/locales/bootstrap-datetimepicker.et.js");

        $this->txtInstructionLink = new Q\Plugin\Control\Label($this);
        $this->txtInstructionLink->Text = t('No specific instructions or results available...');
        $this->txtInstructionLink->setCssStyle('color', '#999');
        $this->txtInstructionLink->setCssStyle('float', 'left');
        $this->txtInstructionLink->setCssStyle('margin-left', '15px');

        if (SportsTables::countBySportsCalendarGroupId($this->intId) === 0) {
            $this->txtInstructionLink->Display = true;
        } else {
            $this->txtInstructionLink->Display = false;
        }

        $this->txtLinkTitle = new Bs\TextBox($this);
        $this->txtLinkTitle->Placeholder = t('Link title');
        $this->txtLinkTitle->Width = '60%';
        $this->txtLinkTitle->setCssStyle('float', 'left');
        $this->txtLinkTitle->Display = false;

        $this->calSelectedDate = new Q\Plugin\DateTimePicker($this);
        $this->calSelectedDate->Language = 'et';
        $this->calSelectedDate->ClearBtn = true;
        $this->calSelectedDate->AutoClose = true;
        $this->calSelectedDate->StartView = 2;
        $this->calSelectedDate->MinView = 2;
        $this->calSelectedDate->ForceParse = false;
        $this->calSelectedDate->Format = 'dd.mm.yyyy';
        $this->calSelectedDate->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calSelectedDate->addCssClass('calendar-trigger');
        $this->calSelectedDate->Placeholder = t('- Select date -');
        $this->calSelectedDate->Width = '100%';
        $this->calSelectedDate->Display = false;
        $this->calSelectedDate->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/locales/bootstrap-datetimepicker.et.js");

        $this->txtSelectedTitle = new Bs\TextBox($this);
        $this->txtSelectedTitle->Display = false;
        $this->txtSelectedTitle->setDataAttribute('view', '');
        $this->txtSelectedTitle->setDataAttribute('open', 'false');

        $this->lstSelectedStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstSelectedStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstSelectedStatus->ButtonGroupClass = 'radio radio-orange form-horizontal radio-inline';
        $this->lstSelectedStatus->setCssStyle('float', 'left');
        $this->lstSelectedStatus->setCssStyle('margin-left', '-10px');
        $this->lstSelectedStatus->Display = false;

        ////////////////////////////////////////////////////////////

        $this->lblInformation = new Q\Plugin\Control\Label($this);
        $this->lblInformation->Text = t('Information');
        $this->lblInformation->setCssStyle('font-weight', 'bold');

        $this->txtInformation = new Q\Plugin\CKEditor($this);
        $this->txtInformation->Text = $this->objSportsCalendar->Information ? $this->objSportsCalendar->Information : null;
        $this->txtInformation->Configuration = 'ckConfig';
        $this->txtInformation->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtInformation->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtInformation->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtInformation->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblSchedule = new Q\Plugin\Control\Label($this);
        $this->lblSchedule->Text = t('Schedule');
        $this->lblSchedule->setCssStyle('font-weight', 'bold');

        $this->txtSchedule = new Q\Plugin\CKEditor($this);
        $this->txtSchedule->Text = $this->objSportsCalendar->Schedule ? $this->objSportsCalendar->Schedule : null;
        $this->txtSchedule->Configuration = 'ckConfig';
        $this->txtSchedule->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtSchedule->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtSchedule->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtSchedule->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objSportsCalendar->PostDate ? $this->objSportsCalendar->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostDate->setCssStyle('font-weight', 'normal');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objSportsCalendar->PostUpdateDate ? $this->objSportsCalendar->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->setCssStyle('font-weight', 'bold');

        $this->txtAuthor  = new Bs\Label($this);
        $this->txtAuthor->Text = $this->objSportsCalendar->Author;
        $this->txtAuthor->setCssStyle('font-weight', 'normal');

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objSportsCalendar->getUserAsEditorsArray());
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

        $this->refreshDisplay();

        $this->objMediaFinder = new Q\Plugin\MediaFinder($this);
        $this->objMediaFinder->TempUrl = APP_UPLOADS_TEMP_URL . "/_files/thumbnail";
        $this->objMediaFinder->PopupUrl = QCUBED_FILEMANAGER_URL . "/examples/finder.php";
        $this->objMediaFinder->EmptyImageAlt = t("Choose a picture");
        $this->objMediaFinder->SelectedImageAlt = t("Selected picture");

        $this->objMediaFinder->SelectedImageId = $this->objSportsCalendar->getPictureId() ? $this->objSportsCalendar->getPictureId() : null;

        if ($this->objMediaFinder->SelectedImageId !== null) {
            $objFiles = Files::loadById($this->objMediaFinder->SelectedImageId);
            $this->objMediaFinder->SelectedImagePath = $this->objMediaFinder->TempUrl . $objFiles->getPath();;
            $this->objMediaFinder->SelectedImageName = $objFiles->getName();
        }

        $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageSave(), new Q\Action\Ajax( 'imageSave_Push'));
        $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageDelete(), new Q\Action\Ajax('imageDelete_Push'));

        $this->lblPictureDescription = new Q\Plugin\Control\Label($this);
        $this->lblPictureDescription->Text = t('Picture description');
        $this->lblPictureDescription->setCssStyle('font-weight', 'bold');

        $this->txtPictureDescription = new Bs\TextBox($this);
        $this->txtPictureDescription->Text = $this->objSportsCalendar->PictureDescription;
        $this->txtPictureDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtPictureDescription->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtPictureDescription->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtPictureDescription->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtPictureDescription->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtPictureDescription->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblAuthorSource = new Q\Plugin\Control\Label($this);
        $this->lblAuthorSource->Text = t('Author/source');
        $this->lblAuthorSource->setCssStyle('font-weight', 'bold');

        $this->txtAuthorSource = new Bs\TextBox($this);
        $this->txtAuthorSource->Text = $this->objSportsCalendar->AuthorSource;
        $this->txtAuthorSource->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtAuthorSource->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtAuthorSource->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtAuthorSource->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtAuthorSource->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        if (!$this->objSportsCalendar->getPictureId()) {
            $this->lblPictureDescription->Display = false;
            $this->txtPictureDescription->Display = false;
            $this->lblAuthorSource->Display = false;
            $this->txtAuthorSource->Display = false;
        }

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->setCssStyle('font-weight', 'bold');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden'), 3 => t('Draft')]);
        $this->lstStatus->SelectedValue = $this->objSportsCalendar->Status;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->Enabled = true;
        $this->lstStatus->AddAction(new Q\Event\Change(), new Q\Action\Ajax('lstStatus_Change'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates various buttons for different functionalities within the sports calendar.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSave_Click'));

        $this->btnSaving = new Bs\Button($this);
        $this->btnSaving->Text = t('Save and close');
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSaveClose_Click'));

        $this->btnDelete = new Bs\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->addWrapperCssClass('center-button');
        $this->btnDelete->CausesValidation = false;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Back');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCancel_Click'));

        $this->btnGoToInstitutions = new Bs\Button($this);
        $this->btnGoToInstitutions->Tip = true;
        $this->btnGoToInstitutions->ToolTip = t('Go the organizing institutions manager');
        $this->btnGoToInstitutions->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToInstitutions->CssClass = 'btn btn-default';
        $this->btnGoToInstitutions->setCssStyle('float', 'right');
        $this->btnGoToInstitutions->addWrapperCssClass('center-button');
        $this->btnGoToInstitutions->CausesValidation = false;
        $this->btnGoToInstitutions->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToInstitutions_Click'));

        $this->btnGoToSportsAreas = new Bs\Button($this);
        $this->btnGoToSportsAreas->Tip = true;
        $this->btnGoToSportsAreas->ToolTip = t('Go the sports areas change manager');
        $this->btnGoToSportsAreas->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToSportsAreas->CssClass = 'btn btn-default';
        $this->btnGoToSportsAreas->setCssStyle('float', 'right');
        $this->btnGoToSportsAreas->addWrapperCssClass('center-button');
        $this->btnGoToSportsAreas->CausesValidation = false;
        $this->btnGoToSportsAreas->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToSportsAreas_Click'));

        $this->btnGoToChanges = new Bs\Button($this);
        $this->btnGoToChanges->Tip = true;
        $this->btnGoToChanges->ToolTip = t('Go the events change manager');
        $this->btnGoToChanges->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToChanges->CssClass = 'btn btn-default';
        $this->btnGoToChanges->setCssStyle('float', 'right');
        $this->btnGoToChanges->addWrapperCssClass('center-button');
        $this->btnGoToChanges->CausesValidation = false;
        $this->btnGoToChanges->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToChanges_Click'));

        $this->btnGoToSettings = new Bs\Button($this);
        $this->btnGoToSettings->Tip = true;
        $this->btnGoToSettings->ToolTip = t('Go to sports calendar settings manager');
        $this->btnGoToSettings->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToSettings->CssClass = 'btn btn-default';
        $this->btnGoToSettings->setCssStyle('float', 'right');
        $this->btnGoToSettings->addWrapperCssClass('center-button');
        $this->btnGoToSettings->CausesValidation = false;
        $this->btnGoToSettings->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToSettings_Click'));

        $this->btnDownloadSave = new Bs\Button($this);
        $this->btnDownloadSave->Text = t('Save');
        $this->btnDownloadSave->CssClass = 'btn btn-orange';
        $this->btnDownloadSave->addWrapperCssClass('center-button');
        $this->btnDownloadSave->setCssStyle('float', 'left');
        $this->btnDownloadSave->setCssStyle('margin-left', '10px');
        $this->btnDownloadSave->CausesValidation = false;
        $this->btnDownloadSave->Display = false;
        $this->btnDownloadSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDownloadSave_Click'));

        $this->btnDownloadCancel = new Bs\Button($this);
        $this->btnDownloadCancel->Text = t('Cancel');
        $this->btnDownloadCancel->CssClass = 'btn btn-default';
        $this->btnDownloadCancel->addWrapperCssClass('center-button');
        $this->btnDownloadCancel->setCssStyle('float', 'left');
        $this->btnDownloadCancel->setCssStyle('margin-left', '5px');
        $this->btnDownloadCancel->CausesValidation = false;
        $this->btnDownloadCancel->Display = false;
        $this->btnDownloadCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDownloadCancel_Click'));

        $this->btnSelectedSave = new Bs\Button($this);
        $this->btnSelectedSave->Text = t('Update');
        $this->btnSelectedSave->CssClass = 'btn btn-orange';
        $this->btnSelectedSave->addWrapperCssClass('center-button');
        $this->btnSelectedSave->setCssStyle('margin-left', '10px');
        $this->btnSelectedSave->Display = false;
        $this->btnSelectedSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSelectedSave_Click'));

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
        $this->btnSelectedDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSelectedDelete_Click'));

        $this->btnSelectedCancel = new Bs\Button($this);
        $this->btnSelectedCancel->Text = t('Cancel');
        $this->btnSelectedCancel->CssClass = 'btn btn-default';
        $this->btnSelectedCancel->addWrapperCssClass('center-button');
        $this->btnSelectedCancel->CausesValidation = false;
        $this->btnSelectedCancel->Display = false;
        $this->btnSelectedCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSelectedCancel_Click'));

        if (!empty( $_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
            $this->btnInstructionLink->Display = false;
            $this->txtInstructionLink->Display = false;
            $this->lstSportsContentTypes->Display = true;
            $this->calShowDate->Display = true;
            $this->txtLinkTitle->Display = true;
            $this->txtLinkTitle->Placeholder = $_SESSION["data_name"];
            $this->btnDownloadSave->Display = true;
            $this->btnDownloadCancel->Display = true;
        }
    }

    /**
     * Creates and initializes multiple Toastr notification dialogs with various
     * properties such as alert type, position, message, and progress bar settings.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
        $this->dlgToastr1->ProgressBar = true;
        $this->dlgToastr1->EscapeHtml = false;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, these fields must be filled!');
        $this->dlgToastr2->ProgressBar = true;
        $this->dlgToastr2->EscapeHtml = false;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Well done!</strong> The start date for this post has been saved or changed.');
        $this->dlgToastr3->ProgressBar = true;
        $this->dlgToastr3->EscapeHtml = false;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this start date does not exist.</p>Please enter the start date!');
        $this->dlgToastr4->ProgressBar = true;
        $this->dlgToastr4->EscapeHtml = false;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('<strong>Well done!</strong> The end date for this post has been saved or changed.');
        $this->dlgToastr5->ProgressBar = true;
        $this->dlgToastr5->EscapeHtml = false;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('<p style=\"margin-bottom: 2px;\">Start date must be smaller then end date!</p><strong>Try to do it right again!</strong>');
        $this->dlgToastr6->ProgressBar = true;
        $this->dlgToastr6->TimeOut = 10000;
        $this->dlgToastr6->EscapeHtml = false;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this start date does not exist.</p>Please enter at least the start date!');
        $this->dlgToastr7->ProgressBar = true;
        $this->dlgToastr7->EscapeHtml = false;

        $this->dlgToastr8 = new Q\Plugin\Toastr($this);
        $this->dlgToastr8->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr8->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr8->Message = t('<strong>Well done!</strong> The start time for this post has been saved or changed.');
        $this->dlgToastr8->ProgressBar = true;
        $this->dlgToastr8->EscapeHtml = false;

        $this->dlgToastr9 = new Q\Plugin\Toastr($this);
        $this->dlgToastr9->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr9->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr9->Message = t('<strong>Well done!</strong> The end time for this post has been saved or changed.');
        $this->dlgToastr9->ProgressBar = true;
        $this->dlgToastr9->EscapeHtml = false;

        $this->dlgToastr10 = new Q\Plugin\Toastr($this);
        $this->dlgToastr10->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr10->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr10->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this end date does not exist.</p>Please enter the end date!');
        $this->dlgToastr10->ProgressBar = true;
        $this->dlgToastr10->EscapeHtml = false;

        $this->dlgToastr11 = new Q\Plugin\Toastr($this);
        $this->dlgToastr11->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr11->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr11->Message = t('<strong>Sorry</strong>, failed to save or edit post!');
        $this->dlgToastr11->ProgressBar = true;

        $this->dlgToastr12 = new Q\Plugin\Toastr($this);
        $this->dlgToastr12->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr12->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr12->Message = t('<strong>Well done!</strong> Updates to some records for this post were discarded, and the record has been restored!');
        $this->dlgToastr12->ProgressBar = true;

        $this->dlgToastr13 = new Q\Plugin\Toastr($this);
        $this->dlgToastr13->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr13->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr13->Message = t('<strong>Sorry</strong>, this field is required!');
        $this->dlgToastr13->ProgressBar = true;
        $this->dlgToastr13->EscapeHtml = false;
    }

    /**
     * Creates and initializes modal dialogs for different actions.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this sports event?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move this event from this sports event group to another event group?</p>
                                ');
        $this->dlgModal2->Title = t('Warning');
        $this->dlgModal2->HeaderClasses = 'btn-danger';
        $this->dlgModal2->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal2->addCloseButton(t("I'll cancel"));
        $this->dlgModal2->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('moveItem_Click'));

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this document?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal3->Title = t('Warning');
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('deleteDocument_Click'));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this event or edit it again?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this event public again later!</p>');
        $this->dlgModal4->Title = t('Question');
        $this->dlgModal4->HeaderClasses = 'btn-danger';
        $this->dlgModal4->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal4->addCloseButton(t("I'll cancel"));
        $this->dlgModal4->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('statusItem_Click'));
        $this->dlgModal4->addAction(new Bs\Event\ModalHidden(), new Q\Action\Ajax('hideItem_Click'));

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event is now hidden!</p>');
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Title = t("Success");
        $this->dlgModal6->HeaderClasses = 'btn-success';
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event has now been made public!</p>');
        $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Title = t("Success");
        $this->dlgModal7->HeaderClasses = 'btn-success';
        $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event is now a draft!</p>');
        $this->dlgModal7->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and configures a new VauuTable for displaying selected list data.
     *
     * @return void
     */
    protected function createTable()
    {
        $this->dtgSelectedList = new Q\Plugin\Control\VauuTable($this);
        $this->dtgSelectedList->CssClass = "table vauu-table table-hover";
        $this->dtgSelectedList->UseAjax = true;
        $this->dtgSelectedList->ShowHeader = false;
        $this->dtgSelectedList->setDataBinder('dtgSelectedList_Bind');
        $this->dtgSelectedList->RowParamsCallback = [$this, 'dtgSelectedList_GetRowParams'];
        $this->dtgSelectedList->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')),
            new Q\Action\Ajax('dtgSelectedList_Click'));

        $col = $this->dtgSelectedList->createNodeColumn(t("Content type"), QQN::SportsTables()->SportsContentTypes);
        $col->CellStyler->Width = '10%';

        $col = $this->dtgSelectedList->createNodeColumn(t("Date"), QQN::SportsTables()->ShowDate);
        $col->Format = 'DD.MM.YYYY';
        $col->CellStyler->Width = '10%';

        $col = $this->dtgSelectedList->createNodeColumn(t('Document link'), QQN::SportsTables()->Title);

        $col = $this->dtgSelectedList->createNodeColumn(t("Status"), QQN::SportsTables()->StatusObject);
        $col->HtmlEntities = false;

        $col = $this->dtgSelectedList->createNodeColumn(t("Post date"), QQN::SportsTables()->PostDate);
        $col->Format = 'DD.MM.YYYY';

        $col = $this->dtgSelectedList->createNodeColumn(t("Post update date"), QQN::SportsTables()->PostUpdateDate);
        $col->Format = 'DD.MM.YYYY';

        if (!empty( $_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
            $this->dtgSelectedList->addCssClass('disabled');
        }
    }

    /**
     * Binds the data source for the selected list DataGrid.
     *
     * This method sets the data source for the dtgSelectedList DataGrid using
     * SportsAreas records filtered by the current sports calendar group ID.
     *
     * @return void
     */
    public function dtgSelectedList_Bind()
    {
        $this->dtgSelectedList->DataSource = SportsTables::loadArrayBySportsCalendarGroupId($this->intId, QQ::Clause(QQ::orderBy(QQN::SportsTables()->Id)));
    }

    /**
     * Retrieves row parameters for a selected item list.
     *
     * @param object $objRowObject The object representing the current row.
     * @param int $intRowIndex The index of the row.
     * @return array An associative array of row parameters.
     */
    public function dtgSelectedList_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Handles the click event for the dtgSelectedList component.
     *
     * This method loads information about a selected sports area based on the
     * provided action parameter, updates the UI elements visibility and interactivity,
     * and sets the relevant data for editing.
     *
     * @param ActionParams $params Parameters containing the action details,
     *                             including the identifier for the selected sports area.
     * @return void
     */
    public function dtgSelectedList_Click(ActionParams $params)
    {
        $this->intDocument = intval($params->ActionParameter);
        $objSportsAreas = SportsTables::loadById($this->intDocument);
        $objFile = Files::loadById($objSportsAreas->getFilesId());

        //this->dtgSelectedList->addCssClass('disabled');
        $this->btnInstructionLink->Enabled = false;

        $this->calSelectedDate->Display = true;
        $this->txtSelectedTitle->Display = true;
        $this->lstSelectedStatus->Display = true;
        $this->btnSelectedSave->Display = true;
        $this->btnSelectedCheck->Display = true;
        $this->btnSelectedDelete->Display = true;
        $this->btnSelectedCancel->Display = true;

        $this->calSelectedDate->Text = $objSportsAreas->getShowDate() ? $objSportsAreas->getShowDate()->qFormat('DD.MM.YYYY') : '';
        $this->txtSelectedTitle->Text = $objSportsAreas->getTitle();
        $this->lstSelectedStatus->SelectedValue = $objSportsAreas->getStatus();
        $this->txtSelectedTitle->setDataAttribute('open', 'true');
        $this->txtSelectedTitle->setDataAttribute('view', APP_UPLOADS_URL . $objFile->getPath());
        $this->txtSelectedTitle->focus();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Retrieves a list of items representing organizing institutions for the sports calendar.
     *
     * This method generates a list of ListItem objects based on the organizing institutions
     * that meet the specified condition. The items in the list are marked as selected if they
     * match the organizing institution of the current sports calendar. Additionally, institutions
     * with a status of 2 are marked as disabled.
     *
     * @return ListItem[] An array of ListItem objects representing the organizing institutions.
     */
    public function lstInstitutions_GetItems() {
        $a = array();
        $objCondition = $this->objSportsAreasCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objSportsAreasCursor = OrganizingInstitution::queryCursor($objCondition, $this->objSportsAreasClauses);

        // Iterate through the Cursor
        while ($objSportsAreas = OrganizingInstitution::instantiateCursor($objSportsAreasCursor)) {
            $objListItem = new ListItem($objSportsAreas->__toString(), $objSportsAreas->Id);
            if (($this->objSportsCalendar->OrganizingInstitution) && ($this->objSportsCalendar->OrganizingInstitution->Id == $objSportsAreas->Id))
                $objListItem->Selected = true;

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objSportsAreas->Status == 2) {
                $objListItem->Disabled = true;
            }

            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Retrieves a list of sports areas and formats them as list items.
     * Disabled items are hidden based on specific conditions.
     *
     * @return ListItem[] An array of list items representing sports areas.
     */
    public function lstSportsAreas_GetItems() {
        $a = array();
        $objCondition = $this->objSportsAreasCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objSportsAreasCursor = SportsAreas::queryCursor($objCondition, $this->objSportsAreasClauses);

        // Iterate through the Cursor
        while ($objSportsAreas = SportsAreas::instantiateCursor($objSportsAreasCursor)) {
            $objListItem = new ListItem($objSportsAreas->__toString(), $objSportsAreas->Id);
            if (($this->objSportsCalendar->SportsAreas) && ($this->objSportsCalendar->SportsAreas->Id == $objSportsAreas->Id))
                $objListItem->Selected = true;

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objSportsAreas->IsEnabled == 2) {
                $objListItem->Disabled = true;
            }

            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Retrieves a list of items representing changes for a sports calendar.
     *
     * This method queries the changes based on specific conditions and creates a list
     * of items that can be used to show changes in the UI. Items with a status of 2
     * are marked as disabled.
     *
     * @return array The array of ListItem objects representing the changes.
     */
    public function lstChanges_GetItems() {
        $a = array();
        $objCondition = $this->objChangesCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objChangesCursor = SportsChanges::queryCursor($objCondition, $this->objChangesClauses);

        // Iterate through the Cursor
        while ($objChanges = SportsChanges::instantiateCursor($objChangesCursor)) {
            $objListItem = new ListItem($objChanges->__toString(), $objChanges->Id);
            if (($this->objSportsCalendar->EventsChanges) && ($this->objSportsCalendar->EventsChanges->Id == $objChanges->Id))
                $objListItem->Selected = true;

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objChanges->Status == 2) {
                $objListItem->Disabled = true;
            }
            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Retrieves a list of website target types.
     *
     * @return array An array of target type names.
     */
    public function lstWebsiteTargetType_GetItems() {
        return TargetType::nameArray();
    }

    /**
     * Retrieves the list of Facebook target types.
     *
     * @return array An array of target type names.
     */
    public function lstFacebookTargetType_GetItems() {
        return TargetType::nameArray();
    }

    /**
     * Retrieves the list of Instagram type items.
     *
     * @return array An array of Instagram type names.
     */
    public function lstInstagramTargetType_GetItems() {
        return TargetType::nameArray();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Sets the beginning date for a sports calendar event.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    public function setDate_BeginningEvent(ActionParams $params)
    {
        if ($this->calBeginningEvent->Text) {
            $this->objSportsCalendar->setBeginningEvent($this->calBeginningEvent->DateTime);

            $this->dlgToastr3->notify(); // StartDate OK
        } else {
            $this->calBeginningEvent->Text = null;
            $this->lstStatus->SelectedValue = 2;

            $this->objSportsCalendar->setBeginningEvent(null);
            $this->objSportsCalendar->setStatus(2);

            $this->dlgToastr4->notify(); // Mandatory StartDate
        }

        $this->objSportsCalendar->save();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Sets the end date for a sports calendar event.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    public function setDate_EndEvent(ActionParams $params)
    {
        if ($this->calBeginningEvent->Text && $this->calEndEvent->Text) {
            if (new DateTime($this->calBeginningEvent->Text) > new DateTime($this->calEndEvent->Text)) {
                $this->calEndEvent->Text = null;
                $this->objSportsCalendar->setEndEvent(null);

                $this->dlgToastr6->notify(); // StartDate smaller than EndDate, warning!
            } else if ($this->calEndEvent->Text) {
                $this->objSportsCalendar->setEndEvent($this->calEndEvent->DateTime);

                $this->dlgToastr5->notify(); // EndDate OK
            } else {
                $this->calEndEvent->Text = null;
                $this->objSportsCalendar->setEndEvent(null);

                $this->dlgToastr5->notify(); // EndDate OK
            }
        } else if (!$this->calBeginningEvent->Text && $this->calEndEvent->Text) {
            $this->calEndEvent->Text = null;
            $this->lstStatus->SelectedValue = 2;

            $this->objSportsCalendar->setEndEvent(null);
            $this->objSportsCalendar->setStatus(2);

            $this->dlgToastr7->notify(); // StartDate not, warning!
        } else {
            $this->calEndEvent->Text = null;
            $this->objSportsCalendar->setEndEvent(null);

            $this->dlgToastr5->notify(); // EndDate OK
        }

        $this->objSportsCalendar->save();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Sets the start time for a sports calendar event.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    public function setTime_StartTime(ActionParams $params)
    {
        if ($this->calBeginningEvent->Text && $this->calStartTime->Text) {
            $this->objSportsCalendar->setStartTime($this->calStartTime->DateTime);

            $this->dlgToastr8->notify(); // StartTime OK
        } else if (!$this->calBeginningEvent->Text && $this->calStartTime->Text) {

            $this->calStartTime->Text = null;
            $this->objSportsCalendar->setStartTime(null);

            $this->dlgToastr7->notify(); // StartDate not, warning!

        } else {
            $this->calStartTime->Text = null;
            $this->objSportsCalendar->setStartTime(null);

            $this->dlgToastr8->notify(); // StartTime OK
        }

        $this->objSportsCalendar->save();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Sets the end time for a sports calendar event.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    public function setTime_EndTime(ActionParams $params)
    {
        if ($this->calEndEvent->Text && $this->calEndTime->Text) {
            $this->objSportsCalendar->setEndTime($this->calEndTime->DateTime);

        } else if (!$this->calEndEvent->Text && $this->calEndTime->Text) {
            $this->calEndTime->Text = null;
            $this->objSportsCalendar->setEndTime(null);

            $this->dlgToastr10->notify(); // EndDate not, warning!

            $this->dlgToastr9->notify(); // EndTime OK
        } else {
            $this->calEndTime->Text = null;
            $this->objSportsCalendar->setEndTime(null);

            $this->dlgToastr9->notify(); // EndTime OK
        }

        $this->objSportsCalendar->save();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the change event for the group title list.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    protected function lstGroupTitle_Change(ActionParams $params)
    {
        if ($this->lstGroupTitle->SelectedValue !== $this->objSportsCalendar->getMenuContentGroupTitleId()) {
            $this->dlgModal2->showDialogBox();
        }
    }

    protected function txtYear_Clear(ActionParams $params)
    {
        if (!$this->txtYear->Text) {
            $this->txtYear->Text = null;
            $this->txtYear->setHtmlAttribute('required', 'required');
        }
    }

    protected function txtYear_Change(ActionParams $params)
    {
        if ($this->txtYear->Text) {
            $this->objSportsCalendar->setYear($this->txtYear->Text);
            $this->objSportsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        } else {
            $this->objSportsCalendar->setYear(2);
            $this->objSportsCalendar->setStatus(2);
            $this->objSportsCalendar->save();

            $this->lstStatus->SelectedValue = 2;
            $this->txtYear->Text = null;
            $this->txtYear->setHtmlAttribute('required', 'required');

            $this->dlgToastr13->notify();
        }
    }

    protected function lstInstitutions_Change()
    {
        if ($this->lstInstitutions->SelectedValue !== $this->objSportsCalendar->getOrganizingInstitutionId()) {
            $this->objSportsCalendar->setOrganizingInstitutionId($this->lstInstitutions->SelectedValue);
            $this->objSportsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }
    }

    /**
     * Handles the change event for the sports areas list.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function lstSportsAreas_Change(ActionParams $params)
    {

        if ($this->lstSportsAreas->SelectedValue) {
            $this->objSportsCalendar->setSportsAreasId($this->lstSportsAreas->SelectedValue);
            $this->objSportsCalendar->setSportArea($this->lstSportsAreas->SelectedName);
            $this->objSportsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();

            $this->lstSportsAreas->removeCssClass('has-error');

        } else {
            $this->objSportsCalendar->setSportsAreasId(null);
            $this->objSportsCalendar->setSportArea(null);
            $this->objSportsCalendar->setStatus(2);
            $this->objSportsCalendar->save();

            $this->lstStatus->SelectedValue = 2;
            $this->lstSportsAreas->addCssClass('has-error');

            $this->dlgToastr13->notify();
        }
    }

    /**
     * Handles the change event for the lstChanges control.
     *
     * @param ActionParams $params Parameters associated with the change event.
     * @return void
     */
    protected function lstChanges_Change(ActionParams $params)
    {
        if ($this->lstChanges->SelectedValue !== $this->objSportsCalendar->getEventsChangesId()) {
            $this->objSportsCalendar->setEventsChangesId($this->lstChanges->SelectedValue);
            $this->objSportsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }
    }

    /**
     * Handles changes to the website target selection and updates the associated data.
     *
     * @param ActionParams $params The parameters related to the triggered action.
     * @return void
     */
    protected function lstWebsiteTarget_Change(ActionParams $params)
    {
        if ($this->lstWebsiteTargetType->SelectedValue !== $this->objSportsCalendar->getWebsiteTargetTypeId()) {
            $this->objSportsCalendar->setWebsiteUrl($this->txtWebsiteUrl->Text);
            $this->objSportsCalendar->setWebsiteTargetTypeId($this->lstWebsiteTargetType->SelectedValue);
            $this->objSportsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }
    }

    /**
     * Handles the KeyUp event for the website target type list. Enables or disables the element
     * based on the text input's content. If the text input is empty, it resets related properties
     * and notifies the user.
     *
     * @param ActionParams $params Event parameters triggered by the KeyUp action.
     * @return void
     */
    public function lstWebsiteTargetType_KeyUp(ActionParams $params)
    {
        if ($this->txtWebsiteUrl->Text) {
            $this->lstWebsiteTargetType->Enabled = true;
        } else {
            $this->lstWebsiteTargetType->Enabled = false;
            $this->objSportsCalendar->setWebsiteUrl(null);
            $this->objSportsCalendar->setWebsiteTargetTypeId(null);
            $this->objSportsCalendar->save();

            $this->txtWebsiteUrl->Text = null;
            $this->lstWebsiteTargetType->SelectedValue = null;

            $this->dlgToastr1->notify();
        }
    }

    /**
     * Handles the change event for the Facebook target selection. Updates the Facebook target type
     * and URL in the associated sports calendar object and refreshes the display if a change is detected.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function lstFacebookTarget_Change(ActionParams $params)
    {
        if ($this->lstFacebookTargetType->SelectedValue !== $this->objSportsCalendar->getFacebookTargetTypeId()) {
            $this->objSportsCalendar->setFacebookUrl($this->txtFacebookUrl->Text);
            $this->objSportsCalendar->setFacebookTargetTypeId($this->lstFacebookTargetType->SelectedValue);
            $this->objSportsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }
    }

    /**
     * Handles the KeyUp event for the Facebook target type list.
     *
     * @param ActionParams $params Parameters received from the event.
     * @return void
     */
    public function lstFacebookTargetType_KeyUp(ActionParams $params)
    {
        if ($this->txtFacebookUrl->Text) {
            $this->lstFacebookTargetType->Enabled = true;
        } else {
            $this->lstFacebookTargetType->Enabled = false;
            $this->objSportsCalendar->setFacebookUrl(null);
            $this->objSportsCalendar->setFacebookTargetTypeId(null);
            $this->objSportsCalendar->save();

            $this->txtFacebookUrl->Text = null;
            $this->lstFacebookTargetType->SelectedValue = null;

            $this->dlgToastr1->notify();
        }
    }

    /**
     * Handles the event triggered when the Instagram target selection changes.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function lstInstagramTarget_Change(ActionParams $params)
    {
        if ($this->lstInstagramTargetType->SelectedValue !== $this->objSportsCalendar->getInstagramTargetTypeId()) {
            $this->objSportsCalendar->setInstagramUrl($this->txtInstagramUrl->Text);
            $this->objSportsCalendar->setInstagramTargetTypeId($this->lstInstagramTargetType->SelectedValue);
            $this->objSportsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }
    }

    /**
     * Handles the key-up event for the Instagram target type selection.
     *
     * @param ActionParams $params Parameters related to the key-up event.
     * @return void
     */
    public function lstInstagramTargetType_KeyUp(ActionParams $params)
    {
        if ($this->txtInstagramUrl->Text) {
            $this->lstInstagramTargetType->Enabled = true;
        } else {
            $this->lstInstagramTargetType->Enabled = false;
            $this->objSportsCalendar->setInstagramUrl(null);
            $this->objSportsCalendar->setInstagramTargetTypeId(null);
            $this->objSportsCalendar->save();

            $this->txtInstagramUrl->Text = null;
            $this->lstInstagramTargetType->SelectedValue = null;

            $this->dlgToastr1->notify();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for moving an item. This function updates the
     * sports calendar item, manages the locking state of event groups, updates
     * the frontend link, and redirects to the edit page.
     *
     * @param ActionParams $params The action parameters passed to the event handler.
     * @return void
     */
    protected function moveItem_Click(ActionParams $params)
    {
        $this->dlgModal2->hideDialogBox();

        $objGroupTitle = SportsSettings::loadById($this->lstGroupTitle->SelectedValue);

        // Before moving on to other activities, you must fix the initial data of the tables "sports_calendar" and "target_group_of_calendar"
        $objLockedGroup = $this->objSportsCalendar->getMenuContentGroupTitleId();
        $objTargetGroup = $objGroupTitle;

        $currentCount = SportsCalendar::countByMenuContentGroupTitleId($objLockedGroup);
        $nextCount = SportsCalendar::countByMenuContentGroupTitleId($objTargetGroup->getId());

        // Here you must first check the lock status of the following folder, to do this check...
        $objGroup = SportsSettings::loadById($objTargetGroup->getId());

        if ($nextCount == 0) {
            $objGroup->setEventsLocked(1);
            $objGroup->save();
        }

        // Next, we check the lock status of the previous folder, to do this, check...
        $objGroup = SportsSettings::loadById($objLockedGroup);

        if ($currentCount) {
            if ($currentCount == 1) {
                $objGroup->setEventsLocked(0);
            } else {
                $objGroup->setEventsLocked(1);
            }
            $objGroup->save();
        }

        $objBeforeEventsSlug = SportsCalendar::load($this->objSportsCalendar->getId());
        $beforeSlug = $objBeforeEventsSlug->getTitleSlug();

        $this->objSportsCalendar->setTitle($this->txtTitle->Text);
        $this->objSportsCalendar->setMenuContentGroupId($objGroupTitle->getMenuContentId());
        $this->objSportsCalendar->setMenuContentGroupTitleId($this->lstGroupTitle->SelectedValue);
        $this->objSportsCalendar->setMenuContentGroupName($this->lstGroupTitle->SelectedName);

        $this->objSportsCalendar->updateSportsEvent($this->txtYear->Text, $this->txtTitle->Text, $this->lstGroupTitle->SelectedName, $objGroupTitle->getTitleSlug());
        $this->objSportsCalendar->setPostUpdateDate(Q\QDateTime::Now());
        $this->objSportsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objSportsCalendar->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objSportsCalendar->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objSportsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->objFrontendLinks->setGroupedId($objGroupTitle->getMenuContentId());
        $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
        $this->objFrontendLinks->setFrontendTitleSlug($this->objSportsCalendar->getTitleSlug());
        $this->objFrontendLinks->save();


        // We are updating the slug
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
            $this->objSportsCalendar->getTitleSlug();
        $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
        $this->txtTitleSlug->HtmlEntities = false;
        $this->txtTitleSlug->setCssStyle('font-weight', 400);

        Application::redirect('sports_calendar_edit.php?id=' . $this->intId . '&group=' . $objGroupTitle->getMenuContentId());

        ///////////////////////////////////////////////////////////////////////////////////////////

        // Since we are using the transfer of a sports event from one group to another and we need to refresh the page using Application::redirect(),
        // we can't report on the success of the transfer of the sports event, so let these Toasts remain as they are...

        $objAfterEventSlug = SportsCalendar::load($this->objSportsCalendar->getId());
        $afterSlug = $objAfterEventSlug->getTitleSlug();

        if ($beforeSlug !== $afterSlug) {
            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr11->notify();
        }
    }

    /**
     * Deletes a sports calendar item if certain conditions are met.
     *
     * @param ActionParams $params Parameters for the action, including the action parameter.
     * @return void
     */
    protected function deleteItem_Click(ActionParams $params)
    {
        if ($params->ActionParameter == "pass") {
            $objFiles = Files::loadById($this->objSportsCalendar->getPictureId());

            if ($this->objSportsCalendar->getPictureId()) {
                if ($objFiles->getLockedFile() !== 0) {
                    $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                    $objFiles->save();
                }
            }

            $objSettings = SportsSettings::loadById($this->objSportsCalendar->getMenuContentGroupTitleId());
            $countLocked = SportsCalendar::countByMenuContentGroupTitleId($this->objSportsCalendar->getMenuContentGroupTitleId());

            if ($countLocked === 1) {
                $objSettings->setEventsLocked(0);
                $objSettings->save();
            }

            $this->objSportsCalendar->unassociateAllUsersAsEditors();
            $this->objSportsCalendar->delete();
            $this->objFrontendLinks->delete();
            $this->redirectToListPage();
        }
        $this->dlgModal1->hideDialogBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Saves an image to the sports calendar and updates related fields and display elements.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    protected function imageSave_Push(ActionParams $params)
    {
        $saveId = $this->objMediaFinder->Item;

        $this->objSportsCalendar->setPictureId($saveId);
        $this->objSportsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objSportsCalendar->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objSportsCalendar->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objSportsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->refreshDisplay();

        $this->lblPictureDescription->Display = true;
        $this->txtPictureDescription->Display = true;
        $this->lblAuthorSource->Display = true;
        $this->txtAuthorSource->Display = true;

        $this->dlgToastr1->notify();
    }

    /**
     * Handles the deletion of an image associated with a sports calendar event.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    protected function imageDelete_Push(ActionParams $params)
    {
        $objFiles = Files::loadById($this->objSportsCalendar->getPictureId());

        if ($objFiles->getLockedFile() !== 0) {
            $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
            $objFiles->save();
        }

        $this->objSportsCalendar->setPictureId(null);
        $this->objSportsCalendar->setPictureDescription(null);
        $this->objSportsCalendar->setAuthorSource(null);
        $this->objSportsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objSportsCalendar->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objSportsCalendar->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objSportsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->refreshDisplay();

        $this->lblPictureDescription->Display = false;
        $this->txtPictureDescription->Display = false;
        $this->lblAuthorSource->Display = false;
        $this->txtAuthorSource->Display = false;

        $this->txtPictureDescription->Text = null;
        $this->txtAuthorSource->Text = null;

        $this->dlgToastr1->notify();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the instruction link button.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    protected function btnInstructionLink_Click(ActionParams $params)
    {
        $_SESSION["sports"] = $this->intId;
        $_SESSION["group"] = $this->intGroup;

        Application::redirect('sports_finder.php');

        $this->btnInstructionLink->Enabled = false;
    }

    /**
     * Handles the click event for the download and save button.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    protected function btnDownloadSave_Click(ActionParams $params)
    {
        $errors = []; // Array for tracking errors

        // We check each field and add errors if necessary
        if (!$this->lstSportsContentTypes->SelectedValue) {
            $this->lstSportsContentTypes->addCssClass('has-error');
            $errors[] = 'lstSportsContentTypes';
        } else {
            $this->lstSportsContentTypes->removeCssClass('has-error');
        }

        if (!$this->calShowDate->Text) {
            $this->calShowDate->setHtmlAttribute('required', 'required');
            $errors[] = 'calShowDate';
        }

        if (!$this->txtLinkTitle->Text) {
            $this->txtLinkTitle->setHtmlAttribute('required', 'required');
            $errors[] = 'txtLinkTitle';
        }

        // Condition for which notification to show
        if (count($errors) === 1) {
            $this->dlgToastr13->notify(); // If only one field is invalid
        } elseif (count($errors) > 1) {
            $this->dlgToastr2->notify(); // If there is more than one invalid field

        } else {
            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {

                $objSportsTables = new SportsTables();
                $objSportsTables->setSportsCalendarGroupId($this->intId);
                $objSportsTables->setMenuContentGroupId($this->objSportsCalendar->getMenuContentGroupId());
                $objSportsTables->setYear($this->txtYear->Text);
                $objSportsTables->setTitle($this->txtLinkTitle->Text);
                $objSportsTables->setSportsContentTypesId($this->lstSportsContentTypes->SelectedValue);
                $objSportsTables->setSportsContentTypeName($this->lstSportsContentTypes->SelectedName);
                $objSportsTables->setShowDate($this->calShowDate->DateTime);
                $objSportsTables->setSportsAreasId($this->lstSportsAreas->SelectedValue);
                $objSportsTables->setSportsAreaName($this->lstSportsAreas->SelectedName);
                $objSportsTables->setFilesId($_SESSION["data_id"]);
                $objSportsTables->setStatus(1);
                $objSportsTables->setPostDate(Q\QDateTime::Now());
                $objSportsTables->save();

                $this->objSportsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objSportsCalendar->save();

                $this->txtUsersAsEditors->Text = implode(', ', $this->objSportsCalendar->getUserAsEditorsArray());
                $this->calPostUpdateDate->Text = $this->objSportsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
                $this->refreshDisplay();

                if (SportsTables::countBySportsCalendarGroupId($this->intId) === 0) {
                    $this->txtInstructionLink->Display = true;
                } else {
                    $this->txtInstructionLink->Display = false;
                }

                $this->btnInstructionLink->Display = true;
                $this->lstSportsContentTypes->Display = false;
                $this->calShowDate->Display = false;
                $this->txtLinkTitle->Display = false;
                $this->btnDownloadSave->Display = false;
                $this->btnDownloadCancel->Display = false;

                $this->lstSportsContentTypes->SelectedValue = null;
                $this->txtLinkTitle->Text = null;

                $this->dtgSelectedList->removeCssClass('disabled');
                $this->dtgSelectedList->refresh();

                $this->dlgToastr1->notify();

                unset($_SESSION["data_id"]);
                unset($_SESSION["data_name"]);
                unset($_SESSION["data_path"]);
            }
        }
    }

    /**
     * Handles the click event for the download cancel button.
     * Resets the UI elements and session data related to file download.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    protected function btnDownloadCancel_Click(ActionParams $params)
    {
        if (SportsTables::countBySportsCalendarGroupId($this->intId) === 0) {
            $this->txtInstructionLink->Display = true;
        } else {
            $this->txtInstructionLink->Display = false;
        }

        $this->btnInstructionLink->Display = true;
        $this->lstSportsContentTypes->Display = false;
        $this->calShowDate->Display = false;
        $this->txtLinkTitle->Display = false;
        $this->btnDownloadSave->Display = false;
        $this->btnDownloadCancel->Display = false;

        if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
            $objFiles = Files::loadById($_SESSION["data_id"]);

            if ($objFiles->getLockedFile() !== 0) {
                $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                $objFiles->save();
            }

            $this->dtgSelectedList->removeCssClass('disabled');
            $this->dtgSelectedList->refresh();

            unset($_SESSION["data_id"]);
            unset($_SESSION["data_name"]);
            unset($_SESSION["data_path"]);
        }
    }

    /**
     * Handles the 'Save' button click event for a selected item, updating the related sports areas and calendar,
     * then refreshing the display and UI elements accordingly.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    protected function btnSelectedSave_Click(ActionParams $params)
    {
        $objSportsTables = SportsTables::loadById($this->intDocument);

        $objSportsTables->setShowDate($this->calSelectedDate->DateTime);
        $objSportsTables->setTitle($this->txtSelectedTitle->Text);
        $objSportsTables->setStatus($this->lstSelectedStatus->SelectedValue);
        $objSportsTables->setPostUpdateDate(Q\QDateTime::Now());
        $objSportsTables->save();

        $this->txtSelectedTitle->setDataAttribute('view', '');
        $this->txtSelectedTitle->setDataAttribute('open', 'false');

        $this->objSportsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objSportsCalendar->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objSportsCalendar->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objSportsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->refreshDisplay();

        $this->dtgSelectedList->removeCssClass('disabled');
        $this->btnInstructionLink->Display = true;
        $this->txtInstructionLink->Display = true;
        $this->dtgSelectedList->refresh();

        $this->calSelectedDate->Display = false;
        $this->txtSelectedTitle->Display = false;
        $this->lstSelectedStatus->Display = false;
        $this->btnSelectedSave->Display = false;
        $this->btnSelectedCheck->Display = false;
        $this->btnSelectedDelete->Display = false;
        $this->btnSelectedCancel->Display = false;

        $this->txtSelectedTitle->Text = null;
        $this->dtgSelectedList->refresh();

        $this->dlgToastr1->notify();
    }

    /**
     * Handle the click event for the delete button, showing the modal dialog box
     * and setting data attributes for the title element.
     *
     * @param ActionParams $params Parameters for the action.
     * @return void
     */
    protected function btnSelectedDelete_Click(ActionParams $params)
    {
        $this->dlgModal3->showDialogBox();
        $this->txtSelectedTitle->setDataAttribute('view', '');
        $this->txtSelectedTitle->setDataAttribute('open', 'false');
    }

    /**
     * Handles the click event for the cancel button in the selected section.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function btnSelectedCancel_Click(ActionParams $params)
    {
        $this->dtgSelectedList->removeCssClass('disabled');
        $this->btnInstructionLink->Enabled = true;
        $this->dtgSelectedList->refresh();

        $this->calSelectedDate->Display = false;
        $this->txtSelectedTitle->Display = false;
        $this->lstSelectedStatus->Display = false;
        $this->btnSelectedSave->Display = false;
        $this->btnSelectedCheck->Display = false;
        $this->btnSelectedDelete->Display = false;
        $this->btnSelectedCancel->Display = false;

        $this->txtSelectedTitle->Text = null;
        $this->txtSelectedTitle->setDataAttribute('view', '');
        $this->txtSelectedTitle->setDataAttribute('open', 'false');
    }

    /**
     * Handles the click event to delete a document in the selected section.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function deleteDocument_Click(ActionParams $params)
    {
        if ($params->ActionParameter == "pass") {

            $objSportsTables = SportsTables::loadById($this->intDocument);
            $objFiles = Files::loadById($objSportsTables->getFilesId());

            if ($objFiles) {
                if ($objFiles->getLockedFile() !== 0) {
                    $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                    $objFiles->save();
                }
            }
            $objSportsTables->delete();
        }
        $this->dtgSelectedList->removeCssClass('disabled');
        $this->btnInstructionLink->Enabled = true;
        $this->dtgSelectedList->refresh();

        $this->calSelectedDate->Display = false;
        $this->txtSelectedTitle->Display = false;
        $this->lstSelectedStatus->Display = false;
        $this->btnSelectedSave->Display = false;
        $this->btnSelectedCheck->Display = false;
        $this->btnSelectedDelete->Display = false;
        $this->btnSelectedCancel->Display = false;

        if (SportsTables::countBySportsCalendarGroupId($this->intId) === 0) {
            $this->txtInstructionLink->Display = true;
        } else {
            $this->txtInstructionLink->Display = false;
        }

        $this->dlgModal3->hideDialogBox();
        $this->dlgToastr1->notify();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the save button. Validates user input fields, provides error
     * notifications, and triggers saving functionality based on input validation status.
     *
     * @param ActionParams $params Parameters passed through the button click action.
     * @return void
     */
    public function btnSave_Click(ActionParams $params)
    {
        $this->renderActionsWithId();
        $this->InputsCheck();

        if (count($this->errors)) {
            $this->lstStatus->SelectedValue =  $this->objSportsCalendar->getStatus();
        }

        // Condition for which notification to show
        if (count($this->errors) === 1) {
            $this->dlgToastr13->notify(); // If only one field is invalid
            $this->saveHelper(); // Partial saving allowed
        } elseif (count($this->errors) > 1) {
            $this->dlgToastr2->notify(); // If there is more than one invalid field
            $this->saveHelper(); // Partial saving allowed
        } else {
            $this->dlgToastr1->notify(); // Everything OK
            $this->saveHelper();
        }

        unset($this->errors);
    }

    /**
     * Handles the 'Save and Close' button click event. Validates form inputs, updates the status,
     * displays notifications for field errors, performs partial saves when applicable, and redirects if saving succeeds without errors.
     *
     * @param ActionParams $params The parameters passed with this action, typically containing event-specific data.
     * @return void This function does not return a value. Instead, it handles validation, updates, and redirects directly.
     */
    public function btnSaveClose_Click(ActionParams $params)
    {
        $this->renderActionsWithId();
        $this->InputsCheck();

        if (count($this->errors)) {
            $this->lstStatus->SelectedValue =  $this->objSportsCalendar->getStatus();
        }

        // Condition for which notification to show
        if (count($this->errors) === 1) {
            $this->dlgToastr13->notify(); // If only one field is invalid
            $this->saveHelper(); // Partial saving allowed
        } elseif (count($this->errors) > 1) {
            $this->dlgToastr2->notify(); // If there is more than one invalid field
            $this->saveHelper(); // Partial saving allowed
        } else {
            $this->saveHelper();
            $this->redirectToListPage();
        }

        unset($this->errors);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Saves the state of various sports calendar and frontend link settings.
     *
     * Loads the necessary template locking and frontend options.
     * Updates and saves the sports calendar with data from form inputs.
     * Also updates and saves the corresponding frontend links with data from the sports calendar and frontend options.
     * Updates the displayed title slug link based on the sports calendar title.
     *
     * @return void
     */
    protected function saveHelper()
    {
        $objTemplateLocking = FrontendTemplateLocking::load(8);
        $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->getId());

        $this->objSportsCalendar->setYear($this->txtYear->Text);

        if ($this->lstSportsAreas->SelectedValue) {
            $this->objSportsCalendar->setSportsAreasId($this->lstSportsAreas->SelectedValue);
            $this->objSportsCalendar->setSportArea($this->lstSportsAreas->SelectedName);
        }

        $this->objSportsCalendar->setTitle($this->txtTitle->Text);
        $this->objSportsCalendar->setEventsChangesId($this->lstChanges->SelectedValue);
        $this->objSportsCalendar->setEventPlace($this->txtEventPlace->Text);

        $this->objSportsCalendar->setWebsiteUrl($this->txtFacebookUrl->Text);
        $this->objSportsCalendar->setWebsiteTargetTypeId($this->lstWebsiteTargetType->SelectedValue);
        $this->objSportsCalendar->setFacebookUrl($this->txtFacebookUrl->Text);
        $this->objSportsCalendar->setFacebookTargetTypeId($this->lstFacebookTargetType->SelectedValue);

        $this->objSportsCalendar->setOrganizers($this->txtOrganizers->Text);
        $this->objSportsCalendar->setPhone($this->txtPhone->Text);
        $this->objSportsCalendar->setEmail($this->txtEmail->Text);

        $this->objSportsCalendar->updateSportsEvent($this->txtYear->Text, $this->txtTitle->Text, $this->lstGroupTitle->SelectedName, $this->objSportsSettings->getTitleSlug());
        $this->objSportsCalendar->setInformation($this->txtInformation->Text);
        $this->objSportsCalendar->setSchedule($this->txtSchedule->Text);

        $this->refreshDisplay();

        $this->objSportsCalendar->setPictureDescription($this->txtPictureDescription->Text);
        $this->objSportsCalendar->setAuthorSource($this->txtAuthorSource->Text);
        $this->objSportsCalendar->setStatus($this->lstStatus->SelectedValue);

        $this->objSportsCalendar->save();

        $this->txtSelectedTitle->setDataAttribute('view', '');
        $this->txtSelectedTitle->setDataAttribute('open', 'false');

        $this->objFrontendLinks->setLinkedId($this->intId);
        $this->objFrontendLinks->setGroupedId($this->intGroup);
        $this->objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
        $this->objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
        $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
        $this->objFrontendLinks->setContentTypesManagamentId(8);
        $this->objFrontendLinks->setFrontendTitleSlug($this->objSportsCalendar->getTitleSlug());
        $this->objFrontendLinks->setIsActivated(1);
        $this->objFrontendLinks->save();

        $this->txtAuthor->Text = $this->objSportsCalendar->getAuthor();

        if ($this->objSportsCalendar->getTitle()) {
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objSportsCalendar->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        } else {
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }
    }

    /**
     * Handles the 'Escape' button click event. This method resets various form inputs to
     * their original values based on the object's current data, and displays a notification
     * if specific conditions are met.
     *
     * @param ActionParams $params The parameters passed with this action, typically containing event-specific data.
     * @return void This function does not return a value. It updates form inputs and displays notifications as needed.
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        $objCancel = $this->objSportsCalendar->getId();

        // Check if $objCancel is available
        if ($objCancel) {
            $this->dlgToastr12->notify();
        }

        $this->txtYear->Text = $this->objSportsCalendar->getYear();
        $this->txtTitle->Text = $this->objSportsCalendar->getTitle();
        $this->txtEventPlace->Text = $this->objSportsCalendar->getEventPlace();
        $this->txtFacebookUrl->Text = $this->objSportsCalendar->getWebsiteUrl();
        $this->txtFacebookUrl->Text = $this->objSportsCalendar->getFacebookUrl();
        $this->txtInstagramUrl->Text = $this->objSportsCalendar->getInstagramUrl();
        $this->txtOrganizers->Text = $this->objSportsCalendar->getOrganizers();
        $this->txtPhone->Text = $this->objSportsCalendar->getPhone();
        $this->txtEmail->Text = $this->objSportsCalendar->getEmail();
        $this->txtInformation->Text = $this->objSportsCalendar->getInformation();
        $this->txtSchedule->Text = $this->objSportsCalendar->getSchedule();
        $this->txtPictureDescription->Text = $this->objSportsCalendar->getPictureDescription();
        $this->txtAuthorSource->Text = $this->objSportsCalendar->getAuthorSource();
    }

    /**
     * Handles the change event for the status selection list. Displays a modal dialog
     * if the current status of the sports calendar object is active, otherwise locks the input fields.
     *
     * @return void This function does not return a value. It performs UI updates based on the status.
     */
    protected  function lstStatus_Change()
    {
        if ($this->objSportsCalendar->getStatus() === 1) {
            $this->dlgModal4->showDialogBox();
        } else {
            $this->lockInputFields();
        }
    }

    /**
     * Handles the 'Status Item' click event. Updates the status of the sports calendar, hides the modal dialog
     * as necessary based on the selected status, saves the updated status, and refreshes the display.
     *
     * @param ActionParams $params The parameters passed with this action, typically containing event-specific data.
     * @return void This function does not return a value. It updates the status, saves changes, and refreshes the display directly.
     */
    protected function statusItem_Click(ActionParams $params)
    {
        if ($this->lstStatus->SelectedValue === 2) {
            $this->dlgModal4->hideDialogBox();
            $this->objSportsCalendar->setStatus(2);
        } else if ($this->lstStatus->SelectedValue === 3){
            $this->dlgModal4->hideDialogBox();
            $this->objSportsCalendar->setStatus(3);
        }

        $this->objSportsCalendar->save();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Validates input fields on the form, updates statuses, displays appropriate notifications,
     * and locks the input fields based on the validation results. Performs updates to the
     * associated sports calendar and refreshes the UI.
     *
     * @return void This function does not return a value. It processes the input fields, handles validation,
     * updates statuses, displays dialogs or notifications, performs necessary saves,
     * and refreshes the display.
     */
    protected function lockInputFields()
    {
        $this->InputsCheck();

        if (count($this->errors)) {
            $this->lstStatus->SelectedValue =  $this->objSportsCalendar->getStatus();
        }

        // Condition for which notification to show
        if (count($this->errors) === 1) {
            $this->dlgToastr13->notify(); // If only one field is invalid
        } elseif (count($this->errors) > 1) {
            $this->dlgToastr2->notify(); // If there is more than one invalid field
        } else if ($this->lstStatus->SelectedValue === 1) {
            $this->objSportsCalendar->setStatus(1);
            $this->dlgModal6->showDialogBox();
        } else if ($this->lstStatus->SelectedValue === 2) {
            $this->objSportsCalendar->setStatus(2);
            $this->dlgModal5->showDialogBox();
        } else if ($this->lstStatus->SelectedValue === 3) {
            $this->objSportsCalendar->setStatus(3);
            $this->dlgModal7->showDialogBox();
        }

        $this->objSportsCalendar->save();
        unset($this->errors);

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Handles the click event for hiding an item and sets the selected value of the status list.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function hideItem_Click(ActionParams $params)
    {
        $this->lstStatus->SelectedValue = $this->objSportsCalendar->getStatus();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Refreshes the display elements based on the state of the sports calendar.
     *
     * This method updates the visibility and CSS classes of various UI components
     * according to the status of the sports calendar's post date, post update date,
     * author, and users as editors.
     *
     * @return void
     */
    protected function refreshDisplay()
    {
        if ($this->objSportsCalendar->getPostDate() &&
            !$this->objSportsCalendar->getPostUpdateDate() &&
            $this->objSportsCalendar->getAuthor() &&
            !$this->objSportsCalendar->countUsersAsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
            $this->calPostDate->addCssClass('calendar-control-remove');
        }

        if ($this->objSportsCalendar->getPostDate() &&
            $this->objSportsCalendar->getPostUpdateDate() &&
            $this->objSportsCalendar->getAuthor() &&
            !$this->objSportsCalendar->countUsersAsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
            $this->calPostDate->removeCssClass('calendar-control-remove');
        }

        if ($this->objSportsCalendar->getPostDate() &&
            $this->objSportsCalendar->getPostUpdateDate() &&
            $this->objSportsCalendar->getAuthor() &&
            $this->objSportsCalendar->countUsersAsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = true;
            $this->txtUsersAsEditors->Display = true;
            $this->txtUsersAsEditors->addCssClass('form-control-add');
        }
    }

    /**
     * Renders actions for the sports calendar depending on whether an ID is present.
     *
     * If an ID is present, it checks if any of the form fields have been changed
     * and updates the sports calendar accordingly, including setting the post
     * update date and assigned editors.
     *
     * If no ID is present, it sets default values for a new sports calendar entry,
     * including setting the status, username, and post date.
     *
     * @return void
     */
    public function renderActionsWithId()
    {
        if (!empty($this->intId)) {
            if ($this->txtYear->Text !== $this->objSportsCalendar->getYear() ||
                $this->lstSportsAreas->SelectedValue !== $this->objSportsCalendar->getSportsAreasId() ||
                $this->txtTitle->Text !== $this->objSportsCalendar->getTitle() ||
                $this->lstChanges->SelectedValue !==$this->objSportsCalendar->getEventsChangesId() ||
                $this->txtEventPlace->Text !== $this->objSportsCalendar->getEventPlace() ||
                $this->calBeginningEvent->Text !== $this->objSportsCalendar->getBeginningEvent() ||
                $this->calEndEvent->Text !== $this->objSportsCalendar->getEndEvent() ||
                $this->calStartTime->Text !== $this->objSportsCalendar->getStartTime() ||
                $this->calEndTime->Text !== $this->objSportsCalendar->getEndTime() ||
                $this->txtOrganizers->Text !== $this->objSportsCalendar->getTitle() ||
                $this->txtTitle->Text !== $this->objSportsCalendar->getOrganizers() ||
                $this->txtPhone->Text !== $this->objSportsCalendar->getPhone() ||
                $this->txtEmail->Text !== $this->objSportsCalendar->getEmail() ||
                $this->txtWebsiteUrl->Text !== $this->objSportsCalendar->getWebsiteUrl() ||
                $this->lstWebsiteTargetType->SelectedValue !== $this->objSportsCalendar->getWebsiteTargetType() ||
                $this->txtFacebookUrl->Text !== $this->objSportsCalendar->getFacebookUrl() ||
                $this->lstFacebookTargetType->SelectedValue !== $this->objSportsCalendar->getFacebookTargetType() ||
                $this->txtInformation->Text !== $this->objSportsCalendar->getInformation() ||
                $this->txtSchedule->Text !== $this->objSportsCalendar->getSchedule() ||
                $this->txtPictureDescription->Text !== $this->objSportsCalendar->getPictureDescription() ||
                $this->txtAuthorSource->Text !== $this->objSportsCalendar->getAuthorSource() ||
                $this->lstStatus->SelectedValue !== $this->objSportsCalendar->getStatus()
            ) {
                $this->objSportsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objSportsCalendar->save();

                $this->txtUsersAsEditors->Text = implode(', ', $this->objSportsCalendar->getUserAsEditorsArray());
                $this->calPostUpdateDate->Text = $this->objSportsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            }
        }
    }

    /**
     * Validates individual form fields and updates the `errors` array with field identifiers for any invalid input.
     * Adds or removes CSS classes and HTML attributes to indicate validation status for each field.
     *
     * @return void This method does not return a value. It performs validation checks on form fields and updates the state accordingly.
     */
    protected function InputsCheck()
    {
       // We check each field and add errors if necessary
        if (!$this->lstSportsAreas->SelectedValue) {
            $this->lstSportsAreas->addCssClass('has-error');
            $this->errors[] = 'lstSportsAreas';
        } else {
            $this->lstSportsAreas->removeCssClass('has-error');
        }

        if (!$this->txtYear->Text) {
            $this->txtYear->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtYear';
        }

        if (!$this->txtTitle->Text) {
            $this->txtTitle->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtTitle';
        }

        if (!$this->txtEventPlace->Text) {
            $this->txtEventPlace->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtEventPlace';
        }

        if (!$this->calBeginningEvent->Text) {
            $this->calBeginningEvent->setHtmlAttribute('required', 'required');
            $this->errors[] = 'calBeginningEvent';
        }

        if (!$this->txtOrganizers->Text) {
            $this->txtOrganizers->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtOrganizers';
        }

        if (!$this->txtPhone->Text) {
            $this->txtPhone->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtPhone';
        }

        if (!$this->txtEmail->Text) {
            $this->txtEmail->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtEmail';
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the cancel button.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnCancel_Click(ActionParams $params)
    {
            $this->redirectToListPage();
    }

    /**
     * Handles the click event for the delete button.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnDelete_Click(ActionParams $params)
    {
        $this->dlgModal1->showDialogBox();
    }

    /**
     * Handles the click event for the "Go to Institutions" button.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnGoToInstitutions_Click(ActionParams $params)
    {
        $_SESSION['dtgInstitution_changes'] = $this->intId;
        $_SESSION['dtgInstitution_group'] = $this->intGroup;
        Application::redirect('sports_calendar_list.php#organizingInstitutions_tab');
    }

    /**
     * Handles the click event for the "Go to Sports Areas" button.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnGoToSportsAreas_Click(ActionParams $params)
    {
        $_SESSION['sports_areas_id'] = $this->intId;
        $_SESSION['sports_areas_group'] = $this->intGroup;
        Application::redirect('sports_calendar_list.php#sportsAreas_tab');
    }

    /**
     * Handles the click event for the "Go to Changes" button.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnGoToChanges_Click(ActionParams $params)
    {
        $_SESSION['sports_changes'] = $this->intId;
        $_SESSION['sports_group'] = $this->intGroup;
        Application::redirect('categories_manager.php#sportsChanges_tab');
    }

    /**
     * Handles the click event for the "Go to Settings" button.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnGoToSettings_Click(ActionParams $params)
    {
        $_SESSION['sports_id'] = $this->intId;
        $_SESSION['sports_group'] = $this->intGroup;

        Application::redirect('settings_manager.php#sportsSettings_tab');
    }

    /**
     * Redirects the user to the sports calendar list page and clears specific session data.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        Application::redirect('sports_calendar_list.php');
        unset($_SESSION['sports_id']);
        unset($_SESSION['sports_group']);
    }
}
SampleForm::run('SampleForm');