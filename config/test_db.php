<?php

$db = require __DIR__ . '/db.php';

$db['dsn'] = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    getenv('DB_HOST') ?: 'db',
    getenv('DB_PORT') ?: '3306',
    'library_test'
);

return $db;