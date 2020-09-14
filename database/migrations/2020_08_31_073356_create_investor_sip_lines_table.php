<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorSipLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_sip_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('investor_sip_id')->index();
            $table->integer('fund_company_id')->index();
            $table->integer('fund_certificate_id')->index();
            $table->integer('fund_product_type_id')->index();
            $table->integer('fund_product_id')->index();
            $table->integer('fund_distributor_id')->index();
            $table->integer('investor_id')->index();
            $table->integer('period_index');
            $table->datetime('period_start_at')->nullable();
            $table->datetime('period_end_at')->nullable();
            $table->double('amount', 10, 2);
            $table->integer('trading_session_id')->index()->nullable()->comment('Phiên giao dịch');
            $table->integer('trading_order_id')->index()->nullable()->comment('Lệnh giao dịch');
            $table->integer('status')->comment('Trạng thái(1:Chưa tạo lệnh,2:Đã tạo lệnh chưa thanh toán,3:Đã thanh toán)');
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
        Schema::dropIfExists('investor_sip_lines');
    }
}
