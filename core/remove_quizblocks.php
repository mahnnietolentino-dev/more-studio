<?php
function replaceQuizBlocks($html, $chapterNum) {

    $quizIndex = 1;

    return preg_replace_callback('/\[QUIZ\].*?\[\/QUIZ\]/s', function () use ($chapterNum, &$quizIndex) {

        $file = "quiz_" . $chapterNum . "_" . $quizIndex . ".json";

        $quizIndex++;

        return '
        <div class="quiz-block">
            <a href="../quizzes/' . $file . '">Start Quiz</a>
        </div>
        ';

    }, $html);
}
?>
