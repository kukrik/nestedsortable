<?php
    require('qcubed.inc.php');

    require ('classes/SportsCalendarListPanel.class.php');
    require ('classes/SportsAreasPanel.class.php');
    require ('classes/OrganizingInstitutionManager.class.php');
    require ('classes/SportsContentTypesPanel.class.php');

    require('tables/SportAreasTable.php');
    require('tables/SportsCalendarTable.php');
    require('tables/OrganizingInstitutionTable.php');
    require('tables/SportsContentTypesTable.php');


    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;

    /**
     * Class SportsCalendarListForm
     */
    class SportsCalendarListForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;
        protected ?object $objMenuContent = null;

        /**
         * Initializes the form by setting up navigation tabs and loading menu content.
         *
         * This method creates a series of tabbed sections for different panels
         * including Sports Calendars List, Sports Areas List, Organizing Institutions,
         * and Sports Content Types, providing an organized layout for the form.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws DateMalformedStringException
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $intId = Application::instance()->context()->queryStringItem('id');
            $this->objMenuContent = MenuContent::load($intId);

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $page = new SportsCalendarListPanel($this->nav, 'sportsCalendarList');
            $page->Name = t('Sports calendars list');

            $page = new SportsAreasPanel($this->nav, 'sportsAreas');
            $page->Name = t('Sports areas list');

            $page = new OrganizingInstitutionManager($this->nav, 'organizingInstitutions');
            $page->Name = t('Organizing institution list');

            $page = new SportsContentTypesPanel($this->nav, 'sportsContentTypes');
            $page->Name = t('Sports content types list');
        }
    }
    SportsCalendarListForm::run('SportsCalendarListForm');
