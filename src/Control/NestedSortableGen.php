<?php
namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Project\Control\ControlBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class NestedSortableGen
 * @package QCubed\Plugin
 */

/**
 * @see NestedSortableBase
 * @property mixed $AppendTo Defines where the helper that moves with the mouse is being appended to during the drag (for example, to resolve overlap/zIndex issues).
 * @property string $Axis If defined, the items can be dragged only horizontally or vertically. Possible values: "x", "y".
 * @property mixed $Cancel
 * Prevents sorting if you start on elements matching the selector.
 * @property mixed $Classes Specify additional classes to add to the widgets elements. Any of classes specified in the Theming section can be used as keys to override their value. To learn more about this option, check out the learn article about the classes option.
 * @property mixed $ConnectWith A selector of other sortable elements that the items from this list should be connected to. This is a one-way relationship, if you want the items to be connected in both directions, the connectWith option must be set on both sortable elements.
 * @property mixed $Containment Defines a bounding box that the sortable items are constrained to while dragging.
 * @property string $Cursor Defines the cursor that is being shown while sorting.
 * @property mixed $CursorAt Moves the sorting element or helper so the cursor always appears to drag from the same position. Coordinates can be given as a hash using a combination of one or two keys: { top, left, right, bottom }.
 * @property integer $Delay Time in milliseconds to define when the sorting should start. Adding a delay helps preventing unwanted drags when clicking on an element.(version deprecated: 1.12)
 * @property boolean $Disabled Disables the sortable if set to true.
 * @property integer $Distance Tolerance, in pixels, for when sorting should start. If specified, sorting will not start until after mouse is dragged beyond distance. Can be used to allow for clicks on elements within a handle.(version deprecated: 1.12)
 * @property boolean $DropOnEmpty If false, items from this sortable cant be dropped on an empty connect sortable (see the connectWith option.
 * @property boolean $ForceHelperSize If true, forces the helper to have a size.
 * @property boolean $ForcePlaceholderSize If true, forces the placeholder to have a size.
 * @property array $Grid Snaps the sorting element or helper to a grid, every x and y pixels. Array values: [ x, y ].
 * @property mixed $Handle Restricts sort start click to the specified element.
 * @property mixed $Helper Allows for a helper element to be used for dragging display.Multiple types supported:
 * @property mixed $Items Specifies which items inside the element should be sortable.
 * @property float $Opacity Defines the opacity of the helper while sorting. From 0.01 to 1.
 * @property string $Placeholder A class name that gets applied to the otherwise white space.
 * @property mixed $Revert Whether the sortable items should revert to their new positions using a smooth animation.Multiple types supported:
 * @property boolean $Scroll If set to true, the page scrolls when coming to an edge.
 * @property integer $ScrollSensitivity Defines how near the mouse must be to an edge to start scrolling.
 * @property integer $ScrollSpeed The speed at which the window should scroll once the mouse pointer gets within the scrollSensitivity distance.
 * @property string $Tolerance Specifies which mode to use for testing whether the item being moved is hovering over another item. Possible values:
 * @property integer $ZIndex Z-index for element/helper while being sorted.
 *
 *
 * @property boolean $DisableParentChange Set this to true to lock the parentship of items. They can only be re-ordered within theire current parent container.
 * @property boolean $DoNotClear Set this to true if you don't want empty lists to be removed. Default: false
 * @property integer $ExpandOnHover How long (in ms) to wait before expanding a collapsed node (useful only if isTree: true). Default: 700
 * @property function $IsAllowed You can specify a custom function to verify if a drop location is allowed. Default: function (placeholder, placeholderParent, currentItem) { return true; }
 * @property boolean $IsTree Set this to true if you want to use the new tree functionality. Default: false
 * @property string $ListType The list type used (ordered or unordered). Default: ol
 * @property integer $MaxLevels The maximum depth of nested items the list can accept. If set to '0' the levels are unlimited. Default: 0
 * @property boolean $ProtectRoot Whether to protect the root level (i.e. root items can be sorted but not nested, sub-items cannot become root items). Default: false
 * @property integer $RootId The id given to the root element (set this to whatever suits your data structure). Default: null
 * @property boolean $ExcludeRoot Exlude the root item from the toArray output
 * @property boolean $RTL Set this to true if you have a right-to-left page. Default: false
 * @property boolean $StartCollapsed Set this to true if you want the plugin to collapse the tree on page load. Default: false
 * @property integer $TabSize How far right or left (in pixels) the item has to travel in order to be nested or to be sent outside its current list. Default: 20
 * @property string $ToleranceElement ...
 * @property string $BranchClass Given to all items that have children. Default: mjs-nestedSortable-branch
 * @property string $CollapsedClass Given to branches that are collapsed. It will be switched to expandedClass when hovering for more then expandOnHover ms. Default: mjs-nestedSortable-collapsed
 * @property string $DisableNestingClass Given to items that will not accept children. Default: mjs-nestedSortable-no-nesting
 * @property string $ErrorClass Given to the placeholder in case of error. Default: mjs-nestedSortable-error
 * @property string $ExpandedClass Given to branches that are expanded. Default: mjs-nestedSortable-expanded
 * @property string $HoveringClass Given to collapsed branches when dragging an item over them. Default: mjs-nestedSortable-hovering
 * @property string $LeafClass Given to items that do not have children. Default: mjs-nestedSortable-leaf
 * @property string $DisabledClass Given to items that should be skipped when sorting over them. For example, non-visible items that are still part of the list. Default: mjs-nestedSortable-disabled
 *
 */

class NestedSortableGen extends Q\Control\BlockControl
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;

    // Copied from SortableGen class

    /** @var mixed */
    protected $mixAppendTo = null;
    /** @var string */
    protected $strAxis = null;
    /** @var mixed */
    protected $mixCancel = null;
    /** @var mixed */
    protected $mixClasses = null;
    /** @var mixed */
    protected $mixConnectWith = null;
    /** @var mixed */
    protected $mixContainment = null;
    /** @var string */
    protected $strCursor = null;
    /** @var mixed */
    protected $mixCursorAt = null;
    /** @var integer */
    protected $intDelay;
    /** @var boolean */
    protected $blnDisabled = null;
    /** @var integer */
    protected $intDistance = null;
    /** @var boolean */
    protected $blnDropOnEmpty = null;
    /** @var boolean */
    protected $blnForceHelperSize = null;
    /** @var boolean */
    protected $blnForcePlaceholderSize = null;
    /** @var array */
    protected $arrGrid = null;
    /** @var mixed */
    protected $mixHandle = null;
    /** @var mixed */
    protected $mixHelper = null;
    /** @var mixed */
    protected $mixItems = null;
    /** @var float */
    protected $fltOpacity = null;
    /** @var string */
    protected $strPlaceholder = null;
    /** @var mixed */
    protected $mixRevert = null;
    /** @var boolean */
    protected $blnScroll = null;
    /** @var integer */
    protected $intScrollSensitivity = null;
    /** @var integer */
    protected $intScrollSpeed = null;
    /** @var string */
    protected $strTolerance = null;
    /** @var integer */
    protected $intZIndex = null;

    ////////////////////////////////////////////////////

    /** @var boolean */
    protected $blnDisableParentChange = null;
    /** @var boolean */
    protected $blnDoNotClear = null;
    /** @var integer */
    protected $intExpandOnHover = null;
    /** @var function */
    protected $objIsAllowed = null;
    /** @var boolean */
    protected $blnIsTree = null;
    /** @var string */
    protected $strListType = null;
    /** @var integer */
    protected $intMaxLevels = null;
    /** @var boolean */
    protected $blnProtectRoot = null;
    /** @var integer */
    protected $intRootId = null;
    /** @var boolean */
    protected $blnExcludeRoot = null;
    /** @var boolean */
    protected $blnRTL = null;
    /** @var boolean */
    protected $blnStartCollapsed = null;
    /** @var integer */
    protected $intTabSize = null;
    /** @var string */
    protected $strToleranceElement = null;
    /** @var string */
    protected $strBranchClass = null;
    /** @var string */
    protected $strCollapsedClass = null;
    /** @var string */
    protected $strDisableNestingClass = null;
    /** @var string */
    protected $strErrorClass = null;
    /** @var string */
    protected $strExpandedClass = null;
    /** @var string */
    protected $strHoveringClass = null;
    /** @var string */
    protected $strLeafClass = null;
    /** @var string */
    protected $strDisabledClass = null;

    /**
     * Generates jQuery options for a sortable control.
     * It includes several customizable parameters like appendTo, axis, cancel, classes, connectWith, containment,
     * cursor, cursorAt, delay, disabled, distance, dropOnEmpty, forceHelperSize, forcePlaceholderSize, grid,
     * handle, helper, items, opacity, placeholder, revert, scroll, scrollSensitivity, scrollSpeed, tolerance,
     * zIndex, and other specific options.
     * Firing a 'create' event once options are set.
     *
     * @return array An associative array containing all the jQuery options for the sortable control.
     */

    protected function makeJqOptions()
    {
        $jqOptions = parent::MakeJqOptions();

        if (!is_null($val = $this->AppendTo)) {$jqOptions['appendTo'] = $val;}
        if (!is_null($val = $this->Axis)) {$jqOptions['axis'] = $val;}
        if (!is_null($val = $this->Cancel)) {$jqOptions['cancel'] = $val;}
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->ConnectWith)) {$jqOptions['connectWith'] = $val;}
        if (!is_null($val = $this->Containment)) {$jqOptions['containment'] = $val;}
        if (!is_null($val = $this->Cursor)) {$jqOptions['cursor'] = $val;}
        if (!is_null($val = $this->CursorAt)) {$jqOptions['cursorAt'] = $val;}
        if (!is_null($val = $this->Delay)) {$jqOptions['delay'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Distance)) {$jqOptions['distance'] = $val;}
        if (!is_null($val = $this->DropOnEmpty)) {$jqOptions['dropOnEmpty'] = $val;}
        if (!is_null($val = $this->ForceHelperSize)) {$jqOptions['forceHelperSize'] = $val;}
        if (!is_null($val = $this->ForcePlaceholderSize)) {$jqOptions['forcePlaceholderSize'] = $val;}
        if (!is_null($val = $this->Grid)) {$jqOptions['grid'] = $val;}
        if (!is_null($val = $this->Handle)) {$jqOptions['handle'] = $val;}
        if (!is_null($val = $this->Helper)) {$jqOptions['helper'] = $val;}
        if (!is_null($val = $this->Items)) {$jqOptions['items'] = $val;}
        if (!is_null($val = $this->Opacity)) {$jqOptions['opacity'] = $val;}
        if (!is_null($val = $this->Placeholder)) {$jqOptions['placeholder'] = $val;}
        if (!is_null($val = $this->Revert)) {$jqOptions['revert'] = $val;}
        if (!is_null($val = $this->Scroll)) {$jqOptions['scroll'] = $val;}
        if (!is_null($val = $this->ScrollSensitivity)) {$jqOptions['scrollSensitivity'] = $val;}
        if (!is_null($val = $this->ScrollSpeed)) {$jqOptions['scrollSpeed'] = $val;}
        if (!is_null($val = $this->Tolerance)) {$jqOptions['tolerance'] = $val;}
        if (!is_null($val = $this->ZIndex)) {$jqOptions['zIndex'] = $val;}

        if (!is_null($val = $this->DisableParentChange)) {$jqOptions['disableParentChange'] = $val;}
        if (!is_null($val = $this->DoNotClear)) {$jqOptions['doNotClear'] = $val;}
        if (!is_null($val = $this->ExpandOnHover)) {$jqOptions['expandOnHover'] = $val;}
        if (!is_null($val = $this->IsAllowed)) {$jqOptions['isAllowed'] = $val;}
        if (!is_null($val = $this->IsTree)) {$jqOptions['isTree'] = $val;}
        if (!is_null($val = $this->ListType)) {$jqOptions['listType'] = $val;}
        if (!is_null($val = $this->MaxLevels)) {$jqOptions['maxLevels'] = $val;}
        if (!is_null($val = $this->ProtectRoot)) {$jqOptions['protectRoot'] = $val;}
        if (!is_null($val = $this->RootId)) {$jqOptions['rootID'] = $val;}
        if (!is_null($val = $this->ExcludeRoot)) {$jqOptions['excludeRoot'] = $val;}
        if (!is_null($val = $this->RTL)) {$jqOptions['rtl'] = $val;}
        if (!is_null($val = $this->StartCollapsed)) {$jqOptions['startCollapsed'] = $val;}
        if (!is_null($val = $this->TabSize)) {$jqOptions['tabSize'] = $val;}
        if (!is_null($val = $this->ToleranceElement)) {$jqOptions['toleranceElement'] = $val;}
        if (!is_null($val = $this->BranchClass)) {$jqOptions['branchClass'] = $val;}
        if (!is_null($val = $this->CollapsedClass)) {$jqOptions['collapsedClass'] = $val;}
        if (!is_null($val = $this->DisableNestingClass)) {$jqOptions['disableNestingClass'] = $val;}
        if (!is_null($val = $this->ErrorClass)) {$jqOptions['errorClass'] = $val;}
        if (!is_null($val = $this->ExpandedClass)) {$jqOptions['expandedClass'] = $val;}
        if (!is_null($val = $this->HoveringClass)) {$jqOptions['hoveringClass'] = $val;}
        if (!is_null($val = $this->LeafClass)) {$jqOptions['leafClass'] = $val;}
        if (!is_null($val = $this->DisabledClass)) {$jqOptions['disabledClass'] = $val;}

        $jqOptions['create'] = new Q\Js\Closure('
                        var arr = jQuery(this).nestedSortable("toArray", {startDepthCount: 0});
                        arr.shift();
                        var str = JSON.stringify(arr);
                        console.log(str);
                        qcubed.recordControlModification("$this->ControlId", "_ItemArray", str);
         ');
        return $jqOptions;
    }

    /**
     * Returns the jQuery setup function name for initializing nested sortable functionality.
     *
     * @return string The jQuery setup function name 'nestedSortable'.
     */
    public function getJqSetupFunction()
    {
        return 'nestedSortable';
    }

    /**
     * Cancels a change in the current sortable and reverts it to the state
     * prior to when the current sort was started. Useful in the stop and
     * receive callback functions.
     *
     * This method does not accept any arguments.
     */
    public function cancel()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "cancel", Application::PRIORITY_LOW);
    }

    /**
     * Removes the sortable functionality completely. This will return the
     * element back to its pre-init state.
     *
     * This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }

    /**
     * Disables the sortable.
     *
     * This method does not accept any arguments.
     */
    public function disable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", Application::PRIORITY_LOW);
    }

    /**
     * Enables the sortable.
     *
     * This method does not accept any arguments.
     */
    public function enable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", Application::PRIORITY_LOW);
    }

    /**
     * Retrieves the sortables instance object. If the element does not have
     * an associated instance, undefined is returned.
     *
     * Unlike other widget methods, instance() is safe to call on any element
     * after the sortable plugin has loaded.
     *
     * This method does not accept any arguments.
     */
    public function instance()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", Application::PRIORITY_LOW);
    }

    /**
     * Gets the value currently associated with the specified optionName.
     *
     * Note: For options that have objects as their value, you can get the
     * value of a specific key by using dot notation. For example, "foo.bar"
     * would get the value of the bar property on the foo option.
     *
     * optionName Type: String The name of the option to get.
     * @param $optionName
     */
    public function option($optionName)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, Application::PRIORITY_LOW);
    }

    /**
     * Gets an object containing key/value pairs representing the current
     * sortable options hash.
     *
     * This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
    }

    /**
     * Sets the value of the sortable option associated with the specified
     * optionName.
     *
     * Note: For options that have objects as their value, you can set the
     * value of just one property by using dot notation for optionName. For
     * example, "foo.bar" would update only the bar property of the foo
     * option.
     *
     * optionName Type: String The name of the option to set.
     * value Type: Object A value to set for the option.
     * @param $optionName
     * @param $value
     */
    public function option2($optionName, $value)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, Application::PRIORITY_LOW);
    }

    /**
     * Sets one or more options for the sortable.
     *
     * options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }

    /**
     * Refresh the sortable items. Triggers the reloading of all sortable
     * items, causing new items to be recognized.
     *
     * This method does not accept any arguments.
     */
    public function refresh()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", Application::PRIORITY_LOW);
    }

    /**
     * Refresh the cached positions of the sortable items. Calling this
     * method refreshes the cached item positions of all sortables.
     *
     * This method does not accept any arguments.
     */
    public function refreshPositions()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refreshPositions", Application::PRIORITY_LOW);
    }

    /**
     * Serializes the sortables item ids into a form/ajax submittable string.
     * Calling this method produces a hash that can be appended to any url to
     * easily submit a new item order back to the server.
     *
     * It works by default by looking at the id of each item in the format
     * "setname_number", and it spits out a hash like
     * "setname[]=number&setname[]=number".
     *
     * _Note: If serialize returns an empty string, make sure the id
     * attributes include an underscore. They must be in the form:
     * "set_number" For example, a 3 element list with id attributes "foo_1",
     * "foo_5", "foo_2" will serialize to "foo[]=1&foo[]=5&foo[]=2". You can
     * use an underscore, equal sign or hyphen to separate the set and
     * number. For example "foo=1", "foo-1", and "foo_1" all serialize to
     * "foo[]=1"._
     *
     * options Type: Object Options to customize the serialization.
     *
     * key (default: the part of the attribute in front of the separator)
     * Type: String Replaces part1[] with the specified value.
     * 	* attribute (default: "id") Type: String The name of the attribute
     * to use for the values.
     * 	* expression (default: /(.+)[-=_](.+)/) Type: RegExp A regular
     * expression used to split the attribute value into key and value parts.
     * @param $options
     */
    public function serialize($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "serialize", $options, Application::PRIORITY_LOW);
    }

    /**
     * Serializes the sortables item ids into an array of string.
     *
     * options Type: Object Options to customize the serialization.
     *
     * attribute (default: "id") Type: String The name of the attribute to
     * use for the values.
     * @param $options
     */
    public function toArray($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "toArray", $options, Application::PRIORITY_LOW);
    }

    /**
     * Fires when the item is dragged to a new location.
     * This triggers for each location it is dragged into not just the ending location.
     *
     * This method does not accept any arguments.
     */
    public function change()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "change", Application::PRIORITY_LOW);
    }

    /**
     * Fires when the item is dragged.
     *
     * This method does not accept any arguments.
     */
    public function sort()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "sort", Application::PRIORITY_LOW);
    }

    /**
     * Fires once the object has moved if the new location is invalid.
     *
     * This method does not accept any arguments.
     */
    public function revert()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "revert", Application::PRIORITY_LOW);
    }

    /**
     * Only fires once when the item is done bing moved at its final location.
     *
     * This method does not accept any arguments.
     */
    public function relocate()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "relocate", Application::PRIORITY_LOW);
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'AppendTo': return $this->mixAppendTo;
            case 'Axis': return $this->strAxis;
            case 'Cancel': return $this->mixCancel;
            case 'Classes': return $this->mixClasses;
            case 'ConnectWith': return $this->mixConnectWith;
            case 'Containment': return $this->mixContainment;
            case 'Cursor': return $this->strCursor;
            case 'CursorAt': return $this->mixCursorAt;
            case 'Delay': return $this->intDelay;
            case 'Disabled': return $this->blnDisabled;
            case 'Distance': return $this->intDistance;
            case 'DropOnEmpty': return $this->blnDropOnEmpty;
            case 'ForceHelperSize': return $this->blnForceHelperSize;
            case 'ForcePlaceholderSize': return $this->blnForcePlaceholderSize;
            case 'Grid': return $this->arrGrid;
            case 'Handle': return $this->mixHandle;
            case 'Helper': return $this->mixHelper;
            case 'Items': return $this->mixItems;
            case 'Opacity': return $this->fltOpacity;
            case 'Placeholder': return $this->strPlaceholder;
            case 'Revert': return $this->mixRevert;
            case 'Scroll': return $this->blnScroll;
            case 'ScrollSensitivity': return $this->intScrollSensitivity;
            case 'ScrollSpeed': return $this->intScrollSpeed;
            case 'Tolerance': return $this->strTolerance;
            case 'ZIndex': return $this->intZIndex;

            case 'DisableParentChange': return $this->blnDisableParentChange;
            case 'DoNotClear': return $this->blnDoNotClear;
            case 'ExpandOnHover': return $this->intExpandOnHover;
            case 'IsAllowed': return $this->objIsAllowed;
            case 'IsTree': return $this->blnIsTree;
            case 'ListType': return $this->strListType;
            case 'MaxLevels': return $this->intMaxLevels;
            case 'ProtectRoot': return $this->blnProtectRoot;
            case 'RootId': return $this->intRootId;
            case 'ExcludeRoot': return $this->blnExcludeRoot;
            case 'RTL': return $this->blnRTL;
            case 'StartCollapsed': return $this->blnStartCollapsed;
            case 'TabSize': return $this->intTabSize;
            case 'ToleranceElement': return $this->strToleranceElement;

            case 'BranchClass': return $this->strBranchClass;
            case 'CollapsedClass': return $this->strCollapsedClass;
            case 'DisableNestingClass': return $this->strDisableNestingClass;
            case 'ErrorClass': return $this->strErrorClass;
            case 'ExpandedClass': return $this->strExpandedClass;
            case 'HoveringClass': return $this->strHoveringClass;
            case 'LeafClass': return $this->strLeafClass;
            case 'DisabledClass': return $this->strDisabledClass;
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

            case 'AppendTo':
                $this->mixAppendTo = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'appendTo', $mixValue);
                break;

            case 'Axis':
                try {
                    $this->strAxis = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'axis', $this->strAxis);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Cancel':
                $this->mixCancel = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'cancel', $mixValue);
                break;

            case 'Classes':
                $this->mixClasses = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'classes', $mixValue);
                break;

            case 'ConnectWith':
                $this->mixConnectWith = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'connectWith', $mixValue);
                break;

            case 'Containment':
                $this->mixContainment = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'containment', $mixValue);
                break;

            case 'Cursor':
                try {
                    $this->strCursor = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'cursor', $this->strCursor);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'CursorAt':
                $this->mixCursorAt = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'cursorAt', $mixValue);
                break;

            case 'Delay':
                try {
                    $this->intDelay = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'delay', $this->intDelay);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Disabled':
                try {
                    $this->blnDisabled = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabled', $this->blnDisabled);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Distance':
                try {
                    $this->intDistance = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'distance', $this->intDistance);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'DropOnEmpty':
                try {
                    $this->blnDropOnEmpty = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'dropOnEmpty', $this->blnDropOnEmpty);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ForceHelperSize':
                try {
                    $this->blnForceHelperSize = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'forceHelperSize', $this->blnForceHelperSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ForcePlaceholderSize':
                try {
                    $this->blnForcePlaceholderSize = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'forcePlaceholderSize', $this->blnForcePlaceholderSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Grid':
                try {
                    $this->arrGrid = Type::Cast($mixValue, Type::ARRAY_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'grid', $this->arrGrid);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Handle':
                $this->mixHandle = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'handle', $mixValue);
                break;

            case 'Helper':
                $this->mixHelper = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'helper', $mixValue);
                break;

            case 'Items':
                $this->mixItems = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'items', $mixValue);
                break;

            case 'Opacity':
                try {
                    $this->fltOpacity = Type::Cast($mixValue, Type::FLOAT);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'opacity', $this->fltOpacity);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Placeholder':
                try {
                    $this->strPlaceholder = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'placeholder', $this->strPlaceholder);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Revert':
                $this->mixRevert = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'revert', $mixValue);
                break;

            case 'Scroll':
                try {
                    $this->blnScroll = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'scroll', $this->blnScroll);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ScrollSensitivity':
                try {
                    $this->intScrollSensitivity = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'scrollSensitivity', $this->intScrollSensitivity);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ScrollSpeed':
                try {
                    $this->intScrollSpeed = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'scrollSpeed', $this->intScrollSpeed);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Tolerance':
                try {
                    $this->strTolerance = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tolerance', $this->strTolerance);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ZIndex':
                try {
                    $this->intZIndex = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'zIndex', $this->intZIndex);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }


            case 'Enabled':
                $this->Disabled = !$mixValue;	// Tie in standard QCubed functionality
                parent::__set($strName, $mixValue);
                break;

            case 'DisableParentChange':
                try {
                    $this->blnDisableParentChange = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disableParentChange', $this->blnDisableParentChange);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'DoNotClear':
                try {
                    $this->blnDoNotClear = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'doNotClear', $this->blnDoNotClear);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ExpandOnHover':
                try {
                    $this->intExpandOnHover = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'expandOnHover', $this->intExpandOnHover);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'IsAllowed':
                try {
                    $this->objIsAllowed = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'isAllowed', $this->objIsAllowed);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'IsTree':
                try {
                    $this->blnIsTree = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'isTree', $this->blnIsTree);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ListType':
                try {
                    $this->strListType = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'listType', $this->strListType);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'MaxLevels':
                try {
                    $this->intMaxLevels = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'maxLevels', $this->intMaxLevels);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ProtectRoot':
                try {
                    $this->blnProtectRoot = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'protectRoot', $this->blnProtectRoot);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'RootId':
                try {
                    $this->intRootId = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rootID', $this->intRootId);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ExcludeRoot':
                try {
                    $this->blnExcludeRoot = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'excludeRoot', $this->intRootId);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'RTL':
                try {
                    $this->blnRTL = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rtl', $this->blnRTL);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'StartCollapsed':
                try {
                    $this->blnStartCollapsed = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'startCollapsed', $this->blnStartCollapsed);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'TabSize':
                try {
                    $this->intTabSize = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tabSize', $this->intTabSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ToleranceElement':
                try {
                    $this->strToleranceElement = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'toleranceElement', $this->strToleranceElement);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }


            case 'BranchClass':
                try {
                    $this->strBranchClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'branchClass', $this->strBranchClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'CollapsedClass':
                try {
                    $this->strCollapsedClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'collapsedClass', $this->strCollapsedClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'DisableNestingClass':
                try {
                    $this->strDisableNestingClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disableNestingClass', $this->strDisableNestingClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ErrorClass':
                try {
                    $this->strErrorClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'errorClass', $this->strErrorClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ExpandedClass':
                try {
                    $this->strExpandedClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'expandedClass', $this->strExpandedClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'HoveringClass':
                try {
                    $this->strHoveringClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'hoveringClass', $this->strHoveringClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'LeafClass':
                try {
                    $this->strLeafClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'leafClass', $this->strLeafClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'DisabledClass':
                try {
                    $this->strDisabledClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabledClass', $this->strDisabledClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
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

    /**
     * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
     * used by the ModelConnector designer dialog to display a list of options for the control.
     * @return QModelConnectorParam[]
     **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array());
    }
}
