<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row pt-3">
        <div class="col-12">
            <div class="form-group">
                <label class="control-label mr-3">{{ __('tool.attackPlanner.exportDSU') }}</label>
                <div class="export-show-field d-none">
                    <button class="btn btn-primary btn-sm" onclick="copy('exportDSU')">{{ __('global.datatables.copy') }}</button>
                    <textarea id="exportDSU" class="form-control form-control-sm" style="height: 300px" rows="1"></textarea>
                </div>
                <div class="export-show-control">
                    <button class="btn btn-primary btn-sm" data-target="exportDSU">{{ __('global.show') }}</button>
                    <h1 class="loading"><i class="fas fa-spinner fa-spin"></i></h1>
                </div>
                <small class="form-control-feedback">{{ __('tool.attackPlanner.exportDSUDesc') }}</small>
            </div>
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
            <form id="importDSUItemsForm">
                @csrf
                <div class="form-group">
                    <input type="submit" class="btn btn-sm btn-success float-right" value="{{ __('tool.attackPlanner.import') }}">
                    <label class="control-label mr-3">{{ __('tool.attackPlanner.importDSU') }}</label>
                    <textarea id="importDSU" class="form-control form-control-sm" style="height: 80px"></textarea>
                    <small class="form-control-feedback">{{ __('tool.attackPlanner.importDSU_helper') }}</small>
                </div>
            </form>
            <form id="importWBItemsForm">
                @csrf
                <div class="form-group">
                    <label class="control-label mr-3">{{ __('tool.attackPlanner.importWB') }}</label>
                    <input type="submit" class="btn btn-sm btn-success float-right" value="{{ __('tool.attackPlanner.import') }}">
                    <textarea id="importWB" class="form-control form-control-sm" style="height: 80px"></textarea>
                    <small class="form-control-feedback">{{ __('tool.attackPlanner.importWB_helper') }}</small>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).on('submit', '#importDSUItemsForm', async function (e) {
        e.preventDefault();

        var rawInput = $('#importDSU').val();
        if (!rawInput) {
            createToast('{{ __('tool.attackPlanner.errorInvalidJSON') }}', '{{ __('tool.attackPlanner.errorTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger');
            return;
        }

        let parsedData;
        try {
            parsedData = JSON.parse(rawInput);
        } catch (e) {
            createToast('{{ __('tool.attackPlanner.errorInvalidJSON') }}', '{{ __('tool.attackPlanner.errorTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger');
            return;
        }

        if (!parsedData.items || !Array.isArray(parsedData.items)) {
            createToast('{{ __('tool.attackPlanner.errorInvalidJSON') }}', '{{ __('tool.attackPlanner.errorTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger');
            return;
        }

        var items = parsedData.items;
        var chunkSize = {{ \App\Http\Controllers\Tools\AttackPlannerController::MAX_IMPORT_ITEMS }};

        var chunks = [];
        for (var i = 0; i < items.length; i += chunkSize) {
            chunks.push(items.slice(i, i + chunkSize));
        }

        if (items.length < 1) {
            chunks.push([]);
        }

        var objectWithoutItems = parsedData;
        objectWithoutItems.items = [];

        try {
            for (let index = 0; index < chunks.length; index++) {
                let payload = Object.assign({}, objectWithoutItems);
                payload.items = chunks[index];

                let response = await axios.post('{{ route('tools.attackPlannerMode', [$attackList->id, 'importDSU', $attackList->edit_key]) }}', payload)
                let data = response.data;

                createToast(data['msg'], data['title'] + ` (${index + 1}/${chunks.length})`, '{{ __('global.now') }}',
                    data['data'] === 'success'
                        ? 'fas fa-check-circle text-success'
                        : 'fas fa-exclamation-circle text-danger'
                );

                if (data['data'] !== 'success') {
                    throw new Error('Server returned error status.');
                }
            }
            // Only executed after ALL chunks succeed
            $('#importDSU').val('');
            reloadData(true);
        } catch (error) {
            console.error('Upload stopped due to error:', error);
            createToast('{{ __('tool.attackPlanner.importError') }}', '{{ __('tool.attackPlanner.errorTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger');
        }
    });

    $(document).on('submit', '#importWBItemsForm', function (e) {
        e.preventDefault();

        var importWB = $('#importWB');
        axios.post('{{ route('tools.attackPlannerMode', [$attackList->id, 'importWB', $attackList->edit_key]) }}', {
            'import': importWB.val(),
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