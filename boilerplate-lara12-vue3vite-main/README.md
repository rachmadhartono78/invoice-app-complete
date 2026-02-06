# Laravel + Vue 3 + TypeScript Boilerplate

A production-ready boilerplate for building modern web applications with Laravel 11, Vue 3, and TypeScript. This starter includes a complete authentication system, role-based access control (RBAC), multi-tenancy support, and real-time notifications.

## Features

### Backend (Laravel 11)
- **Authentication**: Laravel Sanctum with token-based authentication
- **RBAC System**: Role-based access control with Authorities, Menus, and Actions
- **Multi-Tenancy**: Organization-based user segmentation
- **User Management**: Multi-identifier system (email, phone, username) with verification
- **Real-time Notifications**: Laravel Reverb + Pusher for WebSocket connections
- **API-First Architecture**: RESTful API endpoints with consistent JSON responses
- **Sample CRUD**: Complete Item CRUD example

### Frontend (Vue 3 + TypeScript)
- **TypeScript**: Full TypeScript support for type safety
- **Vue 3 Composition API**: Modern Vue development with `<script setup>`
- **State Management**: Pinia with persistence
- **Routing**: Vue Router with authentication guards
- **UI Framework**: Tailwind CSS + Flowbite components
- **Real-time**: Laravel Echo integration for live updates
- **Component Structure**: Atomic Design pattern (atoms, molecules, organisms)

## Tech Stack

- **Backend**: Laravel 11.44.1
- **Frontend**: Vue 3 + TypeScript
- **Build Tool**: Vite
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Real-time**: Laravel Reverb + Pusher
- **CSS**: Tailwind CSS + Flowbite
- **State**: Pinia
- **HTTP Client**: Axios

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

## Installation

### 1. Clone the repository
```bash
git clone <repository-url>
cd boiler-starter
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database and other configuration:
```env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

VITE_APP_URL=http://localhost:8000
VITE_REVERB_APP_KEY=your-reverb-key
VITE_REVERB_PORT=8080
```

### 5. Database Setup
```bash
php artisan migrate --seed
```

This will create:
- 3 sample users (superadmin, admin, user)
- 3 organizations
- Core RBAC tables (Applications, Menus, Authorities, Actions)
- Sample Item CRUD tables

### 6. Start Development Servers

**Option 1: All services at once (recommended)**
```bash
composer run dev
```

**Option 2: Separately**
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:listen

# Terminal 3: Real-time log viewer
php artisan pail

# Terminal 4: Vite dev server
npm run dev
```

### 7. Access the Application

- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api

## Default Credentials

- **Super Admin**: superadmin@example.com / password
- **Admin**: admin@example.com / password
- **User**: user@example.com / password

## Project Structure

### Backend
```
app/
├── Http/
│   ├── Controllers/API/
│   │   ├── Auth/           # Authentication controllers
│   │   ├── Master/         # User management
│   │   ├── Settings/       # RBAC configuration
│   │   ├── Notification/   # Notifications
│   │   └── ItemController  # Sample CRUD
│   └── Middleware/
├── Models/                 # Eloquent models
└── ...

database/
├── migrations/             # Database migrations
└── seeders/               # Database seeders

routes/
└── api.php                # API routes
```

### Frontend
```
resources/js/
├── components/
│   ├── atoms/             # Basic components
│   ├── molecules/         # Composite components
│   ├── pages/             # Page components
│   └── layouts/           # Layout components
├── stores/                # Pinia stores
├── router/                # Vue Router configuration
├── types/                 # TypeScript type definitions
├── utils/                 # Utility functions
└── composables/           # Vue composables

resources/css/
└── app.css               # Tailwind CSS
```

## Core Features

### 1. Authentication
- Login with email/phone/username
- OTP verification
- Token-based API authentication
- Password management

### 2. RBAC (Role-Based Access Control)
- **Applications**: Manage multiple applications
- **Menus**: Dynamic menu structure
- **Authorities**: User roles (Super Admin, Admin, User)
- **Actions**: CRUD permissions (Create, Read, Update, Delete)
- Menu-based authorization system

### 3. Multi-Tenancy
- Organization-based user segmentation
- Users can belong to multiple organizations
- Authority scoped to organizations

### 4. Real-time Notifications
- Laravel Reverb WebSocket server
- Push notifications
- In-app notifications
- Notification bell with unread count

### 5. Sample CRUD (Items)
A complete CRUD example demonstrating:
- Backend: Controller, Model, Migration, Routes
- Frontend: Index page (list), Create/Edit form, TypeScript types
- Validation, Error handling, Toast notifications

## Available Commands

### Development
```bash
composer run dev          # Start all services
npm run dev              # Vite dev server only
php artisan serve        # Laravel server only
php artisan queue:listen # Queue worker
php artisan pail         # Log viewer
```

### Build
```bash
npm run build            # Production build
npm run build:prod       # Production build with NODE_ENV=production
php artisan optimize     # Optimize Laravel
```

### Testing
```bash
php artisan test         # Run PHPUnit tests
vendor/bin/phpunit       # Run tests directly
```

### Code Quality
```bash
vendor/bin/pint          # Format code with Laravel Pint
vendor/bin/pint --test   # Check formatting
```

### Database
```bash
php artisan migrate           # Run migrations
php artisan migrate:fresh     # Fresh migration
php artisan db:seed           # Run seeders
php artisan migrate:fresh --seed  # Fresh migration with seeders
```

## API Endpoints

### Authentication
```
POST /api/auth/register          # Register new user
POST /api/auth/login             # Login
POST /api/auth/request-otp       # Request OTP
POST /api/auth/verify-otp        # Verify OTP
POST /api/logout                 # Logout (authenticated)
GET  /api/me                     # Get current user (authenticated)
```

### RBAC (Settings)
```
GET    /api/v1/settings/applications
POST   /api/v1/settings/applications
GET    /api/v1/settings/menus
GET    /api/v1/settings/authorities
GET    /api/v1/settings/organizations
```

### User Management
```
GET    /api/v1/master/user
POST   /api/v1/master/user
PUT    /api/v1/master/user/{id}
DELETE /api/v1/master/user/{id}
```

## Frontend Routes

```
/app                            # Home dashboard
/app/auth                       # Login page
/app/profile                    # User profile
/app/notification               # Notifications

# Settings (RBAC)
/app/settings/manage/applications
/app/settings/manage/menus
/app/settings/manage/authorities
/app/settings/manage/organizations
/app/settings/manage/users

# Sample CRUD
/app/items                      # List items
/app/items/create               # Create item
/app/items/update/:id           # Edit item
```

## TypeScript Types

The project includes comprehensive TypeScript types in `resources/js/types/`:

- **models.ts**: User, Organization, Authority, Menu, etc.
- **api.ts**: ApiResponse, PaginatedResponse, etc.
- **store.ts**: AuthState, ThemeState, etc.
- **components.ts**: Component prop types

## Deployment

### Build for Production
```bash
npm run build:prod
php artisan optimize
php artisan config:cache
php artisan route:cache
```

### Environment Variables
Update `.env` for production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

## Customization

### Adding New CRUD
1. Create Model and Migration: `php artisan make:model YourModel -m`
2. Create Controller: `php artisan make:controller API/YourModelController --api`
3. Add routes in `routes/api.php`
4. Create frontend pages in `resources/js/components/pages/yourmodel/`
5. Add routes in `resources/js/router/router.ts`

### Adding New Menu
1. Add entry in MenuSeeder
2. Link to Authority in AuthoritySeeder
3. Create frontend route
4. Menu will automatically appear in sidebar

## Troubleshooting

### Database Issues
```bash
php artisan migrate:fresh --seed
```

### Frontend Not Loading
```bash
npm install
npm run dev
```

### Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## License

This boilerplate is open-sourced software licensed under the MIT license.

## Support

For issues and questions, please open an issue on GitHub.
