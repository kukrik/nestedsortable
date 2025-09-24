<?php
    require('qcubed.inc.php');
    require ('classes/ContentTypesManagementEditPanel.class.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging


    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;

    /**
     * Class ContentTypesManagementEditForm
     */
    class ContentTypesManagementEditForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes the form and sets up the navigation control and content type management edit panel.
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

            $page = new ContentTypesManagementsEditPanel($this->nav);
            $page->Name = t('Content type option edit');
        }
    }
    ContentTypesManagementEditForm::run('ContentTypesManagementEditForm');
