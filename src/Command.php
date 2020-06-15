<?php


namespace Bot\TelegramBot;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

abstract class Command
{
    /**
     * @var string
     * Request URL
     */
    protected $request_url = "https://api.telegram.org/bot";

    /**
     * @var Telegram
     */
    protected $telegram;

    /**
     * @return mixed
     */
    abstract public function execute ();

    /**
     * Command constructor.
     * @param Telegram $telegram
     */
    public function __construct(Telegram $telegram)
    {
        $this->telegram = $telegram;
        $this->request_url = $this->request_url . $telegram->bot_api_token;
    }

    /**
     * @param String $send_type
     * @param array $data
     * @param string $request_type
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function send (String $send_type, Array $data, $request_type = "JSON") {

        $options = [];
        switch ($request_type) {
            case "JSON":
                $options = [ RequestOptions::JSON => $data ];
                break;
            case "multipart":
                foreach ($data as $key => $item) {
                    $options[] = [
                        'name' => $key,
                        'contents' => $item
                    ];
                }
                $options = ['multipart' => $options];
                break;
        }

        $client = new Client();
        $response = $client -> post($this->request_url."/".$send_type, $options);

        return $response;
    }

    /**
     * @param array $data
     */
    public function sendMessage (Array $data) {
        $this->send("sendMessage", $data, "JSON");
    }


    public function sendPhoto (Array $data) {
        $data['photo'] = fopen($data['photo'], 'r');
        $this->send("sendPhoto", $data, 'multipart');
    }

    public function sendDocument (Array $data) {
        $data['document'] = fopen($data['document'], 'r');
        $this->send("sendDocument", $data, 'multipart');
    }

    public function setKeyboard ($chat_id, Array $keyboard, $text = "") {

        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => [
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]
        ];

        $this->send("sendMessage", $data, "JSON");
    }

    public function removeKeyboard ($chat_id, $text) {
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => [
                'remove_keyboard' => true
            ]
        ];
        $this->send("sendMessage", $data, "JSON");
    }

    /**
     * @param bool $file_name
     * @return bool
     */
    public function download ($file_name = false) {

        $type_msg = $this->telegram->get_type_message();

        if ($type_msg !== false) {

            $file = $this->telegram->message[$type_msg];
            $file_id = (isset($document[0])) ? $file[0]['file_id'] : $file['file_id'];

            $client = new Client();
            $response = $client->post($this->request_url."/getFile", [RequestOptions::JSON => ['file_id' => $file_id]]);
            $response = json_decode($response->getBody(), true);

            /*
            {
                "ok":true,
                "result":{
                    "file_id":"BQACAgIAAxkBAAPtXjQDqtkRglYwTpv7jIgFCSsCVakAAj0FAAKFhKBJOk5_KnbH2ZoYBA",
                    "file_unique_id":"AgADPQUAAoWEoEk",
                    "file_size":41466,
                    "file_path":"documents/file_41.png"}}
            */

            if ($response['ok'] === true) {
                $src  = 'https://api.telegram.org/file/bot' . $this->telegram->bot_api_token . '/' . $response['result']['file_path'];
                $name = explode("/", $response['result']['file_path'])[1];
                $name = (is_string($file_name)) ? $file_name.stristr($name, ".") : $name;

                copy($src, $this->telegram->download_patch.$name);
                return true;
            }else {
                return false;
            }

        }else {
            return false;
        }
    }
}