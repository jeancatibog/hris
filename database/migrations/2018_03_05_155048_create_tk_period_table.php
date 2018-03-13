<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTkPeriodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tk_period', function (Blueprint $table) {
            $table->increments('id', true);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('status_id')->unsigned()->default(0);
            $table->foreign('status_id')->references('id')->on('tk_period_status');
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
        Schema::dropIfExists('tk_period');
    }
}
