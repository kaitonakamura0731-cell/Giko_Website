
import os
import re

# Configuration
ROOT_DIR = r"c:\Users\kaito\Desktop\技巧 -Giko-\Giko_Website"
# Files to ignore (e.g., config files, tools)
IGNORE_FILES = {
    'audit_links.ps1', 'verify_links.py', 'find_orphans.py', 
    'tailwind_config.js', 'package.json', 'package-lock.json', 
    'server.js', '.htaccess', '404.html', 'sitemap.xml', 'robots.txt'
}
IGNORE_DIRS = {'.git', '.gemini', 'node_modules', 'payjp_test', 'api'}

def get_all_files(root):
    all_files = set()
    for dirpath, dirnames, filenames in os.walk(root):
        # Filter directories
        dirnames[:] = [d for d in dirnames if d not in IGNORE_DIRS]
        
        for f in filenames:
            if f in IGNORE_FILES: continue
            
            full_path = os.path.join(dirpath, f)
            # Normalize to relative path from root
            rel_path = os.path.relpath(full_path, root).replace("\\", "/")
            all_files.add(rel_path)
    return all_files

def get_referenced_files(root):
    referenced = set()
    # Explicitly add index.html as it's the entry point
    referenced.add('index.html')
    
    for dirpath, dirnames, filenames in os.walk(root):
        dirnames[:] = [d for d in dirnames if d not in IGNORE_DIRS]
        
        for f in filenames:
            ext = os.path.splitext(f)[1].lower()
            if ext not in ['.html', '.php', '.js', '.css']: continue
            
            path = os.path.join(dirpath, f)
            try:
                with open(path, 'r', encoding='utf-8', errors='ignore') as file:
                    content = file.read()
                    
                    # Regex for href and src (flexible quote)
                    matches = re.findall(r'(?:href|src)=["\']([^"\']+)["\']', content)
                    
                    for link in matches:
                        # Skip external/special links
                        if link.startswith(('http', '#', 'mailto:', 'tel:', 'javascript:')):
                            continue
                            
                        # Resolve path
                        target = link.split('?')[0].split('#')[0] # Remove query/hash
                        
                        if target.startswith('/'):
                            # Root relative
                            abs_target = target.lstrip('/')
                        else:
                            # File relative
                            # Get dir of current file relative to root
                            rel_dir = os.path.relpath(dirpath, root)
                            if rel_dir == '.': rel_dir = ''
                            
                            # Join and normalize
                            joined = os.path.join(rel_dir, target)
                            abs_target = os.path.normpath(joined).replace("\\", "/")
                        
                        referenced.add(abs_target)
                        
            except Exception as e:
                print(f"Error reading {f}: {e}")
                
    return referenced

def main():
    print("Scannng for orphaned files...")
    all_files = get_all_files(ROOT_DIR)
    referenced_files = get_referenced_files(ROOT_DIR)
    
    # We need to be careful with exact matches vs path normalization
    # Let's try to match loosely
    orphans = []
    
    for f in all_files:
        is_referenced = False
        # Direct match
        if f in referenced_files:
            is_referenced = True
        else:
            # Check if any reference resolves to this file
            # (Simple check: is f in referenced set?)
            pass

        if not is_referenced:
            # Check if it's an asset (often dynamic) or real page
            orphans.append(f)
            
    if not orphans:
        print("PASS: No orphaned files found.")
    else:
        print(f"FAIL: Found {len(orphans)} potentially orphaned files:")
        for o in sorted(orphans):
            print(f" - {o}")

if __name__ == "__main__":
    main()
