<?php

    use QCubed as Q;
    use QCubed\Action\ActionParams;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    use QCubed\Event\Click;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Exception\Caller;
    use QCubed\QDateTime;
    use QCubed\QString;
    use QCubed\Query\QQ;

    /**
     * Represents a panel for displaying an overview of site information,
     * including details about the PHP environment, database, server configurations,
     * and supported features. This class also provides details about disk space usage and allocation.
     */
    class UrlOptionsPanel extends Panel
    {
        protected object $objSiteOptions;
        protected object $objFrontendLink;
        protected array $objRestrictedSlugArray;

        protected Bs\Modal $dlgModal1;
        protected Bs\Modal $dlgModal2;
        protected Bs\Modal $dlgModal3;
        protected Bs\Modal $dlgModal4;

        protected Q\Plugin\Toastr $dlgToastr1;

        protected Q\Plugin\Control\Label $lblUrlOptionTitle;
        protected Q\Plugin\Control\Label $lblLockedName;
        protected Bs\TextBox $txtLockedName;
        protected Q\Plugin\Control\Label $lblLockedNameCheck;
        protected Bs\Button $btnSave;
        protected Bs\Button $btnCancel;

        protected Q\Plugin\Control\Label $lblExclusionListTitle;
        protected Q\Plugin\Control\Alert $lblInfo;
        protected QCubed\Control\Panel $pnlExclusionList;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'UrlOptionsPanel.tpl.php';

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
            $this->objFrontendLink = FrontendLinks::load(1);

            $this->objRestrictedSlugArray = SiteCmsRestrictedSlugs::loadAll(
                [
                    QQ::orderBy(QQN::SiteCmsRestrictedSlugs()->Name)
                ]
            );

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
            $this->createToastr();
            $this->renderExclusionList();
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
         * Retrieves a list of restricted slug names.
         *
         * This method extracts the Name property from each object in the
         * objRestrictedSlugArray and returns them as an array.
         *
         * @return array An array of restricted slug names.
         */
        private function getRestrictedSlugNames(): array
        {
            return array_map(
                fn($obj) => $obj->Name,
                $this->objRestrictedSlugArray
            );
        }

        /**
         * Initializes and creates input controls for metadata management, including alerts, labels, and textboxes for keywords, descriptions, and authors.
         *
         * @return void
         * @throws Caller
         */
        public function createInputs(): void
        {
            $this->lblUrlOptionTitle = new Q\Plugin\Control\Label($this);
            $this->lblUrlOptionTitle->Text = t('URL option modification');
            $this->lblUrlOptionTitle->TagName = 'h3';
            $this->lblUrlOptionTitle->addCssClass('vauu-title-3');
            $this->lblUrlOptionTitle->setCssStyle('margin-top', '-10px');

            $this->lblLockedName = new Q\Plugin\Control\Label($this);
            $this->lblLockedName->Text = t('Locked name');
            $this->lblLockedName->addCssClass('col-md-3');
            $this->lblLockedName->setCssStyle('font-weight', '400');
            $this->lblLockedName->Required = true;

            $this->txtLockedName = new Bs\TextBox($this);
            $this->txtLockedName->Placeholder = t('Locked name');
            $this->txtLockedName->Text = $this->objSiteOptions->LockedName ?? null;
            $this->txtLockedName->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtLockedName->Width = '100%';
            $this->txtLockedName->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtLockedName->addAction(new EnterKey(), new Terminate());
            $this->txtLockedName->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtLockedName->addAction(new EscapeKey(), new Terminate());
            $this->txtLockedName->setHtmlAttribute('required', 'required');
            $this->txtLockedName->focus();

            $this->lblLockedNameCheck = new Q\Plugin\Control\Label($this);
            $this->lblLockedNameCheck->HtmlEntities = false;

            if (in_array($this->objSiteOptions->LockedName, $this->getRestrictedSlugNames(), true)) {
                $this->lblLockedNameCheck->Text = '<i class="fa fa-times text-error" aria-hidden="true"></i>';
            } else {
                $this->lblLockedNameCheck->Text = '<i class="fa fa-check text-success" aria-hidden="true"></i>';
            }
            $this->lblLockedNameCheck->refresh();

            $this->lblExclusionListTitle = new Q\Plugin\Control\Label($this);
            $this->lblExclusionListTitle->Text = t('Restricted slugs list');
            $this->lblExclusionListTitle->TagName = 'h3';
            $this->lblExclusionListTitle->addCssClass('vauu-title-3');
            $this->lblExclusionListTitle->setCssStyle('margin-top', '-10px');

            $this->lblInfo = new Q\Plugin\Control\Alert($this);
            $this->lblInfo->Dismissable = false;
            $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
            $this->lblInfo->Text = t('<p><strong>Important!</strong></p>
                                                <p>Upon your first login, please coordinate with your team to change the 
                                                CMS access URL from <strong>"backend"</strong> to a non-standard, less predictable URL.</p>
                                                <p>This precaution helps to reduce the risk of attacks.</p>
                                                <p>Common URLs are frequently targeted by hackers for admin or login pages.</p>
                                                <p>Below you’ll find a list of frequently used URL paths that should be avoided.</p>');

            $this->pnlExclusionList = new Panel($this);
            $this->pnlExclusionList->addCssClass('col-md-12');
            $this->pnlExclusionList->AutoRenderChildren = true;
        }

        /**
         * Creates and configures the Save and Cancel buttons for the interface.
         *
         * @return void
         * @throws Caller
         */
        protected function createButtons(): void
        {
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
         * Creates and initializes modal dialogs with predefined text, title, and styles.
         *
         * Four modal instances are created, each with specific content and configuration:
         * - Two warning modals with red-styled headers and a specific button for an acknowledgment.
         * - Two tip modals with blue-styled headers and a dismiss button.
         *
         * @return void
         * @throws Caller
         */
        protected function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Do not leave the URL slug empty! 
                                                Otherwise, you will not be able to access the administrator or login page.</p>
                                        <p style="line-height: 25px; margin-bottom: -3px;">The slug will be automatically restored.</p>');
            $this->dlgModal1->Title = t('Warning');
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addButton(t("I understand"), 'ok', false, false, null,
                ['data-dismiss' => 'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">It is recommended to use only standard letters (a–z), numbers, and 
                                                hyphens in the URL slug. Do not use diacritic letters or special characters.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;"><strong>Note:</strong> Many web addresses and servers do not consistently support diacritic letters, 
                                    which may cause access or compatibility issues.</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss' => 'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The selected slug is not suitable and is not secure 
                                                for the admin or login page. This slug is also included in the list of restricted slugs.</p>
                                                <p style="line-height: 15px; margin-bottom: 5px;">Please choose a secure, hard-to-guess slug!</p>
                                                <p style="line-height: 25px; margin-bottom: -3px;">The slug will be automatically restored.</p>');
            $this->dlgModal3->Title = t('Warning');
            $this->dlgModal3->HeaderClasses = 'btn-danger';
            $this->dlgModal3->addButton(t("I understand"), 'ok', false, false, null,
                ['data-dismiss' => 'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 5px;">The new slug is now secure!</p>
                                                <p style="line-height: 15px; margin-bottom: -3px;">Please inform your team members about the updated slug, 
                                                which will be used to access the login page and admin panel.</p>');
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss' => 'modal', 'class' => 'btn btn-orange']);
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
            $this->dlgToastr1->AlertType = Q\Plugin\ToastrBase::TYPE_INFO;
            $this->dlgToastr1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<strong>Well done!</strong> This slug update was canceled and the record has been restored!');
            $this->dlgToastr1->ProgressBar = true;
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

            if (!$this->txtLockedName->Text) {
                $this->txtLockedName->Text = $this->objSiteOptions->LockedName;
                $this->txtLockedName->refresh();
                $this->dlgModal1->showDialogBox();
                return;
            }

            if (trim(QString::containsNonCharacters($this->txtLockedName->Text))) {
                $this->txtLockedName->Text = $this->objSiteOptions->LockedName;
                $this->txtLockedName->refresh();
                $this->dlgModal2->showDialogBox();
                return;
            }

            if (in_array(trim($this->txtLockedName->Text), $this->getRestrictedSlugNames(), true)) {
                $this->txtLockedName->Text = $this->objSiteOptions->LockedName;
                $this->txtLockedName->refresh();
                $this->dlgModal3->showDialogBox();
                return;
            }

            $this->objSiteOptions->setLockedName(trim(QString::sanitizeForUrl($this->txtLockedName->Text)));
            $this->objSiteOptions->setLockedSlug('/' . trim(QString::sanitizeForUrl($this->txtLockedName->Text)));
            $this->objSiteOptions->save();

            $this->objFrontendLink->setFrontendTitleSlug('/' . trim(QString::sanitizeForUrl($this->txtLockedName->Text)));
            $this->objFrontendLink->save();

            if (in_array($this->objSiteOptions->LockedName, $this->getRestrictedSlugNames(), true)) {
                $this->lblLockedNameCheck->Text = '<i class="fa fa-times text-error" aria-hidden="true"></i>';
            } else {
                $this->lblLockedNameCheck->Text = '<i class="fa fa-check text-success" aria-hidden="true"></i>';
            }
            $this->lblLockedNameCheck->refresh();

            $this->dlgModal4->showDialogBox();
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

            $this->txtLockedName->Text = $this->objSiteOptions->LockedName;
            $this->txtLockedName->refresh();
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

            $this->txtLockedName->Text = $this->objSiteOptions->LockedName;
            $this->txtLockedName->refresh();

            $this->dlgToastr1->notify();
        }

        /**
         * Renders the exclusion list based on the restricted slug array.
         * Only items with a status of 1 are displayed as panels within the exclusion list panel.
         *
         * @return void
         * @throws Caller
         */
        protected function renderExclusionList(): void
        {
            if (!$this->objRestrictedSlugArray) {
                return;
            }

            foreach ($this->objRestrictedSlugArray as $objRestrictedSlug) {

                if ($objRestrictedSlug->Status == 1) {
                    $pnl = new Panel($this->pnlExclusionList);
                    $pnl->Text = $objRestrictedSlug->Name;
                    $pnl->addCssClass('col-md-4');
                    $pnl->setCssStyle('line-height', '1.5');
                    $pnl->AutoRenderChildren = true;
                }
            }
        }
    }