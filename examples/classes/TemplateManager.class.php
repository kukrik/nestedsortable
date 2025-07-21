<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;

class TemplateManager extends Q\Control\Panel
{
    protected $objDefaultHomeCondition;
    protected $objDefaultHomeClauses;
    protected $objDefaultArticleCondition;
    protected $objDefaultArticleClauses;
    protected $objDefaultNewsListCondition;
    protected $objDefaultNewsListClauses;
    protected $objDefaultNewsCondition;
    protected $objDefaultNewsClauses;
    protected $objDefaultGalleryListCondition;
    protected $objDefaultGalleryListClauses;
    protected $objDefaultGalleryCondition;
    protected $objDefaultGalleryClauses;
    protected $objDefaultEventsCalendarListCondition;
    protected $objDefaultEventsCalendarListClauses;
    protected $objDefaultEventsCalendarCondition;
    protected $objDefaultEventsCalendarClauses;
    protected $objDefaultSportsCalendarListCondition;
    protected $objDefaultSportsCalendarListClauses;
    protected $objDefaultSportsCalendarCondition;
    protected $objDefaultSportsCalendarClauses;
    protected $objDefaultSportsAreasCondition;
    protected $objDefaultSportsAreasClauses;
    protected $objDefaultBoardCondition;
    protected $objDefaultBoardClauses;
    protected $objDefaultMembersCondition;
    protected $objDefaultMembersClauses;
    protected $objDefaultVideosCondition;
    protected $objDefaultVideosClauses;
    protected $objDefaultRecordsCondition;
    protected $objDefaultRecordsClauses;
    protected $objDefaultRankingsCondition;
    protected $objDefaultRankingsClauses;
    protected $objDefaultAchievementsCondition;
    protected $objDefaultAchievementsClauses;
    protected $objDefaultLinksCondition;
    protected $objDefaultLinksClauses;

    public $dlgModal1;

    protected $dlgToastr1;
    protected $dlgToastr2;

    public $lblDefaultHomeTemplate;
    public $lstDefaultHomeTemplate;
    public $lblDefaultArticleTemplate;
    public $lstDefaultArticleTemplate;
    public $lblDefaultNewsListTemplate;
    public $lstDefaultNewsListTemplate;
    public $lblDefaultNewsTemplate;
    public $lstDefaultNewsTemplate;
    public $lblDefaultGalleryListTemplate;
    public $lstDefaultGalleryListTemplate;
    public $lblDefaultGalleryTemplate;
    public $lstDefaultGalleryTemplate;
    public $lblDefaultEventsCalendarListTemplate;
    public $lstDefaultEventsCalendarListTemplate;
    public $lblDefaultEventsCalendarTemplate;
    public $lstDefaultEventsCalendarTemplate;
    public $lblDefaultSportsCalendarListTemplate;
    public $lstDefaultSportsCalendarListTemplate;
    public $lblDefaultSportsCalendarTemplate;
    public $lstDefaultSportsCalendarTemplate;
    public $lblDefaultSportsAreasTemplate;
    public $lstDefaulSportsAreasTemplate;
    public $lblDefaultBoardTemplate;
    public $lstDefaultBoardTemplate;
    public $lblDefaultMembersTemplate;
    public $lstDefaultMembersTemplate;

    public $lblDefaultVideosTemplate;
    public $lstDefaultVideosTemplate;
    public $lblDefaultRecordsTemplate;
    public $lstDefaultRecordsTemplate;
    public $lblDefaultRankingsTemplate;
    public $lstDefaultRankingsTemplate;
    public $lblDefaultAchievementsTemplate;
    public $lstDefaultAchievementsTemplate;
    public $lblDefaultLinksTemplate;
    public $lstDefaultLinksTemplate;

    protected $intDefaultHome;
    protected $intDefaultArticle;
    protected $intDefaultNewsList;
    protected $intDefaultNews;
    protected $intDefaultGalleryList;
    protected $intDefaultGallery;
    protected $intDefaultEventsCalendarList;
    protected $intDefaultEventsCalendar;
    protected $intDefaultSportsCalendarList;
    protected $intDefaultSportsCalendar;
    protected $intDefaultSportsAreas;
    protected $intDefaultBoard;
    protected $intDefaultMembers;
    protected $intDefaultVideos;
    protected $intDefaultRecords;
    protected $intDefaultRankings;
    protected $intDefaultAchievements;
    protected $intDefaultLinks;

    protected $objDefaultHome;
    protected $objDefaultArticle;
    protected $objDefaultNewsList;
    protected $objDefaultNews;
    protected $objDefaultGalleryList;
    protected $objDefaultGallery;
    protected $objDefaultEventsCalendarList;
    protected $objDefaultEventsCalendar;
    protected $objDefaultSportsCalendarList;
    protected $objDefaultSportsCalendar;
    protected $objDefaultSportsAreas;
    protected $objDefaultBoard;
    protected $objDefaultMembers;
    protected $objDefaultVideos;
    protected $objDefaultRecords;
    protected $objDefaultRankings;
    protected $objDefaultAchievements;
    protected $objDefaultLinks;

    protected $strTemplate = 'TemplateManager.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

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

    protected function createHomeTemplate()
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
        $this->lstDefaultHomeTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultHomeTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultHomeTemplate->addItems($this->lstDefaultHomeTemplate_GetItems());
        $this->lstDefaultHomeTemplate->SelectedValue = $this->intDefaultHome->FrontendTemplateLockedId;
        $this->lstDefaultHomeTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstHomeTemplate_Change'));
    }

    public function lstDefaultHomeTemplate_GetItems()
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

    public function lstHomeTemplate_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultHomeTemplate->SelectedValue);
        $objFrontentend = FrontendLinks::loadByIdFromContentTypesManagamentId(1);

        if ($this->intDefaultHome->FrontendTemplateLockedId && $this->lstDefaultHomeTemplate->SelectedValue) {
            $this->intDefaultHome->setFrontendTemplateLockedId($this->lstDefaultHomeTemplate->SelectedValue);
            $this->intDefaultHome->save();

            $objFrontentendLink = FrontendLinks::loadById($objFrontentend->Id);
            $objFrontentendLink->setFrontendClassName($objFrontendOptions->ClassName);
            $objFrontentendLink->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $objFrontentendLink->save();

            $this->dlgToastr1->notify();

        } else {
            $this->lstDefaultHomeTemplate->SelectedValue = $this->intDefaultHome->getFrontendTemplateLockedId();
            $this->lstDefaultHomeTemplate->refresh();
            $this->dlgToastr2->notify();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createArticleTemplate()
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
        $this->lstDefaultArticleTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultArticleTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultArticleTemplate->addItems($this->lstDefaultArticleTemplate_GetItems());
        $this->lstDefaultArticleTemplate->SelectedValue = $this->intDefaultArticle->FrontendTemplateLockedId;
        $this->lstDefaultArticleTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstArticleTemplate_Change'));
    }

    public function lstDefaultArticleTemplate_GetItems()
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

    public function lstArticleTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createNewsListTemplate()
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
        $this->lstDefaultNewsListTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultNewsListTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultNewsListTemplate->addItems($this->lstDefaultNewsListTemplate_GetItems());
        $this->lstDefaultNewsListTemplate->SelectedValue = $this->intDefaultNewsList->FrontendTemplateLockedId;
        $this->lstDefaultNewsListTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstNewsListTemplate_Change'));
    }

    public function lstDefaultNewsListTemplate_GetItems() {
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

    public function lstNewsListTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createNewsTemplate()
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
        $this->lstDefaultNewsTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultNewsTemplate->addItem(t('- Select one type -'), null, true);
        $this->lstDefaultNewsTemplate->addItems($this->lstDefaultNewsTemplate_GetItems());
        $this->lstDefaultNewsTemplate->SelectedValue = $this->intDefaultNews->FrontendTemplateLockedId;
        $this->lstDefaultNewsTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstNewsTemplate_Change'));
    }

    public function lstDefaultNewsTemplate_GetItems()
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

    public function lstNewsTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createGalleryListTemplate()
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
        $this->lstDefaultGalleryListTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultGalleryListTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultGalleryListTemplate->addItems($this->lstDefaultGalleryListTemplate_GetItems());
        $this->lstDefaultGalleryListTemplate->SelectedValue = $this->intDefaultGalleryList->FrontendTemplateLockedId;
        $this->lstDefaultGalleryListTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstGalleryListTemplate_Change'));
    }

    public function lstDefaultGalleryListTemplate_GetItems()
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

    public function lstGalleryListTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createGalleryTemplate()
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
        $this->lstDefaultGalleryTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultGalleryTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultGalleryTemplate->addItems($this->lstDefaultGalleryTemplate_GetItems());
        $this->lstDefaultGalleryTemplate->SelectedValue = $this->intDefaultGallery->FrontendTemplateLockedId;
        $this->lstDefaultGalleryTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstGalleryTemplate_Change'));
    }

    public function lstDefaultGalleryTemplate_GetItems()
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

    public function lstGalleryTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createEventsCalendarListTemplate()
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
        $this->lstDefaultEventsCalendarListTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultEventsCalendarListTemplate->addItem(t('- Select one type -'), null, true);
        $this->lstDefaultEventsCalendarListTemplate->addItems($this->lstDefaultEventsCalendarListTemplate_GetItems());
        $this->lstDefaultEventsCalendarListTemplate->SelectedValue = $this->intDefaultEventsCalendarList->FrontendTemplateLockedId;
        $this->lstDefaultEventsCalendarListTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstEventsCalendarListTemplate_Change'));
    }

    public function lstDefaultEventsCalendarListTemplate_GetItems()
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

    public function lstEventsCalendarListTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createEventsCalendarTemplate()
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
        $this->lstDefaultEventsCalendarTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultEventsCalendarTemplate->addItem(t('- Select one type -'), null, true);
        $this->lstDefaultEventsCalendarTemplate->addItems($this->lstDefaultEventsCalendarTemplate_GetItems());
        $this->lstDefaultEventsCalendarTemplate->SelectedValue = $this->intDefaultEventsCalendar->FrontendTemplateLockedId;
        $this->lstDefaultEventsCalendarTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstEventsCalendarTemplate_Change'));
    }

     public function lstDefaultEventsCalendarTemplate_GetItems()
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

    public function lstEventsCalendarTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createSportsCalendarListTemplate()
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
        $this->lstDefaultSportsCalendarListTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultSportsCalendarListTemplate->addItem(t('- Select one type -'), null, true);
        $this->lstDefaultSportsCalendarListTemplate->addItems($this->lstDefaultSportsCalendarListTemplate_GetItems());
        $this->lstDefaultSportsCalendarListTemplate->SelectedValue = $this->intDefaultSportsCalendarList->FrontendTemplateLockedId;
        $this->lstDefaultSportsCalendarListTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstSportsCalendarListTemplate_Change'));
    }

    public function lstDefaultSportsCalendarListTemplate_GetItems()
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

    public function lstSportsCalendarListTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createSportsCalendarTemplate()
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
        $this->lstDefaultSportsCalendarTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultSportsCalendarTemplate->addItem(t('- Select one type -'), null, true);
        $this->lstDefaultSportsCalendarTemplate->addItems($this->lstDefaultSportsCalendarTemplate_GetItems());
        $this->lstDefaultSportsCalendarTemplate->SelectedValue = $this->intDefaultSportsCalendar->FrontendTemplateLockedId;
        $this->lstDefaultSportsCalendarTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstSportsCalendarTemplate_Change'));
    }

    public function lstDefaultSportsCalendarTemplate_GetItems()
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

    public function lstSportsCalendarTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createSportsAreasTemplate()
    {
        $this->lblDefaultSportsAreasTemplate = new Q\Plugin\Control\Label($this);
        $this->lblDefaultSportsAreasTemplate->Text = t('Default sports areas detail template');
        $this->lblDefaultSportsAreasTemplate->addCssClass('col-md-4');
        $this->lblDefaultSportsAreasTemplate->setCssStyle('font-weight', 400);
        $this->lblDefaultSportsAreasTemplate->Required = true;

        $this->lstDefaulSportsAreasTemplate = new Q\Plugin\Select2($this);
        $this->lstDefaulSportsAreasTemplate->MinimumResultsForSearch = -1;
        $this->lstDefaulSportsAreasTemplate->Theme = 'web-vauu';
        $this->lstDefaulSportsAreasTemplate->Width = '100%';
        $this->lstDefaulSportsAreasTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaulSportsAreasTemplate->addItem(t('- Select one type -'), null, true);
        $this->lstDefaulSportsAreasTemplate->addItems($this->lstDefaulSportsAreasTemplate_GetItems());
        $this->lstDefaulSportsAreasTemplate->SelectedValue = $this->intDefaultSportsAreas->FrontendTemplateLockedId;
        $this->lstDefaulSportsAreasTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstSportsAreasTemplate_Change'));
    }

    public function lstDefaulSportsAreasTemplate_GetItems()
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

    public function lstSportsAreasTemplate_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFrontendLinks = FrontendLinks::loadArrayByContentTypesManagamentId(11);
        $objFrontendOptions = FrontendOptions::loadById($this->lstDefaulSportsAreasTemplate->SelectedValue);

        if ($this->intDefaultSportsAreas->FrontendTemplateLockedId && $this->lstDefaulSportsAreasTemplate->SelectedValue) {
            $this->intDefaultSportsAreas->setFrontendTemplateLockedId($this->lstDefaulSportsAreasTemplate->SelectedValue);
            $this->intDefaultSportsAreas->save();

            foreach ($objFrontendLinks as $objFrontendLink) {
                $objFrontendLinks = FrontendLinks::loadById($objFrontendLink->getId());
                $objFrontendLinks->setFrontendClassName($objFrontendOptions->ClassName);
                $objFrontendLinks->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
                $objFrontendLinks->save();
            }

            $this->dlgToastr1->notify();

        } else {
            $this->lstDefaulSportsAreasTemplate->SelectedValue = $this->intDefaultSportsAreas->getFrontendTemplateLockedId();
            $this->lstDefaulSportsAreasTemplate->refresh();
            $this->dlgToastr2->notify();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createBoardTemplate()
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
        $this->lstDefaultBoardTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultBoardTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultBoardTemplate->addItems($this->lstDefaultBoardTemplate_GetItems());
        $this->lstDefaultBoardTemplate->SelectedValue = $this->intDefaultBoard->FrontendTemplateLockedId;
        $this->lstDefaultBoardTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstBoardTemplate_Change'));
    }

    public function lstDefaultBoardTemplate_GetItems()
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

    public function lstBoardTemplate_Change(ActionParams $params)
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
    }


    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createMembersTemplate()
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
        $this->lstDefaultMembersTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultMembersTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultMembersTemplate->addItems($this->lstDefaultMembersTemplate_GetItems());
        $this->lstDefaultMembersTemplate->SelectedValue = $this->intDefaultMembers->FrontendTemplateLockedId;
        $this->lstDefaultMembersTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstMembersTemplate_Change'));
    }

    public function lstDefaultMembersTemplate_GetItems()
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

    public function lstMembersTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createVideosTemplate()
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
        $this->lstDefaultVideosTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultVideosTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultVideosTemplate->addItems($this->lstDefaultVideosTemplate_GetItems());
        $this->lstDefaultVideosTemplate->SelectedValue = $this->intDefaultVideos->FrontendTemplateLockedId;
        $this->lstDefaultVideosTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstVideosTemplate_Change'));
    }

    public function lstDefaultVideosTemplate_GetItems()
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

    public function lstVideosTemplate_Change(ActionParams $params)
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createRecordsTemplate()
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
        $this->lstDefaultRecordsTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultRecordsTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultRecordsTemplate->addItems($this->lstDefaultRecordsTemplate_GetItems());
        $this->lstDefaultRecordsTemplate->SelectedValue = $this->intDefaultRecords->FrontendTemplateLockedId;
        $this->lstDefaultRecordsTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstRecordsTemplate_Change'));
    }

    public function lstDefaultRecordsTemplate_GetItems()
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

    public function lsttRecordsTemplate_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultRecordsTemplate->SelectedValue);
        $objFrontentend = FrontendLinks::loadByIdFromContentTypesManagamentId(15);

        if ($this->intDefaultRecords->FrontendTemplateLockedId && $this->lstDefaultRecordsTemplate->SelectedValue) {
            $this->intDefaultRecords->setFrontendTemplateLockedId($this->lstDefaultRecordsTemplate->SelectedValue);
            $this->intDefaultRecords->save();

            $objFrontentendLink = FrontendLinks::loadById($objFrontentend->Id);
            $objFrontentendLink->setFrontendClassName($objFrontendOptions->ClassName);
            $objFrontentendLink->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $objFrontentendLink->save();

            $this->dlgToastr1->notify();

        } else {
            $this->lstDefaultRecordsTemplate->SelectedValue = $this->intDefaultRecords->getFrontendTemplateLockedId();
            $this->lstDefaultRecordsTemplate->refresh();
            $this->dlgToastr2->notify();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createRankingsTemplate()
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
        $this->lstDefaultRankingsTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultRankingsTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultRankingsTemplate->addItems($this->lstDefaultRankingsTemplate_GetItems());
        $this->lstDefaultRankingsTemplate->SelectedValue = $this->intDefaultRankings->FrontendTemplateLockedId;
        $this->lstDefaultRankingsTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstRankingTemplate_Change'));
    }

    public function lstDefaultRankingsTemplate_GetItems()
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

    public function lstRankingTemplate_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultRecordsTemplate->SelectedValue);
        $objFrontentend = FrontendLinks::loadByIdFromContentTypesManagamentId(16);

        if ($this->intDefaultRecords->FrontendTemplateLockedId && $this->lstDefaultRecordsTemplate->SelectedValue) {
            $this->intDefaultRecords->setFrontendTemplateLockedId($this->lstDefaultRecordsTemplate->SelectedValue);
            $this->intDefaultRecords->save();

            $objFrontentendLink = FrontendLinks::loadById($objFrontentend->Id);
            $objFrontentendLink->setFrontendClassName($objFrontendOptions->ClassName);
            $objFrontentendLink->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $objFrontentendLink->save();

            $this->dlgToastr1->notify();

        } else {
            $this->lstDefaultRecordsTemplate->SelectedValue = $this->intDefaultRecords->getFrontendTemplateLockedId();
            $this->lstDefaultRecordsTemplate->refresh();
            $this->dlgToastr2->notify();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createAchievementsTemplate()
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
        $this->lstDefaultAchievementsTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultAchievementsTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultAchievementsTemplate->addItems($this->lstDefaultAchievementsTemplate_GetItems());
        $this->lstDefaultAchievementsTemplate->SelectedValue = $this->intDefaultAchievements->FrontendTemplateLockedId;
        $this->lstDefaultAchievementsTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstAchievementsTemplate_Change'));
    }

    public function lstDefaultAchievementsTemplate_GetItems()
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

    public function lstAchievementsTemplate_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultAchievementsTemplate->SelectedValue);
        $objFrontentend = FrontendLinks::loadByIdFromContentTypesManagamentId(17);

        if ($this->intDefaultAchievements->FrontendTemplateLockedId && $this->lstDefaultAchievementsTemplate->SelectedValue) {
            $this->intDefaultAchievements->setFrontendTemplateLockedId($this->lstDefaultAchievementsTemplate->SelectedValue);
            $this->intDefaultAchievements->save();

            $objFrontentendLink = FrontendLinks::loadById($objFrontentend->Id);
            $objFrontentendLink->setFrontendClassName($objFrontendOptions->ClassName);
            $objFrontentendLink->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $objFrontentendLink->save();

            $this->dlgToastr1->notify();

        } else {
            $this->lstDefaultAchievementsTemplate->SelectedValue = $this->intDefaultAchievements->getFrontendTemplateLockedId();
            $this->lstDefaultAchievementsTemplate->refresh();
            $this->dlgToastr2->notify();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////


    protected function createLinksTemplate()
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
        $this->lstDefaultLinksTemplate->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstDefaultLinksTemplate->addItem(t('- Select one template -'), null, true);
        $this->lstDefaultLinksTemplate->addItems($this->lstDefaultLinksTemplate_GetItems());
        $this->lstDefaultLinksTemplate->SelectedValue = $this->intDefaultLinks->FrontendTemplateLockedId;
        $this->lstDefaultLinksTemplate->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstLinksTemplate_Change'));
    }

    public function lstDefaultLinksTemplate_GetItems()
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

    public function lstLinksTemplate_Change(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal1->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFrontendOptions = FrontendOptions::loadById($this->lstDefaultLinksTemplate->SelectedValue);
        $objFrontentend = FrontendLinks::loadByIdFromContentTypesManagamentId(18);

        if ($this->intDefaultLinks->FrontendTemplateLockedId && $this->lstDefaultLinksTemplate->SelectedValue) {
            $this->intDefaultLinks->setFrontendTemplateLockedId($this->lstDefaultLinksTemplate->SelectedValue);
            $this->intDefaultLinks->save();

            $objFrontentendLink = FrontendLinks::loadById($objFrontentend->Id);
            $objFrontentendLink->setFrontendClassName($objFrontendOptions->ClassName);
            $objFrontentendLink->setFrontendTemplatePath($objFrontendOptions->FrontendTemplatePath);
            $objFrontentendLink->save();

            $this->dlgToastr1->notify();

        } else {
            $this->lstDefaultLinksTemplate->SelectedValue = $this->intDefaultLinks->getFrontendTemplateLockedId();
            $this->lstDefaultAchievementsTemplate->refresh();
            $this->dlgToastr2->notify();
        }

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
    public function createModals()
    {
        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal1->Title = t("Warning");
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addCloseButton(t("I understand"));
    }

    protected function createToastr()
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