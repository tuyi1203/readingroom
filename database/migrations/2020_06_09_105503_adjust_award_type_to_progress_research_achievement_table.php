<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustAwardTypeToProgressResearchAchievementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progress_research_achievement', function (Blueprint $table) {
          $table->string('award_type')->nullable()->comment('获奖类别')->change();
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
          $table->tinyInteger('award_type')->nullable()->comment('获奖类别')->change();
        });
    }
}
