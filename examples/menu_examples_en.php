<?php

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Action\Ajax;
    use QCubed\Action\ActionParams;
    use QCubed\Html;
    use QCubed\Query\QQ;

    require_once('qcubed.inc.php');

    /**
     * Class MenuExampleEnForm
     *
     * This class represents a form that manages and displays various menu configurations.
     * It contains methods to create and bind data to different types of menus such as
     * natural lists, navigation bars, smart menus, side menus, and nested menus.
     */
    class MenuExampleEnForm extends Form
    {
        protected Q\Plugin\Control\NaturalList $tblList;

        protected Bs\Navbar $navBar;
        protected Bs\NavbarItem $objListMenu;
        protected Q\Plugin\Control\NavbarDropdown $objListSubMenu;

        protected Bs\Navbar $smartMenus;
        protected Q\Plugin\Control\SmartMenus $tblNav;

        protected Bs\Navbar $sideMenu;
        protected Q\Plugin\Control\SidebarList $tblBar;
        protected Q\Plugin\Control\Sidebar $tblSubMenu;
        //protected $objSelectedArray;

        protected Q\Plugin\Control\NestedSidebar $tblNestedMenu;

        /**
         * Initializes and sets up various components of the form, such as natural lists, navigation bar, smart menus,
         * side menu, and nested menus.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function formCreate(): void
        {
            $this->naturalList_Create();
            $this->navBar_Create();
            $this->smartMenus_Create();
            $this->sideMenu_Create();
            $this->nestedMenu_Create();
        }

        /**
         * Creates and initializes a NaturalList control.
         *
         * @return void
         * @throws Caller
         */
        protected function naturalList_Create(): void
        {
            $this->tblList = new Q\Plugin\Control\NaturalList($this);
            $this->tblList->CssClass = 'simple';
            $this->tblList->TagName = 'ol';
            $this->tblList->setDataBinder('Menu_Bind');
            $this->tblList->createNodeParams([$this, 'Menu_Draw']);
        }

        /**
         * Creates and initializes a navigation bar for the application using menu data.
         *
         * This method retrieves all menu items from the database, organizes them hierarchically,
         * and adds them to a Bootstrap-styled navigation bar. It handles both parent and child menu
         * relationships and ensures that only enabled menu items are displayed. The navigation bar
         * is styled using Bootstrap classes, with a customizable header and associated menu items.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function navBar_Create(): void
        {
            $objMenuArray = Menu::loadAll(
                QQ::Clause(QQ::OrderBy(QQN::menu()->Left),
                    QQ::expand(QQN::menu()->MenuContent)
                ));

            $this->navBar = new Bs\Navbar($this, 'navbar');
            $header_url = 'menu_examples.php';
            $this->navBar->HeaderText = Html::renderTag("img",
                ["class" => "logo", "src" => QCUBED_IMAGE_URL . "/qcubed-4_logo_footer.png", "alt" => "Logo"], null, true);
            $this->navBar->HeaderAnchor = $header_url;
            $this->navBar->StyleClass = Bs\Bootstrap::NAVBAR_INVERSE;

            $dlgBar = new Bs\NavbarList($this->navBar);

            foreach ($objMenuArray as $objMenu) {
                if ($objMenu->MenuContent->HomelyUrl) {
                    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX . $objMenu->MenuContent->RedirectUrl;
                } else {
                    $url = $objMenu->MenuContent->RedirectUrl;
                }
                if ($objMenu->MenuContent->IsEnabled !== 2 || $objMenu->MenuContent->IsEnabled !== 3) {
                    if ($objMenu->ParentId == null && $objMenu->Right == $objMenu->Left + 1) {

                        $this->objListMenu = new Bs\NavbarItem($objMenu->MenuContent->getMenuText(), null, $url);
                        $dlgBar->addMenuItem($this->objListMenu);
                    } elseif (!in_array($objMenu->ParentId, $this->ControllableValues($objMenuArray, 'Id')) &&
                        $objMenu->Right !== $objMenu->Left + 1) {
                        $this->objListSubMenu = new Q\Plugin\Control\NavbarDropdown($objMenu->MenuContent->getMenuText(), null, $url);
                        $dlgBar->addMenuItem($this->objListSubMenu);
                    }
                    if (in_array($objMenu->ParentId, $this->ControllableValues($objMenuArray, 'Id')) &&
                        $objMenu->Depth == 1) {
                        $this->objListSubMenu->addItem(new Bs\NavbarItem($objMenu->MenuContent->getMenuText(), null, $url));
                    }
                }
            }
        }

        /**
         * Creates and configures a SmartMenus navigation system for the application.
         *
         * This method initializes a Bootstrap-styled navigation bar and integrates it
         * with the SmartMenus plugin to create a dynamic and interactive menu. The menu
         * is configured with specific styling and behavior, including CSS classes, HTML
         * tag properties, and data-binding functionality for dynamic content generation.
         *
         * @return void
         * @throws Caller
         */
        protected function smartMenus_Create(): void
        {
            $this->smartMenus = new Bs\Navbar($this);
            $url = 'menu_examples.php';
            $this->smartMenus->HeaderText = Html::renderTag("img",
                ["class" => "logo", "src" => QCUBED_IMAGE_URL . "/qcubed-4_logo_footer.png", "alt" => "Logo"], null, true);
            $this->smartMenus->HeaderAnchor = $url;
            $this->smartMenus->StyleClass = Bs\Bootstrap::NAVBAR_INVERSE;

            $this->tblNav = new Q\Plugin\Control\SmartMenus($this->smartMenus);
            $this->tblNav->CssClass = 'nav navbar-nav smartside';
            $this->tblNav->TagName = 'ul';
            $this->tblNav->TagStyle = 'dropdown-menu';
            $this->tblNav->setDataBinder('Menu_Bind');
            $this->tblNav->createNodeParams([$this, 'Menu_Draw']);
        }

        /**
         * Creates and initializes a side menu for the application.
         *
         * This method configures a sidebar navigation menu using Bootstrap classes. The side menu
         * contains a header with an image and link, styled using the inverse Bootstrap theme. It
         * also creates and binds a primary menu list (`SidebarList`) for displaying menu items and handles
         * hierarchical data binding. A secondary submenu (`Sidebar`) is also initialized and configured to
         * display child menu items based on user interaction with the primary menu.
         *
         * Event handling and CSS classes are applied to ensure dynamic updates for menu selections and
         * custom styling. Data binding callbacks for both primary and submenus are set to dynamically retrieve
         * and render menu items as needed.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function sideMenu_Create(): void
        {
            $this->sideMenu = new Bs\Navbar($this);
            $url = 'menu_examples.php';
            $this->sideMenu->HeaderText = Html::renderTag("img",
                ["class" => "logo", "src" => QCUBED_IMAGE_URL . "/qcubed-4_logo_footer.png", "alt" => "Logo"], null, true);
            $this->sideMenu->HeaderAnchor = $url;
            $this->sideMenu->StyleClass = Bs\Bootstrap::NAVBAR_INVERSE;

            $this->tblBar = new Q\Plugin\Control\SidebarList($this->sideMenu);
            $this->tblBar->CssClass = 'nav navbar-nav';
            $this->tblBar->TagName = 'ul';
            $this->tblBar->addCssClass('sidemenu');
            $this->tblBar->setDataBinder('Menu_Bind');
            $this->tblBar->createNodeParams([$this, 'Menu_Draw']);
            $this->tblBar->addAction(new Q\Plugin\Event\SidebarSelect(), new Ajax('SubMenuList_Click'));

            $this->tblSubMenu = new Q\Plugin\Control\Sidebar($this);
            $this->tblSubMenu->SubTagName = 'ul';
            $this->tblSubMenu->setDataBinder('Menu_Bind');
            $this->tblSubMenu->createNodeParams([$this, 'Menu_Draw']);
        }

        /**
         * Creates and configures a nested sidebar menu for the application.
         *
         * This method initializes a sidebar menu control and sets up its structure and styling.
         * It specifies the HTML tag and CSS class for submenu items, binds the menu data dynamically,
         * and defines rendering parameters for each menu node using a callable function.
         * The sidebar ensures a hierarchical representation of menu items.
         *
         * @return void
         * @throws Caller
         */
        protected function nestedMenu_Create(): void
        {
            $this->tblNestedMenu = new Q\Plugin\Control\NestedSidebar($this);
            $this->tblNestedMenu->SubTagName = 'ul';
            $this->tblNestedMenu->SubTagClass = 'submenu';
            $this->tblNestedMenu->setDataBinder('Menu_Bind');
            $this->tblNestedMenu->createNodeParams([$this, 'Menu_Draw']);
        }

        //////////////////////////////////////////////////////////////////

        /**
         * Handles the click event for a submenu and populates the submenu table with the associated child items.
         *
         * This method extracts the menu ID from the provided action parameters, retrieves all menu items from
         * the database, and assigns the child items of the selected menu to the submenu table. It ensures that
         * only relevant child items are displayed based on the clicked menu item.
         *
         * @param ActionParams $params The parameters passed during the action, including the menu ID.
         *
         * @return void
         * @throws Caller
         */
        protected function SubMenuList_Click(ActionParams $params): void
        {
            $strMenuId = $params->ActionParameter;
            $ret = explode('_',  $strMenuId);
            $intMenuId = end($ret);

            $objMenuArray = Menu::loadAll();
            $this->tblSubMenu->AssignedItems = $this->tblSubMenu->getChildren($objMenuArray, $intMenuId);
        }

        /**
         * Extracts and returns specific values from an array of objects based on a target property.
         *
         * This method iterates through an array of objects and collects the values
         * of a specified property where the property is not null. The gathered values
         * are returned as an array.
         *
         * @param array $objArrays The array of objects to be processed.
         * @param string $target The name of the property to extract values from.
         *
         * @return array An array of values extracted from the specified property of the objects.
         */
        public function ControllableValues(array $objArrays, string $target): array
        {
            $arrays = [];
            foreach ($objArrays as $objArray) {
                if ($objArray->$target !== null) {
                    $arrays[] = $objArray->$target;
                }
            }
            return $arrays;
        }

        /**
         * Generates a structured array representing a menu item and its details.
         *
         * This method formats a menu object into a structured array containing its properties
         * and metadata, such as hierarchy, text, visibility status, and associated URLs.
         * The returned data can be used for rendering menus or further processing.
         *
         * @param Menu $objMenu The menu object to be processed into an array structure.
         *
         * @return array An associative array representing the menu item and its attributes, including:
         *               - id: The unique identifier of the menu item.
         *               - parent_id: The identifier of the parent menu item, if any.
         *               - depth: The depth level of the menu item in the hierarchy.
         *               - left: The left boundary of the menu item in the nested set model.
         *               - right: The right boundary of the menu item in the nested set model.
         *               - menu_text: The HTML-escaped text representing the menu item.
         *               - status: The enabled/disabled status of the menu item.
         *               - redirect_url: The URL to which the menu item redirects.
         *               - homely_url: A flag indicating if the redirect URL is relative to the application.
         *               - external_url: The URL for external redirects, if applicable.
         *               - target_type: The target frame or behavior for the menu item link, if specified.
         * @throws Caller
         */
        public function Menu_Draw(Menu $objMenu): array
        {
            $a['id'] = $objMenu->Id;
            $a['parent_id'] = $objMenu->ParentId;
            $a['depth'] = $objMenu->Depth;
            $a['left'] = $objMenu->Left;
            $a['right'] = $objMenu->Right;
            $a['menu_text'] = Q\QString::htmlEntities($objMenu->MenuContent->MenuText);
            $a['status'] = $objMenu->MenuContent->IsEnabled;
            $a['redirect_url'] = $objMenu->MenuContent->RedirectUrl;
            $a['homely_url'] = $objMenu->MenuContent->HomelyUrl;
            $a['external_url'] = $objMenu->MenuContent->ExternalUrl;
            $a['target_type'] = $objMenu->MenuContent->TargetType ? TargetType::toTarget($objMenu->MenuContent->TargetType) : null;
            return $a;
        }

        /**
         * Binds data from the Menu table to various UI table controls.
         *
         * This method queries all menu records from the database, organizes them hierarchically,
         * and binds the resulting data to multiple table controls. The data is sorted using the
         * "Left" property for hierarchical order and includes associated menu content through an expansion clause.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function Menu_Bind(): void
        {
            $this->tblList->DataSource =
            $this->tblNav->DataSource =
            $this->tblBar->DataSource =
            $this->tblSubMenu->DataSource =
            $this->tblNestedMenu->DataSource =
                Menu::loadAll(QQ::Clause(QQ::OrderBy(QQN::menu()->Left),
                    QQ::expand(QQN::menu()->MenuContent)));
        }
    }

    MenuExampleEnForm::run('MenuExampleEnForm');
