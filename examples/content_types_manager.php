<?php
require('qcubed.inc.php');
require('tables/FrontendConfigurationTable.php');
require('tables/FrontendLinksOverviewTable.php');
require('tables/ContentTypesManagementTable.php');
require ('classes/TemplateManager.class.php');
require ('classes/FrontendLinksOverview.class.php');
require ('classes/FrontendConfigurationManager.class.php');
require ('classes/ContentTypesManagements.class.php');



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
    protected $pnlPage;
    protected $pnlFrontendLinksOverview;
    protected $pnlConfiguration;
    protected $pnlContentTypesManagement;

    protected function formCreate()
    {
        parent::formCreate();

        $this->nav = new Q\Plugin\Control\Tabs($this);
        $this->nav->addCssClass('tabbable tabbable-custom');

        $this->pnlPage = new TemplateManager($this->nav);
        $this->pnlPage->Name = t('Template management');

        $this->pnlFrontendLinksOverview = new FrontendLinksOverview($this->nav);
        $this->pnlFrontendLinksOverview->Name = t('Frontend links overview');

        $this->pnlConfiguration = new FrontendConfigurationManager($this->nav);
        $this->pnlConfiguration->Name = t('Frontend configuration options');

        $this->pnlContentTypesManagement = new ContentTypesManagements($this->nav);
        $this->pnlContentTypesManagement->Name = t('Content types management');
    }
}
SampleForm::run('SampleForm');
