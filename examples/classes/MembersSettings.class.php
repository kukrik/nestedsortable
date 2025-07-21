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

class MembersSetting extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    protected $dlgToast1;

    public $txtFilter;
    public $dtgMembersGroups;

    public $txtMembersGroup;
    public $txtMembersTitle;
    public $btnSave;
    public $btnCancel;
    public $btnGoToMembers;

    protected $objUser;
    protected $intLoggedUserId;
    protected $intId;

    protected $objMenuContent;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'MembersSettings.tpl.php';

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
        $this->dtgMembersGroups_Create();
        $this->dtgMembersGroups->setDataBinder('BindData', $this);

        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the MembersGroups data table by setting up columns, paginators, and editability.
     * This method configures the data table to display and interact with records of member settings.
     *
     * @return void
     */
    protected function dtgMembersGroups_Create()
    {
        $this->dtgMembersGroups = new MembersSettingsTable($this);
        $this->dtgMembersGroups_CreateColumns();
        $this->createPaginators();
        $this->dtgMembersGroups_MakeEditable();
        $this->dtgMembersGroups->RowParamsCallback = [$this, "dtgMembersGroups_GetRowParams"];
        $this->dtgMembersGroups->SortColumnIndex = 0;
        $this->dtgMembersGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * This method is responsible for creating columns for the dtgMembersGroups object.
     *
     * @return void
     */
    protected function dtgMembersGroups_CreateColumns()
    {
        $this->dtgMembersGroups->createColumns();
    }

    /**
     * Configures the MembersGroups data grid to be editable by adding actions and CSS classes.
     * The method sets up a cell click event to handle row data interactions and applies
     * styling to make the rows appear clickable and responsive.
     *
     * @return void
     */
    protected function dtgMembersGroups_MakeEditable()
    {
        $this->dtgMembersGroups->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgMembersGroups_Click'));
        $this->dtgMembersGroups->addCssClass('clickable-rows');
        $this->dtgMembersGroups->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for the dtgMembersGroups control. It loads the selected member group settings
     * and updates the UI components based on the loaded data.
     *
     * @param ActionParams $params Parameters for the action, including the ActionParameter which contains the ID
     *                             of the member group to be loaded.
     * @return void This method does not return any value.
     */
    protected function dtgMembersGroups_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objMembersGroups = MembersSettings::load($this->intId);
        $objMenuContent = MenuContent::loadById($objMembersGroups->getMenuContentId());

        $this->txtMembersGroup->Enabled = false;
        $this->txtMembersTitle->Text = $objMembersGroups->getTitle();
        $this->txtMembersGroup->Text = $objMembersGroups->getName();
        $this->txtMembersGroup->focus();

        if (!empty($_SESSION['members_edit_group'])) {
            $this->btnGoToMembers->Display = true;
            $this->btnGoToMembers->Enabled = false;
        }

        $this->dtgMembersGroups->addCssClass('disabled');
        $this->txtMembersGroup->Display = true;
        $this->txtMembersTitle->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Generates parameters for a row in a data table, based on the provided row object and row index.
     *
     * @param object $objRowObject The object representing the current row for which parameters are being generated.
     * @param int $intRowIndex The index of the current row in the data table.
     * @return array An associative array containing parameters, with 'data-value' set to the primary key of the row object.
     */
    public function dtgMembersGroups_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures pagination for the members groups data grid.
     *
     * This method sets up a paginator with labels for navigation and configures
     * the number of items per page, default sort column, and enables AJAX updates.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgMembersGroups->Paginator = new Bs\Paginator($this);
        $this->dtgMembersGroups->Paginator->LabelForPrevious = t('Previous');
        $this->dtgMembersGroups->Paginator->LabelForNext = t('Next');

        $this->dtgMembersGroups->ItemsPerPage = 10;
        $this->dtgMembersGroups->SortColumnIndex = 0;
        $this->dtgMembersGroups->UseAjax = true;

        $this->addFilterActions();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the items-per-page selector for the current user and configures its properties and events.
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
     * Retrieves a list of items per page assigned to a user, encapsulated in ListItem objects.
     *
     * This method generates a condition-based query to fetch items per page associated
     * with a specific user. Each item is represented as a ListItem, and if the item
     * matches the one assigned to the current user, it is marked as selected.
     *
     * @return ListItem[] An array containing ListItem objects each representing an item per page.
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
     * Updates the number of items per page displayed for members groups based on the selected user object,
     * and refreshes the data grid to reflect this change.
     *
     * @param ActionParams $params The parameters passed from the action triggering this method.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgMembersGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgMembersGroups->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes a search filter textbox with specific attributes and CSS classes, preparing it for user input and interaction.
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
     * Adds actions to the filter input field to handle user interactions.
     *
     * The method sets up an input event to trigger an Ajax control action after
     * a specified delay, allowing for dynamic filtering. Additionally, it configures
     * an enter key event which triggers the same Ajax action and immediately
     * terminates the event after execution.
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
     * Refreshes the members groups data grid when a filter change is detected.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgMembersGroups->refresh();
    }

    /**
     * Binds data to the data grid using a specified condition.
     *
     * This method retrieves the current condition to filter or modify
     * the dataset and then binds this data to a grid for display or processing.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgMembersGroups->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the current filter text input. If the filter text is empty or null,
     * the condition will match all records. Otherwise, it constructs a condition that matches records where
     * either the 'Name' or 'Title' fields contain the provided search value.
     *
     * @return Q\Query\QQ a query condition object that represents the current filter criteria.
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
                Q\Query\QQ::like(QQN::MembersSettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::MembersSettings()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures several buttons and text boxes for the user interface.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnGoToMembers = new Bs\Button($this);
        $this->btnGoToMembers->Text = t('Go to this members');
        $this->btnGoToMembers->addWrapperCssClass('center-button');
        $this->btnGoToMembers->CssClass = 'btn btn-default';
        $this->btnGoToMembers->CausesValidation = false;
        $this->btnGoToMembers->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToMembers_Click'));
        $this->btnGoToMembers->setCssStyle('float', 'left');
        $this->btnGoToMembers->setCssStyle('margin-right', '10px');

        if (!empty($_SESSION['members_edit_group'])) {
            $this->btnGoToMembers->Display = true;
        } else {
            $this->btnGoToMembers->Display = false;
        }
        
        $this->txtMembersGroup = new Bs\TextBox($this);
        $this->txtMembersGroup->Placeholder = t('Members group');
        $this->txtMembersGroup->ActionParameter = $this->txtMembersGroup->ControlId;
        $this->txtMembersGroup->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtMembersGroup->setHtmlAttribute('autocomplete', 'off');
        $this->txtMembersGroup->setCssStyle('float', 'left');
        $this->txtMembersGroup->setCssStyle('margin-right', '10px');
        $this->txtMembersGroup->Width = 300;
        $this->txtMembersGroup->Display = false;

        $this->txtMembersTitle = new Bs\TextBox($this);
        $this->txtMembersTitle->Placeholder = t('Members title');
        $this->txtMembersTitle->ActionParameter = $this->txtMembersGroup->ControlId;
        $this->txtMembersTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;

        $this->txtMembersTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtMembersTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtMembersTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->txtMembersTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtMembersTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtMembersTitle->setCssStyle('float', 'left');
        $this->txtMembersTitle->setCssStyle('margin-right', '10px');
        $this->txtMembersTitle->Width = 400;
        $this->txtMembersTitle->Display = false;

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
     * Initializes multiple Toastr dialogs with predefined configurations for success and error alerts.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToast1 = new Q\Plugin\Toastr($this);
        $this->dlgToast1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast1->Message = t('<strong>Well done!</strong> The members group title has been saved or modified.');
        $this->dlgToast1->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the save button click event to update or create a Members Group.
     * Depending on the text values provided, it processes the MembersSettings
     * and updates UI components accordingly.
     *
     * @param ActionParams $params The parameters provided by the action triggering the click event.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objMembersGroup = MembersSettings::load($this->intId);
        $objSelectedGroup = MembersSettings::selectedByIdFromMembersSettings($this->intId);
        $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

        $objMenuContent->updateMenuContent($this->txtMembersTitle->Text, $objMembersGroup->getTitleSlug());

        $objMembersGroup->setTitle($this->txtMembersTitle->Text);
        $objMembersGroup->setTitleSlug($objMenuContent->getRedirectUrl());
        $objMembersGroup->setPostUpdateDate(Q\QDateTime::Now());
        $objMembersGroup->setAssignedEditorsNameById($this->intLoggedUserId);
        $objMembersGroup->save();

        $objFrontendLink->setTitle($this->txtMembersTitle->Text);
        $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
        $objFrontendLink->save();

        if (!empty($_SESSION['members_edit_group'])) {
            $this->btnGoToMembers->Display = true;
            // $this->btnGoToMembers->Enabled = true;
        }

        $this->btnGoToMembers->Enabled = true;

        $this->txtMembersGroup->Display = false;
        $this->txtMembersTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgMembersGroups->refresh();
        $this->dtgMembersGroups->removeCssClass('disabled');
        $this->dlgToast1->notify();
    }

    /**
     * Handles the click event for the cancel button.
     * Resets the display and state of various UI elements associated with member groups.
     *
     * @param ActionParams $params Parameters related to the action event triggered.
     * @return void
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['members_edit_group'])) {
            $this->btnGoToMembers->Display = true;
            $this->btnGoToMembers->Enabled = true;
        }

        $this->txtMembersGroup->Display = false;
        $this->txtMembersTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->dtgMembersGroups->removeCssClass('disabled');
        $this->txtMembersGroup->Text = null;
        $this->txtMembersTitle->Text = null;
    }

    /**
     * Handles the click event for the "Go To Members" button. Redirects the user to the appropriate edit page based
     * on the session variables available. Clears the related session variables after redirection.
     *
     * @param ActionParams $params The parameters associated with the action triggering this event.
     * @return void
     */
    protected function btnGoToMembers_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['members_edit_group'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['members_edit_group']);
            unset($_SESSION['members_edit_group']);
        }
    }
}