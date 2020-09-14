<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_commissions', function (Blueprint $table) {
            $table->id();
            $table->integer('ref_id')->index()->comment('ID tham chiếu');
            $table->string('ref_type', 100)->comment('Loại tham chiếu');
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->integer('fund_certificate_id')->index()->comment('Chứng chỉ quỹ');
            $table->integer('fund_product_id')->index()->comment('Sản phẩm quỹ');
            $table->datetime('start_at')->index()->comment('Thời gian áp dụng');
            $table->datetime('end_at')->nullable()->comment('Thời gian kết thúc');
            $table->integer('status');
            $table->datetime('requested_at')->nullable()->comment('Thời gian yêu cầu');
            $table->datetime('rejected_at')->nullable()->comment('Thời gian từ chối');
            $table->datetime('accepted_at')->nullable()->comment('Thời gian duyệt');
            $table->datetime('locked_at')->nullable()->comment('Thời gian khóa');
            $table->integer('created_by')->nullable()->comment('Người tạo');
            $table->integer('updated_by')->nullable()->comment('Người cập nhật');
            $table->integer('requested_by')->nullable()->comment('Người yêu cầu');
            $table->integer('rejected_by')->nullable()->comment('Người từ chối');
            $table->integer('accepted_by')->nullable()->comment('Người duyệt');
            $table->integer('locked_by')->nullable()->comment('Người khóa');
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
        Schema::dropIfExists('setting_commissions');
    }
}
