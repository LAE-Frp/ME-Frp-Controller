<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;


class StatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $host_id;
    public array $requests;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($host_id, $requests)
    {
        $this->host_id = $host_id;
        $this->requests = $requests;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Http::remote()->asForm()->patch('hosts/' . $this->host_id, $this->requests);
    }
}
