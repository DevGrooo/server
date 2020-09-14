<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_funds', function (Blueprint $table) {
            $table->id();
            $table->integer('file_import_id')->index()->comment('File import lệnh');
            $table->integer('file_import_line_id')->index();
            $table->integer('trading_order_id')->index();
            $table->integer('fund_company_id')->index();
            $table->integer('fund_distributor_id')->index()->comment('Đại lý phân phối');
            $table->integer('fund_distributor_bank_account_id')->index()->comment('TK Ngân hàng đại lý phân phối');
            $table->integer('investor_id')->index()->comment('Nhà đầu tư');
            $table->integer('investor_id_type')->comment('Loại ĐKSH(1:CMND,2:Hộ chiếu,3:GĐKKD)');
            $table->string('investor_id_number', 50)->comment('Số ĐKSH');
            $table->integer('trading_frequency_id')->index()->comment('Tần xuất giao dịch');
            $table->integer('trading_session_id')->index()->comment('Phiên giao dịch');
            $table->string('trading_account_number', 50)->comment('Số hiệu TKGD');
            $table->integer('investor_sip_id')->index()->nullable()->comment('Lệnh SIP');
            $table->string('deal_type', 10)->comment('Mã loại lệnh(NS:Mua,NR:Bán:NP:Mua SIP,IP:IPO,SW:Hoán đổi)');
            $table->integer('exec_type')->comment('Loại lệnh(1:Lệnh mua,2:Lệnh bán,3:Lệnh hoán đổi)');
            $table->integer('send_fund_certificate_id')->index()->nullable();
            $table->integer('send_fund_product_id')->index()->nullable()->comment('Sản phẩm quỹ bán');
            $table->integer('send_fund_product_type_id')->index()->nullable();
            $table->integer('send_investor_fund_product_id')->index()->nullable()->comment('Tài khoản sản phẩm quỹ người chuyển');
            $table->double('send_amount', 10, 2)->comment('Số tiền/Chứng chỉ quỹ bán');
            $table->double('send_match_amount', 10, 2)->nullable()->comment('Số tiền/CCCQ được khớp lệnh');
            $table->string('send_currency', 50)->comment('Loại tiền tệ/Mã CCQ');
            $table->integer('receive_fund_certificate_id')->index()->nullable();
            $table->integer('receive_fund_product_id')->index()->nullable()->comment('Sản phẩm quỹ nhận');
            $table->integer('receive_fund_product_type_id')->index()->nullable()->comment('Loại chứng chỉ quỹ nhận');
            $table->integer('receive_investor_fund_product_id')->index()->nullable()->comment('Tài khoản sản phẩm quỹ người nhận');
            $table->double('receive_amount', 10, 2)->nullable()->comment('Số tiền/Chứng chỉ quỹ nhận được');
            $table->double('receive_match_amount', 10, 2)->nullable()->comment('Số tiền/CCQ thực nhận');
            $table->string('receive_currency', 50)->nullable()->comment('Loại tiền tệ/Mã CCQ');
            $table->double('fee', 10, 2)->nullable()->comment('Phí giao dịch');
            $table->double('fee_send', 10, 2)->nullable()->comment('Phí giao dịch cho lệnh bán');
            $table->double('fee_receive', 10, 2)->nullable()->comment('Phí giao dịch cho lệnh mua');
            $table->double('tax', 10, 2)->nullable()->comment('Thuế');
            $table->string('vsd_trading_id', 50)->index()->nullable()->comment('ID lênh trên VSD');
            $table->integer('vsd_time_received')->nullable()->comment('Thời gian nhận lệnh');
            $table->double('nav', 10, 2)->nullable()->comment('Giá trị ròng trên từng CCQ');
            $table->double('total_nav', 10, 2)->nullable()->comment('Giá trị tài sản ròng của quỹ');
            $table->datetime('created_date')->comment('Ngày đăng ký giao dịch');
            $table->integer('status')->comment('Trạng thái lệnh FMC(1:Mới tạo,2:Đã xác nhận,3:Đã hủy,4:Hoàn thành)');
            $table->text('reason_cancel')->nullable()->comment('Lý do hủy');
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
        Schema::dropIfExists('transaction_funds');
    }
}
