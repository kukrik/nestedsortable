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
use QCubed\QString;

class BoardListPanel extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    public $txtFilter;
    public $dtgBoards;
    public $btnBack;

    protected $objUser;
    protected $intLoggedUserId;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'BoardListPanel.tpl.php';

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

        $this->createButtons();
        $this->createModals();
        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgBoards_Create();
        $this->dtgBoards->setDataBinder('BindData', $this);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Create and configure the 'Back' button with associated actions and styles
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->addWrapperCssClass('center-button');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnBack_Click'));
    }

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
     * Handles the 'Back' button click event by redirecting to the menu manager page.
     *
     * @param ActionParams $params The parameters for the action event, typically including context-specific information about the event.
     * @return void
     */
    public function btnBack_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('menu_manager.php');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Create and configure the boards datagrid
     *
     * @return void
     */
    protected function dtgBoards_Create()
    {
        $this->dtgBoards = new BoardTable($this);
        $this->dtgBoards_CreateColumns();
        $this->createPaginators();
        $this->dtgBoards_MakeEditable();
        $this->dtgBoards->RowParamsCallback = [$this, "dtgBoards_GetRowParams"];
        $this->dtgBoards->SortColumnIndex = 5;
        //$this->dtgBoards->SortDirection = -1;
        $this->dtgBoards->UseAjax = true;
        $this->dtgBoards->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Create columns for the datagrid
     *
     * @return void
     */
    protected function dtgBoards_CreateColumns()
    {
        $this->dtgBoards->createColumns();
    }

    /**
     * Configures the dtgBoards datatable to be interactive and editable by adding
     * appropriate actions and CSS classes. This method enables cell click actions
     * that trigger an AJAX control event and applies specified CSS classes to the table.
     *
     * @return void
     */
    protected function dtgBoards_MakeEditable()
    {
        $this->dtgBoards->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgBoardsRow_Click'));
        $this->dtgBoards->addCssClass('clickable-rows');
        $this->dtgBoards->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles click events on rows of the dtgBoards datatable. Retrieves the board
     * settings based on the action parameter's identifier, then redirects the user
     * to the board edit page with the board's ID and group information as query parameters.
     *
     * @param ActionParams $params The parameters associated with the action, containing
     *                             the identifier of the clicked row's board.
     *
     * @return void
     */
    protected function dtgBoardsRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $intId = intval($params->ActionParameter);
        $objBoard = BoardsSettings::loadById($intId);
        $intGroup = $objBoard->getMenuContentId();

        Application::redirect('board_edit.php' . '?id=' . $intId . '&group=' . $intGroup);
    }

    /**
     * Get row parameters for the row tag
     *
     * @param mixed $objRowObject   A database object
     * @param int $intRowIndex      The row index
     * @return array
     */
    public function dtgBoards_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Sets up pagination for the dtgBoards datatable by initializing primary and
     * alternate paginators with labels for navigation controls and specifying
     * the number of items displayed per page. Additionally, invokes actions
     * to handle filtering of data within the table.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgBoards->Paginator = new Bs\Paginator($this);
        $this->dtgBoards->Paginator->LabelForPrevious = t('Previous');
        $this->dtgBoards->Paginator->LabelForNext = t('Next');

        $this->dtgBoards->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgBoards->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgBoards->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgBoards->ItemsPerPage = 10;

        $this->addFilterActions();
    }

    /**
     * Initializes and configures a Select2 control for selecting the number of items
     * per page by an assigned user. This method sets various properties such as the theme,
     * width, and selection mode. It also populates the control with item options and
     * attaches an AJAX change event to handle user interactions.
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
     * Retrieves a list of ListItems representing items per page associated with an assigned user object.
     * This method queries the database for items per page objects based on a specified condition and
     * returns them as ListItem objects. The ListItem will be marked as selected if it matches the
     * currently assigned user object's item.
     *
     * @return ListItem[] An array of ListItems containing items per page associated with an assigned user object.
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
     * Updates the number of items displayed per page for a data grid based on the selection
     * from a list associated with an assigned user object. This method adjusts the items per
     * page of the data grid and refreshes it to reflect the updated pagination settings.
     *
     * @param ActionParams $params The action parameters containing details of the change event.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgBoards->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgBoards->refresh();
    }

    /**
     * Creates a filter control for user input. Initializes a text box with specific
     * properties and styling to serve as a search input field. This text box is designed
     * to provide a seamless user experience for entering search queries.
     *
     * @return void This method does not return any value.
     */
    protected function createFilter() {
        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();
    }

    /**
     * Adds filter actions to the txtFilter control. This method sets up event-driven
     * interactions for the filter functionality. It registers an input event that triggers
     * an AJAX action after a delay, as well as an enter key event that initiates both an
     * AJAX action and an action termination.
     *
     * @return void
     */
    protected function addFilterActions()
    {
        $this->txtFilter->addAction(new Q\Event\Input(300), new Q\Action\AjaxControl($this, 'filterChanged'));
        $this->txtFilter->addActionArray(new Q\Event\EnterKey(),
            [
                new Q\Action\AjaxControl($this, 'FilterChanged'),
                new Q\Action\Terminate()
            ]
        );
    }

    /**
     * Handles the event when a filter is changed, triggering the refresh of the board data grid.
     * This method updates the displayed data in the data grid to reflect the current filter criteria.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgBoards->refresh();
    }

    /**
     * Binds data to the data table by applying a specific condition.
     * This method retrieves a condition, typically used for filtering or querying purposes,
     * and applies it to bind data to a data table component.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgBoards->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the current text input in the filter field.
     * This method evaluates the search value and returns a condition that matches records
     * in the BoardsSettings database table where the name, title, or author fields contain
     * the specified search value. If the search value is empty, it returns a condition that matches all records.
     *
     * @return Q\Query\QQ A query condition that can be used to filter BoardsSettings records
     * based on the text input from the filter, or all records if no input is provided.
     */
    protected function getCondition()
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
                Q\Query\QQ::like(QQN::BoardsSettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::BoardsSettings()->Title, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::BoardsSettings()->Author, "%" . $strSearchValue . "%")
            );
        }
    }
}