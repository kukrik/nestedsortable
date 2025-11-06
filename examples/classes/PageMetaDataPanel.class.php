<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Event\Click;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Exception\Caller;
    use QCubed\QDateTime;
    use Random\RandomException;
    use QCubed\Project\Application;

    /**
     * The PageMetadataPanel class provides an interface to edit and manage metadata
     * for a webpage, including keywords, description, and author information. The panel
     * includes input fields, labels, and buttons to save, delete, or cancel changes.
     */
    class PageMetadataPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;

        protected Q\Plugin\Toastr $dlgToastr;

        protected Q\Plugin\Control\Alert $lblInfo;

        public Q\Plugin\Control\Label $lblKeywords;
        public Bs\TextBox $txtKeywords;

        public Q\Plugin\Control\Label $lblDescription;
        public Bs\TextBox $txtDescription;

        public Q\Plugin\Control\Label $lblAuthor;
        public Bs\TextBox $txtAuthor;

        public Bs\Button $btnSave;
        public Bs\Button $btnSaving;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected string $strSaveButtonId;
        protected string $strSavingButtonId;

        protected ?object $objMenuContent = null;
        protected ?object $objArticle = null;
        protected ?object $objMetadata = null;

        protected int $intId;
        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'PageMetaDataPanel.tpl.php';

        /**
         * Constructor for initializing the object and setting up the necessary components.
         *
         * @param mixed $objParentObject The parent object, typically a form or control, to which this control is being added.
         * @param string|null $strControlId An optional control ID for uniquely identifying this control. If not provided, a unique ID will be generated.
         *
         * @return void
         * @throws Caller Thrown when there is an error during the construction of the object.
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->intId = Application::instance()->context()->queryStringItem('id');
            $this->objMetadata = Metadata::loadByIdFromMetadata($this->intId);
            $this->objArticle = Article::loadByIdFromContentId($this->intId);
            $this->objMenuContent = MenuContent::load($this->intId);

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must save something here $this->objArticle->setUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session
            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

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
         * Initializes and configures the input fields and labels for metadata entry.
         * This includes fields for keywords, description, and author metadata,
         * as well as displaying informational alerts and handling input events.
         *
         * @return void
         * @throws Caller
         */
        public function createInputs(): void
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
            $this->txtKeywords->TextMode = TextBoxBase::MULTI_LINE;
            $this->txtKeywords->Rows = 3;
            $this->txtKeywords->addWrapperCssClass('center-button');
            $this->txtKeywords->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtKeywords->addAction(new EnterKey(), new Q\Action\Terminate());
            $this->txtKeywords->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtKeywords->addAction(new EscapeKey(), new Q\Action\Terminate());

            $this->lblDescription = new Q\Plugin\Control\Label($this);
            $this->lblDescription->Text = t('Description of the metadata');
            $this->lblDescription->addCssClass('col-md-3');
            $this->lblDescription->setCssStyle('font-weight', 400);

            $this->txtDescription = new Bs\TextBox($this);
            $this->txtDescription->Text = $this->objMetadata->Description ?? null;
            $this->txtDescription->TextMode = TextBoxBase::MULTI_LINE;
            $this->txtDescription->Rows = 3;
            $this->txtDescription->addWrapperCssClass('center-button');
            $this->txtDescription->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtDescription->addAction(new EnterKey(), new Q\Action\Terminate());
            $this->txtDescription->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtDescription->addAction(new EscapeKey(), new Q\Action\Terminate());

            $this->lblAuthor = new Q\Plugin\Control\Label($this);
            $this->lblAuthor->Text = t('Author');
            $this->lblAuthor->addCssClass('col-md-3');
            $this->lblAuthor->setCssStyle('font-weight', 400);

            $this->txtAuthor = new Bs\TextBox($this);
            $this->txtAuthor->Text = $this->objMetadata->Author ?? null;
            $this->txtAuthor->addWrapperCssClass('center-button');
            $this->txtAuthor->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtAuthor->addAction(new EnterKey(), new Q\Action\Terminate());
            $this->txtAuthor->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtAuthor->addAction(new EscapeKey(), new Q\Action\Terminate());
        }

        /**
         * Initializes and creates buttons for Save, Save and Close, Delete, and Cancel actions.
         * Each button is set with specific properties such as text, CSS classes, and actions triggered by clicking
         * events. The Save and Save and Close buttons are prepared for fast transmission with their respective IDs.
         *
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnMenuSave_Click'));
            // The variable below is being prepared for fast transmission
            $this->strSaveButtonId = $this->btnSave->ControlId;

            $this->btnSaving = new Bs\Button($this);
            $this->btnSaving->Text = t('Save and close');
            $this->btnSaving->CssClass = 'btn btn-darkblue';
            $this->btnSaving->addWrapperCssClass('center-button');
            $this->btnSaving->PrimaryButton = true;
            $this->btnSaving->addAction(new Click(), new AjaxControl($this, 'btnMenuSaveClose_Click'));
            // The variable below is being prepared for fast transmission
            $this->strSavingButtonId = $this->btnSaving->ControlId;

            $this->btnDelete = new Bs\Button($this);
            $this->btnDelete->Text = t('Delete');
            $this->btnDelete->CssClass = 'btn btn-danger';
            $this->btnDelete->addWrapperCssClass('center-button');
            $this->btnDelete->CausesValidation = false;
            $this->btnDelete->addAction(new Click(), new AjaxControl($this, 'btnMenuDelete_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this,'btnMenuCancel_Click'));
        }

        /**
         * Creates a Toastr dialog object and sets its properties for displaying notifications.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr = new Q\Plugin\Toastr($this);
            $this->dlgToastr->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
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
         * @throws Caller
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to delete the specific metadata of this page?</p>
                            <p style="line-height: 25px; margin-bottom: -3px;">If desired, you can later re-write!</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), t('This menu metadata has been permanently deleted.'), false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addCloseButton(t("I'll cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'deletedItem_Click'));

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The metadata for this article cannot be saved here at the moment.</p>
                                        <p style="line-height: 25px; margin-bottom: -3px;">You need to add content to the article and save it first.</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
            $this->dlgModal2->addAction(new DialogButton(), new AjaxControl($this, 'recallItem_Click'));

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
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function btnMenuSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal3->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->renderActionsWithOrWithoutId();

            if ($this->objMenuContent->getContentType() == 2) {
                if ($this->objArticle->getTitle()) {
                    $this->objArticle->setPostUpdateDate(QDateTime::now());
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
                Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strSave_translate');");
                Application::executeJavaScript("jQuery($this->strSavingButtonId).text('$strSaveAndClose_translate');");
            } else {
                $strUpdate_translate = t('Update');
                $strUpdateAndClose_translate = t('Update and close');
                Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strUpdate_translate');");
                Application::executeJavaScript("jQuery($this->strSavingButtonId).text('$strUpdateAndClose_translate');");
            }

            $this->userOptions();
        }

        /**
         * Handles the click event for the "Save and Close" button in the menu.
         * The method validates and saves the article data, updates metadata, and redirects to the list page upon
         * successful operations.
         *
         * @param ActionParams $params The parameters from the action triggering this method, typically containing
         *     context or state information relevant to the action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws RandomException
         * @throws Throwable
         */
        public function btnMenuSaveClose_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal3->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->renderActionsWithOrWithoutId();

            if ($this->objMenuContent->getContentType() == 2) {
                if ($this->objArticle->getTitle()) {
                    $this->objArticle->setPostUpdateDate(QDateTime::now());
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

            $this->userOptions();
            $this->redirectToListPage();
        }

        /**
         * Handles the button click event for deleting a menu item.
         *
         * @param ActionParams $params The parameters passed to the action, containing context-specific information about the event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function btnMenuDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal3->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

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
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function deletedItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal3->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->objMetadata->setKeywords(null);
            $this->objMetadata->setDescription(null);
            $this->objMetadata->setAuthor(null);
            $this->objMetadata->save();

            $this->txtKeywords->Text = '';
            $this->txtDescription->Text = '';
            $this->txtAuthor->Text = '';

            $strSave_translate = t('Save');
            $strSaveAndClose_translate = t('Save and close');
            Application::executeJavaScript("jQuery($this->strSaveButtonId).text('$strSave_translate');");
            Application::executeJavaScript("jQuery($this->strSavingButtonId).text('$strSaveAndClose_translate');");

            $this->dlgModal1->hideDialogBox();
        }

        /**
         * Resets the text fields for keywords, description, and author to empty strings
         * and hides the dialog box.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws RandomException
         * @throws Caller
         */
        public function recallItem_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal3->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

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
         * post-update date.
         * If the identifier is absent and the content type equals 2, it sets the post-update
         * date to null.
         *
         * @return void
         * @throws Caller
         */
        public function renderActionsWithOrWithoutId(): void
        {
            if (strlen($this->intId)) {
                if ($this->txtKeywords->Text !== $this->objMetadata->getKeywords() ||
                    $this->txtDescription->Text !== $this->objMetadata->getDescription() ||
                    $this->txtAuthor->Text !== $this->objMetadata->getAuthor()
                ) {
                    // $this->objArticle->setAssignedEditorsNameById($_SESSION['logged_user_id'])); // Approximately example here etc...
                    // For example, John Doe is a logged user with his session
                    if ($this->objMenuContent->getContentType() == 2) {
                        $this->objArticle->setAssignedEditorsNameById(2);
                        $this->objArticle->setPostUpdateDate(QDateTime::now());
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
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnMenuCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal3->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->userOptions();

            $this->redirectToListPage();
        }

        /**
         * Redirects the user to the menu edit page using the current object's ID.
         *
         * @return void
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            Application::redirect('menu_edit.php?id=' . $this->intId);
        }

    }