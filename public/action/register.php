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


// check if can register
$serverJSON = json_decode(file_get_contents('../../backend/configuration.json'), true);

if (!$serverJSON['registerAccount']) {
    header("Location: /login/login");
    exit();
}











// only post method and need password isset with min 4 char
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && strlen($_POST['password']) >= 4) {

    $passwordhash = password_hash(htmlspecialchars($_POST['password']), PASSWORD_DEFAULT);

    // user faker to generate a pseudo
    require_once '../../vendor/autoload.php';

    $faker = Faker\Factory::create();
    // generate animal name
    $username = $faker->userName;

    // generate uuid
    $uuid = (string) \Ramsey\Uuid\Uuid::uuid4();

    // generate pfp
    $url = "https://ui-avatars.com/api/?name=" . $username . "&background=random&color=random&size=400";
    // convert to base64
    $pfpBase64 = base64_encode(file_get_contents($url));

    // check if nameid available
    $storageData = json_decode(file_get_contents('../../backend/storage.json'), true);
    if (!isset($storageData['nameid']) || !is_array($storageData['nameid'])) {
        $storageData['nameid'] = [];
    }

    $nameID = generateUniqueUsername($username, array_column($storageData['nameid'], 'nameid'));
    
    $storageData['nameid'][] = [
        "nameid" => $nameID,
        "uuid" => $uuid
    ];

    file_put_contents('../../backend/storage.json', json_encode($storageData, JSON_PRETTY_PRINT));

    // check if other file
    $r = count(scandir('../../backend/storage/accounts/'));
    
    if($r == 3){
        $ownerPerm = true;
        $adminPerm = true;
        $uploadPerm = true;
        $title = "Owner";
        $name_class="effect__sparkles effect__gold";
        $border = "border__gold";
        $svg_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14z"/></svg>';
    } else{
        $ownerPerm = false;
        $adminPerm = false;
        $uploadPerm = false;
        $title = "Member";
        $name_class="";
        $border = "";
        $svg_icon = "";
    }

    // account data
    $jsonAccount = [
        "uuid" => $uuid,
        "nameid" => $username,
        "attributes" => [
            "username" => $username,
            "creation_date" => date('Y-m-d H:i:s'),
            "pfp_encode" => "data:image/png;base64,",
            "pfp" => $pfpBase64,
            "banner_encode" => false,
            "banner" => false,
            "background_encode" => false,
            "background" => false,
            "bio" => "*No biography for the moment.*",
            "stats" => [
                "manga" => 0,
                "manhwa" => 0,
                "manhua" => 0,
                "favorites" => 0
            ],
            "group" => false,
            "decorations"=>[
                "title" => $title,
                "border" => $border,
                "name_style" => "",
                "name_class" => $name_class,
                "svg_icon" => $svg_icon
            ]
        ],
        "security" => [
            "password" => $passwordhash,
        ],
        "settings" => [],
        "permissions" => [
            "owner" => $ownerPerm,
            "admin" => $adminPerm,
            "upload" => $uploadPerm,
        ],
        "notifications" => []
    ];

    // Try to write the JSON file
    $filePath = '../../backend/storage/accounts/' . $uuid . '.json';
    if (file_put_contents($filePath, json_encode($jsonAccount, JSON_PRETTY_PRINT)) === false) {
        // Get the last error if file_put_contents fails
        $error = error_get_last();
        $detailedError = isset($error['message']) ? $error['message'] : 'Unknown error';
        $detailedError .= ' (path: ' . $filePath . ')';
        
        // Log error details for debugging
        error_log('File creation failed: ' . $detailedError);
        
        // Redirect with detailed error message
        header('Location: /err?e=Error while creating account: ' . urlencode($detailedError));
        exit;
    }

    if (file_exists($filePath)) {

        // save uuid in account cookie for 6 months
        setcookie('account', $uuid, time() + 60 * 60 * 24 * 180, '/');

        header('Location: /');
        exit;
    } else {
        header('Location: /err?e=Error while creating account: file storage creation failed');
        exit;
    }
} else{
    header('Location: /err?e=Error while creating account: password too short');
    exit;
}