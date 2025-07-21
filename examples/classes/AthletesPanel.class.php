<?php

use QCubed as Q;
use QCubed\Action\Ajax;
use QCubed\Bootstrap as Bs;
use QCubed\Event\Change;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\QDateTime;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class AthletesPanel extends Q\Control\Panel
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

    public $lblInfo;
    public $btnAddNewRecordsHolder;

    public $lblFirstName;
    public $txtFirstName;
    public $lblLastName;
    public $txtLastName;
    public $lblBirthDate;
    public $dtxBirthDate;
    public $btnBirthDate;

    public $lblGender;
    public $lstGender;
    public $lblStatus;
    public $lstStatus;
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
    public $dtgAthletes;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $intClick;
    protected $blnEditMode = true;
    protected $objAthlete;
    protected $intNewHolderId;
    protected $errors = []; // Array for tracking errors

    protected $DateTimeFormat;

    protected $strTemplate = 'AthletesPanel.tpl.php';

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

        $this->intLoggedUserId = 3;
        $this->objUser = User::load($this->intLoggedUserId);

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgAthletes_Create();
        $this->dtgAthletes->setDataBinder('bindData', $this);

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes the Athletes data grid used for displaying athlete records.
     *
     * The method sets up the data grid by instantiating it, adding necessary columns,
     * configuring pagination, making rows editable, and defining various
     * properties such as sorting and items per page based on the user's preferences.
     *
     * @return void
     */
    protected function dtgAthletes_Create()
    {
        $this->dtgAthletes = new AthletesTable($this);
        $this->dtgAthletes_CreateColumns();
        $this->createPaginators();
        $this->dtgAthletes_MakeEditable();
        $this->dtgAthletes->RowParamsCallback = [$this, "dtgAthletes_GetRowParams"];
        $this->dtgAthletes->SortColumnIndex = 1;
        $this->dtgAthletes->SortDirection = 1;
        $this->dtgAthletes->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Initiates the creation of columns for the data grid of athletes.
     *
     * @return void
     */
    protected function dtgAthletes_CreateColumns()
    {
        $this->dtgAthletes->createColumns();
    }

    /**
     * Makes the athletes datagrid editable by adding event-driven functionality.
     * Sets actions and CSS classes on the datagrid to allow for row click interactions.
     *
     * @return void
     */
    protected function dtgAthletes_MakeEditable()
    {
        $this->dtgAthletes->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgAthletes_Click'));
        $this->dtgAthletes->addCssClass('clickable-rows');
        $this->dtgAthletes->CssClass = 'table vauu-table js-sports-area table-hover table-responsive';
    }

    /**
     * Handles the click event on the athletes data grid. Retrieves the selected athlete's information,
     * updates the UI to display the athlete's details, and prepares certain fields for further input or modification.
     * Additionally, the method disables some UI components and triggers JavaScript for additional UI adjustments.
     *
     * @param ActionParams $params Parameters containing information about the click event, including the selected action parameter.
     *
     * @return void
     */
    protected function dtgAthletes_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objAthlete = Athletes::load($this->intId);
        $this->intClick = $this->intId;

        if ($objAthlete->getIsLocked() == 1) {
            $this->btnDelete->Display = true;
        } else {
            $this->btnDelete->Display = false;
        }

        $this->blnEditMode = true;

        $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
        $this->txtFilter->Enabled = false;
        $this->dtgAthletes->Paginator->Enabled = false;

        $this->dtgAthletes->addCssClass('disabled');
        $this->refreshDisplay($this->intId);

        Application::executeJavaScript("
            $('.setting-wrapper').removeClass('hidden');
            $('.form-actions-wrapper').removeClass('hidden');
            $('.js-wrapper-top').get(0).scrollIntoView({behavior: 'smooth'});
        ");

        $this->activeInputs($objAthlete);
        $this->checkInputs();
    }

    /**
     * Generates an array of parameters for a specific row in the Athletes data grid.
     *
     * @param object $objRowObject The object representing a single row in the data grid.
     * @param int $intRowIndex The index of the current row in the data grid.
     * @return array An associative array of parameters, including CSS class and data attributes, for configuring the row.
     */
    public function dtgAthletes_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();

        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Configures and sets up paginators for the athlete data grid.
     *
     * This method initializes the paginator for the athlete data grid, sets pagination labels,
     * defines the number of items to display per page, specifies the default sorting column,
     * and enables asynchronous interactions. Additionally, it invokes the method to add filter actions.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgAthletes->Paginator = new Bs\Paginator($this);
        $this->dtgAthletes->Paginator->LabelForPrevious = t('Previous');
        $this->dtgAthletes->Paginator->LabelForNext = t('Next');

        $this->dtgAthletes->ItemsPerPage = 10;
        $this->dtgAthletes->SortColumnIndex = 0;
        $this->dtgAthletes->UseAjax = true;
        $this->addFilterActions();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes the Items Per Page select control with specific properties and behavior.
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
     * Retrieves a list of ListItem objects based on the condition and clauses applied
     * to the ItemsPerPage data associated with a specific user.
     *
     * @return ListItem[] An array of ListItem objects, where each item represents
     *                    an entry in the ItemsPerPage collection. If the user's
     *                    assigned ItemsPerPage object matches an entry, it will be
     *                    marked as selected.
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
     * Updates the number of items per page for the datagrid based on the selected option
     * from the dropdown list of available options by the assigned user object.
     *
     * @param ActionParams $params Parameters passed from the triggered action.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgAthletes->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgAthletes->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes a filter input field with specific attributes and styling for search functionality.
     *
     * @return void
     */
    public function createFilter()
    {
        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();
    }

    /**
     * Adds filter actions to the filter text box to handle input and Enter key events.
     *
     * The method attaches an input event to trigger an AJAX update after 300 milliseconds
     * of idle time and assigns a set of actions for the Enter key, including triggering
     * an AJAX update and terminating further actions.
     *
     * @return void
     */
    protected function addFilterActions()
    {
        $this->txtFilter->addAction(new Q\Event\Input(300), new Q\Action\AjaxControl($this, 'filterChanged'));
        $this->txtFilter->addActionArray(new Q\Event\EnterKey(),
            [
                new Q\Action\AjaxControl($this, 'filterChanged'),
                new Q\Action\Terminate()
            ]
        );
    }

    /**
     * Refreshes the athlete data grid when the filter criteria are changed.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgAthletes->refresh();
    }

    /**
     * Binds data to a data grid control based on the condition retrieved.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgAthletes->bindData($objCondition);
    }

    /**
     * Constructs and returns a query condition based on the current filter input.
     * If the filter input is empty or null, it returns a condition that matches all records.
     * Otherwise, it returns a condition that matches records where the first name,
     * last name, birth date, or gender match the filter input.
     *
     * @return Q\Query\Condition\ConditionInterface The constructed query condition.
     */
    public function getCondition()
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
                Q\Query\QQ::like(QQN::Athletes()->FirstName, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::Athletes()->LastName, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::Athletes()->BirthDate, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::Athletes()->AthleteGender->Gender, "%" . $strSearchValue . "%"),
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Sets up various input controls, labels, and UI elements for managing athlete records.
     * This method creates form elements for input fields like first name, last name, birth date,
     * gender, athlete status, along with labels and additional metadata such as creation and update information.
     * It dynamically adjusts the display of certain elements based on the number of athletes available.
     *
     * @return void No value is returned as this method generates and configures form elements.
     */
    protected function createInputs()
    {
        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please create the first record holder!');
        $this->lblInfo->setCssStyle('margin-bottom', 0);

        $countAthletes = Athletes::countAll();

        if ($countAthletes === 0) {
            $this->lblInfo->Display = true;
            $this->lstItemsPerPageByAssignedUserObject->Display = false;
            $this->txtFilter->Display = false;
            $this->dtgAthletes->Paginator->Display = false;
            $this->dtgAthletes->Display = false;
        } else {
            $this->lblInfo->Display = false;
            $this->lstItemsPerPageByAssignedUserObject->Display = true;
            $this->txtFilter->Display = true;
            $this->dtgAthletes->Paginator->Display = true;
            $this->dtgAthletes->Display = true;
        }

        $this->lblFirstName = new Q\Plugin\Control\Label($this);
        $this->lblFirstName->Text = t('First name');
        $this->lblFirstName->addCssClass('col-md-4');
        $this->lblFirstName->setCssStyle('font-weight', 'normal');
        $this->lblFirstName->Required = true;

        $this->txtFirstName = new Bs\TextBox($this);
        $this->txtFirstName->Placeholder = t('First name');
        $this->txtFirstName->setHtmlAttribute('autocomplete', 'off');
        $this->txtFirstName->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtFirstName->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtFirstName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtFirstName->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
        $this->txtFirstName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblLastName = new Q\Plugin\Control\Label($this);
        $this->lblLastName->Text = t('Last name');
        $this->lblLastName->addCssClass('col-md-4');
        $this->lblLastName->setCssStyle('font-weight', 'normal');
        $this->lblLastName->Required = true;

        $this->txtLastName = new Bs\TextBox($this);
        $this->txtLastName->Placeholder = t('Last name');
        $this->txtLastName->setHtmlAttribute('autocomplete', 'off');
        $this->txtLastName->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtLastName->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtLastName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtLastName->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
        $this->txtLastName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblBirthDate = new Q\Plugin\Control\Label($this);
        $this->lblBirthDate->Text = t('Birth date');
        $this->lblBirthDate->addCssClass('col-md-4');
        $this->lblBirthDate->setCssStyle('font-weight', 'normal');
        $this->lblBirthDate->Required = true;

        $this->dtxBirthDate = new Q\Plugin\Control\DateTimeTextBox($this);
        $this->dtxBirthDate->Mode = 'date';
        $this->dtxBirthDate->DateTimeFormat = 'DD.MM.YYYY';
        $this->dtxBirthDate->Placeholder = t('dd.mm.yyyy');
        $this->dtxBirthDate->LabelForInvalid = t('dd.mm.yyyy');
        $this->dtxBirthDate->setCssStyle('width', '78%');
        $this->dtxBirthDate->setHtmlAttribute('autocomplete', 'off');
        $this->dtxBirthDate->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'dtxBirthDate_EnterKey'));
        $this->dtxBirthDate->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->dtxBirthDate->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
        $this->dtxBirthDate->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->dtxBirthDate->UseWrapper = false;

        $this->lblGender = new Q\Plugin\Control\Label($this);
        $this->lblGender->Text = t('Gender');
        $this->lblGender->addCssClass('col-md-4');
        $this->lblGender->setCssStyle('font-weight', 'normal');
        $this->lblGender->Required = true;

        $this->lstGender = new Q\Plugin\Control\Select2($this);
        $this->lstGender->MinimumResultsForSearch = -1;
        $this->lstGender->ContainerWidth = 'resolve';
        $this->lstGender->Theme = 'web-vauu';
        $this->lstGender->Width = '100%';
        $this->lstGender->setCssStyle('float', 'left');
        $this->lstGender->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstGender->addItem(t('- Select gender -'), null, true);
        $this->lstGender->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstGender_Change'));

        $objGenders = AthleteGender::loadAll();

        foreach ($objGenders as $objGender) {
            $this->lstGender->addItem($objGender->Gender, $objGender->Id);
        }

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->addCssClass('col-md-4');
        $this->lblStatus->setCssStyle('font-weight', 'normal');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
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
        $this->btnAddNewRecordsHolder = new Bs\Button($this);
        $this->btnAddNewRecordsHolder->Text = t('Add new record holder');
        $this->btnAddNewRecordsHolder->CssClass = 'btn btn-orange';
        $this->btnAddNewRecordsHolder->CausesValidation = false;
        $this->btnAddNewRecordsHolder->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddNewRecordsHolder_Click'));

        $this->btnBirthDate = new Bs\Button($this);
        $this->btnBirthDate->Tip = true;
        $this->btnBirthDate->Glyph = 'fa fa-chevron-down';
        $this->btnBirthDate->CssClass = 'btn btn-default';
        $this->btnBirthDate->addCssClass('input-group-addon');
        $this->btnBirthDate->setCssStyle('width', 'auto');
        $this->btnBirthDate->CausesValidation = false;
        $this->btnBirthDate->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->btnBirthDate->UseWrapper = false;

        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->setCssStyle('margin-right', '10px');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));

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
    }

    /**
     * Initializes a series of Toastr notification objects with predefined configurations.
     * Each notification displays a specific message, alert type, position, and progress bar.
     *
     * @return void This method does not return a value.
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The new record holder was successfully added to the database.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('The record holder\'s data was saved or modified!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('This new record holder already exists in the database!');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('First name is required!');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('Last name is required!');
        $this->dlgToastr5->ProgressBar = true;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('Birth date is required!');
        $this->dlgToastr6->ProgressBar = true;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('Gender is required!');
        $this->dlgToastr7->ProgressBar = true;

        $this->dlgToastr8 = new Q\Plugin\Toastr($this);
        $this->dlgToastr8->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr8->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr8->Message = t('The first name and last name of the new record holder must be provided together!');
        $this->dlgToastr8->ProgressBar = true;

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
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently 
                                    delete the record holder?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hideItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This record holder cannot be deleted 
                                    because they are locked in the records table or leaderboard!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">If you still wish to delete, 
                                    please unlink the record holder from both tables.</p>');

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

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Tip");
        $this->dlgModal5->HeaderClasses = 'btn-darkblue';
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">Invalid date format!</p>
                                    <p style="line-height: 25px; margin-bottom: 5px;">Please use the date format "' . $this->dtxBirthDate->LabelForInvalid . '"! </p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">The previously saved record holder\'s date will be automatically restored!</p>');

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal6->Title = t("Warning");
        $this->dlgModal6->HeaderClasses = 'btn-danger';
        $this->dlgModal6->addCloseButton(t("I understand"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles click action for adding a new record holder. Updates the UI elements,
     * initializes a new athlete record, and processes form fields for the new entry.
     *
     * @param ActionParams $params The parameters associated with the button click event,
     *                              containing additional context and data for processing.
     * @return void This method does not return any value but performs a sequence of actions,
     *              such as UI updates, creating a new athlete record, and refreshing the display.
     */
    public function btnAddNewRecordsHolder_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').removeClass('hidden');
            $('.form-actions-wrapper').removeClass('hidden');
        ");

        $this->txtFirstName->Text = '';
        $this->txtFirstName->focus();
        $this->dtgAthletes->addCssClass('disabled');

        $this->btnSave->Display = true;
        $this->btnDelete->Display = false;
        $this->blnEditMode = false;

        $this->resetInputs();
    }

    /**
     * Handles the logic executed when the Enter key is pressed within the birthdate text box.
     * It validates and processes the input date based on the athlete's lock status.
     *
     * @param ActionParams $params The parameters associated with the action triggered by pressing the Enter key.
     *
     * @return void This method does not return a value but performs validation, updates the athlete's birthdate,
     *              and shows notifications or dialog boxes depending on the success or failure of the operation.
     */
    protected function dtxBirthDate_EnterKey(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->blnEditMode === true) {

            if ($this->intClick) {
                $objAthlete = Athletes::load($this->intClick);
            }

            $this->checkInputs();

            if ($objAthlete->getIsLocked() === 2) {
                if ($this->dtxBirthDate->Text) {
                    if ($this->dtxBirthDate->validateFormat()) {
                        $objAthlete->setBirthDate($this->dtxBirthDate->DateTime);
                        $objAthlete->save();

                        $this->dlgToastr4->notify();
                        $this->updateAndValidateAthlete($objAthlete);
                    } else {
                        $this->dtxBirthDate->Text = $objAthlete->getBirthDate() ? $objAthlete->getBirthDate()->qFormat('DD.MM.YYYY') : null;

                        $this->dlgModal5->showDialogBox();
                        return;
                    }
                } else {
                    $this->dtxBirthDate->Text = $objAthlete->getBirthDate() ? $objAthlete->getBirthDate()->qFormat('DD.MM.YYYY') : null;

                    $this->dlgModal4->showDialogBox();
                }
            }

            if ($objAthlete->getIsLocked() === 1) {
                if ($this->dtxBirthDate->Text) {
                    if ($this->dtxBirthDate->validateFormat()) {
                        $objAthlete->setBirthDate($this->dtxBirthDate->DateTime);
                        $objAthlete->save();

                        $this->dlgToastr2->notify();
                    } else {
                        $this->dtxBirthDate->Text = $objAthlete->getBirthDate() ? $objAthlete->getBirthDate()->qFormat('DD.MM.YYYY') : null;

                        $this->dlgModal5->showDialogBox();
                        return;
                    }
                } else {
                    $this->dtxBirthDate->Text = null;
                    $objAthlete->setBirthDate(null);

                    $this->lstStatus->SelectedValue = 2;
                    $objAthlete->setStatus(2);

                    $this->dlgToastr11->notify();
                }

                $objAthlete->save();
                $this->updateAndValidateAthlete($objAthlete);
            }

            unset($this->errors);
        }
    }

    /**
     * Handles the change event for the gender selection dropdown and performs updates
     * to the athlete's gender information based on input values, validation, and
     * certain conditional checks.
     *
     * @param ActionParams $params Action parameters containing information related
     *                             to the event that triggered the method execution.
     *
     * @return void This method does not return a value. It updates the athlete object's
     *              gender information, validates input, triggers notifications, and manages
     *              dialog boxes based on the business logic implemented.
     */
    protected function lstGender_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->blnEditMode === true) {

            if ($this->intClick) {
                $objAthlete = Athletes::load($this->intClick);
            }

            if ($objAthlete->getIsLocked() == 2) {
                if ($this->lstGender->SelectedValue) {
                    $objAthlete->setAthleteGenderId($this->lstGender->SelectedValue);
                    $objAthlete->save();

                    $this->updateAndValidateAthlete($objAthlete);
                    $this->dlgToastr2->notify();
                } else {
                    $this->lstGender->SelectedValue = $objAthlete->getAthleteGenderId();
                    $this->lstGender->refresh();

                    $this->dlgModal4->showDialogBox();
                    return;
                }
            }

            $this->checkInputs();

            if (count($this->errors)) {
                $this->lstStatus->SelectedValue = 2;
                $objAthlete->setStatus(2);
            }

            if ($objAthlete->getIsLocked() == 1) {
                if ($this->lstGender->SelectedValue) {
                    $objAthlete->setAthleteGenderId($this->lstGender->SelectedValue);

                    $this->dlgToastr2->notify();
                } else {
                    $objAthlete->setAthleteGenderId($this->lstGender->SelectedValue);
                }

                $objAthlete->save();
                $this->updateAndValidateAthlete($objAthlete);
            }

            unset($this->errors);
        }
    }

    /**
     * Handles changes to the athlete status, validates inputs, updates the athlete's
     * status, and performs corresponding actions based on the status and lock state.
     *
     * @param ActionParams $params The parameters associated with the change action,
     *                              such as the triggering event details.
     *
     * @return void This method performs actions including updating the athlete's
     *              status, displaying notifications or dialogs, and saving changes.
     */
    protected function lstStatus_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->blnEditMode === true) {

            if ($this->intClick) {
                $objAthlete = Athletes::load($this->intClick);
            }

            $this->checkInputs();

            if (count($this->errors)) {
                $this->lstStatus->SelectedValue = 2;
                $objAthlete->setStatus(2);
            }

            if ($objAthlete->getIsLocked() === 2) {
                if ($this->lstStatus->SelectedValue == 2) {
                    $this->dlgModal3->showDialogBox();
                    $this->lstStatus->SelectedValue = 1;
                }
            }

            if ($objAthlete->getIsLocked() === 1) {
                if (!count($this->errors)) {
                    if ($this->lstStatus->SelectedValue == 1) {
                        $this->lstStatus->SelectedValue = 1;
                        $objAthlete->setStatus(1);

                        $this->dlgToastr10->notify();
                    } else {
                        $this->lstStatus->SelectedValue = 2;
                        $objAthlete->setStatus(2);

                        $this->dlgToastr11->notify();
                    }
                }

                $objAthlete->save();
                $this->updateAndValidateAthlete($objAthlete);
            }

            unset($this->errors);
        }
    }

    /**
     * Handles the save click event for the athlete form, performing validation,
     * creation, or updating of athlete records based on the current mode and state.
     *
     * The method executes several actions depending on whether it's in edit or
     * create mode, as well as handling specific validations like duplicate names
     * and birth date format checks. It also manages UI updates and notifications
     * upon success or error.
     *
     * @param ActionParams $params Parameters associated with the button click action,
     *                             generally used for action bindings and event data.
     * @return void This method does not return a value, but performs necessary actions
     *              for saving, updating, and validating athlete data, and managing
     *              UI-related tasks like dialogs and notifications.
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objAthlete = Athletes::load($this->intClick);
        }

        if (($this->blnEditMode === false) || ($objAthlete->getIsLocked() === 1)) {
            $this->checkInputs();
        }

        If ($this->errors) {
            $this->lstStatus->SelectedValue = 2;
            $objAthlete->setStatus(2);
        }

        if ($this->blnEditMode === false) {
            if (!count($this->errors)) {
                if (!Athletes::namesExists(trim($this->txtFirstName->Text), trim($this->txtLastName->Text))) {
                    $objAthlete = new Athletes();
                    $this->saveInputs($objAthlete);
                    $objAthlete->setPostDate(Q\QDateTime::Now());
                    $objAthlete->setAssignedByUser($this->intLoggedUserId);
                    $objAthlete->setAuthor($objAthlete->getAssignedByUserObject());
                    $objAthlete->save();

                    Application::executeJavaScript("
                            $('.setting-wrapper').addClass('hidden');
                            $('.form-actions-wrapper').addClass('hidden');
                        ");

                    $this->hideUserWindow();
                    $this->dtgAthletes->removeCssClass('disabled');

                    $this->dlgToastr1->notify();
                } else if (!$this->dtxBirthDate->validateFormat()) {
                    $this->dtxBirthDate->Text = '';
                    $this->dtxBirthDate->focus();

                    $this->dlgModal5->showDialogBox();
                    return;
                } else {
                    $this->txtFirstName->Text = '';
                    $this->txtLastName->Text = '';
                    $this->txtFirstName->focus();

                    $this->dlgToastr3->notify();
                }
            }
        }

        if ($this->blnEditMode === true) {
            if ($objAthlete->getIsLocked() === 2) {
                if (!count($this->errors)) {
                    if (!$this->dtxBirthDate->validateFormat()) {
                        $this->dtxBirthDate->Text = $objAthlete->getBirthDate() ? $objAthlete->getBirthDate()->qFormat('DD.MM.YYYY') : null;

                        $this->dlgModal5->showDialogBox();
                        return;
                    } else if (!Athletes::namesExists(trim($this->txtFirstName->Text), trim($this->txtLastName->Text))) {
                        $this->saveInputs($objAthlete);
                        $objAthlete->save();

                        $this->updateAndValidateAthlete($objAthlete);
                        $this->dlgToastr4->notify();
                    } else {
                        $this->activeInputs($objAthlete);
                        $this->lstStatus->SelectedValue = 1;

                        $this->dlgModal4->showDialogBox();
                    }
                } else {
                    $this->activeInputs($objAthlete);

                    $this->dlgModal4->showDialogBox();
                }
            }

            if ($objAthlete->getIsLocked() === 1) {
                if (!Athletes::namesExists(trim($this->txtFirstName->Text), trim($this->txtLastName->Text))) {
                    $this->saveInputs($objAthlete);

                    $this->dlgToastr2->notify();
                } else if (!$this->dtxBirthDate->Text) {
                    $objAthlete->setBirthDate(null);
                    $this->dtxBirthDate->Text = '';
                } else if (!$this->dtxBirthDate->validateFormat()) {
                    $this->dtxBirthDate->Text = $objAthlete->getBirthDate() ? $objAthlete->getBirthDate()->qFormat('DD.MM.YYYY') : null;

                    $this->dlgModal5->showDialogBox();
                    return;
                } else {
                    $objAthlete->setBirthDate($this->dtxBirthDate->DateTime);
                    $this->dlgToastr2->notify();
                }

                $this->saveInputs($objAthlete);
                $objAthlete->save();
                $this->updateAndValidateAthlete($objAthlete);
            }
        }

        unset($this->errors);
    }

    /**
     * Resets the input fields in the form to their default or empty state.
     * This includes clearing text input fields, removing attributes like 'required',
     * clearing selected values, and refreshing dropdown lists.
     *
     * @return void This method does not return any value.
     */
    public function resetInputs()
    {
        $this->txtFirstName->Text = '';
        $this->txtFirstName->removeHtmlAttribute('required');

        $this->txtLastName->Text = '';
        $this->txtLastName->removeHtmlAttribute('required');

        $this->dtxBirthDate->Text = '';
        $this->dtxBirthDate->removeHtmlAttribute('required');

        $this->lstGender->SelectedValue = null;
        $this->lstGender->removeCssClass('has-error');
        $this->lstGender->refresh();

        $this->lstStatus->SelectedValue = 2;
        $this->lstStatus->refresh();
    }

    /**
     * Populates input fields with data from the given object and refreshes dropdown elements.
     *
     * @param object $objEdit The object containing the data to populate the input fields,
     *                        such as first name, last name, birth date, gender, and status.
     *
     * @return void
     */
    public function activeInputs($objEdit)
    {
        $this->txtFirstName->Text = $objEdit->getFirstName();
        $this->txtLastName->Text = $objEdit->getLastName();
        $this->dtxBirthDate->Text = $objEdit->getBirthDate() ? $objEdit->getBirthDate()->qFormat('DD.MM.YYYY') : null;
        $this->lstGender->SelectedValue = $objEdit->getAthleteGenderId();
        $this->lstStatus->SelectedValue = $objEdit->getStatus();

        $this->lstGender->refresh();
        $this->lstStatus->refresh();
    }

    /**
     * Saves input values into the provided Athlete object by setting its attributes
     * based on form field data.
     *
     * @param Athlete $objAthlete The Athlete object that will have its properties updated
     *                            using the values from the respective input fields.
     * @return void This method does not return a value.
     */
    public function saveInputs($objAthlete)
    {
        $objAthlete->setFirstName(trim($this->txtFirstName->Text));
        $objAthlete->setLastName(trim($this->txtLastName->Text));
        $objAthlete->setBirthDate($this->dtxBirthDate->DateTime);
        $objAthlete->setAthleteGenderId($this->lstGender->SelectedValue);
        $objAthlete->setStatus($this->lstStatus->SelectedValue);
    }

    /**
     * Handles the click event for the cancel button, performing actions to reset
     * the UI by hiding specific elements and re-enabling functionality for various
     * user interface components.
     *
     * @param ActionParams $params The action parameters that provide context for the
     *                             event, including any relevant metadata associated
     *                             with the click action.
     *
     * @return void This method does not return a value as it operates directly on
     *              the interface and instance properties.
     */
    public function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->hideUserWindow();

        $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
        $this->txtFilter->Enabled = true;
        $this->dtgAthletes->Paginator->Enabled = true;
        $this->dtgAthletes->removeCssClass('disabled');
    }

    /**
     * Handles the click event to update athlete information and display a notification.
     * This method retrieves an athlete's details based on a new holder ID or a default ID,
     * updates UI elements with the athlete's information, and triggers a notification.
     *
     * @param ActionParams $params The parameters passed along with the click action,
     *                              providing context or additional data for the event.
     *
     * @return void This method does not return a value. It updates UI components and
     *              triggers a notification as part of its operation.
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objAthlete = Athletes::load($this->intClick);
        }

        $this->txtFirstName->Text = $objAthlete->getFirstName();
        $this->txtLastName->Text = $objAthlete ->getLastName();
        $this->dtxBirthDate->Text = $objAthlete->getBirthDate() ? $objAthlete->getBirthDate()->qFormat('DD.MM.YYYY') : null;

        $this->dlgToastr7->notify();
    }

    /**
     * Validates form input fields and updates their attributes or styles to
     * reflect validation errors where necessary.
     *
     * @return void This method performs validation checks on input fields
     *              (e.g., first name, last name, birth date, gender) and
     *              modifies the form's attributes or classes to signal
     *              validation issues. Errors are recorded in the $errors array.
     */
    public function checkInputs()
    {
        // We check each field and add errors if necessary
        if (!$this->txtFirstName->Text) {
            $this->dlgToastr4->notify();
            $this->txtFirstName->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtFirstName';
        } else {
            $this->txtFirstName->removeHtmlAttribute('required');
        }

        if (!$this->txtLastName->Text) {
            $this->dlgToastr5->notify();
            $this->txtLastName->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtLastName';
        } else {
            $this->txtLastName->removeHtmlAttribute('required');
        }

        if (!$this->txtFirstName->Text || !$this->txtLastName->Text) {
            $this->dlgToastr8->notify();
            $this->txtFirstName->setHtmlAttribute('required', 'required');
            $this->txtLastName->setHtmlAttribute('required', 'required');
            $this->errors[] = 'names';
        } else {
            $this->txtFirstName->removeHtmlAttribute('required');
            $this->txtLastName->removeHtmlAttribute('required');
        }

        if (!$this->dtxBirthDate->Text) {
            $this->dlgToastr6->notify();
            $this->dtxBirthDate->setHtmlAttribute('required', 'required');
            $this->errors[] = 'dtxBirthDate';
        } else {
            $this->dtxBirthDate->removeHtmlAttribute('required');
        }

        if (!$this->lstGender->SelectedValue) {
            $this->dlgToastr7->notify();
            $this->lstGender->addCssClass('has-error');
            $this->errors[] = 'lstGender';
        } else {
            $this->lstGender->removeCssClass('has-error');
        }
    }

    /**
     * Updates the athlete object with new data, validates the fields, and refreshes
     * the display with the updated information.
     *
     * @param Athlete $objAthlete The athlete object to be updated. This method updates
     *                            the post update date, assigns the editors, and saves
     *                            the changes to the database. It also validates the
     *                            fields and displays appropriate notifications if
     *                            there are validation errors.
     * @return void This method does not return a value but modifies the athlete object,
     *              updates several UI elements with the new data, and performs validation
     *              checks with notifications.
     */
    protected function updateAndValidateAthlete($objAthlete)
    {
        $objAthlete->setPostUpdateDate(Q\QDateTime::Now());
        $objAthlete->setAssignedEditorsNameById($this->intLoggedUserId);
        $objAthlete->save();

        $this->calPostDate->Text = $objAthlete->PostDate ? $objAthlete->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->Text = $objAthlete->PostUpdateDate ? $objAthlete->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->txtAuthor->Text = $objAthlete->Author;
        $this->txtUsersAsEditors->Text = implode(', ', $objAthlete->getUserAsEditorsArray());

        $this->refreshDisplay($objAthlete->getId());
    }

    /**
     * Handles the delete button click event, determining whether to display a confirmation
     * dialog box based on the lock status of the selected athlete.
     *
     * @param ActionParams $params The parameters associated with the action that triggered the button click event.
     * @return void
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objAthlete = Athletes::load($this->intClick);
        }

        if ($objAthlete->getIsLocked() === 2) {
            $this->dlgModal2->showDialogBox();
        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the delete action for an Athlete object. Determines the target Athlete
     * based on the provided parameters, deletes the corresponding record, updates the
     * UI to reflect the changes, and re-enables disabled components.
     *
     * @param ActionParams $params Parameters containing information about the action
     *                              performed, such as identifiers used to determine
     *                              the Athlete to delete.
     *
     * @return void This method performs operations such as deleting the Athlete and
     *              updating the UI but does not return any value.
     */
    public function deleteItem_Click(ActionParams $params)
    {
        if ($this->intClick) {
            $objAthlete = Athletes::load($this->intClick);
        }

        $objAthlete->delete();
        $this->dlgModal1->hideDialogBox();

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
        $this->txtFilter->Enabled = true;
        $this->dtgAthletes->Paginator->Enabled = true;
        $this->dtgAthletes->removeCssClass('disabled');
    }

    /**
     * Handles the click event for hiding specific UI elements and re-enabling
     * disabled components in the application interface.
     *
     * @param ActionParams $params Contains parameters related to the action event
     *                             triggered by the user.
     * @return void
     */
    protected function hideItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
        $this->txtFilter->Enabled = true;
        $this->dtgAthletes->Paginator->Enabled = true;
        $this->dtgAthletes->removeCssClass('disabled');

        $this->dlgModal1->hideDialogBox();
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
        $objAthlete = Athletes::load($objEdit);

        if (!$objAthlete) {
            $this->lblPostDate->Display = false;
            $this->calPostDate->Display = false;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = false;
            $this->txtAuthor->Display = false;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        } else {
            if ($objAthlete->getPostDate() &&
                !$objAthlete->getPostUpdateDate() &&
                $objAthlete->getAuthor() &&
                !$objAthlete->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($objAthlete->getPostDate() &&
                $objAthlete->getPostUpdateDate() &&
                $objAthlete->getAuthor() &&
                !$objAthlete->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($objAthlete->getPostDate() &&
                $objAthlete->getPostUpdateDate() &&
                $objAthlete->getAuthor() &&
                $objAthlete->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = true;
                $this->txtUsersAsEditors->Display = true;
            }

            $this->calPostDate->Text = $objAthlete->PostDate ? $objAthlete->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->Text = $objAthlete->PostUpdateDate ? $objAthlete->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->txtAuthor->Text = $objAthlete->Author;
            $this->txtUsersAsEditors->Text = implode(', ', $objAthlete->getUserAsEditorsArray());
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
}