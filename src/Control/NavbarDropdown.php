<?php

    namespace QCubed\Plugin\Control;

    use QCubed\Bootstrap\NavbarItem;
    use QCubed\Control\ListItemStyle;
    use QCubed\Exception\Caller;
    use QCubed\QString;

    /**
     * Represents a dropdown menu item within a navigation bar.
     */
    class NavbarDropdown extends NavbarItem
    {
        /**
         * NavbarDropdown constructor.
         *
         * @param string $strName
         * @param null|string $strValue
         * @param string|null $strAnchor
         *
         * @throws Caller
         */
        public function __construct(string $strName, ?string $strValue = null, ?string $strAnchor = null)
        {
            parent::__construct($strName, $strValue);
            if ($strAnchor) {
                $this->strAnchor = $strAnchor;
            } else {
                $this->strAnchor = '#'; // need a default for attaching clicks and correct styling.
            }
            $this->objItemStyle = new ListItemStyle();
            $this->objItemStyle->setCssClass('dropdown');
        }

        /**
         * Retrieves the HTML representation of the item's text, including a clickable anchor
         * if an anchor is defined.
         *
         * @return string The HTML string of the item's text.
         */
        public function getItemText(): string
        {
            $strHtml = QString::htmlEntities($this->strName);
            if ($this->strAnchor) {
                $strHtml = sprintf('<a href="%s" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">%s <span class="caret"></span></a>', $this->strAnchor, $strHtml) . "\n";
            }
            return $strHtml;
        }

        /**
         * Retrieves the attributes associated with a sub-tag.
         *
         * @return array|string|null The attributes as an array, a string, or null if no attributes are set.
         */
        public function getSubTagAttributes(): array|string|null
        {
            return ['class'=>'dropdown-menu', 'role'=>'menu'];
        }
    }
