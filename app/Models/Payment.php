<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'receipt_file',
        'created_by'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
            'giro' => 'Giro',
            'credit_card' => 'Credit Card',
            'other' => 'Other',
        ];
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    // Generate next payment number
    public static function generatePaymentNumber()
    {
        $prefix = 'PMT';
        $date = now()->format('Ymd');
        $lastPayment = self::where('payment_number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('payment_number', 'desc')
            ->first();
        
        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->payment_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$date}-{$newNumber}";
    }
}
