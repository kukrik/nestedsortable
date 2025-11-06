<?php
    require('qcubed.inc.php');

    require ('classes/RecordsPanel.class.php');
    require('tables/AthletesTable.php');

    require ('classes/SportsAreasPanel.class.php');
    require ('tables/SportAreasTable.php');


    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Exception\Caller;
    use QCubed\Project\Control\FormBase as Form;

    /**
     * A class that represents a form for managing records. This form extends the base Form class
     * and utilizes a tab navigation control to organize its content.
     */
    class RecordsForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes the form and its components.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $page = new RecordsPanel($this->nav);
            $page->Name = t('Records');

            /* $page = new SportsAreasPanel($this->nav);
             $page->Name = t('Sports areas');*/

        }
    }
    RecordsForm::run('RecordsForm');
