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

class SportsContentTypesPanel extends Q\Control\Panel
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
    public $dtgSportsContentTypes;

    public $btnAddType;
    public $txtType;
    public $lstStatus;
    public $btnSaveType;
    public $btnSave;
    public $btnDelete;
    public $btnCancel;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $objContentTypes;
    protected $oldName;

    protected $strTemplate = 'SportsContentTypesPanel.tpl.php';

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
        $this->dtgSportsContentTypes_Create();
        $this->dtgSportsContentTypes->setDataBinder('BindData', $this);
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->CheckSportsContentTypes();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the data grid for displaying sports content types.
     * This method creates the necessary columns, paginators, and sets the grid to be editable.
     * Additionally, it assigns callbacks and default settings for sorting and pagination.
     *
     * @return void
     */
    protected function dtgSportsContentTypes_Create()
    {
        $this->dtgSportsContentTypes = new SportsContentTypesTable($this);
        $this->dtgSportsContentTypes_CreateColumns();
        $this->createPaginators();
        $this->dtgSportsContentTypes_MakeEditable();
        $this->dtgSportsContentTypes->RowParamsCallback = [$this, "dtgSportsContentTypes_GetRowParams"];
        $this->dtgSportsContentTypes->SortColumnIndex = 0;
        $this->dtgSportsContentTypes->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Initializes and creates columns for the data grid associated with sports content types.
     *
     * @return void
     */
    protected function dtgSportsContentTypes_CreateColumns()
    {
        $this->dtgSportsContentTypes->createColumns();
    }

    /**
     * Configures the sports content types data grid to be editable by adding
     * interactivity and styling. Registers an action to handle cell click events
     * and assigns necessary CSS classes for visual feedback.
     *
     * @return void
     */
    protected function dtgSportsContentTypes_MakeEditable()
    {
        $this->dtgSportsContentTypes->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgSportsContentTypesRow_Click'));
        $this->dtgSportsContentTypes->addCssClass('clickable-rows');
        $this->dtgSportsContentTypes->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for a row in the sports content types data grid.
     *
     * @param ActionParams $params The parameters associated with the action,
     * containing the identifier of the sports content type.
     *
     * @return void
     */
    protected function dtgSportsContentTypesRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objTypes = SportsContentTypes::load($this->intId);

        $this->oldName = $objTypes->getName();

        $this->txtType->Text = $objTypes->getName();
        $this->txtType->focus();
        $this->lstStatus->SelectedValue = $objTypes->Status ?? null;

        $this->dtgSportsContentTypes->addCssClass('disabled');
        $this->btnAddType->Enabled = false;
        $this->txtType->Display = true;
        $this->lstStatus->Display = true;
        $this->btnSave->Display = true;
        $this->btnDelete->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Retrieves the parameters for a row in the sports content types data grid.
     *
     * @param object $objRowObject The object representing the current row.
     * @param int $intRowIndex The index of the current row.
     * @return array An associative array of parameters for the row, including a 'data-value' key.
     */
    public function dtgSportsContentTypes_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes paginators for the sports content types data grid.
     * Configures the paginator with labels for navigation, the number of items per page,
     * the default sort column index, and enables AJAX for data retrieval.
     * Invokes additional filter actions configuration.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgSportsContentTypes->Paginator = new Bs\Paginator($this);
        $this->dtgSportsContentTypes->Paginator->LabelForPrevious = t('Previous');
        $this->dtgSportsContentTypes->Paginator->LabelForNext = t('Next');

        $this->dtgSportsContentTypes->ItemsPerPage = 10;
        $this->dtgSportsContentTypes->SortColumnIndex = 4;
        $this->dtgSportsContentTypes->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Initializes the items per page selection component with specific attributes and settings.
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
     * Retrieves a list of items associated with the assigned user object, applying specific conditions and clauses.
     * Iterates through the result set, creating a list item for each entry and marking it as selected if it matches the user's current assignment.
     *
     * @return ListItem[] Returns an array of ListItem objects, representing the items per page associated with the assigned user object.
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
     * Updates the items per page setting of the data grid based on the selected name
     * from the list control and refreshes the data grid.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgSportsContentTypes->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgSportsContentTypes->refresh();
    }

    /**
     * Initializes and configures the filter text box used for search functionality.
     * Sets placeholder text, text mode, disables autocomplete, and adds a CSS class.
     * Also invokes the method to add related filter actions.
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
     * Adds filter actions to the user interface component. It sets up event
     * listeners on the filter input to trigger AJAX calls when the input value
     * changes or when the enter key is pressed. This allows for dynamic filtering
     * of data without requiring a page reload.
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
     * Refreshes the data grid containing sports content types when a filter change is detected.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgSportsContentTypes->refresh();
    }

    /**
     * Binds data to the sports content types data grid using a specified condition.
     *
     * The method retrieves the condition for data binding through `getCondition()`
     * and applies it to the `dtgSportsContentTypes` for data binding.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgSportsContentTypes->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the user's search input.
     *
     * @return mixed A query condition object that either matches all entries
     *               or filters based on the specified search value.
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
                Q\Query\QQ::equal(QQN::SportsContentTypes()->Id, $strSearchValue),
                Q\Query\QQ::like(QQN::SportsContentTypes()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Processes sports calendar entries and extracts unique sports content type IDs.
     *
     * This method iterates over all entries in the SportsCalendar, retrieves sport content type IDs,
     * and compiles a unique list of these IDs.
     *
     * @return void
     */
    private function CheckSportsContentTypes()
    {
        $rows = SportsCalendar::LoadAll();
        $allValues = [];

        foreach ($rows as $row) {
            if (!empty($row->getSportsContentTypesIds())) {
                $values = explode(',', $row->getSportsContentTypesIds());
                $allValues = array_merge($allValues, $values);
            }
        }

        $this->objContentTypes = array_unique($allValues);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a set of buttons and controls for managing types.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnAddType = new Bs\Button($this);
        $this->btnAddType->Text = t(' Create a new type');
        $this->btnAddType->Glyph = 'fa fa-plus';
        $this->btnAddType->CssClass = 'btn btn-orange';
        $this->btnAddType->addWrapperCssClass('center-button');
        $this->btnAddType->CausesValidation = false;
        $this->btnAddType->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddType_Click'));
        $this->btnAddType->setCssStyle('float', 'left');
        $this->btnAddType->setCssStyle('margin-right', '10px');

        $this->txtType = new Bs\TextBox($this);
        $this->txtType->Placeholder = t('New type');
        $this->txtType->ActionParameter = $this->txtType->ControlId;
        $this->txtType->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtType->setHtmlAttribute('autocomplete', 'off');
        $this->txtType->setCssStyle('float', 'left');
        $this->txtType->setCssStyle('margin-right', '10px');
        $this->txtType->Width = 300;
        $this->txtType->Display = false;

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange form-horizontal radio-inline';
        $this->lstStatus->setCssStyle('float', 'left');
        $this->lstStatus->setCssStyle('margin-left', '15px');
        $this->lstStatus->setCssStyle('margin-right', '15px');
        $this->lstStatus->Display = false;

        $this->btnSaveType = new Bs\Button($this);
        $this->btnSaveType->Text = t('Save');
        $this->btnSaveType->CssClass = 'btn btn-orange';
        $this->btnSaveType->addWrapperCssClass('center-button');
        $this->btnSaveType->PrimaryButton = true;
        $this->btnSaveType->CausesValidation = true;
        $this->btnSaveType->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSaveType_Click'));
        $this->btnSaveType->setCssStyle('float', 'left');
        $this->btnSaveType->setCssStyle('margin-right', '10px');
        $this->btnSaveType->Display = false;

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
     * Initializes and configures two toastr notifications, one for success and one for error.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The type has been saved or modified.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('The type name must exist!');
        $this->dlgToastr2->ProgressBar = true;
    }

    /**
     * Initializes and configures multiple modal dialogs for user interactions and confirmations.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the sports content type?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The content type cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this content type, 
                                    simply release any content type previously associated with created sports calendar.</p>');
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The content type cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this content type, 
                                    simply release any content type previously associated with created sports calendar.</p>');
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The name of this content type already exists in the database, please choose another name!</p>');
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
     * Handles the click event for the Add Type button.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function btnAddType_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->txtType->Display = true;
        $this->lstStatus->Display = true;
        $this->lstStatus->SelectedValue = 2;
        $this->btnSaveType->Display = true;
        $this->btnCancel->Display = true;
        $this->txtType->Text = null;
        $this->txtType->focus();
        $this->btnAddType->Enabled = false;
        $this->dtgSportsContentTypes->addCssClass('disabled');
    }

    /**
     * Handles the click event for the save button to create a new sports content type.
     *
     * @param ActionParams $params Parameters passed from the click action triggering the method.
     * @return void
     */
    protected function btnSaveType_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtType->Text) {
            if (!EventsChanges::titleExists(trim($this->txtType->Text))) {
                $objCategoryNews = new SportsContentTypes();
                $objCategoryNews->setName(trim($this->txtType->Text));
                $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                $objCategoryNews->setPostDate(Q\QDateTime::Now());
                $objCategoryNews->save();

                $this->dtgSportsContentTypes->refresh();

                $this->txtType->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSaveType->Display = false;
                $this->btnCancel->Display = false;
                $this->btnAddType->Enabled = true;
                $this->dtgSportsContentTypes->removeCssClass('disabled');
                $this->txtType->Text = null;
                $this->dlgToastr1->notify();
            } else {
                $this->txtType->Text = null;
                $this->txtType->focus();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtType->Text = null;
            $this->txtType->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the click event for the save button. This function checks and updates
     * the sports content types according to the user input and displays appropriate dialogs.
     *
     * @param ActionParams $params The parameters associated with the click action.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objContentTypes = SportsContentTypes::loadById($this->intId);

        if ($this->txtType->Text) {
            if (in_array($this->intId, $this->objContentTypes) && $this->lstStatus->SelectedValue == 2) {
                $this->lstStatus->SelectedValue = 1;
                $this->dlgModal2->showDialogBox();

                $this->btnAddType->Enabled = true;
                $this->txtType->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;
                $this->dtgSportsContentTypes->removeCssClass('disabled');

            } else if ($this->txtType->Text == $objContentTypes->getName() && $this->lstStatus->SelectedValue !== $objContentTypes->getStatus()) {
                $objContentTypes->setName(trim($this->txtType->Text));
                $objContentTypes->setStatus($this->lstStatus->SelectedValue);
                $objContentTypes->setPostUpdateDate(Q\QDateTime::Now());
                $objContentTypes->save();

                $this->dtgSportsContentTypes->refresh();
                $this->btnAddType->Enabled = true;

                $this->txtType->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;

                $this->dtgSportsContentTypes->removeCssClass('disabled');
                $this->txtType->Text = $objContentTypes->getName();
                $this->dlgToastr1->notify();


            } else if (!SportsContentTypes::titleExists(trim($this->txtType->Text))) {
                $this->txtType->Text = $objContentTypes->getName();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtType->Text = $objContentTypes->getName();
            $this->txtType->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the deletion process when the delete button is clicked.
     * This method checks if the current ID is in the list of content types.
     * If it is, it shows a modal dialog box and updates the display and enabled state
     * of various controls. Otherwise, it shows a different modal dialog box.
     *
     * @param ActionParams $params Parameters associated with the delete button click action.
     * @return void
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (in_array($this->intId, $this->objContentTypes)) {
            $this->dlgModal3->showDialogBox();

            $this->btnAddType->Enabled = true;
            $this->txtType->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgSportsContentTypes->removeCssClass('disabled');

        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the click event for deleting a sports content type item.
     * Depending on the specified action parameter, it deletes the content type
     * and updates the UI components to reflect the changes.
     *
     * @param ActionParams $params Contains the parameters for the action, including the condition for deletion.
     * @return void
     */
    public function deleteItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objContentTypes = SportsContentTypes::loadById($this->intId);

        if ($params->ActionParameter == "pass") {
            $objContentTypes->delete();
        }

        $this->dtgSportsContentTypes->refresh();
        $this->btnAddType->Enabled = true;
        $this->txtType->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgSportsContentTypes->removeCssClass('disabled');
        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the event when the cancel button is clicked. It hides various form elements,
     * resets text fields, and enables certain buttons within the user interface.
     *
     * @param ActionParams $params The parameters associated with the action event triggered by clicking the button.
     * @return void This method does not return a value.
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->txtType->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSaveType->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddType->Enabled = true;
        $this->dtgSportsContentTypes->removeCssClass('disabled');
        $this->txtType->Text = null;
    }
}