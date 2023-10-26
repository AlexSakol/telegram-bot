<?php

require_once('config.php');
require_once(__DIR__ . '/Models/Workers/telegram_worker.php');

use Models\Workers\Telegram;

$telegram = new Telegram(TG_TOKEN);

$keyboard = [//keyboard example
    [//row
        ['text' => 'Хочу фото', 'callback_data' => 'send_photo'],//button
        ['text' => 'Яндекс', 'url' => 'https://yandex.com/'],
    ]
];

echo $telegram->setWebHook(DOMAIN);

$data = json_decode(file_get_contents('php://input'), true); //get an answer and save it as an array

$text = $data["message"]["text"];
$chat_id = $data["message"]["chat"]["id"];

if($data['callback_query']){//work with inline keyboard 
    $result = $data['callback_query']['data'];//value of pressed button 
    $querry_chat_id = $data["callback_query"]["message"]["chat"]["id"];    
    if($result == 'send_photo'){
        $telegram->sendPhoto($querry_chat_id, "Держи фото", getRandomFile(__DIR__.'/img'));
    }
}

if(mb_strtoupper($text) == "ПРИВЕТ" || mb_strtoupper($text) == '/HELLO'){
    $telegram->sendMessage('Привет', $chat_id);
}
elseif(mb_strtoupper($text) == "ПРИШЛИ ФОТО" || mb_strtoupper($text) == "/PHOTO"){
    $telegram->sendPhoto($chat_id, "Держи фото", getRandomFile(__DIR__.'/img'));
    //you should add images to send in the directory "/img" or crate another folder with images  
}
elseif(mb_strtoupper($text) == "ПРИШЛИ КЛАВИАТУРУ"){
    $telegram->sendInlineKeyboard($chat_id, "Клавиатура", $keyboard);       
}
elseif(isset($data["message"]["photo"])){
    $photo = end($data["message"]["photo"]);//select the biggest photo
    $photo_id = $photo['file_id'];
    $telegram->saveFile($photo_id, __DIR__.'/img');//images save in directory "/img" you can create anothr directory to save images
    $telegram->sendMessage('Файл добавлен на сервер', $chat_id);
}
elseif(isset($data["message"]["document"])){    
    $doc_id = $data["message"]["document"]['file_id'];
    $telegram->saveFile($doc_id, __DIR__.'/files');//files save in folder "/files"
    $telegram->sendMessage('Файл добавлен на сервер', $chat_id);
}
elseif(mb_strtoupper($text) == "/WELCOME"){
    $telegram->sendMessage('Добро пожаловать', $chat_id);
}
else{
    $telegram->sendMessage('Команда не найдена', $chat_id); 
}

function getRandomFile($folder){//return random file from folder
	$contents = array_diff(scandir($folder), ['..', '.']);
    $files = [];
    foreach ($contents as $file) {
		if(is_file($folder. '/' . $file)){
            array_push($files, $folder. '/' . $file);     
        }
	}    
	return $files[mt_rand(0, count($files) -1)];
}








