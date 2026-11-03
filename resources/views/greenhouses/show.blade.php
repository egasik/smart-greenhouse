<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $greenhouse->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('dashboard') }}" class="text-green-700 hover:text-green-900">
                ← К списку теплиц
            </a>

            @if (session('success'))
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Добавить посадку</h3>

                @if ($crops->isEmpty())
                    <p class="text-gray-600">
                        Сначала добавьте культуру в
                        <a href="{{ route('crops.index') }}" class="text-green-700 underline">
                            справочнике культур
                        </a>.
                    </p>
                @else
                    <form method="POST"
                          action="{{ route('plantings.store', $greenhouse) }}"
                          class="grid gap-4 md:grid-cols-3">
                        @csrf

                        <select name="crop_id" class="rounded-md border-gray-300" required>
                            <option value="">Выберите культуру</option>
                            @foreach ($crops as $crop)
                                <option value="{{ $crop->id }}">
                                    {{ $crop->name }}{{ $crop->variety ? ' — '.$crop->variety : '' }}
                                </option>
                            @endforeach
                        </select>

                        <input name="planting_date" type="date"
                               value="{{ old('planting_date', now()->format('Y-m-d')) }}"
                               class="rounded-md border-gray-300" required>

                        <input name="quantity" type="number" min="1"
                               value="{{ old('quantity', 1) }}"
                               class="rounded-md border-gray-300" required>

                        <button class="md:col-span-3 w-fit px-4 py-2 bg-green-700 text-white rounded-md">
                            Добавить посадку
                        </button>
                    </form>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Посадки</h3>

                <div class="space-y-4">
                    @forelse ($greenhouse->plantings as $planting)
                        <div class="border rounded-md p-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium">
                                    {{ $planting->crop->name }}
                                    @if ($planting->crop->variety)
                                        — {{ $planting->crop->variety }}
                                    @endif
                                </p>

                                <p class="text-sm text-gray-600">
                                    Дата посадки: {{ $planting->planting_date->format('d.m.Y') }}
                                </p>

                                <p class="text-sm text-gray-600">
                                    Количество: {{ $planting->quantity }}
                                </p>

                                @if ($planting->growthStages->isNotEmpty())
                                    <p class="text-sm text-gray-600">
                                        Последний этап:
                                        {{ $planting->growthStages->first()->stage }}
                                        ({{ $planting->growthStages->first()->recorded_at->format('d.m.Y') }})
                                    </p>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('plantings.destroy', $planting) }}">
                                @csrf
                                @method('DELETE')

                                <button onclick="return confirm('Удалить посадку?')"
                                        class="text-sm text-red-600">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-600">В этой теплице пока нет посадок.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Зафиксировать этап роста</h3>

                @if ($greenhouse->plantings->isNotEmpty())
                    <div class="space-y-5">
                        @foreach ($greenhouse->plantings as $planting)
                            <form method="POST"
                                  action="{{ route('growth-stages.store', $planting) }}"
                                  class="border rounded-md p-4 grid gap-3 md:grid-cols-2">
                                @csrf

                                <p class="font-medium md:col-span-2">
                                    {{ $planting->crop->name }}
                                </p>

                                <select name="stage" class="rounded-md border-gray-300" required>
                                    <option value="">Выберите этап</option>
                                    <option value="Посев">Посев</option>
                                    <option value="Всходы">Всходы</option>
                                    <option value="Рост">Рост</option>
                                    <option value="Цветение">Цветение</option>
                                    <option value="Плодоношение">Плодоношение</option>
                                    <option value="Сбор урожая">Сбор урожая</option>
                                </select>

                                <input name="recorded_at" type="datetime-local"
                                       value="{{ now()->format('Y-m-d\TH:i') }}"
                                       class="rounded-md border-gray-300" required>

                                <input name="status" type="text"
                                       placeholder="Состояние растения"
                                       class="rounded-md border-gray-300">

                                <input name="comment" type="text"
                                       placeholder="Комментарий"
                                       class="rounded-md border-gray-300">

                                <button class="w-fit px-4 py-2 bg-green-700 text-white rounded-md">
                                    Сохранить этап
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">Сначала добавьте посадку.</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Записать показатели</h3>

                <form method="POST"
                      action="{{ route('measurements.store', $greenhouse) }}"
                      class="grid gap-4 md:grid-cols-2">
                    @csrf

                    <select name="planting_id" class="rounded-md border-gray-300">
                        <option value="">Для всей теплицы</option>
                        @foreach ($greenhouse->plantings as $planting)
                            <option value="{{ $planting->id }}">
                                {{ $planting->crop->name }}
                            </option>
                        @endforeach
                    </select>

                    <input name="measured_at" type="datetime-local"
                           value="{{ now()->format('Y-m-d\TH:i') }}"
                           class="rounded-md border-gray-300" required>

                    <input name="temperature" type="number" step="0.01"
                           placeholder="Температура, °C"
                           class="rounded-md border-gray-300">

                    <input name="illumination" type="number" step="0.01"
                           placeholder="Освещённость, лк"
                           class="rounded-md border-gray-300">

                    <button class="md:col-span-2 w-fit px-4 py-2 bg-green-700 text-white rounded-md">
                        Сохранить показатели
                    </button>
                </form>

                @error('measurement')
                    <p class="text-red-600 text-sm mt-3">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Последние показатели</h3>

                <div class="space-y-3">
                    @forelse ($measurements as $measurement)
                        <div class="border-b pb-3 text-sm">
                            <p class="font-medium">
                                {{ $measurement->measured_at->format('d.m.Y H:i') }}
                            </p>

                            <p class="text-gray-600">
                                Температура:
                                {{ $measurement->temperature !== null ? $measurement->temperature.' °C' : 'не указана' }}
                                · Освещённость:
                                {{ $measurement->illumination !== null ? $measurement->illumination.' лк' : 'не указана' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-600">Показателей пока нет.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Планирование ухода</h3>

                @if ($greenhouse->plantings->isNotEmpty())
                    <form method="POST"
                          action="{{ route('care-tasks.store', $greenhouse) }}"
                          class="grid gap-4 md:grid-cols-2">
                        @csrf

                        <select name="planting_id" class="rounded-md border-gray-300" required>
                            <option value="">Выберите посадку</option>
                            @foreach ($greenhouse->plantings as $planting)
                                <option value="{{ $planting->id }}">
                                    {{ $planting->crop->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="type" class="rounded-md border-gray-300" required>
                            <option value="">Выберите процедуру</option>
                            <option value="Полив">Полив</option>
                            <option value="Подкормка">Подкормка</option>
                            <option value="Проветривание">Проветривание</option>
                            <option value="Обработка">Обработка</option>
                            <option value="Другое">Другое</option>
                        </select>

                        <input name="planned_at" type="datetime-local"
                               class="rounded-md border-gray-300" required>

                        <input name="description" type="text"
                               placeholder="Примечание"
                               class="rounded-md border-gray-300">

                        <button class="md:col-span-2 w-fit px-4 py-2 bg-green-700 text-white rounded-md">
                            Запланировать
                        </button>
                    </form>
                @else
                    <p class="text-gray-600">Сначала добавьте посадку.</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Задачи по уходу</h3>

                <div class="space-y-3">
                    @forelse ($careTasks as $task)
                        <div class="border rounded-md p-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium">
                                    {{ $task->type }} — {{ $task->planting->crop->name }}
                                </p>

                                <p class="text-sm text-gray-600">
                                    {{ $task->planned_at->format('d.m.Y H:i') }}
                                </p>

                                @if ($task->description)
                                    <p class="text-sm text-gray-600">
                                        {{ $task->description }}
                                    </p>
                                @endif
                            </div>

                            @if ($task->status === 'planned')
                                <form method="POST"
                                      action="{{ route('care-tasks.complete', $task) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button class="px-3 py-2 bg-green-700 text-white rounded-md text-sm">
                                        Выполнено
                                    </button>
                                </form>
                            @else
                                <span class="text-sm text-green-700">Выполнено</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-600">Запланированных процедур пока нет.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>