<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldDefaultInvestorBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investor_bank_accounts', function (Blueprint $table) {
            $table->integer('is_default')->after('description')->comment('1:Mặc định,2:Không');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investor_bank_accounts', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
}
