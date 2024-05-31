<?php

namespace App\Modules;

use App\Helpers\Helpers;
use App\Models\Link;
use App\Models\Process;
use App\Models\Sent;
use Src\InlineKeyboardMarkup;
use stdClass;

class LinkModule
{

    public static function run()
    {
        return new LinkModule;
    }

    public function inquiryCreateLink()
    {
        if(!isAdmin()){
            return false;
        }

        return UserModule::user()->latestCreateLinkProcess();
    }

    public function startCreateLink()
    {

        if(!isAdmin()){
            return false;
        }

        $form = [
            'label' => 'create_link',
            'user_id' => UserModule::user()->id,
            'current_component' => Setting::getCreateLinkSteps()[1],
            'title' => null,
            'settings' => json_encode([
                'files' => []
            ], JSON_UNESCAPED_UNICODE)
        ];

        $form['hash_id'] = Helpers::linkKey();

        //create new one
        $process = Link::create($form);

        //send message
        return $this->handleMessages($process);
    }

    public function setName()
    {

        if(!isAdmin()){
            return false;
        }

        $process = $this->inquiryCreateLink();



        if (empty($process)) {
            //return error

            return false;
        }


        $stp = $this->detectSteps($process);

        $form = [
            'current_component' => $stp->next['name'],
            'title' => message()->getText()
        ];

        //update this
        $process->update($form);


        //send message

        return $this->handleMessages($process);
    }

    public function handleMessages($process, $customMessage = null)
    {

        if ($process->current_component == 'get_title') {
            return bot()->sendMessage([
                'text' => $customMessage ? $customMessage : config("messages")['step_get_title'],
                'reply_to_message_id' => callback_query()->getMessage()->message_id,
                'parse_mode' => 'html',
                'disable_web_page_preview' => true,
                'reply_markup' => Helpers::replyKeyboardCreatingLink()
            ]);
        } elseif ($process->current_component == 'get_files') {
            return bot()->sendMessage([
                'text' => $customMessage ? $customMessage :  config("messages")['step_get_files'],
                'reply_to_message_id' => message()->getMessageId(),
                'parse_mode' => 'html',
                'disable_web_page_preview' => true,
                'reply_markup' => Helpers::replyKeyboardCreatingLink()
            ]);
        } elseif ($process->current_component == 'done') {
            return bot()->sendMessage([
                'text' => $customMessage ? $customMessage :  config("messages")['step_done'],
                // 'reply_to_message_id' => message()->getMessageId(),
                'parse_mode' => 'html',
                'disable_web_page_preview' => true,
                'reply_markup' => Helpers::replyKeyboard()
            ]);
        }
    }


    //detect steps
    public function detectSteps($process, $component = null)
    {

        $steps = [];
        $currentComponent = $process->current_component;

        if ($component) {
            $currentComponent = $component;
        }

        if ($process->current_component == 'done') {

            $steps['current'] = [
                'name' => 'done'
            ];

            $steps['next'] = [
                'name' => 'done'
            ];
        } else {

            $steps = Setting::getCreateLinkSteps();


            foreach ($steps as $key => $step) {

                if ($step == $currentComponent) {

                    //current component
                    $steps['current'] = [
                        'name' => $step
                    ];

                    //detect next component
                    if (!empty($steps[$key + 1])) {

                        $steps['next'] = [
                            'name' => $steps[$key + 1]
                        ];
                    } else {
                        $steps['next'] = [
                            'name' => 'done'
                        ];
                    }
                }
            }
        }


        return (object) $steps;
    }

    public function currentStep()
    {

        $process = $this->inquiryCreateLink();

        if ($process) {
            return $this->detectSteps($process)->current['name'];
        }

        return false;
    }

    public function setFile()
    {

        $process = $this->inquiryCreateLink();



        if (empty($process)) {
            //return error
            return false;
        }

        $settings = $process->settings;


        $file = getFileFromMessage();

        $settings->files[] = [
            'caption' => !empty(message()->getCaption()) ? message()->getCaption() : str_shuffle(time()),
            'file_id' => $file->file_id,
            'mime_type' => $file->mime_type,
            'file_size' => $file->file_size
        ];



        //update this
        $process->update([
            'settings' => json_encode($settings, JSON_UNESCAPED_UNICODE)
        ]);


        //send message

        return $this->handleMessages($process);
    }

    public function setDone()
    {

        $process = $this->inquiryCreateLink();

        if (empty($process)) {
            //return error

            return false;
        }

        $form = [
            'current_component' => 'done',
        ];


        //update this
        $process->update($form);

        //create or update links 
        $files = $process->settings->files;

        $process->files()->delete();

        if (!empty($files[0])) {

            foreach ($files as $file) {
                $process->files()->create([
                    'hash_id' => Helpers::fileKey(),
                    'user_id' => UserModule::user()->id,
                    'file_id' => $file->file_id,
                    'caption' => $file->caption,
                    'mime_type' => $file->mime_type,
                    'file_size' => $file->file_size
                ]);
            }
        }

        $link = getLink($process);
        //send message
        $message = "عملیات با موفقیت انجام شد. لینک دانلود:\n$link";

        return $this->handleMessages($process, $message);
    }


    public function sendFiles($linkHashId)
    {

        $link = Link::where('hash_id', $linkHashId)->where('status', 'actived')->first();

        if (empty($link)) {
            return bot()->sendMessage([
                'text' => config("messages")['link_not_found'],
                'reply_to_message_id' => message()->getMessageId(),
                'parse_mode' => 'html',
                'disable_web_page_preview' => true,
                'reply_markup' => Helpers::replyKeyboard()
            ]);
        }

        $files = $link->files()->where('status', 'actived')->get();

        if (empty($files[0])) {
            return bot()->sendMessage([
                'text' => config("messages")['not_found_files'],
                'reply_to_message_id' => message()->getMessageId(),
                'parse_mode' => 'html',
                'disable_web_page_preview' => true,
                'reply_markup' => Helpers::replyKeyboard()
            ]);
        }

        //send alert
        $pre = json_decode(bot()->sendMessage([
            'text' => config("messages")['download_alert_msg'],
            'reply_to_message_id' => message()->getMessageId(),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true
        ]));


        if ($pre->ok) {
            Sent::create(['chat_id' => chat()->getChatId(), 'message_id' => $pre->result->message_id]);
        }

        foreach ($files as $file) {



            if (strpos($file->mime_type, 'audio') !== false) {

                $res = json_decode(bot()->sendAudio([
                    'audio' => $file->file_id,
                    'reply_to_message_id' => message()->getMessageId(),
                    'parse_mode' => 'html',
                    'disable_web_page_preview' => true,
                    'reply_markup' => Helpers::replyKeyboard(),
                    'caption' => $file->caption
                ]));

                if ($res->ok) {
                    Sent::create(['chat_id' => chat()->getChatId(), 'message_id' => $res->result->message_id]);
                }
            } elseif (strpos($file->mime_type, 'video') !== false) {

                $res=json_decode(bot()->sendVideo([
                    'video' => $file->file_id,
                    'reply_to_message_id' => message()->getMessageId(),
                    'parse_mode' => 'html',
                    'disable_web_page_preview' => true,
                    'reply_markup' => Helpers::replyKeyboard(),
                    'caption' => $file->caption
                ]));

                if ($res->ok) {
                    Sent::create(['chat_id' => chat()->getChatId(), 'message_id' => $res->result->message_id]);
                }
            } else {

                $res=json_decode(bot()->sendAudio([
                    'document' => $file->file_id,
                    'reply_to_message_id' => message()->getMessageId(),
                    'parse_mode' => 'html',
                    'disable_web_page_preview' => true,
                    'reply_markup' => Helpers::replyKeyboard(),
                    'caption' => $file->caption
                ]));

                if ($res->ok) {
                    Sent::create(['chat_id' => chat()->getChatId(), 'message_id' => $res->result->message_id]);
                }
            }
        }
    }

    public function getMyLinks()
    {

        if(!isAdmin()){
            return false;
        }

        $myLinks = UserModule::user()->links()->where('status', 'actived')->where('title','!=','')->orderBy('created_at', 'desc')->take(50)->get();
        
        if(empty($myLinks[0])){
            return bot()->answerCallbackQuery([
                'callback_query_id'=>callback_query()->getCallbackQueryId(),
                'text'=>config('messages')['link_list_empty']
            ]);
        }


        $kb = [];

        foreach ($myLinks as $myLink) {
            $kb[] = [
                [
                    'text' => '🔗 '.$myLink->title,
                    'callback_data' => 'edit_link_' . $myLink->id,
                ]
            ];
        }

        //send request to telegram
        return bot()->editMessageReplyMarkup([
            'message_id' =>callback_query()->getMessage()->message_id,
            'reply_markup' => InlineKeyboardMarkup::create($kb),
        ]);
    }

    public function linkMenu($id)
    {

        if(!isAdmin()){
            return false;
        }

        $myLink = UserModule::user()->links()->where('id',$id)->where('status', 'actived')->first();

        if(empty($myLink)){
            return bot()->answerCallbackQuery([
                'callback_query_id'=>callback_query()->getCallbackQueryId(),
                'text'=>config('messages')['link_not_found']
            ]);
        }

        $kb =[
            [
                [
                    'text'=>config('buttons')['link_delete'],
                    'callback_data'=>'delete_link_'.$id
                ]
            ]
        ];

        $linkName=$myLink->title;
        $linkAddr=getLink($myLink);

        $text="📲<b>یکی گزینه انتخاب کنید</b>
        🔖 <b>عنوان لینک:</b> $linkName
        🔗 <b>آدرس لینک:</b> $linkAddr";

        //send request to telegram
        return bot()->editMessageText([
            'text'=>$text,
            'parse_mode'=>'html',
            'message_id' =>callback_query()->getMessage()->message_id,
            'reply_markup' => InlineKeyboardMarkup::create($kb),
        ]);
    }

    public function setDelete($id)
    {

        if(!isAdmin()){
            return false;
        }

        $process = UserModule::user()->links()->where('id',$id)->where('status', 'actived')->first();

        if (empty($process)) {
            //return error
            return bot()->answerCallbackQuery([
                'callback_query_id'=>callback_query()->getCallbackQueryId(),
                'text'=>config('messages')['link_not_found']
            ]);
            
        }

        $form = [
            'status' => 'inactived',
        ];


        //update this
        $process->update($form);

        bot()->deleteMessage(['message_id'=>callback_query()->getMessage()->message_id]);


        //send message
        $message = "لینک با موفقیت حذف شد";


        return $this->handleMessages($process, $message);
    }
}
