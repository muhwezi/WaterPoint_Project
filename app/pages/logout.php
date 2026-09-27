
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>
        Sign Out
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

<?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('logout.php'); ?>


<main class="login-page">


    <section class="login-card logout-card">


        <p class="login-eyebrow">
            STAFF PORTAL
        </p>


        <h1>
            Sign out?
        </h1>


        <p>
            You are signed in as
            <strong>
                <?php
                echo authEscape(
                    $user[
                        'full_name'
                    ]
                );
                ?>
            </strong>.
        </p>


        <form
            method="post"
            action="logout.php"
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


            <button
                type="submit"
                class="login-submit"
            >
                Sign Out
            </button>

        </form>


        <p class="cancel-logout">

            <a href="dashboard.php">
                Cancel and return to dashboard
            </a>

        </p>


    </section>


</main>


</body>

</html>