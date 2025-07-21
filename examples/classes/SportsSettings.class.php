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

class SportsCalendarSettings extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    protected $dlgToast1;

    public $txtFilter;
    public $dtgSportsGroups;

    public $txtSportsGroup;
    public $txtSportsTitle;
    public $btnSave;
    public $btnCancel;
    public $btnGoToCalendar;

    protected $objUser;
    protected $intLoggedUserId;
    protected $intId;

    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'SportsSettings.tpl.php';

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
        $this->dtgSportsGroups_Create();
        $this->dtgSportsGroups->setDataBinder('BindData', $this);

        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the sports groups data table.
     *
     * This method is responsible for setting up the sports groups
     * data table by creating columns, adding pagination, and making
     * the table editable. It also sets parameters for row configuration
     * and defines default sorting behavior.
     *
     * @return void
     */
    protected function dtgSportsGroups_Create()
    {
        $this->dtgSportsGroups = new SportsSettingsTable($this);
        $this->dtgSportsGroups_CreateColumns();
        $this->createPaginators();
        $this->dtgSportsGroups_MakeEditable();
        $this->dtgSportsGroups->RowParamsCallback = [$this, "dtgSportsGroups_GetRowParams"];
        $this->dtgSportsGroups->SortColumnIndex = 0;
        $this->dtgSportsGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Creates and initializes the columns for the sports groups data table.
     *
     * @return void
     */
    protected function dtgSportsGroups_CreateColumns()
    {
        $this->dtgSportsGroups->createColumns();
    }

    /**
     * Configures the SportsGroups data grid to be editable by adding appropriate actions and CSS classes.
     *
     * This method adds a cell click event action that triggers an AJAX control callback on click.
     * It also applies CSS classes to make rows clickable and to enhance the visual style of the table.
     *
     * @return void
     */
    protected function dtgSportsGroups_MakeEditable()
    {
        $this->dtgSportsGroups->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgSportsGroups_Click'));
        $this->dtgSportsGroups->addCssClass('clickable-rows');
        $this->dtgSportsGroups->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for the sports groups data grid.
     *
     * This method processes the click event triggered in the sports groups data grid.
     * It loads and configures the necessary data based on the selected sports group for editing.
     *
     * @param ActionParams $params An object containing the parameters associated with the action event,
     *                             including the identifier of the selected sports group.
     * @return void This method does not return a value.
     */
    protected function dtgSportsGroups_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objEventsGroups = SportsSettings::load($this->intId);
        $objMenuContent = MenuContent::loadById($objEventsGroups->getMenuContentId());

        $this->txtSportsGroup->Enabled = false;
        $this->txtSportsGroup->Text = $objEventsGroups->getName();
        $this->txtSportsTitle->Text = $objEventsGroups->getTitle();
        $this->txtSportsTitle->focus();

        if (!empty($_SESSION['sports_edit_group']) || !empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
            $this->btnGoToCalendar->Display = true;
            $this->btnGoToCalendar->Enabled = false;
        }

        $this->dtgSportsGroups->addCssClass('disabled');
        $this->txtSportsGroup->Display = true;
        $this->txtSportsTitle->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Retrieves the parameters for a specific row in the sports groups data grid.
     *
     * @param object $objRowObject The row object containing data for the specific row.
     * @param int $intRowIndex The index of the row in the data grid.
     * @return array An associative array of parameters for the row, with data attributes included.
     */
    public function dtgSportsGroups_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Configures and attaches paginators to the data grid, sets pagination labels,
     * items per page, initializes AJAX functionality, and applies filter actions.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgSportsGroups->Paginator = new Bs\Paginator($this);
        $this->dtgSportsGroups->Paginator->LabelForPrevious = t('Previous');
        $this->dtgSportsGroups->Paginator->LabelForNext = t('Next');

        $this->dtgSportsGroups->ItemsPerPage = 10;
        $this->dtgSportsGroups->SortColumnIndex = 0;
        $this->dtgSportsGroups->UseAjax = true;

        $this->addFilterActions();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the Select2 control for selecting items per page.
     * The control is customized with specific settings such as theme, width,
     * selection mode, and pre-selected value. It also populates the control with
     * a list of items and sets up an AJAX action that triggers on change.
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
     * Retrieves a list of ListItem objects representing items per page settings for the assigned user object.
     *
     * @return ListItem[] An array of ListItem objects, where each item represents a setting for items per page.
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
     * Handles changes to the items per page selection by the assigned user object.
     *
     * @param ActionParams $params The parameters containing action-specific data for the event.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgSportsGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgSportsGroupsdtgSportsGroups->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a filter text box for search functionality.
     *
     * @return void
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
     * Adds actions to the filter input control.
     *
     * This method sets up two types of actions for the filter input control:
     * - An `Input` event with a delay of 300 milliseconds, triggering an Ajax call to 'filterChanged'.
     * - An `EnterKey` event that triggers two actions:
     *   - An Ajax call to 'filterChanged'.
     *   - A termination action to cease any further event processing.
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
     * Refreshes the data display in the sports groups table when the filter settings change.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgSportsGroups->refresh();
    }

    /**
     * Binds data to the sports groups table based on a specific condition.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgSportsGroups->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the filter text input. If the filter text input is empty or null,
     * returns a condition that matches all records. Otherwise, constructs a condition to match records
     * with names similar to the provided filter text.
     *
     * @return Q\Query\QQ The constructed query condition to be used for filtering records based on the given criteria.
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
                Q\Query\QQ::like(QQN::SportsSettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::SportsSettings()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the button and text box controls related to the sports calendar management.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnGoToCalendar = new Bs\Button($this);
        $this->btnGoToCalendar->Text = t('Go to this sports calendar');
        $this->btnGoToCalendar->addWrapperCssClass('center-button');
        $this->btnGoToCalendar->CssClass = 'btn btn-default';
        $this->btnGoToCalendar->CausesValidation = false;
        $this->btnGoToCalendar->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToCalendar_Click'));
        $this->btnGoToCalendar->setCssStyle('float', 'left');
        $this->btnGoToCalendar->setCssStyle('margin-right', '10px');

        if (!empty($_SESSION['sports_edit_group']) || !empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
            $this->btnGoToCalendar->Display = true;
        } else {
            $this->btnGoToCalendar->Display = false;
        }

        $this->txtSportsGroup = new Bs\TextBox($this);
        $this->txtSportsGroup->Placeholder = t('Sports calendar group');
        $this->txtSportsGroup->ActionParameter = $this->txtSportsGroup->ControlId;
        $this->txtSportsGroup->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtSportsGroup->setHtmlAttribute('autocomplete', 'off');
        $this->txtSportsGroup->setCssStyle('float', 'left');
        $this->txtSportsGroup->setCssStyle('margin-right', '10px');
        $this->txtSportsGroup->Width = 300;
        $this->txtSportsGroup->Display = false;

        $this->txtSportsTitle = new Bs\TextBox($this);
        $this->txtSportsTitle->Placeholder = t('Sports calendar title');
        $this->txtSportsTitle->ActionParameter = $this->txtSportsTitle->ControlId;
        $this->txtSportsTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;

        $this->txtSportsTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtSportsTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtSportsTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->txtSportsTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtSportsTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtSportsTitle->setCssStyle('float', 'left');
        $this->txtSportsTitle->setCssStyle('margin-right', '10px');
        $this->txtSportsTitle->Width = 400;
        $this->txtSportsTitle->Display = false;

        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Update');
        $this->btnSave->CssClass = 'btn btn-orange save-js';
        $this->btnSave->addWrapperCssClass('center-button');
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
     * Initializes multiple Toastr dialog instances with different configurations
     * for displaying success and error messages. Configures alert types, positions,
     * messages, and progress bars for each Toastr instance.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToast1 = new Q\Plugin\Toastr($this);
        $this->dlgToast1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast1->Message = t('<strong>Well done!</strong> The sports calendar group has been saved or modified.');
        $this->dlgToast1->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the save button click event to update sports group information.
     *
     * @param ActionParams $params The action parameters associated with the button click event.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objGroup = SportsSettings::load($this->intId);
        $objSelectedGroup = SportsSettings::selectedByIdFromSportsSettings($this->intId);
        $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

        $objMenuContent->updateMenuContent($this->txtSportsTitle->Text, $objGroup->getTitleSlug());

        $objGroup->setTitle($this->txtSportsTitle->Text);
        $objGroup->setPostUpdateDate(Q\QDateTime::Now());
        $objGroup->save();

        $objFrontendLink->setTitle($this->txtSportsTitle->Text);
        $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
        $objFrontendLink->save();

        if (!empty($_SESSION['sports_edit_group']) || !empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
            $this->btnGoToCalendar->Display = true;
            $this->btnGoToCalendar->Enabled = true;
        }

        $this->txtSportsGroup->Display = false;
        $this->txtSportsTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgSportsGroups->refresh();
        $this->dtgSportsGroups->removeCssClass('disabled');
        $this->dlgToast1->notify();
    }

    /**
     * Handles the click event for the cancel button. This function checks specific session variables
     * to determine the display and enabled state of certain UI elements and resets text fields and their visibility.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     * @return void
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['sports_edit_group']) || !empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
            $this->btnGoToCalendar->Display = true;
            $this->btnGoToCalendar->Enabled = true;
        }

        $this->txtSportsGroup->Display = false;
        $this->txtSportsTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->dtgSportsGroups->removeCssClass('disabled');
        $this->txtSportsGroup->Text = null;
        $this->txtSportsTitle->Text = null;
    }

    /**
     * Handles the click event for the "Go To Calendar" button. Redirects the user to the appropriate page
     * based on session variables for sports groups or sports events.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     * @return void
     */
    protected function btnGoToCalendar_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['sports_edit_group'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['sports_edit_group']);
            unset($_SESSION['sports_edit_group']);

       }  else if (!empty($_SESSION['sports_id']) && !empty($_SESSION['sports_group'])) {
            Application::redirect('sports_calendar_edit.php?id=' . $_SESSION['sports_id'] . '&group=' . $_SESSION['sports_group']);
            unset($_SESSION['sports_id']);
            unset($_SESSION['sports_group']);
        }
    }
}