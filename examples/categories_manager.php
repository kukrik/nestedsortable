<?php
require('qcubed.inc.php');

require ('classes/ArticleCategoriesManager.class.php');
require('tables/ArticleCategoriesTable.php');

require ('classes/NewsCategoriesManager.class.php');
require('tables/NewsCategoriesTable.php');

require ('classes/NewsChangesManager.class.php');
require('tables/NewsChangesTable.php');

require ('classes/EventsChangesManager.class.php');
require('tables/EventsChangesTable.php');

require ('classes/SportsChangesManager.class.php');
require('tables/SportsChangesTable.php');

require ('classes/LinkCategoriesManager.class.php');
require('tables/LinksCategoriesTable.php');


error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Application;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $nav;

    protected function formCreate()
    {
        parent::formCreate();

        $this->nav = new Q\Plugin\Control\Tabs($this);
        $this->nav->addCssClass('tabbable tabbable-custom');

        $page = new ArticleCategoriesManager($this->nav, 'articleCategories');
        $page->Name = t('Article categories');

        $page = new NewsCategoriesManager($this->nav, 'newsCategories');
        $page->Name = t('News categories');

        $page = new NewsChangesManager($this->nav, 'newsChanges');
        $page->Name = t('News changes');

        $page = new EventsChangesManager($this->nav, 'eventsChanges');
        $page->Name = t('Event changes');

        $page = new SportsChangesManager($this->nav, 'sportsChanges');
        $page->Name = t('Sports event changes');

        $page = new LinkCategoriesManager($this->nav, 'linksCategories');
        $page->Name = t('Link categories');
    }
}
SampleForm::run('SampleForm');
