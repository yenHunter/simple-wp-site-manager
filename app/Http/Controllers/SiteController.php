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
        // Validate Input
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255',
            'port' => 'required|numeric|unique:sites,port',
            'db_name' => 'required|string|alpha_dash',
            'db_user' => 'required|string|alpha_dash',
            'db_password' => 'required|string|min:8',
        ]);

        // Prepare Data
        $validated['server_id'] = $server->id;
        $validated['container_name'] = Str::slug($validated['domain_name']) . '_' . time();
        $validated['status'] = 'deploying';
        $validated['container_id'] = null;

        // Store in Database
        $site = Site::create($validated);

        // Trigger Deployment in Background
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