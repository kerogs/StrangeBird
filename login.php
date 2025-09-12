<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrangeBird Login system</title>

    <link rel="stylesheet" href="/assets/styles/css/login.css">

    <?php require_once __DIR__ . '/includes/head.php' ?>
</head>

<body class="dark">
    <?php if ($_GET['l'] === "login") { ?>

        <div class="login-page">
            <div class="logo">
                <img src="/assets/img/logo_name_primary.png" alt="StrangeBird Logo">
            </div>
            <div class="login-box">

                <h2>Sign in to your account</h2>

                <form action="/actions/login_form.php" method="POST">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username">

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">

                    <!-- <div class="options">
                        <label><input type="checkbox">Remember me</label>
                        <a href="#" class="forgot">Forgot Password?</a>
                    </div> -->

                    <button type="submit" class="btn-signin">Sign In</button>

                    <div class="register">
                        New user? <a href="/register">Register</a>
                    </div>
                </form>
            </div>
        </div>

    <?php } else { ?>
        <div class="login-page">
            <div class="logo">
                <img src="/assets/img/logo_name_primary.png" alt="StrangeBird Logo">
            </div>
            <div class="login-box">

                <h2>Register</h2>

                <form method="POST" action="/actions/register_form.php">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username">

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">

                    <!-- <div class="options">
                        <label><input type="checkbox">Remember me</label>
                        <a href="#" class="forgot">Forgot Password?</a>
                    </div> -->

                    <button type="submit" class="btn-signin">Sign In</button>

                    <div class="register">
                        Already have an account? <a href="/login">Login</a>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>

</body>

</html>