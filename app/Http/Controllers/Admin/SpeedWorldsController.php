<?php

namespace App\Http\Controllers\Admin;

use App\SpeedWorld;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SpeedWorldsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('speed_world_access'), 403);

        $header = __('admin.speedWorlds.title');
        $create = [
            'permission' => 'speed_world_create',
            'title' => __('admin.speedWorlds.create'),
            'route' => "admin.speedWorlds.create",
        ];
        $tableColumns = static::getIndexColumns();
        $datatableRoute = "admin.api.speedWorlds";
        $defaultOrder = "0, 'desc'";

        return view('admin.shared.index', compact('header', 'create', 'tableColumns', 'datatableRoute', 'defaultOrder'));
    }
    
    public static function getIndexColumns() {
        return [
            BasicFunctions::indexEntry(__('admin.speedWorlds.id'), "id"),
            BasicFunctions::indexEntry(__('admin.speedWorlds.server'), "server_id", "", "", ['dataAdditional' => ', "searchable": false']),
            BasicFunctions::indexEntry(__('admin.speedWorlds.name'), "name"),
            BasicFunctions::indexEntry(__('admin.speedWorlds.display_name'), "display_name"),
            BasicFunctions::indexEntry(__('admin.speedWorlds.plannedStart'), "planned_start"),
            BasicFunctions::indexEntry(__('admin.speedWorlds.plannedEnd'), "planned_end"),
            BasicFunctions::indexEntry(__('admin.speedWorlds.instance'), "instance"),
            BasicFunctions::indexEntry(__('admin.speedWorlds.url'), "url", "", "", ['dataAdditional' => ', "orderable": false, "searchable": false']),
            BasicFunctions::indexEntry(__('admin.speedWorlds.started'), "started"),
            BasicFunctions::indexEntry(" ", "actions", "width:180px;", "align-middle", ['dataAdditional' => ', "orderable": false, "searchable": false']),
        ];
    }

    public function create()
    {
        abort_unless(\Gate::allows('speed_world_create'), 403);

        $formEntries = $this->generateEditFormConfig(null);
        $route = route("admin.speedWorlds.store");
        $header = __('admin.speedWorlds.titleCreate');
        $method = "POST";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('speed_world_create'), 403);
        
        $request->validate([
            'server_id' => 'required|integer',
            'name' => 'required',
            'planned_start_date' => 'required',
            'planned_start_time' => 'required',
        ]);
        
        $speedWorld = new SpeedWorld();
        $speedWorld->server_id = $request->server_id;
        $speedWorld->name = $request->name;
        $speedWorld->display_name = $request->display_name;
        $speedWorld->planned_start = Carbon::parse($request->planned_start_date.' '.$request->planned_start_time)->timestamp;
        if($request->has("planned_end_date") && $request->has("planned_end_time") &&
                $request->planned_end_date !== null && $request->planned_end_time !== null) {
            $speedWorld->planned_end = Carbon::parse($request->planned_end_date.' '.$request->planned_end_time)->timestamp;
        } else {
            $speedWorld->planned_end = -1;
        }
        $speedWorld->started = 0;
        $speedWorld->world_id = null;
        $speedWorld->instance = $request->instance;
        $speedWorld->save();

        return redirect()->route('admin.speedWorlds.index');
    }

    public function edit(SpeedWorld $speedWorld)
    {
        abort_unless(\Gate::allows('speed_world_edit'), 403);

        $formEntries = $this->generateEditFormConfig($speedWorld);
        $route = route("admin.speedWorlds.update", [$speedWorld->id]);
        $header = __('admin.speedWorlds.update');
        $method = "PUT";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function update(Request $request, SpeedWorld $speedWorld)
    {
        abort_unless(\Gate::allows('speed_world_edit'), 403);
        
        $request->validate([
            'server_id' => 'required|integer',
            'name' => 'required',
            'planned_start_date' => 'required',
            'planned_start_time' => 'required',
        ]);

        $speedWorld->server_id = $request->server_id;
        $speedWorld->name = $request->name;
        $speedWorld->display_name = $request->display_name;
        $speedWorld->planned_start = Carbon::parse($request->planned_start_date.' '.$request->planned_start_time)->timestamp;
        if($request->has("planned_end_date") && $request->has("planned_end_time") &&
                $request->planned_end_date !== null && $request->planned_end_time !== null) {
            $speedWorld->planned_end = Carbon::parse($request->planned_end_date.' '.$request->planned_end_time)->timestamp;
        } else {
            $speedWorld->planned_end = -1;
        }
        $speedWorld->instance = $request->instance;
        $speedWorld->save();

        return redirect()->route('admin.speedWorlds.index');
    }

    public function show(SpeedWorld $speedWorld)
    {
        abort_unless(\Gate::allows('speed_world_show'), 403);
        
        $formEntries = $this->generateShowFormConfig($speedWorld);
        $header = __('admin.speedWorlds.show');
        $title = $speedWorld->name;
        return view('admin.shared.form_show', compact('formEntries', 'header', 'title'));
    }

    public function destroy(SpeedWorld $speedWorld)
    {
        abort_unless(\Gate::allows('speed_world_delete'), 403);
        $speedWorld->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        abort_unless(\Gate::allows('speed_world_delete'), 403);
        
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:speed_worlds,id',
        ]);
        SpeedWorld::whereIn('id', $request->input('ids'))->delete();
        return response(null, 204);
    }
    
    private function generateEditFormConfig($values) {
        return [
            BasicFunctions::formEntryEdit($values, 'select', __('admin.speedWorlds.server'), 'server_id', '', false, true, [
                'options' => \App\Server::all()->pluck('code', 'id'),
                'multiple' => false,
            ]),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.speedWorlds.name'), 'name', '', false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.speedWorlds.display_name'), 'display_name', '', false, false),
            BasicFunctions::formEntryEdit($values, 'time', __('admin.speedWorlds.plannedStart'), 'planned_start', time(), false, true),
            BasicFunctions::formEntryEdit($values, 'time', __('admin.speedWorlds.plannedEnd'), 'planned_end', time(), false, false),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.speedWorlds.instance'), 'instance', '', false, false),
        ];
    }
    
    private function generateShowFormConfig($values) {
        return [
            BasicFunctions::formEntryShow(__('admin.speedWorlds.id'), $values->id),
            BasicFunctions::formEntryShow(__('admin.speedWorlds.flag'), '<span class="flag-icon flag-icon-'. htmlentities($values->server->flag).
                    '"></span> ['. htmlentities($values->server->code). ']', false),
            BasicFunctions::formEntryShow(__('admin.speedWorlds.plannedStart'), Carbon::createFromTimestamp($values->planned_start)),
            BasicFunctions::formEntryShow(__('admin.speedWorlds.plannedEnd'), Carbon::createFromTimestamp($values->planned_end)),
            BasicFunctions::formEntryShow(__('admin.speedWorlds.name'), $values->name),
            BasicFunctions::formEntryShow(__('admin.speedWorlds.display_name'), $values->display_name),
            BasicFunctions::formEntryShow(__('admin.speedWorlds.url'), $values->url),
            BasicFunctions::formEntryShow(__('admin.speedWorlds.instance'), $values->instance),
            BasicFunctions::formEntryShow(__('admin.speedWorlds.active'),
                    ($values->active == 1)? '<span class="fas fa-check" style="color: green"></span>' :
                    '<span class="fas fa-times" style="color: red"></span>', false),
        ];
    }
}
