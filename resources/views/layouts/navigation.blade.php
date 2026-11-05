<nav x-data="{ open: false }" class="gh-header" aria-label="Основная навигация">
    <div class="gh-container">
        <div class="gh-header-row">
            <a class="gh-brand" href="{{ route('dashboard') }}">
                <span class="gh-mark">G</span>
                <span>Умная теплица</span>
            </a>

            <div class="gh-nav hidden md:flex">
                <a class="gh-nav-link {{ request()->routeIs('dashboard', 'greenhouses.*') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">Теплицы</a>
                <a class="gh-nav-link {{ request()->routeIs('crops.*') ? 'is-active' : '' }}" href="{{ route('crops.index') }}">Культуры</a>
            </div>

            <div class="hidden md:block">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="gh-user" type="button">
                            <span class="gh-user-name">{{ Auth::user()->name }}</span>
                            <span class="gh-avatar">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Профиль</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Выйти</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = ! open" class="gh-button md:hidden" type="button" aria-label="Открыть меню">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 7h16M4 12h16M4 17h16" /></svg>
            </button>
        </div>
        <div x-cloak x-show="open" class="gh-mobile-menu md:hidden">
            <a class="gh-nav-link {{ request()->routeIs('dashboard', 'greenhouses.*') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">Теплицы</a>
            <a class="gh-nav-link {{ request()->routeIs('crops.*') ? 'is-active' : '' }}" href="{{ route('crops.index') }}">Культуры</a>
            <a class="gh-nav-link" href="{{ route('profile.edit') }}">Профиль</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="gh-nav-link w-full text-left" type="submit">Выйти</button></form>
        </div>
    </div>
</nav>
