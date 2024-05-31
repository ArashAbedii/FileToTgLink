<?php

namespace App\Modules;

use App\Helpers\Helpers;
use App\Models\Config;
use App\Models\User;

class UserModule {

    public static function run(){
        return new UserModule;
    }

    public static function from(){
        return message()->getFrom();
    }

    public static function authHandler(){

        $user=User::where('chat_id',self::from()->id)->first();


        if(empty($user)){

            if(update()->getType()=='message' && message()->getText()){

                //init register form
                $form=[
                    'firstname'=>self::from()->first_name,
                    'lastname'=>!empty(self::from()->last_name) ? self::from()->last_name : null,
                    'username'=>!empty(self::from()->username) ? self::from()->username : null,
                    'chat_id'=>self::from()->id,
                    'status'=>'actived'
                ];

                return User::create($form);

            }
            return false;
        }else{

            if($user->status=='blocked'){
                return false;
            }
        }

        if(!empty(my_chat_member()->getNewChatMember())){
            
            $st=my_chat_member()->getNewChatMember()->status;

            if(!empty($st)){
                if($st=='kicked'){
                    $user->update(['status'=>'left']);
                    return false;
                }elseif($st=='inactived'){
                    $user->update(['status'=>'inactived']);
                    return false;
                }
            }
        }
        
        $form['status']='actived';

        $user->update($form);

        return $user;

    }

    public static function user(){
        return $GLOBALS['currentUser'];
    }

    
}