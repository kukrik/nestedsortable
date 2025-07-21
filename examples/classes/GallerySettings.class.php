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

class GalleriesSettings extends Q\Control\Panel
{
    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    public $dlgModal1;

    protected $dlgToast1;

    public $txtFilter;
    public $dtgGalleryGroups;

    public $txtGalleryGroup;
    public $txtGalleryTitle;
    public $btnSave;
    public $btnCancel;
    public $btnGoToGallery;

    protected $objUser;
    protected $intLoggedUserId;
    protected $intId;

    protected $objMenuContent;
    protected $objGroupTitleCondition;
    protected $objGroupTitleClauses;

    protected $strTemplate = 'GallerySettings.tpl.php';

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
        $this->dtgGalleryGroups_Create();
        $this->dtgGalleryGroups->setDataBinder('BindData', $this);

        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and sets up the Gallery Groups datagrid.
     *
     * @return void
     */
    protected function dtgGalleryGroups_Create()
    {
        $this->dtgGalleryGroups = new GallerySettingsTable($this);
        $this->dtgGalleryGroups_CreateColumns();
        $this->createPaginators();
        $this->dtgGalleryGroups_MakeEditable();
        $this->dtgGalleryGroups->RowParamsCallback = [$this, "dtgGalleryGroups_GetRowParams"];
        $this->dtgGalleryGroups->SortColumnIndex = 0;
        $this->dtgGalleryGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
    }

    /**
     * Creates columns for the GalleryGroups data table. This method delegates the
     * column creation process to the `createColumns` method of the `dtgGalleryGroups` object.
     *
     * @return void
     */
    protected function dtgGalleryGroups_CreateColumns()
    {
        $this->dtgGalleryGroups->createColumns();
    }

    /**
     * Makes the data grid of gallery groups editable by adding specific actions and CSS classes.
     *
     * @return void
     */
    protected function dtgGalleryGroups_MakeEditable()
    {
        $this->dtgGalleryGroups->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgGalleryGroups_Click'));
        $this->dtgGalleryGroups->addCssClass('clickable-rows');
        $this->dtgGalleryGroups->CssClass = 'table vauu-table table-hover table-responsive';
    }

    /**
     * Handles the click event on a gallery group item, retrieving and displaying its information for editing.
     *
     * @param ActionParams $params The parameters associated with the click action, primarily containing the action parameter which is used to identify the gallery group.
     * @return void
     */
    protected function dtgGalleryGroups_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objGalleryGroups = GallerySettings::load($this->intId);

        $this->txtGalleryGroup->Enabled = false;
        $this->txtGalleryGroup->Text = $objGalleryGroups->getName();
        $this->txtGalleryTitle->Text = $objGalleryGroups->getTitle();
        $this->txtGalleryTitle->focus();

        if (!empty($_SESSION['gallery_group_edit']) || !empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
            $this->btnGoToGallery->Display = true;
            $this->btnGoToGallery->Enabled = false;
        }

        $this->dtgGalleryGroups->addCssClass('disabled');
        $this->txtGalleryGroup->Display = true;
        $this->txtGalleryTitle->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;
    }

    /**
     * Retrieve row parameters for a gallery group.
     *
     * @param object $objRowObject The object representing the row.
     * @param int $intRowIndex The index of the row.
     * @return array An associative array of parameters for the row.
     */
    public function dtgGalleryGroups_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Sets up the paginator for the gallery groups data table along with pagination and sorting settings.
     * The paginator will have custom labels for previous and next navigation.
     * It also enables Ajax functionality for the data table and applies filter actions.
     *
     * @return void
     */
    protected function createPaginators()
    {
        $this->dtgGalleryGroups->Paginator = new Bs\Paginator($this);
        $this->dtgGalleryGroups->Paginator->LabelForPrevious = t('Previous');
        $this->dtgGalleryGroups->Paginator->LabelForNext = t('Next');

        $this->dtgGalleryGroups->ItemsPerPage = 10;
        $this->dtgGalleryGroups->SortColumnIndex = 0;
        $this->dtgGalleryGroups->UseAjax = true;

        $this->addFilterActions();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a Select2 control for selecting items per page
     * associated with a user object. Sets various properties of the Select2
     * control including theme, width, and selection mode, and populates it with
     * items. Additionally, it adds an Ajax control change event handler.
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
     * Retrieves a list of items per page assigned to a user.
     *
     * This function queries the `ItemsPerPage` table based on a specified condition.
     * It then iterates through each result, instantiates a `ListItem` object for each entry,
     * and checks if the current user has a specific item selected based on their assigned items.
     * The resulting list of `ListItem` objects is returned.
     *
     * @return ListItem[] An array of ListItem objects representing items per page assigned to the user.
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
     * Updates the number of items displayed per page in the gallery groups data grid based on the selected item.
     *
     * @param ActionParams $params The parameters associated with the action event that triggered this change.
     * @return void
     */
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgGalleryGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgGalleryGroups->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a search filter text box for user input.
     * The text box has a placeholder, is set to search mode, disables auto-complete,
     * and includes specific CSS classes. Invokes additional actions for filter setup.
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
     * Adds filter actions to the text input control.
     *
     * This method associates input and enter key events with appropriate AJAX control actions for
     * filtering functionality. The input event triggers an AJAX call with a delay, while the enter
     * key event triggers immediate AJAX action and subsequently terminates any further actions.
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
     * Refreshes the data display in the `dtgGalleryGroups` object when a filter change is detected.
     *
     * @return void
     */
    protected function filterChanged()
    {
        $this->dtgGalleryGroups->refresh();
    }

    /**
     * Binds the data source to the gallery groups data table using a specified condition.
     *
     * @return void
     */
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgGalleryGroups->bindData($objCondition);
    }

    /**
     * Constructs a query condition based on the filter text present in the input field.
     *
     * If the filter text is null or empty, it returns a query condition that matches all records.
     * Otherwise, it returns a condition to fetch records where the name of the GallerySettings
     * contains the filter text.
     *
     * @return Q\Query\QQ Condition for querying the database, either to match all records or a specific condition
     *                     based on the filter text.
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
                Q\Query\QQ::like(QQN::GallerySettings()->Name, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::GallerySettings()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures a set of UI buttons and textboxes for the gallery interface.
     *
     * The method sets properties and styles for each UI component such as buttons for navigating,
     * saving, and canceling actions, as well as textboxes for entering gallery group and title information.
     * It also manages the visibility and functionality of these components based on session data.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnGoToGallery = new Bs\Button($this);
        $this->btnGoToGallery->Text = t('Go to this gallery');
        $this->btnGoToGallery->addWrapperCssClass('center-button');
        $this->btnGoToGallery->CssClass = 'btn btn-default';
        $this->btnGoToGallery->CausesValidation = false;
        $this->btnGoToGallery->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToGallery_Click'));
        $this->btnGoToGallery->setCssStyle('float', 'left');
        $this->btnGoToGallery->setCssStyle('margin-right', '10px');

        if (!empty($_SESSION['gallery_group_edit']) || !empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
            $this->btnGoToGallery->Display = true;
        } else {
            $this->btnGoToGallery->Display = false;
        }

        $this->txtGalleryGroup = new Bs\TextBox($this);
        $this->txtGalleryGroup->Placeholder = t('Gallery group');
        $this->txtGalleryGroup->ActionParameter = $this->txtGalleryGroup->ControlId;
        $this->txtGalleryGroup->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtGalleryGroup->setHtmlAttribute('autocomplete', 'off');
        $this->txtGalleryGroup->setCssStyle('float', 'left');
        $this->txtGalleryGroup->setCssStyle('margin-right', '10px');
        $this->txtGalleryGroup->Width = 300;
        $this->txtGalleryGroup->Display = false;

        $this->txtGalleryTitle = new Bs\TextBox($this);
        $this->txtGalleryTitle->Placeholder = t('Gallery title');
        $this->txtGalleryTitle->ActionParameter = $this->txtGalleryTitle->ControlId;
        $this->txtGalleryTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;

        $this->txtGalleryTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtGalleryTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtGalleryTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
        $this->txtGalleryTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->txtGalleryTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtGalleryTitle->setCssStyle('float', 'left');
        $this->txtGalleryTitle->setCssStyle('margin-right', '10px');
        $this->txtGalleryTitle->Width = 400;
        $this->txtGalleryTitle->Display = false;

        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Update');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->btnSave->setCssStyle('float', 'left');
        $this->btnSave->setCssStyle('margin-right', '10px');
        $this->btnSave->Display = false;

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
     * Initializes and configures multiple Toastr notification objects with specific
     * alert types, positions, and messages. The method sets up four Toastr instances
     * for different success and error notifications, each with a progress bar enabled.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToast1 = new Q\Plugin\Toastr($this);
        $this->dlgToast1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToast1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToast1->Message = t('<strong>Well done!</strong> The gallery group title has been saved or modified.');
        $this->dlgToast1->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the save operation when the save button is clicked.
     * It updates the gallery group based on the input parameters,
     * toggles the display of UI elements, and triggers notifications.
     *
     * @param ActionParams $params Parameters that determine action details.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objGalleryGroup = GallerySettings::load($this->intId);
        $objSelectedGroup = GallerySettings::selectedByIdFromGallerySettings($this->intId);
        $objMenuContent = MenuContent::load($objSelectedGroup->getGalleryGroupId());
        $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getGalleryGroupId());

        $objMenuContent->updateMenuContent($this->txtGalleryTitle->Text, $objGalleryGroup->getTitleSlug());

        $objGalleryGroup->setTitle($this->txtGalleryTitle->Text);
        $objGalleryGroup->setPostUpdateDate(Q\QDateTime::Now());
        $objGalleryGroup->save();

        $objFrontendLink->setTitle($this->txtGalleryTitle->Text);
        $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
        $objFrontendLink->save();

        if (!empty($_SESSION['gallery_group_edit']) || !empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
            $this->btnGoToGallery->Display = true;
            $this->btnGoToGallery->Enabled = true;
        }

        $this->txtGalleryGroup->Display = false;
        $this->txtGalleryTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgGalleryGroups->refresh();
        $this->dtgGalleryGroups->removeCssClass('disabled');
        $this->dlgToast1->notify();
    }

    /**
     * Handles the cancel button click event. It checks session variables related to the gallery and adjusts the display
     * state of various UI elements accordingly. This method is intended to reset or hide certain form fields and buttons
     * while enabling another button if specific session conditions are met.
     *
     * @param ActionParams $params The parameters associated with the action event, including any context or metadata
     *                             that may be needed by this handler.
     * @return void This method does not return any value.
     */
    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['gallery_group_edit']) || !empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
            $this->btnGoToGallery->Display = true;
            $this->btnGoToGallery->Enabled = true;
        }

        $this->txtGalleryGroup->Display = false;
        $this->txtGalleryTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->dtgGalleryGroups->removeCssClass('disabled');
        $this->txtGalleryGroup->Text = null;
    }

    /**
     * Handles the click event for the "Go To Gallery" button. This method
     * redirects the user to the appropriate gallery editing page based on
     * session variables and clears those session variables after redirection.
     *
     * @param ActionParams $params Parameters associated with the triggered action.
     * @return void
     */
    protected function btnGoToGallery_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['gallery_group_edit'])) {
            Application::redirect('menu_edit.php?id=' . $_SESSION['gallery_group_edit']);
            unset($_SESSION['gallery_group_edit']);

        } else if (!empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
            Application::redirect('album_edit.php?id=' . $_SESSION['gallery'] . '&group=' . $_SESSION['gallery_group'] . '&folder=' . $_SESSION['gallery_folder']);
            unset($_SESSION['gallery']);
            unset($_SESSION['gallery_group']);
            unset($_SESSION['gallery_folder']);
        }
    }
}