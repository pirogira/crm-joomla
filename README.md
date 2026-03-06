# CRM для Joomla 4/5

Компонент CRM: управление стадиями компаний, журнал событий, discovery-формы. Разработан с использованием Cursor — AI помогал с бойлерплейтом и рефакторингом, основную архитектуру и логику делал сам.

---

## Инструкция запуска (тесты)

```bash
composer install
./vendor/bin/phpunit
```

(Для запуска прототипа нужна установленная Joomla 4/5 и установка компонента из `package/com_crm/` — см. раздел «Установка» ниже.)

---

## Архитектура

```
┌─────────────────────────────────────────────────────────────────┐
│  Joomla MVC (site)                                               │
│  ┌─────────────┐  ┌──────────────┐  ┌─────────────────────────┐  │
│  │ Controller  │→ │ Model        │→ │ View (карточка компании) │  │
│  │ (task)      │  │ CompanyModel │  │ default.php             │  │
│  └──────┬──────┘  └──────┬───────┘  └─────────────────────────┘  │
│         │                │                                       │
│         ▼                ▼                                       │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │ CrmCore                                                      │ │
│  │  • StageMachine (переходы стадий)                            │ │
│  │  • State-классы (ограничения по crm_events)                  │ │
│  │  • DatabaseConnector → #__companies, #__crm_events            │ │
│  └─────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

- **Данные**: `DatabaseConnector` + `DatabaseConnectorInterface` (для тестов)
- **Логика**: `StageMachine` + State-паттерн (9 стадий, 3 бизнес-ограничения)
- **UI**: Joomla view — стадия, кнопки действий, инструкция, лента событий

---

## Модель данных

| Таблица | Назначение |
|---------|------------|
| `#__companies` | Компании: id, name, current_stage, created_at |
| `#__crm_events` | Журнал событий: company_id, event_type, payload (JSON), created_at |
| `#__discovery_forms` | Discovery-анкеты: company_id, form_key, data (JSON), created_at |

**Стадии**: Ice → Touched → Aware → Interested → Demo_planned → Demo_done → Committed → Customer → Activated

---

## Тестирование

### Unit-тесты (17 шт.)

- **StageTest**: порядок стадий, валидация, ранг, ограничения
- **StageMachineTest**: переходы между стадиями, ограничения (разговор с ЛПР, demo_conducted за 60 дней), полный пайплайн Ice→Activated

### Запуск

```bash
composer install
./vendor/bin/phpunit
```

Лог прогона: `docs/TEST_RUN_LOG.md`  
Цикл «баг → фикс → зелёные тесты»: `docs/BUG_FIX_CYCLE.md`

---

## Установка

1. Установить компонент из ZIP (`package/com_crm/` — заархивировать папку `com_crm`).
2. Выполнить SQL из `package/com_crm/administrator/sql/install.mysql.utf8mb4.sql` (заменить `#__` на префикс БД, например `jos_`).
3. Добавить компанию в `jos_companies` (или создать через код).

---

## Рабочий прототип

**URL**: http://pirojosf.beget.tech/

**Карточка компании**: http://pirojosf.beget.tech/index.php?option=com_crm&view=company&id=1

Доступ: логин/пароль от Joomla (если нужен демо-доступ — уточнить).

---

## AI-Workflow

Использовал Cursor для ускорения: генерация DDL, CRUD, State-классов, view-шаблона, unit-тестов. Промпты формулировал сам, с явными ограничениями («crm_events — единственный источник истины»). Проверял результат тестами и ручным прогоном. AI помог с бойлерплейтом, архитектуру и бизнес-правила делал сам.

---

## Что прислать

- **Репозиторий**: https://github.com/pirogira/crm-joomla
- **Прототип**: http://pirojosf.beget.tech/index.php?option=com_crm&view=company&id=1

---

## Что бы улучшил

- Список компаний с фильтром по стадии
- Discovery-формы: UI для создания/редактирования
- Интеграционные тесты с реальной БД
- CI/CD (GitHub Actions)
