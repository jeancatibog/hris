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
            $otHrs = round( strtotime($value) - strtotime($parameters[0]) ) / (60 * 60) ;

            $policy = CompanyPolicy::find(1);
            if ($parameters[1] > $otHrs) {
                return false;
            } else {
                return true;
            }    
        });

        Validator::extend('after_shift', function($attribute, $value, $parameters, $validator) {
            if (strtotime($parameters[0]) > strtotime(date("H:i:s",strtotime($value)))) {
                return false;
            } else {
                return true;
            }    
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
