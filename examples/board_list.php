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

        $pnlBoardsList = new BoardListPanel($this->nav, 'boardsList');
        $pnlBoardsList->Name = t('Board list');

        $page = new BoardsSetting($this->nav, 'boardsSettings');
        $page->Name = t('Board settings');

        $page = new BoardOptionsPanel($this->nav, 'boardOptions');
        $page->Name = t('Board options');
    }
}
SampleForm::run('SampleForm');
