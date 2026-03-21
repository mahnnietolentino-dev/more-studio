<?php
function replaceQuizBlocks($html, $chapterNum)
{
    $index = 0;

    return preg_replace_callback(
        '/<p>\s*\[QUIZ\](.*?)\[\/QUIZ\]\s*<\/p>/s',
        function ($matches) use ($chapterNum, &$index) {

            $index++;

            return '<div class="quiz" data-src="../quizzes/ch' 
                . $chapterNum . '.json" data-index="' . $index . '"></div>';
        },
        $html
    );
}
?>
