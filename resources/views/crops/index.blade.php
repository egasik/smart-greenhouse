<x-app-layout>
    <x-slot name="header">
        <p class="gh-eyebrow">Справочник</p>
        <h1 class="gh-title">Культуры</h1>
        <p class="gh-subtitle">Сохраните названия, сорта и особенности растений для повторного использования в посадках.</p>
    </x-slot>

    <div class="gh-page-grid">
        @if(session('success'))<div class="gh-alert">{{ session('success') }}</div>@endif
        <div class="gh-grid gh-grid-2">
            <section class="gh-panel"><div class="gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">Новая культура</h2><p class="gh-section-note">Заполните только известные данные</p></div></div>
                <form method="POST" action="{{ route('crops.store') }}" class="gh-form-grid">@csrf
                    <label class="gh-field"><span class="gh-label">Название</span><input class="gh-input" name="name" value="{{ old('name') }}" placeholder="Например, томат" required></label>
                    <label class="gh-field"><span class="gh-label">Сорт</span><input class="gh-input" name="variety" value="{{ old('variety') }}" placeholder="Необязательно"></label>
                    <label class="gh-field gh-field--wide"><span class="gh-label">Особенности выращивания</span><textarea class="gh-textarea" name="description" placeholder="Краткое описание условий и ухода">{{ old('description') }}</textarea></label>
                    @error('name')<p class="gh-error">{{ $message }}</p>@enderror
                    <div class="gh-actions gh-field--wide"><button class="gh-button gh-button--primary" type="submit">Добавить культуру</button></div>
                </form>
            </div></section>
            <section class="gh-panel"><div class="gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">Список культур</h2><p class="gh-section-note">{{ $crops->count() }} {{ $crops->count() === 1 ? 'запись' : 'записей' }}</p></div></div>
                <div class="gh-card-list">@forelse($crops as $crop)<article class="gh-record"><div class="gh-record-main"><h3 class="gh-record-title">{{ $crop->name }}{{ $crop->variety ? ' — '.$crop->variety : '' }}</h3>@if($crop->description)<p class="gh-record-meta">{{ $crop->description }}</p>@else<p class="gh-record-meta">Описание не добавлено</p>@endif</div></article>@empty<div class="gh-empty">Справочник пока пуст.</div>@endforelse</div>
            </div></section>
        </div>
    </div>
</x-app-layout>
