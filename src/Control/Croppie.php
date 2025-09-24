<?php

namespace QCubed\Plugin\Control;

use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;
use QCubed\Exception\Caller;

/**
 * Class Croppie
 * @package QCubed\Plugin
 */

class Croppie extends CroppieGen
{
    /**
     * Constructor method.
     *
     * @param ControlBase|FormBase $objParentObject The parent object with which this control is associated.
     * @param string|null $strControlId Optional string specifying the control ID.
     *
     * @return void
     * @throws Caller
     */
    public function __construct(ControlBase|FormBase$objParentObject, ?string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->registerFiles();
    }

    /**
     * Registers the necessary JavaScript and CSS files for the control.
     *
     * @return void
     * @throws Caller
     */
    protected function registerFiles(): void
    {
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/croppie.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/exif.js");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL. "/css/croppie.css");
    }
}
