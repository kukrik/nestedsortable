<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;

class PageMetadataPanel extends Q\Control\Panel
{
    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;

    protected $dlgToastr;
    
    protected $lblInfo;

    public $lblKeywords;
    public $txtKeywords;

    public $lblDescription;
    public $txtDescription;

    public $lblAuthor;
    public $txtAuthor;

    public $btnSave;
    public $btnSaving;
    public $btnDelete;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objMenuContent;
    protected $objArticle;
    protected $objMetadata;

    protected $intTemporaryId;

    protected $strTemplate = 'PageMetaDataPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->objMetadata = Metadata::loadByIdFromMetadata($this->intId);
        $this->objArticle = Article::loadByIdFromContentId($this->intId);
        $this->objMenuContent = MenuContent::load($this->intId);

        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes and configures the input fields and labels for metadata entry.
     * This includes fields for keywords, description, and author metadata,
     * as well as displaying informational alerts and handling input events.
     *
     * @return void
     */
    public function createInputs()
    {
        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Display = true;
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
        $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('These fields can be left blank. If there is a special need to highlight the special
                                features of one page or another, which search engines might find and offer to people.
                                By default, the global metadata of the website is sufficient.');

        $this->lblKeywords = new Q\Plugin\Control\Label($this);
        $this->lblKeywords->Text = t('Keywords of the metadata');
        $this->lblKeywords->addCssClass('col-md-3');
        $this->lblKeywords->setCssStyle('font-weight', 400);

        $this->txtKeywords = new Bs\TextBox($this);
        $this->txtKeywords->Text = $this->objMetadata->Keywords ?? null;
        $this->txtKeywords->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtKeywords->Rows = 3;
        $this->txtKeywords->addWrapperCssClass('center-button');
        $this->txtKeywords->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtKeywords->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtKeywords->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtKeywords->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblDescription = new Q\Plugin\Control\Label($this);
        $this->lblDescription->Text = t('Description of the metadata');
        $this->lblDescription->addCssClass('col-md-3');
        $this->lblDescription->setCssStyle('font-weight', 400);

        $this->txtDescription = new Bs\TextBox($this);
        $this->txtDescription->Text = $this->objMetadata->Description ?? null;
        $this->txtDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtDescription->Rows = 3;
        $this->txtDescription->addWrapperCssClass('center-button');
        $this->txtDescription->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtDescription->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtDescription->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtDescription->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->addCssClass('col-md-3');
        $this->lblAuthor->setCssStyle('font-weight', 400);

        $this->txtAuthor = new Bs\TextBox($this);
        $this->txtAuthor->Text = $this->objMetadata->Author ?? null;
        $this->txtAuthor->addWrapperCssClass('center-button');
        $this->txtAuthor->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtAuthor->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtAuthor->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtAuthor->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
    }

    /**
     * Initializes and creates buttons for Save, Save and Close, Delete, and Cancel actions.
     * Each button is set with specific properties such as text, CSS classes, and actions triggered by clicking events.
     * The Save and Save and Close buttons are prepared for fast transmission with their respective IDs.
     *
     * @return void
     */
    public function CreateButtons()
    {
        $this->btnSave = new Bs\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnMenuSave_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSaveButtonId = $this->btnSave->ControlId;

        $this->btnSaving = new Bs\Button($this);
        $this->btnSaving->Text = t('Save and close');
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnMenuSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;

        $this->btnDelete = new Bs\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->addWrapperCssClass('center-button');
        $this->btnDelete->CausesValidation = false;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnMenuDelete_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
    }

    /**
     * Creates a Toastr dialog object and sets its properties for displaying notifications.
     *
     * @return void
     */
    protected function createToastr()
    {
        $this->dlgToastr = new Q\Plugin\Toastr($this);
        $this->dlgToastr->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
        $this->dlgToastr->ProgressBar = true;
    }

    /**
     * Initializes modal dialogs used in the application.
     *
     * This method creates two modals with different purposes and configurations.
     * The first modal is used as a warning confirmation dialog for the deletion
     * of specific metadata and includes options for confirmation or cancellation.
     * It is styled with a danger theme and triggers an AJAX action when a decision is made.
     *
     * The second modal provides a tip message when metadata cannot be saved
     * and prompts the user with an OK button for dismissal. It features a dark blue theme
     * and also triggers an AJAX action on interaction.
     *
     * @return void
     */
    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to delete the specific metadata of this page?</p>
                            <p style="line-height: 25px; margin-bottom: -3px;">If desired, you can later re-write!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), t('This menu metadata has been permanently deleted.'), false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deletedItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Praegu ei saa siin selle artikli metaandmed salvestada.</p><p style="line-height: 25px; margin-bottom: -3px;">
                                Enne peab artiklit sisuga täitma ja salvestama.</p>');
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
        $this->dlgModal2->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'recallItem_Click'));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal3->Title = t("Warning");
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addCloseButton(t("I understand"));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the save action triggered from the menu button click event.
     *
     * @param ActionParams $params Parameters associated with the action event.
     * @return void
     */
    public function btnMenuSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->renderActionsWithOrWithoutId();

        if ($this->objMenuContent->getContentType() == 2) {
            if ($this->objArticle->getTitle()) {
                $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
                $this->objArticle->save();
                $this->dlgToastr->notify();
            } else {
                $this->dlgModal2->showDialogBox();
            }
        }

        $this->objMetadata->setKeywords($this->txtKeywords->Text);
        $this->objMetadata->setDescription($this->txtDescription->Text);
        $this->objMetadata->setAuthor($this->txtAuthor->Text);
        $this->objMetadata->save();

        if (($this->objMetadata->getKeywords() == null) ||
            ($this->objMetadata->getKeywords() == null &&
                $this->objMetadata->getDescription() == null) ||
            ($this->objMetadata->getKeywords() == null &&
                $this->objMetadata->getDescription() == null &&
                $this->objMetadata->getAuthor() == null)
        ) {
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
    }

    /**
     * Handles the click event for the "Save and Close" button in the menu.
     * The method validates and saves the article data, updates metadata, and redirects to the list page upon successful operations.
     *
     * @param ActionParams $params The parameters from the action triggering this method, typically containing context or state information relevant to the action.
     * @return void This method does not return a value.
     */
    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->renderActionsWithOrWithoutId();

        if ($this->objMenuContent->getContentType() == 2) {
            if ($this->objArticle->getTitle()) {
                $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
                $this->objArticle->save();
                $this->redirectToListPage();
            }  else {
                $this->dlgModal2->showDialogBox();
            }
        }

        $this->objMetadata->setKeywords($this->txtKeywords->Text);
        $this->objMetadata->setDescription($this->txtDescription->Text);
        $this->objMetadata->setAuthor($this->txtAuthor->Text);
        $this->objMetadata->save();

        $this->redirectToListPage();
    }

    /**
     * Handles the button click event for deleting a menu item.
     *
     * @param ActionParams $params The parameters passed to the action, containing context-specific information about the event.
     * @return void
     */
    public function btnMenuDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->objMetadata->getKeywords() || $this->objMetadata->getDescription() || $this->objMetadata->getAuthor()) {
            $this->dlgModal1->showDialogBox();
        }
    }

    /**
     * Handles the event when an item is deleted. It resets the metadata fields to null,
     * updates the related text fields to empty strings, adjusts the button labels,
     * and hides the dialog box.
     *
     * @param ActionParams $params Parameters associated with the action.
     * @return void
     */
    public function deletedItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->objMetadata->setKeywords(null);
        $this->objMetadata->setDescription(null);
        $this->objMetadata->setAuthor(null);
        $this->objMetadata->save();

        $this->txtKeywords->Text = '';
        $this->txtDescription->Text = '';
        $this->txtAuthor->Text = '';

        $strSave_translate = t('Save');
        $strSaveAndClose_translate = t('Save and close');
        Application::executeJavaScript(sprintf("jQuery($this->strSaveButtonId).text('{$strSave_translate}');"));
        Application::executeJavaScript(sprintf("jQuery($this->strSavingButtonId).text('{$strSaveAndClose_translate}');"));

        $this->dlgModal1->hideDialogBox();
    }

    /**
     * Resets the text fields for keywords, description, and author to empty strings
     * and hides the dialog box.
     *
     * @param ActionParams $params The parameters associated with the action event.
     * @return void
     */
    public function recallItem_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->txtKeywords->Text = '';
        $this->txtDescription->Text = '';
        $this->txtAuthor->Text = '';

        $this->dlgModal2->hideDialogBox();
    }

    /**
     * Renders actions based on the presence or absence of an identifier.
     *
     * This method checks if the identifier (intId) is present. If so, it compares
     * text fields for keywords, description, and author with existing metadata values.
     * If differences are found and the content type equals 2, it updates the article's
     * post update date.
     * If the identifier is absent and the content type equals 2, it sets the post update
     * date to null.
     *
     * @return void
     */
    public function renderActionsWithOrWithoutId()
    {
        if (strlen($this->intId)) {
            if ($this->txtKeywords->Text !== $this->objMetadata->getKeywords() ||
                $this->txtDescription->Text !== $this->objMetadata->getDescription() ||
                $this->txtAuthor->Text !== $this->objMetadata->getAuthor()
            ) {
                // $this->objArticle->getAssignedEditorsNameById($_SESSION['logged_user_id'])); // Approximately example here etc...
                // For example, John Doe is a logged user with his session
                if ($this->objMenuContent->getContentType() == 2) {
                    $this->objArticle->getAssignedEditorsNameById(2);
                    $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
                }
            }
        } else {
            if ($this->objMenuContent->getContentType() == 2) {
                $this->objArticle->setPostUpdateDate(null);
            }
        }
    }


    /**
     * Handles the click event for the menu cancel button. This method redirects
     * the user to the list page.
     *
     * @param ActionParams $params Contains the parameters passed during the click event.
     * @return void
     */
    public function btnMenuCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->redirectToListPage();
    }

    /**
     * Redirects the user to the menu edit page using the current object's ID.
     *
     * @return void
     */
    protected function redirectToListPage()
    {
        Application::redirect('menu_edit.php?id=' . $this->intId);
    }

}