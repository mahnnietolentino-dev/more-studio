<?php
//runPipeline($destination);

function buildPackage($inputPath, $processingDir)
{
    // 1. Convert DOCX → HTML
    $htmlPath = $processingDir . "/converted.html";

    $command = "pandoc "
        . escapeshellarg($inputPath)
        . " -o " . escapeshellarg($htmlPath)
        . " --extract-media=" . escapeshellarg($processingDir);

    exec($command, $outputLog, $returnCode);

    if ($returnCode !== 0) {
        return [
            'success' => false,
            'error' => 'Pandoc conversion failed'
        ];
    }

    // 2. Parse chapters
    $chapters = parseChapters($htmlPath);

    // 3. Create folders
    $base = $processingDir . "/";

    if (!is_dir($base . "chapters")) mkdir($base . "chapters", 0777, true);
    if (!is_dir($base . "quizzes")) mkdir($base . "quizzes", 0777, true);
    if (!is_dir($base . "assets")) mkdir($base . "assets", 0777, true);

    // 4. Process each chapter
    foreach ($chapters as $index => $ch) {

        $num = $index + 1;

        $processedHtml = processImages($ch["content"], $base);
		$cleanHtml = replaceQuizBlocks($processedHtml, $num);

		// 🔥 FINAL CLEAN (DOM-based)
		$cleanHtml = removeAllIds($cleanHtml);

		file_put_contents($base . "chapters/ch{$num}.html", $cleanHtml);

        $quizzes = parseQuizFromHtml($ch["content"]);

        file_put_contents(
            $base . "quizzes/ch{$num}.json",
            json_encode($quizzes, JSON_PRETTY_PRINT)
        );
    }

    // 5. Create manifest
    $xml = new SimpleXMLElement('<manifest/>');
    $metadata = $xml->addChild('metadata');

    $metadata->addChild('title', 'Sample Lesson');
    $metadata->addChild('format', 'MoRe');
    $metadata->addChild('formatVersion', '1.0');
    $metadata->addChild('formatCreator', 'Mahnnie Tolentino');
    $metadata->addChild('generator', 'MoRe Studio');
    $metadata->addChild('created', date('Y-m-d'));

    $chaptersNode = $xml->addChild('chapters');

    foreach ($chapters as $index => $ch) {
        $num = $index + 1;

        $chapterNode = $chaptersNode->addChild('chapter');
        $chapterNode->addAttribute('file', "chapters/ch{$num}.html");
        $chapterNode->addAttribute('title', htmlspecialchars($ch['title']));
    }

    $quizzesNode = $xml->addChild('quizzes');

    foreach ($chapters as $index => $ch) {
        $num = $index + 1;

        $quizNode = $quizzesNode->addChild('quiz');
        $quizNode->addAttribute('file', "quizzes/ch{$num}.json");
    }

    $manifestPath = $base . "manifest.xml";

    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save($manifestPath);

    // 6. Zip package
    if (!file_exists($htmlPath)) {
    	die("converted.html NOT FOUND at: " . $htmlPath);
	}
	
	$converted = $base . "converted.html";

	$zipPath = $base . "lesson.zip";
	
	// Delete converted.html BEFORE zipping
	if (file_exists($htmlPath)) {
    	unlink($htmlPath);
	}
	
    $zip = new ZipArchive();

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        return [
            'success' => false,
            'error' => 'Failed to create ZIP'
        ];
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    $baseRealPath = realpath($base) . DIRECTORY_SEPARATOR;

    foreach ($files as $file) {

        if (!$file->isDir()) {

            $filePath = $file->getRealPath();

            $filename = basename($filePath);

			if (
			    $filename === "lesson.zip" ||
			    $filename === "lesson.more" ||
			    $filename === "converted.html"
			) continue;

            $relativePath = str_replace($baseRealPath, '', $filePath);

            $zip->addFile($filePath, $relativePath);
        }
    }
    
    $zip->close();

    // 7. Rename to .more
    $morePath = $base . "lesson.more";
    rename($zipPath, $morePath);

    return [
        'success' => true,
        'file' => $morePath
    ];
}


function removeAllIds($html) {
$dom = new DOMDocument();

libxml_use_internal_errors(true);

$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

$xpath = new DOMXPath($dom);

// Remove all id attributes
foreach ($xpath->query('//*[@id]') as $node) {
    $node->removeAttribute('id');
}

// 🔥 Extract ONLY body content
$body = $dom->getElementsByTagName('body')->item(0);

$cleanHtml = '';
foreach ($body->childNodes as $child) {
    $cleanHtml .= $dom->saveHTML($child);
}

libxml_clear_errors();

return $cleanHtml;

}



/**
 * CHAPTER PARSER (UNCHANGED)
 */
function parseChapters($htmlFile)
{
    $html = file_get_contents($htmlFile);

    $parts = preg_split('/(<h1[^>]*>.*?<\/h1>)/is', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

    $chapters = [];

    for ($i = 1; $i < count($parts); $i += 2) {

    	$titleHtml = $parts[$i];

    	// ✅ ADD THIS LINE
    	//$titleHtml = preg_replace('/ id="[^"]*"/', '', $titleHtml);

    	$content = $parts[$i + 1] ?? '';

        $fullContent = $titleHtml . "\n" . $content;

		// Remove ALL id attributes
		//$fullContent = preg_replace('/\s*id="[^"]*"/i', '', $fullContent);

        $chapters[] = [
            "title" => trim(strip_tags($titleHtml)),
            "content" => $fullContent
        ];
    }

    return $chapters;
}
?>
