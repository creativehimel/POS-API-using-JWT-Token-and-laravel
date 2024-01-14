<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'otp',
    ];

    protected $attributes = [
        'otp' => '0',
    ];
}
