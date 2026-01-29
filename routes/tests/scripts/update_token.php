<?php

/**
 * Script to update token in HTTP test files from token.txt
 * Usage: php routes/tests/scripts/update_token.php
 * 
 * Dynamically finds all .http files in routes/tests/ directory
 */

$projectRoot = dirname(__DIR__, 3);
$tokenFile = $projectRoot . '/token.txt';
$testsDir = dirname(__DIR__);

// Find all .http files in routes/tests/ directory
$testFiles = glob($testsDir . '/*.http');

if (!file_exists($tokenFile)) {
    echo "Error: token.txt not found at {$tokenFile}\n";
    exit(1);
}

$token = trim(file_get_contents($tokenFile));

if (empty($token)) {
    echo "Error: token.txt is empty\n";
    exit(1);
}

if (empty($testFiles)) {
    echo "Warning: No .http files found in {$testsDir}\n";
    exit(0);
}

$updatedCount = 0;

foreach ($testFiles as $testFile) {
    if (!file_exists($testFile)) {
        continue;
    }

    $content = file_get_contents($testFile);

    // Replace the token variable line
    $pattern = '/^(@token\s*=)\s*.*$/m';
    $replacement = '$1 ' . $token;

    $updatedContent = preg_replace($pattern, $replacement, $content);

    if ($updatedContent !== $content) {
        file_put_contents($testFile, $updatedContent);
        $updatedCount++;
        $fileName = basename($testFile);
        echo "Success: Token updated in {$fileName}\n";
    }
}

if ($updatedCount === 0) {
    echo "Warning: No token variables found or already updated\n";
} else {
    echo "\nTotal: {$updatedCount} file(s) updated.\n";
    echo "Token: " . substr($token, 0, 20) . "...\n";
}
