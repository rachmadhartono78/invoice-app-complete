<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MenuAuthority extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function authority()
    {
        return $this->belongsTo(Authority::class);
    }

    public function actions()
    {
        return $this->hasMany(ActionUse::class, 'menu_authority_id');
    }
}
