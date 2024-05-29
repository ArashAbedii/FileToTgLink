<?php
namespace Src\UpdateTypes;

class MyChatMember extends Update {

    protected $chat_id;

    public function getChat(){
        return $this->{$this->gettype()}->chat;
    }

    public function getFrom(){
        return $this->{$this->gettype()}->from;
    }

    public function getOldChatMember(){
        return $this->{$this->gettype()}->old_chat_member;
    }

    public function getNewChatMember(){
        return $this->{$this->gettype()}->new_chat_member;
    }
}