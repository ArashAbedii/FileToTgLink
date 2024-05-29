<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

class User extends Model {
    protected $fillable=[
        'username',
        'first_name',
        'last_name',
        'status'
    ];
}