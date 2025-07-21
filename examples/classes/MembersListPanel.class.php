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

class MembersListPanel extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    public $txtFilter;
    public $dtgMembers;
    public $btnBack;

    protected $objUser;
    protected $intLoggedUserId;
    protected $objGroupCondition;
    protected $objGroupClauses;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'MembersListPanel.tpl.php';

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
        $this->dtgMembers_Create();
        $this->dtgMembers->setDataBinder('BindData', $this);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and configures the "Back" button with specified text, CSS classes, and actions.
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
     * Handles the click event for the "Back" button, redirecting the user to the menu manager page.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnBack_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('menu_manager.php');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the members data grid by creating an instance of MembersTable
     * and configuring its properties and behaviors. This includes setting up
     * columns, pagination, editability, row parameters callback, sorting, and
     * AJAX usage.
     *
     * @return void
     */
    protected function dtgMembers_Create()
    {
        $this->dtgMembers = new MembersTable($this);
        $this->dtgMembers_CreateColumns();
        $this->createPaginators();
        $this->dtgMembers_MakeEditable();
        $this->dtgMembers->RowParamsCallback = [$this, "dtgMembers_GetRowParams"];
        $this->dtgMembers->SortColumnIndex = 5;
        //$this->dtgMembers->SortDirection = -1;
        $this->dtgMembers->UseAjax = true;
        $this->dtgMembers->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Creates columns for the members data grid.
     *
     * @return void
     */
    protected function dtgMembers_CreateColumns()
    {
        $this->dtgMembers->createColumns();
    }

    /**
     * Configures the dtgMembers data grid to be editable by adding a cell click event and styling.
     *
     * @return void
     */
    protected function dtgMembers_MakeEditable()
    {
        $this->dtgMembers->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgMembersRow_Click'));
        $this->dtgMembers->addCssClass('clickable-rows');
        $this->dtgMembers->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for a row in the members data grid.
     *
     * @param ActionParams $params The parameters associated with the click action,
     *                             including the action parameter used to identify the specific member.
     * @return void
     */
    protected function dtgMembersRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $intId = intval($params->ActionParameter);
        $objMember = MembersSettings::loadById($intId);
        $intGroup = $objMember->getMenuContentId();

        Application::redirect('members_edit.php' . '?id=' . $intId . '&group=' . $intGroup);
    }

    /**
     * Generates row parameters for a table row representing a member.
     *
     * @param object $objRowObject The object representing the row data, which should have a primary key.
     * @param int $intRowIndex The index of the row in the table.
     * @return array The array of parameters including the 'data-value' attribute assigned to the primary key of the row object.
     */
    public function dtgMembers_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures the paginators for the members data grid.
     *
     * This method sets up two paginators (primary and alternate) for handling
     * pagination of the data grid displaying members. Each paginator is customized
     * with labels for the previous and next controls. The number of items displayed
     * per page is set to a default value. Additionally, this method invokes the
     * addition of filter actions to the data grid.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgMembers->Paginator = new Bs\Paginator($this);
        $this->dtgMembers->Paginator->LabelForPrevious = t('Previous');
        $this->dtgMembers->Paginator->LabelForNext = t('Next');

        $this->dtgMembers->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgMembers->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgMembers->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgMembers->ItemsPerPage = 10;

        $this->addFilterActions();
    }

    /**
     * Initializes and configures the list of items per page for a user interface element.
     * The method sets up a Select2 list control with specified properties and events.
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
     * @return ListItem[] An array of ListItem objects, with selected state based on user assignment.
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
     * Updates the datagrid's items per page setting based on the selected option
     * and refreshes the datagrid to reflect this change.
     *
     * @param ActionParams $params The parameters associated with the change action.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgMembers->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgMembers->refresh();
    }

    /**
     * Initializes the filter text box used for search functionality.
     * The text box is configured with a placeholder, search mode, and additional attributes
     * and CSS classes to enhance user experience. Also, sets up filter actions necessary for interaction.
     *
     * @return void
     */
    protected function createFilter()
    {
        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();
    }

    /**
     * Adds filter actions to the filter text box. This includes setting up an input event
     * handler that triggers an Ajax control response, and another event handler for the
     * Enter key which triggers the same response and then terminates further event propagation.
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
     * Refreshes the members data grid when a filter change is detected.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgMembers->refresh();
    }

    /**
     * Binds data to the data grid based on the current condition.
     *
     * This method retrieves the current condition using the getCondition method
     * and then binds the data to the data grid, dtgMembers, using the retrieved condition.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgMembers->bindData($objCondition);
    }

    /**
     * Constructs and returns a condition for querying MembersSettings based on the current filter text.
     *
     * @return Q\Query\Condition The condition for filtering MembersSettings, or a condition that matches all records if no valid search value is provided.
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
                Q\Query\QQ::like(QQN::MembersSettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::MembersSettings()->Title, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::MembersSettings()->Author, "%" . $strSearchValue . "%")
            );
        }
    }
}