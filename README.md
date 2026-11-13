# Умная теплица

Веб приложение для учёта теплиц, культур, сортов, стадий роста, показателей температуры и освещённости, полива и других процедур ухода.

## Возможности

- регистрация и вход пользователей;
- роли пользователя и администратора;
- создание теплиц и посадок;
- фиксация стадий роста из утверждённого справочника;
- журнал температуры и освещённости;
- планирование и выполнение процедур ухода;
- заявки на новые культуры и сорта;
- административная проверка заявок и назначение стадий роста;
- поиск по теплицам, культурам и сортам;
- публичное версионированное JSON API.

## Стек

PHP 8.3+, Laravel 13, Blade, MySQL, Vite, Tailwind CSS, Alpine.js и Three.js.

## Установка

```bash
git clone https://github.com/egasik/smart-greenhouse.git
cd smart-greenhouse
composer install
copy .env.example .env
php artisan key:generate
npm install
```

В Linux и macOS вместо `copy` используйте `cp`.

Создайте базу MySQL и заполните `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_greenhouse
DB_USERNAME=root
DB_PASSWORD=
```

Подготовьте структуру и справочники:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Для разработки интерфейса запустите `npm run dev` в отдельном терминале.

## Тестовые пользователи

Учётные записи создаёт `DemoUsersSeeder`. Актуальные адреса и пароли указаны в самом сидере. Перед публикацией проекта замените демонстрационные пароли.

## Основные модули

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/V1/
│   ├── Middleware/
│   └── Resources/Api/V1/
├── Models/
database/
├── migrations/
└── seeders/
resources/
├── css/
├── js/
└── views/
routes/
├── api.php
├── auth.php
└── web.php
tests/
├── Feature/
└── Unit/
```

## API

API доступно по адресу `/api/v1`. Описание методов находится в [docs/api.md](docs/api.md), проверочная браузерная страница — `/api-test.html`.

## База данных

Структура создаётся миграциями. ER схема и пояснение находятся в [docs/database.md](docs/database.md). Фиксированные наборы статусов хранятся как `ENUM`, числовые показатели температуры и освещённости — как `DOUBLE`.

## Проверка

```bash
php artisan test
npm run build
```

Перед сдачей также проверьте формы и навигацию при ширине экрана 390 пикселей.
