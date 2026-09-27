<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/repair.php';

requireRole(['ADMIN', 'COORDINATOR', 'TECHNICIAN']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$reference = $_POST['reference'] ?? '';
if (!is_string($reference) || !preg_match('/^[A-Z0-9_-]{3,40}$/D', $reference)) {
    http_response_code(400);
    exit('Invalid fault reference.');
}
$returnPath = 'fault-details.php?reference=' . rawurlencode($reference);
$action = $_POST['workflow_action'] ?? '';
if (!is_string($action)) {
    http_response_code(400);
    exit('Invalid action.');
}
$coordinatorActions = ['assign', 'verify', 'return', 'close'];
$technicianActions = ['accept', 'start', 'update', 'complete'];
if ((in_array($action, $coordinatorActions, true) && !userHasRole(['ADMIN', 'COORDINATOR']))
    || (in_array($action, $technicianActions, true) && !userHasRole('TECHNICIAN'))
    || !in_array($action, [...$coordinatorActions, ...$technicianActions], true)) {
    http_response_code(403);
    exit('You are not authorised for this action.');
}
if (userHasRole('TECHNICIAN')) {
    $returnPath = 'technician.php?reference=' . rawurlencode($reference);
}
if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    $_SESSION['workflow_error'] = 'The form expired. Please try again.';
    redirectTo($returnPath);
}

try {
    setNotice(performRepairAction($pdo, $reference, $action, (int)currentUser()['id']));
    regenerateCsrfToken();
} catch (DomainException $error) {
    $_SESSION['workflow_error'] = $error->getMessage();
} catch (Throwable $error) {
    error_log('Repair workflow failed: ' . $error->getMessage());
    $_SESSION['workflow_error'] = 'The action could not be saved. Please try again.';
}
redirectTo($returnPath);
