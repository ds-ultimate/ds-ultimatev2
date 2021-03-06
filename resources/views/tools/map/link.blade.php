<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row pt-3">
        <div class="col-12">
            <div class="form-group row">
                <label class="control-label col-6 col-md-3 col-lg-2">{{ ucfirst(__('tool.map.editLink')) }}</label>
                <div class="col-6 col-md-1">
                    <a class="float-right btn btn-primary btn-sm" onclick="copy('edit')">{{ ucfirst(__('tool.map.copy')) }}</a>
                </div>
                <div class="col-md-8 col-lg-9">
                    <input id="link-edit" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.mapToolMode', [$wantedMap->id, 'edit', $wantedMap->edit_key]) }}" />
                    <small class="form-control-feedback">{{ ucfirst(__('tool.map.editLinkDesc')) }}</small>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group row">
                <label class="control-label col-6 col-md-3 col-lg-2">{{ ucfirst(__('tool.map.showLink')) }}</label>
                <div class="col-6 col-md-1">
                    <a class="float-right btn btn-primary btn-sm" onclick="copy('show')">{{ ucfirst(__('tool.map.copy')) }}</a>
                </div>
                <div class="col-md-8 col-lg-9">
                    <input id="link-show" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.mapToolMode', [$wantedMap->id, 'show', $wantedMap->show_key]) }}" />
                    <small class="form-control-feedback">{{ ucfirst(__('tool.map.showLinkDesc')) }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
