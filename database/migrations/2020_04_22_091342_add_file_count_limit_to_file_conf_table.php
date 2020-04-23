<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileCountLimitToFileConfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_conf', function (Blueprint $table) {
          $table->tinyInteger('file_count_limit')->comment('业务文件数量限制');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_conf', function (Blueprint $table) {
          $table->dropColumn('file_count_limit');
        });
    }
}
