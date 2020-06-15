<?php


namespace Bot\TelegramBot;


use Bot\TelegramBot\DB\Query;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Dialog
{

    /**
     * @var Telegram
     */
    private $telegram;

    private $current_id;

    /**
     * Dialog constructor.
     * @param Telegram $telegram
     * @param string $stop_command
     * @param bool $stop_message
     */
    public function __construct(Telegram $telegram, $stop_command = false)
    {
        $this->telegram = $telegram;

        if ($stop_command !== false AND $telegram->get_text() === $stop_command) {
            $this->inactive_dialog();
        }else {
            $this->create_dialog();
        }

    }

    /**
     * @param array $notice
     */
    public function set_notice(Array $notice) {
        $notice = json_encode($notice);

        Query::table($this->telegram->dialog_table)
            ->where([
                " user_id = '{$this->telegram->get_chat_id()}' AND",
                " chat_id = '{$this->telegram->get_user_id()}' AND",
                " status = 'active' AND",
                "id = {$this->current_id}"
            ])
            ->update([
                'notice' => $notice
            ]);
    }

    /**
     * @return array|mixed
     */
    public function get_notice () {
        $notice = Query::table($this->telegram->dialog_table)
            ->select('notice')
            ->where([
                " user_id = '{$this->telegram->get_chat_id()}' AND ",
                " chat_id = '{$this->telegram->get_user_id()}' AND ",
                " id = {$this->current_id} AND ",
                " status = 'active' "
            ])
            ->one()['notice'];

        return ($notice != "") ? json_decode($notice, true) : [];
    }

    /**
     *
     */
    public function create_dialog() {
        if ($this->check_dialog()) {
            $this->current_id = Query::table($this->telegram->dialog_table)
                ->insert_id([
                    'user_id'               => $this->telegram->get_chat_id(),
                    'chat_id'               => $this->telegram->get_user_id(),
                    'status'                => 'active',
                    'command'               => $this->telegram->command_name,
                    'user_first_name'       => $this->telegram->get_user_first_name(),
                    'user_last_name'        => $this->telegram->get_user_last_name(),
                    'user_language_code'    => $this->telegram->get_user_language_code()
                ]);
        }else{
            $id = Query::table($this->telegram->dialog_table)
                ->select()
                ->where([
                    " user_id = '{$this->telegram->get_chat_id()}' AND",
                    " chat_id = '{$this->telegram->get_user_id()}' AND",
                    " status = 'active' "
                ])
                ->one()['id'];

            $this->current_id = $id;
        }
    }

    /**
     * @return bool
     */
    private function check_dialog() {

        $check = Query::table($this->telegram->dialog_table)
            ->select()
            ->where([
                " chat_id = '{$this->telegram->get_chat_id()}' AND ",
                " user_id = '{$this->telegram->get_user_id()}' AND ",
                " status = 'active' AND ",
                " command = '{$this->telegram->command_name}' "
            ])
            ->one();

        if (empty($check)){
            return true;
        }else {
            return false;
        }

    }

    /**
     *
     */
    public function inactive_dialog() {
        Query::table($this->telegram->dialog_table)
            ->where([
                " user_id = '{$this->telegram->get_chat_id()}' AND",
                " chat_id = '{$this->telegram->get_user_id()}' ",
            ])
            ->update([
                'status' => "inactive"
            ]);
    }

}