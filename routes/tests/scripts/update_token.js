#!/usr/bin/env node

/**
 * Script to update token in HTTP test files from token.txt
 * Usage: node routes/tests/scripts/update_token.js
 * 
 * Dynamically finds all .http files in routes/tests/ directory
 */

const fs = require('fs');
const path = require('path');

const projectRoot = path.join(__dirname, '../..');
const tokenFile = path.join(projectRoot, 'token.txt');
const testsDir = path.join(__dirname, '..');

try {
    // Read token from token.txt
    if (!fs.existsSync(tokenFile)) {
        console.error(`Error: token.txt not found at ${tokenFile}`);
        process.exit(1);
    }

    const token = fs.readFileSync(tokenFile, 'utf8').trim();

    if (!token) {
        console.error('Error: token.txt is empty');
        process.exit(1);
    }

    // Find all .http files in routes/tests/ directory
    const files = fs.readdirSync(testsDir);
    const testFiles = files
        .filter(file => file.endsWith('.http'))
        .map(file => path.join(testsDir, file));

    if (testFiles.length === 0) {
        console.warn(`Warning: No .http files found in ${testsDir}`);
        process.exit(0);
    }

    let updatedCount = 0;

    testFiles.forEach(testFile => {
        if (!fs.existsSync(testFile)) {
            return;
        }

        let content = fs.readFileSync(testFile, 'utf8');

        // Replace the token variable line
        const pattern = /^(@token\s*=)\s*.*$/m;
        const replacement = `$1 ${token}`;

        const updatedContent = content.replace(pattern, replacement);

        if (updatedContent !== content) {
            fs.writeFileSync(testFile, updatedContent, 'utf8');
            updatedCount++;
            const fileName = path.basename(testFile);
            console.log(`Success: Token updated in ${fileName}`);
        }
    });

    if (updatedCount === 0) {
        console.warn('Warning: No token variables found or already updated');
    } else {
        console.log(`\nTotal: ${updatedCount} file(s) updated.`);
        console.log(`Token: ${token.substring(0, 20)}...`);
    }
} catch (error) {
    console.error('Error:', error.message);
    process.exit(1);
}
