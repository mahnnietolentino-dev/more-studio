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
    <strong>Studio</strong> → Processing → Output
  </p>

  <h2>Create Learning Package</h2>

  <p style="font-size:13px; color:#777; margin-bottom:20px;">
    Define content, assign contributors, and upload source file.
  </p>

  <!-- METADATA -->
  <div class="input-group">
    <label style="font-weight:600; display:block; margin-bottom:5px;">Package Title</label>
	<small style="color:#777;"></small>
    <input type="text" name="title" placeholder="Name of this learning material">
  </div>

  <div class="input-group">
    <label style="font-weight:600; display:block; margin-bottom:5px;">Subject Area</label>
    <select name="subject">
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
    <label style="font-weight:600; display:block; margin-bottom:5px;">Contributors</label>
	<small style="color:#777;">Add authors, editors, or visual artists</small>

<div id="contributors">

  <div class="contributor-row" style="display:flex; gap:8px; align-items:center; margin-bottom:8px;">

    <input type="text" name="contributors[0][name]" placeholder="Full Name" style="flex:2;">

    <select name="contributors[0][role]" style="flex:1;">
      <option value="author">Author</option>
      <option value="editor">Editor</option>
      <option value="artist">Visual Artist</option>
    </select>

    <button type="button" onclick="addContributor()" style="
  padding:4px 8px;
  border:1px solid #ddd;
  background:#fafafa;
  border-radius:6px;
  font-size:13px;
  cursor:pointer;
">+</button>

    <button type="button" disabled style="opacity:0.3; border:none; background:none; padding:5px 10px;">✕</button>

  </div>

</div>
  </div>

  <!-- FILE -->
  <div class="input-group">
    <label style="font-weight:600; display:block; margin-bottom:5px;">Source Document (.docx)</label>
	<small style="color:#777;">Upload your lesson file. Use [MCQ], [TF], [SHORT] for assessments.</small>
    <input type="file" name="docx_file" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
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
  max-width:550px;
  width:90%;
  max-height:80vh;
  overflow:auto;
">

  <h2 style="margin-bottom:10px;">Authoring Guide</h2>

  <p style="font-size:13px; color:#555; margin-bottom:15px;">
    Follow these steps to create a valid learning package.
  </p>

  <!-- STEP 1 -->
  <h4 style="margin:10px 0 5px;">1. Structure Your Content</h4>
  <p style="font-size:13px; color:#555;">
    Use headings in your document to define chapters.
  </p>

<pre style="background:#f6f6f6; padding:10px; border-radius:6px;">
Chapter 1 Title (Heading 1)
Content here...

Chapter 2 Title (Heading 1)
More content...
</pre>

  <!-- STEP 2 -->
  <h4 style="margin:15px 0 5px;">2. Add Learning Content</h4>
  <p style="font-size:13px; color:#555;">
    Write your lesson normally. You can include images and text.
  </p>

  <!-- STEP 3 -->
  <h4 style="margin:15px 0 5px;">3. Add Assessments</h4>
  <p style="font-size:13px; color:#555;">
    Wrap all questions inside a <strong>[QUIZ]</strong> block.
  </p>

<pre style="background:#f6f6f6; padding:10px; border-radius:6px;">
[QUIZ]

[MCQ]
What is 2 + 2?
A. 3
B. 4*
C. 5
D. 6
[/MCQ]

[TF]
The Earth is flat.
Answer: False*
[/TF]

[SHORT]
What is 10 ÷ 2?
5
[/SHORT]

[/QUIZ]
</pre>

  <p style="font-size:12px; color:#777;">
    Use * to mark the correct answer.
  </p>

  <!-- STEP 4 -->
  <h4 style="margin:15px 0 5px;">4. Save and Upload</h4>
  <p style="font-size:13px; color:#555;">
    Save your file as <strong>.docx</strong> and upload it in the Studio.
  </p>

  <!-- DOWNLOAD SAMPLE -->
  <div style="margin-top:20px;">
    <a href="sample.docx" class="button" download>
      Download Sample DOCX
    </a>
    <p style="font-size:12px; color:#777; margin-top:8px;">
		Use this as a template for your own content.
	</p>
  </div>

  <!-- CLOSE -->
  <div style="text-align:right; margin-top:15px;">
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
document.querySelector("form").addEventListener("submit", function(e) {

  const title = document.querySelector('input[name="title"]').value.trim();
  const subject = document.querySelector('select[name="subject"]').value;
  const file = document.querySelector('input[name="docx_file"]').files[0];

  const contributors = document.querySelectorAll('input[name^="contributors"]');

  let errors = [];

  // Title
  if (!title) {
    errors.push("Please enter a title.");
  }

  // Subject
  if (!subject) {
    errors.push("Please select a subject.");
  }

  // File
  if (!file) {
  	errors.push("Please upload a .docx file.");
  } else {
  	const fileName = file.name.toLowerCase();

  	if (!fileName.endsWith(".docx")) {
    	errors.push("Invalid file type. Please upload a .docx file.");
 	}
  }

  // Contributors (at least one name filled)
  let hasContributor = false;
  contributors.forEach(input => {
    if (input.value.trim() !== "") {
      hasContributor = true;
    }
  });

  if (!hasContributor) {
    errors.push("Please add at least one contributor.");
  }

  // 🚨 If errors exist → STOP
  if (errors.length > 0) {
    e.preventDefault();

    alert(
      "Please complete the following:\n\n" +
      errors.map(err => "• " + err).join("\n")
    );

    return;
  }

  // ✅ Proceed normally
  const btn = document.getElementById("submitBtn");
  btn.innerText = "Processing...";
  btn.disabled = true;
});

document.querySelectorAll("input, select").forEach(el => {
  el.addEventListener("input", () => {
    const btn = document.getElementById("submitBtn");
    btn.disabled = false;
    btn.innerText = "Process Material";
  });
});

let contributorIndex = 1;

function addContributor() {
  const container = document.getElementById('contributors');

  const div = document.createElement('div');
  div.className = 'contributor-row';
  div.style.display = 'flex';
  div.style.gap = '8px';
  div.style.alignItems = 'center';
  div.style.marginBottom = '8px';

  div.innerHTML = `
    <input type="text" name="contributors[${contributorIndex}][name]" placeholder="Full Name" style="flex:2;">

    <select name="contributors[${contributorIndex}][role]" style="flex:1;">
      <option value="author">Author</option>
      <option value="editor">Editor</option>
      <option value="artist">Visual Artist</option>
    </select>

    <button type="button" onclick="addContributor()" style="
  padding:4px 8px;
  border:1px solid #ddd;
  background:#fafafa;
  border-radius:6px;
  font-size:13px;
  cursor:pointer;
">+</button>

    <button type="button" onclick="removeContributor(this)" style="
  padding:4px 8px;
  border:1px solid #ddd;
  background:#fafafa;
  border-radius:6px;
  font-size:13px;
  cursor:pointer;
">✕</button>
  `;

  container.appendChild(div);
  contributorIndex++;
}

function removeContributor(btn) {
  const container = document.getElementById('contributors');

  // Prevent deleting last row
  if (container.children.length === 1) return;

  btn.parentElement.remove();
}

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

// overlay click
document.getElementById("modalOverlay").onclick = function() {
  closeHelp();
  closePackages();
};
</script>

</body>
</html>
