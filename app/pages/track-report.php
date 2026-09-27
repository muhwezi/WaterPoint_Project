
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Track Report
        | Water Infrastructure Tracker
    </title>


    <link
        rel="stylesheet"
        href="assets/style.css"
    >


    <link
        rel="stylesheet"
        href="assets/report-fault.css"
    >

</head>


<body>


<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('track-report.php'); ?>



<main class="container tracking-page">


<section class="tracking-search-header">

    <p class="eyebrow">
        COMMUNITY TRACKING
    </p>


    <h2>
        Track a Fault Report
    </h2>


    <p>
        Enter the reference you received when the
        fault was reported.
    </p>


    <form
        method="get"
        action="track-report.php"
        class="tracking-search-form"
    >

        <label for="reference">
            Fault Reference
        </label>


        <div class="tracking-search-row">

            <input
                type="text"
                id="reference"
                name="reference"
                value="<?php
                echo escape(
                    $reference
                );
                ?>"
                placeholder="Example: WFT-26-K7M4P9"
                maxlength="30"
                required
            >


            <button type="submit">
                Track Report
            </button>

        </div>

    </form>

</section>



<?php
if (
    $searched &&
    !$fault
):
?>

    <section class="tracking-not-found">

        <h3>
            Report not found
        </h3>


        <p>
            Check the reference and try again.
        </p>

    </section>

<?php endif; ?>



<?php if ($fault): ?>

    <?php

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
            $fault[
                'fault_location'
            ] .
            ', ' .
            $fault[
                'village'
            ];
    }


    $currentStatus =
        $statusLabels[
            $fault[
                'status'
            ]
        ]
        ?? $fault[
            'status'
        ];


    $faultLabel =
        $faultTypeLabels[
            $fault[
                'fault_type'
            ]
        ]
        ?? $fault[
            'fault_type'
        ];


    $isClosed =
        $fault[
            'status'
        ] ===
        'CLOSED' &&
        $fault[
            'closed_at'
        ] !== null;

    ?>


    <section class="tracking-result">


        <div class="tracking-result-top">


            <div>

                <p class="tracking-reference">
                    <?php
                    echo escape(
                        $fault[
                            'fault_reference'
                        ]
                    );
                    ?>
                </p>


                <h3>
                    <?php
                    echo escape(
                        $faultLabel
                    );
                    ?>
                </h3>


                <p class="tracking-location">
                    <?php
                    echo escape(
                        $locationLabel
                    );
                    ?>
                </p>


                <?php
                if (
                    !empty(
                        $fault[
                            'water_point_code'
                        ]
                    )
                ):
                ?>

                    <p class="tracking-water-point">
                        Water Point:
                        <strong>
                            <?php
                            echo escape(
                                $fault[
                                    'water_point_code'
                                ]
                            );
                            ?>
                        </strong>
                    </p>

                <?php endif; ?>

            </div>



            <div class="tracking-status-box">

                <span>
                    Current Status
                </span>

                <strong>
                    <?php
                    echo escape(
                        $currentStatus
                    );
                    ?>
                </strong>

            </div>


        </div>



        <div class="turnaround-card">

            <?php if ($isClosed): ?>

                <span>
                    Resolution Time
                </span>

                <strong>
                    <?php
                    echo escape(
                        publicDuration(
                            $fault[
                                'reported_at'
                            ],
                            $fault[
                                'closed_at'
                            ]
                        )
                    );
                    ?>
                </strong>

            <?php else: ?>

                <span>
                    Time Since Report
                </span>

                <strong>
                    <?php
                    echo escape(
                        publicDuration(
                            $fault[
                                'reported_at'
                            ]
                        )
                    );
                    ?>
                </strong>

            <?php endif; ?>

        </div>



        <div class="tracking-timeline-card">

            <h3>
                Progress Timeline
            </h3>


            <div class="tracking-timeline">


                <?php
                foreach (
                    $history
                    as $index =>
                        $item
                ):
                ?>

                    <?php

                    $historyLabel =
                        $statusLabels[
                            $item[
                                'status'
                            ]
                        ]
                        ?? $item[
                            'status'
                        ];

                    ?>


                    <article class="timeline-item">

                        <div class="timeline-marker">
                            ✓
                        </div>


                        <div class="timeline-content">

                            <h4>
                                <?php
                                echo escape(
                                    $historyLabel
                                );
                                ?>
                            </h4>


                            <time>
                                <?php
                                echo escape(
                                    publicDate(
                                        $item[
                                            'changed_at'
                                        ]
                                    )
                                );
                                ?>
                            </time>


                            <?php
                            if (
                                !empty(
                                    $item[
                                        'public_note'
                                    ]
                                )
                            ):
                            ?>

                                <p>
                                    <?php
                                    echo escape(
                                        $item[
                                            'public_note'
                                        ]
                                    );
                                    ?>
                                </p>

                            <?php endif; ?>

                        </div>

                    </article>


                <?php endforeach; ?>


            </div>

        </div>



        <div class="tracking-privacy-note">

            <strong>
                Privacy:
            </strong>

            This page shows only public progress information.
            Reporter contact details and internal staff notes
            are not displayed.

        </div>


    </section>

<?php endif; ?>


</main>


</body>

</html>