# Invoice Module - Complete Guide

## 📋 Overview

Modul Invoice yang telah dikembangkan dengan struktur business process yang lengkap, mencakup:

### **Menu Structure**
```
Invoices (Parent Menu - Dropdown)
├── Quotations       - List penawaran harga (DRAFT, QUOTED)
├── Invoices         - List faktur (INVOICED, PARTIAL_PAID, PAID)
├── Customers        - Master data pelanggan
├── Payments         - Recording pembayaran dari customer
├── Items            - Master data produk/jasa (sudah ada sebelumnya)
└── Item Categories  - Kategori produk (sudah ada sebelumnya)
```

---

## 🆕 **New Features Implemented**

### **1. Customer Management**
Master data pelanggan dengan informasi lengkap:

#### **Features:**
- ✅ CRUD operations (Create, Read, Update, Delete)
- ✅ Customer code & name
- ✅ Contact information (email, phone)
- ✅ Contact person details
- ✅ Tax ID (NPWP)
- ✅ Payment terms (Cash, Net 7/14/30/45/60 days)
- ✅ Credit limit management
- ✅ Active/Inactive status
- ✅ Search & filter functionality

#### **API Endpoints:**
```
GET    /api/customers              - List customers
POST   /api/customers              - Create customer
GET    /api/customers/{id}         - View customer detail
PUT    /api/customers/{id}         - Update customer
DELETE /api/customers/{id}         - Delete customer (only if no invoices)
```

#### **Frontend Routes:**
```
/app/invoices/customers             - Customer list
/app/invoices/customers/create      - Create new customer
/app/invoices/customers/{id}/edit   - Edit customer
```

---

### **2. Payment Management**
System tracking pembayaran dari customer dengan multiple payment support:

#### **Features:**
- ✅ Multiple payments per invoice
- ✅ Auto-calculate remaining balance
- ✅ Auto-update invoice status (PARTIAL_PAID → PAID)
- ✅ Payment methods (Cash, Bank Transfer, Check, Giro, Credit Card, Other)
- ✅ Reference number tracking
- ✅ Payment receipt file support
- ✅ Auto-generate payment number (PMT-YYYYMMDD-XXXX)

#### **API Endpoints:**
```
GET    /api/payments               - List payments
GET    /api/payments/next-number   - Get next payment number
POST   /api/payments               - Record payment
GET    /api/payments/{id}          - View payment detail
PUT    /api/payments/{id}          - Update payment
DELETE /api/payments/{id}          - Delete payment (recalculates invoice)
```

#### **Frontend Routes:**
```
/app/invoices/payments              - Payment list
/app/invoices/payments/create       - Record new payment
/app/invoices/payments/{id}/edit    - Edit payment
```

#### **Payment Flow:**
1. Create/Select invoice with status INVOICED or PARTIAL_PAID
2. Record payment with amount ≤ remaining balance
3. System auto-updates:
   - Invoice `paid_amount`
   - Invoice `status` (PARTIAL_PAID or PAID)
   - Invoice `paid_at` and `paid_by` (if fully paid)

---

### **3. Quotations View**
Dedicated view untuk mengelola quotations (penawaran harga):

#### **Features:**
- ✅ List invoices dengan status DRAFT dan QUOTED
- ✅ Mark as Quoted (DRAFT → QUOTED)
- ✅ Convert to Invoice (QUOTED → INVOICED)
- ✅ PDF export
- ✅ Edit quotation before converting
- ✅ Project name tracking
- ✅ Search & filter by status

#### **Frontend Route:**
```
/app/invoices/quotations            - Quotations list (filtered view)
```

#### **Quotation Flow:**
```
DRAFT → [Mark as Quoted] → QUOTED → [Convert to Invoice] → INVOICED
```

---

### **4. Updated Invoice Model**

#### **New Fields Added:**
- `customer_id` (FK to customers table) - Link to customer master
- `quotation_number` - Quotation reference number
- `quotation_date` - Date when quoted
- `created_by`, `quoted_by`, `invoiced_by`, `paid_by` - Audit trail
- `quoted_at`, `invoiced_at`, `paid_at` - Status timestamps
- `dp_amount` - Down payment amount
- `paid_amount` - Total amount paid (sum of payments)
- `project_name` - Project reference
- `delivery_phase` - Delivery phase (Phase 1, Phase 2, etc)
- `internal_notes` - Internal notes (not shown on PDF)

#### **New Relationships:**
```php
Invoice::customer()     // BelongsTo Customer
Invoice::payments()     // HasMany Payment
```

#### **New Computed Properties:**
```php
$invoice->remaining_balance  // total - paid_amount
$invoice->is_overdue        // Check if payment is overdue
$invoice->due_date          // Calculate due date based on payment terms
```

---

### **5. Invoice Status Flow**

```
DRAFT → QUOTED → INVOICED → PARTIAL_PAID → PAID
                          ↘               ↗
                            VOID (cancelled)
```

#### **Status Transitions:**
- **DRAFT → QUOTED**: Mark as quoted (ready to send to customer)
- **QUOTED → INVOICED**: Convert quotation to invoice
- **DRAFT → INVOICED**: Direct invoicing (skip quotation)
- **INVOICED → PARTIAL_PAID**: When payment < total
- **PARTIAL_PAID → PAID**: When sum(payments) >= total
- **Any → VOID**: Cancelled/voided invoice

---

## 🛠️ **Installation & Setup**

### **1. Run Migrations**
```bash
php artisan migrate
```

This will create:
- `customers` table
- `payments` table
- Add `customer_id` to `invoices` table
- Add new columns to `invoices` table

### **2. Seed Demo Data**
```bash
php artisan db:seed --class=CustomerSeeder
```

This creates 5 demo customers:
- PT. Maju Jaya (CUST001)
- CV. Berkah Sejahtera (CUST002)
- PT. Teknologi Indonesia (CUST003)
- UD. Sumber Rezeki (CUST004)
- PT. Global Solutions (CUST005)

### **3. Build Frontend**
```bash
npm run build
# or for development
npm run dev
```

---

## 📊 **Database Schema**

### **customers**
```sql
- id (PK)
- code (unique) - Customer code
- name - Customer name
- address - Full address
- phone - Company phone
- email - Company email
- contact_person - Contact person name
- contact_phone - Contact person phone
- tax_id (NPWP)
- payment_terms (enum)
- credit_limit (decimal)
- notes
- is_active (boolean)
- timestamps
```

### **payments**
```sql
- id (PK)
- invoice_id (FK)
- payment_number (unique)
- payment_date
- amount (decimal)
- payment_method (enum)
- reference_number
- notes
- receipt_file
- created_by (FK users)
- timestamps
```

---

## 🎯 **Usage Examples**

### **Creating an Invoice with Customer**

**Step 1: Create/Select Customer**
```
1. Go to Invoices > Customers
2. Click "New Customer"
3. Fill customer details
4. Save
```

**Step 2: Create Quotation**
```
1. Go to Invoices > Quotations
2. Click "New Quotation"
3. Select customer (auto-fills customer info & payment terms)
4. Add items
5. Save as DRAFT
```

**Step 3: Mark as Quoted**
```
1. Click "Mark as Quoted" button
2. Status changes: DRAFT → QUOTED
3. Send quotation PDF to customer
```

**Step 4: Convert to Invoice**
```
1. After customer approval
2. Click "Convert to Invoice" button
3. Status changes: QUOTED → INVOICED
4. Ready for payment
```

**Step 5: Record Payment**
```
1. Go to Invoices > Payments
2. Click "Record Payment"
3. Select invoice
4. Enter payment amount & method
5. Save
6. Invoice status auto-updates:
   - If partial: INVOICED → PARTIAL_PAID
   - If full: PARTIAL_PAID → PAID
```

---

## 🔐 **Permission & RBAC**

### **Menu Items to Add in RBAC:**

**Parent Menu: Invoices**
- URL: `/app/invoices`
- Icon: 💰

**Child Menus:**
1. **Quotations** - `/app/invoices/quotations`
2. **Invoices** - `/app/invoices/invoices`
3. **Customers** - `/app/invoices/customers`
4. **Payments** - `/app/invoices/payments`
5. **Items** - `/app/invoices/items` (existing)
6. **Item Categories** - `/app/invoices/item-categories` (existing)

### **Actions per Menu:**
- **Create** - Add new record
- **Read** - View list & details
- **Update** - Edit existing record
- **Delete** - Remove record
- **Export** - Export PDF (for Invoices/Quotations)

---

## 📈 **Next Steps / Roadmap**

### **Phase 2 Features (Not Implemented Yet):**

1. **Reports Module**
   - Aging Report (Invoice aging analysis)
   - Sales Report (Sales by period/customer)
   - Outstanding Invoices Report
   - Payment History Report

2. **Advanced Features**
   - Recurring invoices
   - Invoice templates customization
   - Email invoices directly to customers
   - Payment reminders automation
   - Multi-currency support (enhance existing currency field)

3. **Dashboard Widgets**
   - Total outstanding amount
   - Overdue invoices count
   - Revenue by month chart
   - Top customers by revenue

---

## ⚠️ **Important Notes**

### **Customer Deletion:**
- Cannot delete customer if they have invoices
- Set `is_active = false` instead to deactivate

### **Payment Deletion:**
- Deleting payment will recalculate invoice status
- Use with caution - create audit log for compliance

### **Invoice Status:**
- Once PAID, invoice cannot be edited
- VOID invoices are locked
- Use internal_notes for non-customer-facing notes

### **Payment Terms:**
- Automatically calculate due date
- Used for overdue checking
- Inherited from customer when creating invoice

---

## 🐛 **Troubleshooting**

### **Migration Issues**
```bash
# If column already exists error
php artisan migrate:fresh --seed
# WARNING: This drops all tables!
```

### **Customer Not Showing in Invoice Form**
- Check if customer `is_active = true`
- Ensure customers are seeded/created
- Check API endpoint `/api/customers`

### **Payment Validation Error**
- Check remaining balance >= payment amount
- Ensure invoice status is INVOICED or PARTIAL_PAID
- Verify invoice is not VOID or PAID

---

## 📞 **Support**

For questions or issues:
1. Check this documentation
2. Review API responses in browser console
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify database migrations are complete

---

**Last Updated:** February 7, 2026
**Version:** 1.0.0
