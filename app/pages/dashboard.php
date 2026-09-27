
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard
        | Water Infrastructure Tracker
    </title>


    <link
        rel="stylesheet"
        href="assets/style.css"
    >


    <link
        rel="stylesheet"
        href="assets/dashboard.css"
    >

</head>


<body>


<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('dashboard.php'); ?>



<main class="container dashboard-page">


    <section class="dashboard-heading">

        <div>

            <p class="dashboard-eyebrow">
                STAFF PORTAL
            </p>


            <h2>
                Welcome,
                <?php
                echo dashboardEscape(
                    $user[
                        'full_name'
                    ]
                );
                ?>
            </h2>


            <p>
                Role:
                <strong>
                    <?php
                    echo dashboardEscape(
                        ucfirst(
                            strtolower(
                                $user[
                                    'role'
                                ]
                            )
                        )
                    );
                    ?>
                </strong>
            </p>

        </div>

    </section>



    <?php
    if (
        userHasRole([
            'ADMIN',
            'COORDINATOR'
        ])
    ):
    ?>

        <!-- =================================================
             URGENT ACTIONS
        ================================================== -->

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <p class="dashboard-eyebrow">
                        OPERATIONS
                    </p>

                    <h3>
                        Fault Management
                    </h3>

                </div>


                <a
                    href="faults.php"
                    class="section-link"
                >
                    View all faults →
                </a>

            </div>


            <div class="metric-grid">


                <a
                    href="faults.php?status=REPORTED"
                    class="metric-card action-card"
                >

                    <span class="metric-value">
                        <?php
                        echo
                            (int)(
                                $faultSummary[
                                    'reported'
                                ] ?? 0
                            );
                        ?>
                    </span>

                    <span class="metric-label">
                        New Faults
                    </span>

                    <span class="metric-description">
                        Awaiting review
                    </span>

                </a>



                <a
                    href="faults.php?status=REVIEWED"
                    class="metric-card action-card"
                >

                    <span class="metric-value">
                        <?php
                        echo
                            (int)(
                                $faultSummary[
                                    'reviewed'
                                ] ?? 0
                            );
                        ?>
                    </span>

                    <span class="metric-label">
                        Awaiting Assignment
                    </span>

                    <span class="metric-description">
                        Reviewed faults
                    </span>

                </a>



                <a
                    href="faults.php?status=IN_PROGRESS"
                    class="metric-card action-card"
                >

                    <span class="metric-value">
                        <?php
                        echo
                            (int)(
                                $faultSummary[
                                    'in_progress'
                                ] ?? 0
                            );
                        ?>
                    </span>

                    <span class="metric-label">
                        In Progress
                    </span>

                    <span class="metric-description">
                        Repairs underway
                    </span>

                </a>



                <a
                    href="faults.php?status=COMPLETED"
                    class="metric-card action-card"
                >

                    <span class="metric-value">
                        <?php
                        echo
                            (int)(
                                $faultSummary[
                                    'completed'
                                ] ?? 0
                            );
                        ?>
                    </span>

                    <span class="metric-label">
                        Awaiting Verification
                    </span>

                    <span class="metric-description">
                        Technician completed
                    </span>

                </a>



                <a
                    href="faults.php?priority=CRITICAL"
                    class="metric-card critical-card"
                >

                    <span class="metric-value">
                        <?php
                        echo
                            (int)(
                                $faultSummary[
                                    'critical'
                                ] ?? 0
                            );
                        ?>
                    </span>

                    <span class="metric-label">
                        Critical Faults
                    </span>

                    <span class="metric-description">
                        Require priority attention
                    </span>

                </a>


            </div>

        </section>



        <!-- =================================================
             WATER POINT SUMMARY
        ================================================== -->

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <p class="dashboard-eyebrow">
                        INFRASTRUCTURE
                    </p>

                    <h3>
                        Water Point Status
                    </h3>

                </div>


                <a
                    href="water-points.php"
                    class="section-link"
                >
                    View water points →
                </a>

            </div>


            <div class="metric-grid infrastructure-grid">


                <article class="metric-card">

                    <span class="metric-value">
                        <?php
                        echo
                            $totalWaterPoints;
                        ?>
                    </span>

                    <span class="metric-label">
                        Registered
                    </span>

                </article>



                <article class="metric-card">

                    <span class="metric-value">
                        <?php
                        echo
                            $functionalWaterPoints;
                        ?>
                    </span>

                    <span class="metric-label">
                        Functional
                    </span>

                </article>



                <article class="metric-card">

                    <span class="metric-value">
                        <?php
                        echo
                            $partialWaterPoints;
                        ?>
                    </span>

                    <span class="metric-label">
                        Partially Functional
                    </span>

                </article>



                <article class="metric-card">

                    <span class="metric-value">
                        <?php
                        echo
                            $nonFunctionalWaterPoints;
                        ?>
                    </span>

                    <span class="metric-label">
                        Non-Functional
                    </span>

                </article>



                <article class="metric-card">

                    <span class="metric-value">
                        <?php
                        echo
                            $underRepairWaterPoints;
                        ?>
                    </span>

                    <span class="metric-label">
                        Under Repair
                    </span>

                </article>


            </div>

        </section>



        <!-- =================================================
             RECENT FAULTS
        ================================================== -->

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <p class="dashboard-eyebrow">
                        LATEST REPORTS
                    </p>

                    <h3>
                        Recent Faults
                    </h3>

                </div>

            </div>


            <?php
            if (
                empty(
                    $recentFaults
                )
            ):
            ?>

                <div class="dashboard-empty">

                    No fault reports have been submitted yet.

                </div>


            <?php else: ?>


                <div class="dashboard-table-wrapper">

                    <table class="dashboard-table">

                        <thead>

                            <tr>

                                <th>
                                    Reference
                                </th>

                                <th>
                                    Fault
                                </th>

                                <th>
                                    Location
                                </th>

                                <th>
                                    Priority
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Age
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php
                        foreach (
                            $recentFaults
                            as $fault
                        ):
                        ?>

                            <?php

                            if (
                                !empty(
                                    $fault[
                                        'water_point_name'
                                    ]
                                )
                            ) {

                                $location =
                                    $fault[
                                        'water_point_name'
                                    ] .
                                    ', ' .
                                    $fault[
                                        'village'
                                    ];

                            } else {

                                $location =
                                    (
                                        $fault[
                                            'fault_location'
                                        ]
                                        ?: 'Infrastructure fault'
                                    ) .
                                    ', ' .
                                    $fault[
                                        'village'
                                    ];
                            }

                            ?>


                            <tr>

                                <td>

                                    <strong>
                                        <?php
                                        echo dashboardEscape(
                                            $fault[
                                                'fault_reference'
                                            ]
                                        );
                                        ?>
                                    </strong>

                                </td>


                                <td>
                                    <?php
                                    echo dashboardEscape(
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
                                </td>


                                <td>
                                    <?php
                                    echo dashboardEscape(
                                        $location
                                    );
                                    ?>
                                </td>


                                <td>
                                    <?php
                                    echo dashboardEscape(
                                        ucwords(
                                            strtolower(
                                                $fault[
                                                    'priority'
                                                ]
                                            )
                                        )
                                    );
                                    ?>
                                </td>


                                <td>
                                    <?php
                                    echo dashboardEscape(
                                        ucwords(
                                            strtolower(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $fault[
                                                        'status'
                                                    ]
                                                )
                                            )
                                        )
                                    );
                                    ?>
                                </td>


                                <td>
                                    <?php
                                    echo dashboardEscape(
                                        dashboardAge(
                                            $fault[
                                                'reported_at'
                                            ]
                                        )
                                    );
                                    ?>
                                </td>


                                <td>

                                    <a
                                        href="fault-details.php?reference=<?php
                                        echo urlencode(
                                            $fault[
                                                'fault_reference'
                                            ]
                                        );
                                        ?>"
                                        class="table-action"
                                    >
                                        View
                                    </a>

                                </td>

                            </tr>


                        <?php endforeach; ?>


                        </tbody>

                    </table>

                </div>


            <?php endif; ?>


        </section>


    <?php else: ?>


        <!-- =================================================
             TECHNICIAN DASHBOARD
        ================================================== -->

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <p class="dashboard-eyebrow">
                        MY WORK
                    </p>

                    <h3>
                        Repair Assignments
                    </h3>

                </div>

            </div>


            <div class="metric-grid">


                <article class="metric-card">

                    <span class="metric-value">
                        <?php
                        echo
                            (int)(
                                $technicianSummary[
                                    'assigned'
                                ] ?? 0
                            );
                        ?>
                    </span>

                    <span class="metric-label">
                        Newly Assigned
                    </span>

                </article>


                <article class="metric-card">

                    <span class="metric-value">
                        <?php
                        echo
                            (int)(
                                $technicianSummary[
                                    'in_progress'
                                ] ?? 0
                            );
                        ?>
                    </span>

                    <span class="metric-label">
                        In Progress
                    </span>

                </article>


                <article class="metric-card">

                    <span class="metric-value">
                        <?php
                        echo
                            (int)(
                                $technicianSummary[
                                    'completed'
                                ] ?? 0
                            );
                        ?>
                    </span>

                    <span class="metric-label">
                        Completed
                    </span>

                </article>


            </div>


            <div class="dashboard-empty technician-note">

                <a href="technician.php">Open my repairs</a> to accept assignments,
                record work, and submit repairs for verification.

            </div>

        </section>


    <?php endif; ?>


</main>


</body>

</html>
