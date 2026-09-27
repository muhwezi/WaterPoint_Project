<?php

declare(strict_types=1);

// FORM SUBMISSION
// =========================================================

if (
    $_SERVER['REQUEST_METHOD']
        === 'POST'
) {

    // -----------------------------------------------------
    // CSRF
    // -----------------------------------------------------

    if (
        !verifyCsrfToken(
            $_POST['csrf_token']
                ?? null
        )
    ) {

        $errors['form'] =
            'Your form session could not be verified. Please submit the form again.';
    }


    // -----------------------------------------------------
    // READ VALUES
    // -----------------------------------------------------

    foreach (
        $values
        as $field => $default
    ) {

        $submitted =
            $_POST[$field]
                ?? '';

        if (
            !is_string(
                $submitted
            )
        ) {

            $errors[$field] =
                'Enter a valid value.';

            continue;
        }

        $values[$field] =
            trim(
                $submitted
            );
    }


    // =====================================================
    // NAME
    // =====================================================

    if (
        $values['name']
            === ''
    ) {

        $errors['name'] =
            'Enter the water point name.';

    } elseif (
        mb_strlen(
            $values['name']
        ) > 120
    ) {

        $errors['name'] =
            'The water point name must not exceed 120 characters.';
    }


    // =====================================================
    // VILLAGE
    // =====================================================

    if (
        $values['village']
            === ''
    ) {

        $errors['village'] =
            'Enter the village.';

    } elseif (
        mb_strlen(
            $values['village']
        ) > 120
    ) {

        $errors['village'] =
            'The village must not exceed 120 characters.';
    }


    // =====================================================
    // TYPE
    // =====================================================

    if (
        !array_key_exists(
            $values['type'],
            $types
        )
    ) {

        $errors['type'] =
            'Select a valid water point type.';
    }


    // =====================================================
    // COORDINATES
    // =====================================================

    $latitudeEntered =
        $values['latitude']
            !== '';

    $longitudeEntered =
        $values['longitude']
            !== '';


    if (
        $latitudeEntered &&
        !$longitudeEntered
    ) {

        $errors['longitude'] =
            'Enter the longitude as well.';
    }


    if (
        $longitudeEntered &&
        !$latitudeEntered
    ) {

        $errors['latitude'] =
            'Enter the latitude as well.';
    }


    if ($latitudeEntered) {

        if (
            !is_numeric(
                $values['latitude']
            ) ||
            (float)$values[
                'latitude'
            ] < -90 ||
            (float)$values[
                'latitude'
            ] > 90
        ) {

            $errors['latitude'] =
                'Enter a valid latitude between -90 and 90.';
        }
    }


    if ($longitudeEntered) {

        if (
            !is_numeric(
                $values['longitude']
            ) ||
            (float)$values[
                'longitude'
            ] < -180 ||
            (float)$values[
                'longitude'
            ] > 180
        ) {

            $errors['longitude'] =
                'Enter a valid longitude between -180 and 180.';
        }
    }


    // Validate optional photo through the shared upload helper.
    [$photoTempPath, $photoExtension, $photoError] = inspectOptionalPhoto(
        $_FILES['photo'] ?? null, $maximumPhotoSize, $allowedPhotoTypes
    );
    if ($photoError !== null) {
        $errors['photo'] = $photoError;
    }

    // =====================================================
    // SAVE
    // =====================================================

    if (
        empty($errors)
    ) {

        $absolutePhotoPath =
            null;

        $storedPhotoPath =
            null;


        try {

            $pdo->beginTransaction();


            // =============================================
            // CODE
            // =============================================

            $waterPointCode =
                generateWaterPointCode(
                    $pdo
                );


            // =============================================
            // PHOTO
            // =============================================

            if (
                $photoTempPath
                    !== null &&
                $photoExtension
                    !== null
            ) {

                $uploadDirectory =
                    dirname(__DIR__, 2) . '/public' .
                    '/uploads/water-points';


                if (
                    !is_dir(
                        $uploadDirectory
                    )
                ) {

                    if (
                        !mkdir(
                            $uploadDirectory,
                            0755,
                            true
                        ) &&
                        !is_dir(
                            $uploadDirectory
                        )
                    ) {

                        throw new RuntimeException(
                            'Unable to create the water point photo directory.'
                        );
                    }
                }


                $photoFilename =
                    strtolower(
                        str_replace(
                            '-',
                            '_',
                            $waterPointCode
                        )
                    ) .
                    '_' .
                    bin2hex(
                        random_bytes(8)
                    ) .
                    '.' .
                    $photoExtension;


                $absolutePhotoPath =
                    $uploadDirectory .
                    DIRECTORY_SEPARATOR .
                    $photoFilename;


                if (
                    !move_uploaded_file(
                        $photoTempPath,
                        $absolutePhotoPath
                    )
                ) {

                    throw new RuntimeException(
                        'The photo could not be stored.'
                    );
                }


                $storedPhotoPath =
                    'uploads/water-points/' .
                    $photoFilename;
            }


            // =============================================
            // WATER POINT
            // =============================================

            $statement =
                $pdo->prepare(
                    "
                    INSERT INTO water_points
                    (
                        water_point_code,
                        name,
                        type,
                        village,
                        latitude,
                        longitude,
                        photo_path,
                        status,
                        registered_by
                    )
                    VALUES
                    (
                        :water_point_code,
                        :name,
                        :type,
                        :village,
                        :latitude,
                        :longitude,
                        :photo_path,
                        'FUNCTIONAL',
                        :registered_by
                    )
                    "
                );


            $statement->execute([

                ':water_point_code' =>
                    $waterPointCode,

                ':name' =>
                    $values['name'],

                ':type' =>
                    $values['type'],

                ':village' =>
                    $values['village'],

                ':latitude' =>
                    $latitudeEntered
                        ? (float)$values[
                            'latitude'
                        ]
                        : null,

                ':longitude' =>
                    $longitudeEntered
                        ? (float)$values[
                            'longitude'
                        ]
                        : null,

                ':photo_path' =>
                    $storedPhotoPath,

                ':registered_by' =>
                    (int)$currentUser[
                        'id'
                    ]
            ]);


            $waterPointId =
                (int)$pdo
                    ->lastInsertId();


            // =============================================
            // AUDIT
            // =============================================

            writeAuditLog(
                $pdo,
                (int)$currentUser[
                    'id'
                ],
                'WATER_POINT_REGISTERED',
                'WATER_POINT',
                $waterPointId,
                'Registered water point ' .
                $waterPointCode .
                ' - ' .
                $values['name'] .
                '.'
            );


            $pdo->commit();


            regenerateCsrfToken();


            $_SESSION[
                'water_point_success'
            ] = [

                'code' =>
                    $waterPointCode,

                'name' =>
                    $values['name'],

                'village' =>
                    $values['village']
            ];


            header(
                'Location: register-water-point.php'
            );

            exit;


        } catch (Throwable $e) {

            if (
                $pdo->inTransaction()
            ) {

                $pdo->rollBack();
            }


            if (
                $absolutePhotoPath
                    !== null &&
                is_file(
                    $absolutePhotoPath
                )
            ) {

                @unlink(
                    $absolutePhotoPath
                );
            }


            error_log(
                'Water point registration failed: ' .
                $e->getMessage()
            );


            $errors['form'] =
                'The water point could not be registered. Please try again.';
        }
    }
}
