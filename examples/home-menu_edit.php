<?php
require('qcubed.inc.php');
require ('classes/HomePageEditPanel.class.php');
require ('classes/HomePageMetaDataPanel.class.php');

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
    protected $pnlContent;
    protected $pnlMetadata;
    protected $objMenuContent;

    protected function formCreate()
    {
        parent::formCreate();

        $intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenuContent = MenuContent::load($intId);

        $this->nav = new Q\Plugin\Control\Tabs($this);
        $this->nav->addCssClass('tabbable tabbable-custom');

        if ($this->objMenuContent->ContentType == null) {
            $this->pnlPage = new PageEditPanel($this->nav);
            $this->pnlPage->Name = t('Configure page');
        } else {
            $objPanelName = ContentType::toClassNames($this->objMenuContent->ContentType);
            $this->pnlContent = new $objPanelName($this->nav);
            $this->pnlContent->Name = ContentType::toTabsText($this->objMenuContent->ContentType);

            if ($this->objMenuContent->ContentType !== 3 // NewsEditEditPanel
                && $this->objMenuContent->ContentType !== 5 // EventsCalendarEditPanel
                && $this->objMenuContent->ContentType !== 7 // InternalPageEditPanel
                && $this->objMenuContent->ContentType !== 8 // RedirectingEditPanel
                && $this->objMenuContent->ContentType !== 9 // PlaceholderEditPanel
                && $this->objMenuContent->ContentType !== 10 // ErrorPageEditPanel
            ) {
                $this->pnlMetadata = new HomePageMetaDataPanel($this->nav);
                $this->pnlMetadata->Name = t('Metadata');
            }


        }

    }
}

SampleForm::run('SampleForm');