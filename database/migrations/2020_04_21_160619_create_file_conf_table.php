<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileConfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_conf', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bize_type')->comment('业务类型，不同业务不同的类型');
            $table->string('file_type_limit')->nullable()->comment('允许上传的文件类型(mine-type标准)，为空时不限制类型');
            $table->string('file_size_limit')->nullable()->comment('允许上传的文件大小(kb)，为空时不限制大小');
            $table->string('path')->comment('服务器存储文件的路径');
            $table->string('description')->nullable()->comment('描述，如描述该业务类型对应的文件上传业务功能的业务表');
            $table->string('resource_realm')->comment('外部访问文件资源相对根路径');
            $table->tinyInteger('enabled')->default(1)->comment('是否可用(默认1可用，0禁用),用于禁止某个业务上传文件的功能');
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
        Schema::dropIfExists('file_conf');
    }
}
