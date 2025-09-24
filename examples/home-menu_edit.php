<?php
    require('qcubed.inc.php');
    require ('classes/HomePageEditPanel.class.php');
    require ('classes/HomePageMetaDataPanel.class.php');

    use QCubed as Q;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Project\Application;

    /**
     * HomeMenuEditForm
     */
    class HomeMenuEditForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;
        protected object $objMenuContent;

        /**
         * Initializes the form by loading menu content based on the provided ID from the query string
         * and setting up various panels and metadata configurations depending on the content type.
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
            } else {
                $objPanelName = ContentType::toClassNames($this->objMenuContent->ContentType);
                $page = new $objPanelName($this->nav);
                $page->Name = ContentType::toTabsText($this->objMenuContent->ContentType);

                if ($this->objMenuContent->ContentType !== 3 // NewsEditEditPanel
                    && $this->objMenuContent->ContentType !== 5 // EventsCalendarEditPanel
                    && $this->objMenuContent->ContentType !== 7 // InternalPageEditPanel
                    && $this->objMenuContent->ContentType !== 8 // RedirectingEditPanel
                    && $this->objMenuContent->ContentType !== 9 // PlaceholderEditPanel
                    && $this->objMenuContent->ContentType !== 10 // ErrorPageEditPanel
                ) {
                    $page = new HomePageMetaDataPanel($this->nav);
                    $page->Name = t('Metadata');
                }
            }
        }
    }
    HomeMenuEditForm::run('HomeMenuEditForm');