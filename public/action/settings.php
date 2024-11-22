<?php





/**
 * Generates a unique username by appending a suffix to the username if it already exists in the $existingUsernames array.
 * @param string $username The username to generate a unique username for.
 * @param array $existingUsernames An array of existing usernames.
 * @return string The unique username.
 */
function generateUniqueUsername($username, $existingUsernames) {
    $newUsername = $username;
    $suffix = 1;
    
    while (in_array($newUsername, $existingUsernames)) {
        $newUsername = $username . $suffix;
        $suffix++;
    }

    return $newUsername;
}
















require_once('../../backend/core.php');

if (!isset($_COOKIE['account'])) {
    header("Location: /login/login");
    exit();
}

var_dump($_POST);

if (isset($_POST["username"])) {

    $username = htmlspecialchars($_POST["username"]);

    $jsonChange = $jsonAccount;

    $jsonChange["attributes"]["username"] = $username;
    
    file_put_contents('../../backend/storage/accounts/' . $_COOKIE['account'] . '.json', json_encode($jsonChange, JSON_PRETTY_PRINT));

    header("Location: /settings");
    exit();
}

if (isset($_POST["nameid"])) {
    $nameid = htmlspecialchars($_POST["nameid"]);

    // check if nameid available
    $storageData = json_decode(file_get_contents('../../backend/storage.json'), true);
    if (!isset($storageData['nameid']) || !is_array($storageData['nameid'])) {
        $storageData['nameid'] = [];
    }

    $nameID = generateUniqueUsername($username, array_column($storageData['nameid'], 'nameid'));

    $jsonChange = $jsonAccount;

    $jsonChange["nameid"] = $nameid;

    // replace old nameid with new nameid

    foreach ($storageData['nameid'] as $key => $value) {
        if ($value['nameid'] == $nameid) {
            $storageData['nameid'][$key]['nameid'] = $nameID;
        }
    }
    file_put_contents('../../backend/storage.json', json_encode($storageData, JSON_PRETTY_PRINT));
    file_put_contents('../../backend/storage/accounts/' . $_COOKIE['account'] . '.json', json_encode($jsonChange, JSON_PRETTY_PRINT));

    header("Location: /settings");
    exit();
}