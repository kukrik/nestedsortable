<?php
    require('qcubed.inc.php');

    require ('classes/EventsCalendarListPanel.class.php');
    require ('classes/TargetCroupPanel.class.php');

    require('tables/EventsCalendarTable.php');
    require('tables/SportAreasTable.php');
    require('tables/TargetGroupTable.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;

    /**
     * Class EventsCalendarListForm
     */
    class EventsCalendarListForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;
        protected ?object $objMenuContent = null;

        /**
         * Initializes and sets up the form, including loading data and adding UI components.
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

            $page = new EventsCalendarListPanel($this->nav, 'eventsCalendarList');
            $page->Name = t('Event calendar list');

            $page = new TargetCroupPanel($this->nav, 'targetCroupList');
            $page->Name = t('Target group list');
        }
    }
    EventsCalendarListForm::run('EventsCalendarListForm');
