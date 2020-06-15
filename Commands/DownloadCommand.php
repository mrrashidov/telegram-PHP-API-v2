<?php


namespace Commands;


use Bot\TelegramBot\Command;
use Bot\TelegramBot\Dialog;

class DownloadCommand extends Command
{
    public function execute()
    {
        $data = [
            'chat_id' => $this->telegram->get_chat_id(),
        ];

        $dialog = new Dialog($this->telegram, false);

        if (isset($this->telegram->message['text']) AND $this->telegram->get_text() == "/stop") {
            $dialog->inactive_dialog();
            $data['text'] = "Dialogue stopped.";
            $this->sendMessage($data);
        }else {

            $download = $this->download();
            if ($download == false) {
                $data['text'] = "To upload a file, send a file or photo!.";
            }else {
                $data['text'] = "File uploaded.";
            }

            $this->sendMessage($data);

        }
    }
}