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

class LinkCategoriesManager extends Q\Control\Panel
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
    public $dtgLinkCategories;

    public $btnAddCategory;
    public $btnGoToLinks;
    public $txtCategory;
    public $lstStatus;
    public $btnSaveCategory;
    public $btnSave;
    public $btnDelete;
    public $btnCancel;

    protected $intId;
    protected $objUser;
    protected $intLoggedUserId;
    protected $objCategoryIds = [];
    protected $oldName;

    protected $strTemplate = 'LinkCategoriesManager.tpl.php';

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

        // $objUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
        // For example, John Doe is a logged user with his session

        $this->intLoggedUserId = 2;
        $this->objUser = User::load($this->intLoggedUserId);

        $this->createItemsPerPage();
        $this->createFilter();
        $this->dtgLinkCategories_Create();
        $this->dtgLinkCategories->setDataBinder('BindData', $this);
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->CheckCategories();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    
    protected function dtgLinkCategories_Create()
    {
        $this->dtgLinkCategories = new LinksCategoryTable($this);
        $this->dtgLinkCategories_CreateColumns();
        $this->createPaginators();
        $this->dtgLinkCategories_MakeEditable();
        $this->dtgLinkCategories->RowParamsCallback = [$this, "dtgLinkCategories_GetRowParams"];
        $this->dtgLinkCategories->SortColumnIndex = 0;
        $this->dtgLinkCategories->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
    }
    
    protected function dtgLinkCategories_CreateColumns()
    {
        $this->dtgLinkCategories->createColumns();
    }
    
    protected function dtgLinkCategories_MakeEditable()
    {
        $this->dtgLinkCategories->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\AjaxControl($this, 'dtgLinkCategoriesRow_Click'));
        $this->dtgLinkCategories->addCssClass('clickable-rows');
        $this->dtgLinkCategories->CssClass = 'table vauu-table table-hover table-responsive';
    }
    
    protected function dtgLinkCategoriesRow_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->intId = intval($params->ActionParameter);
        $objCategories = LinksCategory::load($this->intId);

        $this->oldName = $objCategories->getTitle();

        $this->txtCategory->Text = $objCategories->getTitle();
        $this->txtCategory->focus();
        $this->lstStatus->SelectedValue = $objCategories->Status ?? null;

        $this->dtgLinkCategories->addCssClass('disabled');
        $this->btnAddCategory->Enabled = false;
        $this->btnGoToLinks->Display = false;
        $this->txtCategory->Display = true;
        $this->lstStatus->Display = true;
        $this->btnSave->Display = true;
        $this->btnDelete->Display = true;
        $this->btnCancel->Display = true;
    }
    
    public function dtgLinkCategories_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }
    
    protected function createPaginators()
    {
        $this->dtgLinkCategories->Paginator = new Bs\Paginator($this);
        $this->dtgLinkCategories->Paginator->LabelForPrevious = t('Previous');
        $this->dtgLinkCategories->Paginator->LabelForNext = t('Next');

        $this->dtgLinkCategories->ItemsPerPage = 10;
        $this->dtgLinkCategories->SortColumnIndex = 4;
        $this->dtgLinkCategories->UseAjax = true;
        $this->addFilterActions();
    }
    
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
    
    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgLinkCategories->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
        $this->dtgLinkCategories->refresh();
    }
    
    protected function createFilter() {
        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-box');
        $this->addFilterActions();
    }
    
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
    
    protected function filterChanged()
    {
        $this->dtgLinkCategories->refresh();
    }
    
    public function bindData()
    {
        $objCondition = $this->getCondition();
        $this->dtgLinkCategories->bindData($objCondition);
    }
    
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
                Q\Query\QQ::equal(QQN::LinksCategory()->Id, $strSearchValue),
                Q\Query\QQ::like(QQN::LinksCategory()->Title, "%" . $strSearchValue . "%")
            );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    
    private function CheckCategories()
    {
        $objLinkArray = Links::loadAll();

        foreach ($objLinkArray as $objLink) {
            if ($objLink->getCategoryId()) {
                $this->objCategoryIds[] = $objLink->getCategoryId();
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

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

        $this->btnGoToLinks = new Bs\Button($this);
        $this->btnGoToLinks->Text = t('Go to this links');
        $this->btnGoToLinks->addWrapperCssClass('center-button');
        $this->btnGoToLinks->CssClass = 'btn btn-default';
        $this->btnGoToLinks->CausesValidation = false;
        $this->btnGoToLinks->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnGoToLinks_Click'));
        $this->btnGoToLinks->setCssStyle('float', 'left');

        if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
            $this->btnGoToLinks->Display = true;
        } else {
            $this->btnGoToLinks->Display = false;
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

    ///////////////////////////////////////////////////////////////////////////////////////////

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
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the category name must exist!');
        $this->dlgToastr2->ProgressBar = true;
    }

    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete the category?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone!</p>');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The category cannot be deactivated at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To deactivate this category, 
                                    simply release any category previously associated with created link.</p>');
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The category cannot be deleted at the moment!</p>
                                    <p style="line-height: 15px; margin-bottom: -3px;">To delete this category, 
                                    simply release any links previously associated with created link.</p>');
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The title of this category already exists in the database, please choose another title!</p>');
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

    protected function btnAddCategory_Click()
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $this->btnGoToLinks->Display = false;
        $this->txtCategory->Display = true;
        $this->lstStatus->Display = true;
        $this->lstStatus->SelectedValue = 2;
        $this->btnSaveCategory->Display = true;
        $this->btnCancel->Display = true;
        $this->txtCategory->Text = null;
        $this->txtCategory->focus();
        $this->btnAddCategory->Enabled = false;
        $this->dtgLinkCategories->addCssClass('disabled');
    }

    protected function btnSaveCategory_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if ($this->txtCategory->Text) {
            if (!LinksCategory::titleExists(trim($this->txtCategory->Text))) {
                $objCategoryNews = new LinksCategory();
                $objCategoryNews->setTitle(trim($this->txtCategory->Text));
                $objCategoryNews->setStatus($this->lstStatus->SelectedValue);
                $objCategoryNews->setPostDate(Q\QDateTime::Now());
                $objCategoryNews->save();

                $this->dtgLinkCategories->refresh();

                if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
                    $this->btnGoToLinks->Display = true;
                }

                $this->txtCategory->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSaveCategory->Display = false;
                $this->btnCancel->Display = false;
                $this->btnAddCategory->Enabled = true;
                $this->dtgLinkCategories->removeCssClass('disabled');
                $this->txtCategory->Text = null;
                $this->dlgToastr1->notify();
            } else {
                $this->txtCategory->Text = null;
                $this->txtCategory->focus();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtCategory->Text = null;
            $this->txtCategory->focus();
            $this->dlgToastr2->notify();
        }
    }

    protected function btnSave_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objCategories = LinksCategory::loadById($this->intId);

        if ($this->txtCategory->Text) {
            if (in_array($this->intId, $this->objCategoryIds) && $this->lstStatus->SelectedValue == 2) {
                $this->lstStatus->SelectedValue = 1;
                $this->dlgModal2->showDialogBox();

                if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
                    $this->btnGoToLinks->Display = true;
                }

                $this->btnAddCategory->Enabled = true;
                $this->txtCategory->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;
                $this->dtgLinkCategories->removeCssClass('disabled');

            } else if ($this->txtCategory->Text == $objCategories->getTitle() && $this->lstStatus->SelectedValue !== $objCategories->getStatus()) {
                $objCategories->setTitle(trim($this->txtCategory->Text));
                $objCategories->setStatus($this->lstStatus->SelectedValue);
                $objCategories->setPostUpdateDate(Q\QDateTime::Now());
                $objCategories->save();

                $this->dtgLinkCategories->refresh();
                $this->btnAddCategory->Enabled = true;

                if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
                    $this->btnGoToLinks->Display = true;
                }

                $this->txtCategory->Display = false;
                $this->lstStatus->Display = false;
                $this->btnSave->Display = false;
                $this->btnDelete->Display = false;
                $this->btnCancel->Display = false;

                $this->dtgLinkCategories->removeCssClass('disabled');
                $this->txtCategory->Text = $objCategories->getTitle();
                $this->dlgToastr1->notify();


            } else if (!LinksCategory::titleExists(trim($this->txtCategory->Text))) {
                $this->txtCategory->Text = $objCategories->getTitle();
                $this->dlgModal4->showDialogBox();
            }
        } else {
            $this->txtCategory->Text = $objCategories->getTitle();
            $this->txtCategory->focus();
            $this->dlgToastr2->notify();
        }
    }

    protected function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (in_array($this->intId, $this->objCategoryIds)) {
            $this->dlgModal3->showDialogBox();

            if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
                $this->btnGoToLinks->Display = true;
            }

            $this->btnAddCategory->Enabled = true;
            $this->txtCategory->Display = false;
            $this->lstStatus->Display = false;
            $this->btnSave->Display = false;
            $this->btnDelete->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgLinkCategories->removeCssClass('disabled');

        } else {
            $this->dlgModal1->showDialogBox();
        }
    }

    public function deleteItem_Click(ActionParams $params)
    {
        $objCategories = LinksCategory::loadById($this->intId);

        if ($params->ActionParameter == "pass") {
            $objCategories->delete();
        }

        $this->dtgLinkCategories->refresh();
        $this->btnAddCategory->Enabled = true;
        $this->txtCategory->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;

        $this->dtgLinkCategories->removeCssClass('disabled');
        $this->dlgModal1->hideDialogBox();
    }

    protected function btnCancel_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {
            $this->btnGoToLinks->Display = true;
        }

        $this->txtCategory->Display = false;
        $this->lstStatus->Display = false;
        $this->btnSaveCategory->Display = false;
        $this->btnSave->Display = false;
        $this->btnDelete->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddCategory->Enabled = true;
        $this->dtgLinkCategories->removeCssClass('disabled');
        $this->txtCategory->Text = null;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function btnGoToLinks_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal5->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        if (!empty($_SESSION['links']) || !empty($_SESSION['group'])) {

        Application::redirect('links_edit.php?id=' . $_SESSION['links'] . '&group=' . $_SESSION['group']);
        unset($_SESSION['links']);
        unset($_SESSION['group']);
        }
    }
}