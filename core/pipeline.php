<?php

require_once __DIR__ . '/chapter_parser.php';
require_once __DIR__ . '/process_images.php';
require_once __DIR__ . '/quiz_parser.php';
require_once __DIR__ . '/remove_quizblocks.php';
require_once __DIR__ . '/validate_quizblock.php';

function generatePackageMeta() {
    return [
        'package_id' => 'pkg_' . bin2hex(random_bytes(6)),
        'version' => 1
    ];
}

function runPipeline($inputPath, $authorId, $contributors, $title, $subject, $conn) {

    // 1. Create processing folder
    $jobId = uniqid('job_', true);
    $processingDir = __DIR__ . '/../storage/processing/' . $jobId;

    if (!is_dir($processingDir)) {
        mkdir($processingDir, 0777, true);
    }

    // 2. Generate package meta
	$meta = generatePackageMeta();
	$packageId = $meta['package_id'];
	$version = getNextVersion($conn, $authorId, $title);

    // 3. Build package
    $result = buildPackage($inputPath, $processingDir, $authorId, $contributors, $packageId, $version,  $title, $subject);

    // 4. Register ONLY if success
    if ($result['success']) {
        registerPackage($conn, $packageId, $authorId, $title, $subject, $version);
    }

    return $result;
}

function registerPackage($conn, $packageId, $authorId, $title, $subject, $version) {
    $stmt = $conn->prepare("
    	INSERT INTO packages (package_id, author_id, title, subject, version, created_at)
    	VALUES (?, ?, ?, ?, ?, NOW())
	");

	$stmt->bind_param("ssssi", $packageId, $authorId, $title, $subject, $version);
    $stmt->execute();
}

function getNextVersion($conn, $authorId, $title) {
    $stmt = $conn->prepare("
        SELECT MAX(version) as max_version
        FROM packages
        WHERE author_id = ? AND title = ?
    ");

    $stmt->bind_param("ss", $authorId, $title);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    return ($result['max_version'] ?? 0) + 1;
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

