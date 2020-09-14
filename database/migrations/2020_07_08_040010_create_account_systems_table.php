<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_systems', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_product_type_id')->index()->comment('Loại sản phẩm quỹ');
            $table->string('ref_type', 100)->comment('Loại tham chiếu(investor|fund_distributor)');
            $table->integer('ref_id')->index()->comment('ID tham chiếu');
            $table->double('balance', 20, 2)->comment('Số dư tài khoản');
            $table->double('balance_available', 20, 2)->comment('Số dư khả dụng');
            $table->double('balance_freezing', 20, 2)->comment('Số dư đóng băng');
            $table->string('currency', 10)->comment('Loại tiền tệ');
            $table->integer('status')->comment('Trạng thái(1:Đang sử dụng,2:Đang khóa)');
            $table->integer('created_by')->nullable()->comment('Người tạo');
            $table->integer('updated_by')->nullable()->comment('Người cập nhật');
            $table->unique(['fund_product_type_id', 'ref_type', 'ref_id']);
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
        Schema::dropIfExists('account_systems');
    }
}
