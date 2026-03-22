<?php

require_once __DIR__ . '/chapter_parser.php';
require_once __DIR__ . '/process_images.php';
require_once __DIR__ . '/quiz_parser.php';
require_once __DIR__ . '/remove_quizblocks.php';
require_once __DIR__ . '/validate_quizblock.php';

function runPipeline($inputPath, $authorId, $contributors) {

    // 1. Create unique processing folder
    $jobId = uniqid('job_', true);
    $processingDir = __DIR__ . '/../storage/processing/' . $jobId;

    if (!is_dir($processingDir)) {
        mkdir($processingDir, 0777, true);
    }

    // 2. CALL YOUR MAIN FUNCTION HERE 🔥
    $result = buildPackage($inputPath, $processingDir, $authorId, $contributors);

    return $result;
}

function generateContributorsXML($contributors) {
    $xml = "<contributors>";

    foreach ($contributors as $person) {
        $xml .= "<person role=\"{$person['role']}\">";

        if (isset($person['id'])) {
            $xml .= "<id>{$person['id']}</id>";
        }

        $xml .= "<name>{$person['name']}</name>";
        $xml .= "</person>";
    }

    $xml .= "</contributors>";

    return $xml;
}
?>

