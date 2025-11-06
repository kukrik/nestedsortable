<?php

    use QCubed as Q;
    use QCubed\Action\ActionParams;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Control\ListBoxBase;
    use QCubed\Control\ListItem;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\TextBoxBase;
    // use QCubed\Event\Change;
    use QCubed\Event\Click;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use QCubed\Query\QQ;

    /**
     * Represents a panel for displaying an overview of site information,
     * including details about the PHP environment, database, server configurations,
     * and supported features. This class also provides details about disk space usage and allocation.
     */
    class SiteOptionsPanel extends Panel
    {
        protected ?object $objDefaultSiteLanguageObjectCondition = null;
        protected ?array $objDefaultSiteLanguageObjectClauses = null;
        protected ?object $objSiteAllowedLanguagesObjectCondition = null;
        protected ?array $objSiteAllowedLanguagesObjectClauses = null;

        protected ?object $objSiteOptions = null;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;

        protected Q\Plugin\Control\Label $lblSiteName;
        protected Bs\TextBox $txtSiteName;
        protected Q\Plugin\Control\Label $lblSiteNameAbbreviation;
        protected Bs\TextBox $txtSiteNameAbbreviation;
        protected Q\Plugin\Control\Label $lblDefaultSiteLanguage;
        protected Q\Plugin\Select2 $lstDefaultSiteLanguage;
        protected Q\Plugin\Control\Label $lblSiteAllowedLanguages;
        protected Q\Plugin\Select2 $lstSiteAllowedLanguages;
        protected Q\Plugin\Control\Label $lblFacebookUrl;
        protected Bs\TextBox $txtFacebookUrl;
        protected Q\Plugin\Control\Label $lblInstagramUrl;
        protected Bs\TextBox $txtInstagramUrl;
        protected Q\Plugin\Control\Label $lblBlankPageText;
        protected Bs\TextBox $txtBlankPageText;

        protected Bs\Button $btnSave;
        protected Bs\Button $btnCancel;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'SiteOptionsPanel.tpl.php';

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

            $this->createInputs();
            $this->createButtons();
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
            $this->lblSiteName = new Q\Plugin\Control\Label($this);
            $this->lblSiteName->Text = t('Site name');
            $this->lblSiteName->addCssClass('col-md-3');
            $this->lblSiteName->setCssStyle('font-weight', '400');
            $this->lblSiteName->Required = true;

            $this->txtSiteName = new Bs\TextBox($this);
            $this->txtSiteName->Placeholder = t('Site name');
            $this->txtSiteName->Text = $this->objSiteOptions->SiteName ?? null;
            $this->txtSiteName->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtSiteName->Width = '100%';
            $this->txtSiteName->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtSiteName->addAction(new EnterKey(), new Terminate());
            $this->txtSiteName->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtSiteName->addAction(new EscapeKey(), new Terminate());
            $this->txtSiteName->setHtmlAttribute('required', 'required');
            $this->txtSiteName->focus();

            $this->lblSiteNameAbbreviation = new Q\Plugin\Control\Label($this);
            $this->lblSiteNameAbbreviation->Text = t('Site name abbreviation');
            $this->lblSiteNameAbbreviation->addCssClass('col-md-3');
            $this->lblSiteNameAbbreviation->setCssStyle('font-weight', '400');

            $this->txtSiteNameAbbreviation = new Bs\TextBox($this);
            $this->txtSiteNameAbbreviation->Placeholder = t('Site name abbreviation');
            $this->txtSiteNameAbbreviation->Text = $this->objSiteOptions->SiteNameAbbreviation ?? null;
            $this->txtSiteNameAbbreviation->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtSiteNameAbbreviation->Width = '100%';
            $this->txtSiteNameAbbreviation->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtSiteNameAbbreviation->addAction(new EnterKey(), new Terminate());
            $this->txtSiteNameAbbreviation->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtSiteNameAbbreviation->addAction(new EscapeKey(), new Terminate());

            $this->lblDefaultSiteLanguage = new Q\Plugin\Control\Label($this);
            $this->lblDefaultSiteLanguage->Text = t('Default site language');
            $this->lblDefaultSiteLanguage->addCssClass('col-md-3');
            $this->lblDefaultSiteLanguage->setCssStyle('font-weight', '400');
            $this->lblDefaultSiteLanguage->Required = true;

            $this->lstDefaultSiteLanguage = new Q\Plugin\Select2($this);
            $this->lstDefaultSiteLanguage->MinimumResultsForSearch = -1;
            $this->lstDefaultSiteLanguage->Theme = 'web-vauu';
            $this->lstDefaultSiteLanguage->Width = '100%';
            $this->lstDefaultSiteLanguage->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultSiteLanguage->addItems($this->lstDefaultSiteLanguageObject_GetItems());
            $this->lstDefaultSiteLanguage->SelectedValue = $this->objSiteOptions->DefaultSiteLanguage ?? null;
            //$this->lstDefaultSiteLanguage->AddAction(new Change(), new AjaxControl($this, 'lstDefaultSiteLanguage_Change'));
            $this->lstDefaultSiteLanguage->Enabled = false;

            $this->lblSiteAllowedLanguages = new Q\Plugin\Control\Label($this);
            $this->lblSiteAllowedLanguages->Text = t('Site allowed languages');
            $this->lblSiteAllowedLanguages->addCssClass('col-md-3');
            $this->lblSiteAllowedLanguages->setCssStyle('font-weight', '400');

            $this->lstSiteAllowedLanguages = new Q\Plugin\Select2($this);
            $this->lstSiteAllowedLanguages->ContainerWidth = 'resolve';
            $this->lstSiteAllowedLanguages->MinimumResultsForSearch = -1;
            $this->lstSiteAllowedLanguages->Theme = 'web-vauu';
            $this->lstSiteAllowedLanguages->Width = '100%';
            $this->lstSiteAllowedLanguages->SelectionMode = ListBoxBase::SELECTION_MODE_MULTIPLE;
            $this->lstSiteAllowedLanguages->addItems($this->lstSiteAllowedLanguagesObject_GetItems());
            $this->lstSiteAllowedLanguages->SelectedValues = explode(',', $this->objSiteOptions->SiteAllowedLanguages) ?? null;
            //$this->lstSiteAllowedLanguages->AddAction(new Change(), new AjaxControl($this, 'lsSiteAllowedLanguages_Change'));
            $this->lstSiteAllowedLanguages->Enabled = false;

            $this->lblFacebookUrl = new Q\Plugin\Control\Label($this);
            $this->lblFacebookUrl->Text = t('Facebook URL');
            $this->lblFacebookUrl->addCssClass('col-md-3');
            $this->lblFacebookUrl->setCssStyle('font-weight', '400');

            $this->txtFacebookUrl = new Bs\TextBox($this);
            $this->txtFacebookUrl->Placeholder = t('Facebook URL');
            $this->txtFacebookUrl->Text = $this->objSiteOptions->FacebookUrl ?? null;
            $this->txtFacebookUrl->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtFacebookUrl->Width = '100%';
            $this->txtFacebookUrl->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtFacebookUrl->addAction(new EnterKey(), new Terminate());
            $this->txtFacebookUrl->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtFacebookUrl->addAction(new EscapeKey(), new Terminate());

            $this->lblInstagramUrl = new Q\Plugin\Control\Label($this);
            $this->lblInstagramUrl->Text = t('Instagram URL');
            $this->lblInstagramUrl->addCssClass('col-md-3');
            $this->lblInstagramUrl->setCssStyle('font-weight', '400');

            $this->txtInstagramUrl = new Bs\TextBox($this);
            $this->txtInstagramUrl->Placeholder = t('Instagram URL');
            $this->txtInstagramUrl->Text = $this->objSiteOptions->InstagramUrl ?? null;
            $this->txtInstagramUrl->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtInstagramUrl->Width = '100%';
            $this->txtInstagramUrl->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtInstagramUrl->addAction(new EnterKey(), new Terminate());
            $this->txtInstagramUrl->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtInstagramUrl->addAction(new EscapeKey(), new Terminate());

            $this->lblBlankPageText = new Q\Plugin\Control\Label($this);
            $this->lblBlankPageText->Text = t('Blank page text');
            $this->lblBlankPageText->addCssClass('col-md-3');
            $this->lblBlankPageText->setCssStyle('font-weight', '400');

            $this->txtBlankPageText = new Bs\TextBox($this);
            $this->txtBlankPageText->Placeholder = t('Blank page text');
            $this->txtBlankPageText->Text = $this->objSiteOptions->BlankPageText ?? null;
            $this->txtBlankPageText->CrossScripting = TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtBlankPageText->Width = '100%';
            $this->txtBlankPageText->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtBlankPageText->addAction(new EnterKey(), new Terminate());
            $this->txtBlankPageText->AddAction(new EscapeKey(), new AjaxControl($this, 'itemEscape_Click'));
            $this->txtBlankPageText->addAction(new EscapeKey(), new Terminate());
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
         * Retrieves a list of `ListItem` objects representing the default site languages.
         *
         * This method queries the `SiteLanguage` table using the specified conditions and clauses
         * and generates a list of items. Each item includes the string representation and ID
         * of the default site language. The method also determines if an item is selected based
         * on the current default site language and disables items that are marked as inactive.
         *
         * @return ListItem[] An array of `ListItem` objects containing the default site languages.
         *                    Disabled items are specified for inactive languages, and the selected
         *                    item is marked accordingly.
         * @throws Caller If the instantiated conditions or clauses are invalid.
         * @throws \DateMalformedStringException
         */
        public function lstDefaultSiteLanguageObject_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultSiteLanguageObjectCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objDefaultSiteLanguageObjectCursor = SiteLanguage::queryCursor($objCondition, $this->objDefaultSiteLanguageObjectClauses);

            // Iterate through the Cursor
            while ($objDefaultSiteLanguageObject = SiteLanguage::instantiateCursor($objDefaultSiteLanguageObjectCursor)) {
                $objListItem = new ListItem($objDefaultSiteLanguageObject->__toString(), $objDefaultSiteLanguageObject->Id);
                if (($this->objSiteOptions->DefaultSiteLanguageObject) && ($this->objSiteOptions->DefaultSiteLanguageObject->Id == $objDefaultSiteLanguageObject->Id))
                    $objListItem->Selected = true;

                // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
                // {display: none;} </style>
                // A little trick on how to hide some options. Just set the option to "disabled" and
                // use it only on a specific page. You just have to use the style.

                if ($objDefaultSiteLanguageObject->IsActive == 2) {
                    $objListItem->Disabled = true;
                }
                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Retrieves a list of `ListItem` objects representing the allowed site languages.
         *
         * This method queries the site languages based on defined conditions and clauses.
         * Each language is instantiated and represented as a `ListItem` object. Specific
         * attributes such as selection and availability (disabled state) are set according
         * to the provided conditions and application logic.
         *
         * @return array Returns an array of `ListItem` objects. Each `ListItem` represents
         *               a site language, containing its display name and identifier. Some
         *               items may be marked as selected or disabled based on internal logic.
         * @throws \DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstSiteAllowedLanguagesObject_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objSiteAllowedLanguagesObjectCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objDefaultSiteLanguageObjectCursor = SiteLanguage::queryCursor($objCondition, $this->objSiteAllowedLanguagesObjectClauses);

            // Iterate through the Cursor
            while ($objDefaultSiteLanguageObject = SiteLanguage::instantiateCursor($objDefaultSiteLanguageObjectCursor)) {
                $objListItem = new ListItem($objDefaultSiteLanguageObject->__toString(), $objDefaultSiteLanguageObject->Id);
                if (($this->objSiteOptions->DefaultSiteLanguageObject) && ($this->objSiteOptions->DefaultSiteLanguageObject->Id == $objDefaultSiteLanguageObject->Id))
                    $objListItem->Selected = true;

                // <style> .select2-container--web-vauu .select2-results__option[aria-disabled=true]
                // {display: none;} </style>
                // A little trick on how to hide some options. Just set the option to "disabled" and
                // use it only on a specific page. You just have to use the style.

                if ($objDefaultSiteLanguageObject->IsActive == 2) {
                    $objListItem->Disabled = true;
                }
                $a[] = $objListItem;
            }
            return $a;
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

        ///////////////////////////////////////////////////////////

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

            $this->objSiteOptions->setSiteName($this->txtSiteName->Text ?? '');
            $this->objSiteOptions->setSiteNameAbbreviation($this->txtSiteNameAbbreviation->Text ?? '');
            $this->objSiteOptions->setDefaultSiteLanguage($this->lstDefaultSiteLanguage->SelectedValue);

            $implodedIds = implode(',', $this->lstSiteAllowedLanguages->SelectedValues);
            $this->objSiteOptions->setSiteAllowedLanguages($implodedIds);

            $this->objSiteOptions->setFacebookUrl($this->txtFacebookUrl->Text ?? '');
            $this->objSiteOptions->setInstagramUrl($this->txtInstagramUrl->Text ?? '');
            $this->objSiteOptions->setBlankPageText($this->txtBlankPageText->Text ?? '');
            $this->objSiteOptions->setPostUpdateDate(QDateTime::now());
            $this->objSiteOptions->save();

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

            $this->txtSiteName->Text = $this->objSiteOptions->SiteName;
            $this->txtSiteNameAbbreviation->Text = $this->objSiteOptions->SiteNameAbbreviation;
            $this->lstDefaultSiteLanguage->SelectedValue = $this->objSiteOptions->DefaultSiteLanguage;
            $this->lstSiteAllowedLanguages->SelectedValues = explode(',', $this->objSiteOptions->SiteAllowedLanguages);
            $this->txtFacebookUrl->Text = $this->objSiteOptions->FacebookUrl;
            $this->txtInstagramUrl->Text = $this->objSiteOptions->InstagramUrl;
            $this->txtBlankPageText->Text = $this->objSiteOptions->BlankPageText;

            $this->txtSiteName->refresh();
            $this->txtSiteNameAbbreviation->refresh();
            $this->lstSiteAllowedLanguages->refresh();
            $this->lstDefaultSiteLanguage->refresh();
            $this->txtFacebookUrl->refresh();
            $this->txtInstagramUrl->refresh();
            $this->txtBlankPageText->refresh();
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

            $this->txtSiteName->Text = $this->objSiteOptions->SiteName;
            $this->txtSiteNameAbbreviation->Text = $this->objSiteOptions->SiteNameAbbreviation;
            $this->lstDefaultSiteLanguage->SelectedValue = $this->objSiteOptions->DefaultSiteLanguage;
            $this->lstSiteAllowedLanguages->SelectedValues = explode(',', $this->objSiteOptions->SiteAllowedLanguages);
            $this->txtFacebookUrl->Text = $this->objSiteOptions->FacebookUrl;
            $this->txtInstagramUrl->Text = $this->objSiteOptions->InstagramUrl;
            $this->txtBlankPageText->Text = $this->objSiteOptions->BlankPageText;

            $this->txtSiteName->refresh();
            $this->txtSiteNameAbbreviation->refresh();
            $this->lstSiteAllowedLanguages->refresh();
            $this->lstDefaultSiteLanguage->refresh();
            $this->txtFacebookUrl->refresh();
            $this->txtInstagramUrl->refresh();
            $this->txtBlankPageText->refresh();

            $this->dlgToastr2->notify();
        }
    }