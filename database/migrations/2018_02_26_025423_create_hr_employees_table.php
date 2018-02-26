<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->increments('id', true);
            $table->integer('employee_number');
            $table->string('lastname', 60);
            $table->string('firstname', 60);
            $table->string('middlename', 60);
            $table->string('address', 120);
            $table->integer('city_id')->unsigned();
            $table->integer('province_id')->unsigned();
            $table->integer('country_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('hr_city');
            $table->foreign('province_id')->references('id')->on('hr_province');
            $table->foreign('country_id')->references('id')->on('hr_country');
            $table->char('zip', 10);
            $table->integer('age')->unsigned();
            $table->date('birthdate');
            // $table->integer('company_id')->unsigned();
            // $table->foreign('company_id')->references('id')->on('company');
            $table->string('picture', 60);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_employees');
    }
}
