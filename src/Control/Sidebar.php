<?php

/** This file contains the Sidebar Class */

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Control\ControlBase;
use QCubed\Project\Control;
use QCubed\Project\Application;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Js;
use QCubed\Type;
use QCubed\Html;

/**
 * Class Sidebar
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
 * @property string $SubTagName
 * @property string $SubTagClass
 * @property mixed $DataSource
 * @property mixed $AssignedItems
 *
 * @package QCubed\Plugin
 */
class Sidebar extends \QCubed\Control\Panel
{
    use Q\Control\DataBinderTrait;

    /** @var string SubTagName */
    protected $strSubTagName = null;
    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;
    /** @var array AssignedItems from which the items are predefined */
    protected $objAssignedItems;

    /**
     * @var
     */
    protected $strParams;
    protected $strObjects;

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
    /** @var int IsHomelyUrl */
    protected $intHomelyUrl;
    /** @var string InternalUrl */
    protected $strExternalUrl;
    /** @var int TargetType */
    protected $strTargetType;


    /**
     * Sidebar constructor.
     * @param ControlBase|FormBase $objParentObject
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
     * Registers the necessary CSS and JavaScript files for Bootstrap and Font Awesome.
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
     * Validates the current object or data.
     *
     * @return bool Always returns true indicating successful validation.
     */
    public function validate() {return true;}

    /**
     * Parses the data received from a POST request.
     *
     * @return void
     */
    public function parsePostData() {}

    /**
     * Sets the callback function that will create node parameters.
     *
     * @param callable $callback The function to be used for creating node parameters.
     * @return void
     */
    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    /**
     * Retrieves raw item data based on the provided object item.
     *
     * @param mixed $objItem The item object to extract raw data from.
     * @return array An associative array containing raw item parameters such as 'id', 'parent_id', 'depth', 'left', 'right', 'menu_text', 'status', 'redirect_url', 'homely_url', and 'target_type'.
     * @throws \Exception If the nodeParamsCallback is not provided.
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
     * Generate HTML for the control based on the data source and assigned items.
     *
     * @return string|null The generated HTML or null if the data source or assigned items are not set.
     */
    protected function getControlHtml()
    {
        $this->dataBind();

        if (empty($this->objDataSource)) {
            $this->objDataSource = null;
        }

        if (empty($this->objAssignedItems)) {
            $this->objAssignedItems = null;
        }

        $this->strParams = [];

        if ($this->objDataSource && $this->objAssignedItems) {
            foreach ($this->objDataSource as $objObject) {
                if (in_array($objObject->Id, $this->objAssignedItems))
                    $this->strParams[] = $this->getItemRaw($objObject);
            }

            $strHtml = $this->renderMenuTree($this->strParams);
            $this->objDataSource = null;
            $this->objAssignedItems = null;
            return $strHtml;
        }
    }

    /**
     * Binds data to the component by running the DataBinder if the data source is not set,
     * the component has a DataBinder, and the component has not yet been rendered.
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
     * Puts the current process to sleep by handling node parameters callback through sleepHelper.
     *
     * @return void
     */
    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
        parent::sleep();
    }

    /**
     * Wakes up the current process by handling node parameters callback through wakeupHelper.
     *
     * @param FormBase $objForm The form object to be used during the wakeup process.
     * @return void
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
    }

    /**
     * Renders a nested menu tree based on the given parameters.
     *
     * @param array $arrParams An array of menu node parameters, where each node contains
     *                         details like id, parent_id, depth, left, right, menu_text,
     *                         status, redirect_url, homely_url, external_url, and target_type.
     * @return string The generated HTML string representing the menu tree.
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
            } elseif ($this->intDepth> $this->intCurrentDepth) {
                $strHtml .= _nl() . '<' . $this->strSubTagName . '>';
                $this->intCurrentDepth = $this->intCurrentDepth + ($this->intDepth - $this->intCurrentDepth);
            } elseif ($this->intDepth < $this->intCurrentDepth) {
                $strHtml .= str_repeat('</li>' . _nl() . '</' . $this->strSubTagName . '>', $this->intCurrentDepth - $this->intDepth) . '</li>';
                $this->intCurrentDepth = $this->intCurrentDepth - ($this->intCurrentDepth - $this->intDepth);
            }

            $strHtml .= _nl() . '<li id="' . $this->ControlId . '_' . $this->intId . '">';
            $strHtml .= '<a href="' . $link . '"' . $target . '>';
            $strHtml .= $this->strMenuText;

            $strHtml .= '</a>';
            ++$this->intCounter;
        }

        $strHtml .= str_repeat('</li>' . _nl() . '</' . $this->strSubTagName . '>', $this->intDepth) . '</li>';

        return $strHtml;
    }

    /**
     * Retrieves a list of child element IDs from the provided array of objects based on the specified parent ID.
     *
     * @param array $objArrays An array of objects, each expected to have 'ParentId' and 'Id' properties.
     * @param mixed $value The parent ID used to filter child elements. Default is null.
     * @return array An array containing IDs of all child elements.
     */
    public function getChildren($objArrays, $value = null)
    {
        $objTempArray = [];
        foreach ($objArrays as $objMenu) {
            if($objMenu->ParentId == $value) {
                $objTempArray[] = $objMenu->Id;
                array_push($objTempArray, ...$this->getChildren($objArrays, $objMenu->Id));
            }
        }
        return $objTempArray;
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
     * Creates a jQuery widget for handling submenu item clicks and highlighting the active link.
     * This method registers a JavaScript function that adds an 'active' class to the clicked submenu item
     * and removes it from any previously active items.
     *
     * @return void
     */
    public function makeJqWidget()
    {
        /**
         * To draw or test the menu, the js code is temporarily placed here at the end: "return false;".
         * This part of the code usually needs to be changed to "return true;" for the links to work properly.
         */

        Application::executeSelectorFunction(".submenu", "on", "click", "a",
            new Js\Closure("jQuery('a.active').removeClass('active'); jQuery(this).addClass('active');
            return false;"),
            Application::PRIORITY_HIGH);
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////

    /**
     * @param string $strName
     * @return array|int|mixed|string
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
            case "SubTagName": return $this->strSubTagName;
            case "DataSource": return $this->objDataSource;
            case "AssignedItems": return $this->objAssignedItems;

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
                    $this->blnModified = true;
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
            case "SubTagName":
                try {
                    $this->blnModified = true;
                    $this->strSubTagName = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "DataSource":
                $this->objDataSource = $mixValue;
                $this->blnModified = true;
                break;
            case "AssignedItems":
                try {
                    $this->blnModified = true;
                    $this->objAssignedItems = Type::Cast($mixValue, Type::ARRAY_TYPE);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
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