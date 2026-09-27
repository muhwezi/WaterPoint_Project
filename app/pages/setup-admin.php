
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>
        Initial Administrator Setup
        | Water Infrastructure Tracker
    </title>


    <link
        rel="stylesheet"
        href="assets/style.css"
    >


    <link
        rel="stylesheet"
        href="assets/setup-admin.css"
    >

</head>


<body>

<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('setup-admin.php'); ?>


<main class="setup-page">


    <section class="setup-card">


        <div class="setup-brand">

            <p class="setup-eyebrow">
                SYSTEM SETUP
            </p>


            <h1>
                Water Infrastructure Tracker
            </h1>


            <p>
                Create the first administrator account.
            </p>

        </div>



        <?php if ($successMessage !== ''): ?>

            <div
                class="setup-message success-message"
                role="status"
            >

                <strong>
                    Setup completed
                </strong>

                <p>
                    <?php
                    echo escape(
                        $successMessage
                    );
                    ?>
                </p>

            </div>

        <?php endif; ?>



        <?php if ($existingAdmin): ?>

            <div
                class="setup-complete"
            >

                <div class="setup-complete-icon">
                    ✓
                </div>


                <h2>
                    Administrator already configured
                </h2>


                <p>
                    The initial administrator setup has been
                    locked because an administrator account
                    already exists.
                </p>


                <dl class="admin-summary">

                    <div>

                        <dt>
                            Administrator
                        </dt>

                        <dd>
                            <?php
                            echo escape(
                                $existingAdmin[
                                    'full_name'
                                ]
                            );
                            ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Username
                        </dt>

                        <dd>
                            <?php
                            echo escape(
                                $existingAdmin[
                                    'username'
                                ]
                            );
                            ?>
                        </dd>

                    </div>

                </dl>


                <p class="setup-next-step">
                    We will use this account on the staff
                    login page in the next step.
                </p>

            </div>


        <?php else: ?>


            <?php if ($errors): ?>

                <div
                    class="setup-message error-message"
                    role="alert"
                >

                    <strong>
                        Please check the form
                    </strong>


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
                                    echo escape(
                                        $message
                                    );
                                    ?>

                                <?php else: ?>

                                    <a
                                        href="#<?php
                                        echo escape(
                                            $field
                                        );
                                        ?>"
                                    >
                                        <?php
                                        echo escape(
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
                action="setup-admin.php"
                class="setup-form"
                autocomplete="off"
            >


                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo escape(
                        $_SESSION[
                            'csrf_token'
                        ]
                    );
                    ?>"
                >



                <!-- FULL NAME -->

                <div class="setup-field">

                    <label for="full_name">
                        Full name
                    </label>


                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        maxlength="120"
                        value="<?php
                        echo escape(
                            $values[
                                'full_name'
                            ]
                        );
                        ?>"
                        required
                    >

                </div>



                <!-- USERNAME -->

                <div class="setup-field">

                    <label for="username">
                        Username
                    </label>


                    <input
                        type="text"
                        id="username"
                        name="username"
                        maxlength="40"
                        value="<?php
                        echo escape(
                            $values[
                                'username'
                            ]
                        );
                        ?>"
                        required
                    >


                    <p class="field-hint">
                        Use letters, numbers, dots,
                        hyphens or underscores.
                    </p>

                </div>



                <!-- EMAIL -->

                <div class="setup-field">

                    <label for="email">
                        Email address
                    </label>


                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="150"
                        value="<?php
                        echo escape(
                            $values[
                                'email'
                            ]
                        );
                        ?>"
                        required
                    >

                </div>



                <!-- PHONE -->

                <div class="setup-field">

                    <label for="phone">
                        Phone number
                    </label>


                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        maxlength="20"
                        placeholder="0786226774"
                        value="<?php
                        echo escape(
                            $values[
                                'phone'
                            ]
                        );
                        ?>"
                        required
                    >

                </div>



                <!-- PASSWORD -->

                <div class="setup-field">

                    <label for="password">
                        Password
                    </label>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        minlength="10"
                        required
                    >


                    <p class="field-hint">
                        Minimum 10 characters including
                        uppercase, lowercase, number and
                        special character.
                    </p>

                </div>



                <!-- CONFIRM PASSWORD -->

                <div class="setup-field">

                    <label
                        for="confirm_password"
                    >
                        Confirm password
                    </label>


                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        minlength="10"
                        required
                    >

                </div>



                <button
                    type="submit"
                    class="setup-submit"
                >
                    Create Administrator
                </button>


            </form>


        <?php endif; ?>


    </section>


</main>


</body>

</html>