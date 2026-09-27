
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Water Points
        | Water Infrastructure Tracker
    </title>


    <link
        rel="stylesheet"
        href="assets/style.css"
    >


    <link
        rel="stylesheet"
        href="assets/water-points.css"
    >

</head>


<body>


<!-- =========================================================
     PUBLIC HEADER
========================================================= -->

<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('water-points.php'); ?>



<!-- =========================================================
     MAIN
========================================================= -->

<main class="container water-points-page">


    <!-- =====================================================
         PAGE INTRO
    ====================================================== -->

    <section class="directory-header">

        <p class="eyebrow">
            COMMUNITY WATER INFRASTRUCTURE
        </p>


        <h2>
            Water Points
        </h2>


        <p class="directory-description">
            Search registered community water points,
            check their current operating status and
            view their location.
        </p>

        <?php if (function_exists('userHasRole') && userHasRole(['ADMIN', 'COORDINATOR'])): ?>
            <p><a class="directory-register-link" href="register-water-point.php">Register a water point →</a></p>
        <?php endif; ?>

    </section>



    <!-- =====================================================
         SUMMARY
    ====================================================== -->

    <section
        class="public-summary"
        aria-label="Water point summary"
    >


        <article class="summary-card">

            <span class="summary-value">
                <?php
                echo $totalWaterPoints;
                ?>
            </span>

            <span class="summary-label">
                Registered
            </span>

        </article>



        <article class="summary-card">

            <span class="summary-value">
                <?php
                echo $functionalCount;
                ?>
            </span>

            <span class="summary-label">
                Functional
            </span>

        </article>



        <article class="summary-card">

            <span class="summary-value">
                <?php
                echo $partialCount;
                ?>
            </span>

            <span class="summary-label">
                Partially Functional
            </span>

        </article>



        <article class="summary-card">

            <span class="summary-value">
                <?php
                echo $nonFunctionalCount;
                ?>
            </span>

            <span class="summary-label">
                Non-Functional
            </span>

        </article>



        <article class="summary-card">

            <span class="summary-value">
                <?php
                echo $underRepairCount;
                ?>
            </span>

            <span class="summary-label">
                Under Repair
            </span>

        </article>


    </section>



    <!-- =====================================================
         SEARCH/FILTER
    ====================================================== -->

    <section class="directory-controls">

        <form
            method="get"
            action="water-points.php"
            class="directory-filter-form"
        >


            <div class="directory-search">

                <label for="search">
                    Search
                </label>


                <input
                    type="search"
                    id="search"
                    name="search"
                    value="<?php
                    echo escape(
                        $search
                    );
                    ?>"
                    placeholder="Code, name or village"
                >

            </div>



            <div>

                <label for="type">
                    Type
                </label>


                <select
                    id="type"
                    name="type"
                >

                    <option value="">
                        All types
                    </option>


                    <?php
                    foreach (
                        $typeLabels
                        as $code =>
                            $label
                    ):
                    ?>

                        <option
                            value="<?php
                            echo escape(
                                $code
                            );
                            ?>"

                            <?php
                            echo
                                $type === $code
                                    ? 'selected'
                                    : '';
                            ?>
                        >
                            <?php
                            echo escape(
                                $label
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>



            <div>

                <label for="status">
                    Status
                </label>


                <select
                    id="status"
                    name="status"
                >

                    <option value="">
                        All statuses
                    </option>


                    <?php
                    foreach (
                        $statusLabels
                        as $code =>
                            $label
                    ):
                    ?>

                        <option
                            value="<?php
                            echo escape(
                                $code
                            );
                            ?>"

                            <?php
                            echo
                                $status === $code
                                    ? 'selected'
                                    : '';
                            ?>
                        >
                            <?php
                            echo escape(
                                $label
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>



            <div class="filter-actions">

                <button
                    type="submit"
                    class="filter-button"
                >
                    Search
                </button>


                <a
                    href="water-points.php"
                    class="clear-filter"
                >
                    Clear
                </a>

            </div>


        </form>

    </section>



    <!-- =====================================================
         RESULTS HEADING
    ====================================================== -->

    <div class="results-heading">

        <h3>
            Registered Water Points
        </h3>


        <p>

            <?php
            echo count(
                $waterPoints
            );
            ?>

            result<?php
            echo
                count(
                    $waterPoints
                ) === 1
                    ? ''
                    : 's';
            ?>

        </p>

    </div>



    <!-- =====================================================
         RESULTS
    ====================================================== -->

    <?php
    if (
        empty(
            $waterPoints
        )
    ):
    ?>

        <section class="empty-state">

            <h3>
                No water points found
            </h3>


            <p>
                Try changing your search terms or
                clearing the filters.
            </p>


            <a
                href="water-points.php"
                class="empty-action"
            >
                View all water points
            </a>

        </section>


    <?php else: ?>


        <section
            class="water-point-grid"
            aria-label="Registered water points"
        >


            <?php
            foreach (
                $waterPoints
                as $waterPoint
            ):
            ?>

                <?php

                $statusClass =
                    strtolower(
                        str_replace(
                            '_',
                            '-',
                            (string)$waterPoint[
                                'status'
                            ]
                        )
                    );


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

                ?>


                <article class="water-point-card">


                    <!-- PHOTO -->

                    <div class="water-point-photo">

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
                                loading="lazy"
                            >

                        <?php else: ?>

                            <div
                                class="water-point-photo-placeholder"
                                aria-hidden="true"
                            >
                                WP
                            </div>

                        <?php endif; ?>

                    </div>



                    <!-- CONTENT -->

                    <div class="water-point-content">


                        <div class="water-point-card-top">

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


                            <span class="water-point-code">
                                <?php
                                echo escape(
                                    $waterPoint[
                                        'water_point_code'
                                    ]
                                );
                                ?>
                            </span>

                        </div>



                        <h3>
                            <?php
                            echo escape(
                                $waterPoint[
                                    'name'
                                ]
                            );
                            ?>
                        </h3>


                        <dl class="water-point-meta">

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

                        </dl>



                        <a
                            href="water-point.php?code=<?php
                            echo urlencode(
                                $waterPoint[
                                    'water_point_code'
                                ]
                            );
                            ?>"
                            class="view-water-point"
                        >
                            View Water Point
                        </a>


                    </div>


                </article>


            <?php endforeach; ?>


        </section>


    <?php endif; ?>


</main>


</body>

</html>
