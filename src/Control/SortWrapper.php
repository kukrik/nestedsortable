<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class SlideWrapper
 *
 * @property boolean $ActivatedLink Default false. If you want to show the link, set the link to true.
 *
 * @package QCubed\Plugin
 */

class SortWrapper extends Q\Project\Jqui\Sortable
{
    use Q\Control\DataBinderTrait;
    /** @var boolean*/
    protected $blnActivatedLink = false;

    protected $objDataSource;
    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var  callable */
    protected $cellParamsCallback = null;
    /** @var  callable */
    protected $buttonParamsCallback = null;
    /** @var  callable */
    protected $inputParamsCallback = null;

    /**
     * @param callable $callback
     * @return void
     */
    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    /**
     * @param callable $callback
     * @return void
     */
    public function createControlButtons(callable $callback)
    {
        $this->buttonParamsCallback = $callback;
    }

    /**
     * @param callable $callback
     * @return void
     */
    public function createRenderInputs(callable $callback)
    {
        $this->inputParamsCallback = $callback;
    }

    /**
     * @param callable $callback
     * @return void
     */
    public function createRenderButtons(callable $callback)
    {
        $this->cellParamsCallback = $callback;
    }

    /**
     * Uses HTML callback to get each loop in the original array. Relies on the NodeParamsCallback
     * to return information on how to draw each node.
     *
     * @param mixed $objItem
     * @return string
     * @throws \Exception
     */
    public function getItem($objItem)
    {
        if (!$this->nodeParamsCallback) {
            throw new \Exception("Must provide an nodeParamsCallback");
        }
        $params = call_user_func($this->nodeParamsCallback, $objItem);

        $intId = '';
        if (isset($params['id'])) {
            $intId = $params['id'];
        }
        $intOrder = '';
        if (isset($params['order'])) {
            $intOrder = $params['order'];
        }
        $strCategory = '';
        if (isset($params['category'])) {
            $strCategory = $params['category'];
        }
        $strName = '';
        if (isset($params['name'])) {
            $strName = $params['name'];
        }
        $strUrl = '';
        if (isset($params['url'])) {
            $strUrl = $params['url'];
        }
        $intStatus = '';
        if (isset($params['status'])) {
            $intStatus = $params['status'];
        }

        $vars = [
            'id' => $intId,
            'order' => $intOrder,
            'category' => $strCategory,
            'name' => $strName,
            'url' => $strUrl,
            'status' => $intStatus,
        ];

        return $vars;
    }

    /**
     * @param $objItem
     * @return mixed
     * @throws \Exception
     */
    public function getObject($objItem)
    {
        if (!$this->cellParamsCallback) {
            throw new \Exception("Must provide an cellParamsCallback");
        }
        $mixButtons = call_user_func($this->cellParamsCallback, $objItem);

        return $mixButtons;
    }

    /**
     * @param $objItem
     * @return mixed
     * @throws \Exception
     */
    public function getButtons($objItem)
    {
        if (!$this->buttonParamsCallback) {
            throw new \Exception("Must provide an buttonParamsCallback");
        }
        $mixButtons = call_user_func($this->buttonParamsCallback, $objItem);

        return $mixButtons;
    }

    /**
     * @param $objItem
     * @return mixed
     * @throws \Exception
     */
    public function getInput($objItem)
    {
        if (!$this->inputParamsCallback) {
            throw new \Exception("Must provide an inputParamsCallback");
        }
        $mixInputs = call_user_func($this->inputParamsCallback, $objItem);

        return $mixInputs;
    }

    /**
     * Fix up possible embedded reference to the form.
     */
    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
        $this->buttonParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->buttonParamsCallback);
        $this->cellParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->cellParamsCallback);
        $this->inputParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->inputParamsCallback);
        parent::sleep();
    }

    /**
     * The object has been unserialized, so fix up pointers to embedded objects.
     * @param \QCubed\Control\FormBase $objForm
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
        $this->buttonParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->buttonParamsCallback);
        $this->cellParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->cellParamsCallback);
        $this->inputParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->inputParamsCallback);
    }

    /**
     * Returns the HTML for the control.
     *
     * @return string
     */
    protected function getControlHtml()
    {
        $this->dataBind();
        $strParams = [];
        $strButtons = [];
        $strObjects = [];
        $strInputs = [];
        $strHtml = "";

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                $strParams[] = $this->getItem($objObject);
                if ($this->cellParamsCallback) {
                    $strObjects[] = $this->getObject($objObject);
                }
                if ($this->buttonParamsCallback) {
                    $strButtons[] = $this->getButtons($objObject);
                }
                if ($this->inputParamsCallback) {
                    $strInputs[] = $this->getInput($objObject);
                }
            }
        }

        $strHtml .= $this->renderTag('div', null, null, $this->renderTree($strParams, $strObjects, $strButtons, $strInputs));

        $this->objDataSource = null;
        return $strHtml;
    }

    /**
     * @throws Caller
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

    public function renderTree($arrParams, $arrObjects, $arrButtons, $arrInputs)
    {
        $strHtml = '';

        for ($i = 0; $i < count($arrParams); $i++) {
            $intId = $arrParams[$i]['id'];
            $intStatus = $arrParams[$i]['status'];
            $strCategory = $arrParams[$i]['category'];
            $strName = $arrParams[$i]['name'];
            $strUrl = $arrParams[$i]['url'];

            if ($this->cellParamsCallback) {
                $strRenderCellHtml = $arrObjects[$i];
            }

            if ($this->buttonParamsCallback) {
                $strRenderButtonHtml = $arrButtons[$i];
            }

            if ($this->inputParamsCallback) {
                $strRenderInputHtml = $arrInputs[$i];
            }

            if ($intStatus !== 2) {
                $strHtml .= _nl('<div id ="' . $this->strControlId . '_' . $intId . '" data-value="' . $intId . '" class="div-block">');
            } else {
                $strHtml .= _nl('<div id ="' . $this->strControlId . '_' . $intId . '" data-value="' . $intId . '" class="div-block inactivated">');
            }

            $strHtml .= _nl(_indent('<div class="events">', 1));
            $strHtml .= _nl(_indent('<span class="icon-set reorder"><i class="fa fa-bars"></i></span>', 2));

            if ($this->buttonParamsCallback) {
                $strHtml .= _nl(_indent($strRenderButtonHtml, 2));
            }

            $strHtml .= _nl(_indent('</div>', 1));

            $strHtml .= _nl(_indent('<div class="div-info">', 1));

            if ($strCategory) {
                $strHtml .= _nl(_indent('<span class="category">' . $strCategory . ' | </span>', 2));
            }

            if (!$this->blnActivatedLink) {
                $strHtml .= _nl(_indent('<span>' . t($strName) . '</span>', 2));
            } else {
                if ($strUrl) {
                    $strHtml .= _nl(_indent('<a class="view-link" href="' . $strUrl . '"  target="_blank" >' . $strName . '</a>', 2));
                } else {
                    $strHtml .= _nl(_indent('<span>' . t($strName) . '</span>', 2));
                }
            }

            $strHtml .= _nl(_indent('</div>', 1));

            if ($this->inputParamsCallback) {
                $strHtml .= _nl(_indent('<div class="status-info">', 1));
                $strHtml .= _nl(_indent($strRenderInputHtml, 2));
                $strHtml .= _nl(_indent('</div>', 1));
            }

            if ($this->cellParamsCallback) {
                $strHtml .= _nl(_indent('<div class="div-buttons">', 1));
                $strHtml .= _nl(_indent($strRenderCellHtml, 2));
                $strHtml .= _nl(_indent('</div>', 1));
            }
            $strHtml .= _nl('</div>');
        }

        return $strHtml;
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
     * @param string $strName
     * @return bool|mixed|null|string
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "ActivatedLink": return $this->blnActivatedLink;
            case "DataSource": return $this->objDataSource;

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
            case "ActivatedLink":
                try {
                    $this->blnActivatedLink = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "DataSource":
                $this->blnModified = true;
                $this->objDataSource = $mixValue;
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