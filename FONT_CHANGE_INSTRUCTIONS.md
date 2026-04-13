# Font Change Instructions - Household Words

## Steps to Change Font

### 1. Download Font
1. Visit https://www.dafont.com/householdwords.font
2. Download the font file (HouseholdWords.ttf or HouseholdWords.otf)
3. Place the font file in `styleu/public/fonts/HouseholdWords.ttf`

### 2. Font Configuration (Already Done)
The CSS has been updated in `resources/css/app.css`:
- Removed Google Fonts import (Bebas Neue & Inter)
- Added @font-face declaration for Household Words
- Updated CSS variables to use Household Words for both heading and body text

### 3. Rebuild Assets
After placing the font file, run:
```bash
cd styleu
npm run build
```

### 4. Clear Browser Cache
After rebuilding, clear your browser cache or do a hard refresh (Cmd+Shift+R on Mac, Ctrl+Shift+R on Windows/Linux)

## Current Font Usage

All text will use "Household Words" font:
- Headings (h1, h2, h3, etc.)
- Body text
- Buttons
- Navigation
- All UI elements

## Font Fallback
If the font file is not found, the system will fallback to:
- ui-sans-serif
- system-ui
- sans-serif

## Files Modified
1. `resources/css/app.css` - Font configuration
2. `public/fonts/` - Font file location (you need to add the font file here)

## Verification
After completing the steps:
1. Open the website
2. Check if all text uses the Household Words font
3. Inspect element and verify font-family is "Household Words"

## Note
The font file `HouseholdWords.ttf` is NOT included in the repository. You must download it from DaFont and place it in the `public/fonts/` directory yourself.

## Alternative: Using font-heading Class
If you want to use different fonts for headings vs body text in the future, you can:
- Use `font-heading` class for headings
- Use `font-sans` class for body text
- Update the CSS variables in `app.css` accordingly
