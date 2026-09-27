<?php

declare(strict_types=1);

function reportDate(string $key, string $fallback): string
{
    $value = $_GET[$key] ?? $fallback;
    if (!is_string($value)) {
        throw new DomainException('Enter a valid report date.');
    }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    if (!$date || $date->format('Y-m-d') !== $value) {
        throw new DomainException('Enter a valid report date.');
    }
    return $value;
}

function reportOpenSummary(PDO $pdo): array
{
    return $pdo->query("SELECT COUNT(*) AS total_open,
        COALESCE(SUM(status = 'REPORTED'), 0) AS awaiting_review,
        COALESCE(SUM(status IN ('REVIEWED', 'RETURNED')), 0) AS awaiting_assignment,
        COALESCE(SUM(status = 'COMPLETED'), 0) AS awaiting_verification
        FROM fault_reports
        WHERE status IN ('REPORTED', 'REVIEWED', 'ASSIGNED', 'IN_PROGRESS', 'COMPLETED', 'RETURNED')")
        ->fetch();
}

function reportOpenFaults(PDO $pdo): array
{
    return $pdo->query("SELECT fr.fault_reference, fr.priority, fr.status, fr.reported_at,
        fr.village, COALESCE(wp.name, fr.fault_location, 'Other infrastructure') AS location,
        ra.expected_completion_at, u.full_name AS technician_name
        FROM fault_reports fr
        LEFT JOIN water_points wp ON wp.id = fr.water_point_id
        LEFT JOIN repair_assignments ra ON ra.id = (
            SELECT MAX(r2.id) FROM repair_assignments r2 WHERE r2.fault_report_id = fr.id
        )
        LEFT JOIN users u ON u.id = ra.technician_id
        WHERE fr.status IN ('REPORTED', 'REVIEWED', 'ASSIGNED', 'IN_PROGRESS', 'COMPLETED', 'RETURNED')
        ORDER BY FIELD(fr.priority, 'CRITICAL', 'HIGH', 'MEDIUM', 'LOW', 'UNASSESSED'),
                 fr.reported_at ASC, fr.id ASC
        LIMIT 100")->fetchAll();
}

function reportOverdueAssignments(PDO $pdo): array
{
    return $pdo->query("SELECT fr.fault_reference, fr.status AS fault_status, fr.village,
        COALESCE(wp.name, fr.fault_location, 'Other infrastructure') AS location,
        ra.expected_completion_at, ra.status AS assignment_status,
        u.full_name AS technician_name,
        GREATEST(TIMESTAMPDIFF(DAY, ra.expected_completion_at, CURRENT_TIMESTAMP), 0) AS days_overdue
        FROM repair_assignments ra
        JOIN fault_reports fr ON fr.id = ra.fault_report_id
        JOIN users u ON u.id = ra.technician_id
        LEFT JOIN water_points wp ON wp.id = fr.water_point_id
        WHERE ra.status IN ('ASSIGNED', 'ACCEPTED', 'IN_PROGRESS')
          AND ra.expected_completion_at < CURRENT_TIMESTAMP
        ORDER BY ra.expected_completion_at ASC, ra.id ASC
        LIMIT 100")->fetchAll();
}

function reportOverdueCount(PDO $pdo): int
{
    return (int)$pdo->query("SELECT COUNT(*) FROM repair_assignments
        WHERE status IN ('ASSIGNED', 'ACCEPTED', 'IN_PROGRESS')
          AND expected_completion_at < CURRENT_TIMESTAMP")->fetchColumn();
}

function reportTechnicianWork(PDO $pdo, string $from, string $to): array
{
    $statement = $pdo->prepare("SELECT u.full_name, u.username,
        COUNT(ra.id) AS completed_jobs,
        COALESCE(SUM(rv.approved), 0) AS verified_jobs,
        COALESCE(SUM(costs.total_cost), 0) AS recorded_cost
        FROM repair_assignments ra
        JOIN users u ON u.id = ra.technician_id
        LEFT JOIN (
            SELECT assignment_id, MAX(decision = 'APPROVED') AS approved
            FROM repair_verifications GROUP BY assignment_id
        ) rv ON rv.assignment_id = ra.id
        LEFT JOIN (
            SELECT assignment_id, SUM(repair_cost) AS total_cost
            FROM repair_updates GROUP BY assignment_id
        ) costs ON costs.assignment_id = ra.id
        WHERE ra.status = 'COMPLETED'
          AND ra.completed_at >= :from_date
          AND ra.completed_at < DATE_ADD(:to_date, INTERVAL 1 DAY)
        GROUP BY u.id, u.full_name, u.username
        ORDER BY completed_jobs DESC, u.full_name ASC");
    $statement->execute([':from_date' => $from, ':to_date' => $to]);
    return $statement->fetchAll();
}

function formatReportAmount(string $amount): string
{
    [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '00');
    return preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $whole) . '.' . str_pad(substr($fraction, 0, 2), 2, '0');
}
