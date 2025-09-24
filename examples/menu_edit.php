<?php
    require('qcubed.inc.php');

    require('classes/PageEditPanel.class.php');

    require ('classes/ArticleEditPanel.class.php');
    require ('classes/NewsEditPanel.class.php');
    require ('classes/GalleryEditPanel.class.php');
    require ('classes/EventsCalendarEditPanel.class.php');
    require ('classes/SportsCalendarEditPanel.class.php');
    require ('classes/InternalPageEditPanel.class.php');
    require ('classes/RedirectingEditPanel.class.php');
    require ('classes/PlaceholderEditPanel.class.php');
    require ('classes/SportsAreasEditPanel.class.php');
    require ('classes/BoardEditPanel.class.php');
    require ('classes/MembersEditPanel.class.php');
    require ('classes/VideosEditPanel.class.php');
    require ('classes/StatisticsEditPanel.class.php');
    require ('classes/LinksEditPanel.class.php');
    require('classes/PageMetaDataPanel.class.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Project\Application;

    /**
     * MenuEditForm
     */
    class MenuEditForm extends Form
    {
        protected  Q\Plugin\Control\Tabs $nav;
        protected object $objMenuContent;

        /**
         * Initializes the form setup for managing MenuContent objects based on the provided context.
         * Establishes navigation controls and dynamically creates content-specific panels.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $intId = Application::instance()->context()->queryStringItem('id');
            $this->objMenuContent = MenuContent::load($intId);

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            if ($this->objMenuContent->ContentType == null) {
                $page = new PageEditPanel($this->nav);
                $page->Name = t('Configure page');

            } else if ($this->objMenuContent->ContentType == 14 || // Statistics (Records) content type
                $this->objMenuContent->ContentType == 15 || // Statistics (Rankings) content type
                $this->objMenuContent->ContentType == 16) { // Statistics (Achievements) content type

                $page = new StatisticsEditPanel($this->nav);
                $page->Name = t('Edit statistics');
            } else {
                $objPanelName = ContentType::toClassNames($this->objMenuContent->ContentType);
                $page = new $objPanelName($this->nav);
                $page->Name = ContentType::toTabsText($this->objMenuContent->ContentType);

                if ($this->objMenuContent->ContentType !== 7 && // InternalPageEditPanel
                    $this->objMenuContent->ContentType !== 8 && // RedirectingEditPanel
                    $this->objMenuContent->ContentType !== 9 // PlaceholderEditPanel
                ) {
                    $page = new PageMetaDataPanel($this->nav);
                    $page->Name = t('Metadata');
                }
            }
        }
    }
    MenuEditForm::run('MenuEditForm');
