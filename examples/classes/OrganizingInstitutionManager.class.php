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

class OrganizingInstitutionManager extends Q\Control\Panel
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
    public $dtgInstitution;

    public $btnAddInstitution;
    public $btnGoToEvents;
    public $txtName;
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

    protected $strTemplate = 'OrganizingInstitutionManager.tpl.php';

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

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgInstitution_Create();
        $this->dtgInstitution->setDataBinder('BindData', $this);
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->CheckChanges();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the data grid for organizing institutions.
     *
     * @return void
     */
    protected function dtgInstitution_Create()
    {
        $this->dtgInstitution = new OrganizingInstitutionTable($this);
        $this->dtgInstitution_CreateColumns();
        $this->createPaginators();
        $this->dtgInstitution_MakeEditable();
        $this->dtgInstitution->RowParamsCallback = [$this, "dtgInstitution_GetRowParams"];
        $this->dtgInstitution->SortColumnIndex = 0;
        $this->dtgInstitution->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Initializes and creates the necessary columns for the data grid component associated with the institution.
     *
     * @return void
     */
    protected function dtgInstitution_CreateColumns()
    {
        $this->dtgInstitution->createColumns();
    }

    /**
     * Configures the institution DataGrid to be editable by adding necessary actions and CSS classes.
     *
     * @return void
     */
    protected function dtgInstitution_MakeEditable()
    {
        $this->dtgInstitution->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgInstitutionRow_Click'));
        $this->dtgInstitution->addCssClass('clickable-rows');
        $this->dtgInstitution->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event on the institution row in the data grid. It retrieves the institution's details
     * based on the selected row and updates the interface to allow for editing.
     *
     * @param ActionParams $params The parameters from the action, including the ActionParameter used to identify the institution.
     * @return void
     */
    protected function dtgInstitutionRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objChanges = OrganizingInstitution::load($this->intId);

        $this->oldName = $objChanges->getName();

        $this->txtName->Text = $objChanges->getName();
        $this->txtName->focus();
        $this->lstStatus->SelectedValue = $objChanges->Status ?? null;

        $this->dtgInstitution->addCssClass('disabled');
        $this->btnAddInstitution->Enabled = false;
        $this->btnGoToEvents->Display = false;
        $this->txtName->Display = true;
        $this->lstStatus->Display = true;
        $this->btnSave->Display = true;
        $this->btnDelete->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Constructs an array of parameters for a row in the institution data grid.
     *
     * @param object $objRowObject The row object representing a single row in the data grid.
     * @param int $intRowIndex The index of the row within the data grid.
     *
     * @return array An associative array containing parameters for the row, including 'data-value' which is the primary key of the row object.
     */
    public function dtgInstitution_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures the pagination mechanism for the data grid.
     * Sets the paginator labels, items per page, and sort column index.
     * Enables AJAX functionality for the data grid to improve user experience.
     * Also triggers the addition of filter actions.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgInstitution->Paginator = new Bs\Paginator($this);
        $this->dtgInstitution->Paginator->LabelForPrevious = t('Previous');
        $this->dtgInstitution->Paginator->LabelForNext = t('Next');

        $this->dtgInstitution->ItemsPerPage = 10;
        $this->dtgInstitution->SortColumnIndex = 4;
        $this->dtgInstitution->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Initializes the items per page selection control with specific configurations and adds available items to it.
     * The control is setup with a specific theme, width, and selection mode, and listens for change events to trigger a specified action.
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
     * Retrieves a list of ListItem objects representing the items per page by assigned user.
     * It instantiates each item in the cursor and checks if it matches the user's current
     * ItemsPerPageByAssignedUserObject, marking it as selected if it does.
     *
     * @return ListItem[] An array of ListItem objects, with one item potentially marked as selected.
     */
    public function lstItemsPerPageByAssignedUserObject_GetItems()
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
     * Updates the items per page setting for the institution data grid
     * based on the selected value from the items per page list.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgInstitution->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgInstitution->refresh();
    }

    /**
     * Initializes and configures a text filter input control for search functionality.
     *
     * @return void
     */
    protected function createFilter()
    {
        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();
    }

    /**
     * Adds filter actions to the text input element to handle user input events.
     * These actions listen for input events with a delay and key press events.
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
     * Refreshes the data grid displaying institution information.
     * This method is typically called when a filter change event is detected,
     * ensuring that the displayed data reflects the updated filter criteria.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgInstitution->refresh();
    }

    /**
     * Binds data to the data grid using a specified condition.
     *
     * This method retrieves the condition to be applied and binds
     * data to the dtgInstitution data grid object based on that condition.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgInstitution->bindData($objCondition);
    }

    /**
     * Constructs and returns a condition object based on the filtered search value.
     *
     * @return mixed A QQ condition object that is either a query match for all records, or a condition
     *               constructed to match the provided search value against the Id or Title fields of
     *               OrganizingInstitution.
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
                Q\Query\QQ::equal(QQN::OrganizingInstitution()->Id, $strSearchValue),
                Q\Query\QQ::like(QQN::OrganizingInstitution()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Checks for any changes in events and stores the change IDs.
     *
     * Retrieves all events from the SportsCalendar. For each event, it checks if there is an associated change ID.
     * If a change ID is found, it appends the ID to the internal collection of change IDs for further processing or tracking.
     *
     * @return void
     */
    private function CheckChanges()
    {
        $objEventArray = SportsCalendar::loadAll();

        foreach ($objEventArray as $objEvent) {
            if ($objEvent->getEventsChangesId()) {
                $this->objChangeIds[] = $objEvent->getEventsChangesId();
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes various buttons and controls for managing institutions,
     * including buttons for adding, saving, and deleting entries, as well as a text box
     * for entering new institution names and a radio list for status selection.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnAddInstitution = new Bs\Button($this);
        $this->btnAddInstitution->Text = t(' Create a new name');
        $this->btnAddInstitution->Glyph = 'fa fa-plus';
        $this->btnAddInstitution->CssClass = 'btn btn-orange';
        $this->btnAddInstitution->addWrapperCssClass('center-button');
        $this->btnAddInstitution->CausesValidation = false;
        $this->btnAddInstitution->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddInstitution_Click'));
        $this->btnAddInstitution->setCssStyle('float', 'left');
        $this->btnAddInstitution->setCssStyle('margin-right', '10px');

        $this->btnGoToEvents = new Bs\Button($this);
        $this->btnGoToEvents->Text = t('Go to the sports calendar events');
        $this->btnGoToEvents->addWrapperCssClass('center-button');
        $this->btnGoToEvents->CssClass = 'btn btn-default';
        $this->btnGoToEvents->CausesValidation = false;
        $this->btnGoToEvents->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToEvents_Click'));
        $this->btnGoToEvents->setCssStyle('float', 'left');

        if (!empty($_SESSION['dtgInstitution_changes']) && !empty($_SESSION['dtgInstitution_group'])) {
            $this->btnGoToEvents->Display = true;
        } else {
            $this->btnGoToEvents->Display = false;
        }

        $this->txtName = new Bs\TextBox($this);
        $this->txtName->Placeholder = t('Organizing institution new name');
        $this->txtName->ActionParameter = $this->txtName->ControlId;
        $this->txtName->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtName->setHtmlAttribute('autocomplete', 'off');
        $this->txtName->setCssStyle('float', 'left');
        $this->txtName->setCssStyle('margin-right', '10px');
        $this->txtName->Width = 300;
        $this->txtName->Display = false;

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
     * Creates two Toastr notification instances with predefined settings.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The institution name has been saved or modified.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('The institution name must exist!');
        $this->dlgToastr2->ProgressBar = true;
    }

    /**
     * Creates and configures modal dialogs for warning and informational messages.
     *
     * The method initializes four different modal dialogs with specified titles,
     * text content, button configurations, and header classes. Each modal is set up
     * with actions and styles suitable for particular scenarios such as warnings or tips.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this institution?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This institution cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this institution, simply release all 
                                    previously created institutions related to sporting events.</p>');
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This institution cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this institutions, simply release all 
                                    previously created changes related to sports events.</p>');
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The name of this institution already 
                                 exists in the database, please choose another name!</p>');
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
     * Handles the click event for the 'Add Institution' button. This method sets up the UI for adding a new institution
     * by adjusting the visibility and state of several controls and components. The institutions' grid is disabled
     * to prevent interaction while new institution data is being entered.
     *
     * @return void
     */
    protected function btnAddInstitution_Click()
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->btnGoToEvents->Display = false;
        $this->txtName->Display = true;
        $this->lstStatus->Display = true;
        $this->lstStatus->SelectedValue = 2;
        $this->btnSaveChange->Display = true;
        $this->btnCancel->Display = true;
        $this->txtName->Text = null;
        $this->txtName->focus();
        $this->btnAddInstitution->Enabled = false;
        $this->dtgInstitution->addCssClass('disabled');
    }

    /**
     * Handles the click event for the save changes button.
     *
     * This method processes the input data from the user interface when the save button is clicked,
     * checks the validity of the entered information, and appropriately updates or alerts the user.
     * It saves a new organizing institution if the entered name is valid and not already existing.
     * It also updates the visual elements and notifies the user upon successful completion or error.
     *
     * @param ActionParams $params The parameters associated with the click action event, including any relevant user interface elements.
     * @return void This method does not return a value.
     */
    protected function btnSaveChange_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtName->Text) {
            if (!OrganizingInstitution::titleExists(trim($this->txtName->Text))) {
                $objCategoryNews = new OrganizingInstitution();
                $objCategoryNews->setName(trim($this->txtName->Text));
                $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                $objCategoryNews->setPostDate(Q\QDateTime::Now());
                $objCategoryNews->save();

                $this->dtgInstitution->refresh();

                if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                    $this->btnGoToEvents->Display = true;
                }

                $this->txtName->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSaveChange->Display = false;
                $this->btnCancel->Display = false;
                $this->btnAddInstitution->Enabled = true;
                $this->dtgInstitution->removeCssClass('disabled');
                $this->txtName->Text = null;
                $this->dlgToastr1->notify();
            } else {
                $this->txtName->Text = null;
                $this->txtName->focus();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtName->Text = null;
            $this->txtName->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the click event for the Save button. It processes and validates the data entered
     * in the institution form, updating the relevant records or showing appropriate notifications
     * depending on the state of the data.
     *
     * @param ActionParams $params Parameters passed during the button click action.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objChanges = OrganizingInstitution::loadById($this->intId);

        if ($this->txtName->Text) {
            if (in_array($this->intId, $this->objChangeIds) && $this->lstStatus->SelectedValue == 2) {
                $this->lstStatus->SelectedValue = 1;
                $this->dlgModal2->showDialogBox();

                if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                    $this->btnGoToEvents->Display = true;
                }

                $this->btnAddInstitution->Enabled = true;
                $this->txtName->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;
                $this->dtgInstitution->removeCssClass('disabled');

            } else if ($this->txtName->Text == $objChanges->getName() && $this->lstStatus->SelectedValue !== $objChanges->getStatus()) {
                $objChanges->setName(trim($this->txtName->Text));
                $objChanges->setStatus($this->lstStatus->SelectedValue);
                $objChanges->setPostUpdateDate(Q\QDateTime::Now());
                $objChanges->save();

                $this->dtgInstitution->refresh();
                $this->btnAddInstitution->Enabled = true;

                if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                    $this->btnGoToEvents->Display = true;
                }

                $this->txtName->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;

                $this->dtgInstitution->removeCssClass('disabled');
                $this->txtName->Text = $objChanges->getName();
                $this->dlgToastr1->notify();


            } else if (!OrganizingInstitution::titleExists(trim($this->txtName->Text))) {
                $this->txtName->Text = $objChanges->getName();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtName->Text = $objChanges->getName();
            $this->txtName->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the delete button click event. Determines the behavior based on the
     * presence of the current ID in the change list. Shows a confirmation dialog and
     * conditionally alters the display and enabled state of various UI elements.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
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

            if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                $this->btnGoToEvents->Display = true;
            }

            $this->btnAddInstitution->Enabled = true;
            $this->txtName->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgInstitution->removeCssClass('disabled');

        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the click event for deleting an item. Depending on the action parameter, it either performs a delete operation
     * or updates the UI components to reflect the current changes without deletion.
     *
     * @param ActionParams $params The parameters object that carries action-specific data, including an action parameter
     *                             that determines the course of action.
     * @return void
     */
    public function deleteItem_Click(ActionParams $params)
    {
        $objChanges = OrganizingInstitution::loadById($this->intId);

        if ($params->ActionParameter == "pass") {
            $objChanges->delete();
        } else {
            if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
                $this->btnGoToEvents->Display = true;
            }
        }

        $this->dtgInstitution->refresh();
        $this->btnAddInstitution->Enabled = true;
        $this->txtName->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgInstitution->removeCssClass('disabled');
        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the cancel button click event by adjusting the display and enabling/disabling
     * various UI elements based on the session state and resets the institution name input field.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {
            $this->btnGoToEvents->Display = true;
        }

        $this->txtName->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSaveChange->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddInstitution->Enabled = true;
        $this->dtgInstitution->removeCssClass('disabled');
        $this->txtName->Text = null;
    }

    /**
     * Handles the click event for the "Go To Events" button. Checks if there are changes
     * or group sessions related to institutions, and redirects to the sports calendar edit page
     * with appropriate query parameters if such sessions are found. Cleans up the session variables
     * after redirection.
     *
     * @param ActionParams $params Parameters associated with the button click event.
     * @return void No return value.
     */
    protected function btnGoToEvents_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['dtgInstitution_changes']) || !empty($_SESSION['dtgInstitution_group'])) {

        Application::redirect('sports_calendar_edit.php?id=' . $_SESSION['dtgInstitution_changes'] . '&group=' . $_SESSION['dtgInstitution_group']);
        unset($_SESSION['dtgInstitution_changes']);
        unset($_SESSION['dtgInstitution_group']);
        }
    }
}