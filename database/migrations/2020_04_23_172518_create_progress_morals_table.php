<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressMoralTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progress_morals', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->comment('用户ID');
      $table->string('category')->comment('师德师风类型');
      $table->string('summary', 2000)->nullable()->comment('师德师风综述');
      $table->string('kaohe', 2000)->nullable()->comment('师德师风考核');
      $table->string('warning', 2000)->nullable()->comment('师德师风警告');
      $table->string('punish',2000)->nullable()->comment('师德师风处分');
      $table->string('niandu1', 20)->nullable()->comment('年度一');
      $table->string('niandu1_kaohe', 1)->nullable()->comment('考核成绩');
      $table->string('niandu2', 20)->nullable()->comment('年度二');
      $table->string('niandu2_kaohe', 1)->nullable()->comment('考核成绩');
      $table->string('niandu3', 20)->nullable()->comment('年度三');
      $table->string('niandu3_kaohe', 1)->nullable()->comment('考核成绩');
      $table->string('niandu4', 20)->nullable()->comment('年度四');
      $table->string('niandu4_kaohe', 1)->nullable()->comment('考核成绩');
      $table->string('niandu5', 20)->nullable()->comment('年度五');
      $table->string('niandu5_kaohe', 1)->nullable()->comment('考核成绩');
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
    Schema::dropIfExists('progress_moral');
  }
}
