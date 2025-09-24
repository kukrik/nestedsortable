<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\CellClick;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\Input;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Query\Condition\All;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;

    /**
     * Class VideosListPanel
     *
     * Represents the panel that manages video lists and their related operations. The panel includes
     * functionalities such as filtering, selecting target groups, creating modals, buttons, and
     * notifications. It integrates user sessions and controls multiple UI components for enhanced
     * user interactions.
     */
    class VideosListPanel extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToast1;
        protected Q\Plugin\Toastr $dlgToast2;
        protected Q\Plugin\Toastr $dlgToast3;
        protected Q\Plugin\Toastr $dlgToast4;
        protected Q\Plugin\Toastr $dlgToast5;
        protected Q\Plugin\Toastr $dlgToast6;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;

        public Bs\Button $btnMove;
        public Bs\Button $btnLockedCancel;
        public ?Q\Plugin\Select2 $lstVideosLocked = null;
        public Q\Plugin\Select2 $lstTargetGroup;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public VideosTable $dtgVideos;
        public Bs\Button $btnBack;

        protected object $objUser;
        protected int $intLoggedUserId;
        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'VideosListPanel.tpl.php';

        /**
         * Constructor method for the class.
         *
         * Initializes the class with a parent object and an optional control ID.
         * It sets up the necessary elements such as user information, inputs, buttons, modals, filters, and data grids.
         *
         * @param mixed $objParentObject The parent object that contains this control.
         * @param string|null $strControlId Optional unique identifier for the control.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller Thrown if there is an exception during the construction process.
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
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

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgVideos_Create();
            $this->dtgVideos->setDataBinder('BindData', $this);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Resets the state of elements by hiding specific UI components.
         *
         * This method executes a JavaScript command to add the 'hidden' class to elements with the 'move-items-js'
         * class, effectively hiding them.
         *
         * @return void
         * @throws Caller
         */
        public function elementsReset(): void
        {
            Application::executeJavaScript("$('.move-items-js').addClass('hidden')");
        }

        /**
         * Creates and initializes input controls for the target group selection.
         *
         * This method configures a Select2 dropdown component with custom settings such as theme, width, and selection mode.
         * It populates the dropdown with target group options retrieved from the VideosSettings data source, excluding reserved items.
         * Additionally, it attaches an AJAX change event handler to the dropdown for dynamic interactions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createInputs(): void
        {
            $this->lstTargetGroup = new Q\Plugin\Select2($this);
            $this->lstTargetGroup->MinimumResultsForSearch = -1;
            $this->lstTargetGroup->Theme = 'web-vauu';
            $this->lstTargetGroup->Width = '100%';
            $this->lstTargetGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);

            $objTargetGroups = VideosSettings::loadAll(QQ::Clause(QQ::orderBy(QQN::VideosSettings()->Id)));
            foreach ($objTargetGroups as $objTitle) {
                if ($objTitle->IsReserved !== 2) {
                    $this->lstTargetGroup->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstTargetGroup->addAction(new Change(), new AjaxControl($this,'lstTargetGroup_Change'));
            $this->lstTargetGroup->Enabled = false;
        }

        /**
         * Create and configure the 'Back' button with associated actions and styles
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnMove = new Bs\Button($this);
            $this->btnMove->Text = t(' Move');
            $this->btnMove->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnMove->CssClass = 'btn btn-darkblue move-button-js';
            $this->btnMove->addWrapperCssClass('center-button');
            $this->btnMove->CausesValidation = false;
            $this->btnMove->addAction(new Click(), new AjaxControl($this, 'btnMove_Click'));

            Application::executeJavaScript("$('.move-items-js').addClass('hidden')");

            $this->btnLockedCancel = new Bs\Button($this);
            $this->btnLockedCancel->Text = t('Cancel');
            $this->btnLockedCancel->addWrapperCssClass('center-button');
            $this->btnLockedCancel->CssClass = 'btn btn-default';
            $this->btnLockedCancel->CausesValidation = false;
            $this->btnLockedCancel->addAction(new Click(), new AjaxControl($this, 'btnLockedCancel_Click'));

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->addWrapperCssClass('center-button');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new AjaxControl($this,'btnBack_Click'));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and configures multiple Toastr notifications used within the interface.
         *
         * This method initializes several instances of the Toastr plugin, each
         * configured with a specific alert type, position, message, and display properties.
         * These notifications are used to display feedback messages such as
         * errors, successes, or alerts within the application.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast3 = new Q\Plugin\Toastr($this);
            $this->dlgToast3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast3->Message = t('<p style=\"margin-bottom: 5px;\">The videos group must be selected beforehand!</p>');
            $this->dlgToast3->ProgressBar = true;
            $this->dlgToast3->TimeOut = 10000;
            $this->dlgToast3->EscapeHtml = false;

            $this->dlgToast4 = new Q\Plugin\Toastr($this);
            $this->dlgToast4->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast4->Message = t('<p style=\"margin-bottom: 5px;\">The videos group cannot be the same as the target group!</p>');
            $this->dlgToast4->ProgressBar = true;
            $this->dlgToast4->TimeOut = 10000;
            $this->dlgToast4->EscapeHtml = false;

            $this->dlgToast5 = new Q\Plugin\Toastr($this);
            $this->dlgToast5->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast5->Message = t('<strong>Well done!</strong> The transfer of this videos group to the new group was successful.');
            $this->dlgToast5->ProgressBar = true;

            $this->dlgToast6 = new Q\Plugin\Toastr($this);
            $this->dlgToast6->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast6->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast6->Message = t('The transfer of this videos group to the new group failed.');
            $this->dlgToast6->ProgressBar = true;
        }

        /**
         * Creates modal dialogs used in the application.
         *
         * This method sets up modal components for user interaction and informs users about actions
         * or warnings. It initializes two modals:
         *
         * 1. A modal to confirm the transfer of videos from one group to another. It includes a warning
         *    message, an "I accept" button to proceed with the transfer, and a cancel button. It also
         *    attaches event handlers for dialog button clicks and modal close events.
         * 2. A modal for CSRF protection alerts, informing the user about an invalid CSRF token with an
         *    appropriate warning message and a close button for an acknowledgment.
         *
         * @return void
         * @throws Caller
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move the videos from this videos group to another videos group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Note: If the selected group contains multiple videos, they will all be transferred to the new group!</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addCloseButton(t("I'll cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'moveItems_Click'));
            $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new AjaxControl($this, 'transferCancelling_Click'));

            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal2->Title = t("Warning");
            $this->dlgModal2->HeaderClasses = 'btn-danger';
            $this->dlgModal2->addCloseButton(t("I understand"));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for the "Move" button.
         *
         * This method manages the interaction of videos grouping in the UI. It validates CSRF tokens,
         * initializes and populates a dropdown menu for selecting video groups, sets up actions,
         * and manages UI elements' enablement and focus states based on business logic.
         *
         * @param ActionParams $params The parameters passed from the triggered action, including event context and data.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnMove_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("$('.move-items-js').removeClass('hidden')");

            $this->lstVideosLocked = new Q\Plugin\Select2($this);
            $this->lstVideosLocked->MinimumResultsForSearch = -1;
            $this->lstVideosLocked->Theme = 'web-vauu';
            $this->lstVideosLocked->Width = '100%';
            $this->lstVideosLocked->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstVideosLocked->addItem(t('- Select one video group -'), null, true);

            $objGroups = VideosSettings::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::VideosSettings()->VideosLocked, 0), QQN::VideosSettings()->Id)
                ]
            );

            $countLocked = VideosSettings::countByVideosLocked(1);

            foreach ($objGroups as $objTitle) {
                if ($countLocked > 1 && $objTitle->VideosLocked === 1) {
                    $this->lstVideosLocked->addItem($objTitle->Name, $objTitle->Id);
                } else if ($countLocked === 1 && $objTitle->VideosLocked === 1) {
                    $this->lstVideosLocked->addItem($objTitle->Name, $objTitle->Id);
                    $this->lstVideosLocked->SelectedValue = $objTitle->Id;
                }
            }

            $this->lstVideosLocked->addAction(new Change(), new AjaxControl($this,'lstVideosLocked_Change'));

            if ($this->lstVideosLocked->SelectedValue === null) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->Enabled = false;
            }

            if ($countLocked === 1) {
                $this->lstVideosLocked->Enabled = false;
                $this->lstTargetGroup->Enabled = true;
                $this->lstTargetGroup->focus();
            } else {
                $this->lstVideosLocked->Enabled = true;
                $this->lstVideosLocked->focus();
            }

            $this->btnMove->Enabled = false;
            $this->dtgVideos->addCssClass('disabled');
        }

        /**
         * Handles the change event for the 'lstVideosLocked' control.
         * Validates the CSRF token and updates the state of the related UI components
         * based on the selected value of 'lstVideosLocked'.
         *
         * @param ActionParams $params The parameters associated with the action trigger.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstVideosLocked_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstVideosLocked->SelectedValue === null) {
                $this->lstTargetGroup->Enabled = false;
                $this->lstVideosLocked->addCssClass('has-error');
                $this->dlgToast3->notify();
            } else {
                $this->lstTargetGroup->Enabled = true;
                $this->lstVideosLocked->removeCssClass('has-error');
                $this->lstTargetGroup->focus();
            }
        }

        /**
         * Handles the change event for the 'lstTargetGroup' control.
         * Validates the CSRF token and updates the state of the 'lstTargetGroup' and related UI components
         * based on the selected values of 'lstVideosLocked' and 'lstTargetGroup'.
         *
         * @param ActionParams $params The parameters associated with the action trigger.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstTargetGroup_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstVideosLocked->SelectedValue === $this->lstTargetGroup->SelectedValue) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->refresh();
                $this->lstTargetGroup->addCssClass('has-error');
                $this->dlgToast4->notify();
            } else if ($this->lstVideosLocked->SelectedValue !== null && $this->lstTargetGroup->SelectedValue !== null) {
                $this->dlgModal1->showDialogBox();
            } else {
                $this->lstTargetGroup->removeCssClass('has-error');
            }
        }

        /**
         * Handles the click event for the 'transferCancelling' action.
         * Verifies the CSRF token and resets the state of the related UI components
         * to their default values while enabling specific controls and refreshing affected elements.
         *
         * @param ActionParams $params The parameters associated with the action trigger.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function transferCancelling_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->elementsReset();

            $this->lstVideosLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->btnMove->Enabled = true;

            $this->lstVideosLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->dtgVideos->removeCssClass('disabled');
        }

        /**
         * Handles the click event for the 'moveItems' operation.
         * Validates the CSRF token, performs the transfer of videos, updates the UI components,
         * and resets the state after the operation is complete.
         *
         * @param ActionParams $params The parameters associated with the action trigger.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function moveItems_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->dlgModal1->hideDialogBox();
            $this->videosTransferOperations();

            $this->elementsReset();
            $this->btnMove->Enabled = true;
        }

        /**
         * Performs the transfer operation of videos between two groups/settings.
         * Updates the status, locked state, and assigned editor information of the
         * involved settings and associated videos. Manages UI refresh and notifies
         * users based on the results of the operation.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        private function videosTransferOperations(): void
        {
            $objLockedGroup = VideosSettings::loadById($this->lstVideosLocked->SelectedValue);
            $objTargetGroup = VideosSettings::loadById($this->lstTargetGroup->SelectedValue);

            $objVideosGroupArray = Videos::loadArrayBySettingsId($objLockedGroup->getId());
            $beforeCount = Videos::countBySettingsId($objTargetGroup->getId());

            $objVideosSettings = VideosSettings::loadById($objLockedGroup->getId());
            $objVideosSettings->setVideosLocked(0);
            $objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $objVideosSettings->save();

            $objVideosSettings = VideosSettings::loadById($objTargetGroup->getId());
            $objVideosSettings->setVideosLocked(1);
            $objVideosSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objVideosSettings->setPostUpdateDate(Q\QDateTime::now());
            $objVideosSettings->save();

            foreach ($objVideosGroupArray as $objVideosGroup) {
                $objVideos = Videos::loadById($objVideosGroup->getId());
                $objVideos->setSettingsId($this->lstTargetGroup->SelectedValue);
                $objVideos->setSettingsIdTitle($this->lstTargetGroup->SelectedName);
                $objVideos->save();
            }

            $this->dtgVideos->refresh();

            Application::executeJavaScript("$('.move-items-js').addClass('hidden');");

//            if (VideosSettings::countAll() !== 1) {
//                Application::executeJavaScript("
//                    $('.move-items-js').addClass('hidden');
//                ");
//            } else {
//                Application::executeJavaScript("
//                    $('.move-items-js').addClass('hidden');
//                ");
//            }

            $afterCount = Videos::countBySettingsId($objTargetGroup->getId());

            if ($beforeCount < $afterCount) {
                $this->dlgToast5->notify();
            } else {
                $this->dlgToast6->notify();
            }

            $objVideoArray = Videos::loadAll(QQ::Clause(QQ::orderBy(QQN::Videos()->PostDate, false)));

            foreach ($objVideoArray as $key => $objVideo) {
                $objVideos = Videos::loadById($objVideo->getId());
                $objVideos->setOrder($key);
                $objVideos->save();
            }
        }

        /**
         * Handles the click event for the 'btnLockedCancel' button.
         * Validates the CSRF token, updates UI elements, and resets the state of specific controls.
         *
         * @param ActionParams $params The parameters associated with the action trigger.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnLockedCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("$('.move-items-js').addClass('hidden');");

//            if (VideosSettings::countAll() !== 1) {
//                Application::executeJavaScript("
//                    $('.move-items-js').addClass('hidden');
//                ");
//            } else {
//                Application::executeJavaScript("
//                    $('.move-items-js').addClass('hidden');
//                ");
//            }

            $this->btnMove->Enabled = true;

            $this->lstVideosLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->lstVideosLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->dtgVideos->removeCssClass('disabled');
        }

        /**
         * Handles the 'Back' button click event by redirecting to the menu manager page.
         *
         * @param ActionParams $params The parameters for the action event, typically including context-specific information about the event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnBack_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::redirect('menu_manager.php');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Create and configure the boards datagrid
         *
         * @return void
         * @throws Caller
         */
        protected function dtgVideos_Create(): void
        {
            $this->dtgVideos = new VideosTable($this);
            $this->dtgVideos_CreateColumns();
            $this->createPaginators();
            $this->dtgVideos_MakeEditable();
            $this->dtgVideos->RowParamsCallback = [$this, "dtgVideos_GetRowParams"];
            $this->dtgVideos->SortColumnIndex = 5;
            //$this->dtgVideos->SortDirection = -1;
            $this->dtgVideos->UseAjax = true;
            $this->dtgVideos->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
        }

        /**
         * Create columns for the datagrid
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgVideos_CreateColumns(): void
        {
            $this->dtgVideos->createColumns();
        }

        /**
         * Configures the dtgVideos datatable to be interactive and editable by adding
         * appropriate actions and CSS classes. This method enables cell click actions
         * that trigger an AJAX control event and applies specified CSS classes to the table.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgVideos_MakeEditable(): void
        {
            $this->dtgVideos->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgVideosRow_Click'));
            $this->dtgVideos->addCssClass('clickable-rows');
            $this->dtgVideos->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles click events on rows of the dtgVideos datatable. Retrieves the board
         * settings based on the action parameter's identifier, then redirects the user
         * to the board edit page with the board's ID and group information as query parameters.
         *
         * @param ActionParams $params The parameters associated with the action, containing
         *                             the identifier of the clicked row's board.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgVideosRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intId = intval($params->ActionParameter);
            $objVideos = VideosSettings::loadById($intId);
            $intGroup = $objVideos->getMenuContentId();

            Application::redirect('videos_edit.php' . '?id=' . $intId . '&group=' . $intGroup);
        }

        /**
         * Retrieves the row parameters for the given video row object.
         * Constructs custom parameters to be associated with a specific row in the data grid.
         *
         * @param mixed $objRowObject The row object representing a video entry.
         * @param int $intRowIndex The index of the current row in the data grid.
         *
         * @return array An associative array of parameters for the row.
         */
        public function dtgVideos_GetRowParams(mixed $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Sets up pagination for the dtgVideos datatable by initializing primary and
         * alternate paginators with labels for navigation controls and specifying
         * the number of items displayed per page. Additionally, invokes actions
         * to handle filtering of data within the table.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgVideos->Paginator = new Bs\Paginator($this);
            $this->dtgVideos->Paginator->LabelForPrevious = t('Previous');
            $this->dtgVideos->Paginator->LabelForNext = t('Next');

            $this->dtgVideos->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgVideos->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgVideos->PaginatorAlternate->LabelForNext = t('Next');

            $this->dtgVideos->ItemsPerPage = 10;

            $this->addFilterActions();
        }

        /**
         * Initializes and configures a Select2 control for selecting the number of items
         * per a page by an assigned user. This method sets various properties such as the theme,
         * width, and selection mode. It also populates the control with item options and
         * attaches an AJAX change event to handle user interactions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createItemsPerPage(): void
        {
            $this->lstItemsPerPageByAssignedUserObject = new Q\Plugin\Select2($this);
            $this->lstItemsPerPageByAssignedUserObject->MinimumResultsForSearch = -1;
            $this->lstItemsPerPageByAssignedUserObject->Theme = 'web-vauu';
            $this->lstItemsPerPageByAssignedUserObject->Width = '100%';
            $this->lstItemsPerPageByAssignedUserObject->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->ItemsPerPageByAssignedUser;
            $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstItemsPerPageByAssignedUserObject_GetItems());
            $this->lstItemsPerPageByAssignedUserObject->AddAction(new Change(), new AjaxControl($this, 'lstItemsPerPageByAssignedUserObject_Change'));
        }

        /**
         * Retrieves a list of ListItems representing items per a page associated with an assigned user object.
         * This method queries the database for items per page objects based on a specified condition and
         * returns them as ListItem objects. The ListItem will be marked as selected if it matches the
         * currently assigned user object's item.
         *
         * @return ListItem[] An array of ListItems containing items per a page associated with an assigned user object.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstItemsPerPageByAssignedUserObject_GetItems(): array
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
         * Updates the number of items displayed per page for a data grid based on the selection
         * from a list associated with an assigned user object. This method adjusts the items per
         * page of the data grid and refreshes it to reflect the updated pagination settings.
         *
         * @param ActionParams $params The action parameters containing details of the change event.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgVideos->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgVideos->refresh();
        }

        /**
         * Creates a filter control for user input. Initializes a text box with specific
         * properties and styling to serve as a search input field. This text box is designed
         * to provide a seamless user experience for entering search queries.
         *
         * @return void This method does not return any value.
         * @throws Caller
         */
        protected function createFilter(): void
        {
            $this->txtFilter = new Bs\TextBox($this);
            $this->txtFilter->Placeholder = t('Search...');
            $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
            $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
            $this->txtFilter->addCssClass('search-box');

            $this->btnClearFilters = new Bs\Button($this);
            $this->btnClearFilters->Text = t('Clear filters');
            $this->btnClearFilters->addWrapperCssClass('center-button');
            $this->btnClearFilters->CssClass = 'btn btn-default';
            $this->btnClearFilters->setCssStyle('float', 'left');
            $this->btnClearFilters->CausesValidation = false;
            $this->btnClearFilters->addAction(new Click(), new AjaxControl($this, 'clearFilters_Click'));

            $this->addFilterActions();
        }

        /**
         * Clears all filters from the interface and refreshes the relevant components.
         * This method resets the filter text field and refreshes both the filter input and the datagrid
         * to display all data without any filtering applied.
         *
         * @param ActionParams $params The parameters passed to the click action, typically containing event details.
         *
         * @return void
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->dtgVideos->refresh();
        }

        /**
         * Adds filter actions to the txtFilter control. This method sets up event-driven
         * interactions for the filter functionality. It registers an input event that triggers
         * an AJAX action after a delay, as well as an enter key event that initiates both an
         * AJAX action and an action termination.
         *
         * @return void
         * @throws Caller
         */
        protected function addFilterActions(): void
        {
            $this->txtFilter->addAction(new Input(300), new AjaxControl($this, 'filterChanged'));
            $this->txtFilter->addActionArray(new EnterKey(),
                [
                    new AjaxControl($this, 'FilterChanged'),
                    new Terminate()
                ]
            );
        }

        /**
         * Handles the event when a filter is changed, triggering the refresh of the board data grid.
         * This method updates the displayed data in the data grid to reflect the current filter criteria.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgVideos->refresh();
        }

        /**
         * Binds data to the data table by applying a specific condition.
         * This method retrieves a condition, typically used for filtering or querying purposes,
         * and applies it to bind data to a data table component.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgVideos->bindData($objCondition);
        }


        /**
         * Generates a query condition based on the current text filter input.
         *
         * If the filter input is empty or null, a condition that matches all entries is returned.
         * Otherwise, returns a compound condition that performs a case-insensitive search for the filter input
         * within the Picture, GroupTitle, Title, Category, and Author fields of the News entity.
         *
         * @return All|OrCondition The generated query condition, either matching all entries or matching specified
         *     fields against the filter input.
         * @throws Caller
         */
        protected function getCondition(): All|OrCondition
        {
            $strSearchValue = $this->txtFilter->Text;

            if ($strSearchValue === null) {
                $strSearchValue = '';
            }

            $strSearchValue = trim($strSearchValue);

            if ($strSearchValue === '') {
                return QQ::all();
            } else {
                return QQ::orCondition(
                    QQ::like(QQN::VideosSettings()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::VideosSettings()->Title, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::VideosSettings()->Author, "%" . $strSearchValue . "%")
                );
            }
        }
    }