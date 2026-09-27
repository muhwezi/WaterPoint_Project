<?php

declare(strict_types=1);

// PROCESS LOGIN
// =========================================================

if (
    $_SERVER['REQUEST_METHOD']
        === 'POST'
) {

    // -----------------------------------------------------
    // CSRF
    // -----------------------------------------------------

    if (
        !verifyCsrfToken(
            $_POST['csrf_token']
                ?? null
        )
    ) {

        $errors['form'] =
            'Your login session could not be verified. Please try again.';
    }


    // -----------------------------------------------------
    // THROTTLE
    // -----------------------------------------------------

    if (
        $attemptCount >=
            $maximumAttempts
    ) {

        $errors['form'] =
            'Too many unsuccessful login attempts. Please wait 15 minutes and try again.';
    }


    // -----------------------------------------------------
    // USERNAME
    // -----------------------------------------------------

    $submittedUsername =
        $_POST['username']
            ?? '';

    if (
        !is_string(
            $submittedUsername
        )
    ) {

        $errors['username'] =
            'Enter a valid username.';

    } else {

        $username =
            strtolower(
                trim(
                    $submittedUsername
                )
            );
    }


    if (
        $username === ''
    ) {

        $errors['username'] =
            'Enter your username.';
    }


    // -----------------------------------------------------
    // PASSWORD
    // -----------------------------------------------------

    $password =
        $_POST['password']
            ?? '';

    if (
        !is_string(
            $password
        ) ||
        $password === ''
    ) {

        $errors['password'] =
            'Enter your password.';
    }


    // =====================================================
    // AUTHENTICATE
    // =====================================================

    if (
        empty($errors)
    ) {

        $statement =
            $pdo->prepare(
                "
                SELECT
                    id,
                    full_name,
                    username,
                    password_hash,
                    role,
                    status
                FROM users
                WHERE username = :username
                LIMIT 1
                "
            );


        $statement->execute([
            ':username' =>
                $username
        ]);


        $user =
            $statement->fetch();


        // -------------------------------------------------
        // INVALID USER/PASSWORD
        // -------------------------------------------------

        if (
            !$user ||
            !password_verify(
                $password,
                $user[
                    'password_hash'
                ]
            )
        ) {

            $_SESSION[
                'login_attempts'
            ][] =
                time();


            writeAuditLog(
                $pdo,
                null,
                'LOGIN_FAILED',
                'USER',
                null,
                'Unsuccessful staff login attempt for username: ' .
                $username
            );


            $errors['form'] =
                'Invalid username or password.';


        // -------------------------------------------------
        // ACCOUNT NOT ACTIVE
        // -------------------------------------------------

        } elseif (
            $user['status']
                !== 'ACTIVE'
        ) {

            $_SESSION[
                'login_attempts'
            ][] =
                time();


            writeAuditLog(
                $pdo,
                (int)$user['id'],
                'LOGIN_BLOCKED',
                'USER',
                (int)$user['id'],
                'Login blocked because account status is ' .
                $user['status'] .
                '.'
            );


            $errors['form'] =
                'This staff account is not currently available. Contact the system administrator.';


        // -------------------------------------------------
        // SUCCESS
        // -------------------------------------------------

        } else {

            // ---------------------------------------------
            // PASSWORD HASH UPGRADE IF NEEDED
            // ---------------------------------------------

            if (
                password_needs_rehash(
                    $user[
                        'password_hash'
                    ],
                    PASSWORD_DEFAULT
                )
            ) {

                $newHash =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                if (
                    $newHash !== false
                ) {

                    $rehashStatement =
                        $pdo->prepare(
                            "
                            UPDATE users
                            SET
                                password_hash =
                                    :password_hash
                            WHERE id = :id
                            "
                        );


                    $rehashStatement
                        ->execute([

                            ':password_hash' =>
                                $newHash,

                            ':id' =>
                                (int)$user[
                                    'id'
                                ]
                        ]);
                }
            }


            // ---------------------------------------------
            // UPDATE LAST LOGIN
            // ---------------------------------------------

            $updateLogin =
                $pdo->prepare(
                    "
                    UPDATE users
                    SET last_login_at =
                        CURRENT_TIMESTAMP
                    WHERE id = :id
                    "
                );


            $updateLogin->execute([
                ':id' =>
                    (int)$user['id']
            ]);


            // ---------------------------------------------
            // CREATE SESSION
            // ---------------------------------------------

            createLoginSession(
                $user
            );


            // ---------------------------------------------
            // CLEAR FAILED ATTEMPTS
            // ---------------------------------------------

            $_SESSION[
                'login_attempts'
            ] = [];


            // ---------------------------------------------
            // AUDIT
            // ---------------------------------------------

            writeAuditLog(
                $pdo,
                (int)$user['id'],
                'LOGIN_SUCCESS',
                'USER',
                (int)$user['id'],
                'Staff user logged in successfully.'
            );


            // ---------------------------------------------
            // REDIRECT
            // ---------------------------------------------

            $destination =
                loginRedirectDestination();


            header(
                'Location: ' .
                $destination
            );

            exit;
        }
    }
}

