<?php


namespace Commands;


use Bot\TelegramBot\Command;

class Send_documentCommand extends Command
{
    public function execute()
    {
        $data = [
            'chat_id' => $this->telegram->get_chat_id(),
            'document' => _TELEGRAM_DOWNLOAD_PATCH."/file_65.png"
        ];
        $this->sendDocument($data);
    }
}