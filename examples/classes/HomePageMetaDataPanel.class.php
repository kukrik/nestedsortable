<?php

    use QCubed as Q;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\Panel;
    use QCubed\Event\Click;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Exception\Caller;
    use Random\RandomException;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;

    /**
     * Class HomePageMetadataPanel
     *
     * Represents a control panel for managing homepage metadata, including functionality
     * for updating keywords, descriptions, and authors. The panel provides UI elements
     * such as input fields, alerts, modals, toaster notifications, and action buttons
     * for saving, deleting, and cancelling operations. It leverages Bootstrap styles
     * and QCubed features for AJAX interactivity.
     */
    class HomePageMetadataPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        protected Q\Plugin\Toastr $dlgToastr;

        public Q\Plugin\Control\Alert $lblKeywordsHint;
        public Q\Plugin\Control\Label $lblKeywords;
        public Bs\TextBox $txtKeywords;

        public Q\Plugin\Control\Alert $lblDescriptionHint;
        public Q\Plugin\Control\Label $lblDescription;
        public Bs\TextBox $txtDescription;

        public Q\Plugin\Control\Alert $lblAuthorHint;
        public Q\Plugin\Control\Label $lblAuthor;
        public Bs\TextBox $txtAuthor;

        public Bs\Button $btnSave;
        public Bs\Button $btnSaving;
        public Bs\Button $btnDelete;
        public Bs\Button $btnCancel;

        protected string $strSaveButtonId;
        protected string $strSavingButtonId;

        protected int $intId;
        protected object $objMetadata;

        protected string $strTemplate = 'HomePageMetaDataPanel.tpl.php';

        /**
         * Constructor method for initializing the object. Sets up metadata and creates
         * necessary UI elements such as inputs, buttons, modals, and notifications.
         *
         * @param mixed $objParentObject The parent object of the control.
         * @param string|null $strControlId An optional control ID for the created object.
         *
         * @return void
         * @throws Caller
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

            $this->createInputs();
            $this->createButtons();
            $this->createToastr();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and creates input controls for metadata management, including alerts, labels, and textboxes for keywords, descriptions, and authors.
         *
         * @return void
         * @throws Caller
         */
        public function createInputs(): void
        {
            $this->lblKeywordsHint = new Q\Plugin\Control\Alert($this);
            $this->lblKeywordsHint->Display = true;
            $this->lblKeywordsHint->Dismissable = true;
            $this->lblKeywordsHint->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblKeywordsHint->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblKeywordsHint->addCssClass('alert alert-info alert-dismissible');
            $this->lblKeywordsHint->Text = t('Separate keywords by commas and <strong>without spaces</strong>, such as: john, doe, john doe, project, projects, project writing, solutions, ideas, cooperation, application, applications, consultation, education, entrepreneurship, social affairs, culture, local funds, international funds, etc...');

            $this->lblKeywords = new Q\Plugin\Control\Label($this);
            $this->lblKeywords->Text = t('Keywords of the metadata');
            $this->lblKeywords->addCssClass('col-md-3');
            $this->lblKeywords->setCssStyle('font-weight', 400);

            $this->txtKeywords = new Bs\TextBox($this);
            $this->txtKeywords->Text = $this->objMetadata->Keywords;
            $this->txtKeywords->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtKeywords->Rows = 3;
            $this->txtKeywords->addWrapperCssClass('center-button');
            $this->txtKeywords->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtKeywords->addAction(new EnterKey(), new Terminate());
            $this->txtKeywords->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtKeywords->addAction(new EscapeKey(), new Terminate());

            $this->lblDescriptionHint = new Q\Plugin\Control\Alert($this);
            $this->lblDescriptionHint->Display = true;
            $this->lblDescriptionHint->Dismissable = true;
            $this->lblDescriptionHint->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblDescriptionHint->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblDescriptionHint->addCssClass('alert alert-info alert-dismissible');
            $this->lblDescriptionHint->Text = t('Think well of the maximum summary and good description of metadata, such as Project writer John Doe - project and application conferences, How to make marketing successful - make clear customer needs, etc...');

            $this->lblDescription = new Q\Plugin\Control\Label($this);
            $this->lblDescription->Text = t('Description of the metadata');
            $this->lblDescription->addCssClass('col-md-3');
            $this->lblDescription->setCssStyle('font-weight', 400);

            $this->txtDescription = new Bs\TextBox($this);
            $this->txtDescription->Text = $this->objMetadata->Description;
            $this->txtDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtDescription->Rows = 3;
            $this->txtDescription->addWrapperCssClass('center-button');
            $this->txtDescription->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtDescription->addAction(new EnterKey(), new Terminate());
            $this->txtDescription->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtDescription->addAction(new EscapeKey(), new Terminate());

            $this->lblAuthorHint = new Q\Plugin\Control\Alert($this);
            $this->lblAuthorHint->Display = true;
            $this->lblAuthorHint->Dismissable = true;
            $this->lblAuthorHint->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblAuthorHint->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblAuthorHint->addCssClass('alert alert-info alert-dismissible');
            $this->lblAuthorHint->Text = t('Author/authors');

            $this->lblAuthor = new Q\Plugin\Control\Label($this);
            $this->lblAuthor->Text = t('Author');
            $this->lblAuthor->addCssClass('col-md-3');
            $this->lblAuthor->setCssStyle('font-weight', 400);

            $this->txtAuthor = new Bs\TextBox($this);
            $this->txtAuthor->Text = $this->objMetadata->Author;
            $this->txtAuthor->addWrapperCssClass('center-button');
            $this->txtAuthor->AddAction(new EnterKey(), new AjaxControl($this,'btnMenuSave_Click'));
            $this->txtAuthor->addAction(new EnterKey(), new Terminate());
            $this->txtAuthor->AddAction(new EscapeKey(), new AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtAuthor->addAction(new EscapeKey(), new Terminate());
        }

        /**
         * Creates and initializes the action buttons for the user interface, specifically Save, Save and Close,
         * Delete, and Cancel buttons.
         *
         * The Save and Save and Close buttons are conditionally set to 'Update' or 'Save' based on the presence of
         * metadata attributes (keywords, description, and author). Attaches the corresponding event handlers for each
         * button, enabling actions such as saving, deleting, and cancelling operations.
         *
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnSave = new Bs\Button($this);
            if ($this->objMetadata->getKeywords() ||
                $this->objMetadata->getDescription() ||
                $this->objMetadata->getAuthor()
            ) {
                $this->btnSave->Text = t('Update');
            } else {
                $this->btnSave->Text = t('Save');
            }
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnMenuSave_Click'));
            // The variable below is being prepared for fast transmission
            $this->strSaveButtonId = $this->btnSave->ControlId;

            $this->btnSaving = new Bs\Button($this);
            if ($this->objMetadata->getKeywords() ||
                $this->objMetadata->getDescription() ||
                $this->objMetadata->getAuthor()
            ) {
                $this->btnSaving->Text = t('Update and close');
            } else {
                $this->btnSaving->Text = t('Save and close');
            }
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
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnMenuCancel_Click'));
        }

        /**
         * Initializes a Toastr notification instance and sets its properties.
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
         * Initializes and sets up a modal dialog for confirming the deletion of global metadata.
         *
         * @return void
         * @throws Caller
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to delete the global metadata of this website?</p>
                            <p style="line-height: 25px; margin-bottom: -3px;">If desired, you can later re-write!</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), t('This menu metadata has been permanently deleted.'), false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addCloseButton(t("I'll cancel"));
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'deletedItem_Click'));

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
         * Handles the event when save a button in the menu is clicked. Updates metadata
         * and saves it. Changes button text based on whether metadata fields are set. Notifies
         * the user of the action performed.
         *
         * @param ActionParams $params Parameters passed to the click event handler.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function btnMenuSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
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

            $this->dlgToastr->notify();
        }

        /**
         * Handles the click event for the Menu Save and Close button.
         * This function sets the keywords, description, and author for the metadata
         * using the values from their respective text fields, saves the metadata,
         * and then redirects the user to the list page.
         *
         * @param ActionParams $params The parameters passed to the action handler.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws RandomException
         * @throws Throwable
         */
        public function btnMenuSaveClose_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->objMetadata->setKeywords($this->txtKeywords->Text);
            $this->objMetadata->setDescription($this->txtDescription->Text);
            $this->objMetadata->setAuthor($this->txtAuthor->Text);
            $this->objMetadata->save();

            $this->redirectToListPage();
        }

        /**
         * Handles the click event for the delete menu button.
         * Checks if there are keywords, description, or author information available in the metadata.
         * If any of these properties are set, it displays a confirmation dialog box before allowing deletion.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function btnMenuDelete_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->objMetadata->getKeywords() || $this->objMetadata->getDescription() || $this->objMetadata->getAuthor()) {
                $this->dlgModal1->showDialogBox();
            }
        }

        /**
         * Handles the event when an item is deleted.
         *
         * Resets metadata fields and UI components, sets default button text,
         * and hides the dialog modal.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        public function deletedItem_Click(ActionParams $params): void
        {
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
         * Handles the click event for the Menu Cancel button.
         *
         * This method is triggered when the user clicks the cancel button in the menu.
         * It redirects the user to the list page.
         *
         * @param ActionParams $params The parameters associated with the action event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnMenuCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->redirectToListPage();
        }

        /**
         * Redirects the user to the list page with the current object's ID appended to the URL.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function redirectToListPage(): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::redirect('home-menu_edit.php?id=' . $this->intId);
        }
    }