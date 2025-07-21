<?php

namespace QCubed\Plugin\Control;

use QCubed\Bootstrap as Bs;
use QCubed\Control\ListControl;
use QCubed\Control\RadioButtonList;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Html;
use QCubed\TagStyler;
use QCubed\Type;

/**
 * Class RadioList
 *
 * Bootstrap specific drawing of a \QCubed\Control\RadioButtonList
 *
 * Modes:
 * 	ButtonModeNone	Display as standard radio buttons using table styling if specified
 *  ButtonModeJq	Display as separate radio buttons styled with bootstrap styling
 *  ButtonModeSet	Display as a button group
 *  ButtonModeList	Display as standard radio buttons with no structure
 *
 * @property string $ButtonGroupClass Allows you to set the theme.
 * @property string $GroupName assigns the radio button into a radio button group (optional) so that no more than one radio in that group may be selected at a time.
 * @property boolean $Checked specifies whether or not the radio is selected
 *
 * @property-write string ButtonStyle Bootstrap::ButtonPrimary, ButtonSuccess, etc.
 * @package QCubed\Bootstrap
 */
class RadioList extends RadioButtonList
{
    protected $strButtonGroupClass = "radio";
    protected $blnChecked;
    protected $strButtonStyle = Bs\Bootstrap::BUTTON_DEFAULT;

    /**
     * Group to which this radio button belongs
     * Groups determine the 'radio' behavior wherein you can select only one option out of all buttons in that group
     * @var null|string Name of the group
     */
    protected $strGroupName = null;

    /**
     * Used by drawing routines to render the attributes associated with this control.
     *
     * @param null|array $attributeOverrides
     * @param null|array $styleOverrides
     * @return string
     */
    public function renderHtmlAttributes($attributeOverrides = null, $styleOverrides = null)
    {
        if ($this->intButtonMode == RadioButtonList::BUTTON_MODE_SET) {
            $attributeOverrides["data-toggle"] = "buttons";
            $attributeOverrides["class"] = $this->CssClass;
            Html::addClass($attributeOverrides["class"], "btn-group");
        }
        return parent::renderHtmlAttributes($attributeOverrides, $styleOverrides);
    }

    /**
     * Overrides the radio list get end script to prevent the default JQueryUi functionality.
     * @return string
     */
    public function getEndScript()
    {
        $strScript = ListControl::getEndScript();    // bypass the \QCubed\Control\RadioButtonList end script
        return $strScript;
    }

    /**
     * @param string $strName
     * @param string $mixValue
     * @throws Caller
     * @throws InvalidCast
     * @return void
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            // APPEARANCE
            case "ButtonStyle":
                try {
                    $this->objItemStyle->removeCssClass($this->strButtonStyle);
                    $this->strButtonStyle = Type::cast($mixValue, Type::STRING);
                    $this->objItemStyle->addCssClass($this->strButtonStyle);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
            case "ButtonMode":    // inherited
                try {
                    if ($mixValue === self::BUTTON_MODE_SET) {
                        $this->objItemStyle->setCssClass("btn");
                        $this->objItemStyle->addCssClass($this->strButtonStyle);
                        parent::__set($strName, $mixValue);
                    }
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
            case "GroupName":
                try {
                    $strGroupName = Type::cast($mixValue, Type::STRING);
                    if ($this->strGroupName != $strGroupName) {
                        $this->strGroupName = $strGroupName;
                        $this->blnModified = true;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Checked":
                try {
                    $val = Type::cast($mixValue, Type::BOOLEAN);
                    if ($val != $this->blnChecked) {
                        $this->blnChecked = $val;
                        if ($this->GroupName && $val == true) {
                            Application::executeJsFunction('qcubed.setRadioInGroup', $this->strControlId);
                        } else {
                            $this->addAttributeScript('prop', 'checked', $val); // just set the one radio
                        }
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "ButtonGroupClass":
                $this->strButtonGroupClass = Type::cast($mixValue, Type::STRING);
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * @param string $strName
     * @return mixed
     * @throws Caller
     */
    public function __get($strName) {
        switch ($strName) {
            case "GroupName": return $this->strGroupName;
            case "Checked": return $this->blnChecked;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}