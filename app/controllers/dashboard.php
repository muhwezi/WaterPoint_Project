<?php

declare(strict_types=1);

require_once
    dirname(__DIR__, 2) .
    '/config/database.php';

require_once
    dirname(__DIR__) .
    '/auth.php';


requireLogin();

$user =
    currentUser();


function dashboardEscape(
    string $value
): string {

    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


function dashboardAge(
    string $date
): string {

    try {

        $start =
            new DateTimeImmutable(
                $date
            );

        $now =
            new DateTimeImmutable(
                'now'
            );

        $seconds =
            max(
                0,
                $now->getTimestamp()
                -
                $start->getTimestamp()
            );


        if ($seconds < 3600) {

            $minutes =
                max(
                    1,
                    intdiv(
                        $seconds,
                        60
                    )
                );

            return
                $minutes .
                'm';
        }


        if ($seconds < 86400) {

            return
                intdiv(
                    $seconds,
                    3600
                ) .
                'h';
        }


        return
            intdiv(
                $seconds,
                86400
            ) .
            'd';


    } catch (Throwable $e) {

        return '-';
    }
}


// =========================================================
// WATER POINT SUMMARY
// =========================================================

$waterSummary =
    $pdo->query(
        "
        SELECT
            COUNT(*) AS total,

            SUM(
                status =
                    'FUNCTIONAL'
            ) AS functional,

            SUM(
                status =
                    'PARTIALLY_FUNCTIONAL'
            ) AS partial,

            SUM(
                status =
                    'NON_FUNCTIONAL'
            ) AS non_functional,

            SUM(
                status =
                    'UNDER_REPAIR'
            ) AS under_repair

        FROM water_points
        "
    )->fetch();


$totalWaterPoints =
    (int)(
        $waterSummary[
            'total'
        ] ?? 0
    );

$functionalWaterPoints =
    (int)(
        $waterSummary[
            'functional'
        ] ?? 0
    );

$partialWaterPoints =
    (int)(
        $waterSummary[
            'partial'
        ] ?? 0
    );

$nonFunctionalWaterPoints =
    (int)(
        $waterSummary[
            'non_functional'
        ] ?? 0
    );

$underRepairWaterPoints =
    (int)(
        $waterSummary[
            'under_repair'
        ] ?? 0
    );


// =========================================================
// ADMIN / COORDINATOR DASHBOARD
// =========================================================

$faultSummary = null;
$recentFaults = [];

if (
    userHasRole([
        'ADMIN',
        'COORDINATOR'
    ])
) {

    $faultSummary =
        $pdo->query(
            "
            SELECT

                COUNT(*) AS total,

                SUM(
                    status =
                        'REPORTED'
                ) AS reported,

                SUM(
                    status =
                        'REVIEWED'
                ) AS reviewed,

                SUM(
                    status =
                        'ASSIGNED'
                ) AS assigned,

                SUM(
                    status =
                        'IN_PROGRESS'
                ) AS in_progress,

                SUM(
                    status =
                        'COMPLETED'
                ) AS completed,

                SUM(
                    status =
                        'RETURNED'
                ) AS returned,

                SUM(
                    status IN (
                        'REPORTED',
                        'REVIEWED',
                        'ASSIGNED',
                        'IN_PROGRESS',
                        'COMPLETED',
                        'RETURNED'
                    )
                ) AS open_faults,

                SUM(
                    priority =
                        'CRITICAL'
                    AND
                    status NOT IN (
                        'CLOSED',
                        'CANCELLED'
                    )
                ) AS critical

            FROM fault_reports
            "
        )->fetch();


    $recentFaultStatement =
        $pdo->query(
            "
            SELECT
                fr.fault_reference,
                fr.fault_type,
                fr.fault_location,
                fr.village,
                fr.priority,
                fr.status,
                fr.reported_at,

                wp.water_point_code,
                wp.name
                    AS water_point_name

            FROM fault_reports fr

            LEFT JOIN water_points wp
                ON wp.id =
                    fr.water_point_id

            ORDER BY
                fr.reported_at DESC,
                fr.id DESC

            LIMIT 8
            "
        );


    $recentFaults =
        $recentFaultStatement
            ->fetchAll();
}


// =========================================================
// TECHNICIAN SUMMARY
// =========================================================

$technicianSummary = null;

if (
    userHasRole(
        'TECHNICIAN'
    )
) {

    $technicianStatement =
        $pdo->prepare(
            "
            SELECT

                COUNT(*) AS total,

                SUM(
                    status =
                        'ASSIGNED'
                ) AS assigned,

                SUM(
                    status =
                        'ACCEPTED'
                ) AS accepted,

                SUM(
                    status =
                        'IN_PROGRESS'
                ) AS in_progress,

                SUM(
                    status =
                        'COMPLETED'
                ) AS completed

            FROM repair_assignments

            WHERE
                technician_id =
                    :technician_id
            "
        );


    $technicianStatement->execute([
        ':technician_id' =>
            (int)$user['id']
    ]);


    $technicianSummary =
        $technicianStatement
            ->fetch();
}

