
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Register Water Point
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
        href="assets/register-water-point.css"
    >


    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        defer
    ></script>


    <script
        src="assets/form.js"
        defer
    ></script>

</head>


<body>


<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('register-water-point.php'); ?>



<main class="container form-container">


<section class="card registration-card">


    <div class="page-heading">

        <p class="eyebrow">
            WATER POINT MANAGEMENT
        </p>

        <h2>
            Register Water Point
        </h2>

        <p class="page-introduction">
            Create an official water-point record,
            capture its location and optionally attach
            a site photograph.
        </p>

    </div>



    <?php
    if (
        is_array(
            $successData
        )
    ):
    ?>

        <div
            class="message preview-message"
            id="form-feedback"
            tabindex="-1"
            role="status"
        >

            <h3>
                Water point registered
            </h3>

            <p>
                <strong>
                    <?php
                    echo authEscape(
                        $successData[
                            'code'
                        ]
                    );
                    ?>
                </strong>
                has been created successfully.
            </p>

            <dl>

                <dt>
                    Water point
                </dt>

                <dd>
                    <?php
                    echo authEscape(
                        $successData[
                            'name'
                        ]
                    );
                    ?>
                </dd>


                <dt>
                    Village
                </dt>

                <dd>
                    <?php
                    echo authEscape(
                        $successData[
                            'village'
                        ]
                    );
                    ?>
                </dd>


                <dt>
                    Permanent code
                </dt>

                <dd>
                    <strong>
                        <?php
                        echo authEscape(
                            $successData[
                                'code'
                            ]
                        );
                        ?>
                    </strong>
                </dd>

            </dl>

        </div>

    <?php endif; ?>



    <?php if ($errors): ?>

        <div
            class="message error-message"
            id="form-feedback"
            tabindex="-1"
            role="alert"
        >

            <h3>
                Please check the form
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
        action="register-water-point.php"
        enctype="multipart/form-data"
        id="water-point-form"
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
             BASIC DETAILS
        ================================================== -->

        <fieldset class="form-section">

            <legend>
                Basic details
            </legend>


            <p class="section-description">
                The permanent water-point code is generated
                automatically after successful registration.
            </p>


            <div class="field-grid">


                <div class="form-field">

                    <label for="name">
                        Water point name *
                    </label>


                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?php
                        echo authEscape(
                            $values[
                                'name'
                            ]
                        );
                        ?>"
                        maxlength="120"
                        required
                    >


                    <?php
                    if (
                        isset(
                            $errors[
                                'name'
                            ]
                        )
                    ):
                    ?>

                        <p
                            class="field-error"
                            id="name-error"
                        >
                            <?php
                            echo authEscape(
                                $errors[
                                    'name'
                                ]
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                </div>



                <div class="form-field">

                    <label for="village">
                        Village *
                    </label>


                    <input
                        type="text"
                        id="village"
                        name="village"
                        value="<?php
                        echo authEscape(
                            $values[
                                'village'
                            ]
                        );
                        ?>"
                        maxlength="120"
                        required
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

                        <p
                            class="field-error"
                            id="village-error"
                        >
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

                <label for="type">
                    Water point type *
                </label>


                <select
                    id="type"
                    name="type"
                    required
                >

                    <option value="">
                        Select a type
                    </option>


                    <?php
                    foreach (
                        $types
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
                                    'type'
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
                            'type'
                        ]
                    )
                ):
                ?>

                    <p
                        class="field-error"
                        id="type-error"
                    >
                        <?php
                        echo authEscape(
                            $errors[
                                'type'
                            ]
                        );
                        ?>
                    </p>

                <?php endif; ?>

            </div>

        </fieldset>



        <!-- =================================================
             LOCATION
        ================================================== -->

        <fieldset class="form-section">

            <legend>
                Location
            </legend>


            <p class="section-description">
                Capture the current location, enter
                coordinates manually, click the map or
                drag the marker.
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
                        value="<?php
                        echo authEscape(
                            $values[
                                'latitude'
                            ]
                        );
                        ?>"
                        step="0.0000001"
                        min="-90"
                        max="90"
                        inputmode="decimal"
                        placeholder="-0.6071234"
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
                        value="<?php
                        echo authEscape(
                            $values[
                                'longitude'
                            ]
                        );
                        ?>"
                        step="0.0000001"
                        min="-180"
                        max="180"
                        inputmode="decimal"
                        placeholder="30.6543210"
                    >

                </div>

            </div>



            <div class="location-actions">

                <button
                    type="button"
                    id="get-location"
                    class="secondary-button"
                >
                    Use Current Location
                </button>


                <p
                    id="location-status"
                    class="location-status"
                    aria-live="polite"
                ></p>

            </div>



            <div class="map-wrapper">

                <div class="map-header">

                    <h3>
                        Water point location
                    </h3>

                    <p>
                        Click the map to select or adjust
                        the location.
                    </p>

                </div>


                <div
                    id="water-point-map"
                    aria-label="Map for selecting the water point location"
                ></div>

            </div>

        </fieldset>



        <!-- =================================================
             PHOTO
        ================================================== -->

        <fieldset class="form-section">

            <legend>
                Water point photo
            </legend>


            <p class="section-description">
                Optional. Take a new photograph using the
                device camera or choose an existing image.
            </p>



            <!-- PHOTO ACTIONS -->

            <div class="photo-source-actions">

                <button
                    type="button"
                    id="open-camera"
                    class="camera-button"
                >
                    Take Photo
                </button>


                <label
                    for="photo"
                    class="choose-photo-button"
                >
                    Choose Photo
                </label>

            </div>



            <!-- FILE INPUT -->

            <input
                type="file"
                id="photo"
                name="photo"
                class="photo-file-input"
                accept="image/jpeg,image/png,image/webp"
                capture="environment"
            >


            <p class="field-help">
                JPG, PNG or WebP. Maximum size: 5 MB.
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

                <p
                    class="field-error"
                    id="photo-error"
                >
                    <?php
                    echo authEscape(
                        $errors[
                            'photo'
                        ]
                    );
                    ?>
                </p>

            <?php endif; ?>



            <!-- CAMERA -->

            <div
                id="camera-panel"
                class="camera-panel"
                hidden
            >

                <div class="camera-heading">

                    <div>

                        <h3>
                            Camera
                        </h3>

                        <p>
                            Position the water point clearly
                            inside the frame.
                        </p>

                    </div>

                </div>


                <video
                    id="camera-stream"
                    class="camera-stream"
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
                        class="primary-photo-button"
                    >
                        Capture Photo
                    </button>


                    <button
                        type="button"
                        id="close-camera"
                        class="secondary-button"
                    >
                        Cancel Camera
                    </button>

                </div>


                <p
                    id="camera-status"
                    class="photo-status"
                    aria-live="polite"
                ></p>

            </div>



            <!-- PREVIEW -->

            <div
                id="photo-preview-container"
                class="photo-preview-container"
                hidden
            >

                <p class="preview-label">
                    Selected photo
                </p>


                <img
                    id="photo-preview"
                    src=""
                    alt="Selected water point photo preview"
                >


                <div class="photo-preview-actions">

                    <button
                        type="button"
                        id="remove-photo"
                        class="secondary-button"
                    >
                        Remove Photo
                    </button>

                </div>

            </div>


            <p
                id="photo-status"
                class="photo-status"
                aria-live="polite"
            ></p>

        </fieldset>



        <!-- =================================================
             REGISTRATION
        ================================================== -->

        <div class="form-section">

            <h3>
                Registration
            </h3>


            <p class="section-description">

                Registered by

                <strong>
                    <?php
                    echo authEscape(
                        $currentUser[
                            'full_name'
                        ]
                    );
                    ?>
                </strong>.

                Initial status:

                <strong>
                    Functional
                </strong>.

            </p>

        </div>



        <div class="form-actions">

            <button
                type="submit"
                class="primary-button"
            >
                Register Water Point
            </button>

        </div>


    </form>


</section>


</main>


</body>

</html>