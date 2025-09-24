<?php

    namespace QCubed\Plugin\Control;

    use QCubed\Control\ControlBase;
    use QCubed\Exception\Caller;
    use QCubed\QString;
    use QCubed\Html;
    use QCubed\Project\Application;

    /**
     * @property string $Template Path to the HTML template (.tpl.php) file (applicable in case a template is being used for Render)
     */

    class Tabs extends ControlBase
    {
        protected string $strSelectedId;

        /**
         * Validates the current instance or data according to predefined criteria.
         *
         * @return bool Returns true if the validation is successful, false otherwise.
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
        {
        }

        /**
         * Generates and returns the HTML structure for the control, including its child controls
         * as a tab-based navigation component.
         *
         * @return string The rendered HTML output for the control and its children.
         * @throws Caller
         */
        public function getControlHtml(): string
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

            return $this->renderTag('div', null, null, $strHtml);
        }

        /**
         * Adds a child control to the current control and sets the first added control as the default selection if applicable.
         *
         * @param ControlBase $objControl The control to be added as a child.
         *
         * @return void
         */
        public function addChildControl(ControlBase $objControl): void
        {
            parent::addChildControl($objControl);
            if (count($this->objChildControlArray) == 1) {
                $this->strSelectedId = $objControl->ControlId;    // default to first item added being selected
            }
        }

        /**
         * Generates and retrieves a JavaScript script that enables tab navigation functionality.
         * This script ensures that tabs respond to hash changes and maintain scroll positions on transition.
         *
         * @return string The JavaScript code that manages tab interactions and hash-based navigation.
         * @throws Caller
         */
        public function getEndScript(): string
        {
            $strJS = parent::getEndScript();

            Application::executeJavaScript("jQuery(function(){
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
  ");

            return $strJS;
        }
    }