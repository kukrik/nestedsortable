<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;

class ArticleCategoryEditPanel extends Q\Control\Panel
{
    protected $dlgToastr1;
    protected $dlgToastr2;

    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;
    public $dlgModal4;

    public $lblName;
    public $txtName;

    public $lblStatus;
    public $lstStatus;

    public $lblPostDate;
    public $calPostDate;

    public $lblPostUpdateDate;
    public $calPostUpdateDate;

    public $btnSave;
    public $btnSaving;
    public $btnDelete;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objCategoryOfArticle;
    protected $objCategoryIds = [];
    protected $objCategoryNames = [];
    protected $objCompressTexts = [];
    protected $objMenuTexts;

    protected $strTemplate = 'ArticleCategoryEditPanel.tpl.php';

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
            $this->objCategoryOfArticle = CategoryOfArticle::load($this->intId);
        } else {

            $this->objCategoryOfArticle = new CategoryOfArticle();
            $this->objCategoryOfArticle->setPostDate(Q\QDateTime::Now());
            // does nothing
        }

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->CheckCategories();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Processes and filters articles by category and retrieves corresponding menu content.
     *
     * @return string A comma-separated string of menu texts associated with the specified category IDs.
     */
    public function CheckCategories()
    {
        $objArticleArray = Article::loadAll();

        foreach ($objArticleArray as $objArticle) {
            if ($objArticle->CategoryId) {
                $this->objCategoryIds[] = $objArticle->CategoryId;
            }
        }

        foreach ($objArticleArray as $objArticle) {
            if ($objArticle->CategoryId == $this->intId) {
                $this->objCategoryNames[] = $objArticle->MenuContentId;
            }
        }
        foreach ($this->objCategoryNames as $objCategoryName) {
           if ($objMenuContent = MenuContent::load($objCategoryName)) {
              $this->objCompressTexts[] = $objMenuContent->MenuText;
           }
        }
        $this->objMenuTexts = implode(', ', $this->objCompressTexts);
        return $this->objMenuTexts;
    }

    /**
     * Handles the delete item action when the delete button is clicked. Deletes the associated category of the article
     * based on the action parameter received and redirects to the list page if the condition is met.
     *
     * @param ActionParams $params The parameters received from the action, including an action parameter to check
     *                             against to proceed with the deletion.
     * @return void
     */
    public function deleteItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal4->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($params->ActionParameter == "pass") {
            $this->objCategoryOfArticle->delete();
            $this->redirectToListPage();
        }
        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Handles the click event for the abort item action.
     *
     * @param ActionParams $params Parameters for the action being executed.
     * @return void
     */
    public function abortItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal4->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->redirectToListPage();
        $this->dlgModal3->hideDialogBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures input controls for the form such as labels, text boxes, and radio lists.
     *
     * @return void
     */
    public function createInputs()
    {
        $this->lblName = new Q\Plugin\Control\Label($this);
        $this->lblName->Text = t('Name');
        $this->lblName->addCssClass('col-md-3');
        $this->lblName->setCssStyle('font-weight', 400);
        $this->lblName->Required = true;

        $this->txtName = new Bs\TextBox($this);
        $this->txtName->Placeholder = t('New category');
        $this->txtName->Text = $this->objCategoryOfArticle->Name ? $this->objCategoryOfArticle->Name : null;
        $this->txtName->addWrapperCssClass('center-button');
        $this->txtName->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnSave_Click'));
        $this->txtName->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtName->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnCancel_Click'));
        $this->txtName->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtName->setHtmlAttribute('required', 'required');

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Is activated');
        $this->lblStatus->addCssClass('col-md-3');
        $this->lblStatus->setCssStyle('font-weight', 400);
        $this->lblStatus->Required = true;

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Active'), 2 => t('Inactive')]);
        $this->lstStatus->SelectedValue = $this->objCategoryOfArticle->IsEnabled ? $this->objCategoryOfArticle->IsEnabled : null;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->addCssClass('col-md-3');
        $this->lblPostDate->setCssStyle('font-weight', 400);

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objCategoryOfArticle->PostDate ? $this->objCategoryOfArticle->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostDate->setCssStyle('font-weight', 400);
        $this->calPostDate->setCssStyle('font-size', '13px');
        $this->calPostDate->setCssStyle('line-height', 2.5);

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->addCssClass('col-md-3');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 400);

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objCategoryOfArticle->PostUpdateDate ? $this->objCategoryOfArticle->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 400);
        $this->calPostUpdateDate->setCssStyle('font-size', '13px');
        $this->calPostUpdateDate->setCssStyle('line-height', 2.5);

        if ($this->intId) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
        } else {
            $this->lblPostDate->Display = false;
            $this->calPostDate->Display = false;
        }

        if ($this->objCategoryOfArticle->getPostUpdateDate()) {
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
        } else {
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
        }
    }

    /**
     * Initializes and configures a set of buttons used in the interface, including Save, Save and Close,
     * Delete, and Cancel buttons. Each button is assigned specific text, CSS classes, actions upon clicking,
     * and various properties depending on the state of the associated category of article.
     *
     * @return void
     */
    public function CreateButtons()
    {
        $this->btnSave = new Bs\Button($this);
        if (is_null($this->objCategoryOfArticle->getName())) {
            $this->btnSave->Text = t('Save');
        } else {
            $this->btnSave->Text = t('Update');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnSave_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSaveButtonId = $this->btnSave->ControlId;

        $this->btnSaving = new Bs\Button($this);
        if (is_null($this->objCategoryOfArticle->getName())) {
            $this->btnSaving->Text = t('Save and close');
        } else {
            $this->btnSaving->Text = t('Update and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;

        $this->btnDelete = new Bs\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->addWrapperCssClass('center-button');
        $this->btnDelete->CausesValidation = false;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnDelete_Click'));

        if (is_null($this->objCategoryOfArticle->getName())) {
            $this->btnDelete->Display = false;
        } else {
            $this->btnDelete->Display = true;
        }

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnCancel_Click'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and configures two Toastr notifications for user feedback. The first notification indicates
     * successful operations with a success alert type and a positive message. The second notification
     * signals an error condition with an error alert type and a related message regarding category naming.
     * Both notifications are configured to appear at the top center of the screen with a progress bar.
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
     * Initializes and configures multiple modal dialog instances for user interactions.
     *
     * @return void
     */
    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the article category?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The article category cannot
                                    be deactivated at this time!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this article category,
                                    just must release article categories related to previously created article.</p>');
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'abortItem_Click'));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal4->Title = t("Warning");
        $this->dlgModal4->HeaderClasses = 'btn-danger';
        $this->dlgModal4->addCloseButton(t("I understand"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the save button, updating article category details
     * based on the current form input and predefined conditions.
     *
     * @param ActionParams $params The parameters related to the action event triggered by the user.
     * @return void
     */
    public function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal4->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (in_array($this->intId, $this->objCategoryIds) && $this->lstStatus->SelectedValue == 2) {
            $this->lstStatus->SelectedValue = 1;
            $this->objCategoryOfArticle->setIsEnabled(1);
            $this->dlgModal3->showDialogBox();
        } else if ($this->txtName->Text) {
            $this->objCategoryOfArticle->setName($this->txtName->Text);
            $this->objCategoryOfArticle->setIsEnabled($this->lstStatus->SelectedValue);

            $this->objCategoryOfArticle->setPostUpdateDate(Q\QDateTime::Now());
            $this->calPostUpdateDate->Text = $this->objCategoryOfArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;

            $this->objCategoryOfArticle->save();

            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->btnDelete->Display = true;

            if (is_null($this->objCategoryOfArticle->getName())) {
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
        }
    }

    /**
     * Handles the event when the "Save and Close" button is clicked. Updates the category of an article
     * based on user input and status, and saves the changes. It displays various UI components
     * and dialogs based on specific conditions.
     *
     * @param ActionParams $params Parameters for the action triggered by the button click.
     * @return void
     */
    public function btnSaveClose_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal4->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (in_array($this->intId, $this->objCategoryIds) && $this->lstStatus->SelectedValue == 2) {
            $this->lstStatus->SelectedValue = 1;
            $this->objCategoryOfArticle->setIsEnabled(1);
            $this->dlgModal3->showDialogBox();
        } else if ($this->txtName->Text) {
            $this->objCategoryOfArticle->setName($this->txtName->Text);
            $this->objCategoryOfArticle->setIsEnabled($this->lstStatus->SelectedValue);

            if ($this->intId) {
                $this->objCategoryOfArticle->setPostUpdateDate(Q\QDateTime::Now());
                $this->calPostUpdateDate->Text = $this->objCategoryOfArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
                $this->lblPostUpdateDate->Display = true;
                $this->calPostUpdateDate->Display = true;
            }

            $this->objCategoryOfArticle->save();

            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->btnDelete->Display = true;

            $this->redirectToListPage();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    /**
     * Handles the click event of the delete button. It checks if the current category ID is in the list
     * of category IDs and shows the appropriate modal dialog based on that condition.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal4->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (in_array($this->intId, $this->objCategoryIds)) {
            $this->dlgModal2->showDialogBox();
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The article category cannot be
                                    deleted at this time!</p>
                                    <p style="line-height: 15px;">Articles related to the category: <span style="color: #ff0000;">
                                    ' . $this->objMenuTexts . '</span>.</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this category, just
                                    must release categories related to previously created articles.</p>');
        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the click event for the cancel button. Upon invocation, this method redirects the user to the list page.
     *
     * @param ActionParams $params The parameters passed from the triggering action.
     * @return void
     */
    public function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal4->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->redirectToListPage();
    }

    /**
     * Executes JavaScript to redirect the user to the previous page in the browser history.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        Application::executeJavaScript(sprintf("history.go(-1);"));
    }
}