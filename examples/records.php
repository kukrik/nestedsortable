<?php
require('qcubed.inc.php');

require ('classes/RecordsPanel.class.php');
require('tables/AthletesTable.php');

require ('classes/SportsAreasPanel.class.php');
require ('tables/SportAreasTable.php');


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

        $page = new RecordsPanel($this->nav);
        $page->Name = t('Records');

       /* $page = new SportsAreasPanel($this->nav);
        $page->Name = t('Sports areas');*/

    }
}
SampleForm::run('SampleForm');
