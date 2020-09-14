<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashinReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashin_receipts', function (Blueprint $table) {
            $table->id();
            $table->integer('cashin_id')->unique()->index();
            $table->integer('supervising_bank_id')->index()->comment('Ngân hàng giám sát');
            $table->string('receipt', 255)->unique()->comment('Mã chứng từ thu');
            $table->integer('created_by')->nullable()->comment('Người tạo');
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
        Schema::dropIfExists('cashin_receipts');
    }
}
