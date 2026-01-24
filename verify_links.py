import os
import re
from urllib.parse import unquote

ROOT_DIR = r"c:\Users\kaito\Desktop\技巧 -Giko-\Giko_Website"

def find_files(root_dir, extensions):
    matches = []
    for root, dirnames, filenames in os.walk(root_dir):
        for filename in filenames:
            if any(filename.endswith(ext) for ext in extensions):
                matches.append(os.path.join(root, filename))
    return matches

def resolve_path(base_file, relative_path):
    if relative_path.startswith(('http://', 'https://', '#', 'mailto:', 'tel:')):
        return None  # External or anchor
    
    # Strip query params and anchors for file check
    clean_path = relative_path.split('?')[0].split('#')[0]
    
    # Handle root relative (rare in this setup but possible)
    if clean_path.startswith('/'):
        target = os.path.join(ROOT_DIR, clean_path.lstrip('/'))
    else:
        target = os.path.join(os.path.dirname(base_file), clean_path)
    
    return os.path.normpath(target)

def check_links():
    files = find_files(ROOT_DIR, ['.html', '.php'])
    errors = []
    
    link_pattern = re.compile(r'(?:href|src)=["\']([^"\']+)["\']')
    
    print(f"Scanning {len(files)} files...")
    
    for file_path in files:
        # relative path for display
        rel_path = os.path.relpath(file_path, ROOT_DIR)
        
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
        except Exception as e:
            print(f"Error reading {rel_path}: {e}")
            continue
            
        matches = link_pattern.findall(content)
        for link in matches:
            target_file = resolve_path(file_path, link)
            
            if target_file:
                if not os.path.exists(target_file):
                    # Check if it is a directory index reference (e.g. contact/)
                    if os.path.isdir(target_file):
                        # check for index.html or index.php inside
                        if os.path.exists(os.path.join(target_file, 'index.html')) or \
                           os.path.exists(os.path.join(target_file, 'index.php')):
                            continue
                    
                    errors.append(f"[{rel_path}] Broken link: '{link}' -> Resolved: {target_file}")

    if errors:
        print(f"Found {len(errors)} broken links:")
        for err in errors:
            print(err)
    else:
        print("No broken internal links found!")

if __name__ == "__main__":
    check_links()
