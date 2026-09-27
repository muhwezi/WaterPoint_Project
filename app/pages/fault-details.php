
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php
        echo faultDetailEscape(
            $fault[
                'fault_reference'
            ]
        );
        ?>
        | Fault Management
    </title>


    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >


    <link
        rel="stylesheet"
        href="assets/style.css"
    >


    <link
        rel="stylesheet"
        href="assets/faults.css"
    >

    <link rel="stylesheet" href="assets/workflow.css">


    <?php if ($hasCoordinates): ?>

        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            defer
        ></script>

        <script
            src="assets/fault-details.js"
            defer
        ></script>

    <?php endif; ?>

</head>


<body>


<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('fault-details.php'); ?>



<main class="container fault-details-page">


    <div class="fault-back">

        <a href="faults.php">
            ← Back to Fault Management
        </a>

    </div>



    <?php if ($successMessage !== ''): ?>

        <div
            class="staff-success"
            role="status"
        >
            <?php
            echo faultDetailEscape(
                $successMessage
            );
            ?>
        </div>

    <?php endif; ?>



    <?php if ($errors): ?>

        <div
            class="staff-error"
            role="alert"
        >

            <strong>
                Please check the review
            </strong>

            <ul>

                <?php
                foreach (
                    $errors
                    as $message
                ):
                ?>

                    <li>
                        <?php
                        echo faultDetailEscape(
                            $message
                        );
                        ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>



    <section class="fault-detail-heading">

        <div>

            <p class="fault-reference">
                <?php
                echo faultDetailEscape(
                    $fault[
                        'fault_reference'
                    ]
                );
                ?>
            </p>


            <h2>
                <?php
                echo faultDetailEscape(
                    ucwords(
                        strtolower(
                            str_replace(
                                '_',
                                ' ',
                                $fault[
                                    'fault_type'
                                ]
                            )
                        )
                    )
                );
                ?>
            </h2>


            <p>
                <?php
                echo faultDetailEscape(
                    $locationLabel
                );
                ?>
            </p>

        </div>


        <div class="fault-current-state">

            <span>
                Status
            </span>

            <strong>
                <?php
                echo faultDetailEscape(
                    $statusLabels[
                        $fault[
                            'status'
                        ]
                    ]
                    ?? $fault[
                        'status'
                    ]
                );
                ?>
            </strong>


            <span>
                Priority
            </span>

            <strong>
                <?php
                echo faultDetailEscape(
                    $fault[
                        'priority'
                    ] ===
                    'UNASSESSED'
                        ? 'Unassessed'
                        : ucfirst(
                            strtolower(
                                $fault[
                                    'priority'
                                ]
                            )
                        )
                );
                ?>
            </strong>

        </div>

    </section>



    <section class="fault-detail-grid">


        <!-- =================================================
             REPORT
        ================================================== -->

        <article class="fault-information-card">

            <h3>
                Fault Report
            </h3>


            <dl class="staff-detail-list">

                <div>

                    <dt>
                        Reported
                    </dt>

                    <dd>
                        <?php
                        echo faultDetailEscape(
                            date(
                                'd M Y, g:i A',
                                strtotime(
                                    $fault[
                                        'reported_at'
                                    ]
                                )
                            )
                        );
                        ?>
                    </dd>

                </div>


                <div>

                    <dt>
                        Scope
                    </dt>

                    <dd>
                        <?php
                        echo faultDetailEscape(
                            $fault[
                                'report_scope'
                            ] ===
                            'KNOWN_WATER_POINT'
                                ? 'Known Water Point'
                                : 'Other Infrastructure'
                        );
                        ?>
                    </dd>

                </div>


                <?php
                if (
                    !empty(
                        $fault[
                            'water_point_code'
                        ]
                    )
                ):
                ?>

                    <div>

                        <dt>
                            Water Point
                        </dt>

                        <dd>

                            <a
                                href="water-point.php?code=<?php
                                echo urlencode(
                                    $fault[
                                        'water_point_code'
                                    ]
                                );
                                ?>"
                            >
                                <?php
                                echo faultDetailEscape(
                                    $fault[
                                        'water_point_code'
                                    ]
                                );
                                ?>
                            </a>

                        </dd>

                    </div>

                <?php endif; ?>


                <div>

                    <dt>
                        Infrastructure
                    </dt>

                    <dd>
                        <?php
                        echo faultDetailEscape(
                            ucwords(
                                strtolower(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $fault[
                                            'infrastructure_type'
                                        ]
                                    )
                                )
                            )
                        );
                        ?>
                    </dd>

                </div>


                <div>

                    <dt>
                        Location
                    </dt>

                    <dd>
                        <?php
                        echo faultDetailEscape(
                            $locationLabel
                        );
                        ?>
                    </dd>

                </div>

            </dl>


            <div class="description-block">

                <h4>
                    Description
                </h4>

                <p>
                    <?php
                    echo nl2br(
                        faultDetailEscape(
                            $fault[
                                'description'
                            ]
                        )
                    );
                    ?>
                </p>

            </div>

        </article>



        <!-- =================================================
             REPORTER
        ================================================== -->

        <article class="fault-information-card">

            <h3>
                Reporter Details
            </h3>


            <dl class="staff-detail-list">

                <div>

                    <dt>
                        Name
                    </dt>

                    <dd>
                        <?php
                        echo faultDetailEscape(
                            $fault[
                                'reporter_name'
                            ]
                        );
                        ?>
                    </dd>

                </div>


                <div>

                    <dt>
                        Phone
                    </dt>

                    <dd>
                        <?php
                        echo faultDetailEscape(
                            $fault[
                                'reporter_phone'
                            ]
                        );
                        ?>
                    </dd>

                </div>

            </dl>


            <p class="staff-private-note">
                Reporter contact details are visible
                to authorized staff only.
            </p>

        </article>


    </section>



    <!-- =====================================================
         PHOTO
    ====================================================== -->

    <?php
    if (
        !empty(
            $fault[
                'photo_path'
            ]
        )
    ):
    ?>

        <section class="fault-information-card">

            <h3>
                Fault Photograph
            </h3>


            <img
                src="<?php
                echo faultDetailEscape(
                    $fault[
                        'photo_path'
                    ]
                );
                ?>"
                alt="Fault report photograph"
                class="staff-fault-photo"
            >

        </section>

    <?php endif; ?>



    <!-- =====================================================
         MAP
    ====================================================== -->

    <?php if ($hasCoordinates): ?>

        <section class="fault-information-card">

            <h3>
                Fault Location
            </h3>


            <div
                id="staff-fault-map"

                data-latitude="<?php
                echo faultDetailEscape(
                    (string)$fault[
                        'latitude'
                    ]
                );
                ?>"

                data-longitude="<?php
                echo faultDetailEscape(
                    (string)$fault[
                        'longitude'
                    ]
                );
                ?>"

                data-reference="<?php
                echo faultDetailEscape(
                    $fault[
                        'fault_reference'
                    ]
                );
                ?>"
            ></div>

        </section>

    <?php endif; ?>



    <!-- =====================================================
         REVIEW FORM
    ====================================================== -->

    <?php
    if (
        $fault[
            'status'
        ] === 'REPORTED'
    ):
    ?>

        <section class="fault-review-card">

            <p class="faults-eyebrow">
                NEXT ACTION
            </p>


            <h3>
                Review Fault
            </h3>


            <p>
                Confirm that the report is valid,
                assign its operational priority and
                add any internal notes.
            </p>


            <form
                method="post"
                action="fault-details.php"
            >

                <input
                    type="hidden"
                    name="reference"
                    value="<?php
                    echo faultDetailEscape(
                        $fault[
                            'fault_reference'
                        ]
                    );
                    ?>"
                >


                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo faultDetailEscape(
                        csrfToken()
                    );
                    ?>"
                >


                <div class="review-field">

                    <label for="priority">
                        Priority *
                    </label>


                    <select
                        id="priority"
                        name="priority"
                        required
                    >

                        <option value="">
                            Select priority
                        </option>


                        <?php
                        foreach (
                            $priorityLabels
                            as $code =>
                                $label
                        ):
                        ?>

                            <option
                                value="<?php
                                echo faultDetailEscape(
                                    $code
                                );
                                ?>"

                                <?php
                                echo
                                    $selectedPriority
                                    === $code
                                        ? 'selected'
                                        : '';
                                ?>
                            >
                                <?php
                                echo faultDetailEscape(
                                    $label
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="review-field">

                    <label for="internal_note">
                        Internal Review Notes
                    </label>


                    <textarea
                        id="internal_note"
                        name="internal_note"
                        rows="4"
                        maxlength="2000"
                        placeholder="Optional notes for staff..."
                    ><?php
                    echo faultDetailEscape(
                        $internalNote
                    );
                    ?></textarea>

                </div>


                <button
                    type="submit"
                    class="review-submit"
                >
                    Confirm Review
                </button>

            </form>

        </section>


    <?php elseif (
        $fault[
            'status'
        ] === 'REVIEWED'
    ): ?>

        <section class="next-action-card">

            <p class="faults-eyebrow">
                NEXT ACTION
            </p>


            <h3>
                Ready for Technician Assignment
            </h3>


            <p>
                This fault has been reviewed and prioritised.
                The next workflow stage is technician assignment.
            </p>

        </section>

    <?php endif; ?>



    <?php require __DIR__ . '/partials/fault-workflow.php'; ?>

    <!-- =====================================================
         INTERNAL TIMELINE
    ====================================================== -->

    <section class="fault-information-card">

        <h3>
            Status History
        </h3>


        <div class="staff-history">


            <?php
            foreach (
                $history
                as $entry
            ):
            ?>

                <article class="staff-history-item">

                    <strong>
                        <?php
                        echo faultDetailEscape(
                            $statusLabels[
                                $entry[
                                    'status'
                                ]
                            ]
                            ?? $entry[
                                'status'
                            ]
                        );
                        ?>
                    </strong>


                    <span>
                        <?php
                        echo faultDetailEscape(
                            date(
                                'd M Y, g:i A',
                                strtotime(
                                    $entry[
                                        'changed_at'
                                    ]
                                )
                            )
                        );
                        ?>
                    </span>


                    <?php
                    if (
                        !empty(
                            $entry[
                                'changed_by_name'
                            ]
                        )
                    ):
                    ?>

                        <p>
                            Updated by:
                            <?php
                            echo faultDetailEscape(
                                $entry[
                                    'changed_by_name'
                                ]
                            );
                            ?>
                        </p>

                    <?php endif; ?>


                    <?php
                    if (
                        !empty(
                            $entry[
                                'internal_note'
                            ]
                        )
                    ):
                    ?>

                        <p class="internal-history-note">

                            <strong>
                                Internal:
                            </strong>

                            <?php
                            echo nl2br(
                                faultDetailEscape(
                                    $entry[
                                        'internal_note'
                                    ]
                                )
                            );
                            ?>

                        </p>

                    <?php endif; ?>

                </article>

            <?php endforeach; ?>


        </div>

    </section>


</main>


</body>

</html>
