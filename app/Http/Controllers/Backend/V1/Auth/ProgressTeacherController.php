<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\ModelFilters\Backend\ProgressBaseinfoFilter;
use App\ModelFilters\Backend\ProgressEducateAchievementFilter;
use App\ModelFilters\Backend\ProgressTeachAchievementFilter as AchievementFilter;
use App\Models\Backend\ExtendRole as Role;
use App\Models\Backend\FileInfo;
use App\Models\Backend\ProgressBaseinfo;
use App\Models\Backend\ProgressEducateAchievement;
use App\Models\Backend\ProgressEducateBaseinfo;
use App\Models\Backend\ProgressMoral;
use App\Models\Backend\ProgressQualificationEducate;
use App\Models\Backend\ProgressQualificationEducateExperience;
use App\Models\Backend\ProgressQualificationManageExperience;
use App\Models\Backend\ProgressQualificationWork;
use App\Models\Backend\ProgressQualificationWorkExperience;
use App\Models\Backend\ProgressTeachAchievement as Achievement;
use App\Models\Backend\User;
use App\ModelFilters\Backend\UserFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use App\Rules\Mobile;

class ProgressTeacherController extends APIBaseController
{
  /**
   * 获取用户列表接口
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    $this->checkPermission('teacher_info_search');
    $teachers = ProgressBaseinfo::filter($this->getParams($request), ProgressBaseinfoFilter::class)
      ->paginate($this->getPageSize($request), ['*'], 'page',
        $this->getCurrentPage($request));

    return $this->success($teachers->toArray());
  }

  /**
   * 获取单个用户信息接口
   * @param Request $request
   * @param $uid
   * @return JsonResponse
   */
  public function show(Request $request, $uid)
  {
    $this->checkPermission('teacher_info_search');
    $teacherInfo = null;
    if (!$request->filled('type')) {
      $teacherInfo = ProgressBaseinfo::where('user_id', $uid)
        ->firstOrFail();
    } else if ($request->input('type') == 'moral') {
      $teacherInfo = ProgressMoral::where('user_id', $uid)
        ->where('category', $request->category)
        ->firstOrFail();
    } else if ($request->input('type') == 'qualification') { // 基本资格
      if ($request->category == 'educate') {
        $teacherInfo = ProgressQualificationEducate::where('user_id', $uid)
          ->firstOrFail();
        $teachExperiences = ProgressQualificationEducateExperience::where('user_id', $uid)
          ->orderBy('order_sort')->get();
        $teacherInfo->experiences = $teachExperiences;
      }
      if ($request->category == 'work') {
        $teacherInfo = ProgressQualificationWork::where('user_id', $uid)
          ->firstOrFail();
      }
      if ($request->category == 'work_experience') {
        $teacherInfo = ProgressQualificationEducate::where('user_id', $uid)
          ->firstOrFail();
        $workExperiences = ProgressQualificationWorkExperience::where('user_id', $uid)
          ->orderBy('order_sort')->get();
        $teacherInfo->experiences = $workExperiences;
      }
      if ($request->category == 'manage') {
        $teacherInfo = ProgressQualificationEducate::where('user_id', $uid)
          ->firstOrFail();
        $manageExperiences = ProgressQualificationManageExperience::where('user_id', $uid)
          ->orderBy('order_sort')->get();
        $teacherInfo->experiences = $manageExperiences;
      }

    } else if ($request->input('type') == 'teach') { // 教育成果
      $columns = $this->getColumns4Show($request->input('category'));
      $params = $this->getParams($request);
      $params['user_id'] = $uid;
      $params['type'] = $request->category;

      $teacherInfo = Achievement::filter($params, AchievementFilter::class)
        ->paginate(
          $this->getPageSize($request),
          $columns,
          'page',
          $this->getCurrentPage($request)
        );
    } else if ($request->input('type') == 'educate') { // 教学成果
      if ($request->category == 'baseinfo') { // 教学成果基本情况
        $teacherInfo = ProgressEducateBaseinfo::where('user_id', $uid)->firstOrFail();
        $files = FileInfo::where('bize_type','educate/baseinfo')->where('bize_id',$teacherInfo->id)->get();
        $teacherInfo->files = $files;
      }
      if ($request->category == 'list') { // 教学成果列表
        $columns = $this->getEducateColumns4Show($request->input('category_type'));
        $params = $this->getParams($request);
        $params['user_id'] = $uid;
        $params['type'] = $request->category_type;
        $teacherInfo = ProgressEducateAchievement::filter($params, ProgressEducateAchievementFilter::class)
          ->paginate(
            $this->getPageSize($request),
            $columns,
            'page',
            $this->getCurrentPage($request)
          );
      }


    }
    return $this->success($teacherInfo);
  }

  /**
   * 获取单个教育成果信息详情
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function teachDetail(Request $request, $id)
  {
    $this->checkPermission('teacher_info_search');
    $detail = Achievement::findOrFail($id);
    $achievementFiles = FileInfo::where('bize_type', 'teach/achievement')->where('bize_id', $id)->get();
    $detail->files = $achievementFiles;
    return $this->success($detail);
  }

  /**
   * 获取单个教学成果信息详情
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function educateDetail(Request $request, $id)
  {
    $this->checkPermission('teacher_info_search');
    $detail = ProgressEducateAchievement::findOrFail($id);
    $achievementFiles = FileInfo::where('bize_type', 'educate/achievement')->where('bize_id', $id)->get();
    $detail->files = $achievementFiles;
    return $this->success($detail);
  }

  /*
  * 获取需要展示的字段
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
          'achievement_type',
          'award_date',
          'award_main',
          'award_title',
          'award_type',
          'award_level',
          'award_position',
          'award_role',
          'award_authoriry_organization',
          'award_authoriry_country',
        ];
        break;
    }
    return $columns;
  }

  /**
   * 设置需要展示的字段
   * @param $type
   * @return array
   */
  private function getEducateColumns4Show($type)
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

}
