<?php


namespace Commands;


use Bot\TelegramBot\Command;
use Bot\TelegramBot\Dialog;

class DialogCommand extends Command
{
    public function execute()
    {

        $dialog = new Dialog($this->telegram, "stop_dialog");

        $data = [
            "chat_id" => $this->telegram->get_chat_id()
        ];

        $notice = $dialog->get_notice();
        if (!isset($notice['state'])) {
            $notice['state'] = 0;
        }

        switch ($notice['state']) {
            case 0:
                $data['text'] = "Enter your name:";

                if ($this->telegram->get_text() !== ""){

                    $notice['state'] += 1;
                }

                break;
            case 1:
                $data['text'] = "Enter your last name:";

                if ($this->telegram->get_text() !== ""){
                    $notice['name'] = $this->telegram->get_text();
                    $notice['state'] += 1;
                }
                break;
            case 2:
                $data['text'] = "Enter Your sex:";

                if ($this->telegram->get_text() !== ""){
                    $notice['last_name'] = $this->telegram->get_text();

                    $notice['state'] += 1;
                }
                break;
            case 3:
                $data['text'] = "Enter your age:";

                if ($this->telegram->get_text() !== ""){
                    $notice['sex'] = $this->telegram->get_text();

                    $notice['state'] += 1;
                }
                break;
            case 4:
                $keyboard = [
                    ['Tasdiqlash'],
                ];
                $data['text'] = "Please Conirm";
                $data['reply_markup'] =  [ 'keyboard' => $keyboard, 'resize_keyboard' => true];

                if ($this->telegram->get_text() !== ""){
                    $notice['age'] = $this->telegram->get_text();

                    $notice['state'] += 1;
                }
                break;

            case 5:
                $data['text'] = "Thank you :)";
                $dialog->inactive_dialog();
                break;
        }

        $this->sendMessage($data);
        $dialog->set_notice($notice);
    }
}