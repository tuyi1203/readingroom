<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressQualificationEducateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progress_qualification_educate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->comment('用户ID');
            $table->string('graduate_school')->nullable()->comment('最后毕业院校');
            $table->date('graduate_time')->nullable()->comment('最后毕业年月');
            $table->tinyInteger('education')->nullable()->comment('最高学历');
            $table->string('education_no')->nullable()->comment('学历证书号');
            $table->string('degree_no')->nullable()->comment('学位证书号');
            $table->string('subject')->nullable()->comment('专业');
            $table->string('manage_years', 10)->nullable()->comment('教育管理工作累计年限');
            $table->string('rural_teach_years', 10)->nullable()->comment('乡村学校或薄弱学校任教累计年限');
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
        Schema::dropIfExists('progress_qualification_educate');
    }
}
