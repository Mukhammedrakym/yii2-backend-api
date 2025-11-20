# Yii2 Library REST API

````

2. **Стек / возможности**
- Yii2 Basic
- JWT (firebase/php-jwt)
- REST JSON ответы
- Docker (php-fpm + MySQL)
- Codeception unit-тесты

3. **Требования**
- Docker + Docker Compose
- PHP 8.2 (если запуск локально)
- Composer

4. **Запуск через Docker**
```bash
git clone <repo>
cd yii2-backend-api

# Создать .env (если используется)
cp .env.example .env

docker compose up -d
docker compose exec php composer install
docker compose exec php php yii migrate
docker compose exec php vendor/bin/codecept run unit
````

Указать, что API доступен на `http://localhost:8080`.

5. **Локальный запуск (если нужен)**

   ```bash
   composer install
   php yii migrate
   php yii serve
   ```

6. **Конфигурация окружения**

   - Перечислить переменные `.env` (DB_HOST, DB_NAME, JWT_SECRET, т.д.)
   - Как работать с тестовой БД (`library_test`)

7. **API эндпоинты**
   Табличка или список:
   ```
   POST /users – регистрация
   POST /auth/login – получить JWT
   GET /users/{id} – профиль (Bearer JWT)
   GET /books – список книг
   POST /books – создать книгу (Bearer JWT)
   GET /books/{id}
   PUT /books/{id} – обновление (Bearer JWT, только владелец)
   DELETE /books/{id} – удаление (Bearer JWT, только владелец)
   ```
