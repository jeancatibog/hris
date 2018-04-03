<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountsTeamLead extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts_team_lead';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
