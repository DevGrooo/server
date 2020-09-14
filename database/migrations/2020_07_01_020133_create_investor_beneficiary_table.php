<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorBeneficiaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_beneficiary', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('investor_id')->index();
            $table->string('be_name')->nullable();
            $table->datetime('be_birthday')->nullable();
            $table->integer('be_gender')->nullable();
            $table->integer('be_id_type')->nullable();
            $table->string('be_id_number', 50)->nullable();
            $table->datetime('be_id_issuing_date')->nullable();
            $table->string('be_id_issuing_place')->nullable();
            $table->datetime('be_id_expiration_date')->nullable();
            $table->text('be_permanent_address')->nullable();
            $table->integer('be_permanent_country_id')->index()->nullable();
            $table->text('be_current_address')->nullable();
            $table->integer('be_current_country_id')->index()->nullable();
            $table->string('be_phone', 50)->nullable();
            $table->string('be_email')->nullable();
            $table->string('be_tax_id', 100)->index()->nullable();
            $table->integer('be_tax_country_id')->index()->nullable();
            $table->string('be_visa_number', 50)->nullable();
            $table->datetime('be_visa_issuing_date')->nullable();
            $table->string('be_visa_issuing_place', 50)->nullable();
            $table->text('be_temporary_address')->nullable();
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
        Schema::dropIfExists('investor_beneficiary');
    }
}
