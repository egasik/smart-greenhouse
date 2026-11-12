<x-app-layout>
    <x-slot name="header">
        <p class="gh-eyebrow">Теплица</p>
        <h1 class="gh-title">{{ $greenhouse->name }}</h1>
        <p class="gh-subtitle">{{ $greenhouse->location ?: 'Расположение не указано' }}@if($greenhouse->note) · {{ $greenhouse->note }}@endif</p>
    </x-slot>

    <div class="gh-page-grid">
        <a class="gh-link" href="{{ route('dashboard') }}">← Все теплицы</a>
        @if(session('success'))<div class="gh-alert">{{ session('success') }}</div>@endif
        @error('measurement')<p class="gh-error">{{ $message }}</p>@enderror

        <section class="gh-panel gh-panel-pad">
            <div class="gh-panel-heading"><div><h2 class="gh-section-title">Новая посадка</h2><p class="gh-section-note">Выберите проверенную культуру и сорт из справочника.</p></div></div>
            <form method="POST" action="{{ route('plantings.store', $greenhouse) }}" class="gh-form-grid">@csrf
                <label class="gh-field gh-field--wide"><span class="gh-label">Культура и сорт</span><select name="variety_id" class="gh-select" required><option value="">Выберите культуру и сорт</option>@foreach($varieties as $variety)<option value="{{ $variety->id }}">{{ $variety->crop->name }} — {{ $variety->name }}</option>@endforeach</select></label>
                <label class="gh-field"><span class="gh-label">Дата посадки</span><input name="planting_date" type="date" value="{{ old('planting_date', now()->format('Y-m-d')) }}" class="gh-input" required></label>
                <label class="gh-field"><span class="gh-label">Количество, шт.</span><input name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" class="gh-input" required></label>
                <div class="gh-actions gh-field--wide"><button class="gh-button gh-button--primary" type="submit">Добавить посадку</button><span class="gh-section-note">Нет записи? <a class="gh-link" href="{{ route('crops.index') }}">Предложить культуру или сорт</a>.</span></div>
            </form>
        </section>

        <section>
            <div class="gh-panel-heading"><div><h2 class="gh-section-title">Посадки и этапы роста</h2><p class="gh-section-note">Фиксируйте только этапы, допустимые для выбранного сорта.</p></div></div>
            <div class="gh-card-list">
                @forelse($greenhouse->plantings as $planting)
                    <article class="gh-panel gh-panel-pad">
                        <div class="gh-panel-heading"><div><h3 class="gh-section-title">{{ $planting->variety->crop->name }} — {{ $planting->variety->name }}</h3><p class="gh-record-meta">Посадка: {{ $planting->planting_date->format('d.m.Y') }} · {{ $planting->quantity }} шт.@if($planting->growthStages->isNotEmpty()) · Последний этап: {{ $planting->growthStages->first()->definition->name }} ({{ $planting->growthStages->first()->recorded_at->format('d.m.Y') }})@endif</p></div><form method="POST" action="{{ route('plantings.destroy', $planting) }}">@csrf @method('DELETE')<button class="gh-button gh-button--danger" type="submit" onclick="return confirm('Удалить посадку?')">Удалить</button></form></div>
                        <form method="POST" action="{{ route('growth-stages.store', $planting) }}" class="gh-form-grid">@csrf
                            <label class="gh-field"><span class="gh-label">Этап роста</span><select class="gh-select" name="growth_stage_definition_id" required><option value="">Выберите допустимый этап</option>@foreach($planting->variety->growthStages as $stage)<option value="{{ $stage->id }}">{{ $stage->name }}</option>@endforeach</select></label>
                            <label class="gh-field"><span class="gh-label">Состояние</span><select class="gh-select" name="condition"><option value="">Не указано</option><option value="good">Хорошее</option><option value="attention">Требует внимания</option><option value="critical">Критическое</option></select></label>
                            <label class="gh-field"><span class="gh-label">Дата и время</span><input class="gh-input" name="recorded_at" type="datetime-local" value="{{ now()->format('Y-m-d\\TH:i') }}" required></label>
                            <label class="gh-field"><span class="gh-label">Комментарий</span><input class="gh-input" name="comment" placeholder="Необязательно"></label>
                            <div class="gh-actions gh-field--wide"><button class="gh-button gh-button--primary" type="submit">Зафиксировать этап</button></div>
                        </form>
                    </article>
                @empty
                    <div class="gh-empty">В этой теплице пока нет посадок.</div>
                @endforelse
            </div>
        </section>

        <div class="gh-grid gh-grid-2">
            <section class="gh-panel gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">Показатели среды</h2><p class="gh-section-note">Температура и освещённость сохраняются в журнале.</p></div></div><form method="POST" action="{{ route('measurements.store', $greenhouse) }}" class="gh-page-grid">@csrf
                <label class="gh-field"><span class="gh-label">Посадка</span><select class="gh-select" name="planting_id"><option value="">Для всей теплицы</option>@foreach($greenhouse->plantings as $planting)<option value="{{ $planting->id }}">{{ $planting->variety->crop->name }} — {{ $planting->variety->name }}</option>@endforeach</select></label><label class="gh-field"><span class="gh-label">Дата и время</span><input class="gh-input" name="measured_at" type="datetime-local" value="{{ now()->format('Y-m-d\\TH:i') }}" required></label><div class="gh-form-grid"><label class="gh-field"><span class="gh-label">Температура, °C</span><input class="gh-input" name="temperature" type="number" step="any"></label><label class="gh-field"><span class="gh-label">Освещённость, лк</span><input class="gh-input" name="illumination" type="number" step="any"></label></div><button class="gh-button gh-button--primary" type="submit">Сохранить измерение</button></form>
                <div class="gh-card-list mt-5">@forelse($measurements as $measurement)<div class="gh-record"><div class="gh-record-main"><p class="gh-record-title">{{ $measurement->measured_at->format('d.m.Y H:i') }}</p><p class="gh-record-meta">Температура: {{ $measurement->temperature ?? '—' }} °C · Освещённость: {{ $measurement->illumination ?? '—' }} лк</p></div></div>@empty<p class="gh-section-note">Измерений пока нет.</p>@endforelse</div>
            </section>
            <section class="gh-panel gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">План ухода</h2><p class="gh-section-note">Запланируйте полив, подкормку или другую процедуру.</p></div></div><form method="POST" action="{{ route('care-tasks.store', $greenhouse) }}" class="gh-page-grid">@csrf
                <label class="gh-field"><span class="gh-label">Посадка</span><select class="gh-select" name="planting_id" required><option value="">Выберите посадку</option>@foreach($greenhouse->plantings as $planting)<option value="{{ $planting->id }}">{{ $planting->variety->crop->name }} — {{ $planting->variety->name }}</option>@endforeach</select></label><div class="gh-form-grid"><label class="gh-field"><span class="gh-label">Процедура</span><select class="gh-select" name="type" required><option value="">Выберите процедуру</option><option value="watering">Полив</option><option value="fertilizing">Подкормка</option><option value="ventilation">Проветривание</option><option value="treatment">Обработка</option><option value="other">Другое</option></select></label><label class="gh-field"><span class="gh-label">Дата и время</span><input class="gh-input" name="planned_at" type="datetime-local" required></label></div><label class="gh-field"><span class="gh-label">Примечание</span><input class="gh-input" name="description" placeholder="Необязательно"></label><button class="gh-button gh-button--primary" type="submit">Запланировать</button></form>
                <div class="gh-card-list mt-5">@forelse($careTasks as $task)<div class="gh-record"><div class="gh-record-main"><p class="gh-record-title">{{ $task->type }} · {{ $task->planting->variety->crop->name }}</p><p class="gh-record-meta">{{ $task->planned_at->format('d.m.Y H:i') }}</p></div>@if($task->status === 'planned')<form method="POST" action="{{ route('care-tasks.complete', $task) }}">@csrf @method('PATCH')<button class="gh-button" type="submit">Выполнено</button></form>@else<span class="gh-status"><span class="gh-status-dot"></span>Выполнено</span>@endif</div>@empty<p class="gh-section-note">Задач пока нет.</p>@endforelse</div>
            </section>
        </div>
    </div>
</x-app-layout>
