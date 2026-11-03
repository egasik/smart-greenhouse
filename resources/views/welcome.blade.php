<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Умная теплица — контроль выращивания растений</title>
    <meta name="description"
          content="Умная теплица помогает учитывать культуры, отслеживать показатели среды и планировать уход за растениями.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .scene-container {
            background:
                radial-gradient(circle at 50% 50%, rgba(76, 195, 138, .12), transparent 62%);
        }

        .scene-container canvas {
            display: block;
            height: 100%;
            width: 100%;
        }

        .scene-caption {
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(17, 20, 28, .74);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, .12),
                0 18px 50px rgba(0, 0, 0, .28);
            backdrop-filter: blur(16px);
        }

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 700ms cubic-bezier(.22, 1, .36, 1),
                        transform 700ms cubic-bezier(.22, 1, .36, 1);
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .tagline-word {
            color: rgba(238, 240, 245, .3);
            transition: color 500ms cubic-bezier(.22, 1, .36, 1);
        }

        .tagline-word.is-active {
            color: #eef0f5;
        }
    </style>
</head>

<body class="bg-[#07080d] text-[#eef0f5] antialiased">
    <a href="#content"
       class="absolute left-4 top-4 z-50 -translate-y-20 rounded-lg bg-[#4cc38a] px-4 py-2 text-sm font-medium text-[#07080d] transition-transform focus:translate-y-0">
        Перейти к содержимому
    </a>

    <header class="absolute inset-x-0 top-0 z-30">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-8 py-6">
            <a href="{{ url('/') }}" class="flex items-center gap-3 text-lg font-semibold">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#4cc38a] text-[#07080d]">
                    G
                </span>
                Умная теплица
            </a>

            <div class="flex items-center gap-6 text-sm text-[#a3a9b8]">
                <a href="#features" class="transition-colors duration-300 hover:text-[#eef0f5]">
                    Возможности
                </a>
                <a href="#process" class="transition-colors duration-300 hover:text-[#eef0f5]">
                    Как работает
                </a>
                <a href="#faq" class="transition-colors duration-300 hover:text-[#eef0f5]">
                    Вопросы
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="rounded-lg bg-[#4cc38a] px-4 py-2 font-medium text-[#07080d] transition-colors duration-300 hover:bg-[#67d89e] active:scale-[.98]">
                        Открыть кабинет
                    </a>
                @else
                    <a href="{{ route('login') }}" class="transition-colors duration-300 hover:text-[#eef0f5]">
                        Войти
                    </a>

                    <a href="{{ route('register') }}"
                       class="rounded-lg bg-[#4cc38a] px-4 py-2 font-medium text-[#07080d] transition-colors duration-300 hover:bg-[#67d89e] active:scale-[.98]">
                        Создать аккаунт
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <main id="content">
        <section class="relative flex min-h-screen items-center overflow-hidden bg-[#07080d]">
    <div id="greenhouse-scene"
         class="scene-container absolute inset-y-0 right-0 w-3/5">
    </div>

    <div class="absolute inset-0 bg-[#07080d]/35"></div>

    <div class="relative z-10 mx-auto w-full max-w-7xl px-8 pt-32">
        <div class="max-w-2xl">
            <p class="mb-6 text-sm text-[#a3a9b8]">
                Контроль среды и ухода за растениями
            </p>

            <h1 class="bg-gradient-to-r from-white to-[#9b9b9b] bg-clip-text text-6xl font-semibold leading-none text-transparent">
                Выращивайте растения<br>
                с точными данными
            </h1>

            <p class="mt-8 max-w-xl text-lg leading-7 text-[#c0c6d1]">
                Создавайте теплицы, отслеживайте показатели среды,
                фиксируйте рост растений и планируйте уход.
            </p>

            <div class="mt-8 flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="glass-button glass-button-accent rounded-lg px-5 py-3 text-base font-semibold">
                        Перейти к теплицам
                    </a>
                @else
                    <a href="{{ route('register') }}"
                       class="glass-button glass-button-accent rounded-lg px-5 py-3 text-base font-semibold">
                        Начать вести теплицу
                    </a>
                @endauth

                <a href="#signals"
                   class="glass-button rounded-lg px-5 py-3 text-base font-medium text-[#eef0f5]">
                    Посмотреть показатели
                </a>
            </div>
        </div>

        <div class="scene-caption mt-16 max-w-3xl rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#a3a9b8]">Интерактивная модель</p>
                    <p class="mt-1 text-xl font-medium">Наведите курсор на теплицу</p>
                </div>

                <p class="text-sm text-[#4cc38a]">
                    Датчики и растения в 3D
                </p>
            </div>
        </div>
    </div>
</section>

        <section class="reveal mx-auto max-w-7xl px-8 py-28">
            <div class="max-w-4xl">
                <p class="text-4xl font-semibold leading-tight text-[#eef0f5]">
                    <span class="tagline-word">Каждое</span>
                    <span class="tagline-word">измерение</span>
                    <span class="tagline-word">помогает</span>
                    <span class="tagline-word">вовремя</span>
                    <span class="tagline-word">заметить</span>
                    <span class="tagline-word">изменения.</span><br>
                    <span class="tagline-word">Каждая</span>
                    <span class="tagline-word">задача</span>
                    <span class="tagline-word">помогает</span>
                    <span class="tagline-word">не</span>
                    <span class="tagline-word">пропустить</span>
                    <span class="tagline-word">уход.</span>
                </p>
            </div>
        </section>

        <section id="features" class="bg-[#11141c] py-28">
            <div class="mx-auto max-w-7xl px-8">
                <p class="text-sm text-[#4cc38a]">Возможности системы</p>
                <h2 class="mt-4 max-w-2xl text-5xl font-semibold leading-none">
                    Все данные о выращивании в одном месте
                </h2>

                <div class="mt-16 grid grid-cols-3 gap-6">
                    <article class="reveal rounded-2xl border border-white/10 bg-[#1a1e28] p-7">
                        <p class="text-sm text-[#4cc38a]">01</p>
                        <h3 class="mt-8 text-2xl font-medium">Теплицы и посадки</h3>
                        <p class="mt-4 leading-6 text-[#a3a9b8]">
                            Создавайте теплицы, добавляйте культуры и фиксируйте дату каждой посадки.
                        </p>
                    </article>

                    <article class="reveal rounded-2xl border border-white/10 bg-[#1a1e28] p-7">
                        <p class="text-sm text-[#4cc38a]">02</p>
                        <h3 class="mt-8 text-2xl font-medium">Показатели среды</h3>
                        <p class="mt-4 leading-6 text-[#a3a9b8]">
                            Сохраняйте температуру и освещённость для всей теплицы или отдельной посадки.
                        </p>
                    </article>

                    <article class="reveal rounded-2xl border border-white/10 bg-[#1a1e28] p-7">
                        <p class="text-sm text-[#4cc38a]">03</p>
                        <h3 class="mt-8 text-2xl font-medium">Уход без пропусков</h3>
                        <p class="mt-4 leading-6 text-[#a3a9b8]">
                            Планируйте полив, подкормку, проветривание и отмечайте выполненные процедуры.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section id="process" class="mx-auto max-w-7xl px-8 py-28">
            <p class="text-sm text-[#4cc38a]">Как начать</p>
            <h2 class="mt-4 text-5xl font-semibold leading-none">
                Три шага до порядка в теплице
            </h2>

            <div class="mt-16 grid grid-cols-3 gap-12">
                <div class="reveal">
                    <p class="text-sm text-[#a3a9b8]">Шаг 1</p>
                    <p class="mt-4 text-2xl font-medium">Создайте теплицу</p>
                    <p class="mt-3 leading-6 text-[#a3a9b8]">
                        Укажите название и расположение, чтобы разделять данные разных теплиц.
                    </p>
                </div>

                <div class="reveal">
                    <p class="text-sm text-[#a3a9b8]">Шаг 2</p>
                    <p class="mt-4 text-2xl font-medium">Добавьте посадки</p>
                    <p class="mt-3 leading-6 text-[#a3a9b8]">
                        Выберите культуру, дату и количество растений.
                    </p>
                </div>

                <div class="reveal">
                    <p class="text-sm text-[#a3a9b8]">Шаг 3</p>
                    <p class="mt-4 text-2xl font-medium">Ведите наблюдения</p>
                    <p class="mt-3 leading-6 text-[#a3a9b8]">
                        Записывайте показатели и выполняйте запланированные процедуры ухода.
                    </p>
                </div>
            </div>
        </section>

        <section id="faq" class="bg-[#11141c] py-28">
            <div class="mx-auto max-w-4xl px-8">
                <p class="text-sm text-[#4cc38a]">Частые вопросы</p>
                <h2 class="mt-4 text-5xl font-semibold leading-none">
                    О системе
                </h2>

                <div class="mt-12 space-y-4">
                    <details class="rounded-xl border border-white/10 bg-[#1a1e28] p-5">
                        <summary class="cursor-pointer text-lg font-medium">
                            Можно ли вести несколько теплиц
                        </summary>
                        <p class="mt-4 leading-6 text-[#a3a9b8]">
                            Да. Для каждой теплицы хранятся отдельные посадки, показатели и задачи по уходу.
                        </p>
                    </details>

                    <details class="rounded-xl border border-white/10 bg-[#1a1e28] p-5">
                        <summary class="cursor-pointer text-lg font-medium">
                            Какие показатели можно записывать
                        </summary>
                        <p class="mt-4 leading-6 text-[#a3a9b8]">
                            В текущей версии доступны температура и освещённость.
                        </p>
                    </details>

                    <details class="rounded-xl border border-white/10 bg-[#1a1e28] p-5">
                        <summary class="cursor-pointer text-lg font-medium">
                            Можно ли планировать полив
                        </summary>
                        <p class="mt-4 leading-6 text-[#a3a9b8]">
                            Да. Полив выбирается как тип процедуры, после выполнения задачу можно отметить завершённой.
                        </p>
                    </details>

                    <details class="rounded-xl border border-white/10 bg-[#1a1e28] p-5">
                        <summary class="cursor-pointer text-lg font-medium">
                            Можно ли добавлять свои культуры
                        </summary>
                        <p class="mt-4 leading-6 text-[#a3a9b8]">
                            Да. В справочнике можно указать название культуры, сорт и описание.
                        </p>
                    </details>

                    <details class="rounded-xl border border-white/10 bg-[#1a1e28] p-5">
                        <summary class="cursor-pointer text-lg font-medium">
                            Будет ли доступна интеграция с погодой
                        </summary>
                        <p class="mt-4 leading-6 text-[#a3a9b8]">
                            Да. В дальнейшем систему можно подключить к бесплатному погодному API при наличии доступа в интернет.
                        </p>
                    </details>

                    <details class="rounded-xl border border-white/10 bg-[#1a1e28] p-5">
                        <summary class="cursor-pointer text-lg font-medium">
                            Где хранятся данные
                        </summary>
                        <p class="mt-4 leading-6 text-[#a3a9b8]">
                            Данные сохраняются в локальной базе MySQL проекта Laravel.
                        </p>
                    </details>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-8 py-28">
            <div class="rounded-2xl bg-[#4cc38a] p-12 text-[#07080d]">
                <p class="text-sm font-medium">Начало работы</p>
                <h2 class="mt-4 max-w-2xl text-5xl font-semibold leading-none">
                    Создайте первую теплицу и начните вести наблюдения
                </h2>

                <div class="mt-8">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex rounded-lg bg-[#07080d] px-5 py-3 font-semibold text-[#eef0f5] transition-colors duration-300 hover:bg-[#1a1e28] active:scale-[.98]">
                            Открыть кабинет
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex rounded-lg bg-[#07080d] px-5 py-3 font-semibold text-[#eef0f5] transition-colors duration-300 hover:bg-[#1a1e28] active:scale-[.98]">
                            Создать аккаунт
                        </a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-8 py-8 text-sm text-[#a3a9b8]">
            <p>Умная теплица</p>

            <div class="flex gap-5">
                <a href="#" class="hover:text-[#eef0f5]">Политика конфиденциальности</a>
                <a href="#" class="hover:text-[#eef0f5]">Условия использования</a>
            </div>
        </div>
    </footer>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.reveal').forEach((element) => {
            observer.observe(element);
        });

        const taglineObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const words = entry.target.querySelectorAll('.tagline-word');

                    words.forEach((word, index) => {
                        setTimeout(() => {
                            word.classList.add('is-active');
                        }, index * 90);
                    });

                    taglineObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.45 });

        const tagline = document.querySelector('.tagline-word')?.closest('section');

        if (tagline) {
            taglineObserver.observe(tagline);
        }
    </script>
</body>
</html>
