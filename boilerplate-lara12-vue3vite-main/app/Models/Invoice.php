<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model {
    use SoftDeletes;
    protected $fillable = ['invoice_number','invoice_date','customer_name','customer_address','payment_terms','expedition','po_number','delivery_date','currency','subtotal','discount','ppn_percent','ppn_amount','other_charges','total','notes','terbilang','prepared_by','approved_by','status'];
    protected $casts = ['invoice_date'=>'date','delivery_date'=>'date','subtotal'=>'decimal:2','discount'=>'decimal:2','ppn_percent'=>'decimal:2','ppn_amount'=>'decimal:2','other_charges'=>'decimal:2','total'=>'decimal:2'];
    
    public function items() { return $this->hasMany(InvoiceItem::class); }
    
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
