<?php

namespace QCubed\Plugin\Control;

require_once ('FileInfoBaseGen.php');


use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Exception\Caller;

/**
 * Class FileInfo
 *
 * @package QCubed\Plugin
 */

class FileInfo extends FileInfoBaseGen
{
    /**
     * Constructor for creating an instance of the class.
     *
     * @param ControlBase|FormBase $objParentObject The parent object this control belongs to.
     *                                              It can be an instance of either ControlBase or FormBase.
     * @param string|null $strControlId
     *
     * @throws Caller
     */
    public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->registerFiles();
    }

    /**
     * Registers required JavaScript and CSS files for the functionality of the file manager.
     *
     * @return void
     * @throws Caller
     */
    protected function registerFiles(): void
    {
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.fileinfo.js");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/qcubed.fileinfo.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/custom.css");
    }

    /**
     * Generates and returns the HTML markup for the control.
     *
     * @return string The HTML string representing the control.
     */
    protected function getControlHtml(): string
    {
        $strHtml = '';
        $strHtml .= _nl(_indent('<div class="file-info-title">', 1));
        $strHtml .= _nl(_indent('<div class="caption" data-lang="file_info">File info</div>', 2));
        $strHtml .= _nl(_indent('</div>', 1));
        $strHtml .= _nl(_indent('<div class="file-info-body"></div>', 1));
        return $strHtml;
    }
}