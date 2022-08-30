<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'name',
    //     'fqdn',
    //     'port',
    //     'status',
    // ];

    protected $fillable = [
        'name', 'server_address', 'server_port', 'token', 'dashboard_port', 'dashboard_user', 'dashboard_password',
        'allow_http', 'allow_https', 'allow_tcp', 'allow_udp', 'allow_stcp', 'min_port', 'max_port', 'max_tunnels',
        'status'
    ];
}
