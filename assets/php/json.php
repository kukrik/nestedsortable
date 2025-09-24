<?php
    require_once('../../../../../qcubed.inc.php');

    $arrFolders = [];
    $arrFiles = [];
    $sortFolders = [];
    $sortFiles = [];

    $objFolders = Folders::LoadAll();

    foreach ($objFolders as $objFolder) {
        $arrFolders[] = getFolderParam($objFolder);
    }

    foreach ($arrFolders as $key => $val) {
        $sortFolders[$key] = strtolower($val['path']);
    }
    array_multisort($sortFolders, SORT_ASC, $arrFolders);

    $objFiles = Files::LoadAll();

    foreach ($objFiles as $objFile) {
        $arrFiles[] = getFileParam($objFile);
    }

    foreach ($arrFiles as $key => $val) {
        $sortFiles[$key] = $val['path'];
    }
    array_multisort($sortFiles, SORT_ASC, $arrFiles);

    /**
     * Retrieves an array of folder parameters based on the provided item object.
     *
     * @param object $objItem The item object containing folder information.
     *
     * @return array An associative array containing folder parameters such as id, parent_id, name, type, path, mtime, locked_file, and activities_locked.
     */
    function getFolderParam(object $objItem): array
    {
        return [
            'id' => $objItem->getId(),
            'parent_id' => $objItem->getParentId(),
            'name' => $objItem->getName(),
            'type' => $objItem->getType(),
            'path' => $objItem->getPath(),
            'mtime' => $objItem->getMtime(),
            'locked_file' => $objItem->getLockedFile(),
            'activities_locked' => $objItem->getActivitiesLocked()
        ];
    }

    /**
     * Retrieves an array of file parameters based on the provided item object.
     *
     * @param object $objItem The object representing the file item, which provides access to file properties.
     *
     * @return array Returns an associative array containing file parameters such as id, folder_id, name, type, path, description, extension, mime_type, size, mtime, dimensions, locked_file, and activities_locked.
     */
    function getFileParam(object $objItem): array
    {
        return [
            'id' => $objItem->getId(),
            'folder_id' => $objItem->getFolderId(),
            'name' => $objItem->getName(),
            'type' => $objItem->getType(),
            'path' => $objItem->getPath(),
            'description' => $objItem->getDescription(),
            'extension' => $objItem->getExtension(),
            'mime_type' => $objItem->getMimeType(),
            'size' => $objItem->getSize(),
            'mtime' => $objItem->getMTime(),
            'dimensions' => $objItem->getDimensions(),
            'locked_file' => $objItem->getLockedFile(),
            'activities_locked' => $objItem->getActivitiesLocked()
        ];
    }

    /**
     * Scans folders and files to organize them into a structured array based on their relationships.
     *
     * @param array $folders The list of folders, where each folder contains details such as `id`, `parent_id`, `name`, `type`, `path`, `mtime`, `locked_file`, `activities_locked`.
     * @param array $files The list of files to be linked to the corresponding folders during filtering.
     *
     * @return array The structured array containing folder information and their associated items.
     */
    function scan(array $folders, array $files): array
    {
        $vars = [];

        foreach ($folders as $value) {
            if ($value["parent_id"] !== $value["id"]) {
                $vars[] = [
                    'id' => $value["id"],
                    'parent_id' => $value["parent_id"],
                    'name' => $value["name"],
                    'type' => $value["type"],
                    'path' => $value["path"],
                    'mtime' => $value["mtime"],
                    'locked_file' => $value["locked_file"],
                    'activities_locked' => $value["activities_locked"],
                    'items' => filter($value["id"], $folders, $files)
                ];
            }
        }
        return $vars;
    }

    /**
     * Filters folders and files to create a structured array based on parent-child relationships.
     *
     * @param mixed $id The identifier of the parent folder to filter associated folders and files.
     * @param array $folders The list of folders, where each folder includes details such as `id`, `parent_id`, `name`, `type`, `path`, `mtime`, `locked_file`, and `activities_locked`.
     * @param array $files The list of files, where each file includes details such as `id`, `folder_id`, `name`, `type`, `path`, `description`, `extension`, `mime_type`, `size`, `mtime`, `dimensions`, `locked_file`, and `activities_locked`.
     *
     * @return array A structured array of folders and files associated with the specified parent folder, containing detailed information for each item.
     */
    function filter(mixed $id, array $folders, array $files): array
    {
        $vars = [];

        foreach ($folders as $value) {
            if ($value["type"] === "dir") {
                if ($id === $value["parent_id"]) {
                    $vars[] = [
                        'id' => $value["id"],
                        'parent_id' => $value["parent_id"],
                        'name' => $value["name"],
                        'type' => $value["type"],
                        'path' => $value["path"],
                        'mtime' => $value["mtime"],
                        'locked_file' => $value["locked_file"],
                        'activities_locked' => $value["activities_locked"]
                    ];
                }
            }
        }
        foreach ($files as $value) {
            if ($value["type"] === "file") {
                if ($id === $value["folder_id"]) {
                    $vars[] = [
                        'id' => $value["id"],
                        'folder_id' => $value["folder_id"],
                        'name' => $value["name"],
                        'type' => $value["type"],
                        'path' => $value["path"],
                        'description' => $value["id"],
                        'extension' => $value["extension"],
                        'mime_type' => $value["mime_type"],
                        'size' => $value["size"],
                        'mtime' => $value["mtime"],
                        'dimensions' => $value["dimensions"],
                        'locked_file' => $value["locked_file"],
                        'activities_locked' => $value["activities_locked"]
                    ];
                }
            }
        }
        return $vars;
    }

    header("Content-Type: application/json"); // Advise client of response type
    print json_encode(scan($arrFolders, $arrFiles), JSON_INVALID_UTF8_SUBSTITUTE);