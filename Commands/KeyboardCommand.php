<?php


namespace Commands;


use Bot\TelegramBot\Command;

class KeyboardCommand extends Command
{
    public function execute()
    {
        $kayboard = [
            ['7','8','9'],
            ['4','5','6'],
            ['1','2','3'],
            ['0'],
        ];

        $this->setKeyboard($this->telegram->get_chat_id(), $kayboard, "Set keyboard!");
    }
}