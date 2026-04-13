# Project: StyleU E-Commerce Fashion Platform

## Stack (FIXED — do not change)
- Framework: Laravel 11 (PHP 8.3)
- Frontend: Inertia.js + Vue.js 3 (Composition API) + Tailwind CSS
- Admin Panel: Filament PHP v3
- Auth: Laravel Sanctum (session-based, cookie HttpOnly)
- ORM: Eloquent (no raw queries unless explicitly needed)
- Database: PostgreSQL (primary) + Redis (cache + session + queue)
- Payment: Midtrans (QRIS, VA Bank, Kartu Kredit)
- Shipping: Biteship API (agregator JNE/J&T/SiCepat/dll)
- Image: Cloudinary (upload + optimasi otomatis)
- Email: Laravel Notification + Resend (atau SMTP Gmail)
- Queue: Laravel Queue dengan Redis driver
- Architecture: Modular Monolith

## Database Tables
users, addresses, categories, products, product_variants,
product_images, carts, cart_items, orders, order_items

## Order Status State Machine
PENDING_PAYMENT → PAID → PROCESSING → SHIPPED → DELIVERED
PENDING_PAYMENT → CANCELLED | PAYMENT_FAILED

## Pages (User)
/, /products, /products/{slug}, /cart, /checkout,
/orders, /orders/{number}, /profile

## Pages (Admin via Filament)
/admin/products, /admin/orders, /admin/users, /admin/categories

## Critical Constraints
- Stok HANYA dikelola di product_variants.stock, BUKAN di products
- Update stok WAJIB dalam DB transaction dengan pessimistic locking
- API Midtrans webhook WAJIB diverifikasi HMAC signature sebelum diproses
- Cart persisten di database — bukan hanya di session/localStorage
- Guest cart di session; saat login, merge ke cart permanen user
- API keys Midtrans/Biteship/Cloudinary HANYA di .env, tidak di kode