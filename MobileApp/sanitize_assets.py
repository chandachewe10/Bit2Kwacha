import os
from PIL import Image

assets_path = r"c:\xampp\htdocs\Macroit\BitKwik\MobileApp\assets"
files = ["icon.png", "adaptive-icon.png", "appstore.png", "playstore.png", "splash.png", "favicon.png"]

for file in files:
    full_path = os.path.join(assets_path, file)
    if os.path.exists(full_path):
        try:
            print(f"Processing {file}...")
            img = Image.open(full_path)
            # Re-save to strip metadata and ensure standard format
            img.save(full_path, "PNG", optimize=True)
            print(f"Successfully sanitized {file}")
        except Exception as e:
            print(f"Error processing {file}: {e}")
    else:
        print(f"File not found: {full_path}")
