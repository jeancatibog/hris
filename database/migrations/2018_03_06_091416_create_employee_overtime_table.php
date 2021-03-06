<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeOvertimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_overtime', function(Blueprint $table) {
            $table->increments('id',true);
            $table->integer('employee_id')->unsigned();
            $table->text('reason')->nullable();
            $table->date('date');
            $table->dateTime('datetime_from');
            $table->dateTime('datetime_to');
            $table->integer('form_status_id')->unsigned();
            $table->text('approvers_remarks')->nullable();
            $table->date('date_approved')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('form_status_id')->references('id')->on('form_status');
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
        Schema::dropIfExists('employee_overtime');
    }
}
