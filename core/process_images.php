<?php
function processImages($html, $basePath) {

    $counter = 0;

    $html = preg_replace_callback('/<img[^>]*src="([^"]+)"[^>]*>/is', function ($matches) use ($basePath, &$counter) {

        $src = $matches[1];

        // Resolve original path
        if (strpos($src, '/') === 0) {
            $originalPath = $src;
        } else {
            $originalPath = $basePath . ltrim($src, './');
        }

        // Prepare output
        $newName = uniqid('img_', true) . ".jpg";
        $newPath = $basePath . "assets/" . $newName;

        $finalSrc = "../media/" . basename($src); // fallback

        if (file_exists($originalPath)) {

            // 🔥 ImageMagick command
            $cmd = "convert " . escapeshellarg($originalPath) . " " .
                "-auto-orient " .
                "-resize 1024x1024\\> " .        // ✅ max width 1024, no upscale
                "-background white -alpha remove -alpha off " . // ✅ handle PNG transparency
                "-strip " .
                "-quality 70 " .
                escapeshellarg($newPath);

            exec($cmd . " 2>&1", $output, $returnCode);

            if ($returnCode === 0 && file_exists($newPath)) {
                $finalSrc = "../assets/" . $newName;
                $counter++;
            }
        }

        // Clean img tag
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
