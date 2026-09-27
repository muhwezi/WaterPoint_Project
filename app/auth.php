<?php

declare(strict_types=1);


// =========================================================
// SESSION INITIALISATION
// =========================================================

if (session_status() !== PHP_SESSION_ACTIVE) {

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
}


// =========================================================
// SECURITY HEADERS
// =========================================================

header(
    'Cache-Control: no-store'
);

header(
    'X-Content-Type-Options: nosniff'
);

header(
    'X-Frame-Options: SAMEORIGIN'
);

header(
    'Referrer-Policy: strict-origin-when-cross-origin'
);


// =========================================================
// CSRF TOKEN
// =========================================================

function ensureCsrfToken(): void
{
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
}


function csrfToken(): string
{
    ensureCsrfToken();

    return
        $_SESSION['csrf_token'];
}


function verifyCsrfToken(
    mixed $submittedToken
): bool {

    if (
        !is_string(
            $submittedToken
        )
    ) {
        return false;
    }

    ensureCsrfToken();

    return hash_equals(
        $_SESSION['csrf_token'],
        $submittedToken
    );
}


function regenerateCsrfToken(): void
{
    $_SESSION['csrf_token'] =
        bin2hex(
            random_bytes(32)
        );
}


// =========================================================
// OUTPUT ESCAPING
// =========================================================

function authEscape(
    string $value
): string {

    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


// =========================================================
// AUTHENTICATION STATE
// =========================================================

function isLoggedIn(): bool
{
    return (
        isset(
            $_SESSION['user_id'],
            $_SESSION['username'],
            $_SESSION['full_name'],
            $_SESSION['role']
        ) &&
        is_int(
            $_SESSION['user_id']
        ) &&
        $_SESSION['user_id'] > 0
    );
}


// =========================================================
// CURRENT USER
// =========================================================

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [

        'id' =>
            $_SESSION['user_id'],

        'username' =>
            $_SESSION['username'],

        'full_name' =>
            $_SESSION['full_name'],

        'role' =>
            $_SESSION['role']
    ];
}


// =========================================================
// ROLE CHECK
// =========================================================

function userHasRole(
    string|array $roles
): bool {

    if (!isLoggedIn()) {
        return false;
    }

    if (is_string($roles)) {
        $roles = [$roles];
    }

    return in_array(
        $_SESSION['role'],
        $roles,
        true
    );
}


// =========================================================
// REQUIRE LOGIN
// =========================================================

function requireLogin(): void
{
    if (isLoggedIn()) {
        global $pdo;
        if ($pdo instanceof PDO) {
            $check = $pdo->prepare('SELECT role, status FROM users WHERE id = ? LIMIT 1');
            $check->execute([$_SESSION['user_id']]);
            $account = $check->fetch();
            if ($account && $account['status'] === 'ACTIVE') {
                $_SESSION['role'] = $account['role'];
                return;
            }
            destroyLoginSession();
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
        }
    }

    $_SESSION['login_redirect'] =
        $_SERVER['REQUEST_URI']
        ?? '';

    header(
        'Location: login.php'
    );

    exit;
}


// =========================================================
// REQUIRE ROLE
// =========================================================

function requireRole(
    string|array $roles
): void {

    requireLogin();

    if (
        userHasRole(
            $roles
        )
    ) {
        return;
    }

    http_response_code(403);

    ?>
    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>
            Access Denied
        </title>

        <link
            rel="stylesheet"
            href="assets/style.css"
        >

    </head>

    <body>

        <main
            class="container"
            style="padding-top: 4rem;"
        >

            <section class="card">

                <h1>
                    Access denied
                </h1>

                <p>
                    Your account does not have permission
                    to access this function.
                </p>

                <p>

                    <a href="dashboard.php">
                        Return to dashboard
                    </a>

                </p>

            </section>

        </main>

    </body>

    </html>

    <?php

    exit;
}


// =========================================================
// REDIRECT AUTHENTICATED USERS
// =========================================================

function redirectIfLoggedIn(): void
{
    if (!isLoggedIn()) {
        return;
    }

    header(
        'Location: dashboard.php'
    );

    exit;
}


// =========================================================
// LOGIN SESSION
// =========================================================

function createLoginSession(
    array $user
): void {

    session_regenerate_id(
        true
    );

    $_SESSION['user_id'] =
        (int)$user['id'];

    $_SESSION['username'] =
        (string)$user['username'];

    $_SESSION['full_name'] =
        (string)$user['full_name'];

    $_SESSION['role'] =
        (string)$user['role'];

    $_SESSION['login_time'] =
        time();

    regenerateCsrfToken();
}


// =========================================================
// DESTROY LOGIN SESSION
// =========================================================

function destroyLoginSession(): void
{
    $_SESSION = [];


    if (
        ini_get(
            'session.use_cookies'
        )
    ) {

        $cookieParameters =
            session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires' =>
                    time() - 42000,

                'path' =>
                    $cookieParameters[
                        'path'
                    ],

                'domain' =>
                    $cookieParameters[
                        'domain'
                    ],

                'secure' =>
                    $cookieParameters[
                        'secure'
                    ],

                'httponly' =>
                    $cookieParameters[
                        'httponly'
                    ],

                'samesite' =>
                    'Lax'
            ]
        );
    }


    session_destroy();
}


// =========================================================
// SAFE REDIRECT AFTER LOGIN
// =========================================================

function loginRedirectDestination(): string
{
    $destination =
        $_SESSION[
            'login_redirect'
        ]
        ?? '';

    unset(
        $_SESSION[
            'login_redirect'
        ]
    );


    if (
        !is_string(
            $destination
        ) ||
        $destination === ''
    ) {

        return 'dashboard.php';
    }


    // Only permit local project paths.
    if (
        str_starts_with(
            $destination,
            '//'
        ) ||
        preg_match(
            '#^https?://#i',
            $destination
        )
    ) {

        return 'dashboard.php';
    }


    return $destination;
}


// =========================================================
// AUDIT HELPER
// =========================================================

function writeAuditLog(
    PDO $pdo,
    ?int $userId,
    string $action,
    ?string $entityType = null,
    ?int $entityId = null,
    ?string $description = null
): void {

    $statement =
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
                :action,
                :entity_type,
                :entity_id,
                :description,
                :ip_address,
                :user_agent
            )
            "
        );


    $statement->execute([

        ':user_id' =>
            $userId,

        ':action' =>
            $action,

        ':entity_type' =>
            $entityType,

        ':entity_id' =>
            $entityId,

        ':description' =>
            $description,

        ':ip_address' =>
            substr(
                $_SERVER[
                    'REMOTE_ADDR'
                ] ?? '',
                0,
                45
            ),

        ':user_agent' =>
            substr(
                $_SERVER[
                    'HTTP_USER_AGENT'
                ] ?? '',
                0,
                255
            )
    ]);
}
