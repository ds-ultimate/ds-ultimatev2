<?php

namespace App\Http\Controllers\Admin;

use App\Server;
use App\Console\DatabaseUpdate\TableGenerator;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('server_access'), 403);

        $header = __('admin.server.title');
        $create = [
            'permission' => 'server_create',
            'title' => __('admin.server.create'),
            'route' => "admin.server.create",
        ];
        $tableColumns = [
            BasicFunctions::indexEntry(__('admin.server.id'), "id"),
            BasicFunctions::indexEntry(__('admin.server.code'), "code"),
            BasicFunctions::indexEntry(__('admin.server.flag'), "flag"),
            BasicFunctions::indexEntry(__('admin.server.url'), "url"),
            BasicFunctions::indexEntry(__('admin.server.speed_active'), "speed_active"),
            BasicFunctions::indexEntry(__('admin.server.classic_active'), "classic_active"),
            BasicFunctions::indexEntry(__('admin.server.active'), "active"),
            BasicFunctions::indexEntry(" ", "actions", "width:180px;", "align-middle", ['dataAdditional' => ', "orderable": false']),
        ];
        $datatableRoute = "admin.api.servers";

        return view('admin.shared.index', compact('header', 'create', 'tableColumns', 'datatableRoute'));
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
        ($request->speed_active === 'on')? $request->merge(['speed_active' => 1]) : $request->merge(['speed_active' => 0]);
        ($request->classic_active === 'on')? $request->merge(['classic_active' => 1]) : $request->merge(['classic_active' => 0]);
        $server = Server::create($request->all());
        TableGenerator::otherServersTable($server);

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
        ($request->speed_active === 'on')? $request->merge(['speed_active' => 1]) : $request->merge(['speed_active' => 0]);
        ($request->classic_active === 'on')? $request->merge(['classic_active' => 1]) : $request->merge(['classic_active' => 0]);
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
            BasicFunctions::formEntryEdit($values, 'check', __('admin.server.speed_active'), 'speed_active', '', false, false),
            BasicFunctions::formEntryEdit($values, 'check', __('admin.server.classic_active'), 'classic_active', '', false, false),
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
            BasicFunctions::formEntryShow(__('admin.server.speed_active'),
                    ($values->speed_active == 1)? '<span class="fas fa-check" style="color: green"></span>' :
                    '<span class="fas fa-times" style="color: red"></span>', false),
            BasicFunctions::formEntryShow(__('admin.server.classic_active'),
                    ($values->classic_active == 1)? '<span class="fas fa-check" style="color: green"></span>' :
                    '<span class="fas fa-times" style="color: red"></span>', false),
        ];
    }
}
