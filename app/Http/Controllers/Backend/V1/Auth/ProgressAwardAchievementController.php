<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\ModelFilters\Backend\ProgressAwardAchievementFilter as AchievementFilter;
use App\Models\Backend\FileInfo;
use App\Models\Backend\ProgressAwardAchievement as Achievement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class ProgressAwardAchievementController extends APIBaseController
{
  /**
   * 创建或更新荣誉详情接口
   * @param Request $request
   * @return JsonResponse
   */
  public function edit(Request $request)
  {
    $this->checkPermission('edit_award_achievement');

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

    //更新附件信息
    if ($request->filled('fileids')) {
      FileInfo::whereIn('id', $request->input('fileids'))
        ->update([
          'bize_id' => $baseInfo->id,
        ]);
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
    $this->checkPermission('list_award_achievement');

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

    $achievementFiles = FileInfo::where('bize_type', 'award/achievement')->whereIn('bize_id', $ids)->get();

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

    $this->checkPermission('detail_award_achievement');

    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->firstOrFail();
    $columns = $this->getColumns4Show($detail->type);
    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first($columns);
    $achievementFiles = FileInfo::where('bize_type', 'award/achievement')->where('bize_id', $id)->get();
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
    $this->checkPermission('del_award_achievement');

    $achievement = Achievement::where('id', $id)->where('user_id', $this->user->id)->firstOrFail();
    $achievementFiles = FileInfo::where('bize_type', 'award/achievement')->where('bize_id', $achievement->id)->get();

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
    $this->checkPermission('edit_award_achievement');

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
    $this->checkPermission('add_award_achievement');

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
          'award/achievement',
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
//      'achievement_type' => $request->input('achievement_type'),
    ];

    if ($request->has('type')) { // 新增的时候
      $columns['type'] = $request->input('type');
    }

    switch ($type) {
//      case 2:
//        $columns = Arr::collapse([$columns, [
//          'lecture_date' => $request->input('lecture_date'),
//          'lecture_content' => $request->input('lecture_content'),
//          'lecture_person' => $request->input('lecture_person'),
//          'lecture_organization' => $request->input('lecture_organization'),
//          'lecture_scope' => $request->input('lecture_scope'),
//        ]]);
//        break;
      case 1:
      default:
        $columns = Arr::collapse([$columns, [
          'award_remark' => $request->input('award_remark'),
          'award_type' => $request->input('award_type'),
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
//      case 2:
//        $columns = [
//          'id',
//          'type',
//          'achievement_type',
//          'lecture_date',
//          'lecture_content',
//          'lecture_person',
//          'lecture_organization',
//          'lecture_scope'
//        ];
//        break;
      case 1:
      default:
        $columns = [
          'id',
          'type',
          'award_date',
          'award_remark',
          'award_title',
          'award_type',
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
