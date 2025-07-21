<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Js;
use QCubed\Html;
use QCubed\Query\QQ;

require_once('qcubed.inc.php');

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $tblList;

    protected $navBar;
    protected $objListMenu;
    protected $objListSubMenu;
    protected $strUrl;

    protected $smartMenus;
    protected $tblNav;

    protected $sideMenu;
    protected $tblBar;
    protected $tblSubMenu;
    protected $objSelectedArray;

    protected $tblNestedMenu;

    protected function formCreate()
    {
        $this->naturalList_Create();
        $this->navBar_Create();
        $this->smartMenus_Create();
        $this->sideMenu_Create();
        $this->nestedMenu_Create();
    }

    protected function naturalList_Create()
    {
        $this->tblList = new Q\Plugin\Control\NaturalList($this);
        $this->tblList->CssClass = 'simple';
        $this->tblList->TagName = 'ol';
        $this->tblList->setDataBinder('Menu_Bind');
        $this->tblList->createNodeParams([$this, 'Menu_Draw']);
    }

    protected function navBar_Create()
    {
        $objMenuArray = Menu::loadAll(
            Q\Query\QQ::Clause(Q\Query\QQ::OrderBy(QQN::menu()->Left),
                Q\Query\QQ::expand(QQN::menu()->MenuContent)
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

    protected function smartMenus_Create()
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

    protected function sideMenu_Create()
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
        $this->tblBar->addAction(new Q\Plugin\Event\SidebarSelect(), new Q\Action\Ajax('SubMenuList_Click'));

        $this->tblSubMenu = new Q\Plugin\Control\Sidebar($this);
        $this->tblSubMenu->SubTagName = 'ul';
        $this->tblSubMenu->setDataBinder('Menu_Bind');
        $this->tblSubMenu->createNodeParams([$this, 'Menu_Draw']);
    }

    protected function nestedMenu_Create()
    {
        $this->tblNestedMenu = new Q\Plugin\Control\NestedSidebar($this);
        $this->tblNestedMenu->SubTagName = 'ul';
        $this->tblNestedMenu->SubTagClass = 'submenu';
        $this->tblNestedMenu->setDataBinder('Menu_Bind');
        $this->tblNestedMenu->createNodeParams([$this, 'Menu_Draw']);
    }

    //////////////////////////////////////////////////////////////////

    protected function SubMenuList_Click(ActionParams $params)
    {
        $strMenuId = $params->ActionParameter;
        $ret = explode('_',  $strMenuId);
        $intMenuId = end($ret);

        $objMenuArray = Menu::loadAll();
        $this->tblSubMenu->AssignedItems = $this->tblSubMenu->getChildren($objMenuArray, $intMenuId);
    }

    public function ControllableValues($objArrays, $target)
    {
        $arrays = [];
        foreach ($objArrays as $objArray) {
            if ($objArray->$target !== null) {
                $arrays[] = $objArray->$target;
            }
        }
        return $arrays;
    }

    public function Menu_Draw(Menu $objMenu)
    {
        $a['id'] = $objMenu->Id;
        $a['parent_id'] = $objMenu->ParentId;
        $a['depth'] = $objMenu->Depth;
        $a['left'] = $objMenu->Left;
        $a['right'] = $objMenu->Right;
        $a['menu_text'] = Q\QString::htmlEntities($objMenu->MenuContent->MenuText);
        $a['status'] = $objMenu->MenuContent->IsEnabled;
        $a['redirect_url'] = $objMenu->MenuContent->RedirectUrl;
        $a['external_url'] = $objMenu->MenuContent->ExternalUrl;
        $a['homely_url'] = $objMenu->MenuContent->HomelyUrl;
        $a['target_type'] = $objMenu->MenuContent->TargetType ? TargetType::toTarget($objMenu->MenuContent->TargetType) : null;
        return $a;
    }

    protected function Menu_Bind()
    {
        $this->tblList->DataSource =
        $this->tblNav->DataSource =
        $this->tblBar->DataSource =
        $this->tblSubMenu->DataSource =
        $this->tblNestedMenu->DataSource =
            Menu::loadAll(QQ::Clause(Q\Query\QQ::OrderBy(QQN::menu()->Left),
                QQ::expand(QQN::menu()->MenuContent)));
    }
}

SampleForm::run('SampleForm');
