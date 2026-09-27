<?php

declare(strict_types=1);


// =========================================================
// DATABASE + AUTHENTICATION
// =========================================================

require_once
    dirname(__DIR__, 2) .
    '/config/database.php';

require_once
    dirname(__DIR__) .
    '/auth.php';
require_once dirname(__DIR__) . '/uploads.php';


// =========================================================
// AUTHORIZATION
// =========================================================

requireRole([
    'ADMIN',
    'COORDINATOR'
]);

$currentUser =
    currentUser();

ensureCsrfToken();


// =========================================================
// WATER POINT TYPES
// =========================================================

$types = [

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
// FORM VALUES
// =========================================================

$values = [

    'name' => '',
    'village' => '',
    'type' => '',
    'latitude' => '',
    'longitude' => ''
];

$errors = [];


// =========================================================
// PHOTO SETTINGS
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
// SUCCESS FLASH
// =========================================================

$successData =
    $_SESSION[
        'water_point_success'
    ] ?? null;

unset(
    $_SESSION[
        'water_point_success'
    ]
);


// =========================================================
// WATER POINT CODE GENERATOR
// =========================================================

require_once dirname(__DIR__) . '/domain/water_points.php';
// =========================================================
require __DIR__ . '/../actions/register_water_point.php';
