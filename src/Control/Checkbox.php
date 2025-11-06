<?php

    namespace QCubed\Plugin\Control;

    require_once(dirname(__DIR__, 2) . '/i18n/i18n-lib.inc.php');


    use QCubed\Css\TextAlignType;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Control\ControlBase;
    use QCubed\QString;
    use QCubed\Type;
    use QCubed\TagStyler;
    use QCubed\ModelConnector\Param as QModelConnectorParam;
    use QCubed\Html;
    use QCubed\Bootstrap as Bs;

    /**
     * Class Checkbox
     *
     * Outputs a bootstrap style checkbox and also takes into account the client's a desired theme.
     *
     * @property string $Text is used to display text that is displayed next to the checkbox. The text is rendered as an HTML "Label For" the checkbox.
     * @property string $TextAlign specifies if "Text" should be displayed to the left or to the right of the checkbox.
     * @property boolean $Checked specifics whether or not the checkbox is checked
     * @property boolean $HtmlEntities specifies whether the checkbox text will have to be run through htmlentities or not.
     * @property string $WrapperClass $WrapperClass only sets or returns the CSS class of this wrapped in a div.
     * @property string $WrapperStyle
     * @property string $InputClass $InputClass only sets or returns the CSS class of this input.
     *
     * @property-write boolean $Inline whether checkbox should be displayed inline or wrapped in a div
     * @package QCubed\Plugin
     */
    class Checkbox extends ControlBase
    {
        /** @var string Tag for rendering the control */
        protected string $strTag = 'input';
        /** @var null|string Text opposite to the checkbox */
        protected ?string $strText = null;
        /** @var string the alignment of the string */
        protected string $strTextAlign = TextAlignType::RIGHT;
        /** @var bool Should the htmlentities function be run on the control's text (strText)? */
        protected bool $blnHtmlEntities = true;
        /** @var bool Determines whether the checkbox is checked? */
        protected bool $blnChecked = false;

        /**
         * @var  null|TagStyler for labels of checkboxes. If side-by-side labeling, the styles will be applied to a
         * Span that wraps both the checkbox and the label.
         */
        protected ?TagStyler $objLabelStyle = null;

        protected bool $blnInline = false;
        protected bool $blnWrapLabel = false;
        protected ?string $strInputClass = null;
        protected ?string $strWrapperClass = null;

        protected string $strLabelAttributes = '';

        /**
         * Parses the POST data related to the current control and updates the control's state accordingly.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function parsePostData(): void
        {
            $val = $this->objForm->checkableControlValue($this->strControlId);
            if ($val !== null) {
                $this->blnChecked = Type::cast($val, Type::BOOLEAN);
            }
        }

        /**
         * Returns the HTML code for the control which can be sent to the client.
         *
         * Note, a previous version wrapped this in a div and made the control a block level control unnecessarily. To
         * achieve a block control, set blnUseWrapper and blnIsBlockElement.
         *
         * @return string THe HTML for the control
         */
        protected function getControlHtml(): string
        {
            $attrOverride = array('type' => 'checkbox', 'name' => $this->strControlId, 'value' => 'true');
            return $this->renderButton($attrOverride);
        }

        /**
         * Renders an HTML button with optional attributes and customizations.
         *
         * @param array $attrOverride An associative array of attributes to override or add to the rendered button.
         *
         * @return string The rendered HTML markup for the button.
         */
        protected function renderButton(array $attrOverride): string
        {
            if ($this->blnChecked) {
                $attrOverride['checked'] = 'checked';
            }

            if ($this->strInputClass) {
                $attrOverride['class'] = $this->strInputClass;
            }

            $strText = ($this->blnHtmlEntities) ? QString::htmlEntities($this->strText) : $this->strText;

            if ($this->strText) {
                $this->strLabelAttributes = ' for="' . $this->strControlId . '"';
            }

            $strCheckHtml = Html::renderLabeledInput(
                $strText,
                $this->strTextAlign == Html::TEXT_ALIGN_LEFT,
                $this->renderHtmlAttributes($attrOverride),
                $this->strLabelAttributes,
                $this->blnWrapLabel
            );

            return Html::renderTag('div', $this->renderLabelAttributes(), $strCheckHtml);
        }

        /**
         * Return a styler to style the label that surrounds the control if the control has text.
         * @return TagStyler
         */
        public function getCheckLabelStyler(): TagStyler
        {
            if (!$this->objLabelStyle) {
                $this->objLabelStyle = new TagStyler();
            }
            return $this->objLabelStyle;
        }

        /**
         * There is a little bit of a conundrum here. If there is a text assigned to the checkbox, we wrap
         * the checkbox in a label. However, in this situation, it's unclear what to do with the class and style
         * attributes that are for the checkbox. We are going to let the developer use the label styler to make
         * it clear what their intentions are.
         * @return string
         */
        protected function renderLabelAttributes(): string
        {
            $objStyler = new TagStyler();
            $attributes = $this->getHtmlAttributes(null, null, ['title']); // copy tooltip to wrapping label
            $objStyler->setAttributes($attributes);
            $objStyler->override($this->getCheckLabelStyler());

            if ($this->WrapperClass) {
                $objStyler->addCssClass($this->WrapperClass);
            }
            if (!$this->Enabled) {
                $objStyler->addCssClass('disabled');    // add the disabled class to the label for styling
            }
            if (!$this->Display) {
                $objStyler->Display = false;
            }
            if ($this->Inline) {
                $objStyler->addCssClass(Bs\Bootstrap::CHECKBOX_INLINE);
            }
            return $objStyler->renderHtmlAttributes();
        }

        /**
         * Checks whether the post-data submitted for the control is valid or not
         * Right now it tests whether or not the control was marked as required and then tests whether it
         * was checked or not
         * @return bool
         */
        public function validate(): bool
        {
            if ($this->blnRequired) {
                if (!$this->blnChecked) {
                    if ($this->strName) {
                        $this->ValidationError = t($this->strName) . ' ' . t('is required');
                    } else {
                        $this->ValidationError = t('Required');
                    }
                    return false;
                }
            }
            return true;
        }

        /**
         * Retrieves the current state of the control as an associative array.
         *
         * @return array|null An associative array representing the control's state, or null if no state is available.
         */
        public function getState(): ?array
        {
            return array('checked' => $this->Checked);
        }

        /**
         * Restore the state of the control.
         *
         * @param mixed $state
         */
        public function putState(mixed $state): void
        {
            if (isset($state['checked'])) {
                $this->Checked = $state['checked'];
            }
        }

        /**
         * Magic method to retrieve the value of a property.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the property does not exist or is inaccessible.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case "Text": return $this->strText;
                case "TextAlign": return $this->strTextAlign;
                case "WrapperClass": return $this->strWrapperClass;
                case "InputClass": return $this->strInputClass;
                case "Inline": return $this->blnInline;
                case "HtmlEntities": return $this->blnHtmlEntities;
                case "Checked": return $this->blnChecked;
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
         * Magic method to set the value of a property dynamically based on its name.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws Caller If the property name is invalid or cannot be set.
         * @throws InvalidCast If the provided value cannot be cast to the expected type.
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case "Text":
                    try {
                        $val = Type::cast($mixValue, Type::STRING);
                        if ($val !== $this->strText) {
                            $this->strText = $val;
                            $this->blnModified = true;
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "TextAlign":
                    try {
                        $val = Type::cast($mixValue, Type::STRING);
                        if ($val !== $this->strTextAlign) {
                            $this->strTextAlign = $val;
                            $this->blnModified = true;
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "HtmlEntities":
                    try {
                        $this->blnHtmlEntities = Type::cast($mixValue, Type::BOOLEAN);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "Checked":
                    try {
                        $val = Type::cast($mixValue, Type::BOOLEAN);
                        if ($val != $this->blnChecked) {
                            $this->blnChecked = $val;
                            $this->addAttributeScript('prop', 'checked', $val);
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "Inline":
                    try {
                        $this->blnInline = Type::cast($mixValue, Type::BOOLEAN);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "WrapperClass":
                    try {
                        $this->strWrapperClass = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "InputClass":
                    try {
                        $this->strInputClass = Type::cast($mixValue, Type::STRING);
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
         * Returns an array of model connector parameters specific to this class, merged with its parent class parameters.
         * Each parameter defines a configurable property for the model connector, enabling customization options such as
         * label text, alignment, HTML entity encoding, and CSS classes.
         *
         * @return array The array of QModelConnectorParam objects representing the connector configuration options.
         * @throws Caller
         */
        public static function getModelConnectorParams(): array
        {
            return array_merge(parent::getModelConnectorParams(), array(
                new QModelConnectorParam (get_called_class(), 'Text', 'Label on checkbox', Type::STRING),
                new QModelConnectorParam (get_called_class(), 'TextAlign', 'Left or right alignment of a label',
                    QModelConnectorParam::SELECTION_LIST,
                    array(
                        '\\QCubed\\Css\\TextAlignType::RIGHT' => 'Right',
                        '\\QCubed\\Css\\TextAlignType::LEFT' => 'Left'
                    )),
                new QModelConnectorParam (get_called_class(), 'HtmlEntities', 'Whether to apply HTML entities on the label',
                    Type::BOOLEAN),
                new QModelConnectorParam (get_called_class(), 'CssClass',
                    'The css class(es) to apply to the checkbox and label together', Type::STRING)
            ));
        }
    }