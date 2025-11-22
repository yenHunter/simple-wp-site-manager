<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Site;
use App\Services\RemoteService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    protected $remoteService;

    public function __construct(RemoteService $remoteService)
    {
        $this->remoteService = $remoteService;
    }

    public function index(Server $server)
    {
        return Inertia::render('Sites/Index', [
            'server' => $server,
            'sites' => $server->sites()->orderBy('id', 'desc')->get()
        ]);
    }

    public function create(Server $server)
    {
        return Inertia::render('Sites/Create', [
            'server' => $server
        ]);
    }

    public function store(Request $request, Server $server)
    {
        // 1. Validate Input
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255', // e.g. blog.test
            'port' => 'required|numeric|unique:sites,port', // e.g. 8082
            'db_name' => 'required|string|alpha_dash',
            'db_user' => 'required|string|alpha_dash',
            'db_password' => 'required|string|min:8',
        ]);

        // 2. Prepare Data
        $validated['server_id'] = $server->id;
        $validated['container_name'] = Str::slug($validated['domain_name']) . '_' . time();
        $validated['status'] = 'deploying';
        $validated['container_id'] = null;

        // 3. Create Database Entry
        $site = Site::create($validated);

        // 4. Trigger Deployment in Background (or sync for simplicity)
        try {
            $this->remoteService->connect($server);
            $output = $this->remoteService->deploySite($site);
            
            // If successful, update status
            $site->update(['status' => 'running']);

            return redirect()->route('servers.sites.index', $server->id)
                ->with('message', 'Site deployed successfully!');

        } catch (\Exception $e) {
            $site->update(['status' => 'failed']);
            
            return redirect()->back()->withErrors([
                'error' => 'Deployment Failed: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(Server $server, Site $site)
    {
        try {
            $this->remoteService->connect($server);
            $this->remoteService->removeSite($site);
            
            $site->delete();

            return redirect()->back()->with('message', 'Site removed and files deleted.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}