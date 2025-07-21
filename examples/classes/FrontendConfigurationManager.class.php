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

class FrontendConfigurationManager extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;
    public $lblInfo;
    public $txtFilter;
    public $dtgFrontendOptions;
    public $btnUpdate;
    public $btnNew;
    public $btnBack;

    protected $objUser;
    protected $intLoggedUserId;

    protected $strTemplate = 'FrontendConfigurationManager.tpl.php';

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

        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Display = true;
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-warning-info alert-dismissible');
        $this->lblInfo->Text = t('<p>Note! This desktop is created for a webmaster or developer. If you want to create 
                                    new front-end templates, it is necessary to create a new class for the designated 
                                    folder on the front end. After that, you can add new entries. In the template manager, 
                                    you can change the front-end class.</p>');

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgFrontendOptions_Create();
        $this->dtgFrontendOptions->setDataBinder('BindData', $this);
        $this->createModals();
        $this->createButtons();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and configures the FrontendConfigurationTable for frontend options.
     * Initializes columns, pagination, editability, and other table parameters.
     *
     * @return void
     */
    protected function dtgFrontendOptions_Create()
    {
        $this->dtgFrontendOptions = new FrontendConfigurationTable($this);
        $this->dtgFrontendOptions_CreateColumns();
        $this->createPaginators();
        $this->dtgFrontendOptions_MakeEditable();
        $this->dtgFrontendOptions->RowParamsCallback = [$this, "dtgFrontendOptions_GetRowParams"];
        $this->dtgFrontendOptions->SortColumnIndex = 0;
        $this->dtgFrontendOptions->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Creates columns for the frontend options data grid.
     *
     * @return void
     */
    protected function dtgFrontendOptions_CreateColumns()
    {
        $this->dtgFrontendOptions->createColumns();
    }

    /**
     * Configures the data grid for frontend options to be editable.
     *
     * This method associates a cell click event with an AJAX action, enabling interactive row selection.
     * It also applies the necessary CSS classes to style the data grid with hover effects and responsiveness.
     *
     * @return void
     */
    protected function dtgFrontendOptions_MakeEditable()
    {
        $this->dtgFrontendOptions->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgFrontendOptions_Click'));
        $this->dtgFrontendOptions->addCssClass('clickable-rows');
        $this->dtgFrontendOptions->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for frontend options and redirects to the edit page of the selected option.
     *
     * @param ActionParams $params The action parameters containing the ID of the selected option.
     * @return void
     */
    protected function dtgFrontendOptions_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $intOptionId = intval($params->ActionParameter);
        Application::redirect('frontend_option_edit.php' . '?id=' . $intOptionId);
    }

    /**
     * Retrieves the parameters for a row in the frontend options table.
     *
     * @param object $objRowObject The row object containing data for which parameters are being set.
     * @param int $intRowIndex The index of the current row.
     * @return array An associative array of parameters for the row, including the data-value key set to the row's primary key.
     */
    public function dtgFrontendOptions_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures the paginators for the frontend options data grid.
     *
     * This method sets up two paginators for navigating through the data grid pages,
     * with labels for previous and next page navigation. It also configures the data grid
     * to display a specific number of items per page, sets the default sort column index,
     * and enables AJAX for data fetching.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgFrontendOptions->Paginator = new Bs\Paginator($this);
        $this->dtgFrontendOptions->Paginator->LabelForPrevious = t('Previous');
        $this->dtgFrontendOptions->Paginator->LabelForNext = t('Next');

        $this->dtgFrontendOptions->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgFrontendOptions->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgFrontendOptions->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgFrontendOptions->ItemsPerPage = 10;
        $this->dtgFrontendOptions->SortColumnIndex = 4;
        $this->dtgFrontendOptions->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Initializes and configures the Select2 control for managing items per page selection.
     *
     * This method sets up a Select2 control with specific configurations including
     * search options, theme, width, and selection mode. It also populates the control
     * with items and sets an action to handle changes in the selection.
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
     * Retrieves a list of items per page that are assigned to a specific user object.
     * The method constructs a list of ListItem objects based on the query result.
     *
     * @return ListItem[] An array of ListItem objects where each item represents a page assigned to the user.
     * If a page is currently selected for the user, its corresponding ListItem is marked as selected.
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
     * Handles the change event for the ItemsPerPageByAssignedUserObject list.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgFrontendOptions->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgFrontendOptions->refresh();
    }

    /**
     * Initializes and configures a searchable text box filter for the application.
     * Sets up various attributes and styles specific to the search functionality.
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
     * Adds filter actions to the txtFilter control. These actions include
     * an input event with a debounce period and a sequence of actions
     * triggered by the Enter key event. The input event triggers the
     * 'filterChanged' method via an Ajax call after 300 milliseconds,
     * while the Enter key event triggers the 'FilterChanged' method
     * followed by a termination action.
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
     * Handles actions or logic that should occur when a filter change event is detected.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgFrontendOptions->refresh();
    }

    /**
     * Binds data to the frontend options using the specified condition.
     *
     * Retrieves the condition for data binding and delegates the binding
     * process to the frontend options component.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgFrontendOptions->bindData($objCondition);
    }

    /**
     * Builds and returns a condition query based on the user's input in a text filter.
     *
     * The method checks the input from the text filter, trims unnecessary whitespace,
     * and constructs an appropriate query condition for the FrontendOptions based on
     * whether the input is empty or not. If there's no search input, the function
     * returns a 'select all' condition. If input exists, a condition is formed
     * that matches against several fields (FrontendTemplateName, ClassNames, ContentType,
     * ViewType, and Status) using 'like' or 'equal' operations.
     *
     * @return Q\Query\QQCondition The condition query for filtering frontend options based on user input.
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
                Q\Query\QQ::like(QQN::FrontendOptions()->FrontendTemplateName, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::FrontendOptions()->ClassNames, "%" . $strSearchValue . "%"),
                Q\Query\QQ::equal(QQN::FrontendOptions()->ContentType, $strSearchValue),
                Q\Query\QQ::equal(QQN::FrontendOptions()->ViewType, $strSearchValue),
                Q\Query\QQ::equal(QQN::FrontendOptions()->Status, $strSearchValue)
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the update and new template buttons.
     * This method creates two buttons with specific styles, text, and actions,
     * which are connected to their respective click event handlers.
     *
     * @return void This method does not return a value.
     */
    public function createButtons()
    {
        $this->btnUpdate = new Bs\Button($this);
        $this->btnUpdate->Text = t('Update table');
        $this->btnUpdate->CssClass = 'btn btn-orange';
        $this->btnUpdate->addWrapperCssClass('center-button');
        $this->btnUpdate->CausesValidation = false;
        $this->btnUpdate->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnUpdate_Click'));

        $this->btnNew = new Bs\Button($this);
        $this->btnNew->Text = t(' Create a new template class');
        $this->btnNew->Glyph = 'fa fa-plus';
        $this->btnNew->CssClass = 'btn btn-orange';
        $this->btnNew->addWrapperCssClass('center-button');
        $this->btnNew->CausesValidation = false;
        $this->btnNew->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnNew_Click'));
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
     * Handles the click event for the update button, triggering a refresh of the data grid.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnUpdate_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->dtgFrontendOptions->refresh();
    }

    /**
     * Redirects the user to the 'frontend_option_edit.php' page.
     *
     * @return void This method does not return any value.
     */
    protected function btnNew_Click()
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('frontend_option_edit.php');
    }
}