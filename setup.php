<?php


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrangeBird | Setup</title>

    <link rel="stylesheet" href="/assets/styles/css/setup.css">

    <link rel="shortcut icon" href="/assets/img/favicon/favicon.ico" type="image/x-icon">

</head>

<body class="dark">

    <header>
        <p>StrangeBird - Setup page</p>
    </header>

    <main>
        <center>
            <img src="/assets/img/logo_name_primary.png" alt="" height="100px">
        </center>

        <?php if (empty($_GET['step']) || $_GET['step'] == 1) { ?>
            <section>
                <p class="note">
                    Welcome to the StrangeBird setup interface. The following information will guide you through the steps to properly install StrangeBird.
                    <br><br>
                    Do not worry. The steps are very simple! And most of them are automatic.
                </p>
            </section>

            <br>
            <hr><br>

            <section>
                <h2>Step 1 - <i>RTFM</i></h2>
                <br>
                <p>
                    - First of all, read the <a href="https://github.com/kerogs/StrangeBird#readme" target="_blank">readme</a> to understand how StrangeBird works.
                    <br><br>
                    - make sure to read the <a href="https://github.com/kerogs/StrangeBird#license" target="_blank">license</a> as well.
                </p>
            </section>
        <?php } ?>


        <?php if ($_GET['step'] == 2) { ?>
            <section>
                <h2>Step 2 - <i>Install</i></h2>
                <br>
                <p>
                    StrangeBird operates entirely offline. Therefore, you will need to install the tools necessary for StrangeBird to function properly.
                    <br><br>
                    To do so, simply enter this command at the root of the StrangeBird folder:
                </p>
                <div class="cli">
                    npm i
                </div>
            </section>
        <?php } ?>






        <?php if ($_GET['step'] == 3) { ?>

            <section>
                <h2>Step 3 - <i>Database file</i></h2>
                <br>
                <p>Now, click on the button below to create the database file automaticly.</p>
            </section>

            <section>
                <?php if (file_exists(__DIR__ . '/backend/database.template.sqlite')) { ?>
                    <div class="dbStatus ok">
                        <p>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M480-520q150 0 255-47t105-113q0-66-105-113t-255-47q-150 0-255 47T120-680q0 66 105 113t255 47Zm0 100q41 0 102.5-8.5T701-456q57-19 98-49.5t41-74.5v100q0 44-41 74.5T701-356q-57 19-118.5 27.5T480-320q-41 0-102.5-8.5T259-356q-57-19-98-49.5T120-480v-100q0 44 41 74.5t98 49.5q57 19 118.5 27.5T480-420Zm0 200q41 0 102.5-8.5T701-256q57-19 98-49.5t41-74.5v100q0 44-41 74.5T701-156q-57 19-118.5 27.5T480-120q-41 0-102.5-8.5T259-156q-57-19-98-49.5T120-280v-100q0 44 41 74.5t98 49.5q57 19 118.5 27.5T480-220Z" />
                            </svg>
                            <span>Template database file found</span>
                        </p>
                    </div>
                <?php } else { ?>
                    <div class="dbStatus">
                        <p>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M446-446Zm106-95Zm-106 95Zm106-95Zm-106 95Zm106-95ZM791-56 56-791l56-57 736 736-57 56Zm-311-64q-151 0-255.5-46.5T120-280v-400q0-26 17.5-49.5T187-773l252 252q-72-3-133-18t-106-40v120q51 29 123 44t157 15q20 0 39-.5t38-2.5l70 70q-34 7-71 10t-76 3q-85 0-157-15t-123-44v99q9 29 97.5 54.5T480-200q64 0 128.5-13T715-245l58 58q-49 31-125.5 49T480-120Zm350-123-70-70v-66q-11 6-22 11t-23 10l-61-61q30-8 56.5-17.5T760-459v-120q-41 23-94 37t-116 19l-76-76q44 0 92-7t89.5-18.5q41.5-11.5 70-26T760-679q-11-29-100.5-55T480-760q-37 0-75.5 5T331-742l-66-66q45-15 100-23.5t115-8.5q149 0 254.5 47T840-680v400q0 10-2.5 19t-7.5 18Z" />
                            </svg>
                            <span>No template for the database found</span>
                        </p>
                    </div>
                    <p class="note__ko">
                        Oops, the template database file was not found. It's not supposed to happen... Try to get it <a href="https://github.com/kerogs/StrangeBird/blob/main/backend/database.template.sqlite" target="_blank">from the repository</a>
                    </p>
                <?php } ?>
            </section>

            <section>
                <?php if (file_exists(__DIR__ . '/backend/database.sqlite')) { ?>
                    <div class="dbStatus ok">
                        <p>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M480-520q150 0 255-47t105-113q0-66-105-113t-255-47q-150 0-255 47T120-680q0 66 105 113t255 47Zm0 100q41 0 102.5-8.5T701-456q57-19 98-49.5t41-74.5v100q0 44-41 74.5T701-356q-57 19-118.5 27.5T480-320q-41 0-102.5-8.5T259-356q-57-19-98-49.5T120-480v-100q0 44 41 74.5t98 49.5q57 19 118.5 27.5T480-420Zm0 200q41 0 102.5-8.5T701-256q57-19 98-49.5t41-74.5v100q0 44-41 74.5T701-156q-57 19-118.5 27.5T480-120q-41 0-102.5-8.5T259-156q-57-19-98-49.5T120-280v-100q0 44 41 74.5t98 49.5q57 19 118.5 27.5T480-220Z" />
                            </svg>
                            <span>Yeah! database found (～￣▽￣)～</span>
                        </p>
                    </div>
                <?php } else { ?>
                    <div class="dbStatus">
                        <p>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M446-446Zm106-95Zm-106 95Zm106-95Zm-106 95Zm106-95ZM791-56 56-791l56-57 736 736-57 56Zm-311-64q-151 0-255.5-46.5T120-280v-400q0-26 17.5-49.5T187-773l252 252q-72-3-133-18t-106-40v120q51 29 123 44t157 15q20 0 39-.5t38-2.5l70 70q-34 7-71 10t-76 3q-85 0-157-15t-123-44v99q9 29 97.5 54.5T480-200q64 0 128.5-13T715-245l58 58q-49 31-125.5 49T480-120Zm350-123-70-70v-66q-11 6-22 11t-23 10l-61-61q30-8 56.5-17.5T760-459v-120q-41 23-94 37t-116 19l-76-76q44 0 92-7t89.5-18.5q41.5-11.5 70-26T760-679q-11-29-100.5-55T480-760q-37 0-75.5 5T331-742l-66-66q45-15 100-23.5t115-8.5q149 0 254.5 47T840-680v400q0 10-2.5 19t-7.5 18Z" />
                            </svg>
                            <span>No database file found</span>
                        </p>
                    </div>
                <?php } ?>
            </section>

            <?php if (!file_exists(__DIR__ . '/backend/database.sqlite')) { ?>
                <a href="?step=<?= $_GET['step'] ?>&action=create_database">
                    <button class="createDatabase">Create the database now</button>
                </a>
            <?php } ?>

            <?php

            if ($_GET['action'] === 'create_database' && !file_exists(__DIR__ . '/backend/database.sqlite')) {
                // check if the template file exists ("more is the security, best is the experience" - yoda)
                $templateFile = __DIR__ . '/backend/database.template.sqlite';
                $databaseFile = __DIR__ . '/backend/database.sqlite';

                if (file_exists($templateFile)) {
                    if (copy($templateFile, $databaseFile)) {
                        echo '<p class="note">Wait... We are creating the database file. We will reload the page when it\'s done.</p>';
                        echo '<script>setTimeout(function(){ window.location.href = "?step=3"; }, 2000);</script>';
                    } else {
                        echo '<p class="note__ko">Error: Could not create database file. Check file permissions.</p>';
                    }
                } else {
                    echo '<p class="note__ko">Error: Template database file not found.</p>';
                }
            }

            ?>

        <?php } ?>

        <?php if ($_GET['step'] == 4) { ?>
            <section>
                <h3>Step 4 - <i>Done</i></h3>
                <br>
                <p>
                    Now, the last step is to create an account. Using an account is not mandatory to use the site. However, you must have an account to add your favourite scans.
                    <br><br>
                    Please note that the first account created on the site will automatically be set as the site administrator.
                </p>
            </section>
        <?php } ?>
































        <div class="btnArea">
            <?php

            // ? to know when finished and add a button to create an account
            $maxStep = 4;

            $nexStep = null;
            $prevStep = null;

            if (empty($_GET['step'])) {
                $nexStep = 2;
            } else {
                $nexStep = $_GET['step'] + 1;
            }

            if ($nexStep > 1) {
                $prevStep = $_GET['step'] - 1;
            } else {
                $prevStep = null;
            }

            ?>
            <?php if ($prevStep && $prevStep != -1) { ?>
                <a href="?step=<?= $prevStep ?>">
                    <button class="left">
                        Prev Step
                    </button>
                </a>
            <?php } ?>
            <?php if ($_GET['step'] < $maxStep) { ?>
                <a href="?step=<?= $nexStep ?>">
                    <button>
                        Next Step
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M760-360q-51 0-85.5-34.5T640-480q0-51 34.5-85.5T760-600q51 0 85.5 34.5T880-480q0 51-34.5 85.5T760-360Zm-400 80-56-57 103-103H80v-80h327L304-624l56-56 200 200-200 200Z" />
                        </svg>
                    </button>
                </a>
            <?php } else { ?>
                <a href="/register">
                    <button>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M720-400v-120H600v-80h120v-120h80v120h120v80H800v120h-80Zm-360-80q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Z" />
                        </svg>

                        Create an account
                    </button>
                </a>
            <?php } ?>

        </div>



    </main>

</body>

</html>