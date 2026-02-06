<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionUse extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function action()
    {
        return $this->belongsTo(Actions::class, 'action_id');
    }

    public function menuAuthority()
    {
        return $this->belongsTo(MenuAuthority::class, 'menu_authority_id');
    }
}
