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

class SportsViewPanel extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;

    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    public $txtFilter;
    public $dtgSportsAreas;
    public $lblInfo;
    public $btnBack;

    protected $objUser;
    protected $intLoggedUserId;

    protected $strTemplate = 'SportsViewPanel.tpl.php';

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
        $this->dtgSportsAreas_Create();
        $this->createButtons();
        $this->createModals();
        $this->dtgSportsAreas->setDataBinder('bindData', $this);
    }

    /**
     * Initializes and configures the SportsViewTable for displaying sports areas.
     *
     * @return void
     */
    protected function dtgSportsAreas_Create()
    {
        $this->dtgSportsAreas = new SportsViewTable($this);
        $this->dtgSportsAreas_CreateColumns();
        $this->createPaginators();
        $this->dtgSportsAreas_MakeEditable();
        $this->dtgSportsAreas->RowParamsCallback = [$this, "dtgSportsAreas_GetRowParams"];
        $this->dtgSportsAreas->SortColumnIndex = 5;
        $this->dtgSportsAreas->SortDirection = -1;
        $this->dtgSportsAreas->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Creates columns for the sports areas data grid.
     *
     * @return void
     */
    protected function dtgSportsAreas_CreateColumns()
    {
        $this->dtgSportsAreas->createColumns();
    }

    /**
     * Makes the sports areas data grid editable by adding interactive features.
     *
     * This method enables a clickable cell action on the data grid, which triggers
     * an Ajax control event when a cell is clicked. It also applies specific CSS
     * classes to enhance the visual style and interactivity of the rows within the
     * sports areas data grid.
     *
     * @return void
     */
    protected function dtgSportsAreas_MakeEditable()
    {
        $this->dtgSportsAreas->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgSportsAreas_Click'));
        $this->dtgSportsAreas->addCssClass('clickable-rows');
        $this->dtgSportsAreas->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for the sports areas data grid.
     * This method redirects the user to the sports calendar edit page,
     * passing the sports calendar group ID and menu content group ID as parameters.
     *
     * @param ActionParams $params The parameters indicating which sports area was clicked.
     * @return void
     */
    protected function dtgSportsAreas_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $intId = intval($params->ActionParameter);
        $objSportsAreas = SportsTables::load($intId);

        Application::redirect('sports_calendar_edit.php?id=' . $objSportsAreas->getSportsCalendarGroupId() . '&group=' . $objSportsAreas->getMenuContentGroupId());
    }

    /**
     * Retrieves the parameters for a specific row in the sports areas data grid.
     *
     * @param object $objRowObject The object representing the row for which parameters are being retrieved.
     * @param int $intRowIndex The index of the current row.
     * @return array An associative array containing the parameters for the row, including the data-value attribute with the primary key of the object.
     */
    public function dtgSportsAreas_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures paginators for the sports areas data grid. Sets up the primary paginator
     * with labels for navigation and specifies the number of items per page. It enables AJAX usage for the
     * data grid and applies additional filter actions through a separate method call.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgSportsAreas->Paginator = new Bs\Paginator($this);
        $this->dtgSportsAreas->Paginator->LabelForPrevious = t('Previous');
        $this->dtgSportsAreas->Paginator->LabelForNext = t('Next');

        //$this->dtgSportsAreas->PaginatorAlternate = new Bs\Paginator($this);
        //$this->dtgSportsAreas->PaginatorAlternate->LabelForPrevious = t('Previous');
        //$this->dtgSportsAreas->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgSportsAreas->ItemsPerPage = 10;
        $this->dtgSportsAreas->SortColumnIndex = 0;
        $this->dtgSportsAreas->UseAjax = true;
        $this->addFilterActions();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the Select2 control for items per page selection.
     * Sets the theme, width, selection mode, and default selected value based on
     * the user's assigned items per page. It also populates the control with
     * available items and assigns a change event action.
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
     * Retrieves a list of list items representing items per page assigned to users.
     * This function fetches and iterates over items per page based on the specified conditions,
     * creating a ListItem for each and marking it as selected if it matches the assigned user's item.
     *
     * @return ListItem[] An array of ListItems, each representing an item per page assigned to a user,
     *                    with the appropriate ListItem marked as selected if it matches the user's current item.
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
     * Handles the change event for the item list associated with a user object for pagination.
     *
     * @param ActionParams $params The parameters provided by the action triggering the change.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgSportsAreas->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgSportsAreas->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a search filter text box with specific attributes and styles.
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
     * Adds filter actions to a text filter control.
     * This method binds actions to be triggered by events such as input and enter key press.
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
     * Triggers a refresh of the sports areas data grid when the filter is changed.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgSportsAreas->refresh();
    }

    /**
     * Binds data to the sports areas data grid using a specified condition.
     *
     * Retrieves the condition for filtering data and applies it to the data grid
     * of sports areas for binding the relevant data sets.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgSportsAreas->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the user's input from a filter text box.
     * If the filter text is empty or null, it returns a condition that matches all records.
     * Otherwise, it creates a condition that matches any of the fields (Year, SportsAreaName,
     * SportsContentTypeName, Title) containing the filter text.
     *
     * @return Q\Query\Condition The constructed query condition for searching sports areas based on the filter criteria.
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
                Q\Query\QQ::like(QQN::SportsAreas()->Year, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::SportsAreas()->SportsAreaName, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::SportsAreas()->SportsContentTypeName, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::SportsAreas()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    /**
     * Initializes and configures informational and navigation buttons for the user interface.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
        $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('<p>Important information! Clicking on a table row will redirect you to the sports calendar 
                                to edit documents if needed. There will be no return to this page!</p>
                                <p>Alternatively, you can return to this page by using the browser\'s "Back" button.</p>');

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back');
        $this->btnBack->addWrapperCssClass('center-button');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnBack_Click'));
        $this->btnBack->setCssStyle('float', 'left');
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
     * Handles the click event for the back button, performing a redirection and session cleanup.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void This method does not return a value. It redirects the application and modifies the session.
     */
    protected function btnBack_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('menu_edit.php?id=' . $_SESSION['sports_view']);
        unset($_SESSION['sports_view']);

    }
}