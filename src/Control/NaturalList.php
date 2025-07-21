<?php

/** This file contains the MenuPanel Class */

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control;
use QCubed\Project\Application;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Control\BlockControl;
use QCubed\Html;
use QCubed\Js;
use QCubed\Type;

/**
 * Class MenuPanelBase
 * @property integer $Id
 * @property integer $ParentId
 * @property integer $Depth
 * @property integer $Left
 * @property integer $Right
 * @property string $MenuText
 * @property integer $Status
 * @property string $TagName
 * @property mixed $DataSource
 *
 * @package QCubed\Plugin
 */
class NaturalList extends ControlBase
{
    use Q\Control\DataBinderTrait;

    /** @var string TagName */
    protected $strTagName = null;
    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;

    protected $intCurrentDepth = 0;
    protected $intCounter = 0;

    /** @var  integer Id */
    protected $intId = null;
    /** @var  integer ParentId */
    protected $intParentId = null;
    /** @var  integer Depth */
    protected $intDepth = null;
    /** @var  integer Left */
    protected $intLeft = null;
    /** @var  integer Right */
    protected $intRight = null;
    /** @var  string MenuText */
    protected $strMenuText;
    /** @var  integer Status */
    protected $intStatus;

    /**
     * MenuPanelBase constructor.
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
     * Registers the necessary CSS and JavaScript files for the functionality.
     *
     * @return void
     */
    protected function registerFiles()
    {
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/menuexample.css");
        Bs\Bootstrap::loadJS($this);
    }

    /**
     * Validates the current context.
     *
     * @return bool Always returns true indicating validation success.
     */
    public function validate() {return true;}

    public function parsePostData() {}

    /**
     * Registers a callback to create node parameters.
     *
     * @param callable $callback A function to create node parameters.
     * @return void
     */
    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    /**
     * Retrieves raw item data based on the provided callback.
     *
     * @param mixed $objItem The item to retrieve data for.
     * @return array The raw item data.
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

        $vars = [
            'id' => $intId,
            'parent_id' => $intParentId,
            'depth' => $intDepth,
            'left' => $intLeft,
            'right' => $intRight,
            'menu_text' => $strMenuText,
            'status' => $intStatus
            ];

        return $vars;
    }

    /**
     * Sets the node parameters callback to a state suitable for serialization
     * and then calls the parent's sleep method.
     *
     * @return void
     */
    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
        parent::sleep();
    }

    /**
     * Restores the node parameters callback from its serialized state and then
     * calls the parent's wakeup method with the given FormBase object.
     *
     * @param FormBase $objForm The form object that is passed to the parent's wakeup method.
     * @return void
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
    }

    protected function renderMenuTree($arrParams)
    {
        $strHtml = '';
        $this->intCurrentDepth = 0; // Algne sügavus

        for ($i = 0; $i < count($arrParams); $i++) {
            // We are loading the data of the currently active node
            $this->intId = $arrParams[$i]['id'];
            $this->intParentId = $arrParams[$i]['parent_id'];
            $this->intDepth = $arrParams[$i]['depth'];
            $this->strMenuText = $arrParams[$i]['menu_text'];
            $this->intStatus = $arrParams[$i]['status'];

            // We avoid statuses that are not recommended to be shown
            if ($this->intStatus === 2 || $this->intStatus === 3) {
                continue;
            }

            // As the depth increases, we close the previous <li> and open a new <ol> or <ul>
            if ($this->intDepth > $this->intCurrentDepth) {
                $strHtml .= _nl() . '<' . $this->strTagName . '>'; // We open a new <ol> or <ul>
            }

            //As the depth decreases, we close the remaining <li> and <ol> or <ul> accordingly
            while ($this->intDepth < $this->intCurrentDepth) {
                $strHtml .= '</li>' . _nl() . '</' . $this->strTagName . '>';
                $this->intCurrentDepth--; // We are reducing the current depth
            }

            // At the same depth, we close the previous <li> if it exists
            if ($this->intCounter > 0 && $this->intDepth === $this->intCurrentDepth) {
                $strHtml .= '</li>';
            }

            // We create a new <li> element with its content and attributes
            $strHtml .= _nl() . '<li id="' . $this->strControlId . '_' . $this->intId . '">';
            $strHtml .= $this->strMenuText;

            // We increase the node counter and adjust the current depth
            ++$this->intCounter;
            $this->intCurrentDepth = $this->intDepth;
        }

        // Finally, we close all remaining open levels
        while ($this->intCurrentDepth > 0) {
            $strHtml .= '</li>' . _nl() . '</' . $this->strTagName . '>';
            $this->intCurrentDepth--;
        }

        return $strHtml;
    }

    /**
     * Binds data to the control, processes the data source, renders the menu tree,
     * and returns the generated HTML string for the control.
     *
     * @return string The generated HTML string for the control.
     */
    protected function getControlHtml()
    {
        $this->dataBind();

        if (empty($this->objDataSource)) {
            $this->objDataSource = null;
            /////////////////////////////
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
     * Executes the data binder if applicable and the data source is not set,
     * and the control has not been rendered yet. Any exception caught during
     * the execution will have its offset incremented and rethrown.
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
     * Creates a jQuery widget using the provided parameters.
     *
     * @return void
     */
    public function makeJqWidget()
    {}

    /////////////////////////
    // Public Properties: GET
    /////////////////////////

    public function __get($strName)
    {
        switch ($strName) {
            case "Id":
                return $this->intId;
            case "ParentId":
                return $this->intParentId;
            case "Depth":
                return $this->intDepth;
            case "Left":
                return $this->intLeft;
            case "Right":
                return $this->intRight;
            case "MenuText":
                return $this->strMenuText;
            case "Status":
                return $this->intStatus;
            case "TagName":
                return $this->strTagName;
            case "DataSource":
                return $this->objDataSource;

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
            case "TagName":
                try {
                    $this->blnModified = true;
                    $this->strTagName = Type::Cast($mixValue, Type::STRING);
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