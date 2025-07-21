<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class CompetitionAreasPanel extends Q\Control\Panel
{
    protected $objUnitCondition;
    protected $objUnitClauses;
    protected $objCompetitionAreas;

    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    protected $dlgToastr1;
    protected $dlgToastr2;
    protected $dlgToastr3;
    protected $dlgToastr4;
    protected $dlgToastr5;

    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;

    public $txtFilter;
    public $dtgCompetitionAreas;

    public $btnAddCompetitionArea;

    public $lblCompetitionArea;
    public $txtCompetitionArea;
    public $lblUnits;
    public $lstUnits;
    public $lblIsDetailedResult;
    public $lstIsDetailedResult;
    public $lblIsEnabled;
    public $lstIsEnabled;

    public $btnSave;
    public $btnDelete;
    public $btnCancel;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $intClick;
    protected $blnEditMode = true;
    protected $errors = []; // Array for tracking errors

    protected $strTemplate = 'SportsCompetitionAreasPanel.tpl.php';

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

        $this->intLoggedUserId= 1;
        $this->objUser = User::load($this->intLoggedUserId);

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgCompetitionAreas_Create();
        $this->dtgCompetitionAreas->setDataBinder('bindData', $this);

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the SportAreasTable component.
     *
     * @return void
     */
    protected function dtgCompetitionAreas_Create()
    {
        $this->dtgCompetitionAreas = new CompetitionAreasTable($this);
        $this->dtgCompetitionAreas_CreateColumns();
        $this->createPaginators();
        $this->dtgCompetitionAreas_MakeEditable();
        $this->dtgCompetitionAreas->RowParamsCallback = [$this, "dtgCompetitionAreas_GetRowParams"];
        $this->dtgCompetitionAreas->SortColumnIndex = 0;
        $this->dtgCompetitionAreas->SortDirection = -1;
        $this->dtgCompetitionAreas->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Creates columns for the sports areas data grid.
     *
     * @return void
     */
    protected function dtgCompetitionAreas_CreateColumns()
    {
        $this->dtgCompetitionAreas->createColumns();
    }

    /**
     * Configures the Sports Areas datagrid to be editable by adding actions to it.
     * It sets up a cell click event on the datagrid that triggers an AJAX control
     * action. It also adds CSS classes to make the rows clickable and gives the
     * datagrid a specific styling with additional CSS classes.
     *
     * @return void
     */
    protected function dtgCompetitionAreas_MakeEditable()
    {
        $this->dtgCompetitionAreas->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgCompetitionAreas_CellClick'));
        $this->dtgCompetitionAreas->CssClass = 'table vauu-table js-competition-area table-hover table-responsive';
    }

    /**
     * Handles the cell click event in the competition areas data grid.
     *
     * This method is triggered when a cell in the competition areas data grid is clicked.
     * It loads the associated competition area based on the clicked item's ID, updates the state
     * of various UI elements (e.g., enabling/disabling controls, toggling visibility of buttons),
     * and executes JavaScript for smooth scrolling and UI adjustments. The method also performs
     * additional checks on inputs related to the selected competition area.
     *
     * @param ActionParams $params The parameters associated with the cell click action,
     *                              including the ID of the clicked item.
     * @return void
     */
    protected function dtgCompetitionAreas_CellClick(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objCompetitionAreas = SportsCompetitionAreas::load($this->intId);
        $this->intClick = $this->intId;

        if ($objCompetitionAreas->getIsLocked() === 1) {
            $this->btnDelete->Display = false;
        } else {
            $this->btnDelete->Display = true;
        }

        $this->blnEditMode = true;

        $this->btnAddCompetitionArea->Enabled = false;
        $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
        $this->txtFilter->Enabled = false;
        $this->dtgCompetitionAreas->Paginator->Enabled = false;
        $this->dtgCompetitionAreas->addCssClass('disabled');

        Application::executeJavaScript("
            $('.setting-wrapper').removeClass('hidden');
            $('.form-actions').removeClass('hidden');
  
            var wrapperTop = document.querySelector('.tabbable-custom'); // tabbable-custom
            
            if (wrapperTop) {
                var offsetTop = wrapperTop.getBoundingClientRect().top + window.scrollY; // Absolute position
                var windowHeight = window.innerHeight; // Browser window height
                var scrollPosition = offsetTop - (windowHeight / 2); // Determining the center of an element
        
                window.scrollTo({
                    top: scrollPosition,
                    behavior: 'smooth' // Smooth scrolling
                });
            }
        ");

        $this->activeInputs($objCompetitionAreas);
        $this->checkInputs();
    }

    /**
     * Retrieves the parameters for a row in the sports areas data table.
     *
     * @param object $objRowObject The object representing the row for which parameters are being set.
     * @param int $intRowIndex The index of the row in the data table.
     * @return array The array of parameters with keys as parameter names and values as parameter values for the row.
     */
    public function dtgCompetitionAreas_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $intLocked = $objRowObject->getIsLocked();

        if ($intLocked == 1) {
            $params['class'] = 'locked';
        }

        $params['data-value'] = $strKey;

        return $params;
    }

    /**
     * Initializes and configures the pagination for the data grid, setting the paginator labels
     * and configuring data grid properties such as items per page, sorting, and AJAX usage.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgCompetitionAreas->Paginator = new Bs\Paginator($this);
        $this->dtgCompetitionAreas->Paginator->LabelForPrevious = t('Previous');
        $this->dtgCompetitionAreas->Paginator->LabelForNext = t('Next');

        //$this->dtgCompetitionAreas->PaginatorAlternate = new Bs\Paginator($this);
        //$this->dtgCompetitionAreas->PaginatorAlternate->LabelForPrevious = t('Previous');
        //$this->dtgCompetitionAreas->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgCompetitionAreas->ItemsPerPage = 10;
        $this->dtgCompetitionAreas->SortColumnIndex = 0;
        $this->dtgCompetitionAreas->UseAjax = true;
        $this->addFilterActions();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a Select2 dropdown control for selecting the number of items per page.
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
     * Retrieves a list of items per page assigned to a user object.
     *
     * Queries the ItemsPerPage using the given condition and clauses, and creates
     * a list of ListItem objects based on the result. The list includes an
     * indication if the item is selected based on the user's assigned object.
     *
     * @return ListItem[] An array of ListItem objects representing the items per page assigned
     *                    to the user object, with the selected item identified if applicable.
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
     * Updates the number of items displayed per page for the data grid and refreshes it based on the selected user object.
     *
     * @param ActionParams $params The parameters related to the action, which may include details about the specific user object selection change.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgCompetitionAreas->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgCompetitionAreas->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the search filter by creating a text box with specific attributes and styles.
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
     * Adds filter actions to the txtFilter control.
     *
     * This method assigns two types of actions to the txtFilter control:
     * 1. An input event that triggers an Ajax control action named 'filterChanged' with a delay of 300 milliseconds.
     * 2. An enter key event that also triggers the 'filterChanged' Ajax control action in addition to a terminate action.
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
     * Refreshes the sports areas data grid when the filter is changed.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgCompetitionAreas->refresh();
    }

    /**
     * Binds data to the data grid based on a specific condition.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgCompetitionAreas->bindData($objCondition);
    }

    /**
     * Constructs and returns a query condition based on the filter text value.
     *
     * @return Q\Query\QQ The query condition, either fetching all records or filtering
     *                    based on the search text present in the filter field. If no
     *                    filter text is provided, it returns a condition to fetch all
     *                    records. Otherwise, it constructs a condition to match records
     *                    where the name includes the filter text or the 'IsEnabled' field
     *                    equals the filter text.
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
                Q\Query\QQ::like(QQN::SportsCompetitionAreas()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::SportsCompetitionAreas()->Unit->Name, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and configures various input controls for the form.
     *
     * Initializes labels, text boxes, selection boxes, and radio buttons
     * to be used in the form. Each control is styled, configured with appropriate
     * behaviors (such as adding CSS classes or setting event actions),
     * and linked to specific actions for user interaction.
     *
     * @return void This method does not return a value.
     */
    public function createInputs()
    {
        $this->lblCompetitionArea = new Q\Plugin\Control\Label($this);
        $this->lblCompetitionArea->Text = t('Competition area');
        $this->lblCompetitionArea->addCssClass('col-md-4');
        $this->lblCompetitionArea->setCssStyle('font-weight', 'normal');
        $this->lblCompetitionArea->Required = true;

        $this->txtCompetitionArea = new Bs\TextBox($this);
        $this->txtCompetitionArea->Placeholder = t('Competition area');
        $this->txtCompetitionArea->ActionParameter = $this->txtCompetitionArea->ControlId;
        $this->txtCompetitionArea->setHtmlAttribute('autocomplete', 'off');
        $this->txtCompetitionArea->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtCompetitionArea->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtCompetitionArea->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
        $this->txtCompetitionArea->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblUnits = new Q\Plugin\Control\Label($this);
        $this->lblUnits->Text = t('Units of measurement');
        $this->lblUnits->addCssClass('col-md-4');
        $this->lblUnits->setCssStyle('font-weight', 'normal');
        $this->lblUnits->Required = true;

        $this->lstUnits = new Q\Plugin\Control\Select2($this);
        $this->lstUnits->MinimumResultsForSearch = -1;
        $this->lstUnits->ContainerWidth = 'resolve';
        $this->lstUnits->Theme = 'web-vauu';
        $this->lstUnits->Width = '100%';
        $this->lstUnits->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstUnits->addItem(t('- Select Unit of measurement -'));
        $this->lstUnits->addItems($this->lstUnit_GetItems());
        $this->lstUnits->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstUnits_Change'));

        $this->lblIsDetailedResult = new Q\Plugin\Control\Label($this);
        $this->lblIsDetailedResult->Text = t('Is detailed result');
        $this->lblIsDetailedResult->addCssClass('col-md-4');
        $this->lblIsDetailedResult->setCssStyle('font-weight', 'normal');

        $this->lstIsDetailedResult = new Q\Plugin\Control\RadioList($this);
        $this->lstIsDetailedResult->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstIsDetailedResult->ButtonGroupClass = 'radio radio-orange radio-inline';
        $this->lstIsDetailedResult->setCssStyle('float', 'left');
        $this->lstIsDetailedResult->setCssStyle('margin-left', '10px');
        $this->lstIsDetailedResult->setCssStyle('margin-right', '10px');
        $this->lstIsDetailedResult->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstIsDetailedResult_Change'));

        $this->lblIsEnabled = new Q\Plugin\Control\Label($this);
        $this->lblIsEnabled->Text = t('Is enabled');
        $this->lblIsEnabled->addCssClass('col-md-4');
        $this->lblIsEnabled->setCssStyle('font-weight', 'normal');

        $this->lstIsEnabled = new Q\Plugin\Control\RadioList($this);
        $this->lstIsEnabled->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstIsEnabled->ButtonGroupClass = 'radio radio-orange radio-inline';
        $this->lstIsEnabled->setCssStyle('float', 'left');
        $this->lstIsEnabled->setCssStyle('margin-left', '10px');
        $this->lstIsEnabled->setCssStyle('margin-right', '10px');
        $this->lstIsEnabled->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstIsEnabled_Change'));
    }

    /**
     * Retrieves a list of sports units for selection.
     *
     * Queries the SportsUnits using the specified condition and clauses, and constructs
     * a list of ListItem objects based on the query results. If a competition area is
     * clicked and its unit matches one from the results, that item is marked as selected.
     *
     * @return ListItem[] An array of ListItem objects representing the sports units,
     *                    with the selected item identified if applicable.
     */
    public function lstUnit_GetItems()
    {
        $a = array();
        $objCondition = $this->objUnitCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objUnitCursor = SportsUnits::queryCursor($objCondition, $this->objUnitClauses);

        // Iterate through the Cursor
        while ($objUnit = SportsUnits::instantiateCursor($objUnitCursor)) {
            $objListItem = new ListItem($objUnit->__toString(), $objUnit->Id);

            if ($this->intClick) {
                $objCompetitionAreas = SportsCompetitionAreas::load($this->intClick);

                if (($objCompetitionAreas->UnitId) && ($objCompetitionAreas->UnitId == $objUnit->Id))
                    $objListItem->Selected = true;
            }

            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Creates and configures a set of buttons for the user interface.
     *
     * This method initializes buttons such as 'Add Competition Area', 'Save',
     * 'Delete', and 'Cancel', setting their properties including text, CSS classes,
     * icons (if applicable), and validation behavior. It also assigns click actions
     * to each button to handle user interaction.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnAddCompetitionArea = new Bs\Button($this);
        $this->btnAddCompetitionArea->Text = t(' Add competition area');
        $this->btnAddCompetitionArea->Glyph = 'fa fa-plus';
        $this->btnAddCompetitionArea->CssClass = 'btn btn-orange';
        $this->btnAddCompetitionArea->CausesValidation = false;
        $this->btnAddCompetitionArea->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddCompetitionArea_Click'));

        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->setCssStyle('margin-right', '10px');
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
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
    }

    /**
     * Creates and initializes multiple Toastr notification dialogs with predefined settings.
     *
     * Configures a set of Toastr dialogs, each with a specific alert type, position,
     * message, and progress bar enabled. These dialogs are used to display notifications
     * for various operations related to competition areas, such as success, error, or informational messages.
     *
     * @return void This method does not return a value.
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> To add a new competition area to the database is successful.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('Competition area is required!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('The unit of measurement for the competition area is at least mandatory!');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('<strong>Well done!</strong> The competition area has been saved or modified.');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('The update to the competition area entry was discarded, and the competition area has been restored!');
        $this->dlgToastr5->ProgressBar = true;
    }

    /**
     * Creates and initializes modal dialog components for user interactions.
     *
     * Defines multiple modal dialogs with specific texts, titles, styles, and buttons
     * for various user scenarios. Each modal is configured with actions and events
     * tailored to its respective purpose, allowing for user feedback and interaction.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the competition area?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hideItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The competition area cannot be deleted
                                    at this time!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this competition area,
                                    just must release sport areas related to previously created calendar event.</p>');

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The competition area cannot
                                    be deactivated at this time!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this competition area, you must first unlink it from the previously created sports areas.</p>');

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The name of this competition area already exists in the database, please choose another name!</p>');
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
     * Handles the action triggered when the 'Add Competition Area' button is clicked.
     *
     * Executes JavaScript to update the UI by displaying certain elements and disabling others.
     * Prepares the interface for adding a new competition area by resetting inputs,
     * clearing relevant fields, and disabling functionality for existing competition area elements.
     *
     * @param ActionParams $params The parameters associated with the action event triggered by the button click.
     *
     * @return void This method does not return any value.
     */
    protected function btnAddCompetitionArea_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').removeClass('hidden');
            $('.form-actions').removeClass('hidden');
        ");

        $this->blnEditMode = false;

        $this->btnDelete->Display = false;
        $this->btnAddCompetitionArea->Enabled = false;
        $this->dtgCompetitionAreas->addCssClass('disabled');
        $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
        $this->txtFilter->Enabled = false;
        $this->dtgCompetitionAreas->Paginator->Enabled = false;

        $this->txtCompetitionArea->Text = '';
        $this->txtCompetitionArea->focus();

        $this->resetInputs();
    }

    /**
     * Handles changes to the units list control in the form.
     *
     * This method processes user input received from a change in the units list,
     * checks the edit mode, validates the associated competition area state, and
     * performs appropriate actions depending on whether the competition area is
     * locked or unlocked. It also verifies the provided inputs and updates the
     * competition area accordingly, including notifying the user of any missing
     * input or successful updates.
     *
     * @param ActionParams $params Parameters related to the change action triggered by the user.
     * @return void
     */
    protected function lstUnits_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->blnEditMode === true) {
            $objCompetitionAreas = SportsCompetitionAreas::load($this->intClick);

            if ($objCompetitionAreas->getIsLocked() == 1) {
                $this->dlgModal3->showDialogBox();
                $this->activeInputs($objCompetitionAreas);
            } else {
                $this->checkInputs();

                if (!$this->txtCompetitionArea->Text) {
                    $this->dlgToastr2->notify();
                }

                if (!$this->lstUnits->SelectedValue) {
                    $this->dlgToastr3->notify();
                }

                if ($objCompetitionAreas->getIsLocked() == 0 && (!$this->txtCompetitionArea->Text || !$this->lstUnits->SelectedValue)) {
                    if ($this->lstUnits->SelectedValue == null) {
                        $this->lstUnits->SelectedValue = null;
                        $this->lstIsEnabled->SelectedValue = 2;
                        $this->saveInputs($objCompetitionAreas);
                        $objCompetitionAreas->setPostUpdateDate(Q\QDateTime::Now());
                    }
                }

                if ($this->txtCompetitionArea->Text && $this->lstUnits->SelectedValue) {
                    $this->saveInputs($objCompetitionAreas);
                    $objCompetitionAreas->setPostUpdateDate(Q\QDateTime::Now());
                    $this->dlgToastr4->notify();
                }

                $objCompetitionAreas->save();
            }
        }

        unset($this->errors);
    }

    /**
     * Handles the change event for the detailed result selection list.
     *
     * Updates the "IsDetailedResult" property of the selected competition area
     * object based on the new selection from the list. If in edit mode, the method
     * modifies the competition area's detailed result status, updates the post-update
     * date to the current timestamp, and saves the changes. A notification is then
     * triggered to confirm the action.
     *
     * @param ActionParams $params Parameters from the action triggering the event.
     * @return void
     */
    protected function lstIsDetailedResult_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->blnEditMode === true) {
            $objCompetitionAreas = SportsCompetitionAreas::load($this->intClick);

            $objCompetitionAreas->setIsDetailedResult($this->lstIsDetailedResult->SelectedValue);
            $objCompetitionAreas->setPostUpdateDate(Q\QDateTime::Now());
            $objCompetitionAreas->save();
            $this->dlgToastr4->notify();
        }
    }

    /**
     * Handles the change event for the "IsEnabled" list.
     *
     * This method processes changes to the "IsEnabled" list based on the current edit mode,
     * input validations, and the state of the associated competition area. It performs various
     * actions such as notifying the user of errors, updating competition area properties, and saving
     * changes to the database.
     *
     * @param ActionParams $params The parameters associated with the change action.
     *
     * @return void
     */
    protected function lstIsEnabled_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->blnEditMode === true) {
            $objCompetitionAreas = SportsCompetitionAreas::load($this->intClick);

            $this->checkInputs();

            if ($objCompetitionAreas->getIsLocked() == 1) {
                $this->dlgModal3->showDialogBox();
                $this->activeInputs($objCompetitionAreas);
            } else {

                if (!$this->txtCompetitionArea->Text) {
                    $this->dlgToastr2->notify();
                }

                if (!$this->lstUnits->SelectedValue) {
                    $this->dlgToastr3->notify();
                }

                if (!$this->txtCompetitionArea->Text || !$this->lstUnits->SelectedValue) {
                    if ($this->lstIsEnabled->SelectedValue == 1) {
                        $this->lstIsEnabled->SelectedValue = 2;
                    }
                } else {
                    $objCompetitionAreas->setIsEnabled($this->lstIsEnabled->SelectedValue);
                    $objCompetitionAreas->setPostUpdateDate(Q\QDateTime::Now());
                    $objCompetitionAreas->save();
                    $this->dlgToastr4->notify();
                }
            }

            unset($this->errors);
        }
    }

    /**
     * Handles the save action for competition areas.
     *
     * This method performs various operations depending on the edit mode and the state
     * of the competition area data. It checks inputs, validates conditions, and either creates
     * a new competition area or updates an existing one. The method also handles UI changes,
     * error notifications, and ensures the application state is updated after the action.
     *
     * @param ActionParams $params Parameters passed to the method during the action invocation.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->checkInputs();

        if ($this->intClick) {
            $objCompetitionAreas = SportsCompetitionAreas::load($this->intClick);
        }

        if ($this->blnEditMode === false) {
            if (!count($this->errors)) {

                $objCompetitionAreas = new SportsCompetitionAreas();
                $this->saveInputs($objCompetitionAreas);
                $objCompetitionAreas->setPostDate(Q\QDateTime::Now());
                $this->dlgToastr1->notify();

                Application::executeJavaScript("
                    $('.setting-wrapper').addClass('hidden');
                    $('.form-actions').addClass('hidden')
                ");

                $this->btnAddCompetitionArea->Enabled = true;
                $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
                $this->txtFilter->Enabled = true;
                $this->dtgCompetitionAreas->Paginator->Enabled = true;
                $this->dtgCompetitionAreas->removeCssClass('disabled');
                $this->dtgCompetitionAreas->refresh();

                $this->resetInputs();
            }
        } else {
            if ($objCompetitionAreas->getIsLocked() == 1) {
                if (!$this->txtCompetitionArea->Text || !$this->lstUnits->SelectedValue) {
                    $this->dlgModal3->showDialogBox();
                    $this->activeInputs($objCompetitionAreas);
                    return;
                }
            } else {
                if (!count($this->errors)) {
                    $this->saveInputs($objCompetitionAreas);
                    $this->dlgToastr4->notify();
                } else {
                    if (!$this->txtCompetitionArea->Text || !$this->lstUnits->SelectedValue) {
                        if (!$this->txtCompetitionArea->Text) {
                            $this->txtCompetitionArea->Text = null;
                            $this->dlgToastr2->notify();
                        }

                        if (!$this->lstUnits->SelectedValue) {
                            $this->lstUnits->SelectedValue = null;
                            $this->dlgToastr3->notify();
                        }

                        if (count($this->errors)) {
                            $this->lstIsEnabled->SelectedValue = 2;
                        }
                    }

                    $objCompetitionAreas->setPostUpdateDate(Q\QDateTime::Now());
                    $this->saveInputs($objCompetitionAreas);
                }
            }

            unset($this->errors);
        }

        $objCompetitionAreas->save();
    }

    /**
     * Validates input fields and updates error tracking.
     *
     * Checks required fields for missing values and manages HTML attributes
     * or CSS classes to visually indicate errors. Errors are added to the internal
     * tracking array for further processing.
     *
     * @return void
     */
    public function checkInputs()
    {
        // We check each field and add errors if necessary
        if (!$this->txtCompetitionArea->Text) {
            $this->txtCompetitionArea->setHtmlAttribute('required', 'required');
            $this->errors[] = 'txtCompetitionArea';
        } else {
            $this->txtCompetitionArea->removeHtmlAttribute('required');
        }

        if (!$this->lstUnits->SelectedValue) {
            $this->lstUnits->addCssClass('has-error');
            $this->errors[] = 'lstUnits';
        } else {
            $this->lstUnits->removeCssClass('has-error');
        }
    }

    /**
     * Resets the input fields to their default states.
     *
     * Clears the text in the competition area field, resets the selected values
     * of the units, detailed result, and enabled status to predefined defaults,
     * and refreshes their respective components to ensure the UI reflects these changes.
     *
     * @return void
     */
    public function resetInputs()
    {
        $this->txtCompetitionArea->Text = null;
        $this->lstUnits->SelectedValue = null;
        $this->lstIsDetailedResult->SelectedValue = 2;
        $this->lstIsEnabled->SelectedValue = 2;

        $this->lstUnits->refresh();
        $this->lstIsDetailedResult->refresh();
        $this->lstIsEnabled->refresh();
    }

    /**
     * Updates the active input fields based on the provided edit object.
     *
     * Sets the text and selected values of input fields to match the properties of
     * the given edit object and refreshes these fields to update their state.
     *
     * @param object $objEdit The edit object containing the data to populate the input fields.
     * @return void
     */
    public function activeInputs($objEdit)
    {
        $this->txtCompetitionArea->Text = $objEdit->getName();
        $this->lstUnits->SelectedValue = $objEdit->getUnitId();
        $this->lstIsDetailedResult->SelectedValue = $objEdit->getIsDetailedResult();
        $this->lstIsEnabled->SelectedValue = $objEdit->getIsEnabled();

        $this->txtCompetitionArea->refresh();
        $this->lstUnits->refresh();
        $this->lstIsDetailedResult->refresh();
        $this->lstIsEnabled->refresh();
    }

    /**
     * Saves the input values into the provided edit object.
     *
     * Transfers user input values (e.g., competition area name, unit ID, detailed result flag,
     * and enabled status) from the form fields to the specified edit object.
     *
     * @param object $objEdit An object where the input values will be saved. The object must
     *                        have appropriate setter methods for name, unit ID, detailed result,
     *                        and enabled status.
     * @return void
     */
    public function saveInputs($objEdit)
    {
        $objEdit->setName($this->txtCompetitionArea->Text);
        $objEdit->setUnitId($this->lstUnits->SelectedValue);
        $objEdit->setIsDetailedResult($this->lstIsDetailedResult->SelectedValue);
        $objEdit->setIsEnabled($this->lstIsEnabled->SelectedValue);
    }

    /**
     * Handles the click event for escaping an item.
     *
     * Loads competition area data based on the given click parameter, activates
     * inputs related to the loaded data, and triggers a notification.
     *
     * @param ActionParams $params The parameters associated with the action triggering the event.
     * @return void This method does not return a value.
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objCompetitionAreas = SportsCompetitionAreas::load($this->intClick);
        }

        $this->activeInputs($objCompetitionAreas);

        $this->dlgToastr5->notify();
    }

    /**
     * Handles the click event of the delete button.
     *
     * The method performs deletion-related actions for a competition area. It first determines
     * if the targeted competition area is locked. If locked, it shows a specific dialog, modifies
     * UI elements' states, resets some inputs, and refreshes the table containing the list of competition areas.
     * If not locked, it shows a different dialog.
     *
     * @param ActionParams $params The parameters associated with the delete action event.
     *
     * @return void The method does not return a value.
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objCompetitionAreas = SportsCompetitionAreas::load($this->intClick);

        if ($objCompetitionAreas->getIsLocked() == 1) {

            $this->dlgModal2->showDialogBox();

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions').addClass('hidden')
            ");

            $this->resetInputs();

            $this->btnAddCompetitionArea->Enabled = true;
            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->dtgCompetitionAreas->Paginator->Enabled = true;
            $this->dtgCompetitionAreas->removeCssClass('disabled');
            $this->dtgCompetitionAreas->refresh();
        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the click event for deleting a competition area item.
     *
     * Loads the competition area specified by the class property and, if the action parameter
     * matches a specific condition, deletes the competition area. Updates the user interface elements
     * by enabling related controls, hiding specific sections, and refreshing the data grid.
     * Additionally, hides the modal dialog box after the operation.
     *
     * @param ActionParams $params The parameters of the action, including the action parameter
     *                              that determines the behavior of the deletion.
     * @return void This method does not return any value.
     */
    public function deleteItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objCompetitionAreas = SportsCompetitionAreas::load($this->intClick);

        if ($params->ActionParameter == "pass") {
            $objCompetitionAreas->delete();

            Application::executeJavaScript("
                $('.setting-wrapper').addClass('hidden');
                $('.form-actions').addClass('hidden')
            ");

            $this->btnAddCompetitionArea->Enabled = true;
            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->txtFilter->Enabled = true;
            $this->dtgCompetitionAreas->Paginator->Enabled = true;
            $this->dtgCompetitionAreas->removeCssClass('disabled');
            $this->dtgCompetitionAreas->refresh();
        }

        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the click event to hide a specific item and reset the interface.
     *
     * Executes JavaScript commands to hide certain UI elements and resets input fields. Re-enables
     * buttons, dropdowns, and other interface elements, and refreshes a data grid after removing
     * the disabled CSS class. Closes a modal dialog box as part of the UI reset process.
     *
     * @param ActionParams $params Parameters associated with the action triggering the method.
     * @return void This method does not return any value.
     */
    protected function hideItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions').addClass('hidden')
        ");

        $this->resetInputs();

        $this->btnAddCompetitionArea->Enabled = true;
        $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
        $this->txtFilter->Enabled = true;
        $this->dtgCompetitionAreas->Paginator->Enabled = true;
        $this->dtgCompetitionAreas->removeCssClass('disabled');
        $this->dtgCompetitionAreas->refresh();

        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the click event for the cancel button, performing UI reset and enabling controls.
     *
     * This method executes JavaScript to hide specific form sections, resets form inputs,
     * and re-enables various UI components including form fields and the paginator.
     * Additionally, it clears any existing error messages associated with the form.
     *
     * @param ActionParams $params Parameters related to the action triggering the event.
     * @return void This method does not return any value.
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions').addClass('hidden')
        ");

        $this->resetInputs();

        $this->btnAddCompetitionArea->Enabled = true;
        $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
        $this->txtFilter->Enabled = true;
        $this->dtgCompetitionAreas->Paginator->Enabled = true;
        $this->dtgCompetitionAreas->removeCssClass('disabled');
        $this->dtgCompetitionAreas->refresh();

        unset($this->errors);
    }
}