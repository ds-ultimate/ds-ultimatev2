<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyServerRequest;
use App\Http\Requests\StoreServerRequest;
use App\Http\Requests\UpdateServerRequest;
use App\Server;

class ServerController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('server_access'), 403);

        $servers = Server::all();

        return view('admin.server.index', compact('servers'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('server_create'), 403);

        return view('admin.server.create');
    }

    public function store(StoreServerRequest $request)
    {
        abort_unless(\Gate::allows('server_create'), 403);

        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);

        $server = Server::create($request->all());

        return redirect()->route('admin.server.index');
    }

    public function edit(Server $server)
    {
        abort_unless(\Gate::allows('server_edit'), 403);

        return view('admin.server.edit', compact('server'));
    }

    public function update(UpdateServerRequest $request, Server $server)
    {
        abort_unless(\Gate::allows('server_edit'), 403);
        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);

        $server->update($request->all());

        return redirect()->route('admin.server.index');
    }

    public function show(Server $server)
    {
        abort_unless(\Gate::allows('server_show'), 403);

        return view('admin.server.show', compact('server'));
    }

    public function destroy(Server $server)
    {
        abort_unless(\Gate::allows('server_delete'), 403);

        $server->delete();

        return back();
    }

    public function massDestroy(MassDestroyServerRequest $request)
    {
        Server::whereIn('id', $request->input('ids'))->delete();

        return response(null, 204);
    }
}
