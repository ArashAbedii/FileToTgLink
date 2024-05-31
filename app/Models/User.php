<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

class User extends Model {
    protected $fillable=[
        'username',
        'first_name',
        'last_name',
        'status',
        'chat_id'
    ];

    public function action(){
        return $this->hasMany(Action::class,'user_id');
    }

    public function links(){
        return $this->hasMany(Link::class,'user_id');
    }

    public function latestCreateLinkProcess(){
        return $this->links()->latest()->first();
    }
    
}