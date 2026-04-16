# Manual Testing Checklist: Corteiz-Style Sidebar

## Overview
This document provides a comprehensive testing checklist for the Corteiz-style sidebar feature. Follow each section systematically to verify all functionality works correctly across different devices, browsers, and scenarios.

---

## Pre-Testing Setup

### Environment Preparation
- [ ] Ensure development server is running (`php artisan serve` or equivalent)
- [ ] Ensure frontend build is up to date (`npm run dev` or `npm run build`)
- [ ] Clear browser cache and application cache
- [ ] Verify database has test categories (both with and without products)
- [ ] Confirm at least one category has `products_count > 0` (active)
- [ ] Confirm at least one category has `products_count = 0` (inactive)

### Test Data Requirements
Create the following test categories in Filament admin if not present:
- **Active Categories** (with products): T-SHIRTS, HOODIES, ACCESSORIES
- **Inactive Categories** (no products): COMING SOON, PRE-ORDER
- Set different `display_order` values to test sorting

---

## Desktop Testing (Viewport > 1024px)

### Browser Compatibility
Test on the following browsers:
- [ ] Google Chrome (latest)
- [ ] Mozilla Firefox (latest)
- [ ] Safari (latest, macOS only)
- [ ] Microsoft Edge (latest)

### 1. Sidebar Layout and Positioning

#### Visual Verification
- [ ] Sidebar is visible on the left side of the page
- [ ] Sidebar has black background (#000000)
- [ ] Sidebar width is 240px (minimum)
- [ ] Sidebar has sharp edges (no rounded corners)
- [ ] All text uses OCR A font
- [ ] Sidebar is fixed position (remains visible when scrolling)

#### Scroll Behavior
- [ ] Scroll down the products page
- [ ] Verify sidebar stays fixed in position
- [ ] Verify sidebar doesn't overlap main content

### 2. Logo Display

- [ ] Logo is visible at the top of the sidebar
- [ ] Logo has appropriate padding from top edge
- [ ] Logo maintains aspect ratio (not stretched/squished)
- [ ] Hover over logo shows pointer cursor
- [ ] Click logo navigates to home page
- [ ] Logo link opens in same tab (not new tab)

### 3. Category Display and Ordering

#### Active Categories
- [ ] Active categories display in white text (#FFFFFF)
- [ ] Active categories show pointer cursor on hover
- [ ] Hover effect changes text color to green stabilo (#CCFF00)
- [ ] Hover effect shows subtle background color change
- [ ] Categories are sorted by `display_order` (ascending)
- [ ] Categories with same `display_order` are sorted alphabetically

#### Inactive Categories
- [ ] Inactive categories display in gray text (#666666)
- [ ] Inactive categories have strikethrough text decoration
- [ ] Inactive categories show default cursor (not pointer)
- [ ] Hover over inactive categories shows NO hover effect
- [ ] Inactive categories are NOT clickable

### 4. Category Filtering Functionality

#### Filter Application
- [ ] Click an active category (e.g., "T-SHIRTS")
- [ ] URL updates with category query parameter (e.g., `?category=t-shirts`)
- [ ] Products page displays only products from selected category
- [ ] Selected category is highlighted with green stabilo background (#CCFF00)
- [ ] Selected category text changes to black (#000000)
- [ ] Page header displays selected category name
- [ ] Product count updates to show filtered count

#### Filter Clearing
- [ ] "CLEAR FILTER" or "ALL PRODUCTS" button is visible when filter active
- [ ] Click clear filter button
- [ ] URL removes category query parameter
- [ ] All products are displayed again
- [ ] Category highlight is removed
- [ ] Page header returns to default state

#### Filter Persistence
- [ ] Apply a category filter
- [ ] Navigate to page 2 of products (if pagination exists)
- [ ] Verify category filter is maintained
- [ ] Verify URL contains both category and page parameters
- [ ] Click different category while on page 2
- [ ] Verify pagination resets to page 1 with new category

### 5. Instagram Link

- [ ] Instagram icon is visible below category list
- [ ] Icon is small and matches Corteiz styling proportions
- [ ] Icon displays in white color (#FFFFFF) by default
- [ ] Hover over icon changes color to green stabilo (#CCFF00)
- [ ] Click Instagram icon
- [ ] Verify it opens `https://instagram.com/marketttmarkettt` in NEW tab
- [ ] Verify original tab remains on products page

### 6. Shipping Policy Link

#### Link Display
- [ ] "SHIPPING POLICY" link is visible below Instagram icon
- [ ] Text is in uppercase
- [ ] Text uses OCR A font
- [ ] Text displays in white color (#FFFFFF)
- [ ] Hover changes text color to green stabilo (#CCFF00)

#### Modal Opening
- [ ] Click "SHIPPING POLICY" link
- [ ] Modal opens immediately
- [ ] Modal has black background (#000000)
- [ ] Modal displays white text (#FFFFFF) in OCR A font
- [ ] Modal heading reads "SHIPPING POLICY" (or "Shipping Policy")

#### Modal Content Verification
Verify all the following text is present in the modal:
- [ ] "WE ONLY SHIP WITHIN INDONESIA"
- [ ] "ALL ORDERS ARE PROCESSED AND SHIPPED WITHIN 5–10 WORKING DAYS"
- [ ] "UNLESS A PRE-ORDER SHIP DATE IS SPECIFIED"
- [ ] "WE DO NOT ACCEPT ORDERS FROM OUTSIDE INDONESIA"
- [ ] "PAYMENTS CANNOT BE COMPLETED BY CUSTOMERS LOCATED OUTSIDE INDONESIA"
- [ ] "ANY ORDERS IDENTIFIED AS BEING FROM OUTSIDE INDONESIA WILL BE AUTOMATICALLY CANCELLED AND NOT PROCESSED"
- [ ] "SHIPPING WILL NOT BE PROCESSED FOR ANY DESTINATIONS OUTSIDE INDONESIA"
- [ ] "IF AN INVALID ORDER IS PLACED FROM OUTSIDE INDONESIA, ANY ASSOCIATED COSTS OR FEES WILL BE THE RESPONSIBILITY OF THE CUSTOMER"
- [ ] "PLEASE REFER TO OUR TERMS OF SALE FOR FURTHER INFORMATION"

#### Modal Closing
- [ ] Close button is visible with green stabilo color (#CCFF00)
- [ ] Click close button - modal closes
- [ ] Re-open modal, click backdrop (outside modal content) - modal closes
- [ ] Re-open modal, press Escape key - modal closes
- [ ] Verify page is scrollable after modal closes
- [ ] Verify no duplicate modals appear after multiple open/close cycles

### 7. Keyboard Navigation and Accessibility

#### Tab Navigation
- [ ] Press Tab key repeatedly from top of page
- [ ] Verify focus moves to logo
- [ ] Verify focus moves through each active category link
- [ ] Verify focus skips inactive categories (not focusable)
- [ ] Verify focus moves to Instagram link
- [ ] Verify focus moves to shipping policy link
- [ ] Verify focus indicators are visible (outline or highlight)

#### Keyboard Interaction
- [ ] Tab to logo, press Enter - navigates to home page
- [ ] Tab to active category, press Enter - applies filter
- [ ] Tab to Instagram link, press Enter - opens Instagram in new tab
- [ ] Tab to shipping policy link, press Enter - opens modal

#### Modal Keyboard Accessibility
- [ ] Open shipping policy modal
- [ ] Press Tab - focus should move to close button (or first focusable element)
- [ ] Verify focus is trapped within modal (doesn't move to page behind)
- [ ] Press Escape - modal closes
- [ ] Verify focus returns to shipping policy link after modal closes

#### Screen Reader Testing (if available)
- [ ] Use screen reader (NVDA, JAWS, VoiceOver)
- [ ] Verify sidebar is announced as navigation landmark
- [ ] Verify active categories are announced as links
- [ ] Verify inactive categories are announced with strikethrough indication
- [ ] Verify selected category is announced with "current" or similar
- [ ] Verify modal has proper role and label announcements

---

## Tablet Testing (Viewport 768px - 1023px)

### Devices to Test
- [ ] iPad (Safari)
- [ ] iPad (Chrome)
- [ ] Android Tablet (Chrome)
- [ ] Browser DevTools responsive mode at 768px, 900px, 1023px

### 1. Sidebar Visibility

- [ ] Resize browser to 1023px width
- [ ] Verify sidebar is hidden (not visible)
- [ ] Hamburger menu button is visible in top-left corner
- [ ] Hamburger button has black background with white border
- [ ] Hamburger icon (three lines) is visible in white

### 2. Mobile Menu Interaction

#### Opening Menu
- [ ] Click/tap hamburger button
- [ ] Sidebar slides in from left with animation
- [ ] Semi-transparent backdrop appears behind sidebar
- [ ] Sidebar displays as overlay (not pushing content)
- [ ] Close button (×) is visible in top-right corner of sidebar
- [ ] Sidebar has same styling as desktop version

#### Menu Content
- [ ] All categories are visible in mobile sidebar
- [ ] Active/inactive category styling is consistent with desktop
- [ ] Logo, Instagram link, and shipping policy link are all present
- [ ] All interactive elements are easily tappable (not too small)

#### Closing Menu
- [ ] Tap close button (×) - sidebar closes with animation
- [ ] Re-open menu, tap backdrop - sidebar closes
- [ ] Re-open menu, tap active category - sidebar closes AND filter applies
- [ ] Verify sidebar doesn't reopen unexpectedly

### 3. Touch Interactions

- [ ] Open mobile menu
- [ ] Tap active category - filter applies correctly
- [ ] Tap inactive category - nothing happens (not clickable)
- [ ] Tap Instagram link - opens in new tab
- [ ] Tap shipping policy link - modal opens
- [ ] Verify no double-tap required for any interaction
- [ ] Verify no accidental interactions when scrolling

### 4. Orientation Changes

#### Portrait to Landscape
- [ ] Hold device in portrait mode with sidebar open
- [ ] Rotate to landscape mode
- [ ] Verify sidebar remains open and properly sized
- [ ] Verify no layout breaks or overlaps

#### Landscape to Portrait
- [ ] Hold device in landscape mode with sidebar closed
- [ ] Rotate to portrait mode
- [ ] Verify hamburger button remains visible
- [ ] Verify sidebar can still be opened

---

## Mobile Testing (Viewport < 768px)

### Devices to Test
- [ ] iPhone (Safari)
- [ ] iPhone (Chrome)
- [ ] Android Phone (Chrome)
- [ ] Browser DevTools responsive mode at 375px, 414px, 767px

### 1. Mobile Layout

- [ ] Sidebar is hidden by default
- [ ] Hamburger menu button is visible and easily tappable
- [ ] Button size is at least 44x44px (iOS touch target guideline)
- [ ] Button doesn't overlap with other UI elements
- [ ] Main content uses full width when sidebar closed

### 2. Mobile Menu Behavior

#### Opening and Closing
- [ ] Tap hamburger button - sidebar opens smoothly
- [ ] Sidebar covers most/all of screen width
- [ ] Backdrop is visible and semi-transparent
- [ ] Close button is easily reachable with thumb
- [ ] Tap backdrop - sidebar closes
- [ ] Tap close button - sidebar closes

#### Scrolling Behavior
- [ ] Open sidebar with many categories
- [ ] Verify sidebar content is scrollable if it exceeds screen height
- [ ] Verify page behind sidebar doesn't scroll when sidebar is open
- [ ] Close sidebar - verify page scrolling works normally

### 3. Category Filtering on Mobile

- [ ] Open mobile menu
- [ ] Tap active category
- [ ] Verify sidebar closes automatically
- [ ] Verify filter is applied
- [ ] Verify URL updates with category parameter
- [ ] Verify filtered products display correctly
- [ ] Verify clear filter button is visible and tappable

### 4. Modal on Mobile

#### Shipping Policy Modal
- [ ] Open mobile menu
- [ ] Tap "SHIPPING POLICY"
- [ ] Verify sidebar closes
- [ ] Verify modal opens and is properly sized for mobile
- [ ] Verify modal content is readable (text not too small)
- [ ] Verify modal is scrollable if content exceeds screen height
- [ ] Tap close button - modal closes
- [ ] Re-open modal, tap backdrop - modal closes
- [ ] Verify modal doesn't cause horizontal scrolling

### 5. Performance on Mobile

- [ ] Open sidebar - animation should be smooth (no lag)
- [ ] Close sidebar - animation should be smooth
- [ ] Apply category filter - page should load quickly
- [ ] Open modal - should appear instantly
- [ ] Verify no janky scrolling or layout shifts
- [ ] Test on slower mobile device or throttled connection

---

## Cross-Browser Compatibility Testing

### Chrome
- [ ] All desktop tests pass
- [ ] All tablet tests pass
- [ ] All mobile tests pass
- [ ] DevTools responsive mode works correctly

### Firefox
- [ ] Sidebar displays correctly
- [ ] Category filtering works
- [ ] Modal opens and closes properly
- [ ] Keyboard navigation works
- [ ] Mobile menu functions correctly

### Safari (macOS/iOS)
- [ ] OCR A font loads correctly
- [ ] Green stabilo color (#CCFF00) displays correctly
- [ ] Hover effects work on desktop
- [ ] Touch interactions work on iOS
- [ ] Modal backdrop works correctly
- [ ] No webkit-specific layout issues

### Edge
- [ ] All functionality matches Chrome behavior
- [ ] No Edge-specific rendering issues

---

## Styling and Aesthetic Verification

### Corteiz Aesthetic Checklist
- [ ] Overall design feels bold and streetwear-inspired
- [ ] Black background creates strong contrast
- [ ] Green stabilo accent color is vibrant and eye-catching
- [ ] OCR A font gives technical/utilitarian feel
- [ ] Sharp edges (no rounded corners) throughout
- [ ] Uppercase text for emphasis
- [ ] Minimal padding/spacing (not too spacious)
- [ ] High contrast between active and inactive elements

### Color Accuracy
- [ ] Black background is true black (#000000), not dark gray
- [ ] White text is true white (#FFFFFF), not off-white
- [ ] Green stabilo is correct shade (#CCFF00)
- [ ] Gray for inactive categories is #666666
- [ ] No unintended color variations across browsers

### Typography
- [ ] OCR A font loads on all browsers
- [ ] Fallback to monospace if OCR A unavailable
- [ ] Text is readable at all sizes
- [ ] Uppercase text is used consistently
- [ ] Letter spacing is appropriate (not too tight or loose)

---

## Edge Cases and Error Scenarios

### 1. No Categories
- [ ] Delete all categories from admin panel
- [ ] Reload products page
- [ ] Verify sidebar still displays (with logo, Instagram, shipping policy)
- [ ] Verify no JavaScript errors in console
- [ ] Verify page doesn't break

### 2. Many Categories (50+)
- [ ] Create 50+ test categories
- [ ] Reload products page
- [ ] Verify sidebar is scrollable
- [ ] Verify performance is acceptable
- [ ] Verify no layout issues

### 3. Long Category Names
- [ ] Create category with very long name (e.g., "EXTRA LONG CATEGORY NAME THAT MIGHT WRAP")
- [ ] Verify text wraps correctly or truncates
- [ ] Verify sidebar width doesn't expand unexpectedly
- [ ] Verify hover effect still works

### 4. Invalid Category in URL
- [ ] Manually navigate to `/products?category=nonexistent-category`
- [ ] Verify page loads without error
- [ ] Verify all products are displayed (filter ignored)
- [ ] Verify no category is highlighted as selected

### 5. Network Issues
- [ ] Open DevTools Network tab
- [ ] Throttle connection to "Slow 3G"
- [ ] Navigate to products page
- [ ] Verify sidebar loads (even if slowly)
- [ ] Verify no broken images or missing content
- [ ] Verify loading states are handled gracefully

### 6. JavaScript Disabled
- [ ] Disable JavaScript in browser
- [ ] Navigate to products page
- [ ] Verify sidebar is visible (desktop)
- [ ] Verify category links still work (full page reload)
- [ ] Verify graceful degradation (no broken functionality)

---

## Admin Panel Testing

### 1. Category Creation
- [ ] Log in to Filament admin panel
- [ ] Navigate to Categories resource
- [ ] Click "Create Category"
- [ ] Enter category name (e.g., "NEW CATEGORY")
- [ ] Verify slug is auto-generated
- [ ] Set display_order value (e.g., 5)
- [ ] Save category
- [ ] Navigate to products page
- [ ] Verify new category appears in sidebar
- [ ] Verify category appears in correct order based on display_order

### 2. Category Editing
- [ ] Edit existing category in admin panel
- [ ] Change display_order value
- [ ] Save changes
- [ ] Reload products page
- [ ] Verify category moved to new position in sidebar
- [ ] Verify cache was invalidated (changes visible immediately)

### 3. Category Deletion
- [ ] Delete a category from admin panel
- [ ] Reload products page
- [ ] Verify deleted category no longer appears in sidebar
- [ ] Verify no broken links or errors

### 4. Display Order Sorting
- [ ] Create/edit multiple categories with different display_order values
- [ ] Set some categories with same display_order
- [ ] Reload products page
- [ ] Verify categories are sorted by display_order (ascending)
- [ ] Verify categories with same display_order are sorted alphabetically

---

## Performance Testing

### 1. Page Load Performance
- [ ] Open DevTools Performance tab
- [ ] Record page load
- [ ] Verify sidebar renders within 1 second
- [ ] Verify no layout shifts (CLS score)
- [ ] Verify smooth animations (60fps)

### 2. Category Filtering Performance
- [ ] Apply category filter
- [ ] Measure time to filter and re-render products
- [ ] Verify filtering completes within 500ms
- [ ] Verify no janky animations during transition

### 3. Cache Effectiveness
- [ ] Clear application cache
- [ ] Load products page (first load)
- [ ] Check database queries in Laravel Debugbar
- [ ] Reload page (second load)
- [ ] Verify categories are loaded from cache (fewer queries)
- [ ] Verify cache TTL is 5 minutes (300 seconds)

---

## Final Verification

### Requirement Coverage
Go through each requirement in `requirements.md` and verify:
- [ ] Requirement 1: Sidebar Layout and Positioning ✓
- [ ] Requirement 2: Sidebar Logo Display ✓
- [ ] Requirement 3: Category Management in Admin Panel ✓
- [ ] Requirement 4: Category Display Order ✓
- [ ] Requirement 5: Active Category Display ✓
- [ ] Requirement 6: Inactive Category Display ✓
- [ ] Requirement 7: Instagram Link Display ✓
- [ ] Requirement 8: Shipping Policy Link Display ✓
- [ ] Requirement 9: Shipping Policy Modal ✓
- [ ] Requirement 10: Category Filtering Functionality ✓
- [ ] Requirement 11: Sidebar Responsive Behavior ✓
- [ ] Requirement 12: Database Schema for Category Display Order ✓
- [ ] Requirement 13: Category Product Count ✓

### Browser Console Check
- [ ] Open browser console on products page
- [ ] Verify no JavaScript errors
- [ ] Verify no Vue warnings
- [ ] Verify no 404 errors for assets (fonts, images)
- [ ] Verify no CORS errors

### Accessibility Audit
- [ ] Run Lighthouse accessibility audit
- [ ] Verify score is 90+ (or identify issues to fix)
- [ ] Run axe DevTools accessibility scan
- [ ] Address any critical or serious issues found

---

## Sign-Off

### Testing Completed By
- **Name**: ___________________________
- **Date**: ___________________________
- **Environment**: ___________________________

### Issues Found
List any issues discovered during testing:

1. ___________________________
2. ___________________________
3. ___________________________

### Overall Status
- [ ] All tests passed - Ready for production
- [ ] Minor issues found - Can deploy with known issues
- [ ] Major issues found - Requires fixes before deployment

### Notes
Additional observations or comments:

___________________________
___________________________
___________________________

---

## Quick Reference: Test URLs

- Products page: `http://localhost:8000/products`
- Products with filter: `http://localhost:8000/products?category=t-shirts`
- Admin categories: `http://localhost:8000/admin/categories`
- Home page: `http://localhost:8000/`

## Quick Reference: Test Accounts

- Admin email: `admin@styleu.com`
- Admin password: (check `.env` or setup docs)

## Quick Reference: Browser DevTools Shortcuts

- Open DevTools: `F12` or `Cmd+Opt+I` (Mac) or `Ctrl+Shift+I` (Windows)
- Responsive mode: `Cmd+Opt+M` (Mac) or `Ctrl+Shift+M` (Windows)
- Console: `Cmd+Opt+J` (Mac) or `Ctrl+Shift+J` (Windows)
