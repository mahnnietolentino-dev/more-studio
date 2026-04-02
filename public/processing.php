<?php
require_once __DIR__ . '/../core/db.php';
require_once '../core/auth.php';
requireAuth();

$user = getUser();
$authorId = $user['author_id'];
$email = $user['email'];

require_once __DIR__ . '/../core/pipeline.php';

$resultData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['docx_file'])) {
        $error = "No file uploaded.";
    } else {

        $file = $_FILES['docx_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = "Upload failed.";
        } else {

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($ext !== 'docx') {
                $error = "Only .docx allowed.";
            } else {

                $uploadDir = __DIR__ . '/../storage/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $filename = uniqid('upload_', true) . '.docx';
                $destination = $uploadDir . $filename;

                move_uploaded_file($file['tmp_name'], $destination);

                // Contributors
                $contributors = [
                    [
                        'role' => 'author',
                        'id' => $authorId,
                        'name' => $email
                    ]
                ];

                if (!empty($_POST['contributors'])) {
                    foreach ($_POST['contributors'] as $person) {
                        if (!empty($person['name']) && !empty($person['role'])) {
                            $contributors[] = [
                                'role' => $person['role'],
                                'name' => $person['name']
                            ];
                        }
                    }
                }

                $title = $_POST['title'] ?? 'Untitled';
                $subject = $_POST['subject'] ?? 'general';

                $result = runPipeline($destination, $authorId, $contributors, $title, $subject, $conn);

                unlink($destination);

                if (!$result['success']) {
                    $error = $result['error'];
                } else {
                    $resultData = $result;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">

  <div class="card" style="max-width:700px; margin:auto; text-align:center;">

    <h2>Processing Material</h2>

    <p style="font-size:13px; color:#777; margin-bottom:30px;">
      Transforming document into structured learning package
    </p>

<div style="height:4px; background:#eee; margin-top:20px;">
  <div id="progress" style="height:4px; width:0%; background:#1a1a1a;"></div>
</div>

    <div id="steps" style="text-align:left;">
      <div class="step">Analyzing document...</div>
      <div class="step">Detecting chapters...</div>
      <div class="step">Parsing assessments...</div>
      <div class="step">Extracting assets...</div>
      <div class="step">Structuring content...</div>
      <div class="step">Packaging .more file...</div>
    </div>

  </div>

</div>

</body>
</html>
<script>
const hasResult = <?= isset($resultData['stats']) ? 'true' : 'false' ?>;

const steps = document.querySelectorAll(".step");
let current = 0;
let progress = 0;

function updateBar() {
  progress += 100 / steps.length;
  document.getElementById("progress").style.width = progress + "%";
}

function nextStep() {
  if (current > 0) {
    steps[current - 1].classList.remove("active");
    steps[current - 1].classList.add("done");
  }

  if (current < steps.length) {
    steps[current].classList.add("active");
    updateBar();
    current++;
    setTimeout(nextStep, 500);
  } else {
    if (hasResult) {
      <?php if ($resultData && isset($resultData['stats'])): 
  $file = basename($resultData['file']);
  $job = basename(dirname($resultData['file']));
  $stats = $resultData['stats'];
?>

<?php 
$warningsEncoded = urlencode(json_encode($resultData['warnings'] ?? []));
?>

window.location.href = "result.php?file=<?= $file ?>&job=<?= $job ?>&c=<?= $stats['chapters'] ?>&a=<?= $stats['assessments'] ?>&i=<?= $stats['images'] ?>&w=<?= $warningsEncoded ?>";

<?php else: ?>
console.error("No result data available");
<?php endif; ?>
    } else {
      console.error("Processing failed");
    }
  }
}

nextStep();
</script>
