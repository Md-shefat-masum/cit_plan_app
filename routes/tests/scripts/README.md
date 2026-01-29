# HTTP Test Files - Token Management

## Overview
This directory contains HTTP test files for API testing. The token is automatically synced from `token.txt` in the project root.

## Updating Token Automatically

### Method 1: Using PHP Script (Recommended)
```bash
php routes/tests/update_token.php
```

### Method 2: Using Node.js Script
```bash
node routes/tests/update_token.js
```

### Method 3: Using Bash Script
```bash
./routes/tests/update_token.sh
```

### Method 4: Using VS Code Task
1. Press `Ctrl+Shift+P` (or `Cmd+Shift+P` on Mac)
2. Type "Tasks: Run Task"
3. Select "Update Token in HTTP Test Files"

## Clearing Token (Before Git Push)

**IMPORTANT:** Always clear tokens before committing to git to avoid exposing sensitive data.

### Method 1: Using PHP Script (Recommended)
```bash
php routes/tests/clear_token.php
```

### Method 2: Using Node.js Script
```bash
node routes/tests/clear_token.js
```

### Method 3: Using Bash Script
```bash
./routes/tests/clear_token.sh
```

### Method 4: Using VS Code Task
1. Press `Ctrl+Shift+P` (or `Cmd+Shift+P` on Mac)
2. Type "Tasks: Run Task"
3. Select "Clear Token in HTTP Test Files"

### Automatic Clearing (Git Pre-commit Hook)
A git pre-commit hook is installed that automatically clears tokens before each commit. This ensures tokens are never accidentally committed.

## How It Works

### Updating Token:
1. The script reads the token from `token.txt` in the project root
2. It updates the `@token` variable in `role_test.http`
3. All API requests in the test file will use the updated token

### Clearing Token:
1. The script sets the `@token` variable to empty in `role_test.http`
2. This prevents tokens from being committed to git
3. The git pre-commit hook automatically runs this before each commit

## Usage Workflow

1. **Before Testing:**
   ```bash
   php routes/tests/update_token.php
   ```

2. **After Testing / Before Git Push:**
   ```bash
   php routes/tests/clear_token.php
   ```

3. **Or let git handle it automatically** - The pre-commit hook will clear tokens automatically

## Notes
- Make sure `token.txt` contains a valid JWT token
- The token will be automatically trimmed of whitespace
- If the token is empty or the file doesn't exist, the script will show an error
- **Always clear tokens before pushing to git** (or use the pre-commit hook)
