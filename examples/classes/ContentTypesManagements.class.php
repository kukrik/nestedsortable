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

class ContentTypesManagements extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    public $lblInfo;
    public $txtFilter;
    public $dtgContentTypesManagements;
    public $btnUpdate;
    public $btnNew;

    protected $objUser;
    protected $intLoggedUserId;

    protected $strTemplate = 'ContentTypesManagements.tpl.php';

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

        // $this->intLoggedUserId= $_SESSION['logged_user_id']; // Approximately example here etc...
        // For example, John Doe is a logged user with his session

        $this->intLoggedUserId = 1;
        $this->objUser = User::load($this->intLoggedUserId);

        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Display = true;
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-warning-info alert-dismissible');
        $this->lblInfo->Text = t('<p>Note! This desktop is created for a webmaster or for a developer. This desktop is 
                                    necessary for adding a new class, such as the Blog class. To do this, you first need 
                                    to create a "blog" table in the database. Then, add a new row in the "content_type" 
                                    table and let the code generator generate the ORM model objects. After that, write 
                                    the custom Blog class and connect it to the template manager.</p>');

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgContentTypesManagements_Create();
        $this->dtgContentTypesManagements->setDataBinder('BindData', $this);
        $this->createModals();
        $this->createButtons();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the ContentTypesManagementTable object.
     *
     * This method sets up the data grid for managing content types by creating
     * its columns, enabling pagination, and making it editable. It also sets
     * up row parameters and sorting configuration. The number of items per
     * page is determined by the user's preference.
     *
     * @return void
     **/
    protected function dtgContentTypesManagements_Create()
    {
        $this->dtgContentTypesManagements = new ContentTypesManagementTable($this);
        $this->dtgContentTypesManagements_CreateColumns();
        $this->createPaginators();
        $this->dtgContentTypesManagements_MakeEditable();
        $this->dtgContentTypesManagements->RowParamsCallback = [$this, "dtgContentTypesManagements_GetRowParams"];
        $this->dtgContentTypesManagements->SortColumnIndex = 0;
        $this->dtgContentTypesManagements->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();

    }

    /**
     * Creates columns for dtgContentTypesManagements.
     *
     * @return void
     **/
    protected function dtgContentTypesManagements_CreateColumns()
    {
        $this->dtgContentTypesManagements->createColumns();
    }

    /**
     * Configures the ContentTypesManagement table to be editable by adding interactivity features.
     *
     * @return void
     **/
    protected function dtgContentTypesManagements_MakeEditable()
    {
        $this->dtgContentTypesManagements->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgContentTypesManagements_Click'));
        $this->dtgContentTypesManagements->addCssClass('clickable-rows');
        $this->dtgContentTypesManagements->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for content type managements.
     *
     * @param ActionParams $params The parameters associated with the click action, including the action parameter which indicates the ID.
     * @return void Redirects to the content types management edit page for the specified ID.
     */
    protected function dtgContentTypesManagements_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $intId = intval($params->ActionParameter);
        Application::redirect('content_types_management_edit.php' . '?id=' . $intId);
    }

    /**
     * Retrieves the parameters for a specific row in the Content Types Management data grid.
     *
     * @param object $objRowObject The object representing the current row from which parameters are extracted.
     * @param int $intRowIndex The index of the current row within the data grid.
     * @return array An associative array of parameters where 'data-value' is set to the primary key of the object.
     */
    public function dtgContentTypesManagements_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures paginators for content type management.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgContentTypesManagements->Paginator = new Bs\Paginator($this);
        $this->dtgContentTypesManagements->Paginator->LabelForPrevious = t('Previous');
        $this->dtgContentTypesManagements->Paginator->LabelForNext = t('Next');

        $this->dtgContentTypesManagements->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgContentTypesManagements->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgContentTypesManagements->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgContentTypesManagements->ItemsPerPage = 10;
        $this->dtgContentTypesManagements->SortColumnIndex = 4;
        $this->dtgContentTypesManagements->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Initializes and configures the items-per-page selector for filtering by assigned user.
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
     * Retrieves a list of ListItem objects representing items per page for the assigned user.
     *
     * @return ListItem[] An array of ListItem objects containing the items per page options.
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
     * Updates the items per page setting for the content types management based on user selection
     * and refreshes the data grid to reflect the changes.
     *
     * @param ActionParams $params Parameters related to the user action triggering this change.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgContentTypesManagements->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgContentTypesManagements->refresh();
    }

    /**
     * Initializes and configures a search filter textbox component within the UI.
     *
     * @return void This method does not return any value.
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
     * Adds input and enter key actions to the filter control.
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
     * Triggers the refresh of the content types management data grid when a filter change occurs.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgContentTypesManagements->refresh();
    }

    /**
     * Binds data to the content types management data grid based on a specified condition.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgContentTypesManagements->bindData($objCondition);
    }

    /**
     * Constructs a query condition for filtering content types based on the user input.
     *
     * @return mixed A QQ condition object that represents the filter criteria for content types.
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
                Q\Query\QQ::equal(QQN::ContentTypesManagement()->Id, $strSearchValue),
                Q\Query\QQ::like(QQN::ContentTypesManagement()->ContentName, "%" . $strSearchValue . "%"),
                Q\Query\QQ::equal(QQN::ContentTypesManagement()->DefaultFrontendTemplateId, $strSearchValue)
            );
        }
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
     * Creates and initializes buttons for update and new actions.
     *
     * @return void No return value, the buttons are created as part of the class state.
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
        $this->btnNew->Text = t(' New');
        $this->btnNew->Glyph = 'fa fa-plus';
        $this->btnNew->CssClass = 'btn btn-orange';
        $this->btnNew->addWrapperCssClass('center-button');
        $this->btnNew->CausesValidation = false;
        $this->btnNew->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnNew_Click'));
    }

    /**
     * Handles the click event for the Update button, triggering a refresh of the content types management data grid.
     *
     * @param ActionParams $params The action parameters associated with the button click event.
     * @return void
     */
    public function btnUpdate_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->dtgContentTypesManagements->refresh();
    }

    /**
     * Handles the click event of the 'New' button, redirecting the user to the content types management edit page.
     *
     * @return void
     */
    protected function btnNew_Click()
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::redirect('content_types_management_edit.php');
    }
}