<?php
require_once "bootstrap.php";

$telegram = new \Bot\TelegramBot\Telegram(_TELEGRAM_BOT_API_TOKEN);
$telegram->set_db_config(_TELEGRAM_DB_CONFIG);
$telegram->dialog_table = _TELEGRAM_TABLE_NAME;
echo $telegram->install(_TELEGRAM_BOT_HOOK_URL);