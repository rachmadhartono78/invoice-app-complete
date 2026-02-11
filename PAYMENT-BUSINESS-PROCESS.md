# Payment Recording - Business Process Documentation

## 📋 Overview

Proses bisnis Payment Recording adalah workflow untuk mencatat pembayaran dari customer terhadap invoice yang telah diterbitkan. Sistem dirancang untuk:
- ✅ Handle partial & full payments
- ✅ Auto-update invoice status
- ✅ Track payment history
- ✅ Maintain financial control

---

## 🔄 Payment Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│ INVOICE LIFECYCLE WITH PAYMENT                                  │
└─────────────────────────────────────────────────────────────────┘

STEP 1: CREATE QUOTATION
    ┌──────────────────┐
    │  Create Proposal │ (DRAFT)
    └────────┬─────────┘
             │
    ┌────────▼──────────┐
    │ Send to Customer  │ → QUOTED
    └────────┬──────────┘
             │
STEP 2: CONVERT TO INVOICE
    ┌────────▼───────────────┐
    │ Customer Approved ✓    │
    │ Convert → Invoice      │ → INVOICED
    │ Ready for Payment      │
    └────────┬───────────────┘
             │
STEP 3: RECORD PAYMENT (PAYMENT MODULE)
    ┌────────▼──────────────────────────────────────┐
    │ Customer Sends Payment                         │
    └────────┬──────────────────────────────────────┘
             │
    ┌────────▼────────────────────────────────────────┐
    │ Accounting Records Payment                       │
    │ - Select Invoice                                │
    │ - Enter Amount & Method                         │
    │ - Enter Payment Reference                       │
    │ - Save Payment Record                           │
    └────────┬─────────────────────────────────────────┘
             │
    ┌────────▼────────────────────────────────────────┐
    │ System Validates:                               │
    │ ✓ Invoice exists & unpaid                       │
    │ ✓ Amount ≤ remaining balance                    │
    │ ✓ Payment details complete                      │
    └────────┬─────────────────────────────────────────┘
             │
    ┌────────▼────────────────────────────────────────┐
    │ Auto-Calculate Invoice Status:                   │
    │                                                 │
    │ IF (paid_amount = total)                        │
    │    → Status = PAID                              │
    │    → Set paid_at, paid_by                       │
    │                                                 │
    │ ELSE IF (paid_amount > 0)                       │
    │    → Status = PARTIAL_PAID                      │
    │    → remaining_balance = total - paid_amount    │
    │                                                 │
    │ ELSE                                            │
    │    → Status = INVOICED                          │
    └────────┬─────────────────────────────────────────┘
             │
             ▼
    ┌──────────────────────────────────────────┐
    │  Payment Recorded ✅                     │
    │  - Payment #: PMT-20260207-0001          │
    │  - Status: Recorded                      │
    │  - Invoice Status Updated                │
    └──────────────────────────────────────────┘
```

---

## 📊 Invoice Status Transition with Payment

```
┌────────────────────────────────────────────────────────────┐
│  STATUS CHANGE BASED ON PAYMENT                            │
└────────────────────────────────────────────────────────────┘

   INVOICED State
   (Total: Rp 10.000.000)
         │
         ├─────────────────────────┬──────────────────┐
         │                         │                  │
    Payment Rp 3.000.000      Payment Rp 10.000.000   No Payment
         │                         │                  │
         ▼                         ▼                  ▼
   PARTIAL_PAID                PAID 🎉            INVOICED
   (Remaining:              (remaining: 0)      (remaining: 10.000.000)
    Rp 7.000.000)

Subsequent Payment:
   PARTIAL_PAID + Rp 7.000.000
         │
         ▼
      PAID 🎉
    (fully paid)
```

---

## 📝 Detailed Payment Recording Process

### **Kondisi: Ada Unpaid Invoices**

#### **User Flow:**
```
1. Navigate to: Invoices > Payments
2. Click: "Record Payment" button
3. Page Load:
   - System fetches unpaid invoices
   - Display loading indicator
   - Once loaded → Show form

4. Select Invoice:
   - Choose invoice from dropdown
   - Display Invoice Details:
     * Total Amount
     * Already Paid Amount
     * Remaining Balance ← maximum payment
   
5. Enter Payment Details:
   - Payment Date (required)
   - Amount (max = remaining balance)
   - Payment Method (required):
     * Cash
     * Bank Transfer
     * Check
     * Giro
     * Credit Card
     * Other
   - Reference Number (optional)
     * Bank transfer: Transaction ID
     * Check: Check number
     * Giro: Giro number
   - Notes (optional)

6. Submit:
   - System validates
   - Auto-generates payment number: PMT-YYYYMMDD-XXXX
   - Create payment record
   - Update invoice status
   - Show success message

7. Redirect to Payment List
```

#### **Backend Validation:**
```php
POST /api/payments

Request Body:
{
    "invoice_id": 1,           // [REQUIRED] Must exist & be unpaid
    "payment_date": "2026-02-07",  // [REQUIRED]
    "amount": 5000000,         // [REQUIRED] Must be > 0 and ≤ remaining
    "payment_method": "bank_transfer", // [REQUIRED]
    "reference_number": "BCA-12345",   // [OPTIONAL]
    "notes": "Via mobile banking"      // [OPTIONAL]
}

Validations:
✓ invoice_id exists in invoices table
✓ invoice status ≠ PAID or VOID
✓ amount > 0 and ≤ remaining_balance
✓ payment_date is valid date
✓ payment_method in enum list

Auto Actions:
✓ Generate payment_number (PMT-YYYYMMDD-XXXX)
✓ Set created_by = current user ID
✓ Create Payment record

ON SUCCESS → Fire PaymentReceived Event:
  ├─ Calculate total paid: sum(payments.amount)
  ├─ Update invoice.paid_amount
  ├─ Determine new status:
  │   ├─ IF paid_amount ≥ total → PAID
  │   ├─ ELSE IF paid_amount > 0 → PARTIAL_PAID
  │   └─ ELSE → INVOICED
  └─ Save to database
```

---

### **Kondisi: Tidak Ada Unpaid Invoices**

#### **User Experience:**

```
User clicks "Record Payment"
         │
         ▼
System loads page
         │
         ▼
Fetch /api/invoices/unpaid
         │
         ├─ Loading... (show spinner)
         │
         ▼
Result: Empty Array []
         │
         ▼
Display WARNING MESSAGE:
┌─────────────────────────────────────────┐
│ 📝 Tidak Ada Invoice untuk Dibayar      │
│                                         │
│ Semua invoice sudah terbayar lunas     │
│ atau tidak ada invoice yang dalam      │
│ status pending pembayaran.             │
│ Silahkan periksa kembali status       │
│ invoice Anda.                          │
│                                         │
│ [Lihat Daftar Invoice] [Kembali]      │
└─────────────────────────────────────────┘

Button Actions:
- "Lihat Daftar Invoice" → Navigate to /invoices
- "Kembali" → Navigate to /payments
```

#### **When to Show Message:**
```javascript
// Component Logic
if (!isEdit && !unpaidInvoices.length && !loading) {
    // Show "No Unpaid Invoices" message
    // Hide form completely
}

if (isEdit || (unpaidInvoices.length > 0)) {
    // Show payment form
}
```

---

## 💾 Database Impact

### **Payment Table Structure**
```sql
payments
├─ id (PK)
├─ invoice_id (FK) → invoices.id
├─ payment_number (UNIQUE) → PMT-YYYYMMDD-XXXX
├─ payment_date (DATE)
├─ amount (DECIMAL)
├─ payment_method (ENUM)
├─ reference_number (VARCHAR)
├─ notes (TEXT)
├─ receipt_file (VARCHAR)
├─ created_by (FK) → users.id
├─ created_at
└─ updated_at
```

### **Invoice Fields Updated**
```javascript
// When payment is created/updated, invoice updates:

invoice.paid_amount = sum(payments.amount)
// Example: paid_amount = 5000000

invoice.status = [INVOICED | PARTIAL_PAID | PAID]
// Calculated based on paid_amount vs total

invoice.paid_at = datetime or null
// Set only when status = PAID
// Null if status becomes PARTIAL_PAID again

invoice.paid_by = user_id or null
// Who marked it as PAID
```

---

## 🔢 Invoice Status Logic

| Condition | Invoice Status | paid_at | paid_by |
|-----------|---|---|---|
| amount = 0 (no payments) | INVOICED | NULL | NULL |
| 0 < amount < total | PARTIAL_PAID | NULL | NULL |
| amount ≥ total | PAID | NOW() | user_id |

---

## 📱 API Endpoints

### **1. Get Unpaid Invoices**
```
GET /api/invoices/unpaid

Response: [
  {
    "id": 1,
    "invoice_number": "SI.2026.02.00001",
    "customer_name": "PT ABC",
    "total": 10000000,
    "paid_amount": 3000000,
    "remaining_balance": 7000000,
    "status": "PARTIAL_PAID"
  },
  ...
]
```

### **2. Record Payment (Create)**
```
POST /api/payments

Request:
{
  "invoice_id": 1,
  "payment_date": "2026-02-07",
  "amount": 7000000,
  "payment_method": "bank_transfer",
  "reference_number": "BCA-TRF-20260207",
  "notes": "Final payment"
}

Response (201):
{
  "message": "✅ Payment recorded successfully",
  "data": {
    "id": 1,
    "payment_number": "PMT-20260207-0001",
    "amount": 7000000,
    "payment_method": "bank_transfer",
    "invoice": {
      "id": 1,
      "status": "PAID",
      "paid_amount": 10000000
    }
  }
}
```

### **3. List Payments**
```
GET /api/payments

Query Parameters:
- page (int)
- per_page (int)
- search (string)
- date_from (date)
- date_to (date)

Response:
{
  "data": [
    {
      "id": 1,
      "payment_number": "PMT-20260207-0001",
      "payment_date": "2026-02-07",
      "amount": 7000000,
      "payment_method": "bank_transfer",
      "invoice": {
        "invoice_number": "SI.2026.02.00001",
        "customer_name": "PT ABC"
      },
      "creator": {
        "name": "Budi Santoso"
      }
    }
  ],
  "current_page": 1,
  "last_page": 1,
  "total": 1
}
```

### **4. Edit Payment**
```
PUT /api/payments/{id}

Updates payment & recalculates invoice status
```

### **5. Delete Payment**
```
DELETE /api/payments/{id}

Removes payment & recalculates invoice status back
```

---

## 🚨 Error Handling

### **Validation Errors (422)**
```javascript
// Case 1: Invoice tidak ditemukan
{
  "message": "Invoice not found"
}

// Case 2: Invoice sudah PAID/VOID
{
  "message": "❌ Cannot add payment to invoice with status PAID"
}

// Case 3: Amount melebihi remaining balance
{
  "message": "❌ Payment amount exceeds remaining balance. Remaining: Rp 7.000.000"
}

// Case 4: Invoice tidak ada di dropdown (unpaid list kosong)
// UI shows: "Tidak Ada Invoice untuk Dibayar"
```

### **Server Errors (500)**
```javascript
{
  "message": "❌ Failed to record payment: Database error"
}
```

---

## 📊 Real-World Scenario

### **Scenario: Partial Payment then Full Payment**

**Initial State:**
```
Invoice SI.2026.02.00001
├─ Total: Rp 10.000.000
├─ Paid: Rp 0
├─ Status: INVOICED
└─ Remaining: Rp 10.000.000
```

**Day 1: Partial Payment**
```
User Records Payment:
- Amount: Rp 3.000.000
- Method: Bank Transfer
- Reference: BCA-20260207-001

System Updates Invoice:
├─ paid_amount: Rp 3.000.000
├─ Status: PARTIAL_PAID 🟠
└─ Remaining: Rp 7.000.000

Dashboard shows: Rp 7.000.000 unpaid
```

**Day 5: Final Payment**
```
User Records Payment:
- Amount: Rp 7.000.000
- Method: Bank Transfer
- Reference: BCA-20260212-002

System Updates Invoice:
├─ paid_amount: Rp 10.000.000
├─ Status: PAID ✅
├─ paid_at: 2026-02-12 14:30:00
├─ paid_by: budi_santoso
└─ Remaining: Rp 0

Dashboard shows: FULLY PAID ✓
```

**Payment History:**
```
Invoice: SI.2026.02.00001

Payment 1: PMT-20260207-0001
├─ Amount: Rp 3.000.000
├─ Date: 2026-02-07
├─ Method: Bank Transfer
└─ Reference: BCA-20260207-001

Payment 2: PMT-20260212-0001
├─ Amount: Rp 7.000.000
├─ Date: 2026-02-12
├─ Method: Bank Transfer
└─ Reference: BCA-20260212-002

Total Payments: Rp 10.000.000 ✅
```

---

## ✅ Payment Recording Checklist

- [ ] Customer sends payment
- [ ] Accounting department receives payment
- [ ] Go to Invoices > Payments
- [ ] Click "Record Payment"
- [ ] Select unpaid invoice
- [ ] Verify amount & remaining balance
- [ ] Enter payment date
- [ ] Enter amount (can be partial)
- [ ] Select payment method
- [ ] Enter reference/transaction number
- [ ] Add notes (if needed)
- [ ] Click "Record Payment"
- [ ] Verify success message
- [ ] Check invoice status updated
- [ ] If partially paid → can record more payments
- [ ] If fully paid → invoice status = PAID

---

## 🔧 Configuration

No special configuration needed. Payment system uses:
- Auto-generated payment numbers (format: PMT-YYYYMMDD-XXXX)
- Auto-status calculation based on paid amount
- Event listener (PaymentReceived) for invoice updates
- Sanctum for authentication
- CORS for frontend access

---

## 📚 Related Documentation

- [INVOICE-MODULE-GUIDE.md](INVOICE-MODULE-GUIDE.md) - Complete invoice system
- [INVOICE_SETUP.md](INVOICE_SETUP.md) - Initial setup
- API Routes in [routes/api.php](routes/api.php)
- Models: [app/Models/Invoice.php](app/Models/Invoice.php)
- Models: [app/Models/Payment.php](app/Models/Payment.php)
- Controller: [app/Http/Controllers/API/PaymentController.php](app/Http/Controllers/API/PaymentController.php)
- Listener: [app/Listeners/UpdateInvoiceStatus.php](app/Listeners/UpdateInvoiceStatus.php)

---

## 🎯 Design Principles

1. **User-Friendly**: Show clear messages when conditions not met
2. **Validation**: Never allow invalid payments
3. **Auto-Calculation**: Invoice status updates automatically
4. **Audit Trail**: Track who recorded what payment
5. **Flexibility**: Support partial & full payments
6. **Financial Accuracy**: Double-check calculations
7. **Error Handling**: Graceful error messages

---

**Last Updated**: February 7, 2026
**Version**: 1.0
