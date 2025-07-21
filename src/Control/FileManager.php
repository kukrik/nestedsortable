<?php

namespace QCubed\Plugin\Control;

require ('FileManagerBaseGen.php');

use QCubed as Q;
use QCubed\Control\FormBase;
use QCubed\Control\ControlBase;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\Folder;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class FileManager provides file management functionality such as setting up storage paths,
 * registering required files, and handling file operations.
 */

class FileManager extends FileManagerBaseGen
{
    use Q\Control\DataBinderTrait;

    /** @var array */
    protected $arrSelectedItems = null;
    /** @var string */
    protected $strRootPath = APP_UPLOADS_DIR;
    /** @var string */
    protected $strRootUrl = APP_UPLOADS_URL;
    /** @var string */
    protected $strTempPath = APP_UPLOADS_TEMP_DIR;
    /** @var string */
    protected $strTempUrl = APP_UPLOADS_TEMP_URL;
    /** @var string */
    protected $strStoragePath = '_files';
    /** @var string */
    protected $strFullStoragePath;

    /**
     * @param $objParentObject
     * @param $strControlId
     * @throws Caller
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->registerFiles();
        $this->setup();;
    }

    /**
     * Register JavaScript and CSS files required by the application.
     * @return void
     */
    protected function registerFiles() {
        $this->AddJavascriptFile("https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.9/dayjs.min.js");
        $this->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/qcubed.filemanager.js");
        $this->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/qcubed.uploadhandler.js");
        $this->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/jquery.slimscroll.js");
        $this->AddJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/custom.js");
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/qcubed.filemanager.css");
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/qcubed.uploadhandler.css");
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/custom.css");
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
    }

    /**
     * Set up the full storage path and necessary directories.
     * Ensures the root and storage paths are writable, creates required subdirectories,
     * and initializes URLs based on the server environment.
     *
     * @return void
     */
    protected function setup()
    {
        $this->strFullStoragePath = $this->strTempPath . '/' . $this->strStoragePath;
        $strCreateDirs = ['/thumbnail', '/medium', '/large', '/zip'];

        if (!is_dir($this->strRootPath)) {
            Folder::makeDirectory(QCUBED_PROJECT_DIR . '/assets/upload', 0777);
        }

        if (!is_dir($this->strFullStoragePath)) {
            Folder::makeDirectory($this->strFullStoragePath, 0777);
            foreach ($strCreateDirs as $strCreateDir) {
                Folder::makeDirectory($this->strFullStoragePath . $strCreateDir, 0777);
            }
        }

        if($_SERVER['REQUEST_METHOD'] == "POST") {exit;} // prevent loading entire page in the echo

        $isHttps = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';

        /** clean and check $strRootPath */
        $this->strRootPath = rtrim($this->strRootPath, '\\/');
        $this->strRootPath = str_replace('\\', '/', $this->strRootPath);

        $permissions = fileperms($this->strRootPath);
        $permissions = substr(sprintf('%o', $permissions), -4);
        if (!Folder::isWritable($this->strRootPath)) {
            throw new Caller('Root path "' . $this->strRootPath . '" not writable or not found and
            it has the following directory permissions: ' . $permissions . '. Please set 0755 or 0777 permissions to the
            directory or create a directory "upload" into the location "/project/assets" and grant permissions 0755 or 0777!');
        };

        if (!Folder::isWritable($this->strRootPath)) {
            throw new Caller('Root path "' . $this->strRootPath . '" not writable or not found and
            it has the following directory permissions: ' . $permissions . '. Please set 0755 or 0777 permissions to the
            directory or create a directory "upload" into the location "/project/assets" and grant permissions 0755 or 0777!');
        };

        if (!Folder::isWritable($this->strFullStoragePath) && isset($this->strFullStoragePath)) {
            throw new Caller('Storage path "' . $this->strTempPath . '/' . $this->strStoragePath .
                '" not writable or not found." Please set permissions to the 0777 directory "/project/tmp", the "_files" folder and subfolders!');
        }

        clearstatcache();
        /** clean $strRootUrl */
        $this->strRootUrl = $this->cleanPath($this->strRootUrl);
        /** clean $strTempUrl */
        $this->strTempUrl = $this->cleanPath($this->strTempUrl);
        /** Server hostname. Can set manually if wrong. Don't change! */
        $strHttpHost = $_SERVER['HTTP_HOST'];

        $this->strRootUrl = $isHttps ? 'https' : 'http' . '://' . $strHttpHost . (!empty($this->strRootUrl) ? '/' . $this->strRootUrl : '');
        $this->strTempUrl = $isHttps ? 'https' : 'http' . '://' . $strHttpHost . (!empty($this->strTempUrl) ? '/' . $this->strTempUrl : '');
    }

    /**
     * Cleans a given file path by removing unnecessary characters and sequences.
     *
     * @param string $path The file path to clean.
     * @return string The cleaned file path.
     */
    protected function cleanPath($path)
    {
        $path = trim($path);
        $path = trim($path, '\\/');
        $path = str_replace(array('../', '..\\'), '', $path);
        if ($path == '..') {
            $path = '';
        }
        return str_replace('\\', '/', $path);
    }

    /**
     * Returns the HTML for the control.
     *
     * @return string
     */
    public function getControlHtml()
    {
        $strHtml = '';
        $strHtml .= _nl('<div id="' . $this->ControlId . '">');
        $strHtml .= _nl(_indent('<div class="empty hidden" data-lang="empty_lang">Folder is empty</div>', 1));
        $strHtml .= _nl(_indent('<div class="no-results hidden" data-lang="no_results_lang">No results found</div>', 1));
        $strHtml .= _nl(_indent('<div class="media-items imageList-layout"></div>', 1));
        $strHtml .= _nl(_indent('<div class="media-items list-layout"></div>', 1));
        $strHtml .= _nl(_indent('<div class="media-items box-layout"></div>', 1));
        $strHtml .= '</div>';
       return $strHtml;
    }

    /**
     * Convert a byte size into a human-readable format with appropriate units.
     *
     * @param int $bytes The byte size to be converted.
     * @return string A string representing the human-readable format of the byte size.
     */
    protected static function readableBytes($bytes)
    {
        $i = floor(log($bytes) / log(1024));
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
    }

    /**
     * Rename a file if the new file name does not already exist and the old file exists
     *
     * @param string $old The current name of the file
     * @param string $new The new name for the file
     * @return bool|null True on success, null on failure
     */
    public function rename($old, $new)
    {
        return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
    }

    /**
     * Removes the file name from a given path.
     *
     * @param string $path The path from which the file name should be removed.
     * @return string The path without the file name.
     */
    public function removeFileName($path)
    {
        return substr($path, 0, (int) strrpos($path, '/'));
    }

    /**
     * Get the relative path from the root path
     * @param string $path The absolute path
     * @return string The relative path
     */
    public function getRelativePath($path)
    {
        return substr($path, strlen($this->strRootPath));
    }

    /**
     * Get the file extension from a given path
     * @param string $path The path to the file
     * @return string|null The file extension in lowercase or null if the path is a directory or not a valid file
     */
    public static function getExtension($path)
    {
        if(!is_dir($path) && is_file($path)){
            return strtolower(substr(strrchr($path, '.'), 1));
        }
    }

    /**
     * Get MIME type of a file
     * @param string $path The path to the file
     * @return string|false The MIME type of the file or false on failure
     */
    public static function getMimeType($path)
    {
        if(function_exists('mime_content_type')){
            return mime_content_type($path);
        } else {
            return function_exists('finfo_file') ? finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path) : false;
        }
    }

    /**
     * Get dimensions of an image given its file path
     * @param string $path The file path of the image
     * @return string The dimensions of the image in the format 'width x height', or an empty string if the file is not an image
     */
    public static function getDimensions($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions()))
        {
            $width = (isset($ImageSize[0]) ? $ImageSize[0] : '0');
            $height = (isset($ImageSize[1]) ? $ImageSize[1] : '0');
            $dimensions = $width . ' x ' . $height;
            return $dimensions;
        }
    }

    /**
     * Retrieve a list of supported image file extensions.
     *
     * @return array An array of image file extensions.
     */
    public static function getImageExtensions()
    {
        return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
    }

    /**
     * Generated method overrides the built-in Control method, causing it to not redraw completely. We restore
     * its functionality here.
     */
    public function refresh()
    {
        parent::refresh();
        ControlBase::refresh();
    }

    /**
     * Generates jQuery widget initialization and event handling code for the given control.
     *
     * @return string JavaScript code as a string after adding jQuery widget-specific code.
     */
    protected function makeJqWidget()
    {
        $strJS = parent::makeJqWidget();

        $strCtrlJs = <<<FUNC
jQuery('#{$this->ControlId}').on("selectablestop", function (event, ui) {
    const items = jQuery('#{$this->ControlId}').find(".ui-selected");
    const result = [];
    for (var i = 0, len = items.length; i < len; i++) {
        const item = items[i],
            itemDetails = {
                "data-id": item.getAttribute("data-id"),
                "data-name": item.getAttribute("data-name"),
                "data-type": item.getAttribute("data-type"),
                "data-item-type": item.getAttribute("data-item-type"),
                "data-path": item.getAttribute("data-path"),
                "data-extension": item.getAttribute("data-extension"),
                "data-mimetype": item.getAttribute("data-mime-type"),
                "data-dimensions": item.getAttribute("data-dimensions"),
                "data-size": item.getAttribute("data-size"),
                "data-date": item.getAttribute("data-date"),
                "data-dimensions": item.getAttribute("data-dimensions"),
                "data-locked": item.getAttribute("data-locked"),
                "data-activities-locked": item.getAttribute("data-activities-locked")
        };
        result.push(itemDetails);
    }
    
    qcubed.getFileInfo(result);
    const str = JSON.stringify(result);
    console.log(str);
    qcubed.recordControlModification("$this->ControlId", "_SelectedItems", str);
})
FUNC;
        Application::executeJavaScript($strCtrlJs, Application::PRIORITY_HIGH);

        return $strJS;
    }

    /**
     * Generates and returns the ending script for the control, including
     * additional JavaScript for making the control selectable.
     *
     * @return string The assembled end script.
     */
    public function getEndScript()
    {
        $strJS = parent::getEndScript();

        $strCtrlJs = <<<FUNC
jQuery('#{$this->ControlId}').selectable({filter:'[data-type="media-item"]', autoRefresh: true})
FUNC;
        Application::executeJavaScript($strCtrlJs, Application::PRIORITY_HIGH);

        return $strJS;
    }

    /**
     * @param $strName
     * @return array|bool|callable|float|int|mixed|string|null
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'SelectedItems': return $this->arrSelectedItems;
            case "RootPath": return $this->strRootPath;
            case "RootUrl": return $this->strRootUrl;
            case "TempPath": return $this->strTempPath;
            case "TempUrl": return $this->strTempUrl;
            case "StoragePath": return $this->strStoragePath;
            case "DateTimeFormat": return $this->strDateTimeFormat;

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
     * @param $strName
     * @param $mixValue
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case '_SelectedItems': // Internal only. Do not use. Used by JS above to track selections.
                try {
                    $data = Type::cast($mixValue, Type::STRING);
                    $this->arrSelectedItems = $data;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
            case "RootPath":
                try {
                    $this->strRootPath = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "RootUrl":
                try {
                    $this->strRootUrl = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "TempPath":
                try {
                    $this->strTempPath = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "TempUrl":
                try {
                    $this->strTempUrl = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "StoragePath":
                try {
                    $this->strStoragePath = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
            case "DateTimeFormat":
                try {
                    $this->strDateTimeFormat = Type::Cast($mixValue, Type::STRING);
                    $this->blnModified = true;
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
}
