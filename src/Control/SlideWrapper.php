<?php

namespace QCubed\Plugin\Control;

use pq\DateTime;
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
 * @property string $TempUrl Default temp path APP_UPLOADS_TEMP_URL. If necessary, the temp dir must be specified.
 * @property string $RootUrl Default root path APP_UPLOADS_URL If necessary, the temp dir must be specified.
 * @property DateTime $DateTimeFormat Default 'DD.MM.YYYY hhhh:mm'. If desired, the date format can be changed to suit the user.
 * @property string $EmptyImagePath Feault null. If necessary, depending on your project, add an empty image path if the desired image is missing.
 *
 * @package QCubed\Plugin
 */

class SlideWrapper extends Q\Project\Jqui\Sortable
{
    use Q\Control\DataBinderTrait;

    /** @var string */
    protected $strRootPath = APP_UPLOADS_DIR;
    /** @var string */
    protected $strRootUrl = APP_UPLOADS_URL;
    /** @var string */
    protected $strTempPath = APP_UPLOADS_TEMP_DIR;
    /** @var string */
    protected $strTempUrl = APP_UPLOADS_TEMP_URL;
    /** @var DateTime */
    protected $strDateTimeFormat = 'DD.MM.YYYY hhhh:mm';
    /** @var string */
    protected $strEmptyImagePath;
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;
    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var  callable */
    protected $cellParamsCallback = null;

    /**
     * @param callable $callback
     */
    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    /**
     * @param callable $callback
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
        $intGroupId = '';
        if (isset($params['group_id'])) {
            $intGroupId = $params['group_id'];
        }
        $intOrder = '';
        if (isset($params['order'])) {
            $intOrder = $params['order'];
        }
        $strTitle = '';
        if (isset($params['title'])) {
            $strTitle = $params['title'];
        }
        $strUrl = '';
        if (isset($params['url'])) {
            $strUrl = $params['url'];
        }
        $strPath = '';
        if (isset($params['path'])) {
            $strPath = $params['path'];
        }
        $strExtension = '';
        if (isset($params['extension'])) {
            $strExtension = $params['extension'];
        }
        $strDimensions = '';
        if (isset($params['dimensions'])) {
            $strDimensions = $params['dimensions'];
        }
        $intWidth = '';
        if (isset($params['width'])) {
            $intWidth = $params['width'];
        }
        $intTop = '';
        if (isset($params['top'])) {
            $intTop = $params['top'];
        }
        $intStatus = '';
        if (isset($params['status'])) {
            $intStatus = $params['status'];
        }
        $calPostDate = '';
        if (isset($params['post_date'])) {
            $calPostDate = $params['post_date'];
        }
        $calPostUpdateDate = '';
        if (isset($params['post_update_date'])) {
            $calPostUpdateDate = $params['post_update_date'];
        }

        $vars = [
            'id' => $intId,
            'group_id' => $intGroupId,
            'order' => $intOrder,
            'title' => $strTitle,
            'url' => $strUrl,
            'path' => $strPath,
            'extension' => $strExtension,
            'dimensions' => $strDimensions,
            'width' => $intWidth,
            'top' => $intTop,
            'status' => $intStatus,
            'post_date' => $calPostDate,
            'post_update_date' => $calPostUpdateDate
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
     * Fix up possible embedded reference to the form.
     */
    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
        $this->cellParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->cellParamsCallback);
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
        $this->cellParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->cellParamsCallback);
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
        $strObjects = [];
        $strHtml = "";

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                $strParams[] = $this->getItem($objObject);
                if ($this->cellParamsCallback) {
                    $strObjects[] = $this->getObject($objObject);
                }
            }
        }

        $strHtml .= $this->renderTag('div', null, null, $this->renderSlide($strParams, $strObjects));

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

    protected function renderSlide($arrParams, $arrObjects)
    {
        $strHtml = '';

        for ($i = 0; $i < count($arrParams); $i++) {
            $intId = $arrParams[$i]['id'];
            $strPath = $arrParams[$i]['path'];
            $strExtension = $arrParams[$i]['extension'];
            $intStatus = $arrParams[$i]['status'];
            $strTitle = $arrParams[$i]['title'];
            $calPostDate = $arrParams[$i]['post_date'];
            $calPostUpdateDate = $arrParams[$i]['post_update_date'];

            if ($this->cellParamsCallback) {
                $strRenderCellHtml = $arrObjects[$i];
            }

            if ($intStatus !== 2) {
                $strHtml .= _nl('<div id ="' . $this->strControlId . '_' . $intId . '" data-value="' . $intId . '" class="image-blocks">');
            } else {
                $strHtml .= _nl('<div id ="' . $this->strControlId . '_' . $intId . '" data-value="' . $intId . '" class="image-blocks inactivated">');
            }

            $strHtml .= _nl(_indent('<div class="preview">', 1));

            if (!$strPath) {
                $strHtml .= _nl(_indent('<img src="' . $this->EmptyImagePath . '">', 2));
            } else if ($strExtension !== "svg") {
                $strHtml .= _nl(_indent('<img src="' . $this->TempUrl . $strPath . '">', 2));
            } else {
                $strHtml .= _nl(_indent('<img src="' . $this->RootUrl . $strPath . '">', 2));
            }

            $strHtml .= _nl(_indent('</div>', 1));
            $strHtml .= _nl(_indent('<div class="events" style="display: inline-block; width: 15%;">', 1));
            $strHtml .= _nl(_indent('<span class="icon-set reorder"><i class="fa fa-bars"></i></span>', 2));

            if ($this->cellParamsCallback) {
                $strHtml .= _nl(_indent($strRenderCellHtml, 2));
            }

            $strHtml .= _nl(_indent('</div>', 1));

            $strHtml .= _nl(_indent('<div class="image-info" style="display: inline-block; width: 70%;">', 1));
            $strHtml .= _nl(_indent('<span style="display: inline-block; width: 50%;">' . $strTitle . '</span>', 2));
            $strHtml .= _nl(_indent('<span style="display: inline-block; width: 24%;">' . $calPostDate->qFormat($this->strDateTimeFormat) . '</span>', 2));

            if (!empty($calPostUpdateDate)) {
                $strHtml .= _nl(_indent('<span style="display: inline-block; width: 24%;">' . $calPostUpdateDate->qFormat($this->strDateTimeFormat) . '</span>', 2));
            }

            $strHtml .= _nl(_indent('</div>', 1));

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
            case 'RootPath': return $this->strRootPath;
            case 'RootUrl': return $this->strRootUrl;
            case 'TempPath': return $this->strTempPath;
            case 'TempUrl': return $this->strTempUrl;
            case "EmptyImagePath": return $this->strEmptyImagePath;
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
            case "RootPath":
                try {
                    $this->strRootPath = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "RootUrl":
                try {
                    $this->strRootUrl = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "TempPath":
                try {
                    $this->strTempPath = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "TempUrl":
                try {
                    $this->strTempUrl = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "DateTimeFormat":
                try {
                    $this->strDateTimeFormat = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                case "EmptyImagePath":
                    try {
                        $this->strEmptyImagePath = Type::Cast($mixValue, Type::STRING);
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