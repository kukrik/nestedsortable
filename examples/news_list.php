<?php
    require('qcubed.inc.php');

    require('classes/PageEditPanel.class.php');
    require ('classes/NewsListPanel.class.php');
    require('tables/NewsTable.php');

    require ('classes/NewsCategoriesManager.class.php');
    require('tables/NewsCategoriesTable.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;

    /**
     * Class responsible for creating a form with navigation tabs and a News List Panel.
     * Extends the base Form class to provide specialized behavior and components.
     */
    class NewsListForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes the form and its components, setting up the navigation tabs
         * as well as adding a News List Panel as a tab.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $pnlPage = new NewsListPanel($this->nav);
            $pnlPage->Name = t('News list');
        }
    }
    NewsListForm::run('NewsListForm');
