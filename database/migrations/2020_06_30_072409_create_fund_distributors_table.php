<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundDistributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_distributors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fund_company_id')->index();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('license_number')->nullable();
            $table->datetime('issuing_date')->nullable();
            $table->text('head_office')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('website')->nullable();
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
        Schema::dropIfExists('fund_distributors');
    }
}
