<?php

namespace QCubed\Plugin\Control;

require ('SliderBaseGen.php');

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class SliderBase
 *
 * @property string $TempUrl Default temp path APP_UPLOADS_TEMP_URL. If necessary, the temp dir must be specified.
 * @property string $RootUrl Default root path APP_UPLOADS_URL. If necessary, the temp dir must be specified.
 * @property integer $SliderStatus Default '2'. If the user decides to publish the slider on the frontend,
 *                                 it will be made visible, i.e. the number will be changed to 1.
 * @property string $ListTag Default: 'div'. Depending on the design of the theme, either use 'div' or 'ul'.
 * @property string $ItemTag Default: 'div'. Depending on the design of the theme, either use 'div' or 'li'.
 *                          If true, it is drawn like this:
 *                          'div' or 'li'
 *                          'a target="_blank" href="..."' 'img alt="..." title="..." src="image.jpg" /' '/a'
 *                          '/div' or '/li'
 *                          If false, it is drawn like this:
 *                          'div' o r'li'
 *                          'img alt="..." title="..." src="image.jpg" /'
 *                          '/div' or '/li'
 *
 * @package QCubed\Plugin
 */

class Slider extends SliderBaseGen
{
    use Q\Control\DataBinderTrait;

    /** @var string */
    protected $strRootUrl = APP_UPLOADS_URL;
    /** @var string  */
    protected $strTempUrl = APP_UPLOADS_TEMP_URL;
    /** @var integer */
    protected $intSliderStatus = 2;
    /** @var string  */
    protected $strListTag = 'div';
    /** @var string  */
    protected $strItemTag = 'div>';
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;
    /** @var  callable */
    protected $nodeParamsCallback = null;

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

    protected function registerFiles() {
        $this->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/jquery.bxslider.js");
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/jquery.bxslider.css");
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
    }

    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
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
        $intHeight = '';
        if (isset($params['height'])) {
            $intHeight = $params['height'];
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
            'height' => $intHeight,
            'top' => $intTop,
            'status' => $intStatus,
            'post_date' => $calPostDate,
            'post_update_date' => $calPostUpdateDate
        ];

        return $vars;
    }

    /**
     * Fix up possible embedded reference to the form.
     */
    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
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
        $strHtml = "";

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                $strParams[] = $this->getItem($objObject);
            }
        }

        if ($this->intSliderStatus !== 2) {
            $strHtml .= $this->renderTag('div', null, null, $this->renderSlide($strParams));
        } else {
            $strHtml .= '';
        }

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

    protected function renderSlide($arrParams)
    {
        $strHtml = '';

        foreach ($arrParams as $params) {
            if ($params['status'] != 1) {
                continue;
            }

            $strHtml .= '<' . $this->ItemTag;

            $strUrl = $params['url'] ?? '';
            $strTitle = $params['title'] ?? '';
            $strPath = $params['path'] ?? '';
            $strExtension = $params['extension'] ?? '';
            $intHeight = $params['height'] ?? '';
            $intTop = $params['top'] ?? '';

            if ($strExtension === 'svg') {
                $strHtml .= '<div class="svg-container"';
                $strHtml .= (!empty($intHeight) || !empty($intTop)) ? ' style="' : '';

                if (!empty($intHeight)) {
                    $strHtml .= 'height:' . $intHeight . 'px;';
                }

                if (!empty($intTop)) {
                    $strHtml .= 'margin-top:' . $intTop . 'px;';
                }

                $strHtml .= (!empty($intHeight) || !empty($intTop)) ? '"' : '';

                $strHtml .= '>';

                if (!empty($strUrl)) {
                    $strHtml .= '<a href="' . $strUrl . '" target="_blank">';
                }

                $strHtml .= '<img src="' . $this->RootUrl . $strPath . '" alt="' . $strTitle . '" title="' . $strTitle . '" />';

                if (!empty($strUrl)) {
                    $strHtml .= '</a>';
                }

                $strHtml .= '</div>';
            } else {
                if (!empty($strUrl)) {
                    $strHtml .= '<a href="' . $strUrl . '" target="_blank">';
                }

                $strHtml .= '<img src="' . $this->TempUrl . $strPath . '" alt="' . $strTitle . '" title="' . $strTitle . '"';

                $strHtml .= (!empty($intHeight) || !empty($intTop)) ? ' style="' : '';

                if (!empty($intHeight)) {
                    $strHtml .= 'height:' . $intHeight . 'px;';
                }

                if (!empty($intTop)) {
                    $strHtml .= 'margin-top:' . $intTop . 'px;';
                }

                $strHtml .= (!empty($intHeight) || !empty($intTop)) ? '" />' : ' />';

                if (!empty($strUrl)) {
                    $strHtml .= '</a>';
                }
            }

            $strHtml .= _nl('</' . $this->ItemTag);
        }

        return $strHtml;
    }

    /**
     * @param string $strName
     * @return bool|mixed|null|string
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'TempUrl': return $this->strTempUrl;
            case 'RootUrl': return $this->strRootUrl;
            case 'SliderStatus': return $this->intSliderStatus;
            case 'ListTag': return $this->strListTag;
            case 'ItemTag': return $this->strItemTag;
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
            case "TempUrl":
                try {
                    $this->strTempUrl = Type::Cast($mixValue, Type::STRING);
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
            case "SliderStatus":
                try {
                    $this->intSliderStatus = Type::Cast($mixValue, Type::INTEGER);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "ListTag":
                try {
                    $this->strListTag = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "ItemTag":
                try {
                    $this->strItemTag = Type::Cast($mixValue, Type::STRING);
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