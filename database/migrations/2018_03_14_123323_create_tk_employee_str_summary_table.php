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
            $table->integer('shift_id')->unsigned();
            $table->date('date');
            $table->time('time_in');
            $table->time('time_out');
            $table->decimal('hours_work', 5,2);
            $table->decimal('late', 5,2)->nullable();
            $table->decimal('undertime', 5,2)->nullable();
            $table->decimal('ot_hours', 5,2)->nullable();
            $table->tinyInteger('absent')->nullable();
            $table->tinyInteger('leave')->nullable();
            $table->foreign('shift_id')->references('id')->on('shift');
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
