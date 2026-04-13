# Checkout Blank Screen Fix

## Problem
The checkout page was showing a blank screen with the following JavaScript error:
```
TypeError: Cannot read properties of undefined (reading 'recipient_name')
```

## Root Cause
In Step 3 (Payment Confirmation) of the checkout flow, the template was trying to access `selectedAddress.recipient_name` and `selectedShipping.courier_name` without checking if these objects exist first.

When the page initially loads or when there are no addresses/shipping options selected, these computed properties return `undefined`, causing the template to crash and show a blank screen.

## Solution
Added `v-if` conditional rendering to the Address Summary and Shipping Summary sections in Step 3:

### Before:
```vue
<div>
    <h3>Alamat Pengiriman</h3>
    <div>
        <p>{{ selectedAddress.recipient_name }}</p>
        <!-- Error: selectedAddress might be undefined -->
    </div>
</div>
```

### After:
```vue
<div v-if="selectedAddress">
    <h3>Alamat Pengiriman</h3>
    <div>
        <p>{{ selectedAddress.recipient_name }}</p>
        <!-- Safe: only renders when selectedAddress exists -->
    </div>
</div>
```

## Files Modified
- `resources/js/Pages/Checkout/Index.vue` - Added `v-if` guards for `selectedAddress` and `selectedShipping`

## Testing
1. Login as test user: `newuser@styleu.com` / `password123`
2. Add products to cart
3. Navigate to checkout page - should load without blank screen
4. Complete all 3 steps:
   - Step 1: Select address
   - Step 2: Select shipping method
   - Step 3: Confirm and pay (should show address and shipping details)

## Status
✅ **FIXED** - Checkout page now loads correctly without blank screen errors.

## Related Fixes
This fix complements the previous orphaned cart items fix:
- Model relationships now filter soft-deleted products
- Orphaned cart items cleaned from database
- Checkout page template now handles undefined data gracefully
