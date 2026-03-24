<?php
require_once '../core/auth.php';
requireAuth();

$user = getUser();

$file = $_GET['file'] ?? null;
$job = $_GET['job'] ?? null;

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
  <a href="logout.php" style="margin-left:2px; text-decoration:none;">
  Logout
</a>
</div>

  <!-- FLOW -->
  <p style="font-size:13px; color:#777; margin-bottom:20px;">
    Dashboard → Studio → Processing → <strong>Output</strong>
  </p>

  <h2>Package Ready</h2>

  <p style="font-size:13px; color:#777; margin-bottom:20px;">
    Your material has been successfully structured under the MoRe Standard.
  </p>

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

    <!-- ACTION -->
    <div style="margin-top:20px;">
      <a href="download.php?file=<?= urlencode($file) ?>&job=<?= urlencode($job) ?>" class="button">
        Download Package
      </a>
    </div>

    <div style="margin-top:15px;">
      <a href="studio.php" style="font-size:13px;">Create Another</a>
    </div>

  </div>

</div>
  <!-- FOOTER -->
  <div style="text-align:center; margin-top:0px; font-size:12px; color:#777;">

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

</body>
</html>
