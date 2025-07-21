<?php

require_once('../qcubed.inc.php');

use QCubed\Plugin\Control\CroppieHandler;
use QCubed\Project\Application;

$options = array(
    //'TempFolders' =>  ['thumbnail', 'medium', 'large'], // Please read the CroppieHandler description and manual
    'ResizeDimensions' => [320, 480, 800], // Please read the CroppieHandler description and manual
);

/**
 * CustomCroppieHandler class extends the functionality of CroppieHandler
 * to handle additional operations during image upload.
 */
class CustomCroppieHandler extends CroppieHandler
{
    protected function uploadInfo()
    {
        parent::uploadInfo();

        if ($this->options['OriginalImageName']) {

            $obj = new Files();
            $obj->setFolderId($this->options['FolderId']);
            $obj->setName(basename($this->options['OriginalImageName']));
            $obj->setType('file');
            $obj->setPath($this->getRelativePath($this->options['OriginalImageName']));
            $obj->setDescription(null);
            $obj->setExtension($this->getExtension($this->options['OriginalImageName']));
            $obj->setMimeType($this->getMimeType($this->options['OriginalImageName']));
            $obj->setSize(filesize($this->options['OriginalImageName']));
            $obj->setMtime(filemtime($this->options['OriginalImageName']));
            $obj->setDimensions($this->getDimensions($this->options['OriginalImageName']));
            $obj->setWidth($this->getImageWidth($this->options['OriginalImageName']));
            $obj->setHeight($this->getImageHeight($this->options['OriginalImageName']));
            $obj->save(true);

            if ($this->options['FolderId']) {
                $objFolder = Folders::loadById($this->options['FolderId']);

                // Check if the folder exists before updating properties
                if ($objFolder) {
                    $objFolder->setLockedFile(1);
                    $objFolder->setMtime(filemtime($this->options['OriginalImageName']));
                    $objFolder->save();
                }
            }
        }
    }
}

$objHandler = new CustomCroppieHandler($options);
