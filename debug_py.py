import os

ROOT = r"c:\Users\kaito\Desktop\技巧 -Giko-\Giko_Website"

def main():
    print("DEBUG: Script started.")
    try:
        files = os.listdir(ROOT)
        print(f"DEBUG: Found {len(files)} files/dirs in root.")
    except Exception as e:
        print(f"DEBUG: Error listing root: {e}")

if __name__ == "__main__":
    main()
