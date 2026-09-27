<?php

declare(strict_types=1);

function normalizeReporterPhone(
    string $phone
): ?string {

    $phone =
        preg_replace(
            '/[\s\-\(\)]/',
            '',
            trim($phone)
        );


    if ($phone === null) {
        return null;
    }


    // +2567XXXXXXXX
    if (
        preg_match(
            '/^\+256(7\d{8})$/',
            $phone,
            $matches
        )
    ) {

        return
            '+256' .
            $matches[1];
    }


    // 2567XXXXXXXX
    if (
        preg_match(
            '/^256(7\d{8})$/',
            $phone,
            $matches
        )
    ) {

        return
            '+256' .
            $matches[1];
    }


    // 07XXXXXXXX
    if (
        preg_match(
            '/^0(7\d{8})$/',
            $phone,
            $matches
        )
    ) {

        return
            '+256' .
            $matches[1];
    }


    return null;
}


// =========================================================
// FIND WATER POINT
// =========================================================

function findWaterPointByCode(
    PDO $pdo,
    string $code
): ?array {

    $statement =
        $pdo->prepare(
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
            WHERE
                water_point_code =
                    :code
            LIMIT 1
            "
        );


    $statement->execute([
        ':code' =>
            strtoupper(
                trim($code)
            )
    ]);


    $result =
        $statement->fetch();


    return
        $result
            ?: null;
}


// =========================================================
// FAULT REFERENCE GENERATOR
//
// Example:
// WFT-26-K7M4P9
// =========================================================

function generateFaultReference(
    PDO $pdo
): string {

    $setting =
        $pdo->prepare(
            "
            SELECT setting_value
            FROM system_settings
            WHERE setting_key =
                'fault_reference_prefix'
            LIMIT 1
            "
        );


    $setting->execute();


    $row =
        $setting->fetch();


    $prefix =
        $row
            ? strtoupper(
                trim(
                    (string)$row[
                        'setting_value'
                    ]
                )
            )
            : 'WFT';


    if (
        !preg_match(
            '/^[A-Z0-9]{1,10}$/',
            $prefix
        )
    ) {

        $prefix =
            'WFT';
    }


    // Excludes easily confused characters:
    // I, O, 0 and 1.
    $alphabet =
        'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';


    $alphabetLength =
        strlen(
            $alphabet
        );


    for (
        $attempt = 0;
        $attempt < 30;
        $attempt++
    ) {

        $randomPart =
            '';


        for (
            $i = 0;
            $i < 6;
            $i++
        ) {

            $randomPart .=
                $alphabet[
                    random_int(
                        0,
                        $alphabetLength - 1
                    )
                ];
        }


        $reference =
            $prefix .
            '-' .
            date('y') .
            '-' .
            $randomPart;


        $check =
            $pdo->prepare(
                "
                SELECT id
                FROM fault_reports
                WHERE
                    fault_reference =
                        :reference
                LIMIT 1
                "
            );


        $check->execute([
            ':reference' =>
                $reference
        ]);


        if (
            !$check->fetch()
        ) {

            return
                $reference;
        }
    }


    throw new RuntimeException(
        'Unable to generate a unique fault reference.'
    );
}


