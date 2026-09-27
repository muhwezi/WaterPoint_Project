<?php

declare(strict_types=1);


require_once
    dirname(__DIR__, 2) .
    '/config/database.php';

require_once
    dirname(__DIR__) .
    '/auth.php';

require_once dirname(__DIR__) . '/repair.php';


requireRole([
    'ADMIN',
    'COORDINATOR'
]);


$currentUser =
    currentUser();

ensureCsrfToken();


function faultDetailEscape(
    string $value
): string {

    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


$priorityLabels = [

    'LOW' =>
        'Low',

    'MEDIUM' =>
        'Medium',

    'HIGH' =>
        'High',

    'CRITICAL' =>
        'Critical'
];


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


$reference =
    strtoupper(
        trim(
            (string)(
                $_GET[
                    'reference'
                ]
                ??
                $_POST[
                    'reference'
                ]
                ??
                ''
            )
        )
    );


if ($reference === '') {

    http_response_code(400);

    exit(
        'Fault reference is required.'
    );
}


// =========================================================
// LOAD FAULT
// =========================================================

function loadFault(
    PDO $pdo,
    string $reference
): ?array {

    $statement =
        $pdo->prepare(
            "
            SELECT
                fr.*,

                wp.water_point_code,
                wp.name
                    AS water_point_name,
                wp.type
                    AS water_point_type,
                wp.status
                    AS water_point_status,

                reviewer.full_name
                    AS reviewed_by_name

            FROM fault_reports fr

            LEFT JOIN water_points wp
                ON wp.id =
                    fr.water_point_id

            LEFT JOIN users reviewer
                ON reviewer.id =
                    fr.reviewed_by

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


    return
        $fault
            ?: null;
}


$fault =
    loadFault(
        $pdo,
        $reference
    );


if (!$fault) {

    http_response_code(
        404
    );

    exit(
        'Fault report not found.'
    );
}


// =========================================================
// FLASH
// =========================================================

$successMessage =
    $_SESSION[
        'fault_review_success'
    ] ?? '';


unset(
    $_SESSION[
        'fault_review_success'
    ]
);


$errors = [];

$selectedPriority =
    $fault[
        'priority'
    ] ===
    'UNASSESSED'
        ? ''
        : $fault[
            'priority'
        ];


$internalNote = '';


// =========================================================
require __DIR__ . '/../actions/review_fault.php';
// RELOAD
// =========================================================

$fault =
    loadFault(
        $pdo,
        $reference
    );


$historyStatement =
    $pdo->prepare(
        "
        SELECT
            fsh.status,
            fsh.public_note,
            fsh.internal_note,
            fsh.changed_at,

            u.full_name
                AS changed_by_name

        FROM fault_status_history fsh

        LEFT JOIN users u
            ON u.id =
                fsh.changed_by

        WHERE
            fsh.fault_report_id =
                :fault_id

        ORDER BY
            fsh.changed_at ASC,
            fsh.id ASC
        "
    );


$historyStatement->execute([
    ':fault_id' =>
        (int)$fault['id']
]);


$history =
    $historyStatement
        ->fetchAll();


if (
    $fault[
        'report_scope'
    ] ===
    'KNOWN_WATER_POINT'
) {

    $locationLabel =
        $fault[
            'water_point_name'
        ] .
        ', ' .
        $fault[
            'village'
        ];

} else {

    $locationLabel =
        (
            $fault[
                'fault_location'
            ]
            ?: 'Infrastructure fault'
        ) .
        ', ' .
        $fault[
            'village'
        ];
}


$hasCoordinates =
    $fault[
        'latitude'
    ] !== null &&
    $fault[
        'longitude'
    ] !== null;

$technicians = activeTechnicians($pdo);
$currentAssignment = assignmentForFault($pdo, (int)$fault['id']);
$repairUpdates = repairUpdates($pdo, (int)$fault['id']);
$workflowNotice = takeNotice();
$workflowError = (string)($_SESSION['workflow_error'] ?? '');
unset($_SESSION['workflow_error']);
