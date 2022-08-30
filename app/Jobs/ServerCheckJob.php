<?php

namespace App\Jobs;

use App\Http\Controllers\FrpController;
use App\Http\Controllers\ServerController;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
class ServerCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $frpServer = Server::find($this->id);
        if (!is_null($frpServer)) {
            // $frp = new FrpController($this->id);
            $s = new ServerController();
            $s->scanTunnel($frpServer->id);
            // if ($frp) {
            //     teamEvent('frpServer.tunnel.server.updated', null, $frpServer->team_id);
            //     teamEvent('frpServer.tunnels.updated', null, $frpServer->team_id);
            // }
        }
    }
}
