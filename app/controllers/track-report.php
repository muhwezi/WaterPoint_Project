<?php

declare(strict_types=1);


require_once
    dirname(__DIR__, 2) .
    '/config/database.php';


// =========================================================
// ESCAPE
// =========================================================

function escape(
    string $value
): string {

    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


// =========================================================
// STATUS LABELS
// =========================================================

$statusLabels = [

    'REPORTED' =>
        'Reported',

    'REVIEWED' =>
        'Reviewed',

    'ASSIGNED' =>
        'Technician Assigned',

    'IN_PROGRESS' =>
        'Repair In Progress',

    'COMPLETED' =>
        'Repair Completed',

    'VERIFIED' =>
        'Verified',

    'RETURNED' =>
        'Returned for Further Work',

    'CLOSED' =>
        'Closed',

    'CANCELLED' =>
        'Cancelled'
];


// =========================================================
// FAULT TYPE LABELS
// =========================================================

$faultTypeLabels = [

    'NO_WATER' =>
        'No water',

    'LOW_WATER_FLOW' =>
        'Low water flow',

    'BROKEN_PUMP' =>
        'Broken pump',

    'LEAKING_PIPE' =>
        'Leaking pipe',

    'BURST_PIPE' =>
        'Burst pipe',

    'DAMAGED_TAP' =>
        'Damaged tap',

    'CONTAMINATED_WATER' =>
        'Contaminated water',

    'STRUCTURAL_DAMAGE' =>
        'Structural damage',

    'OTHER' =>
        'Other'
];


// =========================================================
// FORMAT DATE
// =========================================================

function publicDate(
    string $date
): string {

    $timestamp =
        strtotime(
            $date
        );


    if ($timestamp === false) {
        return $date;
    }


    return date(
        'd M Y, g:i A',
        $timestamp
    );
}


// =========================================================
// FORMAT DURATION
// =========================================================

function publicDuration(
    string $start,
    ?string $end = null
): string {

    try {

        $startDate =
            new DateTimeImmutable(
                $start
            );


        $endDate =
            $end !== null
                ? new DateTimeImmutable(
                    $end
                )
                : new DateTimeImmutable(
                    'now'
                );


        $seconds =
            max(
                0,
                $endDate
                    ->getTimestamp()
                -
                $startDate
                    ->getTimestamp()
            );


        $days =
            intdiv(
                $seconds,
                86400
            );


        $seconds %=
            86400;


        $hours =
            intdiv(
                $seconds,
                3600
            );


        $seconds %=
            3600;


        $minutes =
            intdiv(
                $seconds,
                60
            );


        $parts = [];


        if ($days > 0) {

            $parts[] =
                $days .
                ' day' .
                (
                    $days === 1
                        ? ''
                        : 's'
                );
        }


        if (
            $hours > 0 ||
            $days > 0
        ) {

            $parts[] =
                $hours .
                ' hour' .
                (
                    $hours === 1
                        ? ''
                        : 's'
                );
        }


        $parts[] =
            $minutes .
            ' minute' .
            (
                $minutes === 1
                    ? ''
                    : 's'
            );


        return implode(
            ' ',
            $parts
        );


    } catch (Throwable $e) {

        return 'Unavailable';
    }
}


// =========================================================
// SEARCH VALUE
// =========================================================

$reference =
    strtoupper(
        trim(
            (string)(
                $_GET[
                    'reference'
                ]
                ?? ''
            )
        )
    );


$fault =
    null;

$history =
    [];

$searched =
    $reference !== '';


// =========================================================
// FIND FAULT
// =========================================================

if ($searched) {

    $statement =
        $pdo->prepare(
            "
            SELECT
                fr.id,
                fr.fault_reference,
                fr.report_scope,
                fr.infrastructure_type,
                fr.fault_location,
                fr.village,
                fr.fault_type,
                fr.description,
                fr.status,
                fr.priority,
                fr.reported_at,
                fr.closed_at,

                wp.water_point_code,
                wp.name
                    AS water_point_name

            FROM fault_reports fr

            LEFT JOIN water_points wp
                ON wp.id =
                    fr.water_point_id

            WHERE
                fr.fault_reference =
                    :reference

            LIMIT 1
            "
        );


    $statement->execute([
        ':reference' =>
            $reference
    ]);


    $fault =
        $statement->fetch();


    if ($fault) {

        $historyStatement =
            $pdo->prepare(
                "
                SELECT
                    status,
                    public_note,
                    changed_at
                FROM fault_status_history
                WHERE
                    fault_report_id =
                        :fault_report_id
                ORDER BY
                    changed_at ASC,
                    id ASC
                "
            );


        $historyStatement->execute([
            ':fault_report_id' =>
                (int)$fault[
                    'id'
                ]
        ]);


        $history =
            $historyStatement
                ->fetchAll();
    }
}

