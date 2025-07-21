<?php

/** This file contains the SidebarList Class */

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control;
use QCubed\Project\Application;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Js;
use QCubed\Type;

/**
 * Class SidebarList
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
 * @property mixed $DataSource
 *
 * @package QCubed\Plugin
 */
class SidebarList extends \QCubed\Project\Control\ControlBase
{
    use Q\Control\DataBinderTrait;

    /** @var string TagName */
    protected $strTagName = null;
    /** @var string TagStyle */
    protected $strTagClass = null;
    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;

    protected $strParams = [];

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
    /** @var integer IsHomelyUrl */
    protected $intHomelyUrl;
    /** @var string InternalUrl */
    protected $strExternalUrl;
    /** @var integer TargetType */
    protected $strTargetType;

    /**
     * SidebarList constructor.
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
     * Registers necessary CSS and JavaScript files.
     *
     * @return void
     */
    protected function registerFiles()
    {
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
        Bs\Bootstrap::loadJS($this);
    }

    /**
     * Validates the given input data.
     *
     * @return bool Always returns true, indicating the validation is successful.
     */
    public function validate() {return true;}

    /**
     * Parses the incoming POST data and processes it according to the application's requirements.
     *
     * @return void
     */
    public function parsePostData() {}

    /**
     * Sets the callback function for node parameters.
     *
     * @param callable $callback The callback function to assign for node parameters.
     * @return void
     */
    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    /**
     * Retrieves raw item data based on the provided object item.
     *
     * @param mixed $objItem Object item to be processed.
     * @return array An associative array of item raw data including keys:
     *               'id', 'parent_id', 'depth', 'left', 'right', 'menu_text',
     *               'status', 'redirect_url', 'homely_url', 'target_type'.
     * @throws \Exception If nodeParamsCallback is not provided.
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
     * Prepares the object for serialization by updating the nodeParamsCallback
     * with the serialized version returned by the sleepHelper method.
     *
     * @return void
     */
    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
        parent::sleep();
    }

    /**
     * Restores the object state after deserialization. It updates the
     * nodeParamsCallback using the wakeupHelper method with the provided form object.
     *
     * @param FormBase $objForm The form object used to restore the state.
     * @return void
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
    }

    /**
     * Renders the menu tree in HTML format based on the given parameters array.
     *
     * @param array $arrParams An array of associative arrays containing menu item parameters.
     *                         Each associative array should include 'id', 'parent_id', 'depth',
     *                         'left', 'right', 'menu_text', 'status', 'redirect_url', 'homely_url',
     *                         and 'target_type' keys.
     *
     * @return string A string representing the HTML of the rendered menu tree.
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

            $target = '';
            if (!empty($this->strTargetType)) {
                $target = ' target="' . $this->strTargetType . '"';
            }

            // We determine the correct link
            $link = ($this->intHomelyUrl === 1) ? $this->strRedirectUrl : $this->strExternalUrl;

            if (($this->intStatus !== 2 && $this->intStatus !== 3) && $this->intDepth == 0) {
                $strHtml .= _nl() . '<li id="' . $this->ControlId . '_' . $this->intId . '">';
                $strHtml .= '<a href="' . $link . '"' . $target . '>';
                $strHtml .= $this->strMenuText;

                if ($this->Right !== $this->Left + 1) {
                    $strHtml .= '<span class="caret"></span></a>';
                }

                $strHtml .= '</a>';
                $strHtml .= '</li>';
                }
        }
        return $strHtml;
    }

    /**
     * Generates and returns the HTML for the control. It binds data, processes the data source,
     * and constructs the HTML by rendering the menu tree and wrapping it in the appropriate tag.
     *
     * @return string The resulting HTML of the control.
     */
    protected function getControlHtml()
    {
        $this->dataBind();

        if (empty($this->objDataSource)) {
            $this->objDataSource = null;
        }

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                $this->strParams[] = $this->getItemRaw($objObject);
            }
        }

        if ($this->strTagClass) {
            $attributes['class'] = $this->strTagClass;
        } else {
            $attributes = '';
        }
        $strOut = $this->renderMenuTree($this->strParams);
        $strHtml = $this->renderTag($this->strTagName, $attributes, null, $strOut);

        $this->objDataSource = null;
        return $strHtml;
    }

    /**
     * Binds data to the object by calling the data binder method if the object
     * is not already rendered, there is no data source already present, and
     * a data binder is defined. If an exception occurs during the binding process,
     * the exception offset is incremented before being thrown.
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
     * Generated method overrides the built-in Control method, causing it to not redraw completely. We restore
     * its functionality here.
     */
    public function refresh()
    {
        parent::refresh();
        ControlBase::refresh();
    }

    /**
     * Sets up a jQuery widget with specified behaviors for the sidebar menu.
     *
     * Attaches click event handlers to the list items and anchors within the sidebar menu,
     * triggering custom 'sidebarselect' events and managing 'active' state for clicked elements.
     *
     * This method also initializes the "Home" link as active by default.
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
            new Js\Closure("jQuery(this).trigger('sidebarselect', this.id); return false;"),
            Application::PRIORITY_HIGH);

        /**
         * For production, it is recommended to start activating the "Home" link.
         * The following is intended to introduce such an opportunity.
         */
        Application::executeJavaScript(sprintf("jQuery('.sidemenu #{$this->ControlId}_1').find('a').addClass('active')"));

        Application::executeSelectorFunction(".sidemenu", "on", "click", "a",
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
            case "TagClass": return $this->strTagClass;
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
            case "TagClass":
                try {
                    $this->blnModified = true;
                    $this->strTagClass = Type::Cast($mixValue, Type::STRING);
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