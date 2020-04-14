<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustRemarkToProgressDictTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progress_dicts', function (Blueprint $table) {
          $table->string('remark')->nullable()->comment('字典值描述')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('progress_dicts', function (Blueprint $table) {
          $table->string('remark')->nullable(false)->comment('字典值描述')->change();
        });
    }
}
