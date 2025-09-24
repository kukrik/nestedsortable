<?php
    require('qcubed.inc.php');
    require ('classes/SportsViewPanel.class.php');
    require('tables/SportsViewTable.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;

    /**
     * Class SportsViewForm
     */
    class SportsViewForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes the form creation process. Sets up the navigation tabs and adds the default page to the navigation
         * control.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $page = new SportsViewPanel($this->nav);
            $page->Name = t('Linked documents overview');
        }
    }
    SportsViewForm::run('SportsViewForm');
