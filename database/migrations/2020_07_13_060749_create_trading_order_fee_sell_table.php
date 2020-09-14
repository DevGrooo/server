<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingOrderFeeSellTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_order_fee_sells', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->integer('fund_certificate_id')->index()->comment('Chứng chỉ quỹ');
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ');
            $table->integer('fund_distributor_id')->index()->nullable()->comment('Đại lý phân phối');
            $table->integer('investor_id')->index()->nullable()->comment('Nhà đầu tư');
            $table->datetime('start_at')->comment('Thời gian áp dụng');
            $table->datetime('end_at')->nullable()->comment('Thời gian kết thúc');
            $table->integer('holding_period')->comment('Số ngày giữ quỹ tối thiểu');
            $table->double('fee_amount', 10, 2)->comment('Phí cố định');
            $table->float('fee_percent')->comment('Phí phần trăm');
            $table->integer('status')->comment('Trạng thái(1:Mới tạo,2:Đợi duyệt,3:Từ chối,4:Đã duyệt,5:Đã khóa)');
            $table->datetime('requested_at')->nullable()->comment('Thời gian yêu cầu');
            $table->datetime('rejected_at')->nullable()->comment('Thời gian từ chối');
            $table->datetime('accepted_at')->nullable()->comment('Thời gian duyệt');
            $table->datetime('locked_at')->nullable()->comment('Thời gian khóa');
            $table->integer('created_by')->nullable()->comment('Người tạo');
            $table->integer('updated_by')->nullable()->comment('Người cập nhật');
            $table->integer('requested_by')->nullable()->comment('Người yêu cầu');
            $table->integer('rejected_by')->nullable()->comment('Người từ chối');
            $table->integer('accepted_by')->nullable()->comment('Người duyệt');
            $table->integer('locked_by')->nullable()->comment('Người khóa');
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
        Schema::dropIfExists('trading_order_fee_sells');
    }
}
