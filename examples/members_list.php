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
use QCubed\Bootstrap as Bs;
use QCubed\Folder;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Application;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $nav;

    protected function formCreate()
    {
        parent::formCreate();

        $this->nav = new Q\Plugin\Tabs($this);
        $this->nav->addCssClass('tabbable tabbable-custom');

        $pnlSlidersList = new MembersListPanel($this->nav, 'membersList');
        $pnlSlidersList->Name = t('Members list');

        $page = new MembersSetting($this->nav, 'membersSettings');
        $page->Name = t('Members settings');

        $page = new MembersOptionsPanel($this->nav, 'membersOptions');
        $page->Name = t('Members options');
    }
}
SampleForm::run('SampleForm');
