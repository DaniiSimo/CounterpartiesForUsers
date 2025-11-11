# Система управления контрагентами

Система для управления контрагентами с регистрацией/авторизацией пользователей и возможностью добавления контрагентов по ИНН с получением данных из API DaData.

## Описание проекта

Проект представляет собой REST API для управления контрагентами пользователей. Основные возможности:

- **Регистрация и авторизация пользователей** - регистрация по email и паролю с последующей авторизацией через Laravel Sanctum
- **Управление контрагентами** - создание контрагентов по ИНН с автоматическим получением данных из API DaData
- **Документация API** - полная документация через Swagger UI

При создании контрагента система автоматически получает из API DaData следующие данные:
- `name.short_with_opf` - краткое название организации
- `ogrn` - ОГРН организации
- `address.unrestricted_value` - полный адрес организации

## Технологический стек

- **Backend**: PHP 8.3, Laravel 11
- **База данных**: PostgreSQL 16
- **Веб-сервер**: Nginx
- **Аутентификация**: Laravel Sanctum
- **Документация API**: Swagger/OpenAPI (l5-swagger)
- **Контейнеризация**: Docker, Docker Compose
- **Код-стайл**: Laravel Pint (PSR-12)

## Требования

- Docker и Docker Compose
- Make (опционально, для упрощения работы с командной строкой)

## Быстрый старт

### Запуск проекта

#### С использованием Docker Compose

```bash
docker compose -f compose.yaml up -d
```

#### С использованием Make (если доступен)

```bash
make up
```

### Остановка проекта

#### С использованием Docker Compose

```bash
docker compose -f compose.yaml down
```

#### С использованием Make

```bash
make down
```

## Команды Makefile

Проект включает Makefile со следующими командами для удобной работы:

| Команда | Описание                                                  |
|---------|-----------------------------------------------------------|
| `make up` | Поднять сервисы в фоне                                    |
| `make down` | Остановить и удалить сервисы + сети                       |
| `make hard_down` | Остановить и удалить сервисы + сети + хранилища (volumes) |
| `make logs` | Просмотр логов (фолловинг) php+nginx                      |
| `make app-shell` | Войти в шелл PHP-контейнера                               |
| `make db-shell` | Войти в шелл БД-контейнера                                |
| `make ps` | Список контейнеров                                        |
| `make cache-clear` | Очистка кеша/конфигов/роутов                              |

## Настройка окружения

Перед запуском проекта необходимо настроить переменные окружения. Проект использует следующие файлы конфигурации:

- `.env` - основные настройки приложения
- `.env.database` - настройки базы данных PostgreSQL
- `.env.nginx` - настройки Nginx (опционально)

### Основные переменные окружения (.env)

#### Настройки приложения

```env
APP_NAME=CounterpartiesForUsers
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost
```

- `APP_NAME` - имя приложения
- `APP_KEY` - ключ шифрования приложения (генерируется командой `php artisan key:generate`)
- `APP_DEBUG` - режим отладки (true/false)
- `APP_URL` - базовый URL приложения

#### Настройки базы данных

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

- `DB_CONNECTION` - драйвер базы данных (pgsql, mysql, sqlite)
- `DB_HOST` - хост базы данных (в Docker - имя сервиса `db`)
- `DB_PORT` - порт базы данных
- `DB_DATABASE` - имя базы данных
- `DB_USERNAME` - имя пользователя базы данных
- `DB_PASSWORD` - пароль базы данных

#### Настройки DaData API

```env
DADATA_TOKEN=your_dadata_token
```

- `DADATA_TOKEN` - токен для доступа к API DaData. Получить можно на [dadata.ru](https://dadata.ru/api/)

#### Настройки логирования

```env
LOG_CHANNEL=stack
LOG_LEVEL=debug
LOG_LEVEL_EXTERNAL_API=warning
LOG_LEVEL_EXTERNAL_API_DADATA=warning
```

- `LOG_CHANNEL` - канал логирования
- `LOG_LEVEL` - уровень логирования
- `LOG_LEVEL_EXTERNAL_API` - уровень логирования для внешних API
- `LOG_LEVEL_EXTERNAL_API_DADATA` - уровень логирования для DaData API

### Настройки базы данных (.env.database)

```env
POSTGRES_DB=laravel
POSTGRES_USER=postgres
POSTGRES_PASSWORD=your_password
SUPER_USER=postgres
```

- `POSTGRES_DB` - имя базы данных PostgreSQL
- `POSTGRES_USER` - пользователь базы данных
- `POSTGRES_PASSWORD` - пароль пользователя
- `SUPER_USER` - суперпользователь (по умолчанию postgres)

### Настройки Nginx (.env.nginx)

```env
PORT=80
```

- `PORT` - порт, на котором будет доступно приложение (по умолчанию 80)

## API Endpoints

Базовый URL API: `http://localhost/api`

Все ответы API возвращаются в формате JSON.

### 1. Регистрация пользователя

**POST** `/api/users`

Регистрация нового пользователя в системе.

**Тело запроса:**
```json
{
  "name": "Иван Иванов",
  "email": "user@example.com",
  "password": "password123"
}
```

**Параметры:**
- `name` (string, optional) - имя пользователя
- `email` (string, required) - email пользователя (уникальный)
- `password` (string, required) - пароль пользователя

**Успешный ответ (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Иван Иванов",
    "email": "user@example.com"
  }
}
```

**Ошибка валидации (422):**
```json
{
  "message": "Ошибка валидации",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password field is required."]
  }
}
```

### 2. Аутентификация (получение токена)

**POST** `/api/tokens`

Аутентификация пользователя и получение токена доступа.

**Тело запроса:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Параметры:**
- `email` (string, required) - email пользователя
- `password` (string, required) - пароль пользователя

**Успешный ответ (200):**
```json
{
  "data": {
    "token": "1|abcdef1234567890..."
  }
}
```

**Ошибка аутентификации (401):**
```json
{
  "message": "These credentials do not match our records"
}
```

**Ошибка валидации (422):**
```json
{
  "message": "Ошибка валидации",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

**Превышен лимит попыток (429):**
```json
{
  "message": "Too many login attempts. Please try again in 60 seconds."
}
```

### 3. Выход (удаление токена)

**DELETE** `/api/tokens`

Удаление токена аутентификации текущего пользователя.

**Заголовки:**
- `Authorization: Bearer {token}` - токен доступа

**Успешный ответ (204):**
Нет содержимого

**Ошибка (400):**
```json
{
  "message": "Logout failed."
}
```

### 4. Получение списка контрагентов

**GET** `/api/counterparties`

Получение списка всех контрагентов текущего аутентифицированного пользователя.

**Заголовки:**
- `Authorization: Bearer {token}` - токен доступа

**Успешный ответ (200):**
```json
{
  "data": [
    {
      "name": "ООО \"Пример\"",
      "ogrn": "1027700132195",
      "address": "г Москва, ул Примерная, д 1",
    }
  ]
}
```

**Ошибка аутентификации (401):**
```json
{
  "message": "Unauthenticated."
}
```

### 5. Создание контрагента

**POST** `/api/counterparties`

Создание нового контрагента по ИНН. Данные автоматически получаются из API DaData.

**Заголовки:**
- `Authorization: Bearer {token}` - токен доступа

**Тело запроса:**
```json
{
  "inn": "7707083893"
}
```

**Параметры:**
- `inn` (string, required) - ИНН организации (10 или 12 цифр)

**Успешный ответ (200):**
```json
{
  "data": [
    {
      "name": "ООО \"Пример\"",
      "ogrn": "1027700132195",
      "address": "г Москва, ул Примерная, д 1",
    }
  ]
}
```

**Ошибка валидации (422):**
```json
{
  "message": "Ошибка валидации",
  "errors": {
    "inn": ["The inn field is required."]
  }
}
```

**Ошибка уникальности (409):**
```json
{
  "message": "Counterparty conflict (duplicate OGRN)",
  "ogrns": ["1027700132195"]
}
```

**Ошибка аутентификации (401):**
```json
{
  "message": "Unauthenticated."
}
```

## Аутентификация

Проект использует **Laravel Sanctum** для аутентификации API через токены.

### Как работает аутентификация

1. **Регистрация**: Пользователь регистрируется через `/api/users`, создавая учетную запись с email и паролем.

2. **Авторизация**: Пользователь отправляет запрос на `/api/tokens` с email и паролем. В ответ получает токен доступа.

3. **Использование токена**: Для доступа к защищенным эндпоинтам необходимо передавать токен в заголовке `Authorization`:
   ```
   Authorization: Bearer {token}
   ```

4. **Выход**: Пользователь может удалить свой токен через `DELETE /api/tokens`, передав токен в заголовке `Authorization`.

### Защищенные эндпоинты

Следующие эндпоинты требуют аутентификации:
- `GET /api/counterparties` - получение списка контрагентов
- `POST /api/counterparties` - создание контрагента
- `DELETE /api/tokens` - удаление токена

## Документация API (Swagger)

Интерактивная документация API доступна через Swagger UI:

**URL**: `http://localhost/api/documentation`

В Swagger UI вы можете:
- Просмотреть все доступные эндпоинты
- Увидеть структуру запросов и ответов
- Протестировать API прямо из браузера
- Авторизоваться и использовать защищенные эндпоинты

### Использование Swagger UI

1. Откройте `http://localhost/api/documentation` в браузере
2. Для тестирования защищенных эндпоинтов:
    - Сначала получите токен через `POST /api/tokens`
    - Нажмите кнопку "Authorize" в верхней части страницы
    - Введите токен в формате `Bearer {token}` или просто `{token}`
    - Нажмите "Authorize"
    - Теперь вы можете тестировать защищенные эндпоинты

## Логирование

Логи внешних API (DaData) записываются в отдельный канал `external_api_dadata` с настраиваемым уровнем логирования.

## Разработка

### Код-стайл

Проект следует стандарту PSR-12. Для проверки и исправления кода используется Laravel Pint:

```bash
docker compose exec app ./vendor/bin/pint
```
