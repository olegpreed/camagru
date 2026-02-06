# Superposable Overlay Images

This directory contains the overlay images that users can superimpose on their photos.

## Requirements

- Format: PNG with transparency (recommended)
- Suggested size: 800x600 pixels (will be automatically resized)
- File types supported: PNG, JPG, GIF

## Files to Add

Based on the database entries, you should add the following overlay files:

1. `sunglasses.png` - Sunglasses overlay
2. `mustache.png` - Mustache overlay
3. `crown.png` - Crown overlay
4. `heart-frame.png` - Heart frame overlay
5. `star-effect.png` - Star effect overlay

## Adding More Overlays

To add more overlay options:

1. Add the PNG file to this directory
2. Insert a new record in the `superposable_images` table:
   ```sql
   INSERT INTO superposable_images (name, filename) VALUES ('Your Overlay Name', 'your-file.png');
   ```
3. The overlay will automatically appear in the creation interface
