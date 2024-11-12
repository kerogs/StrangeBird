<?php

// only post method and need password isset with min 4 char and username
if (isset($_POST['password']) && isset($_POST['username']) && strlen($_POST['password']) >= 4) {

    // get username and password
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // check if username (nameid) exists in storage.json

    $storageData = "../../backend/storage.json";
    $storageData = json_decode(file_get_contents($storageData), true);

    $usernameExists = false;
    foreach ($storageData['nameid'] as $user) {
        if ($user['nameid'] === $username) {
            $usernameExists = true;
            // get uuid
            $uuid = $user['uuid'];
            break;
        }
    }

    if ($usernameExists) {

        // get account data
        $accountData = "../../backend/storage/accounts/" . $uuid . ".json";
        $accountData = json_decode(file_get_contents($accountData), true);

        // check if password is correct
        if (password_verify($password, $accountData['security']['password'])) {
            // set cookie
            setcookie('account', $uuid, time() + 60 * 60 * 24 * 180, '/');
            header("Location: /");
            exit;
        } else {
            header("Location: ../err?e=wrong password");
            exit;
        }
    } else {
        header("Location: ../err?e=nameid not found, be sure to write your nameid and not the username");
        exit;
    }
} else{
    header ("Location: ../err?e=wrong password or username");
}