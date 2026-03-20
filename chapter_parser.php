<?php
require 'quiz_parser.php';
require 'process_images.php';
require 'remove_quizblocks.php';

$input = "test.docx";
$output = "output.html";

// Build command safely
$command = "pandoc " 
    . escapeshellarg($input) 
    . " -o " . escapeshellarg($output) 
    . " --extract-media=.";

// Execute command
exec($command, $outputLog, $returnCode);

// Output results
echo "Command: $command\n\n";

if ($returnCode === 0) {
    echo "✅ Conversion successful!\n";
} else {
    echo "❌ Conversion failed!\n";
}

echo "\nOutput log:\n";
print_r($outputLog);


function parseChapters($htmlFile)
{
    $html = file_get_contents($htmlFile);

    // Load into DOM
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // suppress warnings
    $dom->loadHTML($html);
    libxml_clear_errors();

    $body = $dom->getElementsByTagName('body')->item(0);

    $chapters = [];
    $currentChapter = null;

    foreach ($body->childNodes as $node) {

        // Detect <h1> = new chapter
        if ($node->nodeName === 'h1') {

            // Save previous chapter
            if ($currentChapter !== null) {
                $chapters[] = $currentChapter;
            }

            $currentChapter = [
    "title" => trim($node->textContent),
    "content" => $dom->saveHTML($node) // ✅ include the h1
];

        } else {
            // Append content to current chapter
            if ($currentChapter !== null) {
                $currentChapter["content"] .= $dom->saveHTML($node);
            }
        }
    }

    // Save last chapter
    if ($currentChapter !== null) {
        $chapters[] = $currentChapter;
    }

    return $chapters;
}


// 🔍 TEST IT

$chapters = parseChapters("output.html");

$buildId = "build_" . date("Ymd_His");
$base = "storage/processing/" . $buildId . "/";

// Create folders
if (!is_dir($base . "chapters")) {
    mkdir($base . "chapters", 0777, true);
}

if (!is_dir($base . "quizzes")) {
    mkdir($base . "quizzes", 0777, true);
}

if (!is_dir($base . "assets")) {
    mkdir($base . "assets", 0777, true);
}

foreach ($chapters as $index => $ch) {

    $num = $index + 1;
	 $processedHtml = processImages($ch["content"], $base);
	 
    // Save chapter
    // Original (backup)
/*file_put_contents(
    $base . "chapters/ch{$num}_original.html",
    $ch["content"]
);*/

// Quiz cleanup
$cleanHtml = removeQuizBlocks($processedHtml);

// Processed version
file_put_contents(
    $base . "chapters/ch{$num}.html",
    $cleanHtml
);

    echo "Saved: chapters/ch{$num}.html\n";

    // Extract quizzes
    $quizzes = parseQuizFromHtml($ch["content"]);

    // Save quiz
    file_put_contents(
        $base . "quizzes/ch{$num}.json",
        json_encode($quizzes, JSON_PRETTY_PRINT)
    );

    echo "Saved: quizzes/ch{$num}.json\n\n";
}

$xml = new SimpleXMLElement('<manifest/>');
$metadata = $xml->addChild('metadata');

$metadata->addChild('title', 'Sample Lesson');

$metadata->addChild('format', 'MoRe');
$metadata->addChild('formatVersion', '1.0');
$metadata->addChild('formatCreator', 'Mahnnie Tolentino');

$metadata->addChild('generator', 'MoRe Studio');

$metadata->addChild('created', date('Y-m-d'));


// Chapters node
$chaptersNode = $xml->addChild('chapters');

foreach ($chapters as $index => $ch) {

    $num = $index + 1;

    $chapterNode = $chaptersNode->addChild('chapter');

    $chapterNode->addAttribute('file', "chapters/ch{$num}.html");
    $chapterNode->addAttribute('title', htmlspecialchars($ch['title']));
}

// Quizzes node
$quizzesNode = $xml->addChild('quizzes');

foreach ($chapters as $index => $ch) {

    $num = $index + 1;

    $quizNode = $quizzesNode->addChild('quiz');
    $quizNode->addAttribute('file', "quizzes/ch{$num}.json");
}

// Save manifest
$manifestPath = $base . "manifest.xml";

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;

$dom->loadXML($xml->asXML());
$dom->save($manifestPath);

echo "Saved: manifest.xml\n";


$zipPath = $base . "lesson.zip";

$zip = new ZipArchive();

if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {

        if (!$file->isDir()) {

            $filePath = $file->getRealPath();

            // Skip the zip itself if rerunning
            if (basename($filePath) === "lesson.zip") continue;

            // Relative path inside zip
            $baseRealPath = realpath($base) . DIRECTORY_SEPARATOR;

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $file) {

    if (!$file->isDir()) {

        $filePath = $file->getRealPath();

        // Skip zip itself
        if (
    basename($filePath) === "lesson.zip" ||
    basename($filePath) === "lesson.more"
) continue;

        // ✅ CLEAN relative path
        $relativePath = str_replace($baseRealPath, '', $filePath);

        $zip->addFile($filePath, $relativePath);
    }
}

            $zip->addFile($filePath, $relativePath);
        }
    }

    $zip->close();

    echo "ZIP created: $zipPath\n";

} else {
    die("❌ Failed to create ZIP\n");
}

$morePath = $base . "lesson.more";

rename($zipPath, $morePath);

echo "Package ready: $morePath\n";