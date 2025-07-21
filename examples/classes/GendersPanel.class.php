<?php

use QCubed as Q;
use QCubed\Action\Ajax;
use QCubed\Bootstrap as Bs;
use QCubed\Event\Change;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class GendersPanel extends Q\Control\Panel
{
    protected $dlgToastr1;
    protected $dlgToastr2;
    protected $dlgToastr3;
    protected $dlgToastr4;
    protected $dlgToastr5;
    protected $dlgToastr6;
    protected $dlgToastr7;
    protected $dlgToastr8;
    protected $dlgToastr9;

    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;
    public $dlgModal5;

    public $lblInfo;
    public $btnAddNewGender;

    public $lblName;
    public $txtName;
    public $lblStatus;
    public $lstStatus;

    public $btnSave;
    public $btnDelete;
    public $btnCancel;

    public $lblPostDate;
    public $calPostDate;
    public $lblPostUpdateDate;
    public $calPostUpdateDate;
    public $lblAuthor;
    public $txtAuthor;
    public $lblUsersAsEditors;
    public $txtUsersAsEditors;

    public $dtgGenders;

    protected $intId;
    protected $intClick;
    protected $blnEditMode = true;
    protected $objUser;
    protected $intLoggedUserId;
    protected $objGender;

    protected $strTemplate = 'GendersPanel.tpl.php';

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
        
        $this->dtgGenders_Create();

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes the Genders data table.
     * Sets up columns, enables editing capabilities, configures row parameters callback,
     * and defines default sorting behavior.
     *
     * @return void
     */
    protected function dtgGenders_Create()
    {
        $this->dtgGenders = new GendersTable($this);
        $this->dtgGenders_CreateColumns();
        $this->dtgGenders_MakeEditable();
        $this->dtgGenders->RowParamsCallback = [$this, "dtgGenders_GetRowParams"];
        $this->dtgGenders->SortColumnIndex = 0;
        $this->dtgGenders->SortDirection = -1;
    }

    /**
     * Initializes and creates the columns for the dtgGenders data grid.
     *
     * @return void
     */
    protected function dtgGenders_CreateColumns()
    {
        $this->dtgGenders->createColumns();
    }

    /**
     * Configures the "dtgGenders" data grid to be editable by enabling row click functionality.
     * Adds a click event to each row, making it interactive and creating a visual indication of rows being clickable.
     * Applies appropriate CSS classes for styling and interactivity.
     *
     * @return void
     */
    protected function dtgGenders_MakeEditable()
    {
        $this->dtgGenders->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgGenders_Click'));
        $this->dtgGenders->addCssClass('clickable-rows');
        $this->dtgGenders->CssClass = 'table vauu-table js-sports-area table-hover table-responsive';
    }

    /**
     * Handles the click event for the datagrid of genders.
     *
     * Updates internal state variables, toggles UI elements based on the selected gender's properties,
     * and refreshes the display to reflect the selected gender's details.
     *
     * @param ActionParams $params Parameters containing the action's information, including the clicked item's ID.
     * @return void This method modifies the UI and state without returning any value.
     */
    protected function dtgGenders_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objGender = Genders::load($this->intId);
        $this->intClick = $this->intId;

        if ($objGender->getIsLocked() == 1) {
            $this->btnDelete->Display = true;
        } else {
            $this->btnDelete->Display = false;
        }

        $this->blnEditMode = true;

        $this->dtgGenders->addCssClass('disabled');
        $this->refreshDisplay($this->intId);

        Application::executeJavaScript("
            $('.setting-wrapper').removeClass('hidden');
            $('.form-actions-wrapper').removeClass('hidden');
            $('.js-wrapper-top').get(0).scrollIntoView({behavior: 'smooth'});
        ");

        $this->txtName->Text = $objGender->getName();
        $this->lstStatus->SelectedValue = $objGender->getStatus();
    }

    /**
     * Generates row parameters for a data grid row based on the provided object and row index.
     *
     * @param object $objRowObject The row object representing the data for the current row. It must have a `primaryKey()` method to retrieve the primary key value.
     * @param int $intRowIndex The zero-based index of the row in the data grid.
     * @return array An associative array of parameters, including the primary key of the row object as 'data-value'.
     */
    public function dtgGenders_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();

        $params['data-value'] = $strKey;
        return $params;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes various input controls and labels for the application.
     *
     * Creates and configures alert messages, labels, textboxes, radiobutton lists,
     * and other controls used for data input and display. Adjusts the display
     * settings of components based on specific conditions, such as the presence of data.
     *
     * @return void
     */
    protected function createInputs()
    {
        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('Please create the first gender!');
        $this->lblInfo->setCssStyle('margin-bottom', 0);

        $countGenders = Genders::countAll();

        if ($countGenders === 0) {
            $this->lblInfo->Display = true;
            $this->dtgGenders->Display = false;
        } else {
            $this->lblInfo->Display = false;
            $this->dtgGenders->Display = true;
        }

        $this->lblName = new Q\Plugin\Control\Label($this);
        $this->lblName->Text = t('Name');
        $this->lblName->addCssClass('col-md-4');
        $this->lblName->setCssStyle('font-weight', 'normal');
        $this->lblName->Required = true;

        $this->txtName = new Bs\TextBox($this);
        $this->txtName->Placeholder = t('Gender name');
        $this->txtName->setHtmlAttribute('autocomplete', 'off');
        $this->txtName->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtName->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this, 'btnSave_Click'));
        $this->txtName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtName->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this, 'itemEscape_Click'));
        $this->txtName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->addCssClass('col-md-4');
        $this->lblStatus->setCssStyle('font-weight', 'normal');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange edit radio-inline';
        $this->lstStatus->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this, 'lstStatus_Change'));

        ///////////////////////////////////////////////////////////////////////////////////////////

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');
        $this->lblPostDate->Display = false;

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->setCssStyle('font-weight', 'normal');
        $this->calPostDate->Display = false;

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');
        $this->lblPostUpdateDate->Display = false;

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');
        $this->calPostUpdateDate->Display = false;

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->setCssStyle('font-weight', 'bold');
        $this->lblAuthor->Display = false;

        $this->txtAuthor  = new Bs\Label($this);
        $this->txtAuthor->setCssStyle('font-weight', 'normal');
        $this->txtAuthor->Display = false;

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');
        $this->lblUsersAsEditors->Display = false;

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');
        $this->txtUsersAsEditors->Display = false;
    }

    /**
     * Creates buttons for adding a new gender, saving, deleting, and canceling.
     * The buttons are configured with appropriate text, CSS classes, validation settings,
     * and actions to handle click events.
     *
     * @return void
     */
    public function createButtons()
    {
        $this->btnAddNewGender = new Bs\Button($this);
        $this->btnAddNewGender->Text = t('Add a new gender');
        $this->btnAddNewGender->CssClass = 'btn btn-orange';
        $this->btnAddNewGender->CausesValidation = false;
        $this->btnAddNewGender->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnAddNewGender_Click'));

        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->setCssStyle('margin-right', '10px');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnSave_Click'));

        $this->btnDelete = new Bs\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->setCssStyle('margin-right', '10px');
        $this->btnDelete->CausesValidation = true;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnDelete_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
    }

    /**
     * Creates and configures toastr notifications for various scenarios such as success, error, and info.
     * Each toastr is configured with alert type, position, message, and progress bar visibility.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The new gender was successfully added to the database.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('Failed to add the new gender');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('This new gender already exists in the database!');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('The gender data was saved or modified!');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('This field is required!');
        $this->dlgToastr5->ProgressBar = true;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('These fields must be filled!');
        $this->dlgToastr6->ProgressBar = true;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_INFO;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('The gender name update was discarded, and the gender name has been restored!');
        $this->dlgToastr7->ProgressBar = true;

        $this->dlgToastr8 = new Q\Plugin\Toastr($this);
        $this->dlgToastr8->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr8->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr8->Message = t('<strong>Well done!</strong> This gender with data is now active!');
        $this->dlgToastr8->ProgressBar = true;

        $this->dlgToastr9 = new Q\Plugin\Toastr($this);
        $this->dlgToastr9->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr9->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr9->Message = t('<strong>Well done!</strong> This gender with data is now inactive!');
        $this->dlgToastr9->ProgressBar = true;
    }

    /**
     * Initializes and configures multiple modal dialogs.
     *
     * This method creates and sets up several modal instances with specific titles,
     * properties, and actions. Each modal serves a distinct purpose, such as displaying
     * warnings, providing tips, or notifying users of specific conditions.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete the gender?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new Q\Action\AjaxControl($this, 'hideItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">This gender cannot be deleted 
                                    as it is locked in the records table or leaderboard!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                    and both tables will be updated automatically.</p>');

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The status of this gender cannot 
                                    be deactivated as it is locked in the records table or leaderboard!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">However, you can still edit it, 
                                    and both tables will be updated automatically.</p>');

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">When completing a new gender, 
                                    please make sure this field are filled!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">Activating the status is optional!</p>');

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
     * Handles the event when the "Add New Gender" button is clicked.
     *
     * This method modifies the UI to allow the user to add a new gender entry.
     * It updates the appearance of certain elements, prepares the form for
     * new input, and ensures the application is in creation mode.
     *
     * @param ActionParams $params The parameters associated with the action triggering this event.
     * @return void
     */
    public function btnAddNewGender_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').removeClass('hidden');
            $('.form-actions-wrapper').removeClass('hidden');
        ");

        $this->dtgGenders->addCssClass('disabled');
        $this->btnSave->Display = true;
        $this->btnDelete->Display = false;
        $this->blnEditMode = false;

        $this->txtName->Text = '';
        $this->txtName->focus();
        $this->lstStatus->SelectedValue = 2;
    }

    /**
     * Handles the change event for the status dropdown list.
     *
     * This method processes the status change for a selected gender record. Depending on the
     * current edit mode and the lock status of the gender, it validates, updates, and saves
     * the status of the gender while triggering corresponding notifications or dialog boxes.
     * Ensures proper validation and updates related attributes or displays modals if conditions
     * are not met.
     *
     * @param ActionParams $params The parameters related to the action triggering this method.
     * @return void
     */
    protected function lstStatus_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objGender = Genders::load($this->intClick);
        }

        if ($this->blnEditMode === true) {
            if ($objGender->getIsLocked() == 1) {
                if ($this->lstStatus->SelectedValue == 1) {
                    $objGender->setStatus(1);
                    $this->dlgToastr8->notify();
                } else {
                    $objGender->setStatus(2);
                    $this->dlgToastr9->notify();
                }

                $this->updateAndValidateAgeGroups($objGender);
            } else {
                $this->dlgModal3->showDialogBox();
                $this->lstStatus->SelectedValue = 1;
            }

            $objGender->save();
        } else {
            if (!$this->txtName->Text) {
                $this->txtName->setHtmlAttribute('required', 'required');

                $this->txtName->Text = '';
                $this->lstStatus->SelectedValue = 2;

                $this->dlgModal4->showDialogBox();
            }
        }
    }

    /**
     * Handles the save button click event to create or update a gender record.
     *
     * This method processes the user input and executes appropriate logic for creating
     * or updating a gender entity. It validates input, prevents duplicate entries,
     * and updates the UI or displays notifications based on the outcome.
     *
     * @param ActionParams $params Includes dynamic parameters related to the action event.
     * @return void
     */
    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objGender = Genders::load($this->intClick);
        }

        if ($this->blnEditMode === false) {
            if ($this->txtName->Text && !Genders::nameExists(trim($this->txtName->Text))) {
                    $objGender = new Genders();
                    $objGender->setName($this->txtName->Text);
                    $objGender->setStatus($this->lstStatus->SelectedValue);
                    $objGender->setPostDate(Q\QDateTime::Now());
                    $objGender->setAssignedByUser($this->intLoggedUserId);
                    $objGender->setAuthor($objGender->getAssignedByUserObject());
                    $objGender->save();

                    Application::executeJavaScript("
                        $('.setting-wrapper').addClass('hidden');
                        $('.form-actions-wrapper').addClass('hidden');
                    ");

                    $this->hideUserWindow();
                    $this->dtgGenders->removeCssClass('disabled');

                    $this->dlgToastr1->notify();

            } else if ($this->txtName->Text && Genders::nameExists(trim($this->txtName->Text))) {
                    $this->txtName->Text = '';
                    $this->lstStatus->SelectedValue = 2;
                    $this->dlgToastr3->notify(); // Exists ClassName
            } else {
                $this->txtName->Text = '';
                $this->lstStatus->SelectedValue = 2;

                $this->dlgModal4->showDialogBox();
            }
        } else {
            if ($this->txtName->Text) {
                if (!Genders::nameExists(trim($this->txtName->Text))) {
                    $this->dlgToastr4->notify(); // Everything OK

                    $this->updateAndValidateAgeGroups($objGender);
                } else {
                    $this->txtName->Text = '';
                    $this->lstStatus->SelectedValue = 2;
                    $this->dlgToastr3->notify(); // Exists ClassName
                }
            } else {
                $this->txtName->setHtmlAttribute('required', 'required');
                $this->txtName->Text = '';
                $this->lstStatus->SelectedValue = 2;
                $this->dlgToastr5->notify();
            }

            $objGender->setName($this->txtName->Text);
            $objGender->setStatus($this->lstStatus->SelectedValue);
            $objGender->save();
        }
    }

    /**
     * Handles the cancel button click event.
     *
     * This method executes JavaScript to hide certain UI elements, resets various form fields,
     * and modifies the state of controls within the user interface. It is typically used
     * to reset the input form and UI state when the user cancels an operation.
     *
     * @param ActionParams $params Parameters associated with the action that triggered the event.
     * @return void
     */
    public function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->hideUserWindow();

        $this->dtgGenders->removeCssClass('disabled');
        $this->txtName->removeHtmlAttribute('required', 'required');

        $this->txtName->Text = '';
        $this->lstStatus->SelectedValue = 2;
    }

    /**
     * Handles the escape click action for an item.
     *
     * This method retrieves the gender object based on the ID specified in the `intClick` property,
     * updates the text of a specific input field with the name of the gender, and triggers a notification
     * through a modal or toast dialog.
     *
     * @param ActionParams $params Parameters associated with the action event.
     * @return void
     */
    protected function itemEscape_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objGender = Genders::load($this->intClick);
        }

        $this->txtName->Text = $objGender->getName();
        $this->dlgToastr7->notify();
    }

    /**
     * Updates and validates age group data for a given gender object and refreshes the display.
     *
     * This method updates the post update date, assigns the editor's name,
     * and saves changes to the provided gender object. Afterward, it refreshes
     * several display fields, including post dates, author information, and editors'
     * list for the updated gender.
     *
     * @param object $objGender The gender object being updated, validated, and saved.
     * @return void
     */
    protected function updateAndValidateAgeGroups($objGender)
    {
        $objGender->setPostUpdateDate(Q\QDateTime::Now());
        $objGender->setAssignedEditorsNameById($this->intLoggedUserId);
        $objGender->save();

        $this->calPostDate->Text = $objGender->PostDate ? $objGender->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->Text = $objGender->PostUpdateDate ? $objGender->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->txtAuthor->Text = $objGender->Author;
        $this->txtUsersAsEditors->Text = implode(', ', $objGender->getUserAsEditorsArray());

        $this->refreshDisplay($objGender->getId());
    }

    /**
     * Handles the click event for the delete button.
     *
     * This method is triggered when the delete button is clicked. It checks the state of the
     * selected item and displays the appropriate modal dialog box based on whether the
     * item is locked or not.
     *
     * @param ActionParams $params Parameters containing information about the action event.
     * @return void
     */
    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->intClick) {
            $objGender = Genders::load($this->intClick);
        }

        if ($objGender->getIsLocked() == 1) {
            $this->dlgModal1->showDialogBox();
        } else {
            $this->dlgModal2->showDialogBox();
        }
    }

    /**
     * Handles the deletion of an item and updates the UI accordingly.
     *
     * This method performs the deletion of a selected item, hides the relevant modal dialog,
     * executes JavaScript to modify the interface dynamically, and refreshes specific display elements.
     *
     * @param ActionParams $params The parameters associated with the current action, typically containing data about the clicked item.
     * @return void
     */
    public function deleteItem_Click(ActionParams $params)
    {
        if ($this->intClick) {
            $objGender = Genders::load($this->intClick);
        }

        $objGender->delete();
        $this->dlgModal1->hideDialogBox();

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->dtgGenders->removeCssClass('disabled');

        $this->refreshDisplay($objGender->getId());
    }

    /**
     * Handles the click event for hiding UI elements and resetting specific states.
     *
     * This method is triggered when a user interacts with a related action. It hides
     * specific UI elements by adding CSS classes, manages table state by removing
     * a CSS class, and hides a modal dialog box.
     *
     * @param ActionParams $params Parameters related to the triggered action event.
     * @return void
     */
    protected function hideItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript("
            $('.setting-wrapper').addClass('hidden');
            $('.form-actions-wrapper').addClass('hidden')
        ");

        $this->dtgGenders->removeCssClass('disabled');

        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Refreshes the display based on the provided gender object.
     *
     * This method updates the visibility and values of several UI elements
     * depending on the state of the provided gender object. It considers
     * conditions such as post date, update date, author, and the count of users
     * as editors to appropriately display or hide specific fields.
     *
     * @param object $objEdit The identifier or object used to load the gender data.
     * @return void
     */
    protected function refreshDisplay($objEdit)
    {
        $objGender = Genders::load($objEdit);

        if (!$objGender) {
            $this->lblPostDate->Display = false;
            $this->calPostDate->Display = false;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = false;
            $this->txtAuthor->Display = false;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        } else {
            if ($objGender->getPostDate() &&
                !$objGender->getPostUpdateDate() &&
                $objGender->getAuthor() &&
                !$objGender->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = false;
                $this->calPostUpdateDate->Display = false;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($objGender->getPostDate() &&
                $objGender->getPostUpdateDate() &&
                $objGender->getAuthor() &&
                !$objGender->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = false;
                $this->txtUsersAsEditors->Display = false;
            }

            if ($objGender->getPostDate() &&
                $objGender->getPostUpdateDate() &&
                $objGender->getAuthor() &&
                $objGender->countUsersAsEditors()) {
                $this->lblPostDate->Display = true;
                $this->calPostDate->Display = true;
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
                $this->lblAuthor->Display = true;
                $this->txtAuthor->Display = true;
                $this->lblUsersAsEditors->Display = true;
                $this->txtUsersAsEditors->Display = true;
            }

            $this->calPostDate->Text = $objGender->PostDate ? $objGender->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->calPostUpdateDate->Text = $objGender->PostUpdateDate ? $objGender->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            $this->txtAuthor->Text = $objGender->Author;
            $this->txtUsersAsEditors->Text = implode(', ', $objGender->getUserAsEditorsArray());
        }
    }

    /**
     * Hides the user-related input window and resets its fields.
     *
     * This method clears the text fields for post date, update date, author, and editors
     * while setting the visibility of related labels and input fields to false, effectively
     * hiding them from the user interface.
     *
     * @return void
     */
    protected function hideUserWindow()
    {
        $this->calPostDate->Text = '';
        $this->calPostUpdateDate->Text = '';
        $this->txtAuthor->Text = '';
        $this->txtUsersAsEditors->Text = '';

        $this->lblPostDate->Display = false;
        $this->calPostDate->Display = false;
        $this->lblPostUpdateDate->Display = false;
        $this->calPostUpdateDate->Display = false;
        $this->lblAuthor->Display = false;
        $this->txtAuthor->Display = false;
        $this->lblUsersAsEditors->Display = false;
        $this->txtUsersAsEditors->Display = false;
    }
}