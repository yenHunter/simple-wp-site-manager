<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServerController extends Controller
{
    public function index()
    {
        return Inertia::render('Servers/Index', [
            'servers' => Server::withCount('sites')->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Servers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|numeric',
            'username' => 'required|string',
            'ssh_credentials' => 'required|string', // Password or Key
        ]);

        Server::create($validated);

        return redirect()->route('servers.index')
            ->with('message', 'Server added successfully!');
    }
    
    public function destroy(Server $server)
    {
        $server->delete();
        return redirect()->back();
    }
}