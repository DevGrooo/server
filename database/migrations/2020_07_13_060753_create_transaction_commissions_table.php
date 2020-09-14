<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_commissions', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('Loại giao dịch ghi nhận hoa hồng(1:Phí duy trì,2:Phí mua)');
            $table->integer('ref_id')->index()->comment('ID tham chiếu');
            $table->string('ref_type', 100)->comment('Loại tham chiếu');
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->integer('fund_certificate_id')->index()->comment('Chứng chỉ quỹ');
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ');
            $table->integer('trading_order_id')->index()->nullable()->comment('Lệnh mua CCQ');
            $table->integer('trading_session_id')->index()->nullable()->comment('Phiên giao dịch');
            $table->integer('setting_commission_id')->index()->comment('Cấu hình hoa hồng sử dụng');
            $table->integer('send_account_commission_id')->index()->comment('Tài khoản chuyển');
            $table->integer('receive_account_commission_id')->index()->comment('Tài khoản nhận');
            $table->double('amount', 10, 2)->comment('Số tiền giao dịch');
            $table->string('currency', 10)->comment('Đơn vị tiền tệ');
            $table->integer('status')->comment('Trạng thái(1:Mới tạp,2:Đã xác nhận,3:Đã hủy,4:Đã thực hiện)');
            $table->datetime('canceled_at')->nullable()->comment('Thời gian hủy');
            $table->datetime('verified_at')->nullable();
            $table->datetime('performed_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('canceled_by')->nullable();
            $table->integer('verified_by')->nullable();
            $table->integer('performed_by')->nullable();
            $table->unique(['fund_product_id', 'trading_order_id', 'trading_session_id'], 'trans_commission_unique');
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
        Schema::dropIfExists('transaction_commissions');
    }
}
