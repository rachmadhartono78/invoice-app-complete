# Invoice System - Quick Setup

## 1. Install Dependencies
```bash
composer require barryvdh/laravel-dompdf
```

## 2. Add API Routes
Add to `routes/api.php`:
```php
use App\Http\Controllers\API\InvoiceController;
Route::middleware('auth:sanctum')->prefix('invoices')->group(function(){
    Route::get('/',[InvoiceController::class,'index']);
    Route::post('/',[InvoiceController::class,'store']);
    Route::get('/{invoice}',[InvoiceController::class,'show']);
    Route::put('/{invoice}',[InvoiceController::class,'update']);
    Route::delete('/{invoice}',[InvoiceController::class,'destroy']);
    Route::get('/{invoice}/pdf',[InvoiceController::class,'pdf']);
});
```

## 3. Add Frontend Routes
Add to your Vue router:
```js
{path:'/invoices',component:()=>import('@/components/pages/invoices/Index.vue')},
{path:'/invoices/create',component:()=>import('@/components/pages/invoices/Form.vue')},
{path:'/invoices/:id',component:()=>import('@/components/pages/invoices/View.vue')},
{path:'/invoices/:id/edit',component:()=>import('@/components/pages/invoices/Form.vue')},
```

## 4. Run Migration
```bash
php artisan migrate
```

## 5. Build & Run
```bash
npm run build
php artisan serve
```

Visit: http://localhost:8000/invoices

## Features
✅ Create/Edit/Delete invoices
✅ Multi-item with auto-calculation
✅ PDF generation (Indonesian format)
✅ Search & filter
✅ Auto invoice numbering (SI.YYYY.MM.00001)

## Invoice Number Format
Edit `app/Models/Invoice.php` line 13 to change prefix:
```php
return 'SI.' . $date->format('Y.m.') . str_pad($num, 5, '0', STR_PAD_LEFT);
// Change 'SI' to your desired prefix
```
