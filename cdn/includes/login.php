<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require __DIR__ . "/discord.php";
require __DIR__ . "/functions.php";
require "../config.php";


init($redirect_url, $client_id, $secret_id, $bot_token);
get_user();

redirect("https://acemavie.eu/firebase-explorer/cdn");