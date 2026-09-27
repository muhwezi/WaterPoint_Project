<?php

declare(strict_types=1);


// =========================================================
// DATABASE + PUBLIC SESSION / CSRF SUPPORT
// =========================================================

require_once
    dirname(__DIR__, 2) .
    '/config/database.php';

require_once
    dirname(__DIR__) .
    '/auth.php';
require_once dirname(__DIR__) . '/uploads.php';


ensureCsrfToken();


// =========================================================
// FAULT TYPES
// =========================================================

$faultTypes = [

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
// OTHER INFRASTRUCTURE TYPES
// =========================================================

$infrastructureTypes = [

    'DISTRIBUTION_PIPE' =>
        'Distribution pipe',

    'MAIN_WATER_PIPE' =>
        'Main water pipe',

    'PUBLIC_TAP' =>
        'Public tap',

    'VALVE_CHAMBER' =>
        'Valve / chamber',

    'STORAGE_TANK' =>
        'Storage tank',

    'PUMPING_INFRASTRUCTURE' =>
        'Pumping infrastructure',

    'UNKNOWN' =>
        'Unknown',

    'OTHER' =>
        'Other'
];


// =========================================================
// WATER POINTS
// =========================================================

$waterPointStatement =
    $pdo->query(
        "
        SELECT
            id,
            water_point_code,
            name,
            village,
            type,
            status,
            latitude,
            longitude
        FROM water_points
        ORDER BY
            water_point_code ASC
        "
    );


$waterPoints =
    $waterPointStatement
        ->fetchAll();


// =========================================================
// WATER POINT LOOKUP FOR JAVASCRIPT
// =========================================================

$waterPointDirectory = [];


foreach (
    $waterPoints
    as $point
) {

    $waterPointDirectory[] = [

        'code' =>
            $point[
                'water_point_code'
            ],

        'name' =>
            $point[
                'name'
            ],

        'village' =>
            $point[
                'village'
            ],

        'type' =>
            $point[
                'type'
            ],

        'status' =>
            $point[
                'status'
            ],

        'latitude' =>
            $point[
                'latitude'
            ],

        'longitude' =>
            $point[
                'longitude'
            ]
    ];
}


// =========================================================
// FORM VALUES
// =========================================================

$values = [

    'report_scope' => '',

    'water_point_code' => '',

    'infrastructure_type' => '',

    'fault_location' => '',

    'village' => '',

    'fault_type' => '',

    'description' => '',

    'latitude' => '',

    'longitude' => '',

    'reporter_name' => '',

    'reporter_phone' => ''
];


$errors = [];


// =========================================================
// PRESELECT WATER POINT FROM URL
//
// Example:
// report-fault.php?wp=WP-000001
// =========================================================

if (
    $_SERVER['REQUEST_METHOD']
        !== 'POST'
) {

    $requestedWaterPoint =
        strtoupper(
            trim(
                (string)(
                    $_GET['wp']
                    ?? ''
                )
            )
        );


    if (
        $requestedWaterPoint
            !== ''
    ) {

        foreach (
            $waterPoints
            as $point
        ) {

            if (
                strtoupper(
                    $point[
                        'water_point_code'
                    ]
                ) ===
                $requestedWaterPoint
            ) {

                $values[
                    'report_scope'
                ] =
                    'KNOWN_WATER_POINT';


                $values[
                    'water_point_code'
                ] =
                    $point[
                        'water_point_code'
                    ];


                if (
                    $point[
                        'latitude'
                    ] !== null &&
                    $point[
                        'longitude'
                    ] !== null
                ) {

                    $values[
                        'latitude'
                    ] =
                        (string)$point[
                            'latitude'
                        ];


                    $values[
                        'longitude'
                    ] =
                        (string)$point[
                            'longitude'
                        ];
                }


                break;
            }
        }
    }
}


// =========================================================
// SUCCESS FLASH
// =========================================================

$successData =
    $_SESSION[
        'fault_report_success'
    ] ?? null;


unset(
    $_SESSION[
        'fault_report_success'
    ]
);


// =========================================================
// UGANDAN PHONE NORMALIZATION
// =========================================================

require_once dirname(__DIR__) . '/domain/faults.php';
// =========================================================
// PHOTO CONFIGURATION
// =========================================================

$maximumPhotoSize =
    5 * 1024 * 1024;


$allowedPhotoTypes = [

    'image/jpeg' =>
        'jpg',

    'image/png' =>
        'png',

    'image/webp' =>
        'webp'
];


// =========================================================
require __DIR__ . '/../actions/report_fault.php';
