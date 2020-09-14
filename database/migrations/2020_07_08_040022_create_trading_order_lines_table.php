<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingOrderLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_order_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('trading_order_id')->index()->comment('Lệnh bán/hoán đổi CCQ');
            $table->integer('trading_order_buy_id')->index()->comment('Lệnh mua CCQ mà được bán/hoán đổi');
            $table->double('sell_amount', 10, 2)->comment('Số CCQ bán');
            $table->double('fee', 10, 2)->comment('Phí giao dịch');
            $table->string('currency', 50)->comment('Mã CCQ');
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
        Schema::dropIfExists('trading_order_lines');
    }
}
