<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statement_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('statement_id')->index()->comment('Sao kê');
            $table->integer('fund_company_id')->index();
            $table->integer('fund_distributor_id')->index();
            $table->integer('supervising_bank_id')->index();
            $table->integer('investor_id')->index()->nullable();
            $table->integer('fund_product_id')->index()->nullable();
            $table->longtext('data')->nullable()->comment('Dữ liệu dòng');
            $table->longtext('data_result')->nullable()->comment('Dữ liệu sau khi xử lý');
            $table->text('bank_trans_note')->nullable()->comment('Ghi chú của khách hàng');
            $table->datetime('bank_paid_at')->nullable();
            $table->integer('cashin_id')->index()->nullable()->comment('Phiếu thu');
            $table->string('cashin_receipt', 255)->nullable()->comment('Chứng từ thu');
            $table->text('warnings')->nullable()->comment('Cảnh báo kết quả xử lý');
            $table->text('errors')->nullable()->comment('Lỗi kết quả xử lý');
            $table->integer('status')->comment('Trạng thái xử lý(1:Chưa xử lý,2:Đang xử lý,3:Lỗi hoặc thiếu thông tin,4:Đã hoàn thành)');
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
        Schema::dropIfExists('statement_lines');
    }
}
