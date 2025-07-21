<?php

namespace QCubed\Plugin\Control;

use QCubed\Folder;
use QCubed\Project\Application;
use QCubed\QString;

/**
 * Class for handling gallery file uploads, including chunked uploads and image resizing.
 */

class GalleryFileHandler
{
    protected $options;
    // PHP File Upload error message codes:
    // https://www.php.net/manual/en/features.file-upload.errors.php
    protected $uploadErrors;
    protected $index;
    protected $chunk;
    protected $count;
    protected $counter = 0;

    public function __construct($options = null)
    {
        $this->options = array(
            'RootPath' => APP_UPLOADS_DIR,
            'TempPath' => APP_UPLOADS_TEMP_DIR,
            'StoragePath' => '_files',
            'FullStoragePath' => null,
            'ChunkPath' => null,

            'ImageResizeQuality' => 85,
            'ImageResizeFunction' => 'imagecopyresampled', // imagecopyresampled || imagecopyresized
            'ImageResizeSharpen' => true,

            'TempFolders' =>  ['thumbnail', 'medium', 'large'],
            'ResizeDimensions' => [320, 480, 1500],
            'DestinationPath' => null,
            'AcceptFileTypes' => null,
            'MaxFileSize' => null,
            'MinFileSize' => 1,
            'UploadExists' => 'increment', // increment || overwrite

            'File' => null,
            'FileName' => null,
            'FileType' => null,
            'FileSize' => null,
            'FileError' => null,
        );

        $this->uploadErrors = array(
            1 => t('Uploaded file exceeds upload_max_filesize directive in php.ini'),
            2 => t('Uploaded file exceeds MAX_FILE_SIZE directive specified in the HTML form'),
            3 => t('The uploaded file was only partially uploaded'),
            4 => t('Failed to move uploaded file'),
            6 => t('Missing a temporary folder'),
            7 => t('Failed to write file to disk'),
            8 => t('A PHP extension stopped the file upload'),
            'post_max_size' => t('The uploaded file exceeds the post_max_size directive in php.ini'),
            'max_file_size' => 'File is too big',
            'min_file_size' => 'File is too small',
            'accept_file_types' => t('Filetype not allowed'),
            'invalid_image_type' => t('Invalid image type'),
            'invalid_file_size' => t('Invalid file size'),
            'post_max_size' => t('File size exceeds max_filesize %s'),
            'overwritten' => t('This file has been overwritten'),
            'invalid_image' => t('Invalid image / failed getimagesize()'),
            'failed_to_resize_image' => t('Failed to resize image'),
            'resizeimage_failed_to_create_and_resize_the_image' => t('The resizeImage() function failed to create and resize the image'),
            'invalid_chunk_size' => t('Invalid chunk size'),
            'failed_to_open_stream' => t('Failed to open stream: No such directory to put into'),
            'could-not_write_output' => t('Failed to open output stream'),
            'could_not_read_input' => t('Failed to open input stream'),
            'failed_to_move_uploaded_file' => t('Failed to move uploaded file'),
            'file_not_found' => t('File not found')
        );

        if ($options) {
            $this->options = array_merge($this->options, $options);
        }

        $this->options['FullStoragePath'] = $this->options['TempPath'] . '/' . $this->options['StoragePath'];
        $this->options['ChunkPath'] = $this->options['FullStoragePath'] . '/' . 'temp';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->header();
            $this->handleFileUpload();
        }
    }

    /**
     * Send headers to prevent caching and set content type to JSON.
     *
     * @return void
     */
    protected function header()
    {
        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/json');
    }

    /**
     * Handle file upload
     * @return void
     */
    public function handleFileUpload()
    {
        $json = array();

        $chunkEnabled = isset($_REQUEST['chunkEnabled']) ? $_REQUEST['chunkEnabled'] : "false";
        $this->index = isset($_REQUEST['index']) ? intval($_REQUEST['index']) : 0;
        $this->chunk = isset($_REQUEST['chunk']) ? intval($_REQUEST['chunk']) : 0;
        $this->count = isset($_REQUEST['count']) ? intval($_REQUEST['count']) : 0;

        $this->options['FileName'] = $this->options['RootPath'] . '/' . $_FILES["files"]["name"];
        $this->options['File'] = $_FILES["files"]["tmp_name"];
        $this->options['FileType'] = $_FILES["files"]["type"];
        $this->options['FileSize'] = $_FILES["files"]["size"];
        $this->options['FileError'] = $_FILES["files"]["error"];

        // If DestinationPath is set
        if ($this->options['DestinationPath'] !== null) {
            $this->options['FileName'] = $this->options['RootPath'] . $this->options['DestinationPath'] . '/' . basename($this->options['FileName']);
        }

        if ($chunkEnabled === "false") {
            // Check for duplicate filenames and increment if necessary
            $newFileName = $this->checkDuplicateFile($this->options['FileName']);

            // Make sure the new file name is received and then validate the file
            if ($newFileName !== null) {
                $this->options['FileName'] = $newFileName;
            }

            // Validate the file with the updated filename
            if ($this->regularValidate($this->options['File'], $this->options['FileName'], $this->options['FileError'])) {
                // Upload file with new name
                $this->handleRegularUpload($this->options['File'], $this->options['FileName']);
            }
        } else {
            $this->handleChunkUpload($this->options['File'], $this->options['FileName']);
        }
    }

    /**
     * Validate the uploaded file based on various criteria such as errors, size, and type.
     *
     * @param mixed $uploadedFile The file that has been uploaded.
     * @param string $fileName The name of the uploaded file.
     * @param mixed $error The error associated with the file upload, if any.
     * @return bool                Returns true if the file passes all validation checks, false otherwise.
     */
    public function regularValidate($uploadedFile, $fileName, $error)
    {
        if ($error) {
            $file->error = $this->handleError($this->getErrorMessage($error), $fileName);
            return false;
        }

        // Get the value of post_max_size in bytes
        $postMaxSize = $this->getConfigBytes(ini_get('post_max_size'));

        // Check if the file size exceeds the post_max_size limit
        if ($postMaxSize && ($_SERVER['CONTENT_LENGTH'] > $postMaxSize)) {
            $file->error = $this->handleError($this->getErrorMessage('post_max_size'), $fileName);
            return false;
        }

        // Check file size
        $fileSize = $this->getFileSize($uploadedFile);

        if ($this->options['MaxFileSize'] && $fileSize > $this->options['MaxFileSize']) {
            $file->error = $this->handleError($this->getErrorMessage('max_file_size'), $fileName);
            return false;
        }

        if ($this->options['MinFileSize'] && $fileSize < $this->options['MinFileSize']) {
            $file->error = $this->handleError($this->getErrorMessage('min_file_size'), $fileName);
            return false;
        }

        if ($this->options['AcceptFileTypes']) {
            // Check the file type
            if (!in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $this->options['AcceptFileTypes'])) {
                $file->error = $this->handleError($this->getErrorMessage('accept_file_types'), $fileName);
                return false;
            }
        }

        return true;
    }

    /**
     * Validate file upload chunk by chunk.
     *
     * @param resource|string $uploadedFile The uploaded file handle or path.
     * @param string $fileName The name of the file being uploaded.
     * @param int $error Error code associated with the file upload.
     * @param bool $isLastChunk Indicates if this is the last chunk of the file.
     * @return bool Returns true if the chunk is valid, otherwise false.
     */
    public function chunkValidate($uploadedFile, $fileName, $error, $isLastChunk)
    {
        if ($error) {
            $file->error = $this->handleError($this->getErrorMessage($error), $fileName);
            return false;
        }

        // Calculate chunk size
        $fileSize = 0;
        if (is_resource($uploadedFile)) {
            fseek($uploadedFile, 0, SEEK_END);
            $fileSize = ftell($uploadedFile);
            fseek($uploadedFile, 0, SEEK_SET);
        }

        // We only check if all chunks are merged
        if ($isLastChunk) {
            $fileSize = $this->getFileSize($fileName);

            if ($this->options['MaxFileSize'] && $fileSize > $this->options['MaxFileSize']) {
                $file->error = $this->handleError($this->getErrorMessage('max_file_size'), $fileName);
                return false;
            }

            if ($this->options['MinFileSize'] && $fileSize < $this->options['MinFileSize']) {
                $file->error = $this->handleError($this->getErrorMessage('min_file_size'), $fileName);
                return false;
            }

            if ($this->options['AcceptFileTypes']) {
                // We check the file type after merging the chunks
                if (!in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $this->options['AcceptFileTypes'])) {
                    $file->error = $this->handleError($this->getErrorMessage('accept_file_types'), $fileName);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Handles the upload of a regular file.
     *
     * @param string $uploadedFile The temporary file path of the uploaded file.
     * @param string $file The destination file path where the uploaded file should be moved.
     * @return void
     */
    protected function handleRegularUpload($uploadedFile, $file)
    {
        move_uploaded_file($uploadedFile, $file);

        clearstatcache();

        $this->resizeImageProcess($file);
        $this->uploadInfo();
    }

    /**
     * Handles the file chunk upload process, including moving the uploaded chunk
     * to a temporary location, sorting the chunks, and merging them into the final file.
     *
     * @param string $uploadedFile The temporary uploaded file.
     * @param string $file The final target file path.
     * @return void
     */
    protected function handleChunkUpload($uploadedFile, $file)
    {
        $chunkFile = $this->options['ChunkPath'] . '/' . basename($file);

        // Move the file to a temporary location
        if (!move_uploaded_file($uploadedFile, $chunkFile . '.part' . $this->chunk)) {
            $this->handleError($this->getErrorMessage('failed_to_move_uploaded_file'), $file);
            return;
        }

        clearstatcache();

        $filePath = $chunkFile . '.part*';
        $fileParts = glob($filePath);
        sort($fileParts, SORT_NATURAL);
        $_SESSION['parts'] = $fileParts; // We keep the parts in the session

        // Merge chunks
        $finalFile = fopen($chunkFile, 'wb');

        foreach ($fileParts as $filePart) {
            $chunk = file_get_contents($filePart);
            fwrite($finalFile, $chunk);
            $this->counter++;
        }

        fclose($finalFile);

        // When all parts are received
        if ($this->count == $this->counter) {

            $this->partFilesToDelete($_SESSION['parts']);

            // Final validation after merging files
            if ($this->chunkValidate($finalFile, $chunkFile , null, true)) {

                // If filename already exists, check for duplicates
                if (!file_exists($file)) {
                    rename($chunkFile, $file);
                    $this->options['FileSize'] = filesize($file);
                } else {
                    $newFileName = $this->checkDuplicateFile($file);
                    rename($chunkFile, $newFileName);
                    $this->options['FileName'] = $newFileName;
                    $this->options['FileSize'] = filesize($newFileName);
                }

                // Further processing
                $this->resizeImageProcess($this->options['FileName']);
                $this->uploadInfo();
            }
        }
    }

    /**
     * Deletes temporary part files and clears the session data related to these parts.
     *
     * @param array $tempFiles Array of temporary part file paths to be deleted.
     * @return void
     */
    protected function partFilesToDelete($tempFiles)
    {
        foreach ($tempFiles as $tempFile) {
            unlink($tempFile);
        }

        unset($_SESSION['parts']);
    }

    /**
     * Checks for duplicate files and handles them based on the configured option.
     * If the file exists, it either overwrites the existing file or increments the filename.
     *
     * @param string $fileName The full path of the file to check for duplicates.
     * @return string|null The new file name if incremented, the original file name if not
     * a duplicate, or null if the file is meant to be overwritten.
     */
    protected function checkDuplicateFile($fileName) {
        // Set dirname, name and ext
        $dirname = $this->removeFileName($fileName);
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        $isExists = false;
        $files = glob($dirname . '/*', GLOB_NOSORT);

        if (in_array($fileName, $files)) {
            $isExists = true;
        }

        if ($isExists === true) {
            if (file_exists($dirname . '/' . $name . '.' . $ext)) {

                // If 'overwrite' is selected, the file will be overwritten
                if ($this->options['UploadExists'] == 'overwrite') {
                    $this->handleError($this->getErrorMessage('overwritten'), $name);
                    return null;
                }

                // If 'increment' is selected, the filename is incremented
                if ($this->options['UploadExists'] == 'increment') {
                    $inc = 1;
                    while (file_exists($dirname . '/' . $name . '-' . $inc . '.' . $ext)) {
                        $inc++;
                    }
                    return $dirname . '/' . $name . '-' . $inc . '.' . $ext;
                }
            }

            return $fileName;
        }
    }

    /**
     * Retrieves the error message corresponding to the given error code.
     *
     * @param string $error The error code for which the error message is needed.
     * @return string The error message associated with the given error code, or the error code itself if no message is found.
     */
    protected function getErrorMessage($error)
    {
        return isset($this->uploadErrors[$error]) ? $this->uploadErrors[$error] : $error;
    }

    /**
     * Processes the resizing of an image, creating resized copies in various dimensions.
     *
     * @param string $fileName The name of the source image file to be resized.
     * @return void
     */
    protected function resizeImageProcess($fileName)
    {
        $associatedParameters = array_combine($this->options['TempFolders'], $this->options['ResizeDimensions']);

        if (is_file($fileName) && in_array($this->getExtension($fileName), $this->getImageExtensions())) {

            $size = getimagesize($fileName);

            foreach ($associatedParameters as $tempFolder => $resizeDimension) {

                if ($this->options['DestinationPath'] == null) {
                    $newPath = $this->options['FullStoragePath'] . '/' . $tempFolder . '/' . basename($fileName);
                } else {
                    $newPath = $this->options['FullStoragePath'] . '/' . $tempFolder . '/' . $this->options['DestinationPath'] . '/' . basename($fileName);
                }

                if (self::getMimeType($fileName) == 'image/svg+xml' || ($resizeDimension > $size[0]) && $size[0] !== 0) {
                    copy($fileName, $newPath);
                } else {
                    $this->resizeImage($fileName, $newPath, $resizeDimension);
                }
            }
        }
    }

    /**
     * Creates an image resource from a given file path based on image type.
     *
     * @param string $path The file path to the image.
     * @param int $type The type of the image (e.g., IMAGETYPE_JPEG, IMAGETYPE_PNG).
     * @return resource|false An image resource identifier on success, false on errors.
     */
    protected function imageCreateFrom($path, $type)
    {
        if (!$path || !$type) return;
        if ($type === IMAGETYPE_JPEG) {
            return imagecreatefromjpeg($path);
        } else if ($type === IMAGETYPE_PNG) {
            return imagecreatefrompng($path);
        } else if ($type === IMAGETYPE_GIF) {
            return imagecreatefromgif($path);
        } else if ($type === 18/*IMAGETYPE_WEBP*/) {
            if (version_compare(PHP_VERSION, '5.4.0') >= 0) return imagecreatefromwebp($path);
        } else if ($type === IMAGETYPE_BMP) {
            if (version_compare(PHP_VERSION, '7.2.0') >= 0) return imagecreatefrombmp($path);
        }
    }

    /**
     * Applies a sharpening filter to the given image using a convolution matrix.
     *
     * @param resource $image The image resource to be sharpened.
     * @return void
     */
    protected function sharpenImage($image)
    {
        $matrix = array(
            array(-1, -1, -1),
            array(-1, 20, -1),
            array(-1, -1, -1),
        );
        $divisor = array_sum(array_map('array_sum', $matrix));
        $offset = 0;
        imageconvolution($image, $matrix, $divisor, $offset);
    }

    /**
     * Resizes an image to the specified dimensions and saves it to a new path.
     *
     * @param string $path The path to the original image.
     * @param string $newPath The path where the resized image will be saved.
     * @param int $resizeDimensions The new dimension for resizing the image.
     * @return void
     */
    protected function resizeImage($path, $newPath, $resizeDimensions)
    {

        if (function_exists('exif_imagetype') && exif_imagetype($path) !== false) {
            // file size
            $fileSize = filesize($path);
            // imagesize
            $size = getimagesize($path);

            if (empty($size) || !is_array($size)) {
                return $this->handleError($this->getErrorMessage('invalid_image'), $path);
            }

            $resizeRatio = max($size[0], $size[1]) / $resizeDimensions;

            // Calculate new image dimensions.
            $resizeWidth = round($size[0] / $resizeRatio);
            $resizeHeight = round($size[1] / $resizeRatio);

            // Create final image with new dimensions.
            $newImage = imagecreatetruecolor($resizeWidth, $resizeHeight);

            // create new $image
            $image = $this->imageCreateFrom($path, $size[2]);

            imageAlphaBlending($newImage, false);
            imageSaveAlpha($newImage, true);

            if (!call_user_func($this->options['ImageResizeFunction'], $newImage, $image, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $size[0], $size[1])) {
                return $this->handleError($this->getErrorMessage('failed_to_resize_image'), $path);
            }

            // destroy original $image resource
            imagedestroy($image);

            // sharpen resized image
            if ($this->options['ImageResizeSharpen']) {
                $this->sharpenImage($newImage);
            }

            if ($this->options['ImageResizeQuality']) {
                switch ($size[2]) {
                    case IMAGETYPE_JPEG:
                        imagejpeg($newImage, $newPath, $this->options['ImageResizeQuality']);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($newImage, $newPath, $this->options['ImageResizeQuality']);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($newImage, $newPath, floatval($this->options['ImageResizeQuality'] / 100));
                        break;
                    default:
                        throw new Exception(t("Unable to deal with image type"));
                }
            } else {
                return $this->handleError($this->getErrorMessage('resizeimage_failed_to_create_and_resize_the_image'), $path);
            }

            // destroy image
            imagedestroy($newImage);
        }
    }

    /**
     * Outputs file information as a JSON-encoded array, including details
     * such as filename, path, extension, type, error status, size, modification time,
     * and dimensions.
     *
     * @return void
     */
    protected function uploadInfo()
    {
        print json_encode(array(
            'filename' =>  basename($this->options['FileName']),
            'path' => $this->getRelativePath($this->options['FileName']),
            'extension' => $this->getExtension($this->options['FileName']),
            'type' => $this->options['FileType'],
            'error' => $this->options['FileError'],
            'size' => $this->options['FileSize'],
            'mtime' => filemtime($this->options['FileName']),
            'dimensions' => $this->getDimensions($this->options['FileName'])
        ));
    }

    /**
     * Handles errors during the file upload process, including generating
     * an error response and cleaning up temporary files.
     *
     * @param string $errorMessage The error message to be reported.
     * @param string|null $file The file path in question, optional.
     * @return void
     */
    protected function handleError($errorMessage, $file = null) {
        $json['filename'] = basename($file);
        $json['size'] = $this->options['FileSize'];
        $json['type'] = $this->options['FileType'];
        $json['error'] = $errorMessage;
        print json_encode($json);

        // Check and remove temporary files
        $chunkFile = $this->options['ChunkPath'] . '/' . basename($file);

        // If there is an error, delete the final file and all temporary parts
        if ($errorMessage) {
            if (file_exists($chunkFile)) {
                // Remove final file
                unlink($chunkFile);
            }
        }

        exit;
    }

    /**
     * Fixes the integer overflow for the provided size.
     *
     * @param int $size The size value that might have overflowed.
     * @return float The corrected size value.
     */
    protected function fixIntegerOverflow($size) {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }
        return $size;
    }

    /**
     * Retrieves the size of the specified file, with an option to clear the stat cache before fetching the size.
     *
     * @param string $filePath The path to the file whose size is to be fetched.
     * @param bool $clearStatCache Optional. Whether to clear the stat cache before getting the file size. Default is false.
     * @return int The size of the file in bytes.
     */
    protected function getFileSize($filePath, $clearStatCache = false) {
        if ($clearStatCache) {
            if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
                clearstatcache(true, $filePath);
            } else {
                clearstatcache();
            }
        }
        return $this->fixIntegerOverflow(filesize($filePath));
    }

    /**
     * Converts a configuration size string into its byte equivalent. The input value
     * can have a unit suffix (e.g., 'K', 'M', 'G') to indicate kilobytes, megabytes,
     * or gigabytes, respectively.
     *
     * @param string $val The configuration size string to be converted, potentially
     *                    containing a unit suffix.
     * @return int The size in bytes.
     */
    public function getConfigBytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        if (is_numeric($val)) {
            $val = (int)$val;
        } else {
            $val = (int)substr($val, 0, -1);
        }
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    /**
     * Removes the file name from a given path, returning the directory portion of the path.
     *
     * @param string $path The full file path from which the file name should be removed.
     * @return string The directory portion of the path without the file name.
     */
    protected function removeFileName($path)
    {
        return substr($path, 0, (int) strrpos($path, '/'));
    }

    /**
     * Returns the relative path by removing the root path prefix from the given absolute path.
     *
     * @param string $path The absolute file path from which the root path should be removed.
     * @return string The relative path.
     */
    public function getRelativePath($path)
    {
        return substr($path, strlen($this->options['RootPath']));
    }

    /**
     * Retrieves the file extension from the given file path.
     *
     * @param string $path The path of the file.
     * @return string|null The file extension in lowercase or null if the path is not a file.
     */
    public static function getExtension($path)
    {
        if(!is_dir($path) && is_file($path)){
            return strtolower(substr(strrchr($path, '.'), 1));
        }
    }

    /**
     * Retrieves the MIME type of the file at the provided path.
     *
     * @param string $path The path to the file.
     * @return string|false The MIME type of the file, or false if it cannot be determined.
     */
    public static function getMimeType($path)
    {
        if(function_exists('mime_content_type')) {
            return mime_content_type($path);
        } else {
            return function_exists('finfo_file') ? finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path) : false;
        }
    }

    /**
     * Retrieves the dimensions of an image file specified by the given path.
     *
     * @param string $path The path to the image file.
     * @return string The dimensions of the image in the format 'width x height'.
     */
    public static function getDimensions($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions())) {
            $width = (isset($ImageSize[0]) ? $ImageSize[0] : '0');
            $height = (isset($ImageSize[1]) ? $ImageSize[1] : '0');
            $dimensions = $width . ' x ' . $height;
            return $dimensions;
        }
    }

    /**
     * Returns a list of common image file extensions.
     *
     * @return string[] An array of image file extensions.
     */
    public static function getImageExtensions()
    {
        return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
    }

    /**
     * Converts a size in bytes to a human-readable format, using appropriate units such as KB, MB, GB, etc.
     *
     * @param int $bytes The size in bytes to be converted.
     * @return string The human-readable representation of the input size.
     */
    protected function readableBytes($bytes)
    {
        $i = floor(log($bytes) / log(1024));
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
    }

    /**
     * Cleans a given path by removing unnecessary characters,
     * trimming leading and trailing slashes, and converting backslashes to slashes.
     *
     * @param string $path The file or directory path to be cleaned.
     * @return string The cleaned path.
     */
    public static function cleanPath($path)
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
     * Renames a file from the old path to the new path if the new path does not
     * already exist and the old path does exist.
     *
     * @param string $old The current file path.
     * @param string $new The new file path.
     * @return bool|null Returns true if the file was successfully renamed,
     *                   false if the renaming failed, or null if the conditions were not met.
     */
    protected function rename($old, $new)
    {
        return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
    }
}