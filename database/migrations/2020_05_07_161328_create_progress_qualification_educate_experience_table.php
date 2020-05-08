<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressQualificationEducateExperienceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progress_qualification_educate_experience', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->comment('用户ID');
            $table->string('start_year', 4)->nullable()->comment('开始年份');
            $table->string('start_month', 2)->nullable()->comment('开始月份');
            $table->string('end_year', 4)->nullable()->comment('结束年份');
            $table->string('end_month', 2)->nullable()->comment('结束月份');
            $table->tinyInteger('education')->nullable()->comment('学历');
            $table->string('prove_person', 20)->nullable()->comment('证明人');
            $table->integer('order_sort')->default(0)->comment('排序号');
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
        Schema::dropIfExists('progress_qualification_educate_experience');
    }
}
