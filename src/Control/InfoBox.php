<?php

    namespace QCubed\Plugin\Control;

    use QCubed\Control\Panel;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Html;
    use QCubed\Type;

    /**
     * Class InfoBox
     * @package QCubed\Plugin
     */

    /**
     * @property string $Text
     *
     * @package QCubed\Plugin
     */

    class InfoBox extends Panel
    {
        /** @var null|string */
        protected ?string $strText = null;

        /**
         * Constructor method for initializing the object.
         *
         * @param mixed $objParentObject The parent object to which this control belongs.
         * @param string|null $strControlId An optional control ID to uniquely identify this control.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);

            $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/infobox.css");
        }

        /**
         * Generates the HTML output for the control.
         *
         * @return string The generated HTML string containing the control's content.
         */
        protected function getControlHtml(): string
        {
            $strHtml = "";

            $strOut = Html::renderTag('p', null, $this->Text);
            $strHtml .= $this->renderTag('div', null, null, $strOut);

            return $strHtml;
        }

        /**
         * Magic getter method to retrieve property values.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller When the property name is invalid or not accessible.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'Text': return $this->strText;

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
         * Magic method to set the value of a property dynamically.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws InvalidCast Thrown if the provided value cannot be cast to the expected type.
         * @throws Caller Thrown if the property is not recognized or any error occurs in the parent implementation.
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case 'Text':
                    try {
                        $this->blnModified = true;
                        $this->strText = Type::Cast($mixValue, Type::STRING);
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
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