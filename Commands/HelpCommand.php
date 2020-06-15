<?php


namespace Commands;


use Bot\TelegramBot\Command;

class HelpCommand extends Command
{
    public function execute()
    {
        $data = [
            'chat_id' => $this->telegram->get_chat_id(),
            'text' => "Help
            /start          - Start
            /help           - Help
            /dialog         - Dialog
            /download       - Download
            /send_photo     - Send photo
            /send_document  - Send document
            /keyboard       - Keyboard
            /del_keyboard   - Delete keyboard
            "
        ];

        $this->sendMessage($data);
    }
}