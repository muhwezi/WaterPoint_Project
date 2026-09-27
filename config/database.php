<?php

declare(strict_types=1);


// =========================================================
// DATABASE CONFIGURATION
// =========================================================

$host = 'localhost';
$port = '3306';
$dbname = 'waterpoint_db';
$username = 'root';
$password = '';
$charset = 'utf8mb4';


// =========================================================
// PDO DSN
// =========================================================

$dsn =
    "mysql:host={$host};" .
    "port={$port};" .
    "dbname={$dbname};" .
    "charset={$charset}";


// =========================================================
// PDO OPTIONS
// =========================================================

$options = [

    PDO::ATTR_ERRMODE =>
        PDO::ERRMODE_EXCEPTION,

    PDO::ATTR_DEFAULT_FETCH_MODE =>
        PDO::FETCH_ASSOC,

    PDO::ATTR_EMULATE_PREPARES =>
        false,

    PDO::ATTR_STRINGIFY_FETCHES =>
        false
];


// =========================================================
// CONNECT
// =========================================================

try {

    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        $options
    );

} catch (PDOException $e) {

    error_log(
        'Database connection failed: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'The system could not connect to the database.'
    );
}