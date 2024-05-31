<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;


class File extends Model {
    protected $fillable=[
        'user_id',
        'link_id',
        'file_id',
        'hash_id',
        'status',
        'caption',
        'mime_type',
        'file_size'
    ];

    public function link(){
        return $this->belongsTo(Link::class);
    }

}