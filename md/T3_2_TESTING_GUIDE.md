# T3.2 - Shipping Calculator Testing Guide

## Quick Start Testing

### 1. Start Development Server
```bash
cd styleu
php artisan serve
npm run dev  # In another terminal
```

### 2. Login
Navigate to: `http://localhost:8000/login`

**Test Credentials:**
- Customer: `newuser@styleu.com` / `password123`
- Admin: `admin@styleu.com` / `admin123`

### 3. Test Address Management

#### Navigate to Addresses
- Click on your username in the navbar
- Select "ADDRESSES" from dropdown
- Or go directly to: `http://localhost:8000/profile/addresses`

#### Add New Address
1. Click "TAMBAH ALAMAT" button
2. Fill in the form:
   ```
   Label: Rumah
   Nama Penerima: John Doe
   No. Telepon: 081234567890
   Alamat Lengkap: Jl. Sudirman No. 123
   Kota: Jakarta
   Provinsi: DKI Jakarta
   Kode Pos: 12190
   Latitude: -6.2088 (IMPORTANT for shipping!)
   Longitude: 106.8456 (IMPORTANT for shipping!)
   ✓ Jadikan alamat default
   ```
3. Click "SIMPAN"

#### Test Coordinates for Different Cities
```
Jakarta:   -6.2088, 106.8456
Surabaya:  -7.2575, 112.7521
Bandung:   -6.9175, 107.6191
Yogyakarta: -7.7956, 110.3695
Bali:      -8.4095, 115.1889
```

### 4. Test Checkout Flow

#### Add Items to Cart
1. Go to: `http://localhost:8000/products`
2. Click on any product
3. Select size and color
4. Click "ADD TO CART"
5. Repeat for multiple items

#### Proceed to Checkout
1. Click cart icon in navbar
2. Review cart items
3. Click "PROCEED TO CHECKOUT"
4. Or go directly to: `http://localhost:8000/checkout`

#### Select Address & Shipping
1. **Select Address**: Click on an address card
   - Selected address will have white background
   - Must have coordinates (lat/lng) for shipping calculation
2. **Wait for Shipping Rates**: System automatically loads rates
   - Loading spinner appears
   - Rates sorted by price (cheapest first)
3. **Select Shipping Method**: Click on preferred courier
   - Selected method has white background
   - Shows courier name, service, price, and duration
4. **Review Order Summary**:
   - Subtotal (cart items)
   - Shipping cost
   - Total amount

### 5. Test Address Operations

#### Edit Address
1. Go to addresses page
2. Click "EDIT" on any address
3. Modify fields
4. Click "UPDATE"

#### Set Default Address
1. Click "SET DEFAULT" on non-default address
2. Address badge changes to show "DEFAULT"
3. Other addresses lose default status

#### Delete Address
1. Click "HAPUS" on any address
2. Confirm deletion
3. Address removed from list

## Expected Behaviors

### ✅ Success Cases

1. **Address with Coordinates**:
   - Shipping rates load automatically
   - Multiple courier options displayed
   - Prices and durations shown

2. **Address without Coordinates**:
   - Warning message: "⚠ Alamat belum memiliki koordinat"
   - Error when trying to calculate shipping
   - Prompt to update address

3. **Empty Cart**:
   - Redirected to cart page
   - Error message: "Keranjang Anda kosong"

4. **No Addresses**:
   - Prompt to add address
   - Link to address management page

### ⚠️ Error Cases

1. **Invalid Coordinates**:
   - Error message displayed
   - "Coba Lagi" button to retry

2. **API Failure**:
   - Error message: "Gagal memuat tarif pengiriman"
   - Logged to Laravel logs

3. **No Shipping Options**:
   - Message: "Tidak ada layanan pengiriman tersedia"
   - May occur for remote locations

## Validation Checklist

- [ ] Can access address management page
- [ ] Can create new address with coordinates
- [ ] Can edit existing address
- [ ] Can delete address
- [ ] Can set default address
- [ ] Default address badge displays correctly
- [ ] Can access checkout page
- [ ] Cart items display correctly in checkout
- [ ] Can select shipping address
- [ ] Shipping rates load automatically
- [ ] Multiple courier options displayed
- [ ] Can select shipping method
- [ ] Order summary calculates correctly (subtotal + shipping)
- [ ] Mobile responsive design works
- [ ] Corteiz design system applied (black bg, sharp edges, Bebas Neue)
- [ ] Error messages display properly
- [ ] Loading states work correctly

## Troubleshooting

### Shipping Rates Not Loading

**Check:**
1. Address has valid coordinates (lat/lng)
2. BITESHIP_API_KEY is set in .env
3. Internet connection is active
4. Check Laravel logs: `storage/logs/laravel.log`

**Common Issues:**
- Invalid API key → Update .env with valid key
- Missing coordinates → Add lat/lng to address
- API rate limiting → Wait and retry
- Network issues → Check internet connection

### Address Not Saving

**Check:**
1. All required fields filled
2. Phone number format valid
3. Postal code format valid
4. CSRF token present (automatic in Laravel)

### Checkout Page Not Loading

**Check:**
1. Cart has items
2. User is logged in
3. Routes registered: `php artisan route:list --name=checkout`

## API Testing

### Test Biteship API Directly
```bash
php test_biteship_api.php
```

**Expected Output:**
- Success: List of shipping rates with prices
- Failure: Error message with troubleshooting tips

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

Look for:
- `Biteship API Error` - API call failures
- `Shipping Service Error` - Service-level errors

## Database Verification

### Check Addresses Table
```bash
php artisan tinker
```

```php
// List all addresses
\App\Models\Address::all();

// Check specific user's addresses
\App\Models\User::find(1)->addresses;

// Verify coordinates
\App\Models\Address::whereNotNull('latitude')->count();
```

## Performance Notes

- Shipping rate calculation: ~1-3 seconds (API call)
- Address CRUD operations: <100ms (database)
- Checkout page load: <500ms (without shipping rates)

## Security Checklist

- [ ] API key stored in .env (not hardcoded)
- [ ] Address ownership verified in all operations
- [ ] CSRF protection active on forms
- [ ] User authentication required for all routes
- [ ] Input validation on all fields
- [ ] SQL injection prevented (Eloquent ORM)

## Next Steps After Testing

Once T3.2 is validated:
1. Proceed to T3.3 - Payment Integration (Midtrans)
2. Implement order creation with shipping details
3. Add payment webhook handling
4. Complete order status management

## Support

If you encounter issues:
1. Check validation script: `php validate_t3_2.php`
2. Review implementation summary: `T3_2_IMPLEMENTATION_SUMMARY.md`
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify environment variables in `.env`
