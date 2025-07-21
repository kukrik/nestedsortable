<?php

namespace QCubed\Plugin\Control;

require ('FilePopupCroppieGen.php');

use QCubed\Bootstrap\Bootstrap;
use QCubed\Control\ControlBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class FilePopupCroppie
 *
 * Extends the functionality for a popup dialog that handles image cropping with Croppie.
 */

class FilePopupCroppie extends FilePopupCroppieGen
{
    /** @var bool make sure the popupCroppie gets rendered */
    protected $blnAutoRender = true;
    /** @var bool  PRAEGU EI TEA, default to auto open being false, since this would be a rare need, and dialogs are auto-rendered. */
    protected $blnAutoOpen = false;
    /** @var bool records whether dialog is open */
    protected $blnIsOpen = false;
    protected $blnIsChangeObject = false;
    protected $blnUseWrapper = true;

    /** @var string */
    protected $strHeaderTitle = 'Crop image';
    /** @var string */
    protected $strHeaderClass = 'bg-default';
    /** @var string */
    protected $strRotateClass = 'btn-default';
    /** @var string */
    protected $strSaveClass = 'btn-orange';
    /** @var string */
    protected $strSaveText = 'Crop and save';
    /** @var string */
    protected $strCancelClass = 'btn-default';
    /** @var string */
    protected $strCancelText = 'Cancel';
    /** @var string */
    protected $strFinalPath = null;

    /**
     * @param $objParentObject
     * @param $strControlId
     * @throws Caller
     */
    public function __construct($objParentObject = null, $strControlId = null)
    {
        // Detect which mode we are going to display in, whether to show right away, or wait for later.
        if ($objParentObject === null) {
            // The dialog will be shown right away, and then when closed, removed from the form.
            global $_FORM;
            $objParentObject = $_FORM;    // The parent object should be the form. Prevents spurious redrawing.
            $this->blnDisplay = true;
            $this->blnAutoOpen = true;
        } else {
            $this->blnAutoOpen = false;
            $this->blnDisplay = false;
        }

        parent::__construct($objParentObject, $strControlId);
        $this->registerFiles();
    }

    /**
     * Registers the necessary CSS and JavaScript files for the FileManager.
     *
     * @return void
     */
    protected function registerFiles() {
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/croppie.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/custom-switch.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/custom.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/font-awesome.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/awesome-bootstrap-checkbox.css");
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2.css");
        $this->addCssFile(QCUBED_BOOTSTRAP_CSS);
        $this->addCssFile(QCUBED_FILEMANAGER_ASSETS_URL . "/css/select2-web-vauu.css");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/qcubed.croppie.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/croppie.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/exif.js");
        $this->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/select2.js");
        Bootstrap::loadJS($this);
    }

    /**
     * Generates and returns the HTML for a modal control.
     *
     * @return string The generated HTML for the modal control.
     */
    public function getControlHtml()
    {
        $strHtml = '';

        $strHtml .= _nl('<div id="' . $this->ControlId . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">');
        $strHtml .= _nl(_indent('<div class="modal-dialog modal-lg" role="document" tabindex="-1">', 1));
        $strHtml .= _nl(_indent('<div class="modal-content">', 2));
        $strHtml .= _nl(_indent('<div class="modal-header ' . $this->strHeaderClass . '">', 3));
        $strHtml .= _nl(_indent('<button type="button" class="close" data-dismiss="modal" aria-label="Close">', 4));
        $strHtml .= _nl(_indent('<span aria-hidden="true">×</span>', 5));
        $strHtml .= _nl(_indent('</button>', 4));
        $strHtml .= _nl(_indent('<h4 class="modal-title" id="gridSystemModalLabel">' . t($this->strHeaderTitle) . '</h4>', 4));
        $strHtml .= _nl(_indent('</div>', 3));
        $strHtml .= _nl(_indent('<div class="modal-body">', 3));
        $strHtml .= _nl(_indent('<div class="row">', 4));
        $strHtml .= _nl(_indent('<div class="col-md-6">', 5));
        $strHtml .= _nl(_indent('<div class="img-responsive">', 6));
        $strHtml .= _nl(_indent('<div id="cropImage"></div>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('</div>', 5));
        $strHtml .= _nl(_indent('<div class="col-md-6">', 5));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Viewport:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-4">', 6));
        $strHtml .= _nl(_indent('<input id="viewportWidth" class="form-control" value="200" max="330" autocomplete="off" type="text" placeholder="' . t('Width') . '">', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-4">', 6));
        $strHtml .= _nl(_indent('<input id="viewportHeight" class="form-control" value="200" max="330" autocomplete="off" type="text" placeholder="' . t('Height') . '">', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Enable resize:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-8">', 6));
        $strHtml .= _nl(_indent('<div class="switch" id="enable-type">', 7));
        $strHtml .= _nl(_indent(t('Inactive'),8));
        $strHtml .= _nl(_indent('<label>', 8));
        $strHtml .= _nl(_indent('<input type="checkbox">', 9));
        $strHtml .= _nl(_indent('<span class="web-vauu"></span>', 9));
        $strHtml .= _nl(_indent('</label>', 8));
        $strHtml .= _nl(_indent(t('Active'),8));
        $strHtml .= _nl(_indent('</div>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Viewport type:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-8">', 6));
        $strHtml .= _nl(_indent('<select class="web-vauu-type" id="webVauuType" name="webVauuType" style="width:100%">', 7));
        $strHtml .= _nl(_indent('<option value="square" selected="selected">' . t('square') . '</option>', 8));
        $strHtml .= _nl(_indent('<option value="circle">' . t('circle') . '</option>', 8));
        $strHtml .= _nl(_indent('</select>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Rotate:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-4">', 6));
        $strHtml .= _nl(_indent('<button type="button" class="btn ' .  $this->strRotateClass . ' rotate-left" data-deg="-90">' . t('Rotate left') . '</button>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-4">', 6));
        $strHtml .= _nl(_indent('<button type="button" class="btn ' .  $this->strRotateClass . ' rotate-right" data-deg="90">' . t('Rotate right') . '</button>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('<label class="control-label col-md-4">' . t('Destination:') . '</label>', 6));
        $strHtml .= _nl(_indent('<div class="form-group col-md-8">', 6));
        $strHtml .= _nl(_indent('<select class="web-vauu-destination" id="webVauuDestination" name="webVauuDestination" style="width:100%">', 7));
        $strHtml .= _nl(_indent('<option></option>', 8));
        $strHtml .= _nl(_indent('</select>', 7));
        $strHtml .= _nl(_indent('</div>', 6));
        $strHtml .= _nl(_indent('</div>', 5));
        $strHtml .= _nl(_indent('</div>', 4));
        $strHtml .= _nl(_indent('</div>', 3));
        $strHtml .= _nl(_indent('<div class="modal-footer">', 3));
        $strHtml .= _nl(_indent('<button type="button" class="btn ' . $this->strSaveClass . ' btn-crop">' . t($this->strSaveText) . '</button>', 4));
        $strHtml .= _nl(_indent('<button type="button" class="btn ' . $this->strCancelClass . '" data-btnid="Cancel" data-dismiss="modal">' . t($this->strCancelText) . '</button>', 4));
        $strHtml .= _nl(_indent('</div>', 3));
        $strHtml .= _nl(_indent('</div>', 2));
        $strHtml .= _nl(_indent('</div>', 1));
        $strHtml .= '</div>';

        return $strHtml;
    }

    /**
     * @param $strName
     * @return array|bool|callable|float|int|mixed|string|null
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "HeaderTitle": return $this->strHeaderTitle;
            case "HeaderClass": return $this->strHeaderClass;
            case "RotateClass": return $this->strRotateClass;
            case "SaveClass": return $this->strSaveClass;
            case "SaveText": return $this-strSaveText;
            case "CancelClass": return $this->strCancelClass;
            case "CancelText": return $this->strCancelText;
            case 'FinalPath': return $this->strFinalPath;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * @param $strName
     * @param $mixValue
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case '_finalPath': // Internal only to output the cropped image name with relative path after saving.
                try {
                    $this->strFinalPath = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case '_IsChangeObject': // Internal only to detect when recording is triggered.
                try {
                    $this->blnIsChangeObject = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "HeaderTitle":
                try {
                    $this->strHeaderTitle = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "HeaderClass":
                try {
                    $this->strHeaderClass = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "RotateClass":
                try {
                    $this->strRotateClass = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "SaveClass":
                try {
                    $this->strSaveClass = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "SaveText":
                try {
                    $this->strSaveText = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "CancelClass":
                try {
                    $this->strCancelClass = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "CancelText":
                try {
                    $this->strCancelText = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}
