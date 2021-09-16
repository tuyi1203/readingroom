<?php

use App\Models\Backend\FileConf;
use App\Models\Backend\ProgressDict;
use App\Models\Backend\ProgressDictCategory;
use Illuminate\Database\Seeder;

class TeacherNotificationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    $fileConfig = new FileConf();
    $fileConfig->bize_type = 'teacher/notification/plan';
    $fileConfig->file_type_limit = 'application/vnd.ms-excel,application/msexcel,application/x-msexcel,application/x-ms-excel,application/x-excel,application/x-dos_ms_excel,application/x-xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,';
    $fileConfig->file_size_limit = 2048000;
    $fileConfig->path = '/';
    $fileConfig->description = '教师提醒';
    $fileConfig->resource_realm = '/secure_upload';
    $fileConfig->enabled = 1;
    $fileConfig->file_count_limit = 100;
    $fileConfig->save();

    $dictCategory = new ProgressDictCategory();
    $dictCategory->category_name = 'notification_type';
    $dictCategory->remark = '通知类别';
    $dictCategory->order_sort = 0;
    $dictCategory->save();


    $dictCategory = ProgressDictCategory::where('category_name', 'notification_type')->first();


    $dict1 = new ProgressDict();
    $dict1->dict_code = 'attend_class';
    $dict1->dict_name = '上课通知';
    $dict1->dict_value = 'attend_class';
    $dict1->remark = '通知类别';
    $dict1->order_sort = 1;
    $dict1->dict_category = $dictCategory->id;
    $dict1->save();

    $dict2 = new ProgressDict();
    $dict2->dict_code = 'after_class_service';
    $dict2->dict_name = '课后延时服务通知';
    $dict2->dict_value = 'after_class_service';
    $dict2->remark = '通知类别';
    $dict2->order_sort = 2;
    $dict2->dict_category = $dictCategory->id;
    $dict2->save();


    $dict3 = new ProgressDict();
    $dict3->dict_code = 'distribute_food';
    $dict3->dict_name = '打饭通知';
    $dict3->dict_value = 'distribute_food';
    $dict3->remark = '通知类别';
    $dict3->order_sort = 3;
    $dict3->dict_category = $dictCategory->id;
    $dict3->save();
  }
}
