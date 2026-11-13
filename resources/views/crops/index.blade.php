<x-app-layout>
    <x-slot name="header">
        <p class="gh-eyebrow">Справочник</p>
        <h1 class="gh-title">Культуры и сорта</h1>
        <p class="gh-subtitle">Для посадки доступны только записи, проверенные администратором.</p>
    </x-slot>

    <div class="gh-page-grid">
        @if (session('success'))
            <div class="gh-alert">{{ session('success') }}</div>
        @endif

        @error('catalog')
            <div class="gh-alert gh-alert--error">{{ $message }}</div>
        @enderror

        <div class="gh-grid gh-grid-2">
            <section class="gh-panel gh-panel-pad">
                <div class="gh-panel-heading">
                    <div>
                        <h2 class="gh-section-title">Доступные записи</h2>
                        <p class="gh-section-note">Набор этапов роста назначается администратором отдельно для каждого сорта.</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('crops.index') }}" class="gh-actions mb-5" role="search">
                    <label class="gh-field" style="flex: 1"><span class="gh-label">Поиск</span><input class="gh-input" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Культура или сорт"></label>
                    <button class="gh-button gh-button--primary" type="submit">Найти</button>
                    @if(!empty($filters['search']))<a class="gh-button" href="{{ route('crops.index') }}">Сбросить</a>@endif
                </form>

                <div class="gh-card-list">
                    @forelse ($crops as $crop)
                        <article class="gh-record">
                            <div class="gh-record-main">
                                <h3 class="gh-record-title">{{ $crop->name }}</h3>
                                <div class="gh-card-list mt-3">
                                    @forelse ($crop->varieties as $variety)
                                        <p class="gh-record-meta">
                                            <strong class="text-[var(--gh-text)]">{{ $variety->name }}</strong>
                                            · {{ $variety->growthStages->pluck('name')->join(', ') ?: 'этапы пока не назначены' }}
                                        </p>
                                    @empty
                                        <p class="gh-record-meta">Подтверждённых сортов пока нет.</p>
                                    @endforelse
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="gh-empty">Справочник пока пуст.</div>
                    @endforelse
                </div>
            </section>

            <section class="gh-panel gh-panel-pad">
                <div class="gh-panel-heading">
                    <div>
                        <h2 class="gh-section-title">Предложить запись</h2>
                        <p class="gh-section-note">Укажите культуру и сорт. Этапы роста выберет администратор.</p>
                    </div>
                </div>

                <form class="gh-page-grid" method="POST" action="{{ route('catalog-requests.store') }}">
                    @csrf
                    <label class="gh-field">
                        <span class="gh-label">Существующая культура</span>
                        <select class="gh-select" name="crop_id">
                            <option value="">Новой культуры нет в списке</option>
                            @foreach ($crops as $crop)
                                <option value="{{ $crop->id }}" @selected(old('crop_id') == $crop->id)>{{ $crop->name }}</option>
                            @endforeach
                        </select>
                        @error('crop_id')<span class="gh-error">{{ $message }}</span>@enderror
                    </label>

                    <label class="gh-field">
                        <span class="gh-label">Название новой культуры</span>
                        <input class="gh-input" name="crop_name" value="{{ old('crop_name') }}"
                               placeholder="Заполняется, если культуры нет в списке">
                        <span class="gh-section-note">Выберите существующую культуру или заполните это поле.</span>
                        @error('crop_name')<span class="gh-error">{{ $message }}</span>@enderror
                    </label>

                    <label class="gh-field">
                        <span class="gh-label">Сорт</span>
                        <input required class="gh-input" name="variety_name" value="{{ old('variety_name') }}"
                               placeholder="Например, Де Барао">
                        @error('variety_name')<span class="gh-error">{{ $message }}</span>@enderror
                    </label>

                    <button class="gh-button gh-button--primary" type="submit">Отправить на проверку</button>
                </form>
            </section>
        </div>

        <section class="gh-panel gh-panel-pad">
            <div class="gh-panel-heading">
                <div>
                    <h2 class="gh-section-title">Мои заявки</h2>
                    <p class="gh-section-note">Здесь отображается результат проверки администратором.</p>
                </div>
            </div>

            <div class="gh-card-list">
                @forelse ($myRequests as $catalogRequest)
                    @php
                        $statusLabel = match ($catalogRequest->status) {
                            'approved' => 'Одобрена',
                            'rejected' => 'Отклонена',
                            default => 'На проверке',
                        };
                    @endphp
                    <article class="gh-record">
                        <div class="gh-record-main">
                            <h3 class="gh-record-title">
                                {{ $catalogRequest->crop?->name ?? $catalogRequest->crop_name }} — {{ $catalogRequest->variety_name }}
                            </h3>
                            <p class="gh-record-meta">
                                {{ $statusLabel }} · отправлена {{ $catalogRequest->created_at->format('d.m.Y H:i') }}
                            </p>
                            @if ($catalogRequest->review_comment)
                                <p class="gh-record-meta">Комментарий: {{ $catalogRequest->review_comment }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="gh-empty">Вы ещё не отправляли заявки.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
