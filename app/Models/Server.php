<?php

namespace App\Models;

use App\Models\Host;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'status', 'price_per_gb', 'is_china_mainland'
    ];

    // hosts
    public function hosts()
    {
        return $this->hasMany(Host::class);
    }
}
