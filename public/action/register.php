<?php


function generateUniqueUsername($username, $existingUsernames) {
    $newUsername = $username;
    $suffix = 1;
    
    while (in_array($newUsername, $existingUsernames)) {
        $newUsername = $username . $suffix;
        $suffix++;
    }

    return $newUsername;
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
    $url = "https://ui-avatars.com/api/?name=" . $username . "&background=random&color=random";
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
            "banner" => false
        ],
        "security" => [
            "password" => $passwordhash,
        ],
        "settings" => [],
        "permissions" => [
            "admin" => false,
            "upload" => false,
        ],
        "notifications" => []
    ];

    // write json file
    file_put_contents('../../backend/storage/accounts/' . $uuid . '.json', json_encode($jsonAccount, JSON_PRETTY_PRINT));

    if (file_exists('../../backend/storage/accounts/' . $uuid . '.json')) {

        // save uuid in account cookie for 6 months
        setcookie('account', $uuid, time() + 60 * 60 * 24 * 180, '/');

        header('Location: /');
        exit;
    } else {
        header('Location: /err?e=Error while creating account : file storage creation failed');
        exit;
    }
} else{
    header('Location: /err?e=Error while creating account : password too short');
    exit;
}