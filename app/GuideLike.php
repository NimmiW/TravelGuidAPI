<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuideLike extends Model
{
    //
    protected $table = 'guide_likes';
    public $incrementing=false;
    public $timestamps = false;
}
