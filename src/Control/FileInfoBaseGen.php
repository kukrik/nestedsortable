<?php

    namespace QCubed\Plugin\Control;

    use QCubed as Q;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Type;

    /**
     * Class FileInfoBaseGen
     * @see FileInfo
     * @package QCubed\Plugin
     */

    /**
     * @property string $RootPath
     * @property string $RootUrl
     * @property string $TempPath
     * @property string $TempUrl
     *
     * @package QCubed\Plugin
     */

    class FileInfoBaseGen extends Q\Control\Panel
    {
        protected ?string $strRootPath = null;
        protected ?string $strRootUrl = null;
        protected ?string $strTempPath = null;
        protected ?string $strTempUrl = null;
        protected ?string $intFileId = null;
        protected ?string $strFilePath = null;
        protected ?string $strFileExtension = null;

        /**
         * Prepares and returns an array of jQuery options by extracting and adding specific property values
         * if they are not null. This method extends the options from the parent implementation.
         *
         * @return array The array of jQuery options with additional configuration values set from the class properties.
         */
        protected function makeJqOptions(): array
        {
            $jqOptions = parent::MakeJqOptions();
            //if (!is_null($val = $this->Language)) {$jqOptions['language'] = $val;}
            if (!is_null($val = $this->RootPath)) {$jqOptions['rootPath'] = $val;}
            if (!is_null($val = $this->RootUrl)) {$jqOptions['rootUrl'] = $val;}
            if (!is_null($val = $this->TempPath)) {$jqOptions['tempPath'] = $val;}
            if (!is_null($val = $this->TempUrl)) {$jqOptions['tempUrl'] = $val;}
            return $jqOptions;
        }

        /**
         * Retrieves and returns the name of the jQuery setup function to be used for configuration or initialization.
         *
         * @return string The name of the jQuery setup function.
         */
        protected function getJqSetupFunction(): string
        {
            return 'fileInfo';
        }

        /**
         * Retrieves the value of a specified property. If the property name matches one of the predefined
         * cases, the corresponding class property value is returned. If not, the request is passed to the
         * parent implementation. Throws an exception if the property does not exist.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the specified property, or the result from the parent method for undefined properties.
         * @throws Caller If the property does not exist or cannot be accessed.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'RootPath': return $this->strRootPath;
                case 'RootUrl': return $this->strRootUrl;
                case 'TempPath': return $this->strTempPath;
                case 'TempUrl': return $this->strTempUrl;

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
         * Sets the value of a property dynamically by name and performs additional actions based on the property.
         * Updates respective attributes and executes related scripts when the property value is assigned. For unhandled properties,
         * attempts to delegate to the parent implementation.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property, which will be validated and cast as required.
         *
         * @return void
         *
         * @throws InvalidCast If the provided value cannot be properly cast to the expected type.
         * @throws Caller If the property name is not handled by the parent class or the current implementation.
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case 'RootPath':
                    try {
                        $this->strRootPath = Type::Cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rootPath', $this->strRootPath);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case 'RootUrl':
                    try {
                        $this->strRootUrl = Type::Cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rootUrl', $this->strRootUrl);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case 'TempPath':
                    try {
                        $this->strTempPath = Type::Cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tempPath', $this->strTempPath);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case 'TempUrl':
                    try {
                        $this->strTempUrl = Type::Cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tempUrl', $this->strTempUrl);
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
    }


