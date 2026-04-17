# TuneHub Review Notes

Этот файл не про продуктовую документацию, а про системные замечания по репозиторию и поставке.

## Repository-Level Issues

- В репозитории нет одного очевидного source of truth для сборки: рядом лежат root [composer.json](./composer.json) и [package.json](./package.json), а также отдельные [backend/composer.json](./backend/composer.json) и [frontend/package.json](./frontend/package.json).
- CI в [.github/workflows/ci.yml](./.github/workflows/ci.yml) валидирует npm из `backend/`, хотя фронтенд живет в `frontend/`. В таком виде pipeline не гарантирует, что реально собирается то приложение, которое отдается пользователю.
- Тестовый контур хрупкий: [backend/phpunit.xml](./backend/phpunit.xml) задает `DB_DATABASE=testing`, и конфигурация легко расходится с реальным способом запуска тестов.
- В git закоммичены зависимости и артефакты сборки: `vendor/`, `backend/vendor/`, `backend/public/build/`, а также IDE-мусор вроде `.idea/`. Это ухудшает reviewability, обновление зависимостей и качество diff'ов.

## Why It Matters

- PR'ы зашумляются vendor/build-файлами и по ним тяжелее ревьюить реальные изменения.
- CI не является надежным quality gate, потому что проверяет не ту структуру проекта, которую реально использует приложение.
- Репозиторий выглядит как несколько полусвязанных приложений без явно задокументированного build entrypoint.
- Локальные допущения начинают просачиваться в prod, потому что окружения и сборочная модель не сведены к одной схеме.

## What Should Be True

1. Должен быть один понятный backend root и один понятный frontend root.
2. CI должен явно устанавливать зависимости и собирать оба приложения из их настоящих директорий.
3. Vendor, build artifacts и IDE-файлы должны быть в `.gitignore`, а не в истории git.
4. Тестовая конфигурация должна совпадать с реальным способом запуска тестов, а не жить отдельно от него.
