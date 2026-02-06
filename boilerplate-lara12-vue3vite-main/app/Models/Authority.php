<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Authority extends Model
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

    public function users()
    {
        return $this->belongsToMany(User::class, 'authority_users', 'authority_id', 'user_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_authorities', 'authority_id', 'menu_id')
                    ->withPivot('id')
                    ->withTimestamps();
    }

    public function menuAuthorities()
    {
        return $this->hasMany(MenuAuthority::class, 'authority_id');
    }

    public function authorityUsers()
    {
        return $this->hasMany(AuthorityUser::class, 'authority_id');
    }

    public function organizations()
    {
        // For a specific user context, this relationship doesn't make sense
        // because organizations are tied to authority-user pairs, not just authorities
        // This relationship should be accessed through AuthorityUser model
        return $this->hasMany(Organization::class, 'id', 'non_existent_field')->whereRaw('1 = 0');
    }

    public function getOrganizationsForUser($userId)
    {
        return Organization::whereIn('id', function($query) use ($userId) {
            $query->select('organization_id')
                  ->from('authority_user_organizations')
                  ->join('authority_users', 'authority_user_organizations.authority_user_id', '=', 'authority_users.id')
                  ->where('authority_users.authority_id', $this->id)
                  ->where('authority_users.user_id', $userId);
        });
    }
}
