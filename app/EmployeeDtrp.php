<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDtrp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_dtrp';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
