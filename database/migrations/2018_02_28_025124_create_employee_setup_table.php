<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSetupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_setup', function (Blueprint $table) {
            $table->increments('id', true);
            $table->integer('employee_id')->unsigned();
            $table->decimal('salary', 16, 2)->nullable();
            $table->date('date_hired');
            $table->integer('department_id')->nullable()->unsigned();
            $table->integer('division_id')->nullable()->unsigned();
            $table->integer('role_id')->unsigned();
            $table->integer('approver_id')->unsigned();
            $table->integer('reports_to_id')->unsigned();
            $table->integer('shift_id')->unsigned();
            $table->string('position',255)->nullable();
            $table->string('job_title',255)->nullable();
            $table->float('vl_credits')->default(0);
            $table->float('sl_credits')->default(0);
            $table->float('bil_credits')->default(0);
            $table->float('el_credits')->default(0);
            $table->foreign('shift_id')->references('id')->on('shift');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('department_id')->references('id')->on('department');
            $table->foreign('division_id')->references('id')->on('division');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('approver_id')->references('id')->on('employees');
            $table->foreign('reports_to_id')->references('id')->on('employees');
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
        Schema::dropIfExists('employee_setup');
    }
}
