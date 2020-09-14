<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundDistributorProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_distributor_products', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->integer('fund_distributor_id')->index()->comment('Đại lý phân phối');
            $table->integer('fund_certificate_id')->index()->comment('Quỹ');
            $table->integer('fund_product_type_id')->index()->comment('Loại sản phẩm quỹ');
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ');
            $table->integer('fund_distributor_bank_account_id')->index()->nullable()->comment('Tài khoản ngân hàng');
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
        Schema::dropIfExists('fund_distributor_products');
    }
}
