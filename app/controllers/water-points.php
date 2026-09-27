<?php

declare(strict_types=1);


// =========================================================
// DATABASE
// =========================================================

require_once
    dirname(__DIR__, 2) .
    '/config/database.php';

require_once dirname(__DIR__) . '/auth.php';


// =========================================================
// OUTPUT ESCAPING
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
// TYPE LABELS
// =========================================================

$typeLabels = [

    'BOREHOLE' =>
        'Borehole',

    'PROTECTED_SPRING' =>
        'Protected spring',

    'SHALLOW_WELL' =>
        'Shallow well',

    'PUBLIC_TAP' =>
        'Public tap',

    'RAINWATER_TANK' =>
        'Rainwater tank',

    'STORAGE_TANK' =>
        'Storage tank',

    'OTHER' =>
        'Other'
];


// =========================================================
// STATUS LABELS
// =========================================================

$statusLabels = [

    'FUNCTIONAL' =>
        'Functional',

    'PARTIALLY_FUNCTIONAL' =>
        'Partially functional',

    'NON_FUNCTIONAL' =>
        'Non-functional',

    'UNDER_REPAIR' =>
        'Under repair'
];


// =========================================================
// FILTER VALUES
// =========================================================

$search =
    trim(
        (string)(
            $_GET['search']
            ?? ''
        )
    );


$type =
    trim(
        (string)(
            $_GET['type']
            ?? ''
        )
    );


$status =
    trim(
        (string)(
            $_GET['status']
            ?? ''
        )
    );


// =========================================================
// VALIDATE FILTERS
// =========================================================

if (
    $type !== '' &&
    !array_key_exists(
        $type,
        $typeLabels
    )
) {

    $type = '';
}


if (
    $status !== '' &&
    !array_key_exists(
        $status,
        $statusLabels
    )
) {

    $status = '';
}


// =========================================================
// BUILD QUERY
// =========================================================

$where = [];
$params = [];


if ($search !== '') {

    $where[] = "
        (
            water_point_code
                LIKE :search
            OR name
                LIKE :search
            OR village
                LIKE :search
        )
    ";

    $params[':search'] =
        '%' .
        $search .
        '%';
}


if ($type !== '') {

    $where[] =
        "type = :type";

    $params[':type'] =
        $type;
}


if ($status !== '') {

    $where[] =
        "status = :status";

    $params[':status'] =
        $status;
}


// =========================================================
// SQL
// =========================================================

$sql = "
    SELECT
        id,
        water_point_code,
        name,
        type,
        village,
        latitude,
        longitude,
        photo_path,
        status,
        created_at
    FROM water_points
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
        created_at DESC,
        id DESC
";


$statement =
    $pdo->prepare(
        $sql
    );


$statement->execute(
    $params
);


$waterPoints =
    $statement->fetchAll();


// =========================================================
// PUBLIC SUMMARY
// =========================================================

$summaryStatement =
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
    );


$summary =
    $summaryStatement
        ->fetch();


$totalWaterPoints =
    (int)(
        $summary['total']
        ?? 0
    );


$functionalCount =
    (int)(
        $summary[
            'functional'
        ]
        ?? 0
    );


$partialCount =
    (int)(
        $summary[
            'partial'
        ]
        ?? 0
    );


$nonFunctionalCount =
    (int)(
        $summary[
            'non_functional'
        ]
        ?? 0
    );


$underRepairCount =
    (int)(
        $summary[
            'under_repair'
        ]
        ?? 0
    );
