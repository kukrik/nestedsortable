<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Control;
use QCubed\Bootstrap as Bs;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class FileUploadHandlerBaseGen
 *
 * @see FileUploadHandlerBase
 * @package QCubed\Plugin
 */

/**
 * Class FileUploadHandlerBaseGen
 *
 * This class represents a file upload handler inheriting from Q\Control\Panel.
 *
 * @property string $Language Specifies the language setting for the upload handler.
 * @property boolean $MultipleUploads Determines if multiple file uploads are allowed.
 * @property boolean $ShowIcons Indicates whether to show icons for files.
 * @property array $AcceptFileTypes Array of acceptable file types for upload.
 * @property integer $MaxNumberOfFiles Maximum number of files allowed for upload.
 * @property integer $MaxFileSize Maximum file size allowed for upload.
 * @property integer $MinFileSize Minimum file size allowed for upload.
 * @property boolean $ChunkUpload Indicates whether to enable chunked uploading.
 * @property integer $MaxChunkSize Maximum size for each file chunk in bytes.
 * @property integer $LimitConcurrentUploads Limit on the number of concurrent uploads.
 * @property string $Url URL where files will be uploaded.
 * @property integer $PreviewMaxWidth Maximum width of the preview thumbnail.
 * @property integer $PreviewMaxHeight Maximum height of the preview thumbnail.
 * @property boolean $WithCredentials Whether to include credentials (e.g., cookies) in cross-origin requests.
 */

class FileUploadHandlerBaseGen extends Q\Control\Panel
{
    /** @var string */
    protected $strLanguage = null;
    /** @var boolean */
    protected $blnMultipleUploads = null;
    /** @var boolean */
    protected $blnShowIcons = null;
    /** @var array */
    protected $arrAcceptFileTypes = null;
    /** @var integer */
    protected $intMaxNumberOfFiles = null;
    /** @var integer */
    protected $intMaxFileSize = null;
    /** @var integer */
    protected $intMinFileSize = null;
    /** @var boolean */
    protected $blnChunkUpload = null;
    /** @var integer */
    protected $intMaxChunkSize = null;
    /** @var integer */
    protected $intLimitConcurrentUploads = null;
    /** @var string */
    protected $strUrl = null;
    /** @var string */
    protected $intPreviewMaxWidth = null;
    /** @var string */
    protected $intPreviewMaxHeight = null;

    /**
     * Constructs and returns an array of jQuery options based on the current properties of the class.
     *
     * This method extends the parent class's MakeJqOptions method by adding additional options such as:
     * - language
     * - multipleUploads
     * - showIcons
     * - acceptFileTypes
     * - maxNumberOfFiles
     * - maxFileSize
     * - minFileSize
     * - chunkUpload
     * - maxChunkSize
     * - limitConcurrentUploads
     * - url
     * - previewMaxWidth
     * - previewMaxHeight
     *
     * Each option is added to the array only if its corresponding property is not null.
     *
     * @return array The constructed array of jQuery options.
     */
    protected function makeJqOptions()
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Language)) {$jqOptions['language'] = $val;}
        if (!is_null($val = $this->MultipleUploads)) {$jqOptions['multipleUploads'] = $val;}
        if (!is_null($val = $this->ShowIcons)) {$jqOptions['showIcons'] = $val;}
        if (!is_null($val = $this->AcceptFileTypes)) {$jqOptions['acceptFileTypes'] = $val;}
        if (!is_null($val = $this->MaxNumberOfFiles)) {$jqOptions['maxNumberOfFiles'] = $val;}
        if (!is_null($val = $this->MaxFileSize)) {$jqOptions['maxFileSize'] = $val;}
        if (!is_null($val = $this->MinFileSize)) {$jqOptions['minFileSize'] = $val;}
        if (!is_null($val = $this->ChunkUpload)) {$jqOptions['chunkUpload'] = $val;}
        if (!is_null($val = $this->MaxChunkSize)) {$jqOptions['maxChunkSize'] = $val;}
        if (!is_null($val = $this->LimitConcurrentUploads)) {$jqOptions['limitConcurrentUploads'] = $val;}
        if (!is_null($val = $this->Url)) {$jqOptions['url'] = $val;}
        if (!is_null($val = $this->PreviewMaxWidth)) {$jqOptions['previewMaxWidth'] = $val;}
        if (!is_null($val = $this->PreviewMaxHeight)) {$jqOptions['previewMaxHeight'] = $val;}
        return $jqOptions;
    }

    /**
     * Returns the name of the jQuery function to be used for setup.
     *
     * This method specifies the function 'uploadHandler' which is used for handling
     * file uploads in a jQuery setup.
     *
     * @return string The name of the jQuery setup function.
     */
    public function getJqSetupFunction()
    {
        return 'uploadHandler';
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'Language': return t($this->strLanguage);
            case 'MultipleUploads': return $this->blnMultipleUploads;
            case 'ShowIcons': return $this->blnShowIcons;
            case 'AcceptFileTypes': return $this->arrAcceptFileTypes;
            case 'MaxNumberOfFiles': return $this->intMaxNumberOfFiles;
            case 'MaxFileSize': return $this->intMaxFileSize;
            case 'MinFileSize': return $this->intMinFileSize;
            case 'ChunkUpload': return $this->blnChunkUpload;
            case 'MaxChunkSize': return $this->intMaxChunkSize;
            case 'LimitConcurrentUploads': return $this->intLimitConcurrentUploads;
            case 'Url': return $this->strUrl;
            case 'PreviewMaxWidth': return $this->intPreviewMaxWidth;
            case 'PreviewMaxHeight': return $this->intPreviewMaxHeight;

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
            case 'Language':
                try {
                    $this->strLanguage = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'language', $this->strLanguage);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'MultipleUploads':
                try {
                    $this->blnMultipleUploads = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'multipleUploads', $this->blnMultipleUploads);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ShowIcons':
                try {
                    $this->blnShowIcons = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'showIcons', $this->blnShowIcons);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'AcceptFileTypes':
                try {
                    $this->arrAcceptFileTypes = Type::Cast($mixValue, Type::ARRAY_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'acceptFileTypes', $this->arrAcceptFileTypes);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'MaxNumberOfFiles':
                try {
                    $this->intMaxNumberOfFiles = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'maxNumberOfFiles', $this->intMaxNumberOfFiles);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'MaxFileSize':
                try {
                    $this->intMaxFileSize = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'maxFileSize', $this->intMaxFileSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'MinFileSize':
                try {
                    $this->intMinFileSize = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'minFileSize', $this->intMinFileSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ChunkUpload':
                try {
                    $this->blnChunkUpload = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'chunkUpload', $this->blnChunkUpload);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'MaxChunkSize':
                try {
                    $this->intMaxChunkSize = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'maxChunkSize', $this->intMaxChunkSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'LimitConcurrentUploads':
                try {
                    $this->intLimitConcurrentUploads = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'limitConcurrentUploads', $this->intLimitConcurrentUploads);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Url':
                try {
                    $this->strUrl = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'url', $this->strUrl);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'PreviewMaxWidth':
                try {
                    $this->intPreviewMaxWidth = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'previewMaxWidth', $this->intPreviewMaxWidth);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'PreviewMaxHeight':
                try {
                    $this->intPreviewMaxHeight = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'previewMaxHeight', $this->intPreviewMaxHeight);
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
     * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
     * used by the ModelConnector designer dialog to display a list of options for the control.
     * @return QModelConnectorParam[]
     **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array());
    }
}


