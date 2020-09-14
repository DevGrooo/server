<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundDistributorStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_distributor_staffs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fund_company_id')->index();
            $table->integer('fund_distributor_id')->index();
            $table->string('name');
            $table->string('certificate_number')->nullable();
            $table->datetime('issuing_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->tinyInteger('status');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('fund_distributor_staffs');
    }
}
