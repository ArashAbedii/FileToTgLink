<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;


class Link extends Model {
    protected $fillable=[
        'user_id',
        'title',
        'hash_id',
        'status',
        'current_component',
        'settings',
        'title'
    ];

    public function files(){
        return $this->hasMany(File::class,'link_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getSettingsAttribute($value){

        if(!empty($value)){
            return json_decode($value);
        }
        
    }

}