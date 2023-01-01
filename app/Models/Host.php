<?php

namespace App\Models;

use App\Http\Controllers\FrpController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Host extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'protocol', 'custom_domain', 'local_address', 'remote_port', 'client_token',
        'sk', 'status', 'server_id', 'user_id', 'price', 'host_id', 'free_traffic'
    ];

    // 路由主键为 host_id
    public function getRouteKeyName()
    {
        return 'host_id';
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // scope thisUser
    public function scopeThisUser($query)
    {
        $user_id = request('user_id');
        return $query->where('user_id', $user_id);
    }

    protected $casts = [
        'suspended_at' => 'datetime',
        'last_add_free_traffic_at' => 'datetime',
    ];


    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // workOrders
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    // scope
    public function scopeRunning($query)
    {
        return $query->where('status', 'running')->where('price', '!=', 0);
    }

    // on createing
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // if id exists
            if ($model->where('id', $model->id)->exists()) {
                return false;
            }
        });

        // update
        static::updating(function (self $model) {
            $frp = new FrpController($model->server_id);

            if ($model->status == 'suspended') {
                $model->suspended_at = now();
                $frp->close($model->run_id);
            } else if ($model->status == 'stopped') {
                $frp->close($model->run_id);
            } else if ($model->status == 'running') {
                $model->suspended_at = null;
            }
        });
    }
}
