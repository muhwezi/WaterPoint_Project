<?php

declare(strict_types=1);


// =========================================================
// DATABASE
// =========================================================

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
// LABELS
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
// CODE
// =========================================================

$code =
    strtoupper(
        trim(
            (string)(
                $_GET['code']
                ?? ''
            )
        )
    );


// =========================================================
// FIND WATER POINT
// =========================================================

$waterPoint =
    null;


if ($code !== '') {

    $statement =
        $pdo->prepare(
            "
            SELECT
                wp.id,
                wp.water_point_code,
                wp.name,
                wp.type,
                wp.village,
                wp.latitude,
                wp.longitude,
                wp.photo_path,
                wp.status,
                wp.created_at,

                u.full_name
                    AS registered_by_name

            FROM water_points wp

            LEFT JOIN users u
                ON u.id =
                    wp.registered_by

            WHERE
                wp.water_point_code =
                    :code

            LIMIT 1
            "
        );


    $statement->execute([
        ':code' =>
            $code
    ]);


    $waterPoint =
        $statement->fetch();
}


// =========================================================
// NOT FOUND
// =========================================================

if (!$waterPoint) {

    http_response_code(
        404
    );
}

