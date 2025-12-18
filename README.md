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

### Message Broadcasting 🎯
- **WhatsApp Broadcasting**: Send messages to contacts, groups, or bulk recipients
- **Multiple Message Types**: Direct messages or template-based messages with parameters
- **Bulk Message Upload**: Excel/CSV upload for mass messaging campaigns
- **Template Parameters**: Support for header and body variables (@{{name}}, @{{price}}, etc.)
- **Recipient Management**: Individual contacts, groups, or custom recipient lists
- **Typing Indicators**: Professional typing indicators before message delivery
- **WAHA API Status**: Real-time connection monitoring with user alerts
- **Message History**: Complete logging and tracking of all sent messages
- **Session-based Filtering**: Send messages using specific WhatsApp sessions
- **Smart Validation**: Intelligent form validation with contextual error messages
- **Success Reporting**: Detailed delivery statistics and failure notifications
- **Message Status Tracking**: Real-time status tracking (sent, failed, pending) for all messages
- **Failed Message Resend**: One-click resend functionality for failed messages with confirmation dialogs
- **Status Badges**: Visual status indicators in message list (Sent/Failed/Pending)
- **Resend Statistics**: Track resend activities in audit trail
- **Queue System**: Asynchronous message processing with Laravel Queue for better performance
- **Auto Retry**: Automatic retry mechanism (3 attempts) for failed messages
- **Rate Limiting**: Built-in rate limiting to prevent WAHA API overload (5 messages/second)
- **Non-blocking Requests**: Send hundreds of messages without request timeout

### Template Management
- **Message Templates**: Create and manage WhatsApp message templates
- **Template Variables**: Support for dynamic variables (@{{1}}, @{{2}}, etc.)
- **Template Preview**: Real-time preview of how messages will appear
- **Usage Tracking**: Monitor template usage statistics and history
- **Template Validation**: Enforce naming conventions and content rules

### Contacts Management
- **Contact Synchronization**: Sync contacts from WhatsApp via WAHA API
- **Profile Pictures**: Automatic fetching and caching of contact profile pictures
- **Contact Filtering**: Filter contacts by session, verification status, and search
- **Real-time Updates**: Live synchronization with WhatsApp contact data
- **Contact Details**: Comprehensive contact information display with profile images
- **Session-based Management**: Organize contacts by WhatsApp session

### Groups Management
- **Group Synchronization**: Sync WhatsApp groups and communities via WAHA API
- **Group Details**: Comprehensive group information including participants, settings, and metadata
- **Participant Management**: Display group participants with contact names and admin roles
- **Profile Pictures**: Automatic fetching and preview of group and participant profile pictures
- **Group Filtering**: Filter groups by session, type (Community/Group), and search
- **Visual Preview**: Clickable profile pictures for enlarged viewing with contextual information

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
- **API Health Monitoring**: Real-time WAHA server connectivity checks with user alerts
- **Typing Indicators**: Professional WhatsApp-style typing indicators for authentic UX
- **Bulk Processing**: Excel/CSV file processing for mass message campaigns
- **Template Variables**: Advanced parameter substitution (header & body variables)
- **Session Management**: Multi-session WhatsApp broadcasting capabilities
- **Real-time Validation**: Smart form validation with contextual feedback
- **Caching System**: Optimized performance with intelligent caching
- **Background Jobs**: Asynchronous processing for better UX
- **Audit Trails**: Complete activity logging and message delivery tracking

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
WAHA_API_URL=https://your-waha-instance.com/api
WAHA_API_KEY=your_waha_api_key_here
WAHA_SESSION_ID=your_session_name

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Queue (required for message broadcasting)
QUEUE_CONNECTION=database
# Queue configuration for async message processing
# Options: database, redis, sqs, sync (sync = synchronous, not recommended for production)
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

### 9. Start Queue Worker (Required for Message Broadcasting)
```bash
# For development
php artisan queue:listen --queue=messages

# For production
php artisan queue:work --queue=messages --tries=3 --timeout=120
```

**Important**: Queue worker must be running for messages to be sent. Without it, messages will remain in `pending` status.

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
- **Contacts**: Contact synchronization and management
- **Groups**: Group synchronization and participant management
- **Master Data**: Categories, items, customers, suppliers
- **Inventory**: Item management, serial numbers, adjustments
- **Transactions**: Purchase/sales orders, invoices, transfers
- **Reports**: Various business reports
- **Sales & Cashier**: POS and sales operations
- **WhatsApp Integration**: WAHA configuration, session management, and contacts

## 📖 Usage

### Basic Usage

1. **Login**: Access the application with your credentials
2. **Configure WAHA**: Set up your WhatsApp API integration
3. **Create Sessions**: Add and manage WhatsApp sessions
4. **Sync Contacts**: Synchronize contacts from WhatsApp
5. **Sync Groups**: Synchronize groups and communities
6. **Monitor Status**: Check connection health and session status

### Message Broadcasting 🎯

The Message Broadcasting feature allows you to send WhatsApp messages to individual contacts, groups, or bulk recipients through your configured WAHA sessions.

#### Sending Direct Messages

1. **Navigate to Messages**: Click on the "Messages" section in your dashboard
2. **Check WAHA Status**: Ensure the WAHA API connection shows "Connected" (green status)
3. **Select Session**: Choose the WhatsApp session to use for broadcasting
4. **Choose Recipient Type**:
   - **Contact**: Select from dropdown or enter phone number manually
   - **Group**: Choose from filtered list of groups for selected session
   - **Recipients**: Upload Excel/CSV file for bulk messaging campaigns
5. **Compose Message**: Write your message in the text area
6. **Send**: Click "Send Message" - typing indicators will appear automatically

#### Using Message Templates

1. **Select Template**: Choose from available message templates in your system
2. **Fill Parameters**: Enter values for any template variables (header/body parameters)
3. **Preview Message**: Review the final formatted message before sending
4. **Send**: Messages are sent with proper variable substitution and typing indicators

#### Bulk Message Campaigns

1. **Download Template**: Get the appropriate Excel template for your message type
2. **Prepare Data**: Fill the template with recipient phone numbers and message content
3. **Upload File**: Import your recipient list through the bulk upload interface
4. **Review Recipients**: Preview all recipients and their messages before sending
5. **Send Campaign**: Broadcast to all recipients with professional typing indicators

#### Excel Template Formats

**Direct Messages Template:**
```csv
Phone Number,Message
6281234567890,Hello! Welcome to our service.
6289876543210,Thank you for your registration.
```

**Template Messages Template:**
```csv
Phone Number,Header Var 1,Header Var 2,Body Var 1,Body Var 2
6281234567890,John Doe,ABC Corp,Laptop,$999
6289876543210,Jane Smith,XYZ Ltd,Phone,$599
```

#### Message Status Tracking

- **Status Monitoring**: All messages are tracked with status (sent, failed, pending)
- **Visual Indicators**: Color-coded status badges in message list
- **Status Filtering**: Filter messages by delivery status
- **Real-time Updates**: Status updates automatically when messages are sent
- **Queue Processing**: Messages are queued with `pending` status and processed asynchronously
- **Status Flow**: `pending` → `sent` (success) or `failed` (after retries)

#### Queue System & Async Processing

The application uses Laravel Queue for efficient message processing:

- **Asynchronous Processing**: All messages are queued and processed in the background
- **Non-blocking**: Send hundreds of messages without request timeout
- **Auto Retry**: Failed messages automatically retry 3 times with exponential backoff (10s, 30s, 60s)
- **Rate Limiting**: Built-in rate limiting (5 messages/second) to prevent WAHA API overload
- **Status Updates**: Message status automatically updates from `pending` → `sent`/`failed`
- **Queue Monitoring**: Monitor queue status and failed jobs via Laravel commands

**Queue Worker Setup:**
```bash
# Development
php artisan queue:listen --queue=messages

# Production
php artisan queue:work --queue=messages --tries=3 --timeout=120 --sleep=3
```

**Monitor Queue:**
```bash
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

#### Failed Message Resend

- **One-Click Resend**: Easily resend failed messages with a single click
- **Confirmation Dialog**: Safety confirmation before resending messages
- **Message Preview**: Preview message content before resending
- **Status Updates**: Automatic status update to 'sent' after successful resend
- **Activity Logging**: All resend activities are logged in audit trail

#### WAHA API Monitoring

- **Real-time Status**: Automatic WAHA API connectivity monitoring
- **Connection Alerts**: Warning messages when API connection is lost
- **Troubleshooting Guidance**: Clear instructions for fixing configuration issues
- **Health Checks**: Regular API endpoint verification

#### Best Practices

- **Session Selection**: Choose appropriate WhatsApp sessions for your target audience
- **Queue Worker**: Always ensure queue worker is running before sending messages
- **Rate Limiting**: System automatically limits to 5 messages/second - adjust if needed
- **Template Variables**: Use descriptive variable names for clarity
- **File Formats**: Always use the provided Excel templates for bulk uploads
- **Phone Number Format**: Include country codes (e.g., 62812... for Indonesia)
- **Message Preview**: Always preview messages before bulk sending campaigns
- **Monitor Status**: Check message status regularly - failed messages can be resent
- **Bulk Sending**: For large campaigns, messages are automatically queued and processed
- **Typing Indicators**: Automatically disabled for bulk sending to improve performance

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

### Contacts Management

1. **Sync Contacts**: Automatically synchronize contacts from WhatsApp sessions
2. **View Contact Details**: Browse and search through synchronized contacts
3. **Filter Contacts**: Filter by session, verification status, or search terms
4. **Profile Pictures**: View contact profile pictures fetched from WhatsApp

#### Contact Synchronization

```php
// Sync contacts from a specific session
$session = Session::find(1);
$response = Http::withHeaders([
    'accept' => '*/*',
    'X-Api-Key' => env('WAHA_API_KEY'),
])->get(env('WAHA_API_URL') . '/api/contacts/all', [
    'session' => $session->session_id,
    'refresh' => 'false'
]);

// Process and save contacts
foreach ($response->json() as $contactData) {
    // Skip group chats and LID contacts
    if (str_ends_with($contactData['id'], '@g.us') ||
        str_ends_with($contactData['id'], '@lid')) {
        continue;
    }

    Contact::updateOrCreate(
        [
            'waha_session_id' => $session->id,
            'wa_id' => $contactData['id']
        ],
        [
            'name' => $contactData['name'] ?? null,
            'verified_name' => $contactData['verifiedName'] ?? null,
            'push_name' => $contactData['pushname'] ?? null,
        ]
    );
}
```

#### Contact Profile Pictures

```php
// Fetch profile picture for a contact
$contact = Contact::find(1);
$response = Http::withHeaders([
    'accept' => '*/*',
    'X-Api-Key' => env('WAHA_API_KEY'),
])->get(env('WAHA_API_URL') . '/api/contacts/profile-picture', [
    'contactId' => $contact->wa_id,
    'refresh' => 'false',
    'session' => $contact->wahaSession->session_id
]);

if ($response->successful()) {
    $data = $response->json();
    $contact->update([
        'profile_picture_url' => $data['profilePictureURL']
    ]);
}
```

### Groups Management

1. **Sync Groups**: Automatically synchronize groups and communities from WhatsApp sessions
2. **View Group Details**: Browse and search through synchronized groups
3. **Filter Groups**: Filter by session, type (Community/Group), or search terms
4. **Participant Information**: View group participants with contact names and admin roles
5. **Profile Pictures**: Click to preview enlarged group and participant profile pictures

#### Group Synchronization

```php
// Sync groups from a specific session
$session = Session::find(1);
$response = Http::withHeaders([
    'accept' => 'application/json',
    'X-Api-Key' => env('WAHA_API_KEY'),
])->get(env('WAHA_API_URL') . '/api/odon/groups');

// Process and save groups
foreach ($response->json() as $groupId => $groupData) {
    Group::updateOrCreate(
        [
            'waha_session_id' => $session->id,
            'group_wa_id' => $groupId,
        ],
        [
            'name' => $groupData['subject'] ?? null,
            'detail' => $groupData,
        ]
    );
}
```

#### Group Profile Pictures

```php
// Fetch profile picture for a group
$group = Group::find(1);
$response = Http::withHeaders([
    'accept' => 'application/json',
    'X-Api-Key' => env('WAHA_API_KEY'),
])->get(env('WAHA_API_URL') . '/api/' . $group->wahaSession->session_id . '/groups/' . $group->group_wa_id . '/picture', [
    'refresh' => 'false'
]);

if ($response->successful()) {
    $data = $response->json();
    $group->update([
        'picture_url' => $data['url']
    ]);
}
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

### Queue System & Message Processing

The application uses Laravel Queue for efficient asynchronous message processing:

#### Queue Configuration

**Environment Setup:**
```env
QUEUE_CONNECTION=database  # Use database queue driver
```

**Start Queue Worker:**
```bash
# Development
php artisan queue:listen --queue=messages

# Production (with retry and timeout)
php artisan queue:work --queue=messages --tries=3 --timeout=120 --sleep=3
```

#### Message Status Flow

1. **Pending**: Message is queued and waiting to be processed
2. **Processing**: Job is executing (handled automatically)
3. **Sent**: Message successfully sent via WAHA API
4. **Failed**: Message failed after all retry attempts

#### Retry Mechanism

- **Max Attempts**: 3 retries per message
- **Backoff Strategy**: Exponential backoff (10s, 30s, 60s)
- **Auto Update**: Status automatically updates after each attempt
- **Final Status**: After 3 failed attempts, status set to `failed`

#### Rate Limiting

- **Default Rate**: 5 messages per second
- **Configurable**: Adjust in `WahaService::sendBulkText()`
- **Per Session**: Rate limiting applied per WAHA session
- **Automatic**: Rate limiting enforced during queue processing

#### Queue Monitoring

```bash
# Check queue status
php artisan queue:work --queue=messages --verbose

# List failed jobs
php artisan queue:failed

# Retry all failed jobs
php artisan queue:retry all

# Retry specific job
php artisan queue:retry {job-id}

# Clear failed jobs
php artisan queue:flush
```

#### Production Deployment

For production, use Supervisor to manage queue workers:

**Supervisor Config** (`/etc/supervisor/conf.d/broadcast-queue.conf`):
```ini
[program:broadcast-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/broadcast/artisan queue:work --queue=messages --tries=3 --timeout=120 --sleep=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/broadcast/storage/logs/queue-worker.log
stopwaitsecs=3600
```

**Supervisor Commands:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start broadcast-queue:*
sudo supervisorctl status broadcast-queue:*
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

#### Session Management
- `GET /health` - Server health check
- `GET /api/version` - Get WAHA version
- `GET /api/sessions` - List all sessions
- `POST /api/sessions/{sessionId}/start` - Start session
- `POST /api/sessions/{sessionId}/stop` - Stop session
- `GET /api/screenshot` - Get QR code for authentication

#### Contacts Management
- `GET /api/contacts/all?session={sessionId}` - Get all contacts for a session
- `GET /api/contacts/profile-picture?contactId={waId}&session={sessionId}&refresh=false` - Get contact profile picture

#### Groups Management
- `GET /api/odon/groups` - Get all groups and communities
- `GET /api/{sessionId}/groups/{groupId}/picture?refresh=false` - Get group profile picture
- `GET /api/contacts/profile-picture?contactId={waId}&session={sessionId}&refresh=false` - Get participant profile picture

#### Message Templates (if available)
- `POST /api/sendText` - Send text message
- `POST /api/sendTemplate` - Send message using template

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

#### Contacts API Endpoints

- `GET /contacts` - List all contacts with filtering and pagination
- `GET /contacts/{id}` - Get contact details
- `POST /contacts/sync` - Sync contacts from WAHA API
- `GET /contacts/audit` - Contacts audit trail

#### Groups API Endpoints

- `GET /groups` - List all groups with filtering and pagination
- `GET /groups/{id}` - Get group details with participants
- `POST /groups/sync` - Sync groups from WAHA API
- `GET /groups/audit` - Groups audit trail

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
- **Queue Monitoring**: Track queue status, failed jobs, and processing statistics
- **Message Status Tracking**: Real-time status updates (pending, sent, failed)
- **Retry Logging**: Detailed logs for message retry attempts and failures

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

3. **Queue Worker** (required for message broadcasting):
   ```bash
   # Start queue worker for message processing
   php artisan queue:work --queue=messages --tries=3 --timeout=120
   
   # Or use supervisor for production (recommended)
   # See supervisor configuration below
   ```

4. **Supervisor Configuration** (Production - Recommended):
   Create `/etc/supervisor/conf.d/broadcast-queue.conf`:
   ```ini
   [program:broadcast-queue]
   process_name=%(program_name)s_%(process_num)02d
   command=php /path/to/broadcast/artisan queue:work --queue=messages --tries=3 --timeout=120 --sleep=3
   autostart=true
   autorestart=true
   stopasgroup=true
   killasgroup=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/path/to/broadcast/storage/logs/queue-worker.log
   stopwaitsecs=3600
   ```
   
   Then run:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start broadcast-queue:*
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

## 🔌 WAHA API Reference

This application integrates with WAHA (WhatsApp HTTP API) for WhatsApp messaging functionality.

### Authentication
All API requests require authentication via `X-Api-Key` header:
```
X-Api-Key: your_waha_api_key
```

### Messaging Endpoints

#### Send Text Message
```http
POST /api/sendText
Content-Type: application/json

{
  "chatId": "6281234567890@s.whatsapp.net",
  "text": "Hello World!",
  "session": "your_session_name",
  "reply_to": null,
  "linkPreview": true,
  "linkPreviewHighQuality": false
}
```

#### Typing Indicators

**Start Typing:**
```http
POST /api/startTyping
Content-Type: application/json

{
  "chatId": "6281234567890@s.whatsapp.net",
  "session": "your_session_name"
}
```

**Stop Typing:**
```http
POST /api/stopTyping
Content-Type: application/json

{
  "chatId": "6281234567890@s.whatsapp.net",
  "session": "your_session_name"
}
```

#### Health Check
```http
GET /health
```

### Chat ID Formats

- **Individual Contacts**: `6281234567890@s.whatsapp.net`
- **WhatsApp Groups**: `120363XXXXXXX@g.us`

### Response Format

**Success Response:**
```json
{
  "id": "true_6281234567890@s.whatsapp.net_ABC123DEF456",
  "timestamp": 1703123456,
  "from": "6281234567890@s.whatsapp.net",
  "to": "6289876543210@s.whatsapp.net",
  "ack": 1
}
```

**Error Response:**
```json
{
  "error": "Chat not found",
  "code": 404
}
```

### Rate Limiting

- **WAHA API**: Built-in rate limiting to prevent WhatsApp blocking
- **Application Level**: Additional rate limiting (default: 5 messages/second)
- **Configurable**: Adjust rate limit in `WahaService::sendBulkText()` method
- **Recommended**: Max 5 messages/second per session (300 messages/minute)
- **Bulk Sending**: Rate limiting automatically applied for bulk campaigns
- **Queue Processing**: Rate limiting enforced during async queue processing

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

### Version 1.4.1 🚀
- **Queue System Implementation**: Asynchronous message processing with Laravel Queue
- **Auto Retry Mechanism**: Automatic retry (3 attempts) with exponential backoff for failed messages
- **Rate Limiting**: Built-in rate limiting (5 messages/second) to prevent WAHA API overload
- **Non-blocking Requests**: Send hundreds of messages without request timeout
- **Optimized Performance**: Reduced typing indicator delay and improved bulk sending efficiency
- **Enhanced Status Tracking**: Improved status flow (pending → sent/failed) with automatic updates
- **Queue Monitoring**: Commands and tools for monitoring queue status and failed jobs
- **Production Ready**: Supervisor configuration and production deployment guidelines

### Version 1.4.0 🎯
- **Message Broadcasting System**: Complete WhatsApp message broadcasting platform
- **Bulk Message Campaigns**: Excel/CSV upload for mass messaging with custom parameters
- **Template Parameter Support**: Advanced header and body variable substitution
- **Typing Indicators**: Professional WhatsApp-style typing indicators for authentic UX
- **WAHA API Integration**: Real-time message sending via WAHA HTTP API
- **Session-based Broadcasting**: Send messages using specific WhatsApp sessions
- **Smart Recipient Management**: Individual contacts, groups, or custom recipient lists
- **Message Preview System**: WhatsApp-like message preview before sending
- **WAHA Status Monitoring**: Real-time API connectivity checks with user alerts
- **Advanced Validation**: Context-aware form validation with detailed error messages
- **Success Reporting**: Comprehensive delivery statistics and failure notifications
- **Excel Template Generator**: Dynamic Excel templates for different message types
- **Contact Dropdown Selection**: Session-filtered contact selection with names
- **Group Broadcasting**: Send messages to WhatsApp groups with session filtering
- **Message History**: Complete logging and tracking of all broadcast activities
- **Message Status Tracking**: Real-time status tracking (sent, failed, pending) for all messages
- **Failed Message Resend**: One-click resend functionality for failed messages with confirmation dialogs
- **Status Badges**: Visual status indicators in message list with color-coded badges
- **Resend Statistics**: Track resend activities in audit trail with detailed activity logs
- **Enhanced Audit Trail**: Improved audit trail with status information and resend activity tracking

### Version 1.3.0
- **Groups Management System**: Complete group and community synchronization from WhatsApp
- **Participant Information**: Display group participants with contact names and admin roles
- **Group Profile Pictures**: Automatic fetching and preview of group profile pictures
- **Participant Profile Pictures**: Clickable participant photos with enlarged preview
- **Group Type Filtering**: Distinguish between Communities and regular Groups
- **Visual Group Details**: Comprehensive group information with participant management
- **WAHA Groups API**: Full integration with WhatsApp groups API

### Version 1.2.0
- **Contacts Management System**: Complete contact synchronization from WhatsApp
- **Profile Picture Integration**: Automatic fetching and caching of contact photos
- **Advanced Filtering**: Filter contacts by session, verification status, and search
- **WAHA Contacts API**: Full integration with WhatsApp contacts API
- **Contact Details View**: Comprehensive contact information with profile images
- **Database Caching**: Optimized performance with profile picture caching

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

## 🐛 Troubleshooting

### Message Status Issues

**Messages stuck in `pending` status:**
- Ensure queue worker is running: `php artisan queue:work --queue=messages`
- Check queue connection in `.env`: `QUEUE_CONNECTION=database`
- Verify database connection is working
- Check logs: `storage/logs/laravel.log`

**Messages immediately `failed`:**
- Verify WAHA API connection and credentials
- Check session ID is valid and active
- Review error logs for detailed failure reasons
- Ensure WAHA server is accessible

**Queue not processing:**
- Restart queue worker
- Check for failed jobs: `php artisan queue:failed`
- Retry failed jobs: `php artisan queue:retry all`
- Verify database connection

### Performance Issues

**Slow message sending:**
- Check rate limiting settings (default: 5 messages/second)
- Monitor queue worker performance
- Consider increasing queue worker processes
- Check WAHA API response times

**High memory usage:**
- Reduce queue worker processes
- Increase PHP memory limit
- Monitor queue batch sizes

## 🐛 Support

If you encounter any issues:

1. Check the [Issues](https://github.com/your-username/broadcast/issues) page
2. Review troubleshooting section above
3. Check queue status and logs
4. Create a new issue with detailed information
5. Contact the development team

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
