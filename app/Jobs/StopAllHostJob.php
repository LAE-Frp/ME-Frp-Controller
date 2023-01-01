<?php

namespace App\Jobs;

use App\Models\Host;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StopAllHostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $user_id)
    {
        //
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $hosts = Host::where('user_id', $this->user_id);

        $hosts->chunk(100, function () use ($hosts) {
            foreach ($hosts as $host) {
                $host->status = 'stopped';
                $host->save();
            }
        });
    }
}
