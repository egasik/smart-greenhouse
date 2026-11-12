<x-app-layout>
    <x-slot name="header">
        <p class="gh-eyebrow">Администрирование</p>
        <h1 class="gh-title">Проверка справочника</h1>
        <p class="gh-subtitle">Добавляйте культуры и назначайте сортам допустимые этапы роста.</p>
    </x-slot>

    <div class="gh-page-grid">
        @if (session('success'))
            <div class="gh-alert">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="gh-alert gh-alert--error">Исправьте ошибки в форме: {{ $errors->first() }}</div>
        @endif

        <section class="gh-panel gh-panel-pad">
            <div class="gh-panel-heading">
                <div>
                    <h2 class="gh-section-title">Добавить подтверждённую запись</h2>
                    <p class="gh-section-note">Только администратор назначает допустимые этапы роста.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.catalog.store') }}" class="gh-form-grid">
                @csrf
                <label class="gh-field">
                    <span class="gh-label">Название культуры</span>
                    <input class="gh-input" name="crop_name" value="{{ old('crop_name') }}" required>
                </label>
                <label class="gh-field">
                    <span class="gh-label">Сорт</span>
                    <input class="gh-input" name="variety_name" value="{{ old('variety_name') }}" required>
                </label>
                <label class="gh-field gh-field--wide">
                    <span class="gh-label">Описание культуры</span>
                    <textarea class="gh-textarea" name="description">{{ old('description') }}</textarea>
                </label>
                <fieldset class="gh-field gh-field--wide">
                    <legend class="gh-label">Допустимые этапы</legend>
                    <div class="gh-grid gh-grid-3 mt-2">
                        @foreach ($stages as $stage)
                            <label class="gh-record">
                                <input class="gh-check" type="checkbox" name="stage_ids[]" value="{{ $stage->id }}"
                                       @checked(in_array($stage->id, old('stage_ids', [])))>
                                <span class="gh-record-meta">{{ $stage->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <div class="gh-actions gh-field--wide">
                    <button class="gh-button gh-button--primary" type="submit">Добавить в справочник</button>
                </div>
            </form>
        </section>

        <section>
            <div class="gh-panel-heading">
                <div>
                    <h2 class="gh-section-title">Заявки пользователей</h2>
                    <p class="gh-section-note">Перед одобрением выберите этапы, подходящие предложенному сорту.</p>
                </div>
            </div>

            <div class="gh-card-list">
                @forelse ($requests as $catalogRequest)
                    <article class="gh-panel gh-panel-pad">
                        <div class="gh-panel-heading">
                            <div>
                                <h3 class="gh-section-title">
                                    {{ $catalogRequest->crop?->name ?? $catalogRequest->crop_name }} — {{ $catalogRequest->variety_name }}
                                </h3>
                                <p class="gh-record-meta">
                                    Предложил: {{ $catalogRequest->requester->name }} · {{ $catalogRequest->created_at->format('d.m.Y H:i') }}
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.catalog-requests.approve', $catalogRequest) }}" class="gh-page-grid">
                            @csrf
                            @method('PATCH')
                            <fieldset class="gh-field">
                                <legend class="gh-label">Допустимые этапы роста</legend>
                                <div class="gh-grid gh-grid-3 mt-2">
                                    @foreach ($stages as $stage)
                                        <label class="gh-record">
                                            <input class="gh-check" type="checkbox" name="stage_ids[]" value="{{ $stage->id }}">
                                            <span class="gh-record-meta">{{ $stage->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                            <button class="gh-button gh-button--primary" type="submit">Одобрить и сохранить этапы</button>
                        </form>

                        <form method="POST" action="{{ route('admin.catalog-requests.reject', $catalogRequest) }}" class="gh-actions mt-3">
                            @csrf
                            @method('PATCH')
                            <input class="gh-input" name="review_comment" placeholder="Причина отклонения">
                            <button class="gh-button gh-button--danger" type="submit">Отклонить</button>
                        </form>
                    </article>
                @empty
                    <div class="gh-empty">Заявок на проверке нет.</div>
                @endforelse
            </div>
        </section>

        <section class="gh-panel gh-panel-pad">
            <div class="gh-panel-heading">
                <div>
                    <h2 class="gh-section-title">Последние обработанные заявки</h2>
                    <p class="gh-section-note">История одобрений и отклонений.</p>
                </div>
            </div>
            <div class="gh-card-list">
                @forelse ($processedRequests as $catalogRequest)
                    <article class="gh-record">
                        <div class="gh-record-main">
                            <h3 class="gh-record-title">
                                {{ $catalogRequest->crop?->name ?? $catalogRequest->crop_name }} — {{ $catalogRequest->variety_name }}
                            </h3>
                            <p class="gh-record-meta">
                                {{ $catalogRequest->status === 'approved' ? 'Одобрена' : 'Отклонена' }}
                                @if ($catalogRequest->reviewer) · {{ $catalogRequest->reviewer->name }} @endif
                                @if ($catalogRequest->reviewed_at) · {{ $catalogRequest->reviewed_at->format('d.m.Y H:i') }} @endif
                            </p>
                            @if ($catalogRequest->review_comment)
                                <p class="gh-record-meta">Комментарий: {{ $catalogRequest->review_comment }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="gh-empty">Обработанных заявок пока нет.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
