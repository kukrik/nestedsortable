<?php

    /** This file contains the Sidebar Class */

    namespace QCubed\Plugin\Control;

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Control\FormBase;
    use QCubed\Control\ControlBase;
    use QCubed\ApplicationBase;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Exception\DataBind;
    use Exception;
    use QCubed\Project\Application;
    use QCubed\Js\Closure;
    use QCubed\Type;

    /**
     * Class Sidebar
     *
     * Represents a Sidebar control that extends the functionality of a QCubed Panel.
     * The Sidebar control is designed to handle hierarchical data, generate menus,
     * and render HTML elements dynamically.
     *
     * The class utilizes a data source and assigned items to generate a structured menu.
     * It includes various helper methods for data binding, node parameter handling,
     * and rendering functionality. Developers can pass callbacks for node parameters
     * and configure behavior through the properties defined in the class.
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
    class Sidebar extends Panel
    {
        use Q\Control\DataBinderTrait;

        /** @var null|string SubTagName */
        protected ?string $strSubTagName = null;
        /** @var  callable */
        protected mixed $nodeParamsCallback = null;
        /** @var array DataSource, from which the items are picked and rendered */
        protected array $objDataSource;
        /** @var array AssignedItems, from which the items are predefined */
        protected array $objAssignedItems = [];

        protected int $intCurrentDepth = 0;
        protected int $intCounter = 0;

        /** @var  null|integer Id */
        protected ?int $intId = null;
        /** @var  null|integer ParentId */
        protected ?int $intParentId = null;
        /** @var  null|integer Depth */
        protected ?int $intDepth = null;
        /** @var  null|integer Left */
        protected ?int $intLeft = null;
        /** @var  null|integer Right */
        protected ?int $intRight = null;
        /** @var  string MenuText */
        protected string $strMenuText;
        /** @var  integer Status */
        protected int $intStatus;
        /** @var string RedirectUrl */
        protected string $strRedirectUrl;
        /** @var int HomelyUrl */
        protected int $intHomelyUrl;
        /** @var string InternalUrl */
        protected string $strExternalUrl;
        /** @var int TargetType */
        protected int $strTargetType;

        /**
         * Constructs a new instance of the object, initializing it with the given parent object and optional control
         * ID.
         *
         * @param ControlBase|FormBase $objParentObject The parent object that this control belongs to, either a
         *     ControlBase or FormBase instance.
         * @param string|null $strControlId Optional unique ID for the control. If not provided, a default ID may be
         *     generated.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
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
         * @throws Caller
         */
        protected function registerFiles(): void
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
        public function validate(): bool
        {
            return true;
        }

        /**
         * Parses the data received from a POST request.
         *
         * @return void
         */
        public function parsePostData(): void
        {}

        /**
         * Sets the callback function that will create node parameters.
         *
         * @param callable $callback The function to be used for creating node parameters.
         * @return void
         */
        public function createNodeParams(callable $callback): void
        {
            $this->nodeParamsCallback = $callback;
        }

        /**
         * Retrieves raw item data based on the provided object item.
         *
         * @param mixed $objItem The item object to extract raw data from.
         * @return array An associative array containing raw item parameters such as 'id', 'parent_id', 'depth', 'left', 'right', 'menu_text', 'status', 'redirect_url', 'homely_url', and 'target_type'.
         * @throws Exception If the nodeParamsCallback is not provided.
         */
        public function getItemRaw(mixed $objItem): array
        {
            if (!$this->nodeParamsCallback) {
                throw new Exception("Must provide a nodeParamsCallback");
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

            return [
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
        }

        /**
         * Generate HTML for the control based on the data source and assigned items.
         *
         * @return string The generated HTML or null if the data source or assigned items are not set.
         * @throws Caller
         * @throws Exception
         */
        protected function getControlHtml(): string
        {
            $this->dataBind();

            if (empty($this->objDataSource)) {
                $this->objDataSource = [];
            }

            if (empty($this->objAssignedItems)) {
                $this->objAssignedItems = [];
            }

            $strParams = [];

            if ($this->objDataSource && $this->objAssignedItems) {
                foreach ($this->objDataSource as $objObject) {
                    if (in_array($objObject->Id, $this->objAssignedItems))
                        $strParams[] = $this->getItemRaw($objObject);
                }
            }

            $strHtml = $this->renderMenuTree($strParams);

            $this->objDataSource = [];
            $this->objAssignedItems = [];

            return $strHtml;
        }

        /**
         * Binds data to the component by running the DataBinder if the data source is not set,
         * the component has a DataBinder, and the component has not yet been rendered.
         *
         * @return void
         * @throws Caller
         * @throws DataBind
         */
        public function dataBind(): void
        {
            // Run the DataBinder (if applicable)
            if ($this->hasDataBinder() && !$this->blnRendered) {
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
         * @return array
         */
        public function sleep(): array
        {
            $this->nodeParamsCallback = ControlBase::sleepHelper($this->nodeParamsCallback);
            return parent::sleep();
        }

        /**
         * Wakes up the current process by handling node parameters callback through wakeupHelper.
         *
         * @param FormBase $objForm The form object to be used during the wakeup process.
         * @return void
         */
        public function wakeup(FormBase $objForm): void
        {
            parent::wakeup($objForm);
            $this->nodeParamsCallback = ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
        }

        /**
         * Renders a nested menu tree based on the given parameters.
         *
         * @param array $arrParams An array of menu node parameters, where each node contains
         *                         details like id, parent_id, depth, left, right, menu_text,
         *                         status, redirect_url, homely_url, external_url, and target_type.
         *
         * @return string The generated HTML string representing the menu tree.
         */
        protected function renderMenuTree(array $arrParams): string
        {
            $strHtml = '';

            for ($i = 0; $i < count($arrParams); $i++)
            {
                $this->intId = $arrParams[$i]['id'];
                $this->intParentId = (int)$arrParams[$i]['parent_id'];
                $this->intDepth = $arrParams[$i]['depth'];
                $this->intLeft = $arrParams[$i]['left'];
                $this->intRight = $arrParams[$i]['right'];
                $this->strMenuText = $arrParams[$i]['menu_text'];
                $this->intStatus = $arrParams[$i]['status'];
                $this->strRedirectUrl = $arrParams[$i]['redirect_url'];
                $this->intHomelyUrl = (int)$arrParams[$i]['homely_url'];
                $this->strExternalUrl = $arrParams[$i]['external_url'];
                $this->strTargetType = (int)$arrParams[$i]['target_type'];

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
                    $strHtml .= '<' . $this->strSubTagName . '>';
                    $this->intCurrentDepth = $this->intCurrentDepth + ($this->intDepth - $this->intCurrentDepth);
                } elseif ($this->intDepth < $this->intCurrentDepth) {
                    $strHtml .= str_repeat('</li>' . '</' . $this->strSubTagName . '>', $this->intCurrentDepth - $this->intDepth) . '</li>';
                    $this->intCurrentDepth = $this->intCurrentDepth - ($this->intCurrentDepth - $this->intDepth);
                }

                $strHtml .= '<li id="' . $this->ControlId . '_' . $this->intId . '">';
                $strHtml .= '<a href="' . $link . '"' . $target . '>';
                $strHtml .= $this->strMenuText;

                $strHtml .= '</a>';
                ++$this->intCounter;
            }

            if ($this->intCounter > 0) $strHtml .= '</li>';

            return $strHtml;
        }

        /**
         * Retrieves a list of child element IDs from the provided array of objects based on the specified parent ID.
         *
         * @param array $objArrays An array of objects, each expected to have 'ParentId' and 'Id' properties.
         * @param null|mixed $value The parent ID used to filter child elements. Default is null.
         *
         * @return array An array containing IDs of all child elements.
         */
        public function getChildren(array $objArrays, mixed $value = null): array
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
        public function refresh(): void
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
        public function makeJqWidget(): void
        {
            /**
             * To draw or test the menu, the JS code is temporarily placed here at the end: "return false."
             * This part of the code usually needs to be changed to "return true"; for the links to work properly.
             */

            Application::executeSelectorFunction(".submenu", "on", "click", "a",
                new Closure("jQuery('a.active').removeClass('active'); jQuery(this).addClass('active');
            return false;"),
                ApplicationBase::PRIORITY_HIGH);
        }

        /**
         * Magic method to retrieve the value of a requested property.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property, or the result of the parent's __get method if the property
         *               is not found in this class. Throws an exception if the property is invalid or inaccessible.
         * @throws Caller
         */
        public function __get(string $strName): mixed
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

        /**
         * Sets the value of a property dynamically by the provided property name.
         * This method handles a variety of predefined properties and performs type casting
         * and validation where necessary. If the property is not recognized, it delegates
         * the request to the parent implementation.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to be assigned to the property. The type of the value depends on the property.
         *
         * @return void
         *
         * @throws InvalidCast If the provided value cannot be cast to the expected type for the property.
         * @throws Caller If the property name is unknown, and the parent handler does not recognize it.
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case "Id":
                    try {
                        $this->blnModified = true;
                        $this->intId = Type::cast($mixValue, Type::INTEGER);
                        $this->blnModified = true;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "ParentId":
                    try {
                        $this->blnModified = true;
                        $this->intParentId = Type::cast($mixValue, Type::INTEGER);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "Depth":
                    try {
                        $this->blnModified = true;
                        $this->intDepth = Type::cast($mixValue, Type::INTEGER);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "Left":
                    try {
                        $this->blnModified = true;
                        $this->intLeft = Type::cast($mixValue, Type::INTEGER);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "Right":
                    try {
                        $this->blnModified = true;
                        $this->intRight = Type::cast($mixValue, Type::INTEGER);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "MenuText":
                    try {
                        $this->blnModified = true;
                        $this->strMenuText = Type::cast($mixValue, Type::STRING);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "Status":
                    try {
                        $this->blnModified = true;
                        $this->intStatus = Type::cast($mixValue, Type::INTEGER);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "RedirectUrl":
                    try {
                        $this->blnModified = true;
                        $this->strRedirectUrl = Type::cast($mixValue, Type::STRING);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "HomelyUrlUrl":
                    try {
                        $this->blnModified = true;
                        $this->intHomelyUrl = Type::cast($mixValue, Type::INTEGER);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "ExternalUrl":
                    try {
                        $this->blnModified = true;
                        $this->strExternalUrl = Type::cast($mixValue, Type::STRING);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "TargetType":
                    try {
                        $this->blnModified = true;
                        $this->strTargetType = Type::cast($mixValue, Type::STRING);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "SubTagName":
                    try {
                        $this->blnModified = true;
                        $this->strSubTagName = Type::cast($mixValue, Type::STRING);
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
                        $this->objAssignedItems = Type::cast($mixValue, Type::ARRAY_TYPE);
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