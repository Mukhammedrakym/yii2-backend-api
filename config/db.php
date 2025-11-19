<?php
return [
    'class' => yii\db\Connection::class,
    'dsn' => sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        getenv('DB_HOST') ?: 'db',
        getenv('DB_PORT') ?: '3306',
        getenv('DB_NAME') ?: 'library'
    ),
    'username' => getenv('DB_USER') ?: 'yii',
    'password' => getenv('DB_PASSWORD') ?: 'password',
    'charset'  => 'utf8mb4',
];

