<?php

namespace App\Http\Controllers\Backend\V1;

use \Hash;
use App\Rules\Mobile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use function _\find;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Backend\User;
use App\Models\Backend\Menu;
use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Models\Backend\UserInfo;
use Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthController extends APIBaseController
{
  /**
   * Create user
   *
   * @param Request $request
   * @return JsonResponse [string] message
   */
  public function signup(Request $request)
  {
    $request->validate([
      'name' => 'required|string',
      'email' => 'required|string|email|unique:users',
      'password' => 'required|string|confirmed'
    ]);

    $user = new User([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password)
    ]);

    $user->save();

//    return response()->json([
//      'message' => 'Successfully created user!'
//    ], 201);
    return $this->success(null,'Successfully created user!');
  }

  /**
   * Login user and create token
   *
   * @param  [string] email
   * @param  [string] password
   * @param  [boolean] remember_me
   * @return [string] access_token
   * @return [string] token_type
   * @return [string] expires_at
   */
  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return $this->error(422, implode("\n", $messages));
    }

//    $request->validate([
//      'email' => 'required|string|email',
//      'password' => 'required|string',
//      'remember_me' => 'boolean'
//    ]);

    /*
     * 当守卫从web改成api后，不能通过原有的Auth来验证，否则会报错，
     * 此时需要自行验证。
     */
    /*
    $credentials = request(['email', 'password']);

    if (!Auth::attempt($credentials)) {
      return $this->error(401, 'Unauthorized');
    }
    */

    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
      return $this->error(402,'用户名或密码错误');
    }

    $userInfo = UserInfo::where('user_id', $user->id)->first();
    return $this->saveLoginInfo($userInfo);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function bindLogin(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'senceid' => 'required|string',
      'verifycode' => 'required|string',
      'verifykey' => 'required|string',
      'mobile' => ['required', 'string', new Mobile]
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $senceId = $request->senceid;
    if (!Cache::has($senceId . 'wxloginopenid')) {//如果缓存中没有openid信息，则返回失败信息
      return $this->error(401, 'no open id');
    }

    if (!Cache::has($request->verifykey)) {
      return $this->error(401, '验证码已失效');
    }

    $verifyCodeInfo = Cache::get($request->verifykey);
    if ($verifyCodeInfo['mobile'] !== $request->mobile
      || !hash_equals($request->verifycode, $verifyCodeInfo['code'])) {
      return $this->error(401, '验证码不正确');
    }

    //查看电话号码是否在用户表中存在
    $user = User::where('mobile', $request->mobile)->first();
    if (empty($user)) {
      return $this->error(401, '没有您的用户信息，请联系系统管理员添加');
    }

    //保存用户信息，然后登陆
    $openid = Cache::get($senceId . 'wxloginopenid');
    $userInfo = UserInfo::Create([
      'open_id' => $openid,
      'user_id' => $user->id,
      'mobile' => $user->mobile,
      'full_name' => $user->name,
      'guid' => (string)Str::uuid(32)->getHex(),
    ]);

    Cache::forget($request->verifyKey);

    return $this->saveLoginInfo($userInfo);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function QRLogin(Request $request)
  {
    //判断是否有场景值
    if (!$senceid = $request->input('senceid')) {// 如果没有场景值，则返回登陆失败
      return $this->error(401, 'no senceid');
    }

    //如果有场景值，则查看缓存中是否有场景值对应的openid
    if (!$openid = Cache::get($senceid . 'wxloginopenid')) {//如果缓存中没有场景值对应的openid，则返回登陆失败
      return $this->error(401, 'no open id');
    }

    //如果有场景值对应的openid，则查看用户信息表中是否有该openid对应的用户
    $userInfo = UserInfo::where('open_id', $openid)->first();
    if (empty($userInfo)) {// 如果没有该openid对应的用户，则返回需要绑定，前端弹出绑定页面，并清空轮询
      return $this->success([
        'needtobind' => true,
      ]);
    }

    //如果有该openid对应的用户，则允许其登陆，并返回token、权限信息、用户信息、菜单信息等
    return $this->saveLoginInfo($userInfo);

  }

  /**
   * 保存登陆信息
   * @param $userInfo
   * @return JsonResponse
   */
  private function saveLoginInfo($userInfo)
  {
    $user = User::where('id', $userInfo->user_id)->first();
    // 取得用户所有权限
    $permissions = $user->getAllPermissions();

    // 取得用户所有菜单
    $allMenus = Menu::orderby('sort', 'desc')->get();
    $permissionMenus = $this->makeMenuData($permissions, $allMenus);

    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->token;
    $token->expires_at = Carbon::now()->addDays(1);
//    if ($request->remember_me) {
//      $token->expires_at = Carbon::now()->addWeeks(1);
//    }

    $token->save();

    return $this->success([
      'access_token' => $tokenResult->accessToken,
      'token_type' => 'Bearer',
      'permissions' => $permissions,
      'menus' => $permissionMenus,
      'user' => $userInfo,
      'expires_at' => Carbon::parse(
        $tokenResult->token->expires_at
      )->toDateTimeString()
    ]);
  }

  /**
   * 组装菜单数据
   * @param $permissions 用户所有权限
   * @param $allMenus 全部菜单数据
   * @return array
   */
  private function makeMenuData($permissions, $allMenus)
  {
    $tmpMenus = [];
    $permissionMenus = [];
    foreach ($allMenus as $menu) {
      if (find($permissions, function ($permission) use ($menu) {
        return $menu->permission_id === $permission->id;
      })) {
        $tmpMenus[] = $menu;
        $permissionMenus[] = $menu;
      }
    }

    //根据menu的内容找到所有的父级节点
    if (count($tmpMenus) > 0) {
      foreach ($tmpMenus as $menu) {
        $this->getMenuRecursion($permissionMenus, $menu, $allMenus);
      }
    }
    return $permissionMenus;
  }

  /**
   * 递归寻找父级节点
   * @param $permissionMenus
   * @param $menu
   * @param $allMenus
   */
  private function getMenuRecursion(&$permissionMenus, $menu, $allMenus)
  {
    if (!find($permissionMenus, function ($m) use ($menu) {
      return $m->id === $menu->id;
    })) {
      $permissionMenus[] = $menu;
    }

    if (!is_null($menu->parent_id)) {
      $parent_menu = find($allMenus, function ($m) use ($menu) {
        return $menu->parent_id == $m->id;
      });
      $this->getMenuRecursion($permissionMenus, $parent_menu, $allMenus);
    }
  }

  /**
   * Logout user (Revoke the token)
   *
   * @return [string] message
   */
  public function logout(Request $request)
  {
    $request->user()->token()->revoke();

    return response()->json([
      'message' => 'Successfully logged out'
    ]);
  }

  /**
   * Get the authenticated User
   *
   * @return [json] user object
   */
  public function user(Request $request)
  {
    return response()->json($request->user());
  }

  protected function guard()//这个方法trait也有，但是如果我们用其他的guard,就要重写方法
  {
    return Auth::guard('backend');//你要使用的guard
  }
}
