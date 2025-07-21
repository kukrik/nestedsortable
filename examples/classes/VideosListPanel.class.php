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

class VideosListPanel extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    protected $dlgToast1;
    protected $dlgToast2;
    protected $dlgToast3;
    protected $dlgToast4;
    protected $dlgToast5;
    protected $dlgToast6;

    public $dlgModal1;
    public $dlgModal2;

    public $btnMove;
    public $btnLockedCancel;
    public $lstVideosLocked;
    public $lstTargetGroup;

    public $txtFilter;
    public $dtgVideos;
    public $btnBack;

    protected $objUser;
    protected $intLoggedUserId;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'VideosListPanel.tpl.php';

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

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgVideos_Create();
        $this->dtgVideos->setDataBinder('BindData', $this);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function elementsReset()
    {
        Application::executeJavaScript("$('.move-items-js').addClass('hidden')");
    }

    protected function createInputs()
    {
        $this->lstTargetGroup = new Q\Plugin\Select2($this);
        $this->lstTargetGroup->MinimumResultsForSearch = -1;
        $this->lstTargetGroup->Theme = 'web-vauu';
        $this->lstTargetGroup->Width = '100%';
        $this->lstTargetGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);

        $objTargetGroups = VideosSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::VideosSettings()->Id)));
        foreach ($objTargetGroups as $objTitle) {
            if ($objTitle->IsReserved !== 2) {
                $this->lstTargetGroup->addItem($objTitle->Name, $objTitle->Id);
            }
        }

        $this->lstTargetGroup->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstTargetGroup_Change'));
        $this->lstTargetGroup->Enabled = false;
    }

    /**
     * Create and configure the 'Back' button with associated actions and styles
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnMove = new Bs\Button($this);
        $this->btnMove->Text = t(' Move');
        $this->btnMove->Glyph = 'fa fa-flip-horizontal fa-reply-all';
        $this->btnMove->CssClass = 'btn btn-darkblue move-button-js';
        $this->btnMove->addWrapperCssClass('center-button');
        $this->btnMove->CausesValidation = false;
        $this->btnMove->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnMove_Click'));

        if (VideosSettings::countAll() !== 1) {
            Application::executeJavaScript("$('.move-items-js').addClass('hidden')");
        } else {
            Application::executeJavaScript("$('.move-items-js').addClass('hidden')");
        }

        $this->btnLockedCancel = new Bs\Button($this);
        $this->btnLockedCancel->Text = t('Cancel');
        $this->btnLockedCancel->addWrapperCssClass('center-button');
        $this->btnLockedCancel->CssClass = 'btn btn-default';
        $this->btnLockedCancel->CausesValidation = false;
        $this->btnLockedCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnLockedCancel_Click'));

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->addWrapperCssClass('center-button');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnBack_Click'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createToastr()
    {
        $this->dlgToast3 = new Q\Plugin\Toastr($this);
        $this->dlgToast3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast3->Message = t('<p style=\"margin-bottom: 5px;\">The videos group must be selected beforehand!</p>');
        $this->dlgToast3->ProgressBar = true;
        $this->dlgToast3->TimeOut = 10000;
        $this->dlgToast3->EscapeHtml = false;

        $this->dlgToast4 = new Q\Plugin\Toastr($this);
        $this->dlgToast4->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast4->Message = t('<p style=\"margin-bottom: 5px;\">The videos group cannot be the same as the target group!</p>');
        $this->dlgToast4->ProgressBar = true;
        $this->dlgToast4->TimeOut = 10000;
        $this->dlgToast4->EscapeHtml = false;

        $this->dlgToast5 = new Q\Plugin\Toastr($this);
        $this->dlgToast5->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast5->Message = t('<strong>Well done!</strong> The transfer of this videos group to the new group was successful.');
        $this->dlgToast5->ProgressBar = true;

        $this->dlgToast6 = new Q\Plugin\Toastr($this);
        $this->dlgToast6->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToast6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast6->Message = t('The transfer of this videos group to the new group failed.');
        $this->dlgToast6->ProgressBar = true;
    }

    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move the videos from this videos group to another videos group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Note: If the selected group contains multiple videos, they will all be transferred to the new group!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'moveItems_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'transferCancelling_Click'));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal2->Title = t("Warning");
        $this->dlgModal2->HeaderClasses = 'btn-danger';
        $this->dlgModal2->addCloseButton(t("I understand"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function btnMove_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("$('.move-items-js').removeClass('hidden')");

        $this->lstVideosLocked = new Q\Plugin\Select2($this);
        $this->lstVideosLocked->MinimumResultsForSearch = -1;
        $this->lstVideosLocked->Theme = 'web-vauu';
        $this->lstVideosLocked->Width = '100%';
        $this->lstVideosLocked->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstVideosLocked->addItem(t('- Select one videos group -'), null, true);

        $objGroups = VideosSettings::queryArray(
            QQ::all(),
            [
                QQ::orderBy(QQ::notEqual(QQN::VideosSettings()->VideosLocked, 0), QQN::VideosSettings()->Id)
            ]
        );

        $countLocked = VideosSettings::countByVideosLocked(1);

        foreach ($objGroups as $objTitle) {
            if ($countLocked > 1 && $objTitle->VideosLocked === 1) {
                $this->lstVideosLocked->addItem($objTitle->Name, $objTitle->Id);
            } else if ($countLocked === 1 && $objTitle->VideosLocked === 1) {
                $this->lstVideosLocked->addItem($objTitle->Name, $objTitle->Id);
                $this->lstVideosLocked->SelectedValue = $objTitle->Id;
            }
        }

        $this->lstVideosLocked->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstVideosLocked_Change'));

        if ($this->lstVideosLocked->SelectedValue === null) {
            $this->lstTargetGroup->SelectedValue = null;
            $this->lstTargetGroup->Enabled = false;
        }

        if ($countLocked === 1) {
            $this->lstVideosLocked->Enabled = false;
            $this->lstTargetGroup->Enabled = true;
            $this->lstTargetGroup->focus();
        } else {
            $this->lstVideosLocked->Enabled = true;
            $this->lstVideosLocked->focus();
        }

        $this->btnMove->Enabled = false;
        $this->dtgVideos->addCssClass('disabled');
    }

    protected function lstVideosLocked_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->lstVideosLocked->SelectedValue === null) {
            $this->lstTargetGroup->Enabled = false;
            $this->lstVideosLocked->addCssClass('has-error');
            $this->dlgToast3->notify();
        } else {
            $this->lstTargetGroup->Enabled = true;
            $this->lstVideosLocked->removeCssClass('has-error');
            $this->lstTargetGroup->focus();
        }
    }

    protected function lstTargetGroup_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->lstVideosLocked->SelectedValue === $this->lstTargetGroup->SelectedValue) {
            $this->lstTargetGroup->SelectedValue = null;
            $this->lstTargetGroup->refresh();
            $this->lstTargetGroup->addCssClass('has-error');
            $this->dlgToast4->notify();
        } else if ($this->lstVideosLocked->SelectedValue !== null && $this->lstTargetGroup->SelectedValue !== null) {
            $this->dlgModal1->showDialogBox();
        } else {
            $this->lstTargetGroup->removeCssClass('has-error');
        }
    }

    public function transferCancelling_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->elementsReset();

        $this->lstVideosLocked->SelectedValue = null;
        $this->lstTargetGroup->SelectedValue = null;

        $this->btnMove->Enabled = true;

        $this->lstVideosLocked->refresh();
        $this->lstTargetGroup->refresh();

        $this->dtgVideos->removeCssClass('disabled');
    }

    protected function moveItems_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->dlgModal1->hideDialogBox();
        $this->videosTransferOperations();

        $this->elementsReset();
        $this->btnMove->Enabled = true;
    }

    private function videosTransferOperations()
    {
        $objLockedGroup = VideosSettings::loadById($this->lstVideosLocked->SelectedValue);
        $objTargetGroup = VideosSettings::loadById($this->lstTargetGroup->SelectedValue);

        $objVideosGroupArray = Videos::loadArrayBySettingsId($objLockedGroup->getId());
        $beforeCount = Videos::countBySettingsId($objTargetGroup->getId());

        $objVideosSettings = VideosSettings::loadById($objLockedGroup->getId());
        $objVideosSettings->setVideosLocked(0);
        $objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objVideosSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objVideosSettings->save();

        $objVideosSettings = VideosSettings::loadById($objTargetGroup->getId());
        $objVideosSettings->setVideosLocked(1);
        $objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $objVideosSettings->setPostUpdateDate(Q\QDateTime::Now());
        $objVideosSettings->save();

        foreach ($objVideosGroupArray as $objVideosGroup) {
            $objVideos = Videos::loadById($objVideosGroup->getId());
            $objVideos->setSettingsId($this->lstTargetGroup->SelectedValue);
            $objVideos->setSettingsIdTitle($this->lstTargetGroup->SelectedName);
            $objVideos->save();
        }

        $this->dtgVideos->refresh(true);

        if (VideosSettings::countAll() !== 1) {
            Application::executeJavaScript("$('.move-items-js').addClass('hidden')");
        } else {
            Application::executeJavaScript("$('.move-items-js').addClass('hidden')");
        }

        $afterCount = Videos::countBySettingsId($objTargetGroup->getId());

        if ($beforeCount < $afterCount) {
            $this->dlgToast5->notify();
        } else {
            $this->dlgToast6->notify();
        }

        $objVideoArray = Videos::loadAll(QQ::Clause(QQ::orderBy(QQN::Videos()->PostDate, false)));

        foreach ($objVideoArray as $key => $objVideo) {
            $objVideos = Videos::loadById($objVideo->getId());
            $objVideos->setOrder($key);
            $objVideos->save();
        }
    }

    protected function btnLockedCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (VideosSettings::countAll() !== 1) {
            Application::executeJavaScript("
                $('.move-items-js').addClass('hidden');
            ");
        } else {
            Application::executeJavaScript("
                $('.move-items-js').addClass('hidden');
            ");
        }

        $this->btnMove->Enabled = true;

        $this->lstVideosLocked->SelectedValue = null;
        $this->lstTargetGroup->SelectedValue = null;

        $this->lstVideosLocked->refresh();
        $this->lstTargetGroup->refresh();

        $this->dtgVideos->removeCssClass('disabled');
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
            $this->dlgModal2->showDialogBox();
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
    protected function dtgVideos_Create()
    {
        $this->dtgVideos = new VideosTable($this);
        $this->dtgVideos_CreateColumns();
        $this->createPaginators();
        $this->dtgVideos_MakeEditable();
        $this->dtgVideos->RowParamsCallback = [$this, "dtgVideos_GetRowParams"];
        $this->dtgVideos->SortColumnIndex = 5;
        //$this->dtgVideos->SortDirection = -1;
        $this->dtgVideos->UseAjax = true;
        $this->dtgVideos->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }

    /**
     * Create columns for the datagrid
     *
     * @return void
     */
    protected function dtgVideos_CreateColumns()
    {
        $this->dtgVideos->createColumns();
    }

    /**
     * Configures the dtgVideos datatable to be interactive and editable by adding
     * appropriate actions and CSS classes. This method enables cell click actions
     * that trigger an AJAX control event and applies specified CSS classes to the table.
     *
     * @return void
     */
    protected function dtgVideos_MakeEditable()
    {
        $this->dtgVideos->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgVideosRow_Click'));
        $this->dtgVideos->addCssClass('clickable-rows');
        $this->dtgVideos->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles click events on rows of the dtgVideos datatable. Retrieves the board
     * settings based on the action parameter's identifier, then redirects the user
     * to the board edit page with the board's ID and group information as query parameters.
     *
     * @param ActionParams $params The parameters associated with the action, containing
     *                             the identifier of the clicked row's board.
     *
     * @return void
     */
    protected function dtgVideosRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal2->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $intId = intval($params->ActionParameter);
        $objVideos = VideosSettings::loadById($intId);
        $intGroup = $objVideos->getMenuContentId();

        Application::redirect('videos_edit.php' . '?id=' . $intId . '&group=' . $intGroup);
    }

    /**
     * Get row parameters for the row tag
     *
     * @param mixed $objRowObject   A database object
     * @param int $intRowIndex      The row index
     * @return array
     */
    public function dtgVideos_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Sets up pagination for the dtgVideos datatable by initializing primary and
     * alternate paginators with labels for navigation controls and specifying
     * the number of items displayed per page. Additionally, invokes actions
     * to handle filtering of data within the table.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgVideos->Paginator = new Bs\Paginator($this);
        $this->dtgVideos->Paginator->LabelForPrevious = t('Previous');
        $this->dtgVideos->Paginator->LabelForNext = t('Next');

        $this->dtgVideos->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgVideos->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgVideos->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgVideos->ItemsPerPage = 10;

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
        $this->dtgVideos->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgVideos->refresh();
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
        $this->dtgVideos->refresh();
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
        $this->dtgVideos->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the current text input in the filter field.
     * This method evaluates the search value and returns a condition that matches records
     * in the VideosSettings database table where the name, title, or author fields contain
     * the specified search value. If the search value is empty, it returns a condition that matches all records.
     *
     * @return Q\Query\QQ A query condition that can be used to filter VideosSettings records
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
                Q\Query\QQ::like(QQN::VideosSettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::VideosSettings()->Title, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::VideosSettings()->Author, "%" . $strSearchValue . "%")
            );
        }
    }
}