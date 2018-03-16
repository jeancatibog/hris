<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDtrSummary extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tk_employee_dtr_summary';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
