<?php
require_once '../core/auth.php';
require_once '../core/db.php';
requireAuth();

$user = getUser();
$authorId = $user['author_id'];

$title = $_GET['title'] ?? '';

$stmt = $conn->prepare("
    SELECT version, created_at
    FROM packages
    WHERE author_id = ? AND title = ?
    ORDER BY version DESC
");

$stmt->bind_param("ss", $authorId, $title);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "Version " . $row['version'] . " - " . $row['created_at'] . "<br>";
}
?>
