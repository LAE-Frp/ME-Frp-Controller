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
        'free_traffic',
        'created_at',
        'updated_at',
    ];

    // disable auto increment
    public $incrementing = false;
    public $timestamps = true;


    // when update
    public static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if ($model->free_traffic == null) {
                $model->free_traffic = 0;
            }
        });
    }
}
