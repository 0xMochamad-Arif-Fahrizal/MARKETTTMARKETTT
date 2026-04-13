# Non-negotiable Rules

SECURITY
- No API keys hardcoded — gunakan env() helper selalu
- Midtrans webhook: verifikasi HMAC signature sebelum update apapun
- Admin routes diproteksi di middleware level, bukan hanya frontend
- Password hashed dengan bcrypt via Laravel Hash facade
- CSRF protection aktif di semua form (Laravel default)

DATABASE
- Semua query via Eloquent ORM — prepared statements by default
- Stok update: SELECT FOR UPDATE dalam DB transaction
- Soft delete untuk products (jaga integritas order history)
- Alamat disimpan sebagai JSONB snapshot di orders saat checkout

CODE QUALITY
- Vue Composition API dengan <script setup> syntax
- Gunakan Inertia props untuk data dari Laravel ke Vue
- Tailwind CSS untuk semua styling — no custom CSS kecuali terpaksa
- Error handling: try/catch di semua method yang call API eksternal