<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bize_type')->comment('业务类型（冗余字段）');
            $table->integer('bize_id')->comment('业务ID');
            $table->string('original_name')->comment('文件原名称');
            $table->string('new_name')->unique()->comment('文件新名称(随机码');
            $table->string('file_type')->comment('文件类型');
            $table->string('file_size')->comment('文件大小');
            $table->string('file_path')->comment('文件路径');
            $table->string('relative_path')->comment('文件相对路径，域名+此字段为该资源的请求地址');
            $table->tinyInteger('del_flg')->default(0)->comment('文件删除标志：0：未删除，1：文件已经被逻辑删除，2：文件已被物理删除');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_info');
    }
}
