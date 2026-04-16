<?php
require_once '../core/auth.php';
requireAuth();

$user = getUser();

$file = $_GET['file'] ?? null;
$job = $_GET['job'] ?? null;

$warnings = [];

if (isset($_GET['w'])) {
    $warnings = json_decode($_GET['w'], true) ?? [];
}

if (!$file || !$job) {
    die("Invalid request.");
}

$chapters = $_GET['c'] ?? 0;
$assessments = $_GET['a'] ?? 0;
$images = $_GET['i'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Package Ready</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
<div class="card" style="max-width:700px; margin:auto; position:relative;">
<p style="font-size:13px; color:#777; margin-bottom:20px; margin-top:10px;">
<div style="
  position:absolute;
  top:20px;
  right:20px;
  font-size:13px;
  color:#777;
">
Hello, <?= htmlspecialchars($user['email']) ?> | 

<a href="#" onclick="openHelp(); return false;" style="margin:0 5px;">Guide</a> | 

<a href="#" onclick="openPackages(); return false;" style="margin:0 5px;">
Packages
</a> | 

<a href="logout.php" style="margin-left:5px;">
Logout
</a>
</div>

  <!-- FLOW -->
  <p style="font-size:13px; color:#777; margin-bottom:20px;">
    <a href="index.php">Home</a> → Studio → Processing → <strong style="text-decoration:underline;">Output</strong>
  </p>

  <h2>Package Ready</h2>

  <p style="font-size:13px; color:#777; margin-bottom:20px;">
    Your material has been successfully structured under the MORE Standard.
  </p>

<?php if (!empty($warnings)): ?>
<div style="margin-top:20px; padding:15px; background:#fff3cd; border-radius:8px; color:#856404;">
  <strong>Warnings Detected</strong>
  <ul style="margin-top:10px;">
    <?php foreach ($warnings as $w): ?>
      <li>
        Chapter <?= $w['chapter'] ?>:
        <?= implode(', ', $w['issues']) ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>


    <!-- SUMMARY -->
    <div style="text-align:left; margin-bottom:25px;">
      <p><strong>Output:</strong> .more package</p>
      <p><strong>Status:</strong> Ready for deployment</p>
    </div>
    
    <div style="text-align:left; margin-bottom:25px;">

  <p><strong>Content Structure</strong></p>
  <ul>
  	<li>Chapters detected: <?= $chapters ?></li>
  	<li>Assessment Items detected: <?= $assessments ?></li>
  	<li>Images extracted: <?= $images ?></li>
  </ul>

</div>

<div style="margin-top:25px; display:flex; gap:10px; justify-content:flex-start;">

  <a href="download.php?file=<?= urlencode($file) ?>&job=<?= urlencode($job) ?>" class="button">
    Download Package
  </a>

  <a href="studio.php" class="button" style="background:#eee; color:#333;">
    New Upload
  </a>

</div>

  </div>

</div>
  <!-- FOOTER -->
  <div style="text-align:center; margin-top:0px; font-size:12px; color:#777;">
  <p>MORE is an open learning package standard.
  <br>This implementation is provided as open-source software.
  <p><a href="docs/more_standard.md" style="margin:0 5px;">Standard</a> | 
<a href="docs/contributors.md" style="margin:0 5px;">Contributors</a> | 
<a href="docs/disclaimer.md" style="margin-left:5px;">Disclaimer</a>
  <br>
    <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank">Licensed under GNU GPL v3</a>
  </p>
  </div>
</div>
<div id="helpModal" style="
  display:none;
  position:fixed;
  top:50%;
  left:50%;
  transform:translate(-50%, -50%);
  background:#fff;
  padding:25px;
  border-radius:12px;
  box-shadow:0 10px 30px rgba(0,0,0,0.1);
  z-index:1000;
  max-width:500px;
  width:90%;
  max-height:80vh;
  overflow:auto;
">

  <h3 style="margin-bottom:10px;">Next Steps</h3>

  <p style="font-size:13px; color:#555; margin-bottom:15px;">
    Your package is ready. Follow these steps to use it on your device.
  </p>

  <!-- STEP 1 -->
  <h4 style="margin:10px 0 5px;">1. Rename Your Package</h4>
  <p style="font-size:13px; color:#555;">
    After downloading, you may rename the file to match your lesson title.
  </p>

  <pre style="background:#f6f6f6; padding:10px; border-radius:6px;">
example.more → Math_Lesson_1.more
</pre>

  <!-- STEP 2 -->
  <h4 style="margin:15px 0 5px;">2. Save to Your Device</h4>
  <p style="font-size:13px; color:#555;">
    Move the file to your phone's storage. Place it in a folder where your learning app can access files.
  </p>

  <!-- STEP 3 -->
  <h4 style="margin:15px 0 5px;">3. Open in MORE Container</h4>
  <p style="font-size:13px; color:#555;">
    Open your MORE learning app. It will automatically detect available <strong>.more</strong> packages from your device.
  </p>

  <p style="font-size:12px; color:#777; margin-top:10px;">
    Tip: If the file is not detected, make sure it is saved in your device storage (not inside Downloads only).
  </p>

  <!-- CLOSE -->
  <div style="text-align:right; margin-top:20px;">
    <button onclick="closeHelp()" class="button">Close</button>
  </div>

</div>

<div id="packagesModal" style="
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
  width:90%;
  max-width:700px;
  max-height:80vh;
  overflow:auto;
">

  <h3 style="margin-bottom:10px;">Package Library</h3>

<input 
  type="text" 
  id="packageSearch"
  placeholder="Search by title..."
  style="margin-bottom:10px; width:95%;"
  oninput="searchPackages()"
>

  <div id="packagesContent">
    Loading...
  </div>

  <div style="text-align:right; margin-top:15px;">
    <button onclick="closePackages()" class="button">Close</button>
  </div>

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
window.onload = function() {
  openHelp();
};

function openHelp() {
  document.getElementById("helpModal").style.display = "block";
  document.getElementById("modalOverlay").style.display = "block";
}

function closeHelp() {
  document.getElementById("helpModal").style.display = "none";
  document.getElementById("modalOverlay").style.display = "none";
}

function loadPackages(page = 1) {
  const q = document.getElementById("packageSearch")?.value || '';

  fetch('get_packages.php?page=' + page + '&q=' + encodeURIComponent(q))
    .then(res => res.text())
    .then(html => {
      document.getElementById("packagesContent").innerHTML = html;
    });
}

function openPackages() {
  document.getElementById("packagesModal").style.display = "block";
  document.getElementById("modalOverlay").style.display = "block";

  loadPackages(1);
}

function closePackages() {
  document.getElementById("packagesModal").style.display = "none";
  document.getElementById("modalOverlay").style.display = "none";
}

function searchPackages() {
  const q = document.getElementById("packageSearch").value;

  fetch('get_packages.php?q=' + encodeURIComponent(q))
    .then(res => res.text())
    .then(html => {
      document.getElementById("packagesContent").innerHTML = html;
    });
}

// Optional: click outside to close
document.getElementById("modalOverlay").onclick = function() {
  closeHelp();
  closePackages();
};
</script>

</body>
</html>
