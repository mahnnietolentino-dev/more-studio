<?php

require_once __DIR__ . '/chapter_parser.php';
require_once __DIR__ . '/process_images.php';
require_once __DIR__ . '/quiz_parser.php';
require_once __DIR__ . '/remove_quizblocks.php';
require_once __DIR__ . '/validate_quizblock.php';

function runPipeline($inputPath) {

    // 1. Create unique processing folder
    $jobId = uniqid('job_', true);
    $processingDir = __DIR__ . '/../storage/processing/' . $jobId;

    if (!is_dir($processingDir)) {
        mkdir($processingDir, 0777, true);
    }

    // 2. CALL YOUR MAIN FUNCTION HERE 🔥
    $result = buildPackage($inputPath, $processingDir);

    return $result;
}
?>

