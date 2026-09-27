<?php

declare(strict_types=1);


// =========================================================
// INITIAL ADMIN SETUP
//
// This page:
// - works only from localhost;
// - works only when no ADMIN exists;
// - hashes the password;
// - uses prepared statements;
// - uses CSRF protection.
// =========================================================


// =========================================================
// LOCALHOST ONLY
// =========================================================

$remoteAddress =
    $_SERVER['REMOTE_ADDR'] ?? '';

$allowedLocalAddresses = [
    '127.0.0.1',
    '::1'
];

if (
    !in_array(
        $remoteAddress,
        $allowedLocalAddresses,
        true
    )
) {

    http_response_code(403);

    exit(
        'Initial administrator setup is only available from localhost.'
    );
}


// =========================================================
// SESSION SECURITY
// =========================================================

ini_set(
    'session.use_strict_mode',
    '1'
);

$isHttps =
    isset($_SERVER['HTTPS']) &&
    $_SERVER['HTTPS'] !== 'off';

session_set_cookie_params([
    'httponly' => true,
    'secure' => $isHttps,
    'samesite' => 'Lax',
    'path' => '/'
]);

session_start();

header(
    'Cache-Control: no-store'
);

header(
    'X-Content-Type-Options: nosniff'
);


// =========================================================
// DATABASE
// =========================================================

require_once
    dirname(__DIR__, 2) .
    '/config/database.php';


// =========================================================
// ESCAPE OUTPUT
// =========================================================

function escape(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


// =========================================================
// PHONE NORMALIZATION
// =========================================================

require_once dirname(__DIR__) . '/domain/validation.php';

// CHECK WHETHER ADMIN ALREADY EXISTS
// =========================================================

$adminCheck =
    $pdo->prepare(
        "
        SELECT
            id,
            full_name,
            username
        FROM users
        WHERE role = 'ADMIN'
        LIMIT 1
        "
    );

$adminCheck->execute();

$existingAdmin =
    $adminCheck->fetch();


// =========================================================
// CSRF TOKEN
// =========================================================

if (
    !isset($_SESSION['csrf_token']) ||
    !is_string(
        $_SESSION['csrf_token']
    )
) {

    $_SESSION['csrf_token'] =
        bin2hex(
            random_bytes(32)
        );
}


// =========================================================
// FORM VALUES
// =========================================================

$values = [

    'full_name' => '',
    'username' => '',
    'email' => '',
    'phone' => ''
];

$errors = [];


// =========================================================
// SUCCESS FLASH MESSAGE
// =========================================================

$successMessage =
    $_SESSION['admin_setup_success']
        ?? '';

unset(
    $_SESSION['admin_setup_success']
);


// =========================================================
require __DIR__ . '/../actions/setup_admin.php';

// RECHECK ADMIN AFTER REDIRECT
// =========================================================

if (
    $successMessage !== ''
) {

    $adminCheck->execute();

    $existingAdmin =
        $adminCheck->fetch();
}

