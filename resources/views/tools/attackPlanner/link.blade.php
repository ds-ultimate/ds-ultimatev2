<div class="tab-pane fade" id="link" role="tabpanel" aria-labelledby="link-tab">
    <div class="row pt-3">
        <div class="col-12">
            <div class="form-group row">
                <label class="control-label col-md-2">{{ __('tool.attackPlanner.editLink') }}</label>
                <div class="col-md-1">
                    <button class="btn btn-primary btn-sm" onclick="copy('link-edit')">{{ __('global.datatables.copy') }}</button>
                </div>
                <div class="col-md-9">
                    <input id="link-edit" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.attackPlannerMode', [$attackList->id, 'edit', $attackList->edit_key]) }}" />
                    <small class="form-control-feedback">{{ __('tool.attackPlanner.editLink_helper') }}</small>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group row">
                <label class="control-label col-md-2">{{ __('tool.attackPlanner.showLink') }}</label>
                <div class="col-md-1">
                    <button class="btn btn-primary btn-sm" onclick="copy('link-show')">{{ __('global.datatables.copy') }}</button>
                </div>
                <div class="col-md-9">
                    <input id="link-show" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.attackPlannerMode', [$attackList->id, 'show', $attackList->show_key]) }}" />
                    <small class="form-control-feedback">{{ __('tool.attackPlanner.showLink_helper') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
