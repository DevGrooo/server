<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundProductTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('Tên loại sản phẩm quỹ');
            $table->string('code', 50)->unique()->comment('Mã loại sản phẩm');
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
        Schema::dropIfExists('fund_product_types');
    }
}
