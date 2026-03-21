<?php
if (!isset($_GET['file'])) {
    die("No file specified.");
}

$job = $_GET['job'] ?? '';
$file = basename($_GET['file']);

$fullPath = __DIR__ . '/../storage/processing/' . $job . '/' . $file;

if (!file_exists($fullPath)) {
    die("File not found.");
}

// Serve file
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($fullPath));

readfile($fullPath);

// delete entire job folder
deleteFolder(dirname($fullPath));

exit;

function deleteFolder($folder) {
if (!is_dir($folder)) return;

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST
);

foreach ($files as $file) {
    if ($file->isDir()) {
        rmdir($file->getRealPath());
    } else {
        unlink($file->getRealPath());
    }
}

rmdir($folder);

}

?>
