<?php

require_once('../qcubed.inc.php');
require('../../src/Control/GalleryFileHandler.php');

use QCubed\Plugin\Control\GalleryFileHandler;

$options = array(
    //'ImageResizeQuality' => 75, // Defult 85
    //'ImageResizeFunction' => 'imagecopyresized', // Default imagecopyresampled
    //'ImageResizeSharpen' => false, // Default true
    //'TempFolders' =>  ['thumbnail', 'medium', 'large'], // Please read the FileHandler description and manual
    //'ResizeDimensions' => [320, 480, 1500], // Please read the FileHandler description and manual
    //'DestinationPath' => null, // Please read the FileHandler description and manual
    'AcceptFileTypes' => ['jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif'], // Default null
    'DestinationPath' => !empty($_SESSION["path"]) ? $_SESSION["path"] : null, // Default null
    //'MaxFileSize' => 1024 * 1024 * 2, // 2 MB // Default null
    //'MinFileSize' => 500000, // 500 kb // Default null
    //'UploadExists' => 'overwrite', // increment || overwrite Default 'increment'
);

/**
 * CustomGalleryFileHandler extends the base GalleryFileHandler to provide
 * specific processing and handling of gallery files, including setting file
 * metadata and managing related album entries.
 */
class CustomGalleryFileHandler extends GalleryFileHandler
{
    protected function uploadInfo()
    {
        parent::uploadInfo();

        if ($this->options['FileError'] == 0) {
            $objFile = new Files();
            $objFile->setFolderId($_SESSION['folderId']);
            $objFile->setName(basename($this->options['FileName']));
            $objFile->setPath($this->getRelativePath($this->options['FileName']));
            $objFile->setType("file");
            $objFile->setDescription(null);
            $objFile->setExtension($this->getExtension($this->options['FileName']));
            $objFile->setMimeType($this->getMimeType($this->options['FileName']));
            $objFile->setSize($this->options['FileSize']);
            $objFile->setMtime(filemtime($this->options['FileName']));
            $objFile->setDimensions($this->getDimensions($this->options['FileName']));
            $objFile->setWidth($this->getImageWidth($this->options['FileName']));
            $objFile->setHeight($this->getImageHeight($this->options['FileName']));
            $objFile->setLockedFile(1);
            $objFile->setActivitiesLocked(1);
            $objFile->save(true);

            $gallerySettings = GallerySettings::loadById($_SESSION['groupId']);

            $objAlbum = new Album();
            $objAlbum->setGalleryListId($_SESSION['id']);
            $objAlbum->setGalleryGroupTitleId($_SESSION['groupId']);
            $objAlbum->setGroupTitle($gallerySettings->getName());
            $objAlbum->setFolderId($_SESSION['folderId']);
            $objAlbum->setFileId($objFile->getId());
            $objAlbum->setName(basename($this->options['FileName']));
            $objAlbum->setPath($this->getRelativePath($this->options['FileName']));
            $objAlbum->setStatus(1);
            $objAlbum->setPostDate(QCubed\QDateTime::Now());
            $objAlbum->save();
        }

        $objFolder = Folders::loadById($_SESSION['folderId']);

        if ($objFolder->getLockedFile() == 0) {
            $objFolder->setLockedFile(1);
            $objFolder->save();
        }
    }

    /**
     * Get width of an image from a given file path
     * @param string $path Path to the image file
     * @return int Width of the image in pixels, or 0 if the width could not be determined
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
     * @param string $path The file path of the image
     * @return int The height of the image in pixels, or 0 if the height could not be determined
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
     * Retrieves the list of supported image file extensions.
     *
     * @return array An array of supported image file extensions.
     */
    public static function getImageExtensions()
    {
        return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
    }
}

$objHandler = new CustomGalleryFileHandler($options);
















