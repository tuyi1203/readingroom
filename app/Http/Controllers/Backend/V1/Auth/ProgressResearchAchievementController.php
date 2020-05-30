<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\ModelFilters\Backend\ProgressResearchAchievementFilter as AchievementFilter;
use App\Models\Backend\ProgressResearchAchievement as Achievement;
use App\Models\Backend\FileInfo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProgressResearchAchievementController extends APIBaseController
{
  /*
  * 展示科研成果列表
  */
  public function index(Request $request)
  {
    $this->checkPermission('list_research_achievement');

    if (!$request->has('achievement_type')) {
      throw new \ErrorException('No params');
    }

    $columns = $this->getColumns4Show($request->input('achievement_type'));

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

    $achievementFiles = FileInfo::where('bize_type', 'research/achievement')->whereIn('bize_id', $ids)->get();
    $awardFiles = FileInfo::where('bize_type', 'research/award')->whereIn('bize_id', $ids)->get();

    $achievementsMapped = $achievements->map(function ($item, $index) use ($achievementFiles, $awardFiles) {

      $item->award_files = $this->getFiles($awardFiles, $item);

      $item->achievement_files = $this->getFiles($achievementFiles, $item);

      return $item;
    });

    return $this->success($achievementsMapped->all());
  }

  /*
  * 展示科研成果详情
  */
  public function show(Request $request, $id)
  {

    $this->checkPermission('detail_research_achievement');

    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->firstOrFail();
    $columns = $this->getColumns4Show($detail->achievement_type);
    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first($columns);
    $achievementFiles = FileInfo::where('bize_type', 'research/achievement')->where('bize_id', $id)->get();
    $detail->achievement_files = $achievementFiles;
    $awardFiles = FileInfo::where('bize_type', 'research/award')->where('bize_id', $id)->get();
    $detail->award_files = $awardFiles;

    return $this->success($detail->toArray());
  }

  /*
  * 删除科研成果数据
  */
  public function destroy(Request $request, $id)
  {
    $this->checkPermission('del_research_achievement');

    $achievement = Achievement::where('id', $id)->where('user_id', $this->user->id)->firstOrFail();
    $achievementFiles = FileInfo::where('bize_type', 'research/achievement')->where('bize_id', $achievement->id)->get();
    $awardFiles = FileInfo::where('bize_type', 'research/award')->where('bize_id', $achievement->id)->get();

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

    //删除附件
    if (!$awardFiles->isEmpty()) {
      $deleteResult = $awardFiles->each->delete();
      if (!$deleteResult) {
        $this->failed('Delete record failed.');
      }
    }

    return $this->success(null, 'Delete succeed.');
  }

  /*
  * 修改科研成果数据
  */
  public function update(Request $request, $id)
  {
    $this->checkPermission('edit_research_achievement');

    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first();
    $columns = $this->setColunms($detail->achievement_type, $request);
    $detail->fill($columns)->save();
    $columns4Show = $this->getColumns4Show($detail->achievement_type);
    $detail = Achievement::where('user_id', $this->user->id)->where('id', $id)->first($columns4Show);
    //删除不需要的上传文件
    $this->delUploadedCachedFiles($detail);
    return $this->success($detail);
  }

  /*
  * 删除多余的上传文件
  */
  private function delUploadedCachedFiles($achievementRecord)
  {
    if (!$achievementRecord->award) {
      $awardFiles = FileInfo::where('bize_type', 'research/award')->where('bize_id', $achievementRecord->id)->get();
      if (!$awardFiles->isEmpty()) {
        $awardFiles->delete();
      }
    }
  }

  /*
  * 新增科研成果数据
  */
  public function store(Request $request)
  {
    $this->checkPermission('add_research_achievement');

    $validator = Validator::make($request->all(), [
      'course' => 'required|integer',
      'award' => 'required|integer',
      'achievement_type' => 'required|integer',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $result = Achievement::create($this->setColunms($request->input('achievement_type'), $request));
    if (!$result) {
      return $this->failed('Create new achievement error.');
    }

    // 更新附件
    if ($request->has('fileids') && count($request->input('fileids')) > 0) {
      FileInfo::where('user_id', $this->user->id)->whereIn(
        'bize_type',
        [
          'research/achievement',
          'research/award'
        ]
      )->whereIn('id', $request->input('fileids'))
        ->where('user_id', $this->user->id)
        ->where('bize_id', null)
        ->update(['bize_id' => $result->id]);
    }

    //删除不需要的上传文件
    $this->delUploadedCachedFiles($result);

    return $this->success($result->id);
  }

  /*
  * 设置需要填充的字段
  */
  private function setColunms($achievementType, Request $request)
  {
    $columns = [
      'user_id' => $this->user->id,
      'course' => $request->input('course'),
      'award' => $request->input('award'),
    ];


    if ($request->has('achievement_type')) { // 新增的时候
      $columns['achievement_type'] = $request->input('achievement_type');
    }

    switch ($achievementType) {
      case 2:
        $columns = Arr::collapse([$columns, [
          'subject_title' => $request->input('subject_title'),
          'subject_no' => $request->input('subject_no'),
          'subject_type' => $request->input('subject_type'),
          'subject_level' => $request->input('subject_level'),
          'subject_responseable_man' => $request->input('subject_responseable_man'),
          'subject_role' => $request->input('subject_role'),
          'subject_self_rank' => $request->input('subject_self_rank'),
          'subject_cost' => $request->input('subject_cost'),
          'subject_status' => $request->input('subject_status'),
          'subject_delegate' => $request->input('subject_delegate'),
          'subject_exec' => $request->input('subject_exec'),
          'subject_start_date' => $request->input('subject_start_date'),
          'subject_end_date' => $request->input('subject_end_date'),
        ]]);
        break;
      case 3:
        $columns = Arr::collapse([$columns, [
          'book_title' => $request->input('book_title'),
          'book_type' => $request->input('book_type'),
          'book_publish_company_name' => $request->input('book_publish_company_name'),
          'book_publish_no' => $request->input('book_publish_no'),
          'book_publish_date' => $request->input('book_publish_date'),
          'book_role' => $request->input('book_role'),
          'book_write_count' => $request->input('book_write_count'),
          'book_author_num' => $request->input('book_author_num'),
          'book_author_write_count' => $request->input('book_author_write_count'),
          'book_author_rank' => $request->input('book_author_rank'),
        ]]);
        break;
      case 4:
        $columns = Arr::collapse([$columns, [
          'copyright_type' => $request->input('copyright_type'),
          'copyright_title' => $request->input('copyright_title'),
          'copyright_ratification' => $request->input('copyright_ratification'),
          'copyright_role' => $request->input('copyright_role'),
          'copyright_no' => $request->input('copyright_no'),
        ]]);
        break;
      case 1:
      default:
        $columns = Arr::collapse([$columns, [
          'paper_title' => $request->input('paper_title'),
          'paper_book_title' => $request->input('paper_book_title'),
          'paper_book_kanhao' => $request->input('paper_book_kanhao'),
          'paper_book_juanhao' => $request->input('paper_book_juanhao'),
          'paper_date' => $request->input('paper_date'),
          'paper_core_book' => $request->input('paper_core_book'),
          'paper_start_page' => $request->input('paper_start_page'),
          'paper_end_page' => $request->input('paper_end_page'),
          'paper_role' => $request->input('paper_role'),
          'paper_author_num' => $request->input('paper_author_num'),
          'paper_author_rank' => $request->input('paper_author_rank'),
          'paper_author_count' => $request->input('paper_author_count'),
          'paper_author_section' => $request->input('paper_author_section'),
          'paper_quote' => $request->input('paper_quote'),
        ]]);
        break;
    }

    if ($request->input('award')) {
      $columns = Arr::collapse([$columns, [
        'award_date' => $request->input('award_date'),
        'award_title' => $request->input('award_title'),
        'award_authoriry_organization' => $request->input('award_authoriry_organization'),
        'award_type' => $request->input('award_type'),
        'award_level' => $request->input('award_level'),
        'award_position' => $request->input('award_position'),
        'award_author_rank' => $request->input('award_author_rank'),
        'award_authoriry_country' => $request->input('award_authoriry_country'),
      ]]);
    }
    return $columns;
  }

  /*
  * 获取需要展示的字段
  */
  private function getColumns4Show($achievementType)
  {
    $columns = ['*'];
    switch ($achievementType) {
      case 2:
        $columns = [
          'id',
          'achievement_type',
          'course',
          'award',
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
          'award_date',
          'award_title',
          'award_authoriry_organization',
          'award_type',
          'award_level',
          'award_position',
          'award_author_rank',
          'award_authoriry_country',
        ];
        break;
      case 3:
        $columns = [
          'id',
          'achievement_type',
          'course',
          'award',
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
          'award_date',
          'award_title',
          'award_authoriry_organization',
          'award_type',
          'award_level',
          'award_position',
          'award_author_rank',
          'award_authoriry_country',
        ];
        break;
      case 4:
        $columns = [
          'id',
          'achievement_type',
          'course',
          'award',
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
        break;
      case 1:
      default:
        $columns = [
          'id',
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
          'award_date',
          'award_title',
          'award_authoriry_organization',
          'award_type',
          'award_level',
          'award_position',
          'award_author_rank',
          'award_authoriry_country',
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
