<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\CellClick;
    use QCubed\Event\EnterKey;
    use QCubed\Event\EscapeKey;
    use QCubed\Event\Input;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\Terminate;
    use QCubed\Action\ActionParams;
    use QCubed\Control\ListItem;
    use QCubed\Query\Condition\All;
    use QCubed\Query\Condition\OrCondition;
    use QCubed\Query\QQ;

    /**
     * Represents the settings and functionalities for managing user-specific gallery configuration.
     *
     * This class handles user interactions with the gallery settings interface, providing tools
     * for filtering, paginating, and editing gallery group details. It includes a data grid
     * for listing gallery groups, input controls for editing group details, and modal/dialog
     * controls for feedback or navigation.
     */
    class GalleriesSettings extends Panel
    {
        protected ?object $lstItemsPerPageByAssignedUserObject = null;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        public Bs\Modal $dlgModal1;

        protected Q\Plugin\Toastr $dlgToast1;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public GallerySettingsTable $dtgGalleryGroups;

        public Bs\TextBox $txtGalleryGroup;
        public Bs\TextBox $txtGalleryTitle;
        public Bs\Button $btnSave;
        public Bs\Button $btnCancel;
        public Bs\Button $btnGoToGallery;

        protected object $objUser;
        protected int $intLoggedUserId;
        protected int $intId;

        protected object $objMenuContent;
        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'GallerySettings.tpl.php';

        /**
         * Constructor method for initializing the component.
         *
         * This method sets up the basic structure for the controller, including initializing user data,
         * creating page elements, and binding data to the gallery groups. It assumes that user session
         * handling is in place for retrieving the logged-in user's ID.
         *
         * @param mixed $objParentObject The parent object, typically a form or panel, to which this object belongs.
         * @param string|null $strControlId Optional control ID to uniquely identify this control. Defaults to null.
         *
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
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

            $this->intLoggedUserId = 1;
            $this->objUser = User::load($this->intLoggedUserId);

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgGalleryGroups_Create();
            $this->dtgGalleryGroups->setDataBinder('BindData', $this);

            $this->createButtons();
            $this->createModals();
            $this->createToastr();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and sets up the Gallery Groups datagrid.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgGalleryGroups_Create(): void
        {
            $this->dtgGalleryGroups = new GallerySettingsTable($this);
            $this->dtgGalleryGroups_CreateColumns();
            $this->createPaginators();
            $this->dtgGalleryGroups_MakeEditable();
            $this->dtgGalleryGroups->RowParamsCallback = [$this, "dtgGalleryGroups_GetRowParams"];
            $this->dtgGalleryGroups->SortColumnIndex = 0;
            $this->dtgGalleryGroups->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum();
        }

        /**
         * Creates columns for the GalleryGroups data table. This method delegates the
         * column creation process to the `createColumns` method of the `dtgGalleryGroups` object.
         *
         * @return void
         */
        protected function dtgGalleryGroups_CreateColumns(): void
        {
            $this->dtgGalleryGroups->createColumns();
        }

        /**
         * Makes the data grid of gallery groups editable by adding specific actions and CSS classes.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgGalleryGroups_MakeEditable(): void
        {
            $this->dtgGalleryGroups->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgGalleryGroups_Click'));
            $this->dtgGalleryGroups->addCssClass('clickable-rows');
            $this->dtgGalleryGroups->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles the click event on a gallery group item, retrieving and displaying its information for editing.
         *
         * @param ActionParams $params The parameters associated with the click action, primarily containing the action parameter which is used to identify the gallery group.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function dtgGalleryGroups_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $this->intId = intval($params->ActionParameter);
            $objGalleryGroups = GallerySettings::load($this->intId);

            $this->txtGalleryGroup->Enabled = false;
            $this->txtGalleryGroup->Text = $objGalleryGroups->getName() ?? '';
            $this->txtGalleryTitle->Text = $objGalleryGroups->getTitle() ?? '';
            $this->txtGalleryTitle->focus();

            if (!empty($_SESSION['gallery_group_edit']) || !empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
                $this->btnGoToGallery->Display = true;
                $this->btnGoToGallery->Enabled = false;
            }

            $this->dtgGalleryGroups->addCssClass('disabled');
            $this->txtGalleryGroup->Display = true;
            $this->txtGalleryTitle->Display = true;
            $this->btnSave->Display = true;
            $this->btnCancel->Display = true;
        }

        /**
         * Retrieve row parameters for a gallery group.
         *
         * @param object $objRowObject The object representing the row.
         * @param int $intRowIndex The index of the row.
         *
         * @return array An associative array of parameters for the row.
         */
        public function dtgGalleryGroups_GetRowParams(object $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Sets up the paginator for the gallery groups data table along with pagination and sorting settings.
         * The paginator will have custom labels for previous and next navigation.
         * It also enables Ajax functionality for the data table and applies filter actions.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgGalleryGroups->Paginator = new Bs\Paginator($this);
            $this->dtgGalleryGroups->Paginator->LabelForPrevious = t('Previous');
            $this->dtgGalleryGroups->Paginator->LabelForNext = t('Next');

            $this->dtgGalleryGroups->ItemsPerPage = 10;
            $this->dtgGalleryGroups->SortColumnIndex = 0;
            $this->dtgGalleryGroups->UseAjax = true;

            $this->addFilterActions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a Select2 control for selecting items per a page
         * associated with a user object. Sets various properties of the Select2
         *  control, including a theme, width, and selection mode, and populates it with
         * items. Additionally, it adds an Ajax control change event handler.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast|\DateMalformedStringException
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
         * Retrieves a list of items per a page assigned to a user.
         *
         * This function queries the `ItemsPerPage` table based on a specified condition.
         * It then iterates through each result, instantiates a `ListItem` object for each entry,
         * and checks if the current user has a specific item selected based on their assigned items.
         * The resulting list of `ListItem` objects is returned.
         *
         * @return ListItem[] An array of ListItem objects representing items per a page assigned to the user.
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
         * Updates the number of items displayed per page in the gallery groups data grid based on the selected item.
         *
         * @param ActionParams $params The parameters associated with the action event that triggered this change.
         * @return void
         */
        public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params): void
        {
            $this->dtgGalleryGroups->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgGalleryGroups->refresh();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a search filter text box for user input.
         * The text box has a placeholder, is set to search mode, disables auto-complete,
         * and includes specific CSS classes. Invokes additional actions for filter setup.
         *
         * @return void
         * @throws Caller
         */
        public function createFilter(): void
        {
            $this->txtFilter = new Bs\TextBox($this);
            $this->txtFilter->Placeholder = t('Search...');
            $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
            $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
            $this->txtFilter->addCssClass('search-box');

            $this->btnClearFilters = new Bs\Button($this);
            $this->btnClearFilters->Text = t('Clear filter');
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

            $this->dtgGalleryGroups->refresh();
        }

        /**
         * Adds filter actions to the text input control.
         *
         * This method associates input and enter key events with appropriate AJAX control actions for
         * filtering functionality. The input event triggers an AJAX call with a delay, while the enter
         * key event triggers immediate AJAX action and subsequently terminates any further actions.
         *
         * @return void
         * @throws Caller
         */
        protected function addFilterActions(): void
        {
            $this->txtFilter->addAction(new Input(300), new AjaxControl($this, 'filterChanged'));
            $this->txtFilter->addActionArray(new EnterKey(),
                [
                    new AjaxControl($this, 'filterChanged'),
                    new Terminate()
                ]
            );
        }

        /**
         * Refreshes the data display in the `dtgGalleryGroups` object when a filter change is detected.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgGalleryGroups->refresh();
        }

        /**
         * Binds the data source to the gallery groups data table using a specified condition.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(): void
        {
            $objCondition = $this->getCondition();
            $this->dtgGalleryGroups->bindData($objCondition);
        }

        /**
         * Retrieves the query condition based on the current filter input.
         * If the filter input is empty or null, it returns a condition that matches all records.
         * Otherwise, it creates a condition to match records where the 'Name' field of
         * 'NewsSettings' contains the filter input as a substring.
         *
         * @return All|OrCondition The query condition based on the filter input.
         * @throws Caller
         */
        public function getCondition(): All|OrCondition
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
                    QQ::like(QQN::GallerySettings()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::GallerySettings()->Title, "%" . $strSearchValue . "%")
                );
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures a set of UI buttons and textboxes for the gallery interface.
         *
         * The method sets properties and styles for each UI component, such as buttons for navigating,
         * saving, and canceling actions, as well as textboxes for entering gallery group and title information.
         * It also manages the visibility and functionality of these components based on session data.
         *
         * @return void
         * @throws Caller
         */
        public function createButtons(): void
        {
            $this->btnGoToGallery = new Bs\Button($this);
            $this->btnGoToGallery->Text = t('Go to this gallery');
            $this->btnGoToGallery->addWrapperCssClass('center-button');
            $this->btnGoToGallery->CssClass = 'btn btn-default';
            $this->btnGoToGallery->CausesValidation = false;
            $this->btnGoToGallery->addAction(new Click(), new AjaxControl($this, 'btnGoToGallery_Click'));
            $this->btnGoToGallery->setCssStyle('float', 'left');
            $this->btnGoToGallery->setCssStyle('margin-right', '10px');

            if (!empty($_SESSION['gallery_group_edit']) || !empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
                $this->btnGoToGallery->Display = true;
            } else {
                $this->btnGoToGallery->Display = false;
            }

            $this->txtGalleryGroup = new Bs\TextBox($this);
            $this->txtGalleryGroup->Placeholder = t('Gallery group');
            $this->txtGalleryGroup->ActionParameter = $this->txtGalleryGroup->ControlId;
            $this->txtGalleryGroup->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;
            $this->txtGalleryGroup->setHtmlAttribute('autocomplete', 'off');
            $this->txtGalleryGroup->setCssStyle('float', 'left');
            $this->txtGalleryGroup->setCssStyle('margin-right', '10px');
            $this->txtGalleryGroup->Width = 300;
            $this->txtGalleryGroup->Display = false;

            $this->txtGalleryTitle = new Bs\TextBox($this);
            $this->txtGalleryTitle->Placeholder = t('Gallery title');
            $this->txtGalleryTitle->ActionParameter = $this->txtGalleryTitle->ControlId;
            $this->txtGalleryTitle->CrossScripting = Q\Control\TextBoxBase::XSS_HTML_PURIFIER;

            $this->txtGalleryTitle->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
            $this->txtGalleryTitle->addAction(new EnterKey(), new Terminate());
            $this->txtGalleryTitle->AddAction(new EscapeKey(), new AjaxControl($this, 'btnCancel_Click'));
            $this->txtGalleryTitle->addAction(new EscapeKey(), new Terminate());

            $this->txtGalleryTitle->setHtmlAttribute('autocomplete', 'off');
            $this->txtGalleryTitle->setCssStyle('float', 'left');
            $this->txtGalleryTitle->setCssStyle('margin-right', '10px');
            $this->txtGalleryTitle->Width = 400;
            $this->txtGalleryTitle->Display = false;

            $this->btnSave = new Bs\Button($this);
            $this->btnSave->Text = t('Update');
            $this->btnSave->CssClass = 'btn btn-orange';
            $this->btnSave->addWrapperCssClass('center-button');
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
            $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));
            $this->btnSave->setCssStyle('float', 'left');
            $this->btnSave->setCssStyle('margin-right', '10px');
            $this->btnSave->Display = false;

            $this->btnCancel = new Bs\Button($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->addWrapperCssClass('center-button');
            $this->btnCancel->CssClass = 'btn btn-default';
            $this->btnCancel->CausesValidation = false;
            $this->btnCancel->addAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
            $this->btnCancel->setCssStyle('float', 'left');
            $this->btnCancel->Display = false;
        }

        /**
         * Creates modal dialogs to handle specific user-related actions or warnings.
         *
         * This method initializes and configures modal dialogs used for displaying critical
         * messages or warnings. In this case, it creates a modal to notify the user about
         * an invalid CSRF token, including a warning title, styled header, explanatory text,
         * and a close button.
         *
         * @return void This method does not return any value.
         */
        public function createModals(): void
        {
            ///////////////////////////////////////////////////////////////////////////////////////////
            // CSRF PROTECTION

            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
            $this->dlgModal1->Title = t("Warning");
            $this->dlgModal1->HeaderClasses = 'btn-danger';
            $this->dlgModal1->addCloseButton(t("I understand"));
        }

        /**
         * Initializes and configures multiple Toastr notification objects with specific
         * alert types, positions, and messages. The method sets up four Toastr instances
         * for different success and error notifications, each with a progress bar enabled.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('<strong>Well done!</strong> The gallery group title has been saved or modified.');
            $this->dlgToast1->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Handles the save operation when save a button is clicked.
         * It updates the gallery group based on the input parameters,
         * toggles the display of UI elements, and triggers notifications.
         *
         * @param ActionParams $params Parameters that determine action details.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function btnSave_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objGalleryGroup = GallerySettings::load($this->intId);
            $objSelectedGroup = GallerySettings::selectedByIdFromGallerySettings($this->intId);
            $objMenuContent = MenuContent::load($objSelectedGroup->getGalleryGroupId());
            $objFrontendLink = FrontendLinks::loadByIdFromFrontedLinksId($objSelectedGroup->getGalleryGroupId());

            $objMenuContent->updateMenuContent($this->txtGalleryTitle->Text, $objGalleryGroup->getTitleSlug());

            $objGalleryGroup->setTitle($this->txtGalleryTitle->Text);
            $objGalleryGroup->setPostUpdateDate(Q\QDateTime::now());
            $objGalleryGroup->save();

            $objFrontendLink->setTitle($this->txtGalleryTitle->Text);
            $objFrontendLink->setFrontendTitleSlug($objMenuContent->getRedirectUrl());
            $objFrontendLink->save();

            if (!empty($_SESSION['gallery_group_edit']) || !empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
                $this->btnGoToGallery->Display = true;
                $this->btnGoToGallery->Enabled = true;
            }

            $this->txtGalleryGroup->Display = false;
            $this->txtGalleryTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;

            $this->dtgGalleryGroups->refresh();
            $this->dtgGalleryGroups->removeCssClass('disabled');
            $this->dlgToast1->notify();
        }

        /**
         * Handles the cancel button click event. It checks session variables related to the gallery and adjusts the
         * display state of various UI elements accordingly. This method is intended to reset or hide certain form
         * fields and buttons while enabling another button if specific session conditions are met.
         *
         * @param ActionParams $params The parameters associated with the action event, including any context or
         *     metadata that may be needed by this handler.
         *
         * @return void This method does not return any value.
         * @throws RandomException
         */
        protected function btnCancel_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['gallery_group_edit']) || !empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
                $this->btnGoToGallery->Display = true;
                $this->btnGoToGallery->Enabled = true;
            }

            $this->txtGalleryGroup->Display = false;
            $this->txtGalleryTitle->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;
            $this->dtgGalleryGroups->removeCssClass('disabled');
            $this->txtGalleryGroup->Text = '';
        }

        /**
         * Handles the click event for the "Go To Gallery" button. This method
         * redirects the user to the appropriate gallery editing page based on
         * session variables and clears those session variables after redirection.
         *
         * @param ActionParams $params Parameters associated with the triggered action.
         *
         * @return void
         * @throws RandomException
         * @throws Throwable
         */
        protected function btnGoToGallery_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if (!empty($_SESSION['gallery_group_edit'])) {
                Application::redirect('menu_edit.php?id=' . $_SESSION['gallery_group_edit']);
                unset($_SESSION['gallery_group_edit']);

            } else if (!empty($_SESSION['gallery']) || !empty($_SESSION['gallery_group']) || !empty($_SESSION['gallery_folder'])) {
                Application::redirect('album_edit.php?id=' . $_SESSION['gallery'] . '&group=' . $_SESSION['gallery_group'] . '&folder=' . $_SESSION['gallery_folder']);
                unset($_SESSION['gallery']);
                unset($_SESSION['gallery_group']);
                unset($_SESSION['gallery_folder']);
            }
        }
    }