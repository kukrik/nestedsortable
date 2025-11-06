<?php

    use QCubed as Q;
    use QCubed\Bootstrap as Bs;
    use QCubed\Control\ListBoxBase;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use Random\RandomException;
    use QCubed\Event\Change;
    use QCubed\Action\AjaxControl;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Control\ListItem;
    use QCubed\Query\QQ;


    /**
     * TemplateManager class is used to manage and configure various default templates
     * for different sections of a web application. It extends the Q\Control\Panel
     * and provides an interface to handle templates' settings and properties.
     *
     * Fields in this class include default conditions and clauses for templates,
     * UI components for managing template options, and instances that represent
     * the default state or selection of templates for respective sections.
     *
     * Features:
     * - Handles default conditions and clauses for templates.
     * - Provides labels and select controls for managing template settings.
     * - Supports modal and toastr components for user interaction and messaging.
     * - Loads and manages default templates using predefined content types and locks.
     */
    class TemplateManager extends Q\Control\Panel
    {
        protected ?object $objDefaultHomeCondition = null;
        protected ?array $objDefaultHomeClauses = null;

        protected ?object $objDefaultArticleCondition = null;
        protected ?array $objDefaultArticleClauses = null;

        protected ?object $objDefaultNewsListCondition = null;
        protected ?array $objDefaultNewsListClauses = null;

        protected ?object $objDefaultNewsCondition = null;
        protected ?array $objDefaultNewsClauses = null;

        protected ?object $objDefaultGalleryListCondition = null;
        protected ?array $objDefaultGalleryListClauses = null;

        protected ?object $objDefaultGalleryCondition = null;
        protected ?array $objDefaultGalleryClauses = null;

        protected ?object $objDefaultEventsCalendarListCondition = null;
        protected ?array $objDefaultEventsCalendarListClauses = null;

        protected ?object $objDefaultEventsCalendarCondition = null;
        protected ?array $objDefaultEventsCalendarClauses = null;

        protected ?object $objDefaultSportsCalendarListCondition = null;
        protected ?array $objDefaultSportsCalendarListClauses = null;

        protected ?object $objDefaultSportsCalendarCondition = null;
        protected ?array $objDefaultSportsCalendarClauses = null;

        protected ?object $objDefaultSportsAreasCondition = null;
        protected ?array $objDefaultSportsAreasClauses = null;

        protected ?object $objDefaultBoardCondition = null;
        protected ?array $objDefaultBoardClauses = null;

        protected ?object $objDefaultMembersCondition = null;
        protected ?array $objDefaultMembersClauses = null;

        protected ?object $objDefaultVideosCondition = null;
        protected ?array $objDefaultVideosClauses = null;

        protected ?object $objDefaultRecordsCondition = null;
        protected ?array $objDefaultRecordsClauses = null;

        protected ?object $objDefaultRankingsCondition = null;
        protected ?array $objDefaultRankingsClauses = null;

        protected ?object $objDefaultAchievementsCondition = null;
        protected ?array $objDefaultAchievementsClauses = null;

        protected ?object $objDefaultLinksCondition = null;
        protected ?array $objDefaultLinksClauses = null;

        public Bs\Modal $dlgModal1;

        protected Q\Plugin\Toastr $dlgToastr1;
        protected Q\Plugin\Toastr $dlgToastr2;

        public Q\Plugin\Control\Label $lblDefaultHomeTemplate;
        public Q\Plugin\Select2 $lstDefaultHomeTemplate;

        public Q\Plugin\Control\Label $lblDefaultArticleTemplate;
        public Q\Plugin\Select2 $lstDefaultArticleTemplate;

        public Q\Plugin\Control\Label $lblDefaultNewsListTemplate;
        public Q\Plugin\Select2 $lstDefaultNewsListTemplate;

        public Q\Plugin\Control\Label $lblDefaultNewsTemplate;
        public Q\Plugin\Select2 $lstDefaultNewsTemplate;

        public Q\Plugin\Control\Label $lblDefaultGalleryListTemplate;
        public Q\Plugin\Select2 $lstDefaultGalleryListTemplate;

        public Q\Plugin\Control\Label $lblDefaultGalleryTemplate;
        public Q\Plugin\Select2 $lstDefaultGalleryTemplate;

        public Q\Plugin\Control\Label $lblDefaultEventsCalendarListTemplate;
        public Q\Plugin\Select2 $lstDefaultEventsCalendarListTemplate;

        public Q\Plugin\Control\Label $lblDefaultEventsCalendarTemplate;
        public Q\Plugin\Select2 $lstDefaultEventsCalendarTemplate;

        public Q\Plugin\Control\Label $lblDefaultSportsCalendarListTemplate;
        public Q\Plugin\Select2 $lstDefaultSportsCalendarListTemplate;

        public Q\Plugin\Control\Label $lblDefaultSportsCalendarTemplate;
        public Q\Plugin\Select2 $lstDefaultSportsCalendarTemplate;

        public Q\Plugin\Control\Label $lblDefaultSportsAreasTemplate;
        public Q\Plugin\Select2 $lstDefaultSportsAreasTemplate;

        public Q\Plugin\Control\Label $lblDefaultBoardTemplate;
        public Q\Plugin\Select2 $lstDefaultBoardTemplate;

        public Q\Plugin\Control\Label $lblDefaultMembersTemplate;
        public Q\Plugin\Select2 $lstDefaultMembersTemplate;

        public Q\Plugin\Control\Label $lblDefaultVideosTemplate;
        public Q\Plugin\Select2 $lstDefaultVideosTemplate;

        public Q\Plugin\Control\Label $lblDefaultRecordsTemplate;
        public Q\Plugin\Select2 $lstDefaultRecordsTemplate;

        public Q\Plugin\Control\Label $lblDefaultRankingsTemplate;
        public Q\Plugin\Select2 $lstDefaultRankingsTemplate;

        public Q\Plugin\Control\Label $lblDefaultAchievementsTemplate;
        public Q\Plugin\Select2 $lstDefaultAchievementsTemplate;

        public Q\Plugin\Control\Label $lblDefaultLinksTemplate;
        public Q\Plugin\Select2 $lstDefaultLinksTemplate;

        protected object $intDefaultHome;
        protected object $intDefaultArticle;
        protected object $intDefaultNewsList;
        protected object $intDefaultNews;
        protected object $intDefaultGalleryList;
        protected object $intDefaultGallery;
        protected object $intDefaultEventsCalendarList;
        protected object $intDefaultEventsCalendar;
        protected object $intDefaultSportsCalendarList;
        protected object $intDefaultSportsCalendar;
        protected object $intDefaultSportsAreas;
        protected object $intDefaultBoard;
        protected object $intDefaultMembers;
        protected object $intDefaultVideos;
        protected object $intDefaultRecords;
        protected object $intDefaultRankings;
        protected object $intDefaultAchievements;
        protected object $intDefaultLinks;

        protected object $objDefaultHome;
        protected object $objDefaultArticle;
        protected object $objDefaultNewsList;
        protected object $objDefaultNews;
        protected object $objDefaultGalleryList;
        protected object $objDefaultGallery;
        protected object $objDefaultEventsCalendarList;
        protected object $objDefaultEventsCalendar;
        protected object $objDefaultSportsCalendarList;
        protected object $objDefaultSportsCalendar;
        protected object $objDefaultSportsAreas;
        protected object $objDefaultBoard;
        protected object $objDefaultMembers;
        protected object $objDefaultVideos;
        protected object $objDefaultRecords;
        protected object $objDefaultRankings;
        protected object $objDefaultAchievements;
        protected object $objDefaultLinks;

        protected ?int $intLoggedUserId = null;
        protected ?object $objUser = null;

        protected string $strTemplate = 'TemplateManager.tpl.php';

        /**
         * Constructs a new instance of the class.
         *
         * @param mixed $objParentObject The parent object with which this instance is associated.
         * @param string|null $strControlId Optional control ID for the instance.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller If an error occurs during construction.
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->intLoggedUserId = $_SESSION['logged_user_id'];
            $this->objUser = User::load($this->intLoggedUserId);

            $this->intDefaultHome = FrontendTemplateLocking::load(1);
            $this->intDefaultArticle = FrontendTemplateLocking::load(2);
            $this->intDefaultNewsList = FrontendTemplateLocking::load(3);
            $this->intDefaultNews = FrontendTemplateLocking::load(4);
            $this->intDefaultGalleryList = FrontendTemplateLocking::load(5);
            $this->intDefaultGallery = FrontendTemplateLocking::load(6);
            $this->intDefaultEventsCalendarList = FrontendTemplateLocking::load(7);
            $this->intDefaultEventsCalendar = FrontendTemplateLocking::load(8);
            $this->intDefaultSportsCalendarList = FrontendTemplateLocking::load(9);
            $this->intDefaultSportsCalendar = FrontendTemplateLocking::load(10);
            $this->intDefaultSportsAreas = FrontendTemplateLocking::load(11);
            $this->intDefaultBoard = FrontendTemplateLocking::load(12);
            $this->intDefaultMembers = FrontendTemplateLocking::load(13);
            $this->intDefaultVideos = FrontendTemplateLocking::load(14);
            $this->intDefaultRecords = FrontendTemplateLocking::load(15);
            $this->intDefaultRankings = FrontendTemplateLocking::load(16);
            $this->intDefaultAchievements = FrontendTemplateLocking::load(17);
            $this->intDefaultLinks = FrontendTemplateLocking::load(18);

            $this->objDefaultHome = ContentTypesManagement::load(1);
            $this->objDefaultArticle = ContentTypesManagement::load(2);
            $this->objDefaultNewsList = ContentTypesManagement::load(3);
            $this->objDefaultNews = ContentTypesManagement::load(4);
            $this->objDefaultGalleryList = ContentTypesManagement::load(5);
            $this->objDefaultGallery = ContentTypesManagement::load(6);
            $this->objDefaultEventsCalendarList = ContentTypesManagement::load(7);
            $this->objDefaultEventsCalendar = ContentTypesManagement::load(8);
            $this->objDefaultSportsCalendarList = ContentTypesManagement::load(9);
            $this->objDefaultSportsCalendar = ContentTypesManagement::load(10);
            $this->objDefaultSportsAreas = ContentTypesManagement::load(11);
            $this->objDefaultBoard = ContentTypesManagement::load(12);
            $this->objDefaultMembers = ContentTypesManagement::load(13);

            $this->objDefaultVideos = ContentTypesManagement::load(14);
            $this->objDefaultRecords = ContentTypesManagement::load(15);
            $this->objDefaultRankings = ContentTypesManagement::load(16);
            $this->objDefaultAchievements = ContentTypesManagement::load(17);
            $this->objDefaultLinks = ContentTypesManagement::load(18);

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->createHomeTemplate();
            $this->createArticleTemplate();
            $this->createNewsListTemplate();
            $this->createNewsTemplate();
            $this->createGalleryListTemplate();
            $this->createGalleryTemplate();
            $this->createEventsCalendarListTemplate();
            $this->createEventsCalendarTemplate();
            $this->createSportsCalendarListTemplate();
            $this->createSportsCalendarTemplate();
            $this->createSportsAreasTemplate();
            $this->createBoardTemplate();
            $this->createMembersTemplate();
            $this->createVideosTemplate();
            $this->createRecordsTemplate();
            $this->createRankingsTemplate();
            $this->createAchievementsTemplate();
            $this->createLinksTemplate();

            $this->createModals();
            $this->createToastr();
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
         * Initializes and configures the default home template label and selection components.
         *
         * This method sets up a label to display the text "Default home template" with specific
         * CSS styles and marks it as a required field. It also creates a dropdown selection component
         * for choosing a default template, specifying its theme, width, selection mode, and possible
         * options. The dropdown is populated dynamically and linked to handle change events with an
         * AJAX-based action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createHomeTemplate(): void
        {
            $this->lblDefaultHomeTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultHomeTemplate->Text = t('Default home template');
            $this->lblDefaultHomeTemplate->addCssClass('col-md-4');
            $this->lblDefaultHomeTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultHomeTemplate->Required = true;

            $this->lstDefaultHomeTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultHomeTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultHomeTemplate->Theme = 'web-vauu';
            $this->lstDefaultHomeTemplate->Width = '100%';
            $this->lstDefaultHomeTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultHomeTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultHomeTemplate->addItems($this->lstDefaultHomeTemplate_GetItems());
            $this->lstDefaultHomeTemplate->SelectedValue = $this->intDefaultHome->FrontendTemplateLockedId;
            $this->lstDefaultHomeTemplate->addAction(new Change(), new AjaxControl($this,'lstHomeTemplate_Change'));
        }

        /**
         * Retrieves a list of items for the default home template dropdown menu.
         *
         * This method generates an array of ListItem objects representing available templates
         * based on specific conditions. It filters templates by content type and status and
         * marks the appropriate item as selected if it matches the locked template for the
         * current default home configuration.
         *
         * @return ListItem[] Array of ListItem objects to populate the dropdown menu.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function lstDefaultHomeTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultHomeCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultHomeClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 1 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultHome->Id == $this->intDefaultHome->Id) && ($this->objDefaultHome->Id == $this->intDefaultHome->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the home template selection dropdown.
         *
         * This method processes the selection made in the home template dropdown. It verifies the CSRF token,
         * updates the default home template if a new selection is made, and updates the corresponding frontend
         * link settings. If no valid selection is made, the dropdown value is reset to its original state.
         * Notifications are shown based on the action performed.
         *
         * @param ActionParams $params The parameters related to the change action event.
         *
         * @return void
         * @throws Exception If an error occurs during the process.
         */
        public function lstHomeTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(1);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultHomeTemplate->SelectedValue);

            if ($this->intDefaultHome->FrontendTemplateLockedId && $this->lstDefaultHomeTemplate->SelectedValue) {
                $this->intDefaultHome->setFrontendTemplateLockedId($this->lstDefaultHomeTemplate->SelectedValue);
                $this->intDefaultHome->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultHomeTemplate->SelectedValue = $this->intDefaultHome->getFrontendTemplateLockedId();
                $this->lstDefaultHomeTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default article template label and selection components.
         *
         * This method sets up a label to display the text "Default article detail template" with specific
         * CSS styles and marks it as a required field. It also creates a dropdown selection component
         * for choosing a default article detail template, specifying its theme, width, selection mode,
         * and possible options. The dropdown is dynamically populated and linked to handle change events
         * with an AJAX-based action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createArticleTemplate(): void
        {
            $this->lblDefaultArticleTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultArticleTemplate->Text = t('Default article detail template');
            $this->lblDefaultArticleTemplate->addCssClass('col-md-4');
            $this->lblDefaultArticleTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultArticleTemplate->Required = true;

            $this->lstDefaultArticleTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultArticleTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultArticleTemplate->Theme = 'web-vauu';
            $this->lstDefaultArticleTemplate->Width = '100%';
            $this->lstDefaultArticleTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultArticleTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultArticleTemplate->addItems($this->lstDefaultArticleTemplate_GetItems());
            $this->lstDefaultArticleTemplate->SelectedValue = $this->intDefaultArticle->FrontendTemplateLockedId;
            $this->lstDefaultArticleTemplate->addAction(new Change(), new AjaxControl($this,'lstArticleTemplate_Change'));
        }

        /**
         * Retrieves a list of selectable items for the default article template dropdown.
         *
         * This method queries the database based on the specified conditions and clauses to retrieve
         * frontend template options. It filters the results to include only templates with a specific
         * ContentTypesManagementId and active status. The items are represented as ListItem objects,
         * with the selection state determined by predefined criteria.
         *
         * @return array An array of ListItem objects representing the available template options.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function lstDefaultArticleTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultArticleCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultArticleClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 2 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultArticle->Id == $this->intDefaultArticle->Id) && ($this->objDefaultArticle->Id == $this->intDefaultArticle->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the article template selection dropdown.
         *
         * This method verifies the CSRF token for security before proceeding. If the token is invalid,
         * it displays a notification dialog and regenerates the token. Otherwise, it updates the locked
         * template ID for the default article and saves the changes. If the dropdown selection is valid,
         * it iterates through the associated frontend links to update their class names and template paths
         * based on the selected frontend options. Appropriate notifications are triggered upon success or failure.
         *
         * @param ActionParams $params Encapsulates parameters related to the action that triggered the change event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstArticleTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(2);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultArticleTemplate->SelectedValue);

            if ($this->intDefaultArticle->FrontendTemplateLockedId && $this->lstDefaultArticleTemplate->SelectedValue) {
                $this->intDefaultArticle->setFrontendTemplateLockedId($this->lstDefaultArticleTemplate->SelectedValue);
                $this->intDefaultArticle->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultArticleTemplate->SelectedValue = $this->intDefaultArticle->getFrontendTemplateLockedId();
                $this->lstDefaultArticleTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default news list template label and selection components.
         *
         * This method sets up a label to display the text "Default news list template" with specific
         * CSS styles and marks it as a required field. It also creates a dropdown selection component
         * for choosing a default news list template, specifying its theme, width, selection mode, and
         * possible options. The dropdown is populated dynamically and linked to handle change events
         * with an AJAX-based action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createNewsListTemplate(): void
        {
            $this->lblDefaultNewsListTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultNewsListTemplate->Text = t('Default news list template');
            $this->lblDefaultNewsListTemplate->addCssClass('col-md-4');
            $this->lblDefaultNewsListTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultNewsListTemplate->Required = true;

            $this->lstDefaultNewsListTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultNewsListTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultNewsListTemplate->Theme = 'web-vauu';
            $this->lstDefaultNewsListTemplate->Width = '100%';
            $this->lstDefaultNewsListTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultNewsListTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultNewsListTemplate->addItems($this->lstDefaultNewsListTemplate_GetItems());
            $this->lstDefaultNewsListTemplate->SelectedValue = $this->intDefaultNewsList->FrontendTemplateLockedId;
            $this->lstDefaultNewsListTemplate->addAction(new Change(), new AjaxControl($this,'lstNewsListTemplate_Change'));
        }

        /**
         * Retrieves a list of items to be displayed in the default news list template selection component.
         *
         * This method uses the specified conditions to query available options for the news list template.
         * It iterates through the results, filtering items based on content type and status. Eligible items
         * are added to the list and marked as selected if they match the current selection criteria.
         *
         * @return array The array of ListItem objects representing the selectable options for the news list template.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function lstDefaultNewsListTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultNewsListCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultNewsListCondition);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 3 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultNewsList->Id == $this->intDefaultNewsList->Id) && ($this->objDefaultNewsList->Id == $this->intDefaultNewsList->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the news list template selection dropdown.
         *
         * This method verifies the CSRF token for security purposes and updates the selected
         * news list template for the associated content along with its frontend links. If the
         * selected value is updated successfully, the changes are saved and a notification is
         * displayed. Otherwise, the dropdown is refreshed to its previous state, and an alternate
         * notification is shown.
         *
         * @param ActionParams $params Action parameters containing event-specific data.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstNewsListTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(3);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultNewsListTemplate->SelectedValue);

            if ($this->intDefaultNewsList->FrontendTemplateLockedId && $this->lstDefaultNewsListTemplate->SelectedValue) {
                $this->intDefaultNewsList->setFrontendTemplateLockedId($this->lstDefaultNewsListTemplate->SelectedValue);
                $this->intDefaultNewsList->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultNewsListTemplate->SelectedValue = $this->intDefaultNewsList->getFrontendTemplateLockedId();
                $this->lstDefaultNewsListTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default news template label and selection components.
         *
         * This method defines a label for displaying the text "Default news detail template" with specific
         * styling and sets it as a required field. It also creates a dropdown selection component
         * for assigning a default news template, with specified visual and interactive configurations
         * including a theme, width, and selection behavior. The dropdown is dynamically populated with
         * available options and wired to handle change events through an AJAX-based action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createNewsTemplate(): void
        {
            $this->lblDefaultNewsTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultNewsTemplate->Text = t('Default news detail template');
            $this->lblDefaultNewsTemplate->addCssClass('col-md-4');
            $this->lblDefaultNewsTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultNewsTemplate->Required = true;

            $this->lstDefaultNewsTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultNewsTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultNewsTemplate->Theme = 'web-vauu';
            $this->lstDefaultNewsTemplate->Width = '100%';
            $this->lstDefaultNewsTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultNewsTemplate->addItem(t('- Select one type -'), null, true);
            $this->lstDefaultNewsTemplate->addItems($this->lstDefaultNewsTemplate_GetItems());
            $this->lstDefaultNewsTemplate->SelectedValue = $this->intDefaultNews->FrontendTemplateLockedId;
            $this->lstDefaultNewsTemplate->addAction(new Change(), new AjaxControl($this,'lstNewsTemplate_Change'));
        }

        /**
         * Retrieves a list of items to populate the default news template selection component.
         *
         * This method queries the database for frontend options based on a specified condition,
         * iterates through the resulting cursor, and filters the data to include only items
         * meeting specific criteria (e.g., content type and status). For each qualifying entry,
         * it creates a `ListItem` object, marking it as selected if it meets the necessary
         * selection conditions, and appends it to the returned array.
         *
         * @return array The array of `ListItem` objects representing the valid options for the default news template.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstDefaultNewsTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultNewsCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultNewsClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 4 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultNews->Id == $this->intDefaultNews->Id) && ($this->objDefaultNews->Id == $this->intDefaultNews->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event of the news template dropdown.
         *
         * This method performs security checks using CSRF tokens and manages the update of
         * news-related template configurations. It updates the selected template in the database,
         * alongside associated frontend link entries if applicable. Notifications are displayed
         * based on the operation results, and the dropdown is refreshed if an invalid selection
         * occurs.
         *
         * @param ActionParams $params Parameters associated with the action triggering this method.
         *
         * @return void
         * @throws Exception If an error occurs during template loading or saving operations.
         */
        public function lstNewsTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(4);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultNewsTemplate->SelectedValue);

            if ($this->intDefaultNews->FrontendTemplateLockedId && $this->lstDefaultNewsTemplate->SelectedValue) {
                $this->intDefaultNews->setFrontendTemplateLockedId($this->lstDefaultNewsTemplate->SelectedValue);
                $this->intDefaultNews->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultNewsTemplate->SelectedValue = $this->intDefaultNews->getFrontendTemplateLockedId();
                $this->lstDefaultNewsTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default gallery list template label and selection components.
         *
         * This method sets up a label to display the text "Default gallery list template" with specific
         * CSS styles and marks it as a required field. It also creates a dropdown selection component
         * for choosing a default gallery list template, specifying its theme, width, selection mode,
         * and possible options. The dropdown is populated dynamically and linked to handle change events
         * with an AJAX-based action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createGalleryListTemplate(): void
        {
            $this->lblDefaultGalleryListTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultGalleryListTemplate->Text = t('Default gallery list template');
            $this->lblDefaultGalleryListTemplate->addCssClass('col-md-4');
            $this->lblDefaultGalleryListTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultGalleryListTemplate->Required = true;

            $this->lstDefaultGalleryListTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultGalleryListTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultGalleryListTemplate->Theme = 'web-vauu';
            $this->lstDefaultGalleryListTemplate->Width = '100%';
            $this->lstDefaultGalleryListTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultGalleryListTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultGalleryListTemplate->addItems($this->lstDefaultGalleryListTemplate_GetItems());
            $this->lstDefaultGalleryListTemplate->SelectedValue = $this->intDefaultGalleryList->FrontendTemplateLockedId;
            $this->lstDefaultGalleryListTemplate->addAction(new Change(), new AjaxControl($this,'lstGalleryListTemplate_Change'));
        }

        /**
         * Retrieves and constructs a list of available gallery templates suitable for the default gallery list
         * selection.
         *
         * This method queries the `FrontendOptions` table based on a specified condition and iterates through
         * the resulting dataset to filter and transform relevant records into `ListItem` objects. Only entries
         * meeting specific criteria (e.g., having a `ContentTypesManagementId` of 5 and an active `Status`) are added
         * to the resulting list. If an entry matches the currently selected default gallery list configuration, it is
         * marked as selected.
         *
         * @return array An array of `ListItem` objects representing the filtered and formatted options for the gallery
         *     list.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function lstDefaultGalleryListTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultGalleryListCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultGalleryListCondition);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 5 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultGalleryList->Id == $this->intDefaultGalleryList->Id) && ($this->objDefaultGalleryList->Id == $this->intDefaultGalleryList->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles changes made to the gallery list template selection.
         *
         * This method verifies the CSRF token for security before proceeding with the logic. It checks if
         * a new gallery list template is selected and updates the default gallery list's locked template ID.
         * The method dynamically updates associated frontend links with the class name and template path of
         * the selected template. Notifications are triggered to inform the user about the operation success
         * or failure, and the list selection is refreshed if the operation cannot proceed.
         *
         * @param ActionParams $params The parameters associated with the change event.
         *
         * @return void
         * @throws Exception If an error occurs when processing the update or saving changes.
         */
        public function lstGalleryListTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(5);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultGalleryListTemplate->SelectedValue);

            if ($this->intDefaultGalleryList->FrontendTemplateLockedId && $this->lstDefaultGalleryListTemplate->SelectedValue) {
                $this->intDefaultGalleryList->setFrontendTemplateLockedId($this->lstDefaultGalleryListTemplate->SelectedValue);
                $this->intDefaultGalleryList->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultGalleryListTemplate->SelectedValue = $this->intDefaultGalleryList->getFrontendTemplateLockedId();
                $this->lstDefaultGalleryListTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default gallery template label and selection components.
         *
         * This method sets up a label to display the text "Default gallery detail template" with
         * specific CSS styles and marks it as a required field. It also creates a dropdown selection
         * component for choosing a default gallery template, specifying its theme, width, selection
         * mode, and available options. The dropdown is dynamically populated and linked to handle
         * change events using an AJAX-based action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createGalleryTemplate(): void
        {
            $this->lblDefaultGalleryTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultGalleryTemplate->Text = t('Default gallery detail template');
            $this->lblDefaultGalleryTemplate->addCssClass('col-md-4');
            $this->lblDefaultGalleryTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultGalleryTemplate->Required = true;

            $this->lstDefaultGalleryTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultGalleryTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultGalleryTemplate->Theme = 'web-vauu';
            $this->lstDefaultGalleryTemplate->Width = '100%';
            $this->lstDefaultGalleryTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultGalleryTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultGalleryTemplate->addItems($this->lstDefaultGalleryTemplate_GetItems());
            $this->lstDefaultGalleryTemplate->SelectedValue = $this->intDefaultGallery->FrontendTemplateLockedId;
            $this->lstDefaultGalleryTemplate->addAction(new Change(), new AjaxControl($this,'lstGalleryTemplate_Change'));
        }

        /**
         * Retrieves a list of items for populating the default gallery template selection component.
         *
         * This method dynamically fetches and filters a list of frontend options based on specific conditions.
         * The options are filtered to include only those that meet the required content type and status criteria.
         * Each valid option is instantiated as a ListItem object, and selected status is applied where applicable.
         *
         * @return array The list of ListItem objects representing the filtered gallery template options.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstDefaultGalleryTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultGalleryCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultGalleryCondition);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 6 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultGallery->Id == $this->intDefaultGallery->Id) && ($this->objDefaultGallery->Id == $this->intDefaultGallery->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the gallery template selection list and updates related configurations.
         *
         * This method validates the CSRF token to ensure the request's authenticity. If the token verification fails,
         * it displays a modal dialog box, regenerates the token, and terminates further execution. Upon successful
         * verification, it processes the selected gallery template, updating the `FrontendTemplateLockedId` of
         * the gallery and saving the affected changes. Additionally, it propagates template updates to associated
         * frontend links based on the selected template's options like class name and template path. Notifications
         * are triggered at various stages to notify users of actions performed.
         *
         * @param ActionParams $params Parameters associated with the change action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstGalleryTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(6);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultGalleryTemplate->SelectedValue);

            if ($this->intDefaultGallery->FrontendTemplateLockedId && $this->lstDefaultGalleryTemplate->SelectedValue) {
                $this->intDefaultGallery->setFrontendTemplateLockedId($this->lstDefaultGalleryTemplate->SelectedValue);
                $this->intDefaultGallery->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultGalleryTemplate->SelectedValue = $this->intDefaultGallery->getFrontendTemplateLockedId();
                $this->lstDefaultGalleryTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default events calendar list template.
         * Sets up a label and a select dropdown with various properties and associated actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createEventsCalendarListTemplate(): void
        {
            $this->lblDefaultEventsCalendarListTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultEventsCalendarListTemplate->Text = t('Default events calendar list template');
            $this->lblDefaultEventsCalendarListTemplate->addCssClass('col-md-4');
            $this->lblDefaultEventsCalendarListTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultEventsCalendarListTemplate->Required = true;

            $this->lstDefaultEventsCalendarListTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultEventsCalendarListTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultEventsCalendarListTemplate->Theme = 'web-vauu';
            $this->lstDefaultEventsCalendarListTemplate->Width = '100%';
            $this->lstDefaultEventsCalendarListTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultEventsCalendarListTemplate->addItem(t('- Select one type -'), null, true);
            $this->lstDefaultEventsCalendarListTemplate->addItems($this->lstDefaultEventsCalendarListTemplate_GetItems());
            $this->lstDefaultEventsCalendarListTemplate->SelectedValue = $this->intDefaultEventsCalendarList->FrontendTemplateLockedId;
            $this->lstDefaultEventsCalendarListTemplate->addAction(new Change(), new AjaxControl($this,'lstEventsCalendarListTemplate_Change'));
        }

        /**
         * Fetches and returns a list of items for the default events calendar list template.
         * This method queries and filters available frontend templates based on specified conditions
         * and constructs an array of eligible list items for selection.
         *
         * @return array An array of ListItem objects that match the specified conditions.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstDefaultEventsCalendarListTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultEventsCalendarListCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultEventsCalendarListClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 7 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultEventsCalendarList->Id == $this->intDefaultEventsCalendarList->Id) && ($this->objDefaultEventsCalendarList->Id == $this->intDefaultEventsCalendarList->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles change event for the events calendar list template dropdown.
         * Verifies CSRF token, updates the associated frontend template and links
         * based on the selected value, and triggers corresponding notifications.
         *
         * @param ActionParams $params Parameters related to the action event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstEventsCalendarListTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(7);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultEventsCalendarListTemplate->SelectedValue);

            if ($this->intDefaultEventsCalendarList->FrontendTemplateLockedId && $this->lstDefaultEventsCalendarListTemplate->SelectedValue) {
                $this->intDefaultEventsCalendarList->setFrontendTemplateLockedId($this->lstDefaultEventsCalendarListTemplate->SelectedValue);
                $this->intDefaultEventsCalendarList->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultEventsCalendarListTemplate->SelectedValue = $this->intDefaultEventsCalendarList->getFrontendTemplateLockedId();
                $this->lstDefaultEventsCalendarListTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default events calendar detail template.
         * Sets up a label and a select dropdown with specific properties, items, and associated actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createEventsCalendarTemplate(): void
        {
            $this->lblDefaultEventsCalendarTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultEventsCalendarTemplate->Text = t('Default events calendar detail template');
            $this->lblDefaultEventsCalendarTemplate->addCssClass('col-md-4');
            $this->lblDefaultEventsCalendarTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultEventsCalendarTemplate->Required = true;

            $this->lstDefaultEventsCalendarTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultEventsCalendarTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultEventsCalendarTemplate->Theme = 'web-vauu';
            $this->lstDefaultEventsCalendarTemplate->Width = '100%';
            $this->lstDefaultEventsCalendarTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultEventsCalendarTemplate->addItem(t('- Select one type -'), null, true);
            $this->lstDefaultEventsCalendarTemplate->addItems($this->lstDefaultEventsCalendarTemplate_GetItems());
            $this->lstDefaultEventsCalendarTemplate->SelectedValue = $this->intDefaultEventsCalendar->FrontendTemplateLockedId;
            $this->lstDefaultEventsCalendarTemplate->addAction(new Change(), new AjaxControl($this,'lstEventsCalendarTemplate_Change'));
        }

        /**
         * Retrieves a list of items for the default events calendar template dropdown.
         * Iterates through a cursor to fetch and filter frontend template options based
         * on specific conditions such as content type and status. Constructs a list of
         * items with the appropriate selection state.
         *
         * @return array Returns an array of ListItem objects representing the filtered
         *               and formatted items for the dropdown.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstDefaultEventsCalendarTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultEventsCalendarCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultEventsCalendarClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 8 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultEventsCalendar->Id == $this->intDefaultEventsCalendar->Id) && ($this->objDefaultEventsCalendar->Id == $this->intDefaultEventsCalendar->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles changes to the events calendar template dropdown, updating the associated backend data
         * and frontend configurations based on the user's selection. Manages CSRF validation and provides
         * feedback to the user via notification dialogs.
         *
         * @param ActionParams $params The parameters provided by the action triggering this method.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstEventsCalendarTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(8);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultEventsCalendarTemplate->SelectedValue);

            if ($this->intDefaultEventsCalendar->FrontendTemplateLockedId && $this->lstDefaultEventsCalendarTemplate->SelectedValue) {
                $this->intDefaultEventsCalendar->setFrontendTemplateLockedId($this->lstDefaultEventsCalendarTemplate->SelectedValue);
                $this->intDefaultEventsCalendar->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultEventsCalendarTemplate->SelectedValue = $this->intDefaultEventsCalendar->getFrontendTemplateLockedId();
                $this->lstDefaultEventsCalendarTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default sports calendar list template.
         * Sets up a label and a select dropdown with various properties and associated actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createSportsCalendarListTemplate(): void
        {
            $this->lblDefaultSportsCalendarListTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultSportsCalendarListTemplate->Text = t('Default sports calendar list template');
            $this->lblDefaultSportsCalendarListTemplate->addCssClass('col-md-4');
            $this->lblDefaultSportsCalendarListTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultSportsCalendarListTemplate->Required = true;

            $this->lstDefaultSportsCalendarListTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultSportsCalendarListTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultSportsCalendarListTemplate->Theme = 'web-vauu';
            $this->lstDefaultSportsCalendarListTemplate->Width = '100%';
            $this->lstDefaultSportsCalendarListTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultSportsCalendarListTemplate->addItem(t('- Select one type -'), null, true);
            $this->lstDefaultSportsCalendarListTemplate->addItems($this->lstDefaultSportsCalendarListTemplate_GetItems());
            $this->lstDefaultSportsCalendarListTemplate->SelectedValue = $this->intDefaultSportsCalendarList->FrontendTemplateLockedId;
            $this->lstDefaultSportsCalendarListTemplate->addAction(new Change(), new AjaxControl($this,'lstSportsCalendarListTemplate_Change'));
        }

        /**
         * Retrieves a list of items for the default sports calendar list template
         * based on specified conditions. Filters and includes only the relevant
         * frontend options matching the criteria.
         *
         * @return array An array of ListItem objects representing the items for the template.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstDefaultSportsCalendarListTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultSportsCalendarListCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultSportsCalendarListClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 9 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultSportsCalendarList->Id == $this->intDefaultSportsCalendarList->Id) && ($this->objDefaultSportsCalendarList->Id == $this->intDefaultSportsCalendarList->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the sports calendar list template.
         * Validates the CSRF token, updates frontend configurations based on the
         * selected template, and displays appropriate notifications.
         *
         * @param ActionParams $params Parameters containing information about the triggered action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstSportsCalendarListTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(9);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultSportsCalendarListTemplate->SelectedValue);

            if ($this->intDefaultSportsCalendarList->FrontendTemplateLockedId && $this->lstDefaultSportsCalendarListTemplate->SelectedValue) {
                $this->intDefaultSportsCalendarList->setFrontendTemplateLockedId($this->lstDefaultSportsCalendarListTemplate->SelectedValue);
                $this->intDefaultSportsCalendarList->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultSportsCalendarListTemplate->SelectedValue = $this->intDefaultSportsCalendarList->getFrontendTemplateLockedId();
                $this->lstDefaultSportsCalendarListTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default sports calendar template.
         * Sets up a label and a select dropdown with various properties and associated actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createSportsCalendarTemplate(): void
        {
            $this->lblDefaultSportsCalendarTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultSportsCalendarTemplate->Text = t('Default sports calendar detail template');
            $this->lblDefaultSportsCalendarTemplate->addCssClass('col-md-4');
            $this->lblDefaultSportsCalendarTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultSportsCalendarTemplate->Required = true;

            $this->lstDefaultSportsCalendarTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultSportsCalendarTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultSportsCalendarTemplate->Theme = 'web-vauu';
            $this->lstDefaultSportsCalendarTemplate->Width = '100%';
            $this->lstDefaultSportsCalendarTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultSportsCalendarTemplate->addItem(t('- Select one type -'), null, true);
            $this->lstDefaultSportsCalendarTemplate->addItems($this->lstDefaultSportsCalendarTemplate_GetItems());
            $this->lstDefaultSportsCalendarTemplate->SelectedValue = $this->intDefaultSportsCalendar->FrontendTemplateLockedId;
            $this->lstDefaultSportsCalendarTemplate->addAction(new Change(), new AjaxControl($this,'lstSportsCalendarTemplate_Change'));
        }

        /**
         * Retrieves a list of items for the default sports calendar template dropdown.
         * Filters and processes the available frontend options based on predefined conditions
         * and returns an array of list items for use in the dropdown.
         *
         * @return array The array of ListItem objects to populate the dropdown.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function lstDefaultSportsCalendarTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultSportsCalendarCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultSportsCalendarClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 10 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultSportsCalendar->Id == $this->intDefaultSportsCalendar->Id) && ($this->objDefaultSportsCalendar->Id == $this->intDefaultSportsCalendar->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the sports calendar template selection dropdown.
         * Ensures CSRF token validation before processing the change and updates the template-related settings
         * accordingly. Also notifies the user through toastr dialogs upon success or when reverting changes.
         *
         * @param ActionParams $params Parameters passed by the triggering event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstSportsCalendarTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(8);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultSportsCalendarTemplate->SelectedValue);

            if ($this->intDefaultSportsCalendar->FrontendTemplateLockedId && $this->lstDefaultSportsCalendarTemplate->SelectedValue) {
                $this->intDefaultSportsCalendar->setFrontendTemplateLockedId($this->lstDefaultSportsCalendarTemplate->SelectedValue);
                $this->intDefaultSportsCalendar->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultSportsCalendarTemplate->SelectedValue = $this->intDefaultSportsCalendar->getFrontendTemplateLockedId();
                $this->lstDefaultSportsCalendarTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Configures and sets up the default sports areas detail template.
         * Initializes a label and a select dropdown with specific properties and actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createSportsAreasTemplate(): void
        {
            $this->lblDefaultSportsAreasTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultSportsAreasTemplate->Text = t('Default sports areas detail template');
            $this->lblDefaultSportsAreasTemplate->addCssClass('col-md-4');
            $this->lblDefaultSportsAreasTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultSportsAreasTemplate->Required = true;

            $this->lstDefaultSportsAreasTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultSportsAreasTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultSportsAreasTemplate->Theme = 'web-vauu';
            $this->lstDefaultSportsAreasTemplate->Width = '100%';
            $this->lstDefaultSportsAreasTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultSportsAreasTemplate->addItem(t('- Select one type -'), null, true);
            $this->lstDefaultSportsAreasTemplate->addItems($this->lstDefaultSportsAreasTemplate_GetItems());
            $this->lstDefaultSportsAreasTemplate->SelectedValue = $this->intDefaultSportsAreas->FrontendTemplateLockedId;
            $this->lstDefaultSportsAreasTemplate->addAction(new Change(), new AjaxControl($this,'lstSportsAreasTemplate_Change'));
        }

        /**
         * Retrieves the items for the default sports areas template dropdown.
         * Filters and creates a list of items that meet specific criteria, such as content type and status,
         * and includes logic to set a selected item based on predefined conditions.
         *
         * @return array An array of ListItem objects populated for the default sports areas template.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function lstDefaultSportsAreasTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultSportsAreasCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultSportsAreasClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 11 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultSportsAreas->Id == $this->intDefaultSportsAreas->Id) && ($this->objDefaultSportsAreas->Id == $this->intDefaultSportsAreas->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the sports areas template dropdown.
         * Verifies CSRF token, updates the default sports areas template, and propagates the changes to related frontend links.
         * Displays appropriate notifications based on the success or failure of the operation.
         *
         * @param ActionParams $params The parameters passed with the action event.
         *
         * @return void
         * @throws Exception If CSRF token validation fails or errors occur during processing.
         */
        public function lstSportsAreasTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(11);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultSportsAreasTemplate->SelectedValue);

            if ($this->intDefaultSportsAreas->FrontendTemplateLockedId && $this->lstDefaultSportsAreasTemplate->SelectedValue) {
                $this->intDefaultSportsAreas->setFrontendTemplateLockedId($this->lstDefaultSportsAreasTemplate->SelectedValue);
                $this->intDefaultSportsAreas->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultSportsAreasTemplate->SelectedValue = $this->intDefaultSportsAreas->getFrontendTemplateLockedId();
                $this->lstDefaultSportsAreasTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default board detail template.
         * Sets up a label and a select dropdown with various properties and associated actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createBoardTemplate(): void
        {
            $this->lblDefaultBoardTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultBoardTemplate->Text = t('Default board detail template');
            $this->lblDefaultBoardTemplate->addCssClass('col-md-4');
            $this->lblDefaultBoardTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultBoardTemplate->Required = true;

            $this->lstDefaultBoardTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultBoardTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultBoardTemplate->Theme = 'web-vauu';
            $this->lstDefaultBoardTemplate->Width = '100%';
            $this->lstDefaultBoardTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultBoardTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultBoardTemplate->addItems($this->lstDefaultBoardTemplate_GetItems());
            $this->lstDefaultBoardTemplate->SelectedValue = $this->intDefaultBoard->FrontendTemplateLockedId;
            $this->lstDefaultBoardTemplate->addAction(new Change(), new AjaxControl($this,'lstBoardTemplate_Change'));
        }

        /**
         * Retrieves an array of list items for the default board template dropdown.
         * Filters and processes FrontendOptions record based on the specified conditions
         * and include only those that meet the given requirements.
         *
         * @return array The array of ListItem objects representing the available board templates.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function lstDefaultBoardTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultBoardCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultBoardClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 12 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultBoard->Id == $this->intDefaultBoard->Id) && ($this->objDefaultBoard->Id == $this->intDefaultBoard->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the board template dropdown.
         * Validates the CSRF token, updates the frontend template settings for the selected board,
         * and notifies the user with appropriate messages upon success or failure.
         *
         * @param ActionParams $params The action parameters associated with the change event.
         *
         * @return void
         * @throws Exception
         */
        public function lstBoardTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(11);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultBoardTemplate->SelectedValue);

            if ($this->intDefaultBoard->FrontendTemplateLockedId && $this->lstDefaultBoardTemplate->SelectedValue) {
                $this->intDefaultBoard->setFrontendTemplateLockedId($this->lstDefaultBoardTemplate->SelectedValue);
                $this->intDefaultBoard->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultBoardTemplate->SelectedValue = $this->intDefaultBoard->getFrontendTemplateLockedId();
                $this->lstDefaultBoardTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }


        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default member detail template.
         * Sets up a label and a select dropdown with relevant properties and actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createMembersTemplate(): void
        {
            $this->lblDefaultMembersTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultMembersTemplate->Text = t('Default members detail template');
            $this->lblDefaultMembersTemplate->addCssClass('col-md-4');
            $this->lblDefaultMembersTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultMembersTemplate->Required = true;

            $this->lstDefaultMembersTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultMembersTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultMembersTemplate->Theme = 'web-vauu';
            $this->lstDefaultMembersTemplate->Width = '100%';
            $this->lstDefaultMembersTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultMembersTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultMembersTemplate->addItems($this->lstDefaultMembersTemplate_GetItems());
            $this->lstDefaultMembersTemplate->SelectedValue = $this->intDefaultMembers->FrontendTemplateLockedId;
            $this->lstDefaultMembersTemplate->addAction(new Change(), new AjaxControl($this,'lstMembersTemplate_Change'));
        }

        /**
         * Retrieves a list of items for the default members template based on specific conditions.
         * Filters the items by verifying content type and status before adding them to the returned list.
         *
         * @return array The list of filtered items populated as ListItem objects, prepared for selection.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function lstDefaultMembersTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultMembersCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultMembersClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 13 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultMembers->Id == $this->intDefaultMembers->Id) && ($this->objDefaultMembers->Id == $this->intDefaultMembers->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the member template dropdown.
         * Validates CSRF token, updates frontend template configuration, and notifies the user of the result.
         *
         * @param ActionParams $params The parameters associated with the triggered action.
         *
         * @return void
         * @throws Exception
         */
        public function lstMembersTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(13);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultMembersTemplate->SelectedValue);

            if ($this->intDefaultMembers->FrontendTemplateLockedId && $this->lstDefaultMembersTemplate->SelectedValue) {
                $this->intDefaultMembers->setFrontendTemplateLockedId($this->lstDefaultMembersTemplate->SelectedValue);
                $this->intDefaultMembers->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultMembersTemplate->SelectedValue = $this->intDefaultMembers->getFrontendTemplateLockedId();
                $this->lstDefaultMembersTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default video detail template.
         * Creates a label and a select dropdown with specified settings, properties, and actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createVideosTemplate(): void
        {
            $this->lblDefaultVideosTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultVideosTemplate->Text = t('Default videos detail template');
            $this->lblDefaultVideosTemplate->addCssClass('col-md-4');
            $this->lblDefaultVideosTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultVideosTemplate->Required = true;

            $this->lstDefaultVideosTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultVideosTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultVideosTemplate->Theme = 'web-vauu';
            $this->lstDefaultVideosTemplate->Width = '100%';
            $this->lstDefaultVideosTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultVideosTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultVideosTemplate->addItems($this->lstDefaultVideosTemplate_GetItems());
            $this->lstDefaultVideosTemplate->SelectedValue = $this->intDefaultVideos->FrontendTemplateLockedId;
            $this->lstDefaultVideosTemplate->addAction(new Change(), new AjaxControl($this,'lstVideosTemplate_Change'));
        }

        /**
         * Retrieves a list of items for the default videos template based on specific conditions.
         * Filters frontend options with defined conditions and appends valid items to the list.
         *
         * @return array The array of ListItem objects that meet the predefined conditions.
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        public function lstDefaultVideosTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultVideosCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultVideosClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 14 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultVideos->Id == $this->intDefaultVideos->Id) && ($this->objDefaultVideos->Id == $this->intDefaultVideos->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the video template selection dropdown.
         * Verifies CSRF token validity, updates the frontend template lock ID, and propagates new template settings to associated frontend links.
         * Displays notifications based on the success or failure of the operation.
         *
         * @param ActionParams $params The parameters passed by the action triggering the event.
         *
         * @return void
         * @throws Exception
         */
        public function lstVideosTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(14);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultVideosTemplate->SelectedValue);

            if ($this->intDefaultVideos->FrontendTemplateLockedId && $this->lstDefaultVideosTemplate->SelectedValue) {
                $this->intDefaultVideos->setFrontendTemplateLockedId($this->lstDefaultVideosTemplate->SelectedValue);
                $this->intDefaultVideos->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultVideosTemplate->SelectedValue = $this->intDefaultVideos->getFrontendTemplateLockedId();
                $this->lstDefaultVideosTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default records template.
         * Sets up a label and a select dropdown with specified properties and associated actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createRecordsTemplate(): void
        {
            $this->lblDefaultRecordsTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultRecordsTemplate->Text = t('Default records template');
            $this->lblDefaultRecordsTemplate->addCssClass('col-md-4');
            $this->lblDefaultRecordsTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultRecordsTemplate->Required = true;

            $this->lstDefaultRecordsTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultRecordsTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultRecordsTemplate->Theme = 'web-vauu';
            $this->lstDefaultRecordsTemplate->Width = '100%';
            $this->lstDefaultRecordsTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultRecordsTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultRecordsTemplate->addItems($this->lstDefaultRecordsTemplate_GetItems());
            $this->lstDefaultRecordsTemplate->SelectedValue = $this->intDefaultRecords->FrontendTemplateLockedId;
            $this->lstDefaultRecordsTemplate->addAction(new Change(), new AjaxControl($this,'lstRecordsTemplate_Change'));
        }

        /**
         * Retrieves a list of default records template items based on specific conditions.
         * The method filters records using predefined criteria, constructs a list of items,
         * and marks specific items as selected if applicable.
         *
         * @return ListItem[] An array of ListItem objects that meet the specified conditions.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstDefaultRecordsTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultRecordsCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultRecordsClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 15 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultRecords->Id == $this->intDefaultRecords->Id) && ($this->objDefaultRecords->Id == $this->intDefaultRecords->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the record template selection dropdown.
         * Verifies CSRF token, updates the default records frontend template, and adjusts associated frontend settings.
         * Displays notifications and manages the state accordingly.
         *
         * @param ActionParams $params Parameters related to the triggered action, such as event context and target controls.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstRecordsTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultRecordsTemplate->SelectedValue);
            $objFrontend = FrontendLinks::loadByIdFromContentTypesManagamentId(15);

            if ($this->intDefaultRecords->FrontendTemplateLockedId && $this->lstDefaultRecordsTemplate->SelectedValue) {
                $this->intDefaultRecords->setFrontendTemplateLockedId($this->lstDefaultRecordsTemplate->SelectedValue);
                $this->intDefaultRecords->save();

                $objFrontendLink = FrontendLinks::loadById($objFrontend->Id);
                $objFrontendLink->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLink->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLink->save();

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultRecordsTemplate->SelectedValue = $this->intDefaultRecords->getFrontendTemplateLockedId();
                $this->lstDefaultRecordsTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default rankings template.
         * Sets up a label and a select dropdown with specific properties, options, and associated actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createRankingsTemplate(): void
        {
            $this->lblDefaultRankingsTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultRankingsTemplate->Text = t('Default rankings template');
            $this->lblDefaultRankingsTemplate->addCssClass('col-md-4');
            $this->lblDefaultRankingsTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultRankingsTemplate->Required = true;

            $this->lstDefaultRankingsTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultRankingsTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultRankingsTemplate->Theme = 'web-vauu';
            $this->lstDefaultRankingsTemplate->Width = '100%';
            $this->lstDefaultRankingsTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultRankingsTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultRankingsTemplate->addItems($this->lstDefaultRankingsTemplate_GetItems());
            $this->lstDefaultRankingsTemplate->SelectedValue = $this->intDefaultRankings->FrontendTemplateLockedId;
            $this->lstDefaultRankingsTemplate->addAction(new Change(), new AjaxControl($this,'lstRankingTemplate_Change'));
        }

        /**
         * Retrieves and constructs a list of selectable items for the default rankings template.
         * Filters and processes items based on specific conditions and includes only active entries
         * that meet the defined criteria.
         *
         * @return ListItem[] An array of ListItem objects representing the selectable default rankings templates.
         * @throws Caller If an issue with calling a necessary condition or property occurs.
         * @throws InvalidCast If type casting fails during data retrieval or processing.
         * @throws DateMalformedStringException
         */
        public function lstDefaultRankingsTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultRankingsCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultRankingsClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 16 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultRankings->Id == $this->intDefaultRankings->Id) && ($this->objDefaultRankings->Id == $this->intDefaultRankings->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles changes to the ranking template dropdown.
         * Verifies CSRF token, updates relevant frontend template and links,
         * and triggers appropriate notifications or resets based on selection.
         *
         * @param ActionParams $params The parameters associated with the action, typically passed by the event handler.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstRankingTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultRecordsTemplate->SelectedValue);
            $objFrontend = FrontendLinks::loadByIdFromContentTypesManagamentId(16);

            if ($this->intDefaultRecords->FrontendTemplateLockedId && $this->lstDefaultRecordsTemplate->SelectedValue) {
                $this->intDefaultRecords->setFrontendTemplateLockedId($this->lstDefaultRecordsTemplate->SelectedValue);
                $this->intDefaultRecords->save();

                $objFrontendLink = FrontendLinks::loadById($objFrontend->Id);
                $objFrontendLink->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLink->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLink->save();

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultRecordsTemplate->SelectedValue = $this->intDefaultRecords->getFrontendTemplateLockedId();
                $this->lstDefaultRecordsTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and configures the default achievements template.
         * Sets up a label and a select dropdown with various properties and associated actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createAchievementsTemplate(): void
        {
            $this->lblDefaultAchievementsTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultAchievementsTemplate->Text = t('Default achievements template');
            $this->lblDefaultAchievementsTemplate->addCssClass('col-md-4');
            $this->lblDefaultAchievementsTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultAchievementsTemplate->Required = true;

            $this->lstDefaultAchievementsTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultAchievementsTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultAchievementsTemplate->Theme = 'web-vauu';
            $this->lstDefaultAchievementsTemplate->Width = '100%';
            $this->lstDefaultAchievementsTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultAchievementsTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultAchievementsTemplate->addItems($this->lstDefaultAchievementsTemplate_GetItems());
            $this->lstDefaultAchievementsTemplate->SelectedValue = $this->intDefaultAchievements->FrontendTemplateLockedId;
            $this->lstDefaultAchievementsTemplate->addAction(new Change(), new AjaxControl($this,'lstAchievementsTemplate_Change'));
        }

        /**
         * Retrieves a list of items to populate the default achievements template dropdown.
         * Filters and returns a collection of list items based on predefined conditions
         * such as content type management ID and status.
         *
         * @return ListItem[] An array of ListItem objects representing the available options for the default achievements template.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstDefaultAchievementsTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultAchievementsCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultAchievementsClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 17 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultAchievements->Id == $this->intDefaultAchievements->Id) && ($this->objDefaultAchievements->Id == $this->intDefaultAchievements->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles the change event for the achievement template selection dropdown.
         * Verifies CSRF token and updates the corresponding FrontendTemplateLockedId and related frontend links.
         * Provides user feedback via notifications based on the operation's outcome.
         *
         * @param ActionParams $params The parameters associated with the triggered action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws RandomException
         */
        public function lstAchievementsTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultAchievementsTemplate->SelectedValue);
            $objFrontend = FrontendLinks::loadByIdFromContentTypesManagamentId(17);

            if ($this->intDefaultAchievements->FrontendTemplateLockedId && $this->lstDefaultAchievementsTemplate->SelectedValue) {
                $this->intDefaultAchievements->setFrontendTemplateLockedId($this->lstDefaultAchievementsTemplate->SelectedValue);
                $this->intDefaultAchievements->save();

                $objFrontendLink = FrontendLinks::loadById($objFrontend->Id);
                $objFrontendLink->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLink->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLink->save();

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultAchievementsTemplate->SelectedValue = $this->intDefaultAchievements->getFrontendTemplateLockedId();
                $this->lstDefaultAchievementsTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////


        /**
         * Initializes and configures the default links template.
         * Sets up a label and a select dropdown with various properties and associated actions.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function createLinksTemplate(): void
        {
            $this->lblDefaultLinksTemplate = new Q\Plugin\Control\Label($this);
            $this->lblDefaultLinksTemplate->Text = t('Default links template');
            $this->lblDefaultLinksTemplate->addCssClass('col-md-4');
            $this->lblDefaultLinksTemplate->setCssStyle('font-weight', 400);
            $this->lblDefaultLinksTemplate->Required = true;

            $this->lstDefaultLinksTemplate = new Q\Plugin\Select2($this);
            $this->lstDefaultLinksTemplate->MinimumResultsForSearch = -1;
            $this->lstDefaultLinksTemplate->Theme = 'web-vauu';
            $this->lstDefaultLinksTemplate->Width = '100%';
            $this->lstDefaultLinksTemplate->SelectionMode = ListBoxBase::SELECTION_MODE_SINGLE;
            $this->lstDefaultLinksTemplate->addItem(t('- Select one template -'), null, true);
            $this->lstDefaultLinksTemplate->addItems($this->lstDefaultLinksTemplate_GetItems());
            $this->lstDefaultLinksTemplate->SelectedValue = $this->intDefaultLinks->FrontendTemplateLockedId;
            $this->lstDefaultLinksTemplate->addAction(new Change(), new AjaxControl($this,'lstLinksTemplate_Change'));
        }

        /**
         * Retrieves a list of items for the default links template based on specified conditions.
         * Iterates through a database cursor and constructs an array of list items that meet the
         * specific criteria, including filtering by content type and status.
         *
         * @return ListItem[] Returns an array of ListItem objects representing the default links template items.
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        public function lstDefaultLinksTemplate_GetItems(): array
        {
            $a = array();
            $objCondition = $this->objDefaultLinksCondition ?: QQ::all();
            $objDefaultFrontendTemplateCursor = FrontendOptions::queryCursor($objCondition, $this->objDefaultLinksClauses);

            // Iterate through the Cursor
            while ($objDefaultFrontendTemplate = FrontendOptions::instantiateCursor($objDefaultFrontendTemplateCursor)) {
                // Check the conditions and add only the appropriate elements
                if ($objDefaultFrontendTemplate->ContentTypesManagementId === 18 && $objDefaultFrontendTemplate->Status === 1) {
                    $objListItem = new ListItem($objDefaultFrontendTemplate->__toString(), $objDefaultFrontendTemplate->Id);

                    // Set selected if necessary
                    if (($this->objDefaultLinks->Id == $this->intDefaultLinks->Id) && ($this->objDefaultLinks->Id == $this->intDefaultLinks->FrontendTemplateLockedId)) {
                        $objListItem->Selected = true;
                    }

                    $a[] = $objListItem;
                }
            }
            return $a;
        }

        /**
         * Handles changes to the link template dropdown and updates the related frontend links configuration.
         * Validates CSRF token, updates the selected template, and saves changes to the database.
         * Displays notifications based on the update result.
         *
         * @param ActionParams $params Parameters passed from the action triggering the change.
         *
         * @return void
         * @throws Exception If the CSRF token is invalid or other error conditions occur.
         */
        public function lstLinksTemplate_Change(ActionParams $params): void
        {
            if (!Application::verifyCsrfToken()) {
                $this->dlgModal1->showDialogBox();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                return;
            }

            $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(18);
            $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultLinksTemplate->SelectedValue);

            if ($this->intDefaultLinks->FrontendTemplateLockedId && $this->lstDefaultLinksTemplate->SelectedValue) {
                $this->intDefaultLinks->setFrontendTemplateLockedId($this->lstDefaultLinksTemplate->SelectedValue);
                $this->intDefaultLinks->save();

                foreach ($objFrontendLinks as $objFrontendLink) {
                    $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                    $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                    $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                    $objFrontendLinks->save();
                }

                $this->dlgToastr1->notify();

            } else {
                $this->lstDefaultLinksTemplate->SelectedValue = $this->intDefaultLinks->getFrontendTemplateLockedId();
                $this->lstDefaultLinksTemplate->refresh();
                $this->dlgToastr2->notify();
            }

            $this->userOptions();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

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
         * Creates and configures two Toastr notifications for displaying success and error messages.
         * Sets alert type, position, message content, and enables the progress bar for both notifications.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function createToastr(): void
        {
            $this->dlgToastr1 = new Q\Plugin\Toastr($this);
            $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
            $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
            $this->dlgToastr1->Message = t('<p><strong>Well done!</strong> The template has been saved or changed.</p>');
            $this->dlgToastr1->ProgressBar = true;

            $this->dlgToastr2 = new Q\Plugin\Toastr($this);
            $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
            $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
            $this->dlgToastr2->Message = t('<strong>The template must exist!</strong> <p>The previous template has been restored!</p>');
            $this->dlgToastr2->ProgressBar = true;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////
    }