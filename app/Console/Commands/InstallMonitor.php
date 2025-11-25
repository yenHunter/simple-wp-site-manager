<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Services\RemoteService;

class InstallMonitor extends Command
{
    protected $signature = 'server:monitor-install {server_id}';
    protected $description = 'Install the Docker Monitor Bash script on a remote server';

    public function handle(RemoteService $remoteService)
    {
        $id = $this->argument('server_id');
        $server = Server::find($id);

        if (!$server) {
            $this->error("Server with ID $id not found.");
            return;
        }

        $this->info("Connecting to {$server->name} ({$server->ip_address})...");

        try {
            $remoteService->connect($server);
            $this->info("Connected.");

            $this->info("Uploading monitor script and setting up Cron...");
            $msg = $remoteService->installMonitor($server);

            $server->refresh();

            $this->info("Success! $msg");
            $this->info("Token: " . $server->webhook_token);

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}