# Broadcast - WhatsApp Management System

<div align="center">  
  <p>
    <img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP">
    <img src="https://img.shields.io/badge/Laravel-12-red.svg" alt="Laravel">
    <img src="https://img.shields.io/badge/Livewire-3.x-pink.svg" alt="Livewire">
    <img src="https://img.shields.io/badge/Flux_UI-2.x-white.svg" alt="Flux UI">
    <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
  </p>

  <h3>A modern Laravel-based WhatsApp session management system powered by WAHA (WhatsApp HTTP API)</h3>

</div>

## 🚀 Features

### WhatsApp Integration
- **WAHA Integration**: Full integration with WhatsApp HTTP API
- **Session Management**: Create, manage, and monitor WhatsApp sessions
- **Real-time Status**: Live connection status and session health monitoring
- **QR Code Scanning**: Easy WhatsApp Web authentication via QR codes

### Template Management
- **Message Templates**: Create and manage WhatsApp message templates
- **Template Variables**: Support for dynamic variables (@{{1}}, @{{2}}, etc.)
- **Template Preview**: Real-time preview of how messages will appear
- **Usage Tracking**: Monitor template usage statistics and history
- **Template Validation**: Enforce naming conventions and content rules

### User Management
- **Role-Based Access Control**: Comprehensive permission system
- **User Authentication**: Secure login and session management
- **Activity Logging**: Track all user actions and system events

### System Features
- **Dashboard**: Overview of system status and WhatsApp connections
- **Configuration Management**: Easy setup of WAHA API credentials
- **Responsive Design**: Modern UI built with Flux UI components
- **Dark Mode Support**: Full dark/light theme support

### Advanced Features
- **API Health Monitoring**: Real-time WAHA server connectivity checks
- **Template System**: Dynamic message templates with variable substitution
- **Caching System**: Optimized performance with intelligent caching
- **Background Jobs**: Asynchronous processing for better UX
- **Audit Trails**: Complete activity logging and tracking

## 📋 Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 11.x
- **Database**: MySQL 8.0+, PostgreSQL 13+, or SQLite 3.8.8+
- **Node.js**: 18.x or higher (for asset compilation)
- **WAHA Server**: Running WhatsApp HTTP API instance

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/broadcast.git
cd broadcast
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
cp .env.example .env
```

Edit `.env` file with your configuration:
```env
APP_NAME="Broadcast"
APP_ENV=production
APP_KEY=base64:your_app_key_here
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=broadcast
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# WAHA Configuration
WAHA_API_URL=https://your-waha-instance.com
WAHA_API_KEY=your_waha_api_key_here

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Queue (optional)
QUEUE_CONNECTION=database
```

### 5. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 6. Generate Application Key
```bash
php artisan key:generate
```

### 7. Build Assets
```bash
npm run build
# or for development
npm run dev
```

### 8. Start the Application
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## ⚙️ Configuration

### WAHA Setup

1. **Install WAHA Server**: Follow the [WAHA documentation](https://waha.devlike.pro/) to set up your WAHA instance.

2. **Configure Environment**: Add your WAHA credentials to `.env`:
   ```env
   WAHA_API_URL=https://your-waha-instance.cinta.sumopod.my.id
   WAHA_API_KEY=mTQCrBGXqcJHzM5PeZd9vKuYVqNLLb3T
   ```

3. **Access Configuration**: Navigate to `/waha` in your application to configure WAHA settings through the web interface.

### Template Configuration

Templates follow specific naming and validation rules:

- **Template Name**: Must use only lowercase letters and underscores (e.g., `welcome_message`)
- **Header**: Optional, maximum 60 characters
- **Body**: Required, maximum 1024 characters, supports variables like @{{1}}, @{{2}}
- **Variables**: Use @{{1}}, @{{2}}, etc. for dynamic content substitution
- **Formatting**: Supports `*bold*`, `_italic_` text formatting

**Example Template:**
```
Name: order_notification
Header: New Order Received!
Body: Hello @{{1}}, you have a new order #@{{2}} for $@{{3}}.
```

### Permissions

The application includes the following permission groups:

- **System**: Company settings, backups, system management
- **Users & Roles**: User and role management
- **Templates**: Message template management and usage tracking
- **Master Data**: Categories, items, customers, suppliers
- **Inventory**: Item management, serial numbers, adjustments
- **Transactions**: Purchase/sales orders, invoices, transfers
- **Reports**: Various business reports
- **Sales & Cashier**: POS and sales operations
- **WhatsApp Integration**: WAHA configuration and session management

## 📖 Usage

### Basic Usage

1. **Login**: Access the application with your credentials
2. **Configure WAHA**: Set up your WhatsApp API integration
3. **Create Sessions**: Add and manage WhatsApp sessions
4. **Monitor Status**: Check connection health and session status

### Template Management

1. **Create Templates**: Design reusable message templates with variables
2. **Preview Messages**: See real-time preview of how messages will appear
3. **Manage Templates**: Edit, activate/deactivate, and track template usage
4. **Template Variables**: Use @{{1}}, @{{2}}, etc. for dynamic content

#### Template Features

```php
// Create a template with variables
$template = Template::create([
    'name' => 'welcome_message',
    'header' => 'Welcome to Our Service!',
    'body' => 'Hello @{{1}}, thank you for choosing @{{2}}. Your account is now active.',
    'is_active' => true
]);

// Template will render as:
// Header: Welcome to Our Service!
// Body: Hello John, thank you for choosing Our Company. Your account is now active.
```

### WhatsApp Session Management

```php
// Create a new session
$session = Session::create([
    'name' => 'My WhatsApp Session',
    'session_id' => 'session_001'
]);

// Check session status via WAHA API
$response = Http::withHeaders([
    'X-Api-Key' => env('WAHA_API_KEY')
])->get(env('WAHA_API_URL') . '/api/sessions/' . $session->session_id);
```

### Template Management

```php
// Create a new template
$template = Template::create([
    'name' => 'order_confirmation',
    'header' => 'Order Confirmed! 🎉',
    'body' => 'Hi @{{1}}, your order #@{{2}} has been confirmed. Total: $@{{3}}',
    'is_active' => true
]);

// Update usage count when template is used
$template->incrementUsageCount();
$template->last_used_at = now();
$template->save();

// Search templates
$templates = Template::where('name', 'like', '%welcome%')
    ->where('is_active', true)
    ->get();
```

### API Health Check

The application automatically monitors WAHA connectivity:

```php
// Check WAHA server health
$wahaHealth = Http::withHeaders([
    'accept' => 'application/json',
    'X-Api-Key' => env('WAHA_API_KEY')
])->get(env('WAHA_API_URL') . '/health');

if ($wahaHealth->successful()) {
    // WAHA server is connected
    $status = 'Connected';
} else {
    // WAHA server is unreachable
    $status = 'Disconnected';
}
```

## 🔧 API Reference

### WAHA Integration Endpoints

The application integrates with WAHA API endpoints:

- `GET /health` - Server health check
- `GET /api/version` - Get WAHA version
- `GET /api/sessions` - List all sessions
- `POST /api/sessions/{sessionId}/start` - Start session
- `POST /api/sessions/{sessionId}/stop` - Stop session
- `GET /api/screenshot` - Get QR code for authentication

### Application API

The application provides RESTful APIs for:

- User management
- Role and permission management
- Session management
- Template management
- System configuration

#### Template API Endpoints

- `GET /templates` - List all templates
- `POST /templates` - Create new template
- `GET /templates/{id}` - Get template details
- `PUT /templates/{id}` - Update template
- `DELETE /templates/{id}` - Delete template
- `GET /templates/audit` - Template audit trail

## 🔐 Security

- **Authentication**: Laravel Sanctum for API authentication
- **Authorization**: Spatie Laravel Permission for role-based access
- **CSRF Protection**: Built-in Laravel CSRF protection
- **Input Validation**: Comprehensive server-side validation
- **SQL Injection Prevention**: Eloquent ORM with prepared statements

## 📊 Monitoring & Logging

- **Activity Logging**: All user actions are logged using Spatie Activity Log
- **Error Logging**: Comprehensive error tracking and reporting
- **Performance Monitoring**: Built-in Laravel Telescope support
- **Health Checks**: Automated WAHA connectivity monitoring

## 🧪 Testing

```bash
# Run PHP tests
php artisan test

# Run JavaScript tests
npm test

# Run E2E tests
npm run test:e2e
```

## 🚀 Deployment

### Production Deployment

1. **Environment Setup**:
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```

2. **Optimize Application**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Queue Worker** (if using queues):
   ```bash
   php artisan queue:work
   ```

4. **Web Server Configuration**: Configure Apache/Nginx for Laravel

### Docker Deployment

```dockerfile
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache nginx supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

# Copy application
COPY . /var/www/html

# Install Composer dependencies
RUN composer install --optimize-autoloader --no-dev

# Build assets
RUN npm ci && npm run build

# Configure nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/var/www/html/docker/supervisord.conf"]
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation as needed
- Ensure all tests pass before submitting PR

## 📝 Changelog

### Version 1.1.0
- **Template Management System**: Complete CRUD for WhatsApp message templates
- **Template Variables**: Support for dynamic content (@{{1}}, @{{2}}, etc.)
- **Real-time Preview**: Live preview of message templates
- **Template Validation**: Naming conventions and content rules
- **Usage Tracking**: Monitor template usage statistics
- **Enhanced UI**: Improved interface with preview cards

### Version 1.0.0
- Initial release with WAHA integration
- Complete WhatsApp session management
- Role-based access control
- Modern Flux UI interface
- Real-time health monitoring

## 🐛 Support

If you encounter any issues:

1. Check the [Issues](https://github.com/your-username/broadcast/issues) page
2. Create a new issue with detailed information
3. Contact the development team

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

### Core Technologies
- [Laravel](https://laravel.com/) - The PHP framework powering the application
- [Livewire](https://laravel-livewire.com/) - For dynamic, reactive UI components
- [Flux UI](https://fluxui.dev/) - Modern, accessible UI component library
- [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS framework
- [WAHA](https://waha.devlike.pro/) - WhatsApp HTTP API for seamless WhatsApp integration

### Supporting Libraries
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) - Advanced permission management
- [Laravel Sanctum](https://laravel.com/docs/sanctum) - API authentication
- [Spatie Activity Log](https://spatie.be/docs/laravel-activitylog) - Comprehensive activity logging
- [Laravel Telescope](https://laravel.com/docs/telescope) - Application debugging and monitoring

### Development Tools
- [Composer](https://getcomposer.org/) - PHP dependency management
- [NPM](https://www.npmjs.com/) - Node.js package management
- [Vite](https://vitejs.dev/) - Fast build tool and development server

---

<p align="center">
  Made with ❤️ using Laravel & WAHA
</p>
