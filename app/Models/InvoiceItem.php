<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model {
    protected $fillable = ['invoice_id','item_id','item_code','item_name','area','quantity','unit_price','discount','total','sort_order'];
    protected $casts = ['quantity'=>'integer','unit_price'=>'decimal:2','discount'=>'decimal:2','total'=>'decimal:2'];
    
    public function invoice() { return $this->belongsTo(Invoice::class); }
    
    public function item() { return $this->belongsTo(Item::class); }
    
    protected static function boot() {
        parent::boot();
        static::saving(function($item) {
            $item->total = ($item->quantity * $item->unit_price) - $item->discount;
        });
    }
}
