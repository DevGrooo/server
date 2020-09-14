<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_imports', function (Blueprint $table) {
            $table->id();
            $table->string('type', 255)->comment('Loại(Nhà đầu tư, lệnh giao dịch)');
            $table->string('file_name', 255)->comment('Tên file import');
            $table->text('file_path')->comment('Đường dẫn file import');
            $table->integer('total_line')->nullable()->comment('Tổng số dòng');
            $table->integer('processed_line')->nullable()->comment('Số dòng đã xử lý được');
            $table->integer('total_error')->nullable()->comment('Số lỗi khi import');
            $table->integer('total_warning')->nullable()->comment('Số cảnh báo khi import');
            $table->integer('status')->comment('Trạng thái(1:Chưa xử lý,2:Đang xử lý,3:Đã xử lý)');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('file_imports');
    }
}
