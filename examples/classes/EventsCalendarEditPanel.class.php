<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Project\Application;
    use QCubed\Html;
    use QCubed\Control\ListItem;
    use QCubed\Query\QQ;

    /**
     * Class EventsCalendarEditPanel
     *
     * This class is responsible for creating the edit panel for managing event calendar entries, including menu content,
     * settings, and status. It extends the base Q\Control\Panel and provides a UI for editing and managing event-related
     * configurations.
     */
    class EventsCalendarEditPanel extends Panel
    {
        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;
        public Bs\Modal $dlgModal3;
        public Bs\Modal $dlgModal4;
        public Bs\Modal $dlgModal5;

        public Q\Plugin\Control\Label $lblExistingMenuText;
        public Q\Plugin\Control\Label $txtExistingMenuText;

        public Q\Plugin\Control\Label $lblMenuText;
        public Bs\TextBox $txtMenuText;

        public Q\Plugin\Control\Label $lblGroupTitle;

        public Q\Plugin\Control\Label $lblContentType;
        public Q\Plugin\Select2 $lstContentTypes;

        public Q\Plugin\Control\Label $lblStatus;
        public Q\Plugin\Control\RadioList $lstStatus;

        public Q\Plugin\Control\Label $lblTitleSlug;
        public Q\Plugin\Control\Label $txtTitleSlug;

        public Bs\Button $btnGoToSettings;
        public Bs\Button $btnGoToList;
        public Bs\Button $btnGoToMenu;
        public Bs\Button $btnSave;

        protected int $intId;
        protected object $objMenu;
        protected object $objMenuContent;
        protected object$objEventsSettings;

        protected object$objGroupTitleCondition;
        protected ?array $objGroupTitleClauses;

        protected string $strTemplate = 'EventsCalendarEditPanel.tpl.php';

        /**
         * Constructor method for initializing the object.
         *
         * This method sets up the required properties and objects by fetching data from
         * various sources, initializes UI components like inputs, buttons, modals, and
         * toastr notifications, and handles session cleanup for specific keys.
         * It also manages error handling during the parent's constructor invocation.
         *
         * @param mixed $objParentObject The parent object for this constructor,
         *                               usually a form or control.
         * @param string|null $strControlId An optional control ID to identify the control
         *                                  uniquely, or null for auto-generation.
         *
         * @return void
         * @throws Exception
         * @throws Caller Throws an exception if there is an issue
         *                                  during the parent's constructor call.
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            // Deleting sessions, if any.
            if (!empty($_SESSION['events_edit_group'])) {
                unset($_SESSION['events_edit_group']);
            }

            $this->intId = Application::instance()->context()->queryStringItem('id');
            $this->objMenu = Menu::load($this->intId);
            $this->objMenuContent = MenuContent::load($this->intId);
            $this->objEventsSettings = EventsSettings::loadByIdFromEventsSettings($this->intId);

            $this->createInputs();
            $this->createButtons();
            $this->createModals();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Creates and initializes various input controls used for configuring menu content.
         *
         * This method sets up labels, text boxes, and selects controls for existing and new menu texts,
         * content types, and statuses. It adjusts properties such as text, CSS styles, validation requirements,
         * and event actions based on the current state of menu content and settings.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createInputs(): void
        {
            $this->lblExistingMenuText = new Q\Plugin\Control\Label($this);
            $this->lblExistingMenuText->Text = t('Existing menu text');
            $this->lblExistingMenuText->addCssClass('col-md-3');
            $this->lblExistingMenuText->setCssStyle('font-weight', 400);

            $this->txtExistingMenuText = new Q\Plugin\Control\Label($this);
            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtExistingMenuText->setCssStyle('font-weight', 400);

            $this->lblMenuText = new Q\Plugin\Control\Label($this);
            $this->lblMenuText->Text = t('Menu text');
            $this->lblMenuText->addCssClass('col-md-3');
            $this->lblMenuText->setCssStyle('font-weight', 400);
            $this->lblMenuText->Required = true;

            $this->txtMenuText = new Bs\TextBox($this);
            $this->txtMenuText->Placeholder = t('Menu text');
            $this->txtMenuText->Text = $this->objMenuContent->MenuText;
            $this->txtMenuText->addWrapperCssClass('center-button');
            $this->txtMenuText->MaxLength = MenuContent::MENU_TEXT_MAX_LENGTH;
            $this->txtMenuText->setHtmlAttribute('required', 'required');

            if ($this->objEventsSettings->getIsReserved() == 1) {
                $this->txtMenuText->Enabled = false;
            }

            $this->lblGroupTitle = new Q\Plugin\Control\Label($this);
            $this->lblGroupTitle->Text = t('Editing an event group title');
            $this->lblGroupTitle->addCssClass('col-md-3');
            $this->lblGroupTitle->setCssStyle('font-weight', 400);

            $this->lblContentType = new Q\Plugin\Control\Label($this);
            $this->lblContentType->Text = t('Content type');
            $this->lblContentType->addCssClass('col-md-3');
            $this->lblContentType->setCssStyle('font-weight', 400);
            $this->lblContentType->Required = true;

            $this->lstContentTypes = new Q\Plugin\Select2($this);
            $this->lstContentTypes->MinimumResultsForSearch = -1;
            $this->lstContentTypes->Theme = 'web-vauu';
            $this->lstContentTypes->Width = '100%';
            $this->lstContentTypes->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems(), true);
            $this->lstContentTypes->SelectedValue = $this->objMenuContent->ContentType;
            $this->lstContentTypes->addAction(new Change(), new AjaxControl($this,'lstClassNames_Change'));
            $this->lstContentTypes->setHtmlAttribute('required', 'required');

            if ($this->objMenuContent->getContentType()) {
                $this->lstContentTypes->Enabled = false;
            }

            $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
            $this->lblTitleSlug->Text = t('View');
            $this->lblTitleSlug->addCssClass('col-md-3');
            $this->lblTitleSlug->setCssStyle('font-weight', 400);

            if ($this->objMenuContent->getRedirectUrl()) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objMenuContent->getRedirectUrl();
                $this->txtTitleSlug->Text = Html::renderLink($url, $url, ["target" => "_blank", "class" => "view-link"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            $this->lblStatus = new Q\Plugin\Control\Label($this);
            $this->lblStatus->Text = t('Status');
            $this->lblStatus->addCssClass('col-md-3');
            $this->lblStatus->Required = true;

            $this->lstStatus = new Q\Plugin\Control\RadioList($this);
            $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden')]);
            $this->lstStatus->SelectedValue = $this->objMenuContent->IsEnabled;
            $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';
            $this->lstStatus->AddAction(new Change(), new AjaxControl($this,'lstStatus_Click'));

            if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->lstStatus->Enabled = false;
            }
        }

        /**
         * Initializes and configures button components for navigation within the application.
         *
         * This method creates three buttons: 'Back to menu manager', 'Go to the news manager',
         * and 'Go to news settings manager'. Each button is customized with CSS classes
         * and attached with click events for AJAX control actions. The visibility of
         * the 'Go to the news manager' button is toggled based on the menu content's content type.
         *
         * @return void
         * @throws Caller
         */
        public function CreateButtons(): void
        {
            $this->btnGoToMenu = new Bs\Button($this);
            $this->btnGoToMenu->Text = t('Back to menu manager');
            $this->btnGoToMenu->CssClass = 'btn btn-default';
            $this->btnGoToMenu->addWrapperCssClass('center-button');
            $this->btnGoToMenu->CausesValidation = false;
            $this->btnGoToMenu->addAction(new Click(), new AjaxControl($this, 'btnGoToMenu_Click'));

            $this->btnGoToList = new Bs\Button($this);
            $this->btnGoToList->Text = t('Go to the events calendar manager');
            $this->btnGoToList->CssClass = 'btn btn-default';
            $this->btnGoToList->addWrapperCssClass('center-button');
            $this->btnGoToList->CausesValidation = false;
            $this->btnGoToList->addAction(new Click(), new AjaxControl($this, 'btnGoToList_Click'));

            if ($this->objMenuContent->getContentType()) {
                $this->btnGoToList->Display = true;
            } else {
                $this->btnGoToList->Display = false;
            }

            $this->btnGoToSettings = new Bs\Button($this);
            $this->btnGoToSettings->Text = t('Go to the events settings manager');
            $this->btnGoToSettings->addWrapperCssClass('center-button');
            $this->btnGoToSettings->CausesValidation = false;
            $this->btnGoToSettings->addAction(new Click(), new AjaxControl($this,'btnGoToSettings_Click'));
        }

        /**
         * Creates various modal dialogs used within the application, each having unique content and settings.
         * The method initializes multiple modal objects with specific text, titles, header classes, and buttons.
         *
         * @return void
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Currently, the status of this item cannot be changed as it is associated 
                                    with submenu items or the parent menu item.</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">To change the status of this item, you need to go to the menu manager 
                                    and activate or deactivate it there.</p>');
            $this->dlgModal1->Title = t("Tip");
            $this->dlgModal1->HeaderClasses = 'btn-darkblue';
            $this->dlgModal1->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal2 = new Bs\Modal($this);
            $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The status of the event group for this menu item cannot be changed!</p>
                                    <p style="line-height: 25px; margin-bottom: -3px;">Please remove any redirects from other menu tree items that point 
                                    to this page!</p>');
            $this->dlgModal2->Title = t("Tip");
            $this->dlgModal2->HeaderClasses = 'btn-darkblue';
            $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal3 = new Bs\Modal($this);
            $this->dlgModal3->Title = t("Success");
            $this->dlgModal3->HeaderClasses = 'btn-success';
            $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event group is now hidden!</p>');
            $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
                ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

            $this->dlgModal4 = new Bs\Modal($this);
            $this->dlgModal4->Title = t("Success");
            $this->dlgModal4->HeaderClasses = 'btn-success';
            $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">This event group has now been made public!</p>');
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

        /**
         * Generates an array of ListItem objects representing group titles, based on
         * a specified condition and additional clauses.
         *
         * This method queries group titles using a provided condition, iterates through
         * the retrieved results, and creates ListItem instances for each group title.
         * Each ListItem includes the group's display name and ID. The method also marks
         * items as selected if they match the current menu content's EventsTitle and
         * disables items with reserved status.
         *
         * @return array An array of ListItem objects representing group titles,
         *               each containing a display name, ID, and metadata for selection
         *               or disabled states.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstGroupTitle_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objGroupTitleCondition;
            if (is_null($objCondition)) $objCondition = QQ::all();
            $objGroupTitleCursor = EventsSettings::queryCursor($objCondition, $this->objGroupTitleClauses);

            // Iterate through the Cursor
            while ($objGroupTitle = EventsSettings::instantiateCursor($objGroupTitleCursor)) {
                $objListItem = new ListItem($objGroupTitle->__toString(), $objGroupTitle->Id);
                if (($this->objMenuContent->EventsTitle) && ($this->objMenuContent->EventsTitle->Id == $objGroupTitle->Id))
                    $objListItem->Selected = true;
                if ($objGroupTitle->IsReserved == 1) {
                    $objListItem->Disabled = true;
                }
                $a[] = $objListItem;
            }
            return $a;
        }

        /**
         * Retrieves an array of content type names, omitting any entries that are
         * either disabled or have a specific index removed.
         *
         * This method utilizes the nameArray method from the ContentType class to
         * initially populate the array. A specified index is then removed, followed
         * by iterating over additional data from the extraColumnValuesArray method.
         * Entries that are marked as disabled are also removed from the array.
         *
         * @return array An array containing the names of enabled content types,
         *               excluding specified indices.
         */
        public function lstContentTypeObject_GetItems(): array
        {
            $strContentTypeArray = ContentType::nameArray();
            unset($strContentTypeArray[1]);

            $extraColumnValuesArray = ContentType::extraColumnValuesArray();
            for ($i = 1; $i < count($extraColumnValuesArray); $i++) {
                if ($extraColumnValuesArray[$i]['IsEnabled'] == 0) {
                    unset($strContentTypeArray[$i]);
                }
            }
            return $strContentTypeArray;
        }

        /**
         * Handles the click event for the status list. Based on the status and other conditions,
         * it may display different dialog boxes and update a certain menu and settings object.
         *
         * @param ActionParams $params The parameters associated with the action.
         *
         * @return void
         * @throws Caller
         * @throws RandomException
         */
        public function lstStatus_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
                $this->dlgModal1->showDialogBox();
                $this->updateInputFields();
                return;
            }

            if ($this->objMenuContent->SelectedPageLocked === 1) {
                $this->dlgModal2->showDialogBox();
                $this->updateInputFields();
                return;
            }

            $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $this->objMenuContent->setSettingLocked($this->lstStatus->SelectedValue);
            $this->objMenuContent->save();

            $this->objEventsSettings->setStatus($this->lstStatus->SelectedValue);
            $this->objEventsSettings->save();

            if ($this->objMenuContent->getIsEnabled() === 2) {
                $this->dlgModal3->showDialogBox();
            } else if ($this->objMenuContent->getIsEnabled() === 1) {
                $this->dlgModal4->showDialogBox();
            }
        }

        /**
         * Updates the input fields associated with the lstStatus dropdown.
         *
         * This method sets the SelectedValue of the lstStatus control to reflect the
         * current enabled status of the objMenuContent object. It ensures that the
         * interface correctly displays the enabled or disabled state of the menu content.
         *
         * @return void
         * @throws Caller
         */
        private function updateInputFields(): void
        {
            $this->lstStatus->SelectedValue = $this->objMenuContent->getIsEnabled();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the event when the "Go to Settings" button is clicked by updating
         * the session with the current event group ID and redirecting to the settings
         * manager page.
         *
         * The method sets a session variable to hold the event group identifier and
         * directs the application to a specific section of the settings manager,
         * represented by the hash in the URL.
         *
         * @param ActionParams $params The parameters for the action event, which may
         *                             include contextual data for the action.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnGoToSettings_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $_SESSION['events_edit_group'] = $this->intId;
            Application::redirect('settings_manager.php#eventsSettings_tab');
        }

        /**
         * Handles the click event for the "Go To List" button, redirecting the user to the
         * events calendar list page.
         *
         * This method is triggered when the associated button is clicked, invoking the
         * Application's redirect method to navigate to the specified URL.
         *
         * @param ActionParams $params The parameters connected to the action triggering
         *                             this method. Typically, it includes contextual information
         *                             about the event.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnGoToList_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::redirect('events_calendar_list.php');
        }

        /**
         * Handles the event when the "Go to Menu" button is clicked.
         * Redirects the application to the menu manager page.
         *
         * @param ActionParams $params The parameters associated with this action,
         *                             typically including event-specific data.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        public function btnGoToMenu_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal5->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            Application::redirect('menu_manager.php');
        }
    }