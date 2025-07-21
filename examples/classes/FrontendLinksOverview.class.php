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

class FrontendLinksOverview extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;
    public $txtFilter;
    public $dtgFrontendLinks;
    public $btnUpdate;

    protected $objUser;
    protected $intLoggedUserId;

    protected $strTemplate = 'FrontendLinksOverview.tpl.php';

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
        $this->dtgFrontendLinks_Create();
        $this->dtgFrontendLinks->setDataBinder('BindData', $this);
        $this->createModals();
        $this->createButtons();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the FrontendLinksOverviewTable component.
     *
     * @return void
     */
    protected function dtgFrontendLinks_Create()
    {
        $this->dtgFrontendLinks = new FrontendLinksOverviewTable($this);
        $this->dtgFrontendLinks_CreateColumns();
        $this->createPaginators();
        $this->dtgFrontendLinks_MakeEditable();
        $this->dtgFrontendLinks->SortColumnIndex = 0;
        $this->dtgFrontendLinks->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Creates columns for the FrontendLinks data grid.
     *
     * @return void
     */
    protected function dtgFrontendLinks_CreateColumns()
    {
        $this->dtgFrontendLinks->createColumns();
    }

    /**
     * Makes the frontend links data grid editable by setting its CSS class to apply
     * specific styling, enhancing its appearance and interaction.
     *
     * @return void
     */
    protected function dtgFrontendLinks_MakeEditable()
    {
        $this->dtgFrontendLinks->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Initializes and configures the paginators for the frontend links data grid.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgFrontendLinks->Paginator = new Bs\Paginator($this);
        $this->dtgFrontendLinks->Paginator->LabelForPrevious = t('Previous');
        $this->dtgFrontendLinks->Paginator->LabelForNext = t('Next');

        $this->dtgFrontendLinks->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgFrontendLinks->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgFrontendLinks->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgFrontendLinks->ItemsPerPage = 10;
        $this->dtgFrontendLinks->SortColumnIndex = 4;
        $this->dtgFrontendLinks->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Initializes and configures a Select2 control for selecting items per page based on the user's assigned settings.
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
     * Retrieves a list of ListItem objects for the ItemsPerPageByAssignedUserObject based on a specific condition.
     *
     * @return ListItem[] An array of ListItem objects, each representing an item that matches the given condition. If the item is the same as the one assigned to the current user, it is marked as selected.
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
     * Handles the change event for the dropdown list of items per page, updating the number of items
     * displayed in the frontend links datagrid based on the user's selection.
     *
     * @param ActionParams $params The parameters associated with the change event.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgFrontendLinks->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgFrontendLinks->refresh();
    }

    /**
     * Initializes the filter text box component used for searching.
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
     * Adds input and key event actions to the filter text box.
     *
     * Configures the filter text box to trigger an AJAX call when the user provides input or presses the Enter key.
     * Specifically, when the input event is detected with a delay of 300 milliseconds, the 'filterChanged' method
     * is called via an AJAX action. Additionally, when the Enter key is pressed, an array of actions is executed:
     * an AJAX call to the 'FilterChanged' method is made, followed by a termination action.
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
     * Triggers a refresh on the frontend links data grid when the filter is changed.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgFrontendLinks->refresh();
    }

    /**
     * Binds data to the `dtgFrontendLinks` component using a specified condition.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgFrontendLinks->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the current text filter input.
     *
     * This method analyzes the text input from a filter control and generates
     * a corresponding query condition. If the input is null or empty, it returns
     * a condition that matches all records. Otherwise, it constructs a condition
     * using several fields and applies equality or pattern matching based on the
     * input value.
     *
     * @return Q\Query\Condition\ConditionInterface The constructed query condition
     *                                               to be used for searching or filtering
     *                                               data based on the input criteria.
     */
    public function getCondition()
    {
        $strSearchValue = $this->txtFilter->Text;

        if ($strSearchValue !== null) {
            $strSearchValue = trim($strSearchValue);
        }

        if (is_null($strSearchValue) || $strSearchValue === '') {
            return Q\Query\QQ::all();
        } else {
            return Q\Query\QQ::orCondition(
                Q\Query\QQ::equal(QQN::FrontendLinks()->Id, $strSearchValue),
                Q\Query\QQ::equal(QQN::FrontendLinks()->LinkedId, $strSearchValue),
                Q\Query\QQ::equal(QQN::FrontendLinks()->GroupedId, $strSearchValue),
                Q\Query\QQ::equal(QQN::FrontendLinks()->ContentTypesManagamentId, $strSearchValue),
                Q\Query\QQ::like(QQN::FrontendLinks()->FrontendClassName, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::FrontendLinks()->FrontendTemplatePath, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::FrontendLinks()->Title, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::FrontendLinks()->FrontendTitleSlug, "%" . $strSearchValue . "%"),
                Q\Query\QQ::equal(QQN::FrontendLinks()->IsActivated, $strSearchValue)
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the update button for the interface. The button is styled with
     * specific CSS classes, does not trigger form validation, and has an assigned click event
     * that performs an Ajax action.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnUpdate = new Bs\Button($this);
        $this->btnUpdate->Text = t('Update table');
        $this->btnUpdate->CssClass = 'btn btn-orange';
        $this->btnUpdate->addWrapperCssClass('center-button');
        $this->btnUpdate->CausesValidation = false;
        $this->btnUpdate->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnUpdate_Click'));
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
     * Handles the click event for the update button.
     *
     * @param ActionParams $params The parameters associated with the click action event.
     * @return void
     */
    public function btnUpdate_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->dtgFrontendLinks->refresh();
    }
}