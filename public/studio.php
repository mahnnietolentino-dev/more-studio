<?php
require_once '../core/auth.php';
requireAuth();

$user = getUser();

?>
<!DOCTYPE html>

<html>
<head>
    <title>MoRe Studio</title>
</head>
<body>

<h2>Upload .docx File</h2>

<form action="upload.php" method="POST" enctype="multipart/form-data">
	<input type="text" name="title" placeholder="Lesson Title" required><br>
	<select name="subject" required>
  		<option value="ap">Araling Panlipunan</option>
  		<option value="gmrc">Good Manners and Right Conduct</option>
  		<option value="english">English</option>
  		<option value="filipino">Filipino</option>
  		<option value="language">Language</option>
  		<option value="literacy">Reading and Literacy</option>
  		<option value="makabansa">Makabansa</option>
  		<option value="math">Mathematics</option>
  		<option value="ma">Music and Arts</option>
  		<option value="science">Science</option>
  		<option value="peh">PE and Health</option>
  		<option value="shs">Senior High School</option>
  		<option value="tle">Technology and Livelihood Education</option>
  	</select>
  	<br>
  	<div id="contributors">
	  <div class="contributor">
    	<input type="text" name="contributors[0][name]" placeholder="Name">

    	<select name="contributors[0][role]">
    	  <option value="author">Author</option>
    	  <option value="editor">Editor</option>
    	  <option value="artist">Visual Artist</option>
    	</select>

    	<button type="button" onclick="removeContributor(this)">Remove</button>
	  </div>
	</div>

	<button type="button" onclick="addContributor()">+ Add Contributor</button>
	<br>
    <input type="file" name="docx_file" accept=".docx" required>
    <br><br>
    <button type="submit">Upload & Process</button>
</form>
<a href = "./logout.php">Logout</a>

<script>
let contributorIndex = 1;

function addContributor() {
  const container = document.getElementById('contributors');

  const div = document.createElement('div');
  div.className = 'contributor';

  div.innerHTML = `
    <input type="text" name="contributors[${contributorIndex}][name]" placeholder="Name">

    <select name="contributors[${contributorIndex}][role]">
      <option value="author">Author</option>
      <option value="editor">Editor</option>
      <option value="artist">Visual Artist</option>
    </select>

    <button type="button" onclick="removeContributor(this)">Remove</button>
  `;

  container.appendChild(div);
  contributorIndex++;
}

function removeContributor(btn) {
  btn.parentElement.remove();
}
</script>

</body>
</html>
