<?php
    require('qcubed.inc.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\CellClick;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\KeyUp;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Event\DialogButton;
    use QCubed\Action\Ajax;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\Query\QQ;
    use QCubed\Html;

    /**
     * Class EventCalendarEditForm
     *
     * Represents the form for creating or editing an Event Calendar, which includes various input fields,
     * labels, buttons, and modals for user interactions. This class handles the initialization, display,
     * and processing of the event-related data, such as event details, schedule, and target groups.
     * It uses a variety of input types and external data source connections to populate controls dynamically.
     *
     * Core functionality:
     * - Managing form inputs for event information (e.g., year, title, group, date).
     * - Handling modal dialogues and toast notifications for user feedback.
     * - Supporting dynamic fetching of data from backend sources (e.g., target group, events settings).
     * - Defining actions for input interactions (e.g., AJAX actions for dropdowns).
     *
     * Important Notes:
     * - The form deletes existing session data related to events if found.
     * - Incorporates user session handling through `intLoggedUserId` for operations tied to logged users.
     * - Creates internal references to EventCalendar, EventsSettings, and FrontendLinks instances for form data.
     * - Utilizes QPlugin controls and layout helpers for the user interface.
     *
     * Error Tracking:
     * - The `$errors` array is used to store form-related error messages during input validation or processing.
     *
     * Methods Defined:
     * - formCreate: Initializes the form, sets up data bindings, and clears irrelevant session data.
     * - createInputs: Dynamically sets up form input fields, including configuration and associated actions.
     * - createButtons: Prepares buttons for various user actions on the form.
     * - createToastr: Configures toast notifications for runtime success or error messages.
     * - createModals: Sets up modal dialogues for confirmation or additional details.
     *
     * Properties:
     * - Contains various protected properties for labels, inputs, calendars, lists, buttons, and modals.
     * - Includes internal properties for data handling (`intId`, `intGroup`, `objEventsCalendar`, etc.).
     * - Stores UI-related properties like null labels for dynamic list boxes.
     */
    class EventCalendarEditForm extends Form
    {
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

        protected Bs\Modal $dlgModal1;
        protected Bs\Modal $dlgModal2;
        protected Bs\Modal $dlgModal3;
        protected Bs\Modal $dlgModal4;
        protected Bs\Modal $dlgModal5;
        protected Bs\Modal $dlgModal6;

        protected Q\Plugin\Control\Label $lblYear;
        protected Q\Plugin\YearPicker $txtYear;

        protected Q\Plugin\Control\Label $lblTargetGroup;
        protected Q\Plugin\Select2 $lstTargetGroup;

        protected Q\Plugin\Control\Label $lblTitle;
        protected Bs\TextBox $txtTitle;

        protected Q\Plugin\Control\Label $lblChanges;
        protected Q\Plugin\Select2 $lstChanges;

        protected Q\Plugin\Control\Label $lblEventPlace;
        protected Bs\TextBox $txtEventPlace;

        protected Q\Plugin\Control\Label $lblEventDate;
        protected Q\Plugin\DateTimePicker $calBeginningEvent;
        protected Q\Plugin\DateTimePicker $calEndEvent;
        protected Q\Plugin\ClockPicker $calStartTime;
        protected Q\Plugin\ClockPicker $calEndTime;

        protected Q\Plugin\Control\Label $lblWebsiteUrl;
        protected Bs\TextBox $txtWebsiteUrl;
        protected Q\Plugin\Select2 $lstWebsiteTargetType;

        protected Q\Plugin\Control\Label $lblFacebookUrl;
        protected Bs\TextBox $txtFacebookUrl;
        protected Q\Plugin\Select2 $lstFacebookTargetType;

        protected Q\Plugin\Control\Label $lblInstagramUrl;
        protected Bs\TextBox $txtInstagramUrl;
        protected Q\Plugin\Select2 $lstInstagramTargetType;

        protected Q\Plugin\Control\Label $lblContact;
        protected Bs\TextBox $txtOrganizers;
        protected Bs\TextBox $txtPhone;
        protected Q\Plugin\Control\MultipleEmailTextBox $txtEmail;

        protected Q\Plugin\Control\Label $lblGroupTitle;
        protected Q\Plugin\Select2 $lstGroupTitle;

        protected Q\Plugin\Control\Label $lblTitleSlug;
        protected Q\Plugin\Control\Label $txtTitleSlug;

        protected Q\Plugin\Control\Label $lblDocumentLink;
        protected Bs\Button $btnDocumentLink;
        protected Q\Plugin\Control\Label $txtDocumentLink;
        protected Bs\TextBox $txtLinkTitle;
        protected Bs\Button $btnDownloadCancel;
        protected Bs\Button $btnDownloadSave;

        protected Q\Plugin\Control\VauuTable $dtgSelectedList;
        protected Bs\TextBox $txtSelectedTitle;
        protected Q\Plugin\Control\RadioList $lstSelectedStatus;
        protected Bs\Button $btnSelectedSave;
        protected Bs\Button $btnSelectedCheck;
        protected Bs\Button $btnSelectedDelete;
        protected Bs\Button $btnSelectedCancel;

        protected Q\Plugin\Control\Label $lblInformation;
        protected Q\Plugin\CKEditor $txtInformation;

        protected Q\Plugin\Control\Label $lblSchedule;
        protected Q\Plugin\CKEditor $txtSchedule;

        protected Q\Plugin\Control\Label $lblPostDate;
        protected Bs\Label $calPostDate;

        protected Q\Plugin\Control\Label $lblPostUpdateDate;
        protected Bs\Label $calPostUpdateDate;

        protected Q\Plugin\Control\Label $lblAuthor;
        protected Bs\Label $txtAuthor;

        protected Q\Plugin\Control\Label $lblUsersAsEditors;
        protected Bs\Label $txtUsersAsEditors;

        protected Q\Plugin\MediaFinder $objMediaFinder;
        protected Q\Plugin\Control\Label $lblPictureDescription;
        protected Bs\TextBox $txtPictureDescription;

        protected Q\Plugin\Control\Label $lblAuthorSource;
        protected Bs\TextBox $txtAuthorSource;

        protected Q\Plugin\Control\Label $lblStatus;
        protected Q\Plugin\Control\RadioList $lstStatus;

        protected Bs\Button $btnSave;
        protected Bs\Button $btnSaving;
        protected Bs\Button $btnDelete;
        protected Bs\Button $btnCancel;

        protected Bs\Button $btnGoToTargetGroup;
        protected Bs\Button $btnGoToChanges;
        protected Bs\Button $btnGoToSettings;

        protected int $intId;
        protected int $intLoggedUserId;
        protected object $objEventsSettings;
        protected object $objEventsCalendar;
        protected object $objFrontendLinks;
        protected ?array $objEventFiles = null;
        protected int $intGroup;
        protected int $intDocument;

        protected ?object $objChangesCondition = null;
        protected ?array $objChangesClauses = null;

        protected ?object $objTargetGroupCondition = null;
        protected ?array $objTargetGroupClauses = null;

        protected ?string $strWebsiteTargetTypeNullLabel = '';
        protected ?string $strFacebookTargetTypeNullLabel = '';
        protected ?string $strInstagramTargetTypeNullLabel = '';

        protected array $errors = []; // Array for tracking errors
        protected ?array $InvalidEmailList = null;

        /**
         * Initializes and configures the form by setting up necessary session cleanup, loading data,
         * and creating input elements, buttons, toasts, and modals. Handles initialization of key data objects
         * and prepares the form for user interaction.
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
            if (!empty($this->intId)) {
                $this->objEventsCalendar = EventsCalendar::load($this->intId);
            }

            $this->intGroup = Application::instance()->context()->queryStringItem('group');
            $this->objEventsSettings = EventsSettings::loadByIdFromEventsSettings($this->intGroup);
            $this->objFrontendLinks = FrontendLinks::loadByIdFromFrontedLinksId($this->intId);

            // Deleting sessions, if any.
            if (!empty($_SESSION['events_id']) || !empty($_SESSION['events_group'])) {
                unset($_SESSION['events_id']);
                unset( $_SESSION['events_group']);
            } else if (!empty($_SESSION['target_id']) || !empty($_SESSION['target_group'])) {
                unset($_SESSION['target_id']);
                unset($_SESSION['target_group']);
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
            $this->intLoggedUserId = 3;

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
            $this->createTable();
            $this->popupViewer();
            $this->updateBorderOnInvalidEmails();
        }

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
         * Initializes and creates input fields and labels for the events calendar form.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function createInputs(): void
        {
            $this->lblYear = new Q\Plugin\Control\Label($this);
            $this->lblYear->Text = t('Year');
            $this->lblYear->addCssClass('col-md-3');
            $this->lblYear->setCssStyle('font-weight', 400);
            $this->lblYear->Required = true;

            $this->txtYear = new Q\Plugin\YearPicker($this);
            $this->txtYear->Text = $this->objEventsCalendar->Year ?? '';
            $this->txtYear->Language = 'et';
            $this->txtYear->TodayBtn = true;
            $this->txtYear->ClearBtn = true;
            $this->txtYear->AutoClose = true;
            $this->txtYear->setHtmlAttribute('autocomplete', 'off');
            $this->txtYear->addCssClass('calendar-trigger');
            $this->txtYear->addAction(new Change(), new Ajax('txtYear_Change'));
            $this->txtYear->addAction(new Q\Plugin\Event\Clear(), new Ajax('txtYear_Clear'));

            $this->lblTitle = new Q\Plugin\Control\Label($this);
            $this->lblTitle->Text = t('Event title');
            $this->lblTitle->addCssClass('col-md-3');
            $this->lblTitle->setCssStyle('font-weight', 400);
            $this->lblTitle->Required = true;

            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Event title');
            $this->txtTitle->Text = $this->objEventsCalendar->Title ?? '';
            $this->txtTitle->MaxLength = EventsCalendar::TITLE_MAX_LENGTH;
            $this->txtTitle->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtTitle->addAction(new EnterKey(), new Terminate());
            $this->txtTitle->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtTitle->addAction(new EscapeKey(), new Terminate());

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

            $this->lstChanges->AddAction(new Change(), new Ajax('lstChanges_Change'));

            $this->lblTargetGroup = new Q\Plugin\Control\Label($this);
            $this->lblTargetGroup->Text = t('Target Group');
            $this->lblTargetGroup->addCssClass('col-md-3');
            $this->lblTargetGroup->setCssStyle('font-weight', 400);
            //$this->lblTargetGroup->Required = true;

            $this->lstTargetGroup = new Q\Plugin\Select2($this);
            $this->lstTargetGroup->MinimumResultsForSearch = -1;
            $this->lstTargetGroup->ContainerWidth = 'resolve';
            $this->lstTargetGroup->Theme = 'web-vauu';
            $this->lstTargetGroup->addCssClass('js-target-group');
            $this->lstTargetGroup->Width = '90%';
            $this->lstTargetGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);
            $this->lstTargetGroup->addItems($this->lstTargetGroup_GetItems());
            $this->lstTargetGroup->SelectedValue = $this->objEventsCalendar->TargetGroupId;

            if (TargetGroupOfCalendar::countAll() == 0 || TargetGroupOfCalendar::countByIsEnabled(1) == 0) {
                $this->lstTargetGroup->Enabled = false;
            } else {
                $this->lstTargetGroup->Enabled = true;
            }

            $this->lstTargetGroup->addAction(new Change(), new Ajax('lstTargetGroup_Change'));

            $this->lblEventPlace = new Q\Plugin\Control\Label($this);
            $this->lblEventPlace->Text = t('Event place');
            $this->lblEventPlace->addCssClass('col-md-3');
            $this->lblEventPlace->setCssStyle('font-weight', 400);
            $this->lblEventPlace->Required = true;

            $this->txtEventPlace = new Bs\TextBox($this);
            $this->txtEventPlace->Placeholder = t('Event place');
            $this->txtEventPlace->Text = $this->objEventsCalendar->EventPlace ?? '';
            $this->txtEventPlace->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtEventPlace->addAction(new EnterKey(), new Terminate());
            $this->txtEventPlace->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtEventPlace->addAction(new EscapeKey(), new Terminate());

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
            $this->txtWebsiteUrl->Text = $this->objEventsCalendar->WebsiteUrl ?? '';
            $this->txtWebsiteUrl->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtWebsiteUrl->addAction(new EnterKey(), new Terminate());
            $this->txtWebsiteUrl->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtWebsiteUrl->addAction(new EscapeKey(), new Terminate());
            $this->txtWebsiteUrl->AddAction(new KeyUp(), new Ajax('lstWebsiteTargetType_KeyUp'));

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
            $this->txtFacebookUrl->Text = $this->objEventsCalendar->FacebookUrl ?? '';
            $this->txtFacebookUrl->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtFacebookUrl->addAction(new EnterKey(), new Terminate());
            $this->txtFacebookUrl->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtFacebookUrl->addAction(new EscapeKey(), new Terminate());
            $this->txtFacebookUrl->AddAction(new KeyUp(), new Ajax('lstFacebookTargetType_KeyUp'));

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
            $this->txtInstagramUrl->Text = $this->objEventsCalendar->InstagramUrl ?? '';
            $this->txtInstagramUrl->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtInstagramUrl->addAction(new EnterKey(), new Terminate());
            $this->txtInstagramUrl->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtInstagramUrl->addAction(new EscapeKey(), new Terminate());
            $this->txtInstagramUrl->AddAction(new KeyUp(), new Ajax('lstInstagramTargetType_KeyUp'));

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
            $this->txtOrganizers->Text = $this->objEventsCalendar->Organizers ?? '';
            $this->txtOrganizers->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtOrganizers->addAction(new EnterKey(), new Terminate());
            $this->txtOrganizers->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtOrganizers->addAction(new EscapeKey(), new Terminate());

            $this->txtPhone = new Bs\TextBox($this);
            $this->txtPhone->Placeholder = t('+372 1234 5678');
            $this->txtPhone->Text = $this->objEventsCalendar->Phone ?? '';
            $this->txtPhone->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtPhone->addAction(new EnterKey(), new Terminate());
            $this->txtPhone->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtPhone->addAction(new EscapeKey(), new Terminate());

            $this->txtEmail = new Q\Plugin\Control\MultipleEmailTextBox($this);
            $this->txtEmail->Width = '100%';
            $this->txtEmail->Placeholder = t('Email');
            $this->txtEmail->Text = $this->objEventsCalendar->Email ?? '';
            $this->txtEmail->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtEmail->addAction(new EnterKey(), new Terminate());
            $this->txtEmail->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtEmail->addAction(new EscapeKey(), new Terminate());

            $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
            $this->lblGroupTitle->Text = t('Event group');
            $this->lblGroupTitle->addCssClass('col-md-3');
            $this->lblGroupTitle->setCssStyle('font-weight', 400);

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->ContainerWidth = 'resolve';
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '90%';
            $this->lstGroupTitle->setCssStyle('float', 'left');
            $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroupTitle->addAction(new Change(), new Ajax('lstGroupTitle_Change'));

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
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblDocumentLink = new Q\Plugin\Control\Label($this);
            $this->lblDocumentLink->Text = t('Document(s) links');
            $this->lblDocumentLink->addCssClass('col-md-3');
            $this->lblDocumentLink->setCssStyle('font-weight', 400);

            $this->btnDocumentLink = new Bs\Button($this);
            $this->btnDocumentLink->Text = t('Search file...');
            $this->btnDocumentLink->CssClass = 'btn btn-default';
            $this->btnDocumentLink->addWrapperCssClass('center-button');
            $this->btnDocumentLink->setCssStyle('float', 'left');
            $this->btnDocumentLink->CausesValidation = false;
            $this->btnDocumentLink->setDataAttribute('popup', 'popup');
            $this->btnDocumentLink->addAction(new Click(), new Ajax('btnDocumentLink_Click'));

            $this->txtDocumentLink = new Q\Plugin\Control\Label($this);
            $this->txtDocumentLink->Text = t('No documents available...');
            $this->txtDocumentLink->setCssStyle('color', '#999');
            $this->txtDocumentLink->setCssStyle('float', 'left');
            $this->txtDocumentLink->setCssStyle('margin-left', '15px');

            if (EventFiles::countByEventsCalendarGroupId($this->intId) === 0) {
                $this->txtDocumentLink->Display = true;
            } else {
                $this->txtDocumentLink->Display = false;
            }

            $this->txtLinkTitle = new Bs\TextBox($this);
            $this->txtLinkTitle->Placeholder = t('Link title');
            $this->txtLinkTitle->Width = '60%';
            $this->txtLinkTitle->setCssStyle('float', 'left');
            $this->txtLinkTitle->Display = false;

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

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblInformation = new Q\Plugin\Control\Label($this);
            $this->lblInformation->Text = t('Information');
            $this->lblInformation->setCssStyle('font-weight', 'bold');

            $this->txtInformation = new Q\Plugin\CKEditor($this);
            $this->txtInformation->Text = $this->objEventsCalendar->Information ?? '';
            $this->txtInformation->Configuration = 'ckConfig';
            $this->txtInformation->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtInformation->addAction(new EnterKey(), new Terminate());
            $this->txtInformation->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtInformation->addAction(new EscapeKey(), new Terminate());

            $this->lblSchedule = new Q\Plugin\Control\Label($this);
            $this->lblSchedule->Text = t('Schedule');
            $this->lblSchedule->setCssStyle('font-weight', 'bold');

            $this->txtSchedule = new Q\Plugin\CKEditor($this);
            $this->txtSchedule->Text = $this->objEventsCalendar->Schedule ?? '';
            $this->txtSchedule->Configuration = 'ckConfig';
            $this->txtSchedule->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtSchedule->addAction(new EnterKey(), new Terminate());
            $this->txtSchedule->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtSchedule->addAction(new EscapeKey(), new Terminate());

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
            $this->objMediaFinder->PopupUrl = dirname(QCUBED_FILEMANAGER_ASSETS_URL) . "/examples/finder.php";
            $this->objMediaFinder->EmptyImageAlt = t("Choose a picture");
            $this->objMediaFinder->SelectedImageAlt = t("Selected picture");

            $this->objMediaFinder->SelectedImageId = $this->objEventsCalendar->getPictureId() ?? null;

            if ($this->objMediaFinder->SelectedImageId !== null) {
                $objFiles = Files::loadById($this->objMediaFinder->SelectedImageId);
                $this->objMediaFinder->SelectedImagePath = $this->objMediaFinder->TempUrl . $objFiles->getPath();
                $this->objMediaFinder->SelectedImageName = $objFiles->getName();
            }

            $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageSave(), new Ajax( 'imageSave_Push'));
            $this->objMediaFinder->addAction(new Q\Plugin\Event\ImageDelete(), new Ajax('imageDelete_Push'));

            $this->lblPictureDescription = new Q\Plugin\Control\Label($this);
            $this->lblPictureDescription->Text = t('Picture description');
            $this->lblPictureDescription->setCssStyle('font-weight', 'bold');

            $this->txtPictureDescription = new Bs\TextBox($this);
            $this->txtPictureDescription->Text = $this->objEventsCalendar->PictureDescription ?? '';
            $this->txtPictureDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtPictureDescription->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
            $this->txtPictureDescription->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtPictureDescription->addAction(new EnterKey(), new Terminate());
            $this->txtPictureDescription->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtPictureDescription->addAction(new EscapeKey(), new Terminate());

            $this->lblAuthorSource = new Q\Plugin\Control\Label($this);
            $this->lblAuthorSource->Text = t('Author/source');
            $this->lblAuthorSource->setCssStyle('font-weight', 'bold');

            $this->txtAuthorSource = new Bs\TextBox($this);
            $this->txtAuthorSource->Text = $this->objEventsCalendar->AuthorSource ?? '';
            $this->txtAuthorSource->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
            $this->txtAuthorSource->AddAction(new EnterKey(), new Ajax('btnSave_Click'));
            $this->txtAuthorSource->addAction(new EnterKey(), new Terminate());
            $this->txtAuthorSource->AddAction(new EscapeKey(), new Ajax('itemEscape_Click'));
            $this->txtAuthorSource->addAction(new EscapeKey(), new Terminate());

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
            $this->lstStatus->AddAction(new Change(), new Ajax('lstStatus_Change'));
        }

        /**
         * Creates and initializes various buttons with specific properties, styles, and actions.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->addAction(new Click(), new Ajax('btnSave_Click'));

            $this->btnSaving = new Bs\Button($this);
            $this->btnSaving->Text = t('Save and close');
            $this->btnSaving->CssClass = 'btn btn-darkblue';
            $this->btnSaving->addWrapperCssClass('center-button');
            $this->btnSaving->PrimaryButton = true;
            $this->btnSaving->addAction(new Click(), new Ajax('btnSaveClose_Click'));

            $this->btnDelete = new Bs\Button($this);
            $this->btnDelete->Text = t('Delete');
            $this->btnDelete->CssClass = 'btn btn-danger';
            $this->btnDelete->addWrapperCssClass('center-button');
            $this->btnDelete->CausesValidation = false;
            $this->btnDelete->addAction(new Click(), new Ajax('btnDelete_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Back');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new Ajax('btnCancel_Click'));

            $this->btnGoToTargetGroup = new Bs\Button($this);
            $this->btnGoToTargetGroup->Tip = true;
            $this->btnGoToTargetGroup->ToolTip = t('Go the target groups change manager');
            $this->btnGoToTargetGroup->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToTargetGroup->CssClass = 'btn btn-default';
            $this->btnGoToTargetGroup->setCssStyle('float', 'right');
            $this->btnGoToTargetGroup->addWrapperCssClass('center-button');
            $this->btnGoToTargetGroup->CausesValidation = false;
            $this->btnGoToTargetGroup->addAction(new Click(), new Ajax('btnGoToTargetGroup_Click'));

            $this->btnGoToChanges = new Bs\Button($this);
            $this->btnGoToChanges->Tip = true;
            $this->btnGoToChanges->ToolTip = t('Go to the events change manager');
            $this->btnGoToChanges->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToChanges->CssClass = 'btn btn-default';
            $this->btnGoToChanges->setCssStyle('float', 'right');
            $this->btnGoToChanges->addWrapperCssClass('center-button');
            $this->btnGoToChanges->CausesValidation = false;
            $this->btnGoToChanges->addAction(new Click(), new Ajax('btnGoToChanges_Click'));

            $this->btnGoToSettings = new Bs\Button($this);
            $this->btnGoToSettings->Tip = true;
            $this->btnGoToSettings->ToolTip = t('Go to the events settings manager');
            $this->btnGoToSettings->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnGoToSettings->CssClass = 'btn btn-default';
            $this->btnGoToSettings->setCssStyle('float', 'right');
            $this->btnGoToSettings->addWrapperCssClass('center-button');
            $this->btnGoToSettings->CausesValidation = false;
            $this->btnGoToSettings->addAction(new Click(), new Ajax('btnGoToSettings_Click'));

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->btnDownloadSave = new Bs\Button($this);
            $this->btnDownloadSave->Text = t('Save');
            $this->btnDownloadSave->CssClass = 'btn btn-orange';
            $this->btnDownloadSave->addWrapperCssClass('center-button');
            $this->btnDownloadSave->setCssStyle('float', 'left');
            $this->btnDownloadSave->setCssStyle('margin-left', '10px');
            $this->btnDownloadSave->CausesValidation = false;
            $this->btnDownloadSave->Display = false;
            $this->btnDownloadSave->addAction(new Click(), new Ajax('btnDownloadSave_Click'));

            $this->btnDownloadCancel = new Bs\Button($this);
            $this->btnDownloadCancel->Text = t('Cancel');
            $this->btnDownloadCancel->CssClass = 'btn btn-default';
            $this->btnDownloadCancel->addWrapperCssClass('center-button');
            $this->btnDownloadCancel->setCssStyle('float', 'left');
            $this->btnDownloadCancel->setCssStyle('margin-left', '5px');
            $this->btnDownloadCancel->CausesValidation = false;
            $this->btnDownloadCancel->Display = false;
            $this->btnDownloadCancel->addAction(new Click(), new Ajax('btnDownloadCancel_Click'));

            $this->btnSelectedSave = new Bs\Button($this);
            $this->btnSelectedSave->Text = t('Update');
            $this->btnSelectedSave->CssClass = 'btn btn-orange';
            $this->btnSelectedSave->addWrapperCssClass('center-button');
            $this->btnSelectedSave->setCssStyle('margin-left', '10px');
            $this->btnSelectedSave->Display = false;
            $this->btnSelectedSave->addAction(new Click(), new Ajax('btnSelectedSave_Click'));

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

            $this->btnSelectedCancel = new Bs\Button($this);
            $this->btnSelectedCancel->Text = t('Cancel');
            $this->btnSelectedCancel->CssClass = 'btn btn-default';
            $this->btnSelectedCancel->addWrapperCssClass('center-button');
            $this->btnSelectedCancel->CausesValidation = false;
            $this->btnSelectedCancel->Display = false;
            $this->btnSelectedCancel->addAction(new Click(), new Ajax('btnSelectedCancel_Click'));

            if (!empty( $_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
                $this->btnDocumentLink->Display = false;
                $this->txtDocumentLink->Display = false;
                $this->txtLinkTitle->Display = true;
                $this->btnDownloadSave->Display = true;
                $this->btnDownloadCancel->Display = true;

                $filteredDataName = pathinfo($_SESSION["data_name"]);
                $this->txtLinkTitle->Text = $filteredDataName['filename'];
            }
        }

        /**
         * Creates and configures multiple Toastr instances with specific alert types, positions, messages, and other
         * properties.
         *
         * This method initializes various Toastr alerts to indicate success, error, or informational messages. Each
         * toastr instance is configured with options such as alert type, position, message content, progress bar
         * settings, HTML escape, and timeout. These alerts are designed to provide user feedback for different
         * scenarios, including saving, modifying, or restoring data, as well as displaying error messages and
         * validation alerts.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
            $this->dlgToastr1->ProgressBar = true;
            $this->dlgToastr1->EscapeHtml = false;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('<strong>Sorry</strong>, these fields must be filled!');
            $this->dlgToastr2->ProgressBar = true;
            $this->dlgToastr2->EscapeHtml = false;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Well done!</strong> The start date for this post has been saved or changed.');
            $this->dlgToastr3->ProgressBar = true;
            $this->dlgToastr3->EscapeHtml = false;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this start date does not exist.</p>Please enter the start date!');
            $this->dlgToastr4->ProgressBar = true;
            $this->dlgToastr4->EscapeHtml = false;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('<strong>Well done!</strong> The end date for this post has been saved or changed.');
            $this->dlgToastr5->ProgressBar = true;
            $this->dlgToastr5->EscapeHtml = false;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('<p style=\"margin-bottom: 2px;\">Start date must be smaller then end date!</p><strong>Try to do it right again!</strong>');
            $this->dlgToastr6->ProgressBar = true;
            $this->dlgToastr6->TimeOut = 10000;
            $this->dlgToastr6->EscapeHtml = false;

            $this->dlgToastr7 = new Q\Plugin\Toastr($this);
            $this->dlgToastr7->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr7->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr7->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this start date does not exist.</p>Please enter at least the start date!');
            $this->dlgToastr7->ProgressBar = true;
            $this->dlgToastr7->EscapeHtml = false;

            $this->dlgToastr8 = new Q\Plugin\Toastr($this);
            $this->dlgToastr8->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr8->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr8->Message = t('<strong>Well done!</strong> The start time for this post has been saved or changed.');
            $this->dlgToastr8->ProgressBar = true;
            $this->dlgToastr8->EscapeHtml = false;

            $this->dlgToastr9 = new Q\Plugin\Toastr($this);
            $this->dlgToastr9->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr9->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr9->Message = t('<strong>Well done!</strong> The end time for this post has been saved or changed.');
            $this->dlgToastr9->ProgressBar = true;
            $this->dlgToastr9->EscapeHtml = false;

            $this->dlgToastr10 = new Q\Plugin\Toastr($this);
            $this->dlgToastr10->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr10->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr10->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this end date does not exist.</p>Please enter the end date!');
            $this->dlgToastr10->ProgressBar = true;
            $this->dlgToastr10->EscapeHtml = false;

            $this->dlgToastr11 = new Q\Plugin\Toastr($this);
            $this->dlgToastr11->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr11->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr11->Message = t('<strong>Sorry</strong>, failed to save or edit post!');
            $this->dlgToastr11->ProgressBar = true;

            $this->dlgToastr12 = new Q\Plugin\Toastr($this);
            $this->dlgToastr12->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr12->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr12->Message = t('<strong>Well done!</strong> Updates to some records for this post were discarded, and the record has been restored!');
            $this->dlgToastr12->ProgressBar = true;

            $this->dlgToastr13 = new Q\Plugin\Toastr($this);
            $this->dlgToastr13->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr13->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr13->Message = t('<strong>Sorry</strong>, this field is required!');
            $this->dlgToastr13->ProgressBar = true;
            $this->dlgToastr13->EscapeHtml = false;

            $this->dlgToastr14 = new Q\Plugin\Toastr($this);
            $this->dlgToastr14->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr14->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            //$this->dlgToastr14->Message = t('<p style=\"margin-bottom: 2px;\">Invalid or unsupported email address(es): %s</p>Please correct the invalid addresses!');
            $this->dlgToastr14->ProgressBar = true;
            $this->dlgToastr14->EscapeHtml = false;

            $this->dlgToastr15 = new Q\Plugin\Toastr($this);
            $this->dlgToastr15->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr15->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr15->Message = t('<p><strong>Note:</strong> You cannot leave the year field empty!</p><p>It will be automatically restored.</p>');
            $this->dlgToastr15->ProgressBar = true;
            $this->dlgToastr15->EscapeHtml = false;
        }

        /**
         * Creates and configures modal dialogs for various actions such as deleting, moving, or modifying events.
         *
         * @return void
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this event?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone.</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
                ['class' => 'btn btn-default']);
            $this->dlgModal1->addAction(new DialogButton(), new Ajax('deleteItem_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move this event from this event group to another event group?</p>');
            $this->dlgModal2->Title = t('Warning');
            $this->dlgModal2->HeaderClasses = 'btn-danger';
            $this->dlgModal2->addButton(t("I accept"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal2->addCloseButton(t("I'll cancel"));
            $this->dlgModal2->addAction(new DialogButton(), new Ajax('moveItem_Click'));

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event has now been made public!</p>');
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal5 = new Bs\Modal($this);
            $this->dlgModal5->Title = t("Success");
            $this->dlgModal5->HeaderClasses = 'btn-success';
            $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event is now a draft!</p>');
            $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal6 = new Bs\Modal($this);
            $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this document?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone.</p>');
            $this->dlgModal6->Title = t('Warning');
            $this->dlgModal6->HeaderClasses = 'btn-danger';
            $this->dlgModal6->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal6->addButton(t("I'll cancel"), "no-pass", false, false, null,
                ['class' => 'btn btn-default']);
            $this->dlgModal6->addAction(new DialogButton(), new Ajax('deleteDocument_Click'));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and configures a new VauuTable for displaying selected list data.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createTable(): void
        {
            $this->dtgSelectedList = new Q\Plugin\Control\VauuTable($this);
            $this->dtgSelectedList->CssClass = "table vauu-table table-hover";
            $this->dtgSelectedList->UseAjax = true;
            $this->dtgSelectedList->ShowHeader = false;
            $this->dtgSelectedList->setDataBinder('dtgSelectedList_Bind');
            $this->dtgSelectedList->RowParamsCallback = [$this, 'dtgSelectedList_GetRowParams'];
            $this->dtgSelectedList->addAction(new CellClick(0, null, CellClick::rowDataValue('value')),
                new Ajax('dtgSelectedList_Click'));

            $col = $this->dtgSelectedList->createNodeColumn(t('Document link'), QQN::EventFiles()->Title);

            $col = $this->dtgSelectedList->createNodeColumn(t("Status"), QQN::EventFiles()->StatusObject);
            $col->HtmlEntities = false;

            $col = $this->dtgSelectedList->createNodeColumn(t("Post date"), QQN::EventFiles()->PostDate);
            $col->Format = 'DD.MM.YYYY';

            $col = $this->dtgSelectedList->createNodeColumn(t("Post update date"), QQN::EventFiles()->PostUpdateDate);
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
         * @throws Caller
         * @throws InvalidCast
         */
        public function dtgSelectedList_Bind(): void
        {
            $this->dtgSelectedList->DataSource = EventFiles::loadArrayByEventsCalendarGroupId($this->intId, QQ::Clause(QQ::orderBy(QQN::EventFiles()->Id)));
        }

        /**
         * Retrieves row parameters for a selected item list.
         *
         * @param object $objRowObject The object representing the current row.
         * @param int $intRowIndex The index of the row.
         *
         * @return array An associative array of row parameters.
         */
        public function dtgSelectedList_GetRowParams(object $objRowObject, int $intRowIndex): array
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
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function dtgSelectedList_Click(ActionParams $params): void
        {
            $this->intDocument = intval($params->ActionParameter);
            $objEventFile = EventFiles::loadById($this->intDocument);
            $objFile = Files::loadById($objEventFile->getFilesId());

            //this->dtgSelectedList->addCssClass('disabled');
            $this->btnDocumentLink->Enabled = false;

            $this->txtSelectedTitle->Display = true;
            $this->lstSelectedStatus->Display = true;
            $this->btnSelectedSave->Display = true;
            $this->btnSelectedCheck->Display = true;
            $this->btnSelectedDelete->Display = true;
            $this->btnSelectedCancel->Display = true;

            $this->txtSelectedTitle->Text = $objEventFile->getTitle();
            $this->lstSelectedStatus->SelectedValue = $objEventFile->getStatus();
            $this->txtSelectedTitle->setDataAttribute('open', 'true');
            $this->txtSelectedTitle->setDataAttribute('view', APP_UPLOADS_URL . $objFile->getPath());
            $this->txtSelectedTitle->focus();
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
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstTargetGroup_GetItems(): array
        {
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
                //  use it only on a specific page. You just have to use the style.

                if ($objTargetGroup->IsEnabled == 2) {
                    $objListItem->Disabled = true;
                }
                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Retrieves a list of items for the change dropdown or list.
         * Iterates through a cursor of `EventsChanges` objects and creates a list of items,
         * optionally setting selected and disabled attributes based on certain conditions.
         *
         * @return ListItem[] An array of ListItem objects representing changes, with
         *                     possible selected and disabled attributes set.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstChanges_GetItems(): array
        {
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
                //  use it only on a specific page. You just have to use the style.

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
        public function lstWebsiteTargetType_GetItems(): array
        {
            return TargetType::nameArray();
        }

        /**
         * Retrieves an array of Facebook target types.
         *
         * @return array Returns an associative array of target type names.
         */
        public function lstFacebookTargetType_GetItems(): array
        {
            return TargetType::nameArray();
        }

        /**
         * Retrieves a list of target type names for Instagram.
         *
         * @return array An array of target type names fetched from the TargetType name array.
         */
        public function lstInstagramTargetType_GetItems(): array
        {
            return TargetType::nameArray();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Clears the text value of the txtYear field if it is empty and sets its HTML attribute 'required' to 'required'.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         * @return void
         */
        protected function txtYear_Clear(ActionParams $params): void
        {
            if (!$this->txtYear->Text) {
                $this->txtYear->Text = '';
                $this->txtYear->setHtmlAttribute('required', 'required');
            }
        }

        /**
         * Handles the change event for the txtYear input field. Updates the year in the events calendar
         * and triggers necessary UI updates if a valid year is provided. If no year is provided,
         * sets the input field to required and triggers a notification.
         *
         * @param ActionParams $params Parameters associated with the change event.
         *
         * @return void
         * @throws Caller
         */
        protected function txtYear_Change(ActionParams $params): void
        {
            $objEventsFiles = EventFiles::loadArrayByEventsCalendarGroupId($this->intId);

            if ($this->txtYear->Text) {
                $this->objEventsCalendar->setYear($this->txtYear->Text);
                $this->objEventsCalendar->save();
                $this->objEventsCalendar->saveEvent($this->objEventsCalendar->getYear(), $this->objEventsCalendar->getTitle(), $this->objEventsSettings->getTitleSlug());

                if ($objEventsFiles) {
                    foreach ($objEventsFiles as $objEventsFile) {
                        $objEventsFile = EventFiles::loadById($objEventsFile->getId());
                        $objEventsFile->setYear($this->txtYear->Text);
                        $objEventsFile->save();
                    }
                }

                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objEventsCalendar->getTitleSlug();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);

                $this->dlgToastr1->notify();

                $this->renderActionsWithId();
                $this->refreshDisplay();
            } else {
                $this->txtYear->Text = $this->objEventsCalendar->getYear();
                $this->txtYear->refresh();
                $this->dlgToastr15->notify();
            }
        }

        /**
         * Handles the change event for the lstChanges list.
         *
         * @param ActionParams $params Parameters passed to the action.
         *
         * @return void
         * @throws Caller
         */
        protected function lstChanges_Change(ActionParams $params): void
        {
            if ($this->lstChanges->SelectedValue !== null) {
                $this->objEventsCalendar->setEventsChangesId($this->lstChanges->SelectedValue);
            } else {
                $this->objEventsCalendar->setEventsChangesId(null);
            }

            $this->objEventsCalendar->save();

            $this->dlgToastr1->notify();

            EventsChanges::updateAllChangeLockStates();
            $this->renderActionsWithId();
            $this->refreshDisplay();
        }

        /**
         * Updates the target group information in the calendar object based on the selected value and name,
         * saves the changes, triggers a notification, and refreshes relevant UI components.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void
         * @throws Caller
         */
        protected function lstTargetGroup_Change(ActionParams $params): void
        {
            if ($this->lstTargetGroup->SelectedValue !== null) {
                $this->objEventsCalendar->setTargetGroupId($this->lstTargetGroup->SelectedValue);
            } else {
                $this->objEventsCalendar->setTargetGroupId(null);
            }

            $this->objEventsCalendar->save();

            $this->dlgToastr1->notify();

            TargetGroupOfCalendar::updateAllTargetGroupLockStates();
            $this->renderActionsWithId();
            $this->refreshDisplay();
        }

        /**
         * Updates the beginning event date of the calendar based on the provided parameters.
         * If a valid date is provided, it sets the beginning event date and notifies the user.
         * If no date is provided, it clears the date, updates the status, and notifies the user.
         * The changes are then saved and the display is refreshed.
         *
         * @param ActionParams $params Parameters passed to determine and set the beginning event date.
         *
         * @return void
         * @throws Caller
         */
        public function setDate_BeginningEvent(ActionParams $params): void
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
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller
         */
        public function setDate_EndEvent(ActionParams $params): void
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
         *
         * @return void
         * @throws Caller
         */
        public function setTime_StartTime(ActionParams $params): void
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
         * @throws Caller
         */
        public function setTime_EndTime(ActionParams $params): void
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

        /**
         * Handles the change event for the group title dropdown list.
         *
         * @param ActionParams $params The parameters associated with the action.
         * @return void
         */
        protected function lstGroupTitle_Change(ActionParams $params): void
        {
            if ($this->lstGroupTitle->SelectedValue !== $this->objEventsCalendar->getMenuContentGroupTitleId()) {
                $this->dlgModal2->showDialogBox();
            }
        }

        /**
         * Handles the change event for the website target type list.
         * Updates the related properties in the EventsCalendar object and triggers associated UI actions.
         *
         * @param ActionParams $params The parameters passed from the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function lstWebsiteTarget_Change(ActionParams $params): void
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
         *
         * @return void
         * @throws Caller
         */
        public function lstWebsiteTargetType_KeyUp(ActionParams $params): void
        {
            if ($this->txtWebsiteUrl->Text) {
                $this->lstWebsiteTargetType->Enabled = true;
            } else {
                $this->lstWebsiteTargetType->Enabled = false;
                $this->objEventsCalendar->setWebsiteUrl(null);
                $this->objEventsCalendar->setWebsiteTargetTypeId(null);
                $this->objEventsCalendar->save();

                $this->txtWebsiteUrl->Text = '';
                $this->lstWebsiteTargetType->SelectedValue = null;

                $this->dlgToastr1->notify();
            }
        }

        /**
         * Updates the Facebook target settings for the events calendar when the selection is changed.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void
         * @throws Caller
         */
        protected function lstFacebookTarget_Change(ActionParams $params): void
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
         *
         * @return void
         * @throws Caller
         */
        public function lstFacebookTargetType_KeyUp(ActionParams $params): void
        {
            if ($this->txtFacebookUrl->Text) {
                $this->lstFacebookTargetType->Enabled = true;
            } else {
                $this->lstFacebookTargetType->Enabled = false;
                $this->objEventsCalendar->setFacebookUrl(null);
                $this->objEventsCalendar->setFacebookTargetTypeId(null);
                $this->objEventsCalendar->save();

                $this->txtFacebookUrl->Text = '';
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
         * @throws Caller
         */
        protected function lstInstagramTarget_Change(ActionParams $params): void
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
         *
         * @return void
         * @throws Caller
         */
        public function lstInstagramTargetType_KeyUp(ActionParams $params): void
        {
            if ($this->txtInstagramUrl->Text) {
                $this->lstInstagramTargetType->Enabled = true;
            } else {
                $this->lstInstagramTargetType->Enabled = false;
                $this->objEventsCalendar->setInstagramUrl(null);
                $this->objEventsCalendar->setInstagramTargetTypeId(null);
                $this->objEventsCalendar->save();

                $this->txtInstagramUrl->Text = '';
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
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function moveItem_Click(ActionParams $params): void
        {
            $this->dlgModal2->hideDialogBox();

            $objGroupTitle = EventsSettings::loadById($this->lstGroupTitle->SelectedValue);

            // Before proceeding to other activities, you must fix the initial data of the tables "events_calendar" and "target_group_of_calendar"
            $objLockedGroup = $this->objEventsCalendar->getMenuContentGroupTitleId();
            $objTargetGroup = EventsSettings::loadById($this->lstGroupTitle->SelectedValue);

            $currentCount = EventsCalendar::countByMenuContentGroupTitleId($objLockedGroup);
            $nextCount = EventsCalendar::countByMenuContentGroupTitleId($objTargetGroup->getId());

            // Here you must first check the lock status of the following folder to do this check...
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

            $this->objEventsCalendar->setMenuContentGroupId($objGroupTitle->getMenuContentId());
            $this->objEventsCalendar->setMenuContentGroupTitleId($this->lstGroupTitle->SelectedValue);
            $this->objEventsCalendar->setEventsGroupName($this->lstGroupTitle->SelectedName);
            $this->objEventsCalendar->setPostUpdateDate(Q\QDateTime::now());
            $this->objEventsCalendar->updateEvent($this->txtYear->Text, $this->txtTitle->Text, $objGroupTitle->getTitleSlug());
            $this->objEventsCalendar->save();

            $objEventsFiles = EventFiles::loadArrayByEventsCalendarGroupId($this->intId);

            if ($objEventsFiles) {
                foreach ($objEventsFiles as $objEventsFile) {
                    $objEventsFile = EventFiles::loadById($objEventsFile->getId());
                    $objEventsFile->setYear($this->txtYear->Text);
                    $objEventsFile->save();
                }
            }

            $this->objFrontendLinks->setGroupedId($objGroupTitle->getMenuContentId());
            $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
            $this->objFrontendLinks->setFrontendTitleSlug($this->objEventsCalendar->getTitleSlug());
            $this->objFrontendLinks->save();

            // We are updating the slug
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objEventsCalendar->getTitleSlug();
            $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);

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
         * Saves the selected image details to the event calendar, updates post-details, and refreshes the display.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void
         * @throws Caller
         */
        protected function imageSave_Push(ActionParams $params): void
        {
            $saveId = $this->objMediaFinder->Item;

            $this->objEventsCalendar->setPictureId($saveId);
            $this->objEventsCalendar->setPostUpdateDate(Q\QDateTime::now());
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
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function imageDelete_Push(ActionParams $params): void
        {
            $objFiles = Files::loadById($this->objEventsCalendar->getPictureId());

            if ($objFiles->getLockedFile() !== 0) {
                $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                $objFiles->save();
            }

            $this->objEventsCalendar->setPictureId(null);
            $this->objEventsCalendar->setPictureDescription(null);
            $this->objEventsCalendar->setAuthorSource(null);
            $this->objEventsCalendar->setPostUpdateDate(Q\QDateTime::now());
            $this->objEventsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objEventsCalendar->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objEventsCalendar->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objEventsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            $this->refreshDisplay();

            $this->lblPictureDescription->Display = false;
            $this->txtPictureDescription->Display = false;
            $this->lblAuthorSource->Display = false;
            $this->txtAuthorSource->Display = false;

            $this->txtPictureDescription->Text = '';
            $this->txtAuthorSource->Text = '';

            $this->dlgToastr1->notify();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the document link button.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnDocumentLink_Click(ActionParams $params): void
        {
            $_SESSION["redirect-data"] = 'event_calendar_edit.php?id=' . $this->intId . '&group=' . $this->intGroup;

            $this->btnDocumentLink->Enabled = false;

            Application::redirect('file_finder.php');
        }

        /**
         * Handles the click event for the download and save a button.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnDownloadSave_Click(ActionParams $params): void
        {
            // We check each field and add errors if necessary
            if (!$this->txtLinkTitle->Text) {
                $this->txtLinkTitle->setHtmlAttribute('required', 'required');

                $this->dlgToastr13->notify(); // If only one field is invalid
            }

            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {

                $objEventFile = new EventFiles();
                $objEventFile->setEventsCalendarGroupId($this->intId);
                $objEventFile->setMenuContentGroupId($this->objEventsCalendar->getMenuContentGroupId());
                $objEventFile->setYear($this->txtYear->Text);
                $objEventFile->setTitle($this->txtLinkTitle->Text);

                $objEventFile->setFilesId($_SESSION["data_id"]);
                $objEventFile->setStatus(2);
                $objEventFile->setPostDate(Q\QDateTime::now());
                $objEventFile->save();

                $this->objEventsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
                $this->objEventsCalendar->save();

                $this->txtUsersAsEditors->Text = implode(', ', $this->objEventsCalendar->getUserAsEditorsArray());
                $this->calPostUpdateDate->Text = $this->objEventsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

                $this->refreshDisplay();

                EventsChanges::updateAllChangeLockStates();

                if (EventFiles::countByEventsCalendarGroupId($this->intId) === 0) {
                    $this->txtDocumentLink->Display = true;
                } else {
                    $this->txtDocumentLink->Display = false;
                }

                $this->btnDocumentLink->Display = true;
                $this->txtLinkTitle->Display = false;
                $this->btnDownloadSave->Display = false;
                $this->btnDownloadCancel->Display = false;

                $this->txtLinkTitle->Text = '';

                $this->dtgSelectedList->removeCssClass('disabled');
                $this->dtgSelectedList->refresh();

                $this->dlgToastr1->notify();

                unset($_SESSION["data_id"]);
                unset($_SESSION["data_name"]);
                unset($_SESSION["data_path"]);
            }
        }

        /**
         * Handles the click event for the download cancel button.
         * Resets the UI elements and session data related to file download.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnDownloadCancel_Click(ActionParams $params): void
        {
            if (EventFiles::countByEventsCalendarGroupId($this->intId) === 0) {
                $this->txtDocumentLink->Display = true;
            } else {
                $this->txtDocumentLink->Display = false;
            }

            $this->btnDocumentLink->Display = true;
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
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnSelectedSave_Click(ActionParams $params): void
        {
            $objEventFiles = EventFiles::loadById($this->intDocument);
            $errors = []; // Array for tracking errors

            if (!$this->txtSelectedTitle->Text) {
                $this->txtSelectedTitle->setHtmlAttribute('required', 'required');
                $errors[] = 'txtSelectedTitle';
                $this->dlgToastr13->notify(); // If only one field is invalid
            }

            if (count($errors)) {
                return;
            }

            $objEventFiles->setTitle($this->txtSelectedTitle->Text);
            $objEventFiles->setStatus($this->lstSelectedStatus->SelectedValue);
            $objEventFiles->setPostUpdateDate(Q\QDateTime::now());
            $objEventFiles->save();

            $this->txtSelectedTitle->setDataAttribute('view', '');
            $this->txtSelectedTitle->setDataAttribute('open', 'false');

            $this->objEventsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objEventsCalendar->save();

            $this->txtUsersAsEditors->Text = implode(', ', $this->objEventsCalendar->getUserAsEditorsArray());
            $this->calPostUpdateDate->Text = $this->objEventsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->refreshDisplay();

            $this->dtgSelectedList->removeCssClass('disabled');
            $this->btnDocumentLink->Display = true;
            $this->txtDocumentLink->Display = true;
            $this->dtgSelectedList->refresh();

            $this->txtSelectedTitle->Display = false;
            $this->lstSelectedStatus->Display = false;
            $this->btnSelectedSave->Display = false;
            $this->btnSelectedCheck->Display = false;
            $this->btnSelectedDelete->Display = false;
            $this->btnSelectedCancel->Display = false;

            $this->txtSelectedTitle->Text = '';
            $this->dtgSelectedList->refresh();

            $this->dlgToastr1->notify();
        }

        /**
         * Handle the click event for the delete button, showing the modal dialog box
         * and setting data attributes for the title element.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Caller
         */
        protected function btnSelectedDelete_Click(ActionParams $params): void
        {
            $this->dlgModal6->showDialogBox();
            $this->txtSelectedTitle->setDataAttribute('view', '');
            $this->txtSelectedTitle->setDataAttribute('open', 'false');
        }

        /**
         * Handles the click event for the cancel button in the selected section.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function btnSelectedCancel_Click(ActionParams $params): void
        {
            $this->dtgSelectedList->removeCssClass('disabled');
            $this->btnDocumentLink->Enabled = true;
            $this->dtgSelectedList->refresh();

            $this->txtSelectedTitle->Display = false;
            $this->lstSelectedStatus->Display = false;
            $this->btnSelectedSave->Display = false;
            $this->btnSelectedCheck->Display = false;
            $this->btnSelectedDelete->Display = false;
            $this->btnSelectedCancel->Display = false;

            $this->txtSelectedTitle->Text = '';
            $this->txtSelectedTitle->setDataAttribute('view', '');
            $this->txtSelectedTitle->setDataAttribute('open', 'false');
        }

        /**
         * Handles the click event to delete a document in the selected section.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function deleteDocument_Click(ActionParams $params): void
        {
            if ($params->ActionParameter == "pass") {

                $objEventFile = EventFiles::loadById($this->intDocument);
                $objFiles = Files::loadById($objEventFile->getFilesId());

                if ($objFiles) {
                    if ($objFiles->getLockedFile() !== 0) {
                        $objFiles->setLockedFile($objFiles->getLockedFile() - 1);
                        $objFiles->save();
                    }
                }
                $objEventFile->delete();
            }

            EventsChanges::updateAllChangeLockStates();

            $this->dtgSelectedList->removeCssClass('disabled');
            $this->btnDocumentLink->Enabled = true;
            $this->dtgSelectedList->refresh();

            $this->txtSelectedTitle->Display = false;
            $this->lstSelectedStatus->Display = false;
            $this->btnSelectedSave->Display = false;
            $this->btnSelectedCheck->Display = false;
            $this->btnSelectedDelete->Display = false;
            $this->btnSelectedCancel->Display = false;

            if (EventFiles::countByEventsCalendarGroupId($this->intId) === 0) {
                $this->txtDocumentLink->Display = true;
            } else {
                $this->txtDocumentLink->Display = false;
            }

            $this->dlgModal6->hideDialogBox();
            $this->dlgToastr1->notify();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for save a button, validates form fields,
         * and triggers appropriate notifications based on the validation outcome.
         * Applies necessary HTML attributes to indicate invalid fields.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void
         * @throws Caller
         */
        public function btnSave_Click(ActionParams $params): void
        {
            $this->renderActionsWithId();
            $this->InputsCheck();

            if (count($this->errors)) {
                $this->lstStatus->SelectedValue =  $this->objEventsCalendar->getStatus();
            }

            // Condition for which notification to show
            if (count($this->errors) === 1) {
                $this->dlgToastr13->notify(); // If only one field is invalid
                // Partial saving allowed
            } elseif (count($this->errors) > 1) {
                $this->dlgToastr2->notify(); // If there is more than one invalid field
                // Partial saving allowed
            } else {
                $this->dlgToastr1->notify(); // Everything OK
            }
            $this->saveHelper();

            unset($this->errors);
        }

        /**
         * Handles the save and close button click event, validating input fields, notifying errors if needed, and
         * performing necessary redirection or actions.
         *
         * @param ActionParams $params Parameters associated with the button click action.
         *
         * @return void
         * @throws Caller
         * @throws Throwable
         */
        public function btnSaveClose_Click(ActionParams $params): void
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
         * Handles the saving and updating process for event details and frontend link configurations.
         * Retrieves, updates, and stores related data to ensure correct event and link information is recorded.
         * Also manages the display refresh and link rendering for the event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function saveHelper(): void
        {
            $objTemplateLocking = FrontendTemplateLocking::load(8);
            $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->getId());

            $this->objEventsCalendar->setYear($this->txtYear->Text);

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

            $this->updateBorderOnInvalidEmails();

            $this->objFrontendLinks->setLinkedId($this->intId);
            $this->objFrontendLinks->setGroupedId($this->intGroup);
            $this->objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
            $this->objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $this->objFrontendLinks->setTitle(trim($this->txtTitle->Text));
            $this->objFrontendLinks->setContentTypesManagamentId(8);
            $this->objFrontendLinks->setFrontendTitleSlug($this->objEventsCalendar->getTitleSlug());
            $this->objFrontendLinks->save();

            $this->txtAuthor->Text = $this->objEventsCalendar->getAuthor();

            if ($this->objEventsCalendar->getTitle()) {
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objEventsCalendar->getTitleSlug();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
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
         *
         * @return void
         * @throws Caller
         */
        protected function itemEscape_Click(ActionParams $params): void
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
         * @throws Caller
         */
        protected  function lstStatus_Change(): void
        {
            $this->InputsCheck();

            // Condition for which notification to show
            if (count($this->errors) === 1) {
                $this->dlgToastr13->notify(); // If only one field is invalid
            } elseif (count($this->errors) > 1) {
                $this->dlgToastr2->notify(); // If there is more than one invalid field
            }

            // Save the previous status to a variable (before the change)
            $previousStatus = $this->objEventsCalendar->getStatus();
            $selectedStatus = $this->lstStatus->SelectedValue;

            if (count($this->errors)) {
                // Published is not allowed on errors
                if ((int)$selectedStatus === 1) {
                    // We revert to the previous status
                    $this->lstStatus->SelectedValue = $previousStatus;
                    $this->objEventsCalendar->setStatus($previousStatus);
                } else {
                    // If Hidden or Draft, we allow changing the state
                    $this->objEventsCalendar->setStatus($selectedStatus);
                }

                $this->objEventsCalendar->save();
            } else {
                // All fields are fine, let's change them to exactly what was selected
                $this->objEventsCalendar->setStatus($selectedStatus);
                $this->objEventsCalendar->save();

                if ($this->objEventsCalendar->getStatus() === 1) {
                    $this->dlgModal4->showDialogBox();
                } else if ($this->objEventsCalendar->getStatus() === 2) {
                    $this->dlgModal3->showDialogBox();
                } else if ($this->objEventsCalendar->getStatus() === 3) {
                    $this->dlgModal5->showDialogBox();
                }
            }

            $this->renderActionsWithId();
            $this->refreshDisplay();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Updates the visibility of various display elements based on the state of the events calendar.
         *
         * Determines the display state of post-date, post-update date, author, and users as editor fields
         * using conditions derived from the events calendar object properties such as post-date, update date,
         * author assignment, and the number of users assigned as editors.
         *
         * @return void
         */
        protected function refreshDisplay(): void
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
        public function renderActionsWithId(): void
        {
            if ($this->intId) {
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
                    $this->objEventsCalendar->setPostUpdateDate(Q\QDateTime::now());
                    $this->objEventsCalendar->setAssignedEditorsNameById($this->intLoggedUserId);
                    $this->objEventsCalendar->save();

                    $this->txtUsersAsEditors->Text = implode(', ', $this->objEventsCalendar->getUserAsEditorsArray());
                    $this->calPostUpdateDate->Text = $this->objEventsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
                }
            }
        }

        /**
         * Updates the border style of the email input field and displays a notification if there are invalid or
         * unsupported emails. If invalid emails are found, a red border is applied to the field, and a notification
         * message is displayed. If no invalid emails are found, the border style is removed.
         *
         * @return void
         * @throws Caller
         */
        protected function updateBorderOnInvalidEmails(): void
        {
            $emails = $this->txtEmail->getGroupedEmails();

            if (!empty($emails['invalid'])) {
                $this->txtEmail->addCssClass('has-error');
                $this->dlgToastr14->Message = t('<p style=\"margin-bottom: 2px;\">Invalid or unsupported email address(es): ' . implode(', ', $emails['invalid']) . '</p>Please correct the invalid addresses!');
                $this->dlgToastr14->notify();
            } else {
                $this->txtEmail->removeCssClass('has-error');
            }
        }

        /**
         * Validates input fields by checking if they are empty and adding corresponding error messages.
         * Also sets the HTML 'required' attribute for any field that is empty.
         *
         * @return void
         */
        protected  function InputsCheck(): void
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
         * Handles the Cancel button, click event and redirects the user to the list page.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void
         * @throws Throwable
         */
        public function btnCancel_Click(ActionParams $params): void
        {
            $this->redirectToListPage();
        }

        /**
         * Handles the click event for the delete button and displays a modal dialog box.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         * @return void
         */
        public function btnDelete_Click(ActionParams $params): void
        {
            $this->dlgModal1->showDialogBox();
        }

        /**
         * Handles the deletion of an item based on the given action parameters.
         * Performs various checks and operations including updating related file locks,
         * managing associated settings, and deleting related records.
         *
         * @param ActionParams $params Parameters related to the action triggering this method, including
         *     action-specific data.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        protected function deleteItem_Click(ActionParams $params): void
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

                TargetGroupOfCalendar::updateAllTargetGroupLockStates();
                $this->redirectToListPage();
            }
            $this->dlgModal1->hideDialogBox();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event of the btnGoToTargetGroup button. Sets session variables and redirects the user to
         * the events calendar list page.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void
         * @throws Throwable
         */
        public function btnGoToTargetGroup_Click(ActionParams $params): void
        {
            $_SESSION['target_id'] = $this->intId;
            $_SESSION['target_group'] = $this->intGroup;
            Application::redirect('events_calendar_list.php#targetCroupList_tab');
        }

        /**
         * Handles the click event for the btnGoToChanges button, sets session variables, and redirects to the
         * specified URL.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void
         * @throws Throwable
         */
        public function btnGoToChanges_Click(ActionParams $params): void
        {
            $_SESSION['events_changes'] = $this->intId;
            $_SESSION['events_group'] = $this->intGroup;
            Application::redirect('categories_manager.php#eventsChanges_tab');
        }

        /**
         * Handles the click event for the Go-To Settings button. Sets session variables
         * for the event ID and group, then redirects the user to the settings manager page.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void
         * @throws Throwable
         */
        public function btnGoToSettings_Click(ActionParams $params): void
        {
            $_SESSION['events_id'] = $this->intId;
            $_SESSION['events_group'] = $this->intGroup;

            Application::redirect('settings_manager.php#eventsSettings_tab');
        }

        /**
         * Redirects the user to the events calendar list page and clears specific session variables.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            Application::redirect('events_calendar_list.php');
            unset($_SESSION['target_id']);
            unset($_SESSION['target_group']);
        }
    }
    EventCalendarEditForm::run('EventCalendarEditForm');