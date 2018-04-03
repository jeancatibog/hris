<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Validator;
use App\CompanyPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Validator::extend('min_ot', function($attribute, $value, $parameters, $validator) {
            $otHrs = ( ( strtotime($value) - strtotime($parameters[0]) ) / 60 ) ;

            $policy = CompanyPolicy::find(1);
            if ($parameters[1] > $otHrs) {
                return false;
            } else {
                return true;
            }    
        });

        Validator::extend('pre_post_ot', function($attribute, $value, $parameters, $validator) {
            if (strtotime(date("Y-m-d H:i:s",strtotime($value))) >= strtotime($parameters[1]) && strtotime($parameters[0]) >= strtotime($parameters[2])) { // post shift ot filing
                return true;
            } elseif (strtotime(date("Y-m-d H:i:s",strtotime($value))) <= strtotime($parameters[1]) && strtotime($parameters[0]) <= strtotime($parameters[1])) { // pre shift ot filing
                return true;
            } else {
                return false;
            }   
        });

        Validator::extend('overlap', function($attribute, $value, $parameters, $validator) {
            return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
