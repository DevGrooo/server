<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundProductBuyHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_product_buy_history', function (Blueprint $table) {
            $table->id();
            $table->integer('investor_id')->index()->comment('Nhà đầu tư');
            $table->integer('fund_distributor_id')->index()->comment('Đại lý phân phối');
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->integer('fund_certificate_id')->index()->comment('Chứng chỉ quỹ');
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ');
            $table->integer('investor_fund_product_id')->index();
            $table->integer('tranding_order_id')->unique()->comment('Lệnh mua CCQ');
            $table->double('buy_amount', 10, 2)->comment('Số chứng chỉ quy mua');
            $table->double('sell_amount', 10, 2)->comment('Số CCQ đã bán');
            $table->double('current_amount', 10, 2)->comment('Số CCQ còn lại');
            $table->string('currency', 50)->comment('Loại tiền tệ/mã sản phẩm quỹ');
            $table->integer('status')->comment('Trạng thái(1:Chưa bán hết, 2:Đã bán hết)');
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
        Schema::dropIfExists('fund_product_buy_history');
    }
}
