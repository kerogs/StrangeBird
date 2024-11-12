<?php

if (!$kpf_config["labs"]["enable"]) exit(file_get_contents("public/error/403.php"));

$userIP = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
$userIP = explode(',', $userIP)[0];
$userIP = trim($userIP);

if (!empty($kpf_config["labs"]["labsIPAuth"]) && !in_array($userIP, $kpf_config["labs"]["labsIPAuth"])) {
    exit(file_get_contents("public/error/403.php"));
}
