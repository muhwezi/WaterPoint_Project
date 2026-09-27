
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>
        Staff Login
        | Water Infrastructure Tracker
    </title>


    <link
        rel="stylesheet"
        href="assets/style.css"
    >


    <link
        rel="stylesheet"
        href="assets/login.css"
    >

</head>


<body>

<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('login.php'); ?>


<main class="login-page">


    <section class="login-card">


        <!-- =================================================
             BRAND
        ================================================== -->

        <div class="login-brand">

            <a
                href="index.php"
                class="back-home"
            >
                ← Public site
            </a>


            <p class="login-eyebrow">
                AUTHORIZED STAFF
            </p>


            <h1>
                Staff Login
            </h1>


            <p>
                Sign in to manage water points,
                faults and repair activities.
            </p>

        </div>



        <!-- =================================================
             ERRORS
        ================================================== -->

        <?php if ($errors): ?>

            <div
                class="login-message error-message"
                id="login-feedback"
                tabindex="-1"
                role="alert"
            >

                <strong>
                    Sign in unsuccessful
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



        <!-- =================================================
             FORM
        ================================================== -->

        <form
            method="post"
            action="login.php"
            class="login-form"
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



            <div class="login-field">

                <label for="username">
                    Username
                </label>


                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?php
                    echo authEscape(
                        $username
                    );
                    ?>"
                    maxlength="60"
                    autocomplete="username"
                    required
                    autofocus
                >

            </div>



            <div class="login-field">

                <label for="password">
                    Password
                </label>


                <div class="password-wrapper">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >


                    <button
                        type="button"
                        id="toggle-password"
                        class="password-toggle"
                        aria-label="Show password"
                    >
                        Show
                    </button>

                </div>

            </div>



            <button
                type="submit"
                class="login-submit"
            >
                Sign In
            </button>


        </form>



        <div class="login-footer">

            <p>
                Access is restricted to authorized
                system staff.
            </p>

        </div>


    </section>


</main>


<script src="assets/login.js"></script>


<?php if ($errors): ?>

<script>
    document
        .getElementById("login-feedback")
        ?.focus();
</script>

<?php endif; ?>


</body>

</html>