<?php

namespace App\Http\Controllers\Api\V1;

use function _\find;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Menu;
use App\Http\Controllers\BaseController;
use Log;

class AuthController extends BaseController
{
  /**
   * Create user
   *
   * @param  [string] name
   * @param  [string] email
   * @param  [string] password
   * @param  [string] password_confirmation
   * @return [string] message
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

    return response()->json([
      'message' => 'Successfully created user!'
    ], 201);
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
    $request->validate([
      'email' => 'required|string|email',
      'password' => 'required|string',
      'remember_me' => 'boolean'
    ]);

    $credentials = request(['email', 'password']);

    if (!Auth::attempt($credentials)) {
//      return response()->json([
////        'message' => 'Unauthorized'
////      ], 401);
      return $this->error('401', 'Unauthorized');
    }

    $user = $request->user();

    // 取得用户所有权限
    $permissions = $user->getAllPermissions();

    // 取得用户所有菜单
    $allMenus = Menu::orderby('sort', 'desc')->get();
    $permissionMenus = $this->makeMenuData($permissions, $allMenus);
//    $permissionMenus = $allMenus->reject(function ($menu) use ($permissions) {
//      if (find($permissions, function ($permission) use ($menu) {
//        return $menu->permission_id === $permission->id;
//      })) {
//        return $menu;
//      }
//      return find($permissions, function ($permission) use ($menu) {
//        return $menu->permission_id === $permission->id;
//      });
//    });


//    $permissionMenus = $allMenus->reject(function ($record) use ($permissions) {
//      return $record->permission_id === 1;
//    });

    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->token;

    if ($request->remember_me)
      $token->expires_at = Carbon::now()->addWeeks(1);

    $token->save();

//    return response()->json([
//      'access_token' => $tokenResult->accessToken,
//      'token_type' => 'Bearer',
//      'permissions' => $permissions,
//      'menus' => $permissionMenus,
//      'expires_at' => Carbon::parse(
//        $tokenResult->token->expires_at
//      )->toDateTimeString()
//    ]);

    return $this->success([
      'access_token' => $tokenResult->accessToken,
      'token_type' => 'Bearer',
      'permissions' => $permissions,
      'menus' => $permissionMenus,
      'user' => $request->user(0),
      'expires_at' => Carbon::parse(
        $tokenResult->token->expires_at
      )->toDateTimeString()
    ]);
  }


  /**
   * 组装菜单数据
   * @param $permissions 用户所有权限
   * @param $allMenus 全部菜单数据
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
}
