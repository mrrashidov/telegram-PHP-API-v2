<?php


namespace Bot\TelegramBot;

use Bot\TelegramBot\DB\DB;
use Bot\TelegramBot\DB\Query;
use GuzzleHttp\Client;

class Telegram
{
    /**
     * @var
     */
    public $bot_api_token;

    /**
     * @var array
     */
    public $commands = [];

    /**
     * @var
     */
    public $download_patch;

    /**
     * @var
     */
    public $message;

    /**
     * @var
     */
    public $command_name;

    /**
     * @var array
     */
    public $message_types = [
        'document', 'photo', 'voice', 'sticker'
    ];

    public $dialog_table = "telegram_dialog";

    /**
     * Telegram constructor.
     * @param $bot_api_token
     */
    public function __construct($bot_api_token)
    {
        $this->bot_api_token = $bot_api_token;
    }

    /**
     * Enter point
     */
    public function run () {

        $this->message = file_get_contents("php://input");
        $this->message = json_decode($this->message, true)['message'];


        $this->command_name = (isset($this->message['text'])) ? $this->message['text'] : "";
        $this->check_dialog();

        if (array_key_exists($this->command_name, $this->commands)) {
            $command = new $this->commands[$this->command_name]( $this );
            $command->execute();
        }
    }

    /**
     * @param $url
     * @return bool|string
     *
     * Set hook url
     */
    public function install ($url) {

        if (defined("TELEGRAM_BOT_DB_CONFIG") AND !empty(TELEGRAM_BOT_DB_CONFIG)) {
            $this->ini_database();
        }

        $client = new Client();
        $url = "https://api.telegram.org/bot{$this->bot_api_token}/setWebhook?url={$url}";

        try {
            $client->get($url);
            return true;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return bool
     */
    private function ini_database () {
        $sql = "
          CREATE TABLE IF NOT EXISTS `{$this->dialog_table}` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` text NOT NULL,
          `chat_id` varchar(50) NOT NULL,
          `status` varchar(50) NOT NULL,
          `command` varchar(255) NOT NULL,
          `notice` text,
          `user_first_name` varchar(255) DEFAULT NULL,
          `user_last_name` varchar(255) DEFAULT NULL,
          `user_language_code` varchar(50) DEFAULT NULL,
          `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

        return DB::inst()->setSql($sql)->execute();
    }

    /**
     * @param $config
     * Set database settings
     */
    public function set_db_config ($config) {
        define("TELEGRAM_BOT_DB_CONFIG", $config);
    }

    /**
     * @return mixed
     * Check if you can download and return type message or false
     */
    public function get_type_message () {
        $is_download = false;
        foreach ($this->message_types as $key => $item) {
            if (key_exists($item, $this->message)) {
                $is_download = $item;
                break;
            }
        }
        return $is_download;
    }

    /**
     * @return mixed
     */
    public function get_chat_id () {
        return $this->message['chat']['id'];
    }

    /**
     * @return mixed
     */
    public function get_text () {
        return $this->message['text'];
    }

    /**
     * @return mixed
     */
    public function get_user_id () {
        return $this->message['from']['id'];
    }

    /**
     * @return mixed
     */
    public function get_user_first_name () {
        return $this->message['from']['first_name'];
    }

    /**
     * @return mixed
     */
    public function get_user_last_name () {
        return $this->message['from']['last_name'];
    }

    /**
     * @return mixed
     */
    public function get_user_language_code () {
        return $this->message['from']['language_code'];
    }

    /**
     * @return bool
     */
    private function    check_dialog() {
        $check = Query::table($this->dialog_table)
            ->select()
            ->where([
                " chat_id = '{$this->get_chat_id()}' AND ",
                " user_id = '{$this->get_user_id()}' AND ",
                "  status = 'active' "
            ])
            ->one();

        if (!empty($check)){
            $this->command_name = $check['command'];
            return true;
        }else {
            return false;
        }

    }
}