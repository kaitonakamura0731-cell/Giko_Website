import os
import re
from pathlib import Path
from urllib.parse import unquote

def check_assets(root_dir):
    root_path = Path(root_dir)
    html_files = list(root_path.glob("*.html"))
    
    # Regex for src and url()
    src_pattern = re.compile(r'src=["\'](.*?)["\']')
    url_pattern = re.compile(r'url\(["\']?(.*?)["\']?\)')
    
    report = []
    
    for html_file in html_files:
        try:
            content = html_file.read_text(encoding='utf-8')
        except Exception as e:
            report.append(f"Error reading {html_file.name}: {e}")
            continue

        links = src_pattern.findall(content) + url_pattern.findall(content)
        
        for link in links:
            if link.startswith(('http', '#', 'mailto:', 'tel:')) or link.strip() == '':
                continue
            
            # Handle potential query params or anchors
            clean_link = link.split('?')[0].split('#')[0]
            
            # Resolve path
            if clean_link.startswith('/'):
                # Assuming / refers to root_dir
                target_path = root_path / clean_link.lstrip('/')
            else:
                target_path = (html_file.parent / clean_link).resolve()
            
            try:
                if not target_path.exists():
                     # Check if it's just case sensitivity issue (Windows is case insensitive but verify anyway)
                    # Or check if decoding is needed
                    decoded_link = unquote(clean_link)
                    if clean_link.startswith('/'):
                        decoded_target = root_path / decoded_link.lstrip('/')
                    else:
                        decoded_target = (html_file.parent / decoded_link).resolve()

                    if not decoded_target.exists():
                        report.append(f"MISSING: {html_file.name} -> {link}")
            except Exception as e:
                report.append(f"ERROR checking {link} in {html_file.name}: {e}")

    return report

if __name__ == "__main__":
    target_dir = r"c:\Users\kaito\Desktop\技巧 -Giko-\Giko_Website"
    missing_assets = check_assets(target_dir)
    
    if missing_assets:
        print("Found missing assets:")
        for item in set(missing_assets):
            print(item)
    else:
        print("No missing assets found.")
