<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimkeepingPeriod extends Model
{
   	/**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $table = 'tk_period';
    protected $guarded = [];
}
