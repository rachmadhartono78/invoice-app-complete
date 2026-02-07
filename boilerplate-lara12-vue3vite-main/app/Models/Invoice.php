<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model {
    use SoftDeletes;
    protected $fillable = [
        'invoice_number','invoice_date','customer_id','customer_name','customer_address',
        'payment_terms','expedition','po_number','delivery_date','currency_name',
        'subtotal','discount','ppn_percent','ppn_amount','other_charges','total',
        'notes','terbilang','prepared_by','approved_by','status',
        'quotation_number','quotation_date','created_by','quoted_by','quoted_at',
        'invoiced_by','invoiced_at','paid_by','paid_at','dp_amount','paid_amount',
        'project_name','delivery_phase','internal_notes'
    ];
    
    protected $casts = [
        'invoice_date'=>'date',
        'delivery_date'=>'date',
        'quotation_date'=>'date',
        'quoted_at'=>'datetime',
        'invoiced_at'=>'datetime',
        'paid_at'=>'datetime',
        'subtotal'=>'decimal:2',
        'discount'=>'decimal:2',
        'ppn_percent'=>'decimal:2',
        'ppn_amount'=>'decimal:2',
        'other_charges'=>'decimal:2',
        'total'=>'decimal:2',
        'dp_amount'=>'decimal:2',
        'paid_amount'=>'decimal:2'
    ];
    
    public function items() { 
        return $this->hasMany(InvoiceItem::class); 
    }
    
    public function customer() { 
        return $this->belongsTo(Customer::class); 
    }
    
    public function payments() { 
        return $this->hasMany(Payment::class); 
    }
    
    public function getRemainingBalanceAttribute() {
        return $this->total - $this->paid_amount;
    }
    
    public function getIsOverdueAttribute() {
        if ($this->status !== 'INVOICED' && $this->status !== 'PARTIAL_PAID') {
            return false;
        }
        
        $dueDate = $this->getDueDateAttribute();
        return $dueDate ? now()->isAfter($dueDate) : false;
    }
    
    public function getDueDateAttribute() {
        if (!$this->invoice_date) return null;
        
        $terms = $this->payment_terms ?: ($this->customer ? $this->customer->payment_terms : 'net_30');
        
        $daysMap = [
            'cash' => 0,
            'net_7' => 7,
            'net_14' => 14,
            'net_30' => 30,
            'net_45' => 45,
            'net_60' => 60,
        ];
        
        $days = $daysMap[$terms] ?? 30;
        return $this->invoice_date->addDays($days);
    }
    
    protected static function boot() {
        parent::boot();
        static::creating(function($invoice) {
            if(!$invoice->invoice_number) $invoice->invoice_number = self::generateNumber();
        });
    }
    
    public static function generateNumber() {
        $date = now();
        $year = $date->year;
        $month = $date->month;
        $prefix = 'SI.' . $date->format('Y.m.');
        
        // Get the highest number for this month (including soft-deleted)
        $last = self::withTrashed()
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->orderByRaw("CAST(SUBSTRING(invoice_number, -5) AS UNSIGNED) DESC")
            ->first();
        
        if ($last) {
            $lastNum = intval(substr($last->invoice_number, -5));
            $num = $lastNum + 1;
        } else {
            $num = 1;
        }
        
        // Safety check: ensure number doesn't exist (handles race conditions)
        $maxAttempts = 10;
        while ($maxAttempts-- > 0) {
            $candidate = $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
            if (!self::withTrashed()->where('invoice_number', $candidate)->exists()) {
                return $candidate;
            }
            $num++;
        }
        
        // Fallback: if somehow we can't find a unique number
        throw new \Exception("Unable to generate unique invoice number");
    }
    
    public function calculate() {
        $this->subtotal = $this->items->sum('total');
        $afterDiscount = $this->subtotal - $this->discount;
        $this->ppn_amount = $afterDiscount * ($this->ppn_percent / 100);
        $this->total = $afterDiscount + $this->ppn_amount + $this->other_charges;
        return $this;
    }
}
