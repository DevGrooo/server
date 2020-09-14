<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_certificates', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->string('name', 255)->comment('Tên chứng chỉ quỹ');
            $table->string('code', 255)->unique()->comment('Mã chứng chỉ quỹ');       
            $table->text('description')->nullable()->comment('Mô tả');
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
        Schema::dropIfExists('fund_certificates');
    }
}
