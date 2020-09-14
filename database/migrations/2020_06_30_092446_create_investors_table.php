<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->integer('file_import_id')->index()->nullable()->comment('File import');
            $table->integer('fund_company_id')->index();
            $table->integer('fund_distributor_id')->index()->comment('Đại lý phân phối');
            $table->integer('fund_distributor_staff_id')->index()->nullable()->comment('Nhân viên đại lý');
            $table->integer('referral_bank_id')->index()->nullable()->comment('Ngân hàng giới thiệu');
            $table->string('trading_account_number', 255)->unique()->comment('Số TKGD');
            $table->string('trading_reference_number', 255)->nullable()->comment('Số tham chiếu');
            $table->integer('trading_account_type')->comment('Loại TKGD(1:Trực tiếp,...)');
            $table->string('name', 500)->comment('Tên nhà đầu tư');
            $table->integer('zone_type')->comment('Phân loại(1:Trong nước,2:Nước ngoài)');
            $table->integer('scale_type')->comment('Loại NĐT(1:Cá nhân,2:Tổ chức)');
            $table->integer('invest_type')->comment('Loại đầu tư(1:SIP,2:Thông thường)');
            $table->integer('country_id')->index()->nullable()->comment('Quốc tịch');
            $table->datetime('birthday')->nullable()->comment('Ngày sinh');
            $table->string('gender', 1)->nullable()->comment('Giới tính(M:Nam,F:Nữ,O:Khác)');
            $table->integer('id_type_id')->nullable()->comment('Loại giấy ĐKSH(1:CMND,2:Hộ chiếu,3:ĐKKD)');
            $table->string('id_number', 100)->nullable()->comment('Số giấy ĐKSH');
            $table->datetime('id_issuing_date')->nullable()->comment('Ngày cấp');
            $table->string('id_issuing_place', 255)->nullable()->comment('Nơi cấp');
            $table->datetime('id_expiration_date')->nullable()->comment('Ngày hết hạn');
            $table->string('permanent_address', 500)->nullable()->comment('Địa chỉ đăng ký thường trú/Trụ sở');
            $table->integer('permanent_country_id')->index()->nullable()->comment('Quốc gia');
            $table->string('current_address', 500)->nullable()->comment('Địa chỉ hiện tại');
            $table->integer('current_country_id')->index()->nullable()->comment('Quốc gia');
            $table->string('phone', 50)->nullable()->comment('Điện thoại');
            $table->string('fax', 50)->nullable()->comment('Fax');
            $table->string('email', 255)->nullable()->comment('Email');
            $table->string('tax_id', 100)->index()->nullable()->comment('Mã số thuế');
            $table->integer('tax_country_id')->index()->nullable()->comment('Quốc gia đóng thuế');
            $table->string('visa_number', 50)->nullable()->comment('Số thị thực nhập cảnh');
            $table->datetime('visa_issuing_date')->nullable()->comment('Ngày cấp Visa');
            $table->string('visa_issuing_place', 255)->nullable()->comment('Nơi cấp Visa');
            $table->text('temporary_address')->nullable()->comment('Địa chỉ tạm trú(với người nước ngoài)');
            $table->string('re_fullname', 255)->nullable()->comment('Tên người đại diện');
            $table->datetime('re_birthday')->nullable()->comment('Ngày sinh người đại diện');
            $table->integer('re_gender')->nullable()->comment('Giới tính(1:Nam,2:Nữ)');
            $table->string('re_position', 255)->nullable()->comment('Chức vụ');
            $table->integer('re_id_type_id')->nullable()->comment('Loại giấy tờ(1:CMND,2:Hộ chiếu)');
            $table->string('re_id_number', 50)->nullable()->comment('Số CMND/Hộ chiếu người đại diện');
            $table->datetime('re_id_issuing_date')->nullable()->comment('Ngày cấp');
            $table->string('re_id_issuing_place', 255)->nullable();
            $table->datetime('re_id_expiration_date')->nullable()->comment('Ngày hết hạn');
            $table->string('re_phone', 50)->nullable()->comment('Điện thoại');
            $table->text('re_address')->nullable()->comment('Địa chỉ');
            $table->integer('re_country_id')->index()->nullable()->comment('Quốc tịch');
            $table->string('au_fullname', 255)->nullable()->comment('Người được ủy quyền');
            $table->integer('au_id_type_id')->nullable()->comment('Loại giấy tờ(1:CMND,2:Hộ chiếu)');
            $table->string('au_id_number', 50)->nullable()->comment('Số CMND/Hộ chiếu');
            $table->datetime('au_id_issuing_date')->nullable()->comment('Ngày cấp');
            $table->string('au_id_issuing_place', 255)->nullable()->comment('Nơi cấp');
            $table->datetime('au_id_expiration_date')->nullable()->comment('Ngày hết hạn');
            $table->string('au_email', 255)->nullable()->comment('Email');
            $table->string('au_phone', 50)->nullable()->comment('Số điện thoại');
            $table->text('au_address')->nullable()->comment('Địa chỉ');
            $table->integer('au_country_id')->index()->nullable()->comment('Quốc gia');
            $table->datetime('au_start_date')->nullable()->comment('Ngày bắt đầu ủy quyền');
            $table->datetime('au_end_date')->nullable()->comment('Ngày kết thúc ủy quyền');
            $table->string('fatca_link_auth', 255)->nullable()->comment('Link AUTH');
            $table->string('fatca_recode', 255)->nullable()->comment('Recode');
            $table->text('fatca_funds')->nullable()->comment('Các mã quỹ');
            $table->integer('fatca1')->nullable()->comment('(1:Có,2:Không)');
            $table->integer('fatca2')->nullable()->comment('(1:Có,2:Không)');
            $table->integer('fatca3')->nullable()->comment('(1:Có,2:Không)');
            $table->integer('fatca4')->nullable()->comment('(1:Có,2:Không)');
            $table->integer('fatca5')->nullable()->comment('(1:Có,2:Không)');
            $table->integer('fatca6')->nullable()->comment('(1:Có,2:Không)');
            $table->integer('fatca7')->nullable()->comment('(1:Có,2:Không)');
            $table->integer('status')->comment('Trạng thái(1:Mới tạo,2:Đã import VSD,3:Đã hủy,4:Đã đóng)');
            $table->integer('vsd_status')->comment('Trạng thái VSD(1:Đợi kích hoạt,2:Đã kích hoạt,3:Từ chối,4:Đã gửi mail)');
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
        Schema::dropIfExists('investors');
    }
}
