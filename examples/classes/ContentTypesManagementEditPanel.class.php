<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class ContentTypesManagementsEditPanel extends Q\Control\Panel
{
    public $dlgModal1;

    protected $dlgToastr1;
    protected $dlgToastr2;

    public $lblContentName;
    public $txtContentName;

    public $lblContentTypes;
    public $lstContentTypes;

    public $lblViewTypes;
    public $lstViewTypes;

    public $btnSave;
    public $btnSaving;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objContentTypesManagement;

    protected $objDefaultFrontendTemplateCondition;
    protected $objDefaultFrontendTemplateClauses;

    protected $strTemplate = 'ContentTypesManagementEditPanel.tpl.php';

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
            $this->objContentTypesManagement = ContentTypesManagement::load($this->intId);
        } else {
            $this->objContentTypesManagement = new ContentTypesManagement();
        }

        $this->createInputs();
        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and creates input controls for content management,
     * including labels and selection lists for content name, content type, and view type.
     *
     * @return void
     */
    public function createInputs()
    {
        $this->lblContentName = new Q\Plugin\Control\Label($this);
        $this->lblContentName->Text = t('Content name');
        $this->lblContentName->addCssClass('col-md-3');
        $this->lblContentName->setCssStyle('font-weight', 400);
        $this->lblContentName->Required = true;

        $this->txtContentName = new Bs\TextBox($this);
        $this->txtContentName->Placeholder = t('Content new name');
        $this->txtContentName->Text = $this->objContentTypesManagement->ContentName ?
            $this->objContentTypesManagement->ContentName : null;
        $this->txtContentName->addWrapperCssClass('center-button');
        $this->txtContentName->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtContentName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtContentName->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtContentName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        //$this->txtContentName->setHtmlAttribute('required', 'required');

        $this->lblContentTypes = new Q\Plugin\Control\Label($this);
        $this->lblContentTypes->Text = t('Content type');
        $this->lblContentTypes->addCssClass('col-md-3');
        $this->lblContentTypes->setCssStyle('font-weight', 400);
        $this->lblContentTypes->Required = true;

        $this->lstContentTypes = new Q\Plugin\Select2($this);
        $this->lstContentTypes->MinimumResultsForSearch = -1;
        $this->lstContentTypes->Theme = 'web-vauu';
        $this->lstContentTypes->Width = '100%';
        $this->lstContentTypes->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstContentTypes->addItem(t('- Select a content type -'), null, true);
        $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems());
        $this->lstContentTypes->SelectedValue = $this->objContentTypesManagement->ContentType;
        //$this->lstContentTypes->setHtmlAttribute('required', 'required');

        $this->lblViewTypes = new Q\Plugin\Control\Label($this);
        $this->lblViewTypes->Text = t('View type');
        $this->lblViewTypes->addCssClass('col-md-3');
        $this->lblViewTypes->setCssStyle('font-weight', 400);
        $this->lblViewTypes->Required = true;

        $this->lstViewTypes = new Q\Plugin\Select2($this);
        $this->lstViewTypes->MinimumResultsForSearch = -1;
        $this->lstViewTypes->Theme = 'web-vauu';
        $this->lstViewTypes->Width = '100%';
        $this->lstViewTypes->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstViewTypes->addItem(t('- Select a content type -'), null, true);
        $this->lstViewTypes->addItems($this->lstViewTypeObject_GetItems());
        $this->lstViewTypes->SelectedValue = $this->objContentTypesManagement->ViewType;
        $this->lstViewTypes->setHtmlAttribute('required', 'required');
    }

    /**
     * Initialize and configure action buttons for content management.
     *
     * @return void
     */
    public function CreateButtons()
    {
        $this->btnSave = new Bs\Button($this);
        if (is_null($this->objContentTypesManagement->getContentName())) {
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
        if (is_null($this->objContentTypesManagement->getContentName())) {
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
     * Create and configure toastr notifications for success and error alerts.
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
     * Create item list for use by lstContentTypeObject
     *
     * @return array An array of content type names.
     */
    public function lstContentTypeObject_GetItems()
    {
        return ContentType::nameArray();
    }

    /**
     * Retrieves an array of view type names.
     *
     * @return array An array containing the names of view types.
     */
    public function lstViewTypeObject_GetItems() {
        return ViewType::nameArray();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the menu save button, saving or updating content details
     * and displaying appropriate notifications and button labels based on the form's state.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void This method does not return any value.
     */
    public function btnMenuSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtContentName->Text && $this->lstContentTypes->SelectedValue && $this->lstViewTypes->SelectedValue) {
            $this->objContentTypesManagement->setContentName($this->txtContentName->Text);
            $this->objContentTypesManagement->setContentType($this->lstContentTypes->SelectedValue);
            $this->objContentTypesManagement->setViewType($this->lstViewTypes->SelectedValue);
            $this->objContentTypesManagement->save();

            if (is_null($this->objContentTypesManagement->getContentName())) {
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

            if (!$this->txtContentName->Text) {
                $this->txtContentName->addCssClass('has-error');
            } else {
                $this->txtContentName->addCssClass('has-success');
            }
            if (!$this->lstContentTypes->SelectedValue) {
                $this->lstContentTypes->addCssClass('has-error');
            } else {
                $this->lstContentTypes->addCssClass('has-success');
            }
            if (!$this->lstViewTypes->SelectedValue) {
                $this->lstViewTypes->addCssClass('has-error');
            } else {
                $this->lstViewTypes->addCssClass('has-success');
            }
        }
    }

    /**
     * Handles the save and close action for the menu button, saving content information and redirecting or notifying as needed.
     *
     * @param ActionParams $params Parameters associated with the action event.
     * @return void
     */
    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtContentName->Text && $this->lstContentTypes->SelectedValue && $this->lstViewTypes->SelectedValue) {
            $this->objContentTypesManagement->setContentName($this->txtContentName->Text);
            $this->objContentTypesManagement->setContentType($this->lstContentTypes->SelectedValue);
            $this->objContentTypesManagement->setViewType($this->lstViewTypes->SelectedValue);
            $this->objContentTypesManagement->save();

            $this->redirectToListPage();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the click event for the cancel button on the menu.
     *
     * @param ActionParams $params The parameters associated with the action event.
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
     * Redirects the user to the previous page in the browser history.
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