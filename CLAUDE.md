# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel 11 + Vue 3 + TypeScript Boilerplate** - a production-ready starter project with authentication, RBAC, multi-tenancy, and real-time notifications. It includes a complete sample CRUD to demonstrate the development patterns.

## Working with Claude Code

**CRITICAL RULES**:
- **NEVER run any tests** - No `php artisan test`, no `vendor/bin/phpunit`, no testing commands whatsoever
- **NEVER run build commands** - No `npm run build`, no `npm run build:prod`
- The development server runs via `composer run dev` in the background with hot-reload
- Only make code changes - do not execute tests or builds unless the user explicitly asks

## Key Commands

### Development
```bash
# Start all services concurrently (server, queue, logs, vite)
composer run dev

# Alternative: Start services separately
php artisan serve              # Laravel development server
php artisan queue:listen       # Queue worker
php artisan pail               # Real-time log viewer
npm run dev                    # Vite dev server for Vue frontend
```

### Build & Production
```bash
# Frontend build
npm run build                  # Standard build
npm run build:prod            # Production build with NODE_ENV=production

# Backend optimization
php artisan optimize          # Cache config, routes, views
php artisan config:cache      # Cache configuration
php artisan route:cache       # Cache routes
```

### Testing
```bash
# Run PHPUnit tests
php artisan test
vendor/bin/phpunit

# Run specific test
php artisan test --filter TestName
```

### Code Quality
```bash
# Laravel Pint (code formatter)
vendor/bin/pint              # Format code
vendor/bin/pint --test       # Check formatting without changes

# Clear various caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database
```bash
php artisan migrate           # Run migrations
php artisan migrate:rollback  # Rollback migrations
php artisan migrate:fresh     # Drop all tables and re-run migrations
php artisan db:seed          # Run database seeders
```

## Architecture

### Backend Structure (Laravel)
- **API-First Architecture**: All endpoints in `routes/api.php`, grouped under `/api` prefix
- **Controllers**: Located in `app/Http/Controllers/API/`, organized by domain:
  - `Auth/`: Authentication (login, register, OAuth, OTP)
  - `Master/`: Core entities (Users, UserIdentifiers)
  - `Settings/`: RBAC configuration (Applications, Menus, Authorities, Actions, Organizations)
  - `Notification/`: Notification management
- **Models**: Eloquent models in `app/Models/` with relationships
- **Middleware**: Custom middleware in `app/Http/Middleware/`:
  - `LogApiRequests`: API request logging
  - `UpdateLastActiveAt`: Track user activity
  - `CustomSanctumMiddleware`: Enhanced Sanctum auth
- **Jobs**: Background tasks in `app/Jobs/` (notification system ready)
- **Events & Broadcasting**: Real-time features using Laravel Reverb

### Frontend Structure (Vue 3 + TypeScript)
- **SPA Architecture**: Single Page Application with Vue Router
- **TypeScript**: Full TypeScript integration with type definitions in `resources/js/types/`
- **Component Organization** (Atomic Design):
  - `resources/js/components/atoms/`: Basic UI components (buttons, inputs, badges, loaders)
  - `resources/js/components/molecules/`: Composite components (data tables, form builders)
  - `resources/js/components/pages/`: Feature pages (auth, settings, user management)
  - `resources/js/components/layouts/`: Layout components (BaseLayout, Header, Sidebar, Breadcrumb)
  - `resources/js/components/accounts/`: User account management (profile, notifications)
  - `resources/js/components/shared/`: Shared dialogs and utilities
- **State Management**: Pinia stores with persistence plugin:
  - `auth.ts`: Authentication state, user data, menu building
  - `toastStore.ts`: Toast notifications
  - `notificationStore.ts`: Real-time notifications
  - `themeStore.ts`: Dark mode management
  - `loadingStore.ts`: Global loading states
- **Routing**: Type-safe Vue Router in `resources/js/router/router.ts`
- **API Integration**: Two separate Axios instances with different interceptors:
  - `dashboardAxios`: For authenticated admin/dashboard requests (auto-injects Bearer token)
  - `publicAxios`: For public/unauthenticated requests (no auth headers)

### Core Features
1. **Authentication System**:
   - Laravel Sanctum token-based API authentication
   - OAuth2 support (Google OAuth ready)
   - OTP verification system
   - Multi-device session management
   - User identifier system (email, phone, username) with verification

2. **RBAC (Role-Based Access Control)**:
   - **Applications**: Multiple applications per system
   - **Menus**: Hierarchical menu structure with parent-child relationships
   - **Authorities**: Roles/permissions assigned to users
   - **Actions**: Granular permissions (create, read, update, delete, export, etc.)
   - **MenuAuthority**: Links menus to authorities with specific actions
   - Dynamic sidebar generation based on user authorities

3. **Multi-Tenancy**:
   - **Organizations**: Organizational structure for user segmentation
   - Users can belong to multiple organizations
   - Authority-based organization access control

4. **User Management**:
   - Full CRUD for users with authorities and organizations
   - User identifiers (multiple emails, phones, usernames per user)
   - Profile management with photo upload
   - Password and phone number management
   - Device/token revocation

5. **Notification System**:
   - Real-time notifications using Laravel Reverb + Pusher
   - In-app notification center
   - Read/unread status tracking
   - Broadcasting infrastructure ready for custom events

6. **UI/UX**:
   - Tailwind CSS + Flowbite components
   - Dark mode support
   - Responsive design
   - Toast notifications
   - Loading states
   - Dynamic breadcrumbs

### Database Patterns
- MySQL database with Laravel migrations
- Soft deletes on models where applicable
- UUID primary keys for certain models (User, Authority, Application, Menu, Organization, Action)
- Integer primary keys for pivot/junction tables
- Timestamp tracking (created_at, updated_at) on all models
- Foreign key constraints with cascade deletes where appropriate

### Environment Configuration
- `.env` file for local development (copy from `.env.example`)
- Separate `.env.production` for production deployments
- Docker support with `docker-compose.yml` and `Dockerfile`

## Development Guide

### Understanding the RBAC System

The boilerplate uses a flexible RBAC system with these relationships:

```
Application (e.g., "Admin Panel", "User Portal")
    └── Menu (e.g., "Users", "Settings")
        └── MenuAuthority (pivot: links Menu + Authority + Actions)
            ├── Authority (Role like "Admin", "User")
            └── ActionUse (specific permissions)
                └── Action (e.g., "create", "read", "update", "delete")
```

**How it works**:
1. Create an **Application** (e.g., "Admin Panel")
2. Create **Menus** under that application (can be hierarchical with `menu_id` parent)
3. Create **Authorities** (roles like "Super Admin", "Manager", "Staff")
4. Define **Actions** (create, read, update, delete, export, etc.)
5. Link them via **MenuAuthority** with specific **ActionUse** permissions
6. Assign authorities to users via **AuthorityUser** with organization scope

**Dynamic Sidebar**: The sidebar in `BaseLayout.vue` automatically builds from the user's assigned authorities and menus (see `auth.ts` store's `getMenuInduk` getter).

### Adding a New CRUD Entity

The boilerplate provides a clean foundation. To add a new entity (e.g., "Products"):

**Backend (Laravel)**:
1. Create migration: `php artisan make:migration create_products_table`
2. Create model: `php artisan make:model Product`
3. Create controller: `php artisan make:controller API/ProductController`
4. Add routes in `routes/api.php` under the `v1` prefix
5. Add validation rules (optional: create Request class)

**Frontend (Vue + TypeScript)**:
1. Add TypeScript interface in `resources/js/types/models.ts`
2. Create page components in `resources/js/components/pages/products/`:
   - `Index.vue` (list view with dashboardAxios)
   - `Create.vue` (create/edit form with dashboardAxios)
3. Add routes in `resources/js/router/router.ts`
4. Use existing atoms/molecules for forms and tables
5. Import `dashboardAxios` for API calls (not global axios)

**Adding to RBAC**:
1. Create menu entry via Settings > Menus (link to application)
2. Assign menu to authorities with appropriate actions
3. Users with that authority will see it in their sidebar

### TypeScript Patterns

**Component with TypeScript**:
```vue
<script setup lang="ts">
import { ref, onMounted } from 'vue';
import dashboardAxios from '@/api/dashboardAxios'; // For authenticated requests
import { Product } from '@/types/models';

const products = ref<Product[]>([]);
const loading = ref(false);

const fetchProducts = async () => {
    loading.value = true;
    try {
        const response = await dashboardAxios.get('/v1/products');
        products.value = response.data;
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchProducts();
});
</script>
```

**Adding Type Definitions**:
Add new interfaces to `resources/js/types/models.ts`:
```typescript
export interface Product {
  id: number;
  name: string;
  description: string | null;
  price: number;
  created_at: string;
  updated_at: string;
}
```

### Axios API Integration

The project uses **two separate Axios instances** with different configurations and interceptors:

#### 1. **dashboardAxios** - For Authenticated Requests
Located in `resources/js/api/dashboardAxios.ts`

**Features**:
- Automatically injects `Authorization: Bearer {token}` header from auth store
- Adds `X-Member: {user_id}` header for user tracking
- Includes CSRF token (`withXSRFToken: true`)
- Comprehensive error handling with automatic toast notifications
- Auto-redirects to login on 401 Unauthorized
- Unwraps response data automatically (returns `response.data` directly)

**Configuration**:
```typescript
const dashboardAxios = axios.create({
    baseURL: import.meta.env.VITE_APP_URL + '/api/',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});
```

**Request Interceptor**:
- Adds Bearer token from `useAuthStore().user.token`
- Adds `X-Member` header with user ID
- Sets `Accept: application/json`

**Response Interceptor**:
- Success: Returns `response.data` (unwrapped)
- Error handling:
  - `401`: Shows toast, redirects to login
  - `400`: Bad Request toast
  - `403`: Forbidden toast
  - `405`: Method Not Allowed toast
  - `422`: Validation Error toast
  - `500`: Server Error toast
  - `419`: CSRF token mismatch, auto-reloads page

**Usage Example**:
```vue
<script setup lang="ts">
import dashboardAxios from '@/api/dashboardAxios';

// GET request
const fetchUsers = async () => {
    const data = await dashboardAxios.get('/v1/users');
    // data is already unwrapped (response.data)
    return data;
};

// POST request
const createUser = async (userData: any) => {
    const data = await dashboardAxios.post('/v1/users', userData);
    return data;
};

// PUT request
const updateUser = async (id: number, userData: any) => {
    const data = await dashboardAxios.put(`/v1/users/${id}`, userData);
    return data;
};

// DELETE request
const deleteUser = async (id: number) => {
    const data = await dashboardAxios.delete(`/v1/users/${id}`);
    return data;
};
</script>
```

#### 2. **publicAxios** - For Public/Unauthenticated Requests
Located in `resources/js/api/publicAxios.ts`

**Features**:
- No authentication headers
- No credentials (`withCredentials: false`)
- Minimal error handling (logs to console)
- Unwraps response data automatically
- Used for public pages, contact forms, etc.

**Configuration**:
```typescript
const publicAxios = axios.create({
    baseURL: import.meta.env.VITE_APP_URL + '/api/',
    withCredentials: false,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});
```

**Response Interceptor**:
- Success: Returns `response.data` (unwrapped)
- Error handling:
  - `422`: Logs validation error to console
  - `500`: Logs server error to console
  - No toast notifications or redirects

**Usage Example**:
```vue
<script setup lang="ts">
import publicAxios from '@/api/publicAxios';

// GET public data
const fetchPublicData = async () => {
    const data = await publicAxios.get('/v1/public/content');
    return data;
};

// POST contact form (no auth required)
const submitContactForm = async (formData: any) => {
    const data = await publicAxios.post('/v1/contact', formData);
    return data;
};
</script>
```

#### When to Use Each Axios Instance

**Use `dashboardAxios` for**:
- All authenticated/admin panel requests
- User management, settings, RBAC operations
- Any API that requires authentication
- Dashboard pages, protected routes
- Operations that need user tracking (`X-Member` header)

**Use `publicAxios` for**:
- Public website pages
- Contact forms
- Marketing content
- Any endpoint that doesn't require authentication
- Landing pages, public resources

#### Error Handling Best Practices

**With dashboardAxios** (errors auto-handled by interceptor):
```vue
<script setup lang="ts">
import dashboardAxios from '@/api/dashboardAxios';
import { useToastStore } from '@/stores/toastStore';

const toastStore = useToastStore();

const saveData = async () => {
    try {
        const data = await dashboardAxios.post('/v1/items', formData);
        toastStore.addToast('Item created successfully!', 'success');
    } catch (error) {
        // Error toast already shown by interceptor
        // Only handle specific business logic errors here if needed
        console.error('Create failed:', error);
    }
};
</script>
```

**With publicAxios** (manual error handling):
```vue
<script setup lang="ts">
import publicAxios from '@/api/publicAxios';

const submitForm = async () => {
    try {
        const data = await publicAxios.post('/v1/contact', formData);
        console.log('Form submitted successfully');
    } catch (error: any) {
        // Handle errors manually
        if (error.response?.status === 422) {
            console.error('Validation failed:', error.response.data);
        }
    }
};
</script>
```

### API Development

**Controller Pattern** (see `UserController.php` as reference):
```php
public function index(Request $request)
{
    try {
        $items = Model::query()
            ->when($request->has('search'), function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'data' => $items,
            'message' => 'Items fetched successfully'
        ], Response::HTTP_OK);
    } catch (Exception $e) {
        Log::error('Error: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
```

**Route Pattern**:
```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('v1')->group(function () {
        Route::apiResource('products', ProductController::class);
    });
});
```

### Vue Component Development

**Best Practices**:
- Use Composition API with `<script setup lang="ts">` syntax
- Leverage existing atoms and molecules before creating new components
- Use Pinia stores for shared state (auth, notifications, theme, etc.)
- Follow atomic design pattern (atoms → molecules → pages)
- Use TypeScript interfaces for props and emits

**Component Organization**:
- **Atoms**: `PrimaryButton.vue`, `InputField.vue`, `Badge.vue`
- **Molecules**: `DataTable.vue`, `FormBuilder.vue`, `Pagination.vue`
- **Pages**: `Index.vue`, `Create.vue`, `Show.vue`

### Working with Notifications

**Backend Broadcasting**:
```php
// In controller or job
use Illuminate\Support\Facades\Notification;

Notification::create([
    'user_id' => $user->id,
    'title' => 'Welcome!',
    'message' => 'Your account has been created',
    'type' => 'success',
]);

// Broadcast via Reverb
broadcast(new NotificationEvent($notification))->toOthers();
```

**Frontend Listening** (already set up in `app.ts`):
```typescript
Echo.private(`user.${user.id}`)
    .notification((notification: Notification) => {
        notificationStore.addNotification(notification);
        toastStore.addToast(notification.message, notification.type);
    });
```

### Real-Time Features

- **Laravel Reverb** WebSocket server (runs via `composer run dev`)
- **Laravel Echo** client configured in `resources/js/bootstrap.ts`
- **Pusher** as broadcasting driver
- Channels are automatically set up for authenticated users
- Add new event listeners in `app.ts` or component lifecycle hooks