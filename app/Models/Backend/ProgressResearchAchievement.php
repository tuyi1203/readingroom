<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class ProgressResearchAchievement extends Model
{
  use Filterable;
  
  protected $table = 'progress_research_achievement';
  protected $fillable = [
    'user_id',
    'achievement_type',
    'course',
    'award',
    'paper_title',
    'paper_book_title',
    'paper_book_kanhao',
    'paper_book_juanhao',
    'paper_date',
    'paper_core_book',
    'paper_start_page',
    'paper_end_page',
    'paper_role',
    'paper_author_num',
    'paper_author_rank',
    'paper_author_count',
    'paper_author_section',
    'paper_quote',
    'subject_title',
    'subject_no',
    'subject_type',
    'subject_level',
    'subject_responseable_man',
    'subject_role',
    'subject_self_rank',
    'subject_cost',
    'subject_status',
    'subject_delegate',
    'subject_exec',
    'subject_start_date',
    'subject_end_date',
    'book_title',
    'book_type',
    'book_publish_company_name',
    'book_publish_no',
    'book_publish_date',
    'book_role',
    'book_write_count',
    'book_author_num',
    'book_author_write_count',
    'book_author_rank',
    'copyright_type',
    'copyright_title',
    'copyright_ratification',
    'copyright_role',
    'copyright_no',
    'award_date',
    'award_title',
    'award_authoriry_organization',
    'award_type',
    'award_level',
    'award_position',
    'award_author_rank',
    'award_authoriry_country',
  ];
}
