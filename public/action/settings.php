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

// change username
if (isset($_POST["username"])) {

    $username = htmlspecialchars($_POST["username"]);

    $jsonChange = $jsonAccount;

    $jsonChange["attributes"]["username"] = $username;
    
    file_put_contents('../../backend/storage/accounts/' . $_COOKIE['account'] . '.json', json_encode($jsonChange, JSON_PRETTY_PRINT));

    header("Location: /settings");
    exit();
}

// change nameid
if (isset($_POST["nameid"])) {
    $nameid = htmlspecialchars($_POST["nameid"]);

    $oldNameid = $jsonAccount["nameid"];

    // check if nameid available
    $storageData = json_decode(file_get_contents('../../backend/storage.json'), true);
    if (!isset($storageData['nameid']) || !is_array($storageData['nameid'])) {
        $storageData['nameid'] = [];
    }

    $nameIDNew = generateUniqueUsername($nameid, array_column($storageData['nameid'], 'nameid'));

    $jsonChange = $jsonAccount;

    $jsonChange["nameid"] = $nameIDNew;

    // replace old nameid with new nameid

    foreach ($storageData['nameid'] as $key => $value) {
        if ($value['nameid'] == $oldNameid) {
            $storageData['nameid'][$key]['nameid'] = $nameIDNew;
        }
    }
    file_put_contents('../../backend/storage.json', json_encode($storageData, JSON_PRETTY_PRINT));
    file_put_contents('../../backend/storage/accounts/' . $_COOKIE['account'] . '.json', json_encode($jsonChange, JSON_PRETTY_PRINT));

    header("Location: /settings");
    exit();
}

// biography
if (isset($_POST["biography"])) {
    $biography = htmlspecialchars($_POST["biography"]);

    $jsonChange = $jsonAccount;

    $jsonChange["attributes"]["bio"] = $biography;

    file_put_contents('../../backend/storage/accounts/' . $_COOKIE['account'] . '.json', json_encode($jsonChange, JSON_PRETTY_PRINT));

    header("Location: /settings");
    exit();
}

// profile picture
if (isset($_FILES["profilePicture"]) && $_FILES["profilePicture"]["error"] === UPLOAD_ERR_OK) {
    $profilePicture = $_FILES["profilePicture"]["tmp_name"];

    $fileContent = file_get_contents($profilePicture);
    $jsonChange = $jsonAccount;

    $jsonChange["attributes"]["pfp"] = base64_encode($fileContent);

    $fileType = mime_content_type($profilePicture); 
    switch ($fileType) {
        case "image/jpeg": 
            $jsonChange["attributes"]["pfp_encode"] = "data:image/jpeg;base64,";
            break;
        case "image/gif": 
            $jsonChange["attributes"]["pfp_encode"] = "data:image/gif;base64,";
            break;
        case "image/png": 
            $jsonChange["attributes"]["pfp_encode"] = "data:image/png;base64,";
            break;
        default: 
            $jsonChange["attributes"]["pfp_encode"] = "data:image/jpeg;base64,";
    }

    file_put_contents('../../backend/storage/accounts/' . $_COOKIE['account'] . '.json', json_encode($jsonChange, JSON_PRETTY_PRINT));

    header("Location: /settings");
    exit();
}

// banner picture
if (isset($_FILES["bannerPicture"]) && $_FILES["bannerPicture"]["error"] === UPLOAD_ERR_OK) {
    $bannerPicture = $_FILES["bannerPicture"]["tmp_name"];

    $fileContent = file_get_contents($bannerPicture);
    $jsonChange = $jsonAccount;

    $jsonChange["attributes"]["banner"] = base64_encode($fileContent);

    $fileType = mime_content_type($bannerPicture);
    switch ($fileType) {
        case "image/jpeg": 
            $jsonChange["attributes"]["banner_encode"] = "data:image/jpeg;base64,";
            break;
        case "image/gif": 
            $jsonChange["attributes"]["banner_encode"] = "data:image/gif;base64,";
            break;
        case "image/png": 
            $jsonChange["attributes"]["banner_encode"] = "data:image/png;base64,";
            break;
        default: 
            $jsonChange["attributes"]["banner_encode"] = "data:image/jpeg;base64,";
    }

    file_put_contents('../../backend/storage/accounts/' . $_COOKIE['account'] . '.json', json_encode($jsonChange, JSON_PRETTY_PRINT));

    header("Location: /settings");
    exit();
    }

var_dump($_POST);