<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Control\Panel;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;
use QCubed\Exception\Caller;

/**
 *
 */
class DestinationInfo extends Panel
{
    protected Q\Plugin\Label $lblCheckTitle;
    protected Q\Plugin\Label $lblCheckPath;

    protected string $strTemplate = 'DestinationInfo.tpl.php';

    /**
     * Constructor for initializing a control or form object.
     *
     * @param ControlBase|FormBase $objParentObject Parent object that owns this control.
     * @param null $strControlId Optional control ID. If null, a default ID will be generated.
     *
     * @throws Caller
     */
    public function __construct(ControlBase|FormBase $objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
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