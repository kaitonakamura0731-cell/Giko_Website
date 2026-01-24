import os
import urllib.parse

# Root directory
ROOT_DIR = r"c:\Users\kaito\Desktop\技巧 -Giko-\Giko_Website"

def check_link(source_file, link):
    # Ignore externals and anchors
    if link.startswith(('http:', 'https:', 'mailto:', 'tel:', '#', 'javascript:')):
        return True
    
    # Strip query params and anchors for file existence check
    link_path = link.split('?')[0].split('#')[0]
    
    # Handle absolute path (rare in local static sites, but possible)
    if link_path.startswith('/'):
        # Assuming relative to project root
        target_abs = os.path.join(ROOT_DIR, link_path.lstrip('/'))
    else:
        # Relative to current file
        source_dir = os.path.dirname(source_file)
        target_abs = os.path.join(source_dir, link_path)
    
    target_abs = os.path.normpath(target_abs)
    
    return os.path.exists(target_abs)

def audit_file(filepath):
    errors = []
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
    except UnicodeDecodeError:
        try:
             with open(filepath, 'r', encoding='cp932') as f:
                content = f.read()
        except:
            return [f"Could not read file: {filepath}"]

    # Simple regex to find href and src
    import re
    # Match href="..." or src="..."
    # Group 2 is the link
    matches = re.finditer(r'(href|src)=["\']([^"\']+)["\']', content)
    
    for m in matches:
        attr = m.group(1)
        link = m.group(2)
        
        # Skip empty or strict anchors
        if not link or link.startswith('#'):
            continue
            
        if not check_link(filepath, link):
            errors.append(f"  [{attr}] Broken: '{link}'")
            
    return errors

def main():
    print("Starting Link Audit...")
    broken_count = 0
    for root, dirs, files in os.walk(ROOT_DIR):
        if ".git" in root or ".gemini" in root:
            continue
            
        for file in files:
            if file.lower().endswith(('.html', '.php')):
                path = os.path.join(root, file)
                file_errors = audit_file(path)
                if file_errors:
                    print(f"File: {os.path.relpath(path, ROOT_DIR)}")
                    for err in file_errors:
                        print(err)
                        broken_count += 1
    
    if broken_count == 0:
        print("PASS: No broken links found.")
    else:
        print(f"FAIL: Found {broken_count} broken links.")

if __name__ == "__main__":
    main()
