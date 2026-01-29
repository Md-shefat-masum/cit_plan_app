#!/usr/bin/env node

/**
 * Script to clear token in HTTP test files (for git commit safety)
 * Usage: node routes/tests/scripts/clear_token.js
 * 
 * Dynamically finds all .http files in routes/tests/ directory
 */

const fs = require('fs');
const path = require('path');

const testsDir = path.join(__dirname, '..');

try {
    // Find all .http files in routes/tests/ directory
    const files = fs.readdirSync(testsDir);
    const testFiles = files
        .filter(file => file.endsWith('.http'))
        .map(file => path.join(testsDir, file));

    if (testFiles.length === 0) {
        console.warn(`Warning: No .http files found in ${testsDir}`);
        process.exit(0);
    }

    let clearedCount = 0;

    testFiles.forEach(testFile => {
        if (!fs.existsSync(testFile)) {
            return;
        }

        let content = fs.readFileSync(testFile, 'utf8');

        // Replace the token variable line with empty value
        const pattern = /^(@token\s*=)\s*.*$/m;
        const replacement = `$1 `;

        const updatedContent = content.replace(pattern, replacement);

        if (updatedContent !== content) {
            fs.writeFileSync(testFile, updatedContent, 'utf8');
            clearedCount++;
            const fileName = path.basename(testFile);
            console.log(`Success: Token cleared in ${fileName}`);
        }
    });

    if (clearedCount === 0) {
        console.log('No tokens found to clear.');
    } else {
        console.log(`\nTotal: ${clearedCount} file(s) cleared.`);
    }
} catch (error) {
    console.error('Error:', error.message);
    process.exit(1);
}
