<?php

function parseQuizFromHtml($html)
{
    // Strip tags to simplify parsing
    $text = strip_tags($html);

    // Split into lines
    $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));

    $quizzes = [];
    $i = 0;

    while ($i < count($lines)) {

        if ($lines[$i] === '[QUIZ]') {

            $i++;

            $type = str_replace(['[', ']'], '', $lines[$i]);
            $i++;

            // QUESTION
            $question = $lines[$i];
            $i++;

            // MCQ handling
            if ($type === 'MCQ') {

                $choices = [];

                while ($i < count($lines) && preg_match('/^[A-Z]\./', $lines[$i])) {

                    $line = $lines[$i];

                    $isCorrect = str_contains($line, '*');

                    // Clean text
                    $line = str_replace('*', '', $line);
                    $line = preg_replace('/^[A-Z]\.\s*/', '', $line);

                    $choices[] = [
                        "text" => trim($line),
                        "correct" => $isCorrect
                    ];

                    $i++;
                }

                $quizzes[] = [
                    "type" => "MCQ",
                    "question" => $question,
                    "choices" => $choices
                ];
            }

            // Add after MCQ block

elseif ($type === 'TF') {

    // Expect: True or False (or True*, False*)
    $line = $lines[$i];

    $isTrue = str_contains($line, 'True');
    $isCorrect = str_contains($line, '*');

    $quizzes[] = [
        "type" => "TF",
        "question" => $question,
        "answer" => $isTrue ? ($isCorrect ? true : false) : ($isCorrect ? false : true)
    ];

    $i++;
}

elseif ($type === 'SHORT') {

    $answer = $lines[$i];
    $i++;

    $quizzes[] = [
        "type" => "SHORT",
        "question" => $question,
        "answer" => $answer
    ];
}

        } else {
            $i++;
        }
    }

    return $quizzes;
}