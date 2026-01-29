#!/bin/bash

# Script to clear token in HTTP test files (for git commit safety)
# Usage: ./routes/tests/scripts/clear_token.sh
# Dynamically finds all .http files in routes/tests/ directory

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TESTS_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

# Find all .http files in routes/tests/ directory
TEST_FILES=($(find "$TESTS_DIR" -maxdepth 1 -name "*.http" -type f))

if [ ${#TEST_FILES[@]} -eq 0 ]; then
    echo "Warning: No .http files found in $TESTS_DIR"
    exit 0
fi

cleared_count=0

for test_file in "${TEST_FILES[@]}"; do
    if [ ! -f "$test_file" ]; then
        continue
    fi

    # Clear the token in the HTTP file
    if [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        sed -i '' "s|^\(@token\s*=\).*|\1 |" "$test_file"
    else
        # Linux
        sed -i "s|^\(@token\s*=\).*|\1 |" "$test_file"
    fi

    if [ $? -eq 0 ]; then
        cleared_count=$((cleared_count + 1))
        filename=$(basename "$test_file")
        echo "Success: Token cleared in $filename"
    fi
done

if [ $cleared_count -eq 0 ]; then
    echo "No tokens found to clear."
else
    echo ""
    echo "Total: $cleared_count file(s) cleared."
fi
