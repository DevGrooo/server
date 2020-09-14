<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorSipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_sips', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_company_id')->index();
            $table->integer('fund_certificate_id')->index()->comment('Chứng chỉ quỹ');
            $table->integer('fund_product_type_id')->index()->comment('Loại sản phẩm quỹ');
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ');
            $table->integer('fund_distributor_id')->index()->comment('Đại lý phân phối');
            $table->integer('investor_id')->index()->comment('Nhà đầu tư');
            $table->integer('payment_type')->comment('Hình thức thanh toán(1:Cố định)');
            $table->double('periodic_amount', 10, 2)->comment('Số tiền thanh toán định kỳ');
            $table->datetime('start_at')->comment('Thời gian bắt đầu mua SIP');
            $table->text('reason_close')->nullable()->comment('Lý do đóng');
            $table->integer('status')->comment('Trạng thái(1:Đang sử dụng,2:Đang tạm dừng,3:Đã đóng)');
            $table->integer('created_by')->nullable()->comment('Người tạo');
            $table->integer('updated_by')->nullable()->comment('Người cập nhật');
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
        Schema::dropIfExists('investor_sips');
    }
}
