<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_commissions', function (Blueprint $table) {
            $table->id();
            $table->integer('ref_id')->index()->comment('ID tham chiếu');
            $table->string('ref_type', 100)->comment('Loại tham chiếu');
            $table->double('balance', 10, 2)->comment('Số dư');
            $table->double('balance_available', 10, 2)->comment('Số dư khả dụng');
            $table->double('balance_freezing', 10, 2)->comment('Số dư đóng băng');
            $table->double('balance_waiting', 10, 2)->comment('Số dư chờ nhận');
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
        Schema::dropIfExists('account_commissions');
    }
}
