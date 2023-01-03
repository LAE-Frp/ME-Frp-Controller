<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
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

    public function close()
    {
        $frp = new FrpController($this->server_id);
        $closed = $frp->close($this->run_id);

        if ($closed) {
            $cache_key = 'frpTunnel_data_' . $this->client_token;
            Cache::forget($cache_key);
        }

        return true;
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
            $closed = false;
            if ($model->status == 'suspended') {
                $model->suspended_at = now();

                $model->close();
                $closed = true;
            } else if ($model->status == 'stopped') {

                $model->close();
                $closed = true;
            } else if ($model->status == 'running') {
                $model->suspended_at = null;
            }

            if ($closed) {
                $model->run_id = null;
            }

            // if is dirty status
            if ($model->isDirty('status')) {
                Http::remote()->asForm()->patch('hosts/' . $model->host_id, [
                    'status' => $model->status,
                ]);
            }
        });
    }
}
