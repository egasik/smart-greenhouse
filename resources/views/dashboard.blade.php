<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Мои теплицы
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Добавить теплицу</h3>

                <form method="POST" action="{{ route('greenhouses.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700">
                            Название
                        </label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                               class="mt-1 block w-full rounded-md border-gray-300"
                               placeholder="Например: Теплица № 1" required>
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location" class="block font-medium text-sm text-gray-700">
                            Расположение
                        </label>
                        <input id="location" name="location" type="text" value="{{ old('location') }}"
                               class="mt-1 block w-full rounded-md border-gray-300"
                               placeholder="Участок у дома">
                    </div>

                    <div>
                        <label for="note" class="block font-medium text-sm text-gray-700">
                            Примечание
                        </label>
                        <textarea id="note" name="note" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300"
                                  placeholder="Дополнительная информация">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit"
                            class="px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-800">
                        Добавить
                    </button>
                </form>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($greenhouses as $greenhouse)
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $greenhouse->name }}
                        </h3>

                        <p class="text-sm text-gray-600 mt-2">
                            Расположение: {{ $greenhouse->location ?: 'не указано' }}
                        </p>

                        <p class="text-sm text-gray-600 mt-1">
                            Посадок: {{ $greenhouse->plantings_count }}
                        </p>

                        @if ($greenhouse->note)
                            <p class="text-sm text-gray-600 mt-3">
                                {{ $greenhouse->note }}
                            </p>
                        @endif

                        <form method="POST"
                              action="{{ route('greenhouses.destroy', $greenhouse) }}"
                              class="mt-4">
                            @csrf
                            @method('DELETE')
                        <a href="{{ route('greenhouses.show', $greenhouse) }}"
                            class="inline-block mt-4 text-sm text-green-700 hover:text-green-900">
                             Открыть теплицу
                        </a>

                            <button type="submit"
                                    onclick="return confirm('Удалить теплицу и все связанные данные?')"
                                    class="text-sm text-red-600 hover:text-red-800">
                                Удалить
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-gray-600">Теплиц пока нет. Добавьте первую.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>