<?php
require('qcubed.inc.php');

require ('classes/NewsSettings.class.php');
require ('tables/NewsSettingsTable.php');

require ('classes/GallerySettings.class.php');
require ('tables/GallerySettingsTable.php');

require ('classes/EventsSettings.class.php');
require ('tables/EventsSettingsTable.php');

require ('classes/SportsSettings.class.php');
require ('tables/SportsSettingsTable.php');

require ('classes/BoardSettings.class.php');
require ('tables/BoardSettingsTable.php');

require ('classes/MembersSettings.class.php');
require ('tables/MembersSettingsTable.php');

require ('classes/VideosSettings.class.php');
require ('tables/VideosSettingsTable.php');

require ('classes/StatisticsSettings.class.php');
require ('tables/StatisticsSettingsTable.php');

require ('classes/LinksSettings.class.php');
require ('tables/LinksSettingsTable.php');

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

        $page = new NewsSetting($this->nav, 'newsSettings');
        $page->Name = t('News settings');

        $page = new GalleriesSettings($this->nav, 'galleriesSettings');
        $page->Name = t('Gallery settings');

        $page = new EventSettings($this->nav, 'eventsSettings');
        $page->Name = t('Events settings');

        $page = new SportsCalendarSettings($this->nav, 'sportsSettings');
        $page->Name = t('Sports calendar settings');

        $page = new BoardsSetting($this->nav, 'boardsSettings');
        $page->Name = t('Board settings');

        $page = new MembersSetting($this->nav, 'membersSettings');
        $page->Name = t('Members settings');

        $page = new VideosSetting($this->nav, 'videosSettings');
        $page->Name = t('Videos settings');

        $page = new StatisticsSetting($this->nav, 'statisticsSettings');
        $page->Name = t('Statistics settings');

        $page = new LinksSetting($this->nav, 'linksSettings');
        $page->Name = t('Links settings');
    }
}
SampleForm::run('SampleForm');
