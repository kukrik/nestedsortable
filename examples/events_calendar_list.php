<?php
require('qcubed.inc.php');

require ('classes/EventsCalendarListPanel.class.php');
require ('classes/TargetCroupPanel.class.php');

require('tables/EventsCalendarTable.php');
require('tables/SportAreasTable.php');
require('tables/TargetGroupTable.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Application;

/**
* Class SampleForm
*/
class SampleForm extends Form
{
	protected $nav;
	protected $objMenuContent;

	protected function formCreate()
	{
		parent::formCreate();

		$intId = Application::instance()->context()->queryStringItem('id');
		$this->objMenuContent = MenuContent::load($intId);

		$this->nav = new Q\Plugin\Control\Tabs($this);
		$this->nav->addCssClass('tabbable tabbable-custom');

		$page = new EventsCalendarListPanel($this->nav, 'eventsCalendarList');
        $page->Name = t('Event calendar list');

        $page = new TargetCroupPanel($this->nav, 'targetCroupList');
        $page->Name = t('Target group list');
	}
}
SampleForm::run('SampleForm');
