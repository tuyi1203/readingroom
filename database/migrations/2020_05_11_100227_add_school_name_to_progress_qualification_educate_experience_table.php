<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolNameToProgressQualificationEducateExperienceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progress_qualification_educate_experience', function (Blueprint $table) {
          $table->string('school_name')->nullable()->after('end_month')->comment('毕业学校及专业名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('progress_qualification_educate_experience', function (Blueprint $table) {
          $table->dropColumn('school_name');
        });
    }
}
