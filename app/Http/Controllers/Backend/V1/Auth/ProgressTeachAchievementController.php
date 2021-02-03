<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\ModelFilters\Backend\ProgressTeachAchievementFilter as AchievementFilter;
use App\Models\Backend\ProgressTeachAchievement as Achievement;
use App\Models\Backend\FileInfo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProgressTeachAchievementController extends APIBaseController
{
  /*
  * 展示教育成果列表
  */
  public function index(Request $request)
  {
    $this->checkPermission('list_teach_achievement');

    if (!$request->has('type')) {
      throw new \ErrorException('No params');
    }

    $columns = $this->getColumns4Show($request->input('type'));

    $achievements = Achievement::filter($this->getParams($request, ['user_id' => $this->user->id]), AchievementFilter::class)
      ->paginate(
        $this->getPageSize($request),
        $columns,
        'page',
        $this->getCurrentPage($request)
      );

    $ids = $achievements->map(function ($item) {
      return $item->id;
    });

    $achievementFiles = FileInfo::where('bize_type', 'teach/achievement')->whereIn('bize_id', $ids)->get();

    $achievementsMapped = $achievements->map(function ($item, $index) use ($achievementFiles) {

      $item->achievement_files = $this->getFiles($achievementFiles, $item);

      return $item;
    });

    return $this->success($achievementsMapped->all());
  }

  /*
  * 展示教育成果详情
  */
  public function show(Request $request, $id)
  {

    $this->checkPermission('detail_teach_achievement');

    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->firstOrFail();
    $columns = $this->getColumns4Show($detail->type);
    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first($columns);
    $achievementFiles = FileInfo::where('bize_type', 'teach/achievement')->where('bize_id', $id)->get();
    $detail->achievement_files = $achievementFiles;

    return $this->success($detail->toArray());
  }

  /*
  * 删除教育成果数据
  */
  public function destroy(Request $request, $id)
  {
    $this->checkPermission('del_teach_achievement');

    $achievement = Achievement::where('id', $id)->where('user_id', $this->user->id)->firstOrFail();
    $achievementFiles = FileInfo::where('bize_type', 'teach/achievement')->where('bize_id', $achievement->id)->get();

    //删除记录
    $deleteResult = $achievement->delete();
    if (!$deleteResult) {
      $this->failed('Delete record failed.');
    }

    //删除附件
    if (!$achievementFiles->isEmpty()) {
      $deleteResult = $achievementFiles->each->delete();
      if (!$deleteResult) {
        $this->failed('Delete record failed.');
      }
    }

    return $this->success(null, 'Delete succeed.');
  }

  /*
  * 修改教育成果数据
  */
  public function update(Request $request, $id)
  {
    $this->checkPermission('edit_teach_achievement');

    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first();
    $columns = $this->setColunms($detail->type, $request);
    $detail->fill($columns)->save();
    $columns4Show = $this->getColumns4Show($detail->type);
    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first($columns4Show);
    return $this->success($detail);
  }

  /*
  * 新增教育成果数据
  */
  public function store(Request $request)
  {
    $this->checkPermission('add_teach_achievement');

    $validator = Validator::make($request->all(), [
      'type' => 'required|integer',
//      'achievement_type' => 'required|string',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $result = Achievement::create($this->setColunms($request->input('type'), $request));
    if (!$result) {
      return $this->failed('Create new achievement error.');
    }

    // 更新附件
    if ($request->has('fileids') && count($request->input('fileids')) > 0) {
      FileInfo::where('user_id', $this->user->id)->whereIn(
        'bize_type',
        [
          'teach/achievement',
        ]
      )->whereIn('id', $request->input('fileids'))
        ->where('user_id', $this->user->id)
        ->where('bize_id', null)
        ->update(['bize_id' => $result->id]);
    }

    return $this->success($result->id);
  }

  /*
  * 设置需要填充的字段
  */
  private function setColunms($type, Request $request)
  {
    $columns = [
      'user_id' => $this->user->id,
    ];

    if ($request->has('type')) { // 新增的时候
      $columns['type'] = $request->input('type');
    }

    switch ($type) {
      case 2:
        $columns = Arr::collapse([$columns, [
          'achievement_type' => $request->input('achievement_type'),
          'manage_exp_communicate_date' => $request->input('manage_exp_communicate_date'),
          'manage_exp_communicate_content' => $request->input('manage_exp_communicate_content'),
          'manage_exp_communicate_role' => $request->input('manage_exp_communicate_role'),
          'manage_exp_communicate_range' => $request->input('manage_exp_communicate_range'),
        ]]);
        break;
      case 3:
        $columns = Arr::collapse([$columns, [
          'achievement_type' => $request->input('achievement_type'),
          'teacher_guide_date_start' => $request->input('teacher_guide_date_start'),
          'teacher_guide_date_end' => $request->input('teacher_guide_date_end'),
          'teacher_guide_name' => $request->input('teacher_guide_name'),
          'teacher_guide_content' => $request->input('teacher_guide_content'),
          'teacher_guide_effect' => $request->input('teacher_guide_effect'),
        ]]);
        break;
      case 1:
      default:
        $columns = Arr::collapse([$columns, [
          'teacher_guide_date_start' => $request->input('teacher_guide_date_start'),
          'teacher_guide_date_end' => $request->input('teacher_guide_date_end'),
          'teacher_guide_name' => $request->input('teacher_guide_name'),
          'teacher_guide_content' => $request->input('teacher_guide_content'),
          'teacher_guide_effect' => $request->input('teacher_guide_effect'),
          'award_date' => $request->input('award_date'),
          'award_main' => $request->input('award_main'),
          'award_title' => $request->input('award_title'),
          'award_type' => $request->input('award_type'),
          'award_level' => $request->input('award_level'),
          'award_position' => $request->input('award_position'),
          'award_role' => $request->input('award_role'),
          'award_authoriry_organization' => $request->input('award_authoriry_organization'),
          'award_authoriry_country' => $request->input('award_authoriry_country'),
        ]]);
        break;
    }

    return $columns;
  }


  /**
   * 获取需要展示的字段
   * @param $type
   * @return array
   */
  private function getColumns4Show($type)
  {
    $columns = ['*'];
    switch ($type) {
      case 2:
        $columns = [
          'id',
          'type',
          'achievement_type',
          'manage_exp_communicate_date',
          'manage_exp_communicate_content',
          'manage_exp_communicate_role',
          'manage_exp_communicate_range',
        ];
        break;
      case 3:
        $columns = [
          'id',
          'type',
          'achievement_type',
          'teacher_guide_date_start',
          'teacher_guide_date_end',
          'teacher_guide_name',
          'teacher_guide_content',
          'teacher_guide_effect',
        ];
        break;
      case 1:
      default:
        $columns = [
          'id',
          'type',
          'award_date',
          'award_main',
          'award_title',
          'award_type',
          'award_level',
          'award_position',
          'award_role',
          'award_authoriry_organization',
          'award_authoriry_country',
          'teacher_guide_date_start',
          'teacher_guide_date_end',
          'teacher_guide_name',
          'teacher_guide_content',
          'teacher_guide_effect',
        ];
        break;
    }
    return $columns;
  }

  /*
  * 获取数据对应的文件信息
  */
  private function getFiles($fileCollections, $item)
  {
    $files = $fileCollections->filter(function ($file) use ($item) {
      return $item->id === $file->bize_id;
    });

    $tmpFiles = [];
    $files->map(function ($item) use (&$tmpFiles) {
      $tmpFiles[] = $item;
    });

    return $tmpFiles;
  }
}
