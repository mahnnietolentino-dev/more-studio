<?php

function detectMCQ($text) {
    $pattern = '/^\s*(\d+[\.\)]\s*)?(.+?)\n((\s*[A-Da-d][\.\)]\s+.+\n){2,})/m';
    preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);
    return $matches;
}

function convertMCQ($matches) {
    $output = [];

    foreach ($matches as $m) {
        $block = "[MCQ]\n";
        $block .= trim($m[2]) . "\n";

        preg_match_all('/[A-Da-d][\.\)]\s+(.+)/', $m[0], $choices);

        foreach ($choices[1] as $choice) {
            $block .= "- " . trim($choice) . "\n";
        }

        $block .= "[/MCQ]";
        $output[] = $block;
    }

    return $output;
}

function applyAutoFixMCQ($text) {
    if (strpos($text, '[MCQ]') !== false) return $text;

    $matches = detectMCQ($text);
    $converted = convertMCQ($matches);

    foreach ($matches as $i => $m) {
        $text = str_replace($m[0], $converted[$i], $text);
    }

    return $text;
}

?>
