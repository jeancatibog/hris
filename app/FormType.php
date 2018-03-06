<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_type';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
