<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_products', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');;
            $table->integer('fund_certificate_id')->inde()->comment('Chứng chỉ quỹ');
            $table->integer('fund_product_type_id')->index()->comment('Loại sản phẩm quỹ');;
            $table->string('name', 255)->comment('Tên sản phẩm quỹ');
            $table->string('code', 255)->unique()->comment('Mã sản phẩm');
            $table->integer('frequency_type')->nullable()->comment('Tần xuất(1:Mỗi tháng 1 lần)');
            $table->integer('minimum_period')->nullable()->comment('Số tháng tối thiểu');     
            $table->text('description')->nullable()->comment('Mô tả');
            $table->integer('status')->comment('Trạng thái(1:Đang sử dụng,2:Đang khóa)');
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
        Schema::dropIfExists('fund_products');
    }
}
