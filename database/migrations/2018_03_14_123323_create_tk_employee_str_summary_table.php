<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTkEmployeeStrSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tk_employee_dtr_summary', function(Blueprint $table) {
            $table->increments('id',true);
            $table->integer('period_id')->unsigned();
            $table->integer('employee_id')->unsigned();
            $table->integer('shift_id')->unsigned();
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('hours_work', 5,2)->nullable();
            $table->decimal('late', 5,2)->nullable();
            $table->decimal('undertime', 5,2)->nullable();
            $table->decimal('ot_hours', 5,2)->nullable();
            $table->tinyInteger('absent')->nullable();
            $table->tinyInteger('leave')->nullable();
            $table->string('leave_type')->nullable();
            $table->foreign('period_id')->references('id')->on('tk_period');
            $table->foreign('shift_id')->references('id')->on('shift');
            $table->foreign('employee_id')->references('id')->on('employees');
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
        Schema::dropIfExists('tk_employee_dtr_summary');
    }
}
