# Yii2 Library REST API

REST API для управления библиотекой книг: регистрация пользователей, JWT авторизация и CRUD книг. Проект собран на Yii2 Basic, докеризован и покрыт юнит-тестами.

## Стек

- PHP 8.3, Yii2 Basic
- MySQL 8.0
- JWT (firebase/php-jwt)
- Docker Compose
- Codeception (unit tests)

## Быстрый старт (Docker)

```bash
git clone <repo>
cd yii2-backend-api
cp .env.example .env

docker compose up -d
docker compose exec php composer install
docker compose exec php php yii migrate
docker compose exec php vendor/bin/codecept run unit
```

API: `http://localhost:8080`

## Эндпоинты

| Method | URL         | Защита             | Описание             |
| ------ | ----------- | ------------------ | -------------------- |
| POST   | /users      | -                  | Регистрация          |
| POST   | /auth/login | -                  | JWT логин            |
| GET    | /users/{id} | Bearer JWT         | Профиль пользователя |
| GET    | /books      | -                  | Список книг          |
| POST   | /books      | Bearer JWT         | Создать книгу        |
| GET    | /books/{id} | -                  | Получить книгу       |
| PUT    | /books/{id} | Bearer JWT (owner) | Обновить книгу       |
| DELETE | /books/{id} | Bearer JWT (owner) | Удалить книгу        |
