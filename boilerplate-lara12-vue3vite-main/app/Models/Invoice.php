<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model {
    use SoftDeletes;
    protected $fillable = ['invoice_number','invoice_date','customer_name','customer_address','payment_terms','expedition','po_number','delivery_date','currency','subtotal','discount','ppn_percent','ppn_amount','other_charges','total','notes','prepared_by','approved_by','status'];
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
        $last = self::whereYear('invoice_date', $date->year)->whereMonth('invoice_date', $date->month)->max('invoice_number');
        $num = $last ? intval(substr($last, -5)) + 1 : 1;
        return 'SI.' . $date->format('Y.m.') . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
    
    public function calculate() {
        $this->subtotal = $this->items->sum('total');
        $afterDiscount = $this->subtotal - $this->discount;
        $this->ppn_amount = $afterDiscount * ($this->ppn_percent / 100);
        $this->total = $afterDiscount + $this->ppn_amount + $this->other_charges;
        return $this;
    }
}
