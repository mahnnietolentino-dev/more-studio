<?php
function processImages($html, $basePath) {

    $counter = 1;

    return preg_replace_callback('/<img[^>]*src="([^"]+)"[^>]*>/is', function ($matches) use ($basePath, &$counter) {

        $src = $matches[1];

// Resolve path properly
$originalPath = dirname(__FILE__) . '/' . ltrim($src, './');

echo "IMG SRC: $src\n";
echo "Resolved: $originalPath\n";
echo file_exists($originalPath) ? "✅ FOUND\n\n" : "❌ NOT FOUND\n\n";

if (!file_exists($originalPath)) {
    $originalPath = __DIR__ . '/' . ltrim($src, './');
}

        $newName = "img" . $counter . ".jpg";
        $newPath = $basePath . "assets/" . $newName;

        $cmd = "convert " . escapeshellarg($originalPath) .
               " -quality 70 " .
               escapeshellarg($newPath);

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            echo "❌ ImageMagick failed\n";
            return $matches[0];
        }

        echo "✅ Image optimized: $newName\n";

        $counter++;

        // Replace src
        $imgTag = $matches[0];

// 1. Replace src
$imgTag = preg_replace(
    '/src="[^"]+"/',
    'src="../assets/' . $newName . '"',
    $imgTag
);

// 2. Remove style attribute (width/height)
$imgTag = preg_replace('/\s*style="[^"]*"/i', '', $imgTag);

// 3. Optional: remove width/height attributes if present
$imgTag = preg_replace('/\s*(width|height)="[^"]*"/i', '', $imgTag);

return $imgTag;

    }, $html);
}
?>
