<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;
use QCubed\QString;

class EventsCalendarListPanel extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    protected $dlgToast1;
    protected $dlgToast2;
    protected $dlgToast3;
    protected $dlgToast4;
    protected $dlgToast5;
    protected $dlgToast6;

    public $dlgModal1;
    public $dlgModal2;

    public $txtFilter;
    public $dtgEventsCalendars;

    public $btnAddEvent;
    public $btnMove;
    public $lstEventsLocked;
    public $lstTargetGroup;
    public $txtYear;
    public $lstGroupTitle;
    public $txtTitle;
    public $btnSave;
    public $btnCancel;
    public $btnLockedCancel;
    public $btnBack;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $objMenuContent;
    protected $objEventsCalendar;

    protected $objTargetGroupCondition;
    protected $objTargetGroupClauses;

    protected $strTemplate = 'EventsCalendarListPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        // $objUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
        // For example, John Doe is a logged user with his session

        $this->intLoggedUserId = 1;
        $this->objUser = User::load($this->intLoggedUserId);

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
     * Initializes and configures input controls for year, title, and target group selection.
     *
     * @return void
     */
    protected function createInputs()
    {
        $this->txtYear = new Q\Plugin\YearPicker($this);
        $this->txtYear->Language = 'et';
        $this->txtYear->TodayBtn = true;
        $this->txtYear->ClearBtn = true;
        $this->txtYear->AutoClose = true;
        $this->txtYear->ActionParameter = $this->txtYear->ControlId;
        $this->txtYear->setHtmlAttribute('autocomplete', 'off');
        $this->txtYear->setCssStyle('margin-left', '10px');
        $this->txtYear->Width = '100%';
        $this->txtYear->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'setYear'));

        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Title of the new event');
        $this->txtTitle->addWrapperCssClass('center-button');
        $this->txtTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnSave_Click'));
        $this->txtTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnCancel_Click'));
        $this->txtTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lstTargetGroup = new Q\Plugin\Select2($this);
        $this->lstTargetGroup->MinimumResultsForSearch = -1;
        $this->lstTargetGroup->Theme = 'web-vauu';
        $this->lstTargetGroup->Width = '100%';
        $this->lstTargetGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);

        $objTargetGroups = EventsSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::EventsSettings()->Id)));
        foreach ($objTargetGroups as $objTitle) {
            if ($objTitle->IsReserved !== 2) {
                $this->lstTargetGroup->addItem($objTitle->Name, $objTitle->Id);
            }
        }

        $this->lstTargetGroup->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstTargetGroup_Change'));
        $this->lstTargetGroup->Enabled = false;
    }

    /**
     * Initializes and configures a set of button controls for event management,
     * including actions for adding, moving, saving, and cancelling events, as well as returning to a previous state.
     * This method also dynamically adjusts the visibility of certain buttons based on the event settings.
     *
     * @return void
     */
    protected function createButtons()
    {
        $this->btnAddEvent = new Bs\Button($this);
        $this->btnAddEvent->Text = t(' Add event');
        $this->btnAddEvent->Glyph = 'fa fa-plus';
        $this->btnAddEvent->CssClass = 'btn btn-orange';
        $this->btnAddEvent->addWrapperCssClass('center-button');
        $this->btnAddEvent->CausesValidation = false;
        $this->btnAddEvent->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddEvent_Click'));

        $this->btnMove = new Bs\Button($this);
        $this->btnMove->Text = t(' Move');
        $this->btnMove->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnMove->CssClass = 'btn btn-darkblue move-button-js';
        $this->btnMove->addWrapperCssClass('center-button');
        $this->btnMove->CausesValidation = false;
        $this->btnMove->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnMove_Click'));

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
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->setCssStyle('margin-left', '10px');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));

        $this->btnLockedCancel = new Bs\Button($this);
        $this->btnLockedCancel->Text = t('Cancel');
        $this->btnLockedCancel->addWrapperCssClass('center-button');
        $this->btnLockedCancel->CssClass = 'btn btn-default';
        $this->btnLockedCancel->CausesValidation = false;
        $this->btnLockedCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnLockedCancel_Click'));

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->addWrapperCssClass('center-button');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnBack_Click'));
    }

    /**
     * Initializes and configures multiple Toastr notifications with different settings and messages.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToast1 = new Q\Plugin\Toastr($this);
        $this->dlgToast1->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast1->Message = t('The year is at least mandatory!');
        $this->dlgToast1->ProgressBar = true;

        $this->dlgToast2 = new Q\Plugin\Toastr($this);
        $this->dlgToast2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast2->Message = t('The event title is at least mandatory!');
        $this->dlgToast2->ProgressBar = true;

        $this->dlgToast3 = new Q\Plugin\Toastr($this);
        $this->dlgToast3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast3->Message = t('<p style=\"margin-bottom: 5px;\">The event group must be selected beforehand!</p>');
        $this->dlgToast3->ProgressBar = true;
        $this->dlgToast3->TimeOut = 10000;
        $this->dlgToast3->EscapeHtml = false;

        $this->dlgToast4 = new Q\Plugin\Toastr($this);
        $this->dlgToast4->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast4->Message = t('<p style=\"margin-bottom: 5px;\">The event group cannot be the same as the target group!</p>');
        $this->dlgToast4->ProgressBar = true;
        $this->dlgToast4->TimeOut = 10000;
        $this->dlgToast4->EscapeHtml = false;

        $this->dlgToast5 = new Q\Plugin\Toastr($this);
        $this->dlgToast5->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast5->Message = t('<strong>Well done!</strong> The transfer of events to the new group was successful.');
        $this->dlgToast5->ProgressBar = true;

        $this->dlgToast6 = new Q\Plugin\Toastr($this);
        $this->dlgToast6->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast6->Message = t('The transfer of events to the new group failed.');
        $this->dlgToast6->ProgressBar = true;
    }

    /**
     * Initializes and creates modal dialogs with specific settings and actions.
     *
     * @return void
     */
    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move the events from this event group to another event group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Note! If there are several event items in the selected group, they will be transferred to the new group!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'moveItems_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'transferCancelling_Click'));

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
     */
    public function elementsReset()
    {
        Application::executeJavaScript("
            $('.new-item-js').addClass('hidden');
            $('.move-items-js').addClass('hidden');
        ");
    }

    /**
     * Sets the year based on the text of the specified control.
     *
     * @param ActionParams $params The action parameters containing the ID of the control whose text will be used to set the year.
     * @return void
     */
    protected function setYear(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objControlToLookup = $this->Form->getControl($params->ActionParameter);
        $this->txtYear->Text = $objControlToLookup->Text;
    }

    /**
     * Handles the click event for the button to add a new event.
     * It updates UI elements to reflect the state of adding a new event, handles JavaScript executions,
     * and configures the event group selection based on the reserved state of event settings.
     *
     * @param ActionParams $params The parameters triggering the action, including any relevant data for the event handling.
     * @return void
     */
    protected function btnAddEvent_Click(ActionParams $params)
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

        $countByIsReserved = EventsSettings::countByIsReserved(1);

        $this->txtTitle->Text = null;
        $this->txtYear->show();
        $this->dtgEventsCalendars->addCssClass('disabled');
        $this->btnAddEvent->Enabled = false;

        $this->lstGroupTitle = new Q\Plugin\Select2($this);
        $this->lstGroupTitle->MinimumResultsForSearch = -1;
        $this->lstGroupTitle->Theme = 'web-vauu';
        $this->lstGroupTitle->Width = '100%';
        $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
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

        $this->lstGroupTitle->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstGroupTitle_Change'));
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
     * @return void
     */
    protected function btnMove_Click(ActionParams $params)
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

        $this->lstEventsLocked = new Q\Plugin\Select2($this);
        $this->lstEventsLocked->MinimumResultsForSearch = -1;
        $this->lstEventsLocked->Theme = 'web-vauu';
        $this->lstEventsLocked->Width = '100%';
        $this->lstEventsLocked->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
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

        $this->lstEventsLocked->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstEventsLocked_Change'));

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
     * @return void
     */
    protected function lstGroupTitle_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!$this->txtYear) {
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
     * @return void
     */
    protected function lstEventsLocked_Change(ActionParams $params)
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
     * @return void
     */
    protected function lstTargetGroup_Change(ActionParams $params)
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
     * @return void
     */
    public function transferCancelling_Click(ActionParams $params)
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

        $this->dtgEventsCalendars->removeCssClass('disabled');
    }

    /**
     * Handles the click event for moving items. This method hides the dialog box,
     * performs event transfer operations, and resets UI elements. After execution,
     * it enables the "Add Event" and "Move" buttons.
     *
     * @param ActionParams $params An object containing parameters for the action event triggered.
     * @return void
     */
    protected function moveItems_Click(ActionParams $params)
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
     * Transfers event operations by updating their associated groups and settings.
     * The method moves events and frontend links from a locked group to a target group,
     * updates their relevant fields, and saves the changes.
     * It also refreshes the events calendar display and executes JavaScript to update UI elements.
     * Notifies the user with a toast message based on the success of the transfer operation.
     *
     * @return void
     */
    private function eventsTransferOperations()
    {
        $objLockedGroup = EventsSettings::loadById($this->lstEventsLocked->SelectedValue);
        $objTargetGroup = EventsSettings::loadById($this->lstTargetGroup->SelectedValue);
        $objFrontendLinks = FrontendLinks::loadArrayByGroupedId($objLockedGroup->getMenuContentId());
        $objEventsGroupArray = EventsCalendar::loadArrayByMenuContentGroupId($objLockedGroup->getMenuContentId());

        $beforeCount = count(FrontendLinks::loadArrayByGroupedId($objLockedGroup->getMenuContentId()));
        $afterCount = 0;

        $objEventsSettings = EventsSettings::loadById($objLockedGroup->getId());
        $objEventsSettings->setEventsLocked(0);
        $objEventsSettings->save();

        $objEventsSettings = EventsSettings::loadById($objTargetGroup->getId());
        $objEventsSettings->setEventsLocked(1);
        $objEventsSettings->save();

        foreach ($objEventsGroupArray as $objEventsGroup) {
            $objEvents = EventsCalendar::loadById($objEventsGroup->getId());
            $objEvents->setMenuContentGroupId($objTargetGroup->getMenuContentId());
            $objEvents->setMenuContentGroupTitleId($this->lstTargetGroup->SelectedValue);
            $objEvents->setEventsGroupName($this->lstTargetGroup->SelectedName);

            $objEvents->updateEvent($objEvents->getYear(), $objEvents->getTitle(), $this->lstTargetGroup->SelectedName, $objTargetGroup->getTitleSlug());
            $objEvents->save();
        }

        foreach ($objFrontendLinks as $objFrontendLink) {
            $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
            $objFrontendLinks->setGroupedId($objTargetGroup->getMenuContentId());
            $objFrontendLinks->setFrontendTitleSlug($objEvents->getTitleSlug());
            $objFrontendLinks->save();
            $afterCount++;
        }

        $this->dtgEventsCalendars->refresh(true);

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
    }

    /**
     * Handles the click event for saving event details. Validates input fields and
     * saves event and frontend link information if all required fields are filled in.
     * It locks the event if not already locked and resets input fields and UI elements
     * after a successful save operation.
     *
     * @param ActionParams $params An object containing parameters for the action event triggered.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objEventGroup = EventsSettings::selectedByIdFromEventsSettings($this->lstGroupTitle->SelectedValue);
        $objTemplateLocking = FrontendTemplateLocking::load(8);
        $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

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

            $objEventsCalendar = new EventsCalendar();
            $objEventsCalendar->setPostDate(Q\QDateTime::Now());
            $objEventsCalendar->setYear($this->txtYear->Text);
            $objEventsCalendar->setTitle($this->txtTitle->Text);
            $objEventsCalendar->setMenuContentGroupId($objEventGroup->getMenuContentId());
            $objEventsCalendar->setMenuContentGroupTitleId($this->lstGroupTitle->SelectedValue);
            $objEventsCalendar->setEventsGroupName($this->lstGroupTitle->SelectedName);

            $objEventsCalendar->saveEvent($this->txtYear->Text, $this->txtTitle->Text, $this->lstGroupTitle->SelectedName, $objEventGroup->getTitleSlug());
            $objEventsCalendar->setStatus(2);
            $objEventsCalendar->setAssignedByUser($this->objUser->Id);
            $objEventsCalendar->setAuthor($objEventsCalendar->getAssignedByUserObject());
            $objEventsCalendar->save();

            $objFrontendLinks = new FrontendLinks();
            $objFrontendLinks->setLinkedId($objEventsCalendar->getId());
            $objFrontendLinks->setGroupedId($objEventGroup->getMenuContentId());
            $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
            $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $objFrontendLinks->setTitle(trim($this->txtTitle->Text));
            $objFrontendLinks->setContentTypesManagamentId(8);
            $objFrontendLinks->setFrontendTitleSlug($objEventsCalendar->getTitleSlug());
            $objFrontendLinks->setIsActivated(1);
            $objFrontendLinks->save();

            if ($objEventGroup->getEventsLocked() == 0) {
                $objGroup = EventsSettings::loadById($objEventGroup->getId());
                $objGroup->setEventsLocked(1);
                $objGroup->save();
            }

            $this->btnAddEvent->Enabled = true;
            $this->btnMove->Enabled = true;
            $this->txtYear->Text = null;
            $this->lstGroupTitle->SelectedValue = null;
            $this->txtTitle->Text = null;
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
     * @return void
     */
    protected function btnCancel_Click(ActionParams $params)
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
        $this->txtTitle->Text = null;
        $this->lstGroupTitle->refresh();
        $this->dtgEventsCalendars->removeCssClass('disabled');
    }

    /**
     * Handles the click event for the Locked Cancel button. This method manages the visibility
     * of UI elements based on the number of event settings available and updates button states.
     * It also resets and refreshes event and group selections.
     *
     * @param ActionParams $params An object containing parameters for the action event triggered.
     * @return void
     */
    protected function btnLockedCancel_Click(ActionParams $params)
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

        $this->dtgEventsCalendars->removeCssClass('disabled');
    }

    /**
     * Handles the click event for the back button. This method redirects
     * the user to the menu manager page.
     *
     * @param ActionParams $params An object containing parameters for the action event triggered.
     * @return void
     */
    public function btnBack_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('menu_manager.php');;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the EventsCalendarTable instance. This method sets
     * up the table columns, pagination, and makes the table editable. It also assigns
     * the row parameters callback, specifies the column to be sorted, sets the sort
     * direction, and enables Ajax. Additionally, it determines the number of items
     * per page based on user preferences.
     *
     * @return void
     */
    protected function dtgEventsCalendars_Create()
    {
        $this->dtgEventsCalendars = new EventsCalendarTable($this);
        $this->dtgEventsCalendars_CreateColumns();
        $this->createPaginators();
        $this->dtgEventsCalendars_MakeEditable();
        $this->dtgEventsCalendars->RowParamsCallback = [$this, "dtgEventsCalendars_GetRowParams"];
        $this->dtgEventsCalendars->SortColumnIndex = 5;
        $this->dtgEventsCalendars->SortDirection = -1;
        $this->dtgEventsCalendars->UseAjax = true;
        $this->dtgEventsCalendars->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Creates columns for the events calendars data grid. This method utilizes
     * the createColumns function to dynamically generate and configure columns
     * for the data grid displaying event calendars.
     *
     * @return void
     */
    protected function dtgEventsCalendars_CreateColumns()
    {
        $this->dtgEventsCalendars->createColumns();
    }

    /**
     * Configures the datagrid to be editable by adding event actions and CSS classes.
     * This method sets up a cell click event on the datagrid, making each row clickable,
     * and applies a set of CSS classes to style the datagrid for better user interaction.
     *
     * @return void
     */
    protected function dtgEventsCalendars_MakeEditable()
    {
        $this->dtgEventsCalendars->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgEventsCalendarsRow_Click'));
        $this->dtgEventsCalendars->addCssClass('clickable-rows');
        $this->dtgEventsCalendars->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for a row in the event calendars data grid. This method
     * retrieves the event calendar by its ID, determines the associated menu content group ID,
     * and redirects the user to the event calendar edit page with the relevant parameters.
     *
     * @param ActionParams $params An object containing parameters for the action event triggered, including the event calendar ID.
     * @return void
     */
    protected function dtgEventsCalendarsRow_Click(ActionParams $params)
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
     * @param mixed $objRowObject An object representing a row in the data grid.
     * @param int $intRowIndex The index of the row within the data grid.
     * @return array An associative array containing row parameter data with the primary key as 'data-value'.
     */
    public function dtgEventsCalendars_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures the paginators for the events calendars data grid.
     * This method sets up two paginator instances with labels for navigation and
     * configures items per page, sort index, and sort direction. It also enables
     * AJAX for the data grid and adds filter actions.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgEventsCalendars->Paginator = new Bs\Paginator($this);
        $this->dtgEventsCalendars->Paginator->LabelForPrevious = t('Previous');
        $this->dtgEventsCalendars->Paginator->LabelForNext = t('Next');

        $this->dtgEventsCalendars->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgEventsCalendars->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgEventsCalendars->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgEventsCalendars->ItemsPerPage = 10;
        $this->dtgEventsCalendars->SortColumnIndex = 2;
        $this->dtgEventsCalendars->SortDirection = -1;
        $this->dtgEventsCalendars->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Initializes the items per page selector for the assigned user. This method creates
     * a new Select2 instance and configures its properties such as theme, width, and selection mode.
     * It sets the default selected value and populates the list with available items. Additionally,
     * an AjaxControl change event is added to handle user interaction with the selector.
     *
     * @return void
     */
    protected function createItemsPerPage()
    {
        $this->lstItemsPerPageByAssignedUserObject = new Q\Plugin\Select2($this);
        $this->lstItemsPerPageByAssignedUserObject->MinimumResultsForSearch = -1;
        $this->lstItemsPerPageByAssignedUserObject->Theme = 'web-vauu';
        $this->lstItemsPerPageByAssignedUserObject->Width = '100%';
        $this->lstItemsPerPageByAssignedUserObject->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->ItemsPerPageByAssignedUser;
        $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstItemsPerPageByAssignedUserObject_GetItems());
        $this->lstItemsPerPageByAssignedUserObject->AddAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
    }

    /**
     * Retrieves items for pagination by the assigned user object. It constructs a list
     * of items with selection status based on the assigned user's current pagination setting.
     *
     * @return ListItem[] An array of ListItem objects, each representing a pagination option
     *         for the assigned user, with the relevant option marked as selected.
     */
    public function lstItemsPerPageByAssignedUserObject_GetItems() {
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
     * Updates the number of items displayed per page in the events calendar based on the selected value
     * from the items per page list, and refreshes the calendar display.
     *
     * @param ActionParams $params An object containing parameters for the action event triggered.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgEventsCalendars->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgEventsCalendars->refresh();
    }

    /**
     * Initializes and configures a text box to be used as a search filter. The method
     * sets placeholder text, defines the text mode for search, disables autocomplete,
     * and applies a CSS class for styling. Additionally, it triggers the addition of
     * filter-specific actions.
     *
     * @return void
     */
    protected function createFilter() {
        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();
    }

    /**
     * Registers actions for the filter input control. This method binds certain events to
     * AJAX actions, enabling dynamic interaction with the filter component.
     * Specifically, it sets up an input event with a delay and an enter key event to trigger
     * specified callback methods.
     *
     * @return void
     */
    protected function addFilterActions()
    {
        $this->txtFilter->addAction(new Q\Event\Input(300), new Q\Action\AjaxControl($this, 'filterChanged'));
        $this->txtFilter->addActionArray(new Q\Event\EnterKey(),
            [
                new Q\Action\AjaxControl($this, 'FilterChanged'),
                new Q\Action\Terminate()
            ]
        );
    }

    /**
     * Invoked when the filter criteria are changed. This method refreshes the events
     * calendar data grid to reflect the updated filter conditions.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgEventsCalendars->refresh();
    }

    /**
     * Binds data to the events calendar. This method retrieves a condition
     * object and uses it to fetch and display relevant data in the data grid
     * for events calendars.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgEventsCalendars->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the user's input from a filter text field.
     * If the input value is null or an empty string, it returns a condition that matches all records.
     * Otherwise, it creates a condition that checks for the presence of the input value
     * within specific fields of the EventsCalendar.
     *
     * @return QQCondition The constructed query condition object that is either a match-all condition
     *                     or a set of conditions checking for the input value across specified fields.
     */
    protected function getCondition()
    {
        $strSearchValue = $this->txtFilter->Text;

        if ($strSearchValue === null) {
            $strSearchValue = '';
        }

        $strSearchValue = trim($strSearchValue);

        if (is_null($strSearchValue) || $strSearchValue === '') {
            return Q\Query\QQ::all();
        } else {
            return Q\Query\QQ::orCondition(
                Q\Query\QQ::like(QQN::EventsCalendar()->Year, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::EventsCalendar()->EventsGroupName, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::EventsCalendar()->Title, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::EventsCalendar()->Author, "%" . $strSearchValue . "%"),
                Q\Query\QQ::equal(QQN::EventsCalendar()->Status,  "%" . $strSearchValue . "%")
            );
        }
    }
}