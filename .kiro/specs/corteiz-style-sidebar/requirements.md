# Requirements Document

## Introduction

This document specifies requirements for implementing a Corteiz-inspired sidebar on the products page. The sidebar will display dynamic categories, social media links, and shipping policy information. Categories can be managed through the Filament admin panel, and categories without products will appear as strikethrough text to indicate they are inactive.

## Glossary

- **Sidebar**: A vertical navigation panel positioned on the left side of the products page
- **Category**: A product classification that can be managed from the admin panel
- **Active_Category**: A category that has at least one associated product
- **Inactive_Category**: A category that has zero associated products
- **Admin_Panel**: The Filament PHP v3 administrative interface
- **Products_Page**: The /products route displaying the product listing
- **Shipping_Policy_Modal**: A modal dialog displaying shipping policy text
- **Category_Filter**: A mechanism to display only products belonging to a selected category

## Requirements

### Requirement 1: Sidebar Layout and Positioning

**User Story:** As a user, I want to see a sidebar on the products page, so that I can navigate categories and access important information.

#### Acceptance Criteria

1. THE Sidebar SHALL be positioned on the left side of the Products_Page
2. THE Sidebar SHALL have a black background color (#000000)
3. THE Sidebar SHALL use OCR A font for all text elements
4. THE Sidebar SHALL have sharp edges with no rounded corners
5. THE Sidebar SHALL be fixed position and remain visible during page scrolling
6. THE Sidebar SHALL have a minimum width of 240 pixels on desktop viewports
7. WHEN the viewport width is less than 1024 pixels, THE Sidebar SHALL be hidden or collapsed

### Requirement 2: Sidebar Logo Display

**User Story:** As a user, I want to see the brand logo at the top of the sidebar, so that I can identify the website brand.

#### Acceptance Criteria

1. THE Sidebar SHALL display a logo at the top section
2. THE Logo SHALL be positioned with appropriate padding from the top edge
3. THE Logo SHALL maintain its aspect ratio when displayed
4. THE Logo SHALL be clickable and navigate to the home page when clicked

### Requirement 3: Category Management in Admin Panel

**User Story:** As an administrator, I want to manage sidebar categories from the admin panel, so that I can control which categories appear on the website.

#### Acceptance Criteria

1. THE Admin_Panel SHALL allow administrators to create new categories
2. THE Admin_Panel SHALL allow administrators to edit existing category names
3. THE Admin_Panel SHALL allow administrators to delete categories
4. THE Admin_Panel SHALL display the product count for each category
5. WHEN a category is created, THE Admin_Panel SHALL automatically generate a URL-friendly slug
6. THE Admin_Panel SHALL display categories in alphabetical order by default

### Requirement 4: Category Display Order

**User Story:** As an administrator, I want to control the display order of categories in the sidebar, so that I can prioritize important categories.

#### Acceptance Criteria

1. THE Category model SHALL have a display_order field
2. THE Admin_Panel SHALL allow administrators to set the display_order value for each category
3. THE Sidebar SHALL display categories sorted by display_order in ascending order
4. WHEN two categories have the same display_order value, THE Sidebar SHALL sort them alphabetically by name

### Requirement 5: Active Category Display

**User Story:** As a user, I want to click on active categories, so that I can filter products by category.

#### Acceptance Criteria

1. WHEN a Category has one or more associated products, THE Sidebar SHALL display it as an Active_Category
2. THE Active_Category SHALL be displayed in white text color (#FFFFFF)
3. THE Active_Category SHALL be clickable
4. WHEN an Active_Category is clicked, THE Products_Page SHALL display only products belonging to that category
5. THE Active_Category SHALL show a hover effect with green stabilo color (#CCFF00)
6. WHEN an Active_Category is currently selected, THE Sidebar SHALL highlight it with green stabilo background (#CCFF00) and black text (#000000)

### Requirement 6: Inactive Category Display

**User Story:** As a user, I want to see inactive categories with strikethrough styling, so that I know they are not yet available.

#### Acceptance Criteria

1. WHEN a Category has zero associated products, THE Sidebar SHALL display it as an Inactive_Category
2. THE Inactive_Category SHALL be displayed with strikethrough text decoration
3. THE Inactive_Category SHALL be displayed in gray text color (#666666)
4. THE Inactive_Category SHALL not be clickable
5. THE Inactive_Category SHALL not show any hover effects
6. THE Inactive_Category SHALL not have a cursor pointer style

### Requirement 7: Instagram Link Display

**User Story:** As a user, I want to click on the Instagram icon, so that I can visit the brand's Instagram profile.

#### Acceptance Criteria

1. THE Sidebar SHALL display an Instagram icon below the category list
2. THE Instagram_Icon SHALL be small in size matching Corteiz styling proportions
3. WHEN the Instagram_Icon is clicked, THE System SHALL open @marketttmarkettt Instagram profile in a new browser tab
4. THE Instagram_Icon SHALL have a hover effect changing color to green stabilo (#CCFF00)
5. THE Instagram_Icon SHALL use white color (#FFFFFF) by default

### Requirement 8: Shipping Policy Link Display

**User Story:** As a user, I want to click on the shipping policy link, so that I can read the shipping terms.

#### Acceptance Criteria

1. THE Sidebar SHALL display a "SHIPPING POLICY" link below the Instagram icon
2. THE Shipping_Policy_Link SHALL use OCR A font in uppercase
3. THE Shipping_Policy_Link SHALL be displayed in white text color (#FFFFFF)
4. THE Shipping_Policy_Link SHALL have a hover effect changing color to green stabilo (#CCFF00)
5. WHEN the Shipping_Policy_Link is clicked, THE System SHALL open the Shipping_Policy_Modal

### Requirement 9: Shipping Policy Modal

**User Story:** As a user, I want to read the shipping policy in a modal, so that I understand the shipping terms without leaving the products page.

#### Acceptance Criteria

1. WHEN the Shipping_Policy_Link is clicked, THE System SHALL display the Shipping_Policy_Modal
2. THE Shipping_Policy_Modal SHALL have a black background (#000000)
3. THE Shipping_Policy_Modal SHALL display white text (#FFFFFF) using OCR A font
4. THE Shipping_Policy_Modal SHALL contain the heading "Shipping Policy"
5. THE Shipping_Policy_Modal SHALL display the following text: "WE ONLY SHIP WITHIN INDONESIA", "ALL ORDERS ARE PROCESSED AND SHIPPED WITHIN 5–10 WORKING DAYS", "UNLESS A PRE-ORDER SHIP DATE IS SPECIFIED", "WE DO NOT ACCEPT ORDERS FROM OUTSIDE INDONESIA", "PAYMENTS CANNOT BE COMPLETED BY CUSTOMERS LOCATED OUTSIDE INDONESIA", "ANY ORDERS IDENTIFIED AS BEING FROM OUTSIDE INDONESIA WILL BE AUTOMATICALLY CANCELLED AND NOT PROCESSED", "SHIPPING WILL NOT BE PROCESSED FOR ANY DESTINATIONS OUTSIDE INDONESIA", "IF AN INVALID ORDER IS PLACED FROM OUTSIDE INDONESIA, ANY ASSOCIATED COSTS OR FEES WILL BE THE RESPONSIBILITY OF THE CUSTOMER", "PLEASE REFER TO OUR TERMS OF SALE FOR FURTHER INFORMATION"
6. THE Shipping_Policy_Modal SHALL have a close button with green stabilo color (#CCFF00)
7. WHEN the close button is clicked, THE System SHALL hide the Shipping_Policy_Modal
8. WHEN the user clicks outside the modal content area, THE System SHALL hide the Shipping_Policy_Modal
9. WHEN the Escape key is pressed, THE System SHALL hide the Shipping_Policy_Modal

### Requirement 10: Category Filtering Functionality

**User Story:** As a user, I want to see filtered products when I select a category, so that I can browse products within a specific category.

#### Acceptance Criteria

1. WHEN an Active_Category is clicked, THE System SHALL update the URL with the category slug as a query parameter
2. WHEN a category query parameter is present, THE Products_Page SHALL display only products with matching category_id
3. WHEN a category filter is active, THE Products_Page SHALL display the category name in the header
4. THE Products_Page SHALL display a "CLEAR FILTER" or "ALL PRODUCTS" link when a category filter is active
5. WHEN the clear filter link is clicked, THE System SHALL remove the category query parameter and display all products
6. THE Products_Page SHALL maintain pagination when category filtering is active

### Requirement 11: Sidebar Responsive Behavior

**User Story:** As a mobile user, I want the sidebar to adapt to smaller screens, so that I can still access categories on mobile devices.

#### Acceptance Criteria

1. WHEN the viewport width is less than 1024 pixels, THE Sidebar SHALL be hidden by default
2. WHEN the viewport width is less than 1024 pixels, THE Products_Page SHALL display a hamburger menu button
3. WHEN the hamburger menu button is clicked, THE System SHALL display the Sidebar as an overlay
4. WHEN the Sidebar overlay is open, THE System SHALL display a semi-transparent backdrop
5. WHEN the backdrop is clicked, THE System SHALL hide the Sidebar overlay
6. THE Sidebar overlay SHALL slide in from the left side with animation
7. THE Sidebar overlay SHALL have a close button in the top-right corner

### Requirement 12: Database Schema for Category Display Order

**User Story:** As a developer, I want the category table to support display ordering, so that administrators can control category sequence.

#### Acceptance Criteria

1. THE categories table SHALL have a display_order column of type integer
2. THE display_order column SHALL have a default value of 0
3. THE display_order column SHALL be nullable
4. WHEN a new category is created without specifying display_order, THE System SHALL assign the value 0

### Requirement 13: Category Product Count

**User Story:** As a system, I want to efficiently determine if a category is active or inactive, so that the sidebar can display categories correctly.

#### Acceptance Criteria

1. WHEN loading the Products_Page, THE System SHALL query all categories with their associated product counts
2. THE System SHALL use eager loading to minimize database queries
3. THE System SHALL only count products with status equal to "active"
4. THE System SHALL cache category product counts for 5 minutes to improve performance
