<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangelogRequest;
use App\Http\Requests\MassDestroyChangelogRequest;
use App\Http\Requests\StoreChangelogRequest;
use App\Http\Requests\UpdateChangelogRequest;
use App\Changelog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ChangelogsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('changelog_access'), 403);

        $changelogs = Changelog::all();

        return view('admin.changelogs.index', compact('changelogs'));
    }


    public function create()
    {
        abort_unless(\Gate::allows('changelog_create'), 403);

        return view('admin.changelogs.create');
    }

    public function store(StoreChangelogRequest $request)
    {
        abort_unless(\Gate::allows('changelog_create'), 403);

        $changelog = Changelog::create($request->all());

        return redirect()->route('admin.changelogs.index');
    }

    public function edit(Changelog $changelog)
    {
        abort_unless(\Gate::allows('changelog_edit'), 403);

        return view('admin.changelogs.edit', compact('changelog'));
    }

    public function update(UpdateChangelogRequest $request, Changelog $changelog)
    {
        abort_unless(\Gate::allows('changelog_edit'), 403);

        $changelog->update($request->all());

        return redirect()->route('admin.changelogs.index');
    }

    public function show(Changelog $changelog)
    {
        abort_unless(\Gate::allows('changelog_show'), 403);

        return view('admin.changelogs.show', compact('changelog'));
    }

    public function destroy(Changelog $changelog)
    {
        abort_unless(\Gate::allows('changelog_delete'), 403);

        $changelog->delete();

        return back();
    }

    public function massDestroy(MassDestroyChangelogRequest $request)
    {
        Changelog::whereIn('id', $request->input('ids'))->delete();

        return response(null, 204);
    }

}
