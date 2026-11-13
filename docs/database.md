# Структура базы данных

```mermaid
erDiagram
    USERS ||--o{ GREENHOUSES : owns
    GREENHOUSES ||--o{ PLANTINGS : contains
    CROPS ||--o{ VARIETIES : has
    VARIETIES ||--o{ PLANTINGS : selected
    VARIETIES }o--o{ GROWTH_STAGE_DEFINITIONS : allows
    PLANTINGS ||--o{ GROWTH_STAGES : records
    PLANTINGS ||--o{ CARE_TASKS : schedules
    GREENHOUSES ||--o{ MEASUREMENTS : stores
    USERS ||--o{ CATALOG_REQUESTS : submits
```

Миграции являются единственным источником структуры БД. Первичные ключи используют `BIGINT UNSIGNED`, внешние ключи совпадают с ними по типу. Закрытые наборы значений хранятся как `ENUM`, температура и освещённость — как `DOUBLE`.
