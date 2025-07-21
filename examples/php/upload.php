<?php

require_once('../qcubed.inc.php');
require_once ('../../src/Control/FileHandler.php');

use QCubed\Plugin\FileHandler;
use QCubed\Project\Application;

$options = array(
    //'ImageResizeQuality' => 75, // Defult 85
    //'ImageResizeFunction' => 'imagecopyresized', // Default imagecopyresampled
    //'ImageResizeSharpen' => false, // Default true
    //'TempFolders' =>  ['thumbnail', 'medium', 'large'], // Please read the UploadHandler description and manual
    //'ResizeDimensions' => [320, 480, 1500], // Please read the UploadHandler description and manual
    //'DestinationPath' => null, // Please read the UploadHandler description and manual
    //'AcceptFileTypes' => ['gif', 'jpg', 'jpeg', 'png', 'pdf', 'ppt', 'docx', 'xlsx', 'txt', 'mp4', 'mov', 'svg'], // Default null
    'DestinationPath' => !empty($_SESSION["filePath"]) ? $_SESSION["filePath"] : null, // Default null
    //'MaxFileSize' => 1024 * 1024 * 2 // 2 MB // Default null
    //'UploadExists' => 'overwrite', // increment || overwrite Default 'increment'
);


/**
 * CustomFileUploadHandler class extends FileHandler to provide additional functionality
 * for handling file uploads, particularly for managing uploaded file metadata and updating
 * file records without folder IDs.
 */
class CustomFileUploadHandler extends FileHandler
{
    protected function uploadInfo()
    {
        parent::uploadInfo();

        if ($this->options['FileError'] == 0) {
            $obj = new Files();
            $obj->setName(basename($this->options['FileName']));
            $obj->setType('file');
            $obj->setPath($this->getRelativePath($this->options['FileName']));
            $obj->setDescription(null);
            $obj->setExtension($this->getExtension($this->options['FileName']));
            $obj->setMimeType($this->getMimeType($this->options['FileName']));
            $obj->setSize($this->options['FileSize']);
            $obj->setMtime(filemtime($this->options['FileName']));
            $obj->setDimensions($this->getDimensions($this->options['FileName']));
            $obj->setWidth($this->getImageWidth($this->options['FileName']));
            $obj->setHeight($this->getImageHeight($this->options['FileName']));
            $obj->save(true);
        }

        $filesWithoutFolder = [];

        // Find files files without a folder ID
        foreach (Files::loadAll() as $file) {
            if ($file->FolderId === null) {
                $filesWithoutFolder[] = $file->Id;
            }
        }

        // Update folderId for files without a folder ID
        foreach ($filesWithoutFolder as $fileId) {
            $file = Files::loadById($fileId);
            $file->setFolderId($_SESSION['folderId']);
            $file->save();
        }
    }

    /**
     * Get the width of an image
     * @param string $path The file path of the image
     * @return int|string The width of the image or '0' if unable to determine
     */
    public static function getImageWidth($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions())) {
            $width = (isset($ImageSize[0]) ? $ImageSize[0] : '0');
            return $width;
        }
    }

    /**
     * Get the height of an image
     * @param string $path The file path to the image
     * @return int The height of the image in pixels, or 0 if height cannot be determined
     */
    public static function getImageHeight($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions())) {
            $height = (isset($ImageSize[1]) ? $ImageSize[1] : '0');
            return $height;
        }
    }

    /**
     * Retrieves a list of common image file extensions.
     *
     * @return array An array of common image file extensions.
     */
    public static function getImageExtensions()
    {
        return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
    }
}


$objHandler = new CustomFileUploadHandler($options);