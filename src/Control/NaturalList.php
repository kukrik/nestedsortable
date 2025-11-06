<?php

    /** This file contains the MenuPanel Class */

    namespace QCubed\Plugin\Control;

    use QCubed as Q;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\DataBind;
    use QCubed\Exception\InvalidCast;
    use Exception;
    use QCubed\Control\FormBase;
    use QCubed\Control\ControlBase;
    use QCubed\Type;

    /**
     * Represents a hierarchical menu structure implemented in the context
     * of a control. This class provides functionality for rendering nested
     * menu trees with support for custom data sources and node parameter
     * callbacks.
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

        /** @var null|string TagName */
        protected ?string $strTagName = null;
        /** @var  callable */
        protected mixed $nodeParamsCallback = null;
        /** @var array DataSource, from which the items are picked and rendered */
        protected array $objDataSource;

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

        /**
         * Initializes the control by calling the parent constructor with the provided parent object
         * and optional control ID. Registers the necessary files for the control.
         * If an exception occurs, it increments its offset and rethrows it.
         *
         * @param ControlBase|FormBase $objParentObject The parent object to which this control belongs.
         * @param string|null $strControlId An optional ID for the control.
         *
         * @return void
         * @throws \Exception
         * @throws Caller
         */
        public function __construct(ControlBase|FormBase $objParentObject,?string $strControlId = null)
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
         * @throws Caller
         */
        protected function registerFiles(): void
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
        public function validate(): bool
        {
            return true;
        }

        /**
         * Parses and processes post-data received from a request.
         *
         * @return void
         */
        public function parsePostData(): void
        {}

        /**
         * Registers a callback to create node parameters.
         *
         * @param callable $callback A function to create node parameters.
         * @return void
         */
        public function createNodeParams(callable $callback): void
        {
            $this->nodeParamsCallback = $callback;
        }

        /**
         * Retrieves raw item data based on the provided callback.
         *
         * @param mixed $objItem The item to retrieve data for.
         * @return array The raw item data.
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

            return [
                'id' => $intId,
                'parent_id' => $intParentId,
                'depth' => $intDepth,
                'left' => $intLeft,
                'right' => $intRight,
                'menu_text' => $strMenuText,
                'status' => $intStatus
            ];
        }

        /**
         * Sets the node parameters callback to a state suitable for serialization
         * and then calls the parent's sleep method.
         *
         * @return array
         */
        public function sleep(): array
        {
            $this->nodeParamsCallback = ControlBase::sleepHelper($this->nodeParamsCallback);
            return parent::sleep();
        }

        /**
         * Restores the node parameters callback from its serialized state and then
         * calls the parent's wakeup method with the given FormBase object.
         *
         * @param FormBase $objForm The form object that is passed to the parent's wakeup method.
         * @return void
         */
        public function wakeup(FormBase $objForm): void
        {
            parent::wakeup($objForm);
            $this->nodeParamsCallback = ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
        }

        /**
         * Renders the menu tree as a string representation of nested HTML list elements
         * based on the provided parameters. Handles hierarchical structure, depth changes,
         * and ignores nodes with specific status values.
         *
         * @param array $arrParams An array of associative arrays, where each array contains
         *                         the menu node data, including keys: 'id', 'parent_id',
         *                         'depth', 'menu_text', and 'status'.
         *
         * @return string The generated HTML string representing the menu tree.
         */
        protected function renderMenuTree(array $arrParams): string
        {
            $strHtml = '';
            $this->intCurrentDepth = 0; // Initial depth

            for ($i = 0; $i < count($arrParams); $i++) {
                // We are loading the data of the currently active node
                $this->intId = $arrParams[$i]['id'];
                $this->intParentId = (int)$arrParams[$i]['parent_id'];
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
         * @throws Caller
         * @throws Exception
         */
        protected function getControlHtml(): string
        {
            $this->dataBind();

            if (empty($this->objDataSource)) {
                $this->objDataSource = [];
            }

            $strParams = [];

            if ($this->objDataSource) {
                foreach ($this->objDataSource as $objObject) {
                    $strParams[] = $this->getItemRaw($objObject);
                }
            }

            $strOut = $this->renderMenuTree($strParams);
            $strHtml = $this->renderTag($this->strTagName, null, null, $strOut);

            $this->objDataSource = [];

            return $strHtml;
        }

        /**
         * Executes the data binder if applicable, and the data source is not set,
         * and the control has not been rendered yet. Any exception caught during
         * the execution will have its offset incremented and rethrown.
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
         * Creates a jQuery widget using the provided parameters.
         *
         * @return void
         */
        public function makeJqWidget(): void
        {}

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
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
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
                case "TagName":
                    try {
                        $this->blnModified = true;
                        $this->strTagName = Type::cast($mixValue, Type::STRING);
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