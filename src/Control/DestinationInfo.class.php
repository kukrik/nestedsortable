<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;

/**
 * A panel control that displays information about a destination.
 *
 * This control includes labels for the title and path of the destination.
 */
class DestinationInfo extends Q\Control\Panel
{
    protected $lblCheckTitle;
    protected $lblCheckPath;

    protected $strTemplate = 'DestinationInfo.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->lblCheckTitle = new Q\Plugin\Label($this);
        $this->lblCheckTitle->Text = t('Destination: ');
        $this->lblCheckTitle->setCssStyle('font-weight', 600);
        $this->lblCheckTitle->setCssStyle('padding-right', '5px');
        $this->lblCheckTitle->setCssStyle('padding-bottom', '25px');
        $this->lblCheckTitle->UseWrapper = false;

        $this->lblCheckPath = new Q\Plugin\Label($this);
        $this->lblCheckPath->addCssClass("modalPath");
        $this->lblCheckPath->setCssStyle('font-weight', 400);
        $this->lblCheckPath->setCssStyle('padding-bottom', '25px');
        $this->lblCheckPath->UseWrapper = false;
    }
}