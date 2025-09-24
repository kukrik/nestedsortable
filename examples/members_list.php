<?php
    require('qcubed.inc.php');

    require ('classes/MembersListPanel.class.php');
    require('tables/MembersTable.php');

    require ('classes/MembersSettings.class.php');
    require ('tables/MembersSettingsTable.php');

    require ('classes/MembersOptionsPanel.class.php');
    
    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Control\FormBase as Form;

    /**
     * MembersListForm class extends the base Form class to create a custom form
     * containing a tabbed navigation interface for managing members.
     *
     * The form consists of a tabbed navigation component which includes multiple
     * panels for different functionalities:
     *
     * - Members List: Provides a list view of all members.
     * - Members Settings: Allows managing various settings related to members.
     * - Members Options: Provides additional options and configurations for members.
     *
     * The `formCreate` method initializes the tabbed navigation and adds these
     * panels to it.
     */
    class MembersListForm extends Form
    {
        protected Q\Plugin\Control\Tabs $nav;

        /**
         * Initializes the form by creating navigation tabs and associated panels.
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

            $pnlSlidersList = new MembersListPanel($this->nav, 'membersList');
            $pnlSlidersList->Name = t('Members list');

            $page = new MembersSetting($this->nav, 'membersSettings');
            $page->Name = t('Members settings');

            $page = new MembersOptionsPanel($this->nav, 'membersOptions');
            $page->Name = t('Members options');
        }
    }
    MembersListForm::run('MembersListForm');
