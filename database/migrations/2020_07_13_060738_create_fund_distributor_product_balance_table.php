<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundDistributorProductBalanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_distributor_product_balance', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_distributor_id')->index()->comment('Đại lý phân phối');
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ');
            $table->integer('fund_distributor_product_id')->index('fund_distributor_product_id')->comment('Sản phẩm quỹ của đại lý phân phối');
            $table->integer('trading_session_id')->index()->comment('Phiên giao dịch');
            $table->double('balance', 10, 2)->comment('Số dư');
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
        Schema::dropIfExists('fund_distributor_product_balance');
    }
}
