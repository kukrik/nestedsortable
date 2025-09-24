<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Event\EscapeKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\Condition\AndCondition;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\CellClick;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\Input;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Query\Condition\All;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;

    /**
     * Class SportsCalendarListPanel
     *
     * Extends the Panel class to provide a customized interface for managing
     * sports calendar events. This panel allows users to navigate, filter,
     * and interact with sports calendars and related events. It also includes
     * input controls, buttons, modals, and configurations specific to the
     * sports calendar application context. The panel is designed to interact
     * with the underlying business logic and database entities.
     */
    class SportsCalendarListPanel extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

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
        public SportsCalendarTable $dtgSportsCalendars;

        public Bs\Button $btnAddEvent;
        public Bs\Button $btnMove;
        public ?Q\Plugin\Select2 $lstEventsLocked = null;
        public Q\Plugin\Select2 $lstTargetGroup;
        public Q\Plugin\Select2 $lstYears;
        public Q\Plugin\Select2 $lstGroups;
        public Q\Plugin\Select2 $lstSportsAreas;
        public ?Q\Plugin\Select2 $lstChanges = null;

        public Q\Plugin\YearPicker $txtYear;
        public ?Q\Plugin\Select2 $lstGroupTitle = null;
        public Bs\TextBox $txtTitle;
        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnLockedCancel;
        public Bs\Button $btnBack;

        protected int $intId;
        protected object $objUser;
        protected int $intLoggedUserId;
        protected object $objAssignedUser;
        protected object $objMenuContent;
        protected object $objSportsCalendar;
        protected ?object $objEvents = null;

        protected ?object $objTargetGroupCondition = null;
        protected ?array $objTargetGroupClauses = null;

        protected string $strTemplate = 'SportsCalendarListPanel.tpl.php';

        /**
         * Constructor for initializing the class with a parent object and optional control ID.
         *
         * @param mixed $objParentObject The parent object to which this control belongs.
         * @param string|null $strControlId Optional control ID for the instance. Defaults to null.
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

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session

            $this->intLoggedUserId = 4;
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
            $this->elementsReset();

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgSportsCalendars_Create();
            $this->dtgSportsCalendars->setDataBinder('bindData', $this);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures input controls including a year picker, a text box for event title,
         * and a group selection dropdown for target groups in the sports calendar application.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createInputs(): void
        {
            $this->txtYear = new Q\Plugin\YearPicker($this);
            $this->txtYear->Language = 'ee';
            $this->txtYear->TodayBtn = true;
            $this->txtYear->ClearBtn = true;
            $this->txtYear->AutoClose = true;
            $this->txtYear->ActionParameter = $this->txtYear->ControlId;
            $this->txtYear->setHtmlAttribute('autocomplete', 'off');
            $this->txtYear->setCssStyle('margin-left', '10px');
            $this->txtYear->Width = '100%';
            $this->txtYear->addAction(new Change(), new AjaxControl($this, 'setYear'));

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
            $this->lstTargetGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstTargetGroup->addItem(t('- Select one sports calendar group -'), null, true);

            $objTargetGroups = SportsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::SportsSettings()->Id)));
            foreach ($objTargetGroups as $objTitle) {
                if ($objTitle->IsReserved !== 2) {
                    $this->lstTargetGroup->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstTargetGroup->addAction(new Change(), new AjaxControl($this,'lstTargetGroup_Change'));
            $this->lstTargetGroup->Enabled = false;
        }

        /**
         * Initializes and configures multiple buttons within the interface.
         * It sets properties such as text, icons, CSS classes, and event handlers
         * for each button. Additionally, it manages the visibility of certain
         * elements based on conditions.
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

            if (SportsSettings::countAll() !== 1) {
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
         * Initializes and configures multiple Toastr notification instances used for displaying
         * various alert messages to the user. Each Toastr instance is set up with a specific
         * alert type, position, message, and additional options such as progress bar visibility and
         * timeout duration.
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
            $this->dlgToast3->Message = t('<p style=\"margin-bottom: 5px;\">The sports calendar group must be selected beforehand!</p>');
            $this->dlgToast3->ProgressBar = true;
            $this->dlgToast3->TimeOut = 10000;
            $this->dlgToast3->EscapeHtml = false;

            $this->dlgToast4 = new Q\Plugin\Toastr($this);
            $this->dlgToast4->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast4->Message = t('<p style=\"margin-bottom: 5px;\">The sports calendar group cannot be the same as the target group!</p>');
            $this->dlgToast4->ProgressBar = true;
            $this->dlgToast4->TimeOut = 10000;
            $this->dlgToast4->EscapeHtml = false;

            $this->dlgToast5 = new Q\Plugin\Toastr($this);
            $this->dlgToast5->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast5->Message = t('<strong>Well done!</strong> The transfer of sports events to the new group was successful.');
            $this->dlgToast5->ProgressBar = true;

            $this->dlgToast6 = new Q\Plugin\Toastr($this);
            $this->dlgToast6->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast6->Message = t('The transfer of sports events to the new group failed.');
            $this->dlgToast6->ProgressBar = true;
        }

        /**
         * Creates and configures modal dialogs used for transferring sports events between event groups.
         *
         * This method initializes a new modal with specific texts, titles, styles, and actions to
         * facilitate user interaction when moving sports events. It sets up buttons for accepting
         * and cancelling the action and defines the actions to take when buttons are clicked
         * or when the modal is hidden.
         *
         * @return void
         * @throws Caller
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move the sports events from this event group to another event group?</p>
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
         * Resets UI elements by adding the 'hidden' class to elements with specific CSS classes.
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
         * Sets the year by looking up the control specified by the action parameter
         * and assigning its text value to the year text box.
         *
         * @param ActionParams $params The parameters containing the action information, including the action parameter
         *     used to look up the control.
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
         * Handles the click event for the "Add Event" button, setting up the necessary UI elements and states for event creation.
         *
         * @param ActionParams $params The parameters associated with the action triggering this button click.
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

            $this->txtTitle->Text = '';
            $this->txtYear->show();
            $this->dtgSportsCalendars->addCssClass('disabled');
            $this->btnAddEvent->Enabled = false;

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '100%';
            $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroupTitle->addItem(t('- Select one sports calendar group -'), null, true);

            $objGroups = SportsSettings::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::SportsSettings()->EventsLocked, 0), QQN::SportsSettings()->Id)
                ]
            );

            $countByIsReserved = SportsSettings::countByIsReserved(1);

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

            $this->btnAddEvent->Enabled = false;
            $this->dtgSportsCalendars->addCssClass('disabled');
        }

        /**
         * Handles the click event for the "Move" button. This method updates the JavaScript state,
         * initializes and configures a Select2 list box for selecting sports calendar groups,
         * and manages the enabled state and focus of various controls based on the count of locked events.
         *
         * @param ActionParams $params The parameters related to the action event.
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

            $this->lstEventsLocked = new Q\Plugin\Select2($this);
            $this->lstEventsLocked->MinimumResultsForSearch = -1;
            $this->lstEventsLocked->Theme = 'web-vauu';
            $this->lstEventsLocked->Width = '100%';
            $this->lstEventsLocked->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstEventsLocked->addItem(t('- Select one sports calendar group -'), null, true);

            $objGroups = SportsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::SportsSettings()->Id)));
            $countLocked = SportsSettings::countByEventsLocked(1);

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

            $countLocked = SportsSettings::countByEventsLocked(1);

            if ($countLocked === 1) {
                $this->lstEventsLocked->Enabled = false;
                $this->lstTargetGroup->Enabled = true;
                $this->lstTargetGroup->focus();
            } else {
                $this->lstEventsLocked->Enabled = true;
                $this->lstEventsLocked->focus();
            }

            $this->btnAddEvent->Enabled = false;
            $this->dtgSportsCalendars->addCssClass('disabled');
        }

        /**
         * Handles the change event for the group title list.
         * Performs various checks and actions based on the state of other UI components.
         *
         * @param ActionParams $params The parameters associated with the action event.
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
         * Handles the change event for the lstEventsLocked element, enabling or disabling
         * the lstTargetGroup based on the selected value, and managing CSS classes and notifications.
         *
         * @param ActionParams $params The parameters received from the action triggering the change.
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
         * Handles the change event of the target group list.
         *
         * This method checks if the selected value of the target group matches the selected value of events locked,
         * ensuring they are not the same and managing UI feedback accordingly.
         *
         * @param ActionParams $params The parameters associated with the action event.
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
         * Handles the cancellation of a transfer operation, resetting the relevant user interface elements
         * and enabling interaction with the controls again.
         *
         * @param ActionParams $params Parameters associated with the transfer cancellation event.
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
            $this->dtgSportsCalendars->removeCssClass('disabled');
        }

        /**
         * Handles the click event for moving items.
         *
         * @param ActionParams $params The parameters associated with the action event.
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
        }

        /**
         * Handles the transfer of events between groups and updates the necessary settings and UI elements.
         *
         * This method is responsible for:
         * - Unlocking and locking event settings for the specified source and target groups.
         * - Updating the event group information such as the menu content group ID and title.
         * - Adjusting frontend links to reflect changes in grouping and titles.
         * - Refreshing the data grid displaying sports calendars.
         * - Executing JavaScript to adjust UI elements based on the state of settings.
         * - Notifying the user if the transfer was successful or if there was a discrepancy.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        private function eventsTransferOperations(): void
        {
            $objLockedGroup = SportsSettings::loadById($this->lstEventsLocked->SelectedValue);
            $objTargetGroup = SportsSettings::loadById($this->lstTargetGroup->SelectedValue);
            $objFrontendLinks = FrontendLinks::loadArrayByGroupedId($objLockedGroup->getMenuContentId());
            $objEventsGroupArray = SportsCalendar::loadArrayByMenuContentGroupId($objLockedGroup->getMenuContentId());

            $objSportsFiles = SportsTables::loadArrayByMenuContentGroupId($objLockedGroup->getMenuContentId());

            $beforeCount = count(FrontendLinks::loadArrayByGroupedId($objLockedGroup->getMenuContentId()));
            $afterCount = 0;

            $objEventsSettings = SportsSettings::loadById($objLockedGroup->getId());
            $objEventsSettings->setEventsLocked(0);
            $objEventsSettings->save();

            $objEventsSettings = SportsSettings::loadById($objTargetGroup->getId());
            $objEventsSettings->setEventsLocked(1);
            $objEventsSettings->save();

            foreach ($objEventsGroupArray as $objEventsGroup) {
                $this->objEvents = SportsCalendar::loadById($objEventsGroup->getId());
                $this->objEvents->setMenuContentGroupId($objTargetGroup->getMenuContentId());
                $this->objEvents->setMenuContentGroupTitleId($this->lstTargetGroup->SelectedValue);

                $this->objEvents->updateSportsEvent($this->objEvents->getYear(), $this->objEvents->getTitle(), $objTargetGroup->getTitleSlug());
                $this->objEvents->save();
            }

            foreach ($objSportsFiles as $objSportsFile) {
                $objSportsFile = SportsTables::loadById($objSportsFile->getId());
                $objSportsFile->setMenuContentGroupId($objTargetGroup->getMenuContentId());
                $objSportsFile->save();
            }

            foreach ($objFrontendLinks as $objFrontendLink) {
                $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                $objFrontendLinks->setGroupedId($objTargetGroup->getMenuContentId());
                $objFrontendLinks->setFrontendTitleSlug($this->objEvents->getTitleSlug());
                $objFrontendLinks->save();
                $afterCount++;
            }

            $this->dtgSportsCalendars->refresh();

            if (SportsSettings::countAll() !== 1) {
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
         * Handles the click event for the save a button, performing validation checks on input fields
         * and creating or updating a sports calendar entry with the provided data.
         *
         * @param ActionParams $params Parameters sent with the action, typically including context and input values.
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

                $objEventGroup = SportsSettings::selectedByIdFromSportsSettings($this->lstGroupTitle->SelectedValue);
                $objTemplateLocking = FrontendTemplateLocking::load(10);
                $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

                $objSportsCalendar = new SportsCalendar();
                $objSportsCalendar->setPostDate(Q\QDateTime::now());
                $objSportsCalendar->setAssignedByUser($this->objUser->Id);
                $objSportsCalendar->setYear($this->txtYear->Text);
                $objSportsCalendar->setTitle($this->txtTitle->Text);
                $objSportsCalendar->setMenuContentGroupId($objEventGroup->getMenuContentId());
                $objSportsCalendar->setMenuContentGroupTitleId($this->lstGroupTitle->SelectedValue);
                $objSportsCalendar->setStatus(2);
                $objSportsCalendar->setAssignedByUser($this->objUser->Id);
                $objSportsCalendar->setAuthor($objSportsCalendar->getAssignedByUserObject());
                $objSportsCalendar->saveSportsEvent($this->txtYear->Text, $this->txtTitle->Text, $objEventGroup->getTitleSlug());
                $objSportsCalendar->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($objSportsCalendar->getId());
                $objFrontendLinks->setGroupedId($objEventGroup->getMenuContentId());
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtTitle->Text));
                $objFrontendLinks->setContentTypesManagamentId(10);
                $objFrontendLinks->setFrontendTitleSlug($objSportsCalendar->getTitleSlug());
                $objFrontendLinks->save();

                if ($objEventGroup->getEventsLocked() == 0) {
                    $objGroup = SportsSettings::loadById($objEventGroup->getId());
                    $objGroup->setEventsLocked(1);
                    $objGroup->save();
                }

                $this->btnAddEvent->Enabled = true;
                $this->btnMove->Enabled = true;
                $this->txtYear->Text = '';
                $this->lstGroupTitle->SelectedValue = null;
                $this->txtTitle->Text = '';
                $this->elementsReset();

                Application::redirect('sports_calendar_edit.php' . '?id=' . $objSportsCalendar->Id . '&group=' . $objEventGroup->getMenuContentId());

            }
        }

        /**
         * Handles the event when the cancel button is clicked. This method updates the user interface
         * based on the number of sports settings and resets form fields and controls to their default states.
         *
         * @param ActionParams $params The parameters passed to the event handler.
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

            if (SportsSettings::countAll() !== 1) {
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
            $this->txtYear->Text = '';
            $this->txtTitle->Text = '';
            $this->lstGroupTitle->refresh();

            $this->enableInputs();
            $this->dtgSportsCalendars->removeCssClass('disabled');
        }

        /**
         * Handles the click event for the locked cancel button. This method updates the UI components and
         * executes JavaScript to manipulate the visibility of specific elements based on certain conditions.
         *
         * @param ActionParams $params The parameters associated with the action event.
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

            if (SportsSettings::countAll() !== 1) {
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
            $this->dtgSportsCalendars->removeCssClass('disabled');
        }

        /**
         * Handles the click event for the back button.
         *
         * @param ActionParams $params The parameters for the action triggered by the back button click event.
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

            Application::redirect('menu_manager.php');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the sports calendars data table for display and interaction.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgSportsCalendars_Create(): void
        {
            $this->dtgSportsCalendars = new SportsCalendarTable($this);
            $this->dtgSportsCalendars_CreateColumns();
            $this->createPaginators();
            $this->dtgSportsCalendars_MakeEditable();
            $this->dtgSportsCalendars->RowParamsCallback = [$this, "dtgSportsCalendars_GetRowParams"];
            $this->dtgSportsCalendars->SortColumnIndex = 7;
            //$this->dtgSportsCalendars->SortDirection = 1;
            $this->dtgSportsCalendars->UseAjax = true;
            $this->dtgSportsCalendars->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
        }

        /**
         * Creates and configures columns for the sports calendars data grid.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgSportsCalendars_CreateColumns(): void
        {
            $this->dtgSportsCalendars->createColumns();
        }

        /**
         * Configures the sports calendars data grid to be editable by adding actions and CSS styling.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgSportsCalendars_MakeEditable(): void
        {
            $this->dtgSportsCalendars->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgSportsCalendarsRow_Click'));
            $this->dtgSportsCalendars->addCssClass('clickable-rows');
            $this->dtgSportsCalendars->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event for a sports calendar row in the data grid, retrieving the associated event calendar
         * and performing a redirect to an edit page with dynamic parameters.
         *
         * @param ActionParams $params An instance containing parameters for the action, including the ID of the event calendar row clicked.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgSportsCalendarsRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intEventCalendarId = intval($params->ActionParameter);
            $objEventCalendar = SportsCalendar::load($intEventCalendarId);
            $intGroup = $objEventCalendar->getMenuContentGroupId();
            Application::redirect('sports_calendar_edit.php' . '?id=' . $intEventCalendarId . '&group=' . $intGroup);
        }

        /**
         * Generates and returns an array of parameters for a row in the sports calendars data grid.
         *
         * @param object $objRowObject The row object from which parameters are to be extracted.
         * @param int $intRowIndex The index of the current row in the data grid.
         *
         * @return array An associative array containing parameters for the data grid row, including a 'data-value' key set to the primary key of the row object.
         */
        public function dtgSportsCalendars_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Configures pagination for the sports calendars data grid component, including the creation
         * of primary and alternate paginators with labels and setting relevant properties such as
         * items per a page, initial sort column, and enabling Ajax functionality.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgSportsCalendars->Paginator = new Bs\Paginator($this);
            $this->dtgSportsCalendars->Paginator->LabelForPrevious = t('Previous');
            $this->dtgSportsCalendars->Paginator->LabelForNext = t('Next');

            $this->dtgSportsCalendars->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgSportsCalendars->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgSportsCalendars->PaginatorAlternate->LabelForNext = t('Next');

            $this->dtgSportsCalendars->ItemsPerPage = 10;
            $this->dtgSportsCalendars->SortColumnIndex = 0;
            $this->dtgSportsCalendars->UseAjax = true;
            $this->addFilterActions();
        }

        /**
         * Initializes and configures the Select2 component for selecting items per a page by the assigned user.
         * Sets various parameters such as theme, selection mode, and preselected value,
         * and populates the component with items.
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
            $this->lstItemsPerPageByAssignedUserObject->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->ItemsPerPageByAssignedUser;
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstItemsPerPageByAssignedUserObject_GetItems());
            $this->lstItemsPerPageByAssignedUserObject->AddAction(new Change(), new AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
        }

        /**
         * Retrieves a list of items per a page associated with the assigned user object.
         *
         * @return ListItem[] An array of ListItem instances representing the items per page by the assigned user object.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objItemsPerPageByAssignedUserObjectCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objItemsPerPageByAssignedUserObjectCursor = ItemsPerPage::queryCursor($objCondition, $this->objItemsPerPageByAssignedUserObjectClauses);

            // Iterate through the Cursor
            while ($objItemsPerPageByAssignedUserObject = ItemsPerPage::instantiateCursor($objItemsPerPageByAssignedUserObjectCursor)) {
                $objListItem = new ListItem($objItemsPerPageByAssignedUserObject->__toString(), $objItemsPerPageByAssignedUserObject->Id);
                if (($this->objUser->ItemsPerPageByAssignedUserObject) && ($this->objUser->ItemsPerPageByAssignedUserObject->Id == $objItemsPerPageByAssignedUserObject->Id))
                    $objListItem->Selected = true;
                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Handles the change event for the Items Per Page selection by the assigned user object.
         *
         * @param ActionParams $params The parameters associated with the action event.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgSportsCalendars->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgSportsCalendars->refresh();
        }

        /**
         * Initializes and configures a search filter input field.
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
            $this->lstYears->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstYears->addItem(t('- Select year -'), null, true);
            $this->lstYears->addItems($this->clearDuplicateYears());

            $this->lstYears->addAction(new Change(), new AjaxControl($this,'lstYears_Change'));
            $this->lstYears->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstGroups = new Q\Plugin\Select2($this);
            $this->lstGroups->MinimumResultsForSearch = -1;
            $this->lstGroups->Theme = 'web-vauu';
            $this->lstGroups->Width = '100%';
            $this->lstGroups->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroups->addItem(t('- Select sports calendar group -'), null, true);

            $objGroups = SportsSettings::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::SportsSettings()->EventsLocked, 0), QQN::SportsSettings()->Id)
                ]
            );

            $countByEventsLocked = SportsSettings::countByEventsLocked(1);

            foreach ($objGroups as $objTitle) {
                if ($countByEventsLocked > 1 && $objTitle->EventsLocked === 1) {
                    $this->lstGroups->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstGroups->addAction(new Change(), new AjaxControl($this,'lstGroups_Change'));
            $this->lstGroups->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstSportsAreas = new Q\Plugin\Select2($this);
            $this->lstSportsAreas->MinimumResultsForSearch = -1;
            $this->lstSportsAreas->Theme = 'web-vauu';
            $this->lstSportsAreas->Width = '100%';
            $this->lstSportsAreas->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstSportsAreas->addItem(t('- Select sports area -'), null, true);

            foreach ($this->getUniqueSportsAreas() as $value) {
                $objSportsAreas = SportsAreas::loadById($value);
                $this->lstSportsAreas->addItem($objSportsAreas->Name, $value);
            }

            $this->lstSportsAreas->addAction(new Change(), new AjaxControl($this,'lstSportsAreas_Change'));
            $this->lstSportsAreas->setCssStyle('float', 'left');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstChanges = new Q\Plugin\Select2($this);
            $this->lstChanges->MinimumResultsForSearch = -1;
            $this->lstChanges->Theme = 'web-vauu';
            $this->lstChanges->Width = '100%';
            $this->lstChanges->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstChanges->addItem(t('- Select change -'), null, true);

            $objChanges = SportsChanges::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::SportsChanges()->SportsChangeLocked, 0), QQN::SportsChanges()->Id)
                ]
            );

            foreach ($objChanges as $objTitle) {
                if ($objTitle->SportsChangeLocked === 1) {
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
         * Removes duplicate years from the collection of sports table items.
         * This method loads all items from the sports tables, extracts the years,
         * and ensures only unique year values are returned.
         *
         * @return int[] An array of unique years extracted from the sports table items.
         * @throws Caller
         */
        public function clearDuplicateYears(): array
        {
            $allItems = SportsCalendar::loadAll();
            $uniqueYears = [];

            foreach ($allItems as $item) {
                $uniqueYears[] = $item->Year;
            }

            return array_unique($uniqueYears);
        }

        /**
         * Retrieves a list of unique sports area IDs.
         * This method iterates through all sports tables and adds the unique sports area IDs to the result.
         *
         * @return int[] An array of unique sports area IDs.
         * @throws Caller
         */
        public function getUniqueSportsAreas(): array
        {
            $allItems = SportsCalendar::loadAll();
            $uniqueSportsAreas = [];

            foreach ($allItems as $item) {
                if (!in_array($item->SportsAreasId, $uniqueSportsAreas)) {
                    $uniqueSportsAreas[] = $item->SportsAreasId;
                }
            }

            return $uniqueSportsAreas;
        }

        /**
         * Updates the lock status of various elements and refreshes the associated lists.
         *
         * The method performs checks on the count of locked items in different categories
         * (groups, changes, and categories) and applies visibility changes to the respective
         * UI elements using JavaScript. Each associated list is refreshed after the visibility update.
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

            $countByEventsLocked = SportsSettings::countByEventsLocked(1);

            if ($countByEventsLocked > 1) {
                Application::executeJavaScript("$('.js-groups').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-groups').addClass('hidden');");
            }

            $this->lstGroups->refresh();

            $countBySportsAreas = count($this->getUniqueSportsAreas());

            if ($countBySportsAreas > 1) {
                Application::executeJavaScript("$('.js-sports-areas').removeClass('hidden');");
            } else {
                Application::executeJavaScript("$('.js-sports-areas').addClass('hidden');");
            }

            $this->lstSportsAreas->refresh();

            $countBySportsChangeLocked = SportsChanges::countBySportsChangeLocked(1);

            if ($countBySportsChangeLocked > 0) {
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

            $this->lstSportsAreas->SelectedValue = null;
            $this->lstSportsAreas->Enabled = false;
            $this->lstSportsAreas->refresh();

            $this->lstChanges->SelectedValue = null;
            $this->lstChanges->Enabled = false;
            $this->lstChanges->refresh();

            $this->btnClearFilters->Enabled = false;
            $this->btnClearFilters->refresh();

            $this->dtgSportsCalendars->refresh();
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

            $this->lstSportsAreas->SelectedValue = null;
            $this->lstSportsAreas->Enabled = true;
            $this->lstSportsAreas->refresh();

            $this->lstChanges->SelectedValue = null;
            $this->lstChanges->Enabled = true;
            $this->lstChanges->refresh();

            $this->btnClearFilters->Enabled = true;
            $this->btnClearFilters->refresh();

            $this->dtgSportsCalendars->refresh();
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
            $this->dtgSportsCalendars->refresh();
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
            $this->dtgSportsCalendars->refresh();
        }

        /**
         * Handles the change event for the sports areas list.
         *
         * @param ActionParams $params The parameters associated with the change event.
         *
         * @return void
         */
        protected function lstSportsAreas_Change(ActionParams $params): void
        {
            $this->dtgSportsCalendars->refresh();
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
            $this->dtgSportsCalendars->refresh();
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

            $this->lstSportsAreas->SelectedValue = null;
            $this->lstSportsAreas->refresh();

            $this->lstChanges->SelectedValue = null;
            $this->lstChanges->refresh();

            $this->dtgSportsCalendars->refresh();
        }

        /**
         * Adds actions to the filter input field to handle user interactions.
         *
         * Sets up an event to trigger an Ajax control call with debouncing on input changes and an
         * event for the Enter key press that triggers the filter change and terminates further actions.
         *
         * @return void
         * @throws Caller
         */
        protected function addFilterActions(): void
        {
            $this->txtFilter->addAction(new Input(300), new AjaxControl($this, 'filterChanged'));
            $this->txtFilter->addActionArray(new EnterKey(),
                [
                    new AjaxControl($this, 'filterChanged'),
                    new Terminate()
                ]
            );
        }

        /**
         * Triggers an update when the filter settings are modified, causing the sports calendar data grid to refresh.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgSportsCalendars->refresh();
        }

        /**
         * Binds data to the sports calendars data grid using the specified condition.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgSportsCalendars->bindData($objCondition);
        }

        /**
         * Constructs a condition to filter news items based on optional group selection and text search.
         *
         * @return All|AndCondition|OrCondition A condition object that represents the filtering
         *         criteria for news items. If no filter is applied, it returns a condition that selects all items.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function getCondition(): All|AndCondition|OrCondition
        {
            $strText = trim($this->txtFilter->Text ?? '');
            $intYearId = (int)$this->lstYears->SelectedName;
            $intGroupId = $this->lstGroups->SelectedValue; // ID value
            $intSportsAreaId = $this->lstSportsAreas->SelectedValue; // ID value
            $intEventsChangesId = $this->lstChanges->SelectedValue; // ID value

            $condList = [];

            // If a year selected
            if (!empty($intYearId)) {
                $condList[] = QQ::equal(QQN::SportsCalendar()->Year, $intYearId); // or the correct field that you have binding
            }

            // If a group selected
            if (!empty($intGroupId)) {
                $condList[] = QQ::equal(QQN::SportsCalendar()->MenuContentGroupTitleId, $intGroupId); // or the correct field that you have binding
            }

            // If a sports area selected
            if (!empty($intSportsAreaId)) {
                $condList[] = QQ::equal(QQN::SportsCalendar()->SportsAreasId, $intSportsAreaId); // or the correct field that you have binding
            }

            // If a change selected
            if (!empty($intEventsChangesId)) {
                $condList[] = QQ::equal(QQN::SportsCalendar()->EventsChangesId, $intEventsChangesId); // or the correct field that you have binding
            }

            // If a text is entered
            if ($strText !== '') {
                // Do one big 'or' for multiple fields in the text
                $orText = QQ::orCondition(
                    QQ::like(QQN::SportsCalendar()->Year, "%" . $strText . "%"),
                    QQ::like(QQN::SportsCalendar()->MenuContentGroupTitle->Name, "%" . $strText . "%"),
                    QQ::like(QQN::SportsCalendar()->Title, "%" . $strText . "%"),
                    QQ::like(QQN::SportsCalendar()->SportsAreas->Name, "%" . $strText . "%"),
                    QQ::like(QQN::SportsCalendar()->Author, "%" . $strText . "%"),
                    QQ::equal(QQN::SportsCalendar()->Status,  "%" . $strText . "%")
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