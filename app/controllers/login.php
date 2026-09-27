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


redirectIfLoggedIn();

ensureCsrfToken();


// =========================================================
// VALUES
// =========================================================

$username = '';

$errors = [];


// =========================================================
// LOGIN RATE LIMIT
//
// Simple session-level protection for this project.
// =========================================================

$maximumAttempts = 5;

$lockPeriodSeconds =
    15 * 60;


if (
    !isset(
        $_SESSION[
            'login_attempts'
        ]
    ) ||
    !is_array(
        $_SESSION[
            'login_attempts'
        ]
    )
) {

    $_SESSION[
        'login_attempts'
    ] = [];
}


// =========================================================
// REMOVE OLD ATTEMPTS
// =========================================================

$currentTime =
    time();

$_SESSION['login_attempts'] =
    array_values(
        array_filter(
            $_SESSION[
                'login_attempts'
            ],

            static function (
                mixed $attempt
            ) use (
                $currentTime,
                $lockPeriodSeconds
            ): bool {

                return (
                    is_int($attempt) &&
                    $attempt >=
                        (
                            $currentTime -
                            $lockPeriodSeconds
                        )
                );
            }
        )
    );


$attemptCount =
    count(
        $_SESSION[
            'login_attempts'
        ]
    );


// =========================================================
require __DIR__ . '/../actions/login.php';
