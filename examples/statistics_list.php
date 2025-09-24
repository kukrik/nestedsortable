<?php
    require('qcubed.inc.php');

    require ('classes/StatisticsListPanel.class.php');
    require('tables/StatisticsTable.php');

    require ('classes/AthletesPanel.class.php');
    require ('tables/AthletesTable.php');

    require ('classes/GenderMappingPanel.class.php');
    require ('tables/GenderMappingTable.php');

    require ('classes/AgeCategoriesPanel.class.php');
    require ('tables/AgeCategoriesTable.php');

    require ('classes/GendersPanel.class.php');
    require ('tables/GendersTable.php');

    require ('classes/SportsAreasCompetitionAreasPanel.class.php');

    require ('classes/SportsCompetitionAreasPanel.class.php');
    require ('tables/SportCompetitionAreasTable.php');

    require ('classes/SportsAreasPanel.class.php');
    require ('tables/SportAreasTable.php');

    require ('classes/StatisticsSettings.class.php');
    require ('tables/StatisticsSettingsTable.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;

    /**
     * Class StatisticsListForm
     */
    class StatisticsListForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes a form with a tab navigation interface and assigns multiple panels with respective names.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $page = new StatisticsListPanel($this->nav, 'statisticsList');
            $page->Name = t('Statistics list');

            $page = new AthletesPanel($this->nav, 'athletesList');
            $page->Name = t('Athletes list');

            $page = new  GenderMappingPanel($this->nav, 'genderMapping');
            $page->Name = t('Gender mapping');

//        Gender mapping
//        Soo kaardistamine
//        Age group gender distribution and management
//        Vanuseklasside soojaotus ja haldus

            $page = new AgeCategoriesPanel($this->nav, 'ageCategories');
            $page->Name = t('Age categories');

            $page = new GendersPanel($this->nav, 'genders');
            $page->Name = t('Genders');

            $page = new SportsAreasCompetitionAreasPanel($this->nav, 'sportsAreasCompetitionAreas');
            $page->Name = t('Mappings');

            $page = new SportsAreasPanel($this->nav, 'sportsAreas');
            $page->Name = t('Sports areas');

            $page = new CompetitionAreasPanel($this->nav);
            $page->Name = t('Competition areas');

            $page = new StatisticsSetting($this->nav, 'statisticsSettings');
            $page->Name = t('Statistics settings');
        }
    }
    StatisticsListForm::run('StatisticsListForm');
