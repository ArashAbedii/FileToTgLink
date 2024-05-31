<?php

//BOOTSTRAP
require_once "../vendor/autoload.php";

use App\Modules\Setting;
use App\Modules\UserModule;
use Src\Bot;
use Src\DBHandler;
use Src\Helpers\Utilities;

if(config()['debug']){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

//bot starter
$bot=new Bot();
DBHandler::setup(true);

$botSettings=Setting::botSettings();

//handle user permissions
$currentUser=UserModule::authHandler();


if(empty($currentUser->status) || $currentUser->status!='actived'){
    exit();
}

//lock channel
Utilities::lock_on_channels(Setting::getChannels(),message()->getFrom()->id,$botSettings->lock_channel_msg);

//lock add memeber
// $count_added_memeber=Utilities::getCountAddedMemeber(message()->getFrom()->id);
// Utilities::lock_on_add_member(3,message()->getFrom()->id,$count_added_memeber,"you added $count_added_memeber members. you need to add 3 members");


//-------------------------------------------------------------------UPDATE HANDLERS----------------------------------------------------------------

switch(update()->getType()){

    case 'message':
        messageHandler()->run();
    break;


    case 'callback_query':
        callbackQueryHandler()->run();
    break;


    case 'channel_post':
        channelPostHandler()->run();
    break;


    case 'edited_message':
        editedMessageHandler()->run();
    break;


    case 'edited_channel_post':
        editedChannelPostHandler()->run();
    break;


    case 'inline_query':
        inlineQueryHandler()->run();
    break;


    case 'poll':
        pollHandler()->run();
    break;


    case 'poll_answer':
        pollAnswerHandler()->run();
    break;


    case 'my_chat_member':
        myChatMemberHandler()->run();
    break;


    case 'chat_member':
        chatMemberHandler()->run();
    break;

}