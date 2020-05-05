<?php

namespace App\Http\Controllers\Admin;

use App\BugreportComment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BugreportsCommentController extends Controller
{
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('bugreportComment_create'), 403);
        abort_unless(\Auth::user()->id == $request->user_id, 403);
        
        $request->validate([
            'content' => 'required',
            'bugreport_id' => 'required',
            'user_id' => 'required',
        ]);
        $bugreportComment = BugreportComment::create($request->all());
        $bugreportComment->user_id = \Auth::user()->id;

        return redirect()->route('admin.bugreports.show', [$bugreportComment->bugreport_id]);
    }

    public function update(Request $request)
    {
        abort_unless(\Gate::allows('bugreportComment_edit'), 403);
        
        $request->validate([
            'content' => 'required',
        ]);
        $bugreportComment = BugreportComment::find($request->get('id'));
        abort_unless(\Auth::user()->id == $bugreportComment->user_id, 403);
        $bugreportComment->update($request->all());

        return redirect()->route('admin.bugreports.show', [$bugreportComment->bugreport_id]);
    }

    public function destroy($id)
    {
        abort_unless(\Gate::allows('bugreportComment_delete'), 403);
        abort_unless(\Auth::user()->id == BugreportComment::find($id)->user_id, 403);

        BugreportComment::find($id)->delete();

        return back();
    }

}
