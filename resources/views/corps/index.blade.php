<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Справочник культур
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Добавить культуру</h3>

                <form method="POST" action="{{ route('crops.store') }}" class="space-y-4">
                    @csrf

                    <input name="name" type="text" placeholder="Название культуры"
                           value="{{ old('name') }}"
                           class="w-full rounded-md border-gray-300" required>

                    <input name="variety" type="text" placeholder="Сорт"
                           value="{{ old('variety') }}"
                           class="w-full rounded-md border-gray-300">

                    <textarea name="description" rows="3" placeholder="Описание"
                              class="w-full rounded-md border-gray-300">{{ old('description') }}</textarea>

                    <button class="px-4 py-2 bg-green-700 text-white rounded-md">
                        Добавить культуру
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Список культур</h3>

                <div class="space-y-3">
                    @forelse ($crops as $crop)
                        <div class="border-b pb-3">
                            <p class="font-medium">{{ $crop->name }}</p>
                            <p class="text-sm text-gray-600">
                                Сорт: {{ $crop->variety ?: 'не указан' }}
                            </p>
                            @if ($crop->description)
                                <p class="text-sm text-gray-600 mt-1">{{ $crop->description }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-600">Культуры пока не добавлены.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>