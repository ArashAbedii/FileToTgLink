<?php

namespace App\Controllers;

use App\Helpers\Helpers;
use App\Modules\LinkModule;


class MessageHandler{

   private $linkModule;

   public function __construct()
   {
      $this->linkModule=LinkModule::run();
   }

   //main method
   public function run(){

      //do somethings
      if(message()->getText()=='/start' && isAdmin()){

         //send simple message by options
         return bot()->sendMessage([
               'text'=>config("messages")['start'],
               'reply_to_message_id'=>message()->getMessageId(),
               'parse_mode'=>'html',
               'disable_web_page_preview'=>true,
               'reply_markup'=>Helpers::replyKeyboard(),         
         ]);

      }elseif(message()->getText()==config('buttons')['bot_kb_menu']){

         //send request to telegram
         bot()->sendMessage([
               'text'=>config('messages')['menu'],
               'reply_to_message_id'=>message()->getMessageId(),
               'reply_markup'=>Helpers::inlineKeyboard(),
         ]);

      }elseif(  $this->linkModule->currentStep() && !empty(message()->getText()) && message()->getText()==config('buttons')['bot_kb_done'] ){
         return $this->linkModule->setDone();
      }elseif(isDlLink(message()->getText())){

         //handle
         $hashId=extractDlLink(message()->getText());

         return $this->linkModule->sendFiles($hashId);

      }elseif(!empty(message()->getText()) && $this->linkModule->currentStep()=='get_title'){
         return $this->linkModule->setName();
      }elseif(isFile() && $this->linkModule->currentStep()=='get_files'){
         return $this->linkModule->setFile();
      }else{
         //send request to telegram
         bot()->sendMessage([
            'text'=>config('messages')['invalid_command'],
            'reply_to_message_id'=>message()->getMessageId(),
            'reply_markup'=>Helpers::replyKeyboard(),
         ]);
      }
   }
}
