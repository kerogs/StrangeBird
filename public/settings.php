<?php

require_once('../config.php');

?>

<!DOCTYPE html>
<html lang="<?= $kpf_config["seo"]["lang_short"] ?>">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once '../inc/head.php' ?>
    <title><?= $kpf_config["seo"]["title_short"] ?></title>
    <link rel="stylesheet" href="src/css/style.css">

    <!-- src -->
    <link rel="stylesheet" href="./node_modules/boxicons/css/boxicons.min.css">
</head>

<body>
    <?php require_once '../inc/header.php' ?>

    <div class="profile_settings">
        <div class="profile-change">
            <?php

            if (isset($_GET['v'])) {
                echo $_GET['v'] == "true" ? '<div class="notification success">    <h3>Change made</h3>    <p>The change was correctly made for "<b><em>' . $_GET['t'] . '</em></b>".</p></div>' : '<div class="notification alert">    <h3>Action fail</h3>    <p>An error occured while sending for "<b><em>' . $_GET['t'] . '</em></b>".</p></div>';
            }

            ?>
            <h1>User settings</h1>
            <hr>
            <div class="container">
                <div class="left">
                    <ul id="listselection">
                        <li id="all" class="active">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M3 21v-5h2v3h3v2zm13 0v-2h3v-3h2v5zm-4-2q-2.9 0-4.95-2.05T5 12t2.05-4.95T12 5t4.95 2.05T19 12t-2.05 4.95T12 19M3 8V3h5v2H5v3zm16 0V5h-3V3h5v5z" />
                            </svg>
                            All
                        </li>
                        <li id="account">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                <circle cx="12" cy="6" r="4" fill="currentColor" />
                                <path fill="currentColor" d="M20 17.5c0 2.485 0 4.5-8 4.5s-8-2.015-8-4.5S7.582 13 12 13s8 2.015 8 4.5" />
                            </svg>
                            Account
                        </li>
                        <li id="pictures">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16">
                                <path fill="currentColor" fill-rule="evenodd" d="M2.5 2a.5.5 0 0 0-.5.5v11a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5v-11a.5.5 0 0 0-.5-.5zm5.854 9.854L13 7.207V13H3v-1.793l2-2l2.646 2.647a.5.5 0 0 0 .708 0M5 3.99a1 1 0 1 0 0 2a1 1 0 0 0 0-2" clip-rule="evenodd" />
                            </svg>
                            Pictures
                        </li>
                        <li id="information">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                <g fill="none">
                                    <path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z" />
                                    <path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2m-.01 8H11a1 1 0 0 0-.117 1.993L11 12v4.99c0 .52.394.95.9 1.004l.11.006h.49a1 1 0 0 0 .596-1.803L13 16.134V11.01c0-.52-.394-.95-.9-1.004zM12 7a1 1 0 1 0 0 2a1 1 0 0 0 0-2" />
                                </g>
                            </svg>
                            Information
                        </li>
                        <li id="security">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 26 26">
                                <path fill="currentColor" d="M8 .188a7.813 7.813 0 1 0 0 15.625A7.813 7.813 0 0 0 8 .187zM5.5 2.905A2.59 2.59 0 0 1 8.094 5.5A2.587 2.587 0 0 1 5.5 8.094A2.587 2.587 0 0 1 2.906 5.5A2.59 2.59 0 0 1 5.5 2.906zm11.094 8.719a9.2 9.2 0 0 1-1.032 1.813l7.813 7.812a.89.89 0 0 1 0 1.25a.89.89 0 0 1-1.25 0l-7.719-7.75A9.4 9.4 0 0 1 11 16.813v1.375c0 .44.371.812.813.812H14v2.188c0 .44.371.812.813.812H17v2.188c0 .44.372.812.813.812h2.218l.563.563c.342.341 3.768.512 4.625-.344c.857-.858.684-4.284.343-4.625z" />
                            </svg>
                            Security
                        </li>
                    </ul>
                </div>
                <div class="right">
                    <!-- account -->
                    <div data-selection="account">
                        <div>
                            <h3>Username</h3>
                            <p>Here you can enter your username, which will be displayed.</p>
                        </div>
                        <div>
                            <form action="action/settings.php" method="post">
                                <input type="text" name="username" pattern="^[a-zA-Z0-9.]+$" value="<?= $jsonAccount['attributes']['username'] ?>" required id="">
                                <button type="submit">Change</button>
                            </form>
                        </div>
                    </div>
                    <div data-selection="account">
                        <div>
                            <h3>NameID</h3>
                            <p>Your nameID is your unique username. It will be displayed by example to uniquely identify you even if you have a nickname similar to someone else's. It's used, for example, to log in, to be written to display your profile, etc.</p>
                            <p><b><em>Can only contain lowercase letters, dots and numbers.</em></b></p>
                            <br>
                            <p class="note">
                                Once changed, you should use your new nameid to log in!
                            </p>
                        </div>
                        <div>
                            <form action="action/settings.php" method="post">
                                <input type="text" name="nameid" pattern="^[a-z0-9.]+$" value="<?= $jsonAccount['nameid'] ?>" required id="">
                                <button type="submit">Change</button>
                            </form>
                        </div>
                    </div>
                    <div data-selection="account">
                        <div>
                            <h3>Biography</h3>
                            <p>Write down some information about yourself that will be displayed on your profile.</p>
                            <p><b><em>BBCode supported </em></b></p>
                        </div>
                        <div>
                            <form action="action/settings.php" method="post">
                                <textarea name="biography" required id=""><?= $jsonAccount['attributes']['bio'] ?></textarea>
                                <button type="submit">Change</button>
                            </form>
                        </div>
                    </div>
                    <!-- pictures -->
                    <div data-selection="pictures">
                        <div>
                            <h3>Profile picture</h3>
                            <p>Upload a profile picture. <br> <b><em>JPG/JPEG/PNG/GIF supported</em></b></p>
                        </div>
                        <div>
                            <div class="pfp"><img src="<?= $jsonAccount['attributes']['pfp_encode'] . $jsonAccount['attributes']['pfp'] ?>" alt=""></div>
                            <form action="action/settings.php" method="post" enctype="multipart/form-data">
                                <input type="file" name="profilePicture" required id="">
                                <button type="submit">Change</button>
                            </form>
                        </div>
                    </div>
                    <div data-selection="pictures">
                        <div>
                            <h3>Banner picture</h3>
                            <p>Upload a banner picture. <br> <b><em>JPG/JPEG/PNG/GIF supported</em></b></p>
                        </div>
                        <div>
                            <?php if($jsonAccount['attributes']['banner'] !== false) : ?>
                                <div class="pfp"><img src="<?= $jsonAccount['attributes']['banner_encode'] . $jsonAccount['attributes']['banner'] ?>" alt=""></div>
                            <?php endif; ?>
                            <form action="action/settings.php" method="post" enctype="multipart/form-data">
                                <input type="file" name="bannerPicture" required id="">
                                <button type="submit">Change</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const listItems = document.querySelectorAll("#listselection li");

                listItems.forEach(function(item) {
                    item.addEventListener("click", function() {
                        // Remove .active class from all li elements
                        listItems.forEach(function(li) {
                            li.classList.remove("active");
                        });

                        // Add .active class to the clicked li element
                        item.classList.add("active");

                        // Get the data-selection attribute value of the clicked li element
                        const selection = item.getAttribute("id");

                        // Check if #all is clicked
                        if (selection === "all") {
                            // Show all right divs
                            const rightDivs = document.querySelectorAll(".right > div");
                            rightDivs.forEach(function(div) {
                                div.style.display = "flex";
                            });
                        } else {
                            // Hide all right divs
                            const rightDivs = document.querySelectorAll(".right > div");
                            rightDivs.forEach(function(div) {
                                div.style.display = "none";
                            });

                            // Show right divs with matching data-selection attribute
                            const selectedDivs = document.querySelectorAll(`.right > div[data-selection="${selection}"]`);
                            selectedDivs.forEach(function(div) {
                                div.style.display = "flex";
                            });
                        }
                    });
                });
            });
        </script>




    </div>

    <?php require_once '../inc/script.php' ?>
</body>

</html>