<?php

    /** This file contains the SidebarList Class */

    namespace QCubed\Plugin\Control;

    use QCubed as Q;
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
     * Class SidebarList
     *
     * Represents a sidebar list control, capable of rendering hierarchical menu structures.
     * It supports dynamic population and customization via callbacks and data binding.
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
     * @property string $TagName
     * @property string $TagStyle
     * @property mixed $DataSource
     *
     * @package QCubed\Plugin
     */
    class SidebarList extends ControlBase
    {
        use Q\Control\DataBinderTrait;

        /** @var null|string TagName */
        protected ?string $strTagName = null;
        /** @var null|string TagStyle */
        protected ?string $strTagClass = null;
        /** @var  callable */
        protected mixed $nodeParamsCallback = null;
        /** @var array DataSource, from which the items are picked and rendered */
        protected array $objDataSource;

        protected array $strParams = [];

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
         * Constructor method to initialize the control with a parent object and an optional control ID.
         * It invokes the parent constructor and handles any exceptions, ensuring proper file registration.
         *
         * @param ControlBase|FormBase $objParentObject The parent object to which this control belongs.
         * @param string|null $strControlId An optional string identifier for the control.
         *
         * @return void
         * @throws \Exception
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
         * Registers necessary CSS and JavaScript files.
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
         * Validates the given input data.
         *
         * @return bool Always returns true, indicating the validation is successful.
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * Parses the incoming POST data and processes it according to the application's requirements.
         *
         * @return void
         */
        public function parsePostData(): void
        {}

        /**
         * Sets the callback function for node parameters.
         *
         * @param callable $callback The callback function to assign for node parameters.
         * @return void
         */
        public function createNodeParams(callable $callback): void
        {
            $this->nodeParamsCallback = $callback;
        }

        /**
         * Retrieves raw item data based on the provided object item.
         *
         * @param mixed $objItem Object item to be processed.
         * @return array An associative array of item raw data including keys:
         *               'id', 'parent_id', 'depth', 'left', 'right', 'menu_text',
         *               'status', 'redirect_url', 'homely_url', 'target_type'.
         * @throws Exception If nodeParamsCallback is not provided.
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
         * Prepares the object for serialization by updating the nodeParamsCallback
         * with the serialized version returned by the sleepHelper method.
         *
         * @return array
         */
        public function sleep(): array
        {
            $this->nodeParamsCallback = ControlBase::sleepHelper($this->nodeParamsCallback);
            return parent::sleep();
        }

        /**
         * Restores the object state after deserialization. It updates the
         * nodeParamsCallback using the wakeupHelper method with the provided form object.
         *
         * @param FormBase $objForm The form object used to restore the state.
         * @return void
         */
        public function wakeup(FormBase $objForm): void
        {
            parent::wakeup($objForm);
            $this->nodeParamsCallback = ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
        }

        /**
         * Renders the menu tree in HTML format based on the given parameters array.
         *
         * @param array $arrParams An array of associative arrays containing menu item parameters.
         *                         Each associative array should include 'id', 'parent_id', 'depth',
         *                         'left', 'right', 'menu_text', 'status', 'redirect_url', 'homely_url',
         *                         and 'target_type' keys.
         *
         * @return string A string representing the HTML of the rendered menu tree.
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

                $target = '';
                if (!empty($this->strTargetType)) {
                    $target = ' target="' . $this->strTargetType . '"';
                }

                // We determine the correct link
                $link = ($this->intHomelyUrl === 1) ? $this->strRedirectUrl : $this->strExternalUrl;

                if (($this->intStatus !== 2 && $this->intStatus !== 3) && $this->intDepth == 0) {
                    $strHtml .= _nl() . '<li id="' . $this->ControlId . '_' . $this->intId . '">';
                    $strHtml .= '<a href="' . $link . '"' . $target . '>';
                    $strHtml .= $this->strMenuText;

                    if ($this->Right !== $this->Left + 1) {
                        $strHtml .= '<span class="caret"></span>';
                    }

                    $strHtml .= '</a>';
                    $strHtml .= '</li>';
                }
            }
            return $strHtml;
        }

        /**
         * Generates and returns the HTML for the control. It binds data, processes the data source,
         * and constructs the HTML by rendering the menu tree and wrapping it in the appropriate tag.
         *
         * @return string The resulting HTML of the control.
         * @throws Caller
         * @throws Exception
         */
        protected function getControlHtml(): string
        {
            $this->dataBind();

            if (empty($this->objDataSource)) {
                $this->objDataSource = [];
            }

            if ($this->objDataSource) {
                foreach ($this->objDataSource as $objObject) {
                    $this->strParams[] = $this->getItemRaw($objObject);
                }
            }

            if ($this->strTagClass) {
                $attributes['class'] = $this->strTagClass;
            } else {
                $attributes = [];
            }
            $strOut = $this->renderMenuTree($this->strParams);
            $strHtml = $this->renderTag($this->strTagName, $attributes, null, $strOut);

            $this->objDataSource = [];
            return $strHtml;
        }

        /**
         * Binds data to the object by calling the data binder method if the object
         * is not already rendered, there is no data source already present, and
         * a data binder is defined. If an exception occurs during the binding process,
         * the exception offset is incremented before being thrown.
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
         * Generated method overrides the built-in Control method, causing it to not redraw completely. We restore
         * its functionality here.
         */
        public function refresh(): void
        {
            parent::refresh();
            ControlBase::refresh();
        }

        /**
         * Sets up a jQuery widget with specified behaviors for the sidebar menu.
         *
         * Attaches click event handlers to the list items and anchors within the sidebar menu,
         * triggering custom 'sidebarselect' events and managing the 'active' state for clicked elements.
         *
         * This method also initializes the "Home" link as active by default.
         *
         * @return void
         * @throws Caller
         */
        public function makeJqWidget(): void
        {
            /**
             * To draw or test the menu, the JS code is temporarily placed here at the end: "return false."
             * This part of the code usually needs to be changed to "return true"; for the links to work properly.
             */
            Application::executeControlCommand($this->ControlId, 'on', 'click', 'li',
                new Closure("jQuery(this).trigger('sidebarselect', this.id); return false;"),
                ApplicationBase::PRIORITY_HIGH);

            /**
             * For production, it is recommended to start activating the "Home" link.
             * The following is intended to introduce such an opportunity.
             */
            Application::executeJavaScript("jQuery('.sidemenu #{$this->ControlId}_1').find('a').addClass('active')");

            Application::executeSelectorFunction(".sidemenu", "on", "click", "a",
                new Closure("jQuery('a.active').removeClass('active'); jQuery(this).addClass('active');"),
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
         * @throws \Exception
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
                case "TagName": return $this->strTagName;
                case "TagClass": return $this->strTagClass;
                case "DataSource": return $this->objDataSource;

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
         * @param mixed $mixValue The value to be assigned to the property. The type of the value depends on the
         *     property.
         *
         * @return void
         *
         * @throws InvalidCast If the provided value cannot be cast to the expected type for the property.
         * @throws Caller If the property name is unknown, and the parent handler does not recognize it.
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case "Id":
                    try {
                        //$this->blnModified = true;
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
                case "TagName":
                    try {
                        $this->blnModified = true;
                        $this->strTagName = Type::cast($mixValue, Type::STRING);
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                case "TagClass":
                    try {
                        $this->blnModified = true;
                        $this->strTagClass = Type::cast($mixValue, Type::STRING);
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