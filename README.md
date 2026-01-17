# ShopHub - E-commerce Application

A Laravel-based e-commerce application with single login page for both customers and admins, featuring role-based redirection and full CRUD operations.

## Features

### Authentication
- **Single Login Page**: Both customers and admins use the same login page
- **Role-Based Redirection**:
  - Admin users are redirected to `/admin/products` (Admin Panel)
  - Customer users are redirected to `/products` (Customer Dashboard)
- **No Registration**: Registration is disabled - users are created manually or via database seeders
- **Shared Logout**: Both customer and admin use the same logout functionality

### Admin Features
- **Product CRUD Operations**:
  - Create new products (name, SKU, price, stock, status)
  - Read/List all products with search and pagination
  - Update existing products
  - Delete products
  - Toggle product active/inactive status

### Customer Features
- **Product Listing**: View all active products with search
- **Cart Operations**:
  - Add products to cart
  - View cart details
  - Update cart item quantities
  - Remove items from cart
  - Checkout

### API Architecture
- **Session-Based Authentication**: Uses Laravel web session (no Sanctum tokens needed for admin/customer)
- **RESTful APIs**: All operations use API endpoints for AJAX requests
- **Separate API Endpoints**:
  - Admin: `/api/admin/*`
  - Customer: `/api/*`

## Project Structure

### Routes
- `routes/web.php`: Blade-based routes (login, logout, product/cart pages)
- `routes/api.php`: API endpoints for AJAX operations

### Controllers
- `app/Http/Controllers/CustomerAuthController.php`: Customer authentication
- `app/Http/Controllers/Admin/AdminAuthController.php`: Admin authentication
- `app/Http/Controllers/Admin/ProductController.php`: Admin product CRUD
- `app/Http/Controllers/CartController.php`: Customer cart operations

### Middleware
- `app/Http/Middleware/AdminMiddleware.php`: Validates admin role access

### Models
- `app/Models/User.php`: User model with role relationship
- `app/Models/Role.php`: Role model (customer, admin)
- `app/Models/Product.php`: Product model
- `app/Models/Cart.php`: Cart model
- `app/Models/CartItem.php`: Cart item model

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js (for frontend assets)
- MySQL/PostgreSQL database

### Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd <repository-directory>
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
```

4. **Configure database in `.env` file**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

5. **Generate application key**
```bash
php artisan key:generate
```

6. **Run migrations and seeders**
```bash
php artisan migrate --seed
```

7. **Link storage**
```bash
php artisan storage:link
```

8. **Install frontend dependencies**
```bash
npm install
npm run build
```

## Database

### Roles Table
The seeder automatically creates two roles:
- `customer`
- `admin`

### Default Admin User
Use these credentials to login as admin (if seeded):
- **Email**: `admin@example.com`
- **Password**: `password`

Or create your own admin user:
```bash
php artisan tinker
>>> $admin = \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password'),
    'role_id' => \App\Models\Role::where('name', 'admin')->first()->id
]);
```

## API Endpoints

### Admin APIs (Protected with AdminMiddleware)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/products` | List all products (with pagination) |
| POST | `/api/admin/products` | Create new product |
| GET | `/api/admin/products/{id}` | Get product details |
| PUT | `/api/admin/products/{id}` | Update product |
| DELETE | `/api/admin/products/{id}` | Delete product |
| POST | `/api/admin/products/{id}/toggle` | Toggle product status |

### Customer APIs (Protected with Web Session)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/products` | List active products (with search) |
| POST | `/api/cart/items` | Add item to cart |
| GET | `/api/cart` | Get cart details |
| PATCH | `/api/cart/items/{id}` | Update cart item quantity |
| DELETE | `/api/cart/items/{id}` | Remove item from cart |
| POST | `/api/cart/checkout` | Checkout (process order) |

## Web Routes

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/` | Redirect to login |
| GET | `/login` | Show login page (Single login for customers & admins) |
| POST | `/login` | Process login |
| POST | `/logout` | Logout user |
| GET | `/products` | Customer product listing page |
| GET | `/cart` | Customer cart page |
| GET | `/admin/products` | Admin product management page |

## Blade Views

### Customer Views
- `resources/views/customer/auth/login.blade.php` - Login page
- `resources/views/customer/products/list.blade.php` - Product listing
- `resources/views/customer/cart/index.blade.php` - Cart page
- `resources/views/customer/layouts/` - Customer layouts

### Admin Views
- `resources/views/admin/products/index.blade.php` - Admin product management
- `resources/views/admin/layouts/app.blade.php` - Admin layout

### Common Views
- `resources/views/layouts/app.blade.php` - Base layout
- `resources/views/layouts/navigation.blade.php` - Navigation bar

## Workflow

### Customer Flow
1. Customer visits `/login` (single login page)
2. Logs in with customer credentials
3. Redirected to `/products` (customer product listing)
4. Can add items to cart via AJAX
5. Can view cart at `/cart`
6. Can checkout

### Admin Flow
1. Admin visits `/login` (same login page)
2. Logs in with admin credentials
3. Redirected to `/admin/products` (admin panel)
4. Can perform CRUD operations on products via AJAX
5. Can toggle product status
6. Can logout (redirects to login page)

## Technologies Used

- **Backend**: Laravel 10.x
- **Frontend**: Blade Templates, Bootstrap 5.3, Alpine.js
- **JavaScript**: Axios for AJAX requests
- **Database**: MySQL/PostgreSQL
- **CSS Framework**: Bootstrap 5.3 with custom styles
- **Icons**: Bootstrap Icons

## Security

- CSRF protection enabled on all forms
- Session-based authentication
- Role-based authorization
- Form request validation
- SQL injection prevention via Eloquent
- XSS protection via Blade templating

## Development

### Adding New Features
- Add routes to `routes/web.php` or `routes/api.php`
- Create controllers in `app/Http/Controllers/`
- Create views in `resources/views/`
- Run migrations: `php artisan migrate`
- Seed data if needed: `php artisan db:seed`

### Testing
```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter=CartTest
```

## Deployment

### Production Checklist
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Configure queue worker (if using queues)
7. Set up proper file permissions for storage/

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/laravel/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Troubleshooting

### "Route not defined" Error
- Clear route cache: `php artisan route:clear`
- Check route names in `routes/web.php` and `routes/api.php`

### "Unauthenticated" Error on APIs
- Ensure user is logged in via web session
- Check `bootstrap/app.php` middleware configuration
- Verify `auth:web` or `web` middleware is applied

### Database Connection Error
- Check `.env` database configuration
- Run migrations: `php artisan migrate`

## License

This project is open-source and available under the [MIT license](LICENSE).

## Support

For issues and questions, please check Laravel documentation:
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel API Documentation](https://laravel.com/docs/api)
# Product-cart-api
