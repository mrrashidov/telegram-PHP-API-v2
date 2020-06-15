<?php


namespace Commands;


use Bot\TelegramBot\Command;

class Del_keyboardCommand extends Command
{
    public function execute()
    {
       $this->removeKeyboard($this->telegram->get_chat_id(), 'Remove keyboard!');
    }
}