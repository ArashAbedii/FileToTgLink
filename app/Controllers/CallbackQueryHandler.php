<?php

namespace App\Controllers;

use App\Models\Action;
use App\Modules\LinkModule;
use App\Modules\Setting;
use App\Modules\UserModule;

class CallbackQueryHandler {

    public function run(){

        //code
        
        if(callback_query()->getData()=='check_channel_join'){
            
            $msg='عضویت شما تایید شد ✅';
            $ok=true;

            $botSettings=Setting::botSettings();

            $channels=$botSettings->channels;

            
            $user_id=message()->getFrom()->id;

               
            foreach($channels as $channel){

                $response=json_decode(bot()->getChatMember(['chat_id'=>$channel->chat_id,'user_id'=>$user_id]));

                if($response->ok==false){
                    exit();
                }elseif($response->ok && $response->result->status=='left'){
                    $msg='جهت  فعال شدن ربات در تمام چنل ها باید عضو شوید 🔐';
                    $ok=false;
                    break;
                }
            }

            bot()->answerCallbackQuery(
                [
                    'callback_query_id'=>callback_query()->getCallbackQueryId(),
                    'text'=>$msg
                ]
            );

            if($ok){
                

                $action=Action::where('user_id',UserModule::user()->id)->where('action_label','lock_channel')->where('status','pending')->first();
                
                if($action){
                   
                    //sendMedias();
                    $hashId=extractDlLink($action->action_command);

                   


                    return LinkModule::run()->sendFiles($hashId);
                }
            }

            
        }elseif(callback_query()->getData()=='create_link'){
            return LinkModule::run()->startCreateLink();
        }elseif(callback_query()->getData()=='my_links'){
            return LinkModule::run()->getMyLinks();
        }elseif(strpos(callback_query()->getData(),'edit_link_')!==false){
            $linkId=str_replace('edit_link_','',callback_query()->getData());
            return LinkModule::run()->linkMenu($linkId);
        }elseif(strpos(callback_query()->getData(),'delete_link_')!==false){
            $linkId=str_replace('delete_link_','',callback_query()->getData());
            return LinkModule::run()->setDelete($linkId);
        }
    }

}