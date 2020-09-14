<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_company_id')->index()->comment('Công ty quản lý quỹ');
            $table->integer('fund_distributor_id')->index()->comment('Đại lý phân phối');
            $table->integer('supervising_bank_id')->index()->comment('Ngân hàng giám sát');
            $table->string('file_name', 255)->comment('Tên file import');
            $table->text('file_path')->comment('Đường dẫn file import');
            $table->integer('total_line')->nullable()->comment('Tổng số dòng');
            $table->integer('processed_line')->nullable()->comment('Số dòng đã xử lý được');
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
        Schema::dropIfExists('statements');
    }
}
