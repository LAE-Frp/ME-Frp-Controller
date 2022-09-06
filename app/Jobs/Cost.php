<?php

namespace App\Jobs;

use App\Models\Host;
use App\Models\Server;
use App\Helpers\Remote;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Log;

class Cost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $http;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->http = Http::remote('remote')->asForm();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Server::with('hosts')->where('status', 'up')->where('price_per_gb', 0)->chunk(100, function ($servers) {
            foreach ($servers as $server) {
                $ServerCheckJob = new ServerCheckJob($server->id);
                $ServerCheckJob->handle();

                foreach ($server->hosts as $host) {

                    $cache_key = 'frpTunnel_data_' . $host->client_token;
                    // $tunnel = 'frp_user_' . $host->client_token;
                    // $tunnel_user_id = Cache::get($tunnel);
                    $tunnel_data = Cache::get($cache_key);

                    // dd($tunnel_data);

                    if (!is_null($tunnel_data)) {
                        $traffic = $tunnel_data['today_traffic_in'] + $tunnel_data['today_traffic_out'];

                        // 如果今日流量比昨天小，则是新的一天
                        if ($traffic < $host->last_bytes) {
                            $host->last_bytes = $traffic;
                            $host->save();
                        } else {

                            // 要计费的流量
                            $traffic -= $host->last_bytes;

                            // byte 换算为 GB
                            $traffic = $traffic / (1024 * 1024 * 1024);

                            // 计算价格
                            $cost = $traffic * $host->server->price_per_gb;

                            // 记录到日志
                            // if local
                            if (config('app.env') == 'local') {
                                Log::debug('计费：' . $host->server->name . ' ' . $host->name . ' ' . $traffic . 'GB ' . $cost . ' 的 Drops 消耗');
                            }

                            // 如果计费金额大于 0，则扣费
                            if ($cost > 0) {
                                // 发送扣费请求
                                $this->http->patch('hosts/' . $host->host_id, [
                                    'cost_once' => $cost,
                                ]);
                            }
                        }
                    }
                }
            }
        });

        //
        // $cache_key = 'frpTunnel_data_' . $proxy['name'];
        // $tunnel = 'frp_user_' . $proxy['proxy_name'];
        // Cache::get($tunnel);


        // Host::all()->chunk(100, function ($hosts) {
        //     foreach ($hosts as $host) {

        //         dd($host);

        //         // $tunnel->cost = $tunnel->cost + 1;
        //         // $tunnel->save();
        //     }
        // });
        // chunk host
        // Host::all()->chunk(100, function ($hosts) {
        //     foreach ($hosts as $host) {
        //         return $host;
        //         // var_dump($host);
        //     }
        // });
    }
}
