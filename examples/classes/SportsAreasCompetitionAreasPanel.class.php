<?php

use QCubed as Q;
use QCubed\Action\Terminate;
use QCubed\Bootstrap as Bs;
use QCubed\Event\Change;
use QCubed\Event\EnterKey;
use QCubed\Event\Input;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Query\Clause\ClauseInterface as QQClause;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class SportsAreasCompetitionAreasPanel extends Q\Control\Panel
{
    protected $objSportsAreasCondition;
    protected $objSportsAreasClauses;
    protected $objSportsCompetitionAreasCondition;
    protected $objSportsCompetitionAreasClauses;
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
    public $dtgAreas;
    public $lblInfo;
    public $lblWarning;

    public $btnRefresh;
    public $btnAddAreas;

    public $lstSportsAreas;
    public $lstSportsCompetitionAreas;

    public $btnSaveNew;
    public $btnCancelNew;

    public $txtSportsArea;
    public $txtCompetitionArea;
    public $btnDelete;
    public $btnCancel;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $selectedSportId;

    protected $strTemplate = 'SportsAreasCompetitionAreasPanel.tpl.php';

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

        $this->intLoggedUserId= 2;
        $this->objUser = User::load($this->intLoggedUserId);

        $this->createTable();
        $this->dtgAreas_makeEditable();
        $this->createPaginators();
        $this->createItemsPerPage();
        $this->createFilter();

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a data table for displaying sports areas and competition areas.
     * The table includes columns for sport area, competition area, post date, and post update date,
     * and features such as AJAX support and sortable columns.
     *
     * @return void
     */
    protected function createTable()
    {
        $this->dtgAreas = new Q\Plugin\VauuTable($this);
        $this->dtgAreas->CssClass = "table vauu-table table-hover table-responsive";
        $this->dtgAreas->addCssClass('clickable-rows');
        $this->dtgAreas->Display = false;

        $col = $this->dtgAreas->createNodeColumn(t("Sport area"), QQN::SportsAreasCompetitionAreas()->SportsAreas);
        $col->CellStyler->Width = '30%';

        $col = $this->dtgAreas->createNodeColumn(t("Competition area"), QQN::SportsAreasCompetitionAreas()->SportsCompetitionAreas);
        $col->CellStyler->Width = '30%';

        $col = $this->dtgAreas->createNodeColumn(t("Post date"), QQN::SportsAreasCompetitionAreas()->PostDate);
        $col->Format = 'DD.MM.YYYY hhhh:mm';
        $col->CellStyler->Width = '15%';

        $col = $this->dtgAreas->createNodeColumn(t("Post update date"), QQN::SportsAreasCompetitionAreas()->PostUpdateDate);
        $col->Format = 'DD.MM.YYYY hhhh:mm';
        $col->CellStyler->Width = '15%';

        $col = $this->dtgAreas->createNodeColumn(t("Is Locked"), QQN::SportsAreasCompetitionAreas()->IsLockedObject);
        $col->HtmlEntities = false;
        $col->CellStyler->Width = '10%';

        $this->dtgAreas->UseAjax = true;
        $this->dtgAreas->SortColumnIndex = 1;
        $this->dtgAreas->SortDirection = 1;
        $this->dtgAreas->setDataBinder('dtgAreas_Bind', $this);
        $this->dtgAreas->RowParamsCallback = [$this, 'dtgAreas_GetRowParams'];
    }

    /**
     * Makes the DataGrid `dtgAreas` editable by adding a cell click event that triggers an AJAX action.
     *
     * The method adds a CellClick event to the DataGrid, which retrieves the row data value of the clicked cell and
     * triggers an associated AJAXControl action method (`dtgAreas_Click`) to handle the event.
     *
     * @return void
     */
    protected function dtgAreas_makeEditable()
    {
        $this->dtgAreas->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')),
            new Q\Action\AjaxControl($this, 'dtgAreas_Click'));
    }

    /**
     * Handles the click event for the areas data grid, loading relevant data and updating the display.
     *
     * @param ActionParams $params Contains parameters related to the action, such as the action parameter used to identify the selected area.
     * @return void This method does not return a value; it updates the UI and internal properties based on the selected area.
     */
    protected function dtgAreas_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objAreas = SportsAreasCompetitionAreas::load($this->intId);

        $this->txtSportsArea->Text = $objAreas->getSportsAreasName();
        $this->txtCompetitionArea->Text = $objAreas->getSportsCompetitionAreasName();

        $this->dtgAreas->addCssClass('disabled');
        $this->txtSportsArea->Display = true;
        $this->txtCompetitionArea->Display = true;
        $this->btnCancel->Display = true;

        if ($objAreas->IsLocked == 1) {
            $this->btnDelete->Display = true;
        } else {
            $this->btnDelete->Display = false;
        }
    }

    /**
     * Generates and returns an array of parameters for a row in the Areas data grid.
     *
     * @param object $objRowObject The object representing the current row's data, typically an entity or model instance.
     * @param int $intRowIndex The zero-based index of the current row in the data grid.
     * @return array An associative array of parameters for the row, where keys represent parameter names and values represent their corresponding values.
     */
    public function dtgAreas_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures the paginators for the given data grid.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgAreas->Paginator = new Bs\Paginator($this);
        $this->dtgAreas->Paginator->LabelForPrevious = t('Previous');
        $this->dtgAreas->Paginator->LabelForNext = t('Next');
        $this->dtgAreas->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
        $this->dtgAreas->Paginator->Display = false;
    }

    /**
     * Initializes and configures the Select2 control for selecting the ItemsPerPage by the assigned user.
     * This method sets various properties for the Select2 dropdown, including themes, width, selection mode,
     * and assigns the selected value and available items. Additionally, an AJAX control action is linked
     * to handle changes in the selection.
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
        $this->lstItemsPerPageByAssignedUserObject->Display = false;
    }

    /**
     * Retrieves a list of items per page associated with the assigned user object, formatted as ListItem objects.
     *
     * @return ListItem[] An array of ListItem objects representing items per page assigned to the user.
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
     * Updates the items per page value for the associated user object and refreshes the data grid.
     *
     * @param ActionParams $params The parameters associated with the triggered action.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgAreas->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgAreas->refresh();
    }

    /**
     * Creates and configures a search filter textbox for competition area input.
     *
     * @return void
     */
    public function createFilter()
    {
        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search competition area...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();
        $this->txtFilter->Display = false;
    }

    /**
     * Adds filter actions to the filter text box. This method assigns input and enter key events to trigger
     * AJAX control actions for the filter behavior and ensures proper termination of the enter key action.
     *
     * @return void
     */
    public function addFilterActions()
    {
        $this->txtFilter->addAction(new Q\Event\Input(300), new Q\Action\AjaxControl($this, 'filterChanged'));

        $this->txtFilter->addActionArray(
            new Q\Event\EnterKey(),
            [
                new Q\Action\AjaxControl($this, 'filterChanged'),
                new Q\Action\Terminate()
            ]
        );
    }

    /**
     * Refreshes the data grid when the filter is changed.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgAreas->refresh();
    }

    /**
     * Binds data to the dtgAreas control based on the specified filtering criteria and sorting settings.
     *
     * Applies conditions for filtering the data source based on user inputs such as search text and selected sport ID.
     * It also considers any sorting or limiting clauses defined for the dtgAreas control before retrieving data.
     *
     * @return void This method does not return a value. It sets the TotalItemCount and DataSource properties of the dtgAreas control.
     */
    public function dtgAreas_Bind()
    {
        $strSearchValue = trim((string) $this->txtFilter->Text);
        $selectedSportId = (int) $this->lstSportsAreas->SelectedValue;

        if (empty($strSearchValue) && empty($selectedSportId)) {
            $objCondition = QQ::all();
        } else {
            $objCondition = QQ::all();

            if (!empty($strSearchValue)) {
                $objCondition = QQ::andCondition(
                    $objCondition,
                    QQ::orCondition(
                        QQ::like(QQN::SportsAreasCompetitionAreas()->SportsCompetitionAreas->Name, "%" . $strSearchValue . "%")
                    )
                );
            }

            if (!empty($selectedSportId)) {
                $objCondition = QQ::andCondition(
                    $objCondition,
                    QQ::equal(QQN::SportsAreasCompetitionAreas()->SportsAreas->Id, $selectedSportId)
                );
            }
        }

        $clauses = [];
        if ($this->dtgAreas->OrderByClause) {
            $clauses[] = $this->dtgAreas->OrderByClause;
        }
        if ($this->dtgAreas->LimitClause) {
            $clauses[] = $this->dtgAreas->LimitClause;
        }

        $this->dtgAreas->TotalItemCount = SportsAreasCompetitionAreas::queryCount($objCondition);
        $this->dtgAreas->DataSource = SportsAreasCompetitionAreas::queryArray($objCondition, $clauses);
    }

    /**
     * Retrieves a list of sports areas as ListItem objects, applying specific conditions
     * and clauses to the query. Disabled options are marked for conditional styling.
     *
     * @return ListItem[] An array of ListItem objects representing sports areas, with some items
     * potentially marked as disabled based on their properties.
     */
    public function lstSportsAreas_GetItems() {
        $a = array();
        $objCondition = $this->objSportsCompetitionAreasCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objSportsAreasCursor = SportsAreas::queryCursor($objCondition, $this->objSportsAreasClauses);

        // Iterate through the Cursor
        while ($objSportsAreas = SportsAreas::instantiateCursor($objSportsAreasCursor)) {
            $objListItem = new ListItem($objSportsAreas->__toString(), $objSportsAreas->Id);

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objSportsAreas->IsEnabled == 2) {
                $objListItem->Disabled = true;
            }

            $a[] = $objListItem;
        }
        return $a;
    }

    /**
     * Retrieves a list of sports competition areas as ListItem objects.
     * The method iterates through the cursor of sports competition areas
     * and creates ListItem objects for each area. Certain competition areas
     * are marked as disabled based on their "IsLocked" or "IsEnabled" properties.
     *
     * @return array An array of ListItem objects representing sports competition areas.
     *               Disabled items are excluded from selection based on specific conditions.
     */
    public function lstSportsCompetitionAreas_GetItems() {
        $a = array();
        $objCondition = $this->objSportsCompetitionAreasCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objSportsCompetitionAreasCursor = SportsCompetitionAreas::queryCursor($objCondition, $this->objSportsCompetitionAreasClauses);

        // Iterate through the Cursor
        while ($objSportsCompetitionAreas = SportsCompetitionAreas::instantiateCursor($objSportsCompetitionAreasCursor)) {
            $objListItem = new ListItem($objSportsCompetitionAreas->__toString(), $objSportsCompetitionAreas->Id);

            // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
            // {display: none;} </style>
            // A little trick on how to hide some options. Just set the option to "disabled" and
            // use only on a specific page. You just have to use the style.

            if ($objSportsCompetitionAreas->IsLocked == 1 || $objSportsCompetitionAreas->IsEnabled == 2) {
                $objListItem->Disabled = true;
            }

            $a[] = $objListItem;
        }

        return $a;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures input elements for managing sports areas and competition areas.
     *
     * @return void
     */
    public function createInputs()
    {
        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please select a sports area!');

        $this->lblWarning = new Q\Plugin\Control\Alert($this);
        $this->lblWarning->Dismissable = true;
        $this->lblWarning->addCssClass('alert alert-warning alert-dismissible');
        $this->lblWarning->Text = t('<p>The selected sports area has no linked competition areas.
                                    Please select a competition area from the dropdown and save.</p> 
                                    <p>If the desired competition area is missing, create a new one under the "Competition areas" tab.</p>');
        $this->lblWarning->Display = false;

        // Valitud spordialal pole seotud võistlusalasid! Palun vali võistlusalade rippmenüüst võistlusala ja salvesta.
        // Kui sobivat võistlusala ei leidu, loo uus võistlusala vahekaardil "Võistlusalad".

        $this->lstSportsAreas = new Q\Plugin\Select2($this);
        $this->lstSportsAreas->MinimumResultsForSearch = -1;
        $this->lstSportsAreas->Theme = 'web-vauu';
        $this->lstSportsAreas->Width = '100%';
        $this->lstSportsAreas->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstSportsAreas->addItem(t('- Select sport area -'), null, true);
        $this->lstSportsAreas->addItems($this->lstSportsAreas_GetItems());

        $countSportsAreas = SportsAreas::queryCount(QQ::all());

        if ($countSportsAreas === 0) {
            $this->lstSportsAreas->Enabled = false;
        } else {
            $this->lstSportsAreas->Enabled = true;
        }

        $this->lstSportsAreas->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstSportsAreas_Change'));
        $this->lstSportsAreas->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'filterChanged'));

        $this->lstSportsCompetitionAreas = new Q\Plugin\Select2($this);
        $this->lstSportsCompetitionAreas->MinimumResultsForSearch = -1;
        $this->lstSportsCompetitionAreas->Theme = 'web-vauu';
        $this->lstSportsCompetitionAreas->Width = '100%';
        $this->lstSportsCompetitionAreas->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstSportsCompetitionAreas->addItem(t('- Select competition area -'), null, true);
        $this->lstSportsCompetitionAreas->addItems($this->lstSportsCompetitionAreas_GetItems());

        $countCompetitionAreas = SportsCompetitionAreas::queryCount(QQ::all());

        if ($countCompetitionAreas === 0) {
            $this->lstSportsCompetitionAreas->Enabled = false;
        } else {
            $this->lstSportsCompetitionAreas->Enabled = true;
        }

        $this->lstSportsCompetitionAreas->Enabled = false;

        $this->txtSportsArea = new Bs\TextBox($this);
        $this->txtSportsArea->setCssStyle('float', 'left');
        $this->txtSportsArea->setCssStyle('margin-right', '10px');
        $this->txtSportsArea->Width = 250;
        $this->txtSportsArea->Display = false;
        $this->txtSportsArea->Enabled = false;

        $this->txtCompetitionArea = new Bs\TextBox($this);
        $this->txtCompetitionArea->setCssStyle('float', 'left');
        $this->txtCompetitionArea->setCssStyle('margin-right', '10px');
        $this->txtCompetitionArea->Width = 250;
        $this->txtCompetitionArea->Display = false;
        $this->txtCompetitionArea->Enabled = false;
    }

    /**
     * Initializes and creates various button elements used in the interface.
     *
     * The function constructs multiple buttons with specific properties such as CSS classes,
     * tooltips, glyph icons, validation behaviors, and click actions. These buttons are designed
     * to provide functionalities like refreshing data, adding new entries, saving changes,
     * canceling operations, and deleting items. Each button's style and behavior are customized
     * to align with the application's requirements.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnRefresh = new Q\Plugin\Button($this);
        $this->btnRefresh->Tip = true;
        $this->btnRefresh->ToolTip = t('Refresh tables');
        $this->btnRefresh->Glyph = 'fa fa-refresh';
        $this->btnRefresh->CssClass = 'btn btn-darkblue';
        $this->btnRefresh->CausesValidation = false;
        $this->btnRefresh->setCssStyle('float', 'left');
        $this->btnRefresh->setCssStyle('margin-left', '15px');
        $this->btnRefresh->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnRefresh_Click'));

        $this->btnAddAreas = new Bs\Button($this);
        $this->btnAddAreas->Text = t(' Add sport area');
        $this->btnAddAreas->Glyph = 'fa fa-plus';
        $this->btnAddAreas->CssClass = 'btn btn-orange';
        $this->btnAddAreas->addWrapperCssClass('center-button');
        $this->btnAddAreas->CausesValidation = false;
        $this->btnAddAreas->setCssStyle('float', 'left');
        $this->btnAddAreas->setCssStyle('margin-left', '15px');
        $this->btnAddAreas->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddAreas_Click'));

        $this->btnSaveNew = new Bs\Button($this);
        $this->btnSaveNew->Text = t('Save');
        $this->btnSaveNew->CssClass = 'btn btn-orange';
        $this->btnSaveNew->addWrapperCssClass('center-button');
        $this->btnSaveNew->PrimaryButton = true;
        $this->btnSaveNew->CausesValidation = true;
        $this->btnSaveNew->setCssStyle('float', 'left');
        $this->btnSaveNew->setCssStyle('margin-right', '10px');
        $this->btnSaveNew->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSaveNew_Click'));

        $this->btnCancelNew = new Bs\Button($this);
        $this->btnCancelNew->Text = t('Cancel');
        $this->btnCancelNew->addWrapperCssClass('center-button');
        $this->btnCancelNew->CssClass = 'btn btn-default';
        $this->btnCancelNew->CausesValidation = false;
        $this->btnCancelNew->setCssStyle('float', 'left');
        $this->btnCancelNew->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancelNew_Click'));

        $this->btnDelete = new Bs\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->addWrapperCssClass('center-button');
        $this->btnDelete->CausesValidation = true;
        $this->btnDelete->setCssStyle('float', 'left');
        $this->btnDelete->setCssStyle('margin-right', '10px');
        $this->btnDelete->Display = false;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnDelete_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->setCssStyle('float', 'left');
        $this->btnCancel->Display = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
    }

    /**
     * Creates and initializes multiple Toastr dialog instances with predefined configurations.
     *
     * This method sets up various Toastr dialogs with different alert types, positions,
     * messages, and progress bar options to notify users of specific application states
     * or actions.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The selected sports area and the selected competition area have been successfully linked.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('The selected sports area and competition area are already linked in the database.
                                        <p>Please choose a different combination to link!</p>');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Well done!</strong> The sports areas and competition areas tables have been successfully updated!');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('Please choose a competition area!');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('The selected sport and the selected competition have been successfully deleted.');
        $this->dlgToastr5->ProgressBar = true;
    }

    /**
     * Creates and initializes modal dialog instances with predefined text,
     * titles, header styles, and buttons for typical user interactions such as warnings,
     * confirmations, and informational tips. These modals are intended to guide users
     * through various actions and to confirm irreversible operations.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the sport area?</p>
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
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The sports area cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this sports area, you must first unlink it from any previously 
                                    created calendar events or competition areas associated with it.</p>');

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The sports area cannot be disabled at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To disable this sports area, you must first unlink it from all previously 
                                    created calendar events or competition areas associated with it.</p>');

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The name of this sport already exists in the database, please choose another name!</p>');
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
     * Handles the click event for the refresh button.
     *
     * Clears and repopulates the items for the Sports Areas and Sports Competition Areas dropdown lists.
     * This method removes all existing items from both lists, adds a default selection item to each,
     * and fetches new items using the corresponding retrieval methods.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     *
     * @return void This method does not return any value.
     */
    protected function btnRefresh_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->hideDropdowns();
        $this->hideTableControls();
        $this->dtgAreas->refresh();

        $this->dlgToastr3->notify();
    }

    /**
     * Handles the click event for the "Add Areas" button, enabling UI elements and executing client-side JavaScript.
     *
     * @param ActionParams $params Parameters for the action triggered by the click event.
     * @return void
     */
    protected function btnAddAreas_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->btnAddAreas->Enabled = false;
        $this->dtgAreas->removeCssClass('disabled');

        Application::executeJavaScript("
            $('.js-mapping-activities').removeClass('hidden');
        ");
    }

    /**
     * Handles the change event for the sports areas dropdown. Updates the related UI controls based on the selected value.
     *
     * @param ActionParams $params Parameters associated with the event action.
     * @return void
     */
    protected function lstSportsAreas_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->txtFilter->Text = null; // Always have to empty it, no matter what happens...

        if ($this->lstSportsAreas->SelectedValue !== null) {
            $objSportsAreas = SportsAreas::loadById($this->lstSportsAreas->SelectedValue);

            $this->lstSportsCompetitionAreas->Enabled = true;

            if ($objSportsAreas->getIsLocked() == 1) {
                $this->lblInfo->Display = false; // Please select a sports area!
                $this->lblWarning->Display = false; // The selected sport is not related to any competition!

                $this->showTableControls();
            } else {
                $this->lblInfo->Display = false; // Please select a sports area!
                $this->lblWarning->Display = true; // The selected sport is not related to any competition!

                $this->hideTableControls();
            }
        } else {
            $this->lstSportsCompetitionAreas->Enabled = false;

            $this->lblInfo->Display = true; // Please select a sports area!
            $this->lblWarning->Display = false; // The selected sport is not related to any competition!

            $this->hideTableControls();
        }
    }

    /**
     * Handles the save action for a new pairing between sports areas and competition areas.
     *
     * This method validates the user input, checks if the selected pairing already exists,
     * creates a new SportsAreasCompetitionAreas record if not, and performs any necessary
     * updates to the related entities. It also manages UI updates and notifications to
     * provide user feedback.
     *
     * @param ActionParams $params Parameters associated with the action that triggered the button click.
     * @return void
     */
    protected function btnSaveNew_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->lstSportsCompetitionAreas->SelectedValue === null) {
            $this->dlgToastr4->notify();
            return;
        }

        if (SportsAreasCompetitionAreas::pairExists($this->lstSportsAreas->SelectedValue, $this->lstSportsCompetitionAreas->SelectedValue)) {
            $this->dlgToastr2->notify();
        } else {
            $objSportsAreasCompetitionAreas = new SportsAreasCompetitionAreas();
            $objSportsAreasCompetitionAreas->setSportsAreasId($this->lstSportsAreas->SelectedValue);
            $objSportsAreasCompetitionAreas->setSportsAreasName($this->lstSportsAreas->SelectedName);
            $objSportsAreasCompetitionAreas->setSportsCompetitionAreasId($this->lstSportsCompetitionAreas->SelectedValue);
            $objSportsAreasCompetitionAreas->setSportsCompetitionAreasName($this->lstSportsCompetitionAreas->SelectedName);
            $objSportsAreasCompetitionAreas->setPostDate(Q\QDateTime::Now());
            $objSportsAreasCompetitionAreas->save();

            $objSportsAreas = SportsAreas::loadById($this->lstSportsAreas->SelectedValue);

            if ($objSportsAreas->getIsLocked() !== 1) {
                $objSportsAreas->setIsLocked(1);
                $objSportsAreas->save();
            }

            $objCompetitionAreas = SportsCompetitionAreas::loadById($this->lstSportsCompetitionAreas->SelectedValue);
            $objCompetitionAreas->setIsLocked(1);
            $objCompetitionAreas->save();

            $objSelectedAreas = SportsAreasCompetitionAreas::loadById($objSportsAreasCompetitionAreas->Id);
            $countLockedSportsAreas = SportsAreasCompetitionAreas::countBySportsAreasId($objSelectedAreas->SportsAreasId);

            if ($countLockedSportsAreas > 0) {
                $this->lblWarning->Display = false;
            } else {
                $this->lblWarning->Display = true;
            }

            $this->hideSportsCompetitionAreasDropDown();

            $this->dlgToastr1->notify();
            $this->dtgAreas->refresh();
            $this->showTableControls();
        }
    }

    /**
     * Handles the click event for the cancel button in the "New" context.
     * Resets UI components and hides specific elements related to area mapping actions.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     * @return void
     */
    protected function btnCancelNew_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->btnAddAreas->Enabled = true;
        $this->lblInfo->Display = true;

        $this->hideDropdowns();
        $this->dtgAreas->removeCssClass('disabled');
        $this->hideTableControls();

        Application::executeJavaScript("
            $('.js-mapping-activities').addClass('hidden');
        ");
    }

    /**
     * Handles the delete button click event, performing actions based on the state of related records.
     *
     * @param ActionParams $params Parameters passed during the action event.
     * @return void
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objSelectedAreas = SportsAreasCompetitionAreas::loadById($this->intId);

        $countLockedSportsCalendar = SportsCalendar::countBySportsAreasId($objSelectedAreas->SportsAreasId);
        $countLockedSportsAreas = SportsAreasCompetitionAreas::countBySportsAreasId($objSelectedAreas->SportsAreasId);

//        if ($countLockedSportsCalendar > 0 || $countLockedSportsAreas > 0) {
//            $this->dlgModal2->showDialogBox();
//            $this->hideAreasButtons();
//        } else {
            $this->dlgModal1->showDialogBox();
        //}
    }

    /**
     * Handles the click event for deleting a selected item associated with sports areas and competition areas.
     *
     * @param ActionParams $params The parameters associated with the click action, including the action's context and data.
     *
     * @return void This method does not return any value.
     */
    public function deleteItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->dlgModal1->hideDialogBox();

        if ($params->ActionParameter == "pass") {

            $objSelectedAreas = SportsAreasCompetitionAreas::loadById($this->intId);
            $countLockedSportsCalendar = SportsCalendar::countBySportsAreasId($objSelectedAreas->SportsAreasId);
            $countLockedSportsAreas = SportsAreasCompetitionAreas::countBySportsAreasId($objSelectedAreas->SportsAreasId);

            //Application::displayAlert('COUNT: ' . $countLockedSportsAreas);

            $objSportsAreas = SportsAreas::loadById($objSelectedAreas->SportsAreasId);

            if ($countLockedSportsCalendar === 0) {
                $objSportsAreas->setIsLocked(0);
                $objSportsAreas->save();
            }

            $objCompetitionAreas = SportsCompetitionAreas::loadById($objSelectedAreas->SportsCompetitionAreasId);
            $objCompetitionAreas->setIsLocked(0);
            $objCompetitionAreas->save();

            $objSelectedAreas->delete();

            $this->dlgToastr5->notify();

            if ($countLockedSportsAreas === 1) {
                $this->hideTableControls();
                $this->lblWarning->Display = true;
            } else {
                $this->showTableControls();
                $this->lblWarning->Display = false;
            }

            $this->hideAreasButtons();
            $this->btnAddAreas->Enabled = true;
        }
    }

    /**
     * Handles the click event to hide a specific item by executing the appropriate actions.
     *
     * @param ActionParams $params Parameters related to the click action event.
     * @return void
     */
    protected function hideItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->hideAreasButtons();
    }

    /**
     * Handles the click event for the cancel button, performing necessary actions to handle cancellation.
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

        $this->hideAreasButtons();
    }

    /**
     * Resets and populates dropdown lists with default selections and updated items for sports areas
     * and competition areas.
     *
     * @return void
     */
    public function hideDropdowns()
    {
        $this->lstSportsAreas->RemoveAllItems();
        $this->lstSportsAreas->addItem(t('- Select sport area -'), null, true);
        $this->lstSportsAreas->addItems($this->lstSportsAreas_GetItems());

        $this->lstSportsCompetitionAreas->RemoveAllItems();
        $this->lstSportsCompetitionAreas->addItem(t('- Select competition area -'), null, true);
        $this->lstSportsCompetitionAreas->addItems($this->lstSportsCompetitionAreas_GetItems());

    }

    /**
     * Hides the sports competition areas dropdown by clearing its items and adding default selection options.
     *
     * @return void
     */
    public function hideSportsCompetitionAreasDropDown()
    {
        $this->lstSportsCompetitionAreas->RemoveAllItems();
        $this->lstSportsCompetitionAreas->addItem(t('- Select competition area -'), null, true);
        $this->lstSportsCompetitionAreas->addItems($this->lstSportsCompetitionAreas_GetItems());
    }

    /**
     * Hides the display controls for table and its associated elements, including pagination and filtering.
     *
     * @return void
     */
    public function hideTableControls()
    {
        $this->dtgAreas->Display = false;
        $this->lstItemsPerPageByAssignedUserObject->Display = false;
        $this->txtFilter->Display = false;
        $this->dtgAreas->Paginator->Display = false;
    }

    /**
     * Displays the table controls by enabling the visibility of specific components, such as the data grid,
     * paginator, filter textbox, and items-per-page dropdown associated with the assigned user object.
     *
     * @return void
     */
    public function showTableControls()
    {
        $this->dtgAreas->Display = true;
        $this->lstItemsPerPageByAssignedUserObject->Display = true;
        $this->txtFilter->Display = true;
        $this->dtgAreas->Paginator->Display = true;
    }

    /**
     * Hides sports and competition area textfields and buttons, resets their values,
     * and updates the appearance of related UI components.
     *
     * @return void
     */
    public function hideAreasButtons()
    {
        $this->txtSportsArea->Display = false;
        $this->txtCompetitionArea->Display = false;
        $this->txtSportsArea->Text = '';
        $this->txtCompetitionArea->Text = '';

        $this->dtgAreas->removeCssClass('disabled');
        $this->dtgAreas->refresh();
        $this->txtSportsArea->Display = false;
        $this->txtCompetitionArea->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
    }
}