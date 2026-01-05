<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'membership_status',
        'birth_date',
        'birth_place_type',
        'birth_province_code',
        'birth_city',
        'birth_country',
        'residence_type',
        'residence_street',
        'residence_house_number',
        'residence_locality',
        'residence_province_code',
        'residence_city',
        'residence_country',
        'plv_joined_at',
        'plv_expires_at',
        'phone',
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
            'birth_date' => 'date',
            'plv_joined_at' => 'date',
            'plv_expires_at' => 'date',
        ];
    }
    
    /**
     * Get the memberships for the user.
     */
    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
