<?php
function processImages($html, $basePath) {

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $images = $dom->getElementsByTagName('img');

    $counter = 1;

    foreach ($images as $img) {

        $src = $img->getAttribute('src');

        if (!$src) continue;

        // Original path
        $originalPath = __DIR__ . "/" . $src;

echo "IMG SRC: $src\n";
echo "Trying: " . __DIR__ . "/" . $src . "\n";

if (!file_exists(__DIR__ . "/" . $src)) {
    echo "❌ NOT FOUND\n\n";
} else {
    echo "✅ FOUND\n\n";
}
        
        //if (!file_exists($originalPath)) continue;

        // New filename
        $newName = "img" . $counter . ".jpg";
        $newPath = $basePath . "assets/" . $newName;

        // Compress using ImageMagick
        $cmd = "convert " . escapeshellarg($originalPath) .
               " -quality 70 " .
               escapeshellarg($newPath);

        exec($cmd, $output, $returnCode);

if ($returnCode !== 0) {
    echo "❌ ImageMagick failed: $cmd\n";
} else {
    echo "✅ Image optimized: $newName\n";
}

        // Update HTML path
        $img->setAttribute('src', "../assets/" . $newName);

        $counter++;
    }

    return $dom->saveHTML();
}
?>