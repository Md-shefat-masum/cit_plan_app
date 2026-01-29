#!/bin/bash

# Script to update token in HTTP test files from token.txt
# Usage: ./routes/tests/scripts/update_token.sh
# Dynamically finds all .http files in routes/tests/ directory

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../../.." && pwd)"
TOKEN_FILE="$PROJECT_ROOT/token.txt"
TESTS_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

if [ ! -f "$TOKEN_FILE" ]; then
    echo "Error: token.txt not found at $TOKEN_FILE"
    exit 1
fi

TOKEN=$(cat "$TOKEN_FILE" | tr -d '\n\r ')

if [ -z "$TOKEN" ]; then
    echo "Error: token.txt is empty"
    exit 1
fi

# Find all .http files in routes/tests/ directory
TEST_FILES=($(find "$TESTS_DIR" -maxdepth 1 -name "*.http" -type f))

if [ ${#TEST_FILES[@]} -eq 0 ]; then
    echo "Warning: No .http files found in $TESTS_DIR"
    exit 0
fi

updated_count=0

for test_file in "${TEST_FILES[@]}"; do
    if [ ! -f "$test_file" ]; then
        continue
    fi

    # Update the token in the HTTP file
    if [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        sed -i '' "s|^\(@token\s*=\).*|\1 $TOKEN|" "$test_file"
    else
        # Linux
        sed -i "s|^\(@token\s*=\).*|\1 $TOKEN|" "$test_file"
    fi

    if [ $? -eq 0 ]; then
        updated_count=$((updated_count + 1))
        filename=$(basename "$test_file")
        echo "Success: Token updated in $filename"
    fi
done

if [ $updated_count -eq 0 ]; then
    echo "Warning: No token variables found or already updated"
else
    echo ""
    echo "Total: $updated_count file(s) updated."
    echo "Token: ${TOKEN:0:20}..."
fi
