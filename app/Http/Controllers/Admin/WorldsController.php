<?php

namespace App\Http\Controllers\Admin;

use App\World;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorldsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('world_access'), 403);

        $worlds = World::all();
        $now = Carbon::now();

        return view('admin.worlds.index', compact('worlds', 'now'));
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
            'url' => 'required',
            'config' => 'required',
            'units' => 'required',
        ]);
        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);

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
            'url' => 'required',
            'config' => 'required',
            'units' => 'required',
        ]);
        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);

        $world->update($request->all());

        return redirect()->route('admin.worlds.index');
    }

    public function show(World $world)
    {
        abort_unless(\Gate::allows('world_show'), 403);
        
        $formEntries = $this->generateShowFormConfig($world);
        $header = __('admin.worlds.show');
        $title = $world->displayName();
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
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.ally_count'), 'ally_count', 0, false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.player_count'), 'player_count', 0, false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.village_count'), 'village_count', 0, false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.worlds.url'), 'url', '', false, true),
            BasicFunctions::formEntryEdit($values, 'textarea', __('admin.worlds.config'), 'config', '', false, true),
            BasicFunctions::formEntryEdit($values, 'textarea', __('admin.worlds.units'), 'units', '', false, true),
            BasicFunctions::formEntryEdit($values, 'check', __('admin.server.active'), 'active', '', false, false),
        ];
    }
    
    private function generateShowFormConfig($values) {
        return [
            BasicFunctions::formEntryShow(__('admin.worlds.id'), $values->id),
            BasicFunctions::formEntryShow(__('admin.server.flag'), '<span class="flag-icon flag-icon-'. htmlentities($values->server->flag).
                    '"></span> ['. htmlentities($values->server->code). ']', false),
            BasicFunctions::formEntryShow(__('admin.worlds.name'), $values->name),
            BasicFunctions::formEntryShow(__('admin.worlds.ally_count'), $values->ally_count),
            BasicFunctions::formEntryShow(__('admin.worlds.player_count'), $values->player_count),
            BasicFunctions::formEntryShow(__('admin.worlds.village_count'), $values->village_count),
            BasicFunctions::formEntryShow(__('admin.worlds.url'), $values->url),
            BasicFunctions::formEntryShow(__('admin.worlds.active'),
                    ($values->active == 1)? '<span class="fas fa-check" style="color: green"></span>' :
                    '<span class="fas fa-times" style="color: red"></span>', false),
        ];
    }
}
