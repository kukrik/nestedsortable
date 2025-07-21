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

class VideosSetting extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    protected $dlgToast1;

    public $txtFilter;
    public $dtgVideosGroups;

    public $txtVideosGroup;
    public $txtVideosTitle;
    public $btnSave;
    public $btnCancel;
    public $btnGoToVideos;

    protected $objUser;
    protected $intLoggedUserId;
    protected $intId;

    protected $objMenuContent;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'videosSettings.tpl.php';

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
        $this->dtgVideosGroups_Create();
        $this->dtgVideosGroups->setDataBinder('BindData', $this);

        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the videos groups data grid.
     *
     * @return void
     */
    protected function dtgVideosGroups_Create()
    {
        $this->dtgVideosGroups = new VideosSettingsTable($this);
        $this->dtgVideosGroups_CreateColumns();
        $this->createPaginators();
        $this->dtgVideosGroups_MakeEditable();
        $this->dtgVideosGroups->RowParamsCallback = [$this, "dtgVideosGroups_GetRowParams"];
        $this->dtgVideosGroups->SortColumnIndex = 0;
        $this->dtgVideosGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Create columns for the data grid
     *
     * @return void
     */
    protected function dtgVideosGroups_CreateColumns()
    {
        $this->dtgVideosGroups->createColumns();
    }

    /**
     * Configure the videos groups data grid to be editable by adding actions and CSS classes.
     *
     * The method adds an AJAX action on cell click events and applies specific CSS classes
     * to make rows clickable and styles the data grid accordingly.
     *
     * @return void
     */
    protected function dtgVideosGroups_MakeEditable()
    {
        $this->dtgVideosGroups->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgVideosGroups_Click'));
        $this->dtgVideosGroups->addCssClass('clickable-rows');
        $this->dtgVideosGroups->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for the videos groups.
     *
     * @param ActionParams $params Parameters associated with the action event.
     * @return void
     */
    protected function dtgVideosGroups_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objVideosGroups = VideosSettings::load($this->intId);
        $objMenuContent = MenuContent::loadById($objVideosGroups->getMenuContentId());

        $this->txtVideosGroup->Enabled = false;
        $this->txtVideosGroup->Text = $objVideosGroups->getName();
        $this->txtVideosTitle->Text = $objVideosGroups->getTitle();
        $this->txtVideosTitle->focus();

        if (!empty($_SESSION['videos_edit_group']) || (!empty($_SESSION['videos']) || !empty($_SESSION['group']))) {
            $this->btnGoToVideos->Display = true;
            $this->btnGoToVideos->Enabled = false;
        }

        $this->dtgVideosGroups->addCssClass('disabled');
        $this->txtVideosGroup->Display = true;
        $this->txtVideosTitle->Display = true;
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
    public function dtgVideosGroups_GetRowParams($objRowObject, $intRowIndex)
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
        $this->dtgVideosGroups->Paginator = new Bs\Paginator($this);
        $this->dtgVideosGroups->Paginator->LabelForPrevious = t('Previous');
        $this->dtgVideosGroups->Paginator->LabelForNext = t('Next');

        $this->dtgVideosGroups->ItemsPerPage = 10;
        $this->dtgVideosGroups->SortColumnIndex = 0;
        $this->dtgVideosGroups->UseAjax = true;

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
        $this->dtgVideosGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgVideosGroups->refresh();
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
     * Responds to a change in the filter by refreshing the data grid of videos groups.
     *
     * This method is triggered when there is a change in the filter criteria. Its primary
     * function is to refresh the data grid displaying the videos groups to reflect the
     * updated filter conditions.
     *
     * @return void This method does not return any value.
     */
    protected function filterChanged()
    {
        $this->dtgVideosGroups->refresh();
    }

    /**
     * Binds data to the videos groups data grid based on a specified condition.
     *
     * This method retrieves a condition and uses it to bind relevant data to the data grid
     * for videos groups. The condition is obtained from the getCondition method.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgVideosGroups->bindData($objCondition);
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
                Q\Query\QQ::like(QQN::VideosSettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::VideosSettings()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes a set of buttons and text boxes used for videos navigation and actions.
     *
     * This method sets up various UI elements including buttons and text boxes for videos-related
     * operations. It configures their display properties, styles, and behavior based on session variables
     * and user interactions. The visibility and actions of each button are carefully defined to handle
     * specific user requests related to videos management.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnGoToVideos = new Bs\Button($this);
        $this->btnGoToVideos->Text = t('Go to this videos');
        $this->btnGoToVideos->addWrapperCssClass('center-button');
        $this->btnGoToVideos->CssClass = 'btn btn-default';
        $this->btnGoToVideos->CausesValidation = false;
        $this->btnGoToVideos->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToVideos_Click'));
        $this->btnGoToVideos->setCssStyle('float', 'left');
        $this->btnGoToVideos->setCssStyle('margin-right', '10px');

        if (!empty($_SESSION['videos_edit_group']) || (!empty($_SESSION['videos']) || !empty($_SESSION['group']))) {
            $this->btnGoToVideos->Display = true;
        } else {
            $this->btnGoToVideos->Display = false;
        }

        $this->txtVideosGroup = new Bs\TextBox($this);
        $this->txtVideosGroup->Placeholder = t('videos group');
        $this->txtVideosGroup->ActionParameter = $this->txtVideosGroup->ControlId;
        $this->txtVideosGroup->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtVideosGroup->setHtmlAttribute('autocomplete', 'off');
        $this->txtVideosGroup->setCssStyle('float', 'left');
        $this->txtVideosGroup->setCssStyle('margin-right', '10px');
        $this->txtVideosGroup->Width = 300;
        $this->txtVideosGroup->Display = false;

        $this->txtVideosTitle = new Bs\TextBox($this);
        $this->txtVideosTitle->Placeholder = t('Videos group title');
        $this->txtVideosTitle->ActionParameter = $this->txtVideosTitle->ControlId;
        $this->txtVideosTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;

        $this->txtVideosTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtVideosTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtVideosTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->txtVideosTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtVideosTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtVideosTitle->setCssStyle('float', 'left');
        $this->txtVideosTitle->setCssStyle('margin-right', '10px');
        $this->txtVideosTitle->Width = 400;
        $this->txtVideosTitle->Display = false;

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
        $this->dlgToast1->Message = t('<strong>Well done!</strong> The videos group title has been saved or modified.');
        $this->dlgToast1->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the save action for a videos group, updating its properties and managing UI elements
     * based on the provided input parameters.
     *
     * This method updates a videos group's name and date, assigns editors, and toggles the visibility
     * and state of UI elements based on certain conditions. It also manages notification dialogs and
     * refreshes the videos groups display.
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

        $objVideosGroup = VideosSettings::load($this->intId);
        $objSelectedGroup = VideosSettings::selectedByIdFromVideosSettings($this->intId);
        $objMenuContent = MenuContent::load($objSelectedGroup->getMenuContentId());
        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getMenuContentId());

        $objMenuContent->updateMenuContent($this->txtVideosTitle->Text, $objVideosGroup->getTitleSlug());

        $objVideosGroup->setTitle($this->txtVideosTitle->Text);
        $objVideosGroup->setTitleSlug($objMenuContent->getRedirectUrl());
        $objVideosGroup->setPostUpdateDate(Q\QDateTime::Now());
        $objVideosGroup->setAssignedEditorsNameById($this->intLoggedUserId);
        $objVideosGroup->save();

        $objFrontendLink->setTitle($this->txtVideosTitle->Text);
        $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
        $objFrontendLink->save();

        if (!empty($_SESSION['videos_edit_group']) || (!empty($_SESSION['videos']) || !empty($_SESSION['group']))) {
            $this->btnGoToVideos->Display = true;
            $this->btnGoToVideos->Enabled = true;
        }

        $this->txtVideosGroup->Display = false;
        $this->txtVideosTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgVideosGroups->refresh();
        $this->dtgVideosGroups->removeCssClass('disabled');
        $this->dlgToast1->notify();
    }

    /**
     * Handles the cancellation of videos or group edit operation.
     *
     * This method is triggered when the cancel button is clicked. It checks session variables
     * to determine if a videos or group is being edited, and accordingly adjusts the UI elements
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

        if (!empty($_SESSION['videos_edit_group']) || (!empty($_SESSION['videos']) || !empty($_SESSION['group']))) {
            $this->btnGoToVideos->Display = true;
            $this->btnGoToVideos->Enabled = true;
        }

        $this->txtVideosGroup->Display = false;
        $this->txtVideosTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->dtgVideosGroups->removeCssClass('disabled');
        $this->txtVideosGroup->Text = null;
        $this->txtVideosTitle->Text = null;
    }

    /**
     * Handles the click event for the "Go To videos" button, redirecting the user to the appropriate edit page.
     *
     * This method checks session variables to determine which videos or group edit page to redirect to. It first
     * checks for the presence of 'videos_edit_group' session variable and redirects accordingly, clearing the
     * session afterwards. If not present, it then checks for 'videos' or 'group' session variables to redirect
     * to their respective edit page, clearing those sessions as well.
     *
     * @param ActionParams $params The parameters associated with the button click event.
     * @return void
     */
    protected function btnGoToVideos_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['videos_edit_group'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['videos_edit_group']);
            unset($_SESSION['videos_edit_group']);

        } else if (!empty($_SESSION['videos']) || !empty($_SESSION['group'])) {
            Application::redirect('videos_edit.php?id=' . $_SESSION['videos'] . '&group=' . $_SESSION['group']);
            unset($_SESSION['videos']);
            unset($_SESSION['group']);
        }
    }
}