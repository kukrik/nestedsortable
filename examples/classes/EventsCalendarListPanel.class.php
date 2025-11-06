<?php

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Event\CellClick;
    use QCubed\Event\Change;
    use QCubed\Event\Click;
    use QCubed\Action\Terminate;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Event\Input;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use QCubed\Query\Condition\All;
    use QCubed\Query\Condition\AndCondition;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;
    use QCubed\Control\ListItem;
    use Random\RandomException;
    use QCubed\Project\Application;

    /**
     * Class EventsCalendarListPanel
     *
     * A panel for managing event calendars in the system interface. Provides functionality
     * to list, filter, add, move, and update event records for the users.
     * The class dynamically manages user interactions, including configuration
     * of input elements, buttons, toasts, and modals to ensure proper event handling and workflow.
     */
    class EventsCalendarListPanel extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objPreferredItemsPerPageObjectCondition = null;
        protected ?array $objPreferredItemsPerPageObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToast1;
        protected Q\Plugin\Toastr $dlgToast2;
        protected Q\Plugin\Toastr $dlgToast3;
        protected Q\Plugin\Toastr $dlgToast4;
        protected Q\Plugin\Toastr $dlgToast5;
        protected Q\Plugin\Toastr $dlgToast6;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public EventsCalendarTable$dtgEventsCalendars;

        public Bs\Button $btnAddEvent;
        public Bs\Button $btnMove;
        public ?Q\Plugin\Select2 $lstEventsLocked = null;
        public Q\Plugin\Select2 $lstTargetGroup;
        public Q\Plugin\Select2 $lstYears;
        public Q\Plugin\Select2 $lstGroups;
        public Q\Plugin\Select2 $lstTargets;
        public ?Q\Plugin\Select2 $lstChanges = null;

        public Q\Plugin\YearPicker $txtYear;
        public ?Q\Plugin\Select2 $lstGroupTitle = null;
        public Bs\TextBox $txtTitle;
        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnLockedCancel;
        public Bs\Button $btnBack;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;
        protected object $objPortlet;

        protected object $objMenuContent;
        protected object $objEventsCalendar;
        protected ?object $objEvents = null;

        protected string $strTemplate = 'EventsCalendarListPanel.tpl.php';

        /**
         * Constructor method for initializing the class instance. This method sets up the logged-in user,
         * creates UI elements including inputs, buttons, notifications, modals, and filters, and
         * resets elements to their initial state. It also initializes the data table and its data binder.
         *
         * @param mixed $objParentObject The parent object that contains this class instance.
         * @param string|null $strControlId An optional control ID for identifying this UI control.
         *
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            // $objUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);
            $this->objPortlet = Portlet::load(4);

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
            $this->elementsReset();

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgEventsCalendars_Create();
            $this->dtgEventsCalendars->setDataBinder('bindData', $this);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Updates the user's last active timestamp to the current time and saves the changes to the user object.
         *
         * @return void The method does not return a value.
         * @throws Caller
         */
        private function userOptions(): void
        {
            $this->objUser->setLastActive(QDateTime::now());
            $this->objUser->save();
        }

        /**
         * Updates the portlet with the total count of news items and the latest modification date.
         * If news items are available, it sets the total count, assigns the current date and time
         * as the last updated date, and saves these changes to the portlet.
         *
         * @return void
         * @throws Caller
         */
        private function updatePortlet(): void
        {
            $objPage = EventsCalendar::countAll();

            if ($objPage) {
                $this->objPortlet->setTotalValue($objPage);
                $this->objPortlet->setLastDate(QDateTime::now());
                $this->objPortlet->save();
            }
        }

        /**
         * Initializes and configures input controls for a year, title, and target group selection.
         *
         * @return void
         * @throws Caller
         */
        protected function createInputs(): void
        {
            $this->txtYear = new Q\Plugin\YearPicker($this);
            $this->txtYear->Language = $this->objUser->PreferredLanguageObject->Code ?? 'en';
            $this->txtYear->Placeholder = t(' - Year -');
            $this->txtYear->TodayBtn = true;
            $this->txtYear->ClearBtn = true;
            $this->txtYear->AutoClose = true;
            $this->txtYear->ActionParameter = $this->txtYear->ControlId;
            $this->txtYear->setHtmlAttribute('autocomplete', 'off');
            $this->txtYear->setCssStyle('margin-left', '10px');
            $this->txtYear->Width = '100%';
            $this->txtYear->addAction(new Change(), new AjaxControl($this, 'setYear'));
            $this->txtYear->AddJavascriptFile(BACKEND_URL . "/assets/js/locales/bootstrap-datetimepicker." . $this->objUser->PreferredLanguageObject->Code . ".js");

            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Title of the new event');
            $this->txtTitle->addWrapperCssClass('center-button');
            $this->txtTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtTitle->AddAction(new EnterKey(), new AjaxControl($this,'btnSave_Click'));
            $this->txtTitle->addAction(new EnterKey(), new Terminate());
            $this->txtTitle->AddAction(new EscapeKey(), new AjaxControl($this,'btnCancel_Click'));
            $this->txtTitle->addAction(new EscapeKey(), new Terminate());

            $this->lstTargetGroup = new Q\Plugin\Select2($this);
            $this->lstTargetGroup->MinimumResultsForSearch = -1;
            $this->lstTargetGroup->Theme = 'web-vauu';
            $this->lstTargetGroup->Width = '100%';
            $this->lstTargetGroup->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);

            $objTargetGroups = EventsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::EventsSettings()->Id)));
            foreach ($objTargetGroups as $objTitle) {
                if ($objTitle->IsReserved !== 2) {
                    $this->lstTargetGroup->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstTargetGroup->addAction(new Change(), new AjaxControl($this,'lstTargetGroup_Change'));
            $this->lstTargetGroup->Enabled = false;
        }

        /**
         * Initializes and configures a set of button controls for event management,
         * including actions for adding, moving, saving, and cancelling events, as well as returning to a previous
         * state. This method also dynamically adjusts the visibility of certain buttons based on the event settings.
         *
         * @return void
         * @throws Caller
         */
        protected function createButtons(): void
        {
            $this->btnAddEvent = new Bs\Button($this);
            $this->btnAddEvent->Text = t(' Add event');
            $this->btnAddEvent->Glyph = 'fa fa-plus';
            $this->btnAddEvent->CssClass = 'btn btn-orange';
            $this->btnAddEvent->addWrapperCssClass('center-button');
            $this->btnAddEvent->CausesValidation = false;
            $this->btnAddEvent->addAction(new Click(), new AjaxControl($this, 'btnAddEvent_Click'));

            $this->btnMove = new Bs\Button($this);
            $this->btnMove->Text = t(' Move');
            $this->btnMove->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnMove->CssClass = 'btn btn-darkblue move-button-js';
            $this->btnMove->addWrapperCssClass('center-button');
            $this->btnMove->CausesValidation = false;
            $this->btnMove->addAction(new Click(), new AjaxControl($this, 'btnMove_Click'));

            if (EventsSettings::countAll() !== 1) {
                Application::executeJavaScript("
                    $('.move-button-js').removeClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            }

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange save-js';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->setCssStyle('margin-left', '10px');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));

            $this->btnLockedCancel = new Bs\Button($this);
            $this->btnLockedCancel->Text = t('Cancel');
            $this->btnLockedCancel->addWrapperCssClass('center-button');
            $this->btnLockedCancel->CssClass = 'btn btn-default';
            $this->btnLockedCancel->CausesValidation = false;
            $this->btnLockedCancel->addAction(new Click(), new AjaxControl($this, 'btnLockedCancel_Click'));

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->addWrapperCssClass('center-button');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new AjaxControl($this,'btnBack_Click'));
        }

        /**
         * Initializes and configures multiple Toastr notifications with different settings and messages.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('The year is at least mandatory!');
            $this->dlgToast1->ProgressBar = true;

            $this->dlgToast2 = new Q\Plugin\Toastr($this);
            $this->dlgToast2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast2->Message = t('The event title is at least mandatory!');
            $this->dlgToast2->ProgressBar = true;

            $this->dlgToast3 = new Q\Plugin\Toastr($this);
            $this->dlgToast3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast3->Message = t('<p style=\"margin-bottom: 5px;\">The event group must be selected beforehand!</p>');
            $this->dlgToast3->ProgressBar = true;
            $this->dlgToast3->TimeOut = 10000;
            $this->dlgToast3->EscapeHtml = false;

            $this->dlgToast4 = new Q\Plugin\Toastr($this);
            $this->dlgToast4->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast4->Message = t('<p style=\"margin-bottom: 5px;\">The event group cannot be the same as the target group!</p>');
            $this->dlgToast4->ProgressBar = true;
            $this->dlgToast4->TimeOut = 10000;
            $this->dlgToast4->EscapeHtml = false;

            $this->dlgToast5 = new Q\Plugin\Toastr($this);
            $this->dlgToast5->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast5->Message = t('<strong>Well done!</strong> The transfer of events to the new group was successful.');
            $this->dlgToast5->ProgressBar = true;

            $this->dlgToast6 = new Q\Plugin\Toastr($this);
            $this->dlgToast6->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast6->Message = t('The transfer of events to the new group failed.');
            $this->dlgToast6->ProgressBar = true;
        }

        /**
         * Initializes and creates modal dialogs with specific settings and actions.
         *
         * @return void
         * @throws Caller
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move the events from this event group to another event group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Note! If there are several event items in the selected group, they will be transferred to the new group!</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addCloseButton(t("I'll cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'moveItems_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'transferCancelling_Click'));

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal2->Title = t("Warning");
            $this->dlgModal2->HeaderClasses = 'btn-danger';
            $this->dlgModal2->addCloseButton(t("I understand"));
        }

        /**
         * Resets the elements by executing JavaScript to add the 'hidden' class
         * to elements with the classes 'new-item-js' and 'move-items-js'.
         *
         * @return void
         * @throws Caller
         */
        public function elementsReset(): void
        {
            Application::executeJavaScript("
                $('.new-item-js').addClass('hidden');
                $('.move-items-js').addClass('hidden');
            ");
        }

        /**
         * Sets the year based on the text of the specified control.
         *
         * @param ActionParams $params The action parameters containing the ID of the control whose text will be used
         *     to set the year.
         *
         * @return void
         * @throws RandomException
         */
        protected function setYear(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->txtYear->Text = $this->Form->getControl((int)$params->ActionParameter);
        }

        /**
         * Handles the click event for the button to add a new event.
         * It updates UI elements to reflect the state of adding a new event, handles JavaScript executions,
         * and configures the event group selection based on the reserved state of event settings.
         *
         * @param ActionParams $params The parameters triggering the action, including any relevant data for the event handling.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnAddEvent_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.move-button-js').addClass('hidden');
                $('.new-item-js').removeClass('hidden');
                $('.move-items-js').addClass('hidden');
            ");

            $this->updateLockStatus();
            $this->disableInputs();
            $this->userOptions();

            $countByIsReserved = EventsSettings::countByIsReserved(1);

            $this->txtTitle->Text = '';
            $this->txtYear->show();
            $this->dtgEventsCalendars->addCssClass('disabled');
            $this->btnAddEvent->Enabled = false;

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '100%';
            $this->lstGroupTitle->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroupTitle->addItem(t('- Select one event group -'), null, true);

            $objGroups = EventsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::EventsSettings()->Id)));

            foreach ($objGroups as $objTitle) {
                if ($objTitle->IsReserved === 1 && $countByIsReserved === 1) {
                    $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                    $this->lstGroupTitle->SelectedValue = $objTitle->Id;
                } else if ($objTitle->IsReserved === 1 && $countByIsReserved > 1) {
                    $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstGroupTitle->addAction(new Change(), new AjaxControl($this,'lstGroupTitle_Change'));
            $this->lstGroupTitle->setHtmlAttribute('required', 'required');

            if ($countByIsReserved === 1) {
                $this->lstGroupTitle->Enabled = false;
                $this->txtTitle->focus();
            } else {
                $this->lstGroupTitle->Enabled = true;
                $this->lstGroupTitle->focus();
            }
        }

        /**
         * Handles the click event for the move button, updating the UI to transition to a state where items can be moved.
         * It adjusts the visibility of certain sections, initializes and configures a selection list for locked event groups,
         * and manages the enabling and focusing of other UI components based on the locked event counts.
         *
         * @param ActionParams $params Contains the parameters for the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnMove_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.move-items-js').removeClass('hidden');
                $('.new-item-js').addClass('hidden'); 
            ");

            $this->updateLockStatus();
            $this->disableInputs();
            $this->userOptions();

            $this->lstEventsLocked = new Q\Plugin\Select2($this);
            $this->lstEventsLocked->MinimumResultsForSearch = -1;
            $this->lstEventsLocked->Theme = 'web-vauu';
            $this->lstEventsLocked->Width = '100%';
            $this->lstEventsLocked->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstEventsLocked->addItem(t('- Select one event group -'), null, true);

            $objGroups = EventsSettings::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::EventsSettings()->EventsLocked, 0), QQN::EventsSettings()->Id)
                ]
            );

            $countLocked = EventsSettings::countByEventsLocked(1);

            foreach ($objGroups as $objTitle) {
                if ($countLocked > 1 && $objTitle->EventsLocked === 1) {
                    $this->lstEventsLocked->addItem($objTitle->Name, $objTitle->Id);
                } else if ($countLocked === 1 && $objTitle->EventsLocked === 1) {
                    $this->lstEventsLocked->addItem($objTitle->Name, $objTitle->Id);
                    $this->lstEventsLocked->SelectedValue = $objTitle->Id;
                }
            }

            $this->lstEventsLocked->addAction(new Change(), new AjaxControl($this,'lstEventsLocked_Change'));

            if ($this->lstEventsLocked->SelectedValue === null) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->Enabled = false;
            }

            if ($countLocked === 1) {
                $this->lstEventsLocked->Enabled = false;
                $this->lstTargetGroup->Enabled = true;
                $this->lstTargetGroup->focus();
            } else {
                $this->lstEventsLocked->Enabled = true;
                $this->lstEventsLocked->focus();
            }

            $this->btnAddEvent->Enabled = false;
            $this->dtgEventsCalendars->addCssClass('disabled');
        }

        /**
         * Handles the change event for the group title list.
         *
         * @param ActionParams $params Parameters passed from the triggering action.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstGroupTitle_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!$this->txtYear->Text) {
                $this->txtYear->show();
                $this->dlgToast1->notify();
            } else if (!$this->lstGroupTitle->SelectedValue) {
                $this->lstGroupTitle->focus();
                $this->dlgToast2->notify();
            } else if (!$this->txtTitle->Text) {
                $this->txtTitle->focus();
            }
        }

        /**
         * Handles the change event for the locked events list.
         *
         * @param ActionParams $params Parameters for the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstEventsLocked_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstEventsLocked->SelectedValue === null) {
                $this->lstTargetGroup->Enabled = false;
                $this->lstEventsLocked->addCssClass('has-error');
                $this->dlgToast3->notify();
            } else {
                $this->lstTargetGroup->Enabled = true;
                $this->lstEventsLocked->removeCssClass('has-error');
                $this->lstTargetGroup->focus();
            }
        }

        /**
         * Handles the change event for the target group list.
         *
         * @param ActionParams $params The parameters for the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstTargetGroup_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstEventsLocked->SelectedValue === $this->lstTargetGroup->SelectedValue) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->refresh();
                $this->lstTargetGroup->addCssClass('has-error');
                $this->dlgToast4->notify();
            } else if ($this->lstEventsLocked->SelectedValue !== null && $this->lstTargetGroup->SelectedValue !== null) {
                $this->dlgModal1->showDialogBox();
            } else {
                $this->lstTargetGroup->removeCssClass('has-error');
            }
        }

        /**
         * Handles the click event for cancelling a transfer.
         *
         * @param ActionParams $params The parameters for the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function transferCancelling_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->elementsReset();

            $this->lstEventsLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->btnAddEvent->Enabled = true;
            $this->btnMove->Enabled = true;

            $this->lstEventsLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->enableInputs();
            $this->dtgEventsCalendars->removeCssClass('disabled');

            $this->userOptions();
        }

        /**
         * Handles the click event for moving items. This method hides the dialog box,
         * performs event transfer operations, and resets UI elements. After execution,
         * it enables the "Add Event" and "Move" buttons.
         *
         * @param ActionParams $params An object containing parameters for the action event triggered.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function moveItems_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->dlgModal1->hideDialogBox();
            $this->eventsTransferOperations();

            $this->elementsReset();
            $this->btnAddEvent->Enabled = true;
            $this->btnMove->Enabled = true;

            $this->userOptions();
        }

        /**
         * Transfers event operations by updating their associated groups and settings.
         * The method moves events and frontend links from a locked group to a target group,
         * updates their relevant fields, and saves the changes.
         * It also refreshes the events calendar display and executes JavaScript to update UI elements.
         * Notifies the user with a toast message based on the success of the transfer operation.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        private function eventsTransferOperations(): void
        {
            $this->dlgModal1->hideDialogBox();

            $objLockedGroup = EventsSettings::loadById($this->lstEventsLocked->SelectedValue);
            $objTargetGroup = EventsSettings::loadById($this->lstTargetGroup->SelectedValue);
            $objFrontendLinks = FrontendLinks::loadArrayByGroupedId($objLockedGroup->getMenuContentId());
            $objEventsGroupArray = EventsCalendar::loadArrayByMenuContentGroupId($objLockedGroup->getMenuContentId());
            $objEventFiles = EventFiles::loadArrayByMenuContentGroupId($objLockedGroup->getMenuContentId());

            $beforeCount = count(FrontendLinks::loadArrayByGroupedId($objLockedGroup->getMenuContentId()));
            $afterCount = 0;

            $objEventsSettings = EventsSettings::loadById($objLockedGroup->getId());
            $objEventsSettings->setEventsLocked(0);
            $objEventsSettings->save();

            $objEventsSettings = EventsSettings::loadById($objTargetGroup->getId());
            $objEventsSettings->setEventsLocked(1);
            $objEventsSettings->save();

            foreach ($objEventsGroupArray as $objEventsGroup) {
                $this->objEvents = EventsCalendar::loadById($objEventsGroup->getId());
                $this->objEvents->setMenuContentGroupId($objTargetGroup->getMenuContentId());
                $this->objEvents->setMenuContentGroupTitleId($this->lstTargetGroup->SelectedValue);
                $this->objEvents->setEventsGroupName($this->lstTargetGroup->SelectedName);
                $this->objEvents->updateEvent($this->objEvents->getYear(), $this->objEvents->getTitle(), $objTargetGroup->getTitleSlug());
                $this->objEvents->save();
            }

            foreach ($objEventFiles as $objEventFile) {
                $objEventFile = EventFiles::loadById($objEventFile->getId());
                $objEventFile->setMenuContentGroupId($objTargetGroup->getMenuContentId());
                $objEventFile->save();
            }

            foreach ($objFrontendLinks as $objFrontendLink) {
                $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                $objFrontendLinks->setGroupedId($objTargetGroup->getMenuContentId());
                $objFrontendLinks->setFrontendTitleSlug($this->objEvents->getTitleSlug());
                $objFrontendLinks->save();
                $afterCount++;
            }

            $this->dtgEventsCalendars->refresh();

            if (EventsSettings::countAll() !== 1) {
                Application::executeJavaScript("
                    $('.move-button-js').removeClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            }

            if ($beforeCount == $afterCount) {
                $this->dlgToast5->notify();
            } else {
                $this->dlgToast6->notify();
            }

            $this->updateLockStatus();
            $this->enableInputs();
        }

        /**
         * Handles the click event for saving event details. Validates input fields and
         * saves event and frontend link information if all required fields are filled in.
         * It locks the event if not already locked and resets input fields and UI elements
         * after a successful save operation.
         *
         * @param ActionParams $params An object containing parameters for the action event triggered.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!$this->txtYear->Text) {
                $this->txtYear->show();
                $this->dlgToast1->notify();
            } else if (!$this->lstGroupTitle->SelectedValue) {
                $this->lstGroupTitle->focus();
                $this->dlgToast3->notify();
            } elseif (!$this->txtTitle->Text) {
                $this->txtTitle->focus();
                $this->dlgToast2->notify();
            } else if ($this->txtYear->Text && $this->lstGroupTitle->SelectedValue && $this->txtTitle->Text) {

                $objEventGroup = EventsSettings::selectedByIdFromEventsSettings($this->lstGroupTitle->SelectedValue);
                $objTemplateLocking = FrontendTemplateLocking::load(8);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objEventsCalendar = new EventsCalendar();
                $objEventsCalendar->setPostDate(QDateTime::now());
                $objEventsCalendar->setYear($this->txtYear->Text);
                $objEventsCalendar->setTitle($this->txtTitle->Text);
                $objEventsCalendar->setMenuContentGroupId($objEventGroup->getMenuContentId());
                $objEventsCalendar->setMenuContentGroupTitleId($this->lstGroupTitle->SelectedValue);
                $objEventsCalendar->setEventsGroupName($this->lstGroupTitle->SelectedName);
                $objEventsCalendar->setStatus(2);
                $objEventsCalendar->setAssignedByUser($this->objUser->Id);
                $objEventsCalendar->setAuthor($objEventsCalendar->getAssignedByUserObject());
                $objEventsCalendar->saveEvent($this->txtYear->Text, $this->txtTitle->Text, $objEventGroup->getTitleSlug());
                $objEventsCalendar->save();

                $this->userOptions();
                $this->updatePortlet();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($objEventsCalendar->getId());
                $objFrontendLinks->setGroupedId($objEventGroup->getMenuContentId());
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtTitle->Text));
                $objFrontendLinks->setContentTypesManagamentId(8);
                $objFrontendLinks->setFrontendTitleSlug($objEventsCalendar->getTitleSlug());
                $objFrontendLinks->save();

                if ($objEventGroup->getEventsLocked() == 0) {
                    $objGroup = EventsSettings::loadById($objEventGroup->getId());
                    $objGroup->setEventsLocked(1);
                    $objGroup->save();
                }

                $this->btnAddEvent->Enabled = true;
                $this->btnMove->Enabled = true;
                $this->txtYear->Text = '';
                $this->lstGroupTitle->SelectedValue = null;
                $this->txtTitle->Text = '';
                $this->elementsReset();

                Application::redirect('event_calendar_edit.php' . '?id=' . $objEventsCalendar->Id . '&group=' . $objEventGroup->getMenuContentId());
            }
        }

        /**
         * Handles the click event for the cancel button. This method updates the visibility
         * of UI elements based on the number of total events, resets input fields, and
         * refreshes the group title list. It also re-enables the "Add Event" button and
         * removes the 'disabled' CSS class from the events calendar data grid.
         *
         * @param ActionParams $params An object containing parameters for the action event triggered.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (EventsSettings::countAll() !== 1) {
                Application::executeJavaScript("
                    $('.move-button-js').removeClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            }

            $this->btnAddEvent->Enabled = true;
            $this->lstGroupTitle->SelectedValue = null;
            $this->txtTitle->Text = '';
            $this->lstGroupTitle->refresh();

            $this->enableInputs();
            $this->dtgEventsCalendars->removeCssClass('disabled');

            $this->userOptions();
        }

        /**
         * Handles the click event for the Locked Cancel button. This method manages the visibility
         * of UI elements based on the number of event settings available and updates button states.
         * It also resets and refreshes event and group selections.
         *
         * @param ActionParams $params An object containing parameters for the action event triggered.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnLockedCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (EventsSettings::countAll() !== 1) {
                Application::executeJavaScript("
                    $('.move-button-js').removeClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            }

            $this->btnMove->Enabled = true;
            $this->btnAddEvent->Enabled = true;

            $this->lstEventsLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->lstEventsLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->enableInputs();
            $this->dtgEventsCalendars->removeCssClass('disabled');

            $this->userOptions();
        }

        /**
         * Handles the click event for the back button. This method redirects
         * the user to the menu manager page.
         *
         * @param ActionParams $params An object containing parameters for the action event triggered.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnBack_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            Application::executeJavaScript("history.go(-1);");
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the EventsCalendarTable instance. This method sets
         * up the table columns, pagination, and makes the table editable. It also assigns
         * the row parameters callback, specifies the column to be sorted, sets the sort
         * direction, and enables Ajax. Additionally, it determines the number of items
         * per a page based on user preferences.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgEventsCalendars_Create(): void
        {
            $this->dtgEventsCalendars = new EventsCalendarTable($this);
            $this->dtgEventsCalendars_CreateColumns();
            $this->createPaginators();
            $this->dtgEventsCalendars_MakeEditable();
            $this->dtgEventsCalendars->RowParamsCallback = [$this, "dtgEventsCalendars_GetRowParams"];
            $this->dtgEventsCalendars->SortColumnIndex = 2;
            $this->dtgEventsCalendars->SortDirection = -1;
            $this->dtgEventsCalendars->ItemsPerPage = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->dtgEventsCalendars->UseAjax = true;
        }

        /**
         * Creates columns for the events calendars data grid. This method utilizes
         * the createColumns function to dynamically generate and configure columns
         * for the data grid displaying event calendars.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgEventsCalendars_CreateColumns(): void
        {
            $this->dtgEventsCalendars->createColumns();
        }

        /**
         * Configures the datagrid to be editable by adding event actions and CSS classes.
         * This method sets up a cell click event on the datagrid, making each row clickable,
         * and applies a set of CSS classes to style the datagrid for better user interaction.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgEventsCalendars_MakeEditable(): void
        {
            $this->dtgEventsCalendars->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgEventsCalendarsRow_Click'));
            $this->dtgEventsCalendars->addCssClass('clickable-rows');
            $this->dtgEventsCalendars->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for a row in the event calendars data grid. This method
         * retrieves the event calendar by its ID, determines the associated menu content group ID,
         * and redirects the user to the event calendar edit page with the relevant parameters.
         *
         * @param ActionParams $params An object containing parameters for the action event triggered, including the event calendar ID.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgEventsCalendarsRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intEventCalendarId = intval($params->ActionParameter);
            $objEventCalendar = EventsCalendar::load($intEventCalendarId);
            $intGroup = $objEventCalendar->getMenuContentGroupId();
            Application::redirect('event_calendar_edit.php' . '?id=' . $intEventCalendarId . '&group=' . $intGroup);
        }

        /**
         * Retrieves the row parameters for the event calendars data grid, specifically fetching
         * the primary key from the provided row object and setting it as a data attribute for the row.
         *
         * @param object $objRowObject An object representing a row in the data grid.
         * @param int $intRowIndex The index of the row within the data grid.
         *
         * @return array An associative array containing row parameter data with the primary key as 'data-value'.
         */
        public function dtgEventsCalendars_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures the paginators for the events calendars data grid.
         * This method sets up two paginator instances with labels for navigation and
         * configures items per a page, sort index, and sort direction. It also enables
         * AJAX for the data grid and adds filter actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgEventsCalendars->Paginator = new Bs\Paginator($this);
            $this->dtgEventsCalendars->Paginator->LabelForPrevious = t('Previous');
            $this->dtgEventsCalendars->Paginator->LabelForNext = t('Next');

            $this->dtgEventsCalendars->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgEventsCalendars->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgEventsCalendars->PaginatorAlternate->LabelForNext = t('Next');

            $this->addFilterActions();
        }

        /**
         * Initializes the items per page selector for the assigned user. This method creates
         * a new Select2 instance and configures its properties such as theme, width, and selection mode.
         * It sets the default selected value and populates the list with available items. Additionally,
         * an AjaxControl change event is added to handle user interaction with the selector.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createItemsPerPage(): void
        {
            $this->lstItemsPerPageByAssignedUserObject = new Q\Plugin\Select2($this);
            $this->lstItemsPerPageByAssignedUserObject->MinimumResultsForSearch = -1;
            $this->lstItemsPerPageByAssignedUserObject->Theme = 'web-vauu';
            $this->lstItemsPerPageByAssignedUserObject->Width = '100%';
            $this->lstItemsPerPageByAssignedUserObject->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->PreferredItemsPerPageObject->getItemsPer();
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstPreferredItemsPerPageObject_GetItems());
            $this->lstItemsPerPageByAssignedUserObject->AddAction(new Change(), new AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
        }

        /**
         * Retrieves items for pagination by the assigned user object. It constructs a list
         * of items with selection status based on the assigned user's current pagination setting.
         *
         * @return ListItem[] An array of ListItem objects, each representing a pagination option
         *         for the assigned user, with the relevant option marked as selected.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstPreferredItemsPerPageObject_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objPreferredItemsPerPageObjectCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objPreferredItemsPerPageObjectCursor = ItemsPerPage::queryCursor($objCondition, $this->objPreferredItemsPerPageObjectClauses);

            // Iterate through the Cursor
            while ($objPreferredItemsPerPageObject = ItemsPerPage::instantiateCursor($objPreferredItemsPerPageObjectCursor)) {
                $objListItem = new ListItem($objPreferredItemsPerPageObject->__toString(), $objPreferredItemsPerPageObject->Id);
                if (($this->objUser->PreferredItemsPerPageObject) && ($this->objUser->PreferredItemsPerPageObject->Id == $objPreferredItemsPerPageObject->Id))
                    $objListItem->Selected = true;
                $a[] = $objListItem;
            }

            return $a;
        }

        /**
         * Updates the number of items displayed per page in the events calendar based on the selected value
         * from the items per page list and refreshes the calendar display.
         *
         * @param ActionParams $params An object containing parameters for the action event triggered.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgEventsCalendars->ItemsPerPage = ItemsPerPage::load($this->lstItemsPerPageByAssignedUserObject->SelectedValue)->getItemsPer();
            $this->dtgEventsCalendars->refresh();
        }

        /**
         * Initializes and configures a text box to be used as a search filter. The method
         * sets placeholder text, defines the text mode for search, disables autocomplete,
         * and applies a CSS class for styling. Additionally, it triggers the addition of
         * filter-specific actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createFilter(): void
        {
            $this->txtFilter = new Bs\TextBox($this);
            $this->txtFilter->Placeholder = t('Search...');
            $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
            $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
            $this->txtFilter->addCssClass('search-box');
            $this->addFilterActions();

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstYears = new Q\Plugin\Select2($this);
            $this->lstYears->MinimumResultsForSearch = -1;
            $this->lstYears->Theme = 'web-vauu';
            $this->lstYears->Width = '100%';
            $this->lstYears->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstYears->addItem(t('- Select year -'), null, true);
            $this->lstYears->addItems($this->clearDuplicateYears());

            $this->lstYears->addAction(new Change(), new AjaxControl($this,'lstYears_Change'));
            $this->lstYears->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstGroups = new Q\Plugin\Select2($this);
            $this->lstGroups->MinimumResultsForSearch = -1;
            $this->lstGroups->Theme = 'web-vauu';
            $this->lstGroups->Width = '100%';
            $this->lstGroups->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroups->addItem(t('- Select events calendar group -'), null, true);

            $objGroups = EventsSettings::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::EventsSettings()->EventsLocked, 0), QQN::EventsSettings()->Id)
                ]
            );

            $countByEventsLocked = EventsSettings::countByEventsLocked(1);

            foreach ($objGroups as $objTitle) {
                if ($countByEventsLocked > 1 && $objTitle->EventsLocked === 1) {
                    $this->lstGroups->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstGroups->addAction(new Change(), new AjaxControl($this,'lstGroups_Change'));
            $this->lstGroups->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstTargets = new Q\Plugin\Select2($this);
            $this->lstTargets->MinimumResultsForSearch = -1;
            $this->lstTargets->Theme = 'web-vauu';
            $this->lstTargets->Width = '100%';
            $this->lstTargets->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstTargets->addItem(t('- Select target group -'), null, true);

            $objTargets = TargetGroupOfCalendar::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::TargetGroupOfCalendar()->TargetLocked, 0), QQN::TargetGroupOfCalendar()->Id)
                ]
            );

            foreach ($objTargets as $objTitle) {
                if ($objTitle->TargetLocked === 1) {
                    $this->lstTargets->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstTargets->addAction(new Change(), new AjaxControl($this,'lstTargets_Change'));
            $this->lstTargets->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstChanges = new Q\Plugin\Select2($this);
            $this->lstChanges->MinimumResultsForSearch = -1;
            $this->lstChanges->Theme = 'web-vauu';
            $this->lstChanges->Width = '100%';
            $this->lstChanges->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstChanges->addItem(t('- Select change -'), null, true);

            $objChanges = EventsChanges::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::EventsChanges()->EventsChangeLocked, 0), QQN::EventsChanges()->Id)
                ]
            );

            foreach ($objChanges as $objTitle) {
                if ($objTitle->EventsChangeLocked === 1) {
                    $this->lstChanges->addItem($objTitle->Title, $objTitle->Id);
                }
            }

            $this->lstChanges->addAction(new Change(), new AjaxControl($this,'lstChanges_Change'));
            $this->lstChanges->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->btnClearFilters = new Bs\Button($this);
            $this->btnClearFilters->Text = t('Clear filters');
            $this->btnClearFilters->addWrapperCssClass('center-button');
            $this->btnClearFilters->CssClass = 'btn btn-default';
            $this->btnClearFilters->setCssStyle('float', 'left');
            $this->btnClearFilters->CausesValidation = false;
            $this->btnClearFilters->addAction(new Click(), new AjaxControl($this, 'clearFilters_Click'));

            $this->updateLockStatus();
            $this->addFilterActions();
        }

        /**
         * Filters and returns unique years from the SportsCalendar records where the status is active.
         * Iterates through all loaded items, identifies active ones, and collects their years.
         *
         * @return array An array of unique years derived from active SportsCalendar records.
         * @throws Caller
         */
        public function clearDuplicateYears(): array
        {
            $allItems = EventsCalendar::loadAll();
            $uniqueYears = [];

            foreach ($allItems as $item) {
                $uniqueYears[] = $item->Year;
            }

            return array_unique($uniqueYears);
        }

        /**
         * Updates the lock status of various components based on specific conditions.
         * This method performs checks on duplicate years, locked events, locked targets,
         * and changes in locked events, and updates the visibility of corresponding UI elements.
         * Following these checks, it refreshes the respective dropdown lists.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function updateLockStatus(): void
        {
            $countByYears = count($this->clearDuplicateYears());

            if ($countByYears > 1) {
                Application::executeJavaScript("$('.js-years').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-years').addClass('hidden');");
            }

            $countByEventsLocked = EventsSettings::countByEventsLocked(1);

            if ($countByEventsLocked > 1) {
                Application::executeJavaScript("$('.js-groups').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-groups').addClass('hidden');");
            }

            $this->lstGroups->refresh();

            $countByTargetsLocked = TargetGroupOfCalendar::countByTargetLocked(1);

            if ($countByTargetsLocked > 0) {
                Application::executeJavaScript("$('.js-targets').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-targets').addClass('hidden');");
            }

            $this->lstTargets->refresh();

            $countByEventsChangeLocked = EventsChanges::countByEventsChangeLocked(1);

            if ($countByEventsChangeLocked > 0) {
                Application::executeJavaScript("$('.js-changes').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-changes').addClass('hidden');");
            }

            $this->lstChanges->refresh();
        }

        /**
         * Disables various input controls and resets their values.
         *
         * @return void
         */
        protected function disableInputs(): void
        {
            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->lstItemsPerPageByAssignedUserObject->refresh();

            $this->txtFilter->Text = '';
            $this->txtFilter->Enabled = false;
            $this->txtFilter->refresh();

            $this->lstYears->SelectedValue = null;
            $this->lstYears->Enabled = false;
            $this->lstYears->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->Enabled = false;
            $this->lstGroups->refresh();

            $this->lstTargets->SelectedValue = null;
            $this->lstTargets->Enabled = false;
            $this->lstTargets->refresh();

            $this->lstChanges->SelectedValue = null;
            $this->lstChanges->Enabled = false;
            $this->lstChanges->refresh();

            $this->btnClearFilters->Enabled = false;
            $this->btnClearFilters->refresh();

            $this->dtgEventsCalendars->refresh();
        }

        /**
         * Enables a series of input controls and clears their current values as well as refreshes their state.
         *
         * @return void
         */
        protected function enableInputs(): void
        {
            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->lstItemsPerPageByAssignedUserObject->refresh();

            $this->txtFilter->Text = '';
            $this->txtFilter->Enabled = true;
            $this->txtFilter->refresh();

            $this->lstYears->SelectedValue = null;
            $this->lstYears->Enabled = true;
            $this->lstYears->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->Enabled = true;
            $this->lstGroups->refresh();

            $this->lstTargets->SelectedValue = null;
            $this->lstTargets->Enabled = true;
            $this->lstTargets->refresh();

            $this->lstChanges->SelectedValue = null;
            $this->lstChanges->Enabled = true;
            $this->lstChanges->refresh();

            $this->btnClearFilters->Enabled = true;
            $this->btnClearFilters->refresh();

            $this->dtgEventsCalendars->refresh();
        }

        /**
         * Handles the change event for the list of years.
         * This function triggers the refresh of the sports areas datagrid when the year selection changes.
         *
         * @param ActionParams $params Parameters associated with the action, providing context for the event.
         *
         * @return void
         */
        protected function lstYears_Change(ActionParams $params): void
        {
            $this->dtgEventsCalendars->refresh();
        }

        /**
         * Handles the change event for the search list and refreshes the news data grid.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         */
        protected function lstGroups_Change(ActionParams $params): void
        {
            $this->dtgEventsCalendars->refresh();
        }

        /**
         * Handles the "Change" event for the lstTargets list. This method refreshes the
         * events calendar data grid to update the displayed information based on the
         * new selection or changes.
         *
         * @param ActionParams $params An object containing parameters for the action event triggered.
         *
         * @return void
         */
        protected function lstTargets_Change(ActionParams $params): void
        {
            $this->dtgEventsCalendars->refresh();
        }

        /**
         * Handles changes made to the list of changes.
         * Refreshes the news data grid in response to the action triggered.
         *
         * @param ActionParams $params The parameters associated with the action triggering this change.
         *
         * @return void
         */
        protected function lstChanges_Change(ActionParams $params): void
        {
            $this->dtgEventsCalendars->refresh();
        }

        /**
         * Clears all applied filters in the current view, resetting text and dropdown fields
         * and refreshing associated controls.
         *
         * @param ActionParams $params The parameters passed to the action, containing any additional
         *         information about the event triggered.
         *
         * @return void This method does not return a value.
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->lstYears->SelectedValue = null;
            $this->lstYears->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->refresh();

            $this->lstTargets->SelectedValue = null;
            $this->lstTargets->refresh();

            $this->lstChanges->SelectedValue = null;
            $this->lstChanges->refresh();

            $this->dtgEventsCalendars->refresh();
        }

        /**
         * Registers actions for the filter input control. This method binds certain events to
         * AJAX actions, enabling dynamic interaction with the filter component.
         * Specifically, it sets up an input event with a delay and an enter key event to trigger
         * specified callback methods.
         *
         * @return void
         * @throws Caller
         */
        protected function addFilterActions(): void
        {
            $this->txtFilter->addAction(new Input(300), new AjaxControl($this, 'filterChanged'));
            $this->txtFilter->addActionArray(new EnterKey(),
                [
                    new AjaxControl($this, 'FilterChanged'),
                    new Terminate()
                ]
            );
        }

        /**
         * Invoked when the filter criteria are changed. This method refreshes the events
         * calendar data grid to reflect the updated filter conditions.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgEventsCalendars->refresh();
        }

        /**
         * Binds data to the events calendar. This method retrieves a condition
         * object and uses it to fetch and display relevant data in the data grid
         * for events calendars.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgEventsCalendars->bindData($objCondition);
        }

        /**
         * Constructs and returns a condition object based on user-selected filters. This method evaluates
         * various input fields (year, group, target, changes, and text criteria) to create a dynamic set
         * of conditions. If no filters are specified, it returns a condition to match all records.
         *
         * @return All|AndCondition|OrCondition The resulting condition object, which could represent
         *                                       in all records, a combination of conditions joined with AND,
         *                                       or a set of conditions joined with OR.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function getCondition(): All|AndCondition|OrCondition
        {
            $strText = trim($this->txtFilter->Text ?? '');
            $intYearId = (int)$this->lstYears->SelectedName;
            $intGroupId = $this->lstGroups->SelectedValue; // ID value
            $intTargetId = $this->lstTargets->SelectedValue; // ID value
            $intEventsChangesId = $this->lstChanges->SelectedValue; // ID value

            $condList = [];

            // If a year selected
            if (!empty($intYearId)) {
                $condList[] = QQ::equal(QQN::EventsCalendar()->Year, $intYearId); // or the correct field that you have binding
            }

            // If a group selected
            if (!empty($intGroupId)) {
                $condList[] = QQ::equal(QQN::EventsCalendar()->MenuContentGroupTitleId, $intGroupId); // or the correct field that you have binding
            }

            // If a sports area selected
            if (!empty($intTargetId)) {
                $condList[] = QQ::equal(QQN::EventsCalendar()->TargetGroupId, $intTargetId); // or the correct field that you have binding
            }

            // If a change selected
            if (!empty($intEventsChangesId)) {
                $condList[] = QQ::equal(QQN::EventsCalendar()->EventsChangesId, $intEventsChangesId); // or the correct field that you have binding
            }

            // If a text is entered
            if ($strText !== '') {
                // Do one big 'or' for multiple fields in the text
                $orText = QQ::orCondition(
                    QQ::like(QQN::EventsCalendar()->Year, "%" . $strText . "%"),
                    QQ::like(QQN::EventsCalendar()->EventsGroupName, "%" . $strText . "%"),
                    QQ::like(QQN::EventsCalendar()->Title, "%" . $strText . "%"),
                    QQ::like(QQN::EventsCalendar()->Author, "%" . $strText . "%"),
                    QQ::equal(QQN::EventsCalendar()->Status,  "%" . $strText . "%")
                );
                $condList[] = $orText;
            }

            // If neither filter is present, return all
            if (count($condList) === 0) {
                return QQ::all();
            }

            // If both conditions are met, combine with AND
            return QQ::andCondition(...$condList);
        }
    }