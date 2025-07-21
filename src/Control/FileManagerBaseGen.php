<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Control;
use QCubed\Project\Control\ControlBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class FileManagerBaseGen
 *
 * @see FileManagerBase
 * @package QCubed\Plugin
 */

/**
 * Class FileManagerBaseGen
 *
 * Provides a set of properties and methods to manage file operations and configurations through
 * the integration with a jQuery-based file manager.
 */

class FileManagerBaseGen extends Q\Control\Panel
{
    protected $strLanguage = null;
    protected $strRootPath = null;
    protected $strRootUrl = null;
    protected $strTempPath = null;
    protected $strTempUrl = null;
    protected $strDateTimeFormat = null;
    protected $blnIsImageListView = null;
    protected $blnIsListView = null;
    protected $blnIsBoxView = null;
    protected $strUpdatedHash = null;
    protected $blnLockedDocuments = null;
    protected $blnLockedImages = null;

    /**
     * Generates an associative array of jQuery options based on the provided properties.
     * Inherits options from the parent config and adds or overrides specific settings if they are not null.
     *
     * @return array An associative array containing the jQuery options.
     */
    protected function makeJqOptions()
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Language)) {$jqOptions['language'] = $val;}
        if (!is_null($val = $this->RootPath)) {$jqOptions['rootPath'] = $val;}
        if (!is_null($val = $this->RootUrl)) {$jqOptions['rootUrl'] = $val;}
        if (!is_null($val = $this->TempPath)) {$jqOptions['tempPath'] = $val;}
        if (!is_null($val = $this->TempUrl)) {$jqOptions['tempUrl'] = $val;}
        if (!is_null($val = $this->DateTimeFormat)) {$jqOptions['dateTimeFormat'] = $val;}
        if (!is_null($val = $this->IsImageListView)) {$jqOptions['isImageListView'] = $val;}
        if (!is_null($val = $this->IsListView)) {$jqOptions['isListView'] = $val;}
        if (!is_null($val = $this->IsBoxView)) {$jqOptions['isBoxView'] = $val;}
        if (!is_null($val = $this->UpdatedHash)) {$jqOptions['updatedHash'] = $val;}
        if (!is_null($val = $this->LockedDocuments)) {$jqOptions['lockedDocuments'] = $val;}
        if (!is_null($val = $this->LockedImages)) {$jqOptions['lockedImages'] = $val;}
        return $jqOptions;
    }

    /**
     * Returns the name of the jQuery setup function to be used.
     *
     * @return string The name of the jQuery setup function.
     */
    protected function getJqSetupFunction()
    {
        return 'fileManager';
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'Language': return $this->strLanguage;
            case 'RootPath': return $this->strRootPath;
            case 'RootUrl': return $this->strRootUrl;
            case 'TempPath': return $this->strTempPath;
            case 'TempUrl': return $this->strTempUrl;
            case 'DateTimeFormat': return $this->strDateTimeFormat;
            case 'IsImageListView': return $this->blnIsImageListView;
            case 'IsListView': return $this->blnIsListView;
            case 'IsBoxView': return $this->blnIsBoxView;
            case 'UpdatedHash': return $this->strUpdatedHash;
            case 'LockedDocuments': return $this->blnLockedDocuments;
            case 'LockedImages': return $this->blnLockedImages;

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
            case 'RootPath':
                try {
                    $this->strRootPath = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rootPath', $this->strRootPath);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'RootUrl':
                try {
                    $this->strRootUrl = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rootUrl', $this->strRootUrl);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'TempPath':
                try {
                    $this->strTempPath = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tempPath', $this->strTempPath);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'TempUrl':
                try {
                    $this->strTempUrl = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tempUrl', $this->strTempUrl);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'DateTimeFormat':
                try {
                    $this->strDateTimeFormat = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tempUrl', $this->strDateTimeFormat);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'IsImageListView':
                try {
                    $this->blnIsImageListView = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'isImageListView', $this->blnIsImageListView);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'IsListView':
                try {
                    $this->blnIsListView = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'isListView', $this->blnIsListView);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'IsBoxView':
                try {
                    $this->blnIsBoxView = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'isBoxView', $this->blnIsBoxView);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'UpdatedHash':
                try {
                    $this->strUpdatedHash = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'updatedHash', $this->strUpdatedHash);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'LockedDocuments':
                try {
                    $this->blnLockedDocuments = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'lockedDocuments', $this->blnLockedDocuments);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'LockedImages':
                try {
                    $this->blnLockedImages = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'lockedImages', $this->blnLockedImages);
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