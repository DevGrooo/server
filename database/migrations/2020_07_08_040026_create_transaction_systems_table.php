<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_systems', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('Loại giao dịch(1:Nạp,2:Chuyển,3:Rút)');
            $table->integer('ref_id')->index()->comment('ID tham chiếu');
            $table->string('ref_type', 50)->comment('Loại tham chiếu(1:TradingOrder,2:Cashin,3:Cashout)');
            $table->integer('send_account_system_id')->index()->comment('Tài khoản chuyển');
            $table->integer('receive_account_system_id')->index()->comment('Tài khoản nhận');
            $table->double('amount', 10, 2)->comment('Số tiền chuyển');
            $table->double('send_fee', 10, 2)->comment('Phí giao dịch người chuyển chịu');
            $table->double('receive_fee', 10, 2)->comment('Phí giao dịch người nhận chịu');
            $table->string('currency', 10)->comment('Loại tiền tệ');
            $table->integer('refer_transaction_system_id')->index()->nullable()->comment('Mã giao dịch tham chiếu');
            $table->integer('status')->comment('Trạng thái(1:Mới tạo,2:Đã xác nhận đang tạm giữ,3:Đã hủy,4:Đã hoàn thành)');
            $table->datetime('verified_at')->nullable()->comment('Thời gian xác thực');
            $table->datetime('cancelled_at')->nullable()->comment('Thời gian hủy');
            $table->datetime('performed_at')->nullable()->comment('Thời gian hoàn thành');
            $table->integer('created_by')->nullable()->comment('Người tạo');
            $table->integer('updated_by')->nullable()->comment('Người cập nhật');
            $table->integer('verified_by')->nullable()->comment('Người xác thực');
            $table->integer('cancelled_by')->nullable()->comment('Người hủy');
            $table->integer('performed_by')->nullable()->comment('Người hoàn thành');
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
        Schema::dropIfExists('transaction_systems');
    }
}
