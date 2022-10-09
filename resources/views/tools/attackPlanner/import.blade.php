<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row pt-3">
        <div class="col-12">
            <div class="form-group">
                <label class="control-label mr-3">{{ __('tool.attackPlanner.exportWB') }}</label>
                <div class="export-show-field d-none">
                    <button class="btn btn-primary btn-sm" onclick="copy('exportWB')">{{ __('global.datatables.copy') }}</button>
                    <textarea id="exportWB" class="form-control form-control-sm" style="height: 300px" rows="1"></textarea>
                </div>
                <div class="export-show-control">
                    <button class="btn btn-primary btn-sm" data-target="exportWB">{{ __('global.show') }}</button>
                    <h1 class="loading"><i class="fas fa-spinner fa-spin"></i></h1>
                </div>
                <small class="form-control-feedback">{{ __('tool.attackPlanner.exportWBDesc') }}</small>
            </div>
            <div class="form-group">
                <label class="control-label mr-3">{{ __('tool.attackPlanner.exportBB') }}</label>
                <div class="export-show-field d-none">
                    <button class="btn btn-primary btn-sm" onclick="copy('exportBB')">{{ __('global.datatables.copy') }}</button>
                    <textarea id="exportBB" class="form-control form-control-sm" style="height: 300px" rows="1"></textarea>
                </div>
                <div class="export-show-control">
                    <button class="btn btn-primary btn-sm" data-target="exportBB">{{ __('global.show') }}</button>
                    <h1 class="loading"><i class="fas fa-spinner fa-spin"></i></h1>
                </div>
                <small class="form-control-feedback">{{ __('tool.attackPlanner.exportBBDesc') }}</small>
            </div>
            <div class="form-group">
                <label class="control-label mr-3">{{ __('tool.attackPlanner.exportIGM') }}</label>
                <div class="export-show-field d-none">
                    <button class="btn btn-primary btn-sm" onclick="copy('exportIGM')">{{ __('global.datatables.copy') }}</button>
                    <textarea id="exportIGM" class="form-control form-control-sm" style="height: 300px" rows="1"></textarea>
                </div>
                <div class="export-show-control">
                    <button class="btn btn-primary btn-sm" data-target="exportIGM">{{ __('global.show') }}</button>
                    <h1 class="loading"><i class="fas fa-spinner fa-spin"></i></h1>
                </div>
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
                reloadData(true);
                createToast(data['msg'], data['title'], '{{ __('global.now') }}', data['data'] === 'success'? 'fas fa-check-circle text-success' :'fas fa-exclamation-circle text-danger')
            })
            .catch((error) => {

            });
    });

    function updateExports() {
        $('.export-show-control').removeClass('d-none')
        $('.export-show-control .btn').removeClass('d-none')
        $('.export-show-control .loading').addClass('d-none')
        $('.export-show-field').removeClass('d-inline')
        $('.export-show-field').addClass('d-none')
    }
    
    $(function () {
        $('.export-show-control .btn').on('click', function() {
            $(this).addClass('d-none')
            $(".loading", $(this).parent()).removeClass('d-none')
            
            var target = $(this).attr('data-target')
            axios.get('{{ route('tools.attackPlannerMode', [$attackList->id, '%TYPE%', $attackList->edit_key]) }}'
                    .replace('%TYPE%', target))
                .then((response) => {
                    $('#' + target).val(response.data.data);
                    $(this).parent().addClass('d-none')
                    var showField = $('.export-show-field', $(this).parent().parent())
                    showField.removeClass('d-none')
                    showField.addClass('d-inline')
                })
                .catch((error) => {

                });
        })
    })
</script>
@endpush