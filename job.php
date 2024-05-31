<?php

use App\Models\Sent;
use Carbon\Carbon;
use Src\Bot;
use Src\DBHandler;

require_once __DIR__.'/vendor/autoload.php';
$bot=new Bot();
DBHandler::setup(true);


$messages=Sent::where('created_at','<=',Carbon::now()->subSeconds(58))->get();

foreach($messages as $message){
    $tg=json_decode(bot()->deleteMessage([
        'chat_id'=>$message->chat_id,
        'message_id'=>$message->message_id
    ]));

    if($tg->ok){
        $message->delete();
    }

    sleep(1);
}