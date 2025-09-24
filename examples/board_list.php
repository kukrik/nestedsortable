<?php
    require('qcubed.inc.php');

    require ('classes/BoardListPanel.class.php');
    require('tables/BoardTable.php');

    require ('classes/BoardSettings.class.php');
    require ('tables/BoardSettingsTable.php');

    require ('classes/BoardOptionsPanel.class.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Control\FormBase as Form;

    /**
     * Class BoardListForm
     *
     * This class represents the form for displaying and managing a list of boards
     * within a tabbed interface. It extends the base Form class and incorporates
     * a tab navigation structure using Q\Plugin\Tabs.
     *
     * The form consists of the following panels:
     *  - Board List Panel: Displays the list of boards.
     *  - Board Settings: Provides settings options for boards.
     *  - Board Options Panel: Contains additional board options.
     *
     * The navigation is styled using custom CSS classes for customization.
     */
    class BoardListForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes the form and creates the necessary components.
         *
         * @return void
         * @throws DateMalformedStringException
         * @throws Caller
         * @throws InvalidCast
         */
        protected function formCreate(): void
        {
            parent::formCreate();

            $this->nav = new Q\Plugin\Control\Tabs($this);
            $this->nav->addCssClass('tabbable tabbable-custom');

            $pnlBoardsList = new BoardListPanel($this->nav, 'boardsList');
            $pnlBoardsList->Name = t('Board list');

            $page = new BoardsSetting($this->nav, 'boardsSettings');
            $page->Name = t('Board settings');

            $page = new BoardOptionsPanel($this->nav, 'boardOptions');
            $page->Name = t('Board options');
        }
    }
    BoardListForm::run('BoardListForm');
