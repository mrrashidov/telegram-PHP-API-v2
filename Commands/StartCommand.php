<?php

namespace Commands;

class StartCommand extends \Bot\TelegramBot\Command
{
    public function execute()
    {
        $data = [
            'chat_id' => $this->telegram->get_chat_id(),
            "text" => "Welcome"

        ];

        $this->sendMessage($data);
    }
}