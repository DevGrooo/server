<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashoutRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashout_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('Loại yêu cầu rút tiền(1:Từ giao dịch bán,2:Tiền thừa giao dịch mua)');
            $table->integer('transaction_system_id')->index()->comment('Giao dịch rút tiền');
            $table->double('amount', 10, 2)->comment('Số tiền rút');
            $table->integer('bank_id')->index()->comment('Ngân hàng rút tiền');
            $table->string('account_holder', 255)->comment('Tển chủ tài khoản người nhận');
            $table->string('account_number', 50)->comment('Số tài khoản người nhận');
            $table->string('branch', 255)->nullable()->comment('Chi nhánh ngân hàng');
            $table->text('description')->nullable()->comment('Ghi chú');
            $table->integer('status')->comment('Trạng thái(1:Đợi duyệt,2:Từ chối,3:Duyệt,4:Đã chi tiền)');
            $table->datetime('accepted_at')->nullable()->comment('Thời gian duyệt');
            $table->datetime('rejected_at')->nullable()->comment('Thời gian từ chối');
            $table->integer('created_by')->nullable()->comment('Người tạo');
            $table->integer('updated_by')->nullable()->comment('Người cập nhật');
            $table->integer('accepted_by')->nullable()->comment('Người duyệt');
            $table->integer('rejected_by')->nullable()->comment('Người từ chối');
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
        Schema::dropIfExists('cashout_requests');
    }
}
