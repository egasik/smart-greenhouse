<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Умная теплица помогает учитывать культуры, контролировать показатели среды и планировать уход за растениями.">
    <title>Умная теплица</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html { scroll-behavior: smooth; }
        body { background: #07080d; color: #eef0f5; }
        .page-shell { background: #07080d; }
        .nav-surface {
            border: 1px solid rgba(255, 255, 255, .09);
            background: rgba(12, 15, 20, .76);
            box-shadow: 0 16px 48px rgba(0, 0, 0, .22);
            backdrop-filter: blur(16px);
        }
        .button-primary {
            border: 1px solid rgba(163, 255, 198, .20);
            background: rgba(76, 195, 138, .94);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .26), 0 10px 28px rgba(41, 151, 92, .18);
            color: #07100c;
            transition: transform 300ms cubic-bezier(.22,1,.36,1), background-color 300ms cubic-bezier(.22,1,.36,1);
        }
        .button-primary:hover { background: #63d99a; transform: translateY(-2px); }
        .button-primary:active { transform: scale(.98); }
        .button-quiet {
            border: 1px solid rgba(255, 255, 255, .16);
            background: rgba(255, 255, 255, .045);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
            color: #eef0f5;
            transition: transform 300ms cubic-bezier(.22,1,.36,1), background-color 300ms cubic-bezier(.22,1,.36,1);
        }
        .button-quiet:hover { background: rgba(255, 255, 255, .10); transform: translateY(-2px); }
        .button-quiet:active { transform: scale(.98); }
        .signal-card {
            border: 1px solid rgba(255, 255, 255, .09);
            background: #11141a;
        }
        .section-line { border-color: rgba(255, 255, 255, .09); }
        .muted { color: #a3a9b8; }
        .hero-copy { text-wrap: pretty; }
        .hero-title { text-wrap: balance; }
        .metric-grid line { stroke: rgba(255, 255, 255, .10); stroke-width: 1; }
        .metric-line { fill: none; stroke: #4cc38a; stroke-linecap: round; stroke-linejoin: round; stroke-width: 3; }
        .metric-area { fill: rgba(76, 195, 138, .10); }
        .feature-icon { border: 1px solid rgba(76, 195, 138, .22); background: rgba(76, 195, 138, .08); color: #70d99c; }
        :focus-visible { outline: 2px solid #70d99c; outline-offset: 4px; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition-duration: .01ms !important; animation-duration: .01ms !important; }
        }
    </style>
</head>
<body class="antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-6 focus:top-6 focus:z-50 focus:rounded-lg focus:bg-[#4cc38a] focus:px-4 focus:py-3 focus:text-sm focus:font-semibold focus:text-[#07100c]">
        Перейти к содержимому
    </a>

    <div class="page-shell min-h-screen overflow-x-hidden">
        <header class="fixed inset-x-0 top-0 z-40 px-4 pt-4 sm:px-6">
            <nav class="nav-surface mx-auto flex max-w-6xl items-center justify-between rounded-2xl px-4 py-3 sm:px-5" aria-label="Основная навигация">
                <a href="{{ url('/') }}" class="flex items-center gap-3 text-base font-semibold text-white">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#4cc38a] text-sm font-bold text-[#07100c]">G</span>
                    Умная теплица
                </a>

                <div class="hidden items-center gap-6 text-sm text-[#a3a9b8] lg:flex">
                    <a href="#signals" class="transition-colors duration-300 hover:text-white">Показатели</a>
                    <a href="#features" class="transition-colors duration-300 hover:text-white">Возможности</a>
                    <a href="#workflow" class="transition-colors duration-300 hover:text-white">Как это работает</a>
                    <a href="#questions" class="transition-colors duration-300 hover:text-white">Вопросы</a>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="button-primary rounded-lg px-3 py-2 text-sm font-semibold">Открыть кабинет</a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="hidden text-sm text-[#c0c6d1] transition-colors duration-300 hover:text-white sm:block">Войти</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="button-primary rounded-lg px-3 py-2 text-sm font-semibold">Создать аккаунт</a>
                        @endif
                    @endauth
                </div>
            </nav>
        </header>

        <main id="main">
            <section class="relative flex min-h-screen items-center overflow-hidden border-b section-line bg-[#07080d]">
                <div id="greenhouse-scene" class="scene-container absolute inset-y-0 right-0 z-0 w-full lg:w-3/5" aria-label="Интерактивная трёхмерная модель умной теплицы"></div>
                <div class="pointer-events-none absolute inset-0 z-[1] bg-[#07080d]/20"></div>

                <div class="relative z-10 mx-auto w-full max-w-6xl px-6 pb-14 pt-32 sm:px-8 lg:px-10">
                    <div class="max-w-2xl">
                        <p class="mb-6 text-sm font-medium text-[#a3a9b8]">Контроль среды и ухода за растениями</p>
                        <h1 class="hero-title max-w-[680px] text-5xl font-semibold leading-none text-white sm:text-6xl">
                            Выращивайте растения<br>с точными данными
                        </h1>
                        <p class="hero-copy mt-8 max-w-xl text-lg leading-7 text-[#c0c6d1]">
                            Создавайте теплицы, учитывайте культуры, фиксируйте рост и планируйте полив, подкормку и другие процедуры ухода.
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="button-primary rounded-lg px-4 py-3 text-base font-semibold">Перейти к теплицам</a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="button-primary rounded-lg px-4 py-3 text-base font-semibold">Начать вести теплицу</a>
                                @endif
                            @endauth
                            <a href="#signals" class="button-quiet rounded-lg px-4 py-3 text-base font-semibold">Посмотреть показатели</a>
                        </div>
                    </div>

                    <div class="nav-surface mt-16 flex max-w-2xl items-center justify-between gap-6 rounded-2xl p-5 sm:p-6">
                        <div>
                            <p class="text-sm text-[#a3a9b8]">Интерактивная модель</p>
                            <p class="mt-1 text-lg font-medium text-white">Потяните сцену, чтобы осмотреть теплицу</p>
                        </div>
                        <span class="hidden text-sm font-medium text-[#70d99c] sm:block">3D растения и датчик</span>
                    </div>
                </div>
            </section>

            <section id="signals" class="border-b section-line bg-[#0b0e13] py-20 sm:py-24">
                <div class="mx-auto max-w-6xl px-6 sm:px-8 lg:px-10">
                    <div class="grid gap-10 lg:grid-cols-[.8fr_1.2fr] lg:items-end">
                        <div>
                            <p class="text-sm font-medium text-[#70d99c]">Показатели среды</p>
                            <h2 class="mt-4 max-w-xl text-4xl font-semibold leading-tight text-white">Видите состояние теплицы в одном окне</h2>
                            <p class="mt-5 max-w-lg text-base leading-7 text-[#a3a9b8]">В журнале измерений сохраняются температура, влажность и освещённость. По ним легче заметить отклонение и не пропустить уход.</p>
                        </div>
                        <div class="signal-card rounded-2xl p-5 sm:p-6">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm text-[#a3a9b8]">Температура за сегодня</p>
                                    <p class="mt-2 text-4xl font-semibold tabular-nums text-white">24,6 °C</p>
                                </div>
                                <span class="rounded-full border border-[#4cc38a]/25 bg-[#4cc38a]/10 px-3 py-1 text-sm font-medium text-[#70d99c]">В норме</span>
                            </div>
                            <svg class="metric-grid mt-8 h-48 w-full" viewBox="0 0 620 220" role="img" aria-label="График температуры за день">
                                <line x1="0" y1="30" x2="620" y2="30"></line>
                                <line x1="0" y1="85" x2="620" y2="85"></line>
                                <line x1="0" y1="140" x2="620" y2="140"></line>
                                <line x1="0" y1="195" x2="620" y2="195"></line>
                                <path class="metric-area" d="M0 166 C55 151 78 127 125 137 S191 84 248 103 S318 140 374 96 S465 44 526 73 S582 90 620 55 L620 220 L0 220 Z"></path>
                                <path class="metric-line" d="M0 166 C55 151 78 127 125 137 S191 84 248 103 S318 140 374 96 S465 44 526 73 S582 90 620 55"></path>
                                <circle cx="620" cy="55" r="5" fill="#70d99c"></circle>
                            </svg>
                            <div class="mt-3 flex justify-between text-xs text-[#707786]"><span>06:00</span><span>10:00</span><span>14:00</span><span>18:00</span><span>22:00</span></div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="border-b section-line bg-[#07080d] py-20 sm:py-24">
                <div class="mx-auto max-w-6xl px-6 sm:px-8 lg:px-10">
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                        <div id="plants-scene" class="scene-container h-[420px] w-full rounded-2xl sm:h-[520px]" aria-label="Трёхмерная модель выращиваемых культур"></div>
                        <div>
                            <p class="text-sm font-medium text-[#70d99c]">Культуры и уход</p>
                            <h2 class="mt-4 text-4xl font-semibold leading-tight text-white">Ведите растения от посадки до сбора урожая</h2>
                            <div class="mt-8 space-y-5">
                                <article class="flex gap-4">
                                    <span class="feature-icon grid h-10 w-10 shrink-0 place-items-center rounded-lg text-sm font-semibold">01</span>
                                    <div><h3 class="text-lg font-medium text-white">Карточки культур</h3><p class="mt-1 text-sm leading-6 text-[#a3a9b8]">Сохраняйте сорт, описание и особенности выращивания.</p></div>
                                </article>
                                <article class="flex gap-4">
                                    <span class="feature-icon grid h-10 w-10 shrink-0 place-items-center rounded-lg text-sm font-semibold">02</span>
                                    <div><h3 class="text-lg font-medium text-white">Этапы роста</h3><p class="mt-1 text-sm leading-6 text-[#a3a9b8]">Отмечайте посадку, всходы, цветение и плодоношение.</p></div>
                                </article>
                                <article class="flex gap-4">
                                    <span class="feature-icon grid h-10 w-10 shrink-0 place-items-center rounded-lg text-sm font-semibold">03</span>
                                    <div><h3 class="text-lg font-medium text-white">План процедур</h3><p class="mt-1 text-sm leading-6 text-[#a3a9b8]">Создавайте задачи на полив, подкормку, пересадку и осмотр.</p></div>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-b section-line bg-[#0b0e13] py-24 sm:py-32">
                <div class="mx-auto max-w-4xl px-6 text-center sm:px-8">
                    <p class="text-4xl font-semibold leading-tight text-[#717886] sm:text-5xl">
                        Меньше записей в блокноте.<br><span class="text-white">Больше понятных действий для каждой культуры.</span>
                    </p>
                </div>
            </section>

            <section id="workflow" class="border-b section-line bg-[#07080d] py-20 sm:py-24">
                <div class="mx-auto max-w-6xl px-6 sm:px-8 lg:px-10">
                    <p class="text-sm font-medium text-[#70d99c]">Как это работает</p>
                    <h2 class="mt-4 max-w-2xl text-4xl font-semibold leading-tight text-white">Три шага для контроля выращивания</h2>
                    <div class="mt-12 grid gap-5 md:grid-cols-3">
                        <article class="signal-card rounded-2xl p-6"><p class="text-sm text-[#70d99c]">Шаг 1</p><h3 class="mt-5 text-xl font-medium text-white">Добавьте теплицу и культуры</h3><p class="mt-3 text-sm leading-6 text-[#a3a9b8]">Создайте пространство, грядки и карточки растений.</p></article>
                        <article class="signal-card rounded-2xl p-6"><p class="text-sm text-[#70d99c]">Шаг 2</p><h3 class="mt-5 text-xl font-medium text-white">Записывайте показатели</h3><p class="mt-3 text-sm leading-6 text-[#a3a9b8]">Вносите измерения температуры и освещения в удобный журнал.</p></article>
                        <article class="signal-card rounded-2xl p-6"><p class="text-sm text-[#70d99c]">Шаг 3</p><h3 class="mt-5 text-xl font-medium text-white">Выполняйте процедуры вовремя</h3><p class="mt-3 text-sm leading-6 text-[#a3a9b8]">Отмечайте завершённые задачи и следите за этапами развития.</p></article>
                    </div>
                </div>
            </section>

            <section id="questions" class="border-b section-line bg-[#0b0e13] py-20 sm:py-24">
                <div class="mx-auto max-w-3xl px-6 sm:px-8">
                    <p class="text-sm font-medium text-[#70d99c]">Вопросы</p>
                    <h2 class="mt-4 text-4xl font-semibold leading-tight text-white">Частые вопросы</h2>
                    <div class="mt-10 space-y-3">
                        <details class="signal-card rounded-xl p-5"><summary class="cursor-pointer list-none pr-8 text-base font-medium text-white">Можно ли вести несколько теплиц?</summary><p class="mt-3 text-sm leading-6 text-[#a3a9b8]">Да. Для каждой теплицы можно учитывать отдельные культуры, посадки, показатели и процедуры.</p></details>
                        <details class="signal-card rounded-xl p-5"><summary class="cursor-pointer list-none pr-8 text-base font-medium text-white">Какие данные можно фиксировать?</summary><p class="mt-3 text-sm leading-6 text-[#a3a9b8]">Температуру, освещённость и другие показатели, необходимые для наблюдения за условиями выращивания.</p></details>
                        <details class="signal-card rounded-xl p-5"><summary class="cursor-pointer list-none pr-8 text-base font-medium text-white">Подходит ли система для учёбы?</summary><p class="mt-3 text-sm leading-6 text-[#a3a9b8]">Да. Проект работает локально и подходит для ведения учебного примера теплицы с несколькими культурами.</p></details>
                    </div>
                </div>
            </section>

            <section class="bg-[#07080d] py-20 sm:py-24">
                <div class="mx-auto max-w-6xl px-6 sm:px-8 lg:px-10">
                    <div class="signal-card flex flex-col items-start justify-between gap-8 rounded-2xl p-8 sm:flex-row sm:items-center sm:p-10">
                        <div><h2 class="text-3xl font-semibold text-white">Начните вести свою теплицу</h2><p class="mt-3 max-w-xl text-base leading-7 text-[#a3a9b8]">Создайте первую теплицу и добавьте выращиваемые культуры.</p></div>
                        @auth
                            <a href="{{ route('dashboard') }}" class="button-primary shrink-0 rounded-lg px-4 py-3 text-base font-semibold">Открыть кабинет</a>
                        @elseif (Route::has('register'))
                            <a href="{{ route('register') }}" class="button-primary shrink-0 rounded-lg px-4 py-3 text-base font-semibold">Создать аккаунт</a>
                        @endif
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t section-line bg-[#07080d] py-8">
            <div class="mx-auto flex max-w-6xl flex-col gap-3 px-6 text-sm text-[#707786] sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
                <p>© {{ date('Y') }} Умная теплица</p>
                <p>Учебный проект для контроля выращивания растений</p>
            </div>
        </footer>
    </div>
</body>
</html>
