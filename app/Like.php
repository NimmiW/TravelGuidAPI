<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    //
    protected $table = 'likes';
    public $incrementing=false;
    public $timestamps = false;
}
