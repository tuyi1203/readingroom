<?php

namespace App\Services;

use App\Traits\RmxxSystemCommonApiTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RmxxSystemApiService
{
  use RmxxSystemCommonApiTrait;

  public function getSchoolTimetableTree(): array
  {
    $data = [];
    $schoolTimetables = $this->getSchooTimetableList([]);
    if (!empty($schoolTimetables)) {
      foreach ($schoolTimetables as $item) {
        if (!empty($item['classNo']) && !empty($item['dayOfWeek']) && !empty($item['orderOfDay'])) {
          if (preg_match('/^([1-9][0-9]{3})级([1-9][0-9]*)班$/', trim($item['classNo']), $matches)) {
            list($gradeClass, $stuGrade, $stuClass) = $matches;
            //$data[$stuGrade][$stuClass][$item['dayOfWeek']][$item['orderOfDay']] = $item;
            unset($item['userName']);
            $data[$stuGrade.'级'][$stuClass.'班'][] = $item;
          }
        }
      }
    }
    return $data;
  }

  public function getTeacherCourseByClass(int $classOrderOfDay): array
  {
    // 【0】获取今天 第几节课
    $nWeekday = date('N');
    $weekdayStr = config('rmxx.weekday_map.'.$nWeekday);
    $classTimeMap = config('rmxx.class_time_map');
    $classOrderStr = $classTimeMap[$classOrderOfDay][0]??null;
    if (is_null($weekdayStr[date('N')])){
      Log::notice('缺少配置 rmxx.weekday_map.'.$nWeekday);
      return [];
    }
    if (is_null($classOrderStr)) {
      Log::notice('缺少配置 rmxx.class_time_map.'.$classOrderOfDay.'.0');
      return [];
    }
    // 读取这次课的上课时间
    $notificationTime = empty($classTimeMap[$classOrderOfDay][$nWeekday+1]) ? ($classTimeMap[$classOrderOfDay][1]??'') : $classTimeMap[$classOrderOfDay][$nWeekday+1];
    if (empty($notificationTime)) {
      Log::notice('缺少配置 rmxx.class_time_map.'.$classOrderOfDay.'.1-'.($nWeekday+1));
      return [];
    }



    // 【1】获取全校基本教师信息
    $nameList = [];
    $mobileList = [];
    $nameMobileList = [];
    $nameSubjectList = [];
    $nameMobileSubjectList = [];
    $teachers = $this->getUserList(['page_size' => 1000, 'page' => 1]);
    if (!empty($teachers['rows'])) {
      foreach ($teachers['rows'] as $index => $teacher) {
        $teacherName = isset($teacher['name']) ? trim($teacher['name']) : '_';
        $teacherMobile = isset($teacher['tel']) ? trim($teacher['tel']) : '_';
        $nameList[$teacherName][] = $index;
        $mobileList[$teacherMobile][] = $index;
        $nameMobileList[$teacherName][$teacherMobile][] = $index;
        if (!empty($teacher['subjects'])) {
          foreach ($teacher['subjects'] as $subject) {
            $subject = trim($subject);
            if (!empty($subject)) {
              $nameSubjectList[$teacherName][$subject][] = $index;
              $nameMobileSubjectList[$teacherName][$teacherMobile][$subject][] = $index;
            }
          }
        }
      }
    }


    // 【2】查询开启了上课提醒的教师
    $notificationUsers = DB::table('teacher_notification_settings')
      ->leftJoin('users', 'teacher_notification_settings.user_id', '=', 'users.id')
      ->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id')
      ->select(
        'teacher_notification_settings.id as setting_id',
        'teacher_notification_settings.state',
        'teacher_notification_settings.user_id',
        'users.name','users.email','users.is_active','users.mobile',
        'user_infos.guid','user_infos.full_name','user_infos.open_id',
        'user_infos.union_id','user_infos.mobile as mobile2','user_infos.gender'
      )
      ->where('teacher_notification_settings.notification_type', 'attend_class')
      ->where('teacher_notification_settings.state', 1)
      ->get();
    if (!empty($notificationUsers)) {
      foreach ($notificationUsers as $notificationUser) {
        $name = empty($notificationUser->name) ? '_' : trim($notificationUser->name);
        $name2 = empty($notificationUser->full_name) ? '_' : trim($notificationUser->full_name);
        $mobile = empty($notificationUser->mobile) ? '_' : trim($notificationUser->mobile);
        $mobile2 = empty($notificationUser->mobile2) ? '_' : trim($notificationUser->mobile2);
        if ($name2!='_' && isset($nameMobileList[$name2][$mobile2]) && count($nameMobileList[$name2][$mobile2])===1) {
          $index = $nameMobileList[$name2][$mobile2][0];
          $case = 'A';
        } else if ($name!='_' && isset($nameMobileList[$name][$mobile]) && count($nameMobileList[$name][$mobile])===1) {
          $index = $nameMobileList[$name][$mobile][0];
          $case = 'B';
        } else if ($mobile2!='_' && isset($mobileList[$mobile2]) && count($mobileList[$mobile2])===1) {
          $index = $mobileList[$mobile2][0];
          $case = 'C';
        } else if ($mobile!='_' && isset($mobileList[$mobile]) && count($mobileList[$mobile])===1) {
          $index = $mobileList[$mobile][0];
          $case = 'D';
        } else if ($name2!='_' && isset($nameList[$name2]) && count($nameList[$name2])===1) {
          $index = $nameList[$name2][0];
          $case = 'E';
        } else if ($name!='_' && isset($nameList[$name]) && count($nameList[$name])===1) {
          $index = $nameList[$name][0];
          $case = 'F';
        } else {
          $index = null;
          $case = 'G';
        }
        if (!is_null($index)) {
          $teachers['rows'][$index]['dUser'] = (array)$notificationUser;
          $teachers['rows'][$index]['dCase'] = $case;
        }
      }
    }


    // 获取全校课表信息
    $data = [];
    $schoolTimetables = $this->getSchooTimetableList([]);
    if (!empty($schoolTimetables)) {
      foreach ($schoolTimetables as $item) {
        // 非今日数据跳过
        $dayOfWeek = empty($item['dayOfWeek']) ? '_' : trim($item['dayOfWeek']);
        $orderOfDay = empty($item['orderOfDay']) ? '_' : trim($item['orderOfDay']);
        if ($dayOfWeek != $weekdayStr || $orderOfDay != $classOrderStr) {
          continue;
        }

        $teacher = empty($item['teacher']) ? '_' : trim($item['teacher']);
        $subject = empty($item['subject']) ? '_' : trim($item['subject']);
        $mobile = empty($item['userName']) ? '_' : trim($item['userName']);

        if (($teacher!='_') && isset($nameMobileSubjectList[$teacher][$mobile][$subject]) && count($nameMobileSubjectList[$teacher][$mobile][$subject])===1) {
          $index = $nameMobileSubjectList[$teacher][$mobile][$subject][0];
          $case = 'a';
        } else if (($teacher!='_') && isset($nameMobileList[$teacher][$mobile]) && count($nameMobileList[$teacher][$mobile])===1) {
          $index = $nameMobileList[$teacher][$mobile][$subject][0];
          $case = 'b';
        } else if (($teacher!='_') && isset($nameSubjectList[$teacher][$subject]) && count($nameSubjectList[$teacher][$subject])===1) {
          $index = $nameSubjectList[$teacher][$subject][0];
          $case = 'c';
        } else if (($mobile!='_') && isset($mobileList[$mobile]) && count($mobileList[$mobile])===1) {
          $index = $mobileList[$mobile][0];
          $case = 'd';
        } else if (($teacher!='_') && isset($nameList[$teacher]) && count($nameList[$teacher])===1) {
          $index = $nameList[$teacher][0];
          $case = 'e';
        } else {
          $index = null;
          $case = 'f';
        }

        if (is_null($index) || empty($teachers['rows'][$index]['dUser'])) {
          continue;
        }
        $dUser = $teachers['rows'][$index]['dUser']??[];
        $tUser = $teachers['rows'][$index]??[];
        unset($tUser['dUser']);
        $data[$dayOfWeek][$orderOfDay][] = array_merge($item, [
          'dUser' => $dUser,
          'tUser' => $tUser,
          'tCase' => $case,
          'notificationTime' => date('Y-m-d '.$notificationTime.':00')
        ]);
      }
    }

    return $data[$weekdayStr][$classOrderStr] ?? [];
  }



}
