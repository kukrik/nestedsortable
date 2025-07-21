<?php

namespace QCubed\Plugin\Control;

require_once ('FileInfoBaseGen.php');

use QCubed as Q;
use QCubed\Control\FormBase;
use QCubed\Control\ControlBase;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\Project\Application;
use QCubed\Type;
/**
 * Class FileInfo
 *
 * @package QCubed\Plugin
 */

class FileInfo extends FileInfoBaseGen
{
    /**
     * @param $objParentObject
     * @param $strControlId
     * @throws Caller
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->registerFiles();
    }

    /**
     * Registers the necessary JavaScript and CSS files for the component.
     *
     * @return void
     */
    protected function registerFiles()
    {
        $this->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/qcubed.fileinfo.js");
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/qcubed.fileinfo.css");
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/custom.css");
    }

    /**
     * Returns the HTML for the control.
     *
     * @return string
     */
    protected function getControlHtml()
    {
        $strHtml = '';
        $strHtml .= _nl(_indent('<div class="file-info-title">', 1));
        $strHtml .= _nl(_indent('<div class="caption" data-lang="file_info">File info</div>', 2));
        $strHtml .= _nl(_indent('</div>', 1));
        $strHtml .= _nl(_indent('<div class="file-info-body"></div>', 1));
        return $strHtml;
    }
}