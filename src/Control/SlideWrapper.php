<?php

    namespace QCubed\Plugin\Control;

    use QCubed as Q;
    use QCubed\Control\FormBase;
    use QCubed\Control\ControlBase;
    use QCubed\Exception\Caller;
    use Exception;
    use QCubed\Exception\DataBind;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Jqui\Sortable;
    use QCubed\Type;

    /**
     * Class SlideWrapper
     *
     * @property string $TempUrl Default temp path APP_UPLOADS_TEMP_URL. If necessary, the temp dir must be specified.
     * @property string $RootUrl Default root path APP_UPLOADS_URL If necessary, the temp dir must be specified.
     * @property string $DateTimeFormat Default 'DD.MM.YYYY hhhh:mm'. If desired, the date format can be changed to suit the user.
     * @property string $EmptyImagePath Fault null. If necessary, depending on your project, add an empty image path if the desired image is missing.
     * @property mixed $DataSource
     *
     * @package QCubed\Plugin
     */

    class SlideWrapper extends Sortable
    {
        use Q\Control\DataBinderTrait;

        /** @var string */
        protected string $strRootPath = APP_UPLOADS_DIR;
        /** @var string */
        protected string $strRootUrl = APP_UPLOADS_URL;
        /** @var string */
        protected string $strTempPath = APP_UPLOADS_TEMP_DIR;
        /** @var string */
        protected string $strTempUrl = APP_UPLOADS_TEMP_URL;
        /** @var string */
        protected string $strDateTimeFormat = 'DD.MM.YYYY hhhh:mm';
        /** @var string */
        protected string $strEmptyImagePath;
        /** @var array DataSource, from which the items are picked and rendered */
        protected array $objDataSource;
        /** @var  callable */
        protected mixed $nodeParamsCallback = null;
        /** @var  callable */
        protected mixed $cellParamsCallback = null;

        protected mixed $strRenderCellHtml;

        /**
         * Assigns a callback function to generate node parameters for later use.
         *
         * @param callable $callback A callback function to be used for processing and generating node parameters.
         *
         * @return void
         */
        public function createNodeParams(callable $callback): void
        {
            $this->nodeParamsCallback = $callback;
        }

        /**
         * Assigns a callback function for defining the parameters needed to render buttons in the grid.
         *
         * @param callable $callback The callback function that returns button configuration parameters.
         *
         * @return void
         */
        public function createRenderButtons(callable $callback): void
        {
            $this->cellParamsCallback = $callback;
        }

        /**
         * Uses HTML callback to get each loop in the original array. Relies on the NodeParamsCallback
         * to return information on how to draw each node.
         *
         * @param mixed $objItem
         *
         * @return array|string
         * @throws Exception
         */
        public function getItem(mixed $objItem): array|string
        {
            if (!$this->nodeParamsCallback) {
                throw new Exception("Must provide a nodeParamsCallback");
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

            return [
                'id' => $intId,
                'group_id' => $intGroupId,
                'order' => $intOrder,
                'title' => $strTitle,
                'url' => $strUrl,
                'path' => $strPath,
                'extension' => $strExtension,
                'dimensions' => $strDimensions,
                'width' => $intWidth,
                'top' => $intTop,
                'status' => $intStatus,
                'post_date' => $calPostDate,
                'post_update_date' => $calPostUpdateDate
            ];
        }

        /**
         * Retrieves an object using the provided callback to process the input item.
         * The callback should return the desired object or value based on the input.
         *
         * @param mixed $objItem The input item to be processed by the callback function.
         *
         * @return mixed The result of the callback function processing the input item.
         * @throws Exception If the cellParamsCallback is not defined.
         */
        public function getObject(mixed $objItem): mixed
        {
            if (!$this->cellParamsCallback) {
                throw new Exception("Must provide a cellParamsCallback");
            }
            return call_user_func($this->cellParamsCallback, $objItem);
        }

        /**
         * Prepares the object for serialization by adjusting callback properties using the sleepHelper method.
         * Ensures that callback properties are properly handled during the sleep process to maintain data integrity.
         *
         * @return array Properties to be serialized
         */
        public function sleep(): array
        {
            $this->nodeParamsCallback = ControlBase::sleepHelper($this->nodeParamsCallback);
            $this->cellParamsCallback = ControlBase::sleepHelper($this->cellParamsCallback);
            return parent::sleep();
        }

        /**
         * Wake-up method to properly restore the state of the object and its properties during deserialization.
         *
         * @param FormBase $objForm The form object being referenced.
         *
         * @return void
         */
        public function wakeup(FormBase $objForm): void
        {
            parent::wakeup($objForm);
            $this->nodeParamsCallback = ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
            $this->cellParamsCallback = ControlBase::wakeupHelper($objForm, $this->cellParamsCallback);
        }

        /**
         * Returns the HTML for the control.
         *
         * @return string
         * @throws Caller
         * @throws Exception
         */
        protected function getControlHtml(): string
        {
            $this->dataBind();
            $strParams = [];
            $strObjects = [];

            if ($this->objDataSource) {
                foreach ($this->objDataSource as $objObject) {
                    $strParams[] = $this->getItem($objObject);
                    if ($this->cellParamsCallback) {
                        $strObjects[] = $this->getObject($objObject);
                    }
                }
            }

            $strHtml = $this->renderTag('div', null, null, $this->renderSlide($strParams, $strObjects));

            $this->objDataSource = [];

            return $strHtml;
        }

        /**
         * Binds the data source to the UI component.
         * If the data source is not set and a data binder is available, it calls the data binder method.
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
         * Renders a slide consisting of HTML elements for a given set of parameters and objects.
         * Processes the provided parameters and objects to generate the HTML structure for slide rendering.
         *
         * @param array $arrParams Array of parameters, each of which should include keys such as 'id', 'path', 'extension',
         *                         'status', 'title', 'post_date', and optionally 'post_update_date'.
         * @param array $arrObjects Array of objects corresponding to the parameters, used for rendering specific content
         *                          within each slide.
         *
         * @return string Returns the generated HTML for the slide as a string.
         */
        protected function renderSlide(array $arrParams, array $arrObjects): string
        {
            $strHtml = '';

            for ($i = 0; $i < count($arrParams); $i++) {
                $intId = $arrParams[$i]['id'];
                $strPath = $arrParams[$i]['path'];
                $strExtension = $arrParams[$i]['extension'];
                $intStatus = $arrParams[$i]['status'];
                $strTitle = $arrParams[$i]['title'];
                $calPostDate = $arrParams[$i]['post_date'];
                $calPostUpdateDate = $arrParams[$i]['post_update_date'];

                if ($this->cellParamsCallback) {
                    $this->strRenderCellHtml = $arrObjects[$i];
                }

                if ($intStatus !== 2) {
                    $strHtml .= _nl('<div id ="' . $this->strControlId . '_' . $intId . '" data-value="' . $intId . '" class="image-blocks">');
                } else {
                    $strHtml .= _nl('<div id ="' . $this->strControlId . '_' . $intId . '" data-value="' . $intId . '" class="image-blocks inactivated">');
                }

                $strHtml .= _nl(_indent('<div class="preview">', 1));

                if (!$strPath) {
                    $strHtml .= _nl(_indent('<img src="' . $this->EmptyImagePath . '">', 2));
                } else if ($strExtension !== "svg") {
                    $strHtml .= _nl(_indent('<img src="' . $this->TempUrl . $strPath . '">', 2));
                } else {
                    $strHtml .= _nl(_indent('<img src="' . $this->RootUrl . $strPath . '">', 2));
                }

                $strHtml .= _nl(_indent('</div>', 1));
                $strHtml .= _nl(_indent('<div class="events" style="display: inline-block; width: 15%;">', 1));
                $strHtml .= _nl(_indent('<span class="icon-set reorder"><i class="fa fa-bars"></i></span>', 2));

                if ($this->cellParamsCallback) {
                    $strHtml .= _nl(_indent($this->strRenderCellHtml, 2));
                }

                $strHtml .= _nl(_indent('</div>', 1));

                $strHtml .= _nl(_indent('<div class="image-info" style="display: inline-block; width: 70%;">', 1));
                $strHtml .= _nl(_indent('<span style="display: inline-block; width: 50%;">' . $strTitle . '</span>', 2));
                $strHtml .= _nl(_indent('<span style="display: inline-block; width: 24%;">' . $calPostDate->qFormat($this->strDateTimeFormat) . '</span>', 2));

                if (!empty($calPostUpdateDate)) {
                    $strHtml .= _nl(_indent('<span style="display: inline-block; width: 24%;">' . $calPostUpdateDate->qFormat($this->strDateTimeFormat) . '</span>', 2));
                }

                $strHtml .= _nl(_indent('</div>', 1));

                $strHtml .= _nl('</div>');
            }

            return $strHtml;
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
                case 'RootPath': return $this->strRootPath;
                case 'RootUrl': return $this->strRootUrl;
                case 'TempPath': return $this->strTempPath;
                case 'TempUrl': return $this->strTempUrl;
                case "EmptyImagePath": return $this->strEmptyImagePath;
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
                case "RootPath":
                    try {
                        $this->strRootPath = Type::cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                case "RootUrl":
                    try {
                        $this->strRootUrl = Type::cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                case "TempPath":
                    try {
                        $this->strTempPath = Type::cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                case "TempUrl":
                    try {
                        $this->strTempUrl = Type::cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                case "DateTimeFormat":
                    try {
                        $this->strDateTimeFormat = Type::cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                case "EmptyImagePath":
                    try {
                        $this->strEmptyImagePath = Type::cast($mixValue, Type::STRING);
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