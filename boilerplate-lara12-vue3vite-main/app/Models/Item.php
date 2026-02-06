<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    protected $fillable = ['category_id', 'name', 'code', 'unit', 'price', 'quantity', 'area', 'description', 'is_active'];
    protected $casts = ['price' => 'decimal:2', 'is_active' => 'boolean'];

    public function category() {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function invoiceItems() {
        return $this->hasMany(InvoiceItem::class);
    }

    // Scope untuk active items saja
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }
}
