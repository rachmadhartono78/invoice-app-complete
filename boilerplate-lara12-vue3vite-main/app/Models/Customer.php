<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'contact_person',
        'contact_phone',
        'tax_id',
        'payment_terms',
        'credit_limit',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTotalOutstandingAttribute()
    {
        return $this->invoices()
            ->whereIn('status', ['INVOICED', 'PARTIAL_PAID'])
            ->sum('total');
    }

    public function getPaymentTermsLabelAttribute()
    {
        $terms = [
            'cash' => 'Cash',
            'net_7' => 'Net 7 Days',
            'net_14' => 'Net 14 Days',
            'net_30' => 'Net 30 Days',
            'net_45' => 'Net 45 Days',
            'net_60' => 'Net 60 Days',
        ];
        return $terms[$this->payment_terms] ?? $this->payment_terms;
    }
}
