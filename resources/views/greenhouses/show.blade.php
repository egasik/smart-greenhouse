<x-app-layout>
    <x-slot name="header">
        <div class="gh-panel-heading"><div><a href="{{ route('dashboard') }}" class="gh-eyebrow" style="display:inline-block">← Все теплицы</a><h1 class="gh-title">{{ $greenhouse->name }}</h1><p class="gh-subtitle">{{ $greenhouse->location ?: 'Расположение не указано' }}</p></div><span class="gh-status"><span class="gh-status-dot"></span>Активна</span></div>
    </x-slot>
    <div class="gh-page-grid">
        @if(session('success'))<div class="gh-alert">{{ session('success') }}</div>@endif
        @error('measurement')<div class="gh-error">{{ $message }}</div>@enderror

        <div class="gh-grid gh-grid-3">
            <article class="gh-panel gh-stat"><p class="gh-stat-label">Посадки</p><p class="gh-stat-value">{{ $greenhouse->plantings->count() }}</p><p class="gh-stat-note">активные культуры</p></article>
            <article class="gh-panel gh-stat"><p class="gh-stat-label">Измерения</p><p class="gh-stat-value">{{ $measurements->count() }}</p><p class="gh-stat-note">в журнале</p></article>
            <article class="gh-panel gh-stat"><p class="gh-stat-label">Задачи</p><p class="gh-stat-value">{{ $careTasks->where('status', 'planned')->count() }}</p><p class="gh-stat-note">ожидают выполнения</p></article>
        </div>

        <div class="gh-grid gh-grid-2">
            <section class="gh-panel"><div class="gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">Добавить посадку</h2><p class="gh-section-note">Свяжите культуру с этой теплицей</p></div></div>
                @if($crops->isEmpty())<div class="gh-empty">Сначала <a class="underline" href="{{ route('crops.index') }}">добавьте культуру в справочник</a>.</div>@else
                <form method="POST" action="{{ route('plantings.store', $greenhouse) }}" class="gh-form-grid">@csrf
                    <label class="gh-field"><span class="gh-label">Культура</span><select class="gh-select" name="crop_id" required><option value="">Выберите культуру</option>@foreach($crops as $crop)<option value="{{ $crop->id }}">{{ $crop->name }}{{ $crop->variety ? ' — '.$crop->variety : '' }}</option>@endforeach</select></label>
                    <label class="gh-field"><span class="gh-label">Дата посадки</span><input class="gh-input" name="planting_date" type="date" value="{{ old('planting_date', now()->format('Y-m-d')) }}" required></label>
                    <label class="gh-field"><span class="gh-label">Количество</span><input class="gh-input" name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" required></label>
                    <div class="gh-actions gh-field--wide"><button class="gh-button gh-button--primary">Добавить посадку</button></div>
                </form>@endif
            </div></section>
            <section class="gh-panel"><div class="gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">Записать показатели</h2><p class="gh-section-note">Температура и освещённость</p></div></div>
                <form method="POST" action="{{ route('measurements.store', $greenhouse) }}" class="gh-form-grid">@csrf
                    <label class="gh-field"><span class="gh-label">Посадка</span><select class="gh-select" name="planting_id"><option value="">Вся теплица</option>@foreach($greenhouse->plantings as $planting)<option value="{{ $planting->id }}">{{ $planting->crop->name }}</option>@endforeach</select></label>
                    <label class="gh-field"><span class="gh-label">Дата и время</span><input class="gh-input" name="measured_at" type="datetime-local" value="{{ now()->format('Y-m-d\\TH:i') }}" required></label>
                    <label class="gh-field"><span class="gh-label">Температура, °C</span><input class="gh-input" name="temperature" type="number" step="0.01" placeholder="Например, 24.5"></label>
                    <label class="gh-field"><span class="gh-label">Освещённость, лк</span><input class="gh-input" name="illumination" type="number" step="0.01" placeholder="Например, 7200"></label>
                    <div class="gh-actions gh-field--wide"><button class="gh-button gh-button--primary">Сохранить показатели</button></div>
                </form>
            </div></section>
        </div>

        <section class="gh-panel"><div class="gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">Посадки и этапы роста</h2><p class="gh-section-note">Фиксируйте текущую фазу развития растения</p></div></div>
            <div class="gh-card-list">@forelse($greenhouse->plantings as $planting)<article class="gh-record"><div class="gh-record-main"><h3 class="gh-record-title">{{ $planting->crop->name }}{{ $planting->crop->variety ? ' — '.$planting->crop->variety : '' }}</h3><p class="gh-record-meta">Посажено {{ $planting->planting_date->format('d.m.Y') }} · {{ $planting->quantity }} шт.</p>@if($planting->growthStages->isNotEmpty())<p class="gh-record-meta">Текущий этап: {{ $planting->growthStages->first()->stage }} · {{ $planting->growthStages->first()->recorded_at->format('d.m.Y') }}</p>@endif</div><form method="POST" action="{{ route('plantings.destroy', $planting) }}">@csrf @method('DELETE')<button class="gh-button gh-button--danger" type="submit" onclick="return confirm('Удалить посадку?')">Удалить</button></form></article>
                <form method="POST" action="{{ route('growth-stages.store', $planting) }}" class="gh-form-grid" style="padding: 0 16px 16px;">@csrf
                    <label class="gh-field"><span class="gh-label">Этап</span><select class="gh-select" name="stage" required><option value="">Выберите этап</option><option>Посев</option><option>Всходы</option><option>Рост</option><option>Цветение</option><option>Плодоношение</option><option>Сбор урожая</option></select></label>
                    <label class="gh-field"><span class="gh-label">Дата и время</span><input class="gh-input" name="recorded_at" type="datetime-local" value="{{ now()->format('Y-m-d\\TH:i') }}" required></label>
                    <label class="gh-field"><span class="gh-label">Состояние</span><input class="gh-input" name="status" placeholder="Например, хорошее"></label>
                    <label class="gh-field"><span class="gh-label">Комментарий</span><input class="gh-input" name="comment" placeholder="Необязательно"></label>
                    <div class="gh-actions gh-field--wide"><button class="gh-button">Зафиксировать этап</button></div>
                </form>@empty<div class="gh-empty">В этой теплице пока нет посадок.</div>@endforelse</div>
        </div></section>

        <div class="gh-grid gh-grid-2">
            <section class="gh-panel"><div class="gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">Последние показатели</h2><p class="gh-section-note">Журнал измерений</p></div></div><div class="gh-card-list">@forelse($measurements as $measurement)<article class="gh-record"><div class="gh-record-main"><h3 class="gh-record-title">{{ $measurement->measured_at->format('d.m.Y H:i') }}</h3><p class="gh-record-meta">Температура: {{ $measurement->temperature !== null ? $measurement->temperature.' °C' : 'не указана' }} · Освещённость: {{ $measurement->illumination !== null ? $measurement->illumination.' лк' : 'не указана' }}</p></div></article>@empty<div class="gh-empty">Измерений пока нет.</div>@endforelse</div></div></section>
            <section class="gh-panel"><div class="gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">План ухода</h2><p class="gh-section-note">Задачи для культур</p></div></div>
                @if($greenhouse->plantings->isNotEmpty())<form method="POST" action="{{ route('care-tasks.store', $greenhouse) }}" class="gh-form-grid">@csrf
                    <label class="gh-field"><span class="gh-label">Посадка</span><select class="gh-select" name="planting_id" required><option value="">Выберите посадку</option>@foreach($greenhouse->plantings as $planting)<option value="{{ $planting->id }}">{{ $planting->crop->name }}</option>@endforeach</select></label>
                    <label class="gh-field"><span class="gh-label">Процедура</span><select class="gh-select" name="type" required><option value="">Выберите процедуру</option><option>Полив</option><option>Подкормка</option><option>Проветривание</option><option>Обработка</option><option>Другое</option></select></label>
                    <label class="gh-field"><span class="gh-label">Срок</span><input class="gh-input" name="planned_at" type="datetime-local" required></label>
                    <label class="gh-field"><span class="gh-label">Примечание</span><input class="gh-input" name="description" placeholder="Необязательно"></label>
                    <div class="gh-actions gh-field--wide"><button class="gh-button gh-button--primary">Запланировать</button></div>
                </form>@else<div class="gh-empty">Сначала добавьте посадку.</div>@endif
                <div class="gh-card-list mt-5">@forelse($careTasks as $task)<article class="gh-record"><div class="gh-record-main"><h3 class="gh-record-title">{{ $task->type }} — {{ $task->planting->crop->name }}</h3><p class="gh-record-meta">{{ $task->planned_at->format('d.m.Y H:i') }}@if($task->description) · {{ $task->description }}@endif</p></div>@if($task->status === 'planned')<form method="POST" action="{{ route('care-tasks.complete', $task) }}">@csrf @method('PATCH')<button class="gh-button">Выполнено</button></form>@else<span class="gh-status"><span class="gh-status-dot"></span>Выполнено</span>@endif</article>@empty<div class="gh-empty">Запланированных процедур пока нет.</div>@endforelse</div>
            </div></section>
        </div>
    </div>
</x-app-layout>
