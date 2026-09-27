
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Report a Fault
        | Water Infrastructure Tracker
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
        href="assets/report-fault.css"
    >


    <script>
        window.waterPointDirectory =
        <?php
        echo json_encode(
            $waterPointDirectory,
            JSON_HEX_TAG |
            JSON_HEX_AMP |
            JSON_HEX_APOS |
            JSON_HEX_QUOT
        );
        ?>;
    </script>


    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        defer
    ></script>


    <script
        src="assets/report-fault.js"
        defer
    ></script>

</head>


<body>


<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('report-fault.php'); ?>



<main class="container fault-report-page">


<?php
if (
    is_array(
        $successData
    )
):
?>

    <section
        class="fault-success-card"
        id="form-feedback"
        tabindex="-1"
        role="status"
    >

        <div class="success-symbol">
            ✓
        </div>


        <p class="eyebrow">
            REPORT RECEIVED
        </p>


        <h2>
            Fault report submitted
        </h2>


        <p>
            Your fault has been recorded successfully.
            Keep the reference below so that you can
            track its progress.
        </p>


        <div class="tracking-reference-box">

            <span>
                Tracking Reference
            </span>

            <strong>
                <?php
                echo authEscape(
                    $successData[
                        'reference'
                    ]
                );
                ?>
            </strong>

        </div>


        <dl class="success-details">

            <div>

                <dt>
                    Fault
                </dt>

                <dd>
                    <?php
                    echo authEscape(
                        $successData[
                            'fault_type'
                        ]
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
                    echo authEscape(
                        $successData[
                            'location'
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
                    Reported
                </dd>

            </div>

        </dl>


        <div class="success-actions">

            <a
                href="track-report.php?reference=<?php
                echo urlencode(
                    $successData[
                        'reference'
                    ]
                );
                ?>"
                class="primary-public-button"
            >
                Track This Report
            </a>


            <a
                href="report-fault.php"
                class="secondary-public-button"
            >
                Report Another Fault
            </a>

        </div>

    </section>


<?php else: ?>


    <section class="fault-form-header">

        <p class="eyebrow">
            COMMUNITY REPORTING
        </p>


        <h2>
            Report a Water Fault
        </h2>


        <p>
            Report a problem at a registered water point
            or another part of the community water
            infrastructure.
        </p>

    </section>



    <?php if ($errors): ?>

        <div
            class="fault-error-summary"
            id="form-feedback"
            tabindex="-1"
            role="alert"
        >

            <h3>
                Please check the report
            </h3>


            <ul>

                <?php
                foreach (
                    $errors
                    as $field =>
                        $message
                ):
                ?>

                    <li>

                        <?php
                        if (
                            $field ===
                            'form'
                        ):
                        ?>

                            <?php
                            echo authEscape(
                                $message
                            );
                            ?>

                        <?php else: ?>

                            <a
                                href="#<?php
                                echo authEscape(
                                    $field
                                );
                                ?>"
                            >
                                <?php
                                echo authEscape(
                                    $message
                                );
                                ?>
                            </a>

                        <?php endif; ?>

                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>



    <form
        method="post"
        action="report-fault.php"
        enctype="multipart/form-data"
        id="fault-report-form"
        class="fault-report-form"
    >


        <input
            type="hidden"
            name="csrf_token"
            value="<?php
            echo authEscape(
                csrfToken()
            );
            ?>"
        >



        <!-- =================================================
             WHERE IS THE PROBLEM?
        ================================================== -->

        <fieldset class="fault-section">

            <legend>
                1. Where is the problem?
            </legend>


            <p class="section-description">
                Tell us whether the fault is at a
                registered water point or somewhere else.
            </p>


            <div class="scope-options">


                <label class="scope-card">

                    <input
                        type="radio"
                        name="report_scope"
                        value="KNOWN_WATER_POINT"

                        <?php
                        echo
                            $values[
                                'report_scope'
                            ] ===
                            'KNOWN_WATER_POINT'
                                ? 'checked'
                                : '';
                        ?>
                    >

                    <span>

                        <strong>
                            Known Water Point
                        </strong>

                        <small>
                            Borehole, spring, tank or
                            another registered water point.
                        </small>

                    </span>

                </label>



                <label class="scope-card">

                    <input
                        type="radio"
                        name="report_scope"
                        value="OTHER_INFRASTRUCTURE"

                        <?php
                        echo
                            $values[
                                'report_scope'
                            ] ===
                            'OTHER_INFRASTRUCTURE'
                                ? 'checked'
                                : '';
                        ?>
                    >

                    <span>

                        <strong>
                            Other Infrastructure
                        </strong>

                        <small>
                            Burst road pipe, valve, public
                            tap or another water fault.
                        </small>

                    </span>

                </label>


            </div>


            <?php
            if (
                isset(
                    $errors[
                        'report_scope'
                    ]
                )
            ):
            ?>

                <p class="field-error">
                    <?php
                    echo authEscape(
                        $errors[
                            'report_scope'
                        ]
                    );
                    ?>
                </p>

            <?php endif; ?>


        </fieldset>



        <!-- =================================================
             KNOWN WATER POINT
        ================================================== -->

        <fieldset
            class="fault-section"
            id="known-water-point-section"
            <?php
            echo
                $values[
                    'report_scope'
                ] ===
                'KNOWN_WATER_POINT'
                    ? ''
                    : 'hidden';
            ?>
        >

            <legend>
                2. Identify the Water Point
            </legend>


            <div class="form-field">

                <label for="water_point_code">
                    Water Point Code *
                </label>


                <input
                    type="text"
                    id="water_point_code"
                    name="water_point_code"
                    list="water-point-options"
                    value="<?php
                    echo authEscape(
                        $values[
                            'water_point_code'
                        ]
                    );
                    ?>"
                    autocomplete="off"
                    placeholder="Example: WP-000001"
                >


                <datalist id="water-point-options">

                    <?php
                    foreach (
                        $waterPoints
                        as $point
                    ):
                    ?>

                        <option
                            value="<?php
                            echo authEscape(
                                $point[
                                    'water_point_code'
                                ]
                            );
                            ?>"
                        >
                            <?php
                            echo authEscape(
                                $point[
                                    'name'
                                ] .
                                ' — ' .
                                $point[
                                    'village'
                                ]
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </datalist>


                <p class="field-help">
                    Enter the code shown on the water point,
                    or start typing to search.
                </p>


                <?php
                if (
                    isset(
                        $errors[
                            'water_point_code'
                        ]
                    )
                ):
                ?>

                    <p class="field-error">
                        <?php
                        echo authEscape(
                            $errors[
                                'water_point_code'
                            ]
                        );
                        ?>
                    </p>

                <?php endif; ?>

            </div>



            <div
                id="selected-water-point"
                class="selected-water-point"
                hidden
            >

                <p class="selected-code"></p>

                <h3 class="selected-name"></h3>

                <p class="selected-village"></p>

                <p class="selected-status"></p>

            </div>

        </fieldset>



        <!-- =================================================
             OTHER INFRASTRUCTURE
        ================================================== -->

        <fieldset
            class="fault-section"
            id="other-infrastructure-section"
            <?php
            echo
                $values[
                    'report_scope'
                ] ===
                'OTHER_INFRASTRUCTURE'
                    ? ''
                    : 'hidden';
            ?>
        >

            <legend>
                2. Identify the Location
            </legend>


            <div class="field-grid">


                <div class="form-field">

                    <label for="infrastructure_type">
                        Infrastructure Type *
                    </label>


                    <select
                        id="infrastructure_type"
                        name="infrastructure_type"
                    >

                        <option value="">
                            Select infrastructure
                        </option>


                        <?php
                        foreach (
                            $infrastructureTypes
                            as $code =>
                                $label
                        ):
                        ?>

                            <option
                                value="<?php
                                echo authEscape(
                                    $code
                                );
                                ?>"

                                <?php
                                echo
                                    $values[
                                        'infrastructure_type'
                                    ] === $code
                                        ? 'selected'
                                        : '';
                                ?>
                            >
                                <?php
                                echo authEscape(
                                    $label
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>


                    <?php
                    if (
                        isset(
                            $errors[
                                'infrastructure_type'
                            ]
                        )
                    ):
                    ?>

                        <p class="field-error">
                            <?php
                            echo authEscape(
                                $errors[
                                    'infrastructure_type'
                                ]
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                </div>



                <div class="form-field">

                    <label for="village">
                        Village / Area *
                    </label>


                    <input
                        type="text"
                        id="village"
                        name="village"
                        maxlength="120"
                        value="<?php
                        echo authEscape(
                            $values[
                                'village'
                            ]
                        );
                        ?>"
                    >


                    <?php
                    if (
                        isset(
                            $errors[
                                'village'
                            ]
                        )
                    ):
                    ?>

                        <p class="field-error">
                            <?php
                            echo authEscape(
                                $errors[
                                    'village'
                                ]
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                </div>

            </div>



            <div class="form-field">

                <label for="fault_location">
                    Location / Nearby Landmark *
                </label>


                <input
                    type="text"
                    id="fault_location"
                    name="fault_location"
                    maxlength="180"
                    value="<?php
                    echo authEscape(
                        $values[
                            'fault_location'
                        ]
                    );
                    ?>"
                    placeholder="Example: Main road near Nyakatooma Trading Centre"
                >


                <?php
                if (
                    isset(
                        $errors[
                            'fault_location'
                        ]
                    )
                ):
                ?>

                    <p class="field-error">
                        <?php
                        echo authEscape(
                            $errors[
                                'fault_location'
                            ]
                        );
                        ?>
                    </p>

                <?php endif; ?>

            </div>

        </fieldset>



        <!-- =================================================
             FAULT DETAILS
        ================================================== -->

        <fieldset class="fault-section">

            <legend>
                3. Describe the Fault
            </legend>


            <div class="form-field">

                <label for="fault_type">
                    Fault Type *
                </label>


                <select
                    id="fault_type"
                    name="fault_type"
                    required
                >

                    <option value="">
                        Select fault type
                    </option>


                    <?php
                    foreach (
                        $faultTypes
                        as $code =>
                            $label
                    ):
                    ?>

                        <option
                            value="<?php
                            echo authEscape(
                                $code
                            );
                            ?>"

                            <?php
                            echo
                                $values[
                                    'fault_type'
                                ] === $code
                                    ? 'selected'
                                    : '';
                            ?>
                        >
                            <?php
                            echo authEscape(
                                $label
                            );
                            ?>
                        </option>

                    <?php endforeach; ?>

                </select>


                <?php
                if (
                    isset(
                        $errors[
                            'fault_type'
                        ]
                    )
                ):
                ?>

                    <p class="field-error">
                        <?php
                        echo authEscape(
                            $errors[
                                'fault_type'
                            ]
                        );
                        ?>
                    </p>

                <?php endif; ?>

            </div>



            <div class="form-field">

                <label for="description">
                    Description *
                </label>


                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    maxlength="2000"
                    required
                    placeholder="Describe what is happening and any useful details..."
                ><?php
                echo authEscape(
                    $values[
                        'description'
                    ]
                );
                ?></textarea>


                <?php
                if (
                    isset(
                        $errors[
                            'description'
                        ]
                    )
                ):
                ?>

                    <p class="field-error">
                        <?php
                        echo authEscape(
                            $errors[
                                'description'
                            ]
                        );
                        ?>
                    </p>

                <?php endif; ?>

            </div>

        </fieldset>



        <!-- =================================================
             LOCATION / GPS
        ================================================== -->

        <fieldset class="fault-section">

            <legend>
                4. Fault Location on Map
            </legend>


            <p class="section-description">
                Optional but strongly recommended.
                Use your current location or select the
                fault location on the map.
            </p>


            <div class="field-grid">


                <div class="form-field">

                    <label for="latitude">
                        Latitude
                    </label>


                    <input
                        type="number"
                        id="latitude"
                        name="latitude"
                        step="0.0000001"
                        min="-90"
                        max="90"
                        value="<?php
                        echo authEscape(
                            $values[
                                'latitude'
                            ]
                        );
                        ?>"
                    >

                </div>



                <div class="form-field">

                    <label for="longitude">
                        Longitude
                    </label>


                    <input
                        type="number"
                        id="longitude"
                        name="longitude"
                        step="0.0000001"
                        min="-180"
                        max="180"
                        value="<?php
                        echo authEscape(
                            $values[
                                'longitude'
                            ]
                        );
                        ?>"
                    >

                </div>


            </div>



            <button
                type="button"
                id="get-location"
                class="secondary-public-button"
            >
                Use Current Location
            </button>


            <p
                id="location-status"
                class="live-status"
                aria-live="polite"
            ></p>


            <div
                id="fault-map"
                aria-label="Map for selecting the fault location"
            ></div>

        </fieldset>



        <!-- =================================================
             PHOTO
        ================================================== -->

        <fieldset class="fault-section">

            <legend>
                5. Fault Photo
            </legend>


            <p class="section-description">
                Optional. A photograph can help staff
                assess the problem before travelling to
                the site.
            </p>


            <div class="photo-source-actions">

                <button
                    type="button"
                    id="open-camera"
                    class="primary-public-button"
                >
                    Take Photo
                </button>


                <label
                    for="photo"
                    class="secondary-public-button photo-picker-label"
                >
                    Choose Photo
                </label>

            </div>


            <input
                type="file"
                id="photo"
                name="photo"
                class="hidden-photo-input"
                accept="image/jpeg,image/png,image/webp"
                capture="environment"
            >


            <p class="field-help">
                JPG, PNG or WebP. Maximum 5 MB.
            </p>


            <?php
            if (
                isset(
                    $errors[
                        'photo'
                    ]
                )
            ):
            ?>

                <p class="field-error">
                    <?php
                    echo authEscape(
                        $errors[
                            'photo'
                        ]
                    );
                    ?>
                </p>

            <?php endif; ?>



            <div
                id="camera-panel"
                class="camera-panel"
                hidden
            >

                <video
                    id="camera-stream"
                    autoplay
                    playsinline
                    muted
                ></video>


                <canvas
                    id="camera-canvas"
                    hidden
                ></canvas>


                <div class="camera-actions">

                    <button
                        type="button"
                        id="capture-photo"
                        class="primary-public-button"
                    >
                        Capture Photo
                    </button>


                    <button
                        type="button"
                        id="close-camera"
                        class="secondary-public-button"
                    >
                        Cancel
                    </button>

                </div>


                <p
                    id="camera-status"
                    class="live-status"
                    aria-live="polite"
                ></p>

            </div>



            <div
                id="photo-preview-container"
                class="fault-photo-preview"
                hidden
            >

                <img
                    id="photo-preview"
                    src=""
                    alt="Selected fault photograph"
                >


                <button
                    type="button"
                    id="remove-photo"
                    class="secondary-public-button"
                >
                    Remove Photo
                </button>

            </div>


            <p
                id="photo-status"
                class="live-status"
                aria-live="polite"
            ></p>

        </fieldset>



        <!-- =================================================
             REPORTER DETAILS
        ================================================== -->

        <fieldset class="fault-section">

            <legend>
                6. Your Contact Details
            </legend>


            <p class="section-description">
                Your name and phone number are required
                so that the responsible team can contact
                you for clarification or follow-up.
            </p>


            <div class="field-grid">


                <div class="form-field">

                    <label for="reporter_name">
                        Your Name *
                    </label>


                    <input
                        type="text"
                        id="reporter_name"
                        name="reporter_name"
                        maxlength="120"
                        value="<?php
                        echo authEscape(
                            $values[
                                'reporter_name'
                            ]
                        );
                        ?>"
                        autocomplete="name"
                        required
                    >


                    <?php
                    if (
                        isset(
                            $errors[
                                'reporter_name'
                            ]
                        )
                    ):
                    ?>

                        <p class="field-error">
                            <?php
                            echo authEscape(
                                $errors[
                                    'reporter_name'
                                ]
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                </div>



                <div class="form-field">

                    <label for="reporter_phone">
                        Phone Number *
                    </label>


                    <input
                        type="tel"
                        id="reporter_phone"
                        name="reporter_phone"
                        maxlength="20"
                        placeholder="0786226774"
                        value="<?php
                        echo authEscape(
                            $values[
                                'reporter_phone'
                            ]
                        );
                        ?>"
                        autocomplete="tel"
                        required
                    >


                    <?php
                    if (
                        isset(
                            $errors[
                                'reporter_phone'
                            ]
                        )
                    ):
                    ?>

                        <p class="field-error">
                            <?php
                            echo authEscape(
                                $errors[
                                    'reporter_phone'
                                ]
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                </div>


            </div>

        </fieldset>



        <div class="fault-submit-area">

            <p>
                After submission you will receive a
                unique reference for tracking the
                response and repair progress.
            </p>


            <button
                type="submit"
                class="submit-fault-button"
            >
                Submit Fault Report
            </button>

        </div>


    </form>


<?php endif; ?>


</main>


</body>

</html>