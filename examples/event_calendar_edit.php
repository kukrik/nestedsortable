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
use QCubed\Event\KeyUp;

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

    protected $lblYear;
    protected $txtYear;

    protected $lblTargetGroup;
    protected $lstTargetGroup;

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

    protected $lblGroupTitle;
    protected $lstGroupTitle;

    protected $lblTitleSlug;
    protected $txtTitleSlug;

    protected $lblInstructionLink;
    protected $btnInstructionLink;
    protected $txtInstructionLink;

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

    protected $btnGoToTargetGroup;
    protected $btnGoToChanges;
    protected $btnGoToSettings;

    protected $txtYearId;

    protected $intId;
    protected $objEventsSettings;
    protected $objEventsCalendar;
    protected $objFrontendLinks;
    protected $intGroup;

    protected $intLoggedUserId;
    protected $intTemporaryId;

    protected $objChangesCondition;
    protected $objChangesClauses;

    protected $objTargetGroupCondition;
    protected $objTargetGroupClauses;

    protected $strWebsiteTargetTypeNullLabel;
    protected $strFacebookTargetTypeNullLabel;
    protected $strInstagramTargetTypeNullLabel;

    protected $errors = []; // Array for tracking errors

    protected function formCreate()
    {
        parent::formCreate();

        // Deleting sessions, if any.
        if (!empty($_SESSION['events_id']) || !empty($_SESSION['events_group'])) {
            unset($_SESSION['events_id']);
            unset( $_SESSION['events_group']);
        } else if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
            unset($_SESSION['target_id']);
            unset($_SESSION['target_group']);
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        if (!empty($this->intId)) {
            $this->objEventsCalendar = EventsCalendar::load($this->intId);
        } else {
            // does nothing
        }

        $this->intGroup = Application::instance()->context()->queryStringItem('group');
        $this->objEventsSettings = EventsSettings::loadByIdFromEventsSettings($this->intGroup);
        $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and creates input fields and labels for the events calendar form.
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
        $this->txtYear->Text = $this->objEventsCalendar->Year ? $this->objEventsCalendar->Year : null;
        $this->txtYear->Language = 'et';
        $this->txtYear->TodayBtn = true;
        $this->txtYear->ClearBtn = true;
        $this->txtYear->AutoClose = true;
        $this->txtYear->setHtmlAttribute('autocomplete', 'off');
        $this->txtYear->addCssClass('calendar-trigger');
        $this->txtYear->addAction(new Q\Event\Change(), new Q\Action\Ajax('txtYear_Change'));
        $this->txtYear->addAction(new Q\Plugin\Event\Clear(), new Q\Action\Ajax('txtYear_Clear'));

        $this->lblTargetGroup = new Q\Plugin\Control\Label($this);
        $this->lblTargetGroup->Text = t('Target Group');
        $this->lblTargetGroup->addCssClass('col-md-3');
        $this->lblTargetGroup->setCssStyle('font-weight', 400);
        //$this->lblTargetGroup->Required = true;

        $this->lstTargetGroup = new Q\Plugin\Control\Select2($this);
        $this->lstTargetGroup->MinimumResultsForSearch = -1;
        $this->lstTargetGroup->ContainerWidth = 'resolve';
        $this->lstTargetGroup->Theme = 'web-vauu';
        $this->lstTargetGroup->addCssClass('js-target-group');
        $this->lstTargetGroup->Width = '90%';
        $this->lstTargetGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstTargetGroup->addItems($this->lstTargetGroup_GetItems());
        $this->lstTargetGroup->addAction(new Q\Event\Change(), new Q\Action\Ajax('lstTargetGroup_Change'));

        if (TargetGroupOfCalendar::countByIsEnabled(1) == 0) {
            $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);
            $this->lstTargetGroup->SelectedValue = $this->objEventsCalendar->TargetGroupId;
            $this->lstTargetGroup->Enabled = false;
        } else {
            $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);
            $this->lstTargetGroup->SelectedValue = $this->objEventsCalendar->TargetGroupId ? $this->objEventsCalendar->TargetGroupId : null;
        }

        $this->lblTitle = new Q\Plugin\Control\Label($this);
        $this->lblTitle->Text = t('Event title');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 400);
        $this->lblTitle->Required = true;

        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Event title');
        $this->txtTitle->Text = $this->objEventsCalendar->Title ? $this->objEventsCalendar->Title : null;
        $this->txtTitle->MaxLength = EventsCalendar::TitleMaxLength;
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
        $this->lstChanges->SelectedValue = $this->objEventsCalendar->EventsChangesId;

        if (EventsChanges::countAll() == 0 || EventsChanges::countByStatus(1) == 0) {
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
        $this->txtEventPlace->Text = $this->objEventsCalendar->EventPlace ? $this->objEventsCalendar->EventPlace : null;
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
        $this->calBeginningEvent->Text = $this->objEventsCalendar->BeginningEvent ?
            $this->objEventsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') : null;
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
        $this->calEndEvent->Text = $this->objEventsCalendar->EndEvent ?
            $this->objEventsCalendar->EndEvent->qFormat('DD.MM.YYYY') : null;
        $this->calEndEvent->addCssClass('calendar-trigger');
        $this->calEndEvent->Placeholder = t('End');
        $this->calEndEvent->addAction(new Change(), new Ajax('setDate_EndEvent'));
        $this->calEndEvent->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/locales/bootstrap-datetimepicker.et.js");

        $this->calStartTime = new Q\Plugin\ClockPicker($this);
        $this->calStartTime->AutoClose = true;
        $this->calStartTime->Text = $this->objEventsCalendar->StartTime ?
            $this->objEventsCalendar->StartTime->qFormat('hhhh:mm') : null;
        $this->calStartTime->addCssClass('clock-trigger');
        $this->calStartTime->Placeholder = t('Start');
        $this->calStartTime->addAction(new Change(), new Ajax('setTime_StartTime'));

        $this->calEndTime = new Q\Plugin\ClockPicker($this);
        $this->calEndTime->AutoClose = true;
        $this->calEndTime->Text = $this->objEventsCalendar->EndTime ?
            $this->objEventsCalendar->EndTime->qFormat('hhhh:mm') : null;
        $this->calEndTime->addCssClass('clock-trigger');
        $this->calEndTime->Placeholder = t('End');
        $this->calEndTime->addAction(new Change(), new Ajax('setTime_EndTime'));

        $this->lblWebsiteUrl = new Q\Plugin\Control\Label($this);
        $this->lblWebsiteUrl->Text = t('Website');
        $this->lblWebsiteUrl->addCssClass('col-md-3');
        $this->lblWebsiteUrl->setCssStyle('font-weight', 400);

        $this->txtWebsiteUrl = new Bs\TextBox($this);
        $this->txtWebsiteUrl->Placeholder = t('Website address');
        $this->txtWebsiteUrl->Text = $this->objEventsCalendar->WebsiteUrl ? $this->objEventsCalendar->WebsiteUrl : null;
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
        $this->lstWebsiteTargetType->SelectedValue = $this->objEventsCalendar->WebsiteTargetTypeId;

        if (!$this->objEventsCalendar->WebsiteUrl) {
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
        $this->txtFacebookUrl->Text = $this->objEventsCalendar->FacebookUrl ? $this->objEventsCalendar->FacebookUrl : null;
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
        $this->lstFacebookTargetType->SelectedValue = $this->objEventsCalendar->FacebookTargetTypeId;

        if (!$this->objEventsCalendar->FacebookUrl) {
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
        $this->txtInstagramUrl->Text = $this->objEventsCalendar->InstagramUrl ? $this->objEventsCalendar->InstagramUrl : null;
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
        $this->lstInstagramTargetType->SelectedValue = $this->objEventsCalendar->InstagramTargetTypeId;

        if (!$this->objEventsCalendar->InstagramUrl) {
            $this->lstInstagramTargetType->Enabled = false;
        } else {
            $this->lstInstagramTargetType->Enabled = true;
        }

        $this->lstInstagramTargetType->addAction(new Change(), new Ajax('lstInstagramTarget_Change'));

        $this->lblContact = new Q\Plugin\Control\Label($this);
        $this->lblContact->Text = t('Contact');
        $this->lblContact->addCssClass('col-md-3');
        $this->lblContact->setCssStyle('font-weight', 400);
        $this->lblContact->Required = true;

        $this->txtOrganizers = new Bs\TextBox($this);
        $this->txtOrganizers->Placeholder = t('Organizers');
        $this->txtOrganizers->Text = $this->objEventsCalendar->Organizers ? $this->objEventsCalendar->Organizers : null;
        $this->txtOrganizers->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtOrganizers->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtOrganizers->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtOrganizers->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtPhone = new Bs\TextBox($this);
        $this->txtPhone->Placeholder = t('+372 1234 5678');
        $this->txtPhone->Text = $this->objEventsCalendar->Phone ? $this->objEventsCalendar->Phone : null;
        $this->txtPhone->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtPhone->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtPhone->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtPhone->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtEmail = new Bs\TextBox($this);
        $this->txtEmail->Placeholder = t('Email');
        $this->txtEmail->Text = $this->objEventsCalendar->Email ? $this->objEventsCalendar->Email : null;
        $this->txtEmail->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtEmail->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtEmail->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtEmail->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
        $this->lblGroupTitle->Text = t('Event group');
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

        $countByIsReserved = EventsSettings::countByIsReserved(1);
        $objGroups = EventsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::EventsSettings()->Id)));

        foreach ($objGroups as $objTitle) {
            if ($objTitle->IsReserved === 1) {
                $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                $this->lstGroupTitle->SelectedValue = $this->objEventsCalendar->MenuContentGroupTitleId;
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

        if ($this->objEventsCalendar->getTitleSlug()) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
            $this->txtTitleSlug->setCssStyle('text-align', 'left;');

            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objEventsCalendar->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;

        } else {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->lblInformation = new Q\Plugin\Control\Label($this);
        $this->lblInformation->Text = t('Information');
        $this->lblInformation->setCssStyle('font-weight', 'bold');

        $this->txtInformation = new Q\Plugin\CKEditor($this);
        $this->txtInformation->Text = $this->objEventsCalendar->Information ? $this->objEventsCalendar->Information : null;
        $this->txtInformation->Configuration = 'ckConfig';
        $this->txtInformation->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtInformation->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtInformation->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtInformation->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblSchedule = new Q\Plugin\Control\Label($this);
        $this->lblSchedule->Text = t('Schedule');
        $this->lblSchedule->setCssStyle('font-weight', 'bold');

        $this->txtSchedule = new Q\Plugin\CKEditor($this);
        $this->txtSchedule->Text = $this->objEventsCalendar->Schedule ? $this->objEventsCalendar->Schedule : null;
        $this->txtSchedule->Configuration = 'ckConfig';
        $this->txtSchedule->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtSchedule->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtSchedule->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtSchedule->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objEventsCalendar->PostDate ? $this->objEventsCalendar->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostDate->setCssStyle('font-weight', 'normal');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objEventsCalendar->PostUpdateDate ? $this->objEventsCalendar->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->setCssStyle('font-weight', 'bold');

        $this->txtAuthor  = new Bs\Label($this);
        $this->txtAuthor->Text = $this->objEventsCalendar->Author;
        $this->txtAuthor->setCssStyle('font-weight', 'normal');

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objEventsCalendar->getUserAsEditorsArray());
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

        $this->refreshDisplay();

        $this->objMediaFinder = new Q\Plugin\MediaFinder($this);
        $this->objMediaFinder->TempUrl = APP_UPLOADS_TEMP_URL . "/_files/thumbnail";
        $this->objMediaFinder->PopupUrl = QCUBED_FILEMANAGER_URL . "/examples/finder.php";
        $this->objMediaFinder->EmptyImageAlt = t("Choose a picture");
        $this->objMediaFinder->SelectedImageAlt = t("Selected picture");

        $this->objMediaFinder->SelectedImageId = $this->objEventsCalendar->getPictureId() ? $this->objEventsCalendar->getPictureId() : null;

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
        $this->txtPictureDescription->Text = $this->objEventsCalendar->PictureDescription;
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
        $this->txtAuthorSource->Text = $this->objEventsCalendar->AuthorSource;
        $this->txtAuthorSource->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtAuthorSource->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtAuthorSource->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtAuthorSource->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('itemEscape_Click'));
        $this->txtAuthorSource->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        if (!$this->objEventsCalendar->getPictureId()) {
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
        $this->lstStatus->SelectedValue = $this->objEventsCalendar->Status;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->Enabled = true;
        $this->lstStatus->AddAction(new Q\Event\Change(), new Q\Action\Ajax('lstStatus_Change'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes various buttons with specific properties, styles, and actions.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSave_Click'));

        $this->btnSaving = new Bs\Button($this);
        $this->btnSaving->Text = t('Save and close');
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
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

        $this->btnGoToTargetGroup = new Bs\Button($this);
        $this->btnGoToTargetGroup->Tip = true;
        $this->btnGoToTargetGroup->ToolTip = t('Go the target groups change manager');
        $this->btnGoToTargetGroup->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToTargetGroup->CssClass = 'btn btn-default';
        $this->btnGoToTargetGroup->setCssStyle('float', 'right');
        $this->btnGoToTargetGroup->addWrapperCssClass('center-button');
        $this->btnGoToTargetGroup->CausesValidation = false;
        $this->btnGoToTargetGroup->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToTargetGroup_Click'));

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
        $this->btnGoToSettings->ToolTip = t('Go to events settings manager');
        $this->btnGoToSettings->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnGoToSettings->CssClass = 'btn btn-default';
        $this->btnGoToSettings->setCssStyle('float', 'right');
        $this->btnGoToSettings->addWrapperCssClass('center-button');
        $this->btnGoToSettings->CausesValidation = false;
        $this->btnGoToSettings->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGoToSettings_Click'));
    }

    /**
     * Creates and configures multiple Toastr instances with specific alert types, positions, messages, and other properties.
     *
     * This method initializes various Toastr alerts to indicate success, error, or informational messages. Each toastr instance
     * is configured with options such as alert type, position, message content, progress bar settings, HTML escape, and timeout.
     * These alerts are designed to provide user feedback for different scenarios, including saving, modifying, or restoring data,
     * as well as displaying error messages and validation alerts.
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
     * Creates and configures modal dialogs for various actions such as deleting, moving, or modifying events.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this event?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move this event from this event group to another event group?</p>');
        $this->dlgModal2->Title = t('Warning');
        $this->dlgModal2->HeaderClasses = 'btn-danger';
        $this->dlgModal2->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal2->addCloseButton(t("I'll cancel"));
        $this->dlgModal2->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('moveItem_Click'));

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to hide this event or edit it again?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">You can make this event public again later!</p>');
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
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event is now hidden!</p>');
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Success");
        $this->dlgModal5->HeaderClasses = 'btn-success';
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event has now been made public!</p>');
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Title = t("Success");
        $this->dlgModal6->HeaderClasses = 'btn-success';
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event is now a draft!</p>');
        $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Retrieves a list of items representing target groups.
     *
     * This method creates a list of ListItem objects associated with target groups
     * using a query cursor. Each ListItem object contains the target group's string representation
     * and its ID. The method also determines if the item should be marked as selected
     * based on the current events calendar's associated target group and applies a disabled
     * state to items as needed.
     *
     * @return ListItem[] An array of ListItem objects representing target groups.
     */
    public function lstTargetGroup_GetItems() {
        $a = array();
        $objCondition = $this->objTargetGroupCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objTargetGroupCursor = TargetGroupOfCalendar::queryCursor($objCondition, $this->objTargetGroupClauses);

        // Iterate through the Cursor
        while ($objTargetGroup = TargetGroupOfCalendar::instantiateCursor($objTargetGroupCursor)) {
            $objListItem = new ListItem($objTargetGroup->__toString(), $objTargetGroup->Id);
            if (($this->objEventsCalendar->TargetGroup) && ($this->objEventsCalendar->TargetGroup->Id == $objTargetGroup->Id))
                $objListItem->Selected = true;

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objTargetGroup->IsEnabled == 2) {
                $objListItem->Disabled = true;
            }
            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Retrieves a list of items for the changes dropdown or list.
     * Iterates through a cursor of `EventsChanges` objects and creates a list of items,
     * optionally setting selected and disabled attributes based on certain conditions.
     *
     * @return ListItem[] An array of ListItem objects representing changes, with
     *                     possible selected and disabled attributes set.
     */
    public function lstChanges_GetItems() {
        $a = array();
        $objCondition = $this->objChangesCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objChangesCursor = EventsChanges::queryCursor($objCondition, $this->objChangesClauses);

        // Iterate through the Cursor
        while ($objChanges = EventsChanges::instantiateCursor($objChangesCursor)) {
            $objListItem = new ListItem($objChanges->__toString(), $objChanges->Id);
            if (($this->objEventsCalendar->EventsChanges) && ($this->objEventsCalendar->EventsChanges->Id == $objChanges->Id))
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
     * Retrieves a list of website target types from the TargetType class.
     *
     * @return array An associative array where keys represent target type identifiers and values represent target type names.
     */
    public function lstWebsiteTargetType_GetItems() {
        return TargetType::nameArray();
    }

    /**
     * Retrieves an array of Facebook target types.
     *
     * @return array Returns an associative array of target type names.
     */
    public function lstFacebookTargetType_GetItems() {
        return TargetType::nameArray();
    }

    /**
     * Retrieves a list of target type names for Instagram.
     *
     * @return array An array of target type names fetched from the TargetType name array.
     */
    public function lstInstagramTargetType_GetItems() {
        return TargetType::nameArray();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Updates the beginning event date of the calendar based on the provided parameters.
     * If a valid date is provided, it sets the beginning event date and notifies the user.
     * If no date is provided, it clears the date, updates the status, and notifies the user.
     * The changes are then saved and the display is refreshed.
     *
     * @param ActionParams $params Parameters passed to determine and set the beginning event date.
     * @return void
     */
    public function setDate_BeginningEvent(ActionParams $params)
    {
        if ($this->calBeginningEvent->Text) {
            $this->objEventsCalendar->setBeginningEvent($this->calBeginningEvent->DateTime);

            $this->dlgToastr3->notify(); // StartDate OK
        } else {
            $this->calBeginningEvent->Text = null;
            $this->lstStatus->SelectedValue = 2;

            $this->objEventsCalendar->setBeginningEvent(null);
            $this->objEventsCalendar->setStatus(2);

            $this->dlgToastr4->notify(); // Mandatory StartDate
        }

        $this->objEventsCalendar->save();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Sets the end date for an event and performs validation based on the beginning date.
     *
     * @param ActionParams $params An object containing parameters for the action.
     * @return void
     */
    public function setDate_EndEvent(ActionParams $params)
    {
        if ($this->calBeginningEvent->Text && $this->calEndEvent->Text) {
            if (new DateTime($this->calBeginningEvent->Text) > new DateTime($this->calEndEvent->Text)) {
                $this->calEndEvent->Text = null;
                $this->objEventsCalendar->setEndEvent(null);

                $this->dlgToastr6->notify(); // StartDate smaller than EndDate, warning!
            } else if ($this->calEndEvent->Text) {
                $this->objEventsCalendar->setEndEvent($this->calEndEvent->DateTime);

                $this->dlgToastr5->notify(); // EndDate OK
            } else {
                $this->calEndEvent->Text = null;
                $this->objEventsCalendar->setEndEvent(null);

                $this->dlgToastr5->notify(); // EndDate OK
            }
        } else if (!$this->calBeginningEvent->Text && $this->calEndEvent->Text) {
            $this->calEndEvent->Text = null;
            $this->lstStatus->SelectedValue = 2;

            $this->objEventsCalendar->setEndEvent(null);
            $this->objEventsCalendar->setStatus(2);

            $this->dlgToastr7->notify(); // StartDate not, warning!
        } else {
            $this->calEndEvent->Text = null;
            $this->objEventsCalendar->setEndEvent(null);

            $this->dlgToastr5->notify(); // EndDate OK
        }

        $this->objEventsCalendar->save();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Sets the start time for the event calendar based on the provided parameters and updates related components.
     *
     * @param ActionParams $params The parameters containing necessary information to set the start time.
     * @return void
     */
    public function setTime_StartTime(ActionParams $params)
    {
        if ($this->calBeginningEvent->Text && $this->calStartTime->Text) {
            $this->objEventsCalendar->setStartTime($this->calStartTime->DateTime);

            $this->dlgToastr8->notify(); // StartTime OK
        } else if (!$this->calBeginningEvent->Text && $this->calStartTime->Text) {

            $this->calStartTime->Text = null;
            $this->objEventsCalendar->setStartTime(null);

            $this->dlgToastr7->notify(); // StartDate not, warning!

        } else {
            $this->calStartTime->Text = null;
            $this->objEventsCalendar->setStartTime(null);

            $this->dlgToastr8->notify(); // StartTime OK
        }

        $this->objEventsCalendar->save();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Sets the end time for the event calendar based on the provided parameters.
     *
     * This method evaluates the provided `ActionParams` values and adjusts the end time
     * and corresponding values for the calendar, while providing appropriate notifications
     * if certain conditions are met.
     *
     * @param ActionParams $params The parameters specifying the end time and associated conditions.
     *
     * @return void
     */
    public function setTime_EndTime(ActionParams $params)
    {
        if ($this->calEndEvent->Text && $this->calEndTime->Text) {
            $this->objEventsCalendar->setEndTime($this->calEndTime->DateTime);

        } else if (!$this->calEndEvent->Text && $this->calEndTime->Text) {
            $this->calEndTime->Text = null;
            $this->objEventsCalendar->setEndTime(null);

            $this->dlgToastr10->notify(); // EndDate not, warning!

            $this->dlgToastr9->notify(); // EndTime OK
        } else {
            $this->calEndTime->Text = null;
            $this->objEventsCalendar->setEndTime(null);

            $this->dlgToastr9->notify(); // EndTime OK
        }

        $this->objEventsCalendar->save();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the change event for the group title dropdown list.
     *
     * @param ActionParams $params The parameters associated with the action.
     * @return void
     */
    protected function lstGroupTitle_Change(ActionParams $params)
    {
        if ($this->lstGroupTitle->SelectedValue !== $this->objEventsCalendar->getMenuContentGroupTitleId()) {
            $this->dlgModal2->showDialogBox();
        }
    }

    /**
     * Clears the text value of the txtYear field if it is empty and sets its HTML attribute 'required' to 'required'.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    protected function txtYear_Clear(ActionParams $params)
    {
        if (!$this->txtYear->Text) {
            $this->txtYear->Text = null;
            $this->txtYear->setHtmlAttribute('required', 'required');
        }
    }

    /**
     * Handles the change event for the txtYear input field. Updates the year in the events calendar
     * and triggers necessary UI updates if a valid year is provided. If no year is provided,
     * sets the input field to required and triggers a notification.
     *
     * @param ActionParams $params Parameters associated with the change event.
     * @return void
     */
    protected function txtYear_Change(ActionParams $params)
    {
        if ($this->txtYear->Text) {
            $this->objEventsCalendar->setYear($this->txtYear->Text);
            $this->objEventsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        } else {
            $this->txtYear->Text = null;
            $this->txtYear->setHtmlAttribute('required', 'required');

            $this->dlgToastr13->notify();
        }
    }

    /**
     * Handles the change event for the lstChanges list.
     *
     * @param ActionParams $params Parameters passed to the action.
     * @return void
     */
    protected function lstChanges_Change(ActionParams $params)
    {
        if ($this->lstChanges->SelectedValue !== $this->objEventsCalendar->getEventsChangesId()) {
            $this->objEventsCalendar->setEventsChangesId($this->lstChanges->SelectedValue);
            $this->objEventsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }
    }

    /**
     * Handles the change event for the website target type list.
     * Updates the related properties in the EventsCalendar object and triggers associated UI actions.
     *
     * @param ActionParams $params The parameters passed from the action event.
     * @return void
     */
    protected function lstWebsiteTarget_Change(ActionParams $params)
    {
        if ($this->lstWebsiteTargetType->SelectedValue !== $this->objEventsCalendar->getWebsiteTargetTypeId()) {
            $this->objEventsCalendar->setWebsiteUrl($this->txtWebsiteUrl->Text);
            $this->objEventsCalendar->setWebsiteTargetTypeId($this->lstWebsiteTargetType->SelectedValue);
            $this->objEventsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }
    }

    /**
     * Handles the KeyUp event for the website target type list.
     *
     * @param ActionParams $params The parameters related to the action triggering this method.
     * @return void
     */
    public function lstWebsiteTargetType_KeyUp(ActionParams $params)
    {
        if ($this->txtWebsiteUrl->Text) {
            $this->lstWebsiteTargetType->Enabled = true;
        } else {
            $this->lstWebsiteTargetType->Enabled = false;
            $this->objEventsCalendar->setWebsiteUrl(null);
            $this->objEventsCalendar->setWebsiteTargetTypeId(null);
            $this->objEventsCalendar->save();

            $this->txtWebsiteUrl->Text = null;
            $this->lstWebsiteTargetType->SelectedValue = null;

            $this->dlgToastr1->notify();
        }
    }

    /**
     * Updates the Facebook target settings for the events calendar when the selection is changed.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    protected function lstFacebookTarget_Change(ActionParams $params)
    {
        if ($this->lstFacebookTargetType->SelectedValue !== $this->objEventsCalendar->getFacebookTargetTypeId()) {
            $this->objEventsCalendar->setFacebookUrl($this->txtFacebookUrl->Text);
            $this->objEventsCalendar->setFacebookTargetTypeId($this->lstFacebookTargetType->SelectedValue);
            $this->objEventsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }
    }

    /**
     * Handles the KeyUp event for the Facebook target type dropdown list.
     * Enables or disables the dropdown based on the value in the Facebook URL text field.
     * If the Facebook URL text field is empty, it clears and resets relevant fields and properties.
     *
     * @param ActionParams $params The parameters for the action triggering the event.
     * @return void
     */
    public function lstFacebookTargetType_KeyUp(ActionParams $params)
    {
        if ($this->txtFacebookUrl->Text) {
            $this->lstFacebookTargetType->Enabled = true;
        } else {
            $this->lstFacebookTargetType->Enabled = false;
            $this->objEventsCalendar->setFacebookUrl(null);
            $this->objEventsCalendar->setFacebookTargetTypeId(null);
            $this->objEventsCalendar->save();

            $this->txtFacebookUrl->Text = null;
            $this->lstFacebookTargetType->SelectedValue = null;

            $this->dlgToastr1->notify();
        }
    }

    /**
     * Handles the change event for the Instagram target selection.
     * Updates the Instagram URL and target type ID in the event calendar object,
     * saves the changes, and updates the UI.
     *
     * @param ActionParams $params The parameters associated with the action.
     *
     * @return void
     */
    protected function lstInstagramTarget_Change(ActionParams $params)
    {
        if ($this->lstInstagramTargetType->SelectedValue !== $this->objEventsCalendar->getInstagramTargetTypeId()) {
            $this->objEventsCalendar->setInstagramUrl($this->txtInstagramUrl->Text);
            $this->objEventsCalendar->setInstagramTargetTypeId($this->lstInstagramTargetType->SelectedValue);
            $this->objEventsCalendar->save();

            $this->dlgToastr1->notify();

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }
    }

    /**
     * Handles the key-up event for the Instagram target type selection list.
     *
     * @param ActionParams $params The parameters related to the key-up action.
     * @return void
     */
    public function lstInstagramTargetType_KeyUp(ActionParams $params)
    {
        if ($this->txtInstagramUrl->Text) {
            $this->lstInstagramTargetType->Enabled = true;
        } else {
            $this->lstInstagramTargetType->Enabled = false;
            $this->objEventsCalendar->setInstagramUrl(null);
            $this->objEventsCalendar->setInstagramTargetTypeId(null);
            $this->objEventsCalendar->save();

            $this->txtInstagramUrl->Text = null;
            $this->lstInstagramTargetType->SelectedValue = null;

            $this->dlgToastr1->notify();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click action for moving an event item to a different group.
     * Updates the related database records, recalculates locks for groups,
     * and refreshes the interface components. Redirects to the updated event
     * calendar edit page after completing the operation.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    protected function moveItem_Click(ActionParams $params)
    {
        $this->dlgModal2->hideDialogBox();

        $objGroupTitle = EventsSettings::loadById($this->lstGroupTitle->SelectedValue);

        // Before proceeding to other activities, you must fix the initial data of the tables "events_calendar" and "target_group_of_calendar"
        $objLockedGroup = $this->objEventsCalendar->getMenuContentGroupTitleId();
        $objTargetGroup = $objGroupTitle;

        $currentCount = EventsCalendar::countByMenuContentGroupTitleId($objLockedGroup);
        $nextCount = EventsCalendar::countByMenuContentGroupTitleId($objTargetGroup->getId());

        // Here you must first check the lock status of the following folder, to do this check...
        $objGroup = EventsSettings::loadById($objTargetGroup->getId());

        if ($nextCount == 0) {
            $objGroup->setEventsLocked(1);
            $objGroup->save();
        }

        // Next, we check the lock status of the previous folder, to do this, check...
        $objGroup = EventsSettings::loadById($objLockedGroup);

        if ($currentCount) {
            if ($currentCount == 1) {
                $objGroup->setEventsLocked(0);
            } else {
                $objGroup->setEventsLocked(1);
            }
            $objGroup->save();
        }

        $objBeforeEventsSlug = EventsCalendar::load($this->objEventsCalendar->getId());
        $beforeSlug = $objBeforeEventsSlug->getTitleSlug();

        $this->objEventsCalendar->setTitle($this->txtTitle->Text);
        $this->objEventsCalendar->setMenuContentGroupId($objGroupTitle->getMenuContentId());
        $this->objEventsCalendar->setMenuContentGroupTitleId($this->lstGroupTitle->SelectedValue);
        $this->objEventsCalendar->setEventsGroupName($this->lstGroupTitle->SelectedName);

        $this->objEventsCalendar->updateEvent($this->txtYear->Text, $this->txtTitle->Text, $this->lstGroupTitle->SelectedName, $objGroupTitle->getTitleSlug());
        $this->objEventsCalendar->setPostUpdateDate(Q\QDateTime::Now());
        $this->objEventsCalendar->save();

        $this->objFrontendLinks->setGroupedId($objGroupTitle->getMenuContentId());
        $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
        $this->objFrontendLinks->setFrontendTitleSlug($this->objEventsCalendar->getTitleSlug());
        $this->objFrontendLinks->save();


        // We are updating the slug
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
            $this->objEventsCalendar->getTitleSlug();
        $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
        $this->txtTitleSlug->HtmlEntities = false;
        $this->txtTitleSlug->setCssStyle('font-weight', 400);

        Application::redirect('event_calendar_edit.php?id=' . $this->intId . '&group=' . $objGroupTitle->getMenuContentId());

        ///////////////////////////////////////////////////////////////////////////////////////////

        // Since we are using event redirection from one group to another and need to refresh the page using Application::redirect(),
        // we can't report on the success of the event redirection, so let these Toasts remain as they are...
        $objAfterEventSlug = EventsCalendar::load($this->objEventsCalendar->getId());
        $afterSlug = $objAfterEventSlug->getTitleSlug();

        if ($beforeSlug !== $afterSlug) {
            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr11->notify();
        }

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Handles the deletion of an item based on the given action parameters.
     * Performs various checks and operations including updating related file locks,
     * managing associated settings, and deleting related records.
     *
     * @param ActionParams $params Parameters related to the action triggering this method, including action-specific data.
     * @return void
     */
    protected function deleteItem_Click(ActionParams $params)
    {
        if ($params->ActionParameter == "pass") {
            $objFiles = Files::loadById($this->objEventsCalendar->getPictureId());

            if ($this->objEventsCalendar->getPictureId()) {
                if ($objFiles->getLockedFile() !== 0) {
                    $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                    $objFiles->save();
                }
            }

            $objSettings = EventsSettings::loadById($this->objEventsCalendar->getMenuContentGroupTitleId());
            $countLocked = EventsCalendar::countByMenuContentGroupTitleId($this->objEventsCalendar->getMenuContentGroupTitleId());

            if ($countLocked === 1) {
                $objSettings->setEventsLocked(0);
                $objSettings->save();
            }
            $this->objEventsCalendar->unassociateAllUsersAsEditors();
            $this->objEventsCalendar->delete();
            $this->objFrontendLinks->delete();
            $this->redirectToListPage();
        }
        $this->dlgModal1->hideDialogBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Saves the selected image details to the event calendar, updates post details, and refreshes the display.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    protected function imageSave_Push(ActionParams $params)
    {
        $saveId = $this->objMediaFinder->Item;

        $this->objEventsCalendar->setPictureId($saveId);
        $this->objEventsCalendar->setPostUpdateDate(Q\QDateTime::Now());
        $this->objEventsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objEventsCalendar->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objEventsCalendar->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objEventsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->refreshDisplay();

        $this->lblPictureDescription->Display = true;
        $this->txtPictureDescription->Display = true;
        $this->lblAuthorSource->Display = true;
        $this->txtAuthorSource->Display = true;

        $this->dlgToastr1->notify();
    }

    /**
     * Handles the deletion of the image associated with the current event and updates related properties.
     * Reduces the lock count of the associated file, clears picture-related data, and refreshes the UI.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    protected function imageDelete_Push(ActionParams $params)
    {
        $objFiles = Files::loadById($this->objEventsCalendar->getPictureId());

        if ($objFiles->getLockedFile() !== 0) {
            $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
            $objFiles->save();
        }

        $this->objEventsCalendar->setPictureId(null);
        $this->objEventsCalendar->setPictureDescription(null);
        $this->objEventsCalendar->setAuthorSource(null);
        $this->objEventsCalendar->setPostUpdateDate(Q\QDateTime::Now());
        $this->objEventsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->objEventsCalendar->save();

        $this->txtUsersAsEditors->Text = implode(', ', $this->objEventsCalendar->getUserAsEditorsArray());
        $this->calPostUpdateDate->Text = $this->objEventsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

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
     * Handles the click event for the save button, validates form fields,
     * and triggers appropriate notifications based on the validation outcome.
     * Applies necessary HTML attributes to indicate invalid fields.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    public function btnSave_Click(ActionParams $params)
    {
        $this->renderActionsWithId();
        $this->InputsCheck();

        if (count($this->errors)) {
            $this->lstStatus->SelectedValue =  $this->objEventsCalendar->getStatus();
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
     * Handles the save and close button click event, validating input fields, notifying errors if needed, and performing necessary redirection or actions.
     *
     * @param ActionParams $params Parameters associated with the button click action.
     * @return void
     */
    public function btnSaveClose_Click(ActionParams $params)
    {
        $this->renderActionsWithId();
        $this->InputsCheck();

        if (count($this->errors)) {
            $this->lstStatus->SelectedValue =  $this->objEventsCalendar->getStatus();
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
     * Updates the target group information in the calendar object based on the selected value and name,
     * saves the changes, triggers a notification, and refreshes relevant UI components.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    protected function lstTargetGroup_Change(ActionParams $params)
    {
        $this->objEventsCalendar->setTargetGroupId($this->lstTargetGroup->SelectedValue);

        if ($this->lstTargetGroup->SelectedValue) {
            $this->objEventsCalendar->setTargetGroupTitle($this->lstTargetGroup->SelectedName);
        } else {
            $this->objEventsCalendar->setTargetGroupTitle(null);
        }

        $this->objEventsCalendar->save();

        $this->dlgToastr1->notify();

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the saving and updating process for event details and frontend link configurations.
     * Retrieves, updates, and saves related data to ensure proper event and link information is stored.
     * Also manages the display refresh and link rendering for the event.
     *
     * @return void
     */
    protected function saveHelper()
    {
        $objTemplateLocking = FrontendTemplateLocking::load(8);
        $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->getId());

        $this->objEventsCalendar->setYear($this->txtYear->Text);

        if ($this->lstTargetGroup->SelectedValue) {
            $this->objEventsCalendar->setTargetGroupId($this->lstTargetGroup->SelectedValue);
            $this->objEventsCalendar->setTargetGroupTitle($this->lstTargetGroup->SelectedName);
        }

        $this->objEventsCalendar->setTitle($this->txtTitle->Text);
        $this->objEventsCalendar->setEventsChangesId($this->lstChanges->SelectedValue);
        $this->objEventsCalendar->setEventPlace($this->txtEventPlace->Text);

        $this->objEventsCalendar->setWebsiteUrl($this->txtWebsiteUrl->Text);
        $this->objEventsCalendar->setWebsiteTargetTypeId($this->lstWebsiteTargetType->SelectedValue);

        $this->objEventsCalendar->setFacebookUrl($this->txtFacebookUrl->Text);
        $this->objEventsCalendar->setFacebookTargetTypeId($this->lstFacebookTargetType->SelectedValue);
        $this->objEventsCalendar->setInstagramUrl($this->txtInstagramUrl->Text);

        $this->objEventsCalendar->setInstagramTargetTypeId($this->lstInstagramTargetType->SelectedValue);

        $this->objEventsCalendar->setOrganizers($this->txtOrganizers->Text);
        $this->objEventsCalendar->setPhone($this->txtPhone->Text);
        $this->objEventsCalendar->setEmail($this->txtEmail->Text);

        $this->objEventsCalendar->updateEvent($this->txtYear->Text, $this->txtTitle->Text, $this->lstGroupTitle->SelectedName, $this->objEventsSettings->getTitleSlug());
        $this->objEventsCalendar->setInformation($this->txtInformation->Text);
        $this->objEventsCalendar->setSchedule($this->txtSchedule->Text);

        $this->refreshDisplay();

        $this->objEventsCalendar->setPictureDescription($this->txtPictureDescription->Text);
        $this->objEventsCalendar->setAuthorSource($this->txtAuthorSource->Text);
        $this->objEventsCalendar->setStatus($this->lstStatus->SelectedValue);

        $this->objEventsCalendar->save();

        $this->objFrontendLinks->setLinkedId($this->intId);
        $this->objFrontendLinks->setGroupedId($this->intGroup);
        $this->objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
        $this->objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
        $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
        $this->objFrontendLinks->setContentTypesManagamentId(8);
        $this->objFrontendLinks->setFrontendTitleSlug($this->objEventsCalendar->getTitleSlug());
        $this->objFrontendLinks->setIsActivated(1);
        $this->objFrontendLinks->save();

        $this->txtAuthor->Text = $this->objEventsCalendar->getAuthor();

        if ($this->objEventsCalendar->getTitle()) {
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objEventsCalendar->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        } else {
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->renderActionsWithId();
        $this->refreshDisplay();
    }

    /**
     * Handles the click event for escaping or resetting item-related data fields.
     * Updates various UI components with data retrieved from the objEventsCalendar object.
     * If a cancellation condition is detected, it triggers a notification dialog.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        $objCancel = $this->objEventsCalendar->getId();

        // Check if $objCancel is available
        if ($objCancel) {
            $this->dlgToastr12->notify();
        }

        $this->txtYear->Text = $this->objEventsCalendar->getYear();
        $this->txtTitle->Text = $this->objEventsCalendar->getTitle();
        $this->txtEventPlace->Text = $this->objEventsCalendar->getEventPlace();
        $this->txtFacebookUrl->Text = $this->objEventsCalendar->getWebsiteUrl();
        $this->txtFacebookUrl->Text = $this->objEventsCalendar->getFacebookUrl();
        $this->txtInstagramUrl->Text = $this->objEventsCalendar->getInstagramUrl();
        $this->txtOrganizers->Text = $this->objEventsCalendar->getOrganizers();
        $this->txtPhone->Text = $this->objEventsCalendar->getPhone();
        $this->txtEmail->Text = $this->objEventsCalendar->getEmail();
        $this->txtInformation->Text = $this->objEventsCalendar->getInformation();
        $this->txtSchedule->Text = $this->objEventsCalendar->getSchedule();
        $this->txtPictureDescription->Text = $this->objEventsCalendar->getPictureDescription();
        $this->txtAuthorSource->Text = $this->objEventsCalendar->getAuthorSource();
    }

    /**
     * Handles changes to the lstStatus field by checking the current status of the Events Calendar object.
     * If the status is 1, a modal dialog box is displayed. Otherwise, input fields are locked.
     *
     * @return void
     */
    protected  function lstStatus_Change()
    {
        if ($this->objEventsCalendar->getStatus() === 1) {
            $this->dlgModal3->showDialogBox();
        } else {
            $this->lockInputFields();
        }
    }

    /**
     * Handles the click event for the status item, updating the status of the event calendar
     * and performing necessary UI updates.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    protected function statusItem_Click(ActionParams $params)
    {
        if ($this->lstStatus->SelectedValue === 2) {
            $this->dlgModal3->hideDialogBox();
            $this->objEventsCalendar->setStatus(2);
        } else if ($this->lstStatus->SelectedValue === 3){
            $this->dlgModal3->hideDialogBox();
            $this->objEventsCalendar->setStatus(3);
        }

        $this->objEventsCalendar->save();

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
            $this->lstStatus->SelectedValue =  $this->objEventsCalendar->getStatus();
        }

        // Condition for which notification to show
        if (count($this->errors) === 1) {
            $this->dlgToastr13->notify(); // If only one field is invalid
        } elseif (count($this->errors) > 1) {
            $this->dlgToastr2->notify(); // If there is more than one invalid field
        } else if ($this->lstStatus->SelectedValue === 1) {
            $this->objEventsCalendar->setStatus(1);
            $this->dlgModal5->showDialogBox();
        } else if ($this->lstStatus->SelectedValue === 2) {
            $this->objEventsCalendar->setStatus(2);
            $this->dlgModal4->showDialogBox();
        } else if ($this->lstStatus->SelectedValue === 3) {
            $this->objEventsCalendar->setStatus(3);
            $this->dlgModal6->showDialogBox();
        }

        $this->objEventsCalendar->save();
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
        $this->lstStatus->SelectedValue = $this->objEventsCalendar->getStatus();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Updates the visibility of various display elements based on the state of the events calendar.
     *
     * Determines the display state of post date, post update date, author, and users as editors fields
     * using conditions derived from the events calendar object properties such as post date, update date,
     * author assignment, and the number of users assigned as editors.
     *
     * @return void
     */
    protected function refreshDisplay()
    {
        if ($this->objEventsCalendar->getPostDate() &&
            !$this->objEventsCalendar->getPostUpdateDate() &&
            $this->objEventsCalendar->getAuthor() &&
            !$this->objEventsCalendar->countUsersAsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }

        if ($this->objEventsCalendar->getPostDate() &&
            $this->objEventsCalendar->getPostUpdateDate() &&
            $this->objEventsCalendar->getAuthor() &&
            !$this->objEventsCalendar->countUsersAsEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }

        if ($this->objEventsCalendar->getPostDate() &&
            $this->objEventsCalendar->getPostUpdateDate() &&
            $this->objEventsCalendar->getAuthor() &&
            $this->objEventsCalendar->countUsersAsEditors()) {
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
     * Renders and updates actions associated with the current event identified by its ID.
     * Compares the form fields' values with the corresponding properties of the event object.
     * If any differences are found, it updates the event data, sets post-update metadata, and saves the changes.
     *
     * @return void
     */
    public function renderActionsWithId()
    {
        if (strlen($this->intId)) {
            if ($this->txtYear->Text !== $this->objEventsCalendar->getYear() ||
                $this->lstTargetGroup->SelectedValue !== $this->objEventsCalendar->getTargetGroupId() ||
                $this->txtTitle->Text !== $this->objEventsCalendar->getTitle() ||
                $this->lstChanges->SelectedValue !==$this->objEventsCalendar->getEventsChangesId() ||
                $this->txtEventPlace->Text !== $this->objEventsCalendar->getEventPlace() ||
                $this->calBeginningEvent->Text !== $this->objEventsCalendar->getBeginningEvent() ||
                $this->calEndEvent->Text !== $this->objEventsCalendar->getEndEvent() ||
                $this->calStartTime->Text !== $this->objEventsCalendar->getStartTime() ||
                $this->calEndTime->Text !== $this->objEventsCalendar->getEndTime() ||
                $this->txtOrganizers->Text !== $this->objEventsCalendar->getTitle() ||
                $this->txtTitle->Text !== $this->objEventsCalendar->getOrganizers() ||
                $this->txtPhone->Text !== $this->objEventsCalendar->getPhone() ||
                $this->txtEmail->Text !== $this->objEventsCalendar->getEmail() ||
                $this->txtWebsiteUrl->Text !== $this->objEventsCalendar->getWebsiteUrl() ||
                $this->lstWebsiteTargetType->SelectedValue !== $this->objEventsCalendar->getWebsiteTargetType() ||
                $this->txtFacebookUrl->Text !== $this->objEventsCalendar->getFacebookUrl() ||
                $this->lstFacebookTargetType->SelectedValue !== $this->objEventsCalendar->getFacebookTargetType() ||
                $this->txtInstagramUrl->Text !== $this->objEventsCalendar->getInstagramUrl() ||
                $this->lstInstagramTargetType->SelectedValue !== $this->objEventsCalendar->getInstagramTargetType() ||
                $this->txtInformation->Text !== $this->objEventsCalendar->getInformation() ||
                $this->txtSchedule->Text !== $this->objEventsCalendar->getSchedule() ||
                $this->txtPictureDescription->Text !== $this->objEventsCalendar->getPictureDescription() ||
                $this->txtAuthorSource->Text !== $this->objEventsCalendar->getAuthorSource() ||
                $this->lstStatus->SelectedValue !== $this->objEventsCalendar->getStatus()
            ) {
                $this->objEventsCalendar->setPostUpdateDate(Q\QDateTime::Now());
                $this->objEventsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objEventsCalendar->save();

                $this->txtUsersAsEditors->Text = implode(', ', $this->objEventsCalendar->getUserAsEditorsArray());
                $this->calPostUpdateDate->Text = $this->objEventsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            }
        }
    }

    /**
     * Validates input fields by checking if they are empty and adding corresponding error messages.
     * Also sets the HTML 'required' attribute for any field that is empty.
     *
     * @return void
     */
    protected  function InputsCheck()
    {
        // We check each field and add errors if necessary
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
     * Handles the Cancel button click event and redirects the user to the list page.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    public function btnCancel_Click(ActionParams $params)
    {
            $this->redirectToListPage();
    }

    /**
     * Handles the click event for the delete button and displays a modal dialog box.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    public function btnDelete_Click(ActionParams $params)
    {
        $this->dlgModal1->showDialogBox();
    }

    /**
     * Handles the click event of the btnGoToTargetGroup button. Sets session variables and redirects the user to the events calendar list page.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    public function btnGoToTargetGroup_Click(ActionParams $params)
    {
        $_SESSION['target_id'] = $this->intId;
        $_SESSION['target_group'] = $this->intGroup;
        Application::redirect('events_calendar_list.php#targetCroupList_tab');
    }

    /**
     * Handles the click event for the btnGoToChanges button, sets session variables, and redirects to the specified URL.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    public function btnGoToChanges_Click(ActionParams $params)
    {
        $_SESSION['events_changes'] = $this->intId;
        $_SESSION['events_group'] = $this->intGroup;
        Application::redirect('categories_manager.php#eventsChanges_tab');
    }

    /**
     * Handles the click event for the Go To Settings button. Sets session variables
     * for the event ID and group, then redirects the user to the settings manager page.
     *
     * @param ActionParams $params Parameters related to the action triggering this method.
     * @return void
     */
    public function btnGoToSettings_Click(ActionParams $params)
    {
        $_SESSION['events_id'] = $this->intId;
        $_SESSION['events_group'] = $this->intGroup;

        Application::redirect('settings_manager.php#eventsSettings_tab');
    }

    /**
     * Redirects the user to the events calendar list page and clears specific session variables.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        Application::redirect('events_calendar_list.php');
        unset($_SESSION['target_id']);
        unset($_SESSION['target_group']);
    }
}
SampleForm::run('SampleForm');