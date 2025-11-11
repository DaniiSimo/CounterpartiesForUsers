DC              ?= docker compose
APP             ?= app
NGINX           ?= nginx
DB              ?= db

.PHONY: up
up: ## Поднять сервисы в фоне
	$(DC) up -d

.PHONY: down
down: ## Остановить и удалить сервисы + сети
	$(DC) down

.PHONY: hard_down
hard_down: ## Остановить и удалить сервисы + сети + хранилищца
	$(DC) down -v

.PHONY: logs
logs: ## Логи (фолловинг) app+nginx
	$(DC) logs -f $(APP) $(NGINX)

.PHONY: app-shell
app-shell: ## Войти в шелл PHP-контейнера
	$(DC) exec $(APP) sh

.PHONY: db-shell
db-shell: ## Войти в шелл БД-контейнера
	$(DC) exec $(DB) sh

.PHONY: ps
ps: ## Список контейнеров
	$(DC) ps

.PHONY: cache-clear
cache-clear: ## Очистка кеша/конфигов/роутов
	$(DC) exec $(APP) php artisan optimize:clear

