<?php

/**
 * T4.3 Design System Enhancement Validation Script
 * 
 * This script validates the enhanced Corteiz design system applied to Orders pages.
 */

require __DIR__ . '/vendor/autoload.php';

echo "=== T4.3 DESIGN SYSTEM ENHANCEMENT VALIDATION ===\n\n";

$passed = 0;
$failed = 0;

// Test 1: Check Orders/Index.vue exists
echo "Test 1: Orders/Index.vue file exists... ";
if (file_exists(__DIR__ . '/resources/js/Pages/Orders/Index.vue')) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 2: Check Orders/Show.vue exists
echo "Test 2: Orders/Show.vue file exists... ";
if (file_exists(__DIR__ . '/resources/js/Pages/Orders/Show.vue')) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 3: Check status badge colors in Index.vue
echo "Test 3: Status badge colors updated in Index.vue... ";
$indexContent = file_get_contents(__DIR__ . '/resources/js/Pages/Orders/Index.vue');
if (
    strpos($indexContent, "bg-[#92400E] text-[#FEF3C7]") !== false &&
    strpos($indexContent, "bg-[#1e3a5f] text-[#93C5FD]") !== false &&
    strpos($indexContent, "bg-[#3b0764] text-[#DDD6FE]") !== false &&
    strpos($indexContent, "bg-[#14532d] text-[#86EFAC]") !== false &&
    strpos($indexContent, "bg-[#166534] text-[#DCFCE7]") !== false &&
    strpos($indexContent, "bg-[#7f1d1d] text-[#FECACA]") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 4: Check status badge colors in Show.vue
echo "Test 4: Status badge colors updated in Show.vue... ";
$showContent = file_get_contents(__DIR__ . '/resources/js/Pages/Orders/Show.vue');
if (
    strpos($showContent, "bg-[#92400E] text-[#FEF3C7]") !== false &&
    strpos($showContent, "bg-[#1e3a5f] text-[#93C5FD]") !== false &&
    strpos($showContent, "bg-[#3b0764] text-[#DDD6FE]") !== false &&
    strpos($showContent, "bg-[#14532d] text-[#86EFAC]") !== false &&
    strpos($showContent, "bg-[#166534] text-[#DCFCE7]") !== false &&
    strpos($showContent, "bg-[#7f1d1d] text-[#FECACA]") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 5: Check border color updated to #222222 in Index.vue
echo "Test 5: Border color updated to #222222 in Index.vue... ";
if (strpos($indexContent, "border-[#222222]") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 6: Check border color updated to #222222 in Show.vue
echo "Test 6: Border color updated to #222222 in Show.vue... ";
if (strpos($showContent, "border-[#222222]") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 7: Check button text updated to "Lanjutkan Pembayaran" in Index.vue
echo "Test 7: Button text updated to 'Lanjutkan Pembayaran' in Index.vue... ";
if (strpos($indexContent, "Lanjutkan Pembayaran") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 8: Check button text updated to "Lanjutkan Pembayaran" in Show.vue
echo "Test 8: Button text updated to 'Lanjutkan Pembayaran' in Show.vue... ";
if (strpos($showContent, "Lanjutkan Pembayaran") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 9: Check monospace font for tracking number in Show.vue
echo "Test 9: Monospace font applied to tracking number in Show.vue... ";
if (strpos($showContent, "font-mono") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 10: Check product image size 60x60 in Show.vue
echo "Test 10: Product image size set to 60x60 in Show.vue... ";
if (strpos($showContent, "w-[60px] h-[60px]") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 11: Check hover effect on order cards in Index.vue
echo "Test 11: Hover effect on order cards in Index.vue... ";
if (strpos($indexContent, "hover:border-[#333333]") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 12: Check Bebas Neue font usage
echo "Test 12: Bebas Neue font used for headings... ";
if (
    strpos($indexContent, "font-['Bebas_Neue']") !== false &&
    strpos($showContent, "font-['Bebas_Neue']") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 13: Check uppercase text styling
echo "Test 13: Uppercase text styling applied... ";
if (
    strpos($indexContent, "uppercase") !== false &&
    strpos($showContent, "uppercase") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 14: Check #999999 muted text color
echo "Test 14: Muted text color #999999 used... ";
if (
    strpos($indexContent, "text-[#999999]") !== false &&
    strpos($showContent, "text-[#999999]") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 15: Check #ff0000 red color for countdown warning
echo "Test 15: Red color #ff0000 used for countdown warning... ";
if (
    strpos($indexContent, "text-[#ff0000]") !== false &&
    strpos($showContent, "text-[#ff0000]") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Summary
echo "\n=== VALIDATION SUMMARY ===\n";
echo "Total Tests: " . ($passed + $failed) . "\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Success Rate: " . round(($passed / ($passed + $failed)) * 100, 2) . "%\n\n";

if ($failed === 0) {
    echo "✓ ALL TESTS PASSED! T4.3 Design System Enhancement is complete.\n";
    echo "\nDesign System Features Applied:\n";
    echo "✓ Status badge colors (sharp, no rounded corners)\n";
    echo "  - pending_payment: #92400E bg, #FEF3C7 text\n";
    echo "  - paid: #1e3a5f bg, #93C5FD text\n";
    echo "  - processing: #3b0764 bg, #DDD6FE text\n";
    echo "  - shipped: #14532d bg, #86EFAC text\n";
    echo "  - delivered: #166534 bg, #DCFCE7 text\n";
    echo "  - cancelled: #7f1d1d bg, #FECACA text\n";
    echo "✓ Border color updated to #222222\n";
    echo "✓ Hover effects on order cards\n";
    echo "✓ Button text: 'Lanjutkan Pembayaran'\n";
    echo "✓ Monospace font for tracking numbers\n";
    echo "✓ Product image size: 60x60\n";
    echo "✓ Bebas Neue font for headings (UPPERCASE)\n";
    echo "✓ Muted text color #999999\n";
    echo "✓ Red countdown warning #ff0000\n";
    echo "\nNext steps:\n";
    echo "1. Visit /orders to view the enhanced order list\n";
    echo "2. Click on an order to view enhanced order details\n";
    echo "3. Verify status badge colors\n";
    echo "4. Test countdown timer with red warning\n";
    echo "5. Check tracking number monospace font\n";
} else {
    echo "✗ SOME TESTS FAILED. Please review the errors above.\n";
}

exit($failed > 0 ? 1 : 0);
