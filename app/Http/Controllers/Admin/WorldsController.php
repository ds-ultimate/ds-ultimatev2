<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyWorldRequest;
use App\Http\Requests\StoreWorldRequest;
use App\Http\Requests\UpdateWorldRequest;
use App\World;
use Illuminate\Support\Carbon;

class WorldsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('world_access'), 403);

        $worlds = World::all();

        $now = Carbon::createFromTimestamp(time());

        return view('admin.worlds.index', compact('worlds', 'now'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('world_create'), 403);

        return view('admin.worlds.create');
    }

    public function store(StoreWorldRequest $request)
    {
        abort_unless(\Gate::allows('world_create'), 403);

        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);

        $world = World::create($request->all());

        return redirect()->route('admin.worlds.index');
    }

    public function edit(World $world)
    {
        abort_unless(\Gate::allows('world_edit'), 403);

        return view('admin.worlds.edit', compact('world'));
    }

    public function update(UpdateWorldRequest $request, World $world)
    {
        abort_unless(\Gate::allows('world_edit'), 403);
        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);

        $world->update($request->all());

        return redirect()->route('admin.worlds.index');
    }

    public function show(World $world)
    {
        abort_unless(\Gate::allows('world_show'), 403);

        return view('admin.worlds.show', compact('world'));
    }

    public function destroy(World $world)
    {
        abort_unless(\Gate::allows('world_delete'), 403);

        $world->delete();

        return back();
    }

    public function massDestroy(MassDestroyWorldRequest $request)
    {
        World::whereIn('id', $request->input('ids'))->delete();

        return response(null, 204);
    }
}
