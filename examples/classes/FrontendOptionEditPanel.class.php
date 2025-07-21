<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\ListItem;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Query\Clause\ClauseInterface as QQClause;
use QCubed\Query\QQ;

class FrontendOptionEditPanel extends Q\Control\Panel
{
    public $dlgModal1;

    protected $dlgToastr1;
    protected $dlgToastr2;

    public $lblFrontendTemplateName;
    public $txtFrontendTemplateName;

    public $lblContentTypesManagement;
    public $lstContentTypesManagement;

    public $lblClassName;
    public $txtClassName;

    public $lblContentType;
    public $txtContentType;

    public $lblViewType;
    public $txtViewType;

    public $lblFrontendTemplatePath;
    public $txtFrontendTemplatePath;

    public $lblStatus;
    public $lstStatus;

    public $btnSave;
    public $btnSaving;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objFrontendOptions;
    protected $objContentTypesManagement;

    protected $objContentTypesManagementCondition;
    protected $objContentTypesManagementClauses;

    protected $strTemplate = 'FrontendOptionEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        if (!empty($this->intId)) {
            $this->objFrontendOptions = FrontendOptions::load($this->intId);
            $this->objContentTypesManagement = ContentTypesManagement::load($this->intId);
        } else {
            $this->objFrontendOptions = new FrontendOptions();
            $this->objContentTypesManagement = new ContentTypesManagement();
        }

        $this->createInputs();
        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Retrieves a list of ListItem objects representing content types management entries.
     *
     * @return ListItem[] An array of ListItem objects, each created from a ContentTypesManagement object.
     */
    public function lstContentTypesManagement_GetItems() {
        $a = array();
        $objCondition = $this->objContentTypesManagementCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objContentTypesManagementCursor = ContentTypesManagement::queryCursor($objCondition, $this->objContentTypesManagementClauses);

        // Iterate through the Cursor
        while ($objContentTypesManagement = ContentTypesManagement::instantiateCursor($objContentTypesManagementCursor)) {
            $objListItem = new ListItem($objContentTypesManagement->__toString(), $objContentTypesManagement->Id);
            if (($this->objFrontendOptions->ContentTypesManagement) && ($this->objFrontendOptions->ContentTypesManagement->Id == $objContentTypesManagement->Id))
                $objListItem->Selected = true;
            $a[] = $objListItem;
        }
        return $a;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the input controls for frontend template options.
     *
     * This method sets up labels and input fields for frontend template name,
     * content type management, class name, template path, and status. It assigns
     * necessary properties like text, placeholder, CSS classes, and actions to each
     * control to prepare them for user interaction.
     *
     * @return void
     */
    public function createInputs()
    {
        $this->lblFrontendTemplateName = new Q\Plugin\Control\Label($this);
        $this->lblFrontendTemplateName->Text = t('Frontend template name');
        $this->lblFrontendTemplateName->addCssClass('col-md-3');
        $this->lblFrontendTemplateName->setCssStyle('font-weight', 400);
        $this->lblFrontendTemplateName->Required = true;

        $this->txtFrontendTemplateName = new Bs\TextBox($this);
        $this->txtFrontendTemplateName->Placeholder = t('Frontend template new name');
        $this->txtFrontendTemplateName->Text = $this->objFrontendOptions->FrontendTemplateName ?? null;
        $this->txtFrontendTemplateName->addWrapperCssClass('center-button');
        $this->txtFrontendTemplateName->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtFrontendTemplateName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtFrontendTemplateName->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtFrontendTemplateName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        //$this->txtFrontendTemplateName->setHtmlAttribute('required', 'required');

        $this->lblContentTypesManagement = new Q\Plugin\Control\Label($this);
        $this->lblContentTypesManagement->Text = t('Custom content type');
        $this->lblContentTypesManagement->addCssClass('col-md-3');
        $this->lblContentTypesManagement->setCssStyle('font-weight', 400);
        $this->lblContentTypesManagement->Required = true;

        $this->lstContentTypesManagement = new Q\Plugin\Select2($this);
        $this->lstContentTypesManagement->MinimumResultsForSearch = -1;
        $this->lstContentTypesManagement->Theme = 'web-vauu';
        $this->lstContentTypesManagement->Width = '100%';
        $this->lstContentTypesManagement->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstContentTypesManagement->addItem(t('- Select custom content type -'), null, true);
        $this->lstContentTypesManagement->addItems($this->lstContentTypesManagement_GetItems());
        $this->lstContentTypesManagement->SelectedValue = $this->objFrontendOptions->ContentTypesManagementId;
        //$this->lstContentTypesManagement->setHtmlAttribute('required', 'required');

        $this->lblClassName = new Q\Plugin\Control\Label($this);
        $this->lblClassName->Text = t('Frontend class name');
        $this->lblClassName->addCssClass('col-md-3');
        $this->lblClassName->setCssStyle('font-weight', 400);
        $this->lblClassName->Required = true;

        $this->txtClassName = new Bs\TextBox($this);
        $this->txtClassName->Placeholder = t('New class name');
        $this->txtClassName->Text = $this->objFrontendOptions->ClassName ?
            $this->objFrontendOptions->ClassName : null;
        $this->txtClassName->addWrapperCssClass('center-button');
        $this->txtClassName->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtClassName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtClassName->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtClassName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        //$this->txtClassName->setHtmlAttribute('required', 'required');

        $this->lblFrontendTemplatePath = new Q\Plugin\Control\Label($this);
        $this->lblFrontendTemplatePath->Text = t('Frontend template path');
        $this->lblFrontendTemplatePath->addCssClass('col-md-3');
        $this->lblFrontendTemplatePath->setCssStyle('font-weight', 400);
        $this->lblFrontendTemplatePath->Required = true;

        $this->txtFrontendTemplatePath = new Bs\TextBox($this);
        $this->txtFrontendTemplatePath->Placeholder = t('Frontend template new path');
        $this->txtFrontendTemplatePath->Text = $this->objFrontendOptions->FrontendTemplatePath ?
            $this->objFrontendOptions->FrontendTemplatePath : null;
        $this->txtFrontendTemplatePath->addWrapperCssClass('center-button');
        $this->txtFrontendTemplatePath->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtFrontendTemplatePath->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtFrontendTemplatePath->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtFrontendTemplatePath->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        //$this->txtFrontendTemplatePath->setHtmlAttribute('required', 'required');

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->addCssClass('col-md-3');
        $this->lblStatus->Required = true;

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->SelectedValue = $this->objFrontendOptions->Status;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';
    }

    /**
     * Creates and configures three buttons: Save, Save and Close, and Cancel.
     * The Save and Save and Close buttons are initialized with text and styled
     * based on the presence of a class name in `objFrontendOptions`.
     * The buttons are also linked to specific event handlers to perform actions
     * when clicked. The button IDs are stored for quick access.
     *
     * @return void
     */
    public function CreateButtons()
    {
        $this->btnSave = new Bs\Button($this);
        if (is_null($this->objFrontendOptions->getClassName())) {
            $this->btnSave->Text = t('Save');
        } else {
            $this->btnSave->Text = t('Update');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSaveButtonId = $this->btnSave->ControlId;

        $this->btnSaving = new Bs\Button($this);
        if (is_null($this->objFrontendOptions->getClassName())) {
            $this->btnSaving->Text = t('Save and close');
        } else {
            $this->btnSaving->Text = t('Update and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
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
     * Initializes two Toastr notification instances with predefined settings for
     * success and error alerts. The first Toastr is configured to display a
     * success message when a post has been successfully saved or modified. The
     * second Toastr is set up to show an error message when there are missing
     * required fields.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('All fields are required!');
        $this->dlgToastr2->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the menu save button. Validates all input fields
     * and updates the frontend options. If any input is missing, it applies error
     * styling to the respective field.
     *
     * @param ActionParams $params An object containing parameters for the action.
     * @return void
     */
    public function btnMenuSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtFrontendTemplateName->Text &&
            $this->lstContentTypesManagement->SelectedValue &&
            $this->txtClassName->Text &&
            $this->txtFrontendTemplatePath->Text) {

            $this->objFrontendOptions->setFrontendTemplateName($this->txtFrontendTemplateName->Text);
            $this->objFrontendOptions->setContentTypesManagementId($this->lstContentTypesManagement->SelectedValue);
            $this->objFrontendOptions->setClassName($this->txtClassName->Text);
            $this->objFrontendOptions->setFrontendTemplatePath($this->txtFrontendTemplatePath->Text);
            $this->objFrontendOptions->setStatus($this->lstStatus->SelectedValue);
            $this->objFrontendOptions->save();

            if (is_null($this->objFrontendOptions->getClassName())) {
                $strSave_translate = t('Save');
                $strSaveAndClose_translate = t('Save and close');
                Application::executeJavaScript(sprintf("jQuery($this->strSaveButtonId).text('{$strSave_translate}');"));
                Application::executeJavaScript(sprintf("jQuery($this->strSavingButtonId).text('{$strSaveAndClose_translate}');"));
            } else {
                $strUpdate_translate = t('Update');
                $strUpdateAndClose_translate = t('Update and close');
                Application::executeJavaScript(sprintf("jQuery($this->strSaveButtonId).text('{$strUpdate_translate}');"));
                Application::executeJavaScript(sprintf("jQuery($this->strSavingButtonId).text('{$strUpdateAndClose_translate}');"));
            }

            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr2->notify();

            if (!$this->txtFrontendTemplateName->Text) {
                $this->txtFrontendTemplateName->addCssClass('has-error');
            } else {
                $this->txtFrontendTemplateName->addCssClass('has-success');
            }
            if (!$this->lstContentTypesManagement->SelectedValue) {
                $this->lstContentTypesManagement->addCssClass('has-error');
            } else {
                $this->lstContentTypesManagement->addCssClass('has-success');
            }
            if (!$this->txtClassName->Text) {
                $this->txtClassName->addCssClass('has-error');
            } else {
                $this->txtClassName->addCssClass('has-success');
            }
            if (!$this->txtFrontendTemplatePath->Text) {
                $this->txtFrontendTemplatePath->addCssClass('has-error');
            } else {
                $this->txtFrontendTemplatePath->addCssClass('has-success');
            }
        }
    }

    /**
     * Handles the event triggered by clicking the "Save and Close" button in the menu.
     *
     * @param ActionParams $params Contains parameters related to the action event.
     * @return void
     */
    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtFrontendTemplateName->Text &&
            $this->lstContentTypesManagement->SelectedValue &&
            $this->txtClassName->Text &&
            $this->txtFrontendTemplatePath->Text) {

            $this->objFrontendOptions->setFrontendTemplateName($this->txtFrontendTemplateName->Text);
            $this->objFrontendOptions->setContentTypesManagementId($this->lstContentTypesManagement->SelectedValue);
            $this->objFrontendOptions->setClassName($this->txtClassName->Text);
            $this->objFrontendOptions->setFrontendTemplatePath($this->txtFrontendTemplatePath->Text);
            $this->objFrontendOptions->setStatus($this->lstStatus->SelectedValue);
            $this->objFrontendOptions->save();

            $this->redirectToListPage();
        } else {
            $this->dlgToastr2->notify();

            if (!$this->txtFrontendTemplateName->Text) {
                $this->txtFrontendTemplateName->addCssClass('has-error');
            } else {
                $this->txtFrontendTemplateName->addCssClass('has-success');
            }
            if (!$this->lstContentTypesManagement->SelectedValue) {
                $this->lstContentTypesManagement->addCssClass('has-error');
            } else {
                $this->lstContentTypesManagement->addCssClass('has-success');
            }
            if (!$this->txtClassName->Text) {
                $this->txtClassName->addCssClass('has-error');
            } else {
                $this->txtClassName->addCssClass('has-success');
            }
            if (!$this->txtFrontendTemplatePath->Text) {
                $this->txtFrontendTemplatePath->addCssClass('has-error');
            } else {
                $this->txtFrontendTemplatePath->addCssClass('has-success');
            }

        }
    }

    /**
     * Handles the click event for the menu cancel button.
     *
     * Redirects the user to the list page when the button is clicked.
     *
     * @param ActionParams $params The parameters related to the action triggered by the button click.
     * @return void
     */
    public function btnMenuCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->redirectToListPage();
    }

    /**
     * Redirects the browser to the previous page by executing a JavaScript command.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        Application::executeJavaScript(sprintf("history.go(-1);"));
    }
}