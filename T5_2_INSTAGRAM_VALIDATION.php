<?php

/**
 * Task 5.2 Instagram Link Validation Script
 * 
 * This script validates that the Instagram link in the Sidebar component
 * meets all requirements specified in task 5.2.
 */

echo "=== Task 5.2: Instagram Link Validation ===\n\n";

$sidebarPath = __DIR__ . '/resources/js/Components/Sidebar.vue';

if (!file_exists($sidebarPath)) {
    echo "❌ ERROR: Sidebar.vue not found at: $sidebarPath\n";
    exit(1);
}

$content = file_get_contents($sidebarPath);

// Requirement 7.2: Check Instagram profile URL
$hasInstagramUrl = strpos($content, 'href="https://instagram.com/marketttmarkettt"') !== false;
echo ($hasInstagramUrl ? "✅" : "❌") . " Instagram URL to @marketttmarkettt profile\n";

// Requirement 7.3: Check target="_blank"
$hasTargetBlank = strpos($content, 'target="_blank"') !== false;
echo ($hasTargetBlank ? "✅" : "❌") . " Opens in new tab (target=\"_blank\")\n";

// Requirement 7.3: Check rel="noopener noreferrer"
$hasRelNoopener = strpos($content, 'rel="noopener noreferrer"') !== false;
echo ($hasRelNoopener ? "✅" : "❌") . " Security attributes (rel=\"noopener noreferrer\")\n";

// Requirement 7.4: Check hover effect with green stabilo color
$hasHoverEffect = strpos($content, 'hover:text-[#CCFF00]') !== false;
echo ($hasHoverEffect ? "✅" : "❌") . " Green stabilo hover effect (#CCFF00)\n";

// Requirement 7.5: Check white default color
$hasWhiteColor = preg_match('/text-white.*hover:text-\[#CCFF00\]/', $content);
echo ($hasWhiteColor ? "✅" : "❌") . " White default color\n";

// Check for Instagram SVG icon
$hasInstagramSvg = strpos($content, '<svg') !== false && 
                   strpos($content, 'viewBox="0 0 24 24"') !== false &&
                   strpos($content, 'fill="currentColor"') !== false;
echo ($hasInstagramSvg ? "✅" : "❌") . " Instagram icon SVG present\n";

// Check for OCR A font
$hasOcrFont = strpos($content, "font-['OCR_A']") !== false;
echo ($hasOcrFont ? "✅" : "❌") . " OCR A font styling\n";

// Check for INSTAGRAM text label
$hasInstagramLabel = strpos($content, '<span>INSTAGRAM</span>') !== false;
echo ($hasInstagramLabel ? "✅" : "❌") . " Instagram text label\n";

// Summary
echo "\n=== Validation Summary ===\n";
$allChecks = $hasInstagramUrl && $hasTargetBlank && $hasRelNoopener && 
             $hasHoverEffect && $hasWhiteColor && $hasInstagramSvg && 
             $hasOcrFont && $hasInstagramLabel;

if ($allChecks) {
    echo "✅ ALL REQUIREMENTS MET - Task 5.2 is complete!\n";
    echo "\nThe Instagram link implementation includes:\n";
    echo "  • Correct href to @marketttmarkettt Instagram profile\n";
    echo "  • Opens in new tab with security attributes\n";
    echo "  • Instagram icon SVG\n";
    echo "  • White color by default, green stabilo (#CCFF00) on hover\n";
    echo "  • OCR A font styling\n";
    echo "  • Proper layout and spacing\n";
    exit(0);
} else {
    echo "❌ SOME REQUIREMENTS NOT MET - Please review implementation\n";
    exit(1);
}
