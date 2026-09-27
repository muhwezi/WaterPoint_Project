<?php

declare(strict_types=1);


require_once
    dirname(__DIR__, 2) .
    '/config/database.php';

require_once
    dirname(__DIR__) .
    '/auth.php';


requireRole([
    'ADMIN',
    'COORDINATOR'
]);


function faultListEscape(
    string $value
): string {

    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


$statusLabels = [

    'REPORTED' =>
        'Reported',

    'REVIEWED' =>
        'Reviewed',

    'ASSIGNED' =>
        'Assigned',

    'IN_PROGRESS' =>
        'In Progress',

    'COMPLETED' =>
        'Completed',

    'VERIFIED' =>
        'Verified',

    'RETURNED' =>
        'Returned',

    'CLOSED' =>
        'Closed',

    'CANCELLED' =>
        'Cancelled'
];


$priorityLabels = [

    'UNASSESSED' =>
        'Unassessed',

    'LOW' =>
        'Low',

    'MEDIUM' =>
        'Medium',

    'HIGH' =>
        'High',

    'CRITICAL' =>
        'Critical'
];


$search =
    trim(
        (string)(
            $_GET['search']
            ?? ''
        )
    );


$status =
    strtoupper(
        trim(
            (string)(
                $_GET['status']
                ?? ''
            )
        )
    );


$priority =
    strtoupper(
        trim(
            (string)(
                $_GET['priority']
                ?? ''
            )
        )
    );


if (
    $status !== '' &&
    !array_key_exists(
        $status,
        $statusLabels
    )
) {

    $status = '';
}


if (
    $priority !== '' &&
    !array_key_exists(
        $priority,
        $priorityLabels
    )
) {

    $priority = '';
}


// =========================================================
// QUERY
// =========================================================

$where = [];
$params = [];


if ($search !== '') {

    $where[] = "
        (
            fr.fault_reference
                LIKE :search
            OR fr.village
                LIKE :search
            OR fr.fault_location
                LIKE :search
            OR fr.reporter_name
                LIKE :search
            OR fr.reporter_phone
                LIKE :search
            OR wp.water_point_code
                LIKE :search
            OR wp.name
                LIKE :search
        )
    ";


    $params[
        ':search'
    ] =
        '%' .
        $search .
        '%';
}


if ($status !== '') {

    $where[] =
        "fr.status = :status";


    $params[
        ':status'
    ] =
        $status;
}


if ($priority !== '') {

    $where[] =
        "fr.priority = :priority";


    $params[
        ':priority'
    ] =
        $priority;
}


$sql = "
    SELECT
        fr.id,
        fr.fault_reference,
        fr.report_scope,
        fr.infrastructure_type,
        fr.fault_location,
        fr.village,
        fr.fault_type,
        fr.reporter_name,
        fr.reporter_phone,
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
";


if (!empty($where)) {

    $sql .=
        " WHERE " .
        implode(
            " AND ",
            $where
        );
}


$sql .= "
    ORDER BY

        CASE fr.priority

            WHEN 'CRITICAL'
                THEN 1

            WHEN 'HIGH'
                THEN 2

            WHEN 'MEDIUM'
                THEN 3

            WHEN 'LOW'
                THEN 4

            ELSE 5

        END,

        fr.reported_at ASC
";


$statement =
    $pdo->prepare(
        $sql
    );


$statement->execute(
    $params
);


$faults =
    $statement->fetchAll();

