<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustCopyrightTitleAndTypeToProgressResearchAchievementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progress_research_achievement', function (Blueprint $table) {
          $table->string('copyright_type')->nullable()->comment('专利或软件著作权类型')->change();
          $table->string('copyright_title')->nullable()->comment('专利或软件著作权名称')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('progress_research_achievement', function (Blueprint $table) {
          $table->tinyInteger('copyright_type')->nullable()->comment('专利或软件著作权类型')->change();
          $table->tinyInteger('copyright_title')->nullable()->comment('专利或软件著作权名称')->change();
        });
    }
}
