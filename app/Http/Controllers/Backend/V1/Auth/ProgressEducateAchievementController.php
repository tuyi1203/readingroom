<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Http\Controllers\Controller;
use App\ModelFilters\Backend\ProgressEducateAchievementFilter as AchievementFilter;
use App\Models\Backend\FileInfo;
use App\Models\Backend\ProgressEducateAchievement as Achievement;
use App\Models\Backend\ProgressEducateBaseinfo as BaseInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class ProgressEducateAchievementController extends APIBaseController
{
  /**
   * 显示教学成果基本信息详情接口
   * @param Request $request
   * @return JsonResponse
   */
  public function getBaseInfo(Request $request)
  {
    $this->checkPermission('detail_educate_baseinfo');
    $baseInfo = BaseInfo::where('user_id', $this->user->id)->first();
    if (is_null($baseInfo)) {
      return $this->success(null);
    }
    return $this->success($baseInfo);
  }

  /**
   * 创建或更新教学成果基本信息详情接口
   * @param Request $request
   * @return JsonResponse
   */
  public function edit(Request $request)
  {
    $this->checkPermission('add_or_edit_educate_baseinfo');

    $validator = Validator::make($request->all(), [

    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $baseInfo = BaseInfo::updateOrCreate([
      'user_id' => $this->user->id,
    ], [
      'effect' => $request->input('effect'),
      'observe' => $request->input('observe'),
      'communicate' => $request->input('communicate'),
      'guide' => $request->input('guide'),
      'elective' => $request->input('elective'),
    ]);

    if (!$baseInfo) {
      return $this->failed('Update failed.');
    }
    return $this->success($baseInfo, 'Update succeed.');
  }


  /**
   * 展示教学成果列表
   * @param Request $request
   * @return mixed
   * @throws \ErrorException
   */
  public function index(Request $request)
  {
    $this->checkPermission('list_educate_achievement');

    if (!$request->has('type')) {
      throw new \ErrorException('No params');
    }

    $columns = $this->getColumns4Show($request->input('type'));

    $achievements = Achievement::filter($this->getParams($request), AchievementFilter::class)
      ->paginate(
        $this->getPageSize($request),
        $columns,
        'page',
        $this->getCurrentPage($request)
      );

    $ids = $achievements->map(function ($item) {
      return $item->id;
    });

    $achievementFiles = FileInfo::where('bize_type', 'educate/achievement')->whereIn('bize_id', $ids)->get();

    $achievementsMapped = $achievements->map(function ($item, $index) use ($achievementFiles) {

      $item->achievement_files = $this->getFiles($achievementFiles, $item);

      return $item;
    });

    return $this->success($achievementsMapped->all());
  }


  /**
   * 展示教学成果详情
   * @param Request $request
   * @param $id
   * @return mixed
   */
  public function show(Request $request, $id)
  {

    $this->checkPermission('detail_educate_achievement');

    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->firstOrFail();
    $columns = $this->getColumns4Show($detail->type);
    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first($columns);
    $achievementFiles = FileInfo::where('bize_type', 'educate/achievement')->where('bize_id', $id)->get();
    $detail->achievement_files = $achievementFiles;

    return $this->success($detail->toArray());
  }


  /**
   * 显出教学成果数据
   * @param Request $request
   * @param $id
   * @return mixed
   */
  public function destroy(Request $request, $id)
  {
    $this->checkPermission('del_educate_achievement');

    $achievement = Achievement::where('id', $id)->where('user_id', $this->user->id)->firstOrFail();
    $achievementFiles = FileInfo::where('bize_type', 'educate/achievement')->where('bize_id', $achievement->id)->get();

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

  /**
   * 修改教学成果数据
   * @param Request $request
   * @param $id
   * @return mixed
   */
  public function update(Request $request, $id)
  {
    $this->checkPermission('edit_educate_achievement');

    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first();
    $columns = $this->setColunms($detail->type, $request);
    $detail->fill($columns)->save();
    $columns4Show = $this->getColumns4Show($detail->type);
    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first($columns4Show);
    return $this->success($detail);
  }

  /**
   * 新增教学成果数据
   * @param Request $request
   * @return mixed
   */
  public function store(Request $request)
  {
    $this->checkPermission('add_educate_achievement');

    $validator = Validator::make($request->all(), [
      'type' => 'required|integer',
      'achievement_type' => 'required|string',
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
          'educate/achievement',
        ]
      )->whereIn('id', $request->input('fileids'))
        ->where('user_id', $this->user->id)
        ->where('bize_id', null)
        ->update(['bize_id' => $result->id]);
    }

    return $this->success($result->id);
  }

  /**
   * 设置需要填充的字段
   * @param $type
   * @param Request $request
   * @return array
   */
  private function setColunms($type, Request $request)
  {
    $columns = [
      'user_id' => $this->user->id,
      'achievement_type' => $request->input('achievement_type'),
    ];

    if ($request->has('type')) { // 新增的时候
      $columns['type'] = $request->input('type');
    }

    switch ($type) {
      case 2:
        $columns = Arr::collapse([$columns, [
          'lecture_date' => $request->input('lecture_date'),
          'lecture_content' => $request->input('lecture_content'),
          'lecture_person' => $request->input('lecture_person'),
          'lecture_organization' => $request->input('lecture_organization'),
        ]]);
        break;
      case 1:
      default:
        $columns = Arr::collapse([$columns, [
          'award_date' => $request->input('award_date'),
          'award_title' => $request->input('award_title'),
          'award_level' => $request->input('award_level'),
          'award_position' => $request->input('award_position'),
          'award_authoriry_organization' => $request->input('award_authoriry_organization'),
          'award_authoriry_country' => $request->input('award_authoriry_country'),
        ]]);
        break;
    }

    return $columns;
  }

  /**
   * 设置需要展示的字段
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
          'lecture_date',
          'lecture_content',
          'lecture_person',
          'lecture_organization',
        ];
        break;
      case 1:
      default:
        $columns = [
          'id',
          'type',
          'achievement_type',
          'award_date',
          'award_title',
          'award_level',
          'award_position',
          'award_authoriry_organization',
          'award_authoriry_country',
        ];
        break;
    }
    return $columns;
  }

  /**
   * 获取数据对应的文件信息
   * @param $fileCollections
   * @param $item
   * @return array
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
