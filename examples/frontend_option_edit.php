<?php
    require('qcubed.inc.php');
    require ('classes/FrontendOptionEditPanel.class.php');
    
    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;

    /**
     * Class FrontendOptionEditForm
     */
    class FrontendOptionEditForm extends Form
    {
        protected Q\Plugin\Control\Tabs$nav;

        /**
         * Initializes the form creation process by setting up the necessary components and adding a navigation tab
         * interface.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $page = new FrontendOptionEditPanel($this->nav);
            $page->Name = t('Frontend option edit');
        }
    }
    FrontendOptionEditForm::run('FrontendOptionEditForm');