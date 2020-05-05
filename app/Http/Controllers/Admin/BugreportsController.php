<?php

namespace App\Http\Controllers\Admin;

use App\Bugreport;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        
        $formEntries = $this->generateEditFormConfig(null);
        $route = route("admin.bugreports.store");
        $header = __('user.bugreport.title');
        $method = "POST";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('bugreport_create'), 403);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'title' => 'required',
            'priority' => 'required|integer',
            'description' => 'required',
            'status' => 'required|integer',
        ]);
        ($request->active === 'on')? $request->merge(['active' => 1]) : $request->merge(['active' => 0]);

        $bugreport = Bugreport::create($request->all());

        return redirect()->route('admin.bugreports.index');
    }

    public function edit(Bugreport $bugreport)
    {
        abort_unless(\Gate::allows('bugreport_edit'), 403);

        $formEntries = $this->generateEditFormConfig($bugreport);
        $route = route("admin.bugreports.update", [$bugreport->id]);
        $header = __('admin.bugreports.update');
        $method = "PUT";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function update(Request $request, Bugreport $bugreport)
    {
        abort_unless(\Gate::allows('bugreport_edit'), 403);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'title' => 'required',
            'priority' => 'required|integer',
            'description' => 'required',
            'status' => 'required|integer',
        ]);
        ($request->status != $bugreport->status)?(($request->status == 2 || $request->status == 3)? $bugreport->delivery = Carbon::now() : $bugreport->delivery = null) : null;

        $bugreport->update($request->all());

        return redirect()->route('admin.bugreports.index');
    }

    public function show(Bugreport $bugreport)
    {
        abort_unless(\Gate::allows('bugreport_show'), 403);

        if ($bugreport->firstSeen === null) {
            $bugreport->firstSeenUser_id = Auth::user()->id;
            $bugreport->firstSeen = Carbon::now();
            $bugreport->save();
        }
        
        $formEntries = $this->generateShowFormConfig($bugreport);
        $header = __('admin.bugreports.show');
        $title = $bugreport->title;
        return view('admin.bugreports.show', compact('bugreport', 'formEntries', 'header', 'title'));
    }

    public function destroy(Bugreport $bugreport)
    {
        abort_unless(\Gate::allows('bugreport_delete'), 403);

        $bugreport->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        abort_unless(\Gate::allows('bugreport_delete'), 403);
        
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:bugreports,id',
        ]);
        Bugreport::whereIn('id', $request->input('ids'))->delete();

        return response(null, 204);
    }
    
    private function generateEditFormConfig($values) {
        return [
            BasicFunctions::formEntryEdit($values, 'text', __('user.bugreport.name'), 'name', Auth::user()->name, true, true),
            BasicFunctions::formEntryEdit($values, 'text', __('user.bugreport.email'), 'email', Auth::user()->email, true, true),
            BasicFunctions::formEntryEdit($values, 'text', __('user.bugreport.form_title'), 'title', null, false, true),
            BasicFunctions::formEntryEdit($values, 'select', __('user.bugreport.priority'), 'priority', 0, false, true, [
                'options' => [
                    '0' => __('user.bugreport.prioritySelect.low'),
                    '1' => __('user.bugreport.prioritySelect.normal'),
                    '2' => __('user.bugreport.prioritySelect.high'),
                    '3' => __('user.bugreport.prioritySelect.critical'),
                ],
                'multiple' => false,
            ]),
            BasicFunctions::formEntryEdit($values, 'select', __('admin.bugreport.status'), 'status', 0, false, true, [
                'options' => [
                    '0' => __('admin.bugreport.statusSelect.open'),
                    '1' => __('admin.bugreport.statusSelect.inprogress'),
                    '2' => __('admin.bugreport.statusSelect.resolved'),
                    '3' => __('admin.bugreport.statusSelect.close'),
                ],
                'multiple' => false,
            ]),
            BasicFunctions::formEntryEdit($values, 'text', __('user.bugreport.description'), 'description', null, false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('user.bugreport.url'), 'url', null, false, true),
        ];
    }
    
    private function generateShowFormConfig($values) {
        return [
            BasicFunctions::formEntryShow(__('user.bugreport.name'), $values->name),
            BasicFunctions::formEntryShow(__('user.bugreport.email'), $values->email),
            BasicFunctions::formEntryShow(__('user.bugreport.priority'), $values->getPriorityBadge(), false),
            BasicFunctions::formEntryShow(__('admin.bugreport.status'), $values->getStatusBadge(), false),
            BasicFunctions::formEntryShow(__('admin.bugreport.created_at'),
                    "{$values->created_at} || {$values->created_at->diffForHumans()}"),
            BasicFunctions::formEntryShow(__('user.bugreport.url'), $values->url),
            BasicFunctions::formEntryShow(__('user.bugreport.description'), $values->description),
            BasicFunctions::formEntryShow(__('admin.bugreport.seen'),
                    "{$values->firstSeenUser->name} || {$values->firstSeen} || {$values->firstSeen->diffForHumans()}"),
        ];
    }
}
