<?php

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Control;
use QCubed\Project\Control\ControlBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class FileInfoBaseGen
 * @see FileInfo
 * @package QCubed\Plugin
 */

/**
 * @property string $RootPath
 * @property string $RootUrl
 * @property string $TempPath
 * @property string $TempUrl
 *
 * @package QCubed\Plugin
 */

class FileInfoBaseGen extends Q\Control\Panel
{
    /** @var string */

    protected $strRootPath = null;
    protected $strRootUrl = null;
    protected $strTempPath = null;
    protected $strTempUrl = null;
    protected $intFileId = null;
    protected $strFilePath = null;
    protected $strFileExtension = null;

    protected function makeJqOptions()
    {
        $jqOptions = parent::MakeJqOptions();
        //if (!is_null($val = $this->Language)) {$jqOptions['language'] = $val;}
        if (!is_null($val = $this->RootPath)) {$jqOptions['rootPath'] = $val;}
        if (!is_null($val = $this->RootUrl)) {$jqOptions['rootUrl'] = $val;}
        if (!is_null($val = $this->TempPath)) {$jqOptions['tempPath'] = $val;}
        if (!is_null($val = $this->TempUrl)) {$jqOptions['tempUrl'] = $val;}
        return $jqOptions;
    }

    protected function getJqSetupFunction()
    {
        return 'fileInfo';
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'RootPath': return $this->strRootPath;
            case 'RootUrl': return $this->strRootUrl;
            case 'TempPath': return $this->strTempPath;
            case 'TempUrl': return $this->strTempUrl;

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


