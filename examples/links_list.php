<?php
    require('qcubed.inc.php');

    require ('classes/LinksListPanel.class.php');
    require('tables/LinksTable.php');

    require ('classes/LinksSettings.class.php');
    require ('tables/LinksSettingsTable.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;

    /**
     * Class LinkListForm
     */
    class LinkListForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes the form and sets up the navigation tabs with associated panels.
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

            $page = new LinksListPanel($this->nav, 'linksList');
            $page->Name = t('Links list');

            $page = new LinksSetting($this->nav, 'linksSettings');
            $page->Name = t('Links settings');
        }
    }
    LinkListForm::run('LinkListForm');
