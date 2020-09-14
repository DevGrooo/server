<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingCommissionLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_commission_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('setting_commission_id')->index();
            $table->integer('ref_id')->index();
            $table->string('ref_type', 100)->comment('Loại tham chiếu');
            $table->integer('fund_company_id')->index();
            $table->integer('fund_certificate_id')->index();
            $table->integer('fund_product_id')->index();
            $table->datetime('start_at');
            $table->datetime('end_at')->nullable();            
            $table->double('min_amount', 10, 2);
            $table->float('sell_commission');
            $table->float('maintance_commission_amount');            
            $table->integer('status');
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
        Schema::dropIfExists('setting_commission_lines');
    }
}
