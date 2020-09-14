<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashouts', function (Blueprint $table) {
            $table->id();
            $table->integer('cashout_request_id')->index()->comment('Yêu cầu rút');
            $table->integer('transaction_system_id')->index()->comment('Giao dịch');
            $table->double('amount', 10, 2)->comment('Số tiền rút chưa có phí');
            $table->double('fee', 10, 2)->comment('Phí rút tiền');
            $table->integer('bank_id')->index()->comment('Ngân hàng');
            $table->string('account_holder', 255)->comment('Tên chủ TK ngân hàng');
            $table->string('account_number', 50)->comment('Số TK ngân hàng');
            $table->string('branch', 255)->nullable()->comment('Chi nhánh');
            $table->string('cashout_receipt', 50)->nullable()->comment('Mã chứng từ chi');
            $table->integer('status')->comment('Trạng thái(1:Chưa chi tiền,2:Đã chi tiền,3:Lỗi)');
            $table->datetime('performed_at')->nullable()->comment('Thời gian thực hiện');
            $table->integer('created_by')->nullable()->comment('Người tạo');
            $table->integer('updated_by')->nullable()->comment('Người cập nhật');
            $table->integer('performed_by')->nullable()->comment('Người thực hiện');
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
        Schema::dropIfExists('cashouts');
    }
}
