<?php
require('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;
use QCubed\QString;
use QCubed\Action\Ajax;
use QCubed\Event\Change;

class SampleForm extends Form
{
    protected $dlgModal1;
    protected $dlgModal2;

    protected $dlgToastr1;
    protected $dlgToastr2;
    protected $dlgToastr3;
    protected $dlgToastr4;
    protected $dlgToastr5;
    protected $dlgToastr6;
    protected $dlgToastr7;
    protected $dlgToastr8;
    protected $dlgToastr9;
    protected $dlgToastr10;
    protected $dlgToastr11;
    protected $dlgToastr12;
    protected $dlgToastr13;
    protected $dlgToastr14;

    protected $objTestSlider;
    protected $btnRefresh;
    protected $btnAddImage;
    protected $dlgSorter;

    protected $txtTitle;
    protected $txtUrl;
    protected $lblDimensions;
    protected $txtWidth;
    protected $lblCross;
    protected $txtHeight;
    protected $txtTop;
    protected $lstStatusSlider;
    protected $calSliderPostUpdateDate;
    protected $btnUpdate;
    protected $btnCancel;

    protected $btnBack;

    //////////////////////

    protected $lblPostDate;
    protected $calPostDate;
    protected $lblPostUpdateDate;
    protected $calPostUpdateDate;
    protected $lblAuthor;
    protected $txtAuthor;
    protected $lblUsersAsEditors;
    protected $txtUsersAsEditors;
    protected $lblStatus;
    protected $lstStatus;
    protected $lblUsePublicationDate;
    protected $chkUsePublicationDate;
    protected $lblAvailableFrom;
    protected $calAvailableFrom;
    protected $lblExpiryDate;
    protected $calExpiryDate;

    //////////////////////

    protected $intId;
    protected $intLoggedUserId;
    protected $intClick;
    protected $objSliders;
    protected $objSlidersSettings;

    /** @var string */
    protected $strRootPath = APP_UPLOADS_DIR;
    /** @var string */
    protected $strTempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
    protected $strDateTimeFormat = 'd.m.Y H:i';

    protected function formCreate()
    {
        parent::formCreate();

        $this->intId = Application::instance()->context()->queryStringItem('id');
        if (!empty($this->intId)) {
            $this->objSliders = Sliders::loadByIdFromSlidersGroupId($this->intId);
            $this->objSlidersSettings = SlidersSettings::load($this->intId);
        } else {
            // does nothing
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
        $this->intLoggedUserId = 4;


        $this->createInputs();
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
        $this->createSorter();
        $this->createSlider();
        $this->refreshDisplay();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createInputs()
    {
        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Title');
        $this->txtTitle->setHtmlAttribute('autocomplete', 'off');
        $this->txtTitle->CrossScripting = Bs\TextBox::XSS_HTML_PURIFIER;

        $this->txtUrl = new Bs\TextBox($this);
        $this->txtUrl->Placeholder = t('Url');
        $this->txtUrl->setHtmlAttribute('autocomplete', 'off');

        $this->lblDimensions = new Bs\TextBox($this);
        $this->lblDimensions->ReadOnly = true;
        $this->lblDimensions->setCssStyle('margin-top', '10px');

        $this->txtWidth = new Bs\TextBox($this, 'width');
        $this->txtWidth->Placeholder = t('Width');
        $this->txtWidth->addCssClass('no-spinners');
        $this->txtWidth->setCssStyle('margin-top', '10px');
        $this->txtWidth->setCssStyle('float', 'left');
        $this->txtWidth->Width = '45%';
        //$this->txtWidth->ReadOnly = true;
        $this->txtWidth->TextMode = Q\Control\TextBoxBase::NUMBER;

        $this->lblCross = new Bs\Label($this);
        $this->lblCross->Text = 'x';
        $this->lblCross->setCssStyle('margin-top', '15px');
        $this->lblCross->setCssStyle('margin-left', '10px');
        $this->lblCross->setCssStyle('margin-right', '10px');
        $this->lblCross->setCssStyle('float', 'left');
        $this->lblCross->Width = '3%';

        $this->txtHeight = new Bs\TextBox($this, 'height');
        $this->txtHeight->Placeholder = t('Height');
        $this->txtHeight->addCssClass('no-spinners');
        $this->txtHeight->setCssStyle('margin-top', '10px');
        $this->txtHeight->setCssStyle('float', 'left');
        $this->txtHeight->Width = '45%';
        //$this->txtHeight->ReadOnly = true;
        $this->txtHeight->TextMode = Q\Control\TextBoxBase::NUMBER;

        $this->txtTop = new Bs\TextBox($this);
        $this->txtTop->Placeholder = t('Top');
        $this->txtTop->addCssClass('no-spinners');
        $this->txtTop->setCssStyle('margin-top', '10px');
        $this->txtTop->setCssStyle('float', 'left');
        $this->txtTop->Width = '17%';
        $this->txtTop->TextMode = Q\Control\TextBoxBase::NUMBER;

        $this->lstStatusSlider = new Q\Plugin\RadioList($this);
        $this->lstStatusSlider->addItems([1 => t('Published'), 2 => t('Hidden')]);
        $this->lstStatusSlider->ButtonGroupClass = 'radio radio-orange edit radio-inline';
        $this->lstStatusSlider->setCssStyle('margin-left', '15px');
        $this->lstStatusSlider->setCssStyle('float', 'left');
        $this->lstStatusSlider->Width = '48%';

        $this->calSliderPostUpdateDate = new Bs\Label($this);
        $this->calSliderPostUpdateDate->setCssStyle('font-weight', 'normal');
        $this->calSliderPostUpdateDate->setCssStyle('margin-top', '18px');
        $this->calSliderPostUpdateDate->setCssStyle('margin-left', '10px');
        $this->calSliderPostUpdateDate->setCssStyle('float', 'left');

        ///////////////////////////////////////////////////////////////////////////////////////////

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Created');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objSlidersSettings->PostDate ? $this->objSlidersSettings->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostDate->setCssStyle('font-weight', 'normal');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objSlidersSettings->PostUpdateDate ? $this->objSlidersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->setCssStyle('font-weight', 'bold');

        $this->txtAuthor  = new Bs\Label($this);
        $this->txtAuthor->Text = $this->objSlidersSettings->Author;
        $this->txtAuthor->setCssStyle('font-weight', 'normal');

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objSlidersSettings->getUserAsSlidersEditorsArray());
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->setCssStyle('font-weight', 'bold');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Public carousel'), 2 => t('Hidden carousel'), 3 => t('Carousel draft')]);
        $this->lstStatus->SelectedValue = $this->objSlidersSettings->Status;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->Enabled = true;
        $this->lstStatus->addAction(new Q\Event\Change(), new Ajax('lstStatus_Change'));

        if ($this->objSlidersSettings->getUsePublicationDate()) {
            $this->lstStatus->Enabled = false;
        }

        $this->lblUsePublicationDate = new Q\Plugin\Control\Label($this);
        $this->lblUsePublicationDate->Text = t('Use publication date');
        $this->lblUsePublicationDate->setCssStyle('font-weight', 'bold');

        $this->chkUsePublicationDate = new Q\Plugin\Control\Checkbox($this);
        $this->chkUsePublicationDate->Checked = $this->objSlidersSettings->UsePublicationDate;
        $this->chkUsePublicationDate->WrapperClass = 'checkbox checkbox-orange';
        $this->chkUsePublicationDate->addAction(new Change(), new Ajax('setUse_PublicationDate'));
        
        $this->lblAvailableFrom = new Q\Plugin\Control\Label($this);
        $this->lblAvailableFrom->Text = t('Available From');
        $this->lblAvailableFrom->setCssStyle('font-weight', 'bold');

        $this->calAvailableFrom = new Q\Plugin\DateTimePicker($this);
        $this->calAvailableFrom->Language = 'et';
        $this->calAvailableFrom->TodayHighlight = true;

        $today = date('Y-m-d H:i:s');
        $this->calAvailableFrom->StartDate = $today;

        $this->calAvailableFrom->AutoClose = true;
        $this->calAvailableFrom->StartView = 2;
        $this->calAvailableFrom->ForceParse = false;
        $this->calAvailableFrom->Format = 'dd.mm.yyyy hh:ii';
        $this->calAvailableFrom->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calAvailableFrom->Text = $this->objSlidersSettings->AvailableFrom ? $this->objSlidersSettings->AvailableFrom->qFormat('DD.MM.YYYY hhhh:mm') : null;
        $this->calAvailableFrom->addCssClass('calendar-trigger');
        $this->calAvailableFrom->ActionParameter = $this->calAvailableFrom->ControlId;
        $this->calAvailableFrom->addAction(new Change(), new Ajax('setDate_AvailableFrom'));

        $this->lblExpiryDate = new Q\Plugin\Control\Label($this);
        $this->lblExpiryDate->Text = t('Expiry Date');
        $this->lblExpiryDate->setCssStyle('font-weight', 'bold');

        $this->calExpiryDate = new Q\Plugin\DateTimePicker($this);
        $this->calExpiryDate->Language = 'et';
        $this->calExpiryDate->ClearBtn = true;

        $tomorrow = date('Y-m-d H:i:s', strtotime('+1 day'));
        $this->calExpiryDate->StartDate = $tomorrow;

        $this->calExpiryDate->AutoClose = true;
        $this->calExpiryDate->StartView = 2;
        $this->calExpiryDate->ForceParse = false;
        $this->calExpiryDate->Format = 'dd.mm.yyyy hh:ii';
        $this->calExpiryDate->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calExpiryDate->Text = $this->objSlidersSettings->ExpiryDate ? $this->objSlidersSettings->ExpiryDate->qFormat('DD.MM.YYYY hhhh:mm') : null;
        $this->calExpiryDate->addCssClass('calendar-trigger');
        $this->calExpiryDate->ActionParameter = $this->calExpiryDate->ControlId;
        $this->calExpiryDate->addAction(new Change(), new Ajax('setDate_ExpiryDate'));

        if (!$this->objSlidersSettings->getUsePublicationDate()) {
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
        }
    }

    protected function createButtons()
    {
        $this->btnRefresh = new Bs\Button($this);
        $this->btnRefresh->Glyph = 'fa fa-refresh';
        $this->btnRefresh->Tip = true;
        $this->btnRefresh->ToolTip = t('Refresh');
        $this->btnRefresh->CssClass = 'btn btn-darkblue';
        $this->btnRefresh->CausesValidation = false;
        $this->btnRefresh->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnRefresh_Click'));

        $this->btnAddImage = new Bs\Button($this);
        $this->btnAddImage->Text = t(' Add images');
        $this->btnAddImage->CssClass = 'btn btn-orange';
        $this->btnAddImage->CausesValidation = false;
        $this->btnAddImage->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddImage_Click'));

        $this->btnUpdate = new Bs\Button($this);
        $this->btnUpdate->Text = t('Update');
        $this->btnUpdate->CssClass = 'btn btn-orange js-update';
        $this->btnUpdate->setCssStyle('margin-top', '20px');
        $this->btnUpdate->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnUpdate_Click'));

        $this->btnCancel = new Bs\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->setCssStyle('margin-top', '20px');
        $this->btnCancel->setCssStyle('margin-left', '10px');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCancel_Click'));

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back');
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->setCssStyle('margin-left', '10px');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnBack_Click'));
    }

    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The data for this image has been updated successfully.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('<strong>Sorry!</strong> Failed to update data for this image.');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Well done!</strong> Deleting this image data was successful.');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('<strong>Sorry!</strong> Deleting this image data failed.');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('<strong>Well done!</strong> This carousel is now public!');
        $this->dlgToastr5->ProgressBar = true;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('<strong>Well done!</strong> This carousel is now hidden!');
        $this->dlgToastr6->ProgressBar = true;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('<strong>Well done!</strong> The carousel is now under construction!');
        $this->dlgToastr7->ProgressBar = true;

        $this->dlgToastr8 = new Q\Plugin\Toastr($this);
        $this->dlgToastr8->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr8->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr8->Message = t('<strong>Well done!</strong> The publication date for this post has been saved or changed.');
        $this->dlgToastr8->ProgressBar = true;

        $this->dlgToastr9 = new Q\Plugin\Toastr($this);
        $this->dlgToastr9->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr9->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr9->Message = t('<strong>Well done!</strong> The expiration date for this post has been saved or changed.');
        $this->dlgToastr9->ProgressBar = true;

        $this->dlgToastr10 = new Q\Plugin\Toastr($this);
        $this->dlgToastr10->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr10->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr10->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this date \"Available from\" does not exist.</p>Please enter at least the date of publication!');
        $this->dlgToastr10->ProgressBar = true;
        $this->dlgToastr10->TimeOut = 10000;
        $this->dlgToastr10->EscapeHtml = false;

        $this->dlgToastr11 = new Q\Plugin\Toastr($this);
        $this->dlgToastr11->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr11->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr11->Message = t('<p style=\"margin-bottom: 2px;\">Start date must be smaller then end date!</p><strong>Try to do it right again!</strong>');
        $this->dlgToastr11->ProgressBar = true;
        $this->dlgToastr11->TimeOut = 10000;
        $this->dlgToastr11->EscapeHtml = false;

        $this->dlgToastr12 = new Q\Plugin\Toastr($this);
        $this->dlgToastr12->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr12->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr12->Message = t('Publication date have been canceled.');
        $this->dlgToastr12->ProgressBar = true;

        $this->dlgToastr13 = new Q\Plugin\Toastr($this);
        $this->dlgToastr13->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr13->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr13->Message = t('Expiration date have been canceled.');
        $this->dlgToastr13->ProgressBar = true;

        $this->dlgToastr14 = new Q\Plugin\Toastr($this);
        $this->dlgToastr14->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr14->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr14->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this date \"Available from\" does not exist.</p><strong>Re-enter publication date and expiration date!</strong>');
        $this->dlgToastr14->ProgressBar = true;
        $this->dlgToastr14->TimeOut = 10000;
        $this->dlgToastr14->EscapeHtml = false;
    }

    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this image along with its data?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = 'Warning';
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton("I accept", 'This file has been permanently deleted', false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t('Tip');
        $this->dlgModal2->Text = t('<p style="margin-top: 15px;">The carousel\'s status cannot be changed to public without images!</p>
                                <p style="margin-top: 25px; margin-bottom: 15px;">After uploading images and activating their status, the carousel can be made public.</p>');
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addCloseButton(t("I understand"));
    }

    protected function createSorter()
    {
        $this->dlgSorter = new Q\Plugin\Control\SlideWrapper($this);
        $this->dlgSorter->createNodeParams([$this, 'Sorter_Draw']);
        $this->dlgSorter->createRenderButtons([$this, 'Buttons_Draw']);
        $this->dlgSorter->setDataBinder('Sorter_Bind');
        $this->dlgSorter->addCssClass('sortable');
        $this->dlgSorter->DateTimeFormat = 'DD.MM.YYYY hhhh:mm:ss';
        $this->dlgSorter->TempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
        $this->dlgSorter->RootUrl = APP_UPLOADS_URL;
        $this->dlgSorter->Placeholder = 'placeholder';
        $this->dlgSorter->Handle = '.reorder';
        $this->dlgSorter->Items = 'div.image-blocks';
        $this->dlgSorter->addAction(new Q\Jqui\Event\SortableStop(), new Q\Action\Ajax('sortable_stop'));
        $this->dlgSorter->watch(QQN::sliders());
    }

    protected function createSlider()
    {
        $this->objTestSlider = new Q\Plugin\Control\SliderSetupAdmin($this);
        $this->objTestSlider->createNodeParams([$this, 'Sorter_Draw']);
        $this->objTestSlider->setDataBinder('Sorter_Bind');
        $this->objTestSlider->addCssClass('slider');

        $objCountByGroupId = Sliders::countByGroupId($this->intId);

        if ($objCountByGroupId === 0) {
            $this->objTestSlider->Display = false;
        } else {
            $this->objTestSlider->Display = true;
        }

        if ($this->intId == 2) {
            $this->objTestSlider->TempUrl = APP_UPLOADS_TEMP_URL . '/_files/large';
            $this->objTestSlider->RootUrl = APP_UPLOADS_URL;
            $this->objTestSlider->Mode = 'fade';
            $this->objTestSlider->Captions = true;
            $this->objTestSlider->Auto = true;
            //$this->objTestSlider->AutoControls = true;
            $this->objTestSlider->Controls = true;
            //$this->objTestSlider->Pager = false;
            $this->objTestSlider->SlideWidth = 500;
        }

        if ($this->intId == 1) {
            $this->objTestSlider->TempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
            $this->objTestSlider->RootUrl = APP_UPLOADS_URL;
            $this->objTestSlider->Auto = true;
            $this->objTestSlider->Pager = false;
            $this->objTestSlider->Speed = 2000;
            $this->objTestSlider->TouchEnabled = true;
            $this->objTestSlider->Controls = false;
            $this->objTestSlider->TickerHover = true;
            $this->objTestSlider->MinSlides = 4;
            $this->objTestSlider->MaxSlides = 5;
            $this->objTestSlider->MoveSlides = 1;
            $this->objTestSlider->SlideWidth = 200;
            $this->objTestSlider->SlideMargin = 50;
        }
    }

    protected function Sorter_Bind()
    {
        $this->dlgSorter->DataSource = Sliders::QueryArray(
            QQ::Equal(QQN::sliders()->GroupId, $this->intId),
            QQ::orderBy(QQN::sliders()->Order)
        );

        $this->objTestSlider->DataSource = Sliders::QueryArray(
            QQ::Equal(QQN::sliders()->GroupId, $this->intId),
            QQ::orderBy(QQN::sliders()->Order)
        );
    }

    public function Sorter_Draw(Sliders $objSliders)
    {
        $a['id'] = $objSliders->Id;
        $a['group_id'] = $objSliders->GroupId;
        $a['order'] = $objSliders->Order;
        $a['title'] = $objSliders->Title;
        $a['url'] = $objSliders->Url;
        $a['path'] = $objSliders->Path;
        $a['extension'] = $objSliders->Extension;
        $a['dimensions'] = $objSliders->Dimensions;
        $a['width'] = $objSliders->Width;
        $a['height'] = $objSliders->Height;
        $a['top'] = $objSliders->Top;
        $a['post_date'] = $objSliders->PostDate;
        $a['post_update_date'] = $objSliders->PostUpdateDate;
        $a['top'] = $objSliders->Top;
        $a['status'] = $objSliders->Status;
        return $a;
    }

    public function Buttons_Draw(Sliders $objSliders)
    {
        $strEditId = 'btnEdit' . $objSliders->Id;

        if (!$btnEdit = $this->getControl($strEditId)) {
            $btnEdit = new Bs\Button($this->dlgSorter, $strEditId);
            $btnEdit->Glyph = 'glyphicon glyphicon-pencil';
            $btnEdit->Tip = true;
            $btnEdit->ToolTip = t('Edit');
            $btnEdit->CssClass = 'btn btn-icon btn-xs edit';
            $btnEdit->ActionParameter = $objSliders->Id;
            $btnEdit->UseWrapper = false;
            $btnEdit->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnEdit_Click'));
        }

        $strDeleteId = 'btnDelete' . $objSliders->Id;

        if (!$btnDelete = $this->getControl($strDeleteId)) {
            $btnDelete = new Bs\Button($this->dlgSorter, $strDeleteId);
            $btnDelete->Glyph = 'glyphicon glyphicon-trash';
            $btnDelete->Tip = true;
            $btnDelete->ToolTip = t('Delete');
            $btnDelete->CssClass = 'btn btn-icon btn-xs delete';
            $btnDelete->ActionParameter = $objSliders->Id;
            $btnDelete->UseWrapper = false;
            $btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));
        }

        return $btnEdit->render(false) . $btnDelete->render(false);
    }

    protected function sortable_stop(ActionParams $params) {
        $arr = $this->dlgSorter->ItemArray;

        foreach ($arr as $order => $cids) {
            $cid = explode('_',  $cids);
            $id = end($cid);

            $objSorter = Sliders::load($id);
            $objSorter->setOrder($order);
            $objSorter->save();
        }

        $this->dlgToastr1->notify();

        $this->objSliders->setPostUpdateDate(Q\QDateTime::Now());
        $this->objSliders->save();

        $this->objSlidersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objSlidersSettings->getUserAsSlidersEditorsArray());
        $this->objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objSlidersSettings->save();

        $this->calPostUpdateDate->Text = $this->objSlidersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    protected function btnRefresh_Click(ActionParams $params)
    {
        $this->objTestSlider->refresh();
    }

    protected function btnAddImage_Click(ActionParams $params)
    {
        $_SESSION['finder_id'] = $this->intId;
        Application::redirect('finder_slider.php');
    }

    protected function btnEdit_Click(ActionParams $params)
    {
        $this->txtWidth->Text = '';
        $this->txtHeight->Text = '';

        $intEditId = intval($params->ActionParameter);
        $objEdit = Sliders::load($intEditId);
        $this->intClick = intval($intEditId);

        $this->txtTitle->Text = $objEdit->Title;
        $this->txtUrl->Text = $objEdit->Url;
        $this->lblDimensions->Text = $objEdit->Dimensions;
        $this->txtWidth->Text = $objEdit->Width;
        $this->txtHeight->Text = $objEdit->Height;
        $this->txtTop->Text = $objEdit->Top;
        $this->lstStatusSlider->SelectedValue = $objEdit->Status;

        if (!$objEdit->PostUpdateDate) {
            $date = $objEdit->PostDate ? $objEdit->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        } else {
            $date = $objEdit->PostUpdateDate ? $objEdit->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        }

        $this->calSliderPostUpdateDate->Text = $date;

        Application::executeJavaScript("
        
            var widthInput = $('#width');
            var heightInput = $('#height');
        
            $(\"[data-value='{$intEditId}']\").addClass('activated');
            $(\"[data-value='{$intEditId}']\").removeClass('inactivated');
            $('.slider-setting-wrapper').removeClass('hidden');

            var img = $('#{$this->dlgSorter->ControlId}_{$intEditId} img');
            var img_width = $('#{$this->dlgSorter->ControlId}_{$intEditId} img')[0].naturalWidth 

            widthInput.val(img_width);
            heightInput.val(img[0].naturalHeight);
            
            widthInput.val(img[0].naturalWidth);
            heightInput.val(img[0].naturalHeight);

            var aspectRatio = img[0].naturalWidth / img[0].naturalHeight;
            
            widthInput.on('keyup', function() {
                    var height = widthInput.val() / aspectRatio;
                    heightInput.val(Math.floor(height));
            });

            heightInput.on('keyup', function() {
                var width = heightInput.val() * aspectRatio;
                widthInput.val(Math.floor(width));                 
            });
       ");
    }

    protected function btnUpdate_Click(ActionParams $params)
    {
        $objCountByStatusfromId = Sliders::countByStatusFromId($this->intId, 1);

        $objUpdate = Sliders::load($this->intClick);
        $objUpdate->setTitle($this->txtTitle->Text);
        $objUpdate->setUrl($this->txtUrl->Text);
        $objUpdate->setWidth($this->objTestSlider->WidthInput);
        $objUpdate->setheight($this->objTestSlider->HeightInput);
        $objUpdate->setTop($this->txtTop->Text);
        $objUpdate->setStatus($this->lstStatusSlider->SelectedValue);
        $objUpdate->setPostUpdateDate(Q\QDateTime::Now());
        $objUpdate->save();

        $this->calSliderPostUpdateDate->Text = $objUpdate->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->objSlidersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objSlidersSettings->getUserAsSlidersEditorsArray());
        $this->objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objSlidersSettings->save();

        $this->calPostUpdateDate->Text = $this->objSlidersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

        if (is_file($this->strRootPath . $objUpdate->getPath())) {
            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr2->notify();
        }

        if ($objCountByStatusfromId === 1 && $this->lstStatusSlider->SelectedValue === 2) {

            $this->objSlidersSettings->setStatus(2);
            $this->objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
            $this->objSlidersSettings->save();

            $this->lstStatus->SelectedValue = 2;
            $this->lstStatus->refresh();

            $this->lblStatus->Text = $this->objSlidersSettings->getStatusObject();
            $this->lblStatus->refresh();
        }

        $this->txtTitle->refresh();
        $this->txtUrl->refresh();
        $this->refreshDisplay();

        Application::executeJavaScript(sprintf("
             $(\"[data-value='{$this->intClick}']\").addClass('activated');
            //$('.slider-setting-wrapper').addClass('hidden');  
       "));

        $this->objTestSlider->refresh();
    }

    protected function btnDelete_Click(ActionParams $params)
    {
        $this->intClick = intval($params->ActionParameter);
        $this->dlgModal1->showDialogBox();
    }

    protected function deleteItem_Click(ActionParams $params)
    {
        $objSliders = Sliders::load($this->intClick);
        $objCountByGroupId = Sliders::countByGroupId($this->intId);

        $objSlider = Sliders::loadById($objSliders->getId());
        $objSlider->delete();

        $this->objSlidersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objSlidersSettings->getUserAsSlidersEditorsArray());
        $this->objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objSlidersSettings->save();

        $this->calPostUpdateDate->Text = $this->objSlidersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();

        if ($objSliders->getId() !== $objSliders) {
            $this->dlgToastr3->notify();
        } else {
            $this->dlgToastr4->notify();
        }

        if ($objCountByGroupId == 1) {
            $this->objTestSlider->Display = false;

            $this->objSlidersSettings->setStatus(2);
            $this->objSlidersSettings->save();

            $this->lblStatus->Text =  $this->objSlidersSettings->getStatusObject();
            $this->lblStatus->refresh();
        }

        Application::executeJavaScript(sprintf("
            $('.slider-setting-wrapper').addClass('hidden');  
       "));

        $objFile = Files::loadById($objSliders->getFileId());
        $objFile->setLockedFile($objFile->getLockedFile() - 1);
        $objFile->save();

        $this->dlgModal1->hideDialogBox();
    }

    protected function btnCancel_Click(ActionParams $params)
    {
        Application::executeJavaScript(sprintf("
            jQuery(\"[data-value='{$this->intClick}']\").removeClass('activated');
            jQuery('.slider-setting-wrapper').addClass('hidden');  
       "));
    }

    protected function btnBack_Click(ActionParams $params)
    {

        $this->redirectToListPage();
    }
    
    protected function redirectToListPage()
    {
        Application::redirect('sliders_admin.php');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function lstStatus_Change(ActionParams $params)
    {
        $objCountByGroupId = Sliders::countByGroupId($this->intId);
        $objCountByStatusfromId = Sliders::countByStatusFromId($this->intId, 1);
        $beforeStatus = $this->objSlidersSettings->getStatus();

        if ($objCountByGroupId === 0 || $objCountByStatusfromId === 0) {
            $this->dlgModal2->showDialogBox();
        } else {
            $this->objSlidersSettings->setStatus($this->lstStatus->SelectedValue);
            $this->objSlidersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $this->txtUsersAsEditors->Text = implode(', ', $this->objSlidersSettings->getUserAsSlidersEditorsArray());
            $this->objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
            $this->objSlidersSettings->save();

            $this->calPostUpdateDate->Text = $this->objSlidersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');
        }

        if ($this->lstStatus->SelectedValue !== $beforeStatus) {
            if ($this->lstStatus->SelectedValue === 1) {
                $this->dlgToastr5->notify();
            } else if ($this->lstStatus->SelectedValue === 2) {
                $this->dlgToastr6->notify();
            } else if ($this->lstStatus->SelectedValue === 3) {
                $this->dlgToastr7->notify();
            }
        }

        $this->refreshDisplay();
    }

    protected function setUse_PublicationDate(ActionParams $params)
    {
        if ($this->chkUsePublicationDate->Checked) {
            $this->lblAvailableFrom->Display = true;
            $this->calAvailableFrom->Display = true;
            $this->lblExpiryDate->Display = true;
            $this->calExpiryDate->Display = true;

            $this->lstStatus->Enabled = false;
            $this->lstStatus->SelectedValue = null;

            $this->objSlidersSettings->setUsePublicationDate(1);
            $this->objSlidersSettings->setStatus(4);
            $this->calAvailableFrom->focus();
        } else {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = 2;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->objSlidersSettings->setUsePublicationDate(0);
            $this->objSlidersSettings->setStatus(2);
            $this->objSlidersSettings->setAvailableFrom(null);
            $this->objSlidersSettings->setExpiryDate(null);

            $this->dlgToastr12->notify();
        }

        $this->objSlidersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objSlidersSettings->getUserAsSlidersEditorsArray());
        $this->objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objSlidersSettings->save();

        $this->calPostUpdateDate->Text = $this->objSlidersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    protected function setDate_AvailableFrom(ActionParams $params)
    {
        if ($this->calAvailableFrom->Text) {
            $this->objSlidersSettings->setAvailableFrom($this->calAvailableFrom->DateTime);

            $this->dlgToastr8->notify();
        } else {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = 2;

            $this->objSlidersSettings->setUsePublicationDate(0);
            $this->objSlidersSettings>setStatus(2);
            $this->objSlidersSettings->setAvailableFrom(null);
            $this->objSlidersSettings->setExpiryDate(null);

            $this->dlgToastr12->notify();
        }

        $this->objSlidersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objSlidersSettings->getUserAsSlidersEditorsArray());
        $this->objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objSlidersSettings->save();

        $this->calPostUpdateDate->Text = $this->objSlidersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    protected function setDate_ExpiryDate(ActionParams $params)
    {
        if ($this->calAvailableFrom->Text && $this->calExpiryDate->Text) {
            if (new DateTime($this->calAvailableFrom->Text) > new DateTime($this->calExpiryDate->Text)) {

                $this->calExpiryDate->Text = null;
                $this->objSlidersSettings->setExpiryDate(null);

                $this->dlgToastr11->notify();
            } else if ($this->calExpiryDate->Text) {
                $this->objSlidersSettings->setExpiryDate($this->calExpiryDate->DateTime);

                $this->dlgToastr9->notify();
            } else {
                $this->calExpiryDate->Text = null;
                $this->objSlidersSettings->setExpiryDate(null);

                $this->dlgToastr13->notify();
            }
        } else if ($this->calAvailableFrom->Text && !$this->calExpiryDate->Text) {
            $this->calExpiryDate->Text = null;
            $this->objSlidersSettings->setExpiryDate(null);

            $this->dlgToastr13->notify();
        } else {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = 2;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->objSlidersSettings>setUseprotectedationDate(0);
            $this->objSlidersSettings->setStatus(2);
            $this->objSlidersSettings->setAvailableFrom(null);
            $this->objSlidersSettings->setExpiryDate(null);

            $this->dlgToastr14->notify();
        }

        $this->objSlidersSettings->setAssignedEditorsNameById($this->intLoggedUserId);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objSlidersSettings->getUserAsSlidersEditorsArray());
        $this->objSlidersSettings->setPostUpdateDate(Q\QDateTime::Now());
        $this->objSlidersSettings->save();

        $this->calPostUpdateDate->Text = $this->objSlidersSettings->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

        $this->refreshDisplay();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function refreshDisplay()
    {
        if ($this->objSlidersSettings->getPostDate() &&
            !$this->objSlidersSettings->getPostUpdateDate() &&
            $this->objSlidersSettings->getAuthor() &&
            !$this->objSlidersSettings->countUsersAsSlidersEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = false;
            $this->calPostUpdateDate->Display = false;
            $this->lblAuthor->Display = false;
            $this->txtAuthor->Display = false;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
            $this->calPostDate->addCssClass('form-control-remove');
        }

        if ($this->objSlidersSettings->getPostDate() &&
            $this->objSlidersSettings->getPostUpdateDate() &&
            $this->objSlidersSettings->getAuthor() &&
            !$this->objSlidersSettings->countUsersAsSlidersEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
            $this->calPostDate->removeCssClass('form-control-remove');
        }

        if ($this->objSlidersSettings->getPostDate() &&
            $this->objSlidersSettings->getPostUpdateDate() &&
            $this->objSlidersSettings->getAuthor() &&
            $this->objSlidersSettings->countUsersAsSlidersEditors()) {
            $this->lblPostDate->Display = true;
            $this->calPostDate->Display = true;
            $this->lblPostUpdateDate->Display = true;
            $this->calPostUpdateDate->Display = true;
            $this->lblAuthor->Display = true;
            $this->txtAuthor->Display = true;
            $this->lblUsersAsEditors->Display = true;
            $this->txtUsersAsEditors->Display = true;
            $this->txtUsersAsEditors->addCssClass('form-control-add');
        }
    }
}
SampleForm::run('SampleForm');