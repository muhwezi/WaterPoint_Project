<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__) . '/auth.php';
require_once dirname(__DIR__) . '/users.php';
requireRole(['ADMIN', 'COORDINATOR']);
ensureCsrfToken();

$error = '';
$notice = takeNotice();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!userHasRole('ADMIN')) {
        http_response_code(403);
        exit('Only an administrator can manage users.');
    }
    try {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            throw new DomainException('The form expired. Please try again.');
        }
        $action = postedText('action', 30);
        if ($action === 'create') {
            createStaffUser($pdo, (int)currentUser()['id']);
            setNotice('Staff account created.');
        } elseif ($action === 'status') {
            changeStaffStatus($pdo, (int)currentUser()['id'], postedId('user_id'), postedText('status', 20));
            setNotice('Account status updated.');
        } else {
            throw new DomainException('Unknown action.');
        }
        regenerateCsrfToken();
        redirectTo('users.php');
    } catch (DomainException $exception) {
        $error = $exception->getMessage();
    } catch (Throwable $exception) {
        error_log('User management failed: ' . $exception->getMessage());
        $error = 'The account could not be saved. Check the details and try again.';
    }
}
$staff = staffUsers($pdo);
