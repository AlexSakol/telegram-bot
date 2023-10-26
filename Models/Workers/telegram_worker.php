<?php

    namespace Models\Workers;    
    
    require_once(__DIR__ . '\curl_worker.php');    

    class Telegram{

        private $token;// bot token
        const URL = 'https://api.telegram.org/bot';    

        public function __construct(string $token)
        {
            $this->token = $token;             
        }
        
        public function getUpdates(){
            return 
                CURL_WORKER::sendRequest(self::URL . $this->token . '/getUpdates');
        }

        public function setWebHook(string $domain){
            return 
                CURL_WORKER::sendRequest(self::URL . $this->token . '/setWebhook', 
                    "GET",
                    ['url' => $domain]
                );
        }

        public function sendMessage(string $message, int $chat_id){            
            return 
                CURL_WORKER::sendRequest(self::URL . $this->token . '/sendMessage', 
                    "POST",
                    ['chat_id' => $chat_id, 'text' => $message]);
        }

        public function replyToMessage(string $message, int $chat_id, int $reply_message_id){
            return    
                CURL_WORKER::sendRequest(self::URL . $this->token . '/sendMessage', 
                    "POST",
                    ['chat_id' => $chat_id, 'text' => $message, 'reply_to_message_id' => $reply_message_id]);
        }

        public function deleteMessage(int $chat_id, int $message_id){
            return    
                CURL_WORKER::sendRequest(self::URL . $this->token . '/deleteMessage',
                    'GET',
                    ['chat_id' => $chat_id, 'message_id' => $message_id]);
        }

        public function sendKeyboard(int $chat_id, string $message, array $buttons, 
            bool $resize_keyboard = true, bool $one_time_keyboard = true){
                $keyboard = [
                    'keyboard' => $buttons,
                    'resize_keyboard' => $resize_keyboard,
                    'one_time_keyboard' => $one_time_keyboard,
                ];
                return 
                    CURL_WORKER::sendRequest(self::URL . $this->token . '/sendMessage', 
                        "POST",
                        ['chat_id' => $chat_id, 'text' => $message, 'reply_markup'=>
                            json_encode($keyboard)
                ]);

        }

        public function sendInlineKeyboard(int $chat_id, string $message, array $keyboard){            
            return 
                CURL_WORKER::sendRequest(self::URL . $this->token . '/sendMessage', 
                    "POST",
                    ['chat_id' => $chat_id, 'text' => $message, 'reply_markup'=>
                        json_encode(['inline_keyboard' => $keyboard])
            ]);
        }

        public function sendPhoto(int $chat_id, string $caption = null, string $photo/*path to photo*/){
            return 
                CURL_WORKER::sendRequest(self::URL . $this->token . '/sendPhoto', 
                    "POST",
                    ['chat_id' => $chat_id, 'caption' => $caption, 'photo' => curl_file_create($photo)]);
        }

        public function sendDocument(int $chat_id, string $caption = null, string $document/*path to document*/){
            return 
                CURL_WORKER::sendRequest(self::URL . $this->token . '/sendDocument', 
                    "POST",
                    ['chat_id' => $chat_id, 'caption' => $caption, 'document' => curl_file_create($document)]);
        }

        public function sendMediaGroup(int $chat_id, array $media){//send two or more photos in one message              
            return 
                CURL_WORKER::sendRequest(self::URL . $this->token . '/sendMediaGroup', 
                    "POST",
                    ['chat_id' => $chat_id, 'media' => json_encode($media)]);                    
        } 

        public function getFile(string $file_id){//get file info
            return 
                CURL_WORKER::sendRequest(self::URL . $this->token . '/getFile', 
                    "GET",
                    ['file_id' => $file_id]);
        }

        public function saveFile(string $file_id, string $save_directory){
            $data = json_decode($this->getFile($file_id), true);    
            $file_name = $data['result']['file_path'];//get file name from api    
            $photo_URL = "https://api.telegram.org/file/bot". $this->token ."/" . $file_name;//URL to the file   
            $arr_file_path = explode("/", $file_name);//separate the path as an array 
            $file_path = $save_directory.'/'.$arr_file_path[1]; //set file name to save 
            file_put_contents($file_path, file_get_contents($photo_URL));//save file
        }    
        
    }