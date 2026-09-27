
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
        if ($waterPoint):

            echo escape(
                $waterPoint[
                    'water_point_code'
                ]
            );

            echo ' | ';

        else:

            echo 'Water Point Not Found | ';

        endif;
        ?>

        Water Infrastructure Tracker
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
        href="assets/water-points.css"
    >


    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        defer
    ></script>


    <script
        src="assets/water-point-view.js"
        defer
    ></script>

</head>


<body>


<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('water-point.php'); ?>



<main class="container water-point-view-page">


<?php if (!$waterPoint): ?>


    <section class="empty-state not-found-state">

        <h2>
            Water point not found
        </h2>


        <p>
            The water-point code could not be found.
        </p>


        <a
            href="water-points.php"
            class="empty-action"
        >
            Browse Water Points
        </a>

    </section>


<?php else: ?>


    <?php

    $typeLabel =
        $typeLabels[
            $waterPoint[
                'type'
            ]
        ]
        ?? $waterPoint[
            'type'
        ];


    $statusLabel =
        $statusLabels[
            $waterPoint[
                'status'
            ]
        ]
        ?? $waterPoint[
            'status'
        ];


    $statusClass =
        strtolower(
            str_replace(
                '_',
                '-',
                $waterPoint[
                    'status'
                ]
            )
        );


    $hasCoordinates =
        $waterPoint[
            'latitude'
        ] !== null &&
        $waterPoint[
            'longitude'
        ] !== null;

    ?>


    <!-- =====================================================
         BREADCRUMB
    ====================================================== -->

    <div class="water-point-back">

        <a href="water-points.php">
            ← Back to Water Points
        </a>

    </div>



    <!-- =====================================================
         HERO
    ====================================================== -->

    <section class="water-point-detail-header">


        <div>


            <div class="detail-badges">

                <span
                    class="status-badge <?php
                    echo escape(
                        $statusClass
                    );
                    ?>"
                >
                    <?php
                    echo escape(
                        $statusLabel
                    );
                    ?>
                </span>


                <span class="water-point-code-large">
                    <?php
                    echo escape(
                        $waterPoint[
                            'water_point_code'
                        ]
                    );
                    ?>
                </span>

            </div>


            <h2>
                <?php
                echo escape(
                    $waterPoint[
                        'name'
                    ]
                );
                ?>
            </h2>


            <p class="detail-location">
                <?php
                echo escape(
                    $waterPoint[
                        'village'
                    ]
                );
                ?>
            </p>


        </div>


        <!-- FUTURE FAULT REPORT ACTION -->

        <div class="detail-primary-action">

            <a
                href="report-fault.php?wp=<?php
                echo urlencode(
                    $waterPoint[
                        'water_point_code'
                    ]
                );
                ?>"
                class="report-fault-button"
            >
                Report a Fault
            </a>

        </div>


    </section>



    <!-- =====================================================
         MAIN DETAIL GRID
    ====================================================== -->

    <section class="water-point-detail-grid">


        <!-- PHOTO -->

        <div class="detail-photo-card">


            <?php
            if (
                !empty(
                    $waterPoint[
                        'photo_path'
                    ]
                )
            ):
            ?>

                <img
                    src="<?php
                    echo escape(
                        $waterPoint[
                            'photo_path'
                        ]
                    );
                    ?>"
                    alt="<?php
                    echo escape(
                        $waterPoint[
                            'name'
                        ]
                    );
                    ?>"
                >

            <?php else: ?>

                <div class="detail-photo-placeholder">

                    <span>
                        No site photo available
                    </span>

                </div>

            <?php endif; ?>


        </div>



        <!-- DETAILS -->

        <div class="detail-information-card">


            <h3>
                Water Point Details
            </h3>


            <dl class="detail-list">


                <div>

                    <dt>
                        Permanent Code
                    </dt>

                    <dd>
                        <strong>
                            <?php
                            echo escape(
                                $waterPoint[
                                    'water_point_code'
                                ]
                            );
                            ?>
                        </strong>
                    </dd>

                </div>



                <div>

                    <dt>
                        Type
                    </dt>

                    <dd>
                        <?php
                        echo escape(
                            $typeLabel
                        );
                        ?>
                    </dd>

                </div>



                <div>

                    <dt>
                        Village
                    </dt>

                    <dd>
                        <?php
                        echo escape(
                            $waterPoint[
                                'village'
                            ]
                        );
                        ?>
                    </dd>

                </div>



                <div>

                    <dt>
                        Current Status
                    </dt>

                    <dd>
                        <?php
                        echo escape(
                            $statusLabel
                        );
                        ?>
                    </dd>

                </div>



                <?php if ($hasCoordinates): ?>

                    <div>

                        <dt>
                            Latitude
                        </dt>

                        <dd>
                            <?php
                            echo escape(
                                (string)$waterPoint[
                                    'latitude'
                                ]
                            );
                            ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Longitude
                        </dt>

                        <dd>
                            <?php
                            echo escape(
                                (string)$waterPoint[
                                    'longitude'
                                ]
                            );
                            ?>
                        </dd>

                    </div>

                <?php endif; ?>


                <div>

                    <dt>
                        Registered
                    </dt>

                    <dd>

                        <?php
                        echo escape(
                            date(
                                'd M Y',
                                strtotime(
                                    $waterPoint[
                                        'created_at'
                                    ]
                                )
                            )
                        );
                        ?>

                    </dd>

                </div>


            </dl>


        </div>


    </section>



    <!-- =====================================================
         MAP
    ====================================================== -->

    <section class="public-map-card">


        <div class="public-map-heading">

            <div>

                <h3>
                    Location
                </h3>


                <?php if ($hasCoordinates): ?>

                    <p>
                        Registered geographical position
                        of this water point.
                    </p>

                <?php else: ?>

                    <p>
                        Coordinates have not yet been
                        recorded for this water point.
                    </p>

                <?php endif; ?>

            </div>

        </div>



        <?php if ($hasCoordinates): ?>

            <div
                id="public-water-point-map"

                data-latitude="<?php
                echo escape(
                    (string)$waterPoint[
                        'latitude'
                    ]
                );
                ?>"

                data-longitude="<?php
                echo escape(
                    (string)$waterPoint[
                        'longitude'
                    ]
                );
                ?>"

                data-name="<?php
                echo escape(
                    $waterPoint[
                        'name'
                    ]
                );
                ?>"

                data-code="<?php
                echo escape(
                    $waterPoint[
                        'water_point_code'
                    ]
                );
                ?>"

                aria-label="Map showing the registered water point location"
            ></div>

        <?php else: ?>

            <div class="no-map-location">

                Location coordinates are not available.

            </div>

        <?php endif; ?>


    </section>



    <!-- =====================================================
         COMMUNITY ACTION
    ====================================================== -->

    <section class="community-action-card">


        <div>

            <p class="eyebrow">
                COMMUNITY REPORTING
            </p>


            <h3>
                Is there a problem with this water point?
            </h3>


            <p>
                Report a fault using this permanent
                water-point code. You will receive a
                tracking reference after submitting
                the report.
            </p>

        </div>


        <a
            href="report-fault.php?wp=<?php
            echo urlencode(
                $waterPoint[
                    'water_point_code'
                ]
            );
            ?>"
            class="report-fault-button"
        >
            Report Fault at
            <?php
            echo escape(
                $waterPoint[
                    'water_point_code'
                ]
            );
            ?>
        </a>


    </section>


<?php endif; ?>


</main>


</body>

</html>