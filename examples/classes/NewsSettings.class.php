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

class NewsSetting extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    protected $dlgToast1;

    public $txtFilter;
    public $dtgNewsGroups;

    public $txtNewsGroup;
    public $txtNewsTitle;
    public $btnSave;
    public $btnCancel;
    public $btnGoToNews;

    protected $objUser;
    protected $intLoggedUserId;
    protected $intId;

    protected $objMenuContent;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'NewsSettings.tpl.php';

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
        $this->dtgNewsGroups_Create();
        $this->dtgNewsGroups->setDataBinder('BindData', $this);

        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the NewsGroups data grid, sets up columns, pagination, editability,
     * and row parameters, and configures default sorting and items per page.
     *
     * @return void
     */
    protected function dtgNewsGroups_Create()
    {
        $this->dtgNewsGroups = new NewsSettingsTable($this);
        $this->dtgNewsGroups_CreateColumns();
        $this->createPaginators();
        $this->dtgNewsGroups_MakeEditable();
        $this->dtgNewsGroups->RowParamsCallback = [$this, "dtgNewsGroups_GetRowParams"];
        $this->dtgNewsGroups->SortColumnIndex = 0;
        $this->dtgNewsGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Initializes and creates columns for the data grid of news groups.
     *
     * @return void
     */
    protected function dtgNewsGroups_CreateColumns()
    {
        $this->dtgNewsGroups->createColumns();
    }

    /**
     * Configures the NewsGroups data grid to be editable by adding interactivity and styling.
     *
     * This method attaches an action to the data grid that triggers an AJAX call when a cell is clicked.
     * It also applies CSS classes to make rows clickable and to style the grid with hover and responsive features.
     *
     * @return void
     */
    protected function dtgNewsGroups_MakeEditable()
    {
        $this->dtgNewsGroups->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgNewsGroups_Click'));
        $this->dtgNewsGroups->addCssClass('clickable-rows');
        $this->dtgNewsGroups->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for the data grid displaying news groups.
     *
     * @param ActionParams $params The parameters associated with the action, including action-specific parameters.
     * @return void
     */
    protected function dtgNewsGroups_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objNewsGroups = NewsSettings::load($this->intId);

        $this->txtNewsGroup->Enabled = false;
        $this->txtNewsGroup->Text = $objNewsGroups->getName();
        $this->txtNewsTitle->Text = $objNewsGroups->getTitle();
        $this->txtNewsTitle->focus();

        if (!empty($_SESSION['news_edit_group']) || (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group']))) {
            $this->btnGoToNews->Display = true;
            $this->btnGoToNews->Enabled = false;
        }

        $this->dtgNewsGroups->addCssClass('disabled');
        $this->txtNewsGroup->Display = true;
        $this->txtNewsTitle->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Generates an array of parameters for a specific row in a data grid.
     *
     * @param object $objRowObject The object representing the data for the current row.
     * @param int $intRowIndex The index of the current row in the data grid.
     * @return array An associative array of parameters for the current row, including data attributes.
     */
    public function dtgNewsGroups_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Creates and configures paginators for the dtgNewsGroups data table.
     * This includes setting up the Paginator object, setting the previous and next labels,
     * defining the number of items per page, setting the default sort column, and enabling AJAX for loading data.
     * It also initiates additional filter actions.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgNewsGroups->Paginator = new Bs\Paginator($this);
        $this->dtgNewsGroups->Paginator->LabelForPrevious = t('Previous');
        $this->dtgNewsGroups->Paginator->LabelForNext = t('Next');

        $this->dtgNewsGroups->ItemsPerPage = 10;
        $this->dtgNewsGroups->SortColumnIndex = 0;
        $this->dtgNewsGroups->UseAjax = true;

        $this->addFilterActions();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the `lstItemsPerPageByAssignedUserObject` control, which is a Select2 dropdown.
     * The dropdown is configured with specific settings including theme, width, and selection mode.
     * Populates the dropdown with items and sets the selected value based on the user's assigned items per page.
     * Attaches an AJAX action to handle changes in the selection.
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
     * Retrieves a list of ListItem objects representing items per page settings for an assigned user.
     *
     * This method queries the ItemsPerPage objects based on a condition and creates a list of ListItem objects,
     * marking the one associated with the current user as selected if applicable.
     *
     * @return ListItem[] An array of ListItem objects, with one marked as selected if it matches the assigned user's settings.
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
     * Updates the number of items displayed per page in the data grid based on the selected option.
     *
     * @param ActionParams $params The action parameters containing the context for the change event.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgNewsGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgNewsGroups->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the filter text box with specific properties and settings.
     * Sets placeholder, text mode, disables autocomplete, and applies CSS class.
     * Invokes a method to add necessary actions to the filter.
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
     * Adds filter actions to the filter input component, enabling the execution of specified actions
     * upon certain events such as input changes or when the Enter key is pressed. The actions trigger
     * an Ajax call to handle the event and optionally terminate further event handling.
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
     * Refreshes the data grid of news groups whenever the filter criteria are modified.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgNewsGroups->refresh();
    }

    /**
     * Binds data to the data grid based on the current condition.
     *
     * This method retrieves the current condition using the getCondition method
     * and applies it to bind data to the dtgNewsGroups data grid.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgNewsGroups->bindData($objCondition);
    }

    /**
     * Retrieves the query condition based on the current filter input.
     * If the filter input is empty or null, it returns a condition that matches all records.
     * Otherwise, it creates a condition to match records where the 'Name' field of
     * 'NewsSettings' contains the filter input as a substring.
     *
     * @return \Q\Query\QQ\Condition The query condition based on the filter input.
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
                Q\Query\QQ::like(QQN::NewsSettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::NewsSettings()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a set of buttons and text boxes for interacting with news items.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnGoToNews = new Bs\Button($this);
        $this->btnGoToNews->Text = t('Go to this news');
        $this->btnGoToNews->addWrapperCssClass('center-button');
        $this->btnGoToNews->CssClass = 'btn btn-default';
        $this->btnGoToNews->CausesValidation = false;
        $this->btnGoToNews->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToNews_Click'));
        $this->btnGoToNews->setCssStyle('float', 'left');
        $this->btnGoToNews->setCssStyle('margin-right', '10px');

        if (!empty($_SESSION['news_edit_group']) || (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group']))) {
            $this->btnGoToNews->Display = true;
        } else {
            $this->btnGoToNews->Display = false;
        }

        $this->txtNewsGroup = new Bs\TextBox($this);
        $this->txtNewsGroup->Placeholder = t('News group');
        $this->txtNewsGroup->ActionParameter = $this->txtNewsGroup->ControlId;
        $this->txtNewsGroup->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtNewsGroup->setHtmlAttribute('autocomplete', 'off');
        $this->txtNewsGroup->setCssStyle('float', 'left');
        $this->txtNewsGroup->setCssStyle('margin-right', '10px');
        $this->txtNewsGroup->Width = 300;
        $this->txtNewsGroup->Display = false;

        $this->txtNewsTitle = new Bs\TextBox($this);
        $this->txtNewsTitle->Placeholder = t('News title');
        $this->txtNewsTitle->ActionParameter = $this->txtNewsTitle->ControlId;
        $this->txtNewsTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;

        $this->txtNewsTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtNewsTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtNewsTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->txtNewsTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtNewsTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtNewsTitle->setCssStyle('float', 'left');
        $this->txtNewsTitle->setCssStyle('margin-right', '10px');
        $this->txtNewsTitle->Width = 400;
        $this->txtNewsTitle->Display = false;

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
     * Initializes and configures multiple Toastr notification instances with different alert types,
     * positions, messages, and progress bar settings for displaying various success or error messages.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToast1 = new Q\Plugin\Toastr($this);
        $this->dlgToast1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast1->Message = t('<strong>Well done!</strong> The news group title has been saved or modified.');
        $this->dlgToast1->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the save button click event to update news group settings, menu content, and frontend links.
     *
     * This method updates the news group information based on user input, including the title and slug. It also updates the
     * associated menu content and frontend link records. After saving the updates, it modifies the visibility and state
     * of specific UI components and refreshes the data grid of news groups.
     *
     * @param ActionParams $params Parameters related to the triggering of the save action.
     * @return void No return value.
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objNewGroup = NewsSettings::load($this->intId);
        $objSelectedGroup = NewsSettings::selectedByIdFromNewsSettings($this->intId);

        //Application::displayAlert(json_encode($objSelectedGroup));

        $objMenuContent = MenuContent::load($objSelectedGroup->getNewsGroupId());
        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getNewsGroupId());

        $objMenuContent->updateMenuContent($this->txtNewsTitle->Text, $objNewGroup->getTitleSlug());

        $objNewGroup->setTitle($this->txtNewsTitle->Text);
        $objNewGroup->setTitleSlug($objMenuContent->getRedirectUrl());
        $objNewGroup->setPostUpdateDate(Q\QDateTime::Now());
        $objNewGroup->save();

        $objFrontendLink->setTitle($this->txtNewsTitle->Text);
        $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
        $objFrontendLink->save();

        if (!empty($_SESSION['news_edit_group']) || (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group']))) {
            $this->btnGoToNews->Display = true;
            $this->btnGoToNews->Enabled = true;
        }

        $this->txtNewsGroup->Display = false;
        $this->txtNewsTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgNewsGroups->refresh();
        $this->dtgNewsGroups->removeCssClass('disabled');
        $this->dlgToast1->notify();
    }

    /**
     * Handles the click event for the cancel button. This method is responsible for adjusting the UI elements
     * based on session variables related to news editing and settings. It modifies the visibility and enabled
     * state of certain UI components and resets text fields.
     *
     * @param ActionParams $params The parameters associated with the action event triggered by the cancel button click.
     * @return void This method does not return any value.
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['news_edit_group']) || (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group']))) {
            $this->btnGoToNews->Display = true;
            $this->btnGoToNews->Enabled = true;
        }

        $this->txtNewsGroup->Display = false;
        $this->txtNewsTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->dtgNewsGroups->removeCssClass('disabled');
        $this->txtNewsGroup->Text = null;
        $this->txtNewsTitle->Text = null;
    }

    /**
     * Handles the redirection logic when the "Go To News" button is clicked,
     * directing the user to the appropriate edit page based on session variables.
     *
     * @param ActionParams $params The parameters passed with the action event.
     * @return void
     */
    protected function btnGoToNews_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['news_edit_group'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['news_edit_group']);
            unset($_SESSION['news_edit_group']);

        } else if (!empty($_SESSION['news_settings_id']) || !empty($_SESSION['news_settings_group'])) {
            Application::redirect('news_edit.php?id=' . $_SESSION['news_settings_id'] . '&group=' . $_SESSION['news_settings_group']);
            unset($_SESSION['news_settings_id']);
            unset($_SESSION['news_settings_group']);
        }
    }

//    public function __get($strName)
//    {
//        switch ($strName) {
//            case 'Idendificator': // Getter omadus 'Idendificator' jaoks
//                return $this->strIdendificator;
//            default:
//                return parent::__get($strName); // QCubed standardne getter
//        }
//    }
//
//    public function __set($strName, $mixValue)
//    {
//        switch ($strName) {
//            case 'Idendificator': // Setter omadus 'Idendificator' jaoks
//                $this->strIdendificator = (string) $mixValue;
//                break;
//            default:
//                parent::__set($strName, $mixValue); // QCubed standardne setter
//        }
//    }

}