<?php

include "config.php";

//Security options
$allowDelete = true; // Set to false to disable delete button and delete POST request.
$allowUpload = true; // Set to true to allow upload files
$allowCreateFolder = true; // Set to false to disable folder creation
$allowDirectLink = true; // Set to false to only allow downloads and not direct link
$allowShowFolders = true; // Set to false to hide all subdirectories

$disallowedExtensions = ['php'];  // must be an array. Extensions disallowed to be uploaded
$hiddenExtensions = ['php']; // must be an array of lowercase file extensions. Extensions hidden in directory index

///////// MAIN ROUTINE /////////

$task = $_POST['do'];
if (!$task) {
    $task = $_GET['do'];
}

$file = $_REQUEST['file'] ?: STORED_DIR;


switch ($task) {
    case 'list':
        getListOfFilesAndFolders($file, $allowShowFolders, $hiddenExtensions);
        break;
    case 'delete':
        deleteFileOrFolderByPath($file, $allowDelete);
        break;
    case 'mkdir':
        $directoryName = $_POST['name'];
        makeDirectory($file, $directoryName, $allowCreateFolder);
        break;
    case 'folder_rename':
        $newName = $_POST['name'];
        $oldName = $_POST['old_name'];
        folderRename($file, $newName, $oldName);
        break;
    case 'upload':
        uploadFileToFolder($file, $disallowedExtensions, $allowUpload);
        break;
    case 'download':
        downloadFileExecute($file);
        break;
    default:
        break;
}

function downloadFileExecute($file)
{
    $downloadRes = is_dir(realpath($file)) ? zipAndDownloadFolder($file) : downloadFile($file);
    exit;
}

function uploadFileToFolder($file, $disallowedExtensions, $allowUpload)
{
    if (!$allowUpload) {
        return;
    }

    foreach($disallowedExtensions as $ext) {
        if(preg_match(sprintf('/\.%s$/', preg_quote($ext)), $_FILES['file_data']['name'])) {
            err(403, "Files of this type are not allowed.");
        }
    }

    move_uploaded_file($_FILES['file_data']['tmp_name'], $file . '/' . $_FILES['file_data']['name']);
    exit;
}

function folderRename($file, $newName, $oldName)
{
    try {
        if ($newName == $oldName) {
            throw new Exception('The new folder name must differ from the old one.');
        }

        if (file_exists($file . '/' . $newName)) {
            throw new Exception('The new folder name already exists. Please choose a different name.');
        }

        $res = rename('./' . $oldName, $file . '/' . $newName);
        if (!$res) {
            throw new Exception('Failed to change folder name.');
        }

        echo json_encode([
            'success' => true,
            'message' => 'older name changed successfully.'
        ]);
    } catch (Exception $exc) {
        echo json_encode([
            'success' => false,
            'message' => $exc->getMessage()
        ]);
    }

    exit;
}

function makeDirectory($file, $directoryName, $allowCreateFolder)
{
    if (!$allowCreateFolder) {
        return;
    }

    $dir = str_replace('/', '', $directoryName);
    if(substr($dir, 0, 2) === '..') {
        exit;
    }

    if (is_dir($file . '/' . $dir)) {
        echo json_encode([
            'success' => false,
            'message' => 'The new folder name already exists in the directory.'
        ]);
    } else {
        chdir($file);
        @mkdir($directoryName);

        echo json_encode([
            'success' => true,
            'message' => 'Created a new folder successfully!'
        ]);
    }

    exit;
}

function deleteFileOrFolderByPath($file, $allowDelete)
{
    if ($allowDelete) {
        rmrf($file);
    }
    exit;
}

function getListOfFilesAndFolders($file, $allowShowFolders, $hiddenExtensions)
{
    if (is_dir($file)) {
        $directory = $file;
        $result = [];
        $searchFiles = $_REQUEST['search'] ?: '';
        $searchScope = $_REQUEST['search_scope'] ?: CURRENT_FOLDER_SEARCH_SCOPE;
        $isGlobalSearch = !empty($searchFiles) && $searchScope == ALL_FOLDER_SEARCH_SCOPE;
        $files = [];

        if ($isGlobalSearch) {
            $paths = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(STORED_DIR, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach($paths as $path) {
                $files[] = $path->getRealPath();
            }
        } else {
            $files = array_diff(scandir($directory), ['.','..']);
        }

        $matching_files = !$searchFiles ? $files : array_filter($files, function ($file) use ($searchFiles) {
            return strpos(basename($file), $searchFiles) !== false;
        });

        foreach ($matching_files as $entry) {
            if (!isEntryIgnored($entry, $allowShowFolders, $hiddenExtensions)) {
                $full_path = $isGlobalSearch ? $entry : $directory . '/' . $entry;
                $stat = stat($full_path);
                $result[] = [
                    'mtime' => $stat['mtime'],
                    'size' => $stat['size'],
                    'name' => basename($full_path),
                    'path' => $isGlobalSearch
                        ? str_replace(PATH_TO_THE_TOOL, '.', $full_path)
                        : preg_replace('@^\./|@', '', $full_path),
                    'is_dir' => is_dir($full_path),
                    'is_deleteable' => $allowDelete && ((!is_dir($full_path) && is_writable($directory)) ||
                                                               (is_dir($full_path) && is_writable($directory) && isRecursivelyDeleteable($full_path))),
                    'is_readable' => is_readable($full_path),
                    'is_writable' => is_writable($full_path),
                    'is_executable' => is_executable($full_path),
                ];
            }
        }
    } else {
        err(412, "Not a Directory");
    }

    echo json_encode(['success' => true, 'is_writable' => is_writable($file), 'results' => $result]);
    exit;
}

function downloadFile($file)
{
    $filename = basename($file);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    header('Content-Type: ' . finfo_file($finfo, $file));
    header('Content-Length: ' . filesize($file));
    header(sprintf(
        'Content-Disposition: attachment; filename=%s',
        strpos('MSIE', $_SERVER['HTTP_REFERER']) ? rawurlencode($filename) : "\"$filename\""
    ));
    ob_flush();
    readfile($file);
}

function zipAndDownloadFolder($folderPath)
{
    $zip = new ZipArchive();
    $splittedFolderPath = explode("/", $folderPath) ?? DEFAULT_DOWNLOAD_FILE_NAME;
    $zipFileName = count($splittedFolderPath) == 1
        ? ($splittedFolderPath[0] . '.zip')
        : (end($splittedFolderPath) . '.zip');

    // Open the zip file for writing
    if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
        // Add all files and subdirectories to the zip file
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (is_dir($file)) {
                $zip->addEmptyDir(str_replace($folderPath . '/', '', $file . '/'));
            } elseif (is_file($file)) {
                $zip->addFromString(str_replace($folderPath . '/', '', $file), file_get_contents($file));
            }
        }

        // Close the zip file
        $zip->close();

        // Set headers for force download
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipFileName);
        header('Content-Length: ' . filesize($zipFileName));

        // Read the zip file and output it to the browser
        readfile($zipFileName);

        // Delete the temporary zip file
        unlink($zipFileName);
    } else {
        echo 'Failed to create the zip file.';
    }
}

function isEntryIgnored($entry, $allowShowFolders, $hiddenExtensions)
{
    if ($entry === basename(__FILE__)) {
        return true;
    }

    if (is_dir($entry) && !$allowShowFolders) {
        return true;
    }

    $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
    if (in_array($ext, $hiddenExtensions)) {
        return true;
    }

    return false;
}

function rmrf($dir)
{
    if(is_dir($dir)) {
        $files = array_diff(scandir($dir), ['.','..']);
        foreach ($files as $file) {
            rmrf("$dir/$file");
        }
        rmdir($dir);
    } else {
        unlink($dir);
    }
}

function isRecursivelyDeleteable($d)
{
    $stack = [$d];
    while($dir = array_pop($stack)) {
        if(!is_readable($dir) || !is_writable($dir)) {
            return false;
        }
        $files = array_diff(scandir($dir), ['.','..']);
        foreach($files as $file) {
            if(is_dir($file)) {
                $stack[] = "$dir/$file";
            }
        }
    }
    return true;
}

function err($code, $msg)
{
    http_response_code($code);
    echo json_encode(['error' => ['code' => intval($code), 'msg' => $msg]]);
    exit;
}

function asBytes($ini_v)
{
    $ini_v = trim($ini_v);
    $s = ['g' => 1 << 30, 'm' => 1 << 20, 'k' => 1 << 10];
    return intval($ini_v) * ($s[strtolower(substr($ini_v, -1))] ?: 1);
}

$MAX_UPLOAD_SIZE = min(asBytes(ini_get('post_max_size')), asBytes(ini_get('upload_max_filesize')));
