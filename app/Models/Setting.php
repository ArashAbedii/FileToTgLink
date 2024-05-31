<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;


class Setting extends Model {

    public function getValueAttribute($value){
        return json_decode($value);
    }

}