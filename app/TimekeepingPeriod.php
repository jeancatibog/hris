<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimekeepingPeriod extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tk_period';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
