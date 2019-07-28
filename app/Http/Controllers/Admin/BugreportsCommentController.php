<?php

namespace App\Http\Controllers\Admin;

use App\BugreportComment;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBugreportCommentRequest;
use App\Http\Requests\UpdateBugreportCommentRequest;


class BugreportsCommentController extends Controller
{
    public function store(StoreBugreportCommentRequest $request)
    {
        abort_unless(\Gate::allows('bugreportComment_create'), 403);

        $bugreportComment = BugreportComment::create($request->all());

        return redirect()->route('admin.bugreports.show', [$bugreportComment->bugreport_id]);
    }

    public function update(UpdateBugreportCommentRequest $request, BugreportComment $bugreportComment)
    {
        abort_unless(\Gate::allows('bugreportComment_edit'), 403);
        $bugreportComment = BugreportComment::find($request->get('id'));
        $bugreportComment->update($request->all());

        return redirect()->route('admin.bugreports.show', [$bugreportComment->bugreport_id]);
    }

    public function destroy($id)
    {
        abort_unless(\Gate::allows('bugreportComment_delete'), 403);

        BugreportComment::find($id)->delete();

        return back();
    }

}
