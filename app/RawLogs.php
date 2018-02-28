<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RawLogs extends Model
{
   	/**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $table = 'tk_employee_raw_logs';
    protected $guarded = [];
}
