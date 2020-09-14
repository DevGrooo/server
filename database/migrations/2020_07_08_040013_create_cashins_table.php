<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashins', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->integer('fund_certificate_id')->index()->comment('Quỹ');
            $table->integer('fund_distributor_id')->index()->comment('Đại lý phân phối');
            $table->integer('fund_distributor_bank_account_id')->index()->comment('Tài khoản ngân hàng');            
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ');
            $table->integer('fund_product_type_id')->index()->comment('Loại sản phẩm quỹ');
            $table->integer('supervising_bank_id')->index()->comment('Ngân hàng giám sát');
            $table->integer('deposit_account_system_id')->index()->comment('Tài khoản nạp');
            $table->integer('investor_id')->index()->nullable()->comment('Nhà đầu tư');
            $table->integer('target_account_system_id')->index()->nullable()->comment('Tài khoản đích');
            $table->integer('statement_id')->index()->nullable()->comment('Sao kê');
            $table->double('amount', 10, 2)->comment('Số tiền nạp đã trừ phí');
            $table->double('amount_not_perform', 10, 2)->comment('Số tiền chưa chuyển ngân cho NĐT');
            $table->double('amount_available', 10, 2)->comment('Số tiền cho phép đối chiếu');
            $table->double('amount_freezing', 10, 2)->comment('Số tiền đóng băng do đang đối chiếu');
            $table->double('fee', 10, 2)->nullable()->comment('Phí nạp tiền');
            $table->string('currency', 10)->comment('Loại tiền tệ');
            $table->string('receipt', 255)->nullable()->comment('Chứng từ thu');
            $table->datetime('bank_paid_at')->nullable()->comment('Thời gian thanh toán trên ngân hàng');
            $table->text('bank_trans_note')->nullable()->comment('Ghi chú trong nội dung chuyển khoản trên GD ngân hàng');
            $table->integer('status')->comment('Trạng thái(1:Mới tạo,2:Đã thanh toán,3:Đã hủy,4:Đã chuyển ngân)');
            $table->datetime('paid_at')->nullable()->comment('Thời gian thanh toán');
            $table->datetime('perform_at')->nullable()->comment('Thời gian chuyển ngân');
            $table->integer('created_by')->nullable()->comment('Người tạo');
            $table->integer('updated_by')->nullable()->comment('Người cập nhật');
            $table->integer('paid_by')->nullable()->comment('Người cập nhật thanh toán');
            $table->integer('perform_by')->nullable()->comment('Người cập nhật chuyển ngân');
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
        Schema::dropIfExists('cashins');
    }
}
