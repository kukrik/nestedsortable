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
 * Class SliderSetupAdmin
 *
 * @property string $TempUrl Default temp path APP_UPLOADS_TEMP_URL. If necessary, the temp url must be specified.
 * @property string $RootUrl Default temp path APP_UPLOADS_URL. If necessary, the root url must be specified.
 * @property string $ListTag Default: 'div'. Depending on the design of the theme, either use 'div' or 'ul'.
 * @property string $ItemTag Default: 'div'. Depending on the design of the theme, either use 'div' or 'li'.
 * @property boolean $IsLink Default: false.
 *                          If true, it is drawn like this:
 *                          'div' or 'li'
 *                          'a target="_blank" href="..."' 'img alt="..." title="..." src="image.jpg" /' '/a'
 *                          '/div' or '/li'
 *                          If false, it is drawn like this:
 *                          'div' o r'li'
 *                          'img alt="..." title="..." src="image.jpg" /'
 *                          '/div' or '/li'
 *
 * ### GENERAL ###
 *
 * @property string $Mode Default: 'horizontal'. Mode Type of transition between slides
 *                  Options: 'horizontal', 'vertical', 'fade'
 * @property integer $Speed Default: 500. Slide transition duration (in ms)
 * @property integer $SlideMargin Default: 0. Margin between each slide
 * @property integer $StartSlide Default: 0. Starting slide index (zero-based)
 * @property boolean $RandomStart Default: false. Start slider on a random slide
 * @property string $SlideSelector Default: ''. Element to use as slides (ex. 'div.slide').
 *                  Note: by default, bxSlider will use all immediate children of the slider element.
 *                  Options: jQuery selector
 * @property boolean $InfiniteLoop Default: true. If true, clicking "Next" while on the last slide will transition
 *                  to the first slide and vice-versa
 * @property boolean $HideControlOnEnd Default: false. If true, "Prev" and "Next" controls will receive a class
 *                  disabled when slide is the first or the last.
 *                  Note: Only used when infiniteLoop: false
 * @property string $Easing Default: null. The type of "easing" to use during transitions. If using CSS transitions,
 *                  include a value for the transition-timing-function property. If not using CSS transitions,
 *                  you may include plugins/jquery.easing.1.3.js for many options.
 *                  See http://gsgd.co.uk/sandbox/jquery/easing/</a> for more info.
 *                  Options: if using CSS: 'linear', 'ease', 'ease-in', 'ease-out', 'ease-in-out', 'cubic-bezier(n,n,n,n)'.
 *                  If not using CSS: 'swing', 'linear' (see the above file for more options)
 * @property boolean $Captions Default: false. Include image captions. Captions are derived from the image's title attribute
 * @property boolean $Ticker Default: false. Use slider in ticker mode (similar to a news ticker).
 * @property boolean $TickerHover Default: false. Ticker will pause when mouse hovers over slider.
 *                  Note: this functionality does NOT work if using CSS transitions!
 * @property boolean $AdaptiveHeight Default: false. Dynamically adjust slider height based on each slide's height.
 * @property integer $AdaptiveHeightSpeed Default: 500. Slide height transition duration (in ms).
 *                  Note: only used if adaptiveHeight: true.
 * @property boolean $Video Default: false. If any slides contain video, set this to true.
 *                  Also, include plugins/jquery.fitvids.js
 *                  See http://fitvidsjs.com/</a> for more info.
 * @property boolean $Responsive Default: true. Enable or disable auto resize of the slider.
 *                  Useful if you need to use fixed width sliders.
 * @property boolean $UseCSS If true, Default: true.  CSS transitions will be used for horizontal and vertical slide
 *                  animations (this uses native hardware acceleration). If false, jQuery animate() will be used.
 * @property string $PreloadImages Default: 'visible'. If 'all', preloads all images before starting the slider.
 *                  If 'visible', preloads only images in the initially visible slides before starting the slider
 *                  (tip: use 'visible' if all slides are identical dimensions).
 *                  Options: 'all', 'visible'.
 * @property boolean $TouchEnabled Default: true. If true, slider will allow touch swipe transitions.
 * @property integer $SwipeThreshold Default: 50. Amount of pixels a touch swipe needs to exceed in order to execute
 *                  a slide transition. Note: only used if touchEnabled: true.
 * @property boolean $OneToOneTouch Default: true. If true, non-fade slides follow the finger as it swipes.
 * @property boolean $PreventDefaultSwipeX Default: true. If true, touch screen will not move along the x-axis as the finger swipes.
 * @property string $WrapperClass Default: 'bx-wrapper'. Class to wrap the slider in. Change to prevent
 *                  from using default bxSlider styles.
 *
 * ### PAGER ###
 *
 * @property boolean $Pager Default: true. If true, a pager will be added.
 * @property string $PagerType Default: 'full'. If 'full', a pager link will be generated for each slide.
 *                  If 'short', a x / y pager will be used (ex. 1 / 5).
 *                  Options: 'full', 'short'.
 * @property string $PagerShortSeparator Default: ' / '. If pagerType: 'short', pager will use this value as the separating character
 * @property string $PagerSelector Default: ''. Element used to populate the populate the pager.
 *                  By default, the pager is appended to the bx-viewport.
 *                  Options: jQuery selector
 * @property string $PagerCustom Default: null.  Parent element to be used as the pager. Parent element must contain
 *                  a <a data-slide-index="x"> element for each slide. See example here. Not for use with dynamic carousels.
 *                  Options: jQuery selector
 * @property object $BuildPager Default: null.  If supplied, function is called on every slide element, and the returned
 *                  value is used as the pager item markup. See examples (https://bxslider.com/examples) for detailed implementation
 *                  Options: function(slideIndex).
 *
 * ### CONTROLS ###
 *
 * @property boolean $Controls Default: true. If true, "Next" / "Prev" controls will be added.
 * @property string $NextText Default: 'Next'. Text to be used for the "Next" control.
 * @property string $PrevText Default: 'Prev'. Text to be used for the "Prev" control.
 * @property string $NextSelector Default: null. Element used to populate the "Next" control.
 *                  Options: jQuery selector
 * @property string $PrevSelector Default: null. Element used to populate the "Prev" control.
 *                  Options: jQuery selector
 * @property boolean $AutoControls Default: false. If true, "Start" / "Stop" controls will be added.
 * @property string $StartText Default: 'Start'. Text to be used for the "Start" control.
 * @property string $StopText Default: 'Stop'. Text to be used for the "Stop" control.
 * @property boolean $AutoControlsCombine Default: false. When slideshow is playing only "Stop" control is displayed and vice-versa
 * @property string $AutoControlsSelector Default: null. Element used to populate the auto controls
 *                  Options: jQuery selector
 * @property boolean $KeyboardEnabled Default: false. Enable keyboard navigation for visible sliders.
 *
 * ### AUTO ###
 *
 * @property boolean $Auto Default: false. Slides will automatically transition.
 * @property boolean $StopAutoOnClick Default: false. Auto will stop on interaction with controls.
 * @property integer $Pause Default: 4000. The amount of time (in ms) between each auto transition.
 * @property boolean $AutoStart Default: true. Auto show starts playing on load. If false,
 *                  slideshow will start when the "Start" control is clicked.
 * @property string $AutoDirection Default: 'next'. The direction of auto show slide transitions.
 *                  Options: 'next', 'prev'
 * @property boolean $AutoHover Default: false. Auto show will pause when mouse hovers over slider.
 * @property integer $AutoDelay Default: 0. Time (in ms) auto show should wait before starting.
 *
 * ### CAROUSEL ###
 *
 * @property integer $MinSlides Default: 1. The minimum number of slides to be shown. Slides will be sized down
 *                  if carousel becomes smaller than the original size.
 * @property integer $MaxSlides Default: 1. The maximum number of slides to be shown. Slides will be sized up
 *                  if carousel becomes larger than the original size.
 * @property integer $MoveSlides Default: 0. The number of slides to move on transition.
 *                  This value must be >= minSlides, and <= maxSlides. If zero (default), the number of fully-visible
 *                  slides will be used.
 * @property integer $SlideWidth Default: 0. The width of each slide. This setting is required for all horizontal carousels!
 * @property boolean $ShrinkItems Default: false. The Carousel will only show whole items and shrink the images
 *                  to fit the viewport based on maxSlides/MinSlides.
 *
 * ### ACCESSIBILITY ###
 *
 * @property boolean $AriaHidden Default: true. Adds Aria Hidden attribute to any nonvisible slides.
 *
 * ###  CALLBACKS ###
 *
 * @see https://bxslider.com/
 *
 * Suggestion to use other QCubed-4 options like Application::executeJavaScript() etc...
 *
 * @property integer $WidthInput Default 0. ....
 * @property integer $HeightInput Default 0. ....
 *
 * @package QCubed\Plugin
 */

class SliderSetupAdmin extends SliderBaseGen
{
    use Q\Control\DataBinderTrait;

    /** @var string  */
    protected $strTempUrl = APP_UPLOADS_TEMP_URL;
    /** @var string  */
    protected $strRootUrl = APP_UPLOADS_URL;
    /** @var string  */
    protected $strListTag = 'div';
    /** @var string  */
    protected $strItemTag = 'div>';
   /** @var boolean  */
    protected $blnIsLink = false;
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;
    /** @var  callable */
    protected $nodeParamsCallback = null;

    protected $intWidthInput = null;
    protected $intHeightInput = null;

    public function  __construct($objParentObject, $strControlId = null) {
        parent::__construct($objParentObject, $strControlId);
        $this->registerFiles();
    }

    /**
     * @throws Caller
     */
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

        $strHtml .= $this->renderTag($this->ListTag, null, null, $this->renderImage($strParams));

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

    protected function renderImage($arrParams)
    {
        $strHtml = '';

        foreach ($arrParams as $params) {
//            if ($params['status'] != 1) {
//                continue;
//            }

            $strHtml .= '<' . $this->ItemTag;

            $strUrl = $params['url'] ?? '';
            $strTitle = $params['title'] ?? '';
            $strPath = $params['path'] ?? '';
            $strExtension = $params['extension'] ?? '';
            $intWidth = $params['width'] ?? '';
            $intHeight = $params['height'] ?? '';
            $intTop = $params['top'] ?? '';

            if ($strExtension === 'svg') {
                $strHtml .= '<div class="svg-container"';
                $strHtml .= (!empty($intWidth) || !empty($intHeight) || !empty($intTop)) ? ' style="' : '';

                if (!empty($intWidth)) {
                    $strHtml .= 'width:' . $intWidth . 'px;';
                }

                if (!empty($intHeight)) {
                    $strHtml .= 'height:' . $intHeight . 'px;';
                }

                if (!empty($intTop)) {
                    $strHtml .= 'margin-top:' . $intTop . 'px;';
                }

                $strHtml .= (!empty($intWidth) || !empty($intHeight) || !empty($intTop)) ? '"' : '';

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

                $strHtml .= (!empty($intWidth) || !empty($intHeight) || !empty($intTop)) ? ' style="' : '';

                if (!empty($intWidth)) {
                    $strHtml .= 'width:' . $intWidth . 'px;';
                }

                if (!empty($intHeight)) {
                    $strHtml .= 'height:' . $intHeight . 'px;';
                }

                if (!empty($intTop)) {
                    $strHtml .= 'margin-top:' . $intTop . 'px;';
                }

                $strHtml .= (!empty($intWidth) || !empty($intHeight) || !empty($intTop)) ? '" />' : ' />';

                if (!empty($strUrl)) {
                    $strHtml .= '</a>';
                }
            }

            $strHtml .= _nl('</' . $this->ItemTag);
        }

        return $strHtml;
    }

    public function getEndScript()
    {
        $strJS = parent::getEndScript();

        $strCtrlJs = <<<FUNC
            $('.js-update').on('click', function () {
                var widthInput = document.querySelector("#width");
                var heightInput = document.querySelector("#height");
                
                qcubed.recordControlModification("$this->ControlId", "_widthInput", widthInput.value);
                qcubed.recordControlModification("$this->ControlId", "_heightInput", heightInput.value);
            });
FUNC;
        Application::executeJavaScript($strCtrlJs, Application::PRIORITY_HIGH);

        return $strJS;
    }

    /**
     * @param string $strName
     * @return bool|mixed|null|string
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'WidthInput': return $this->intWidthInput;
            case 'HeightInput': return $this->intHeightInput;
            case 'TempUrl': return $this->strTempUrl;
            case 'RootUrl': return $this->strRootUrl;
            case 'ListTag': return $this->strListTag;
            case 'ItemTag': return $this->strItemTag;
            case 'IsLink': return $this->blnIsLink;
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
            case '_widthInput': // Internal only to output the desired width of the image when clicked
                try {
                    $this->intWidthInput = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case '_heightInput': // Internal only to output the desired height of the image when clicked
                try {
                    $this->intHeightInput = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
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
            case "RootUrl":
                try {
                    $this->strRootUrl = Type::Cast($mixValue, Type::STRING);
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
            case "IsLink":
                try {
                    $this->blnIsLink = Type::Cast($mixValue, Type::BOOLEAN);
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