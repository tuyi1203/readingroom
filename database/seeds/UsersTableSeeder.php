<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      // 初始化用户角色，将 1 号用户指派为『站长』
      $user = new User();
      $user->name = '涂涂';
      $user->email = '343745799@qq.com';
      $user->password = bcrypt('123456a');
//      $user->avatar = 'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png';
      $user->save();
      $user->assignRole('Founder');
    }
}
