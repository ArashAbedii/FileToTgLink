<?php

namespace App\Helpers;

use App\Models\File;
use App\Models\Link;
use App\Models\Process;
use App\Modules\LinkModule;
use App\Modules\ProcessModule;
use Src\InlineKeyboardMarkup;
use Src\ReplyKeyboardMarkup;

class Helpers
{
    public static function replyKeyboard()
    {
        if(!isAdmin()){
            return false;
        }

        $kb_menu_btn = config('buttons')['bot_kb_menu'];

        return ReplyKeyboardMarkup::createEasyier(
            "$kb_menu_btn"
        );
    }


    public static function inlineKeyboard()
    {

        if(!isAdmin()){
            return false;
        }

        $kb = [
            [
                [
                    'text' => config('buttons')['inline_kb_create_link'],
                    'callback_data' => 'create_link'
                ],
                [
                    'text' => config('buttons')['inline_kb_my_links'],
                    'callback_data' => 'my_links'
                ]
            ]
        ];

        return InlineKeyboardMarkup::create($kb);
    }

    public static function replyKeyboardCreatingLink()
    {

        if(!isAdmin()){
            return false;
        }


        $str = "";

        
        $kb_menu_btn = config('buttons')['bot_kb_done'];
        $str .= $kb_menu_btn . "//";
        

        return ReplyKeyboardMarkup::createEasyier(
            $str
        );
    }

    public static function linkKey()
    {

        $generatedKey = self::genCode(config()['hash_pattern'], rand(6, 12));

        if (Link::where('hash_id')->exists()) {
            return self::linkKey();
        }

        return $generatedKey;
    }

    public static function fileKey()
    {

        $generatedKey = self::genCode(config()['hash_pattern'], rand(8, 16));

        if (File::where('hash_id')->exists()) {
            return self::fileKey();
        }

        return $generatedKey;
    }


    //code generator
    public static function genCode($chars = "abcdefg123456", $len = 5)
    {

        $newWord = str_shuffle($chars);

        $newWordArr = str_split($newWord);

        $countChar = count($newWordArr);

        $output = '';

        for ($i = 0; $i < $len; $i++) {
            $output .= $newWordArr[rand(0, $countChar - 1)];
        }

        return $output;
    }
}
