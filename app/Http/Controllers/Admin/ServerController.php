<?php

namespace App\Http\Controllers\Admin;

use App\Server;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

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

        $formEntries = $this->generateEditFormConfig(null);
        $route = route("admin.server.store");
        $header = __('admin.server.titleCreate');
        $method = "POST";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('server_create'), 403);

        $request->validate([
            'code' => 'required',
            'flag' => 'required',
            'url' => 'required',
        ]);
        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);
        $server = Server::create($request->all());

        return redirect()->route('admin.server.index');
    }

    public function edit(Server $server)
    {
        abort_unless(\Gate::allows('server_edit'), 403);

        $formEntries = $this->generateEditFormConfig($server);
        $route = route("admin.server.update", [$server->id]);
        $header = __('admin.server.update');
        $method = "PUT";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function update(Request $request, Server $server)
    {
        abort_unless(\Gate::allows('server_edit'), 403);
        
        $request->validate([
            'code' => 'required',
            'flag' => 'required',
            'url' => 'required',
        ]);
        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);
        $server->update($request->all());

        return redirect()->route('admin.server.index');
    }

    public function show(Server $server)
    {
        abort_unless(\Gate::allows('server_show'), 403);
        
        $formEntries = $this->generateShowFormConfig($server);
        $header = __('admin.server.show');
        $title = $server->code;
        return view('admin.shared.form_show', compact('formEntries', 'header', 'title'));
    }

    public function destroy(Server $server)
    {
        abort_unless(\Gate::allows('server_delete'), 403);

        $server->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        abort_unless(\Gate::allows('server_delete'), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:server,id',
        ]);
        Server::whereIn('id', $request->input('ids'))->delete();
        return response(null, 204);
    }
    
    private function generateEditFormConfig($values) {
        return [
            BasicFunctions::formEntryEdit($values, 'text', __('admin.server.code'), 'code', '', false, true),
            BasicFunctions::formEntryEdit($values, 'select', __('admin.server.flag'), 'flag', '', false, true, [
                'options' => \App\Util\Flag::flagsWithSymbol(),
                'multiple' => false,
                'raw' => true,
            ]),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.server.url'), 'url', '', false, true),
            BasicFunctions::formEntryEdit($values, 'check', __('admin.server.active'), 'active', '', false, false),
        ];
    }
    
    private function generateShowFormConfig($values) {
        return [
            BasicFunctions::formEntryShow(__('admin.server.code'), $values->code),
            BasicFunctions::formEntryShow(__('admin.server.flag'), '<span class="flag-icon flag-icon-'. htmlentities($values->flag).
                    '"></span> ['. htmlentities($values->flag). ']', false),
            BasicFunctions::formEntryShow(__('admin.server.url'), $values->url),
            BasicFunctions::formEntryShow(__('admin.server.active'),
                    ($values->active == 1)? '<span class="fas fa-check" style="color: green"></span>' :
                    '<span class="fas fa-times" style="color: red"></span>', false),
        ];
    }
}
