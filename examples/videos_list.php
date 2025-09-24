<?php
    require('qcubed.inc.php');

    require ('classes/VideosListPanel.class.php');
    require('tables/VideosTable.php');

    require ('classes/VideosSettings.class.php');
    require ('tables/VideosSettingsTable.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Exception\Caller;

    /**
     * Class VideoListForm
     */
    class VideoListForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes the form, sets up navigation tabs, and adds panels for video-related configurations.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $pnlVideosList = new VideosListPanel($this->nav, 'videosList');
            $pnlVideosList->Name = t('Videos list');

            $page = new VideosSetting($this->nav, 'videosSettings');
            $page->Name = t('Videos settings');
        }
    }
    VideoListForm::run('VideoListForm');
