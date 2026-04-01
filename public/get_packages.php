<?php
require_once '../core/auth.php';
require_once '../core/db.php';
requireAuth();

$user = getUser();
$authorId = $user['author_id'];

$search = $_GET['q'] ?? '';
$searchParam = "%" . $search . "%";

$stmt = $conn->prepare("
    SELECT *
    FROM packages p1
    WHERE p1.version = (
        SELECT MAX(p2.version)
        FROM packages p2
        WHERE p2.title = p1.title
        AND p2.author_id = p1.author_id
    )
    AND p1.author_id = ?
    AND p1.title LIKE ?
    ORDER BY p1.created_at DESC
    LIMIT ? OFFSET ?
");

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$stmt->bind_param("ssii", $authorId, $searchParam, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<table style="width:95%; border-collapse:collapse; font-size:13px;">
  <tr style="text-align:left; color:#777;">
    <th>Title</th>
    <th>Subject</th>
    <th>Version</th>
    <th>Created</th>
  </tr>

  <?php while ($row = $result->fetch_assoc()): ?>
  <tr style="border-top:1px solid #eee;">
    <td style="padding:10px 0;">
      <a href="#" onclick="openVersions('<?= htmlspecialchars($row['title']) ?>'); return false;">
        <?= htmlspecialchars($row['title']) ?>
      </a>
    </td>
    <td><?= htmlspecialchars($row['subject']) ?></td>
    <td><?= $row['version'] ?></td>
    <td><?= $row['created_at'] ?></td>
  </tr>
  <?php endwhile; ?>
</table>
<hr />
<div style="margin-top:10px; font-size:13px; text-align:right;">
  <?php if ($page > 1): ?>
    <a href="#" onclick="loadPackages(<?= $page - 1 ?>); return false;">Prev</a>
  <?php endif; ?>

  <?php if ($result->num_rows === $limit): ?>
    <?php if ($page > 1) echo " | "; ?>
    <a href="#" onclick="loadPackages(<?= $page + 1 ?>); return false;">Next</a>
  <?php endif; ?>
</div>
