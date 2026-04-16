# Implementation Plan: Corteiz-Style Sidebar

## Overview

This implementation plan breaks down the Corteiz-style sidebar feature into discrete coding tasks. The sidebar provides category filtering, social media links, and shipping policy information with a black-and-green aesthetic. The implementation follows a backend-first approach, then frontend components, and finally integration.

## Tasks

- [ ] 1. Set up database schema and backend models
  - [x] 1.1 Create migration to add display_order column to categories table
    - Create migration file with display_order integer field (default 0)
    - Add column after slug field
    - _Requirements: 4.1, 12.1, 12.2, 12.3, 12.4_

  - [x] 1.2 Enhance Category model with ordering and product count methods
    - Add display_order to $fillable array
    - Implement ordered() scope (sort by display_order ASC, then name ASC)
    - Implement activeProductsCount() method to count active products
    - Add model event listeners to clear cache on save/delete
    - _Requirements: 4.2, 4.3, 4.4, 13.1, 13.2, 13.3_

  - [ ]* 1.3 Write unit tests for Category model
    - Test ordered() scope returns correct sort order
    - Test activeProductsCount() returns correct count
    - Test activeProductsCount() excludes inactive products
    - Test cache invalidation on model save/delete
    - _Requirements: 4.2, 4.3, 4.4, 13.3_

- [ ] 2. Enhance Filament admin panel for category management
  - [x] 2.1 Update CategoryResource form to include display_order field
    - Add TextInput for display_order with numeric validation
    - Set default value to 0
    - Add helper text explaining ordering behavior
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 4.2_

  - [x] 2.2 Update CategoryResource table to display and sort by display_order
    - Add display_order column to table
    - Add products_count column using counts() relationship
    - Set default sort to display_order ascending
    - Make display_order sortable
    - _Requirements: 3.4, 3.6, 4.2, 4.3_

- [ ] 3. Enhance ProductController for category data and filtering
  - [x] 3.1 Update ProductController index method to fetch categories with product counts
    - Query categories using ordered() scope
    - Use withCount() to get products_count for each category
    - Filter product count to only include active products
    - Cache categories for 5 minutes using Cache::remember()
    - Map categories to include is_active flag (products_count > 0)
    - _Requirements: 4.3, 4.4, 13.1, 13.2, 13.3, 13.4_

  - [x] 3.2 Implement category filtering logic in ProductController
    - Check for category query parameter in request
    - Filter products by category slug if present
    - Fetch selectedCategory object when filtering
    - Pass selectedCategory to Inertia response
    - Handle invalid category slugs gracefully (ignore and show all products)
    - _Requirements: 10.1, 10.2, 10.3, 10.6_

  - [ ]* 3.3 Write unit tests for ProductController category logic
    - Test category filtering applies correct where clause
    - Test invalid category slug is ignored gracefully
    - Test categories are cached with correct TTL
    - Test is_active flag is set correctly
    - _Requirements: 10.1, 10.2, 13.4_

- [x] 4. Checkpoint - Ensure backend tests pass
  - Run migrations on development database
  - Verify Filament admin can create/edit categories with display_order
  - Ensure all backend tests pass
  - Ask the user if questions arise

- [ ] 5. Create frontend Sidebar component
  - [x] 5.1 Create Sidebar.vue component with layout structure
    - Create component file at resources/js/Components/Sidebar.vue
    - Define props: categories (Array), selectedCategorySlug (String), isOpen (Boolean)
    - Define emits: close event
    - Implement template with logo, category list slot, Instagram link, shipping policy button
    - Add mobile close button (visible only when isOpen is true)
    - Apply Tailwind classes for black background, fixed positioning, 240px width
    - Add responsive classes to hide on mobile (<1024px) and show on desktop
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 2.1, 2.2, 2.3, 2.4_

  - [x] 5.2 Add Instagram link to Sidebar component
    - Add anchor tag with href to @marketttmarkettt Instagram profile
    - Set target="_blank" and rel="noopener noreferrer"
    - Add Instagram icon SVG
    - Apply white color by default, green stabilo (#CCFF00) on hover
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

  - [x] 5.3 Add shipping policy button to Sidebar component
    - Add button element with "SHIPPING POLICY" text
    - Apply OCR A font and uppercase styling
    - Set white color by default, green stabilo on hover
    - Add click handler to emit showShippingModal event
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [ ]* 5.4 Write component tests for Sidebar.vue
    - Test sidebar renders with provided categories
    - Test logo links to home page
    - Test Instagram link has correct href and opens in new tab
    - Test shipping policy button emits event on click
    - Test mobile close button emits close event
    - _Requirements: 2.4, 7.3, 8.5_

- [ ] 6. Create CategoryList component
  - [x] 6.1 Create CategoryList.vue component
    - Create component file at resources/js/Components/CategoryList.vue
    - Define props: categories (Array), selectedSlug (String)
    - Define emits: category-click event
    - Iterate over categories and render each as a list item
    - _Requirements: 5.1, 6.1_

  - [x] 6.2 Implement active category rendering and interaction
    - Render active categories (is_active === true) as clickable Link components
    - Set href to /products?category={slug}
    - Apply white text color by default
    - Apply green stabilo background and black text when selected (selectedSlug === category.slug)
    - Apply green stabilo text color and background on hover
    - Add cursor-pointer class
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

  - [x] 6.3 Implement inactive category rendering
    - Render inactive categories (is_active === false) as span elements
    - Apply gray text color (#666666)
    - Apply line-through text decoration
    - Remove cursor-pointer class
    - Ensure no hover effects
    - Ensure not clickable
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

  - [ ]* 6.4 Write component tests for CategoryList.vue
    - Test active categories render as clickable links
    - Test inactive categories render with strikethrough
    - Test inactive categories are not clickable
    - Test selected category has correct styling
    - Test category links have correct href format
    - _Requirements: 5.3, 5.4, 6.3, 6.4_

- [ ] 7. Create ShippingPolicyModal component
  - [x] 7.1 Create ShippingPolicyModal.vue component with modal structure
    - Create component file at resources/js/Components/ShippingPolicyModal.vue
    - Define props: show (Boolean)
    - Define emits: close event
    - Use Teleport to render modal in body
    - Implement modal backdrop with semi-transparent black background
    - Implement modal content container with black background and white border
    - Add modal title "SHIPPING POLICY"
    - _Requirements: 9.1, 9.2, 9.3, 9.4_

  - [x] 7.2 Add shipping policy text content to modal
    - Add all required shipping policy text paragraphs in uppercase
    - Apply OCR A font and white text color
    - Format text with appropriate spacing
    - _Requirements: 9.5_

  - [x] 7.3 Implement modal close interactions
    - Add close button with green stabilo background and black text
    - Emit close event when close button clicked
    - Emit close event when backdrop clicked (use @click.stop on content to prevent)
    - Add keyboard event listener for Escape key
    - Emit close event when Escape pressed
    - Clean up event listener on component unmount
    - _Requirements: 9.6, 9.7, 9.8, 9.9_

  - [ ]* 7.4 Write component tests for ShippingPolicyModal.vue
    - Test modal renders when show prop is true
    - Test modal hidden when show prop is false
    - Test close button emits close event
    - Test backdrop click emits close event
    - Test Escape key emits close event
    - Test modal content displays all required text
    - _Requirements: 9.1, 9.7, 9.8, 9.9_

- [x] 8. Checkpoint - Ensure frontend components are functional
  - Verify all Vue components compile without errors
  - Test components in isolation if possible
  - Ensure all tests pass
  - Ask the user if questions arise

- [ ] 9. Integrate sidebar into Products/Index.vue page
  - [x] 9.1 Import and add Sidebar component to Products/Index page
    - Import Sidebar, CategoryList, and ShippingPolicyModal components
    - Add categories, selectedCategory, and filters props to defineProps
    - Add mobileMenuOpen ref for mobile sidebar state
    - Add showShippingModal ref for modal state
    - _Requirements: 1.1, 11.1_

  - [x] 9.2 Render Sidebar component in template
    - Wrap page content in flex container
    - Render Sidebar with categories and selectedCategorySlug props
    - Add hidden lg:block classes for desktop visibility
    - Render second Sidebar instance for mobile with conditional v-if based on mobileMenuOpen
    - Add lg:hidden class for mobile-only visibility
    - Pass isOpen prop to mobile Sidebar
    - Listen to close event and set mobileMenuOpen to false
    - _Requirements: 1.1, 1.7, 11.1, 11.2, 11.3, 11.4, 11.5, 11.6, 11.7_

  - [x] 9.3 Add hamburger menu button for mobile
    - Add button element with hamburger icon SVG
    - Position button fixed in top-left corner
    - Add lg:hidden class to hide on desktop
    - Add click handler to set mobileMenuOpen to true
    - Apply black background with white border
    - _Requirements: 11.2, 11.3_

  - [x] 9.4 Update page header to display selected category
    - Display selectedCategory.name in h1 when category filter is active
    - Display "MARKETTTMARKETTT" when no filter is active
    - Add "CLEAR FILTER" button when selectedCategory exists
    - Style button with green stabilo text color
    - Add click handler to navigate to /products (clearing filter)
    - _Requirements: 10.3, 10.4, 10.5_

  - [x] 9.5 Adjust main content layout for sidebar
    - Add flex-1 class to main content container
    - Add lg:ml-60 class to offset content by sidebar width on desktop
    - Ensure products grid and pagination work correctly with new layout
    - _Requirements: 1.1, 1.5, 1.6_

- [ ] 10. Add OCR A font and styling enhancements
  - [x] 10.1 Add OCR A font files to project
    - Download OCR A font file (OCRA.ttf)
    - Place font file in public/fonts/ directory
    - _Requirements: 1.3_

  - [x] 10.2 Update app.css with font-face declaration
    - Add @font-face rule for OCR_A font family
    - Reference /fonts/OCRA.ttf file
    - Verify font is already configured in Tailwind config for font-ocr class
    - _Requirements: 1.3_

  - [x] 10.3 Apply OCR A font to sidebar components
    - Ensure Sidebar, CategoryList, and ShippingPolicyModal use font-['OCR_A'] class
    - Apply uppercase text transformation where appropriate
    - Verify letter-spacing and text styling matches Corteiz aesthetic
    - _Requirements: 1.3, 8.2, 9.3_

- [x] 11. Final integration and testing
  - [ ]* 11.1 Write integration tests for category filtering flow
    - Test clicking active category filters products
    - Test URL updates with category query parameter
    - Test clear filter button removes category parameter
    - Test pagination maintains category filter
    - Test filtered products all belong to selected category
    - _Requirements: 10.1, 10.2, 10.5, 10.6_

  - [ ]* 11.2 Write integration tests for mobile sidebar behavior
    - Test hamburger button visible on mobile viewport
    - Test sidebar hidden by default on mobile
    - Test clicking hamburger opens sidebar overlay
    - Test clicking backdrop closes sidebar
    - Test sidebar visible by default on desktop viewport
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_

  - [x] 11.3 Perform manual testing across devices and browsers
    - Test on desktop viewport (>1024px)
    - Test on tablet viewport (768px-1023px)
    - Test on mobile viewport (<768px)
    - Verify all interactions work correctly
    - Verify styling matches Corteiz aesthetic
    - Test keyboard navigation and accessibility
    - _Requirements: All requirements_

- [x] 12. Final checkpoint - Ensure all tests pass and feature is complete
  - Run all unit tests, component tests, and integration tests
  - Verify no console errors or warnings
  - Ensure all requirements are met
  - Ask the user if questions arise

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- The implementation follows a backend-first approach to ensure data layer is solid before building UI
- Mobile responsiveness is built in from the start rather than retrofitted
- Cache invalidation is handled automatically through model events
- All styling uses Tailwind CSS classes for consistency with existing codebase
