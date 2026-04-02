<?php
function parseQuizFromHtml($html)
{
    $quizBlocks = [];

    // 1. Extract QUIZ blocks
    preg_match_all('/\[QUIZ\]((?:(?!\[QUIZ\]).)*?)\[\/QUIZ\]/s', $html, $blocks);

    foreach ($blocks[1] as $block) {

        $questions = [];

        // 2. Extract types inside
        preg_match_all('/\[(MCQ|TF|SHORT)\](.*?)\[\/\1\]/s', $block, $types, PREG_SET_ORDER);

        foreach ($types as $t) {

            $type = $t[1];
            $content = trim(strip_tags($t[2]));

            $lines = array_values(array_filter(array_map('trim', explode("\n", $content))));

            // ---- MCQ ----
            if ($type === 'MCQ') {

                $question = array_shift($lines);
                $choices = [];

                foreach ($lines as $line) {

                    if (!preg_match('/^[A-Z]\./', $line)) continue;

                    $isCorrect = str_contains($line, '*');

                    $line = str_replace('*', '', $line);
                    $line = preg_replace('/^[A-Z]\.\s*/', '', $line);

                    $choices[] = [
                        "text" => trim($line),
                        "correct" => $isCorrect
                    ];
                }

                $questions[] = [
                    "type" => "MCQ",
                    "question" => $question,
                    "choices" => $choices
                ];
            }

            // ---- TF ----
            elseif ($type === 'TF') {

                $question = $lines[0] ?? '';
                $answerLine = $lines[1] ?? '';

                $isTrue = str_contains($answerLine, 'True');
                $isCorrect = str_contains($answerLine, '*');

                $answer = $isTrue ? $isCorrect : !$isCorrect;

                $questions[] = [
                    "type" => "TF",
                    "question" => $question,
                    "answer" => $answer
                ];
            }

            // ---- SHORT ----
            elseif ($type === 'SHORT') {

                $question = $lines[0] ?? '';
                $answer = $lines[1] ?? '';

                $questions[] = [
                    "type" => "SHORT",
                    "question" => $question,
                    "answer" => $answer
                ];
            }
        }

        // ✅ THIS IS THE KEY FIX
        $quizBlocks[] = [
            "questions" => $questions
        ];
    }

    return $quizBlocks;
}
?>
