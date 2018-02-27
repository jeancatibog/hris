<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shift';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
