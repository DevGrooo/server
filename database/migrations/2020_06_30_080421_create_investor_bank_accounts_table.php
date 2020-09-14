<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_id')->index();
            $table->integer('fund_company_id')->index();
            $table->integer('fund_distributor_id')->index();
            $table->integer('investor_id')->index();
            $table->string('account_holder')->nullable();
            $table->string('account_number')->nullable();
            $table->string('branch')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('investor_bank_accounts');
    }
}
