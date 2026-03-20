<?php
function removeQuizBlocks($html) {

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // Find all nodes containing [QUIZ]
    foreach ($xpath->query("//*[contains(text(),'[QUIZ]')]") as $node) {

        $current = $node;

        // Remove until next non-quiz block
        while ($current && strpos($current->textContent, '[QUIZ]') !== false) {
            $next = $current->nextSibling;
            $current->parentNode->removeChild($current);
            $current = $next;
        }
    }

    return $dom->saveHTML();
}
?>