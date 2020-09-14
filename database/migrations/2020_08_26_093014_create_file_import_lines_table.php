<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileImportLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_import_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('file_import_id')->index()->comment('Sao kê');
            $table->text('data')->nullable()->comment('Dữ liệu dòng');
            $table->text('data_result')->nullable()->comment('Dữ liệu sau khi xử lý');
            $table->text('warnings')->nullable()->comment('Cảnh báo kết quả xử lý');
            $table->text('errors')->nullable();
            $table->integer('status')->comment('Trạng thái xử lý(1:Chưa xử lý,2:Đang xử lý,3:Lỗi hoặc thiếu thông tin,4:Đã hoàn thành)');
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
        Schema::dropIfExists('file_import_lines');
    }
}
