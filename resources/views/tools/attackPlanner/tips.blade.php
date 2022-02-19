<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row p-3">
        <table class="table table-striped">
            <tr>
                <th style="width: 300px">{{ __('tool.attackPlanner.hints.selectMultiple.title') }}</th>
                <td>{!! __('tool.attackPlanner.hints.selectMultiple.desc') !!}</td>
            </tr>
            <tr>
                <th style="width: 300px">{{ __('tool.attackPlanner.hints.workbenchImport.title') }}</th>
                <td>{!! __('tool.attackPlanner.hints.workbenchImport.desc') !!}</td>
            </tr>
        </table>
    </div>
</div>
