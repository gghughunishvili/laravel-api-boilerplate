<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Hash;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, UuidTrait;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Hash password
     * @param $pass
     */
    public function setPasswordAttribute($pass)
    {
        if ($pass) {
            $this->attributes['password'] = app('hash')->needsRehash($pass) ? Hash::make($pass) : $pass;
        }
    }

    public function findForPassport($identifier)
    {
        return $this->where('status', config('custom.user.status.active'))
            ->where(function ($query) use ($identifier) {
                $query->orWhere('email', $identifier)
                    ->orWhere('username', $identifier);
            })->first();
    }
}
