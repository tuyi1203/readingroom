<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressResearchAchievementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progress_research_achievement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->comment('用户ID');
            $table->tinyInteger('achievement_type')->comment('成果类型');
            $table->integer('course')->comment('学科领域');
            $table->boolean('award')->comment('是否获奖');

            $table->string('paper_title')->nullable()->comment('论文名称');
            $table->string('paper_book_title')->nullable()->comment('发表刊物名称');
            $table->string('paper_book_kanhao')->nullable()->comment('刊号');
            $table->string('paper_book_juanhao')->nullable()->comment('卷号');
            $table->date('paper_date')->nullable()->comment('发表年月');
            $table->boolean('paper_core_book')->nullable()->comment('是否核心刊物');
            $table->integer('paper_start_page')->nullable()->comment('起始页码');
            $table->integer('paper_end_page')->nullable()->comment('结束页码');
            $table->tinyInteger('paper_role')->nullable()->comment('本人作用');
            $table->tinyInteger('paper_author_num')->nullable()->comment('作者人数');
            $table->tinyInteger('paper_author_rank')->nullable()->comment('本人排名');
            $table->integer('paper_author_count')->nullable()->comment('本人撰写字数');
            $table->string('paper_author_section')->nullable()->comment('本人撰写章节');
            $table->string('paper_quote')->nullable()->comment('论文收录情况');

            $table->string('subject_title')->nullable()->comment('课题名称');
            $table->string('subject_no')->nullable()->comment('课题批准号');
            $table->string('subject_type')->nullable()->comment('课题类别');
            $table->string('subject_level')->nullable()->comment('课题等级');
            $table->string('subject_responseable_man')->nullable()->comment('课题负责人');
            $table->string('subject_role')->nullable()->comment('课题中本人角色');
            $table->integer('subject_self_rank')->nullable()->comment('课题本人排名');
            $table->double('subject_cost')->nullable()->comment('课题经费');
            $table->tinyInteger('subject_status')->nullable()->comment('课题状态');
            $table->string('subject_delegate')->nullable()->comment('课题委托单位');
            $table->string('subject_exec')->nullable()->comment('课题承担单位');
            $table->date('subject_start_date')->nullable()->comment('课题开始日期');
            $table->date('subject_end_date')->nullable()->comment('课题结束日期');

            $table->string('book_title')->nullable()->comment('著作名称');
            $table->string('book_type')->nullable()->comment('著作类别');
            $table->string('book_publish_company_name')->nullable()->comment('出版社名称');
            $table->string('book_publish_no')->nullable()->comment('出版号');
            $table->date('book_publish_date')->nullable()->comment('出版日期');
            $table->string('book_role')->nullable()->comment('著作中本人角色');
            $table->integer('book_write_count')->nullable()->comment('总字数');
            $table->tinyInteger('book_author_num')->nullable()->comment('作者人数');
            $table->integer('book_author_write_count')->nullable()->comment('本人撰写字数');
            $table->tinyInteger('book_author_rank')->nullable()->comment('本人排名');

            $table->tinyInteger('copyright_type')->nullable()->comment('专利或软件著作权类型');
            $table->tinyInteger('copyright_title')->nullable()->comment('专利或软件著作权名称');
            $table->date('copyright_ratification')->nullable()->comment('审批时间');
            $table->string('copyright_role')->nullable()->comment('本人角色');
            $table->string('copyright_no')->nullable()->comment('专利号（登记号）');

            $table->date('award_date')->nullable()->comment('获奖时间');
            $table->date('award_title')->nullable()->comment('获奖名称');
            $table->string('award_authoriry_organization')->nullable()->comment('颁奖单位');
            $table->tinyInteger('award_type')->nullable()->comment('获奖类别');
            $table->tinyInteger('award_level')->nullable()->comment('获奖级别');
            $table->string('award_position')->nullable()->comment('获奖等次');
            $table->string('award_author_rank')->nullable()->comment('本人排名');
            $table->string('award_authoriry_country')->nullable()->comment('颁奖国家(地区）');

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
        Schema::dropIfExists('progress_research_achievement');
    }
}
