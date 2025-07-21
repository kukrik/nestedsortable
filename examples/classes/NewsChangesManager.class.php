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

class NewsChangesManager extends Q\Control\Panel
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
    public $dtgNewsChanges;

    public $btnAddChange;
    public $btnGoToNews;
    public $txtChange;
    public $lstStatus;
    public $btnSaveChange;
    public $btnSave;
    public $btnDelete;
    public $btnCancel;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $objChangeNames = [];
    protected $objChangeIds = [];
    protected $oldName;

    protected $strTemplate = 'NewsChangesManager.tpl.php';

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
        $this->dtgNewsChanges_Create();
        $this->dtgNewsChanges->setDataBinder('BindData', $this);
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->NewsChangeNames();
        $this->CheckChanges();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the news changes data table, defining its columns, pagination,
     * and editability features, as well as configuring its row parameters and
     * sorting behavior.
     *
     * @return void
     */
    protected function dtgNewsChanges_Create()
    {
        $this->dtgNewsChanges = new NewsChangesTable($this);
        $this->dtgNewsChanges_CreateColumns();
        $this->createPaginators();
        $this->dtgNewsChanges_MakeEditable();
        $this->dtgNewsChanges->RowParamsCallback = [$this, "dtgNewsChanges_GetRowParams"];
        $this->dtgNewsChanges->SortColumnIndex = 0;
        $this->dtgNewsChanges->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Initializes and creates columns for the dtgNewsChanges component.
     *
     * @return void This method does not return a value.
     */
    protected function dtgNewsChanges_CreateColumns()
    {
        $this->dtgNewsChanges->createColumns();
    }

    /**
     * Configures the datagrid for news changes to be editable by adding an action
     * that triggers a click event and adds relevant CSS classes for styling.
     *
     * @return void
     */
    protected function dtgNewsChanges_MakeEditable()
    {
        $this->dtgNewsChanges->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgNewsChangesRow_Click'));
        $this->dtgNewsChanges->addCssClass('clickable-rows');
        $this->dtgNewsChanges->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event for a row in the news changes data grid.
     * Loads the news change item based on the provided action parameters,
     * initializing form controls with the item's data and updating UI elements
     * accordingly.
     *
     * @param ActionParams $params Contains the action parameter indicating
     *                             which row was clicked, specifically the ID of
     *                             the news change item.
     *
     * @return void
     */
    protected function dtgNewsChangesRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objNewsChanges = NewsChanges::load($this->intId);

        $this->oldName = $objNewsChanges->getTitle();

        $this->txtChange->Text = $objNewsChanges->getTitle();
        $this->txtChange->focus();
        $this->lstStatus->SelectedValue = $objNewsChanges->Status ?? null;

        $this->dtgNewsChanges->addCssClass('disabled');
        $this->btnAddChange->Enabled = false;
        $this->btnGoToNews->Display = false;
        $this->txtChange->Display = true;
        $this->lstStatus->Display = true;
        $this->btnSave->Display = true;
        $this->btnDelete->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Retrieves parameters for a row in the news changes data grid.
     *
     * @param object $objRowObject The object representing the row in the data grid.
     * @param int $intRowIndex The index of the row in the data grid.
     * @return array An associative array containing parameters for the row, including a 'data-value' key set to the row's primary key.
     */
    public function dtgNewsChanges_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Initializes and configures paginators for the news changes data grid component.
     * The paginators are set up with labels for navigation and specific pagination settings.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgNewsChanges->Paginator = new Bs\Paginator($this);
        $this->dtgNewsChanges->Paginator->LabelForPrevious = t('Previous');
        $this->dtgNewsChanges->Paginator->LabelForNext = t('Next');

        $this->dtgNewsChanges->ItemsPerPage = 10;
        $this->dtgNewsChanges->SortColumnIndex = 4;
        $this->dtgNewsChanges->UseAjax = true;
        $this->addFilterActions();
    }

    /**
     * Initializes and configures the items-per-page selection component for the assigned user object.
     * It sets display properties like theme, width, and selection mode, and populates it with items.
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
     * Retrieves a list of list items representing each `ItemsPerPage` object associated with the assigned user object.
     *
     * Iterates through the `ItemsPerPage` objects retrieved by the query and creates a `ListItem` for each.
     * If the current `ItemsPerPage` object matches the one associated with the user, it is marked as selected.
     *
     * @return ListItem[] An array of `ListItem` objects based on the `ItemsPerPage` associated objects.
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
     * Updates the items per page for a data grid based on the user's selection and refreshes the grid.
     *
     * @param ActionParams $params The parameters passed from the action triggering the change in items per page.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgNewsChanges->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgNewsChanges->refresh();
    }

    /**
     * Initializes and configures the search filter text box component.
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
     * Adds filter actions to the text filter input control.
     *
     * This method assigns AJAX-based actions to respond to user interactions with the filter input.
     * An input event with a delay is added to trigger the 'filterChanged' method asynchronously.
     * An additional action array is added to handle when the Enter key is pressed, which triggers
     * the 'FilterChanged' method and then terminates further event propagation.
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
     * Refreshes the data grid for news changes when the filter criteria is modified.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgNewsChanges->refresh();
    }

    /**
     * Binds data to the data grid using the specified condition.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgNewsChanges->bindData($objCondition);
    }

    /**
     * Constructs and returns a query condition based on the current filter input.
     *
     * @return \Q\Query\QQ The query condition to apply, which may either be for retrieving all records
     * or filtering based on the specified search criteria.
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
                Q\Query\QQ::equal(QQN::NewsChanges()->Id, $strSearchValue),
                Q\Query\QQ::like(QQN::NewsChanges()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and configures various buttons and controls for managing changes,
     * including add, save, delete, and cancel operations, as well as input and status controls.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnAddChange = new Bs\Button($this);
        $this->btnAddChange->Text = t(' Create a new change');
        $this->btnAddChange->Glyph = 'fa fa-plus';
        $this->btnAddChange->CssClass = 'btn btn-orange';
        $this->btnAddChange->addWrapperCssClass('center-button');
        $this->btnAddChange->CausesValidation = false;
        $this->btnAddChange->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddChange_Click'));
        $this->btnAddChange->setCssStyle('float', 'left');
        $this->btnAddChange->setCssStyle('margin-right', '10px');

        $this->btnGoToNews = new Bs\Button($this);
        $this->btnGoToNews->Text = t('Go to this news');
        $this->btnGoToNews->addWrapperCssClass('center-button');
        $this->btnGoToNews->CssClass = 'btn btn-default';
        $this->btnGoToNews->CausesValidation = false;
        $this->btnGoToNews->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToNews_Click'));
        $this->btnGoToNews->setCssStyle('float', 'left');

        if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
            $this->btnGoToNews->Display = true;
        } else {
            $this->btnGoToNews->Display = false;
        }

        $this->txtChange = new Bs\TextBox($this);
        $this->txtChange->Placeholder = t('New change');
        $this->txtChange->ActionParameter = $this->txtChange->ControlId;
        $this->txtChange->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtChange->setHtmlAttribute('autocomplete', 'off');
        $this->txtChange->setCssStyle('float', 'left');
        $this->txtChange->setCssStyle('margin-right', '10px');
        $this->txtChange->Width = 300;
        $this->txtChange->Display = false;

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange form-horizontal radio-inline';
        $this->lstStatus->setCssStyle('float', 'left');
        $this->lstStatus->setCssStyle('margin-left', '15px');
        $this->lstStatus->setCssStyle('margin-right', '15px');
        $this->lstStatus->Display = false;

        $this->btnSaveChange = new Bs\Button($this);
        $this->btnSaveChange->Text = t('Save');
        $this->btnSaveChange->CssClass = 'btn btn-orange';
        $this->btnSaveChange->addWrapperCssClass('center-button');
        $this->btnSaveChange->PrimaryButton = true;
        $this->btnSaveChange->CausesValidation = true;
        $this->btnSaveChange->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSaveChange_Click'));
        $this->btnSaveChange->setCssStyle('float', 'left');
        $this->btnSaveChange->setCssStyle('margin-right', '10px');
        $this->btnSaveChange->Display = false;

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
     * Initializes and configures two Toastr notification instances for success and error alerts.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The change has been saved or modified.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('The change name must exist!');
        $this->dlgToastr2->ProgressBar = true;
    }

    /**
     * Creates and initializes several modal dialogs with specific titles, texts, headers, and buttons.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the news change?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The news change cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this news change, 
                                    simply release any news changes previously associated with created news.</p>');
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The news change cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this change, 
                                    simply release any changes previously associated with created news.</p>');
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This change already exists! Please choose another new name!</p>');
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
     * Handles the event when the Add/Change button is clicked. This method updates the UI components
     * to enable editing a change, by displaying and enabling relevant input fields and buttons while
     * disabling the Add/Change button. It also sets the selected status to a default value.
     *
     * @param ActionParams $params Parameters provided by the action event.
     * @return void
     */
    protected function btnAddChange_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->btnGoToNews->Display = false;
        $this->txtChange->Display = true;
        $this->lstStatus->Display = true;
        $this->lstStatus->SelectedValue = 2;
        $this->btnSaveChange->Display = true;
        $this->btnCancel->Display = true;
        $this->txtChange->Text = null;
        $this->txtChange->focus();
        $this->btnAddChange->Enabled = false;
        $this->dtgNewsChanges->addCssClass('disabled');
    }

    /**
     * Handles the logic for saving changes when the save button is clicked.
     *
     * @param ActionParams $params The parameters associated with the action triggered.
     *
     * @return void
     */
    protected function btnSaveChange_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtChange->Text) {
            if (!in_array(trim(strtolower($this->txtChange->Text)), $this->objChangeNames)) {

                $objCategoryNews = new NewsChanges();
                $objCategoryNews->setTitle(trim($this->txtChange->Text));
                $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                $objCategoryNews->setPostDate(Q\QDateTime::Now());
                $objCategoryNews->save();

                $this->dtgNewsChanges->refresh();

                if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                    $this->btnGoToNews->Display = true;
                }

                $this->txtChange->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSaveChange->Display = false;
                $this->btnCancel->Display = false;
                $this->btnAddChange->Enabled = true;
                $this->dtgNewsChanges->removeCssClass('disabled');
                $this->txtChange->Text = null;
                $this->dlgToastr1->notify();

            } else {
                $this->txtChange->Text = null;
                $this->txtChange->focus();
                $this->dlgModal4->showDialogBox();
            }

        } else {
            $this->txtChange->Text = null;
            $this->txtChange->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the logic for saving changes to news items. This method checks the status of the change, validates the input,
     * and updates the news item if necessary. It also manages the display and state of UI components based on the operation outcome.
     *
     * @param ActionParams $params Parameters passed from the action triggering this method. Contains information about the action context.
     * @return void This method does not return any value.
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objNewsChanges = NewsChanges::loadById($this->intId);

        if ($this->txtChange->Text) {
            if (in_array($this->intId, $this->objChangeIds) && $this->lstStatus->SelectedValue == 2) {
                $this->lstStatus->SelectedValue = 1;
                $this->dlgModal2->showDialogBox();

                if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                    $this->btnGoToNews->Display = true;
                }

                $this->btnAddChange->Enabled = true;
                $this->txtChange->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;
                $this->dtgNewsChanges->removeCssClass('disabled');

            } else if ($this->txtChange->Text == $objNewsChanges->getTitle() && $this->lstStatus->SelectedValue !== $objNewsChanges->getStatus()  ||
                $this->txtChange->Text !== $objNewsChanges->getTitle() && !in_array(trim(strtolower($this->txtChange->Text)), $this->objCategoryNames)) {

                $objNewsChanges->setTitle(trim($this->txtChange->Text));
                $objNewsChanges->setStatus($this->lstStatus->SelectedValue);
                $objNewsChanges->setPostUpdateDate(Q\QDateTime::Now());
                $objNewsChanges->save();

                $this->dtgNewsChanges->refresh();
                $this->btnAddChange->Enabled = true;

                if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                    $this->btnGoToNews->Display = true;
                }

                $this->txtChange->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;

                $this->dtgNewsChanges->removeCssClass('disabled');
                $this->txtChange->Text = $objNewsChanges->getTitle();
                $this->dlgToastr1->notify();


            } else if (in_array(trim(strtolower($this->txtChange->Text)), $this->objChangeNames)) {
                $this->txtChange->Text = $objNewsChanges->getTitle();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtChange->Text = $objNewsChanges->getTitle();
            $this->txtChange->focus();
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the delete button click event. Checks if the current ID is in the list of change IDs,
     * and shows a modal dialog accordingly. It adjusts the visibility and enabled state of various
     * UI components based on the session state and other conditions.
     *
     * @param ActionParams $params The parameters associated with the delete button click event.
     * @return void
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (in_array($this->intId, $this->objChangeIds)) {
            $this->dlgModal3->showDialogBox();

            if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
                $this->btnGoToNews->Display = true;
            }

            $this->btnAddChange->Enabled = true;
            $this->txtChange->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgNewsChanges->removeCssClass('disabled');

        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the click event for deleting a news item change.
     *
     * @param ActionParams $params The parameters passed with the action, including the action parameter to confirm deletion.
     * @return void
     */
    public function deleteItem_Click(ActionParams $params)
    {
        $objNewsChanges = NewsChanges::loadById($this->intId);

        if ($params->ActionParameter == "pass") {
            $objNewsChanges->delete();
        }

        $this->dtgNewsChanges->refresh();
        $this->btnAddChange->Enabled = true;
        $this->txtChange->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgNewsChanges->removeCssClass('disabled');
        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the cancel button click event to reset the UI to its initial state by hiding certain elements,
     * enabling others, and clearing any entered text changes.
     *
     * @param ActionParams $params The parameters associated with the action that triggered this event.
     * @return void This method does not return a value.
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
            $this->btnGoToNews->Display = true;
        }

        $this->txtChange->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSaveChange->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddChange->Enabled = true;
        $this->dtgNewsChanges->removeCssClass('disabled');
        $this->txtChange->Text = null;
    }

    /**
     * Handles the click event for the "Go To News" button. Redirects the user to the news edit page if session variables
     * for news changes are set, and clears these session variables afterwards.
     *
     * @param ActionParams $params The event parameters associated with the button click action.
     * @return void
     */
    protected function btnGoToNews_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['news_changes_id']) || !empty($_SESSION['news_changes_group'])) {
            $news = $_SESSION['news_changes_id'];
            $group = $_SESSION['news_changes_group'];

            Application::redirect('news_edit.php?id=' . $news . '&group=' . $group);
            unset($_SESSION['news_changes_id']);
            unset($_SESSION['news_changes_group']);
        }
    }

    /**
     * Iterates over all news items and collects their change IDs if available.
     *
     * @return void
     */
    private function CheckChanges()
    {
        $objNewsArray = News::loadAll();

        foreach ($objNewsArray as $objNews) {
            if ($objNews->getChangesId()) {
                $this->objChangeIds[] = $objNews->getChangesId();
            }
        }
    }

    /**
     * Processes all NewsChanges objects and appends the lowercase version of each title to the objChangeNames array.
     *
     * @return void
     */
    private function NewsChangeNames()
    {
        $objChanges = NewsChanges::loadAll();

        foreach ($objChanges as $objChange) {
            if ($objChange->getTitle()) {
                $this->objChangeNames[] = strtolower($objChange->getTitle());
            }
        }
    }
}