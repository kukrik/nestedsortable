<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Bootstrap as Bs;
    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Event\DialogButton;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use Random\RandomException;
    use QCubed\Event\Click;
    use QCubed\Event\Change;
    use QCubed\Event\CellClick;
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
     * Class LinksListPanel
     *
     * This class extends the Panel functionality to handle a link list interface.
     * It provides various components such as dropdowns, buttons, datagrids, and modals to manage links
     * and their associated groups effectively. The class includes logic for initializing UI components,
     * handling user interactions, and binding data.
     *
     * Functionalities include:
     * - Managing items per a page based on user-specific settings.
     * - Displaying and handling modals and toasts for notifications and confirmations.
     * - Managing locked links and target group lists with interactive and dynamic behaviors.
     * - Handling event-driven changes to user inputs with server-side logic via AJAX.
     */
    class LinksListPanel extends Panel
    {
        protected Q\Plugin\Select2 $lstItemsPerPageByAssignedUserObject;
        protected ?object $objItemsPerPageByAssignedUserObjectCondition = null;
        protected ?array $objItemsPerPageByAssignedUserObjectClauses = null;

        protected Q\Plugin\Toastr $dlgToast1;
        protected Q\Plugin\Toastr $dlgToast2;
        protected Q\Plugin\Toastr $dlgToast3;
        protected Q\Plugin\Toastr $dlgToast4;

        public Bs\Modal $dlgModal1;
        public Bs\Modal $dlgModal2;

        public Bs\Button $btnMove;
        public Bs\Button $btnLockedCancel;
        public ?Q\Plugin\Select2 $lstLinksLocked = null;
        public ?Q\Plugin\Select2 $lstTargetGroup = null;

        public Bs\TextBox $txtFilter;
        public Bs\Button $btnClearFilters;
        public LinksTable $dtgLinks;
        public Bs\Button $btnBack;

        protected object $objUser;
        protected int $intLoggedUserId;
        protected ?object $objGroupTitleCondition = null;
        protected ?array $objGroupTitleClauses = null;

        protected string $strTemplate = 'LinksListPanel.tpl.php';

        /**
         * Constructor method for initializing the object and setting up user-specific data, controls,
         * modals, filters, and data binding for a link table grid.
         *
         * @param mixed $objParentObject The parent object which is typically a form or control that this object is a
         *     part of.
         * @param string|null $strControlId Optional control ID for this object can be null if not specified.
         *
         * @return void
         *
         * @throws DateMalformedStringException
         * @throws Caller If an exception occurs during the parent constructor call.
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

            $this->createButtons();
            $this->createToastr();
            $this->createModals();

            $this->createItemsPerPage();
            $this->createFilter();
            $this->dtgLinks_Create();
            $this->dtgLinks->setDataBinder('BindData', $this);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Resets UI elements related to item movement by hiding elements with the 'move-items-js' class.
         * Executes a JavaScript command to add the 'hidden' class to these elements.
         *
         * @return void No return value.
         * @throws Caller
         */
        public function elementsReset(): void
        {
            Application::executeJavaScript("
                $('.move-items-js').addClass('hidden');
            ");
        }

        /**
         * Initializes and configures input controls for managing locked links and target groups.
         * This method creates two Select2 input components, configures their properties, populates them
         * with relevant options retrieved from the database, and sets their behavior based on certain
         * conditions such as the count of locked links. It also assigns Ajax-based change actions to the inputs.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createInputs(): void
        {
            $this->lstLinksLocked = new Q\Plugin\Select2($this);
            $this->lstLinksLocked->MinimumResultsForSearch = -1;
            $this->lstLinksLocked->Theme = 'web-vauu';
            $this->lstLinksLocked->Width = '100%';
            $this->lstLinksLocked->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstLinksLocked->addItem(t('- Select one links group -'), null, true);

            $this->lstTargetGroup = new Q\Plugin\Select2($this);
            $this->lstTargetGroup->MinimumResultsForSearch = -1;
            $this->lstTargetGroup->Theme = 'web-vauu';
            $this->lstTargetGroup->Width = '100%';
            $this->lstTargetGroup->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstTargetGroup->addItem(t('- Select one links group -'), null, true);

            $objGroups = LinksSettings::queryArray(
                QQ::AndCondition(
                    QQ::notEqual(QQN::LinksSettings()->LinksLocked, 0),
                    QQ::OrCondition(
                        QQ::equal(QQN::LinksSettings()->LinkType, 1),
                        QQ::equal(QQN::LinksSettings()->LinkType, 2)
                    )
                ),
                [
                    QQ::orderBy(QQN::LinksSettings()->Id)
                ]
            );

            // We count locked links
            $countLocked = LinksSettings::countByLinksLocked(1);

            foreach ($objGroups as $objTitle) {
                if ($countLocked > 1 && $objTitle->LinksLocked === 1) {
                    $this->lstLinksLocked->addItem($objTitle->Name, $objTitle->Id);
                } else if ($countLocked === 1 && $objTitle->LinksLocked === 1) {
                    $this->lstLinksLocked->addItem($objTitle->Name, $objTitle->Id);
                    $this->lstLinksLocked->SelectedValue = $objTitle->Id;
                }
            }

            if ($this->lstLinksLocked->SelectedValue === null) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->Enabled = false;
            }

            if ($countLocked === 1) {
                $this->lstLinksLocked->Enabled = false;
                $this->lstTargetGroup->Enabled = true;
                $this->lstTargetGroup->focus();
            } else {
                $this->lstLinksLocked->Enabled = true;
                $this->lstLinksLocked->focus();
            }

            $this->lstLinksLocked->addAction(new Change(), new AjaxControl($this,'lstLinksLocked_Change'));
            $this->lstTargetGroup->addAction(new Change(), new AjaxControl($this,'lstTargetGroup_Change'));
            $this->lstTargetGroup->Enabled = false;
        }

        /**
         * Updates the target group list based on the selected value in the link locked dropdown.
         * This method retrieves the settings corresponding to the selected link value and populates
         * the target group list with appropriate entries. If no value is selected, the target group
         * list becomes disabled, and error styling is applied to the link locked dropdown.
         *
         * @param ActionParams $params The parameters associated with the action event triggered by the links locked dropdown change.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        protected function lstLinksLocked_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objLinksSettings = LinksSettings::loadById($this->lstLinksLocked->SelectedValue);

            $objTargetGroups = LinksSettings::queryArray(
                QQ::Equal(QQN::LinksSettings()->LinkType, $objLinksSettings->LinkType),
                QQ::orderBy(QQN::LinksSettings()->Id)
            );

            foreach ($objTargetGroups as $objTitle) {
                if ($objTitle->IsReserved !== 2) {
                    $this->lstTargetGroup->addItem($objTitle->Name, $objTitle->Id);
                }
            }

            if ($this->lstLinksLocked->SelectedValue === null) {
                $this->lstTargetGroup->Enabled = false;
                $this->lstLinksLocked->addCssClass('has-error');
                $this->dlgToast1->notify();
            } else {
                $this->lstTargetGroup->Enabled = true;
                $this->lstLinksLocked->removeCssClass('has-error');
                $this->lstTargetGroup->focus();
            }
        }

        /**
         * Handles the change event of the target group list control and performs various validations
         * and updates based on the selected values of related controls.
         *
         * If the selected value of the target group matches the selected value of the links locked list control,
         * the target group's selection is cleared, its CSS class is updated, and a notification is triggered.
         * If both controls have non-null, non-matching selected values, a modal dialog box is displayed.
         * Otherwise, the error CSS class is removed from the target group.
         *
         * @param ActionParams $params The parameters related to the change action event.
         *
         * @return void
         * @throws RandomException
         * @throws Caller
         */
        protected function lstTargetGroup_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            if ($this->lstLinksLocked->SelectedValue === $this->lstTargetGroup->SelectedValue) {
                $this->lstTargetGroup->SelectedValue = null;
                $this->lstTargetGroup->refresh();
                $this->lstTargetGroup->addCssClass('has-error');
                $this->dlgToast2->notify();
            } else if ($this->lstLinksLocked->SelectedValue !== null && $this->lstTargetGroup->SelectedValue !== null) {
                $this->dlgModal1->showDialogBox();
            } else {
                $this->lstTargetGroup->removeCssClass('has-error');
            }
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

            Application::executeJavaScript("$('.move-items-js').addClass('hidden');");

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
         * Creates and configures multiple Toastr notification objects for use within the application.
         * Each Toastr object is customized with a specific alert type, position class, message, progress bar,
         * timeout duration, and HTML escaping settings as required.
         *
         * @return void
         * @throws Caller
         */
        protected function createToastr(): void
        {
            $this->dlgToast1 = new Q\Plugin\Toastr($this);
            $this->dlgToast1->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast1->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast1->Message = t('<p style=\"margin-bottom: 5px;\">The links group must be selected beforehand!</p>');
            $this->dlgToast1->ProgressBar = true;
            $this->dlgToast1->TimeOut = 10000;
            $this->dlgToast1->EscapeHtml = false;

            $this->dlgToast2 = new Q\Plugin\Toastr($this);
            $this->dlgToast2->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast2->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast2->Message = t('<p style=\"margin-bottom: 5px;\">The links group cannot be the same as the target group!</p>');
            $this->dlgToast2->ProgressBar = true;
            $this->dlgToast2->TimeOut = 10000;
            $this->dlgToast2->EscapeHtml = false;

            $this->dlgToast3 = new Q\Plugin\Toastr($this);
            $this->dlgToast3->AlertType = Q\Plugin\ToastrBase::TYPE_SUCCESS;
            $this->dlgToast3->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast3->Message = t('<strong>Well done!</strong> The transfer of this links group to the new group was successful.');
            $this->dlgToast3->ProgressBar = true;

            $this->dlgToast4 = new Q\Plugin\Toastr($this);
            $this->dlgToast4->AlertType = Q\Plugin\ToastrBase::TYPE_ERROR;
            $this->dlgToast4->PositionClass = Q\Plugin\ToastrBase::POSITION_TOP_CENTER;
            $this->dlgToast4->Message = t('The transfer of this links group to the new group failed.');
            $this->dlgToast4->ProgressBar = true;
        }

        /**
         * Creates and configures modal dialogs used for confirming and managing actions
         * related to transferring links between groups.
         *
         * The first modal (dlgModal1) is configured to present a warning message about transferring links.
         * It includes a title, styled header classes, confirmation and cancellation buttons,
         * as well as actions triggered by button clicks and modal events.
         *
         * @return void This method does not return a value.
         * @throws Caller
         */
        public function createModals(): void
        {
            $this->dlgModal1 = new Bs\Modal($this);
            $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to move the links from this links group to another links group?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Note: If the selected group contains multiple links, they will all be transferred to the new group!</p>');
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
         * Handles the click event of the move button. This method performs actions such as displaying
         * item movement elements, creating necessary input fields, disabling the move button,
         * and adding a disabled CSS class to the link table.
         *
         * @param ActionParams $params Parameters passed from the action triggering the event, typically containing event-related details.
         *
         * @return void This method does not return any value.
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

            Application::executeJavaScript("$('.move-items-js').removeClass('hidden');");

            $this->createInputs();

            $this->btnMove->Enabled = false;
            $this->dtgLinks->addCssClass('disabled');
        }

        /**
         * Handles the "transfer cancelling" action triggered by a user interaction. This method resets
         * form elements, clears selected values for linked and target group lists, enables the move button,
         * refreshes the state of the lists, and updates the interface by enabling the data grid.
         *
         * @param ActionParams $params Parameters associated with the triggered action.
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

            $this->lstLinksLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->btnMove->Enabled = true;

            $this->lstLinksLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->dtgLinks->removeCssClass('disabled');
        }

        /**
         * Handles the click event for moving items. This method performs several actions, including hiding
         * a modal dialog box, transferring operations related to item links, resetting UI elements, and
         * enabling the move button.
         *
         * @param ActionParams $params Parameters associated with the action that triggered the click event.
         *
         * @return void This method does not return any value.
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
            $this->linksTransferOperations();

            $this->elementsReset();
            $this->btnMove->Enabled = true;
        }

        /**
         * Handles the transfer of link group operations between locked and target settings groups.
         * This method updates the settings of groups and their associated links, refreshes the
         * data grid, and executes JavaScript for UI updates. Upon successful transfer, it evaluates
         * the count of links in the target group to trigger corresponding notifications. It also
         * reorders all links by their post-date after the operation is completed.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        private function linksTransferOperations(): void
        {
            $objLockedGroup = LinksSettings::loadById($this->lstLinksLocked->SelectedValue);
            $objTargetGroup = LinksSettings::loadById($this->lstTargetGroup->SelectedValue);

            $objLinksGroupArray = Links::loadArrayBySettingsId($objLockedGroup->getId());
            $beforeCount = Links::countBySettingsId($objTargetGroup->getId());

            $objLinksSettings = LinksSettings::loadById($objLockedGroup->getId());
            $objLinksSettings->setLinksLocked(0);
            $objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objLinksSettings->setPostUpdateDate(Q\QDateTime::Now());
            $objLinksSettings->save();

            $objLinksSettings = LinksSettings::loadById($objTargetGroup->getId());
            $objLinksSettings->setLinksLocked(1);
            $objLinksSettings->setAssignedEditorsNameById($this->intLoggedUserId);
            $objLinksSettings->setPostUpdateDate(Q\QDateTime::now());
            $objLinksSettings->save();

            foreach ($objLinksGroupArray as $objLinksGroup) {
                $objLinks = Links::loadById($objLinksGroup->getId());
                $objLinks->setSettingsId($this->lstTargetGroup->SelectedValue);
                $objLinks->setSettingsIdTitle($this->lstTargetGroup->SelectedName);
                $objLinks->save();
            }

            $this->dtgLinks->refresh();

            Application::executeJavaScript("$('.move-items-js').addClass('hidden');");

            $afterCount = Links::countBySettingsId($objTargetGroup->getId());

            if ($beforeCount < $afterCount) {
                $this->dlgToast3->notify();
            } else {
                $this->dlgToast4->notify();
            }

            $objLinkArray = Links::loadAll(QQ::Clause(QQ::orderBy(QQN::Links()->PostDate, false)));

            foreach ($objLinkArray as $key => $objLink) {
                $objLinks = Links::loadById($objLink->getId());
                $objLinks->setOrder($key);
                $objLinks->save();
            }
        }

        /**
         * Handles the click event for the "Locked Cancel" button.
         * This method checks the count of LinksSettings, updates the user interface based on the condition,
         * and resets various controls to their initial state. It ensures that the move items functionality
         * is hidden, re-enables the move button, clears selected values of lists, refreshes the lists,
         * and removes specific CSS classes from the table.
         *
         * @param ActionParams $params The parameters triggered by the action, typically containing the event's context and metadata.
         *
         * @return void This method does not return a value.
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

            $this->btnMove->Enabled = true;

            $this->lstLinksLocked->SelectedValue = null;
            $this->lstTargetGroup->SelectedValue = null;

            $this->lstLinksLocked->refresh();
            $this->lstTargetGroup->refresh();

            $this->dtgLinks->removeCssClass('disabled');
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
         * Create and configure the links datagrid
         *
         * @return void
         * @throws \QCubed\Exception\Caller
         */
        protected function dtgLinks_Create(): void
        {
            $this->dtgLinks = new LinksTable($this);
            $this->dtgLinks_CreateColumns();
            $this->createPaginators();
            $this->dtgLinks_MakeEditable();
            $this->dtgLinks->RowParamsCallback = [$this, "dtgLinks_GetRowParams"];
            $this->dtgLinks->SortColumnIndex = 5;
            //$this->dtgLinks->SortDirection = -1;
            $this->dtgLinks->UseAjax = true;
            $this->dtgLinks->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();
        }

        /**
         * Create columns for the datagrid
         *
         * @return void
         */
        protected function dtgLinks_CreateColumns(): void
        {
            $this->dtgLinks->createColumns();
        }

        /**
         * Configures the dtgLinks datatable to be interactive and editable by adding
         * appropriate actions and CSS classes. This method enables cell click actions
         * that trigger an AJAX control event and applies specified CSS classes to the table.
         *
         * @return void
         * @throws Caller
         */
        protected function dtgLinks_MakeEditable(): void
        {
            $this->dtgLinks->addAction(new CellClick(0, null, CellClick::rowDataValue('value')), new AjaxControl($this, 'dtgLinksRow_Click'));
            $this->dtgLinks->addCssClass('clickable-rows');
            $this->dtgLinks->CssClass = 'table vauu-table table-hover table-responsive';
        }

        /**
         * Handles click events on rows of the dtgLinks datatable. Retrieves the link
         * settings based on the action parameter's identifier, then redirects the user
         * to the board edit page with the link's ID and group information as query parameters.
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
        protected function dtgLinksRow_Click(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal2->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $intId = intval($params->ActionParameter);
            $objLinks = LinksSettings::loadById($intId);
            $intGroup = $objLinks->getMenuContentId();

            Application::redirect('Links_edit.php' . '?id=' . $intId . '&group=' . $intGroup);
        }

        /**
         * Get row parameters for the row tag
         *
         * @param mixed $objRowObject   A database object
         * @param int $intRowIndex      The row index
         *
         * @return array
         */
        public function dtgLinks_GetRowParams(mixed $objRowObject, int $intRowIndex): array
        {
            $strKey = $objRowObject->primaryKey();
            $params['data-value'] = $strKey;
            return $params;
        }

        /**
         * Sets up pagination for the dtgLinks datatable by initializing primary and
         * alternate paginators with labels for navigation controls and specifying
         * the number of items displayed per page. Additionally, invokes actions
         * to handle filtering of data within the table.
         *
         * @return void
         * @throws Caller
         */
        protected function createPaginators(): void
        {
            $this->dtgLinks->Paginator = new Bs\Paginator($this);
            $this->dtgLinks->Paginator->LabelForPrevious = t('Previous');
            $this->dtgLinks->Paginator->LabelForNext = t('Next');

            $this->dtgLinks->PaginatorAlternate = new Bs\Paginator($this);
            $this->dtgLinks->PaginatorAlternate->LabelForPrevious = t('Previous');
            $this->dtgLinks->PaginatorAlternate->LabelForNext = t('Next');

            $this->dtgLinks->ItemsPerPage = 10;

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
            $this->dtgLinks->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
            $this->dtgLinks->refresh();
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

            $this->dtgLinks->refresh();
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
         * Handles the event when a filter is changed, triggering the refresh of the link data grid.
         * This method updates the displayed data in the data grid to reflect the current filter criteria.
         *
         * @return void
         */
        protected function filterChanged(): void
        {
            $this->dtgLinks->refresh();
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
            $this->dtgLinks->bindData($objCondition);
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
                    QQ::like(QQN::LinksSettings()->Name, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::LinksSettings()->Title, "%" . $strSearchValue . "%"),
                    QQ::like(QQN::LinksSettings()->Author, "%" . $strSearchValue . "%")
                );
            }
        }
    }