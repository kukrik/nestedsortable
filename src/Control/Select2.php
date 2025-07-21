<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Project\Control\ControlBase;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Application;



class Select2 extends Select2ListBoxBaseGen
{
    public function  __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->removeCssClass("listbox");
        $this->registerFiles();
    }

    protected function registerFiles()
    {
        $this->addJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/select2.js");
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL. "/css/select2.css");
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL. "/css/select2-bootstrap.css");
    }

    public function getJqControlId()
    {
        return $this->ControlId;
    }

    public function getResetButtonHtml()
    {
        return "";
    }
}

//class Select2 extends Select2ListBoxBaseGen
//{
//    public function getJqControlId()
//    {
//        return $this->ControlId;
//    }
//
//    public function getResetButtonHtml()
//    {
//        return "";
//    }
//}

