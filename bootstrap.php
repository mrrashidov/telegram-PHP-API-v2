<?php
require_once "vendor/autoload.php";

ini_set('display_errors',1);
error_reporting(E_ALL);


define("_TELEGRAM_BOT_API_TOKEN" , "1093348417:AAHJhyDbj9G98EcETGJ0bwF3ysnSrpbK5Sk");
define("_TELEGRAM_BOT_HOOK_URL" , "https://bot.new-projects.uz/telegram.php");
define("_TELEGRAM_DB_CONFIG", [
    'db_host'       => 'localhost',
    'db_name'       => 'korin171_bot',
    'db_user'       => 'korin171_bot',
    'db_password'   => 'ucPZOO*g^nU,',
]);
define("_TELEGRAM_TABLE_NAME", "telegram_dialog");
define("_TELEGRAM_DOWNLOAD_PATCH", $_SERVER['DOCUMENT_ROOT']."/storage/");

