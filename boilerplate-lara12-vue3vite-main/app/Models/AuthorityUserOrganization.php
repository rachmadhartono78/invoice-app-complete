<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorityUserOrganization extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function authorityUser()
    {
        return $this->belongsTo(AuthorityUser::class, 'authority_user_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
