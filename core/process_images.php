<?php
function processImages($html, $basePath) {

$counter = 0;
$index = 1;

$html = preg_replace_callback('/<img[^>]*src="([^"]+)"[^>]*>/is', function ($matches) use ($basePath, &$counter, &$index) {

    $src = $matches[1];

    // input (from pandoc)
    if (strpos($src, '/') === 0) {
    // absolute path → use directly
    $originalPath = $src;
} else {
    // relative path → resolve normally
    $originalPath = $basePath . ltrim($src, './');
}

    // output (your system)
    $newName = uniqid('img_', true) . ".jpg";
	$newPath = $basePath . "assets/" . $newName;

    $finalSrc = "../media/" . basename($src); // fallback

    if (file_exists($originalPath)) {

        $cmd = "convert " . escapeshellarg($originalPath) .
       " -auto-orient -strip -quality 70 JPG:" .
       escapeshellarg($newPath);
       
        exec($cmd . " 2>&1", $output, $returnCode);

        if ($returnCode === 0) {
            $finalSrc = "../assets/" . $newName;
            $counter++;
        }
    }

    $index++;

    $imgTag = $matches[0];

    $imgTag = preg_replace('/src="[^"]+"/', 'src="' . $finalSrc . '"', $imgTag);
    $imgTag = preg_replace('/\s*style="[^"]*"/i', '', $imgTag);
    $imgTag = preg_replace('/\s*(width|height)="[^"]*"/i', '', $imgTag);

    return $imgTag;

}, $html);

return [
    'html' => $html,
    'count' => $counter
];
}
?>
