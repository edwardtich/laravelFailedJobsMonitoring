<form action="{{ route('failed-jobs-manager::index') }}" method="get">
    <div class="fjm-panel">
        <div class="fjm-form-grid">
            <div class="fjm-field">
                <label>Очередь</label>
                <select name="queue" class="fjm-control" onchange="this.form.submit()">
                    <option value="">не выбрано</option>
                    @foreach($queues as $queue)
                        <option value="{{ $queue }}" @if(request('queue') === $queue) selected @endif>{{ $queue }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fjm-field">
                <label>Поиск</label>
                <input class="fjm-control" name="search" value="{{ request('search') }}" placeholder="payload или exception"/>
            </div>
            <div class="fjm-field">
                <label>Дата с</label>
                <input class="fjm-control" name="failed_from" value="{{ request('failed_from') }}" type="date"/>
            </div>
            <div class="fjm-field">
                <label>Дата по</label>
                <input class="fjm-control" name="failed_to" value="{{ request('failed_to') }}" type="date"/>
            </div>
            <div class="fjm-field">
                <label>Найти</label>
                <button class="fjm-button fjm-button-green" type="submit">Найти</button>
            </div>
            <div class="fjm-field">
                <label>Сброс</label>
                <a href="{{ route('failed-jobs-manager::index') }}" title="Сброс" class="fjm-button fjm-button-red">Сброс</a>
            </div>
        </div>
    </div>
</form>
