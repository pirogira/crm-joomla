# Лог прогона unit-тестов

## Запуск тестов

```bash
composer install
./vendor/bin/phpunit
```

Или с подробным выводом:

```bash
./vendor/bin/phpunit --testdox
```

## Ожидаемый вывод (все тесты зелёные)

```
PHPUnit 10.x.x by Sebastian Bergmann and contributors.

Runtime:       PHP 8.x.x
Configuration: c:\Users\pirog\projects\1\phpunit.xml

.........                                                        11 / 11 (100%)

Time: 00:00.123, Memory: 10.00 MB

OK (11 tests, 25 assertions)
```

## Вывод с --testdox (читаемые имена тестов)

```
PHPUnit 10.x.x by Sebastian Bergmann and contributors.

Runtime:       PHP 8.x.x

Stage
 ✔ Order contains all stages
 ✔ Is valid accepts all known stages
 ✔ Is valid rejects unknown stage
 ✔ Rank returns correct index
 ✔ Rank throws for unknown stage
 ✔ Aware is above ice and touched

StageMachine
 ✔ Transition ice to touched succeeds
 ✔ Transition touched to aware without event fails
 ✔ Transition touched to aware with event succeeds
 ✔ Transition to demo planned from ice fails
 ✔ Transition to demo planned from touched fails
 ✔ Transition to demo done without recent demo fails
 ✔ Transition to demo done with recent demo succeeds
 ✔ Transition skip step fails
 ✔ Transition to invalid stage throws
 ✔ Get current stage throws for missing company
 ✔ Full pipeline ice to activated

OK (17 tests, 36 assertions)
```

## Примечание

Если PHP или Composer не установлены, установите их и выполните команды выше. Тесты не требуют Joomla или базы данных — используется `FakeDatabaseConnector`.
