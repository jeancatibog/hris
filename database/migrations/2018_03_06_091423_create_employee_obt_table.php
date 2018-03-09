<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeObtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_obt', function(Blueprint $table) {
            $table->increments('id',true);
            $table->integer('employee_id')->unsigned();
            $table->text('reason')->nullable();
            $table->date('date_from');
            $table->date('date_to');
            $table->dateTime('starttime');
            $table->dateTime('endtime');
            $table->integer('form_status_id')->unsigned();
            $table->string('contact_name')->nullable();
            $table->string('contact_info')->nullable();
            $table->string('contact_position')->nullable();
            $table->string('company_to_visit')->nullable();
            $table->string('company_location')->nullable();
            $table->text('approvers_remarks');
            $table->date('date_approved');
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
        Schema::dropIfExists('employee_obt');
    }
}
