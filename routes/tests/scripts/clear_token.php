<?php

/**
 * Script to clear token in HTTP test files (for git commit safety)
 * Usage: php routes/tests/scripts/clear_token.php
 * 
 * Dynamically finds all .http files in routes/tests/ directory
 */

$testsDir = dirname(__DIR__);

// Find all .http files in routes/tests/ directory
$testFiles = glob($testsDir . '/*.http');

if (empty($testFiles)) {
    echo "Warning: No .http files found in {$testsDir}\n";
    exit(0);
}

$clearedCount = 0;

foreach ($testFiles as $testFile) {
    if (!file_exists($testFile)) {
        continue;
    }

    $content = file_get_contents($testFile);

    // Replace the token variable line with empty value
    $pattern = '/^(@token\s*=)\s*.*$/m';
    $replacement = '$1 ';

    $updatedContent = preg_replace($pattern, $replacement, $content);

    if ($updatedContent !== $content) {
        file_put_contents($testFile, $updatedContent);
        $clearedCount++;
        $fileName = basename($testFile);
        echo "Success: Token cleared in {$fileName}\n";
    }
}

if ($clearedCount === 0) {
    echo "No tokens found to clear.\n";
} else {
    echo "\nTotal: {$clearedCount} file(s) cleared.\n";
}
