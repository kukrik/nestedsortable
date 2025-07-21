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

class ArticleCategoriesManager extends Q\Control\Panel
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
    public $dlgModal6;

    public $txtFilter;
    public $dtgCategoryOfArticle;

    public $btnAddCategory;
    public $btnGoToArticle;
    public $txtCategory;
    public $lstStatus;
    public $btnSaveCategory;
    public $btnSave;
    public $btnDelete;
    public $btnCancel;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $oldName;

    protected $objCategoryOfArticle;
    protected $objCategoryIds = [];
    protected $objCategoryNames = [];
    protected $objCompressTexts = [];
    protected $objMenuTexts;

    protected $strTemplate = 'ArticleCategoriesManager.tpl.php';

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

        //$this->intId = Application::instance()->context()->queryStringItem('id');
        //$this->objCategoryOfArticle = CategoryOfArticle::load($this->intId);

        /**
         * NOTE: if the user_id is stored in session (e.g. if a User is logged in), as well, for example:
         * checking against user session etc.
         *
         * Must to save something here $this->objNews->setUserId(logged user session);
         * or something similar...
         *
         * Options to do this are left to the developer.
         **/

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgCategoryOfArticle_Create();
        $this->dtgCategoryOfArticle->setDataBinder('BindData', $this);
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->CheckCategories();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the Article Categories Table with columns, paginators,
     * and editability settings, and configures row parameters and sorting.
     *
     * @return void
     **/
    protected function dtgCategoryOfArticle_Create()
    {
        $this->dtgCategoryOfArticle = new ArticleCategoriesTable($this);
        $this->dtgCategoryOfArticle_CreateColumns();
        $this->createPaginators();
        $this->dtgCategoryOfArticle_MakeEditable();
        $this->dtgCategoryOfArticle->RowParamsCallback = [$this, "dtgCategoryOfArticle_GetRowParams"];
        $this->dtgCategoryOfArticle->SortColumnIndex = 0;
        $this->dtgCategoryOfArticle->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Initializes and creates columns for the CategoryOfArticle data grid.
     *
     * This method is responsible for setting up the necessary columns
     * in the data grid that displays categories of articles. It delegates
     * the actual creation of columns to the `createColumns` method
     * of the data grid object.
     *
     * @return void
     */
    protected function dtgCategoryOfArticle_CreateColumns()
    {
        $this->dtgCategoryOfArticle->createColumns();
    }

    /**
     * Configures the CategoryOfArticle data grid to be editable through user interactions.
     *
     * This method sets up the data grid to respond to cell click events by linking the event
     * to the specified AJAX control action. It also modifies the CSS classes to ensure
     * that the data grid rows are visually distinct and responsive, enhancing the user
     * interface for interactivity.
     *
     * @return void
     */
    protected function dtgCategoryOfArticle_MakeEditable()
    {
        $this->dtgCategoryOfArticle->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgCategoryOfArticleRow_Click'));
        $this->dtgCategoryOfArticle->addCssClass('clickable-rows');
        $this->dtgCategoryOfArticle->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click action on the CategoryOfArticle data grid row.
     *
     * This method is triggered when a user clicks on a row in the CategoryOfArticle
     * data grid. It loads the corresponding CategoryOfArticle record based on the
     * provided action parameter, updates the UI controls with the category's details,
     * and adjusts the visibility and state of several buttons and inputs.
     *
     * @param ActionParams $params The parameters associated with the click action,
     *                             including the identifier for the selected category.
     *
     * @return void
     */
    protected function dtgCategoryOfArticleRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objCategoryOfArticle = CategoryOfArticle::load($this->intId);

        $this->oldName = $objCategoryOfArticle->getName();

        $this->txtCategory->Text = $objCategoryOfArticle->getName();
        $this->txtCategory->focus();
        $this->lstStatus->SelectedValue = $objCategoryOfArticle->IsEnabled ?? null;

        $this->dtgCategoryOfArticle->addCssClass('disabled');
        $this->btnAddCategory->Enabled = false;
        $this->btnGoToArticle->Display = false;
        $this->txtCategory->Display = true;
        $this->lstStatus->Display = true;
        $this->btnSave->Display = true;
        $this->btnDelete->Display = true;
        $this->btnCancel->Display = true;

        $this->oldName = $objCategoryOfArticle->getName();

        $this->txtCategory->Text = $objCategoryOfArticle->getName();
        $this->txtCategory->focus();
        $this->lstStatus->SelectedValue = $objCategoryOfArticle->IsEnabled ?? null;

        $this->dtgCategoryOfArticle->addCssClass('disabled');
        $this->btnAddCategory->Enabled = false;
        $this->btnGoToArticle->Display = false;
        $this->txtCategory->Display = true;
        $this->lstStatus->Display = true;
        $this->btnSave->Display = true;
        $this->btnDelete->Display = true;
        $this->btnCancel->Display = true;

        $this->oldName = $objCategoryOfArticle->getName();

        $this->txtCategory->Text = $objCategoryOfArticle->getName();
        $this->txtCategory->focus();
        $this->lstStatus->SelectedValue = $objCategoryOfArticle->IsEnabled ?? null;

        $this->dtgCategoryOfArticle->addCssClass('disabled');
        $this->btnAddCategory->Enabled = false;
        $this->btnGoToArticle->Display = false;
        $this->txtCategory->Display = true;
        $this->lstStatus->Display = true;
        $this->btnSave->Display = true;
        $this->btnDelete->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Retrieves parameters for a specific row in the CategoryOfArticle data grid.
     *
     * This method extracts the primary key from the given row object and uses it
     * to set a data attribute in the returned parameters array. This is typically
     * used to provide additional metadata for the row, which can be utilized in
     * front-end interactions or data tracking.
     *
     * @param object $objRowObject The row object from which the primary key is extracted.
     * @param int $intRowIndex The index of the row in the data grid.
     *
     * @return array An associative array of parameters where 'data-value' is set
     *               to the primary key of the row object.
     */
    public function dtgCategoryOfArticle_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Configures and initializes paginators for the CategoryOfArticle data grid.
     *
     * This method sets up the paginators for navigating the data grid,
     * assigning labels for the navigation controls. It also sets the number of items per page,
     * specifies the default sort column, and enables AJAX for seamless data grid interactions.
     * Additional filter actions are added to enhance the grid functionality.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgCategoryOfArticle->Paginator = new Bs\Paginator($this);
        $this->dtgCategoryOfArticle->Paginator->LabelForPrevious = t('Previous');
        $this->dtgCategoryOfArticle->Paginator->LabelForNext = t('Next');

        $this->dtgCategoryOfArticle->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgCategoryOfArticle->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgCategoryOfArticle->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgCategoryOfArticle->ItemsPerPage = 10;
        $this->dtgCategoryOfArticle->SortColumnIndex = 4;
        $this->dtgCategoryOfArticle->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Configures and initializes the items per page selection control.
     *
     * This method sets up a Select2 widget for choosing the number of items
     * displayed per page, tailored for the assigned user. It configures visual
     * and functional properties of the widget, populates it with available
     * options, and binds a change event handler.
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
     * Retrieves a list of items per page, filtered by the assigned user's object condition.
     *
     * @return ListItem[] An array of ListItem objects representing the items per page associated with the assigned user. Each ListItem will have its 'Selected' property set to true if it matches the user's current assignment.
     */
    public function lstItemsPerPageByAssignedUserObject_GetItems()
    {
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
     * Updates the items per page for the category of article data grid based on the selected value from the list of items per page by the assigned user object and refreshes the data grid.
     *
     * @param ActionParams $params The parameters containing action-related data that may be used to determine how the change should be handled.
     * @return void This method does not return any value.
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgCategoryOfArticle->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgCategoryOfArticle->refresh();
    }

    /**
     * Initializes a search filter as a text input field with specific attributes for user interaction.
     *
     * @return void This method does not return a value. It configures a text box for search functionality, setting attributes such as placeholder text and autocomplete, and applies CSS classes for styling. Additionally, it invokes actions to handle filter events.
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
     * Adds filter actions to the text filter input control. These actions facilitate dynamic filtering
     * based on user input and interaction with the filter's UI.
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
     * Refreshes the data grid displaying the category of articles when the filter is changed.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgCategoryOfArticle->refresh();
    }

    /**
     * Binds data to the dtgCategoryOfArticle data grid based on a specified condition.
     *
     * @return void This method does not return a value but updates the data grid with the relevant data according to the provided condition.
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgCategoryOfArticle->bindData($objCondition);
    }

    /**
     * Generates a condition based on the user's search input for filtering categories of articles.
     *
     * @return Q\Query\QQ The condition object to be used for querying categories. If the search input is empty,
     * returns a condition that matches all categories. Otherwise, returns a condition that matches categories
     * by ID or name based on the search input.
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
                Q\Query\QQ::equal(QQN::CategoryOfArticle()->Id, $strSearchValue),
                Q\Query\QQ::like(QQN::CategoryOfArticle()->Name, "%" . $strSearchValue . "%")

            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a set of buttons and other UI controls for managing categories and articles.
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

        $this->btnGoToArticle = new Bs\Button($this);
        $this->btnGoToArticle->Text = t('Go to this article');
        $this->btnGoToArticle->addWrapperCssClass('center-button');
        $this->btnGoToArticle->CssClass = 'btn btn-default';
        $this->btnGoToArticle->CausesValidation = false;
        $this->btnGoToArticle->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToArticle_Click'));
        $this->btnGoToArticle->setCssStyle('float', 'left');

        if (!empty($_SESSION['article'])) {
            $this->btnGoToArticle->Display = true;
        } else {
            $this->btnGoToArticle->Display = false;
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

    /**
     * Initializes and configures two Toastr notifications for different alert types.
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
        $this->dlgToastr2->Message = t('Tthe category name must exist!');
        $this->dlgToastr2->ProgressBar = true;
    }

    /**
     * Initializes and configures a set of modal dialogs used for different user interactions.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the article category?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hide_Click'));


        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss' => 'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            [ 'class' => 'btn btn-orange']);
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'hide_Click'));
        $this->dlgModal3->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hide_Click'));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t('Warning');
        $this->dlgModal4->HeaderClasses = 'btn-danger';
        $this->dlgModal4->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal4->addCloseButton(t("I'll cancel"));
        $this->dlgModal4->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'categoryItem_Click'));
        $this->dlgModal4->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hide_Click'));

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Title = t("Tip");
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This category already exists! Please choose another new name!</p>');
        $this->dlgModal5->HeaderClasses = 'btn-darkblue';
        $this->dlgModal5->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal6->Title = t("Warning");
        $this->dlgModal6->HeaderClasses = 'btn-danger';
        $this->dlgModal6->addCloseButton(t("I understand"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the "Add Category" button. This method prepares the UI for adding a new category by updating display properties and resetting relevant fields.
     *
     * @return void
     */
    protected function btnAddCategory_Click()
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->btnGoToArticle->Display = false;
        $this->txtCategory->Display = true;
        $this->lstStatus->Display = true;
        $this->lstStatus->SelectedValue = 2;
        $this->btnSaveCategory->Display = true;
        $this->btnCancel->Display = true;
        $this->txtCategory->Text = null;
        $this->txtCategory->focus();
        $this->btnAddCategory->Enabled = false;
        $this->dtgCategoryOfArticle->addCssClass('disabled');
    }

    /**
     * Handles the save operation when the "Save Category" button is clicked.
     * Validates the input category name to ensure it is not already taken,
     * then creates a new category and updates the display accordingly.
     *
     * @param ActionParams $params The parameters associated with the button click event.
     * @return void
     */
    protected function btnSaveCategory_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtCategory->Text) {
            if (!in_array(trim(strtolower($this->txtCategory->Text)), $this->objCategoryNames)) {

                $objCategoryNews = new CategoryOfArticle();
                $objCategoryNews->setName(trim($this->txtCategory->Text));
                $objCategoryNews->setIsEnabled($this->lstStatus->SelectedValue);
                $objCategoryNews->setPostDate(Q\QDateTime::Now());
                $objCategoryNews->save();

                $this->dtgCategoryOfArticle->refresh();

                if (!empty($_SESSION['article'])) {
                    $this->btnGoToArticle->Display = true;
                }

                unset($this->objCategoryNames);

                $this->txtCategory->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSaveCategory->Display = false;
                $this->btnCancel->Display = false;
                $this->btnAddCategory->Enabled = true;
                $this->dtgCategoryOfArticle->removeCssClass('disabled');
                $this->txtCategory->Text = null;
                $this->dlgToastr1->notify();
            } else {
                $this->txtCategory->Text = null;
                $this->txtCategory->focus();
                $this->dlgModal5->showDialogBox();
            }
        } else {
            $this->txtCategory->Text = null;
            $this->txtCategory->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the save button click event, processing the current article category and its related articles.
     *
     * @param ActionParams $params Parameters related to the button click action.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objArticleArray = Article::loadAll();
        $objCategoryOfArticle = CategoryOfArticle::loadById($this->intId);

        foreach ($objArticleArray as $objArticle) {
            if ($objArticle->CategoryId == $this->intId) {
                $this->objCompressTexts[] = $objArticle->Title;
            }
        }

        $this->objMenuTexts = implode(', ', $this->objCompressTexts);

        if ($this->txtCategory->Text) {
            if (in_array($this->intId, $this->objCategoryIds) && $this->lstStatus->SelectedValue == 2) {
                $this->lstStatus->SelectedValue = 1;

                $this->dlgModal3->showDialogBox();
                $this->dlgModal3->Text = t('<p style="line-height: 25px;">The article category cannot be deactivated at this time!</p>
                                            <p style="line-height: 25px;">Articles related to the category: <span style="color: #ff0000;">
                                    ' . $this->objMenuTexts . '</span>.</p>
                                            <p style="line-height: 25px; margin-bottom: -3px;">To deactivate this article category,
                                            just must release article categories related to previously created article.</p>');

                if (!empty($_SESSION['article'])) {
                    $this->btnGoToArticle->Display = false;
                }

            } else if ($this->lstStatus->SelectedValue == $objCategoryOfArticle->getIsEnabled() && $this->txtCategory->Text !== $objCategoryOfArticle->getName() && in_array($this->intId, $this->objCategoryIds)) {
                $this->dlgModal4->showDialogBox();
                $this->dlgModal4->Text = t('<p style="line-height: 25px">Are you sure you want to rename the article category?</p>
                                    <p style="line-height: 25px;">Articles related to the category: <span style="color: #ff0000;">
                                    ' . $this->objMenuTexts . '</span>.</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Renaming this category will update the category for all articles associated with it! </p>');

                if (!empty($_SESSION['article'])) {
                    $this->btnGoToArticle->Display = false;
                }

            } else if ($this->txtCategory->Text == $objCategoryOfArticle->getName() && $this->lstStatus->SelectedValue !== $objCategoryOfArticle->getIsEnabled() ||
                $this->txtCategory->Text !== $objCategoryOfArticle->getName() && !in_array(trim(strtolower($this->txtCategory->Text)), $this->objCategoryNames)) {

                $objCategoryOfArticle->setName(trim($this->txtCategory->Text));
                $objCategoryOfArticle->setIsEnabled($this->lstStatus->SelectedValue);
                $objCategoryOfArticle->setPostUpdateDate(Q\QDateTime::Now());
                $objCategoryOfArticle->save();

                $this->dtgCategoryOfArticle->refresh();
                $this->btnAddCategory->Enabled = true;

                if (!empty($_SESSION['article'])) {
                    $this->btnGoToArticle->Display = true;
                }

                $this->txtCategory->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;

                $this->dtgCategoryOfArticle->removeCssClass('disabled');
                $this->txtCategory->Text = $objCategoryOfArticle->getName();
                $this->dlgToastr1->notify();
            }
        } else {
            $this->txtCategory->Text = $objCategoryOfArticle->getName();
            $this->txtCategory->focus();
            $this->dlgToastr2->notify();
        }

        unset($this->objCompressTexts);
    }

    /**
     * Handles the click event for a category item, updating the category details and managing UI components.
     *
     * @param ActionParams $params Parameters associated with the action, including any event-specific data needed for processing.
     * @return void No direct return value, but updates the UI state and persists changes to the category.
     */
    public function categoryItem_Click(ActionParams $params)
    {
        $objCategoryOfArticle = CategoryOfArticle::loadById($this->intId);
        $objCategoryOfArticle->setName(trim($this->txtCategory->Text));
        $objCategoryOfArticle->setIsEnabled($this->lstStatus->SelectedValue);
        $objCategoryOfArticle->setPostUpdateDate(Q\QDateTime::Now());
        $objCategoryOfArticle->save();

        if (!empty($_SESSION['article'])) {
            $this->btnGoToArticle->Display = true;
        }

        $this->btnAddCategory->Enabled = true;
        $this->txtCategory->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
        $this->dtgCategoryOfArticle->removeCssClass('disabled');

        $this->dlgModal4->hideDialogBox();
        $this->dlgToastr1->notify();

        unset($this->objCompressTexts);
    }

    /**
     * Handles the click event for hiding certain UI elements and resetting specific controls within the application interface.
     *
     * @param ActionParams $params The parameters associated with the action event triggering the click.
     * @return void
     */
    public function hide_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['article'])) {
            $this->btnGoToArticle->Display = true;
        }

        $this->btnAddCategory->Enabled = true;
        $this->txtCategory->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
        $this->dtgCategoryOfArticle->removeCssClass('disabled');

        $this->dlgModal1->hideDialogBox();
        $this->dlgModal3->hideDialogBox();
        $this->dlgModal4->hideDialogBox();
        unset($this->objCompressTexts);
    }

    /**
     * Handles the click event for the delete button. Checks if the selected article category can be deleted
     * by verifying its association with existing articles. If associated, displays a modal dialog informing
     * the user of the related articles and restricts deletion. Otherwise, shows a confirmation dialog.
     *
     * @param ActionParams $params The parameters associated with the action triggering this method.
     * @return void
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objArticleArray = Article::loadAll();

        foreach ($objArticleArray as $objArticle) {
            if ($objArticle->CategoryId == $this->intId) {
                $this->objCompressTexts[] = $objArticle->Title;
            }
        }

        $this->objMenuTexts = implode(', ', $this->objCompressTexts);

        if (in_array($this->intId, $this->objCategoryIds)) {
            $this->dlgModal2->showDialogBox();
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The article category cannot be
                                    deleted at this time!</p>
                                    <p style="line-height: 15px;">Articles related to the category: <span style="color: #ff0000;">
                                    ' . $this->objMenuTexts . '</span>.</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this category, just
                                    must release categories related to previously created articles.</p>');

            if (!empty($_SESSION['article'])) {
                $this->btnGoToArticle->Display = true;
            }

            unset($this->objCompressTexts);

            $this->btnAddCategory->Enabled = true;
            $this->txtCategory->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgCategoryOfArticle->removeCssClass('disabled');

        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the click event for deleting an item from the category of articles.
     *
     * @param ActionParams $params The parameters associated with the action, including the action parameter that determines if the item should be deleted.
     * @return void This method does not return a value but performs actions such as deleting a category if specified and updating the UI components accordingly.
     */
    public function deleteItem_Click(ActionParams $params)
    {
        $objCategoryOfArticle = CategoryOfArticle::loadById($this->intId);

        if ($params->ActionParameter == "pass") {
            $objCategoryOfArticle->delete();
        }

        $this->dtgCategoryOfArticle->refresh();
        $this->btnAddCategory->Enabled = true;
        $this->txtCategory->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgCategoryOfArticle->removeCssClass('disabled');
        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the cancel button click event, performing cleanup and UI updates.
     *
     * @param ActionParams $params The parameters provided by the action triggering the button click.
     * @return void
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->txtCategory->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSaveCategory->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddCategory->Enabled = true;
        $this->dtgCategoryOfArticle->removeCssClass('disabled');
        $this->txtCategory->Text = null;

        if (!empty($_SESSION['article'])) {
            $this->btnGoToArticle->Display = true;
        }
    }

    /**
     * Handles the button click event to redirect the user to the appropriate edit menu page
     * based on the session data available.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void This method does not return a value. It performs a redirect operation.
     */
    protected function btnGoToArticle_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal6->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['news_group'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['news_group']);
            unset($_SESSION['news_group']);
        } else if (!empty($_SESSION['gallery_group'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['gallery_group']);
            unset($_SESSION['gallery_group']);
        } else if (!empty($_SESSION['article'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['article']);
            unset($_SESSION['article']);
        } else if (!empty($_SESSION['news']) || !empty($_SESSION['group'])) {
            Application::redirect('news_edit.php?id=' . $_SESSION['news']) . '&group=' . $_SESSION['group'];
            unset($_SESSION['news']);
            unset($_SESSION['group']);
        }
    }

    /**
     * Checks all articles and categories, storing associated category IDs and category names.
     *
     * @return void This method populates the objCategoryIds property with category IDs associated with articles
     * and the objCategoryNames property with the lowercase names of all categories.
     */
    public function CheckCategories()
    {
        $objArticleArray = Article::loadAll();
        $objCategoryArray = CategoryOfArticle::loadAll();

        foreach ($objArticleArray as $objArticle) {
            if ($objArticle->CategoryId) {
                $this->objCategoryIds[] = $objArticle->CategoryId;
            }
        }

        foreach ($objCategoryArray as $objCategory) {
            $this->objCategoryNames[] = strtolower($objCategory->Name);
        }
    }
}