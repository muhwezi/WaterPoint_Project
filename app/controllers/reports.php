<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__) . '/auth.php';
require_once dirname(__DIR__) . '/reports.php';
requireRole(['ADMIN', 'COORDINATOR']);

$today = (new DateTimeImmutable('today', new DateTimeZone('Africa/Kampala')))->format('Y-m-d');
$defaultFrom = (new DateTimeImmutable('-30 days', new DateTimeZone('Africa/Kampala')))->format('Y-m-d');
$error = '';
try {
    $from = reportDate('from', $defaultFrom);
    $to = reportDate('to', $today);
    if ($from > $to || $to > $today) {
        throw new DomainException('Choose a date range ending today or earlier.');
    }
} catch (DomainException $exception) {
    $error = $exception->getMessage();
    $from = $defaultFrom;
    $to = $today;
}

$summary = reportOpenSummary($pdo);
$openFaults = reportOpenFaults($pdo);
$overdueCount = reportOverdueCount($pdo);
$overdue = reportOverdueAssignments($pdo);
$technicianWork = reportTechnicianWork($pdo, $from, $to);
