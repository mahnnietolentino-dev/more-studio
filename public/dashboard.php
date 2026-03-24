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
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">

  <!-- PRIMARY ACTION -->
<div class="card" style="max-width:700px; margin:auto; position:relative;">

  <!-- USER -->
  <div style="position:absolute; top:20px; right:20px; font-size:13px; color:#777;">
    Hello, <?= htmlspecialchars($user['email']) ?> |
  <a href="logout.php" style="margin-left:2px; text-decoration:none;">
  Logout</a>
  </div>

  <p style="font-size:13px; color:#777; margin-bottom:20px;">
    <strong>Dashboard</strong> → Studio → Processing → Output
  </p>
  
  <h2>Workspace</h2>
  <p style="font-size:13px; color:#777; margin-bottom:20px;">
    Create a new package, find previous packages.
  </p>

  <!-- PRIMARY ACTION -->
  <div style="margin-bottom:25px;">
    <a href="studio.php" class="button">New Upload</a>
  </div>

  <p style="font-size:13px; color:#777; margin-bottom:20px;">
    Total Packages: <?= $total ?>
  </p>

  <!-- SEARCH -->
  <form method="GET" style="display:flex; gap:10px; align-items:center;">
    <input 
      type="text" 
      name="q" 
      placeholder="Search by title..." 
      value="<?= $_GET['q'] ?? '' ?>"
      style="max-width:300px;"
    >

    <a href="dashboard.php?show=all" style="font-size:13px;">See All</a>
  </form>
  
  <?php
$showAll = isset($_GET['show']) && $_GET['show'] === 'all';
$hasSearch = !empty($search);

if ($showAll || $hasSearch):
?>

  <div style="margin-top:25px;">
    <table style="width:100%; border-collapse:collapse;">
      <tr style="text-align:left; font-size:13px; color:#777;">
        <th>Title</th>
        <th>Subject</th>
        <th>Version</th>
        <th>Created</th>
      </tr>

      <?php while ($row = $result->fetch_assoc()): ?>
      <tr style="border-top:1px solid #eee;">
        <td style="padding:10px 0;">
          <a href="#" class="link-title"
             onclick="openVersions('<?= htmlspecialchars($row['title']) ?>'); return false;">
            <?= htmlspecialchars($row['title']) ?>
          </a>
        </td>
        <td><?= htmlspecialchars($row['subject']) ?></td>
        <td><?= $row['version'] ?></td>
        <td><?= $row['created_at'] ?></td>
      </tr>
      <?php endwhile; ?>
    </table>

    <!-- Pagination -->
    <div style="margin-top:15px; font-size:13px;">
      <a href="?page=<?= max(1, $page - 1) ?>&q=<?= urlencode($search) ?>">Prev</a> |
      <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>">Next</a>
    </div>
  </div>
  
<?php endif; ?>
</div>
  <!-- FOOTER -->
  <div style="text-align:center; margin-top:20px; font-size:12px; color:#777;">

  <div style="margin-bottom:0px;">
    <img src="images/deped-logo.png" alt="DepEd" style="height:34px; opacity:0.7; margin:0 10px;">
    <img src="images/deped-aklan-logo.jpg" alt="DepEd Aklan" style="height:34px; opacity:0.7; margin:0 10px;">
  </div>

  <p>Standard developed by Mahnnie Q. Tolentino
  <br><a href = "https://home.depedaklan.online">Department of Education - Division of Aklan</a>
  <br>Training supported by Benita and Catalino Yap Foundation (BCYF)
  <br>
    Licensed under 
    <a href="https://opensource.org/licenses/MIT" target="_blank">MIT License</a>
  </p>

<div id="versionModal" style="
  display:none;
  position:fixed;
  top:50%;
  left:50%;
  transform:translate(-50%, -50%);
  background:#fff;
  padding:20px;
  border-radius:12px;
  box-shadow:0 10px 30px rgba(0,0,0,0.1);
  z-index:1000;
  min-width:300px;
">

  <h3 style="margin-bottom:10px;">Versions</h3>

  <div id="versionContent"></div>

  <button onclick="closeModal()" class="button" style="margin-top:15px;">
    Close
  </button>

</div>

<div id="modalOverlay" style="
  display:none;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.2);
  z-index:999;
"></div>

<script>
function openVersions(title) {
  fetch('get_versions.php?title=' + encodeURIComponent(title))
    .then(res => res.text())
    .then(data => {
      document.getElementById('versionContent').innerHTML = data;
      document.getElementById('versionModal').style.display = 'block';
      document.getElementById('modalOverlay').style.display = 'block';
    });
}

function closeModal() {
  document.getElementById('versionModal').style.display = 'none';
  document.getElementById('modalOverlay').style.display = 'none';
}
</script>
</body>
</html>
