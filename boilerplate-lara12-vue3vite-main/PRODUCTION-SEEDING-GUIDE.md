# Production Seeding Guide - Invoice App

## Overview
This guide explains how to safely migrate invoice data from development to production without losing data or creating duplicates.

---

## Strategy 1: Export Existing Data (Recommended for Migration)

### Step 1: Export Dev Database to Seeder

In your development environment, run:

```bash
php artisan invoices:export-to-seeder --file=database/seeders/ProductionInvoiceSeeder.php
```

This command:
- Queries all invoices with their items
- Generates `ProductionInvoiceSeeder.php` with proper PHP code
- Each invoice is checked before insertion (prevents duplicate invoice numbers)
- Outputs timestamps and status for all invoices

**Output Example:**
```
✅ Exported 5 invoices to: database/seeders/ProductionInvoiceSeeder.php
```

### Step 2: Review Generated Seeder

Open `database/seeders/ProductionInvoiceSeeder.php` and verify:
- All invoice numbers are correct (no duplicates)
- Customer names and amounts match your records
- Status values are valid (draft, pending, paid, cancelled)

### Step 3: Deploy to Production

1. **Backup production database:**
   ```bash
   # SSH into production server
   mysqldump -u[user] -p[password] [database] > backup-$(date +%Y%m%d-%H%M%S).sql
   ```

2. **Push code with new seeder:**
   ```bash
   git add database/seeders/ProductionInvoiceSeeder.php
   git commit -m "Add production invoice data seeder"
   git push origin main
   ```

3. **Pull and run seeder in production:**
   ```bash
   git pull origin main
   php artisan db:seed --class=ProductionInvoiceSeeder
   ```

### Step 4: Verify Data
```bash
# Check invoice count
php artisan tinker
> Invoice::count()

# Check specific invoice
> Invoice::where('invoice_number', 'SI.2026.02.00001')->first()
```

---

## Strategy 2: Manual Seeding (For Fresh Production Setup)

If production database is empty:

### Option A: Use InvoiceSeeder
```bash
php artisan db:seed --class=InvoiceSeeder
```

This creates one sample invoice. Safe for fresh databases.

### Option B: Use All Seeders
```bash
php artisan db:seed
```

This runs DatabaseSeeder which includes InvoiceSeeder plus other seeders.

---

## Strategy 3: Continuous Data Sync

For ongoing development-to-production sync:

### Setup
1. Create new seeder file each week: `ProductionInvoiceSeeder_2026_02_15.php`
2. Export only new/updated invoices:
   ```bash
   php artisan invoices:export-to-seeder \
     --file=database/seeders/ProductionInvoiceSeeder_2026_02_15.php
   ```

3. Add each seeder to `DatabaseSeeder`:
   ```php
   public function run() {
       $this->call([
           ProductionInvoiceSeeder::class,
           ProductionInvoiceSeeder_2026_02_15::class,
       ]);
   }
   ```

---

## Safety Features Built In

### 1. Duplicate Prevention
```php
$existingInv = Invoice::where('invoice_number', '{$number}')->first();
if ($existingInv) {
    // Skip - already exists
}
```

Generated seeders check if invoice number exists before inserting.

### 2. Soft Delete Handling
Invoice model uses soft deletes. The system:
- Includes soft-deleted records in uniqueness checks
- Prevents reusing deleted invoice numbers

### 3. Race Condition Protection
Auto-generated invoice numbers use `withTrashed()` to check uniqueness:
```php
Invoice::withTrashed()->where('invoice_number', $number)->exists()
```

---

## Common Scenarios

### Scenario 1: First Time Production Setup
1. Export dev invoices: `php artisan invoices:export-to-seeder --file=...`
2. Review and push to production
3. Run seeder: `php artisan db:seed --class=ProductionInvoiceSeeder`

### Scenario 2: Adding New Invoices in Production
1. **Don't** export again - invoice numbers might conflict
2. Create invoices directly in production via app
3. Auto-generation ensures unique numbers

### Scenario 3: Syncing Weekly Updates
1. Export new invoices to new seeder file
2. Only include invoices created after last sync
3. Run new seeder in production

### Scenario 4: Disaster Recovery
```bash
# If corrupted data in production:
# 1. Stop app server
# 2. Restore from backup
mysql -u[user] -p[password] [database] < backup-20260209.sql
# 3. Clear and reseed if needed
php artisan migrate:fresh --seed
```

---

## Important: Preventing Invoice Number Conflicts

⚠️ **CRITICAL**: Invoice numbers must be unique across all environments.

### DO:
✅ Export ONCE: dev → production  
✅ Create new invoices in production via app  
✅ Use timestamps for backup files  
✅ Test seeder in staging first  

### DON'T:
❌ Create same invoice in dev and prod (will conflict)  
❌ Manually edit invoice numbers  
❌ Export multiple times without renumbering  
❌ Use same seeder file twice  

---

## Commands Reference

```bash
# Export invoices to seeder code
php artisan invoices:export-to-seeder --file=path/to/file.php

# Run specific seeder
php artisan db:seed --class=ProductionInvoiceSeeder

# Run all seeders
php artisan db:seed

# Check database invoices
php artisan tinker
> Invoice::pluck('invoice_number')

# Backup database
mysqldump -u user -p database > backup.sql

# Restore database
mysql -u user -p database < backup.sql
```

---

## Monitoring & Verification

After seeding production, always verify:

```bash
# Count invoices
php artisan tinker
> Invoice::count()  // Should match expected count

# Check invoice numbers are sequential
> Invoice::orderBy('invoice_number')->pluck('invoice_number')

# Verify calculations
> $inv = Invoice::first()
> $inv->total  // Should equal subtotal + ppn - discounts

# Check no orphaned items
> InvoiceItem::whereNull('invoice_id')->count()  // Should be 0
```

---

## Support

If you encounter issues:

**Duplicate Invoice Number Error:**
```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry
```
→ Invoice already exists. Run the fix command:
```bash
php artisan invoices:fix-duplicates
```

**Auto-Generation Skipping Numbers:**
→ Soft-deleted records conflict. Check:
```bash
php artisan tinker
> Invoice::onlyTrashed()->pluck('invoice_number')
```

**Seeder Not Running:**
→ Check DatabaseSeeder.php includes your seeder class in `$this->call([])`

---

## Best Practices Checklist

- [ ] Always backup production before seeding
- [ ] Test seeder in staging environment first
- [ ] Review generated seeder code before deploying
- [ ] Use meaningful file names with timestamps
- [ ] Document which seeder files have been run
- [ ] Keep old seeder files for audit trail
- [ ] Monitor invoice number generation after seeding
- [ ] Verify totals and calculations in production

---

**Last Updated:** 2026-02-09  
**Status:** Production Ready  
**Tested:** ✅ Yes
