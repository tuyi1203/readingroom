<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\ModelFilters\Backend\ProgressAwardAchievementFilter;
use App\ModelFilters\Backend\ProgressBaseinfoFilter;
use App\ModelFilters\Backend\ProgressEducateAchievementFilter;
use App\ModelFilters\Backend\ProgressResearchAchievementFilter;
use App\ModelFilters\Backend\ProgressTeachAchievementFilter as AchievementFilter;
use App\Models\Backend\ExtendRole as Role;
use App\Models\Backend\FileInfo;
use App\Models\Backend\ProgressAwardAchievement;
use App\Models\Backend\ProgressBaseinfo;
use App\Models\Backend\ProgressDict;
use App\Models\Backend\ProgressDictCategory;
use App\Models\Backend\ProgressEducateAchievement;
use App\Models\Backend\ProgressEducateBaseinfo;
use App\Models\Backend\ProgressMoral;
use App\Models\Backend\ProgressQualificationEducate;
use App\Models\Backend\ProgressQualificationEducateExperience;
use App\Models\Backend\ProgressQualificationManageExperience;
use App\Models\Backend\ProgressQualificationWork;
use App\Models\Backend\ProgressQualificationWorkExperience;
use App\Models\Backend\ProgressResearchAchievement;
use App\Models\Backend\ProgressTeachAchievement as Achievement;
use App\Models\Backend\User;
use App\ModelFilters\Backend\UserFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Rules\Mobile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Str;
use Meneses\LaravelMpdf\Facades\LaravelMpdf as PDF;

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
        $files = FileInfo::where('bize_type', 'educate/baseinfo')->where('bize_id', $teacherInfo->id)->get();
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
    } else if ($request->input('type') == 'research') { // 科研成果
      $columns = $this->getResearchColumns4Show($request->input('category'));
      $params = $this->getParams($request);
      $params['user_id'] = $uid;
      $params['type'] = $request->category;

      $teacherInfo = ProgressResearchAchievement::filter($params, ProgressResearchAchievementFilter::class)
        ->paginate(
          $this->getPageSize($request),
          $columns,
          'page',
          $this->getCurrentPage($request)
        );
    } else if ($request->input('type') == 'award') { // 荣誉及其他
      $columns = $this->getAwardColumns4Show($request->input('category'));
      $params = $this->getParams($request);
      $params['user_id'] = $uid;
      $params['type'] = $request->category;

      $teacherInfo = ProgressAwardAchievement::filter($params, ProgressAwardAchievementFilter::class)
        ->paginate(
          $this->getPageSize($request),
          $columns,
          'page',
          $this->getCurrentPage($request)
        );
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

  /**
   * 获取单个研究成果信息详情
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function researchDetail(Request $request, $id)
  {
    $this->checkPermission('teacher_info_search');
    $detail = ProgressResearchAchievement::findOrFail($id);
    $achievementFiles = FileInfo::where('bize_type', 'research/achievement')->where('bize_id', $id)->get();
    $detail->achievement_files = $achievementFiles;
    $awardFiles = FileInfo::where('bize_type', 'research/award')->where('bize_id', $id)->get();
    $detail->award_files = $awardFiles;
    return $this->success($detail);
  }

  /**
   * 获取单个荣誉及其他详情
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function awardDetail(Request $request, $id)
  {
    $this->checkPermission('teacher_info_search');
    $detail = ProgressAwardAchievement::findOrFail($id);
    $achievementFiles = FileInfo::where('bize_type', 'award/achievement')->where('bize_id', $id)->get();
    $detail->files = $achievementFiles;
    return $this->success($detail);
  }

  /**
   * 生成PDF文件
   * @param Request $request
   * @param $uid
   * @return BinaryFileResponse
   */
  public function pdf(Request $request, $uid)
  {
    $teacher = ProgressBaseinfo::where('user_id', $uid)->firstOrFail()->toArray();
    $this->setColumnsByValue([
      'work_time',
      'teach_years',
      'graduate_school',
      'graduate_time',
      'subject',
      'education',
      'education_no',
      'degree_no',
      'school_manager',
      'apply_course',
      'title', // 现任专业技术职务
      'qualification_time', // 现任专业技术职务取得资格时间
      'work_first_time', // 现任专业技术职务首聘时间
      'manage_years',
      'rural_teach_years',
      'remark'
    ], $teacher, $uid); // 参加工作时间
    $teacher['kaohe'] = ProgressMoral::where('user_id', $uid)->where('category', 'kaohe')->first()->toArray();
    $teacher['educate_experiences'] = ProgressQualificationEducateExperience::where('user_id', $uid)
      ->orderBy('order_sort', 'asc')
      ->get()
      ->toArray();

    $teacher['work_experiences'] = ProgressQualificationWorkExperience::where('user_id', $uid)
      ->orderBy('order_sort', 'asc')
      ->get()
      ->toArray();

    $teacher['manage_experiences'] = ProgressQualificationManageExperience::where('user_id', $uid)
      ->orderBy('order_sort', 'asc')
      ->get()
      ->toArray();

    $teacher['educate_baseinfo'] = ProgressEducateBaseinfo::where('user_id', $uid)->first()->toArray();

    // 获取数据字典
    $categories = ProgressDictCategory::all()->toArray();
    $dictList = ProgressDict::orderby('dict_category', 'asc')
      ->orderby('order_sort', 'asc')
      ->get()
      ->groupBy(function ($dictItem, $dictKey) use ($categories) {
        return Arr::first($categories, function ($cateItem, $cateKey) use ($dictItem, $dictKey) {
          return $dictItem['dict_category'] === $cateItem['id'];
        })['category_name'];
      })
      ->toArray();

    $pdfFileName = (String)Str::uuid(32)->getHex() . '.pdf';

    // 解决中文乱码问题
    $pdf = PDF::loadView('pdfs.teacher', [
      'teacher' => $teacher,
      'gender_list' => [
        '0' => '女',
        '1' => '男'
      ],
      'zaibian_list' => [
        '0' => '不在编',
        '1' => '在编'
      ],
      'yesno_list' => [
        '0' => '否',
        '1' => '是'
      ],
      'minzu_list' => $this->getDictListByCategoryName('min_zu', $dictList),
      'education_list' => $this->getDictListByCategoryName('education', $dictList),
      'kaohe_list' => $this->getDictListByCategoryName('kaohe_level', $dictList),
      'course_list' => $this->getDictListByCategoryName('course', $dictList),
    ], [
      'mode' => '+aCJK',
      'allow_charset_conversion' => true,
      'charset_in' => 'utf-8',
      "setAutoTopMargin" => "stretch",
      "setAutoBottomMargin" => "stretch",
      "autoMarginPadding" => 5,
      "margin_left" => 5,
      "margin_right" => 5,
      'fontdata' => [
        'ipa' => [
          'R' => 'simfang.ttf'
        ]
      ]
    ], ['+aCJK', 'A4', '', '', 0, 0, 0, 0, 0, 0]);
    $pdfFullPath = storage_path('app/tmp/') . $pdfFileName;
    $pdf->save($pdfFullPath); // 保存到本地

    $headers = [
      'Content-Type' => 'application/x-pdf',
    ];
//    return response()->stream(function () use ($pdfFullPath) {
//      echo file_get_contents($pdfFullPath);
//    }, 200, $headers);

    return response()
      ->download(
        storage_path('app/tmp/') . $pdfFileName,
        $pdfFileName,
        $headers
      );
  }

  /**
   * 取得数据字典列表
   * @param $categoryName
   * @param $dictList
   * @return array
   */
  private function getDictListByCategoryName($categoryName, $dictList)
  {
    $list = [];
    foreach ($dictList[$categoryName] as $item) {
      $list[$item['dict_value']] = $item['dict_name'];
    }
    return $list;
  }

  private function setColumnsByValue($columnNames, &$assignList, $uid)
  {
    $progressQualificationWork = ProgressQualificationWork::where('user_id', $uid)->firstOrFail();
    $progressQualificationEducate = ProgressQualificationEducate::where('user_id', $uid)->firstOrFail();
    foreach ($columnNames as $columnName) {
      if ($val = $progressQualificationWork[$columnName]) {
        $assignList[$columnName] = $progressQualificationWork[$columnName];
      }
      if ($val = $progressQualificationEducate[$columnName]) {
        $assignList[$columnName] = $progressQualificationEducate[$columnName];
      }
    }
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
      case 3:
        $columns = [
          '*'
        ];
        break;
      case 1:
      default:
        $columns = [
          'id',
          'type',
          'achievement_type',
          'award_type',
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
   * 设置需要展示的字段
   * @param $type
   * @return array
   */
  private function getAwardColumns4Show($type)
  {
    $columns = ['*'];
//    switch ($type) {
//      case 2:
//        $columns = [
//          'id',
//          'type',
//          'achievement_type',
//          'lecture_date',
//          'lecture_content',
//          'lecture_person',
//          'lecture_organization',
//        ];
//        break;
//      case 3:
//        $columns = [
//          '*'
//        ];
//        break;
//      case 1:
//      default:
//        $columns = [
//          'id',
//          'type',
//          'achievement_type',
//          'award_type',
//          'award_date',
//          'award_title',
//          'award_level',
//          'award_position',
//          'award_authoriry_organization',
//          'award_authoriry_country',
//        ];
//        break;
//    }
    return $columns;
  }

  /*
  * 获取需要展示的字段
  */
  private function getResearchColumns4Show($achievementType)
  {
    $columns = ['*'];
    switch ($achievementType) {
      case 2:
        $columns = [
          'id',
          'type',
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
          'type',
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
          'type',
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
          'type',
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

}
