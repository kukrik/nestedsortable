<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class EventsChangesManager extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    protected $dlgToastr1;
    protected $dlgToastr2;

    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;

    public $txtFilter;
    public $dtgEventsChanges;

    public $btnAddChange;
    public $btnGoToEvents;
    public $txtChange;
    public $lstStatus;
    public $btnSaveChange;
    public $btnSave;
    public $btnDelete;
    public $btnCancel;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $objChangeIds = [];
    protected $oldName;

    protected $strTemplate = 'EventsChangesManager.tpl.php';

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

        // $objUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
        // For example, John Doe is a logged user with his session

        $this->intLoggedUserId = 2;
        $this->objUser = User::load($this->intLoggedUserId);

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgEventsChanges_Create();
        $this->dtgEventsChanges->setDataBinder('BindData', $this);
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->CheckChanges();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the data table for displaying event changes.
     *
     * @return void
     */
    protected function dtgEventsChanges_Create()
    {
        $this->dtgEventsChanges = new EventsChangesTable($this);
        $this->dtgEventsChanges_CreateColumns();
        $this->createPaginators();
        $this->dtgEventsChanges_MakeEditable();
        $this->dtgEventsChanges->RowParamsCallback = [$this, "dtgEventsChanges_GetRowParams"];
        $this->dtgEventsChanges->SortColumnIndex = 0;
        $this->dtgEventsChanges->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Initializes and creates the columns for the dtgEventsChanges data grid.
     *
     * @return void
     */
    protected function dtgEventsChanges_CreateColumns()
    {
        $this->dtgEventsChanges->createColumns();
    }

    /**
     * Configures the DataGrid to allow rows to be editable by adding a click action.
     * Sets up a CellClick event to trigger an Ajax action when a row is clicked,
     * and modifies the CSS classes to enhance the DataGrid's appearance and interactivity.
     *
     * @return void
     */
    protected function dtgEventsChanges_MakeEditable()
    {
        $this->dtgEventsChanges->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgEventsChangesRow_Click'));
        $this->dtgEventsChanges->addCssClass('clickable-rows');
        $this->dtgEventsChanges->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event on a row in the events changes data grid.
     * Initializes the form fields based on the selected event change record
     * and modifies UI components to reflect the current state.
     *
     * @param ActionParams $params The parameters associated with the action, containing the action parameter which
     *                             identifies the specific event change record selected.
     * @return void
     */
    protected function dtgEventsChangesRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objChanges = EventsChanges::load($this->intId);

        $this->oldName = $objChanges->getTitle();

        $this->txtChange->Text = $objChanges->getTitle();
        $this->txtChange->focus();
        $this->lstStatus->SelectedValue = $objChanges->Status ?? null;

        $this->dtgEventsChanges->addCssClass('disabled');
        $this->btnAddChange->Enabled = false;
        $this->btnGoToEvents->Display = false;
        $this->txtChange->Display = true;
        $this->lstStatus->Display = true;
        $this->btnSave->Display = true;
        $this->btnDelete->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Retrieves the parameters for a row in the dtgEventsChanges data grid.
     *
     * @param object $objRowObject An object representing the current row in the data grid.
     * @param int $intRowIndex The index of the current row.
     * @return array An associative array of parameters for the row.
     */
    public function dtgEventsChanges_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Sets up pagination for the event changes data grid.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgEventsChanges->Paginator = new Bs\Paginator($this);
        $this->dtgEventsChanges->Paginator->LabelForPrevious = t('Previous');
        $this->dtgEventsChanges->Paginator->LabelForNext = t('Next');

        $this->dtgEventsChanges->ItemsPerPage = 10;
        $this->dtgEventsChanges->SortColumnIndex = 4;
        $this->dtgEventsChanges->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Creates and configures a Select2 element for managing items per page.
     *
     * This method initializes the Select2 element with specific properties such as theme, width, and
     * selection mode. It populates the Select2 with items and sets the currently selected value based
     * on the user's items per page setting. An AJAX action is added for handling changes to the selection.
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
     * Retrieves a list of items per page assigned to a specific user.
     *
     * This method queries the database for items per page that match a given condition
     * and creates a list of ListItems. Each ListItem represents an item retrieved from
     * the database with an associated selection state, indicating if it matches the
     * currently assigned user object.
     *
     * @return ListItem[] An array of ListItem objects, each representing an item per page
     * assigned to the user, with the relevant selected state.
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
     * Updates the items per page setting for a data grid based on the user's selection.
     *
     * @param ActionParams $params The parameters received from the action triggering this change.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgEventsChanges->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgEventsChanges->refresh();
    }

    /**
     * Initializes and configures the filter component for search functionality.
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
     * Adds filter actions for the txtFilter control. The method binds an AjaxControl action to the input event
     * triggered after a specified delay and sets up an array of actions for when the Enter key is pressed.
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
     * Triggers a refresh of the dtgEventsChanges data table grid.
     * This method is called when the filter criteria have changed, ensuring that the
     * displayed data is updated to reflect the current filter settings.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgEventsChanges->refresh();
    }

    /**
     * Binds data to the events changes data grid based on the specified condition.
     * This method retrieves the condition using the getCondition method and uses it to filter the data that is bound to the data grid.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgEventsChanges->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the value of the search input field.
     *
     * @return object Returns a Q\Query\QQ condition. If the search value is null or empty,
     *                a condition for selecting all records is returned. Otherwise, a condition
     *                is returned that matches either an exact ID or a partial title.
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
                Q\Query\QQ::equal(QQN::EventsChanges()->Id, $strSearchValue),
                Q\Query\QQ::like(QQN::EventsChanges()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Checks for changes in the events calendar and stores their change IDs.
     *
     * This method loads all events from the events calendar and iterates through them
     * to identify any events that have a change ID. If an event has a change ID, it is
     * added to the list of change IDs for further processing or reference.
     *
     * @return void This method does not return a value, as it updates the internal list
     * of change IDs.
     */
    private function CheckChanges()
    {
        $objEventArray = EventsCalendar::loadAll();

        foreach ($objEventArray as $objEvent) {
            if ($objEvent->getEventsChangesId()) {
                $this->objChangeIds[] = $objEvent->getEventsChangesId();
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes user interface buttons and input elements for managing changes.
     *
     * This method sets up multiple buttons including 'Add Change', 'Go To Events', 'Save Change',
     * 'Save', 'Delete', and 'Cancel', along with a text input and a radio list for status selection.
     * Each button is assigned appropriate text, style classes, events, and actions.
     * Elements like the 'Go To Events' button, text input for changes, status radio list, and other buttons
     * are conditionally displayed based on the session data or other logic.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnAddChange = new Bs\Button($this);
        $this->btnAddChange->Text = t(' Create a new change');
        $this->btnAddChange->Glyph = 'fa fa-plus';
        $this->btnAddChange->CssClass = 'btn btn-orange';
        $this->btnAddChange->addWrapperCssClass('center-button');
        $this->btnAddChange->CausesValidation = false;
        $this->btnAddChange->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddChange_Click'));
        $this->btnAddChange->setCssStyle('float', 'left');
        $this->btnAddChange->setCssStyle('margin-right', '10px');

        $this->btnGoToEvents = new Bs\Button($this);
        $this->btnGoToEvents->Text = t('Go to the calendar events');
        $this->btnGoToEvents->addWrapperCssClass('center-button');
        $this->btnGoToEvents->CssClass = 'btn btn-default';
        $this->btnGoToEvents->CausesValidation = false;
        $this->btnGoToEvents->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToEvents_Click'));
        $this->btnGoToEvents->setCssStyle('float', 'left');

        if (!empty($_SESSION['events_changes']) && !empty($_SESSION['events_group'])) {
            $this->btnGoToEvents->Display = true;
        } else {
            $this->btnGoToEvents->Display = false;
        }

        $this->txtChange = new Bs\TextBox($this);
        $this->txtChange->Placeholder = t('New change');
        $this->txtChange->ActionParameter = $this->txtChange->ControlId;
        $this->txtChange->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtChange->setHtmlAttribute('autocomplete', 'off');
        $this->txtChange->setCssStyle('float', 'left');
        $this->txtChange->setCssStyle('margin-right', '10px');
        $this->txtChange->Width = 300;
        $this->txtChange->Display = false;

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange form-horizontal radio-inline';
        $this->lstStatus->setCssStyle('float', 'left');
        $this->lstStatus->setCssStyle('margin-left', '15px');
        $this->lstStatus->setCssStyle('margin-right', '15px');
        $this->lstStatus->Display = false;

        $this->btnSaveChange = new Bs\Button($this);
        $this->btnSaveChange->Text = t('Save');
        $this->btnSaveChange->CssClass = 'btn btn-orange';
        $this->btnSaveChange->addWrapperCssClass('center-button');
        $this->btnSaveChange->PrimaryButton = true;
        $this->btnSaveChange->CausesValidation = true;
        $this->btnSaveChange->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSaveChange_Click'));
        $this->btnSaveChange->setCssStyle('float', 'left');
        $this->btnSaveChange->setCssStyle('margin-right', '10px');
        $this->btnSaveChange->Display = false;

        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->btnSave->setCssStyle('float', 'left');
        $this->btnSave->setCssStyle('margin-right', '10px');
        $this->btnSave->Display = false;

        $this->btnDelete = new Bs\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->addWrapperCssClass('center-button');
        $this->btnDelete->CausesValidation = true;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnDelete_Click'));
        $this->btnDelete->setCssStyle('float', 'left');
        $this->btnDelete->setCssStyle('margin-right', '10px');
        $this->btnDelete->Display = false;

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->btnCancel->setCssStyle('float', 'left');
        $this->btnCancel->Display = false;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures toastr notifications for the current instance.
     *
     * This method creates two toastr notifications with distinct alert types and messages.
     * Each toastr is configured for its position, alert type, and includes a progress bar.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The change has been saved or modified.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('The change name must exist!');
        $this->dlgToastr2->ProgressBar = true;
    }

    /**
     * Initializes and configures multiple modal dialog instances for user interaction.
     *
     * This method creates several modal dialogs, each with specific titles, message texts,
     * and button configurations. The dialogs are designed to handle various user actions
     * such as confirming deletions or acknowledging informational tips. Each modal is
     * customized with different header styles and button options for enhancing user
     * experience and interaction consistency within the application.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the event change?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The event change cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this event change, 
                                    simply release any event change previously associated with created events calendar.</p>');
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The event change cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this change, 
                                    simply release any changes previously associated with created events calendar.</p>');
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The title of this change already exists in the database, please choose another title!</p>');
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal5->Title = t("Warning");
        $this->dlgModal5->HeaderClasses = 'btn-danger';
        $this->dlgModal5->addCloseButton(t("I understand"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the "Add Change" button, configuring display and state of related UI elements.
     *
     * This method disables the "Go To Events" button and enables several other UI components such as text inputs
     * and dropdown lists to facilitate the addition of a new change. It resets values, sets focus, and updates
     * the state and appearance of elements like buttons and data tables.
     *
     * @return void
     */
    protected function btnAddChange_Click()
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->btnGoToEvents->Display = false;
        $this->txtChange->Display = true;
        $this->lstStatus->Display = true;
        $this->lstStatus->SelectedValue = 2;
        $this->btnSaveChange->Display = true;
        $this->btnCancel->Display = true;
        $this->txtChange->Text = null;
        $this->txtChange->focus();
        $this->btnAddChange->Enabled = false;
        $this->dtgEventsChanges->addCssClass('disabled');
    }

    /**
     * Handles the event triggered when the save changes button is clicked.
     *
     * This method processes the input text from the change text box, checks for
     * uniqueness, and saves a new change event if the title does not already exist.
     * It manages UI component states based on the success or failure of the operation,
     * updates the data grid, and provides user feedback through notifications or
     * dialog boxes.
     *
     * @param ActionParams $params The parameters associated with the button click event.
     * @return void
     */
    protected function btnSaveChange_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtChange->Text) {
            if (!EventsChanges::titleExists(trim($this->txtChange->Text))) {
                $objCategoryNews = new EventsChanges();
                $objCategoryNews->setTitle(trim($this->txtChange->Text));
                $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                $objCategoryNews->setPostDate(Q\QDateTime::Now());
                $objCategoryNews->save();

                $this->dtgEventsChanges->refresh();

                if (!empty($_SESSION['events_changes']) || !empty($_SESSION['events_group'])) {
                    $this->btnGoToEvents->Display = true;
                }

                $this->txtChange->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSaveChange->Display = false;
                $this->btnCancel->Display = false;
                $this->btnAddChange->Enabled = true;
                $this->dtgEventsChanges->removeCssClass('disabled');
                $this->txtChange->Text = null;
                $this->dlgToastr1->notify();
            } else {
                $this->txtChange->Text = null;
                $this->txtChange->focus();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtChange->Text = null;
            $this->txtChange->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the click event of the save button.
     *
     * This method manages the logic for saving changes to an event based on user inputs
     * and the current state of the event change object. It conditions actions such as
     * updating the status, displaying modal dialogs, and refreshing the event data
     * grid. The method also alters UI components' visibility and enabled state
     * accordingly.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void This method does not return a value; it performs UI updates and data changes.
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objChanges = EventsChanges::loadById($this->intId);

        if ($this->txtChange->Text) {
            if (in_array($this->intId, $this->objChangeIds) && $this->lstStatus->SelectedValue == 2) {
                $this->lstStatus->SelectedValue = 1;
                $this->dlgModal2->showDialogBox();

                if (!empty($_SESSION['events_changes']) || !empty($_SESSION['events_group'])) {
                    $this->btnGoToEvents->Display = true;
                }

                $this->btnAddChange->Enabled = true;
                $this->txtChange->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;
                $this->dtgEventsChanges->removeCssClass('disabled');

            } else if ($this->txtChange->Text == $objChanges->getTitle() && $this->lstStatus->SelectedValue !== $objChanges->getStatus()) {
                $objChanges->setTitle(trim($this->txtChange->Text));
                $objChanges->setStatus($this->lstStatus->SelectedValue);
                $objChanges->setPostUpdateDate(Q\QDateTime::Now());
                $objChanges->save();

                $this->dtgEventsChanges->refresh();
                $this->btnAddChange->Enabled = true;

                if (!empty($_SESSION['events_changes']) || !empty($_SESSION['events_group'])) {
                    $this->btnGoToEvents->Display = true;
                }

                $this->txtChange->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;

                $this->dtgEventsChanges->removeCssClass('disabled');
                $this->txtChange->Text = $objChanges->getTitle();
                $this->dlgToastr1->notify();


            } else if (!EventsChanges::titleExists(trim($this->txtChange->Text))) {
                $this->txtChange->Text = $objChanges->getTitle();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtChange->Text = $objChanges->getTitle();
            $this->txtChange->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the click event for the delete button and performs conditional UI updates.
     *
     * This method checks if the current ID is in the list of change IDs. If it is, the method
     * triggers a modal dialog box and updates several UI elements based on session data.
     * Specifically, it enables or disables buttons and hides or shows certain elements. If
     * the current ID is not found in the list, a different modal dialog box is shown.
     *
     * @param ActionParams $params Parameters associated with the action event, typically providing
     *                             context for the click event, such as the source of the action.
     * @return void This method does not return a value.
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (in_array($this->intId, $this->objChangeIds)) {
            $this->dlgModal3->showDialogBox();

            if (!empty($_SESSION['events_changes']) || !empty($_SESSION['events_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            $this->btnAddChange->Enabled = true;
            $this->txtChange->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgEventsChanges->removeCssClass('disabled');

        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the click event for deleting an item.
     *
     * This method performs the deletion of an item based on a given action parameter.
     * If the action parameter is set to "pass", the specified event change is deleted.
     * Subsequently, various UI components are updated to reflect the deletion, including
     * refreshing the data grid and updating the display and enabled states of form controls.
     *
     * @param ActionParams $params The parameters containing the action identifier which
     * dictates whether the deletion should occur.
     *
     * @return void This method does not return a value. It performs UI updates and
     * potentially deletes a database record based on the action parameter.
     */
    public function deleteItem_Click(ActionParams $params)
    {
        $objChanges = EventsChanges::loadById($this->intId);

        if ($params->ActionParameter == "pass") {
            $objChanges->delete();
        }

        $this->dtgEventsChanges->refresh();
        $this->btnAddChange->Enabled = true;
        $this->txtChange->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgEventsChanges->removeCssClass('disabled');
        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the click event for the cancel button.
     *
     * This method is triggered when the cancel button is clicked. It adjusts the
     * visibility and state of various UI components based on session data.
     * Specifically, it checks the session for changes to events or groups and sets
     * the visibility of the events button accordingly. It also toggles the display
     * and enabled state of several user interface elements to reflect the cancellation
     * of an action, reverting any changes made in the current session.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void This method does not return a value.
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['events_changes']) || !empty($_SESSION['events_group'])) {
            $this->btnGoToEvents->Display = true;
        }

        $this->txtChange->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSaveChange->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddChange->Enabled = true;
        $this->dtgEventsChanges->removeCssClass('disabled');
        $this->txtChange->Text = null;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the "Go To Events" button, redirecting the user to the event calendar edit page.
     *
     * This method checks session variables for event changes and group data. If any such data is found,
     * it redirects the user to a specific URL for editing events and subsequently clears the session variables.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     * @return void This method does not return any value.
     */
    protected function btnGoToEvents_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['events_changes']) || !empty($_SESSION['events_group'])) {
            Application::redirect('event_calendar_edit.php?id=' . $_SESSION['events_changes'] . '&group=' . $_SESSION['events_group']);
            unset($_SESSION['events_changes']);
            unset($_SESSION['events_group']);
        }
    }
}