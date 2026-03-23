<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../core/auth.php';
require_once '../core/db.php';
requireAuth();

$user = getUser();
$authorId = $user['author_id'];

$limit = 10;
$page = $_GET['page'] ?? 1;
$page = max(1, (int)$page);
$offset = ($page - 1) * $limit;

$search = $_GET['q'] ?? '';
$searchParam = "%" . $search . "%";

// ✅ MAIN QUERY (THIS DRIVES TABLE)
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

$stmt->bind_param("ssii", $authorId, $searchParam, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// ✅ TOTAL COUNT (for display)
$stmt2 = $conn->prepare("
    SELECT COUNT(*) as total
    FROM packages
    WHERE author_id = ?
");

$stmt2->bind_param("s", $authorId);
$stmt2->execute();

$total = $stmt2->get_result()->fetch_assoc()['total'];
?>
<html>
<body>
<h2>My Packages</h2>
<p>Total Packages: <?= $total ?></p>
<a href="studio.php">+ Create New Package</a>
<br><br>
<form method="GET">
  <input type="text" name="q" placeholder="Search title..." value="<?= $_GET['q'] ?? '' ?>">
  <button type="submit">Search</button>
</form>
<table border="1" cellpadding="8">
<tr>
  <th>Title</th>
  <th>Subject</th>
  <th>Version</th>
  <th>Created</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
  <td>
  	<a href="#" onclick="openVersions('<?= htmlspecialchars($row['title']) ?>')">
    	<?= htmlspecialchars($row['title']) ?>
	</a>
  </td>
  <td><?= htmlspecialchars($row['subject']) ?></td>
  <td><?= $row['version'] ?></td>
  <td><?= $row['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>
<div id="versionModal" style="display:none; position:fixed; top:20%; left:30%; background:#fff; padding:20px; border:1px solid #000;">
  <h3>Versions</h3>
  <div id="versionContent"></div>
  <button onclick="closeModal()">Close</button>
</div>
<a href="?page=<?= max(1, $page - 1) ?>&q=<?= urlencode($search) ?>">Prev</a>
<a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>">Next</a>

<br>
<a href="logout.php">Logout</a>

<script>
function openVersions(title) {
  fetch('get_versions.php?title=' + encodeURIComponent(title))
    .then(res => res.text())
    .then(data => {
      document.getElementById('versionContent').innerHTML = data;
      document.getElementById('versionModal').style.display = 'block';
    });
}

function closeModal() {
  document.getElementById('versionModal').style.display = 'none';
}
</script>

</body>
</html>
