<x-app-layout>
    <x-slot name="header">
        <p class="gh-eyebrow">Рабочее пространство</p>
        <h1 class="gh-title">Мои теплицы</h1>
        <p class="gh-subtitle">Создавайте отдельные пространства для культур, показателей и процедур ухода.</p>
    </x-slot>

    <div class="gh-page-grid">
        @if (session('success'))<div class="gh-alert">{{ session('success') }}</div>@endif

        <div class="gh-grid gh-grid-3">
            <article class="gh-panel gh-stat"><p class="gh-stat-label">Теплицы</p><p class="gh-stat-value">{{ $greenhouses->count() }}</p><p class="gh-stat-note">в рабочем пространстве</p></article>
            <article class="gh-panel gh-stat"><p class="gh-stat-label">Посадки</p><p class="gh-stat-value">{{ $greenhouses->sum('plantings_count') }}</p><p class="gh-stat-note">во всех теплицах</p></article>
            <article class="gh-panel gh-stat"><p class="gh-stat-label">Состояние системы</p><p class="gh-stat-value" style="font-size: 20px; margin-top: 25px;"><span class="gh-status"><span class="gh-status-dot"></span>Готова к работе</span></p><p class="gh-stat-note">данные ведутся вручную</p></article>
        </div>

        <section class="gh-panel">
            <div class="gh-panel-pad">
                <div class="gh-panel-heading"><div><h2 class="gh-section-title">Новая теплица</h2><p class="gh-section-note">Добавьте название и место расположения</p></div></div>
                <form method="POST" action="{{ route('greenhouses.store') }}" class="gh-form-grid">
                    @csrf
                    <label class="gh-field"><span class="gh-label">Название</span><input class="gh-input" id="name" name="name" value="{{ old('name') }}" placeholder="Например, Теплица у дома" required></label>
                    <label class="gh-field"><span class="gh-label">Расположение</span><input class="gh-input" name="location" value="{{ old('location') }}" placeholder="Например, участок у дома"></label>
                    <label class="gh-field gh-field--wide"><span class="gh-label">Примечание</span><textarea class="gh-textarea" name="note" placeholder="Краткая информация о теплице">{{ old('note') }}</textarea></label>
                    @error('name')<p class="gh-error">{{ $message }}</p>@enderror
                    <div class="gh-actions gh-field--wide"><button class="gh-button gh-button--primary" type="submit">Добавить теплицу</button></div>
                </form>
            </div>
        </section>

        <section>
            <div class="gh-panel-heading"><div><h2 class="gh-section-title">Теплицы</h2><p class="gh-section-note">Откройте теплицу, чтобы вести посадки и уход</p></div></div>
            <div class="gh-grid gh-grid-3">
                @forelse ($greenhouses as $greenhouse)
                    <article class="gh-panel gh-panel-pad">
                        <div class="gh-panel-heading"><div><h3 class="gh-section-title">{{ $greenhouse->name }}</h3><p class="gh-section-note">{{ $greenhouse->location ?: 'Расположение не указано' }}</p></div><span class="gh-status"><span class="gh-status-dot"></span>Активна</span></div>
                        <p class="gh-record-meta">Посадок: {{ $greenhouse->plantings_count }}</p>
                        @if($greenhouse->note)<p class="gh-record-meta">{{ $greenhouse->note }}</p>@endif
                        <div class="gh-actions mt-5"><a class="gh-button gh-button--primary" href="{{ route('greenhouses.show', $greenhouse) }}">Открыть</a><form method="POST" action="{{ route('greenhouses.destroy', $greenhouse) }}">@csrf @method('DELETE')<button class="gh-button gh-button--danger" type="submit" onclick="return confirm('Удалить теплицу и связанные данные?')">Удалить</button></form></div>
                    </article>
                @empty
                    <div class="gh-empty" style="grid-column: 1 / -1;">Теплиц пока нет. Создайте первую выше.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
