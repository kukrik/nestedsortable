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

class LinksSetting extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    protected $dlgToast1;

    public $txtFilter;
    public $dtgLinksGroups;

    public $txtLinksGroup;
    public $txtLinksTitle;
    public $btnSave;
    public $btnCancel;
    public $btnGoToLinks;

    protected $objUser;
    protected $intLoggedUserId;
    protected $intId;

    protected $objMenuContent;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'LinksSettings.tpl.php';

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
        $this->dtgLinksGroups_Create();
        $this->dtgLinksGroups->setDataBinder('BindData', $this);

        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the links groups data grid.
     *
     * @return void
     */
    protected function dtgLinksGroups_Create()
    {
        $this->dtgLinksGroups = new LinksSettingsTable($this);
        $this->dtgLinksGroups_CreateColumns();
        $this->createPaginators();
        $this->dtgLinksGroups_MakeEditable();
        $this->dtgLinksGroups->RowParamsCallback = [$this, "dtgLinksGroups_GetRowParams"];
        $this->dtgLinksGroups->SortColumnIndex = 0;
        $this->dtgLinksGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Create columns for the data grid
     *
     * @return void
     */
    protected function dtgLinksGroups_CreateColumns()
    {
        $this->dtgLinksGroups->createColumns();
    }

    /**
     * Configure the links groups data grid to be editable by adding actions and CSS classes.
     *
     * The method adds an AJAX action on cell click events and applies specific CSS classes
     * to make rows clickable and styles the data grid accordingly.
     *
     * @return void
     */
    protected function dtgLinksGroups_MakeEditable()
    {
        $this->dtgLinksGroups->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgLinksGroups_Click'));
        $this->dtgLinksGroups->addCssClass('clickable-rows');
        $this->dtgLinksGroups->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for the links groups.
     *
     * @param ActionParams $params Parameters associated with the action event.
     * @return void
     */
    protected function dtgLinksGroups_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objLinksGroups = LinksSettings::load($this->intId);
        $objMenuContent = MenuContent::loadById($objLinksGroups->getMenuContentId());

        $this->txtLinksGroup->Enabled = false;
        $this->txtLinksGroup->Text = $objLinksGroups->getName();
        $this->txtLinksTitle->Text = $objLinksGroups->getTitle();
        $this->txtLinksTitle->focus();

        if (!empty($_SESSION['links_edit_group']) || (!empty($_SESSION['links']) || !empty($_SESSION['group']))) {
            $this->btnGoToLinks->Display = true;
            $this->btnGoToLinks->Enabled = false;
        }

        $this->dtgLinksGroups->addCssClass('disabled');
        $this->txtLinksGroup->Display = true;
        $this->txtLinksTitle->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Retrieves the row parameters for a given row object in a data grid, primarily
     * focusing on obtaining a key value associated with the row object.
     *
     * @param object $objRowObject The row object from the data grid, expected to contain a method for retrieving its primary key.
     * @param int $intRowIndex The index of the row for which parameters are being retrieved. Used for tracking row position.
     * @return array An associative array containing the row parameters, specifically the 'data-value' key holding the primary key of the object.
     */
    public function dtgLinksGroups_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures the paginators for the data grid, setting up
     * pagination labels, items per page, sorting index, and enabling AJAX for
     * dynamic data fetching.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgLinksGroups->Paginator = new Bs\Paginator($this);
        $this->dtgLinksGroups->Paginator->LabelForPrevious = t('Previous');
        $this->dtgLinksGroups->Paginator->LabelForNext = t('Next');

        $this->dtgLinksGroups->ItemsPerPage = 10;
        $this->dtgLinksGroups->SortColumnIndex = 0;
        $this->dtgLinksGroups->UseAjax = true;

        $this->addFilterActions();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Sets up a Select2 dropdown list control for managing items per page, utilizing the Select2 plugin
     * and customizes its properties such as theme, width, and search behavior. It attaches items to
     * the dropdown sourced from a method and associates an event action that triggers on value change.
     *
     * @return void This method does not return a value as it configures a control instance variable.
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
     * Retrieves a list of ListItem objects representing the items per page assigned to the current user.
     *
     * This method queries the items per page assigned to a user based on a specified condition or uses
     * a default condition if none is provided. It iterates through the query results to generate
     * ListItem objects. Optionally marks a ListItem as selected if it matches the user's assigned item.
     *
     * @return ListItem[] An array of ListItem objects that represent the items per page assigned to the user.
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
     * Updates the items per page setting for a user's assigned object and refreshes the display.
     *
     * This method modifies the number of items per page displayed in the data grid groups based on the
     * selected name from the user's assigned objects list. After updating the items per page, it refreshes
     * the data grid to reflect the changes.
     *
     * @param ActionParams $params The parameters received from the change action, providing context about the event.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgLinksGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgLinksGroups->refresh();
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
     * Responds to a change in the filter by refreshing the data grid of links groups.
     *
     * This method is triggered when there is a change in the filter criteria. Its primary
     * function is to refresh the data grid displaying the links groups to reflect the
     * updated filter conditions.
     *
     * @return void This method does not return any value.
     */
    protected function filterChanged()
    {
        $this->dtgLinksGroups->refresh();
    }

    /**
     * Binds data to the links groups data grid based on a specified condition.
     *
     * This method retrieves a condition and uses it to bind relevant data to the data grid
     * for links groups. The condition is obtained from the getCondition method.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgLinksGroups->bindData($objCondition);
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
                Q\Query\QQ::like(QQN::LinksSettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::LinksSettings()->Title, "%" . $strSearchValue . "%")
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
        $this->btnGoToLinks = new Bs\Button($this);
        $this->btnGoToLinks->Text = t('Go to this links');
        $this->btnGoToLinks->addWrapperCssClass('center-button');
        $this->btnGoToLinks->CssClass = 'btn btn-default';
        $this->btnGoToLinks->CausesValidation = false;
        $this->btnGoToLinks->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToLinks_Click'));
        $this->btnGoToLinks->setCssStyle('float', 'left');
        $this->btnGoToLinks->setCssStyle('margin-right', '10px');

        if (!empty($_SESSION['links_edit_group']) || (!empty($_SESSION['links']) || !empty($_SESSION['group']))) {
            $this->btnGoToLinks->Display = true;
        } else {
            $this->btnGoToLinks->Display = false;
        }

        $this->txtLinksGroup = new Bs\TextBox($this);
        $this->txtLinksGroup->Placeholder = t('Links group');
        $this->txtLinksGroup->ActionParameter = $this->txtLinksGroup->ControlId;
        $this->txtLinksGroup->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtLinksGroup->setHtmlAttribute('autocomplete', 'off');
        $this->txtLinksGroup->setCssStyle('float', 'left');
        $this->txtLinksGroup->setCssStyle('margin-right', '10px');
        $this->txtLinksGroup->Width = 300;
        $this->txtLinksGroup->Display = false;

        $this->txtLinksTitle = new Bs\TextBox($this);
        $this->txtLinksTitle->Placeholder = t('Links group title');
        $this->txtLinksTitle->ActionParameter = $this->txtLinksTitle->ControlId;
        $this->txtLinksTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;

        $this->txtLinksTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtLinksTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtLinksTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->txtLinksTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtLinksTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtLinksTitle->setCssStyle('float', 'left');
        $this->txtLinksTitle->setCssStyle('margin-right', '10px');
        $this->txtLinksTitle->Width = 400;
        $this->txtLinksTitle->Display = false;

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
     * Initializes various Toastr notification objects with predefined messages and settings.
     *
     * This method sets up a series of Toastr notifications with different alert types, positions,
     * messages, and displays settings for operational feedback. Each Toastr is configured to show a
     * progress bar and messages are localized using the translation function.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToast1 = new Q\Plugin\Toastr($this);
        $this->dlgToast1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast1->Message = t('<strong>Well done!</strong> The links group title has been saved or modified.');
        $this->dlgToast1->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the save action for a links group, updating its properties and managing UI elements
     * based on the provided input parameters.
     *
     * This method updates a links group's name and date, assigns editors, and toggles the visibility
     * and state of UI elements based on certain conditions. It also manages notification dialogs and
     * refreshes the links groups display.
     *
     * @param ActionParams $params The parameters passed to the save action, which includes user input data.
     * @return void This method does not return any value.
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objLinksGroup = LinksSettings::load($this->intId);
        $objSelectedGroup = LinksSettings::selectedByIdFromLinksSettings($this->intId);
        $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

        $objMenuContent->updateMenuContent($this->txtLinksTitle->Text, $objLinksGroup->getTitleSlug());

        $objLinksGroup->setTitle($this->txtLinksTitle->Text);
        $objLinksGroup->setTitleSlug($objMenuContent->getRedirectUrl());
        $objLinksGroup->setPostUpdateDate(Q\QDateTime::Now());
        $objLinksGroup->setAssignedEditorsNameById($this->intLoggedUserId);
        $objLinksGroup->save();

        $objFrontendLink->setTitle($this->txtLinksTitle->Text);
        $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
        $objFrontendLink->save();

        if (!empty($_SESSION['links_edit_group']) || (!empty($_SESSION['links']) || !empty($_SESSION['group']))) {
            $this->btnGoToLinks->Display = true;
            $this->btnGoToLinks->Enabled = true;
        }

        $this->txtLinksGroup->Display = false;
        $this->txtLinksTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgLinksGroups->refresh();
        $this->dtgLinksGroups->removeCssClass('disabled');
        $this->dlgToast1->notify();
    }

    /**
     * Handles the cancellation of links or group edit operation.
     *
     * This method is triggered when the cancel button is clicked. It checks session variables
     * to determine if a links or group is being edited, and accordingly adjusts the UI elements
     * by toggling the display and enabling/disabling certain controls. It also resets text inputs
     * and removes a CSS class from the data grid.
     *
     * @param ActionParams $params The parameters associated with the action that triggered this method.
     * @return void
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['links_edit_group']) || (!empty($_SESSION['links']) || !empty($_SESSION['group']))) {
            $this->btnGoToLinks->Display = true;
            $this->btnGoToLinks->Enabled = true;
        }

        $this->txtLinksGroup->Display = false;
        $this->txtLinksTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->dtgLinksGroups->removeCssClass('disabled');
        $this->txtLinksGroup->Text = null;
        $this->txtLinksTitle->Text = null;
    }

    /**
     * Handles the click event for the "Go To links" button, redirecting the user to the appropriate edit page.
     *
     * This method checks session variables to determine which links or group edit page to redirect to. It first
     * checks for the presence of 'links_edit_group' session variable and redirects accordingly, clearing the
     * session afterwards. If not present, it then checks for 'links' or 'group' session variables to redirect
     * to their respective edit page, clearing those sessions as well.
     *
     * @param ActionParams $params The parameters associated with the button click event.
     * @return void
     */
    protected function btnGoToLinks_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['links_edit_group'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['links_edit_group']);
            unset($_SESSION['links_edit_group']);

        } else if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
            Application::redirect('links_edit.php?id=' . $_SESSION['links'] . '&group=' . $_SESSION['group']);
            unset($_SESSION['links']);
            unset($_SESSION['group']);
        }
    }
}