<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'all_emails',
        'all_phones',
        'all_usernames',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * User belongs to many organizations
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_users', 'user_id', 'organization_id');
    }

    /**
     * User belongs to many authorities (roles)
     */
    public function authorities()
    {
        return $this->belongsToMany(Authority::class, 'authority_users', 'user_id', 'authority_id');
    }

    /**
     * Get the authority-user pivot records for this user
     */
    public function authorityUsers()
    {
        return $this->hasMany(AuthorityUser::class, 'user_id');
    }

    /**
     * Get all user identifiers
     */
    public function identifiers()
    {
        return $this->hasMany(UserIdentifier::class);
    }

    /**
     * Get all email identifiers
     */
    public function emails()
    {
        return $this->hasMany(UserIdentifier::class)->ofType('email');
    }

    /**
     * Get all phone identifiers
     */
    public function phones()
    {
        return $this->hasMany(UserIdentifier::class)->ofType('phone');
    }

    /**
     * Get all username identifiers
     */
    public function usernames()
    {
        return $this->hasMany(UserIdentifier::class)->ofType('username');
    }

    /**
     * Get all emails as array
     */
    public function getAllEmailsAttribute()
    {
        return $this->emails()->get()->map(function ($identifier) {
            return [
                'id' => $identifier->id,
                'value' => $identifier->value,
                'verified_at' => $identifier->verified_at,
            ];
        });
    }

    /**
     * Get all phones as array
     */
    public function getAllPhonesAttribute()
    {
        return $this->phones()->get()->map(function ($identifier) {
            return [
                'id' => $identifier->id,
                'value' => $identifier->value,
                'verified_at' => $identifier->verified_at,
            ];
        });
    }

    /**
     * Get all usernames as array
     */
    public function getAllUsernamesAttribute()
    {
        return $this->usernames()->get()->map(function ($identifier) {
            return [
                'id' => $identifier->id,
                'value' => $identifier->value,
                'verified_at' => $identifier->verified_at,
            ];
        });
    }
}
