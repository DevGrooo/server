<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingOrderCollatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_order_collates', function (Blueprint $table) {
            $table->id();
            $table->integer('trading_order_id')->unique();
            $table->double('trading_order_amount', 10, 2);
            $table->double('total_cashin_amount', 10, 2)->nullable()->comment('Tổng số tiền sử dụng trong phiếu thu');
            $table->integer('investor_id')->index()->comment('NĐT');
            $table->integer('investor_account_system_id')->index()->comment('Tài khoản hệ thống của NĐT');
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ muốn mua');
            $table->integer('trading_session_id')->index()->comment('Phiên giao dịch');
            $table->double('balance', 10, 2)->nullable()->comment('Số dư ví sử dụng');
            $table->double('overpayment', 10, 2)->nullable()->comment('Số tiền thanh toán thừa');
            $table->string('currency', 10)->comment('Loại tiền tệ');
            $table->integer('comparison_result')->comment('Kết quả đối chiếu(1:Khớp,2:Không khớp)');            
            $table->integer('keep_for_next')->nullable()->comment('Sử dụng tiền thừa cho GD sau không(1:Có,2:Không)');
            $table->integer('withdraw_transaction_system_id')->index()->nullable()->comment('Giao dịch rút tiền khỏi TK NĐT');
            $table->integer('status')->comment('Trạng thái(1:Đợi xác nhận,2:Đã xác nhận)');
            $table->integer('created_by')->nullable()->comment('Tạo bởi');
            $table->integer('updated_by')->nullable()->comment('Cập nhật bởi');
            $table->datetime('verified_at')->nullable()->comment('Xác thực lúc');
            $table->integer('verified_by')->nullable()->comment('Xác thực bởi');
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
        Schema::dropIfExists('trading_order_collates');
    }
}
