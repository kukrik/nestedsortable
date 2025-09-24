<?php
    require('qcubed.inc.php');
    require('tables/FrontendConfigurationTable.php');
    require('tables/FrontendLinksOverviewTable.php');
    require('tables/ContentTypesManagementTable.php');
    require ('classes/TemplateManager.class.php');
    require ('classes/FrontendLinksOverview.class.php');
    require ('classes/FrontendConfigurationManager.class.php');
    require ('classes/ContentTypesManagements.class.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging


    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;

    /**
     * Class ContentTypesManagerForm
     */
    class ContentTypesManagerForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes the form by creating navigation tabs and adding various management pages.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $page = new TemplateManager($this->nav);
            $page->Name = t('Template management');

            $page = new FrontendLinksOverview($this->nav);
            $page->Name = t('Frontend links overview');

            $page = new FrontendConfigurationManager($this->nav);
            $page->Name = t('Frontend configuration options');

            $page = new ContentTypesManagements($this->nav);
            $page->Name = t('Content types management');
        }
    }
    ContentTypesManagerForm::run('ContentTypesManagerForm');
