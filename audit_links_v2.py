import os
import sys
import re
import urllib.parse

# Force UTF-8 for stdout/stderr
sys.stdout.reconfigure(encoding='utf-8')
sys.stderr.reconfigure(encoding='utf-8')

ROOT_DIR = r"c:\Users\kaito\Desktop\技巧 -Giko-\Giko_Website"

def check_path_exists(source_file, relative_link):
    # Decode URL encoding (e.g. %20 -> space)
    relative_link = urllib.parse.unquote(relative_link)
    
    # Clean query params and anchors
    clean_link = relative_link.split('?')[0].split('#')[0]
    
    if not clean_link:
        # Link to just '#' or '?' or empty
        return True

    # Check if absolute path (from project root)
    if clean_link.startswith('/'):
        target_path = os.path.join(ROOT_DIR, clean_link.lstrip('/'))
    else:
        # Relative path
        source_dir = os.path.dirname(source_file)
        target_path = os.path.join(source_dir, clean_link)
    
    target_path = os.path.normpath(target_path)
    return os.path.exists(target_path)

def audit_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
    except Exception:
        return [] # Skip unreadable

    broken_links = []
    
    # Regex for href and src
    # Matches href="val" or src='val'
    matches = re.finditer(r'(href|src)=["\']([^"\']+)["\']', content)
    
    for m in matches:
        attr = m.group(1)
        link = m.group(2)
        
        # Skip special schemes
        if link.startswith(('http:', 'https:', 'mailto:', 'tel:', 'javascript:')):
            continue
            
        # Skip anchor-only links unless they point to a file
        if link.startswith('#'):
            continue
            
        if not check_path_exists(filepath, link):
            broken_links.append(f"  [{attr}] Broken link: '{link}'")

    return broken_links

def main():
    print(f"Scanning root: {ROOT_DIR}")
    count = 0
    for root, dirs, files in os.walk(ROOT_DIR):
        if ".git" in root or "node_modules" in root:
            continue
            
        for file in files:
            if file.lower().endswith(('.html', '.php', '.js', '.css')):
                full_path = os.path.join(root, file)
                errors = audit_file(full_path)
                if errors:
                    # Print relative path for readability
                    rel_path = os.path.relpath(full_path, ROOT_DIR)
                    print(f"File: {rel_path}")
                    for err in errors:
                        print(err)
                        count += 1
    
    if count == 0:
        print("SUCCESS: No broken links found.")
    else:
        print(f"Found {count} broken links.")

if __name__ == "__main__":
    main()
