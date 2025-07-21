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

function getFolderParam($objItem) {
    $vars = [
        'id' => $objItem->getId(),
        'parent_id' => $objItem->getParentId(),
        'name' => $objItem->getName(),
        'type' => $objItem->getType(),
        'path' => $objItem->getPath(),
        'mtime' => $objItem->getMtime(),
        'locked_file' => $objItem->getLockedFile(),
        'activities_locked' => $objItem->getActivitiesLocked()
    ];
    return $vars;
}

function getFileParam($objItem) {
    $vars = [
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
    return $vars;
}

function scan($folders, $files) {
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

function filter($id, $folders, $files) {
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