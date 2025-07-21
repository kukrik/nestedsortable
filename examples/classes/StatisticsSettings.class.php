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

class StatisticsSetting extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    protected $dlgToast1;

    public $txtFilter;
    public $dtgStatisticsGroups;

    public $txtStatisticsGroup;
    public $txtStatisticsTitle;
    public $btnSave;
    public $btnCancel;
    public $btnGoToStatistics;

    protected $objUser;
    protected $intLoggedUserId;
    protected $intId;

    protected $objMenuContent;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'StatisticsSettings.tpl.php';

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

        $this->intLoggedUserId = 1;
        $this->objUser = User::load($this->intLoggedUserId);

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgStatisticsGroups_Create();
        $this->dtgStatisticsGroups->setDataBinder('BindData', $this);

        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes the StatisticsGroups data grid.
     *
     * This method sets up the data grid by instantiating it, configuring its columns,
     * applying pagination settings, enabling editing capabilities, and defining row parameters.
     * It also sets the default sort column and the number of items displayed per page
     * based on the user's preferences.
     *
     * @return void
     */
    public function dtgStatisticsGroups_Create()
    {
        $this->dtgStatisticsGroups = new StatisticsSettingsTable($this);
        $this->dtgStatisticsGroups_CreateColumns();
        $this->createPaginators();
        $this->dtgStatisticsGroups_MakeEditable();
        $this->dtgStatisticsGroups->RowParamsCallback = [$this, "dtgStatisticsGroups_GetRowParams"];
        $this->dtgStatisticsGroups->SortColumnIndex = 0;
        $this->dtgStatisticsGroups->SortDirection = -1;
        $this->dtgStatisticsGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Creates the columns for the statistics groups data grid.
     *
     * @return void
     */
    protected function dtgStatisticsGroups_CreateColumns()
    {
        $this->dtgStatisticsGroups->createColumns();
    }

    /**
     * Configures the `dtgStatisticsGroups` DataTable to be editable by adding interactivity, including cell click actions and CSS styling.
     *
     * @return void
     */
    protected function dtgStatisticsGroups_MakeEditable()
    {
        $this->dtgStatisticsGroups->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgStatisticsGroups_CellClick'));
        $this->dtgStatisticsGroups->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for the statistics groups data grid. Sets up the UI for editing
     * or viewing a specific statistics group and its associated settings based on the action parameters.
     *
     * @param ActionParams $params The action parameters that specify the context of the event,
     *                             including the action parameter representing the ID of the statistics group.
     *
     * @return void
     */
    protected function dtgStatisticsGroups_CellClick(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objStatisticsGroups = StatisticsSettings::load($this->intId);
        $objMenuContent = MenuContent::loadById($objStatisticsGroups->getMenuContentId());

        $this->txtStatisticsGroup->Enabled = false;
        $this->txtStatisticsGroup->Text = $objStatisticsGroups->getName();
        $this->txtStatisticsTitle->Text = $objStatisticsGroups->getTitle();
        $this->txtStatisticsTitle->focus();

        if (!empty($_SESSION['statistics_edit_group']) || (!empty($_SESSION['statistics']) || !empty($_SESSION['group']))) {
            $this->btnGoToStatistics->Display = true;
            $this->btnGoToStatistics->Enabled = false;
        }

        $this->dtgStatisticsGroups->addCssClass('disabled');
        $this->txtStatisticsGroup->Display = true;
        $this->txtStatisticsTitle->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Generates and returns an array of parameters for a specific row in the data table.
     *
     * @param object $objRowObject The object representing the current row in the data table.
     * @param int $intRowIndex The index position of the current row in the data table.
     * @return array An associative array of parameters, including a 'data-value' key containing the primary key of the current row.
     */
    public function dtgStatisticsGroups_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $intIsReserved = $objRowObject->getIsReserved();

        if ($intIsReserved == 2) {
            $params['class'] = 'hidden';
        }

        $params['data-value'] = $strKey;

        return $params;
    }

    /**
     * Creates and configures paginators for the statistics group data grid.
     * Sets up paginator labels for navigation, defines the number of items per page,
     * establishes the initial sorting column, and enables AJAX functionality.
     * Also triggers the addition of filter actions for the data grid.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgStatisticsGroups->Paginator = new Bs\Paginator($this);
        $this->dtgStatisticsGroups->Paginator->LabelForPrevious = t('Previous');
        $this->dtgStatisticsGroups->Paginator->LabelForNext = t('Next');

        $this->dtgStatisticsGroups->ItemsPerPage = 10;
        $this->dtgStatisticsGroups->SortColumnIndex = 0;
        $this->dtgStatisticsGroups->UseAjax = true;

        $this->addFilterActions();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes a Select2 dropdown for managing the items per page selection.
     *
     * This method configures the dropdown with specific properties such as theme, width,
     * and selection mode. It sets the initially selected value based on the current user's
     * items per page setting, populates the dropdown with options, and assigns an AJAX action
     * for handling change events.
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
     * Retrieves a list of ListItem objects representing items per page assigned to the user.
     *
     * The method queries and iterates through the ItemsPerPage objects based on the set condition,
     * creating ListItem objects for each ItemsPerPage object. If the user has an assigned
     * ItemsPerPage object, the corresponding ListItem is marked as selected.
     *
     * @return ListItem[] An array of ListItem objects representing the items per page assigned to the user.
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
     * Updates the items per page for the statistics groups data table
     * based on the selected value from the assigned user object list and refreshes the table.
     *
     * @param ActionParams $params The action parameters passed during the call, typically related to the event triggering the method.
     * @return void This method does not return any value.
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgStatisticsGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgStatisticsGroups->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a search filter text box component for user input.
     *
     * This method creates a search text box with a placeholder text indicating a search action.
     * The text box is set to search mode with autocomplete disabled, and a specific CSS class is applied for styling.
     * Additional actions are added to enhance the filter functionality.
     *
     * @return void No return value as the method sets up the filter component within the class context.
     */
    public function createFilter() {
        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();
    }

    /**
     * Adds filter actions to the filter input control.
     *
     * This method assigns actions to the filter input control to handle user interactions. It adds an
     * Input event action to trigger an AJAX call after a specified delay when the input changes. It also
     * adds a series of actions that execute when the Enter key is pressed, including an AJAX call and a
     * termination of further events.
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
     * Refreshes the data grid when a filter change is detected.
     *
     * This method handles the logic for updating the display of the data grid
     * to reflect new filter criteria, ensuring accurate and up-to-date data.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgStatisticsGroups->refresh();
    }

    /**
     * Binds data to the statistics groups data grid based on a specified condition.
     *
     * This method retrieves the filtering condition using the getCondition method
     * and applies it to the data grid for statistics groups. It ensures that the
     * data grid displays relevant data based on the current criteria.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgStatisticsGroups->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the current filter text input.
     *
     * This method utilizes the text from the filter input to create a query condition.
     * If the filter is empty or null, it returns a condition that matches all records.
     * If the filter is not empty, it constructs an "or" condition to match either the
     * 'Name' or 'Title' fields against the specified search value.
     *
     * @return Q\Query\QQ A query condition object that represents either a match-all condition or
     *                    a specific condition based on the filter input.
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
                Q\Query\QQ::like(QQN::StatisticsSettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::StatisticsSettings()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes a set of buttons and text boxes used for links navigation and actions.
     *
     * This method sets up various UI elements including buttons and text boxes for links-related
     * operations. It configures their display properties, styles, and behavior based on session variables
     * and user interactions. The visibility and actions of each button are carefully defined to handle
     * specific user requests related to links management.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnGoToStatistics = new Bs\Button($this);
        $this->btnGoToStatistics->Text = t('Go to this statistics');
        $this->btnGoToStatistics->addWrapperCssClass('center-button');
        $this->btnGoToStatistics->CssClass = 'btn btn-default';
        $this->btnGoToStatistics->CausesValidation = false;
        $this->btnGoToStatistics->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToStatistics_Click'));
        $this->btnGoToStatistics->setCssStyle('float', 'left');
        $this->btnGoToStatistics->setCssStyle('margin-right', '10px');

        if (!empty($_SESSION['statistics_edit_group']) || (!empty($_SESSION['statistics']) || !empty($_SESSION['group']))) {
            $this->btnGoToStatistics->Display = true;
        } else {
            $this->btnGoToStatistics->Display = false;
        }

        $this->txtStatisticsGroup = new Bs\TextBox($this);
        $this->txtStatisticsGroup->Placeholder = t('Statistics group');
        $this->txtStatisticsGroup->ActionParameter = $this->txtStatisticsGroup->ControlId;
        $this->txtStatisticsGroup->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtStatisticsGroup->setHtmlAttribute('autocomplete', 'off');
        $this->txtStatisticsGroup->setCssStyle('float', 'left');
        $this->txtStatisticsGroup->setCssStyle('margin-right', '10px');
        $this->txtStatisticsGroup->Width = 300;
        $this->txtStatisticsGroup->Display = false;

        $this->txtStatisticsTitle = new Bs\TextBox($this);
        $this->txtStatisticsTitle->Placeholder = t('Statistics group title');
        $this->txtStatisticsTitle->ActionParameter = $this->txtStatisticsTitle->ControlId;
        $this->txtStatisticsTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;

        $this->txtStatisticsTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtStatisticsTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtStatisticsTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->txtStatisticsTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtStatisticsTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtStatisticsTitle->setCssStyle('float', 'left');
        $this->txtStatisticsTitle->setCssStyle('margin-right', '10px');
        $this->txtStatisticsTitle->Width = 400;
        $this->txtStatisticsTitle->Display = false;

        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Update');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->btnSave->setCssStyle('float', 'left');
        $this->btnSave->setCssStyle('margin-right', '10px');
        $this->btnSave->Display = false;

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->btnCancel->setCssStyle('float', 'left');
        $this->btnCancel->Display = false;
    }

    /**
     * Creates modal dialogs to handle specific user-related actions or warnings.
     *
     * This method initializes and configures modal dialogs used for displaying critical
     * messages or warnings. In this case, it creates a modal to notify the user about
     * an invalid CSRF token, including a warning title, styled header, explanatory text,
     * and a close button.
     *
     * @return void This method does not return any value.
     */
    public function createModals()
    {
        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal1->Title = t("Warning");
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addCloseButton(t("I understand"));
    }

    /**
     * Creates and configures a Toastr notification instance.
     *
     * This method initializes and sets up a Toastr instance for displaying notification messages.
     * It defines the alert type, position class, message content, and additional features such as
     * a progress bar, ensuring the notification is styled and positioned appropriately.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToast1 = new Q\Plugin\Toastr($this);
        $this->dlgToast1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast1->Message = t('<strong>Well done!</strong> The statistics group title has been saved or modified.');
        $this->dlgToast1->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the save button click event to update statistics settings and frontend links.
     *
     * This method updates the title and metadata of a statistics group, modifies related menu content,
     * and adjusts the associated frontend links. It also manages the visibility and interactivity of
     * interface elements such as buttons and text boxes, ensuring consistency in the user interface.
     * Finally, it refreshes the data grid displaying the statistics groups and triggers a notification.
     *
     * @param ActionParams $params Event action parameters used during the button click event.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objGroup = StatisticsSettings::load($this->intId);
        $objSelectedGroup = StatisticsSettings::selectedByIdFromStatisticsSettings($this->intId);
        $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

        $objMenuContent->updateMenuContent($this->txtStatisticsTitle->Text, $objGroup->getTitleSlug());

        $objGroup->setTitle($this->txtStatisticsTitle->Text);
        $objGroup->setPostUpdateDate(Q\QDateTime::Now());
        $objGroup->setAssignedEditorsNameById($this->intLoggedUserId);
        $objGroup->save();

        $objFrontendLink->setTitle($this->txtStatisticsTitle->Text);
        $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
        $objFrontendLink->save();

        if (!empty($_SESSION['links_edit_group']) || (!empty($_SESSION['links']) || !empty($_SESSION['group']))) {
            $this->btnGoToStatistics->Display = true;
            $this->btnGoToStatistics->Enabled = true;
        }

        $this->txtStatisticsGroup->Display = false;
        $this->txtStatisticsTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgStatisticsGroups->refresh();
        $this->dtgStatisticsGroups->removeCssClass('disabled');
        $this->dlgToast1->notify();
    }

    /**
     * Handles the cancel button click event to reset the UI elements and data for statistics management.
     *
     * This method is triggered when the cancel button is clicked. It updates the display properties and
     * state of various UI components related to statistics groups and titles. Additionally, it ensures
     * the proper reset of text inputs and re-enables relevant controls to restore default functionality.
     *
     * @param ActionParams $params The parameters associated with the cancel button click action.
     * @return void
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['statistics_edit_group']) || (!empty($_SESSION['statistics']) || !empty($_SESSION['group']))) {
            $this->btnGoToStatistics->Display = true;
            $this->btnGoToStatistics->Enabled = true;
        }

        $this->txtStatisticsGroup->Display = false;
        $this->txtStatisticsTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->dtgStatisticsGroups->removeCssClass('disabled');
        $this->txtStatisticsGroup->Text = null;
        $this->txtStatisticsTitle->Text = null;
    }

    /**
     * Handles the click event for the "Go to Statistics" button.
     *
     * Directs the user to the appropriate page depending on the session variables related
     * to editing statistics or groups. If a specific statistics edit group is set in the
     * session, the user is redirected to the corresponding menu edit page. Otherwise,
     * redirects to the statistics edit page based on session variables for statistics or group.
     * Clears the relevant session data after redirection.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     * @return void
     */
    protected function btnGoToStatistics_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['statistics_edit_group'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['statistics_edit_group']);
            unset($_SESSION['statistics_edit_group']);

        } else if (!empty($_SESSION['statistics']) || !empty($_SESSION['group'])) {
            Application::redirect('statistics_edit.php?id=' . $_SESSION['statistics'] . '&group=' . $_SESSION['group']);
            unset($_SESSION['statistics']);
            unset($_SESSION['group']);
        }
    }
}