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
            <h1>Server settings</h1>
            <p>Version <?= $kpf_config["other"]["website_version"] ?></p>
            <hr>
            <div class="container">
                <div class="left">
                    <ul id="listselection">
                        <!-- <li id="all" class="active">All</li> -->
                        <li class="active" id="account">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                <path fill="currentColor" fill-rule="evenodd" d="M13.204 2.244C11.347 1.826 10 3.422 10 5v14c0 1.578 1.347 3.174 3.204 2.756C17.666 20.752 21 16.766 21 12s-3.334-8.752-7.796-9.756m.089 6.049a1 1 0 0 1 1.414 0l3 3a1 1 0 0 1 0 1.414l-3 3a1 1 0 0 1-1.414-1.414L14.586 13H4a1 1 0 1 1 0-2h10.586l-1.293-1.293a1 1 0 0 1 0-1.414" clip-rule="evenodd" />
                            </svg>
                            Connection management
                        </li>
                    </ul>
                </div>
                <div class="right">
                    <!-- register Account -->
                    <div data-selection="account">
                        <div>
                            <h3>Register Account</h3>
                            <p>Allows you to authorize people to register on the NunaLab.</p>
                        </div>
                        <div>
                            <form action="action/settings_admin.php" method="post">
                                <select name="registerAccount" id="">
                                    <option <?= ($serverJSON['registerAccount'] == true ? "selected" : "") ?> value="y">true</option>
                                    <option <?= ($serverJSON['registerAccount'] == false ? "selected" : "") ?> value="n">false</option>
                                </select>
                                <button type="submit">Change</button>
                            </form>
                        </div>
                    </div>
                    <!-- Force login -->
                    <div data-selection="account">
                        <div>
                            <h3>Force Login</h3>
                            <p>This option forces all users not logged in to log in to the site before being able to use it.</p>
                        </div>
                        <div>
                            <form action="action/settings_admin.php" method="post">
                                <select name="forceLogin" id="">
                                    <option <?= ($serverJSON['forceLogin'] == true ? "selected" : "") ?> value="true">true</option>
                                    <option <?= ($serverJSON['forceLogin'] == false ? "selected" : "") ?> value="false">false</option>
                                </select>
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