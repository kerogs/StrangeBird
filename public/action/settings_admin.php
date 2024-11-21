<?php

require_once('../../backend/core.php');

if(!$jsonAccount['permissions']['owner'] || !$jsonAccount['permissions']['admin']) {
    header("Location: /err?e=You don't have permission to access this page.");
    exit();
}

if(isset($_POST['registerAccount'])){
    // add true|false to configuration file

    if($_POST['registerAccount'] == "y") $serverJSON['registerAccount'] = true;
    else $serverJSON['registerAccount'] = false;
    file_put_contents('../../backend/configuration.json', json_encode($serverJSON, JSON_PRETTY_PRINT));
    header("Location: /settings");
    exit();
}

if(isset($_POST['forceLogin'])){
    // add true|false to configuration file

    if($_POST['forceLogin'] == "true") $serverJSON['forceLogin'] = true;
    else $serverJSON['forceLogin'] = false;
    file_put_contents('../../backend/configuration.json', json_encode($serverJSON, JSON_PRETTY_PRINT));    
    header("Location: /settings");
    exit();
}



// ! if nothing
header("Location: /err?e=Error, nothing was done.");
exit();