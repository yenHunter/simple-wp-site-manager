<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Site;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function update(Request $request)
    {
        // 1. Authenticate via Token
        $token = $request->header('X-Server-Token');
        $server = Server::where('webhook_token', $token)->first();

        if (!$server) {
            return response()->json(['error' => 'Invalid Token'], 401);
        }

        // 2. Process Containers
        $containers = $request->input('containers', []);

        foreach ($containers as $data) {
            // Find site by container name on this specific server
            $site = Site::where('server_id', $server->id)
                ->where('container_name', $data['name'])
                ->first();

            if ($site) {
                // Normalize Docker status (running, exited, dead -> stopped)
                $status = strtolower($data['state']);
                if (in_array($status, ['exited', 'dead', 'paused'])) {
                    $status = 'stopped';
                } elseif ($status === 'restarting') {
                    $status = 'deploying';
                }

                $site->update(['status' => $status]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
