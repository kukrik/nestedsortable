<?php
    require('qcubed.inc.php');
    require ('classes/GalleryListPanel.class.php');
    require('tables/GalleryTable.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;

    /**
     * Class GalleryListForm
     */
    class GalleryListForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Handles the initialization of the form components and their properties.
         * Sets up the navigation control and adds a panel for the albums list.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $pnlPage = new GalleryListPanel($this->nav);
            $pnlPage->Name = t('Albums list');
        }
    }
    GalleryListForm::run('GalleryListForm');