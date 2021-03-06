<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $table = 'employees';
    protected $guarded = [];
}
