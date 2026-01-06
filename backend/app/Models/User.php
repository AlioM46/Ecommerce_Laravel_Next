<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject

{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login',
        'role',
        'refresh_token',
        'refresh_token_expiration_at',
    ];

    protected $hidden = [
        'password',
        // 'refresh_token',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'role' => UserRole::class,
    ];

    public function orders() {
        return $this->hasMany(Order::class);
    }


    public function addresses() {
        return $this->hasMany(Address::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

   public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // === custom claims (role, name, email) ===
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name'  => $this->name,
            'role'  => $this->role,
        ];
    }
}
