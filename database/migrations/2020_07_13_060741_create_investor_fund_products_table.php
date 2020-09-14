<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorFundProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_fund_products', function (Blueprint $table) {
            $table->id();
            $table->integer('investor_id')->index()->comment('Nhà đầu tư');
            $table->integer('fund_distributor_id')->index()->comment('Đại lý phân phối');
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->integer('fund_certificate_id')->index()->comment('Chứng chỉ quỹ');
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ');
            $table->double('balance', 10, 2)->comment('Số dư chứng chỉ quỹ');
            $table->double('balance_available', 10, 2)->comment('Số dư khả dụng');
            $table->double('balance_freezing', 10, 2)->comment('Số dư đóng băng');
            $table->string('currency', 50)->comment('Loại tiền tệ/Mã CCQ');
            $table->integer('status')->comment('Trạng thái(1:Đang sử dụng,2:Đang khóa)');
            $table->integer('created_by')->nullable()->comment('Người tạo');
            $table->integer('update_by')->nullable()->comment('Người cập nhật');
            $table->unique(['investor_id', 'fund_product_id']);
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
        Schema::dropIfExists('investor_fund_products');
    }
}
