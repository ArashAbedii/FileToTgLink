<?php

$host='http://localhost:8005'; //PUT YOUR IP OR DOMAIN ADDRESS HERE. EXAMPLE: http://192.168.1.15
$botUrl=$host.''; //PUT BOT ROOT PATH HERE. EXAMPLE: $botUrl=$host.'/TeleBotDir';


return [
    'token'=>'6747104891:AAEkPktZMe1MYmaBAEaxcSyT1iJhKBoKTHA', //PUT YOUR BOT TOKEN HERE
    'ADMIN_CHAT_ID'=>'530351595', //PUT YOUR CHAT_ID HERE
    'host'=>$host,
    'bot_url'=>$botUrl,
    'request_handler_path'=>$botUrl.'/requestsHandler.php',
    'bot_main_path'=>$botUrl.'/bootstrap/bot.php',
    'DB_CONNECTION'=>'mysql', //or sqlite
    'DB_HOST'=>'localhost',
    'DB_NAME'=>'file_to_tg_link', //database name
    'DB_USERNAME'=>'root', //database username
    'DB_PASSWORD'=>'', //database password
    'DB_CHARSET'=>'utf8',
    'DB_COLLATION'=>'utf8_unicode_ci',
    'APP_BASE_PATH'=>dirname(__DIR__),
    'ADMINS'=>[
        '530351595'
    ],
    'STORAGE_CHANNEL'=>''
];
