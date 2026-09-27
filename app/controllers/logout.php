<?php

declare(strict_types=1);


// =========================================================
// DATABASE + AUTH
// =========================================================

require_once
    dirname(__DIR__, 2) .
    '/config/database.php';

require_once
    dirname(__DIR__) .
    '/auth.php';


requireLogin();

ensureCsrfToken();


// =========================================================
// CURRENT USER
// =========================================================

$user =
    currentUser();


// =========================================================
// PROCESS LOGOUT
// =========================================================

if (
    $_SERVER['REQUEST_METHOD']
        === 'POST'
) {

    if (
        !verifyCsrfToken(
            $_POST['csrf_token']
                ?? null
        )
    ) {

        http_response_code(
            403
        );

        exit(
            'The logout request could not be verified.'
        );
    }


    if ($user !== null) {

        writeAuditLog(
            $pdo,
            (int)$user['id'],
            'LOGOUT',
            'USER',
            (int)$user['id'],
            'Staff user logged out.'
        );
    }


    destroyLoginSession();


    header(
        'Location: login.php?logged_out=1'
    );

    exit;
}

