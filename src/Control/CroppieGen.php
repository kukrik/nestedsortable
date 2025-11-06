<?php

    namespace QCubed\Plugin\Control;


    use QCubed\Control\Panel;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\ApplicationBase;
    use QCubed\Project\Application;use QCubed\Type;

    /**
     * Class CroppieGen
     *
     * @see Croppie
     * @package QCubed\Plugin
     */

    /**
     * ## OPTIONS ##
     *
     * @property object $Boundary Default: will default to the size of the container. The outer container of the cropper.
     * @property string $CustomClass Default: ''. A class of you're choosing to add to the container to add custom styles to your croppie.
     * @property boolean $EnableExif Default: false. Enable exif orientation reading. Tells Croppie to read exif orientation
     *                                  from the image data and orient the image correctly before rendering to the page.
     * @property boolean $EnableOrientation Default: false. Enable or disable support for specifying a custom orientation
     *                                          when binding images (See bind method).
     * @property boolean $EnableResize Default: false. Enable or disable support for resizing the viewport area.
     * @property boolean $EnableZoom Default: true. Enable zooming functionality. If set to false - scrolling and pinching would not zoom.
     * @property boolean $EnforceBoundary Default: true, /*Experimental/. Restricts zoom so the image cannot be smaller than the viewport.
     * @property boolean $MouseWheelZoom Default: true. Enable or disable the ability to use the mouse wheel to zoom in and
     *                                      out on a croppie instance. If 'ctrl' is passed, the mouse wheel will only work while
     *                                       the control keyboard is pressed
     * @property boolean $ShowZoomer Default: true. Hide or Show the zoom slider.
     * @property object $Viewport Default: { width: 100, height: 100, type: 'square' }. The inner container of the coppie.
     *                              The visible part of the image. Valid type values: 'square' 'circle'.
     * @property string $Url Default: null. Image path.

     *
     * See also: http://foliotek.github.io/Croppie/
     *
     * @package QCubed\Plugin
     */

    class CroppieGen extends Panel
    {
        /** @var null|array */
        protected ?array $arrBoundary = null;
        /** @var null|string */
        protected ?string $strCustomClass = null;
        /** @var boolean */
        protected ?bool $blnEnableExif = null;
        /** @var boolean */
        protected ?bool $blnEnableOrientation = null;
        /** @var boolean */
        protected ?bool $blnEnableResize = null;
        /** @var boolean */
        protected ?bool $blnEnableZoom = null;
        /** @var boolean */
        protected ?bool $blnEnforceBoundary = null;
        /** @var boolean */
        protected ?bool $blnMouseWheelZoom = null;
        /** @var boolean */
        protected ?bool $blnShowZoomer = null;
        /** @var null|array */
        protected ?array $arrViewport = null;
        /** @var null|string */
        protected ?string $strUrl = null;

        /**
         * Generate and return an array of options for the jQuery plugin.
         *
         * The method compiles a set of key-value pairs representing configuration options
         * for the jQuery component, based on the defined properties of the current object.
         * It merges parent options and conditionally adds additional options if the
         * corresponding properties are not null.
         *
         * @return array An associative array of jQuery plugin configuration options.
         */
        protected function makeJqOptions(): array
        {
            $jqOptions = parent::MakeJqOptions();
            if (!is_null($val = $this->Boundary)) {$jqOptions['boundary'] = $val;}
            if (!is_null($val = $this->CustomClass)) {$jqOptions['customClass'] = $val;}
            if (!is_null($val = $this->EnableExif)) {$jqOptions['enableExif'] = $val;}
            if (!is_null($val = $this->EnableOrientation)) {$jqOptions['enableOrientation'] = $val;}
            if (!is_null($val = $this->EnableResize)) {$jqOptions['enableResize'] = $val;}
            if (!is_null($val = $this->EnableZoom)) {$jqOptions['enableZoom'] = $val;}
            if (!is_null($val = $this->EnforceBoundary)) {$jqOptions['enforceBoundary'] = $val;}
            if (!is_null($val = $this->MouseWheelZoom)) {$jqOptions['mouseWheelZoom'] = $val;}
            if (!is_null($val = $this->ShowZoomer)) {$jqOptions['showZoomer'] = $val;}
            if (!is_null($val = $this->Viewport)) {$jqOptions['viewport'] = $val;}
            if (!is_null($val = $this->Url)) {$jqOptions['url'] = $val;}
            return $jqOptions;
        }

        /**
         * Retrieve the jQuery setup function name for the component.
         *
         * @return string The name of the jQuery setup function.
         */
        public function getJqSetupFunction(): string
        {
            return 'croppie';
        }

        /**
         * Get the crop points, and the zoom of the image.
         *
         * * This method does not accept any arguments.
         */
        public function get(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "get", ApplicationBase::PRIORITY_LOW);
        }

        /**
         *  Bind an image to the croppie. Returns a promise
         *                           to be resolved when the image has been loaded and the croppie has been initialized.
         *                           Parameters
         *                               URL to image
         *                               points Array of points that translate into [topLeftX, topLeftY, bottomRightX, bottomRightY]
         *                               zoom Apply zoom after an image has been bound
         *                               orientation Custom orientation, applied after exif orientation (if enabled).
         *                               Only works with enableOrientation option enabled (see 'Options').
         *                           Valid options are:
         *                               1 unchanged
         *                               2 flipped horizontally
         *                               3 rotated 180 degrees
         *                               4 flipped vertically
         *                               5 flipped horizontally, then rotated left by 90 degrees
         *                               6 rotated clockwise by 90 degrees
         *                               7 flipped horizontally, then rotated right by 90 degrees
         *                               8 rotated counter-clockwise by 90 degrees
         *
         * @param $options
         *
         * * This method does not accept any arguments.
         */
        public function bind(array $options): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "bind", $options, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Destroy a croppie instance and remove it from the DOM
         *
         * * This method does not accept any arguments.
         */
        public function destroy(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", ApplicationBase::PRIORITY_LOW);
        }

        /**
         *  Get the resulting crop of the image.
         *                           To be resolved when the image has been loaded and the croppie has been initialized.
         *                           Parameters
         *                              'type' The type of result to return defaults to 'canvas'
         *                                  'base64' returns a cropped image encoded in base64
         *                                  'HTML' returns HTML of the image positioned within a div of hidden overflow
         *                                  'blob' returns a blob of the cropped image
         *                                  'rawcanvas' returns the canvas element allowing you to manipulate prior to getting the resulted image
         *                              'size' The size of the cropped image defaults to 'viewport'
         *                                  'viewport' the size of the resulting image will be the same width and height as the viewport
         *                                  'original' the size of the resulting image will be at the original scale of the image
         *                                  {width, height} an object defining the width and height. If only one dimension is specified, the other will be calculated using the viewport aspect ratio.
         *                              'format' Indicating the image format.
         *                                  Default:'png'
         *                                  Valid values:'jpeg'|'png'|'webp'
         *                              'quality' Number between 0 and 1 indicating image quality.
         *                                  Default:1
         *                              'circle' force the result to be cropped into a circle
         *                                  Valid Values:true | false
         *
         * @param $parameters
         *
         * * This method does not accept any arguments.
         */
        public function result(array $parameters): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "result", $parameters, ApplicationBase::PRIORITY_LOW);
        }
        /**
         *  Rotate the image by a specified degree amount. Only works with enableOrientation option enabled (see 'Options').
         *                              'degrees' Valid Values: 90, 180, 270, -90, -180, -270
         *
         * @param $degrees
         *
         * * This method does not accept any arguments.
         */

        public function rotate(int $degrees): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "rotate", $degrees, ApplicationBase::PRIORITY_LOW);
        }

        /**
         *  Set the zoom of a Croppie instance. The value passed in is still restricted to the min/max set by Croppie.
         * 'value' a floating point to scale the image within the croppie. Must be between a min and max value set by a croppie.
         *
         * @param $value
         *
         * * This method does not accept any arguments.
         */
        public function setZoom(float $value): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "setZoom", $value, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Magic method for getting the value of a property.
         *
         * This method retrieves the value of a specific property based on the provided name.
         * It supports multiple predefined properties and delegates the call to the parent
         * if the property name is not recognized.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property, retrieved too from this class
         *               or its parent class.
         * @throws Caller If the property is not defined in this class or the parent.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'Boundary': return $this->arrBoundary;
                case 'CustomClass': return $this->strCustomClass;
                case 'EnableExif': return $this->blnEnableExif;
                case 'EnableOrientation': return $this->blnEnableOrientation;
                case 'EnableResize': return $this->blnEnableResize;
                case 'EnableZoom': return $this->blnEnableZoom;
                case 'EnforceBoundary': return $this->blnEnforceBoundary;
                case 'MouseWheelZoom': return $this->blnMouseWheelZoom;
                case 'ShowZoomer': return $this->blnShowZoomer;
                case 'Viewport': return $this->arrViewport;
                case 'Url': return $this->strUrl;

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
         * Magic method to set property values dynamically for the class.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         *
         * @throws InvalidCast If the provided value does not match the expected type for the property.
         * @throws Caller If the property does not exist or cannot be set in the superclass.
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case 'Boundary':
                    try {
                        $this->arrBoundary = Type::cast($mixValue, Type::ARRAY_TYPE);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'boundary', $this->arrBoundary);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'CustomClass':
                    try {
                        $this->strCustomClass = Type::cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'customClass', $this->strCustomClass);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'EnableExif':
                    try {
                        $this->blnEnableExif = Type::cast($mixValue, Type::BOOLEAN);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enableExif', $this->blnEnableExif);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'EnableOrientation':
                    try {
                        $this->blnEnableOrientation = Type::cast($mixValue, Type::BOOLEAN);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enableOrientation', $this->blnEnableOrientation);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'EnableResize':
                    try {
                        $this->blnEnableResize = Type::cast($mixValue, Type::BOOLEAN);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enableResize', $this->blnEnableResize);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'EnableZoom':
                    try {
                        $this->blnEnableZoom = Type::cast($mixValue, Type::BOOLEAN);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enableZoom', $this->blnEnableZoom);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'EnforceBoundary':
                    try {
                        $this->blnEnforceBoundary = Type::cast($mixValue, Type::BOOLEAN);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'enforceBoundary', $this->blnEnforceBoundary);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'MouseWheelZoom':
                    try {
                        $this->blnMouseWheelZoom = Type::cast($mixValue, Type::BOOLEAN);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'mouseWheelZoom', $this->blnMouseWheelZoom);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'ShowZoomer':
                    try {
                        $this->blnShowZoomer = Type::cast($mixValue, Type::BOOLEAN);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'showZoomer', $this->blnShowZoomer);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Viewport':
                    try {
                        $this->arrViewport = Type::cast($mixValue, Type::ARRAY_TYPE);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'viewport', $this->arrViewport);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Url':
                    try {
                        $this->strUrl = Type::cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'url', $this->strUrl);
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
         * Retrieve the parameters for the model connector.
         *
         * This method returns an array of parameters by merging the parent model connector parameters with additional parameters, if any.
         *
         * @return array The combined array of model connector parameters.
         */
        public static function getModelConnectorParams(): array
        {
            return array_merge(parent::GetModelConnectorParams(), array());
        }
    }


