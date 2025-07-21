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

class NewsCategoriesManager extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    protected $dlgToastr1;
    protected $dlgToastr2;

    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;

    public $txtFilter;
    public $dtgCategoryOfNewses;

    public $btnAddCategory;
    public $btnGoToNews;
    public $txtCategory;
    public $lstStatus;
    public $btnSaveCategory;
    public $btnSave;
    public $btnDelete;
    public $btnCancel;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $objCategoryNames = [];
    protected $objCategoryIds = [];
    protected $oldName;

    protected $strTemplate = 'NewsCategoriesManager.tpl.php';

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

        $this->intLoggedUserId = 2;
        $this->objUser = User::load($this->intLoggedUserId);

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgNews_Create();
        $this->dtgCategoryOfNewses->setDataBinder('BindData', $this);
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->NewsCategoryNames();
        $this->CheckCategories();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the NewsCategoriesTable for displaying news categories.
     * Sets up columns, pagination, editability, and row parameters callback.
     * Sorts by the first column and sets the items per page based on user preferences.
     *
     * @return void
     */
    protected function dtgNews_Create()
    {
        $this->dtgCategoryOfNewses = new NewsCategoriesTable($this);
        $this->dtgCategoryOfNewses_CreateColumns();
        $this->createPaginators();
        $this->dtgCategoryOfNewses_MakeEditable();
        $this->dtgCategoryOfNewses->RowParamsCallback = [$this, "dtgCategoryOfNewses_GetRowParams"];
        $this->dtgCategoryOfNewses->SortColumnIndex = 0;
        $this->dtgCategoryOfNewses->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();

    }

    /**
     * Creates columns for the category of news data grid.
     *
     * @return void
     */
    protected function dtgCategoryOfNewses_CreateColumns()
    {
        $this->dtgCategoryOfNewses->createColumns();
    }

    /**
     * Configures the category of news data grid to be editable by making its rows clickable.
     * It sets up an action for cell clicks to trigger an Ajax control event and applies
     * the necessary CSS classes to the table for styling and responsiveness.
     *
     * @return void
     */
    protected function dtgCategoryOfNewses_MakeEditable()
    {
        $this->dtgCategoryOfNewses->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgCategoryOfNewsesRow_Click'));
        $this->dtgCategoryOfNewses->addCssClass('clickable-rows');
        $this->dtgCategoryOfNewses->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event on a category of news row, updating the UI for editing the selected category.
     *
     * @param ActionParams $params An object containing parameters related to the action event, such as the selected row's identifier.
     * @return void
     */
    protected function dtgCategoryOfNewsesRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objCategoryNews = CategoryOfNews::load($this->intId);

        $this->oldName = $objCategoryNews->getName();

        $this->txtCategory->Text = $objCategoryNews->getName();
        $this->txtCategory->focus();
        $this->lstStatus->SelectedValue = $objCategoryNews->IsEnabled ?? null;

        $this->dtgCategoryOfNewses->addCssClass('disabled');
        $this->btnAddCategory->Enabled = false;
        $this->btnGoToNews->Display = false;
        $this->txtCategory->Display = true;
        $this->lstStatus->Display = true;
        $this->btnSave->Display = true;
        $this->btnDelete->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Retrieves the parameters for a row in the CategoryOfNewses data table.
     *
     * @param object $objRowObject The object representing the row, which contains the data attributes.
     * @param int $intRowIndex The index of the current row within the data table.
     * @return array An associative array containing parameters for the row, with 'data-value' as a key.
     */
    public function dtgCategoryOfNewses_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures the paginators for the data grid.
     *
     * The method sets up a new paginator with specific labels for navigation.
     * It also configures the items per page, the default sort column, and enables Ajax for dynamic updates.
     * Additionally, it adds filter actions to the data grid.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgCategoryOfNewses->Paginator = new Bs\Paginator($this);
        $this->dtgCategoryOfNewses->Paginator->LabelForPrevious = t('Previous');
        $this->dtgCategoryOfNewses->Paginator->LabelForNext = t('Next');

        $this->dtgCategoryOfNewses->ItemsPerPage = 10;
        $this->dtgCategoryOfNewses->SortColumnIndex = 4;
        $this->dtgCategoryOfNewses->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Initializes and configures a Select2 control for selecting the number of items per page.
     * Sets the required properties and event handling for changes in selection.
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
     * Retrieves a list of items per page associated with an assigned user object.
     *
     * @return ListItem[] An array of ListItem objects representing the items per page
     *                    for the assigned user. Each ListItem contains the display
     *                    text and associated ID, with the appropriate item marked
     *                    as selected based on the current user's assignment.
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
     * Handles the change event for the items per page list controlled by assigned user object.
     *
     * @param ActionParams $params The parameters for the action event that triggered this change.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgCategoryOfNewses->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgCategoryOfNewses->refresh();
    }

    /**
     * Initializes a text filter for search functionality.
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
     * Adds filter actions to the txtFilter control. Specifically, it registers AJAX actions
     * to be triggered on specific events, such as input and pressing the enter key. When
     * triggered, these actions invoke the filterChanged method, allowing dynamic filtering
     * functionality in the interface.
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
     * Handles the event when a filter is changed in the CategoryOfNewses data table.
     *
     * @return void This method refreshes the data table to reflect any changes in the filter settings.
     */
    protected function filterChanged()
    {
        $this->dtgCategoryOfNewses->refresh();
    }

    /**
     * Binds data to the data grid using specific conditions.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgCategoryOfNewses->bindData($objCondition);
    }

    /**
     * Constructs and returns a query condition based on the input from a filter text field.
     * The condition returned depends on whether the filter is empty or not.
     *
     * @return Q\Query\Condition The constructed query condition. If the filter is empty, it returns
     * a condition that matches all records. Otherwise, it constructs a condition that matches
     * records by ID or similar names based on the filter value.
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
                Q\Query\QQ::equal(QQN::CategoryOfNews()->Id, $strSearchValue),
                Q\Query\QQ::like(QQN::CategoryOfNews()->Name, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and creates various buttons and input controls for category management in the user interface.
     * Configures buttons with specific styles, actions, and visibility based on session conditions.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnAddCategory = new Bs\Button($this);
        $this->btnAddCategory->Text = t(' Create a new category');
        $this->btnAddCategory->Glyph = 'fa fa-plus';
        $this->btnAddCategory->CssClass = 'btn btn-orange';
        $this->btnAddCategory->addWrapperCssClass('center-button');
        $this->btnAddCategory->CausesValidation = false;
        $this->btnAddCategory->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddCategory_Click'));
        $this->btnAddCategory->setCssStyle('float', 'left');
        $this->btnAddCategory->setCssStyle('margin-right', '10px');

        $this->btnGoToNews = new Bs\Button($this);
        $this->btnGoToNews->Text = t('Go to this news');
        $this->btnGoToNews->addWrapperCssClass('center-button');
        $this->btnGoToNews->CssClass = 'btn btn-default';
        $this->btnGoToNews->CausesValidation = false;
        $this->btnGoToNews->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToNews_Click'));
        $this->btnGoToNews->setCssStyle('float', 'left');

        if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
            $this->btnGoToNews->Display = true;
        } else {
            $this->btnGoToNews->Display = false;
        }

        $this->txtCategory = new Bs\TextBox($this);
        $this->txtCategory->Placeholder = t('New category');
        $this->txtCategory->ActionParameter = $this->txtCategory->ControlId;
        $this->txtCategory->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtCategory->setHtmlAttribute('autocomplete', 'off');
        $this->txtCategory->setCssStyle('float', 'left');
        $this->txtCategory->setCssStyle('margin-right', '10px');
        $this->txtCategory->Width = 300;
        $this->txtCategory->Display = false;

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange form-horizontal radio-inline';
        $this->lstStatus->setCssStyle('float', 'left');
        $this->lstStatus->setCssStyle('margin-left', '15px');
        $this->lstStatus->setCssStyle('margin-right', '15px');
        $this->lstStatus->Display = false;

        $this->btnSaveCategory = new Bs\Button($this);
        $this->btnSaveCategory->Text = t('Save');
        $this->btnSaveCategory->CssClass = 'btn btn-orange';
        $this->btnSaveCategory->addWrapperCssClass('center-button');
        $this->btnSaveCategory->PrimaryButton = true;
        $this->btnSaveCategory->CausesValidation = true;
        $this->btnSaveCategory->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSaveCategory_Click'));
        $this->btnSaveCategory->setCssStyle('float', 'left');
        $this->btnSaveCategory->setCssStyle('margin-right', '10px');
        $this->btnSaveCategory->Display = false;

        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->btnSave->setCssStyle('float', 'left');
        $this->btnSave->setCssStyle('margin-right', '10px');
        $this->btnSave->Display = false;

        $this->btnDelete = new Bs\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->addWrapperCssClass('center-button');
        $this->btnDelete->CausesValidation = true;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnDelete_Click'));
        $this->btnDelete->setCssStyle('float', 'left');
        $this->btnDelete->setCssStyle('margin-right', '10px');
        $this->btnDelete->Display = false;

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->btnCancel->setCssStyle('float', 'left');
        $this->btnCancel->Display = false;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes two toastr notifications with predefined settings. The first
     * toastr is configured to display a success message, while the second toastr
     * is configured to display an error message. Both toastrs are displayed at
     * the top center of the screen and include a progress bar.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The category has been saved or modified.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('The category name must exist!');
        $this->dlgToastr2->ProgressBar = true;
    }

    /**
     * Creates and configures a series of modals for various user interactions
     * such as warnings, tips, and confirmations regarding news category actions.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the news category?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The news category cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this news category, 
                                    simply release any news categories previously associated with created news.</p>');
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The news category cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this category, 
                                    simply release any categories previously associated with created news.</p>');
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This category already exists! Please choose another new name!</p>');
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal5->Title = t("Warning");
        $this->dlgModal5->HeaderClasses = 'btn-danger';
        $this->dlgModal5->addCloseButton(t("I understand"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the "Add Category" button, updating the display and state of various UI components.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function btnAddCategory_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->btnGoToNews->Display = false;
        $this->txtCategory->Display = true;
        $this->lstStatus->Display = true;
        $this->lstStatus->SelectedValue = 2;
        $this->btnSaveCategory->Display = true;
        $this->btnCancel->Display = true;
        $this->txtCategory->Text = null;
        $this->txtCategory->focus();
        $this->btnAddCategory->Enabled = false;
        $this->dtgCategoryOfNewses->addCssClass('disabled');
    }

    /**
     * Handles the save operation for a new category when the save button is clicked.
     * Validates the category name and ensures it is not a duplicate before saving.
     * Toggles UI element visibility and states based on the operation's success or failure.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    protected function btnSaveCategory_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtCategory->Text) {
            if (!in_array(trim(strtolower($this->txtCategory->Text)), $this->objCategoryNames)) {

                $objCategoryNews = new CategoryOfNews();
                $objCategoryNews->setName(trim($this->txtCategory->Text));
                $objCategoryNews->setIsEnabled($this->lstStatus->SelectedValue);
                $objCategoryNews->setPostDate(Q\QDateTime::Now());
                $objCategoryNews->save();

                $this->dtgCategoryOfNewses->refresh();

                if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                    $this->btnGoToNews->Display = true;
                }

                $this->txtCategory->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSaveCategory->Display = false;
                $this->btnCancel->Display = false;
                $this->btnAddCategory->Enabled = true;
                $this->dtgCategoryOfNewses->removeCssClass('disabled');
                $this->txtCategory->Text = null;
                $this->dlgToastr1->notify();

            } else {
                $this->txtCategory->Text = null;
                $this->txtCategory->focus();
                $this->dlgModal4->showDialogBox();
            }

        } else {
            $this->txtCategory->Text = null;
            $this->txtCategory->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the click event for the save button, performing various operations
     * based on the current state of the category of news being edited or added.
     *
     * @param ActionParams $params The parameters associated with the save button's action event.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objCategoryOfNews = CategoryOfNews::loadById($this->intId);

        if ($this->txtCategory->Text) {
            if (in_array($this->intId, $this->objCategoryIds) && $this->lstStatus->SelectedValue == 2) {
                $this->lstStatus->SelectedValue = 1;
                $this->dlgModal2->showDialogBox();

                if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                    $this->btnGoToNews->Display = true;
                }

                $this->btnAddCategory->Enabled = true;
                $this->txtCategory->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;
                $this->dtgCategoryOfNewses->removeCssClass('disabled');

            } else if ($this->txtCategory->Text == $objCategoryOfNews->getName() && $this->lstStatus->SelectedValue !== $objCategoryOfNews->getIsEnabled()  ||
                $this->txtCategory->Text !== $objCategoryOfNews->getName() && !in_array(trim(strtolower($this->txtCategory->Text)), $this->objCategoryNames)) {

                $objCategoryOfNews->setName(trim($this->txtCategory->Text));
                $objCategoryOfNews->setIsEnabled($this->lstStatus->SelectedValue);
                $objCategoryOfNews->setPostUpdateDate(Q\QDateTime::Now());
                $objCategoryOfNews->save();

                $this->dtgCategoryOfNewses->refresh();
                $this->btnAddCategory->Enabled = true;

                if (!empty($_SESSION['news_categories_']) || !empty($_SESSION['news_categories_group'])) {
                    $this->btnGoToNews->Display = true;
                }

                $this->txtCategory->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;

                $this->dtgCategoryOfNewses->removeCssClass('disabled');
                $this->txtCategory->Text = $objCategoryOfNews->getName();
                $this->dlgToastr1->notify();


            } else if (in_array(trim(strtolower($this->txtCategory->Text)), $this->objCategoryNames)) {
                $this->txtCategory->Text = $objCategoryOfNews->getName();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtCategory->Text = $objCategoryOfNews->getName();
            $this->txtCategory->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the click event for the delete button. Determines the state of various UI components and displays
     * the appropriate dialog based on whether the category ID is present in the category IDs list.
     *
     * @param ActionParams $params The parameters associated with the button click event.
     * @return void
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (in_array($this->intId, $this->objCategoryIds)) {
            $this->dlgModal3->showDialogBox();

            if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
                $this->btnGoToNews->Display = true;
            }

            $this->btnAddCategory->Enabled = true;
            $this->txtCategory->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgCategoryOfNewses->removeCssClass('disabled');

        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the click event for deleting a category of news item.
     *
     * @param ActionParams $params The parameters associated with the action, including the action parameter.
     * @return void
     */
    public function deleteItem_Click(ActionParams $params)
    {
        $objCategoryOfNews = CategoryOfNews::loadById($this->intId);

        if ($params->ActionParameter == "pass") {
            $objCategoryOfNews->delete();
        }

        $this->dtgCategoryOfNewses->refresh();
        $this->btnAddCategory->Enabled = true;
        $this->txtCategory->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgCategoryOfNewses->removeCssClass('disabled');
        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the click event of the cancel button. It resets the display states
     * of various UI components and clears the category text input. Additionally,
     * it manages the enabled/disabled state of the add category button and the
     * CSS class of the news categories datagrid.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     * @return void
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
            $this->btnGoToNews->Display = true;
        }

        $this->txtCategory->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSaveCategory->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddCategory->Enabled = true;
        $this->dtgCategoryOfNewses->removeCssClass('disabled');
        $this->txtCategory->Text = null;
    }

    /**
     * Handles the click event for the "Go To News" button. Redirects the user to the news edit page
     * with the appropriate news category ID and group retrieved from the session variables. Clears the
     * session variables after redirection.
     *
     * @param ActionParams $params The parameters associated with the button click action.
     * @return void
     */
    protected function btnGoToNews_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['news_categories_id']) || !empty($_SESSION['news_categories_group'])) {
            $news = $_SESSION['news_categories_id'];
            $group = $_SESSION['news_categories_group'];

            Application::redirect('news_edit.php?id=' . $news . '&group=' . $group);
            unset($_SESSION['news_categories_id']);
            unset($_SESSION['news_categories_group']);
        }
    }

    /**
     * Checks the categories associated with each news item and populates the list of category IDs.
     *
     * Iterates over all loaded news items and captures the category ID for each item if available.
     * Populates the internal array with these category IDs.
     *
     * @return void
     */
    private function CheckCategories()
    {
        $objNewsArray = News::loadAll();

        foreach ($objNewsArray as $objNews) {
            if ($objNews->getNewsCategoryId()) {
                $this->objCategoryIds[] = $objNews->getNewsCategoryId();
            }
        }
    }

    /**
     * Processes and stores the names of all news categories in lowercase.
     *
     * @return void
     */
    private function NewsCategoryNames()
    {
        $objCategories = CategoryOfNews::loadAll();

        foreach ($objCategories as $objCategory) {
            if ($objCategory->getName()) {
                $this->objCategoryNames[] = strtolower($objCategory->getName());
            }
        }
    }
}