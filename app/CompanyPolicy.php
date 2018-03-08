<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyPolicy extends Model
{
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $table = 'company_policy';
    protected $guarded = [];
}
