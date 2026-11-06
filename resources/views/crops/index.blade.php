<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Справочник растений</h2></x-slot>
    <div class="py-12"><div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))<div class="rounded bg-green-100 p-4 text-green-800">{{ session('success') }}</div>@endif
        @error('catalog')<div class="rounded bg-red-100 p-4 text-red-800">{{ $message }}</div>@enderror
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="bg-white shadow-sm rounded-lg p-6"><h3 class="text-lg font-semibold">Доступные культуры и сорта</h3><p class="mt-1 text-sm text-gray-600">В посадке можно выбрать только проверенные администратором записи.</p><div class="mt-5 space-y-4">@forelse($crops as $crop)<article class="border rounded p-4"><h4 class="font-semibold">{{ $crop->name }}</h4><ul class="mt-2 space-y-2 text-sm text-gray-600">@foreach($crop->varieties as $variety)<li><span class="font-medium text-gray-800">{{ $variety->name }}</span> — {{ $variety->growthStages->pluck('name')->join(', ') }}</li>@endforeach</ul></article>@empty<p class="text-gray-600">Справочник пока пуст.</p>@endforelse</div></section>
            <section class="bg-white shadow-sm rounded-lg p-6"><h3 class="text-lg font-semibold">Предложить культуру или сорт</h3><p class="mt-1 text-sm text-gray-600">После проверки администратора запись станет доступна всем пользователям.</p><form class="mt-5 space-y-4" method="POST" action="{{ route('catalog-requests.store') }}">@csrf
                <div><label class="block text-sm font-medium">Существующая культура</label><select class="mt-1 w-full rounded border-gray-300" name="crop_id"><option value="">Новой культуры нет в списке</option>@foreach($crops as $crop)<option value="{{ $crop->id }}" @selected(old('crop_id') == $crop->id)>{{ $crop->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium">Название новой культуры</label><input class="mt-1 w-full rounded border-gray-300" name="crop_name" value="{{ old('crop_name') }}" placeholder="Заполняется, если культуры нет в списке"><p class="mt-1 text-xs text-gray-500">Заполните либо существующую культуру, либо это поле.</p>@error('crop_name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium">Сорт</label><input required class="mt-1 w-full rounded border-gray-300" name="variety_name" value="{{ old('variety_name') }}" placeholder="Например, Де Барао"><p class="mt-1 text-xs text-gray-500">Если предлагаете только новую культуру, укажите её первый сорт.</p></div>
                <fieldset><legend class="text-sm font-medium">Допустимые этапы роста</legend><div class="mt-2 grid grid-cols-2 gap-2">@foreach($stages as $stage)<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="stage_ids[]" value="{{ $stage->id }}" @checked(in_array($stage->id, old('stage_ids', [])))>{{ $stage->name }}</label>@endforeach</div>@error('stage_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</fieldset>
                <button class="rounded bg-green-700 px-4 py-2 text-white hover:bg-green-800">Отправить на проверку</button>
            </form></section>
        </div>
    </div></div>
</x-app-layout>
