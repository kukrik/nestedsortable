<?php

use QCubed as Q;
use QCubed\Action\Ajax;
use QCubed\Action\Terminate;
use QCubed\Bootstrap as Bs;
use QCubed\Event\Change;
use QCubed\Event\EnterKey;
use QCubed\Event\Input;
use QCubed\Folder;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;
use QCubed\QString;

class SliderListSettings extends Q\Control\Panel
{
    public $dlgModal1;
    public $dlgModal2;
    public $dlgModal3;

    public $dlgToastr1;
    public $dlgToastr2;

    public $btnSave;
    public $btnCancel;
    public $txtTitle;

    protected $dtgSliders;
    protected $lstItemsPerPage;
    protected $objSlidersSettings;
    protected $intLoggedUserId;

    protected $strTemplate = 'SliderListSettings.tpl.php';

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
         * Must to save something here $this->objNews->setUserId(logged user session);
         * or something similar...
         *
         * Options to do this are left to the developer.
         **/

        // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
        // For example, John Doe is a logged user with his session
        $this->intLoggedUserId = 2;

        $this->createTable();
        $this->createInputs();
        $this->createButtons();
        $this->createModals();
        $this->createToastr();
    }

    protected function createTable()
    {
        $this->dtgSliders = new Q\Plugin\VauuTable($this);
        $this->dtgSliders->CssClass = "table vauu-table table-hover table-responsive";

        $col = $this->dtgSliders->createNodeColumn(t('Title'), QQN::SlidersSettings()->Title);

        $col = $this->dtgSliders->createNodeColumn(t('Status'), QQN::SlidersSettings()->StatusObject);
        $col->HtmlEntities = false;

        $col = $this->dtgSliders->createNodeColumn(t('Created'), QQN::SlidersSettings()->PostDate);
        $col->Format = 'DD.MM.YYYY hhhh:mm';

        $col = $this->dtgSliders->createNodeColumn(t('Modified'), QQN::SlidersSettings()->PostUpdateDate);
        $col->Format = 'DD.MM.YYYY hhhh:mm';

        $this->dtgSliders->Paginator = new Bs\Paginator($this);
        $this->dtgSliders->Paginator->LabelForPrevious = t('Previous');
        $this->dtgSliders->Paginator->LabelForNext = t('Next');
        $this->dtgSliders->ItemsPerPage = 10;

        $this->dtgSliders->UseAjax = true;
        $this->dtgSliders->SortColumnIndex = 2;
        $this->dtgSliders->SortDirection = -1;
        $this->dtgSliders->setDataBinder('dtgSliders_Bind', $this);
        $this->dtgSliders->RowParamsCallback = [$this, 'dtgSliders_GetRowParams'];
        $this->dtgSliders->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')),
            new Q\Action\AjaxControl($this, 'dtgSlidersRow_Click'));

        $this->lstItemsPerPage = new Q\Plugin\Select2($this);
        $this->lstItemsPerPage->addCssFile(QCUBED_FILEUPLOAD_ASSETS_URL . '/css/select2-web-vauu.css');
        $this->lstItemsPerPage->MinimumResultsForSearch = -1;
        $this->lstItemsPerPage->Theme = 'web-vauu';
        $this->lstItemsPerPage->Width = '100%';
        $this->lstItemsPerPage->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstItemsPerPage->SelectedValue = $this->dtgSliders->ItemsPerPage;
        $this->lstItemsPerPage->addItems(array(10, 25, 50, 100));
        $this->lstItemsPerPage->AddAction(new Change(), new Q\Action\AjaxControl($this, 'lstItemsPerPage_Change'));
    }

    protected function createInputs()
    {
        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Change the title of the carousel');
        $this->txtTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtTitle->setCssStyle('float', 'left');
        $this->txtTitle->setCssStyle('margin-right', '10px');
        $this->txtTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;
        $this->txtTitle->Width = '30%';
        $this->txtTitle->Display = false;
        $this->txtTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnSave_Click'));
        $this->txtTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnCancel_Click'));
        $this->txtTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
    }

    public function createButtons()
    {
        $this->btnSave = new Q\Plugin\Button($this);
        $this->btnSave->Text = t('Update');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->setCssStyle('float', 'left');
        $this->btnSave->setCssStyle('margin-right', '10px');
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->Display = false;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnSave_Click'));

        $this->btnCancel = new Q\Plugin\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->setCssStyle('float', 'left');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->Display = false;
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnCancel_Click'));
    }

    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t('Tip');
        $this->dlgModal1->Text = t('<p style="margin-top: 15px;">Carousel cannot be created without name!</p>');
        $this->dlgModal1->HeaderClasses = 'btn-darkblue';
        $this->dlgModal1->addCloseButton(t("I close the window"));
        //$this->dlgModal1->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\AjaxControl($this, 'restoreTitle_Click'));
        //$this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\AjaxControl($this, 'restoreTitle_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Warning");
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This name is already used by another carousel!</p>');
        $this->dlgModal2->HeaderClasses = 'btn-danger';
        $this->dlgModal2->addCloseButton(t("I understand"));
        //$this->dlgModal2->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\AjaxControl($this, 'restoreTitle_Click'));
        //$this->dlgModal2->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\AjaxControl($this, 'restoreTitle_Click'));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal3->Title = t("Warning");
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addCloseButton(t("I understand"));
    }

    public function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The carousel header has been successfully updated.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('Failed to update carousel header.');
        $this->dlgToastr2->ProgressBar = true;
    }

    public function dtgSliders_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    protected function dtgSlidersRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $intSliderId = intval($params->ActionParameter);
        $this->objSlidersSettings = SlidersSettings::loadById($intSliderId);

        $this->txtTitle->Text = $this->objSlidersSettings->getTitle();
        $this->txtTitle->focus();

        $this->txtTitle->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;

        $this->dtgSliders->addCssClass('disabled');
    }

    protected function lstItemsPerPage_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->dtgSliders->refresh();
    }

    public function dtgSliders_Bind()
    {
        $this->dtgSliders->TotalItemCount = SlidersSettings::countAll();

        $objClauses = array();
        if ($objClause = $this->dtgSliders->OrderByClause)
            $objClauses[] = $objClause;
        if ($objClause = $this->dtgSliders->LimitClause)
            $objClauses[] = $objClause;

        $this->dtgSliders->DataSource = SlidersSettings::loadAll($objClauses);
    }

    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!$this->txtTitle->Text) {
            $this->dlgModal1->showDialogBox();
            $this->txtTitle->Text = $this->objSlidersSettings->getTitle();
            $this->txtTitle->focus();
        } else if ($this->objSlidersSettings::titleExists($this->txtTitle->Text)) {
            $this->dlgModal2->showDialogBox();
            $this->txtTitle->Text = $this->objSlidersSettings->getTitle();
            $this->txtTitle->focus();
        } else if ($this->txtTitle->Text) {
            $this->objSlidersSettings->setTitle(trim($this->txtTitle->Text));
            $this->objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
            $this->objSlidersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->objSlidersSettings->save();

            $this->txtTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;
            $this->txtTitle->Text = null;

            $this->dlgToastr1->notify();
            $this->dtgSliders->removeCssClass('disabled');
            $this->dtgSliders->refresh();
        } else {
            $this->dlgToastr2->notify();
            $this->txtTitle->Text = $this->objSlidersSettings->getTitle();
        }
    }

    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal3->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->txtTitle->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgSliders->removeCssClass('disabled');
        $this->txtTitle->Text = null;
    }
}