<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function menuInduk()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function authorities()
    {
        return $this->belongsToMany(Menu::class, 'menu_authorities', 'menu_id', 'authority_id');
    }

    public function actions()
    {
        return $this->hasManyThrough(
            ActionUse::class, // Final target model
            MenuAuthority::class, // Intermediate pivot table
        );
    }
}
