import json
import re

file_path = 'database/data/movements.json'

equipment_words = ['Barbell', 'Machine', 'Smith Machine', 'Band', 'Dumbbell', 'Kettlebell', 'Cable']

def capitalize_every_word(s):
    # This regex finds "words" which are sequences of letters.
    # We want to capitalize the first letter of each sequence.
    # However, we should be careful not to break existing logic.
    # Let's use a simpler approach:
    # Replace any character that starts a word (after a non-alphanumeric or at start) with its uppercase.

    # Actually, a simple regex replace with a function can work.
    return re.sub(r'([a-zA-Z])([a-zA-Z]*)', lambda m: m.group(1).upper() + m.group(2).lower(), s)

# Open and read lines
with open(file_path, 'r') as f:
    # Try to load as JSON first to get clean strings if it's already valid-ish
    # But it was not valid JSON because of line 30-90.
    lines = f.readlines()

processed_names = []

for line in lines:
    line = line.strip()
    if not line or line in ['[', ']', ',']:
        continue

    # Remove quotes and comma
    clean_line = line.strip('", ')

    # Normalize: Remove parentheses from equipment words first
    # This helps in case they were partially parenthesized or inconsistent
    for eq in equipment_words:
        pattern = re.compile(rf'\({re.escape(eq)}\)', re.IGNORECASE)
        clean_line = pattern.sub(eq, clean_line)

    # Capitalize everything first
    clean_line = capitalize_every_word(clean_line)

    # Now wrap equipment words in parentheses
    # Sort equipment words by length descending
    sorted_eq = sorted(equipment_words, key=len, reverse=True)

    for eq in sorted_eq:
        # Use placeholders to avoid double wrapping
        placeholder = f"___{eq.upper().replace(' ', '_')}___"
        # Match eq as whole word
        pattern = re.compile(rf'\b{re.escape(eq)}\b', re.IGNORECASE)
        if pattern.search(clean_line):
            clean_line = pattern.sub(placeholder, clean_line)

    for eq in sorted_eq:
        placeholder = f"___{eq.upper().replace(' ', '_')}___"
        # Ensure capitalization of equipment word itself matches the requirement
        # (Though capitalize_every_word should have handled it)
        clean_line = clean_line.replace(placeholder, f"({eq})")

    # Final cleanup:
    # Ensure space before open parenthesis if it follows a word
    clean_line = re.sub(r'([a-zA-Z0-9])\(', r'\1 (', clean_line)
    # Remove double spaces
    clean_line = re.sub(r'\s+', ' ', clean_line).strip()

    processed_names.append(clean_line)

# Write back as JSON-like format but manually to control formatting
with open(file_path, 'w') as f:
    f.write('[\n')
    for i, name in enumerate(processed_names):
        comma = ',' if i < len(processed_names) - 1 else ''
        f.write(f'    "{name}"{comma}\n')
    f.write(']\n')
