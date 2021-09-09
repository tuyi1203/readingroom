<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(
  [
    'prefix' => 'v1',
    'domain' => env('APP_DEV_DOMAIN'),
    'namespace' => 'Backend',
    'middleware' => 'backend',
  ],
  function () {
    Route::group([
      'namespace' => 'V1',
//      'middleware' => 'throttle:120,1'
    ], function () {

      Route::prefix('wechat')->group(function () {
        Route::get('getloginqrcode', 'WechatController@loginQRCode'); // 获取微信登陆用二维码
        Route::any('/', 'WechatController@serve'); // 本地服务器与微信服务器通信接口
      });

      /*
       * 不需要认证的路由 -- Start
       */
      Route::post('login', 'AuthController@login'); // 首页登陆接口
      Route::Post('qrlogin', 'AuthController@QRLogin'); // 微信扫码登陆接口
      Route::Post('bindlogin', 'AuthController@bindLogin'); // 绑定手机号登陆接口
      Route::post('signup', 'AuthController@signup'); // 注册（暂时不需要）接口
      Route::post('getverifycode', 'VerificationCodesController@store'); // 发送短信验证码接口
      Route::get('logout', 'AuthController@logout');

      /*
       * 需要认证的路由 -- Start
       */
      Route::group([
        'namespace' => 'Auth',
        'middleware' => [
          'auth:backend',
//          'throttle:120,1'
        ] //passport验证
      ], function () {
        //角色接口
        Route::apiResource('roles', 'RoleController');

        Route::put('profile/password', 'ProfileController@setPassword');

        // 权限管理接口
        Route::get('permissions/rid/{rid}', 'PermissionController@index'); // 获取角色权限接口
        Route::get('permissions/pid/{pid}', 'PermissionController@index'); // 获取权限接口
        Route::apiResource('permissions', 'PermissionController'); //获取所有权限接口
        Route::put('permissions/rid/{rid}', 'PermissionController@updateRolePermissions'); // 更新角色权限接口


        //用户管理接口
        Route::apiResource('users', 'UserController');

        Route::get('files/ids', 'FileController@getList'); // 获取文件列表接口
        Route::get('files/download/{id}', 'FileController@download'); // 文件下载接口
        Route::apiResource('files', 'FileController'); // 文件操作接口

        //用户快捷菜单
        Route::apiResource('menu', 'MenuController', ['only' => [
          'index', 'store', 'destroy'
        ]]);


        // 学生档案管理子系统接口
        Route::group(['prefix' => 'students'], function () {
          Route::group(['prefix' => 'speciality'], function () {

            //----------------学生档案系统通用接口----------------------
            // 获取班级信息接口
            Route::get('get_campus_classes', 'StudentsSpecialityCommonController@getCampusClasses');

            // 获取学生特长类别信息接口
            Route::get('get_speciality_types', 'StudentsSpecialityCommonController@getSpecialityTypes');

            //----------------学生档案系统特长信息接口----------------------
            // 获取学生特长信息接口
            Route::get('info_search', 'StudentsSpecialityInfoController@getInfos');

            // 获取学生特长名称列表接口
            Route::get('get_info_names', 'StudentsSpecialityInfoController@getInfoNames');

            // 学生特长数据下载接口
            Route::get('info_search_excel', 'StudentsSpecialityInfoController@download');

            //----------------学生档案系统获奖信息接口----------------------
            // 获取学生获奖信息接口
            Route::get('award_search', 'StudentsSpecialityAwardController@getAwards');

            // 下载学生获奖数据
            Route::get('award_search_excel', 'StudentsSpecialityAwardController@download');
          });
        });

        // 职称申报子系统接口
        Route::group(['prefix' => 'progress'], function () {

          Route::get('/baseinfo/detail', 'ProgressBaseInfoController@getBaseInfo'); // 获得用户自己申报基本信息接口
          Route::post('/baseinfo/edit', 'ProgressBaseInfoController@edit'); // 修改用户自己的申报基本信息接口

          Route::apiResource('/baseinfo', 'ProgressBaseInfoController'); // 教师申报基本信息接口

//        Route::get('/dict', 'ProgressDictController@index'); // 数据字典取得接口
          Route::get('/dict/search', 'ProgressDictController@search'); // 数据字典查询接口（带分页）
          Route::apiResource('/dict', 'ProgressDictController'); // 数据字典增删改查接口
          Route::get('/dict_category', 'ProgressDictCategoryController@index'); // 数据字典类型

          //师德师风接口组
          Route::group(['prefix' => 'morals'], function () {
            Route::post('/edit', 'ProgressMoralController@edit'); //师德师风接口
            Route::get('/detail', 'ProgressMoralController@detail'); //师德师风接口
          });

          // 基本资格接口组
          Route::group(['prefix' => 'qualification'], function () {

            // 教育经历接口组
            Route::group(['prefix' => 'educate'], function () {
              Route::post('/edit', 'ProgressQualificationEducationController@edit'); //基本资格教育经历接口
              Route::get('/detail', 'ProgressQualificationEducationController@detail'); //基本资格教育经历详情接口
            });

            // 工作信息接口组
            Route::group(['prefix' => 'work'], function () {
              Route::post('/edit', 'ProgressQualificationWorkController@edit'); //基本资格工作信息接口
              Route::get('/detail', 'ProgressQualificationWorkController@detail'); //基本资格工作信息详情接口
              Route::post('/experience/edit', 'ProgressQualificationWorkExperienceController@edit'); //基本资格工作经历信息接口
              Route::get('/experience/detail', 'ProgressQualificationWorkExperienceController@detail'); //基本资格工作经历信息详情接口
            });

            // 管理经历接口组
            Route::group(['prefix' => 'manage/experience'], function () {
              Route::post('/edit', 'ProgressQualificationManageExperienceController@edit'); //基本资格管理经历信息接口
              Route::get('/detail', 'ProgressQualificationManageExperienceController@detail'); //基本资格管理经历信息详情接口
            });
          });

          // 科研成果接口组
          Route::group(['prefix' => 'research/achievement'], function () {
            Route::get('/{id}', 'ProgressResearchAchievementController@show'); // 科研成果详情接口（教师自身）
            Route::put('/{id}', 'ProgressResearchAchievementController@update'); // 修改科研成果（教师自身）

            Route::get('/', 'ProgressResearchAchievementController@index'); // 科研成果列表接口（教师自身）
            Route::post('/', 'ProgressResearchAchievementController@store'); // 增加科研成果接口（教师自身）
            Route::delete('/{id}', 'ProgressResearchAchievementController@destroy'); // 删除科研成果（教师自身）
            Route::delete('/del', 'ProgressResearchAchievementController@destroy'); // 删除科研成果（教师自身）
          });

          // 教育成果接口组
          Route::group(['prefix' => 'teach/achievement'], function () {
            Route::get('/{id}', 'ProgressTeachAchievementController@show'); // 教育成果详情接口（教师自身）
            Route::put('/{id}', 'ProgressTeachAchievementController@update'); // 修改教育成果（教师自身）
            Route::get('/', 'ProgressTeachAchievementController@index'); // 教育成果列表接口（教师自身）
            Route::post('/', 'ProgressTeachAchievementController@store'); // 增加教育成果接口（教师自身）
            Route::delete('/{id}', 'ProgressTeachAchievementController@destroy'); // 删除教育成果（教师自身）
          });

          // 教学成果接口组
          Route::group(['prefix' => 'educate'], function () {
            Route::get('/detail', 'ProgressEducateAchievementController@getBaseInfo'); // 获得用户自己教学成果基本信息接口
            Route::post('/edit', 'ProgressEducateAchievementController@edit'); // 修改用户自己的教学成果基本信息接口

//            Route::group(['prefix' => 'achievement'], function () {
//              Route::get('/{id}', 'ProgressEducateAchievementController@show'); // 教学成果详情接口（教师自身）
//              Route::put('/{id}', 'ProgressEducateAchievementController@update'); // 修改教学成果（教师自身）
//              Route::get('/', 'ProgressEducateAchievementController@index'); // 教学成果列表接口（教师自身）
//              Route::post('/', 'ProgressEducateAchievementController@store'); // 增加教学成果接口（教师自身）
//              Route::delete('/{id}', 'ProgressEducateAchievementController@destroy'); // 删除教学成果（教师自身）
//
//            });
            Route::apiResource('achievement', 'ProgressEducateAchievementController');

          });

          // 业务档案管理荣誉及其他奖励
          Route::apiResource('/award/achievement', 'ProgressAwardAchievementController'); // 荣誉（教师自身）

          // 教师信息查询组
          Route::group(['prefix' => 'teacher'], function () {
            Route::get('/teach/{id}', 'ProgressTeacherController@teachDetail'); // 教育成果详情
            Route::get('/educate/{id}', 'ProgressTeacherController@educateDetail'); // 教学成果详情
            Route::get('/research/{id}', 'ProgressTeacherController@researchDetail'); // 科研成果详情
            Route::get('/award/{id}', 'ProgressTeacherController@awardDetail'); // 荣誉和其他详情
            Route::get('/pdf/{uid}', 'ProgressTeacherController@pdf'); // 教师信息PDF下载
          });
          Route::apiResource('teacher', 'ProgressTeacherController', ['only' => ['index', 'show']]); // 教师信息查询接口


          // 教师获奖情况奖励组
          Route::group(['prefix' => 'award'], function () {
            Route::get('/search/excel', 'ProgressAwardSearchController@download'); // 下载教师获奖excel数据
            Route::apiResource('/search', 'ProgressAwardSearchController', ['only' => ['index', 'show']]); // 教师获奖信息查询接口
          });

        });

        // 教师通知子系统接口
        Route::group(['prefix' => 'teacher/notifications'], function(){ //教师通知系统
          Route::group(['prefix' => 'attend_class'], function(){ // 上课提醒
            Route::put('/setting', 'TeacherNotificationAttendClassController@setting'); // 开关
            Route::post('/excel', 'TeacherNotificationAttendClassController@excel'); // 上传excel
            Route::get('/{id}', 'TeacherNotificationAttendClassController@show');
            Route::put('/{id}', 'TeacherNotificationAttendClassController@update');
            Route::delete('/{id?}', 'TeacherNotificationAttendClassController@destroy');
            Route::get('/', 'TeacherNotificationAttendClassController@index'); // 通知日期列表
            Route::post('/', 'TeacherNotificationAttendClassController@store'); // 批量添加通知日期
          });
          Route::group(['prefix' => 'distribute_food'], function(){ // 打饭提醒
            Route::post('/setting', 'TeacherNotificationDistributeFoodController@setting'); // 开关
            Route::post('/excel', 'TeacherNotificationDistributeFoodController@excel'); // 上传excel
            Route::get('/{id}', 'TeacherNotificationDistributeFoodController@show')->where('id', '[0-9]+');
            Route::put('/{id}', 'TeacherNotificationDistributeFoodController@update')->where('id', '[0-9]+');
            Route::delete('/{id?}', 'TeacherNotificationDistributeFoodController@destroy');
            Route::get('/', 'TeacherNotificationDistributeFoodController@index'); // 通知日期列表
            Route::post('/', 'TeacherNotificationDistributeFoodController@store'); // 批量添加通知日期
          });
          Route::group(['prefix' => 'after_class_service'], function(){ // 上课提醒
            Route::post('/setting', 'TeacherNotificationAfterClassServiceController@setting'); // 开关
            Route::post('/excel', 'TeacherNotificationAfterClassServiceController@excel'); // 上传excel
            Route::get('/{id}', 'TeacherNotificationAfterClassServiceController@show')->where('id', '[0-9]+');
            Route::put('/{id}', 'TeacherNotificationAfterClassServiceController@update')->where('id', '[0-9]+');
            Route::delete('/{id?}', 'TeacherNotificationAfterClassServiceController@destroy');
            Route::get('/', 'TeacherNotificationAfterClassServiceController@index'); // 通知日期列表
            Route::post('/', 'TeacherNotificationAfterClassServiceController@store'); // 批量添加通知日期
          });
        });

      });
    });
  }
);
