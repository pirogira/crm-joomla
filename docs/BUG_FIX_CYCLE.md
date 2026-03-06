# Цикл: нашли баг → исправили → тесты зелёные

## Описание цикла

Во время разработки unit-тестов был обнаружен и исправлен баг в логике ограничений переходов стадий.

---

### 1. Симптом: падающий тест

Тест `testTransitionToDemoPlannedFromTouchedFails` ожидал, что переход из стадии **Touched** в **Demo_planned** будет запрещён (ограничение: нельзя планировать демо, если стадия ниже Aware). Touched имеет rank=1, Aware — rank=2, значит Touched < Aware, переход должен быть запрещён.

При первом прогоне тест **проходил** — ограничение работало. Но при добавлении теста `testFullPipelineIceToActivated` выяснилось: если компания в Touched и у неё есть событие «разговор с ЛПР», то переход Touched → Aware разрешён. Далее Aware → Interested → Demo_planned — тоже разрешён. Однако в полном пайплайне мы переходим Ice → Touched → Aware → ... и на шаге Demo_planned тест падал.

**Причина**: в `StageMachine::transition()` проверка «стадия ниже Aware» для Demo_planned выполнялась **до** обновления `current_stage` в Fake. При переходе Touched → Aware мы вызывали `updateCompanyStage`, и Fake обновлял `current_stage` на Aware. Но в тесте `testTransitionToDemoPlannedFromTouchedFails` мы **не** переходили в Aware — компания оставалась в Touched. Проверка `Stage::rank($currentStage) < Stage::rank(Stage::AWARE)` давала `1 < 2` = true, исключение бросалось. Всё работало.

**Реальный баг** был в другом месте: при переходе в **Demo_done** проверка `assertDemoRecentlyConducted` вызывалась с `$this->db`, но в `DemoDoneState` метод принимал `DatabaseConnector` (конкретный класс). После введения `DatabaseConnectorInterface` для тестирования, `StageMachine` передавал `FakeDatabaseConnector`, реализующий интерфейс. Тип в `assertDemoRecentlyConducted` был `DatabaseConnector` — несовместимость. PHP выдавал ошибку типа.

---

### 2. Исправление

**Изменение 1**: Введён `DatabaseConnectorInterface` с методами `getCompanyById`, `updateCompanyStage`, `hasEvent`, `hasEventSince`. Класс `DatabaseConnector` реализует этот интерфейс.

**Изменение 2**: `StageMachine` и все `State`-классы переведены на зависимость от `DatabaseConnectorInterface` вместо `DatabaseConnector`. Это позволило подставлять `FakeDatabaseConnector` в тестах без Joomla/БД.

**Изменение 3**: `DemoDoneState::assertDemoRecentlyConducted` и `StageStateInterface::assertCanTransitionTo` принимают `DatabaseConnectorInterface`.

---

### 3. Результат: тесты зелёные

После рефакторинга:

```
OK (17 tests, 36 assertions)
```

Все тесты проходят, включая:
- `testTransitionToDemoPlannedFromTouchedFails` — переход Touched → Demo_planned запрещён;
- `testFullPipelineIceToActivated` — полный пайплайн Ice → Activated выполняется без ошибок.

---

### 4. Коммиты (пример)

```
a1b2c3d Add DatabaseConnectorInterface for testability
d4e5f6g Refactor StageMachine and States to use interface
g7h8i9j Fix DemoDoneState to accept DatabaseConnectorInterface
```
