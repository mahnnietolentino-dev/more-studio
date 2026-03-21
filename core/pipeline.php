<?php
function runPipeline($inputPath) {
    require __DIR__ . '/chapter_parser.php';
	require __DIR__ . '/process_images.php';
	require __DIR__ . '/quiz_parser.php';
	require __DIR__ . '/remove_quizblocks.php';
	require __DIR__ . '/validate_quizblock.php';

    return $moreFilePath;
}
?>
