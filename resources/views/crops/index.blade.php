<x-app-layout>
    <x-slot name="header"><p class="gh-eyebrow">Справочник</p><h1 class="gh-title">Культуры и сорта</h1><p class="gh-subtitle">Для посадки доступны только записи, проверенные администратором.</p></x-slot>
    <div class="gh-page-grid">
        @if(session('success'))<div class="gh-alert">{{ session('success') }}</div>@endif
        @error('catalog')<div class="gh-alert gh-alert--error">{{ $message }}</div>@enderror
        <div class="gh-grid gh-grid-2">
            <section class="gh-panel gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">Доступные записи</h2><p class="gh-section-note">У каждого сорта указан свой набор этапов роста.</p></div></div><div class="gh-card-list">@forelse($crops as $crop)<article class="gh-record"><div class="gh-record-main"><h3 class="gh-record-title">{{ $crop->name }}</h3><div class="gh-card-list mt-3">@foreach($crop->varieties as $variety)<p class="gh-record-meta"><strong class="text-[var(--gh-text)]">{{ $variety->name }}</strong> · {{ $variety->growthStages->pluck('name')->join(', ') }}</p>@endforeach</div></div></article>@empty<div class="gh-empty">Справочник пока пуст.</div>@endforelse</div></section>
            <section class="gh-panel gh-panel-pad"><div class="gh-panel-heading"><div><h2 class="gh-section-title">Предложить запись</h2><p class="gh-section-note">После проверки она станет доступна всем пользователям.</p></div></div><form class="gh-page-grid" method="POST" action="{{ route('catalog-requests.store') }}">@csrf
                <label class="gh-field"><span class="gh-label">Существующая культура</span><select class="gh-select" name="crop_id"><option value="">Новой культуры нет в списке</option>@foreach($crops as $crop)<option value="{{ $crop->id }}" @selected(old('crop_id') == $crop->id)>{{ $crop->name }}</option>@endforeach</select></label>
                <label class="gh-field"><span class="gh-label">Название новой культуры</span><input class="gh-input" name="crop_name" value="{{ old('crop_name') }}" placeholder="Заполняется, если культуры нет в списке"><span class="gh-section-note">Заполните существующую культуру либо это поле.</span>@error('crop_name')<span class="gh-error">{{ $message }}</span>@enderror</label>
                <label class="gh-field"><span class="gh-label">Сорт</span><input required class="gh-input" name="variety_name" value="{{ old('variety_name') }}" placeholder="Например, Де Барао"></label>
                <fieldset class="gh-field"><legend class="gh-label">Допустимые этапы роста</legend><div class="gh-grid gh-grid-2">@foreach($stages as $stage)<label class="gh-record"><input class="gh-check" type="checkbox" name="stage_ids[]" value="{{ $stage->id }}" @checked(in_array($stage->id, old('stage_ids', [])))><span class="gh-record-meta">{{ $stage->name }}</span></label>@endforeach</div>@error('stage_ids')<span class="gh-error">{{ $message }}</span>@enderror</fieldset>
                <button class="gh-button gh-button--primary" type="submit">Отправить на проверку</button>
            </form></section>
        </div>
    </div>
</x-app-layout>
