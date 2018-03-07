<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeOvertime extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_overtime';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
