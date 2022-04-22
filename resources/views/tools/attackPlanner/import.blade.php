<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row pt-3">
        <div class="col-12">
            <div class="form-group">
                <label class="control-label mr-3">{{ __('tool.attackPlanner.exportWB') }}</label> <button class="btn btn-primary btn-sm" onclick="copy('exportWB')">{{ __('global.datatables.copy') }}</button>
                <textarea id="exportWB" class="form-control form-control-sm" rows="1"></textarea>
                <small class="form-control-feedback">{{ __('tool.attackPlanner.exportWBDesc') }}</small>
            </div>
            <div class="form-group">
                <label class="control-label mr-3">{{ __('tool.attackPlanner.exportBB') }}</label> <button class="btn btn-primary btn-sm" onclick="copy('exportBB')">{{ __('global.datatables.copy') }}</button>
                <textarea id="exportBB" class="form-control form-control-sm" rows="1"></textarea>
                <small class="form-control-feedback">{{ __('tool.attackPlanner.exportBBDesc') }}</small>
            </div>
            <div class="form-group">
                <label class="control-label mr-3">{{ __('tool.attackPlanner.exportIGM') }}</label> <button class="btn btn-primary btn-sm" onclick="copy('exportIGM')">{{ __('global.datatables.copy') }}</button>
                <textarea id="exportIGM" class="form-control form-control-sm" rows="1"></textarea>
                <small class="form-control-feedback">{{ __('tool.attackPlanner.exportIGMDesc') }}</small>
            </div>
            <form id="importItemsForm">
                @csrf
                <div class="form-group">
                    <label class="control-label mr-3">{{ __('tool.attackPlanner.import') }}</label>
                    <textarea id="importWB" class="form-control form-control-sm" style="height: 80px"></textarea>
                    <small class="form-control-feedback">{{ __('tool.attackPlanner.import_helper') }}</small>
                </div>
                <div class="col-12">
                    <input type="submit" class="btn btn-sm btn-success float-right" value="{{ __('tool.attackPlanner.import') }}">
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).on('submit', '#importItemsForm', function (e) {
        e.preventDefault();
        
        var importWB = $('#importWB');
        axios.post('{{ route('tools.attackPlannerMode', [$attackList->id, 'importWB', $attackList->edit_key]) }}', {
            'import': importWB.val(),
            'key': '{{$attackList->edit_key}}',
        })
            .then((response) => {
                importWB.val('');
                var data = response.data;
                reloadData();
                createToast(data['msg'], data['title'], '{{ __('global.now') }}', data['data'] === 'success'? 'fas fa-check-circle text-success' :'fas fa-exclamation-circle text-danger')
            })
            .catch((error) => {

            });
    });

    function updateExports() {
        axios.get('{{ route('tools.attackPlannerMode', [$attackList->id, 'exportAll', $attackList->edit_key]) }}', {
        })
            .then((response) => {
                $('#exportWB').val(response.data.wb);
                $('#exportBB').val(response.data.bb);
                $('#exportIGM').val(response.data.igm);
            })
            .catch((error) => {

            });
    }
</script>
@endpush