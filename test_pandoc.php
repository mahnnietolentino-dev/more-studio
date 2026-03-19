<?php

$input = "test.docx";
$output = "output.html";

// Build command safely
$command = "pandoc " . escapeshellarg($input) . " -o " . escapeshellarg($output);

// Execute command
exec($command, $outputLog, $returnCode);

// Output results
echo "Command: $command\n\n";

if ($returnCode === 0) {
    echo "✅ Conversion successful!\n";
} else {
    echo "❌ Conversion failed!\n";
}

echo "\nOutput log:\n";
print_r($outputLog);