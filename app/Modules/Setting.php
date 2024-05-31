<?php

namespace App\Modules;

use App\Models\Setting as ModelsSetting;

class Setting {

    private static $settings;
    private static $linkSettings;

    public static function run(){
        return new Setting;
    }

    public static function botSettings(){
        if(empty(self::$settings)){
            self::$settings=ModelsSetting::where('name','bot')->first()->value;
        }

        return self::$settings;
    }

    public static function getChannels(){
        return self::botSettings()->channels;
    }

    public static function getAdmins(){
        return self::botSettings()->admins;
    }

    public static function getAdmin($id){

        $target=null;

        foreach(self::getAdmins() as $ad){
            if($ad->id==$id){
                $target=$ad;
                break;
            }
        }

        return $target;
    }
    
    public static function getAdminsChatIdList(){

        $list=[];

        foreach(self::getAdmins() as $ad){
            $list[]=$ad->chat_id;
        }

        return $list;
    }

    public static function linkSettings(){
        if(empty(self::$linkSettings)){
            self::$linkSettings=ModelsSetting::where('name','link')->first()->value;
        }

        return self::$linkSettings;
    }

    public static function getCreateLinkSteps(){
        return self::linkSettings()->create_link->steps;
    }
}