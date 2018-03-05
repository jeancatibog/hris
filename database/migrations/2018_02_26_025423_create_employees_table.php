<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id', true);
            $table->integer('employee_number')->unique();
            $table->string('lastname', 60);
            $table->string('firstname', 60);
            $table->string('middlename', 60)->nullable();
            $table->string('address', 255)->nullable();
            $table->integer('city_id')->nullable()->unsigned();
            $table->integer('province_id')->nullable()->unsigned();
            $table->integer('country_id')->nullable()->unsigned();
            $table->integer('zip')->nullable()->unsigned();
            $table->integer('age')->nullable()->unsigned();
            $table->date('birthdate')->nullable();
            $table->string('picture', 60)->nullable();
            $table->foreign('city_id')->references('id')->on('city');
            $table->foreign('province_id')->references('id')->on('province');
            $table->foreign('country_id')->references('id')->on('country');
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
        Schema::dropIfExists('employees');
    }
}
