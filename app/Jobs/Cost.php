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

        Server::with('hosts')->where('status', 'up')->whereNot('price_per_gb', 0)->chunk(100, function ($servers) {
            foreach ($servers as $server) {
                // $ServerCheckJob = new ServerCheckJob($server->id);
                // $ServerCheckJob->handle();

                foreach ($server->hosts as $host) {
                    $host->load('user');

                    Log::debug('------------');
                    Log::debug('主机: ' . $host->name);
                    Log::debug('属于用户: ' . $host->user->name);


                    $cache_key = 'frpTunnel_data_' . $host->client_token;
                    // $tunnel = 'frp_user_' . $host->client_token;
                    // $tunnel_user_id = Cache::get($tunnel);
                    $tunnel_data = Cache::get($cache_key, null);

                    if (!is_null($tunnel_data)) {
                        $traffic = ($tunnel_data['today_traffic_in'] ?? 0) + ($tunnel_data['today_traffic_out'] ?? 0);

                        // $traffic = 1073741824 * 10;

                        Log::debug('本次使用的流量: ' . $traffic / 1024 / 1024 / 1024);


                        $day = date('d');

                        $traffic_key = 'traffic_day_' . $day . '_used_' . $host->id;

                        $used_traffic = Cache::get($traffic_key, 0);
                        if ($used_traffic !== $traffic) {
                            // 保存 2 天
                            Cache::put($traffic_key, $traffic, 60 * 48);

                            $used_traffic_gb = $used_traffic / 1024 / 1024 / 1024;

                            // Log::debug('上次使用的流量: ' . $used_traffic);
                            Log::debug('上次使用的流量 GB: ' . $used_traffic_gb);

                            $used_traffic = $traffic - $used_traffic;

                            Log::debug('流量差值: ' . $used_traffic / 1024 / 1024 / 1024);
                        }

                        $left_traffic = 0;

                        if ($host->user->free_traffic > 0) {

                            Log::debug('开始扣除免费流量时的 used_traffic: ' . $used_traffic / 1024 / 1024 / 1024);

                            $user_free_traffic = $host->user->free_traffic * 1024 * 1024 * 1024;

                            Log::debug('用户免费流量: ' . $user_free_traffic / 1024 / 1024 / 1024);

                            // $used_traffic -= $user_free_traffic;
                            // $used_traffic = abs($used_traffic);


                            Log::debug('扣除免费流量时的 used_traffic: ' . $used_traffic / 1024 / 1024 / 1024);

                            // 获取剩余
                            $left_traffic = $user_free_traffic - $used_traffic;

                            Log::debug('计算后剩余的免费流量: ' . $left_traffic / 1024 / 1024 / 1024);

                            // 保存

                            if ($left_traffic < 0) {
                                $left_traffic = 0;
                            }

                            $host->user->free_traffic = $left_traffic / 1024 / 1024 / 1024;
                            $host->user->save();
                        }

                        $used_traffic = abs($used_traffic);

                        Log::debug('实际用量:' . $used_traffic  / 1024 / 1024 / 1024);


                        // $used_traffic -= $server->free_traffic * 1024 * 1024 * 1024;
                        // // $used_traffic = abs($used_traffic);

                        // Log::debug('服务器免费流量: ' . $server->free_traffic * 1024 * 1024 * 1024);

                        // Log::debug('使用的流量（减去服务器免费流量）: ' . $used_traffic);


                        if ($used_traffic > 0 && $left_traffic == 0) {

                            Log::debug('此时 used_traffic: ' . $used_traffic);

                            // 要计费的流量
                            $traffic = $used_traffic / (1024 * 1024 * 1024);

                            $traffic = abs($traffic);

                            $gb = ceil($traffic);

                            // 计算价格
                            $cost = $traffic * $host->server->price_per_gb;
                            $cost = abs($cost);

                            // 记录到日志
                            // if local
                            if (config('app.env') == 'local') {
                                Log::debug('计费：' . $host->server->name . ' ' . $host->name . ' ' . $gb . 'GB ' . $cost . ' 的 Drops 消耗');
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
