<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $dates = ['published_at'];

    public function setTitleAttribute($value){

        $this->attribute['title'] = $value;

        if(!$this->exists){
            $this->attribute['slug'] = str_slug($value);
        }
    }
}
