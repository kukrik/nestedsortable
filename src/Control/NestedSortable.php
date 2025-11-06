<?php

    namespace QCubed\Plugin\Control;

    use QCubed as Q;
    use QCubed\ApplicationBase;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\DataBind;
    use QCubed\Exception\InvalidCast;
    use Exception;
    use QCubed\Control\FormBase;
    use QCubed\Control\ControlBase;
    use QCubed\Project\Application;
    use QCubed\Type;
    use QCubed\Js;
    use QCubed\Html;

    /**
     * Class NestedSortableBase
     *
     * If you want to will be overwritten when you update QCubed. To override, make your changes
     * to the NestedSortable.class.php file instead.
     *
     * NestedSortable is a group of panels that can be dragged to reorder them. You will need to put
     * some care into the CSS styling of the objects so that the CSS allows them to be moved. It
     * will use the top level HTML objects inside the panel to decide what to sort. Make sure
     * they have IDs so it can return the IDs of the items in sort order.
     *
     * @property string $WrapperClass
     * @property string $SectionClass
     * @property-read array $ItemArray List of ControlIds in sort orders.
     * @property string $Item
     * @property array $DataSource
     *
     * @link https://github.com/ilikenwf/nestedSortable
     * @package QCubed\Plugin
     */
    class NestedSortable extends NestedSortableGen
    {
        use Q\Control\DataBinderTrait;

        protected ?string $strItem = null;
        protected ?array $aryItemArray = null;

        /** @var null|string WrapperClass */
        protected ?string $strWrapperClass = null;
        /** @var null|string SectionClass */
        protected ?string $strSectionClass = null;
        /** @var  callable */
        protected mixed $nodeParamsCallback = null;
        /** @var  callable */
        protected mixed $cellParamsCallback = null;
        /** @var */
        protected mixed $mixButtons;
        /** @var array DataSource, from which the items are picked and rendered */
        protected array $objDataSource;
        protected array $strParams;
        protected array $strObjects;

        protected int $intCurrentDepth = 0;
        protected int $intCounter = 0;
        /** @var null|string */
        protected ?string $strRenderCellHtml = null;

        /** @var  null|integer Id */
        protected ?int $intId = null;
        /** @var  null|string ParentId */
        protected ?string $strParentId = '';
        /** @var  null|integer Depth */
        protected ?int $intDepth = null;
        /** @var  null|integer Left */
        protected ?int $intLeft = null;
        /** @var  null|integer Right */
        protected ?int $intRight = null;
        /** @var  string MenuText */
        protected string $strMenuText;
        /** @var  string RedirectUrl */
        protected string $strRedirectUrl;
        /** @var  int IsRedirect */
        protected int $intIsRedirect;
        /** @var  string ExternalUrl */
        protected string $strExternalUrl;
        /** @var  string SelectedPage */
        protected string $intSelectedPageId;
        /** @var  string SelectedPage */
        protected string $strSelectedPage;
        /** @var  integer SelectedPageLocked */
        protected int $intSelectedPageLocked;
        /** @var  string ContentTypeObject */
        protected string $strContentTypeObject;
        /** @var  int ContentType */
        protected int $intContentType;
        /** @var  int Status */
        protected int $intStatus;

        /**
         * Constructor method for initializing the control object. It ensures a proper setup by calling the parent
         * constructor and registers the necessary files.
         *
         * @param mixed $objParentObject The parent object to which this control belongs.
         * @param string|null $strControlId An optional control ID to uniquely identify the control. Defaults to null.
         *
         * @return void
         * @throws \Exception
         * @throws Caller If an error occurs during the parent constructor call.
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
         * Registers the necessary JS and CSS files for nested sortable functionality,
         * Bootstrap, and Font Awesome.
         *
         * @return void
         * @throws Caller
         */
        protected function registerFiles(): void
        {
            $this->addJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/jquery.mjs.nestedSortable.js");
            $this->addJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/select2.js");
            $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
            $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
            //$this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/style.css");
            $this->addCssFile(NESTEDSORTABLE_BACKEND_URL . "/assets/css/style.css");
            Bs\Bootstrap::loadJS($this);
        }

        /**
         * Sets the callback function to create node parameters.
         *
         * @param callable $callback The callback function that generates node parameters.
         * @return void
         */
        public function createNodeParams(callable $callback): void
        {
            $this->nodeParamsCallback = $callback;
        }

        /**
         * Assigns a callback function intended to handle the rendering of buttons.
         *
         * @param callable $callback The callback function that will be used to render buttons.
         * @return void
         */
        public function createRenderButtons(callable $callback): void
        {
            $this->cellParamsCallback = $callback;
        }

        /**
         * Retrieves the raw item parameters from the given item using a callback function.
         * Throws an exception if the nodeParamsCallback is not set.
         *
         * @param mixed $objItem The item from which parameters are to be extracted.
         * @return array The extracted parameters including an id, parent_id, depth, left, right, menu_text,
         *               content_type_object, content_type, group_title_id, redirect_url, is_redirect,
         *               selected_page_id, selected_page, selected_page_locked, and status.
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
            $strParentId = '';
            if (isset($params['parent_id'])) {
                $strParentId = $params['parent_id'];
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

            return [
                'id' => $intId,
                'parent_id' => $strParentId,
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
        }

        /**
         * Retrieves the drawing parameters for the given object.
         * The parameters are determined using the provided cellParamsCallback.
         *
         * @param mixed $objItem The object for which to retrieve the drawing parameters.
         *
         * @return mixed The drawing parameters for the object.
         * @throws Exception If cellParamsCallback is not provided.
         *
         */
        public function getObjectDraw(mixed $objItem): mixed
        {
            if (!$this->cellParamsCallback) {
                throw new Exception("Must provide a cellParamsCallback");
            }

            $this->mixButtons = call_user_func($this->cellParamsCallback, $objItem);
            return $this->mixButtons;
        }

        /**
         * Prepares the object for serialization by transforming callback parameters using the sleepHelper method.
         * The parent sleep method is then called.
         *
         * @return array
         */
        public function sleep(): array
        {
            $this->nodeParamsCallback = ControlBase::sleepHelper($this->nodeParamsCallback);
            $this->cellParamsCallback = ControlBase::sleepHelper($this->cellParamsCallback);
            return parent::sleep();
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
        public function wakeup(FormBase $objForm): void
        {
            parent::wakeup($objForm);
            $this->nodeParamsCallback = ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
            $this->cellParamsCallback = ControlBase::wakeupHelper($objForm, $this->cellParamsCallback);
        }

        /**
         * Generates the HTML for the control, including any bound data and configured attributes.
         *
         * @return string The generated HTML for the control.
         * @throws Caller
         * @throws Exception
         */
        protected function getControlHtml(): string
        {
            $this->dataBind();

            $this->strParams = [];
            $this->strObjects = [];

            $strHtml = $this->welcomeMessage();

            if ($this->objDataSource) {
                foreach ($this->objDataSource as $objObject) {
                    $this->strParams[] = $this->getItemRaw($objObject);
                    if ($this->cellParamsCallback) {
                        $this->strObjects[] = $this->getObjectDraw($objObject);
                    }
                }
            }

            $strHtml .= $this->renderMenuTree($this->strParams, $this->strObjects);

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
         * Generates a welcome message if there is exactly one item in the data source.
         *
         * @return string A formatted HTML string containing a welcome message with instructions to create menu items,
         *                wrapped in a styled alert div.
         */
        public function welcomeMessage(): string
        {
            if (count($this->objDataSource) == 1) {
                $strEmptyMenuText = t('<strong>Welcome! </strong> Create the following menu items!');
                return "<div class='alert alert-info alert-dismissible' role='alert' style='display: block;'>
$strEmptyMenuText
</div>";
            }

            return '';
        }

        /**
         * Renders an HTML menu tree based on provided parameters and objects.
         *
         * @param array $arrParams An array of parameters, each of which contains information about a menu item such as 'id', 'parent_id', 'depth',
         *                          'left', 'right', 'menu_text', 'redirect_url', 'is_redirect', 'selected_page_id',
         *                          'selected_page', 'content_type_object', 'content_type', 'group_title_id', and 'status'.
         * @param array $arrObjects An array of additional objects that may be used for rendering each menu item.
         *
         * @return string A formatted HTML string representing the nested menu structure.
         */
        protected function renderMenuTree(array $arrParams, array $arrObjects): string
        {
            $strHtml = '';

            // Let's start with the menu wrapper
            $strHtml .= '<' . $this->TagName . ' class="' . $this->WrapperClass . '" id="' . $this->ControlId . '">';

            // Let's start the walkthrough
            for ($i = 0; $i < count($arrParams); $i++) {
                $this->intId = $arrParams[$i]['id'];
                $this->strParentId = $arrParams[$i]['parent_id'];
                $this->intDepth = $arrParams[$i]['depth'];
                $this->intLeft = $arrParams[$i]['left'];
                $this->intRight = $arrParams[$i]['right'];
                $this->strMenuText = $arrParams[$i]['menu_text'];
                $this->strRedirectUrl = $arrParams[$i]['redirect_url'];
                $this->intIsRedirect = (int)$arrParams[$i]['is_redirect'];
                $this->strExternalUrl = $arrParams[$i]['external_url'];
                $this->intSelectedPageId = (int)$arrParams[$i]['selected_page_id'];
                $this->strSelectedPage = $arrParams[$i]['selected_page'];
                $this->strContentTypeObject = $arrParams[$i]['content_type_object'];
                $this->intContentType = (int)$arrParams[$i]['content_type'];
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
         * Renders the cell content as an HTML section if the cell parameter callback is defined.
         *
         * @param mixed $value The value to be rendered inside the HTML section.
         * @return string|null The formatted HTML section containing the value or null if the callback is not defined.
         */
        protected function getRenderCellHtml(mixed $value): ?string
        {
            if ($this->cellParamsCallback) {
                $strHtml = '';
                $attributes = [];
                if ($this->strSectionClass) {
                    $attributes['class'] = $this->strSectionClass;
                }
                $strHtml .= $value;
                return Html::renderTag('section', $attributes, $strHtml);
            } else {
                return null;
            }
        }

        /**
         * Retrieves routing information for a given key and selected page.
         *
         * @param int $key The key to be checked against double redirects.
         * @param string $selectedpage The selected page's name to be displayed in the routing information.
         *
         * @return string A formatted string containing routing information, indicating whether or not
         *                there's a double redirection to the selected page, wrapped in styled HTML elements.
         */
        protected function getRoutingInfo(int $key, string $selectedpage): string
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
                $strHtml = ' - ' . t('Redirected to this page: ') . '<span class="view-link">' . $selectedpage . '</span>';
            } else {
                $strHtml = ' - ' . t('Redirected to this page ') . ' | ' . '<span style="color: #ff0000;">' .
                    t('Warning, double redirection: ') . '</span><span class="view-link">' . $selectedpage . '</span>';
            }
            return $strHtml;
        }

        /**
         * Recursively retrieves all child menu item IDs for a given parent menu item ID.
         *
         * @param array $objMenuArray Array of menu items to search through.
         * @param null|mixed $clickedId ID of the parent menu item, null if starting from root.
         *
         * @return array An array of IDs representing the full hierarchy of child menu items.
         */
        public function getFullChildren(array $objMenuArray, mixed $clickedId = null): array
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
         * @param null|mixed $clickedId The ID of the clicked menu item for which the ancestor ID is to be found.
         *                         Defaults to null if no specific ID is given.
         *
         * @return mixed The ancestor ID if found, null otherwise.
         */
        public function getAncestorId(array $objMenuArray, mixed $clickedId = null): mixed
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
         * Retrieves the IDs of child menu items based on their parent ID.
         *
         * @param array $objMenuArray An array of menu objects to search for child items.
         * @param mixed $clickedId The ID of the parent menu item to find children for, or null to find top-level items.
         *
         * @return array An array of IDs representing the children of the specified parent menu item.
         */
        public function getChildren(array $objMenuArray, mixed $clickedId = null): array
        {
            $children = [];
            foreach ($objMenuArray as $objMenu) {
                if ($objMenu->ParentId == $clickedId) {
                    $children[] = $objMenu->Id;
                }
            }
            return $children;
        }


        /**
         * Retrieves the parent ID of a clicked menu item from the given menu array.
         *
         * @param array $objMenuArray An array of menu objects to search through.
         * @param mixed $clickedId The ID of the clicked menu item to find the parent ID for. Defaults to null.
         *
         * @return mixed The parent ID of the clicked menu item if found, null if the item is a root element or not found.
         */
        public function getParentId(array $objMenuArray, mixed $clickedId = null): mixed
        {
            foreach ($objMenuArray as $objMenu) {
                if ($objMenu->Id == $clickedId) {
                    return $objMenu->ParentId;
                }
            }
            return null;
        }


        /**
         * Verifies the lock status of selected pages by loading their content and checking if they are locked.
         *
         * @param object $objMenuContent An object that must have a `load` method to retrieve page content.
         * @param array $selectedPageArray An array of page IDs whose lock status will be verified.
         *
         * @return int The number of pages that are locked.
         * @throws Exception If the $objMenuContent object does not have a `load` method
         *                    or if content for a selected page fails to load.
         */
        public function verifyPageLockStatus(mixed $objMenuContent, array $selectedPageArray): int
        {
            // Check if $objMenuContent has a `load` method
            if (!method_exists($objMenuContent, 'load')) {
                throw new Exception("The given object does not have a working `load` method.");
            }

            $countPageLocks = 0;

            foreach ($selectedPageArray as $selectedPage) {
                // Load the page content and check if it is correct
                $objContent = $objMenuContent::load($selectedPage);

                if (!$objContent) {
                    throw new Exception("Failed to load content for a page with ID $selectedPage.");
                }

                $parentId = end($selectedPageArray);
                $selectedPageId = $objMenuContent::load($parentId)->SelectedPageId;

                if (!in_array($selectedPageId, $selectedPageArray)) {
                    // Check if SelectedPageLocked is 1
                    if ($objContent->SelectedPageLocked == 1) {
                        $countPageLocks++;
                    }
                } else {
                    return 0;
                }
            }

            return $countPageLocks;
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
         * Generates an array of jQuery UI options for the control, including a custom 'create' event handler.
         *
         * @return array An associative array containing jQuery UI options, where the 'create' option is a JavaScript closure
         *               in that process the nested sortable structure and records control modifications within the framework.
         */
        public function makeJqOptions(): array
        {
            $jqOptions = parent::makeJqOptions();

            // TODO: Put this in the qcubed.js file, or something like it.
            $jqOptions['create'] = new Q\Js\Closure("
            var arr = jQuery(this).nestedSortable('toArray', {startDepthCount: 0});
            var str = JSON.stringify(arr);
            //console.log('CREATE: ' + str);
            qcubed.recordControlModification('$this->ControlId', '_ItemArray', str);
            ");
            return $jqOptions;
        }

        /**
         * Attaches various event listeners and JavaScript functionalities to HTML elements
         * for handling nested sortable lists, button states, and sort stop events within a given context.
         *
         * @return string The parent class's end script, modified with additional JavaScript functionalities
         *                for nested sortable lists and UI interactions.
         * @throws Caller
         */
        public function getEndScript(): string
        {
            Application::executeSelectorFunction("body", "on", "click", ".js-btn-collapse-all",
                new Js\Closure("jQuery('#$this->ControlId').find('li.mjs-nestedSortable-expanded').removeClass('mjs-nestedSortable-expanded').addClass('mjs-nestedSortable-collapsed')"),
                ApplicationBase::PRIORITY_HIGH);

            Application::executeSelectorFunction("body", "on", "click", ".js-btn-expand-all",
                new Js\Closure("jQuery('#$this->ControlId').find('li.mjs-nestedSortable-collapsed').removeClass('mjs-nestedSortable-collapsed').addClass('mjs-nestedSortable-expanded')"),
                ApplicationBase::PRIORITY_HIGH);

            Application::executeSelectorFunction(".disclose", "on", "click",
                new Js\Closure("jQuery(this).closest('li').toggleClass('mjs-nestedSortable-expanded').toggleClass('mjs-nestedSortable-collapsed')"),
                ApplicationBase::PRIORITY_HIGH);

            $strJS = parent::getEndScript();

            $strCtrlJs = <<<FUNC
jQuery('#$this->ControlId').on("sortstop", function (event, ui) {
            var draggedItemId = ui.item.attr('id');
            var cleanedId = draggedItemId.split('_')[1];
            console.log("Cleaned Item ID: " + cleanedId);
        
            var arr = jQuery(this).nestedSortable("toArray", {startDepthCount: 0});
            //arr.shift();
            var str = JSON.stringify(arr);
            console.log("SORTSTOP: " + str);
            
            qcubed.recordControlModification("$this->ControlId", "_ItemArray", str);
            qcubed.recordControlModification("$this->ControlId", "_Item", cleanedId);
})
FUNC;
            Application::executeJavaScript($strCtrlJs, ApplicationBase::PRIORITY_HIGH);

            return $strJS;
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
                case 'Item': return $this->strItem;
                case 'ItemArray': return $this->aryItemArray;
                case "WrapperClass": return $this->strWrapperClass;
                case "SectionClass": return $this->strSectionClass;
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
    }