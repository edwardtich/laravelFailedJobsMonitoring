<div class="fjm-table-wrap">
    <table class="fjm-table">
        <thead>
        <tr>
            @if(config('failed-jobs-manager.ui.allow_retry'))
                <th>
                    <input type="checkbox" onclick="document.querySelectorAll('input[name=&quot;ids[]&quot;]').forEach(function (checkbox) { checkbox.checked = this.checked; }.bind(this))">
                </th>
            @endif
            <th>ID</th>
            <th>{{ __('failed-jobs-manager::failed-jobs-manager.filters.queue') }}</th>
            <th>{{ __('failed-jobs-manager::failed-jobs-manager.table.payload') }}</th>
            <th>{{ __('failed-jobs-manager::failed-jobs-manager.table.exception') }}</th>
            <th>{{ __('failed-jobs-manager::failed-jobs-manager.table.failed_at') }}</th>
            <th>{{ __('failed-jobs-manager::failed-jobs-manager.table.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($jobs as $job)
            <tr>
                @if(config('failed-jobs-manager.ui.allow_retry'))
                    <td>
                        <input type="checkbox" name="ids[]" value="{{ $job->id }}" form="failed-jobs-bulk-retry-form">
                    </td>
                @endif
                <td><span class="fjm-id">{{ $job->id }}</span></td>
                <td><span class="fjm-badge">{{ $job->queue ?: 'default' }}</span></td>
                <td>
                    @php($payload = $job->payloadDetails())
                    <div class="fjm-preview-title">{{ $payload->shortJobName() }}</div>
                    <div class="fjm-preview-muted">{{ $payload->uuid() ?: __('failed-jobs-manager::failed-jobs-manager.table.no_uuid') }}</div>
                </td>
                <td>
                    <div class="fjm-preview-error">{{ $job->getExceptionPreview() }}</div>
                </td>
                <td>{{ $job->failed_at?->format('d.m.Y H:i:s') }}</td>
                <td>
                    <div class="fjm-actions">
                        <a href="{{ route('failed-jobs-manager::show', $job->id) }}" class="fjm-button fjm-button-blue">
                            {{ __('failed-jobs-manager::failed-jobs-manager.actions.details') }}
                        </a>

                        @if(config('failed-jobs-manager.ui.allow_retry'))
                            <form action="{{ route('failed-jobs-manager::retry', $job->id) }}" method="post" class="fjm-inline-form">
                                @csrf
                                @method('patch')
                                <button type="submit" class="fjm-button fjm-button-orange">
                                    {{ __('failed-jobs-manager::failed-jobs-manager.actions.retry') }}
                                </button>
                            </form>
                        @endif

                        @if(config('failed-jobs-manager.ui.allow_delete'))
                            <form action="{{ route('failed-jobs-manager::destroy', $job->id) }}" method="post" class="fjm-inline-form">
                                @csrf
                                @method('delete')
                                <button type="submit" class="fjm-button fjm-button-red" onclick="return confirm('{{ __('failed-jobs-manager::failed-jobs-manager.actions.delete_confirm') }}')">
                                    {{ __('failed-jobs-manager::failed-jobs-manager.actions.delete') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100">{{ __('failed-jobs-manager::failed-jobs-manager.table.empty') }}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
