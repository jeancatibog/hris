<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_users', function (Blueprint $table) {
            $table->increments('id', true);
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('lastname');
            $table->string('firstname');
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('hr_employees');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_users');
    }
}
