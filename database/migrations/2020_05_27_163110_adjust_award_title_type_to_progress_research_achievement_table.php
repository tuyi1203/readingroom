<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustAwardTitleTypeToProgressResearchAchievementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progress_research_achievement', function (Blueprint $table) {
          $table->string('award_title')->nullable()->comment('获奖名称')->change();
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
          $table->date('award_title')->nullable()->comment('获奖名称')->change();
        });
    }
}
