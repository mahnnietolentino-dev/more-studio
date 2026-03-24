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

?>
<?php
$isFirst = true;

while ($row = $result->fetch_assoc()):
?>

<div style="padding:10px 0; border-bottom:1px solid #eee;">

  <strong>
    v<?= $row['version'] ?>
    <?php if ($isFirst): ?>
      <span style="font-size:12px; color:#777;"> — Latest</span>
    <?php endif; ?>
  </strong>

  <div style="font-size:12px; color:#777;">
    Created: <?= $row['created_at'] ?>
  </div>

</div>

<?php 
$isFirst = false;
endwhile;
?>
