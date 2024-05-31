<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;


class Sent extends Model {
    protected $fillable=[
        'message_id',
        'chat_id'
    ];
}