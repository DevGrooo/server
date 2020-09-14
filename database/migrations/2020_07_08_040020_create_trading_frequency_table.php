<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingFrequencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_frequency', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_company_id')->index();
            $table->integer('type')->comment('Loại(1:Theo ngày trong tuần,2:Theo ngày trong tháng)');
            $table->string('name', 255)->comment('Tên tần xuất giao dịch');
            $table->integer('wday')->nullable()->comment('Ngày trong tuần, 0=CN, 6=T7');
            $table->integer('mday')->nullable()->comment('Ngày trong tháng');
            $table->integer('cut_off_date')->comment('Ngày giới hạn');
            $table->string('cut_off_hour', 5)->comment('Giờ giới hạn(VD: 13:00)');
            $table->string('cut_off_time', 50)->comment('Thời gian giới hạn(VD: 11 am on T-1)');
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
        Schema::dropIfExists('trading_frequency');
    }
}
