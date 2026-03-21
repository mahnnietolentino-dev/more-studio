<?php
function validateQuizBlock($block)
{
    $errors = [];

    if (!str_contains($block, '[/QUIZ]')) {
        $errors[] = "Missing [/QUIZ]";
    }

    if (preg_match_all('/\[(.*?)\]/', $block, $tags)) {

        $valid = ['QUIZ', '/QUIZ', 'MCQ', '/MCQ', 'TF', '/TF', 'SHORT', '/SHORT'];

        foreach ($tags[1] as $tag) {
            if (!in_array($tag, $valid)) {
                $errors[] = "Unknown tag [$tag]";
            }
        }
    }

    return $errors;
}
?>
