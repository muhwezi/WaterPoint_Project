<?php

declare(strict_types=1);

// PROCESS FORM
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
            'Your form session could not be verified. Please try again.';
    }


    // -----------------------------------------------------
    // READ INPUTS
    // -----------------------------------------------------

    foreach (
        $values
        as $field =>
            $default
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
    // REPORT SCOPE
    // =====================================================

    $allowedScopes = [

        'KNOWN_WATER_POINT',

        'OTHER_INFRASTRUCTURE'
    ];


    if (
        !in_array(
            $values[
                'report_scope'
            ],
            $allowedScopes,
            true
        )
    ) {

        $errors[
            'report_scope'
        ] =
            'Select where the problem is located.';
    }


    // =====================================================
    // RESOLVED REPORT DATA
    // =====================================================

    $waterPoint =
        null;

    $waterPointId =
        null;

    $infrastructureType =
        null;

    $faultLocation =
        null;

    $reportVillage =
        null;


    // =====================================================
    // KNOWN WATER POINT
    // =====================================================

    if (
        $values[
            'report_scope'
        ] ===
        'KNOWN_WATER_POINT'
    ) {

        if (
            $values[
                'water_point_code'
            ] === ''
        ) {

            $errors[
                'water_point_code'
            ] =
                'Enter or select the water point code.';

        } else {

            $waterPoint =
                findWaterPointByCode(
                    $pdo,
                    $values[
                        'water_point_code'
                    ]
                );


            if (!$waterPoint) {

                $errors[
                    'water_point_code'
                ] =
                    'The selected water point could not be found.';

            } else {

                $values[
                    'water_point_code'
                ] =
                    $waterPoint[
                        'water_point_code'
                    ];


                $waterPointId =
                    (int)$waterPoint[
                        'id'
                    ];


                $infrastructureType =
                    'WATER_POINT';


                $reportVillage =
                    $waterPoint[
                        'village'
                    ];
            }
        }
    }


    // =====================================================
    // OTHER INFRASTRUCTURE
    // =====================================================

    if (
        $values[
            'report_scope'
        ] ===
        'OTHER_INFRASTRUCTURE'
    ) {

        if (
            !array_key_exists(
                $values[
                    'infrastructure_type'
                ],
                $infrastructureTypes
            )
        ) {

            $errors[
                'infrastructure_type'
            ] =
                'Select the affected infrastructure type.';

        } else {

            $infrastructureType =
                $values[
                    'infrastructure_type'
                ];
        }


        if (
            $values[
                'fault_location'
            ] === ''
        ) {

            $errors[
                'fault_location'
            ] =
                'Enter a location or nearby landmark.';

        } elseif (
            mb_strlen(
                $values[
                    'fault_location'
                ]
            ) > 180
        ) {

            $errors[
                'fault_location'
            ] =
                'The location must not exceed 180 characters.';

        } else {

            $faultLocation =
                $values[
                    'fault_location'
                ];
        }


        if (
            $values[
                'village'
            ] === ''
        ) {

            $errors['village'] =
                'Enter the village or area.';

        } elseif (
            mb_strlen(
                $values[
                    'village'
                ]
            ) > 120
        ) {

            $errors['village'] =
                'The village or area must not exceed 120 characters.';

        } else {

            $reportVillage =
                $values[
                    'village'
                ];
        }
    }


    // =====================================================
    // FAULT TYPE
    // =====================================================

    if (
        !array_key_exists(
            $values[
                'fault_type'
            ],
            $faultTypes
        )
    ) {

        $errors[
            'fault_type'
        ] =
            'Select the type of fault.';
    }


    // =====================================================
    // DESCRIPTION
    // =====================================================

    if (
        $values[
            'description'
        ] === ''
    ) {

        $errors[
            'description'
        ] =
            'Describe the problem.';

    } elseif (
        mb_strlen(
            $values[
                'description'
            ]
        ) < 5
    ) {

        $errors[
            'description'
        ] =
            'Provide a little more detail about the problem.';

    } elseif (
        mb_strlen(
            $values[
                'description'
            ]
        ) > 2000
    ) {

        $errors[
            'description'
        ] =
            'The description is too long.';
    }


    // =====================================================
    // REPORTER NAME - REQUIRED
    // =====================================================

    if (
        $values[
            'reporter_name'
        ] === ''
    ) {

        $errors[
            'reporter_name'
        ] =
            'Enter your name.';

    } elseif (
        mb_strlen(
            $values[
                'reporter_name'
            ]
        ) > 120
    ) {

        $errors[
            'reporter_name'
        ] =
            'The reporter name must not exceed 120 characters.';
    }


    // =====================================================
    // PHONE - REQUIRED
    // =====================================================

    if (
        $values[
            'reporter_phone'
        ] === ''
    ) {

        $errors[
            'reporter_phone'
        ] =
            'Enter your phone number.';

    } else {

        $normalizedPhone =
            normalizeReporterPhone(
                $values[
                    'reporter_phone'
                ]
            );


        if (
            $normalizedPhone
                === null
        ) {

            $errors[
                'reporter_phone'
            ] =
                'Enter a valid Ugandan mobile number.';

        } else {

            $values[
                'reporter_phone'
            ] =
                $normalizedPhone;
        }
    }


    // =====================================================
    // COORDINATES
    // =====================================================

    $latitudeEntered =
        $values[
            'latitude'
        ] !== '';


    $longitudeEntered =
        $values[
            'longitude'
        ] !== '';


    if (
        $latitudeEntered &&
        !$longitudeEntered
    ) {

        $errors[
            'longitude'
        ] =
            'Enter the longitude as well.';
    }


    if (
        $longitudeEntered &&
        !$latitudeEntered
    ) {

        $errors[
            'latitude'
        ] =
            'Enter the latitude as well.';
    }


    if ($latitudeEntered) {

        if (
            !is_numeric(
                $values[
                    'latitude'
                ]
            ) ||
            (float)$values[
                'latitude'
            ] < -90 ||
            (float)$values[
                'latitude'
            ] > 90
        ) {

            $errors[
                'latitude'
            ] =
                'Enter a valid latitude.';
        }
    }


    if ($longitudeEntered) {

        if (
            !is_numeric(
                $values[
                    'longitude'
                ]
            ) ||
            (float)$values[
                'longitude'
            ] < -180 ||
            (float)$values[
                'longitude'
            ] > 180
        ) {

            $errors[
                'longitude'
            ] =
                'Enter a valid longitude.';
        }
    }


    // -----------------------------------------------------
    // IF KNOWN WATER POINT AND NO MANUAL COORDINATES,
    // USE REGISTERED WATER POINT LOCATION.
    // -----------------------------------------------------

    if (
        $waterPoint &&
        !$latitudeEntered &&
        !$longitudeEntered &&
        $waterPoint[
            'latitude'
        ] !== null &&
        $waterPoint[
            'longitude'
        ] !== null
    ) {

        $values[
            'latitude'
        ] =
            (string)$waterPoint[
                'latitude'
            ];


        $values[
            'longitude'
        ] =
            (string)$waterPoint[
                'longitude'
            ];


        $latitudeEntered =
            true;


        $longitudeEntered =
            true;
    }


    // Validate optional photo through the shared upload helper.
    [$photoTempPath, $photoExtension, $photoError] = inspectOptionalPhoto(
        $_FILES['photo'] ?? null, $maximumPhotoSize, $allowedPhotoTypes
    );
    if ($photoError !== null) {
        $errors['photo'] = $photoError;
    }

    // =====================================================
    // SAVE FAULT
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
            // REFERENCE
            // =============================================

            $faultReference =
                generateFaultReference(
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
                    '/uploads/faults';


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
                            'Unable to create fault photo directory.'
                        );
                    }
                }


                $photoFilename =
                    strtolower(
                        str_replace(
                            '-',
                            '_',
                            $faultReference
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
                        'Unable to store fault photo.'
                    );
                }


                $storedPhotoPath =
                    'uploads/faults/' .
                    $photoFilename;
            }


            // =============================================
            // INSERT FAULT REPORT
            // =============================================

            $insertFault =
                $pdo->prepare(
                    "
                    INSERT INTO fault_reports
                    (
                        fault_reference,
                        report_scope,
                        water_point_id,
                        infrastructure_type,
                        fault_location,
                        village,
                        fault_type,
                        description,
                        latitude,
                        longitude,
                        photo_path,
                        reporter_name,
                        reporter_phone,
                        priority,
                        status
                    )
                    VALUES
                    (
                        :fault_reference,
                        :report_scope,
                        :water_point_id,
                        :infrastructure_type,
                        :fault_location,
                        :village,
                        :fault_type,
                        :description,
                        :latitude,
                        :longitude,
                        :photo_path,
                        :reporter_name,
                        :reporter_phone,
                        'UNASSESSED',
                        'REPORTED'
                    )
                    "
                );


            $insertFault->execute([

                ':fault_reference' =>
                    $faultReference,

                ':report_scope' =>
                    $values[
                        'report_scope'
                    ],

                ':water_point_id' =>
                    $waterPointId,

                ':infrastructure_type' =>
                    $infrastructureType,

                ':fault_location' =>
                    $faultLocation,

                ':village' =>
                    $reportVillage,

                ':fault_type' =>
                    $values[
                        'fault_type'
                    ],

                ':description' =>
                    $values[
                        'description'
                    ],

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

                ':reporter_name' =>
                    $values[
                        'reporter_name'
                    ],

                ':reporter_phone' =>
                    $values[
                        'reporter_phone'
                    ]
            ]);


            $faultId =
                (int)$pdo
                    ->lastInsertId();


            // =============================================
            // INITIAL STATUS HISTORY
            // =============================================

            $history =
                $pdo->prepare(
                    "
                    INSERT INTO fault_status_history
                    (
                        fault_report_id,
                        status,
                        changed_by,
                        public_note,
                        internal_note
                    )
                    VALUES
                    (
                        :fault_report_id,
                        'REPORTED',
                        NULL,
                        'Fault report received.',
                        NULL
                    )
                    "
                );


            $history->execute([
                ':fault_report_id' =>
                    $faultId
            ]);


            // =============================================
            // AUDIT
            // =============================================

            writeAuditLog(
                $pdo,
                null,
                'PUBLIC_FAULT_REPORTED',
                'FAULT_REPORT',
                $faultId,
                'Public fault report ' .
                $faultReference .
                ' submitted.'
            );


            $pdo->commit();


            // =============================================
            // LOCATION LABEL FOR SUCCESS
            // =============================================

            if ($waterPoint) {

                $successLocation =
                    $waterPoint[
                        'name'
                    ] .
                    ', ' .
                    $waterPoint[
                        'village'
                    ];

            } else {

                $successLocation =
                    $faultLocation .
                    ', ' .
                    $reportVillage;
            }


            $_SESSION[
                'fault_report_success'
            ] = [

                'reference' =>
                    $faultReference,

                'fault_type' =>
                    $faultTypes[
                        $values[
                            'fault_type'
                        ]
                    ],

                'location' =>
                    $successLocation
            ];


            regenerateCsrfToken();


            header(
                'Location: report-fault.php?submitted=1'
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
                'Public fault report failed: ' .
                $e->getMessage()
            );


            $errors['form'] =
                'The fault report could not be submitted. Please try again.';
        }
    }
}
