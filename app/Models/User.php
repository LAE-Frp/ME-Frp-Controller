<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // fillable
    protected $fillable = [
        'id',
        'name',
        'email',
        'created_at',
        'updated_at',
    ];

    // disable auto increment
    public $incrementing = false;
    public $timestamps = false;
}
