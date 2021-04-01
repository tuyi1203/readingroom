<?php


namespace App\Services;


use App\Models\Backend\User;
use \Hash;

class ProfileService extends BaseService
{
  /**
   * 检查输入的密码是否和当前数据库中的用户密码一致
   * @param $inputPassword
   * @param $currentPassword
   * @return mixed
   */
  public function checkPassword($inputPassword, $currentPassword)
  {
    return Hash::check($inputPassword, $currentPassword);
  }

  /**
   * 更新用户密码
   * @param $userId
   * @param $newPassword
   */
  public function updateUserPassword($userId, $newPassword)
  {
    $user = User::where('id', $userId)->firstOrFail();
    $user->password = bcrypt($newPassword);
    $user->save();
  }
}
