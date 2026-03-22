<?php
require_once '../core/auth.php';
requireAuth();

$user = getUser();

echo "Logged in as: " . $user['email'];
echo "<br>Author ID: " . $user['author_id'];
?>
<!DOCTYPE html>

<html>
<head>
    <title>MoRe Studio</title>
</head>
<body>

<h2>Upload .docx File</h2>

<form action="upload.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="docx_file" accept=".docx" required>
    <br><br>
    <button type="submit">Upload & Process</button>
</form>
<a href = "./logout.php">Logout</a>
</body>
</html>
