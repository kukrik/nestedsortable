<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Control\FormBase;
use QCubed\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Type;
use QCubed\Js;
use QCubed\Html;

/**
 * Class NestedSortableBase
 *
 * If want to will be overwritten when you update QCubed. To override, make your changes
 * to the NestedSortable.class.php file instead.
 *
 * NestedSortable is a group of panels that can be dragged to reorder them. You will need to put
 * some care into the css styling of the objects so that the css allows them to be moved. It
 * will use the top level html objects inside the panel to decide what to sort. Make sure
 * they have ids so it can return the ids of the items in sort order.
 *
 * @property integer $Id
 * @property integer $ParentId
 * @property integer $Depth
 * @property integer $Left
 * @property integer $Right
 * @property string $MenuText
 * @property string $RedirectUrl
 * @property integer $IsRedirect
 * @property integer $ExternalUrl
 * @property integer $SelectedPageId
 * @property string $SelectedPage
 * @property integer $SelectedPageLocked
 * @property string $ContentTypeObject
 * @property integer $ContentType
 * @property string $GroupTitle
 * @property integer $Status
 * @property string $WrapperClass
 * @property string $SectionClass
 * @property array $DataSource
 *
 * @property-read array $ItemArray List of ControlIds in sort orders.
 *
 * @link https://github.com/ilikenwf/nestedSortable
 * @package QCubed\Plugin
 */
class NestedSortableBase extends NestedSortableGen
{
    use Q\Control\DataBinderTrait;

    protected $strItem = null;
    protected $aryItemArray = null;

    /** @var string WrapperClass */
    protected $strWrapperClass = null;
    /** @var string SectionClass */
    protected $strSectionClass = null;
    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var  callable */
    protected $cellParamsCallback = null;
    /** @var */
    protected $mixButtons;
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;
    protected $strParams;
    protected $strObjects;

    protected $intCurrentDepth = 0;
    protected $intCounter = 0;
    /** @var null */
    protected $strRenderCellHtml = null;

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
    /** @var  string RedirectUrl */
    protected $strRedirectUrl;
    /** @var  int IsRedirect */
    protected $intIsRedirect;
    /** @var  string ExternalUrl */
    protected $strExternalUrl;
    /** @var  int SelectedPage */
    protected $intSelectedPageId;
    /** @var  string SelectedPage */
    protected $strSelectedPage;
    /** @var  integer SelectedPageLocked */
    protected $intSelectedPageLocked;
    /** @var  string ContentTypeObject */
    protected $strContentTypeObject;
    /** @var  int ContentType */
    protected $intContentType;
    /** @var  int Status */
    protected $intStatus;

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
     * Registers the necessary JS and CSS files for nested sortable functionality,
     * Bootstrap, and Font Awesome.
     *
     * @return void
     */
    protected function registerFiles()
    {
        $this->addJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/jquery.mjs.nestedSortable.js");
        $this->addJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/select2.js");
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/style.css");
        Bs\Bootstrap::loadJS($this);
    }

    /**
     * Sets the callback function to create node parameters.
     *
     * @param callable $callback The callback function that generates node parameters.
     * @return void
     */
    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    /**
     * Assigns a callback function intended to handle the rendering of buttons.
     *
     * @param callable $callback The callback function that will be used to render buttons.
     * @return void
     */
    public function createRenderButtons(callable $callback)
    {
        $this->cellParamsCallback = $callback;
    }

    /**
     * Retrieves the raw item parameters from the given item using a callback function.
     * Throws an exception if the nodeParamsCallback is not set.
     *
     * @param mixed $objItem The item from which parameters are to be extracted.
     * @return array The extracted parameters including id, parent_id, depth, left, right, menu_text,
     *               content_type_object, content_type, group_title_id, redirect_url, is_redirect,
     *               selected_page_id, selected_page, selected_page_locked, and status.
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
        $strContentTypeObject = '';
        if (isset($params['content_type_object'])) {
            $strContentTypeObject = $params['content_type_object'];
        }
        $intContentType = '';
        if (isset($params['content_type'])) {
            $intContentType = $params['content_type'];
        }
        $strRedirectUrl = '';
        if (isset($params['redirect_url'])) {
            $strRedirectUrl = $params['redirect_url'];
        }
        $intIsRedirect = '';
        if (isset($params['is_redirect'])) {
            $intIsRedirect = $params['is_redirect'];
        }
        $strExternalUrl = '';
        if (isset($params['external_url'])) {
            $strExternalUrl = $params['external_url'];
        }
        $intSelectedPageId = '';
        if (isset($params['selected_page_id'])) {
            $intSelectedPageId = $params['selected_page_id'];
        }
        $strSelectedPage = '';
        if (isset($params['selected_page'])) {
            $strSelectedPage = $params['selected_page'];
        }
        $intSelectedPageLocked = '';
        if (isset($params['selected_page_locked'])) {
            $intSelectedPageLocked = $params['selected_page_locked'];
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
            'content_type_object' => $strContentTypeObject,
            'content_type' => $intContentType,
            'redirect_url' => $strRedirectUrl,
            'is_redirect' => $intIsRedirect,
            'external_url' => $strExternalUrl,
            'selected_page_id' => $intSelectedPageId,
            'selected_page' => $strSelectedPage,
            'selected_page_locked' => $intSelectedPageLocked,
            'status' => $intStatus
        ];

        return $vars;
    }

    /**
     * Retrieves the drawing parameters for the given object.
     * The parameters are determined using the provided cellParamsCallback.
     *
     * @param mixed $objItem The object for which to retrieve the drawing parameters.
     *
     * @return mixed The drawing parameters for the object.
     * @throws \Exception If cellParamsCallback is not provided.
     *
     */
    public function getObjectDraw($objItem)
    {
        if (!$this->cellParamsCallback) {
            throw new \Exception("Must provide an cellParamsCallback");
        }
        $this->mixButtons = call_user_func($this->cellParamsCallback, $objItem);
        return $this->mixButtons;
    }

    /**
     * Prepares the object for serialization by transforming callback parameters using the sleepHelper method.
     * The parent sleep method is then called.
     *
     * @return void
     */
    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
        $this->cellParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->cellParamsCallback);
        parent::sleep();
    }

    /**
     * This method initializes the FormBase object passed to it.
     * It also sets the nodeParamsCallback and cellParamsCallback properties
     * using the parent class's wakeupHelper method.
     *
     * @param FormBase $objForm The form object to be initialized and processed.
     *
     * @return void
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
        $this->cellParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->cellParamsCallback);
    }

    /**
     * Generates the HTML for the control, including any bound data and configured attributes.
     *
     * @return string The generated HTML for the control.
     */
    protected function getControlHtml()
    {
        $this->dataBind();

        $this->strParams = [];
        $this->strObjects = [];

        $strHtml = '';
        $strHtml .= $this->welcomeMessage();

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                $this->strParams[] = $this->getItemRaw($objObject);
                if ($this->cellParamsCallback) {
                    $this->strObjects[] = $this->getObjectDraw($objObject);
                }
            }
        }

        $strHtml .= $this->renderMenuTree($this->strParams, $this->strObjects);

        $this->objDataSource = null;
        return $strHtml;
    }

    /**
     * Binds the data source to the UI component.
     * If the data source is not set and a data binder is available, it calls the data binder method.
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
     * Generates a welcome message if there is exactly one item in the data source.
     *
     * @return string A formatted HTML string containing a welcome message with instructions to create menu items,
     *                wrapped in a styled alert div.
     */
    public function welcomeMessage()
    {
        if (count($this->objDataSource) == 1) {
            $strEmptyMenuText = sprintf(t('<strong>Welcome! </strong> Create the following menu items!'));
            return "<div class='alert alert-info alert-dismissible' role='alert' style='display: block;'>
$strEmptyMenuText
</div>";
        }
    }

    /**
     * Renders an HTML menu tree based on provided parameters and objects.
     *
     * @param array $arrParams An array of parameters each of which contains information about a menu item such as 'id', 'parent_id', 'depth',
     *                          'left', 'right', 'menu_text', 'redirect_url', 'is_redirect', 'selected_page_id',
     *                          'selected_page', 'content_type_object', 'content_type', 'group_title_id', and 'status'.
     * @param array $arrObjects An array of additional objects that may be used for rendering each menu item.
     *
     * @return string A formatted HTML string representing the nested menu structure.
     */
    protected function renderMenuTree($arrParams, $arrObjects)
    {
        $strHtml = '';

        // Let's start with the menu wrapper
        $strHtml .= '<' . $this->TagName . ' class="' . $this->strWrapperClass . '" id="' . $this->ControlId . '">';

        // Let's start the walkthrough
        for ($i = 0; $i < count($arrParams); $i++) {
            $this->intId = $arrParams[$i]['id'];
            $this->intParentId = $arrParams[$i]['parent_id'];
            $this->intDepth = $arrParams[$i]['depth'];
            $this->intLeft = $arrParams[$i]['left'];
            $this->intRight = $arrParams[$i]['right'];
            $this->strMenuText = $arrParams[$i]['menu_text'];
            $this->strRedirectUrl = $arrParams[$i]['redirect_url'];
            $this->intIsRedirect = $arrParams[$i]['is_redirect'];
            $this->strExternalUrl = $arrParams[$i]['external_url'];
            $this->intSelectedPageId = $arrParams[$i]['selected_page_id'];
            $this->strSelectedPage = $arrParams[$i]['selected_page'];
            $this->strContentTypeObject = $arrParams[$i]['content_type_object'];
            $this->intContentType = $arrParams[$i]['content_type'];
            $this->intStatus = $arrParams[$i]['status'];

            // We implement the callback function when specified
            if ($this->cellParamsCallback) {
                $this->strRenderCellHtml = $this->getRenderCellHtml($arrObjects[$i]);
            }

            // Depth comparisons for hierarchy
            if ($this->intDepth == $this->intCurrentDepth) {
                if ($this->intCounter > 0) {
                    $strHtml .= '</li>';
                }
            } elseif ($this->intDepth > $this->intCurrentDepth) {
                $strHtml .= '<' . $this->TagName . '>';
                $this->intCurrentDepth += ($this->intDepth - $this->intCurrentDepth);
            } elseif ($this->intDepth < $this->intCurrentDepth) {
                $strHtml .= str_repeat('</li></' . $this->TagName . '>', $this->intCurrentDepth - $this->intDepth) . '</li>';
                $this->intCurrentDepth -= ($this->intCurrentDepth - $this->intDepth);
            }

            // Let's start creating <li>
            $strHtml .= '<li id="' . $this->ControlId . '_' . $this->intId . '"';
            if ($this->intLeft + 1 == $this->intRight) {
                $strHtml .= ' class="mjs-nestedSortable-leaf"';
            } else {
                $strHtml .= ' class="mjs-nestedSortable-expanded"';
            }
            $strHtml .= '>';

            // We define different state data
            $strCheckStatus = $this->intStatus === 1 ? 'enable' : 'disable';
            $strDisplayedType = $this->strContentTypeObject ? ' Type: ' . $this->strContentTypeObject : ' Type: NULL';
            $strRoutingInfo = $this->intContentType === 8 ? ' - <span style="color: #2980b9;">' . $this->strExternalUrl . '</span>' : '';
            $strDoubleRoutingInfo = $this->intContentType === 7 && $this->intIsRedirect === 2 ? $this->getRoutingInfo($this->intSelectedPageId, $this->strSelectedPage) : '';

            // We add the menu text and details section in the HTML line

            if ($this->intId == 1) { // If item ID = 1
                $strHtml .= '<div class="menu-row-highlight ' . $strCheckStatus . '"><span class="reorder"><i class="fa fa-bars"></i></span>
            <span class="disclose"><span></span></span><section class="menu-body">' . $this->strMenuText . '<span class="separator">&nbsp;</span>' .
                    $strDisplayedType . $strRoutingInfo . $strDoubleRoutingInfo;
            } else {
                $strHtml .= '<div class="menu-row ' . $strCheckStatus . '"><span class="reorder"><i class="fa fa-bars"></i></span>
            <span class="disclose"><span></span></span><section class="menu-body">' . $this->strMenuText . '<span class="separator">&nbsp;</span>' .
                    $strDisplayedType . $strRoutingInfo . $strDoubleRoutingInfo;
            }
            $strHtml .= '</section>';

            // We add the callback content if it is specified
            if ($this->cellParamsCallback) {
                $strHtml .= $this->strRenderCellHtml;
            }

            $strHtml .= '</div>';
            ++$this->intCounter;
        }

        // We close to the end of the depth
        if ($this->intCurrentDepth > 0) {
            $strHtml .= str_repeat('</li></' . $this->TagName . '>', $this->intCurrentDepth);
            $this->intCurrentDepth = 0; // We reset the depth
        }

        // Let's close the global ul-wrapper
        $strHtml .= '</' . $this->TagName . '>';

        return $strHtml;
    }

    /**
     * Renders the cell content as an HTML section if cell parameter callback is defined.
     *
     * @param mixed $value The value to be rendered inside the HTML section.
     * @return string|null The formatted HTML section containing the value, or null if the callback is not defined.
     */
    protected function getRenderCellHtml($value)
    {
        if ($this->cellParamsCallback) {
            $strHtml = '';
            $attributes = [];
            if ($this->strSectionClass) {
                $attributes['class'] = $this->strSectionClass;
            }
            $strHtml .= $value;
            $strHtml = Html::renderTag('section', $attributes, $strHtml);
            return $strHtml;
        } else {
            return null;
        }
    }

    /**
     * Retrieves routing information for a given key and selected page.
     *
     * @param int $key The key to be checked against double redirects.
     * @param string $selectedpage The selected page's name to be displayed in the routing information.
     * @return string A formatted string containing routing information, indicating whether or not
     *                there's a double redirection to the selected page, wrapped in styled HTML elements.
     */
    protected function getRoutingInfo($key, $selectedpage)
    {
        $arrDoubleRedirects = [];
        $count = [];

        foreach ($this->strParams as $strParam) {
            if ($strParam['is_redirect'] === 2 && $strParam['content_type'] === 7) {
                $arrDoubleRedirects[] = $strParam['selected_page_id'];
            }
        }
        foreach ($arrDoubleRedirects as $doubleRedirect) {
            if ($key == $doubleRedirect) {
                $count[] = $doubleRedirect;
            }
        }

        if (count($count) === 1) {
            $strHtml = ' - ' . t('Redirected to this page: ') . '<span style="color: #2980b9;">' . $selectedpage . '</span>';
        } else {
            $strHtml = ' - ' . t('Redirected to this page ') . ' | ' . '<span style="color: #ff0000;">' .
                t('Warning, double redirection: ') . '</span><span style="color: #2980b9;">' . $selectedpage . '</span>';
        }
        return $strHtml;
    }

    /**
     * Recursively retrieves all child menu item IDs for a given parent menu item ID.
     *
     * @param array $objMenuArray Array of menu items to search through.
     * @param mixed $clickedId ID of the parent menu item, null if starting from root.
     *
     * @return array An array of IDs representing the full hierarchy of child menu items.
     */
    public function getFullChildren($objMenuArray, $clickedId = null)
    {
        $objTempArray = [];
        foreach ($objMenuArray as $objMenu) {
            if ($objMenu->ParentId == $clickedId) {
                $objTempArray[] = $objMenu->Id;
                array_push($objTempArray, ...$this->getFullChildren($objMenuArray, $objMenu->Id));
            }
        }
        return $objTempArray;
    }

    /**
     * Retrieves the ancestor ID of a currently clicked menu item from an array of menu objects.
     *
     * @param array $objMenuArray The array of menu objects to search through.
     * @param mixed $clickedId The ID of the clicked menu item for which the ancestor ID is to be found.
     *                         Defaults to null if no specific ID is given.
     * @return mixed The ancestor ID if found, null otherwise.
     */
    public function getAncestorId($objMenuArray, $clickedId = null)
    {
        foreach($objMenuArray as $objMenu) {
            if ($objMenu->Id == $clickedId) {
                return $objMenu->ParentId == null &&
                $objMenu->Right !== $objMenu->Left + 1 ? $objMenu->Id : $this->getAncestorId($objMenuArray, $objMenu->ParentId);
            }
        }
        return null;
    }

    /**
     * Verifies the lock status of selected pages by loading their content and checking if they are locked.
     *
     * @param object $objMenuContent An object that must have a `load` method to retrieve page content.
     * @param array $selectedPageArray An array of page IDs whose lock status will be verified.
     * @return int The number of pages that are locked.
     * @throws \Exception If the $objMenuContent object does not have a `load` method
     *                    or if content for a selected page fails to load.
     */
    public function verifyPageLockStatus($objMenuContent, array $selectedPageArray): int
    {
        // Check if $objMenuContent has a `load` method
        if (!method_exists($objMenuContent, 'load')) {
            throw new \Exception("The given object does not have a working `load` method.");
        }

        $countPageLocks = 0;

        foreach ($selectedPageArray as $selectedPage) {
            // Load the page content and check if it is correct
            $objContent = $objMenuContent::load($selectedPage);

            if (!$objContent) {
                throw new \Exception("Failed to load content for page with ID {$selectedPage}.");
            }

            // Check if SelectedPageLocked is 1
            if ($objContent->SelectedPageLocked == 1) {
                $countPageLocks++;
            }
        }

        return $countPageLocks;
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
     * Attaches various event listeners and JavaScript functionalities to HTML elements
     * for handling nested sortable lists, button states, and sort stop events within a given context.
     *
     * @return string The parent class's end script, modified with additional JavaScript functionalities
     *                for nested sortable lists and UI interactions.
     */
    public function getEndScript()
    {
        Application::executeSelectorFunction(".disclose", "on", "click",
            new Js\Closure("jQuery(this).closest('li').toggleClass('mjs-nestedSortable-expanded').toggleClass('mjs-nestedSortable-collapsed')"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("[data-collapse='true']", "on", "click",
            new Js\Closure("jQuery('#{$this->ControlId}').find('li.mjs-nestedSortable-expanded').removeClass('mjs-nestedSortable-expanded').addClass('mjs-nestedSortable-collapsed')"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("[data-collapse='false']", "on", "click",
            new Js\Closure("jQuery('#{$this->ControlId}').find('li.mjs-nestedSortable-collapsed').removeClass('mjs-nestedSortable-collapsed').addClass('mjs-nestedSortable-expanded')"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("body", "on", "click", "[data-buttons='true']",
            new Js\Closure("jQuery(\"[data-status='change'], [data-edit='true'], [data-delete='true']\").prop('disable', true);"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("body", "on", "click", "[data-buttons='false']",
            new Js\Closure("jQuery(\"[data-status='change'], [data-edit='true'], [data-delete='true']\").prop('disable', false);"),
            Application::PRIORITY_HIGH);

        /**
         * The nestedsortable functions here do not support locking the first menu item.
         * Or are they unsupported or not working well?
         * Simple locking is added here.
         * But it can be hidden or removed if you need to.
         */
        //Application::executeJavaScript(sprintf("jQuery('#{$this->ControlId}_1').addClass('disabled')"));
        //Application::executeJavaScript(sprintf("jQuery('#{$this->ControlId}_33').closest('li').css('border', '2px solid red')"));

        $strJS = parent::getEndScript();

        $strCtrlJs = <<<FUNC
jQuery('#{$this->ControlId}').on("sortstop", function (event, ui) {
    var draggedItemId = ui.item.attr('id');
    var cleanedId = draggedItemId.split('_')[1];
    console.log("Cleaned Item ID: " + cleanedId);

    var arr = jQuery(this).nestedSortable("toArray", {startDepthCount: 0});
    arr.shift();
    var str = JSON.stringify(arr);
    console.log(str);
    
    qcubed.recordControlModification("$this->ControlId", "_ItemArray", str);
    qcubed.recordControlModification("$this->ControlId", "_Item", cleanedId);
})
FUNC;
        Application::executeJavaScript($strCtrlJs, Application::PRIORITY_HIGH);

        return $strJS;
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case '_Item': // Internal only. Do not use. Used by JS above to track selections.
                try {
                    $data = Type::cast($mixValue, Type::STRING);
                    $this->strItem = $data;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
            case '_ItemArray': // Internal only. Do not use. Used by JS above to track selections.
                try {
                    $jsonData = json_decode($mixValue, true);
                    $data = Type::cast($jsonData, Type::ARRAY_TYPE);
                    $this->aryItemArray = $data;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
            case "Id":
                try {
                    $this->blnModified = true;
                    $this->intId = Type::Cast($mixValue, Type::INTEGER);
                    $this->blnModified = true;
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
            case "RedirectUrl":
                try {
                    $this->blnModified = true;
                    $this->strRedirectUrl = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "IsRedirect":
                try {
                    $this->blnModified = true;
                    $this->intIsRedirect = Type::Cast($mixValue, Type::INTEGER);
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
            case "SelectedPageId":
                try {
                    $this->blnModified = true;
                    $this->intSelectedPageId = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "SelectedPage":
                try {
                    $this->blnModified = true;
                    $this->strSelectedPage = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "SelectedPageLocked":
                try {
                    $this->blnModified = true;
                    $this->intSelectedPageLocked = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "ContentTypeObject":
                try {
                    $this->blnModified = true;
                    $this->strContentTypeObject = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "ContentType":
                try {
                    $this->blnModified = true;
                    $this->intContentType = Type::Cast($mixValue, Type::INTEGER);
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
            case "WrapperClass":
                try {
                    $this->blnModified = true;
                    $this->strWrapperClass = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "SectionClass":
                try {
                    $this->blnModified = true;
                    $this->strSectionClass = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "DataSource":
                $this->blnModified = true;
                $this->objDataSource = $mixValue;
                break;
            case "MenuItemAppend":
                try {
                    $this->blnModified = true;
                    $this->blnMenuItemAppend = Type::Cast($mixValue, Type::BOOLEAN);
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

    public function __get($strName)
    {
        switch ($strName) {
            case 'Item': return $this->strItem;
            case 'ItemArray': return $this->aryItemArray;
            case "Id": return $this->intId;
            case "ParentId": return $this->intParentId;
            case "Depth": return $this->intDepth;
            case "Left": return $this->intLeft;
            case "Right": return $this->intRight;
            case "MenuText": return $this->strMenuText;
            case "RedirectUrl": return $this->strRedirectUrl;
            case "IsRedirect": return $this->intIsRedirect;
            case "ExternalUrl": return $this->strExternalUrl;
            case "SelectedPageId": return $this->intSelectedPageId;
            case "SelectedPage": return $this->strSelectedPage;
            case "SelectedPageLocked": return $this->intSelectedPageLocked;
            case "ContentTypeObject": return $this->strContentTypeObject;
            case "ContentType": return $this->intContentType;
            case "Status": return $this->intStatus;
            case "WrapperClass": return $this->strWrapperClass;
            case "SectionClass": return $this->strSectionClass;
            case "DataSource": return $this->objDataSource;
            case "MenuItemAppend": return $this->blnMenuItemAppend;

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