<?php

    use QCubed as Q;
    use QCubed\Action\ActionParams;
    use QCubed\Action\Ajax;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\ListItem;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Event\Change;
    use QCubed\Event\Click;
    use QCubed\Event\DialogButton;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\QDateTime;
    use QCubed\QString;
    use QCubed\Query\QQ;

    /**
     * Represents a panel for displaying an overview of site information,
     * including details about the PHP environment, database, server configurations,
     * and supported features. This class also provides details about disk space usage and allocation.
     */
    class SitePrivacyPanel extends Panel
    {
        protected ?object $objSiteOptions = null;
        protected ?object $objFrontendLink = null;

        protected Bs\Modal $dlgModal1;
        protected Bs\Modal $dlgModal2;
        protected Bs\Modal $dlgModal3;
        protected Bs\Modal $dlgModal4;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;

        protected Q\Plugin\Control\Label $lblPrivacyPolicyTitle;
        protected Bs\TextBox $txtPrivacyPolicyTitle;
        protected Q\Plugin\Control\Label $lblGoogleAnalyticsCode;
        protected Bs\TextBox $txtGoogleAnalyticsCode;



        protected Q\Plugin\Control\Label $lblPrivacyPolicyLink;
        protected Bs\Button $btnDocumentLink;
        protected Q\Plugin\Control\Label $txtDocumentLink;
        protected Q\Plugin\Control\Label $lblPrivacyFileName;
        protected Bs\Button $btnDownloadSave;
        protected Bs\Button $btnDownloadCancel;
        protected Bs\Button $btnDocumentCheck;
        protected Bs\Button $btnDocumentDelete;
        protected Q\Plugin\Control\Label $lblPrivacyPolicy;
        protected Bs\Button $btnShow;
        protected Bs\Button $btnHide;
        protected Q\Plugin\CKEditor $txtContent;




        protected Bs\Button $btnSave;
        protected Bs\Button $btnCancel;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'SitePrivacyPanel.tpl.php';

        /**
         * Constructor method for initializing the object. Sets up metadata and creates
         * necessary UI elements such as inputs, buttons, modals, and notifications.
         *
         * @param mixed $objParentObject The parent object of the control.
         * @param string|null $strControlId An optional control ID for the created object.
         *
         * @return void
         * @throws \DateMalformedStringException
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

            /**
             * NOTE: if the user_id is stored in session (e.g., if a User is logged in), as well, for example,
             * checking against user session etc.
             *
             * Must save something here $this->objNews->setUserId(logged user session);
             * or something similar...
             *
             * Options to do this are left to the developer.
             **/

            // $this->intLoggedUserId = $_SESSION['logged_user_id']; // Approximately example here etc...
            // For example, John Doe is a logged user with his session
            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->objSiteOptions = SiteOptions::load(1);
            $this->objFrontendLink = FrontendLinks::load(3);

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
            $this->createToastr();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Updates the user's last active time and synchronizes portlet data such as counters, totals, and timestamps.
         *
         * @return void
         * @throws Caller
         */
        private function userOptions(): void
        {
            $this->objUser->setLastActive(QDateTime::now());
            $this->objUser->save();
        }

        /**
         * Creates and initializes input controls and their corresponding labels for configuring
         * various site settings, including general and advanced settings.
         *
         * This method handles multiple inputs:
         * - Labels for organizing sections like "General Settings" and "Advanced Settings."
         * - Input controls for site name, site name abbreviation, default language, and allowed languages.
         * - Integration of specific styles, placeholders, required states, and validation rules.
         * - Actions for keys such as Enter and Escape to trigger specific methods for submitting or cancelling.
         *
         * The following specific configurations are included within this setup:
         * - TextBox controls for entering site name and site name abbreviation, with focus and XSS protection.
         * - Select2 dropdowns for selecting the default language or multiple allowed site languages, styled and populated dynamically.
         * - Labels styled for each setting, with optional requirements indicated.
         *
         * @return void
         * @throws \DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->lblGoogleAnalyticsCode = new Q\Plugin\Control\Label($this);
            $this->lblGoogleAnalyticsCode->Text = t('Google Analytics code');
            $this->lblGoogleAnalyticsCode->addCssClass('col-md-3');
            $this->lblGoogleAnalyticsCode->setCssStyle('font-weight', '400');

            $this->txtGoogleAnalyticsCode = new Bs\TextBox($this);
            $this->txtGoogleAnalyticsCode->Placeholder = t('Google Analytics code');
            $this->txtGoogleAnalyticsCode->Text = $this->objSiteOptions->GoogleAnalyticsCode ?? null;
            $this->txtGoogleAnalyticsCode->TextMode = TextBoxBase::MULTI_LINE;
            $this->txtGoogleAnalyticsCode->Rows = 5;
            $this->txtGoogleAnalyticsCode->Width = '100%';
            $this->txtGoogleAnalyticsCode->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtGoogleAnalyticsCode->addAction(new EnterKey(), new Terminate());
            $this->txtGoogleAnalyticsCode->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtGoogleAnalyticsCode->addAction(new EscapeKey(), new Terminate());


            $this->lblPrivacyPolicyTitle = new Q\Plugin\Control\Label($this);
            $this->lblPrivacyPolicyTitle->Text = t('Privacy policy title');
            $this->lblPrivacyPolicyTitle->addCssClass('col-md-3');
            $this->lblPrivacyPolicyTitle->setCssStyle('font-weight', '400');

            $this->txtPrivacyPolicyTitle = new Bs\TextBox($this);
            $this->txtPrivacyPolicyTitle->Placeholder = t('Privacy policy title');
            $this->txtPrivacyPolicyTitle->Text = $this->objSiteOptions->PrivacyPolicyTitle ?? null;
            $this->txtPrivacyPolicyTitle->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtPrivacyPolicyTitle->Width = '100%';
            $this->txtPrivacyPolicyTitle->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtPrivacyPolicyTitle->addAction(new EnterKey(), new Terminate());
            $this->txtPrivacyPolicyTitle->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtPrivacyPolicyTitle->addAction(new EscapeKey(), new Terminate());

            $this->lblPrivacyPolicyLink = new Q\Plugin\Control\Label($this);
            $this->lblPrivacyPolicyLink->Text = t(' Privacy policy Link');
            $this->lblPrivacyPolicyLink->addCssClass('col-md-3');
            $this->lblPrivacyPolicyLink->setCssStyle('font-weight', '400');

            $this->txtDocumentLink = new Q\Plugin\Control\Label($this);
            $this->txtDocumentLink->Text = t('Document not available...');
            $this->txtDocumentLink->setCssStyle('color', '#999');
            $this->txtDocumentLink->setCssStyle('float', 'left');
            $this->txtDocumentLink->setCssStyle('margin-left', '15px');

            $this->lblPrivacyFileName = new Q\Plugin\Control\Label($this);
            $this->lblPrivacyFileName->setCssStyle('font-weight', '400');
            $this->lblPrivacyFileName->setDataAttribute('open', 'true');

            $this->lblPrivacyPolicy = new Q\Plugin\Control\Label($this);
            $this->lblPrivacyPolicy->Text = t('Privacy policy content');
            $this->lblPrivacyPolicy->addCssClass('col-md-3');
            $this->lblPrivacyPolicy->setCssStyle('font-weight', '400');

            $this->txtContent = new Q\Plugin\CKEditor($this);
            $this->txtContent->Text = $this->objSiteOptions->PrivacyPolicy ?? null;
            $this->txtContent->Configuration = 'ckConfig';

            if ($this->objSiteOptions->ShowHide) {
                $this->txtContent->Display = true;
            } else {
                $this->txtContent->Display = false;
            }
        }

        /**
         * Creates and configures the Save and Cancel buttons for the interface.
         *
         * @return void
         * @throws Caller
         */
        protected function createButtons(): void
        {
            $this->btnDocumentLink = new Bs\Button($this);
            $this->btnDocumentLink->Text = t('Search file...');
            $this->btnDocumentLink->CssClass = 'btn btn-default';
            $this->btnDocumentLink->addWrapperCssClass('center-button');
            $this->btnDocumentLink->setCssStyle('float', 'left');
            $this->btnDocumentLink->CausesValidation = false;
            $this->btnDocumentLink->addAction(new Click(), new AjaxControl($this, 'btnDocumentLink_Click'));

            $this->btnDownloadSave = new Bs\Button($this);
            $this->btnDownloadSave->Text = t('Save');
            $this->btnDownloadSave->CssClass = 'btn btn-orange';
            $this->btnDownloadSave->setCssStyle('float', 'right');
            $this->btnDownloadSave->setCssStyle('margin-left', '10px');
            $this->btnDownloadSave->CausesValidation = false;
            $this->btnDownloadSave->Display = false;
            $this->btnDownloadSave->addAction(new Click(), new AjaxControl($this, 'btnDownloadSave_Click'));

            $this->btnDownloadCancel = new Bs\Button($this);
            $this->btnDownloadCancel->Text = t('Cancel');
            $this->btnDownloadCancel->CssClass = 'btn btn-default';
            $this->btnDownloadCancel->setCssStyle('float', 'right');
            $this->btnDownloadCancel->setCssStyle('margin-left', '5px');
            $this->btnDownloadCancel->CausesValidation = false;
            $this->btnDownloadCancel->Display = false;
            $this->btnDownloadCancel->addAction(new Click(), new AjaxControl($this, 'btnDownloadCancel_Click'));

            $this->btnDocumentCheck = new Bs\Button($this);
            $this->btnDocumentCheck->Text = t('Check PDF');
            $this->btnDocumentCheck->CssClass = 'btn btn-darkblue view-js';
            $this->btnDocumentCheck->setCssStyle('float', 'right');
            $this->btnDocumentCheck->setCssStyle('margin-left', '5px');
            $this->btnDocumentCheck->Display = false;

            $this->btnDocumentDelete = new Bs\Button($this);
            $this->btnDocumentDelete->Text = t('Delete');
            $this->btnDocumentDelete->CssClass = 'btn btn-danger';
            $this->btnDocumentDelete->setCssStyle('float', 'right');
            $this->btnDocumentDelete->setCssStyle('margin-left', '5px');
            $this->btnDocumentDelete->CausesValidation = false;
            $this->btnDocumentDelete->Display = false;
            $this->btnDocumentDelete->addAction(new Click(), new AjaxControl($this, 'btnDocumentDelete_Click'));

            if (!empty( $_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
                $this->lblPrivacyFileName->Text = $_SESSION["data_name"];
                $this->btnDocumentLink->Display = false;
                $this->txtDocumentLink->Display = false;
                $this->btnDownloadSave->Display = true;
                $this->btnDownloadCancel->Display = true;
            }

            if ($this->objSiteOptions->getPrivacyFile()) {
                $this->lblPrivacyFileName->Text = $this->objSiteOptions->PrivacyFileName;
                $this->btnDocumentLink->Display = false;
                $this->txtDocumentLink->Display = false;
                $this->btnDownloadSave->Display = false;
                $this->btnDownloadCancel->Display = false;
                $this->btnDocumentCheck->Display = true;
                $this->btnDocumentDelete->Display = true;

                $objFile = Files::loadById($this->objSiteOptions->getPrivacyFile());
                $this->lblPrivacyFileName->setDataAttribute('open', 'true');
                $this->lblPrivacyFileName->setDataAttribute('view', APP_UPLOADS_URL . $objFile->getPath());
            }

            $this->btnShow = new Bs\Button($this);
            $this->btnShow->Text = t('Show');
            $this->btnShow->CssClass = 'btn btn-orange';
            $this->btnShow->CausesValidation = false;
            $this->btnShow->addAction(new Click(), new AjaxControl($this, 'btnShow_Click'));

            $this->btnHide = new Bs\Button($this);
            $this->btnHide->Text = t('Hide');
            $this->btnHide->CssClass = 'btn btn-orange';
            $this->btnHide->CausesValidation = false;
            $this->btnHide->addAction(new Click(), new AjaxControl($this, 'btnHide_Click'));

            if ($this->objSiteOptions->ShowHide) {
                $this->btnShow->Display = false;
                $this->btnHide->Display = true;
            } else {
                $this->btnShow->Display = true;
                $this->btnHide->Display = false;
            }

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->setCssStyle('margin-right', '10px');
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
        }

        /**
         * Creates and initializes a Toastr notification with predefined configuration.
         *
         * A single Toastr notification instance is created with the following specifications:
         * - Alert type is set to "info".
         * - Position is set to "top center".
         * - Message includes a formatted text indicating successful operation.
         * - A progress bar is enabled for the notification.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> The data has been saved or modified.');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('<strong>Well done!</strong> This slug update was canceled and the record has been restored!');
            $this->dlgToastr2->ProgressBar = true;
        }

        /**
         * Creates and configures modals used for user confirmation dialogs.
         * This method initializes a modal dialog with a warning message prompting the user for confirmation
         * before permanently deleting a document. It sets the modal's header, buttons with their respective
         * actions, and styling to emphasize the warning nature of the action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this document?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">This action cannot be undone.</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
                ['class' => 'btn btn-orange']);
            $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
                ['class' => 'btn btn-default']);
            $this->dlgModal1->addAction(new DialogButton(), new AjaxControl($this, 'deleteDocument_Click'));
        }

        ///////////////////////////////////////////////////////////

        /**
         * Handles the click event for the document link button.
         *
         * @param ActionParams $params Parameters for the action.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnDocumentLink_Click(ActionParams $params): void
        {
            $this->userOptions();

            $_SESSION["redirect-data"] = 'site_options.php#c9_tab';

            $this->btnDocumentLink->Display = false;
            $this->txtDocumentLink->Display = false;

            Application::redirect('file_finder.php');
        }

        /**
         * Handles the click event for the download save button.
         * Updates the site options with session data, adjusts UI elements accordingly,
         * notifies the user, and clears session variables related to the privacy file.
         *
         * @param ActionParams $params Event parameters passed during the button click action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         */
        protected function btnDownloadSave_Click(ActionParams $params): void
        {
            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {

                $this->objSiteOptions->setPrivacyFile($_SESSION["data_id"]);
                $this->objSiteOptions->setPrivacyFileName($_SESSION["data_name"]);
                $this->objSiteOptions->save();
            }

            $this->lblPrivacyFileName->Text = $this->objSiteOptions->PrivacyFileName;
            $this->btnDocumentLink->Display = false;
            $this->txtDocumentLink->Display = false;
            $this->btnDownloadSave->Display = false;
            $this->btnDownloadCancel->Display = false;
            $this->btnDocumentCheck->Display = true;
            $this->btnDocumentDelete->Display = true;

            $this->dlgToastr1->notify();

            unset($_SESSION["data_id"]);
            unset($_SESSION["data_name"]);
            unset($_SESSION["data_path"]);

            $objFile = Files::loadById($this->objSiteOptions->getPrivacyFile());
            $this->lblPrivacyFileName->setDataAttribute('open', 'true');
            $this->lblPrivacyFileName->setDataAttribute('view', APP_UPLOADS_URL . $objFile->getPath());

            $this->userOptions();
        }

        /**
         * Handles the click event for the download cancel button.
         * Adjusts the display of UI elements to their default states, decrements the lock count
         * for the associated file if necessary, and clears session variables related to the file.
         *
         * @param ActionParams $params Event parameters provided during the button click action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function btnDownloadCancel_Click(ActionParams $params): void
        {
            $this->btnDocumentLink->Display = true;
            $this->txtDocumentLink->Display = true;
            $this->lblPrivacyFileName->Display = false;
            $this->btnDownloadSave->Display = false;
            $this->btnDownloadCancel->Display = false;
            $this->btnDocumentCheck->Display = false;
            $this->btnDocumentDelete->Display = false;

            if (!empty($_SESSION["data_id"]) || !empty($_SESSION["data_name"]) || !empty($_SESSION["data_path"])) {
                $objFile = Files::loadById($_SESSION["data_id"]);

                if ($objFile->getLockedFile() !== 0) {
                    $objFile->setLockedFile($objFile->getLockedFile() - 1);
                    $objFile->save();
                }

                unset($_SESSION["data_id"]);
                unset($_SESSION["data_name"]);
                unset($_SESSION["data_path"]);
            }

            $this->userOptions();
        }

        /**
         * Handles the click event for the document delete button.
         * Triggers user-specific options setup and displays a modal dialog box.
         *
         * @param ActionParams $params Event parameters passed during the button click action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         */
        protected function btnDocumentDelete_Click(ActionParams $params): void
        {
            $this->userOptions();
            $this->dlgModal1->showDialogBox();
        }

        /**
         * Handles the click event for deleting a document.
         * Updates the site options by removing the associated document data, adjusts UI elements accordingly,
         * and notifies the user through a modal and toast notifications.
         *
         * @param ActionParams $params Event parameters passed during the button click action.
         *                              Contains the action-specific parameter to confirm deletion.
         *
         * @return void This method does not return a value.
         * @throws Caller
         * @throws InvalidCast
         */
        protected function deleteDocument_Click(ActionParams $params): void
        {
            if ($params->ActionParameter == "pass") {

                $objFile = Files::loadById($this->objSiteOptions->getPrivacyFile());

                if ($objFile) {
                    if ($objFile->getLockedFile() !== 0) {
                        $objFile->setLockedFile($objFile->getLockedFile() - 1);
                        $objFile->save();
                    }
                }

                $this->objSiteOptions->setPrivacyFile(null);
                $this->objSiteOptions->setPrivacyFileName(null);
                $this->objSiteOptions->save();
            }

            $this->userOptions();

            $this->btnDocumentLink->Display = true;
            $this->txtDocumentLink->Display = true;
            $this->lblPrivacyFileName->Display = false;
            $this->btnDownloadSave->Display = false;
            $this->btnDownloadCancel->Display = false;
            $this->btnDocumentCheck->Display = false;
            $this->btnDocumentDelete->Display = false;

            $this->dlgModal1->hideDialogBox();
            $this->dlgToastr1->notify();
        }

        /**
         * Handles the click event for the show button.
         * Updates the UI elements to reflect the show state, modifies site options,
         * and applies the necessary JavaScript to adjust the active content visibility.
         *
         * @param ActionParams $params Event parameters passed during the button click action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         */
        protected function btnShow_Click(ActionParams $params): void
        {
            $this->userOptions();

            $this->btnShow->Display = false;
            $this->btnHide->Display = true;

            $this->btnShow->refresh();
            $this->btnHide->refresh();

            $this->objSiteOptions->setShowHide(1);
            $this->objSiteOptions->save();

            $this->txtContent->Display = true;
        }

        /**
         * Handles the click event for the hide button.
         * Updates site options to reflect the hidden state, adjusts UI elements visibility,
         * and executes JavaScript to show active content.
         *
         * @param ActionParams $params Event parameters passed during the button click action.
         *
         * @return void This method does not return a value.
         * @throws Caller
         */
        protected function btnHide_Click(ActionParams $params): void
        {
            $this->userOptions();

            $this->btnHide->Display = false;
            $this->btnShow->Display = true;

            $this->btnShow->refresh();
            $this->btnHide->refresh();

            $this->objSiteOptions->setShowHide(0);
            $this->objSiteOptions->save();

            $this->txtContent->Display = false;
        }

        /**
         * Handles the click event of the save a button, performing validation
         * and updating user interface elements or triggering modal dialogs
         * based on the validation results.
         *
         * The method validates the `LockedName` input field. It performs the
         * following checks in order:
         * - If the field is empty, restores the original value, updates the interface,
         *   and displays a warning modal.
         * - If the field contains non-slug characters, restores the original value,
         *   updates the interface, and displays another warning modal.
         * - If the sanitized slug exists in the list of restricted slugs, restores the
         *   original value, updates the interface, and displays a warning modal.
         *
         * Each validation failure restores the current value using options stored
         * in `objSiteOptions` and utilizes modal dialogs for user feedback.
         *
         * @param ActionParams $params The action parameters associated with the
         *        button click event.
         *
         * @return void
         * @throws Caller
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            $this->userOptions();

            $this->objSiteOptions->setPrivacyPolicyTitle($this->txtPrivacyPolicyTitle->Text ?? '');
            $this->objSiteOptions->setPostUpdateDate(QDateTime::now());

            if ($this->txtPrivacyPolicyTitle->Text) {
                $this->objSiteOptions->setPolicyTitleSlug('/' . QString::sanitizeForUrl($this->txtPrivacyPolicyTitle->Text));
            }

            if ($this->txtContent->Text) {
                $this->objSiteOptions->setPrivacyPolicy($this->txtContent->Text ?? '');
                $this->objSiteOptions->setPolicyUpdateDate(QDateTime::now());
            }

            $this->objSiteOptions->save();

            if ($this->txtPrivacyPolicyTitle->Text) {
                $this->objFrontendLink->setTitle($this->txtPrivacyPolicyTitle->Text);
                $this->objFrontendLink->setFrontendTitleSlug('/'. QString::sanitizeForUrl($this->txtPrivacyPolicyTitle->Text));
                $this->objFrontendLink->save();
            }

            $this->dlgToastr1->notify();
        }

        /**
         * Handles the click event for the Cancel button.
         * This method restores user options and updates the locked name text field.
         *
         * @param ActionParams $params Parameters associated with the action event.
         *
         * @return void
         * @throws Caller
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            $this->userOptions();

            $this->txtPrivacyPolicyTitle->Text = $this->objSiteOptions->PrivacyPolicyTitle;
            $this->txtPrivacyPolicyTitle->refresh();
        }

        /**
         * Handles the click action for item escape functionality.
         *
         * This method performs the following:
         * - Updates user options.
         * - Sets the LockedName property of the text input field and refreshes it to display
         *   the updated value based on the site options.
         * - Triggers a notification using a Toastr dialog.
         *
         * @param ActionParams $params Parameters associated with the item escape action.
         *
         * @return void
         * @throws Caller
         */
        protected function itemEscape_Click(ActionParams $params): void
        {
            $this->userOptions();

            $this->txtPrivacyPolicyTitle->Text = $this->objSiteOptions->PrivacyPolicyTitle;
            $this->txtPrivacyPolicyTitle->refresh();

            $this->dlgToastr2->notify();
        }


    }