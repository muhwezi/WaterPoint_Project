<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__) . '/auth.php';
require_once dirname(__DIR__) . '/repair.php';
requireRole(['TECHNICIAN']);
ensureCsrfToken();

$user = currentUser();
$reference = $_GET['reference'] ?? '';
if (!is_string($reference) || strlen($reference) > 40) {
    $reference = '';
}
$assignments = $pdo->prepare('SELECT ra.id, ra.status AS assignment_status,
    ra.assignment_notes AS instructions, ra.expected_completion_at,
    ra.assigned_at, ra.started_at, ra.completed_at, fr.fault_reference, fr.status AS fault_status,
    fr.priority, fr.fault_type, fr.description, fr.village, fr.fault_location,
    wp.name AS water_point_name, wp.water_point_code
    FROM repair_assignments ra
    JOIN fault_reports fr ON fr.id = ra.fault_report_id
    LEFT JOIN water_points wp ON wp.id = fr.water_point_id
    WHERE ra.technician_id = :technician_id
    ORDER BY CASE WHEN ra.status IN (\'ASSIGNED\',\'ACCEPTED\',\'IN_PROGRESS\') THEN 0 ELSE 1 END,
             ra.assigned_at DESC, ra.id DESC');
$assignments->execute([':technician_id' => (int)$user['id']]);
$jobs = $assignments->fetchAll();
$selected = null;
foreach ($jobs as $job) {
    if ($job['fault_reference'] === $reference) {
        $selected = $job;
        break;
    }
}
$notice = takeNotice();
$error = (string)($_SESSION['workflow_error'] ?? '');
unset($_SESSION['workflow_error']);
