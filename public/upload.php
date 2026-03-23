<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../core/db.php';
require_once '../core/auth.php';
requireAuth();

$user = getUser();
$authorId = $user['author_id'];
$email = $user['email'];

require_once __DIR__ . '/../core/pipeline.php';

// 1. Check file exists
if (!isset($_FILES['docx_file'])) {
    die("No file uploaded.");
}

$file = $_FILES['docx_file'];

// 2. Check upload error
if ($file['error'] !== UPLOAD_ERR_OK) {
    die("Upload failed with error code: " . $file['error']);
}

// 3. Validate extension
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'docx') {
    die("Only .docx files are allowed.");
}

// 4. Validate MIME type (extra safety)
$mime = mime_content_type($file['tmp_name']);
$allowedMime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

if ($mime !== $allowedMime) {
    die("Invalid file type.");
}

// 5. File size limit (10MB)
if ($file['size'] > 10 * 1024 * 1024) {
    die("File too large. Max 10MB.");
}

// 6. Ensure upload directory exists
$uploadDir = __DIR__ . '/../storage/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// 7. Generate unique file name
$uniqueName = uniqid('upload_', true);
$filename = $uniqueName . '.docx';
$destination = $uploadDir . $filename;

// 8. Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    die("Failed to save uploaded file.");
}

// 9. Run pipeline
$contributors = [];

// Owner
$contributors[] = [
    'role' => 'author',
    'id' => $authorId,
    'name' => $email
];

$contributors = [];

// Owner (always included)
$contributors[] = [
    'role' => 'author',
    'id' => $authorId,
    'name' => $email
];

// Dynamic contributors
if (!empty($_POST['contributors'])) {
    foreach ($_POST['contributors'] as $person) {
        if (!empty($person['name']) && !empty($person['role'])) {
            $contributors[] = [
                'role' => $person['role'],
                'name' => $person['name']
            ];
        }
    }
}

$title = $_POST['title'] ?? 'Untitled Package';
$subject = $_POST['subject'] ?? 'general';

// ✅ SINGLE correct call
$result = runPipeline($destination, $authorId, $contributors, $title, $subject, $conn);

// 10. Handle pipeline result
if (!$result['success']) {
    echo "<h3>Pipeline Error</h3>";
    echo "<p>" . htmlspecialchars($result['error']) . "</p>";
    exit;
}

unlink($destination);

// 11. Prepare download
$moreFilePath = $result['file'];
$moreFileName = basename($moreFilePath);

// 12. Output response
echo "<h3>✅ Processing Complete</h3>";
echo "<p>Your file is ready.</p>";
$jobId = basename(dirname($moreFilePath));

echo "<a href='download.php?file=" . urlencode($moreFileName) . "&job=" . urlencode($jobId) . "'>Download</a>";

?>
