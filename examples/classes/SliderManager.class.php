<?php

    require(__DIR__ .  '/../src/Control/SliderSetupAdmin.php');

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Control\ControlBase;
    use QCubed\Control\FormBase;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Event\Change;
    use QCubed\Event\Click;
    use QCubed\Action\AjaxControl;
    use QCubed\Event\DialogButton;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Project\Application;
    use QCubed\Action\ActionParams;
    use QCubed\QDateTime;
    use QCubed\Query\QQ;

    /**
     * SliderManager is a class that extends the base Panel class.
     * The purpose of this class is to manage the functionality and UI components
     * for managing slider items in an application. It handles the initialization
     * of modals, toasts notifications, buttons, text input fields, and other interactive
     * UI elements for managing sliders and their properties.
     */
    class SliderManager extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;

        public Q\Plugin\Toastr $dlgToastr1;
        public Q\Plugin\Toastr $dlgToastr2;
        public Q\Plugin\Toastr $dlgToastr3;
        public Q\Plugin\Toastr $dlgToastr4;
        public Q\Plugin\Toastr $dlgToastr5;
        public Q\Plugin\Toastr $dlgToastr6;

        public Bs\Button $btnAddImage;
        public Bs\Button $btnChangeStatus;
        public Bs\Label $lblPublishingSlider;
        public Bs\RadioList $lstPublishingSlider;
        public Bs\Button $btnPublishingUpdate;
        public Bs\Button $btnPublishingCancel;

        public Bs\Button $btnRefresh;
        public Bs\Button $btnBack;
        public Q\Plugin\SlideWrapper $dlgSorter;
        public Q\Plugin\Control\SliderSetupAdmin $objTestSlider;
        public Bs\TextBox $txtTitle;
        public Bs\TextBox $txtUrl;
        public Bs\TextBox $lblDimensions;
        public Bs\TextBox $txtWidth;
        public Bs\Label $lblCross;
        public Bs\TextBox $txtHeight;
        public Bs\TextBox $txtTop;
        public Bs\RadioList $lstStatusSlider;
        public Bs\Label $calPostUpdateDate;
        public Bs\Button $btnUpdate;
        public Bs\Button $btnCancel;

        protected int $intClick;
        protected mixed $objSlider;
        protected mixed $objListOfSlider;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        /** @var string */
        protected string $strRootPath = APP_UPLOADS_DIR;
        protected string $strTempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
        protected string $strDateTimeFormat = 'd.m.Y H:i';
        protected string $strTemplate = 'SliderManager.tpl.php';

        /**
         * Constructor for initializing the control or form object.
         *
         * @param ControlBase|FormBase $objParentObject The parent object to which this control/form belongs.
         * @param string|null $strControlId Optional control ID to uniquely identify the control/form.
         *
         * @throws Caller
         * @throws InvalidCast
         */
        public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->intId = Application::instance()->context()->queryStringItem('id');
            if (strlen($this->intId)) {
                $this->objSlider = Sliders::load($this->intId);
                $this->objListOfSlider = SlidersList::load($this->intId);
            }

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
            $this->createToastr();
            $this->createSorter();
            $this->createSlider();
        }

        /**
         * Updates the user's last active timestamp to the current time and saves the changes to the user object.
         *
         * @return void The method does not return a value.
         * @throws Caller
         */
        private function userOptions(): void
        {
            $this->objUser->setLastActive(QDateTime::now());
            $this->objUser->save();
        }

        /**
         * Creates and initializes input fields, labels, and controls used in the form or control.
         *
         * This method sets up various UI elements, including labels, text boxes, and radio button lists,
         * with their respective styles, attributes, and properties. It prepares the input fields for data
         * entry or display, ensuring proper layout and functionality within the application interface.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createInputs(): void
        {
            $this->lblPublishingSlider = new Bs\Label($this);
            $this->lblPublishingSlider->Text = $this->objListOfSlider->getStatusObject();
            $this->lblPublishingSlider->HtmlEntities = false;
            $this->lblPublishingSlider->setCssStyle('float', 'left');
            $this->lblPublishingSlider->setCssStyle('margin-top', '7px');
            $this->lblPublishingSlider->setCssStyle('margin-left', '20px');

            $this->lstPublishingSlider = new Bs\RadioList($this);
            $this->lstPublishingSlider->addItems([1 => t('Public carousel'), 2 => t('Hidden slider')]);
            $this->lstPublishingSlider->SelectedValue = $this->objListOfSlider->getStatus();
            $this->lstPublishingSlider->Display = false;
            $this->lstPublishingSlider->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstPublishingSlider->setCssStyle('float', 'left');
            $this->lstPublishingSlider->setCssStyle('margin-left', '15px');

            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Title');
            $this->txtTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtTitle->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;

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
            $this->txtWidth->TextMode = TextBoxBase::NUMBER;

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
            $this->txtHeight->TextMode = TextBoxBase::NUMBER;

            $this->txtTop = new Bs\TextBox($this);
            $this->txtTop->Placeholder = t('Top');
            $this->txtTop->addCssClass('no-spinners');
            $this->txtTop->setCssStyle('margin-top', '10px');
            $this->txtTop->setCssStyle('float', 'left');
            $this->txtTop->Width = '17%';
            $this->txtTop->TextMode = TextBoxBase::NUMBER;

            if ($this->intId == 2) {
                $this->txtWidth->ReadOnly = true;
                $this->txtHeight->ReadOnly = true;
                $this->txtTop->ReadOnly = true;
            }

            $this->lstStatusSlider = new Bs\RadioList($this);
            $this->lstStatusSlider->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstStatusSlider->ButtonGroupClass = 'radio radio-orange edit radio-inline js-refresh-slider';
            $this->lstStatusSlider->setCssStyle('margin-left', '15px');
            $this->lstStatusSlider->setCssStyle('float', 'left');
            $this->lstStatusSlider->Width = '48%';
            $this->lstStatusSlider->addAction(new Change(), new AjaxControl($this, 'lstStatusSlider_Change'));

            $this->calPostUpdateDate = new Bs\Label($this);
            $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');
            $this->calPostUpdateDate->setCssStyle('margin-top', '18px');
            $this->calPostUpdateDate->setCssStyle('margin-left', '10px');
            $this->calPostUpdateDate->setCssStyle('float', 'left');
        }

        /**
         * Creates and configures a set of buttons with associated styles, actions, and behaviors.
         *
         * This method initializes multiple buttons, sets their properties such as text, CSS classes,
         * styles, validation behavior, and associates click events with their respective callback methods.
         * The buttons include functionalities like adding images, changing status, refreshing, navigation controls,
         * and update or cancel options.
         *
         * @return void This method does not return any value.
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnAddImage = new Bs\Button($this);
            $this->btnAddImage->Text = t(' Add images');
            $this->btnAddImage->CssClass = 'btn btn-orange overlay';
            $this->btnAddImage->Width = '100%';
            $this->btnAddImage->setCssStyle('margin-bottom', '20px');
            $this->btnAddImage->CausesValidation = false;
            $this->btnAddImage->addAction(new Click(), new AjaxControl($this, 'btnAddImage_Click'));

            $this->btnChangeStatus = new Bs\Button($this);
            $this->btnChangeStatus->Text = t(' Change status');
            $this->btnChangeStatus->CssClass = 'btn btn-orange';
            $this->btnChangeStatus->setCssStyle('float', 'left');
            $this->btnChangeStatus->CausesValidation = false;
            $this->btnChangeStatus->addAction(new Click(), new AjaxControl($this, 'btnChangeStatus_Click'));

            $this->btnPublishingUpdate = new Bs\Button($this);
            $this->btnPublishingUpdate->Text = t('Update');
            $this->btnPublishingUpdate->CssClass = 'btn btn-orange';
            $this->btnPublishingUpdate->Display = false;
            $this->btnPublishingUpdate->setCssStyle('float', 'left');
            $this->btnPublishingUpdate->setCssStyle('margin-left', '10px');
            $this->btnPublishingUpdate->PrimaryButton = true;
            $this->btnPublishingUpdate->addAction(new Click(), new AjaxControl($this,'btnPublishingUpdate_Click'));

            $this->btnPublishingCancel = new Bs\Button($this);
            $this->btnPublishingCancel->Text = t('Cancel');
            $this->btnPublishingCancel->CssClass = 'btn btn-default';
            $this->btnPublishingCancel->Display = false;
            $this->btnPublishingCancel->setCssStyle('float', 'left');
            $this->btnPublishingCancel->setCssStyle('margin-left', '10px');
            $this->btnPublishingCancel->CausesValidation = false;
            $this->btnPublishingCancel->addAction(new Click(), new AjaxControl($this, 'btnPublishingCancel_Click'));

            $this->btnRefresh = new Bs\Button($this);
            $this->btnRefresh->Glyph = 'fa fa-refresh';
            $this->btnRefresh->Tip = true;
            $this->btnRefresh->ToolTip = t('Refresh');
            $this->btnRefresh->CssClass = 'btn btn-darkblue js-refresh-slider';
            $this->btnRefresh->CausesValidation = false;

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->setCssStyle('margin-left', '10px');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new AjaxControl($this,'btnBack_Click'));

            $this->btnUpdate = new Bs\Button($this);
            $this->btnUpdate->Text = t('Update');
            $this->btnUpdate->CssClass = 'btn btn-orange js-update js-refresh-slider';
            $this->btnUpdate->setCssStyle('margin-top', '20px');
            $this->btnUpdate->addAction(new Click(), new AjaxControl($this,'btnUpdate_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->setCssStyle('margin-top', '20px');
            $this->btnCancel->setCssStyle('margin-left', '10px');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
        }

        /**
         * Creates and initializes modal dialogs with specific configurations.
         *
         * @return void
         * @throws Caller
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this image along with its data?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone.</p>');
            $this->dlgModal1->Title = 'Warning';
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton("I accept", 'This file has been permanently deleted', false, false, null,
                ['class' => 'btn btn-orange js-refresh-slider']);
            $this->dlgModal1->addCloseButton(t("I'll cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'deleteItem_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Title = t('Tip');
            $this->dlgModal2->Text = t('<p style="margin-top: 15px;">The carousel\'s status cannot be changed to public without images!</p>
                                <p style="margin-top: 25px; margin-bottom: 15px;">After uploading images and activating their status, the carousel can be made public.</p>');
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addCloseButton(t("I understand"));
        }

        /**
         * Creates and initializes toastr notifications with predefined configurations for various scenarios.
         *
         * @return void
         * @throws Caller
         */
        public function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The data for this image has been updated successfully.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('<strong>Sorry!</strong> Failed to update data for this image.');
            $this->dlgToastr2->ProgressBar = true;

            $this->dlgToastr3 = new Q\Plugin\Toastr($this);
            $this->dlgToastr3->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr3->Message = t('<strong>Well done!</strong> <p>Deleting this image data was successful.</p> <p>Please refresh the slider too!</p>');
            $this->dlgToastr3->ProgressBar = true;

            $this->dlgToastr4 = new Q\Plugin\Toastr($this);
            $this->dlgToastr4->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToastr4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr4->Message = t('<strong>Sorry!</strong> Deleting this image data failed.');
            $this->dlgToastr4->ProgressBar = true;

            $this->dlgToastr5 = new Q\Plugin\Toastr($this);
            $this->dlgToastr5->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr5->Message = t('<strong>Well done!</strong> This carousel is now public!');
            $this->dlgToastr5->ProgressBar = true;

            $this->dlgToastr6 = new Q\Plugin\Toastr($this);
            $this->dlgToastr6->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr6->Message = t('<strong>Well done!</strong> This carousel is now hidden!');
            $this->dlgToastr6->ProgressBar = true;
        }

        /**
         * Creates and configures a sortable interface with specified settings.
         *
         * @return void
         * @throws Caller
         */
        public function createSorter(): void
        {
            $this->dlgSorter = new Q\Plugin\SlideWrapper($this);
            $this->dlgSorter->createNodeParams([$this, 'Sorter_Draw']);
            $this->dlgSorter->createRenderButtons([$this, 'Buttons_Draw']);
            $this->dlgSorter->setDataBinder('Sorter_Bind', $this);
            $this->dlgSorter->addCssClass('sortable js-refresh-slider');
            $this->dlgSorter->TempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
            $this->dlgSorter->RootUrl = APP_UPLOADS_URL;
            $this->dlgSorter->Placeholder = 'placeholder';
            $this->dlgSorter->Handle = '.reorder';
            $this->dlgSorter->Items = 'div.image-blocks';
            $this->dlgSorter->addAction(new Q\Jqui\Event\SortableStop(), new AjaxControl($this, 'sortable_stop'));
            $this->dlgSorter->watch(QQN::sliders());
        }

        /**
         * Creates and configures a slider component for the admin interface.
         *
         * Initializes the slider with default settings, such as URLs and slide options.
         *
         * @return void
         * @throws Caller
         */
        public function createSlider(): void
        {
            $this->objTestSlider = new Q\Plugin\Control\SliderSetupAdmin($this);
            $this->objTestSlider->RootUrl = APP_UPLOADS_URL;

            if ($this->intId == 1) {
                $this->objTestSlider->TempUrl = APP_UPLOADS_TEMP_URL . '/_files/thumbnail';
            }

            if ($this->intId == 2) {
                $this->objTestSlider->TempUrl = APP_UPLOADS_TEMP_URL . '/_files/large';
            }

            $this->objTestSlider->SelectedGroup = $this->intId;

            if ($this->intId == 1) {
                $this->objTestSlider->SliderOptions = [
                    'auto' => true,
                    'pager' => false,
                    'speed' => 2000,
                    'touchEnabled' => true,
                    'controls' => false,
                    'tickerHover' => true,
                    'minSlides' => 4,
                    'maxSlides' => 5,
                    'moveSlides' => 1,
                    'slideWidth' => 200,
                    'slideMargin' => 50
                ];
            }

            if ($this->intId == 2) {
                $this->objTestSlider->SliderOptions = [
                    'mode' => 'fade',
                    'auto' => true,
                    'controls' => true,
                    'captions' => true,
                    'slideWidth' => 600
                ];
            }
        }

        /**
         * Binds data sources for sorting sliders based on a specific group ID and order.
         *
         * @return void
         * @throws Caller
         */
        public function Sorter_Bind(): void
        {
            $this->dlgSorter->DataSource = Sliders::queryArray(
                QQ::Equal(QQN::sliders()->GroupId, $this->intId),
                QQ::Clause(QQ::orderBy(QQN::sliders()->Order)
                ));

        }

        /**
         * Generates an array representation of the provided slider object.
         *
         * @param Sliders $objSlider The slider object to be processed and converted into an array.
         *
         * @return array An associative array containing detailed attributes of the slider object,
         *               including an id, group_id, order, title, url, path, extension, dimensions, width, height, top, and status.
         */
        public function Sorter_Draw(Sliders $objSlider): array
        {
            $a['id'] = $objSlider->Id;
            $a['group_id'] = $objSlider->GroupId;
            $a['order'] = $objSlider->Order;
            $a['title'] = $objSlider->Title;
            $a['url'] = $objSlider->Url;
            $a['path'] = $objSlider->Path;
            $a['extension'] = $objSlider->Extension;
            $a['dimensions'] = $objSlider->Dimensions;
            $a['width'] = $objSlider->Width;
            $a['height'] = $objSlider->Height;
            $a['top'] = $objSlider->Top;
            $a['status'] = $objSlider->Status;
            return $a;
        }

        /**
         * Generates and renders edit and delete buttons for the given slider object.
         *
         * @param Sliders $objSlider The slider object for which the buttons are to be created and rendered.
         *
         * @return string The HTML string containing the rendered edit and delete buttons.
         * @throws Caller
         */
        public function Buttons_Draw(Sliders $objSlider): string
        {
            $strEditId = 'btnEdit' . $objSlider->Id;

            if (!$btnEdit = $this->Form->getControl($strEditId)) {
                $btnEdit = new Bs\Button($this->dlgSorter, $strEditId);
                $btnEdit->Glyph = 'glyphicon glyphicon-pencil';
                $btnEdit->Tip = true;
                $btnEdit->ToolTip = t('Edit');
                $btnEdit->CssClass = 'btn btn-icon btn-xs edit';
                $btnEdit->ActionParameter = $objSlider->Id;
                $btnEdit->UseWrapper = false;
                $btnEdit->CausesValidation = false;
                $btnEdit->addAction(new Click(), new AjaxControl($this,'btnEdit_Click'));
            }

            $strDeleteId = 'btnDelete' . $objSlider->Id;

            if (!$btnDelete = $this->Form->getControl($strDeleteId)) {
                $btnDelete = new Bs\Button($this->dlgSorter, $strDeleteId);
                $btnDelete->Glyph = 'glyphicon glyphicon-trash';
                $btnDelete->Tip = true;
                $btnDelete->ToolTip = t('Delete');
                $btnDelete->CssClass = 'btn btn-icon btn-xs delete';
                $btnDelete->ActionParameter = $objSlider->Id;
                $btnDelete->addAction(new Click(), new AjaxControl($this,'btnDelete_Click'));
            }

            return $btnEdit->render(false) . $btnDelete->render(false);
        }

        /**
         * Handles the completion of a sortable action and updates the order of items in the database.
         *
         * @param ActionParams $params The parameters sent by the sortable action, including item positions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function sortable_stop(ActionParams $params): void
        {
            $arr = $this->dlgSorter->ItemArray;

            foreach ($arr as $order => $cids) {
                $cid = explode('_',  $cids);
                $id = end($cid);

                $objSorter = Sliders::load($id);
                $objSorter->setOrder($order + 1);
                $objSorter->setPostUpdateDate(QDateTime::now());
                $objSorter->save();
            }

            $this->userOptions();
        }

        /**
         * Handles the click event for adding an image button. Stores the finder ID
         * in the session and redirects to the finder slider page.
         *
         * @param ActionParams $params Parameters associated with the action event.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnAddImage_Click(ActionParams $params): void
        {
            $_SESSION["redirect-data"] = $this->intId;

            $this->userOptions();

            Application::redirect('slider_finder.php');
        }

        /**
         * Handles the click event for the status change button.
         * Modifies the display and enabled state of various UI elements related to publishing status.
         *
         * @param ActionParams $params Parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function btnChangeStatus_Click(ActionParams $params): void
        {
            $this->btnChangeStatus->Enabled = false;
            $this->lblPublishingSlider->Display = false;
            $this->lstPublishingSlider->Display = true;
            $this->btnPublishingUpdate->Display = true;
            $this->btnPublishingCancel->Display = true;

            $this->userOptions();
        }

        /**
         * Handles the publishing update process for a slider group.
         *
         * This method updates the publishing status of a group of sliders based on the current
         * status and associated criteria. If the group does not have valid configurations
         * (e.g., no sliders or all sliders inactive), a warning dialog is shown. Otherwise,
         * the publishing status is updated, UI elements are refreshed, and notifications are triggered
         * based on the updated status.
         *
         * @param ActionParams $params The parameters passed to this action, including event data.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnPublishingUpdate_Click(ActionParams $params): void
        {
            $objSlidersList = SlidersList::load($this->intId);
            $objCountByGroupId = Sliders::countByGroupId($this->intId);
            $objCountByStatusfromId = Sliders::countByStatusFromId($this->intId, 1);
            $beforeStatus = $objSlidersList->getStatus();

            if ($objCountByGroupId === 0 || $objCountByStatusfromId === 0) {
                $this->dlgModal2->showDialogBox();
            } else {
                $objSlidersList->setStatus($this->lstPublishingSlider->SelectedValue);
                $objSlidersList->setPostUpdateDate(QDateTime::now());
                $objSlidersList->save();

                $this->lblPublishingSlider->Text = $objSlidersList->getStatusObject();
                $this->lblPublishingSlider->refresh();
            }

            $this->btnChangeStatus->Enabled = true;
            $this->lblPublishingSlider->Display = true;
            $this->lstPublishingSlider->Display = false;
            $this->btnPublishingUpdate->Display = false;
            $this->btnPublishingCancel->Display = false;

            if ($beforeStatus !== $objSlidersList->getStatus()) {
                if ($objSlidersList->getStatus() == 1) {
                    $this->dlgToastr5->notify();
                } else {
                    $this->dlgToastr6->notify();
                }
            }

            $this->userOptions();
        }

        /**
         * Handles the click event for the "Publishing Cancel" button, reversing any changes made during
         * the publishing process and resetting the interface to its prior state.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function btnPublishingCancel_Click(ActionParams $params): void
        {
            $this->btnChangeStatus->Enabled = true;
            $this->lblPublishingSlider->Display = true;
            $this->lstPublishingSlider->Display = false;
            $this->btnPublishingUpdate->Display = false;
            $this->btnPublishingCancel->Display = false;

            $this->userOptions();
        }

        /**
         * Handles the back button click event, redirecting the user to the slider administration page.
         *
         * @param ActionParams $params The parameters associated with the button click event.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnBack_Click(ActionParams $params): void
        {
            $this->userOptions();

            Application::redirect('slider_manager.php');
        }

        /**
         * Handles the click event for the edit button, loads and populates the necessary data fields
         * based on the provided action parameter. It also configures the front-end input behavior
         * for width and height calculations based on an image's aspect ratio.
         *
         * @param ActionParams $params The parameters associated with the action event, including the identifier for the item to edit.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnEdit_Click(ActionParams $params): void
        {
            $this->txtWidth->Text = '';
            $this->txtHeight->Text = '';

            $intEditId = intval($params->ActionParameter);
            $objEdit = Sliders::load($intEditId);
            $this->intClick = $intEditId;

            $this->txtTitle->Text = $objEdit->Title ?? '';
            $this->txtUrl->Text = $objEdit->Url ?? '';
            $this->lblDimensions->Text = $objEdit->Dimensions;
            $this->txtWidth->Text = $objEdit->Width ?? '';
            $this->txtHeight->Text = $objEdit->Height ?? '';
            $this->txtTop->Text = $objEdit->Top ?? '';
            $this->lstStatusSlider->SelectedValue = $objEdit->Status;

            if (!$objEdit->PostUpdateDate) {
                $date = $objEdit->PostDate ? $objEdit->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            } else {
                $date = $objEdit->PostUpdateDate ? $objEdit->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
            }

            $this->calPostUpdateDate->Text = $date;

            Application::executeJavaScript("
        
                var widthInput = $('#width');
                var heightInput = $('#height');
            
                $(\"[data-value='$intEditId']\").addClass('activated');
                $(\"[data-value='$intEditId']\").removeClass('inactivated');
                $('.slider-setting-wrapper').removeClass('hidden');
    
                var img = $('#{$this->dlgSorter->ControlId}_$intEditId img');
                var img_width = $('#{$this->dlgSorter->ControlId}_$intEditId img')[0].naturalWidth 
    
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

            $this->userOptions();
        }

        /**
         * Handles changes to the status slider, updating the visual state and underlying data.
         *
         * @param ActionParams $params Parameters related to the action triggering this method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function lstStatusSlider_Change(ActionParams $params): void
        {
            $objSlider = Sliders::load($this->intClick);

            $objId = $this->intClick;
            $objStatus = $this->lstStatusSlider->SelectedValue;

            Application::executeJavaScript("
                if ('$objStatus' == 1) {
                    $(\"[data-value='$objId']\").removeClass('activated');
                    $(\"[data-value='$objId']\").removeClass('inactivated');
                } else {
                    $(\"[data-value='$objId']\").removeClass('activated');
                    $(\"[data-value='$objId']\").addClass('inactivated');
                }
            ");

            $objSlider->setStatus($this->lstStatusSlider->SelectedValue);
            $objSlider->setPostUpdateDate(QDateTime::now());
            $objSlider->save();

            $this->dlgToastr1->notify();

            Application::executeJavaScript("$('.slider-setting-wrapper').addClass('hidden');");

            $this->userOptions();
        }

        /**
         * Handles the update operation when the update button is clicked, modifying slider data and statuses.
         *
         * @param ActionParams $params Parameters containing the details of the triggered action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnUpdate_Click(ActionParams $params): void
        {
            $objSlidersList = SlidersList::load($this->intId);
            $objCountByStatusfromId = Sliders::countByStatusFromId($this->intId, 1);

            $objUpdate = Sliders::load($this->intClick);
            $objUpdate->setTitle($this->txtTitle->Text);
            $objUpdate->setUrl($this->txtUrl->Text);

            if ($this->intId == 2) {
                $objUpdate->setWidth($this->objTestSlider->WidthInput);
                $objUpdate->setheight($this->objTestSlider->HeightInput);
                $objUpdate->setTop((int)$this->txtTop->Text);
            }

            // $objUpdate->setStatus($this->lstStatusSlider->SelectedValue); // That's not necessary!
            $objUpdate->setPostUpdateDate(QDateTime::now());
            $objUpdate->save();

            if (is_file($this->strRootPath . $objUpdate->getPath())) {
                $this->dlgToastr1->notify();
            } else {
                $this->dlgToastr2->notify();
            }

            if ($objCountByStatusfromId === 1 && $this->lstStatusSlider->SelectedValue === 2) {

                $objSlidersList->setStatus(2);
                $objSlidersList->setPostUpdateDate(QDateTime::now());
                $objSlidersList->save();

                $this->lstPublishingSlider->SelectedValue = 2;
                $this->lstPublishingSlider->refresh();

                $this->lblPublishingSlider->Text = $objSlidersList->getStatusObject();
                $this->lblPublishingSlider->refresh();
            }

            $this->txtTitle->refresh();
            $this->txtUrl->refresh();
            $this->userOptions();

            $this->calPostUpdateDate->Text = $objUpdate->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss');

            Application::executeJavaScript("
                $('.slider-setting-wrapper').addClass('hidden');
            ");

            $objId = $this->intClick;
            $objStatus = $this->lstStatusSlider->SelectedValue;

            Application::executeJavaScript("
                if ('$objStatus' == 1) {
                    $(\"[data-value='$objId']\").removeClass('activated');
                    $(\"[data-value='$objId']\").removeClass('inactivated');
                } else {
                    $(\"[data-value='$objId']\").removeClass('activated');
                    $(\"[data-value='$objId']\").addClass('inactivated');
                }
            ");
        }

        /**
         * Handles the click event for the delete button, sets the action parameter,
         * and displays the confirmation modal dialog.
         *
         * @param ActionParams $params The parameters passed from the action, including the action parameter.
         *
         * @return void
         * @throws Caller
         */
        protected function btnDelete_Click(ActionParams $params): void
        {
            $this->intClick = intval($params->ActionParameter);
            $this->dlgModal1->showDialogBox();

            $this->userOptions();
        }

        /**
         * Handles the click event for deleting a slider item and its associated data.
         * Performs various operations such as deleting the slider, updating the interface,
         * notifying the user, and altering related configuration states.
         *
         * @param ActionParams $params Contains the parameters associated with the action event,
         *                              typically user interaction data.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        protected function deleteItem_Click(ActionParams $params): void
        {
            $objSliders = Sliders::load($this->intClick);
            $objCountByGroupId = Sliders::countByGroupId($this->intId);

            $objSlider = Sliders::loadById($objSliders->getId());
            $objSlider->delete();

            Application::executeJavaScript("
                $(\"[data-value='$objSliders']\").remove();    
            ");

            if ($objSliders->getId() !== $objSliders) {
                $this->dlgToastr3->notify();
            } else {
                $this->dlgToastr4->notify();
            }

            if ($objCountByGroupId == 1) {
                $this->objTestSlider->Display = false;

                $this->objListOfSlider->setStatus(2);
                $this->objListOfSlider->save();

                $this->lblPublishingSlider->Text =  $this->objListOfSlider->getStatusObject();
            }

            Application::executeJavaScript("$('.slider-setting-wrapper').addClass('hidden');");

            $objFile = Files::loadById($objSliders->getFileId());
            $objFile->setLockedFile($objFile->getLockedFile() - 1);
            $objFile->save();

            $this->dlgModal1->hideDialogBox();

            $this->userOptions();
        }

        /**
         * Handles the click event for the cancel button, executing JavaScript to update the UI state.
         *
         * @param ActionParams $params Parameters associated with the action triggering this event.
         *
         * @return void
         * @throws Caller
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            Application::executeJavaScript("
                jQuery(\"[data-value='$this->intClick']\").removeClass('activated');
                jQuery('.slider-setting-wrapper').addClass('hidden');  
           ");

            $this->userOptions();
        }
    }