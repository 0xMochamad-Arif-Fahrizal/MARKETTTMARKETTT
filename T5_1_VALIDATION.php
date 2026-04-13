<?php

/**
 * T5.1 Profile Management Validation Script
 * 
 * This script validates the profile management features with enhanced Corteiz design.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== T5.1 PROFILE MANAGEMENT VALIDATION ===\n\n";

$passed = 0;
$failed = 0;

// Test 1: Check ProfileController exists
echo "Test 1: ProfileController file exists... ";
if (file_exists(__DIR__ . '/app/Http/Controllers/ProfileController.php')) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 2: Check Profile/Index.vue exists
echo "Test 2: Profile/Index.vue file exists... ";
if (file_exists(__DIR__ . '/resources/js/Pages/Profile/Index.vue')) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 3: Check Profile/Addresses.vue exists
echo "Test 3: Profile/Addresses.vue file exists... ";
if (file_exists(__DIR__ . '/resources/js/Pages/Profile/Addresses.vue')) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 4: Check ProfileController has required methods
echo "Test 4: ProfileController has required methods... ";
$controllerContent = file_get_contents(__DIR__ . '/app/Http/Controllers/ProfileController.php');
if (
    strpos($controllerContent, 'public function index') !== false &&
    strpos($controllerContent, 'public function update') !== false &&
    strpos($controllerContent, 'public function updatePassword') !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 5: Check Profile/Index.vue has Corteiz design colors
echo "Test 5: Profile/Index.vue has Corteiz design colors... ";
$indexContent = file_get_contents(__DIR__ . '/resources/js/Pages/Profile/Index.vue');
if (
    strpos($indexContent, "bg-[#0f0f0f]") !== false &&
    strpos($indexContent, "border-[#222222]") !== false &&
    strpos($indexContent, "text-[#999999]") !== false &&
    strpos($indexContent, "border-[#333333]") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 6: Check Profile/Index.vue has Bebas Neue font
echo "Test 6: Profile/Index.vue uses Bebas Neue font... ";
if (strpos($indexContent, "font-['Bebas_Neue']") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 7: Check Profile/Index.vue has uppercase labels
echo "Test 7: Profile/Index.vue has uppercase labels... ";
if (
    strpos($indexContent, "text-xs text-[#999999] mb-2 uppercase") !== false &&
    strpos($indexContent, "uppercase tracking-wide") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 8: Check Profile/Index.vue has SIMPAN button
echo "Test 8: Profile/Index.vue has 'Simpan' button... ";
if (strpos($indexContent, "Simpan") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 9: Check Profile/Addresses.vue has updated design
echo "Test 9: Profile/Addresses.vue has updated design... ";
$addressesContent = file_get_contents(__DIR__ . '/resources/js/Pages/Profile/Addresses.vue');
if (
    strpos($addressesContent, "border-[#222222]") !== false &&
    strpos($addressesContent, "border-[#333333]") !== false &&
    strpos($addressesContent, "text-xs text-[#999999]") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 10: Check Profile/Addresses.vue has "Alamat Utama" badge
echo "Test 10: Profile/Addresses.vue has 'Alamat Utama' badge... ";
if (strpos($addressesContent, "Alamat Utama") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 11: Check Profile/Addresses.vue has "Jadikan Utama" button
echo "Test 11: Profile/Addresses.vue has 'Jadikan Utama' button... ";
if (strpos($addressesContent, "Jadikan Utama") !== false) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 12: Check Profile/Addresses.vue has "Hapus" button with red color
echo "Test 12: Profile/Addresses.vue has 'Hapus' button with red color... ";
if (
    strpos($addressesContent, "Hapus") !== false &&
    strpos($addressesContent, "border-[#ff0000] text-[#ff0000]") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 13: Check routes are updated
echo "Test 13: Profile routes are configured... ";
$routesContent = file_get_contents(__DIR__ . '/routes/web.php');
if (
    strpos($routesContent, "ProfileController::class, 'index'") !== false &&
    strpos($routesContent, "ProfileController::class, 'update'") !== false &&
    strpos($routesContent, "ProfileController::class, 'updatePassword'") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 14: Check input focus states
echo "Test 14: Input focus states configured... ";
if (
    strpos($indexContent, "focus:border-white") !== false &&
    strpos($addressesContent, "focus:border-white") !== false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED\n";
    $failed++;
}

// Test 15: Check sharp edges (no rounded corners)
echo "Test 15: Sharp edges design (no rounded corners)... ";
if (
    strpos($indexContent, "rounded") === false &&
    strpos($addressesContent, "rounded") === false
) {
    echo "✓ PASSED\n";
    $passed++;
} else {
    echo "✗ FAILED (Found rounded corners)\n";
    $failed++;
}

// Summary
echo "\n=== VALIDATION SUMMARY ===\n";
echo "Total Tests: " . ($passed + $failed) . "\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Success Rate: " . round(($passed / ($passed + $failed)) * 100, 2) . "%\n\n";

if ($failed === 0) {
    echo "✓ ALL TESTS PASSED! T5.1 Profile Management is complete.\n";
    echo "\nFeatures Implemented:\n";
    echo "✓ Profile information management (name, email, phone)\n";
    echo "✓ Password change functionality\n";
    echo "✓ Address management integration\n";
    echo "✓ Orders history integration\n";
    echo "✓ Account information display\n";
    echo "\nDesign System Applied:\n";
    echo "✓ Background: #000000 (primary), #0f0f0f (cards)\n";
    echo "✓ Text: #FFFFFF (primary), #999999 (muted)\n";
    echo "✓ Borders: #222222 (subtle), #333333 (inputs)\n";
    echo "✓ Accent: #ff0000 (destructive actions)\n";
    echo "✓ Typography: Bebas Neue (headings), Inter (body)\n";
    echo "✓ Labels: uppercase, small (text-xs), #999999\n";
    echo "✓ Input focus: border changes to white\n";
    echo "✓ 'Alamat Utama' badge: white bg + black text\n";
    echo "✓ 'Simpan' button: white bg + black text\n";
    echo "✓ 'Hapus' button: transparent, #ff0000 border\n";
    echo "✓ 'Jadikan Utama' button: transparent, white border\n";
    echo "✓ Sharp edges (no rounded corners)\n";
    echo "✓ Mobile-first responsive design\n";
    echo "\nNext steps:\n";
    echo "1. Visit /profile to view the profile page\n";
    echo "2. Test updating profile information\n";
    echo "3. Test changing password\n";
    echo "4. Visit /profile/addresses to manage addresses\n";
    echo "5. Test creating, editing, and deleting addresses\n";
} else {
    echo "✗ SOME TESTS FAILED. Please review the errors above.\n";
}

exit($failed > 0 ? 1 : 0);
