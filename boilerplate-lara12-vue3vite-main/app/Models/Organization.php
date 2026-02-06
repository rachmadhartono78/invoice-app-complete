<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'code',
        'type',
        'city',
        'province',
        'address',
        'phone',
        'email',
        'longitude',
        'latitude',
        'organization_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'longitude' => 'decimal:8',
        'latitude' => 'decimal:8',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user');
    }

    public function parent()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function children()
    {
        return $this->hasMany(Organization::class, 'organization_id');
    }
}
