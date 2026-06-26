<form action="{{ route('failed-jobs-manager::index') }}" method="get">
    <div class="fjm-panel">
        <div class="fjm-form-grid">
            <div class="fjm-field">
                <label>{{ __('failed-jobs-manager::failed-jobs-manager.filters.queue') }}</label>
                <select name="queue" class="fjm-control" onchange="this.form.submit()">
                    <option value="">{{ __('failed-jobs-manager::failed-jobs-manager.filters.not_selected') }}</option>
                    @foreach($queues as $queue)
                        <option value="{{ $queue }}" @if(request('queue') === $queue) selected @endif>{{ $queue }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fjm-field">
                <label>{{ __('failed-jobs-manager::failed-jobs-manager.filters.search') }}</label>
                <input class="fjm-control" name="search" value="{{ request('search') }}" placeholder="{{ __('failed-jobs-manager::failed-jobs-manager.filters.search_placeholder') }}"/>
            </div>
            <div class="fjm-field">
                <label>{{ __('failed-jobs-manager::failed-jobs-manager.filters.failed_from') }}</label>
                <input class="fjm-control" name="failed_from" value="{{ request('failed_from') }}" type="date"/>
            </div>
            <div class="fjm-field">
                <label>{{ __('failed-jobs-manager::failed-jobs-manager.filters.failed_to') }}</label>
                <input class="fjm-control" name="failed_to" value="{{ request('failed_to') }}" type="date"/>
            </div>
            <div class="fjm-field">
                <label>{{ __('failed-jobs-manager::failed-jobs-manager.filters.submit') }}</label>
                <button class="fjm-button fjm-button-green" type="submit">{{ __('failed-jobs-manager::failed-jobs-manager.filters.submit') }}</button>
            </div>
            <div class="fjm-field">
                <label>{{ __('failed-jobs-manager::failed-jobs-manager.filters.reset') }}</label>
                <a href="{{ route('failed-jobs-manager::index') }}" title="{{ __('failed-jobs-manager::failed-jobs-manager.filters.reset') }}" class="fjm-button fjm-button-red">
                    {{ __('failed-jobs-manager::failed-jobs-manager.filters.reset') }}
                </a>
            </div>
        </div>
    </div>
</form>
