<?php

namespace App\Http\Controllers\Admin;

use App\World;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

class WorldsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('world_access'), 403);

        $header = __('admin.worlds.title');
        $create = [
            'permission' => 'world_create',
            'title' => __('admin.worlds.create'),
            'route' => "admin.worlds.create",
        ];
        $tableColumns = static::getIndexColumns();
        $datatableRoute = "admin.api.worlds";

        return view('admin.shared.index', compact('header', 'create', 'tableColumns', 'datatableRoute'));
    }
    
    public static function getIndexColumns() {
        return [
            BasicFunctions::indexEntry(__('admin.worlds.id'), "id"),
            BasicFunctions::indexEntry(__('admin.worlds.server'), "server", "", "", ['dataAdditional' => ', "orderable": false, "searchable": false']),
            BasicFunctions::indexEntry(__('admin.worlds.name'), "display_name"),
            BasicFunctions::indexEntry(__('admin.worlds.name'), "name"),
            BasicFunctions::indexEntry(__('admin.worlds.ally_count'), "ally_count"),
            BasicFunctions::indexEntry(__('admin.worlds.player_count'), "player_count"),
            BasicFunctions::indexEntry(__('admin.worlds.village_count'), "village_count"),
            BasicFunctions::indexEntry(__('admin.worlds.url'), "url"),
            BasicFunctions::indexEntry(__('admin.worlds.active'), "active"),
            BasicFunctions::indexEntry(__('admin.worlds.update'), "worldUpdated_at"),
            BasicFunctions::indexEntry(__('admin.worlds.clean'), "worldCleaned_at"),
            BasicFunctions::indexEntry(" ", "actions", "width:180px;", "align-middle", ['dataAdditional' => ', "orderable": false']),
        ];
    }

    public function create()
    {
        abort_unless(\Gate::allows('world_create'), 403);

        $formEntries = $this->generateEditFormConfig(null);
        $route = route("admin.worlds.store");
        $header = __('admin.worlds.titleCreate');
        $method = "POST";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('world_create'), 403);
        
        $request->validate([
            'server_id' => 'required',
            'name' => 'required',
            'display_name' => 'nullable|alpha_num',
            'url' => 'required',
            'config' => 'required',
            'units' => 'required',
            'hash_ally' => 'required',
            'hash_player' => 'required',
            'hash_village' => 'required',
        ]);
        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);
        ($request->maintananceMode === 'on')? $request->merge(['maintananceMode' => 1]) : $request->merge(['maintananceMode' => 0]);

        $world = World::create($request->all());

        return redirect()->route('admin.worlds.index');
    }

    public function edit(World $world)
    {
        abort_unless(\Gate::allows('world_edit'), 403);

        $formEntries = $this->generateEditFormConfig($world);
        $route = route("admin.worlds.update", [$world->id]);
        $header = __('admin.worlds.update');
        $method = "PUT";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function update(Request $request, World $world)
    {
        abort_unless(\Gate::allows('world_edit'), 403);
        
        $request->validate([
            'server_id' => 'required',
            'name' => 'required',
            'display_name' => 'nullable|alpha_num',
            'url' => 'required',
            'config' => 'required',
            'units' => 'required',
            'hash_ally' => 'required',
            'hash_player' => 'required',
            'hash_village' => 'required',
        ]);
        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);
        ($request->maintananceMode === 'on')? $request->merge(['maintananceMode' => 1]) : $request->merge(['maintananceMode' => 0]);

        $world->update($request->all());

        return redirect()->route('admin.worlds.index');
    }

    public function show(World $world)
    {
        abort_unless(\Gate::allows('world_show'), 403);
        
        $formEntries = $this->generateShowFormConfig($world);
        $header = __('admin.worlds.show');
        $title = $world->display_name;
        return view('admin.shared.form_show', compact('formEntries', 'header', 'title'));
    }

    public function destroy(World $world)
    {
        abort_unless(\Gate::allows('world_delete'), 403);

        $world->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        abort_unless(\Gate::allows('world_delete'), 403);
        
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:worlds,id',
        ]);
        World::whereIn('id', $request->input('ids'))->delete();
        return response(null, 204);
    }
    
    private function generateEditFormConfig($values) {
        return [
            BasicFunctions::formEntryEdit($values, 'select', __('admin.worlds.server'), 'server_id', '', false, true, [
                'options' => \App\Server::all()->pluck('code', 'id'),
                'multiple' => false,
            ]),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.name'), 'name', '', false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.display_name'), 'display_name', '', false, false),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.ally_count'), 'ally_count', 0, false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.player_count'), 'player_count', 0, false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.village_count'), 'village_count', 0, false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.url'), 'url', '', false, true),
            BasicFunctions::formEntryEdit($values, 'textarea', __('admin.worlds.config'), 'config', '', false, true),
            BasicFunctions::formEntryEdit($values, 'textarea', __('admin.worlds.units'), 'units', '', false, true),
            BasicFunctions::formEntryEdit($values, 'text', "Hash ally", 'hash_ally', '', false, true),
            BasicFunctions::formEntryEdit($values, 'text', "Hash player", 'hash_player', '', false, true),
            BasicFunctions::formEntryEdit($values, 'text', "Hash village", 'hash_village', '', false, true),
            BasicFunctions::formEntryEdit($values, 'check', __('admin.worlds.active'), 'active', '', false, false),
            BasicFunctions::formEntryEdit($values, 'check', __('admin.worlds.maintananceMode'), 'maintananceMode', '', false, false),
        ];
    }
    
    private function generateShowFormConfig($values) {
        return [
            BasicFunctions::formEntryShow(__('admin.worlds.id'), $values->id),
            BasicFunctions::formEntryShow(__('admin.server.flag'), '<span class="flag-icon flag-icon-'. htmlentities($values->server->flag).
                    '"></span> ['. htmlentities($values->server->code). ']', false),
            BasicFunctions::formEntryShow(__('admin.worlds.name'), $values->name),
            BasicFunctions::formEntryShow(__('admin.worlds.display_name'), $values->display_name),
            BasicFunctions::formEntryShow(__('admin.worlds.ally_count'), $values->ally_count),
            BasicFunctions::formEntryShow(__('admin.worlds.player_count'), $values->player_count),
            BasicFunctions::formEntryShow(__('admin.worlds.village_count'), $values->village_count),
            BasicFunctions::formEntryShow(__('admin.worlds.url'), $values->url),
            BasicFunctions::formEntryShow(__('admin.worlds.active'),
                    ($values->active == 1)? '<span class="fas fa-check" style="color: green"></span>' :
                    '<span class="fas fa-times" style="color: red"></span>', false),
            BasicFunctions::formEntryShow(__('admin.worlds.maintananceMode'),
                    ($values->maintananceMode == 1)? '<span class="fas fa-check" style="color: green"></span>' :
                    '<span class="fas fa-times" style="color: red"></span>', false),
        ];
    }
}
