<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BugreportRequest;
use App\Http\Requests\MassDestroyBugreportRequest;
use App\Http\Requests\StoreBugreportRequest;
use App\Http\Requests\UpdateBugreportRequest;
use App\Bugreport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BugreportsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('bugreport_access'), 403);

        $bugreports = Bugreport::orderBy('firstSeen')->get();

        return view('admin.bugreports.index', compact('bugreports'));
    }

    public function indexPriority($priority)
    {
        abort_unless(\Gate::allows('bugreport_access'), 403);

        $bugreports = Bugreport::where('priority', $priority)->orderBy('firstSeen')->get();

        return view('admin.bugreports.index', compact('bugreports'));
    }

    public function indexStatus($status)
    {
        abort_unless(\Gate::allows('bugreport_access'), 403);

        $bugreports = Bugreport::where('status', $status)->orderBy('firstSeen')->get();

        return view('admin.bugreports.index', compact('bugreports'));
    }
    public function indexNew()
    {
        abort_unless(\Gate::allows('bugreport_access'), 403);

        $bugreports = Bugreport::where('firstSeen', null)->orderBy('firstSeen')->get();

        return view('admin.bugreports.index', compact('bugreports'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('bugreport_create'), 403);

        return view('admin.bugreports.create');
    }

    public function store(StoreBugreportRequest $request)
    {
        abort_unless(\Gate::allows('bugreport_create'), 403);

        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);

        $bugreport = Bugreport::create($request->all());

        return redirect()->route('admin.bugreports.index');
    }

    public function edit(Bugreport $bugreport)
    {
        abort_unless(\Gate::allows('bugreport_edit'), 403);

        return view('admin.bugreports.edit', compact('bugreport'));
    }

    public function update(UpdateBugreportRequest $request, Bugreport $bugreport)
    {
        abort_unless(\Gate::allows('bugreport_edit'), 403);

        ($request->status != $bugreport->status)?(($request->status == 2 || $request->status == 3)? $bugreport->delivery = Carbon::now() : $bugreport->delivery = null) : null;

        $bugreport->update($request->all());

        return redirect()->route('admin.bugreports.index');
    }

    public function show(Bugreport $bugreport)
    {
        abort_unless(\Gate::allows('bugreport_show'), 403);

        if ($bugreport->firstSeen === null) {
            $bugreport->firstSeenUser = Auth::user()->id;
            $bugreport->firstSeen = Carbon::now();
            $bugreport->save();
        }

        return view('admin.bugreports.show', compact('bugreport'));
    }

    public function destroy(Bugreport $bugreport)
    {
        abort_unless(\Gate::allows('bugreport_delete'), 403);

        $bugreport->delete();

        return back();
    }

    public function massDestroy(MassDestroyBugreportRequest $request)
    {
        Bugreport::whereIn('id', $request->input('ids'))->delete();

        return response(null, 204);
    }

}
