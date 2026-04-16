# StyleU E-commerce - Installation Guide

## 📋 Requirements

- PHP 8.3 or higher
- Composer
- Node.js 18+ and npm
- PostgreSQL 14+
- Redis (optional, for caching)
- Laravel Herd (recommended) or any local PHP server

## 🚀 Installation Steps

### 1. Clone Repository

```bash
git clone https://github.com/0xMochamad-Arif-Fahrizal/MARKETTTMARKETTT.git
cd MARKETTTMARKETTT
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Database

Edit `.env` file and set your database credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=styleu
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Configure API Keys

Add these to your `.env` file:

```env
# Midtrans (Payment Gateway)
MIDTRANS_SERVER_KEY=your_midtrans_server_key
MIDTRANS_CLIENT_KEY=your_midtrans_client_key
MIDTRANS_IS_PRODUCTION=false

# Biteship (Shipping Calculator)
BITESHIP_API_KEY=your_biteship_api_key
BITESHIP_ORIGIN_LAT=-6.200000
BITESHIP_ORIGIN_LNG=106.816666
```

**Get API Keys:**
- Midtrans: https://dashboard.midtrans.com/
- Biteship: https://biteship.com/

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Seed Database (Optional)

```bash
php artisan db:seed
```

This will create:
- Admin user: `admin@styleu.com` / `admin123`
- Sample products and categories

### 9. Create Storage Link

```bash
php artisan storage:link
```

### 10. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 11. Start Development Server

**Option A: Using Laravel Herd (Recommended)**
- Just open `http://styleu.test` in your browser
- Herd automatically serves the application

**Option B: Using Artisan**
```bash
php artisan serve
```
Then open `http://localhost:8000`

## 🎨 Access Points

### Frontend (Customer)
- **URL**: `http://styleu.test` or `http://localhost:8000`
- **Test Account**: 
  - Email: `newuser@styleu.com`
  - Password: `password123`

### Admin Panel
- **URL**: `http://styleu.test/admin` or `http://localhost:8000/admin`
- **Admin Account**:
  - Email: `admin@styleu.com`
  - Password: `admin123`

## 🛠️ Development Commands

### Run Development Server
```bash
npm run dev
```

### Build for Production
```bash
npm run build
```

### Run Tests
```bash
php artisan test
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Queue Worker (for background jobs)
```bash
php artisan queue:work
```

## 📁 Project Structure

```
styleu/
├── app/
│   ├── Filament/          # Admin panel resources
│   ├── Http/
│   │   ├── Controllers/   # Application controllers
│   │   └── Middleware/    # Custom middleware
│   ├── Models/            # Eloquent models
│   └── Services/          # Business logic services
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── public/
│   └── fonts/             # OCR A Extended font
├── resources/
│   ├── css/               # Stylesheets
│   ├── js/
│   │   ├── Components/    # Vue components
│   │   ├── Layouts/       # Layout components
│   │   └── Pages/         # Page components
│   └── views/             # Blade templates
└── routes/
    └── web.php            # Web routes
```

## 🎯 Key Features

### Customer Features
- ✅ User authentication (register/login)
- ✅ Product browsing with filters
- ✅ Shopping cart (guest + persistent)
- ✅ Address management
- ✅ Checkout with shipping calculator
- ✅ Payment integration (Midtrans)
- ✅ Order tracking
- ✅ Profile management

### Admin Features
- ✅ Product management (CRUD)
- ✅ Category management
- ✅ Order management
- ✅ Order status updates
- ✅ Tracking number management

## 🎨 Design System

- **Font**: OCR A Extended (monospace)
- **Colors**: Black (#000000), White (#FFFFFF), Gray (#999999)
- **Style**: Corteiz-inspired (sharp edges, no rounded corners)
- **Typography**: UPPERCASE for headings and buttons

## 🔧 Troubleshooting

### Issue: "Class not found" errors
```bash
composer dump-autoload
```

### Issue: Assets not loading
```bash
npm run build
php artisan cache:clear
```

### Issue: Database connection failed
- Check PostgreSQL is running
- Verify database credentials in `.env`
- Create database if it doesn't exist:
  ```bash
  createdb styleu
  ```

### Issue: Permission denied on storage
```bash
chmod -R 775 storage bootstrap/cache
```

### Issue: Vite not connecting
- Make sure `npm run dev` is running
- Check `vite.config.js` server configuration
- Clear browser cache

## 📝 Environment Variables Reference

```env
# Application
APP_NAME=StyleU
APP_ENV=local
APP_DEBUG=true
APP_URL=http://styleu.test

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=styleu
DB_USERNAME=postgres
DB_PASSWORD=

# Midtrans Payment
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

# Biteship Shipping
BITESHIP_API_KEY=
BITESHIP_ORIGIN_LAT=-6.200000
BITESHIP_ORIGIN_LNG=106.816666

# Session
SESSION_DRIVER=database

# Cache
CACHE_DRIVER=file

# Queue
QUEUE_CONNECTION=sync
```

## 🚀 Deployment

### Production Checklist

1. Set environment to production:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Build assets:
```bash
npm run build
```

3. Optimize Laravel:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. Set proper permissions:
```bash
chmod -R 755 storage bootstrap/cache
```

5. Use production API keys for Midtrans and Biteship

## 📞 Support

For issues or questions:
- GitHub Issues: https://github.com/0xMochamad-Arif-Fahrizal/MARKETTTMARKETTT/issues
- Email: [your-email@example.com]

## 📄 License

This project is open-sourced software licensed under the MIT license.

---

**Built with ❤️ using Laravel 11, Inertia.js, Vue 3, and Filament**
