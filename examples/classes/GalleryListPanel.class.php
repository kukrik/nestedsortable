<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\Condition\AndCondition;
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
    use QCubed\Folder;
    use QCubed\QString;

    /**
     * GalleryListPanel is a class responsible for managing the interface and interactions
     * related to displaying and organizing a list of galleries. It provides functionality
     * for managing albums, configuring input controls, and handling user interactions.
     */
    class GalleryListPanel extends Panel
    {
        protected string $strRootPath = APP_UPLOADS_DIR;
        protected string $strRootUrl = APP_UPLOADS_URL;
        protected string $strTempPath = APP_UPLOADS_TEMP_DIR;
        protected string $strTempUrl = APP_UPLOADS_TEMP_URL;
        protected array $tempFolders = ['thumbnail', 'medium', 'large'];
        protected array $arrAllowed = array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');

        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToast1;
        protected Q\Plugin\Toastr $dlgToast2;
        protected Q\Plugin\Toastr $dlgToast3;
        protected Q\Plugin\Toastr $dlgToast4;
        protected Q\Plugin\Toastr $dlgToast5;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public GalleryTable $dtgGallery;

        public Bs\Button $btnAddAlbum;
        public Bs\Button $btnMove;
        public Bs\TextBox $txtTitle;
        public ?Q\Plugin\Select2 $lstGalleryLocked = null;
        public Q\Plugin\Select2 $lstTargetGroup;
        public ?Q\Plugin\Select2 $lstGroupTitle = null;
        public Q\Plugin\Select2 $lstGroups;
        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnLockedCancel;
        public Bs\Button $btnBack;

        protected object $objGalleryList;

        protected object $objUser;
        protected int $intLoggedUserId;
        protected int $countByIsReserved;
        protected int $countByAlbumsLocked;
        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $changeColClass;

        protected string $strTemplate = 'GalleryListPanel.tpl.php';

        /**
         * Constructor method for initializing the component.
         *
         * @param mixed $objParentObject The parent object that will hold this control.
         * @param string|null $strControlId Optional Control ID for this object. If null, a unique ID will be generated
         *     automatically.
         *
         * @throws Caller
         * @throws InvalidCast
         * @throws \DateMalformedStringException
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->countByIsReserved = GallerySettings::countByIsReserved(1);
            $this->countByAlbumsLocked = GallerySettings::countByAlbumsLocked(1);

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
            $this->elementsReset();

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgGallery_Create();
            $this->dtgGallery->setDataBinder('BindData', $this);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the input controls necessary for the current form or context.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createInputs(): void
        {
            $this->txtTitle = new Bs\TextBox($this);
            $this->txtTitle->Placeholder = t('Title of the new album');
            $this->txtTitle->setCssStyle('float', 'left');
            $this->txtTitle->setHtmlAttribute('autocomplete', 'off');

            $this->lstTargetGroup = new Q\Plugin\Select2($this);
            $this->lstTargetGroup->MinimumResultsForSearch = -1;
            $this->lstTargetGroup->Theme = 'web-vauu';
            $this->lstTargetGroup->Width = '100%';
            $this->lstTargetGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstTargetGroup->addItem(t('- Select one target group -'), null, true);

            $objTargetGroups = GallerySettings::loadAll(QQ::Clause(QQ::orderBy(QQN::GallerySettings()->Id)));
            foreach ($objTargetGroups as $objTitle) {
                if ($objTitle->IsReserved !== 2) {
                    $this->lstTargetGroup->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstTargetGroup->addAction(new Change(), new AjaxControl($this,'lstTargetGroup_Change'));
            $this->lstTargetGroup->setCssStyle('float', 'left');
            $this->lstTargetGroup->Enabled = false;
        }

        /**
         * Initializes and configures various buttons for the interface, including the actions and styles each button
         * will have.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnAddAlbum = new Bs\Button($this);
            $this->btnAddAlbum->Text = t(' Add album');
            $this->btnAddAlbum->Glyph = 'fa fa-plus';
            $this->btnAddAlbum->CssClass = 'btn btn-orange';
            $this->btnAddAlbum->addWrapperCssClass('center-button');
            $this->btnAddAlbum->CausesValidation = false;
            $this->btnAddAlbum->addAction(new Click(), new AjaxControl($this, 'btnAddAlbum_Click'));

            $this->btnMove = new Bs\Button($this);
            $this->btnMove->Text = t(' Move');
            $this->btnMove->Glyph = 'fa fa-flip-horizontal fa-reply-all';
            $this->btnMove->CssClass = 'btn btn-darkblue move-button-js';
            $this->btnMove->addWrapperCssClass('center-button');
            $this->btnMove->CausesValidation = false;
            $this->btnMove->addAction(new Click(), new AjaxControl($this, 'btnMove_Click'));

            if (GallerySettings::countAll() !== 1) {
                Application::executeJavaScript("
                    $('.move-button-js').removeClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            }

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange save-js';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->setCssStyle('float', 'left');
            $this->btnSave->setCssStyle('margin-right', '10px');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->setCssStyle('float', 'left');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));

            $this->btnLockedCancel = new Bs\Button($this);
            $this->btnLockedCancel->Text = t('Cancel');
            $this->btnLockedCancel->addWrapperCssClass('center-button');
            $this->btnLockedCancel->CssClass = 'btn btn-default';
            $this->btnLockedCancel->setCssStyle('float', 'left');
            $this->btnLockedCancel->CausesValidation = false;
            $this->btnLockedCancel->addAction(new Click(), new AjaxControl($this, 'btnLockedCancel_Click'));

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->addWrapperCssClass('center-button');
            $this->btnBack->CausesValidation = false;
            $this->btnBack->addAction(new Click(), new AjaxControl($this,'btnBack_Click'));
        }

        /**
         * Creates and configures multiple Toastr notifications with specific messages,
         * alert types, and other properties for user notification purposes.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('The album title is at least mandatory!');
            $this->dlgToast1->ProgressBar = true;

            $this->dlgToast2 = new Q\Plugin\Toastr($this);
            $this->dlgToast2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast2->Message = t('<p style=\"margin-bottom: 5px;\">The gallery group must be selected beforehand!</p>');
            $this->dlgToast2->ProgressBar = true;
            $this->dlgToast2->TimeOut = 10000;
            $this->dlgToast2->EscapeHtml = false;

            $this->dlgToast3 = new Q\Plugin\Toastr($this);
            $this->dlgToast3->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast3->Message = t('<p style=\"margin-bottom: 5px;\">The gallery group cannot be the same as the target group!</p>');
            $this->dlgToast3->ProgressBar = true;
            $this->dlgToast3->TimeOut = 10000;
            $this->dlgToast3->EscapeHtml = false;

            $this->dlgToast4 = new Q\Plugin\Toastr($this);
            $this->dlgToast4->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast4->Message = t('<strong>Well done!</strong> The transfer of albums to the new group was successful.');
            $this->dlgToast4->ProgressBar = true;

            $this->dlgToast5 = new Q\Plugin\Toastr($this);
            $this->dlgToast5->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast5->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast5->Message = t('The transfer of albums to the new group failed.');
            $this->dlgToast5->ProgressBar = true;
        }

        /**
         * Creates and configures modal dialogs for user interactions.
         *
         * The method sets up the properties and actions of a modal dialog, including its text,
         * title, header classes, and buttons. It also specifies actions triggered by modal events.
         *
         * @return void
         * @throws Caller
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move the albums from this gallery group to another gallery group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Note! If there are several albums in the selected group, they will be transferred to the new group!</p>');
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
         * Resets the visibility of UI elements by adding 'hidden' class to specific elements.
         *
         * @return void
         * @throws Caller
         */
        public function elementsReset(): void
        {
            Application::executeJavaScript("
                $('.new-item-js').addClass('hidden');
                $('.move-items-js').addClass('hidden');
            ");
        }

        /**
         * Handles the 'click' event for the Add Album button. This method updates UI elements and initializes
         * a select control for selecting a gallery group. The select control's behavior varies depending on
         * the number of gallery groups available.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnAddAlbum_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::executeJavaScript("
                $('.move-button-js').addClass('hidden');
                $('.new-item-js').removeClass('hidden');
                $('.move-items-js').addClass('hidden');
            ");

            $this->updateLockStatus();
            $this->disableInputs();

            $this->txtTitle->Text = '';
            $this->btnAddAlbum->Enabled = false;
            $this->dtgGallery->addCssClass('disabled');

            $this->lstGroupTitle = new Q\Plugin\Select2($this);
            $this->lstGroupTitle->MinimumResultsForSearch = -1;
            $this->lstGroupTitle->Theme = 'web-vauu';
            $this->lstGroupTitle->Width = '100%';
            $this->lstGroupTitle->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroupTitle->addItem(t('- Select one gallery group -'), null, true);

            $objGroups = GallerySettings::loadAll(QQ::Clause(QQ::orderBy(QQN::GallerySettings()->Id)));

            foreach ($objGroups as $objTitle) {
                if ($objTitle->IsReserved === 1 && $this->countByIsReserved === 1) {
                    $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                    $this->lstGroupTitle->SelectedValue = $objTitle->Id;
                } else if ($objTitle->IsReserved === 1 && $this->countByIsReserved > 1) {
                    $this->lstGroupTitle->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstGroupTitle->addAction(new Change(), new AjaxControl($this,'lstGroupTitle_Change'));
            $this->lstGroupTitle->setHtmlAttribute('required', 'required');

            if ($this->countByIsReserved === 1) {
                $this->lstGroupTitle->Enabled = false;
                $this->txtTitle->focus();
            } else {
                $this->lstGroupTitle->Enabled = true;
                $this->lstGroupTitle->focus();
            }
        }

        /**
         * Handles the click event for the move button. This method manages the display
         * of certain UI elements, configures a Select2 widget, and updates the state
         * and appearance of various UI controls based on the conditions related to
         * gallery group selection.
         *
         * @param ActionParams $params The parameters related to the action event.
         *
         * @return void Does not return any value.
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

            Application::executeJavaScript("
                $('.move-items-js').removeClass('hidden');
                $('.new-item-js').addClass('hidden'); 
            ");

            $this->updateLockStatus();
            $this->disableInputs();

            $this->lstGalleryLocked = new Q\Plugin\Select2($this);
            $this->lstGalleryLocked->MinimumResultsForSearch = -1;
            $this->lstGalleryLocked->Theme = 'web-vauu';
            $this->lstGalleryLocked->Width = '100%';
            $this->lstGalleryLocked->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGalleryLocked->addItem(t('- Select one gallery group -'), null, true);

            $objGroups = GallerySettings::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::GallerySettings()->AlbumsLocked, 0), QQN::GallerySettings()->Id)
                ]
            );

            foreach ($objGroups as $objTitle) {
                if ($this->countByIsReserved > 1 && $objTitle->AlbumsLocked === 1) {
                    $this->lstGalleryLocked->addItem($objTitle->Name, $objTitle->Id);
                } else if ($this->countByIsReserved === 1 && $objTitle->AlbumsLocked === 1) {
                    $this->lstGalleryLocked->addItem($objTitle->Name, $objTitle->Id);
                    $this->lstGalleryLocked->SelectedValue = $objTitle->Id;
                }
            }

            $this->lstGalleryLocked->addAction(new Change(), new AjaxControl($this,'lstGalleryLocked_Change'));

            if ($this->lstGalleryLocked->SelectedValue === null) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->Enabled = false;
            }

            if ($this->countByIsReserved === 1) {
                $this->lstGalleryLocked->Enabled = false;
                $this->lstTargetGroup->Enabled = true;
                $this->lstTargetGroup->focus();
            } else {
                $this->lstGalleryLocked->Enabled = true;
                $this->lstGalleryLocked->focus();
            }

            $this->btnAddAlbum->Enabled = false;
            $this->btnMove->Enabled = false;
            $this->dtgGallery->addCssClass('disabled');
        }

        /**
         * Handles the change event for the group title selection list.
         *
         * @param ActionParams $params The parameters associated with the change event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstGroupTitle_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstGroupTitle->SelectedValue === null) {
                $this->lstGroupTitle->focus();
                $this->dlgToast2->notify();
            } else {
                $this->txtTitle->focus();
            }
        }

        /**
         * Handles the change event for the lstGalleryLocked list, enabling or disabling
         * the lstTargetGroup based on the selected value. Notifies with a toast if
         * no value is selected and marks the field with an error class.
         *
         * @param ActionParams $params The parameters associated with the change action
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function lstGalleryLocked_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstGalleryLocked->SelectedValue === null) {
                $this->lstTargetGroup->Enabled = false;
                $this->lstGalleryLocked->addCssClass('has-error');
                $this->dlgToast2->notify();
            } else {
                $this->lstTargetGroup->Enabled = true;
                $this->lstGalleryLocked->removeCssClass('has-error');
                $this->lstTargetGroup->focus();
            }
        }

        /**
         * Handles the change event for the target group selection list.
         *
         * @param ActionParams $params The parameters associated with the action event.
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

            if ($this->lstGalleryLocked->SelectedValue === $this->lstTargetGroup->SelectedValue) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->refresh();
                $this->lstTargetGroup->addCssClass('has-error');
                $this->dlgToast3->notify();
            } else if ($this->lstGalleryLocked->SelectedValue !== null && $this->lstTargetGroup->SelectedValue !== null) {
                $this->dlgModal1->showDialogBox();
            } else {
                $this->lstTargetGroup->removeCssClass('has-error');
            }
        }

        /**
         * Handles the click event for the transfer cancellation process.
         * Resets certain elements and re-enables specific buttons in the UI.
         *
         * @param ActionParams $params The parameters associated with the click action.
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

            $this->lstGalleryLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->btnAddAlbum->Enabled = true;
            $this->btnMove->Enabled = true;

            $this->lstGalleryLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->enableInputs();
            $this->dtgGallery->removeCssClass('disabled');
        }

        /**
         * Handles the click event for moving items from one context to another.
         *
         * @param ActionParams $params The parameters passed with the click event.
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
            $this->albumsTransferOperations();

            $this->elementsReset();
            $this->btnAddAlbum->Enabled = true;
            $this->btnMove->Enabled = true;
        }

        /**
         * Executes operations for transferring albums between groups in the gallery application.
         *
         * This method handles the migration of albums, images, and their associated data from a source group
         * to a target group, ensuring all relevant database entries and folder structures are updated. It
         * includes steps such as resetting lock statuses, updating paths, folder associations, and notifying
         * users of the changes. The method ensures data integrity and uniqueness checks throughout the process.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        private function albumsTransferOperations(): void
        {
            $objLockedGroup = GallerySettings::loadById($this->lstGalleryLocked->SelectedValue);
            $objTargetGroup = GallerySettings::loadById($this->lstTargetGroup->SelectedValue);
            $objGalleryLists = GalleryList::loadByGalleryListId($objLockedGroup->getId());
            $objGalleryListArray = [];

            foreach ($objGalleryLists as $objGalleryList) {
                $objGalleryListArray[] = $objGalleryList->getId();
            }

            // Resetting the album counter
            $beforeCount = count(Folder::listFilesInFolder($this->strRootPath . $objTargetGroup->getTitleSlug(), false));

            // Here you need to make some small changes to GallerySettings right away.
            $objLockedGroup->setAlbumsLocked(0);
            $objLockedGroup->setPostUpdateDate(Q\QDateTime::Now());
            $objLockedGroup->save();

            if ($objTargetGroup->getAlbumsLocked() == 0) {
                $objTargetGroup->setAlbumsLocked(1);
                $objTargetGroup->setPostUpdateDate(Q\QDateTime::Now());
                $objTargetGroup->save();
            }

            // Here you must first check the lock status of the following folder to do this check...
            if (GalleryList::countByParentFolderId($objTargetGroup->getFolderId()) == 0) {
                $objFolder = Folders::loadById($objTargetGroup->getFolderId());
                $objFolder->setLockedFile(1);
                $objFolder->save();
            }

            // Next, we check the lock status of the previous folder, to do this, check...
            if ($objLockedGroup->getFolderId()) {
                $objFolder = Folders::loadById($objLockedGroup->getFolderId());

                if (GalleryList::countByParentFolderId($objLockedGroup->getFolderId()) == 1) {
                    $objFolder->setLockedFile(0);
                } else {
                    $objFolder->setLockedFile(1);
                }
                $objFolder->save();
            }

            // Prepare the next folder for path formatting
            $strPath = '/' . QString::sanitizeForUrl($objTargetGroup->getName()) . '/';

            foreach ($objGalleryListArray as $objGalleryList) {
                $objList = GalleryList::loadById($objGalleryList);
                $objFolder = Folders::loadById($objList->getFolderId());
                $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objList->getId());
                $objFileArray = Files::loadArrayByFolderId($objList->getFolderId());
                $objAlbumArray = Album::loadArrayByFolderId($objList->getFolderId());

                // Prepare here to change the title to be PATH-compatible
                $strNewPath = $strPath . QString::sanitizeForUrl($objList->getTitle());

                $src = $this->strRootPath . $objList->getPath();
                $dst = $this->strRootPath . GalleryList::generateUniqueSlug($strNewPath);

                // We keep the path and slug separate, we only update the slug
                $fullPath = $this->strRootPath . '/' . QString::sanitizeForUrl($objTargetGroup->getName());
                $verifiedFolder = $this->generateUniqueFolderName(QString::sanitizeForUrl($objList->getTitle()), $fullPath);

                $objSettings = GallerySettings::loadById($objTargetGroup->getId());
                $updatedSlug = $objSettings->getTitleSlug() . '/' . $verifiedFolder;

                // Now we need to change the GalleryList data: we need to change menu-content_group_id, parent_folder_id, path, title_slug and title uniqueness check
                $objList->setMenuContentGroupId($objTargetGroup->getGalleryGroupId());
                $objList->setGalleryGroupTitleId($objTargetGroup->getId());
                $objList->setGroupTitle($objTargetGroup->getName());
                $objList->setParentFolderId($objTargetGroup->getFolderId());
                $objList->setPath(GalleryList::generateUniqueSlug($strNewPath));
                $objList->setTitleSlug($updatedSlug);
                $objList->setPostUpdateDate(Q\QDateTime::Now());
                $objList->save();

                // However, changing the data of the subfolders that are being moved immediately: parent_id, path and title must be changed to check for uniqueness
                $objFolder->setParentId($objTargetGroup->getFolderId());
                $objFolder->setPath($this->getRelativePath($dst));
                $objFolder->save();

                // Here you need to change some data from the previous folders FrontendLinks
                $objFrontendLink->setGroupedId($objTargetGroup->getGalleryGroupId());
                $objFrontendLink->setFrontendTitleSlug($updatedSlug);
                $objFrontendLink->save();

                // next we check the association of the images in the "files" and "album" tables with the previous folder
                // and change some data to match the next folder
                foreach ($objFileArray as $objFile) {
                    if ($objFile->getFolderId() == $objList->getFolderId()) {
                        $objFile->setPath($this->getRelativePath($dst) . '/' . $objFile->getName());
                        $objFile->save();
                    }
                }

                foreach ($objAlbumArray as $objAlbum) {
                    if ($objAlbum->getFolderId() == $objList->getFolderId()) {
                        $objAlbum->setGalleryListId($objList->getId());
                        $objAlbum->setGalleryGroupTitleId($this->lstTargetGroup->SelectedValue);
                        $objAlbum->setGroupTitle($this->lstTargetGroup->SelectedName);
                        $objAlbum->setPath($this->getRelativePath($dst) . '/' . $objAlbum->getName());
                        $objAlbum->save();
                    }
                }

                // Now we will move the album or album with images to another folder
                $this->fullMove($src, $dst);

                // We need to inform other users about the user who last did
                $objList->setPostUpdateDate(Q\QDateTime::now());
                $objList->setAssignedEditorsNameById($this->intLoggedUserId);
                $objList->save();
            }

            $this->dtgGallery->refresh();

            if (GallerySettings::countAll() !== 1) {
                    Application::executeJavaScript("
                        $('.move-button-js').removeClass('hidden');
                        $('.move-items-js').addClass('hidden');
                        $('.new-item-js').addClass('hidden');
                    ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            }

            // Valuing the album counter
            $afterCount = count(Folder::listFilesInFolder($this->strRootPath . $objTargetGroup->getTitleSlug(), false));

            // Here you should be informed whether the albums or albums with images have been successfully moved to a new location or not
            if ($beforeCount !== $afterCount) {
                $this->dlgToast4->notify();
            } else {
                $this->dlgToast5->notify();
            }

            $this->updateLockStatus();
            $this->enableInputs();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the save button click event for the gallery.
         *
         * This method is responsible for processing user inputs related to gallery settings,
         * validating input fields such as group title and album title, and saving the gallery data.
         * It creates a new folder for the gallery if the required fields are provided, saves
         * the information in the database, and redirects the user to the album edit page upon success.
         *
         * @param ActionParams $params The parameters containing context about the action, such as any associated event data.
         *
         * @return void This method does not return any value. It performs actions like notifications, saving data, and redirecting.
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objGallerySettings = GallerySettings::selectedByIdFromGallerySettings($this->lstGroupTitle->SelectedValue);
            $objTemplateLocking = FrontendTemplateLocking::load(6);
            $objFrontendOptions = FrontendOptions::loadById($objTemplateLocking->FrontendTemplateLockedId);

            if ($this->lstGroupTitle->SelectedValue === null) {
                $this->dlgToast2->notify();
                $this->lstGroupTitle->focus();
            } else if ($this->lstGroupTitle->SelectedValue && !$this->txtTitle->Text) {
                $this->dlgToast1->notify();
                $this->txtTitle->focus();
            } else if ($this->lstGroupTitle->SelectedValue && $this->txtTitle->Text) {

                $fullPath = $this->strRootPath . '/' . QString::sanitizeForUrl($this->lstGroupTitle->SelectedName);

                $relativePath = $this->getRelativePath($fullPath);

                $verifiedName = $this->generateUniqueFolderName(QString::sanitizeForUrl($this->txtTitle->Text), $fullPath);

                $this->makeGallery($verifiedName, $this->lstGroupTitle->SelectedName);

                $objAddFolder = new Folders();
                $objAddFolder->setParentId($objGallerySettings->getFolderId());
                $objAddFolder->setPath($relativePath . '/' . $verifiedName);
                $objAddFolder->setName(trim($this->txtTitle->Text));
                $objAddFolder->setType('dir');
                $objAddFolder->setMtime(filemtime($fullPath . '/' . $verifiedName));
                $objAddFolder->setLockedFile(0);
                $objAddFolder->setActivitiesLocked(1);
                $objAddFolder->save();

                $objLockedFolder = Folders::loadById($objGallerySettings->getFolderId());

                if ($objLockedFolder->getLockedFile() == 0) {
                    $objLockedFolder->setLockedFile(1);
                    $objLockedFolder->setMtime(filemtime($this->strRootPath . '/' . $objLockedFolder->getPath()));
                    $objLockedFolder->save();
                }

                $objGalleryList = new GalleryList();
                $objGalleryList->setMenuContentGroupId($objGallerySettings->getGalleryGroupId());
                $objGalleryList->setGalleryGroupTitleId($objGallerySettings->getId());
                $objGalleryList->setGroupTitle($this->lstGroupTitle->SelectedName);
                $objGalleryList->setParentFolderId($objGallerySettings->getFolderId());
                $objGalleryList->setFolderId($objAddFolder->getId());
                $objGalleryList->setTitle(trim($this->txtTitle->Text));
                $objGalleryList->setPath($relativePath . '/' . $verifiedName);
                $objGalleryList->setTitleSlug($relativePath . '/' . $verifiedName);
                $objGalleryList->setAssignedByUser($this->objUser->getId());
                $objGalleryList->setAuthor($objGalleryList->getAssignedByUserObject());
                $objGalleryList->setStatus(2);
                $objGalleryList->setPostDate(Q\QDateTime::Now());
                $objGalleryList->save();

                $objFrontendLinks = new FrontendLinks();
                $objFrontendLinks->setLinkedId($objGalleryList->getId());
                $objFrontendLinks->setGroupedId($objGallerySettings->getGalleryGroupId());
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->setTitle(trim($this->txtTitle->Text));
                $objFrontendLinks->setContentTypesManagamentId(6);
                $objFrontendLinks->setFrontendTitleSlug($relativePath . '/' . $verifiedName);
                $objFrontendLinks->save();

                $this->dtgGallery->removeCssClass('disabled');
                $this->btnAddAlbum->Enabled = true;
                $this->btnMove->Enabled = true;
                $this->txtTitle->Text = '';
                $this->elementsReset();

                Application::redirect('album_edit.php' . '?id=' . $objGalleryList->getId() . '&group=' . $objGallerySettings->getId() . '&folder=' . $objAddFolder->getId());
            }
        }

        /**
         * Handles the click event for the cancel button.
         *
         * Depending on the count of GallerySettings, this method alters the visibility of certain UI elements
         * using JavaScript execution. It also resets various form fields and refreshes the group title list.
         *
         * @param ActionParams $params The action parameters for the event, providing context or additional data for the click event handling.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (GallerySettings::countAll() === 1) {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').removeClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden'); 
                ");
            }

            $this->btnAddAlbum->Enabled = true;
            $this->txtTitle->Text = '';
            $this->lstGroupTitle->SelectedValue = null;
            $this->lstGroupTitle->refresh();

            $this->enableInputs();
            $this->dtgGallery->removeCssClass('disabled');
        }

        /**
         * Handles the click event for the locked cancel button. This method manages the visibility of UI components
         * and modifies the state of particular controls based on the gallery settings.
         *
         * @param ActionParams $params Parameters associated with the action event.
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

            if (GallerySettings::countAll() !== 1) {
                    Application::executeJavaScript("
                        $('.move-button-js').removeClass('hidden');
                        $('.move-items-js').addClass('hidden');
                        $('.new-item-js').addClass('hidden');
                    ");
            } else {
                Application::executeJavaScript("
                    $('.move-button-js').addClass('hidden');
                    $('.move-items-js').addClass('hidden');
                    $('.new-item-js').addClass('hidden');
                ");
            }

            $this->btnMove->Enabled = true;
            $this->btnAddAlbum->Enabled = true;

            $this->lstGalleryLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->lstGalleryLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->enableInputs();
            $this->dtgGallery->removeCssClass('disabled');
        }

        /**
         * Handles the back button click event by redirecting to the menu manager page.
         *
         * @param ActionParams $params The parameters associated with the button click event.
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
         * Creates a gallery directory structure based on the given title and optional relative path.
         *
         * @param string $title The title of the gallery, which will be sanitized for URL usage.
         * @param null|string $relativePath An optional relative path to prepend to the gallery directory.
         *
         * @return void
         */
        protected function makeGallery(string $title, ?string $relativePath = null): void
        {
            if ($relativePath) {
                $fullPath = $this->strRootPath . '/' . QString::sanitizeForUrl($relativePath) . '/' . QString::sanitizeForUrl(trim($title));
            } else {
                $fullPath = $this->strRootPath . '/' . QString::sanitizeForUrl(trim($title));
            }

            $relativePath = $this->getRelativePath($fullPath);

            if ($this->strRootPath) {
                Folder::makeDirectory($fullPath, 0777);
            }

            foreach ($this->tempFolders as $tempFolder) {
                $tempPath = $this->strTempPath . '/_files/' . $tempFolder . $relativePath;
                Folder::makeDirectory($tempPath, 0777);
            }
        }

        /**
         * Updates the name of a gallery folder and ensures the new name is unique within the specified destination.
         * It also renames any associated temporary folders to match the new name.
         *
         * @param string $destination The intended destination path for the gallery folder.
         * @param string $currentTitleSlug The current slug of the gallery folder that needs to be updated.
         * @param string $newFolderName The desired new name for the gallery folder.
         *
         * @return void
         */
        public function updateGalleryFolder(string $destination, string $currentTitleSlug, string $newFolderName): void
        {
            $basePath = $this->strRootPath . '/' . QString::sanitizeForUrl($destination);

            $currentFolderPath = $this->strRootPath . $currentTitleSlug;
            $newFolderPath = $basePath . '/' . $newFolderName;

            // Check if the new folder name is unique
            $uniqueNewFolderName = $this->generateUniqueFolderName(QString::sanitizeForUrl($newFolderName), $basePath);
            $uniqueNewFolderPath = $basePath . '/' . $uniqueNewFolderName;

            // Update folder name
            rename($currentFolderPath, $uniqueNewFolderPath);

            // Update temp folders name
            foreach ($this->tempFolders as $tempFolder) {
                $currentTempPath = QString::sanitizeForUrl($destination) . '/' . basename($currentFolderPath);
                $newTempPath = QString::sanitizeForUrl($destination) . '/' . basename($uniqueNewFolderPath);

                $currentTempFolderPath = $this->strTempPath . '/_files/' . $tempFolder . '/' . $currentTempPath;
                $newTempFolderPath = $this->strTempPath . '/_files/' . $tempFolder . '/' . $newTempPath;

                rename($currentTempFolderPath, $newTempFolderPath);
            }
        }

        /**
         * Moves the contents from the source directory to the destination directory.
         *
         * This method copies all files and directories from the source to the destination
         * and then removes the original source directory and its contents.
         *
         * @param string $src The path of the source directory to move.
         * @param string $dst The path of the destination directory where the contents should be moved.
         *
         * @return void
         * @throws Caller
         */
        protected function fullMove(string $src, string $dst): void
        {
            $this->fullCopy($src, $dst);
            $this->fullRemove($src);
        }

        /**
         * Performs a full copy of a source file or directory to a destination, handling unique naming for conflicts
         * and additional processing for temporary files and directories.
         *
         * @param string $src The source path, which can be a file or a directory.
         * @param string $dst The destination path where the source should be copied.
         *
         * @return void
         */
        protected function fullCopy(string $src, string $dst): void
        {
            $dirname = $this->removeFileName($dst);
            $name = pathinfo($dst, PATHINFO_FILENAME);
            $ext = pathinfo($dst, PATHINFO_EXTENSION);

            if (is_dir($src)) {
                // Let's check if the folder already exists
                if (file_exists($dirname . '/' . basename($name))) {
                    $inc = 1;
                    while (file_exists($dirname . '/' . $name . '-' . $inc)) {
                        $inc++;
                    }
                    $dst = $dirname . '/' . $name . '-' . $inc; // We use a unique name
                }

                Folder::makeDirectory($dst, 0777);

                foreach ($this->tempFolders as $tempFolder) {
                    Folder::makeDirectory($this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($dst), 0777);
                }

                $files = array_diff(scandir($src), array('..', '.'));
                foreach ($files as $file) {
                    // Recursive copying
                    $this->fullCopy("$src/$file", "$dst/$file");
                }

            } else if (file_exists($src)) {
                // If the file already exists, we add a unique name
                if (file_exists($dirname . '/' . basename($name) . '.' . $ext)) {
                    $inc = 1;
                    while (file_exists($dirname . '/' . $name . '-' . $inc . '.' . $ext)) {
                        $inc++;
                    }
                    $dst = $dirname . '/' . $name . '-' . $inc . '.' . $ext; // We use a unique name
                }

                copy($src, $dst);

                // Strategy for copying temp files
                if (in_array(strtolower($ext), $this->arrAllowed)) {
                    foreach ($this->tempFolders as $tempFolder) {
                        copy(
                            $this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($src),
                            $this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($dst)
                        );
                    }
                }
            }
        }

        /**
         * Recursively removes a directory or file and its associated temporary directories or files.
         *
         * @param string $dir The directory or file path to be removed.
         *
         * @return void
         * @throws Caller
         */
        protected function fullRemove(string $dir): void
        {
            $objFolders = Folders::loadAll();
            $objFiles = Files::loadAll();

            if (is_dir($dir)) {
                $files = array_diff(scandir($dir), array('..', '.'));

                foreach ($files as $file) {
                    $this->fullRemove($dir . "/" . $file);
                }

                if (file_exists($dir)) {
                    rmdir($dir);

                    foreach ($this->tempFolders as $tempFolder) {
                        $tempPath = $this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($dir);
                        if (is_dir($tempPath)) {
                            rmdir($tempPath);
                        }
                    }
                }
            } elseif (file_exists($dir)) {
                unlink($dir);

                foreach ($this->tempFolders as $tempFolder) {
                    $tempPath = $this->strTempPath . '/_files/' . $tempFolder . $this->getRelativePath($dir);
                    if (is_file($tempPath)) {
                        unlink($tempPath);
                    }
                }
            }

            $dirname = dirname($dir);
            if (is_dir($dirname)) {
                $folders = glob($dirname . '/*', GLOB_ONLYDIR);
                $files = array_filter(glob($dirname . '/*'), 'is_file');
            }
        }

        /**
         * Generates a unique folder name by appending an incremental index if the base folder name already exists in the specified path.
         *
         * @param string $baseFolderName The desired base folder name.
         * @param string $path The directory path where the folder will be created.
         *
         * @return string A unique folder name that does not conflict with existing folders in the specified path.
         */
        protected function generateUniqueFolderName(string $baseFolderName, string $path): string
        {
            // Download only folder names from the given directory
            $existingFolders = array_map('basename', glob($path . '/*', GLOB_ONLYDIR));

            // If the original name does not exist, return it directly
            if (!in_array($baseFolderName, $existingFolders)) {
                return $baseFolderName;
            }

            $uniqueFolderName = $baseFolderName;
            $inc = 1;

            // Add an index until a unique name is found
            while (in_array($uniqueFolderName, $existingFolders)) {
                $uniqueFolderName = $baseFolderName . '-' . $inc;
                $inc++;
            }

            return $uniqueFolderName;
        }

        /**
         * Retrieves the ID of a folder by comparing the given path with the paths of all loaded folders.
         *
         * @param string $path The absolute path for which to find the corresponding folder ID.
         *
         * @return int|null The ID of the folder if a match is found; 1 if the path is empty; null if no match is found.
         * @throws Caller
         */
        private function getIdFromParent(string $path): ?int
        {
            $objFolders = Folders::loadAll();
            $objPath = $this->getRelativePath(realpath(dirname($path)));

            foreach ($objFolders as $objFolder) {
                if ($objPath == $objFolder->getPath()) {
                    return $objFolder->getId();
                }
            }

            // Handle the case where no matching folder is found.
            return ($objPath == "") ? 1 : null;
        }

        /**
         * Calculates the relative path from the given absolute path by removing the root path prefix.
         *
         * @param string $path The absolute path from which the relative path is to be calculated.
         *
         * @return string The relative path with the root path prefix removed.
         */
        protected function getRelativePath(string $path): string
        {
            return substr($path, strlen($this->strRootPath));
        }

        /**
         * Removes the file name from a given file path, returning the directory path.
         *
         * @param string $path The full file path from which to remove the file name.
         *
         * @return string The directory path without the file name.
         */
        public function removeFileName(string $path): string
        {
            return substr($path, 0, (int)strrpos($path, '/'));
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the gallery data table.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgGallery_Create(): void
        {
            $this->dtgGallery = new GalleryTable($this);
            $this->dtgGallery_CreateColumns();
            $this->createPaginators();
            $this->dtgGallery_MakeEditable();
            $this->dtgGallery->RowParamsCallback = [$this, "dtgGallery_GetRowParams"];
            $this->dtgGallery->SortColumnIndex = 3;
            //$this->dtgGallery->SortDirection = -1;
            $this->dtgGallery->UseAjax = true;
            $this->dtgGallery->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
        }

        /**
         * Initializes the creation of columns for the dtgGallery object.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function dtgGallery_CreateColumns(): void
        {
            $this->dtgGallery->createColumns();
        }

        /**
         * Configures the gallery data table to be editable by adding a cell click action
         * and applying specific CSS classes for interactivity and styling.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgGallery_MakeEditable(): void
        {
            $this->dtgGallery->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgGalleryRow_Click'));
            $this->dtgGallery->addCssClass('clickable-rows');
            $this->dtgGallery->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event on a gallery row, retrieving the gallery list
         * based on the given action parameter and then redirects to the edit page
         * for the selected gallery.
         *
         * @param ActionParams $params The parameters associated with the action,
         *        primarily containing the action parameter which is the identifier
         *        for the gallery.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         * @throws Throwable
         */
        protected function dtgGalleryRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intId = intval($params->ActionParameter);
            $objGalleryList = GalleryList::load($intId);

            Application::redirect('album_edit.php?id=' . $objGalleryList->getId() . '&group=' . $objGalleryList->getGalleryGroupTitleId() . '&folder=' . $objGalleryList->getFolderId());
        }

        /**
         * Retrieves parameters for a gallery table row.
         *
         * @param object $objRowObject The row object containing data for the row.
         * @param int $intRowIndex The index of the row in the table.
         *
         * @return array An associative array of parameters for the row, including a data-value keyed by the primary key of the row object.
         */
        public function dtgGallery_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Initializes and configures paginators for the gallery data grid.
         * This method sets up primary and alternate paginators with labels for navigation.
         * It also configures the data grid to display a specific number of items per a page.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgGallery->Paginator = new Bs\Paginator($this);
            $this->dtgGallery->Paginator->LabelForPrevious = t('Previous');
            $this->dtgGallery->Paginator->LabelForNext = t('Next');

            $this->dtgGallery->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgGallery->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgGallery->PaginatorAlternate->LabelForNext = t('Next');

            $this->dtgGallery->ItemsPerPage = 10;

            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the Select2 dropdown control for selecting items per a page.
         *
         * The control is set with a custom theme, width, and allows a single selection mode.
         * It is populated with items and is linked to an Ajax action triggered on change.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
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
         * Retrieves a list of items per a page associated with the assigned user object.
         *
         * @return ListItem[] Array of ListItem objects, each representing an item per page associated with the assigned user object. If an item is associated with the current user, it will be marked as selected.
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
         * Updates the number of items per a page in the gallery based on the selected value from the dropdown list.
         *
         * @param ActionParams $params The parameters passed from the action triggering this change.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgGallery->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgGallery->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a search filter input. This includes setting HTML attributes,
         * placeholders, and styles related to the search functionality. Additional filter actions
         * are also applied.
         *
         * @return void
         * @throws Caller
         */
        protected function createFilter(): void
        {
            $this->txtFilter = new Bs\TextBox($this);
            $this->txtFilter->Placeholder = t('Search...');
            $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
            $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
            $this->txtFilter->addCssClass('search-box');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lstGroups = new Q\Plugin\Select2($this);
            $this->lstGroups->MinimumResultsForSearch = -1;
            $this->lstGroups->Theme = 'web-vauu';
            $this->lstGroups->Width = '100%';
            $this->lstGroups->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstGroups->addItem(t('- Select a gallery group -'), null, true);

            $objGroups = GallerySettings::queryArray(
                QQ::all(),
                [
                    QQ::orderBy(QQ::notEqual(QQN::GallerySettings()->AlbumsLocked, 0), QQN::GallerySettings()->Id)
                ]
            );

            $countByAlbumsLocked = GallerySettings::countByAlbumsLocked(1);

            foreach ($objGroups as $objTitle) {
                if ($countByAlbumsLocked> 1 && $objTitle->AlbumsLocked === 1) {
                    $this->lstGroups->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            $this->lstGroups->addAction(new Change(), new AjaxControl($this,'lstGroups_Change'));
            $this->lstGroups->setCssStyle('float', 'left');

            $this->btnClearFilters = new Bs\Button($this);
            $this->btnClearFilters->Text = t('Clear filters');
            $this->btnClearFilters->addWrapperCssClass('center-button');
            $this->btnClearFilters->CssClass = 'btn btn-default';
            $this->btnClearFilters->setCssStyle('float', 'left');
            $this->btnClearFilters->CausesValidation = false;
            $this->btnClearFilters->addAction(new Click(), new AjaxControl($this, 'clearFilters_Click'));

            $this->updateLockStatus();
            $this->addFilterActions();
        }

        /**
         * Updates the lock status in the gallery system and adjusts the UI accordingly.
         * If there are multiple albums locked, it displays the group UI by removing the hidden class.
         * Otherwise, it hides the group UI by adding the hidden class.
         * Finally, it refreshes the group list to reflect the current state.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function updateLockStatus(): void
        {
            $countByAlbumsLocked = GallerySettings::countByAlbumsLocked(1);

            if ($countByAlbumsLocked > 1) {
                Application::executeJavaScript("$('.js-groups').removeClass('hidden');");
                $this->changeColClass = 'col-md-3';
            } else {
                Application::executeJavaScript("$('.js-groups').addClass('hidden');");
                $this->changeColClass = 'col-md-6';
            }

            $this->lstGroups->refresh();
        }

        /**
         * Disables various input controls and resets their values.
         *
         * @return void
         */
        protected function disableInputs(): void
        {
            $this->lstItemsPerPageByAssignedUserObject->Enabled = false;
            $this->lstItemsPerPageByAssignedUserObject->refresh();

            $this->txtFilter->Text = '';
            $this->txtFilter->Enabled = false;
            $this->txtFilter->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->Enabled = false;
            $this->lstGroups->refresh();

            $this->btnClearFilters->Enabled = false;
            $this->btnClearFilters->refresh();

            $this->dtgGallery->refresh();
        }

        /**
         * Enables a series of input controls and clears their current values as well as refreshes their state.
         *
         * @return void
         */
        protected function enableInputs(): void
        {
            $this->lstItemsPerPageByAssignedUserObject->Enabled = true;
            $this->lstItemsPerPageByAssignedUserObject->refresh();

            $this->txtFilter->Text = '';
            $this->txtFilter->Enabled = true;
            $this->txtFilter->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->Enabled = true;
            $this->lstGroups->refresh();

            $this->btnClearFilters->Enabled = true;
            $this->btnClearFilters->refresh();

            $this->dtgGallery->refresh();
        }

        /**
         * Handles the change event for the "lstGroups" control.
         *
         * @param ActionParams $params The parameters associated with the change action.
         *
         * @return void
         */
        protected function lstGroups_Change(ActionParams $params): void
        {
            $this->dtgGallery->refresh();
        }

        /**
         * Clears all applied filters on the filter UI and refreshes the associated components.
         *
         * @param ActionParams $params Parameters associated with the action triggering the method.
         *
         * @return void This method does not return a value.
         */
        protected function clearFilters_Click(ActionParams $params): void
        {
            $this->txtFilter->Text = '';
            $this->txtFilter->refresh();

            $this->lstGroups->SelectedValue = null;
            $this->lstGroups->refresh();

            $this->dtgGallery->refresh();
        }

        /**
         * Adds filter actions to the txtFilter control. This method sets up actions
         * to be performed when the user interacts with the filter input. Specifically,
         * it adds actions for input events with a given delay and enters key events.
         * For input events, it triggers an AJAX control. To enter key events, it
         * triggers an AJAX control followed by a terminate action.
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
         * Refreshes the gallery data table when a filter is changed.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgGallery->refresh();
        }

        /**
         * Binds data to the gallery based on the supplied condition.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgGallery->bindData($objCondition);
        }

        /**
         * Constructs and returns a condition object based on a selected group and text filter.
         *
         * @return All|AndCondition|OrCondition Returns a condition object that filters results based on the selected group or text fields. Returns an `All` condition if no filters are applied, an `AndCondition` if multiple filters are combined, or an `OrCondition` for text-based field matches.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function getCondition(): All|AndCondition|OrCondition
        {
            $strText = trim($this->txtFilter->Text ?? '');
            $intGroupId = $this->lstGroups->SelectedValue; // ID value

            $condList = [];

            // If a group selected
            if (!empty($intGroupId)) {
                $condList[] = QQ::equal(QQN::GalleryList()->GalleryGroupTitleId, $intGroupId); // or the correct field that you have binding
            }

            // If a text is entered
            if ($strText !== '') {
                // Do one big 'or' for multiple fields in the text
                $orText = QQ::orCondition(
                    QQ::like(QQN::GalleryList()->GroupTitle, "%" . $strText . "%"),
                    QQ::like(QQN::GalleryList()->Title, "%" . $strText . "%"),
                    QQ::like(QQN::GalleryList()->Author, "%" . $strText . "%"),
                );
                $condList[] = $orText;
            }

            // If neither filter is present, return all
            if (count($condList) === 0) {
                return QQ::all();
            }

            // If both conditions are met, combine with AND
            return QQ::andCondition(...$condList);
        }
    }