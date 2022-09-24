<?php

namespace App\Jobs;

use App\Models\Host;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddFreeTraffic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        // last_add_free_traffic_at 为空的，或者距离上次补给时间超过 一个月的
        // $hosts = Host::where(function ($query) {
        //     $query->whereNull('last_add_free_traffic_at')
        //         ->orWhere('last_add_free_traffic_at', '<', now()->subMonth());
        // })


        Host::where('free_traffic', '>', 0)->chunk(100, function ($hosts) {

            foreach ($hosts as $host) {
                // last_add_free_traffic_at 大于一个月
                if ($host->last_add_free_traffic_at == null || $host->last_add_free_traffic_at->diffInDays(now()) > 30) {
                    User::find($host->user_id)->increment('free_traffic', $host->free_traffic);

                    $host->last_add_free_traffic_at = now();
                    $host->save();
                }
            }
        });
    }
}
