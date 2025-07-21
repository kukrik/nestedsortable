<?php

namespace QCubed\Plugin\Control;

use QCubed\Control\ControlBase;
use QCubed\Exception\Caller;
//use QCubed\Exception\InvalidCast;
use QCubed\QString;
//use QCubed\Type;
use QCubed\Html;
use QCubed\Project\Application;

/**
 * @property string $Template Path to the HTML template (.tpl.php) file (applicable in case a template is being used for Render)
 */

class Tabs extends \QCubed\Project\Control\ControlBase
{
    protected $strSelectedId;

    public function validate()
    {
        return true;
    }

    public function parsePostData()
    {
    }

    public function getControlHtml()
    {
        $strHtml = '';
        foreach ($this->objChildControlArray as $objChildControl) {
            $strInnerHtml = Html::renderTag('a',
                [
                    'href' => '#' . $objChildControl->ControlId . '_tab',
                    'aria-controls' => $objChildControl->ControlId . '_tab',
                    'role' => 'tab',
                    'data-toggle' => 'tab'
                ],
                QString::htmlEntities($objChildControl->Name)
            );
            $attributes = ['role' => 'presentation'];
            if ($objChildControl->ControlId == $this->strSelectedId) {
                $attributes['class'] = 'active';
            }

            $strTag = Html::renderTag('li', $attributes, $strInnerHtml);
            $strHtml .= $strTag;
        }
        $strHtml = Html::renderTag('ul', ['class' => 'nav nav-tabs', 'role' => 'tablist'], $strHtml);

        $strInnerHtml = '';
        foreach ($this->objChildControlArray as $objChildControl) {
            $class = 'tab-pane';
            $strItemHtml = null;
            if ($objChildControl->ControlId == $this->strSelectedId) {
                $class .= ' active';
            }
            $strItemHtml = $objChildControl->render(false);

            $strInnerHtml .= Html::renderTag('div',
                [
                    'role' => 'tabpanel',
                    'class' => $class,
                    'id' => $objChildControl->ControlId . '_tab'
                ],
                $strItemHtml
            );
        }

        $strTag = Html::renderTag('div', ['class' => 'tab-content'], $strInnerHtml);

        $strHtml .= $strTag;

        $strTag = $this->renderTag('div', null, null, $strHtml);

        return $strTag;
    }

    public function addChildControl(ControlBase $objControl)
    {
        parent::addChildControl($objControl);
        if (count($this->objChildControlArray) == 1) {
            $this->strSelectedId = $objControl->ControlId;    // default to first item added being selected
        }
    }

    public function getEndScript()
    {
        Application::executeJavaScript(sprintf("jQuery(function(){
  // Change tab on load
  var hash = window.location.hash;
  hash && jQuery('ul.nav a[href=\"' + hash + '\"]').tab('show');

  jQuery('.nav-tabs a').click(function (e) {
    jQuery(this).tab('show');
    var scrollmem = jQuery('body').scrollTop();
    window.location.hash = this.hash;
    jQuery('html,body').scrollTop(scrollmem);
  });

  // Change tab on hashchange
  window.addEventListener('hashchange', function() {
    var changedHash = window.location.hash;
    changedHash && jQuery('ul.nav a[href=\"' + changedHash + '\"]').tab('show');
  }, false);})
  "));
    }
}