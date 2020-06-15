<?php


namespace Commands;


use Bot\TelegramBot\Command;

class Send_photoCommand extends Command
{
    public function execute()
    {
        $data = [
            'chat_id' => $this->telegram->get_chat_id(),
            'photo' => _TELEGRAM_DOWNLOAD_PATCH."/file_65.png"
        ];
        $this->sendPhoto($data);
    }
}