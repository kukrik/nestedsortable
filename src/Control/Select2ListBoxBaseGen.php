<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Control;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Project\Control\ControlBase;
use QCubed\Type;

/**
 * Class Select2Gen
 * @package QCubed\Plugin
 */

/**
 * @see Select2Base
 * @package Controls\Base
 * @property string $ContainerWidth Controls the width style attribute of the Select2 container div. The following values are supported: "off", "element", "copy", "resolve"
 * @property integer $MinimumInputLength Number of characters necessary to start a search
 * @property integer $MaximumInputLength Number of characters that can be entered for an input
 * @property integer $MinimumResultsForSearch The minimum number of results that must be initially (after opening the dropdown for the first time) populated in order to keep the search field. This is useful for cases where local data is used with just a few results, in which case the search box is not very useful and wastes screen space.
 * @property integer $MaximumSelectionSize The maximum number of items that can be selected in a multi-select control. If this number is less than 1 selection is not limited. Once the number of selected items reaches the maximum specified the contents of the dropdown will be populated by the formatSelectionTooBig function
 * @property string $Placeholder Initial value that is selected if no other selection is made. The placeholder can also be specified as a data-placeholder attribute on the select or input element that Select2 is attached to.
 * @property mixed $PlaceholderOption When attached to a select resolves the option that should be used as the placeholder. Can either be a function which given the select element should return the option element or a string first to indicate that the first option should be used. This option is useful when Select2's default of using the first option only if it has no value and no text is not suitable.
 * @property string $Separator Separator character or string used to delimit ids in value attribute of the multi-valued selects. The default delimiter is the , character.
 * @property boolean $AllowClear Whether or not a clear button is displayed when the select box has a selection. The button, when clicked, resets the value of the select box back to the placeholder, thus this option is only available when the placeholder is specified.
 * @property boolean $CloseOnSelect If set to false the dropdown is not closed after a selection is made, allowing for rapid selection of multiple items. By default this option is disabled.
 * @property boolean $OpenOnEnter If set to true the dropdown is opened when the user presses the enter key and Select2 is closed. By default this option is enabled.
 * @property callable $Matcher Used to determine whether or not the search term matches an option when a built-in query function is used. The built in query function is used when Select2 is attached to a select, or the local or tags helpers are used.
 * @property callable $SortResults    Used to sort the results list for searching right before display. Useful for sorting matches by relevance to the user's search term. SortResults(results, container, query)
 * @property callable $FormatResultCssClass Function used to add css classes to result elements
 * @property callable $FormatNoMatches Function used to render the "No matches" message
 * @property callable $FormatSearching Function used to render the "Searching..." message that is displayed while search is in progress
 * @property callable $FormatInputTooShort Function used to render the "Search input too short" message
 * @property callable $FormatInputTooLong String containing "Search input too long" message, or Function used to render the message. FormatInputTooLong(term, maxLength)
 * @property callable $FormatAjaxError String containing "Loading Failed" message, or Function used to render the message formatAjaxError(jqXHR, textStatus, errorThrown)
 * @property callable $FormatSelectionTooBig Function used to render the "You cannot select any more choices" message
 * @property callable $FormatLoadMore Function used to render the "Loading more results..." message
 * @property callable $Tokenizer A tokenizer function can process the input typed into the search field after every keystroke and extract and select choices. This is useful, for example, in tagging scenarios where the user can create tags quickly by separating them with a comma or a space instead of pressing enter.
 * @property array $TokenSeparators An array of strings that define token separators for the default tokenizer function. By default, this option is set to an empty array which means tokenization using the default tokenizer is disabled. Usually it is sensible to set this option to a value similar to [",", " "]
 * @property callable $Query Function used to query results for the search term.
 * @property mixed $Ajax Options for the built in ajax query function. This object acts as a shortcut for having to manually write a function that performs ajax requests. The built-in function supports more advanced features such as throttling and dropping out-of-order responses.
 * @property mixed $Data Options for the built in query function that works with arrays.
 * @property mixed $Tags Puts Select2 into 'tagging' mode where the user can add new choices and pre-existing tags are provided via this options attribute which is either an array or a function that returns an array of objects or strings
 * @property mixed $ContainerCss Inline css that will be added to select2's container. Either an object containing css property/value key pairs or a function that returns such an object.
 * @property mixed $ContainerCssClass Css class that will be added to select2's container tag
 * @property mixed $DropdownCss Inline css that will be added to select2's dropdown container. Either an object containing css property/value key pairs or a function that returns such an object.
 * @property mixed $DropdownCssClass Css class that will be added to select2's dropdown container
 * @property boolean $DropdownAutoWidth When set to true attempts to automatically size the width of the dropdown based on content inside.
 * @property mixed $AdaptContainerCssClass    Function that filters/renames css classes as they are copied from the source tag to the select2 container tag. AdaptContainerCssClass(clazz)
 * @property mixed $AdaptDropdownCssClass Function that filters/renames css classes as they are copied from the source tag to the select2 dropdown tag. AdaptDropdownCssClass(clazz)
 * @property callable $EscapeMarkup Function used to post-process markup returned from formatter functions. By default this function escapes html entities to prevent javascript injection.
 * @property boolean $SelectOnClose Set to true if you want Select2 to select the currently highlighted option when it is blurred.
 * @property integer $LoadMorePadding Defines how many pixels need to be below the fold before the next page is loaded. The default value is 0 which means the result list needs to be scrolled all the way to the bottom for the next page of results to be loaded. This option can be used to trigger the load sooner, possibly resulting in a smoother user experience.
 * @property callable $NextSearchTerm Function used to determine what the next search term should be.
 * @property callable $DropdownParent  Allows you to customize placement of the dropdown.
 * @property string Language Specify the language used for Select2 messages.
 * @property callable $TemplateResult Customizes the way that search results are rendered.
 * @property callable $TemplateSelection Customizes the way that selections are rendered.
 * @property boolean $ScrollAfterSelect If true, resolves issue for multiselects using closeOnSelect: false that caused the list of results to scroll to the first selection after each select/unselect (see https://github.com/select2/select2/pull/5150). This behaviour was intentional to deal with infinite scroll UI issues (if you need this behavior, set false) but it created an issue with multiselect dropdown boxes of fixed length. This pull request adds a configurable option to toggle between these two desirable behaviours.
 * @property callable $CreateTag Creates a new selectable choice from user's search term. Allows creation of choices not available via the query function. Useful when the user can create choices on the fly, eg for the 'tagging' usecase.
 * @property callable $InsertTag Called when select2 is created to allow the user to initialize the selection based on the value of the element select2 is attached to.
 * @property string $Theme Allows you to set the theme.
 */
class Select2ListBoxBaseGen extends Q\Project\Control\ListBox
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var string */
    protected $strContainerWidth = null;
    /** @var integer */
    protected $intMinimumInputLength = null;
    /** @var integer */
    protected $intMaximumInputLength = null;
    /** @var integer */
    protected $intMinimumResultsForSearch = null;
    /** @var integer */
    protected $intMaximumSelectionSize = null;
    /** @var string */
    protected $strPlaceholder = null;
    /** @var callable */
    protected $mixPlaceholderOption = null;
    /** @var string */
    protected $strSeparator = null;
    /** @var boolean */
    protected $blnAllowClear = null;
    /** @var boolean */
    protected $blnCloseOnSelect = null;
    /** @var boolean */
    protected $blnOpenOnEnter = null;
    /** @var callable */
    protected $mixMatcher = null;
    /** @var callable */
    protected $mixSortResults = null;
    /** @var callable */
    protected $mixFormatResultCssClass = null;
    /** @var callable */
    protected $mixFormatNoMatches = null;
    /** @var callable */
    protected $mixFormatSearching = null;
    /** @var callable */
    protected $mixFormatAjaxError = null;
    /** @var callable */
    protected $mixFormatInputTooShort = null;
    /** @var callable */
    protected $mixFormatInputTooLong = null;
    /** @var callable */
    protected $mixFormatSelectionTooBig = null;
    /** @var callable */
    protected $mixFormatLoadMore = null;
    /** @var callable */
    protected $mixTokenizer = null;
    /** @var array */
    protected $arrTokenSeparators = null;
    /** @var callable */
    protected $mixQuery = null;
    /** @var mixed */
    protected $mixAjax = null;
    /** @var mixed */
    protected $mixData = null;
    /** @var mixed */
    protected $mixTags = null;
    /** @var mixed */
    protected $mixContainerCss = null;
    /** @var mixed */
    protected $mixContainerCssClass = null;
    /** @var mixed */
    protected $mixDropdownCss = null;
    /** @var mixed */
    protected $mixDropdownCssClass = null;
    /** @var boolean */
    protected $blnDropdownAutoWidth = null;
    /** @var mixed */
    protected $mixAdaptContainerCssClass = null;
    /** @var mixed */
    protected $mixAdaptDropdownCssClass = null;
    /** @var callable */
    protected $mixEscapeMarkup = null;
    /** @var boolean */
    protected $blnSelectOnClose = null;
    /** @var integer */
    protected $intLoadMorePadding = null;
    /** @var callable */
    protected $mixNextSearchTerm = null;
    /** @var callable */
    protected $mixDropdownParent = null;
    /** @var string */
    protected $strLanguage = null;
    /** @var callable */
    protected $mixTemplateResult = null;
    /** @var callable */
    protected $mixTemplateSelection = null;
    /** @var boolean */
    protected $blnScrollAfterSelect = null;
    /** @var callable */
    protected $mixCreateTag = null;
    /** @var callable */
    protected $mixInsertTag = null;
    /** @var string */
    protected $strTheme = null;

    protected function makeJqOptions()
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->ContainerWidth)) {$jqOptions['width'] = $val;}
        if (!is_null($val = $this->MinimumInputLength)) {$jqOptions['minimumInputLength'] = $val;}
        if (!is_null($val = $this->MaximumInputLength)) {$jqOptions['maximumInputLength'] = $val;}
        if (!is_null($val = $this->MaximumSelectionSize)) {$jqOptions['maximumSelectionSize'] = $val;}
        if (!is_null($val = $this->MinimumResultsForSearch)) {$jqOptions['minimumResultsForSearch'] = $val;}
        if (!is_null($val = $this->MaximumSelectionSize)) {$jqOptions['maximumSelectionSize'] = $val;}
        if (!is_null($val = $this->Placeholder)) {$jqOptions['placeholder'] = $val;}
        if (!is_null($val = $this->PlaceholderOption)) {$jqOptions['placeholderOption'] = $val;}
        if (!is_null($val = $this->Separator)) {$jqOptions['separator'] = $val;}
        if (!is_null($val = $this->AllowClear)) {$jqOptions['allowClear'] = $val;}
        if (!is_null($val = $this->CloseOnSelect)) {$jqOptions['closeOnSelect'] = $val;}
        if (!is_null($val = $this->OpenOnEnter)) {$jqOptions['openOnEnter'] = $val;}
        if (!is_null($val = $this->Matcher)) {$jqOptions['matcher'] = $val;}
        if (!is_null($val = $this->SortResults)) {$jqOptions['sortResults'] = $val;}
        if (!is_null($val = $this->FormatResultCssClass)) {$jqOptions['formatResultCssClass'] = $val;}
        if (!is_null($val = $this->FormatNoMatches)) {$jqOptions['formatNoMatches'] = $val;}
        if (!is_null($val = $this->FormatSearching)) {$jqOptions['formatSearching'] = $val;}
        if (!is_null($val = $this->FormatAjaxError)) {$jqOptions['formatAjaxError'] = $val;}
        if (!is_null($val = $this->FormatInputTooShort)) {$jqOptions['formatInputTooShort'] = $val;}
        if (!is_null($val = $this->FormatInputTooLong)) {$jqOptions['formatInputTooLong'] = $val;}
        if (!is_null($val = $this->FormatSelectionTooBig)) {$jqOptions['formatSelectionTooBig'] = $val;}
        if (!is_null($val = $this->FormatLoadMore)) {$jqOptions['formatLoadMore'] = $val;}
        if (!is_null($val = $this->Tokenizer)) {$jqOptions['tokenizer'] = $val;}
        if (!is_null($val = $this->TokenSeparators)) {$jqOptions['tokenSeparators'] = $val;}
        if (!is_null($val = $this->Query)) {$jqOptions['query'] = $val;}
        if (!is_null($val = $this->Ajax)) {$jqOptions['ajax'] = $val;}
        if (!is_null($val = $this->Data)) {$jqOptions['data'] = $val;}
        if (!is_null($val = $this->Tags)) {$jqOptions['tags'] = $val;}
        if (!is_null($val = $this->ContainerCss)) {$jqOptions['containerCss'] = $val;}
        if (!is_null($val = $this->ContainerCssClass)) {$jqOptions['containerCssClass'] = $val;}
        if (!is_null($val = $this->DropdownCss)) {$jqOptions['dropdownCss'] = $val;}
        if (!is_null($val = $this->DropdownCssClass)) {$jqOptions['dropdownCssClass'] = $val;}
        if (!is_null($val = $this->DropdownAutoWidth)) {$jqOptions['dropdownAutoWidth'] = $val;}
        if (!is_null($val = $this->AdaptContainerCssClass)) {$jqOptions['adaptContainerCssClass'] = $val;}
        if (!is_null($val = $this->AdaptDropdownCssClass)) {$jqOptions['adaptDropdownCssClass'] = $val;}
        if (!is_null($val = $this->EscapeMarkup)) {$jqOptions['escapeMarkup'] = $val;}
//        if (!is_null($val = $this->SelectOnClose)) {$jqOptions['selectOnClose'] = $val;}
        if (!is_null($val = $this->LoadMorePadding)) {$jqOptions['loadMorePadding'] = $val;}
        if (!is_null($val = $this->NextSearchTerm)) {$jqOptions['nextSearchTerm'] = $val;}
        if (!is_null($val = $this->DropdownParent)) {$jqOptions['dropdownParent'] = $val;}
        if (!is_null($val = $this->Language)) {$jqOptions['Language'] = $val;}
        if (!is_null($val = $this->TemplateResult)) {$jqOptions['templateResult'] = $val;}
        if (!is_null($val = $this->TemplateSelection)) {$jqOptions['templateSelection'] = $val;}
        if (!is_null($val = $this->ScrollAfterSelect)) {$jqOptions['scrollAfterSelect'] = $val;}
        if (!is_null($val = $this->CreateTag)) {$jqOptions['createTag'] = $val;}
        if (!is_null($val = $this->InsertTag)) {$jqOptions['insertTag'] = $val;}
        if (!is_null($val = $this->Theme)) {$jqOptions['theme'] = $val;}
        return $jqOptions;
    }

    public function getJqSetupFunction()
    {
        return 'select2';
    }


    public function __get($strName)
    {
        switch ($strName) {
            case 'ContainerWidth': return $this->strContainerWidth;
            case 'MinimumInputLength': return $this->intMinimumInputLength;
            case 'MaximumInputLength': return $this->intMaximumInputLength;
            case 'MinimumResultsForSearch': return $this->intMinimumResultsForSearch;
            case 'MaximumSelectionSize': return $this->intMaximumSelectionSize;
            case 'Placeholder': return $this->strPlaceholder;
            case 'PlaceholderOption': return $this->mixPlaceholderOption;
            case 'Separator': return $this->strSeparator;
            case 'AllowClear': return $this->blnAllowClear;
            case 'CloseOnSelect': return $this->blnCloseOnSelect;
            case 'OpenOnEnter': return $this->blnOpenOnEnter;
            case 'Matcher': return $this->mixMatcher;
            case 'SortResults': return $this->mixSortResults;
            case 'FormatResultCssClass': return $this->mixFormatResultCssClass;
            case 'FormatNoMatches': return $this->mixFormatNoMatches;
            case 'FormatSearching': return $this->mixFormatSearching;
            case 'FormatAjaxError': return $this->mixFormatAjaxError;
            case 'FormatInputTooShort': return $this->mixFormatInputTooShort;
            case 'FormatInputTooLong': return $this->mixFormatInputTooLong;
            case 'FormatSelectionTooBig': return $this->mixFormatSelectionTooBig;
            case 'FormatLoadMore': return $this->mixFormatLoadMore;
            case 'Tokenizer': return $this->mixTokenizer;
            case 'TokenSeparators': return $this->arrTokenSeparators;
            case 'Query': return $this->mixQuery;
            case 'Ajax': return $this->mixAjax;
            case 'Data': return $this->mixData;
            case 'Tags': return $this->mixTags;
            case 'ContainerCss': return $this->mixContainerCss;
            case 'ContainerCssClass': return $this->mixContainerCssClass;
            case 'DropdownCss': return $this->mixDropdownCss;
            case 'DropdownCssClass': return $this->mixDropdownCssClass;
            case 'DropdownAutoWidth': return $this->blnDropdownAutoWidth;
            case 'AdaptContainerCssClass': return $this->mixAdaptContainerCssClass;
            case 'AdaptDropdownCssClass': return $this->mixAdaptDropdownCssClass;
            case 'EscapeMarkup': return $this->mixEscapeMarkup;
            case 'SelectOnClose': return $this->blnSelectOnClose;
            case 'LoadMorePadding': return $this->intLoadMorePadding;
            case 'NextSearchTerm': return $this->mixNextSearchTerm;
            case 'DropdownParent': return $this->mixDropdownParent;
            case 'Language': return $this->strLanguage;
            case 'TemplateResult': return $this->mixTemplateResult;
            case 'TemplateSelection': return $this->mixTemplateSelection;
            case 'ScrollAfterSelect': return $this->blnScrollAfterSelect;
            case 'CreateTag': return $this->mixCreateTag;
            case 'InsertTag': return $this->mixInsertTag;
            case 'Theme': return $this->strTheme;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'ContainerWidth':
                try {
                    $this->strContainerWidth = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'width', $this->strContainerWidth);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'MinimumInputLength':
                try {
                    $this->intMinimumInputLength = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'minimumInputLength', $this->intMinimumInputLength);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'MaximumInputLength':
                try {
                    $this->intMaximumInputLength = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'maximumInputLength', $this->intMaximumInputLength);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'MinimumResultsForSearch':
                try {
                    $this->intMinimumResultsForSearch = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'minimumResultsForSearch', $this->intMinimumResultsForSearch);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'MaximumSelectionSize':
                try {
                    $this->intMaximumSelectionSize = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'maximumSelectionSize', $this->intMaximumSelectionSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Placeholder':
                try {
                    $this->strPlaceholder = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'placeholder', $this->strPlaceholder);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'PlaceholderOption':
                $this->mixPlaceholderOption = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'placeholder', $mixValue);
                break;
            case 'Separator':
                try {
                    $this->strSeparator = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'separator', $this->strSeparator);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'AllowClear':
                try {
                    $this->blnAllowClear = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'allowClear', $this->blnAllowClear);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'CloseOnSelect':
                try {
                    $this->blnCloseOnSelect = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'closeOnSelect', $this->blnCloseOnSelect);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'OpenOnEnter':
                try {
                    $this->blnOpenOnEnter = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'openOnEnter', $this->blnOpenOnEnter);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Matcher':
                try {
                    $this->mixMatcher = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'matcher', $this->mixMatcher);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'SortResults':
                try {
                    $this->mixSortResults = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'matcher', $this->mixSortResults);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'FormatResultCssClass':
                try {
                    $this->mixFormatResultCssClass = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'formatResultCssClass', $this->mixFormatResultCssClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'FormatNoMatches':
                try {
                    $this->mixFormatNoMatches = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'formatNoMatches', $this->mixFormatNoMatches);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'FormatSearching':
                try {
                    $this->mixFormatSearching = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'formatSearching', $this->mixFormatSearching);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'FormatAjaxError':
                try {
                    $this->mixFormatAjaxError = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'formatSearching', $this->mixFormatSearching);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'FormatInputTooShort':
                try {
                    $this->mixFormatInputTooShort = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'formatInputTooShort', $this->mixFormatInputTooShort);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'FormatInputTooLong':
                try {
                    $this->mixFormatInputTooLong = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'formatInputTooLong', $this->mixFormatInputTooLong);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'FormatSelectionTooBig':
                try {
                    $this->mixFormatSelectionTooBig = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'formatSelectionTooBig', $this->mixFormatSelectionTooBig);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'FormatLoadMore':
                try {
                    $this->mixFormatLoadMore = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'formatLoadMore', $this->mixFormatLoadMore);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Tokenizer':
                try {
                    $this->mixTokenizer = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tokenizer', $this->mixTokenizer);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'TokenSeparators':
                try {
                    $this->arrTokenSeparators = Type::Cast($mixValue, Type::ARRAY_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tokenSeparators', $this->arrTokenSeparators);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Query':
                try {
                    $this->mixQuery = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'query', $this->mixQuery);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Ajax':
                $this->mixAjax = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'ajax', $mixValue);
                break;
            case 'Data':
                $this->mixData = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'data', $mixValue);
                break;
            case 'Tags':
                $this->mixTags = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tags', $mixValue);
                break;
            case 'ContainerCss':
                $this->mixContainerCss = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'containerCss', $mixValue);
                break;
            case 'ContainerCssClass':
                $this->mixContainerCssClass = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'containerCssClass', $mixValue);
                break;
            case 'DropdownCss':
                $this->mixDropdownCss = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'dropdownCss', $mixValue);
                break;
            case 'DropdownCssClass':
                $this->mixDropdownCssClass = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'dropdownCssClass', $mixValue);
                break;
            case 'DropdownAutoWidth':
                try {
                    $this->blnDropdownAutoWidth = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'dropdownAutoWidth', $this->blnDropdownAutoWidth);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'AdaptContainerCssClass':
                $this->mixAdaptContainerCssClass = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'adaptContainerCssClass', $mixValue);
                break;
            case 'AdaptDropdownCssClass':
                $this->mixAdaptDropdownCssClass = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'adaptDropdownCssClass', $mixValue);
                break;
            case 'EscapeMarkup':
                try {
                    $this->mixEscapeMarkup = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'escapeMarkup', $this->mixEscapeMarkup);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'SelectOnClose':
                try {
                    $this->blnSelectOnClose = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'selectOnClose', $this->blnSelectOnClose);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'LoadMorePadding':
                try {
                    $this->intLoadMorePadding = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'loadMorePadding', $this->intLoadMorePadding);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'NextSearchTerm':
                try {
                    $this->mixNextSearchTerm = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'nextSearchTerm', $this->mixNextSearchTerm);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'DropdownParent':
                try {
                    $this->mixDropdownParent = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'dropdownParent', $this->mixDropdownParent);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'Language':
                try {
                    $this->strLanguage = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'language', $this->blnDropdownAutoWidth);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'TemplateResult':
                try {
                    $this->mixTemplateResult = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'templateResult', $this->mixTemplateResult);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'TemplateSelection':
                try {
                    $this->mixTemplateSelection = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'templateSelection', $this->mixTemplateSelection);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'ScrollAfterSelect':
                try {
                    $this->blnScrollAfterSelect = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'scrollAfterSelect', $this->blnScrollAfterSelect);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'CreateTag':
                try {
                    $this->mixCreateTag = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'createTag', $this->mixCreateTag);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'InsertTag':
                try {
                    $this->mixInsertTag = Type::Cast($mixValue, Type::CALLABLE_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'insertTag', $this->mixInsertTag);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case 'Theme':
                try {
                    $this->strTheme = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'theme', $this->strTheme);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
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
     * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
     * used by the ModelConnector designer dialog to display a list of options for the control.
     * @return QModelConnectorParam[]
     **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array());
    }
}


