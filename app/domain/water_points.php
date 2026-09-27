<?php

declare(strict_types=1);

function generateWaterPointCode(
    PDO $pdo
): string {

    $prefixStatement =
        $pdo->prepare(
            "
            SELECT setting_value
            FROM system_settings
            WHERE setting_key =
                'water_point_code_prefix'
            LIMIT 1
            FOR UPDATE
            "
        );

    $prefixStatement->execute();

    $prefixRow =
        $prefixStatement->fetch();

    $prefix =
        $prefixRow
            ? strtoupper(
                trim(
                    (string)$prefixRow[
                        'setting_value'
                    ]
                )
            )
            : 'WP';


    if (
        !preg_match(
            '/^[A-Z0-9]{1,10}$/',
            $prefix
        )
    ) {

        $prefix =
            'WP';
    }


    $insertSequence =
        $pdo->prepare(
            "
            INSERT INTO system_settings
            (
                setting_key,
                setting_value,
                description
            )
            VALUES
            (
                'water_point_next_number',
                '1',
                'Next sequential number used when generating water point codes.'
            )
            ON DUPLICATE KEY UPDATE
                setting_key =
                    VALUES(setting_key)
            "
        );

    $insertSequence->execute();


    $sequenceStatement =
        $pdo->prepare(
            "
            SELECT setting_value
            FROM system_settings
            WHERE setting_key =
                'water_point_next_number'
            LIMIT 1
            FOR UPDATE
            "
        );

    $sequenceStatement->execute();

    $sequenceRow =
        $sequenceStatement->fetch();

    $nextNumber =
        max(
            1,
            (int)(
                $sequenceRow[
                    'setting_value'
                ] ?? 1
            )
        );


    do {

        $code =
            $prefix .
            '-' .
            str_pad(
                (string)$nextNumber,
                6,
                '0',
                STR_PAD_LEFT
            );


        $duplicateCheck =
            $pdo->prepare(
                "
                SELECT id
                FROM water_points
                WHERE water_point_code =
                    :code
                LIMIT 1
                "
            );

        $duplicateCheck->execute([
            ':code' =>
                $code
        ]);

        $exists =
            (bool)$duplicateCheck
                ->fetch();

        if ($exists) {
            $nextNumber++;
        }

    } while ($exists);


    $updateSequence =
        $pdo->prepare(
            "
            UPDATE system_settings
            SET
                setting_value =
                    :next_number,
                updated_by =
                    :updated_by
            WHERE setting_key =
                'water_point_next_number'
            "
        );

    $updateSequence->execute([

        ':next_number' =>
            (string)(
                $nextNumber + 1
            ),

        ':updated_by' =>
            (int)$_SESSION[
                'user_id'
            ]
    ]);


    return $code;
}


