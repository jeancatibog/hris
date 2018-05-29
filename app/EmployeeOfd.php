<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeOfd extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_ofd';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
