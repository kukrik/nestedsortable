<?php

/** This file contains the SmartMenus Class */

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Project\Control;
use QCubed\Project\Application;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Js;
use QCubed\Type;

/**
 * Class SmartMenus
 *
 * @property integer $Id
 * @property integer $ParentId
 * @property integer $Depth
 * @property integer $Left
 * @property integer $Right
 * @property string $MenuText
 * @property integer $Status
 * @property string $RedirectUrl
 * @property integer $HomelyUrl
 * @property string $ExternalUrl
 * @property string $TargetType
 * @property string $TagName
 * @property string $TagStyle
 * @property string $DataSource
 *
 * @package QCubed\Plugin
 */
class SmartMenus extends \QCubed\Project\Control\ControlBase
{
    use Q\Control\DataBinderTrait;

    /** @var string TagName */
    protected $strTagName = null;
    /** @var string TagStyle */
    protected $strTagStyle = null;
    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;

    protected $intCurrentDepth = 0;
    protected $intCounter = 0;

    /** @var integer Id */
    protected $intId = null;
    /** @var integer ParentId */
    protected $intParentId = null;
    /** @var integer Depth */
    protected $intDepth = null;
    /** @var integer Left */
    protected $intLeft = null;
    /** @var integer Right */
    protected $intRight = null;
    /** @var string MenuText */
    protected $strMenuText;
    /** @var int Status */
    protected $intStatus;
    /** @var string RedirectUrl */
    protected $strRedirectUrl;
    /** @var int HomelyUrl */
    protected $intHomelyUrl;
    /** @var string InternalUrl */
    protected $strExternalUrl;
    /** @var int TargetType */
    protected $strTargetType;

    /**
     * SmartMenus constructor.
     * @param Q\Control\ControlBase|FormBase $objParentObject
     * @param null $strControlId
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller  $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
        $this->registerFiles();
    }

    /**
     * Register required CSS and JavaScript files for the module.
     *
     * @return void
     */
    protected function registerFiles()
    {
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/smartmenus-1.1.0/addons/bootstrap/jquery.smartmenus.bootstrap.css");
        Bs\Bootstrap::loadJS($this);
        $this->addJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/smartmenus-1.1.0/jquery.smartmenus.js");
        $this->addJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/smartmenus-1.1.0/addons/bootstrap/jquery.smartmenus.bootstrap.js");
    }

    /**
     * Validates the current state.
     *
     * @return bool Always returns true.
     */
    public function validate() {return true;}

    /**
     * Parses the incoming POST data for processing.
     *
     * @return void
     */
    public function parsePostData() {}

    /**
     * Sets the node parameters callback.
     *
     * @param callable $callback The callback to set for node parameters.
     * @return void
     */
    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    /**
     * Retrieves raw item parameters using a callback function.
     *
     * @param mixed $objItem The item required to fetch its parameters.
     * @return array The raw parameters of the item including 'id', 'parent_id', 'depth',
     *               'left', 'right', 'menu_text', 'status', 'redirect_url', 'homely_url', 'target_type'.
     * @throws \Exception If the nodeParamsCallback is not set.
     */
    public function getItemRaw($objItem)
    {
        if (!$this->nodeParamsCallback) {
            throw new \Exception("Must provide an nodeParamsCallback");
        }
        $params = call_user_func($this->nodeParamsCallback, $objItem);

        $intId = '';
        if (isset($params['id'])) {
            $intId = $params['id'];
        }
        $intParentId = '';
        if (isset($params['parent_id'])) {
            $intParentId = $params['parent_id'];
        }
        $intDepth = '';
        if (isset($params['depth'])) {
            $intDepth = $params['depth'];
        }
        $intLeft = '';
        if (isset($params['left'])) {
            $intLeft = $params['left'];
        }
        $intRight = '';
        if (isset($params['right'])) {
            $intRight = $params['right'];
        }
        $strMenuText = '';
        if (isset($params['menu_text'])) {
            $strMenuText = $params['menu_text'];
        }
        $intStatus = '';
        if (isset($params['status'])) {
            $intStatus = $params['status'];
        }
        $strRedirectUrl = '';
        if (isset($params['redirect_url'])) {
            $strRedirectUrl = $params['redirect_url'];
        }
        $intHomelyUrl = '';
        if (isset($params['homely_url'])) {
            $intHomelyUrl = $params['homely_url'];
        }
        $strExternalUrl = '';
        if (isset($params['external_url'])) {
            $strExternalUrl = $params['external_url'];
        }
        $strTargetType = '';
        if (isset($params['target_type'])) {
            $strTargetType = $params['target_type'];
        }

        $vars = [
            'id' => $intId,
            'parent_id' => $intParentId,
            'depth' => $intDepth,
            'left' => $intLeft,
            'right' => $intRight,
            'menu_text' => $strMenuText,
            'status' => $intStatus,
            'redirect_url' => $strRedirectUrl,
            'homely_url' => $intHomelyUrl,
            'external_url' => $strExternalUrl,
            'target_type' => $strTargetType
            ];
        return $vars;
    }

    /**
     * Puts the current object into a sleep state by handling the node parameters callback
     * through the ControlBase's sleepHelper method and then invoking the parent's sleep
     * method.
     *
     * @return void
     */
    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
        parent::sleep();
    }

    /**
     * Restores the state of the current object by invoking the parent's wakeup method
     * with the given FormBase object and then calling ControlBase's wakeupHelper method
     * to manage the node parameters callback.
     *
     * @param FormBase $objForm The form object used to restore the state of the current object.
     * @return void
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
    }

    /**
     * Generates an HTML representation of a menu tree based on an array of menu parameters.
     *
     * @param array $arrParams An array containing menu parameters. Each element in the array should be an
     *                         associative array with the following keys: 'id', 'parent_id', 'depth', 'left',
     *                         'right', 'menu_text', 'status', 'redirect_url', 'homely_url', 'external_url', 'target_type'.
     *
     * @return string HTML string representing the menu tree.
     */
    protected function renderMenuTree($arrParams)
    {
        $strHtml = '';

        for ($i = 0; $i < count($arrParams); $i++)
        {
            $this->intId = $arrParams[$i]['id'];
            $this->intParentId = $arrParams[$i]['parent_id'];
            $this->intDepth = $arrParams[$i]['depth'];
            $this->intLeft = $arrParams[$i]['left'];
            $this->intRight = $arrParams[$i]['right'];
            $this->strMenuText = $arrParams[$i]['menu_text'];
            $this->intStatus = $arrParams[$i]['status'];
            $this->strRedirectUrl = $arrParams[$i]['redirect_url'];
            $this->intHomelyUrl = $arrParams[$i]['homely_url'];
            $this->strExternalUrl = $arrParams[$i]['external_url'];
            $this->strTargetType = $arrParams[$i]['target_type'];

            if ($this->intStatus == 2 || $this->intStatus == 3) {
                continue;
            }

            $target = '';
            if (!empty($this->strTargetType)) {
                $target = ' target="' . $this->strTargetType . '"';
            }

            // We determine the correct link
            $link = ($this->intHomelyUrl === 1) ? $this->strRedirectUrl : $this->strExternalUrl;

            if ($this->intDepth == $this->intCurrentDepth) {
                if ($this->intCounter > 0) $strHtml .= '</li>';
            } elseif ($this->intDepth > $this->intCurrentDepth) {
                $strHtml .= _nl() . '<' . $this->strTagName . ' class="' . $this->strTagStyle . '">';
                $this->intCurrentDepth = $this->intCurrentDepth + ($this->intDepth - $this->intCurrentDepth);
            } elseif ($this->intDepth < $this->intCurrentDepth) {
                $strHtml .= str_repeat('</li>' . _nl() . '</' . $this->strTagName . '>', $this->intCurrentDepth - $this->intDepth) . '</li>';
                $this->intCurrentDepth = $this->intCurrentDepth - ($this->intCurrentDepth - $this->intDepth);
            }

            $strHtml .= _nl() . '<li id="' . $this->ControlId . '_' . $this->intId . '">';
            $strHtml .= '<a href="' . $link . '"' . $target . '>';
            $strHtml .= $this->strMenuText;

            if ($this->Right !== $this->Left + 1) {
                $strHtml .= '<span class="caret"></span></a>';
            }

            $strHtml .= '</a>';
            ++$this->intCounter;
        }

        $strHtml .= str_repeat('</li>' . _nl() . '</' . $this->strTagName . '>', $this->intDepth) . '</li>';

        return $strHtml;
    }

    /**
     * Generates the HTML for the control by first binding data to the source,
     * processing each data item, rendering a menu tree, and finally wrapping
     * the rendered content in a specified HTML tag.
     *
     * @return string The generated HTML for the control.
     */
    protected function getControlHtml()
    {
        $this->dataBind();

        if (empty($this->objDataSource)) {
            $this->objDataSource = null;
        }

        $strParams = [];

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                $strParams[] = $this->getItemRaw($objObject);
            }
        }

        $strOut = $this->renderMenuTree($strParams);
        $strHtml = $this->renderTag($this->strTagName, null, null, $strOut);

        $this->objDataSource = null;
        return $strHtml;
    }

    /**
     * Binds the data to the object by running the DataBinder if the data source is null,
     * the object has a DataBinder, and has not been rendered. If the DataBinder call fails,
     * it catches the exception, increments its offset, and rethrows it.
     *
     * @return void
     */
    public function dataBind()
    {
        // Run the DataBinder (if applicable)
        if (($this->objDataSource === null) && ($this->hasDataBinder()) && (!$this->blnRendered)) {
            try {
                $this->callDataBinder();
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }
    }

    /**
     * Initializes the JQuery widget for the control. It sets up event listeners for
     * user interaction and executes necessary JavaScript commands to ensure proper
     * functionality of the sidebar menu and link activation.
     *
     * @return void
     */
    public function makeJqWidget()
    {
        /**
         * To draw or test the menu, the js code is temporarily placed here at the end: "return false;".
         * This part of the code usually needs to be changed to "return true;" for the links to work properly.
         */
        Application::executeControlCommand($this->ControlId, 'on', 'click', 'li',
            new Js\Closure("jQuery(this).trigger('sidebarselect', this.id); return true;"),
            Application::PRIORITY_HIGH);

        /**
         * For production, it is recommended to start activating the "Home" link.
         * The following is intended to introduce such an opportunity.
         */
        Application::executeJavaScript(sprintf("jQuery('#{$this->ControlId}_1').find('a').addClass('active')"));

        Application::executeSelectorFunction(".smartside", "on", "click", "a",
            new Js\Closure("jQuery('a.active').removeClass('active'); jQuery(this).addClass('active');"),
            Application::PRIORITY_HIGH);
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////

    /**
     * @param string $strName
     * @return array|mixed|string
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "Id": return $this->intId;
            case "ParentId": return $this->intParentId;
            case "Depth": return $this->intDepth;
            case "Left": return $this->intLeft;
            case "Right": return $this->intRight;
            case "MenuText": return $this->strMenuText;
            case "Status": return $this->intStatus;
            case "RedirectUrl": return $this->strRedirectUrl;
            case "HomelyUrl": return $this->intHomelyUrl;
            case "ExternalUrl": return $this->strExternalUrl;
            case "TargetType": return $this->strTargetType;
            case "TagName": return $this->strTagName;
            case "TagStyle": return $this->strTagStyle;
            case "DataSource": return $this->objDataSource;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////

    /**
     * @param string $strName
     * @param string $mixValue
     * @throws Caller
     * @throws InvalidCast
     * @throws \Exception
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Id":
                try {
                    //$this->blnModified = true;
                    $this->intId = Type::Cast($mixValue, Type::INTEGER);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "ParentId":
                try {
                    $this->blnModified = true;
                    $this->intParentId = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Depth":
                try {
                    $this->blnModified = true;
                    $this->intDepth = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Left":
                try {
                    $this->blnModified = true;
                    $this->intLeft = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Right":
                try {
                    $this->blnModified = true;
                    $this->intRight = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "MenuText":
                try {
                    $this->blnModified = true;
                    $this->strMenuText = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Status":
                try {
                    $this->blnModified = true;
                    $this->intStatus = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "RedirectUrl":
                try {
                    $this->blnModified = true;
                    $this->strRedirectUrl = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "HomelyUrlUrl":
                try {
                    $this->blnModified = true;
                    $this->intHomelyUrl = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "ExternalUrl":
                try {
                    $this->blnModified = true;
                    $this->strExternalUrl = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "TargetType":
                try {
                    $this->blnModified = true;
                    $this->strTargetType = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "TagName":
                try {
                    $this->blnModified = true;
                    $this->strTagName = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "TagStyle":
                try {
                    $this->blnModified = true;
                    $this->strTagStyle = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "DataSource":
                $this->objDataSource = $mixValue;
                $this->blnModified = true;
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

}