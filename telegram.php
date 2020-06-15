<?php
require_once "bootstrap.php";



$telegram = new \Bot\TelegramBot\Telegram(_TELEGRAM_BOT_API_TOKEN);
$telegram->set_db_config(_TELEGRAM_DB_CONFIG);
$telegram->dialog_table = _TELEGRAM_TABLE_NAME;
$telegram->download_patch = _TELEGRAM_DOWNLOAD_PATCH;

$telegram->commands = [
    '/start'            => \Commands\StartCommand::class,
    '/help'             => \Commands\HelpCommand::class,
    '/dialog'           => \Commands\DialogCommand::class,
    '/download'         => \Commands\DownloadCommand::class,
    '/send_photo'       => \Commands\Send_photoCommand::class,
    '/send_document'    => \Commands\Send_documentCommand::class,
    '/keyboard'         => \Commands\KeyboardCommand::class,
    '/del_keyboard'     => \Commands\Del_keyboardCommand::class,
];

$telegram->run();
