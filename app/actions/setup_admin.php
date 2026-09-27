<?php

declare(strict_types=1);

// PROCESS SETUP
// =========================================================

if (
    $_SERVER['REQUEST_METHOD']
        === 'POST' &&
    !$existingAdmin
) {

    // -----------------------------------------------------
    // READ TEXT VALUES
    // -----------------------------------------------------

    foreach (
        $values
        as $field => $default
    ) {

        $submitted =
            $_POST[$field]
                ?? '';

        if (
            !is_string(
                $submitted
            )
        ) {

            $errors[$field] =
                'Enter a valid value.';

            continue;
        }


        $values[$field] =
            trim($submitted);
    }


    // -----------------------------------------------------
    // PASSWORD VALUES
    // -----------------------------------------------------

    $password =
        $_POST['password']
            ?? '';

    $confirmPassword =
        $_POST['confirm_password']
            ?? '';


    if (
        !is_string($password) ||
        !is_string(
            $confirmPassword
        )
    ) {

        $errors['password'] =
            'Enter a valid password.';
    }


    // -----------------------------------------------------
    // CSRF
    // -----------------------------------------------------

    $submittedToken =
        $_POST['csrf_token']
            ?? '';

    if (
        !is_string(
            $submittedToken
        ) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $submittedToken
        )
    ) {

        $errors['form'] =
            'Your form session could not be verified. Please try again.';
    }


    // =====================================================
    // FULL NAME
    // =====================================================

    if (
        $values['full_name']
            === ''
    ) {

        $errors['full_name'] =
            'Enter the administrator full name.';

    } elseif (
        mb_strlen(
            $values['full_name']
        ) > 120
    ) {

        $errors['full_name'] =
            'The full name is too long.';
    }


    // =====================================================
    // USERNAME
    // =====================================================

    $values['username'] =
        strtolower(
            $values['username']
        );


    if (
        $values['username']
            === ''
    ) {

        $errors['username'] =
            'Enter a username.';

    } elseif (
        !preg_match(
            '/^[a-z0-9._-]{3,40}$/',
            $values['username']
        )
    ) {

        $errors['username'] =
            'Use 3 to 40 letters, numbers, dots, hyphens or underscores.';
    }


    // =====================================================
    // EMAIL
    // =====================================================

    $values['email'] =
        strtolower(
            $values['email']
        );


    if (
        $values['email']
            === ''
    ) {

        $errors['email'] =
            'Enter an email address.';

    } elseif (
        filter_var(
            $values['email'],
            FILTER_VALIDATE_EMAIL
        ) === false
    ) {

        $errors['email'] =
            'Enter a valid email address.';
    }


    // =====================================================
    // PHONE
    // =====================================================

    if (
        $values['phone']
            === ''
    ) {

        $errors['phone'] =
            'Enter a phone number.';

    } else {

        $normalizedPhone =
            normalizeUgandanPhone(
                $values['phone']
            );


        if (
            $normalizedPhone
                === null
        ) {

            $errors['phone'] =
                'Enter a valid Ugandan mobile number.';

        } else {

            $values['phone'] =
                $normalizedPhone;
        }
    }


    // =====================================================
    // PASSWORD
    // =====================================================

    if (
        !isset(
            $errors['password']
        )
    ) {

        if (
            strlen(
                $password
            ) < 10
        ) {

            $errors['password'] =
                'Use at least 10 characters.';

        } elseif (
            !preg_match(
                '/[A-Z]/',
                $password
            )
        ) {

            $errors['password'] =
                'Include at least one uppercase letter.';

        } elseif (
            !preg_match(
                '/[a-z]/',
                $password
            )
        ) {

            $errors['password'] =
                'Include at least one lowercase letter.';

        } elseif (
            !preg_match(
                '/[0-9]/',
                $password
            )
        ) {

            $errors['password'] =
                'Include at least one number.';

        } elseif (
            !preg_match(
                '/[^A-Za-z0-9]/',
                $password
            )
        ) {

            $errors['password'] =
                'Include at least one special character.';
        }
    }


    // -----------------------------------------------------
    // CONFIRM PASSWORD
    // -----------------------------------------------------

    if (
        !isset(
            $errors['password']
        ) &&
        $password !==
            $confirmPassword
    ) {

        $errors['confirm_password'] =
            'The passwords do not match.';
    }


    // =====================================================
    // DUPLICATE USERNAME / EMAIL
    // =====================================================

    if (
        empty($errors)
    ) {

        $duplicateCheck =
            $pdo->prepare(
                "
                SELECT id
                FROM users
                WHERE
                    username = :username
                    OR email = :email
                LIMIT 1
                "
            );


        $duplicateCheck->execute([
            ':username' =>
                $values['username'],

            ':email' =>
                $values['email']
        ]);


        if (
            $duplicateCheck->fetch()
        ) {

            $errors['form'] =
                'That username or email address is already in use.';
        }
    }


    // =====================================================
    // CREATE ADMINISTRATOR
    // =====================================================

    if (
        empty($errors)
    ) {

        try {

            $pdo->beginTransaction();


            // ---------------------------------------------
            // HASH PASSWORD
            // ---------------------------------------------

            $passwordHash =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            if (
                $passwordHash
                    === false
            ) {

                throw new RuntimeException(
                    'Password hashing failed.'
                );
            }


            // ---------------------------------------------
            // INSERT USER
            // ---------------------------------------------

            $statement =
                $pdo->prepare(
                    "
                    INSERT INTO users
                    (
                        full_name,
                        username,
                        email,
                        phone,
                        password_hash,
                        role,
                        status,
                        created_by
                    )
                    VALUES
                    (
                        :full_name,
                        :username,
                        :email,
                        :phone,
                        :password_hash,
                        'ADMIN',
                        'ACTIVE',
                        NULL
                    )
                    "
                );


            $statement->execute([

                ':full_name' =>
                    $values['full_name'],

                ':username' =>
                    $values['username'],

                ':email' =>
                    $values['email'],

                ':phone' =>
                    $values['phone'],

                ':password_hash' =>
                    $passwordHash
            ]);


            $adminId =
                (int)$pdo
                    ->lastInsertId();


            // ---------------------------------------------
            // AUDIT LOG
            // ---------------------------------------------

            $audit =
                $pdo->prepare(
                    "
                    INSERT INTO audit_logs
                    (
                        user_id,
                        action,
                        entity_type,
                        entity_id,
                        description,
                        ip_address,
                        user_agent
                    )
                    VALUES
                    (
                        :user_id,
                        'INITIAL_ADMIN_CREATED',
                        'USER',
                        :entity_id,
                        :description,
                        :ip_address,
                        :user_agent
                    )
                    "
                );


            $audit->execute([

                ':user_id' =>
                    $adminId,

                ':entity_id' =>
                    $adminId,

                ':description' =>
                    'Initial system administrator account created.',

                ':ip_address' =>
                    $remoteAddress,

                ':user_agent' =>
                    substr(
                        $_SERVER[
                            'HTTP_USER_AGENT'
                        ] ?? '',
                        0,
                        255
                    )
            ]);


            $pdo->commit();


            // ---------------------------------------------
            // NEW CSRF TOKEN
            // ---------------------------------------------

            $_SESSION['csrf_token'] =
                bin2hex(
                    random_bytes(32)
                );


            $_SESSION[
                'admin_setup_success'
            ] =
                'Initial administrator account created successfully.';


            // ---------------------------------------------
            // REDIRECT
            // ---------------------------------------------

            header(
                'Location: setup-admin.php'
            );

            exit;


        } catch (Throwable $e) {

            if (
                $pdo->inTransaction()
            ) {

                $pdo->rollBack();
            }


            error_log(
                'Admin setup failed: ' .
                $e->getMessage()
            );


            $errors['form'] =
                'The administrator account could not be created.';
        }
    }
}


// =========================================================
