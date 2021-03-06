<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_type', function(Blueprint $table) {
            $table->increments('id',true);
            $table->string('code')->unique();
            $table->string('form');
            $table->integer('is_leave')->unsigned()->default(0);
            $table->text('description')->nullable();
            $table->integer('for_women')->unsigned()->default(1);
            $table->integer('for_men')->unsigned()->default(1);
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
        Schema::dropIfExists('form_type');
    }
}
