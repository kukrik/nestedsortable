<?php
    namespace QCubed\Plugin\Control;

    use QCubed as Q;
    use QCubed\ApplicationBase;
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
     * @property mixed $Classes Specify additional classes to add to the widget elements. Any of the classes specified in the Theming section can be used as keys to override their value. To learn more about this option, check out the learned article about the classes option.
     * @property mixed $ConnectWith A selector of other sortable elements that the items from this list should be connected to. This is a one-way relationship, if you want the items to be connected in both directions, the connectWith option must be set on both sortable elements.
     * @property mixed $Containment Defines a bounding box that the sortable items are constrained to while dragging.
     * @property string $Cursor Defines the cursor that is being shown while sorting.
     * @property mixed $CursorAt Moves the sorting element or helper so the cursor always appears to drag from the same position. Coordinates can be given as a hash using a combination of one or two keys: { top, left, right, bottom }.
     * @property integer $Delay Time in milliseconds to define when the sorting should start. Adding a delay helps to prevent unwanted drags when clicking on an element. (version deprecated: 1.12)
     * @property boolean $Disabled Disables the sortable if set to true.
     * @property integer $Distance Tolerance, in pixels, for when sorting should start. If specified, sorting will not start until after a mouse is dragged beyond distance. Can be used to allow for clicks on elements within a handle. (version deprecated: 1.12)
     * @property boolean $DropOnEmpty If false, items from this sortable can't be dropped on an empty connecting Portable (see the connectWith option.
     * @property boolean $ForceHelperSize If true, forces the helper to have a size.
     * @property boolean $ForcePlaceholderSize If true, forces the placeholder to have a size.
     * @property array $Grid Snaps the sorting element or helper to a grid, every x and y pixels. Array values: [ x, y ].
     * @property mixed $Handle Restricts sort starts to click to the specified element.
     * @property mixed $Helper Allows for a helper element to be used for dragging display.Multiple types supported:
     * @property mixed $Items Specifies which items inside the element should be sortable.
     * @property float $Opacity Defines the opacity of the helper while sorting. From 0.01 to 1.
     * @property string $Placeholder A class name that gets applied to the otherwise white space.
     * @property mixed $Revert Whether the sortable items should revert to their new positions using a smooth animation.Multiple types supported:
     * @property boolean $Scroll If set to true, the page scrolls when coming to an edge.
     * @property integer $ScrollSensitivity Defines how near the mouse must be to an edge to start scrolling.
     * @property integer $ScrollSpeed The speed at which the window should scroll once the mouse pointer gets within the scrollSensitivity distance.
     * @property string $Tolerance Specifies which mode to use for testing whether the item being moved is hovering over another item. Possible values:
     * @property integer $ZIndex Z-index for an element/helper while being sorted.
     *
     *
     * @property boolean $DisableParentChange Set this to true to lock the parentship of items. They can only be re-ordered within their current parent container.
     * @property boolean $DoNotClear Set this to true if you don't want empty lists to be removed. Default: false
     * @property integer $ExpandOnHover How long (in ms) to wait before expanding a collapsed node (useful only if isTree: true). Default: 700
     * @property boolean $IsAllowed You can specify a custom function to verify if a drop location is allowed. Default: function (placeholder, placeholderParent, currentItem) { return true; }
     * @property boolean $IsTree Set this to true if you want to use the new tree functionality. Default: false
     * @property string $ListType The list type used (ordered or unordered). Default: ol
     * @property integer $MaxLevels The maximum depth of nested items the list can accept. If set to '0', the levels are unlimited. Default: 0
     * @property boolean $ProtectRoot Whether to protect the root level (i.e., root items can be sorted but not nested, subitems cannot become root items). Default: false
     * @property integer $RootId The id given to the root element (set this to whatever suits your data structure). Default: null
     * @property boolean $ExcludeRoot Exclude the root item from the toArray output
     * @property boolean $RTL Set this to true if you have a right-to-left page. Default: false
     * @property boolean $StartCollapsed Set this to true if you want the plugin to collapse the tree on a page load. Default: false
     * @property integer $TabSize How far right or left (in pixels) the item has to travel in order to be nested or to be sent outside its current list. Default: 20
     * @property string $ToleranceElement ...
     * @property string $BranchClass Given to all items that have children. Default: mjs-nestedSortable-branch
     * @property string $CollapsedClass Given to branches that are collapsed. It will be switched to expandedClass when hovering for more than expandOnHover ms. Default: mjs-nestedSortable-collapsed
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
        protected string $strJavaScripts = QCUBED_JQUI_JS;
        protected string $strStyleSheets = QCUBED_JQUI_CSS;

        // Copied from SortableGen class

        /** @var mixed */
        protected mixed $mixAppendTo = null;
        /** @var null|string */
        protected ?string $strAxis = null;
        /** @var mixed */
        protected mixed $mixCancel = null;
        /** @var mixed */
        protected mixed $mixClasses = null;
        /** @var mixed */
        protected mixed $mixConnectWith = null;
        /** @var mixed */
        protected mixed $mixContainment = null;
        /** @var null|string */
        protected ?string $strCursor = null;
        /** @var mixed */
        protected mixed $mixCursorAt = null;
        /** @var null|integer */
        protected ?int $intDelay = null;
        /** @var boolean */
        protected ?bool $blnDisabled = null;
        /** @var null|integer */
        protected ?int $intDistance = null;
        /** @var boolean */
        protected ?bool $blnDropOnEmpty = null;
        /** @var boolean */
        protected ?bool $blnForceHelperSize = null;
        /** @var boolean */
        protected ?bool $blnForcePlaceholderSize = null;
        /** @var null|array */
        protected ?array $arrGrid = null;
        /** @var mixed */
        protected mixed $mixHandle = null;
        /** @var mixed */
        protected mixed $mixHelper = null;
        /** @var mixed */
        protected mixed $mixItems = null;
        /** @var null|float */
        protected ?float $fltOpacity = null;
        /** @var null|string */
        protected ?string $strPlaceholder = null;
        /** @var mixed */
        protected mixed $mixRevert = null;
        /** @var boolean */
        protected ?bool $blnScroll = null;
        /** @var null|integer */
        protected ?int $intScrollSensitivity = null;
        /** @var null|integer */
        protected ?int $intScrollSpeed = null;
        /** @var null|string */
        protected ?string $strTolerance = null;
        /** @var null|integer */
        protected ?int $intZIndex = null;

        ////////////////////////////////////////////////////

        /** @var boolean */
        protected ?bool $blnDisableParentChange = null;
        /** @var boolean */
        protected ?bool $blnDoNotClear = null;
        /** @var null|integer */
        protected ?int $intExpandOnHover = null;
        /** @var boolean */
        protected ?bool $objIsAllowed = null;
        /** @var boolean */
        protected ?bool $blnIsTree = null;
        /** @var null|string */
        protected ?string $strListType = null;
        /** @var null|integer */
        protected ?int $intMaxLevels = null;
        /** @var boolean */
        protected ?bool $blnProtectRoot = null;
        /** @var null|integer */
        protected ?int $intRootId = null;
        /** @var boolean */
        protected ?bool $blnExcludeRoot = null;
        /** @var boolean */
        protected ?bool $blnRTL = null;
        /** @var boolean */
        protected ?bool $blnStartCollapsed = null;
        /** @var null|integer */
        protected ?int $intTabSize = null;
        /** @var null|string */
        protected ?string $strToleranceElement = null;
        /** @var null|string */
        protected ?string $strBranchClass = null;
        /** @var null|string */
        protected ?string $strCollapsedClass = null;
        /** @var null|string */
        protected ?string $strDisableNestingClass = null;
        /** @var null|string */
        protected ?string $strErrorClass = null;
        /** @var null|string */
        protected ?string $strExpandedClass = null;
        /** @var null|string */
        protected ?string $strHoveringClass = null;
        /** @var null|string */
        protected ?string $strLeafClass = null;
        /** @var null|string */
        protected ?string $strDisabledClass = null;

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
        protected function makeJqOptions(): array
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

            return $jqOptions;
        }

        /**
         * Returns the jQuery setup function name for initializing nested sortable functionality.
         *
         * @return string The jQuery setup function names 'nestedSortable'.
         */
        public function getJqSetupFunction(): string
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
        public function cancel(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "cancel", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Removes the sortable functionality completely. This will return the
         * element back to its pre-init state.
         *
         * This method does not accept any arguments.
         */
        public function destroy(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Disables the sortable.
         *
         * This method does not accept any arguments.
         */
        public function disable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Enables the sortable.
         *
         * This method does not accept any arguments.
         */
        public function enable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", ApplicationBase::PRIORITY_LOW);
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
        public function instance(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Updates the specified option for the control.
         * This modifies the configuration of the control dynamically at runtime.
         *
         * @param string $optionName The name of the option to update.
         *
         * @return void
         */
        public function option(string $optionName): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Gets an object containing key/value pairs representing the current
         * sortable options hash.
         *
         * This signature does not accept any arguments.
         */
        public function option1(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Sets the value of a specified option for the control.
         *
         * @param string $optionName The name of the option to be set.
         * @param mixed $value The value to assign to the option.
         *
         * @return void
         */
        public function option2(string $optionName, mixed $value): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Sets one or more options for the control.
         *
         * @param mixed $options The option name or an associative array of options and their values.
         *
         * @return void
         */
        public function option3(mixed $options): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Refresh the sortable items. Triggers the reloading of all sortable
         * items, causing new items to be recognized.
         *
         * This method does not accept any arguments.
         */
//        public function refresh(): void
//        {
//            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", ApplicationBase::PRIORITY_LOW);
//        }

        /**
         * Refresh the cached positions of the sortable items. Calling this
         * method refreshes the cached item positions of all sortables.
         *
         * This method does not accept any arguments.
         */
        public function refreshPositions(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refreshPositions", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Serializes the sortables item IDs into a form/ajax submittable string.
         * Calling this method produces a hash that can be appended to any url to
         * easily submit a new item order back to the server.
         *
         * It works by default by looking at the id of each item in the format
         * "setname_number", and it spits out a hash like
         * "setname[]=number&setname[]=number".
         *
         * _Note: If serialize returns an empty string, make sure the id
         * attributes include an underscore. They must be in the form:
         * "set_number," For example, a 3-element list with id attributes "foo_1",
         * "foo_5", "foo_2" will serialize to "foo[]=1&foo[]=5&foo[]=2". You can
         * use an underscore, equal sign or hyphen to separate the set and
         * number. For example, "foo=1", "foo-1", and "foo_1" all serialize to
         * "foo[]=1"._
         *
         * Options Type: Object Options to customize the serialization.
         *
         * Key (default: the part of the attribute in front of the separator)
         * Type: String Replaces part1[] with the specified value.
         *    * attribute (default: "id") Type: String The name of the attribute
         * to use for the values.
         *    * expression (default: /(.+)[-=_](.+)/) Type: RegExp A regular
         * expression used to split the attribute value into key and value parts.
         *
         * @param array $options
         */
        public function serialize(array $options): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "serialize", $options, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Serializes the sortables item IDs into an array of string.
         *
         * Options Type: Object Options to customize the serialization.
         *
         * Attribute (default: "id") Type: String The name of the attribute to
         * use for the values.
         *
         * @param array $options
         */
        public function toArray(array $options): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "toArray", $options, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Fires when the item is dragged to a new location.
         * This triggers for each location it is dragged into, not just the ending location.
         *
         * This method does not accept any arguments.
         */
        public function change(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "change", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Fires when the item is dragged.
         *
         * This method does not accept any arguments.
         */
        public function sort(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "sort", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Fires once the object has moved if the new location is invalid.
         *
         * This method does not accept any arguments.
         */
        public function revert(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "revert", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Only fires once when the item is done being moved at its final location.
         *
         * This method does not accept any arguments.
         */
        public function relocate(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "relocate", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Retrieves the value of a given property name.
         * This magic method handles dynamic property access, returning
         * corresponding property values based on the provided name.
         *
         * @param string $strName Name of the property to retrieve.
         *
         * @return mixed The value of the requested property, or throws an exception
         *               if the property does not exist or is inaccessible.
         *
         * @throws Caller If the property is not found in the current context
         *                and the parent::__get method fails.
         */
        public function __get(string $strName): mixed
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

        /**
         * Magic method to set the property value dynamically.
         * Updates the corresponding property and applies the appropriate changes
         * to the jQuery widget's configuration.
         *
         * @param string $strName Name of the property to update.
         * @param mixed $mixValue New value to assign to the specified property.
         *
         * @return void
         *
         * @throws Caller
         * @throws InvalidCast Thrown if the provided value type does not match the
         *                     expected type of the property being set.
         */
        public function __set(string $strName, mixed $mixValue): void
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
        public static function getModelConnectorParams(): array
        {
            return array_merge(parent::GetModelConnectorParams(), array());
        }
    }
