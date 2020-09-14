<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('previous_trading_session_id')->index()->comment('Phiên giao dịch trước');
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->integer('trading_frequency_id')->index()->comment('Tần xuất giao dịch');
            $table->string('code', 255)->unique()->comment('Mã phiên');
            $table->datetime('start_at')->index()->comment('Thời gian bắt đầu phiên');
            $table->datetime('end_at')->index()->nullable()->comment('Thời gian kết thúc phiên');
            $table->datetime('limit_order_at')->index()->nullable()->comment('Thời hạn đặt lệnh');
            $table->double('nav', 10, 2)->nullable()->comment('Chỉ số NAV');
            $table->integer('status')->comment('Trạng thái(1:Đang sử dụng,2:Hết thời gian đặt lệnh,3:Đã kết thúc,4:Đã hủy)');
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
        Schema::dropIfExists('trading_sessions');
    }
}
