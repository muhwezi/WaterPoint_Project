<?php

declare(strict_types=1);

// REVIEW SUBMISSION
// =========================================================

if (
    $_SERVER[
        'REQUEST_METHOD'
    ] === 'POST'
) {

    if (
        !verifyCsrfToken(
            $_POST[
                'csrf_token'
            ] ?? null
        )
    ) {

        $errors[
            'form'
        ] =
            'The review request could not be verified.';
    }


    $selectedPriority =
        strtoupper(
            trim(
                (string)(
                    $_POST[
                        'priority'
                    ] ?? ''
                )
            )
        );


    $internalNote =
        trim(
            (string)(
                $_POST[
                    'internal_note'
                ] ?? ''
            )
        );


    if (
        $fault[
            'status'
        ] !== 'REPORTED'
    ) {

        $errors[
            'form'
        ] =
            'This fault has already moved beyond the initial review stage.';
    }


    if (
        !array_key_exists(
            $selectedPriority,
            $priorityLabels
        )
    ) {

        $errors[
            'priority'
        ] =
            'Select a priority.';
    }


    if (
        mb_strlen(
            $internalNote
        ) > 2000
    ) {

        $errors[
            'internal_note'
        ] =
            'Internal notes must not exceed 2,000 characters.';
    }


    if (
        empty(
            $errors
        )
    ) {

        try {

            $pdo->beginTransaction();


            // =============================================
            // LOCK CURRENT RECORD
            // =============================================

            $lockStatement =
                $pdo->prepare(
                    "
                    SELECT
                        id,
                        status
                    FROM fault_reports
                    WHERE
                        fault_reference =
                            :reference
                    FOR UPDATE
                    "
                );


            $lockStatement->execute([
                ':reference' =>
                    $reference
            ]);


            $lockedFault =
                $lockStatement->fetch();


            if (
                !$lockedFault ||
                $lockedFault[
                    'status'
                ] !== 'REPORTED'
            ) {

                throw new RuntimeException(
                    'Fault is no longer available for review.'
                );
            }


            // =============================================
            // UPDATE REPORT
            // =============================================

            $update =
                $pdo->prepare(
                    "
                    UPDATE fault_reports

                    SET
                        priority =
                            :priority,

                        status =
                            'REVIEWED',

                        reviewed_by =
                            :reviewed_by,

                        reviewed_at =
                            CURRENT_TIMESTAMP

                    WHERE
                        id =
                            :id
                    "
                );


            $update->execute([

                ':priority' =>
                    $selectedPriority,

                ':reviewed_by' =>
                    (int)$currentUser[
                        'id'
                    ],

                ':id' =>
                    (int)$lockedFault[
                        'id'
                    ]
            ]);


            // =============================================
            // HISTORY
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
                        'REVIEWED',
                        :changed_by,
                        :public_note,
                        :internal_note
                    )
                    "
                );


            $history->execute([

                ':fault_report_id' =>
                    (int)$lockedFault[
                        'id'
                    ],

                ':changed_by' =>
                    (int)$currentUser[
                        'id'
                    ],

                ':public_note' =>
                    'Fault report reviewed and awaiting technician assignment.',

                ':internal_note' =>
                    $internalNote !== ''
                        ? $internalNote
                        : null
            ]);


            // =============================================
            // AUDIT
            // =============================================

            writeAuditLog(
                $pdo,
                (int)$currentUser[
                    'id'
                ],
                'FAULT_REVIEWED',
                'FAULT_REPORT',
                (int)$lockedFault[
                    'id'
                ],
                'Reviewed fault ' .
                $reference .
                ' and assigned ' .
                $selectedPriority .
                ' priority.'
            );


            $pdo->commit();


            regenerateCsrfToken();


            $_SESSION[
                'fault_review_success'
            ] =
                'Fault reviewed successfully. It is now ready for technician assignment.';


            header(
                'Location: fault-details.php?reference=' .
                urlencode(
                    $reference
                )
            );

            exit;


        } catch (Throwable $e) {

            if (
                $pdo->inTransaction()
            ) {

                $pdo->rollBack();
            }


            error_log(
                'Fault review failed: ' .
                $e->getMessage()
            );


            $errors[
                'form'
            ] =
                'The fault could not be reviewed. Please refresh and try again.';
        }
    }
}


// =========================================================
