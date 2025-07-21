<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Control;
use QCubed\Html;
use QCubed\Project\Control\ControlBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class InfoBox
 * @package QCubed\Plugin
 */

/**
 * @property string $Text
 *
 * @package QCubed\Plugin
 */

class InfoBox extends Q\Control\Panel
{
    /** @var string */
    protected $strtext = null;

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->addCssFile(QCUBED_NANOGALLERY_ASSETS_URL . "/css/infobox.css");
    }

    protected function getControlHtml()
    {
        $strHtml = "";

        $strOut = Html::renderTag('p', null, $this->Text);
        $strHtml .= $this->renderTag('div', null, null, $strOut);

        return $strHtml;
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'Text': return $this->strText;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'Text':
                try {
                    $this->blnModified = true;
                    $this->strText = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

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