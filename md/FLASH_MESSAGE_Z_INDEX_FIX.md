# Flash Message Z-Index Fix

## Problem
Flash message "Product added to cart" muncul di belakang header navbar, sehingga tertutup dan tidak terlihat dengan jelas oleh user.

## Root Cause
- Navbar memiliki `z-50` (sticky positioning)
- FlashMessage juga memiliki `z-50`
- Karena navbar muncul lebih dulu di DOM tree, FlashMessage yang muncul kemudian akan tertutup oleh navbar

## Solution
Meningkatkan z-index FlashMessage dari `z-50` menjadi `z-[100]` agar selalu muncul di atas navbar.

### Before:
```vue
<div
    v-if="show"
    :class="[bgColor, 'fixed top-4 right-4 z-50 px-6 py-4 max-w-sm shadow-lg']"
>
```

### After:
```vue
<div
    v-if="show"
    :class="[bgColor, 'fixed top-4 right-4 z-[100] px-6 py-4 max-w-sm shadow-lg']"
>
```

## Z-Index Hierarchy
- FlashMessage: `z-[100]` (highest - always visible)
- Navbar: `z-50` (sticky header)
- Regular content: default z-index

## Files Modified
- `resources/js/Components/FlashMessage.vue` - Increased z-index to z-[100]

## Testing
1. Add a product to cart
2. Flash message "Product added to cart" should appear at top-right
3. Message should be clearly visible above the navbar
4. Message should auto-dismiss after 4 seconds
5. User can manually close the message with X button

## Status
✅ **FIXED** - Flash messages now appear in front of the header navbar.
