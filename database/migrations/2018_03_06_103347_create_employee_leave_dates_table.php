<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeLeaveDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_leave_dates', function(Blueprint $table) {
            $table->increments('id',true);
            $table->integer('employee_leave_id')->unsigned();
            $table->date('date');
            // $table->dateTime('starttime');
            $table->float('leave_credit')->unsigned();
            $table->foreign('employee_leave_id')->references('id')->on('employee_leaves');
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
        //
    }
}
