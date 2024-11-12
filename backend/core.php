<?php

    session_start();

    // ? Import functions
    require_once __DIR__."/func/functions.php";

    // check for "storage.json" file
    
    if(!file_exists(__DIR__."/storage.json")) file_put_contents(__DIR__."/storage.json", "{}");

    // check if user is logged (cookie name : account)
    if($_COOKIE['account']) {
        // check if account file exists
        if(file_exists(__DIR__."/storage/accounts/" . $_COOKIE['account'] . ".json")) {
            // get account
            $jsonAccount = json_decode(file_get_contents(__DIR__."/storage/accounts/" . $_COOKIE['account'] . ".json"), true);
        } else{
            // delete cookie
            setcookie("account", "", time() - 3600);
        }
    }