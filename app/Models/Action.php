<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;


class Action extends Model {
    protected $fillable=[
        'user_id',
        'action_label',
        'action_command',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}