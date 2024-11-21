<?php

session_start();

// ? Import functions
require_once __DIR__ . "/func/functions.php";


// check for configuration file

$serverJSON = json_decode(file_get_contents(__DIR__ . "/configuration.json"), true);
// var_dump($serverJSON);

// if page is login or register get with base url

if (basename($_SERVER['PHP_SELF']) !== "login.php" && basename($_SERVER['PHP_SELF']) !== "register" && !$_COOKIE['account']) {
    if (basename($_SERVER['PHP_SELF']) !== "err.php"){
        if ($serverJSON['forceLogin'] == true && empty($_COOKIE['account'])) {
            header("Location: login/login");
            exit();
        }
    }
}

// check for "storage.json" file

if (!file_exists(__DIR__ . "/storage.json")) file_put_contents(__DIR__ . "/storage.json", "{}");

// check if user is logged (cookie name : account)
if ($_COOKIE['account']) {
    // check if account file exists
    if (file_exists(__DIR__ . "/storage/accounts/" . $_COOKIE['account'] . ".json")) {
        // get account
        $jsonAccount = json_decode(file_get_contents(__DIR__ . "/storage/accounts/" . $_COOKIE['account'] . ".json"), true);
    } else {
        // delete cookie
        setcookie("account", "", time() - 3600);
        header("Location: /");
        exit();
    }
}