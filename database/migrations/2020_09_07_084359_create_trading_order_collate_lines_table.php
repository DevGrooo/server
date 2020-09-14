<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingOrderCollateLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_order_collate_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('trading_order_collate_id')->index();
            $table->integer('trading_order_id')->index();
            $table->integer('cashin_id')->index();
            $table->double('cashin_amount', 10, 2);
            $table->double('overpayment', 10, 2);
            $table->string('currency', 10)->comment('Loại tiền tệ');
            $table->text('bank_trans_note')->nullable()->comment('Nội dung chuyển khoản trong phiếu thu');
            $table->integer('deposit_transaction_system_id')->index()->nullable()->comment('Giao dịch nạp tiền vào ví NĐT');
            $table->integer('status')->comment('Trạng thái(1:Mới tạo,2:Đã xác nhận,3:Đã hủy)');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->unique(['trading_order_id', 'cashin_id'], 'trading_order_cashin');
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
        Schema::dropIfExists('trading_order_collate_lines');
    }
}
