<?php
require 'quiz_parser.php';

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
                "content" => ""
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

echo "Parsed Chapters:\n\n";

foreach ($chapters as $index => $ch) {

    echo "Chapter " . ($index + 1) . ": " . $ch["title"] . "\n";
    echo "----------------------\n";

    $quizzes = parseQuizFromHtml($ch["content"]);

    print_r($quizzes);

    echo "\n\n";
}