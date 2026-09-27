
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Fault Management
        | Water Infrastructure Tracker
    </title>


    <link
        rel="stylesheet"
        href="assets/style.css"
    >


    <link
        rel="stylesheet"
        href="assets/faults.css"
    >

</head>


<body>


<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('faults.php'); ?>



<main class="container faults-page">


    <section class="faults-heading">

        <p class="faults-eyebrow">
            OPERATIONS
        </p>


        <h2>
            Fault Management
        </h2>


        <p>
            Review community reports, prioritise faults
            and move valid reports into the repair workflow.
        </p>

    </section>



    <!-- =====================================================
         STATUS SHORTCUTS
    ====================================================== -->

    <nav
        class="fault-status-tabs"
        aria-label="Fault status filters"
    >

        <a
            href="faults.php"
            <?php
            echo
                $status === ''
                    ? 'aria-current="page"'
                    : '';
            ?>
        >
            All
        </a>


        <?php
        foreach (
            $statusLabels
            as $code =>
                $label
        ):
        ?>

            <a
                href="faults.php?status=<?php
                echo urlencode(
                    $code
                );
                ?>"

                <?php
                echo
                    $status === $code
                        ? 'aria-current="page"'
                        : '';
                ?>
            >
                <?php
                echo faultListEscape(
                    $label
                );
                ?>
            </a>

        <?php endforeach; ?>

    </nav>



    <!-- =====================================================
         FILTER
    ====================================================== -->

    <section class="fault-filter-card">

        <form
            method="get"
            action="faults.php"
            class="fault-filter-form"
        >


            <div>

                <label for="search">
                    Search
                </label>


                <input
                    type="search"
                    id="search"
                    name="search"
                    value="<?php
                    echo faultListEscape(
                        $search
                    );
                    ?>"
                    placeholder="Reference, code, village, reporter..."
                >

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
                            echo faultListEscape(
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
                            echo faultListEscape(
                                $label
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>



            <div>

                <label for="priority">
                    Priority
                </label>


                <select
                    id="priority"
                    name="priority"
                >

                    <option value="">
                        All priorities
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
                            echo faultListEscape(
                                $code
                            );
                            ?>"

                            <?php
                            echo
                                $priority === $code
                                    ? 'selected'
                                    : '';
                            ?>
                        >
                            <?php
                            echo faultListEscape(
                                $label
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>



            <div class="filter-buttons">

                <button type="submit">
                    Apply
                </button>


                <a href="faults.php">
                    Clear
                </a>

            </div>


        </form>

    </section>



    <div class="fault-result-heading">

        <h3>
            Fault Reports
        </h3>


        <span>
            <?php
            echo count(
                $faults
            );
            ?>
            record<?php
            echo
                count(
                    $faults
                ) === 1
                    ? ''
                    : 's';
            ?>
        </span>

    </div>



    <?php
    if (
        empty(
            $faults
        )
    ):
    ?>

        <section class="fault-empty">

            No fault reports match the selected filters.

        </section>


    <?php else: ?>


        <div class="fault-table-wrapper">

            <table class="fault-table">

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
                            Reporter
                        </th>

                        <th>
                            Priority
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Reported
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php
                foreach (
                    $faults
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


                    $priorityClass =
                        strtolower(
                            $fault[
                                'priority'
                            ]
                        );


                    $statusClass =
                        strtolower(
                            str_replace(
                                '_',
                                '-',
                                $fault[
                                    'status'
                                ]
                            )
                        );

                    ?>


                    <tr>

                        <td>

                            <strong>
                                <?php
                                echo faultListEscape(
                                    $fault[
                                        'fault_reference'
                                    ]
                                );
                                ?>
                            </strong>


                            <?php
                            if (
                                !empty(
                                    $fault[
                                        'water_point_code'
                                    ]
                                )
                            ):
                            ?>

                                <small>
                                    <?php
                                    echo faultListEscape(
                                        $fault[
                                            'water_point_code'
                                        ]
                                    );
                                    ?>
                                </small>

                            <?php endif; ?>

                        </td>


                        <td>
                            <?php
                            echo faultListEscape(
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
                            echo faultListEscape(
                                $location
                            );
                            ?>
                        </td>


                        <td>

                            <?php
                            echo faultListEscape(
                                $fault[
                                    'reporter_name'
                                ]
                            );
                            ?>

                            <small>
                                <?php
                                echo faultListEscape(
                                    $fault[
                                        'reporter_phone'
                                    ]
                                );
                                ?>
                            </small>

                        </td>


                        <td>

                            <span
                                class="priority-badge <?php
                                echo faultListEscape(
                                    $priorityClass
                                );
                                ?>"
                            >
                                <?php
                                echo faultListEscape(
                                    $priorityLabels[
                                        $fault[
                                            'priority'
                                        ]
                                    ]
                                    ?? $fault[
                                        'priority'
                                    ]
                                );
                                ?>
                            </span>

                        </td>


                        <td>

                            <span
                                class="fault-status <?php
                                echo faultListEscape(
                                    $statusClass
                                );
                                ?>"
                            >
                                <?php
                                echo faultListEscape(
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
                            </span>

                        </td>


                        <td>
                            <?php
                            echo faultListEscape(
                                date(
                                    'd M Y H:i',
                                    strtotime(
                                        $fault[
                                            'reported_at'
                                        ]
                                    )
                                )
                            );
                            ?>
                        </td>


                        <td>

                            <a
                                class="fault-view-link"
                                href="fault-details.php?reference=<?php
                                echo urlencode(
                                    $fault[
                                        'fault_reference'
                                    ]
                                );
                                ?>"
                            >
                                <?php
                                echo
                                    $fault[
                                        'status'
                                    ] ===
                                    'REPORTED'
                                        ? 'Review'
                                        : 'View';
                                ?>
                            </a>

                        </td>

                    </tr>


                <?php endforeach; ?>


                </tbody>

            </table>

        </div>


    <?php endif; ?>


</main>


</body>

</html>