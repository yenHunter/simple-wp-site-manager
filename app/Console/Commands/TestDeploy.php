<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Models\Server;
use App\Services\RemoteService;
use Illuminate\Console\Command;

class TestDeploy extends Command
{
    protected $signature = 'test:deploy';
    protected $description = 'Test SSH Connection and Docker Deployment';

    public function handle(RemoteService $remoteService)
    {
        $this->info('--- Starting Deployment Test ---');

        // 1. Gather Info
        $ip = $this->ask('Enter WSL IP Address');
        $user = $this->ask('Enter WSL Username');
        $pass = $this->secret('Enter WSL Password');

        // 2. Create/Save Server (Encrypts password automatically)
        $server = Server::create([
            'name' => 'Local WSL',
            'ip_address' => $ip,
            'port' => 22,
            'username' => $user,
            'ssh_credentials' => $pass
        ]);

        $this->info('✅ Server saved to DB (Password Encrypted).');

        // 3. Create Site Data
        $site = Site::create([
            'server_id' => $server->id,
            'domain_name' => 'test-site.local',
            'port' => 8081, // We will access via localhost:8081 later
            'container_name' => 'wp_test_site',
            'db_name' => 'wp_db',
            'db_user' => 'wp_user',
            'db_password' => 'secret123',
            'status' => 'deploying'
        ]);

        // 4. Attempt Connection & Deployment
        $this->info('🔄 Connecting via SSH...');
        
        try {
            // Connect
            $remoteService->connect($server);
            $this->info('✅ SSH Connected!');

            // Deploy
            $this->info('🐳 Generating Docker Config & Starting Container...');
            $output = $remoteService->deploySite($site);
            
            $this->info('Output from Server:');
            $this->line($output);

            // Update Status
            $site->update(['status' => 'running', 'container_id' => 'test-id']);
            $this->info('🚀 Site Deployed Successfully!');
            $this->info('Visit http://localhost:8081 (if localhost forwarding works) or http://'.$ip.':8081');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $site->update(['status' => 'failed']);
        }
    }
}