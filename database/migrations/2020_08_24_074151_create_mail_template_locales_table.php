<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailTemplateLocalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_template_locales', function (Blueprint $table) {
            $table->id();
            $table->integer('mail_template_id')->index();
            $table->string('locale', 50)->nullable()->comment('Ngôn ngữ');
            $table->text('subject')->comment('Tiêu đề');
            $table->longText('content')->comment('Nội dung');
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
        Schema::dropIfExists('mail_template_locales');
    }
}
