<?php
require_once '../core/auth.php';
requireAuth();

$user = getUser();

?>
<!DOCTYPE html>
<html>
<head>
  <title>Studio</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">

  <form action="processing.php" method="POST" enctype="multipart/form-data">

    <!-- METADATA -->
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
    Dashboard → <strong>Studio</strong> → Processing → Output
  </p>

  <h2>Create Learning Package</h2>

  <p style="font-size:13px; color:#777; margin-bottom:20px;">
    Define content, assign contributors, and upload source file.
  </p>

  <!-- METADATA -->
  <div class="input-group">
    <label>Title</label>
    <input type="text" name="title" required style="max-width:500px;">
  </div>

  <div class="input-group">
    <label>Subject</label>
    <select name="subject" style="max-width:300px;">
      <option value="" disabled selected>Select subject</option>
  <optgroup label="Core Subjects">
    <option value="math">Mathematics</option>
    <option value="science">Science</option>
    <option value="english">English</option>
    <option value="filipino">Filipino</option>
  </optgroup>

  <optgroup label="Social & Values">
    <option value="ap">Araling Panlipunan</option>
    <option value="gmrc">Good Manners and Right Conduct</option>
    <option value="makabansa">Makabansa</option>
  </optgroup>

  <optgroup label="Skills & Applied">
    <option value="tle">Technology and Livelihood Education</option>
    <option value="peh">PE and Health</option>
    <option value="ma">Music and Arts</option>
  </optgroup>

  <optgroup label="Language & Literacy">
    <option value="language">Language</option>
    <option value="literacy">Reading and Literacy</option>
  </optgroup>

  <optgroup label="Other">
    <option value="shs">Senior High School</option>
  </optgroup>

    </select>
  </div>

  <!-- CONTRIBUTORS -->
  <div class="input-group">
    <label>Contributors</label>

    <div id="contributors">
      <div class="input-group" style="display:flex; gap:10px; align-items:center;">
        <input type="text" name="contributors[0][name]" placeholder="Name" style="flex:2;">
        <select name="contributors[0][role]" style="flex:1;">
          <option value="author">Author</option>
          <option value="editor">Editor</option>
          <option value="artist">Visual Artist</option>
        </select>
        <button type="button" onclick="removeContributor(this)" style="background:none; border:none;">✕</button>
      </div>
    </div>

    <button type="button" onclick="addContributor()" class="button" style="margin-top:10px;">
      Add Contributor
    </button>
  </div>

  <!-- FILE -->
  <div class="input-group">
    <label>Upload File</label>
    <p style="font-size:13px; color:#777;">.docx format</p>
    <input type="file" name="docx_file" required style="max-width:500px;">
  </div>

  <!-- ACTION -->
  <div style="text-align:center; margin-top:25px;">
    <button type="submit" class="button" id="submitBtn">
      Process Material
    </button>
  </div>

</div>
  </form>

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


</div>

<script>
document.querySelector("form").addEventListener("submit", function() {
  const btn = document.getElementById("submitBtn");
  btn.innerText = "Processing...";
  btn.disabled = true;
});


let contributorIndex = 1;

function addContributor() {
  const container = document.getElementById('contributors');

  const div = document.createElement('div');
  div.className = 'input-group';
div.style.display = 'flex';
div.style.gap = '10px';
div.style.alignItems = 'center';

div.innerHTML = `
  <input type="text" name="contributors[${contributorIndex}][name]" placeholder="Name" style="flex:2;">

  <select name="contributors[${contributorIndex}][role]" style="flex:1;">
    <option value="author">Author</option>
    <option value="editor">Editor</option>
    <option value="artist">Visual Artist</option>
  </select>

  <button type="button" onclick="removeContributor(this)">✕</button>
`;

  container.appendChild(div);
  contributorIndex++;
}

function removeContributor(btn) {
  btn.parentElement.remove();
}

document.querySelector("form").addEventListener("submit", function() {
  const btn = document.getElementById("submitBtn");
  btn.innerText = "Processing...";
  btn.disabled = true;
});
</script>

</body>
</html>
